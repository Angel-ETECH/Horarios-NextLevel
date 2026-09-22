<?php
// app/Services/HorarioGeneratorService.php

namespace App\Services;

use App\Models\Horario;
use App\Models\ProfesorCurso;
use App\Models\Aula;
use App\Models\DisponibilidadProfesor;
use App\Models\ConfiguracionHorario;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HorarioGeneratorService
{
    protected array $aulasOcupadas = [];
    protected array $profesoresOcupados = [];
    protected array $gradosOcupados = [];
    protected array $conflictos = [];
    protected array $asignacionesIncompletas = [];
    protected array $estadisticas = [];
    protected array $configuracionesCache = [];

    /**
     * ========================================
     * MÉTODO PRINCIPAL
     * ========================================
     */
    public function generarHorarios(array $opciones = []): array
    {
        Log::info('Iniciando generación de horarios...', $opciones);

        DB::beginTransaction();

        try {
            // 1. CARGAR HORARIOS EXISTENTES
            $this->cargarHorariosExistentes($opciones);

            // 2. Obtener asignaciones activas
            $asignaciones = $this->obtenerAsignaciones($opciones);
            $this->estadisticas['total_asignaciones'] = $asignaciones->count();

            // 3. Ordenar por prioridad
            $asignacionesOrdenadas = $this->ordenarAsignacionesPorPrioridad($asignaciones);

            // 4. Procesar cada asignación
            $horariosCreados = 0;

            foreach ($asignacionesOrdenadas as $asignacion) {
                $resultado = $this->procesarAsignacion($asignacion);

                if ($resultado['creado']) {
                    $horariosCreados += $resultado['horas_asignadas'];
                }

                if ($resultado['horas_asignadas'] < $asignacion->horas_asignadas) {
                    $this->asignacionesIncompletas[] = [
                        'asignacion_id' => $asignacion->id,
                        'profesor' => $asignacion->profesor->nombre_completo ?? 'N/A',
                        'curso' => $asignacion->curso->nombre ?? 'N/A',
                        'grado' => $asignacion->grado->nombre_completo ?? 'N/A',
                        'horas_requeridas' => $asignacion->horas_asignadas,
                        'horas_asignadas' => $resultado['horas_asignadas'],
                        'horas_pendientes' => $asignacion->horas_asignadas - $resultado['horas_asignadas'],
                        'motivo' => $resultado['motivo'] ?? 'No se pudieron asignar todas las horas'
                    ];
                }
            }

            $this->estadisticas['horarios_creados'] = $horariosCreados;
            $this->estadisticas['total_conflictos'] = count($this->conflictos);
            $this->estadisticas['asignaciones_incompletas'] = count($this->asignacionesIncompletas);

            DB::commit();

            return [
                'success' => true,
                'estadisticas' => $this->estadisticas,
                'conflictos' => $this->conflictos,
                'asignaciones_incompletas' => $this->asignacionesIncompletas,
                'horarios_creados' => $horariosCreados,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en generación: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'conflictos' => $this->conflictos,
            ];
        }
    }

    /**
     * Cargar horarios existentes
     */
    protected function cargarHorariosExistentes(array $opciones): void
    {
        $query = Horario::where('estado', 'activo');

        if (!empty($opciones['institucion'])) {
            $query->where('institucion', $opciones['institucion']);
        }

        if (!empty($opciones['periodo_academico'])) {
            $query->where('periodo_academico', $opciones['periodo_academico']);
        }

        $horariosExistentes = $query->get();

        foreach ($horariosExistentes as $horario) {
            $this->marcarOcupado(
                $horario->profesor_id,
                $horario->grado_id,
                $horario->aula_id,
                $horario->dia_semana,
                $horario->hora_inicio
            );
        }

        $this->estadisticas['horarios_existentes'] = $horariosExistentes->count();
    }

    /**
     * Ordenar asignaciones por prioridad
     */
    protected function ordenarAsignacionesPorPrioridad(Collection $asignaciones): Collection
    {
        return $asignaciones->sortByDesc(function ($asignacion) {
            $prioridadHoras = $asignacion->horas_asignadas * 10;
            $prioridadTipo = $asignacion->curso->tipo === 'obligatorio' ? 100 : 0;
            $prioridadEstudiantes = ($asignacion->grado->numero_estudiantes ?? 0);
            return $prioridadHoras + $prioridadTipo + $prioridadEstudiantes;
        })->values();
    }

    /**
     * Procesar una asignación
     */
    protected function procesarAsignacion($asignacion): array
    {
        $profesorId = $asignacion->profesor_id;
        $gradoId = $asignacion->grado_id;
        $institucion = $asignacion->institucion ?? 'colegio';
        $horasNecesarias = $asignacion->horas_asignadas;

        // 1. Obtener la configuración desde BD
        $config = $this->obtenerConfiguracion(
            $institucion,
            $asignacion->grado->nivel,
            $asignacion->grado->turno
        );

        if (!$config) {
            return [
                'creado' => false,
                'horas_asignadas' => 0,
                'motivo' => "No hay configuración activa para {$institucion}/{$asignacion->grado->nivel}/{$asignacion->grado->turno}"
            ];
        }

        // 2. Disponibilidad del profesor
        $disponibilidades = $this->obtenerDisponibilidadProfesor($profesorId, $institucion);

        if ($disponibilidades->isEmpty()) {
            return [
                'creado' => false,
                'horas_asignadas' => 0,
                'motivo' => 'El profesor no tiene disponibilidad registrada'
            ];
        }

        // 3. Aulas adecuadas
        $aulas = $this->obtenerAulasParaGrado($asignacion->grado, $institucion);

        if ($aulas->isEmpty()) {
            return [
                'creado' => false,
                'horas_asignadas' => 0,
                'motivo' => 'No hay aulas disponibles'
            ];
        }

        // 4. Asignar bloques
        $bloquesAsignados = $this->asignarBloquesConConfiguracion(
            $asignacion,
            $config,
            $disponibilidades,
            $aulas
        );

        if (empty($bloquesAsignados)) {
            return [
                'creado' => false,
                'horas_asignadas' => 0,
                'motivo' => 'No se encontraron bloques horarios disponibles'
            ];
        }

        // 5. Crear registros
        $this->crearRegistrosHorario($asignacion, $bloquesAsignados);

        return [
            'creado' => true,
            'horas_asignadas' => count($bloquesAsignados),
        ];
    }

    /**
     * Obtener configuración desde BD (con cache)
     */
    protected function obtenerConfiguracion(string $institucion, string $nivel, string $turno): ?ConfiguracionHorario
    {
        $key = "{$institucion}_{$nivel}_{$turno}";

        if (isset($this->configuracionesCache[$key])) {
            return $this->configuracionesCache[$key];
        }

        $config = ConfiguracionHorario::with('bloques')
            ->where('institucion', $institucion)
            ->where('nivel', $nivel)
            ->where('turno', $turno)
            ->where('activo', true)
            ->orderBy('año_academico', 'desc')
            ->first();

        $this->configuracionesCache[$key] = $config;

        return $config;
    }

    /**
     * Asignar bloques usando la configuración de BD
     */
    protected function asignarBloquesConConfiguracion(
        $asignacion,
        ConfiguracionHorario $config,
        Collection $disponibilidades,
        Collection $aulas
    ): array {
        $bloquesAsignados = [];
        $horasRestantes = $asignacion->horas_asignadas;
        $profesorId = $asignacion->profesor_id;
        $gradoId = $asignacion->grado_id;

        // Obtener bloques de CLASE (excluir recesos)
        $bloquesClase = $config->bloquesClase;

        // Días disponibles del profesor
        $diasDisponibles = $disponibilidades->pluck('dia_semana')->unique()->values();

        foreach ($diasDisponibles as $dia) {
            if ($horasRestantes <= 0) break;

            foreach ($bloquesClase as $bloque) {
                if ($horasRestantes <= 0) break;

                $horaInicio = $bloque->hora_inicio->format('H:i');
                $horaFin = $bloque->hora_fin->format('H:i');

                // Verificar disponibilidad del profesor
                if (!$this->profesorDisponibleEnBloque($disponibilidades, $dia, $horaInicio, $horaFin)) {
                    continue;
                }

                // Verificar que no esté ocupado
                if ($this->horaOcupada($profesorId, $gradoId, $dia, $horaInicio)) {
                    continue;
                }

                // Buscar aula
                $aulaAsignada = $this->buscarAulaDisponible($aulas, $dia, $horaInicio);

                if (!$aulaAsignada) {
                    continue;
                }

                // Asignar
                $bloquesAsignados[] = [
                    'dia_semana' => $dia,
                    'hora_inicio' => $horaInicio,
                    'hora_fin' => $horaFin,
                    'aula_id' => $aulaAsignada->id,
                    'numero_bloque' => $bloque->numero_bloque,
                ];

                $this->marcarOcupado($profesorId, $gradoId, $aulaAsignada->id, $dia, $horaInicio);
                $horasRestantes--;
            }
        }

        return $bloquesAsignados;
    }

    /**
     * Verificar disponibilidad del profesor en un bloque
     */
    protected function profesorDisponibleEnBloque(
        Collection $disponibilidades,
        string $dia,
        string $horaInicio,
        string $horaFin
    ): bool {
        $bloquesDia = $disponibilidades->where('dia_semana', $dia);

        foreach ($bloquesDia as $disp) {
            $dispInicio = Carbon::parse($disp->hora_inicio);
            $dispFin = Carbon::parse($disp->hora_fin);
            $bloqueInicio = Carbon::parse($horaInicio);
            $bloqueFin = Carbon::parse($horaFin);

            if ($bloqueInicio >= $dispInicio && $bloqueFin <= $dispFin) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtener disponibilidad del profesor
     */
    protected function obtenerDisponibilidadProfesor(int $profesorId, string $institucion): Collection
    {
        return DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('institucion', $institucion)
            ->where('tipo', 'disponible')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Obtener asignaciones activas
     */
    protected function obtenerAsignaciones(array $opciones = []): Collection
    {
        $query = ProfesorCurso::with(['profesor', 'curso', 'grado'])
            ->where('activo', true);

        if (!empty($opciones['profesor_id'])) {
            $query->where('profesor_id', $opciones['profesor_id']);
        }

        if (!empty($opciones['institucion'])) {
            $query->where('institucion', $opciones['institucion']);
        }

        if (!empty($opciones['grado_id'])) {
            $query->where('grado_id', $opciones['grado_id']);
        }

        return $query->get();
    }

    /**
     * Obtener aulas adecuadas
     */
    protected function obtenerAulasParaGrado($grado, string $institucion): Collection
    {
        return Aula::where('activo', true)
            ->where('capacidad', '>=', $grado->numero_estudiantes ?? 0)
            ->where(function ($q) use ($grado) {
                $q->where('nivel', 'todos')
                  ->orWhere('nivel', $grado->nivel);
            })
            ->orderBy('capacidad', 'asc')
            ->get();
    }

    /**
     * Buscar aula disponible
     */
    protected function buscarAulaDisponible(Collection $aulas, string $dia, string $hora): ?Aula
    {
        foreach ($aulas as $aula) {
            $clave = "{$aula->id}_{$dia}_{$hora}";
            if (!isset($this->aulasOcupadas[$clave])) {
                return $aula;
            }
        }
        return null;
    }

    /**
     * Verificar si una hora está ocupada
     */
    protected function horaOcupada(int $profesorId, int $gradoId, string $dia, string $hora): bool
    {
        return isset($this->profesoresOcupados["{$profesorId}_{$dia}_{$hora}"])
            || isset($this->gradosOcupados["{$gradoId}_{$dia}_{$hora}"]);
    }

    /**
     * Marcar como ocupado
     */
    protected function marcarOcupado(int $profesorId, int $gradoId, int $aulaId, string $dia, string $hora): void
    {
        $this->profesoresOcupados["{$profesorId}_{$dia}_{$hora}"] = true;
        $this->gradosOcupados["{$gradoId}_{$dia}_{$hora}"] = true;
        $this->aulasOcupadas["{$aulaId}_{$dia}_{$hora}"] = true;
    }

    /**
     * Crear registros de horario
     */
    protected function crearRegistrosHorario($asignacion, array $bloquesAsignados): array
    {
        $horariosCreados = [];
        $periodoAcademico = date('Y') . '-' . (date('Y') + 1);

        foreach ($bloquesAsignados as $bloque) {
            $horario = Horario::create([
                'profesor_id' => $asignacion->profesor_id,
                'curso_id' => $asignacion->curso_id,
                'aula_id' => $bloque['aula_id'],
                'grado_id' => $asignacion->grado_id,
                'dia_semana' => $bloque['dia_semana'],
                'hora_inicio' => $bloque['hora_inicio'],
                'hora_fin' => $bloque['hora_fin'],
                'turno' => $this->determinarTurno($bloque['hora_inicio']),
                'institucion' => $asignacion->institucion ?? 'colegio',
                'tipo' => 'regular',
                'semana' => 1,
                'periodo_academico' => $periodoAcademico,
                'estado' => 'activo',
                'version' => 1,
                'creado_por' => 'sistema_automatico',
                'observacion' => 'Generado automáticamente',
            ]);

            $horariosCreados[] = $horario;
        }

        return $horariosCreados;
    }

    /**
     * Determinar turno según la hora
     */
    protected function determinarTurno(string $hora): string
    {
        $horaInt = (int) explode(':', $hora)[0];

        if ($horaInt < 12) return 'mañana';
        if ($horaInt < 18) return 'tarde';
        return 'noche';
    }

    /**
     * Obtener estadísticas
     */
    public function obtenerEstadisticas(): array
    {
        return [
            'horarios_creados' => $this->estadisticas['horarios_creados'] ?? 0,
            'horarios_existentes' => $this->estadisticas['horarios_existentes'] ?? 0,
            'total_asignaciones' => $this->estadisticas['total_asignaciones'] ?? 0,
            'total_conflictos' => $this->estadisticas['total_conflictos'] ?? 0,
            'asignaciones_incompletas' => $this->estadisticas['asignaciones_incompletas'] ?? 0,
            'tasa_exito' => $this->estadisticas['total_asignaciones'] > 0
                ? round((($this->estadisticas['horarios_creados'] ?? 0) /
                    max(1, $this->estadisticas['total_asignaciones'])) * 100, 2)
                : 0,
        ];
    }

    /**
     * Obtener conflictos
     */
    public function obtenerConflictos(): array
    {
        return $this->conflictos;
    }

    /**
     * Obtener asignaciones incompletas
     */
    public function obtenerAsignacionesIncompletas(): array
    {
        return $this->asignacionesIncompletas;
    }
}
