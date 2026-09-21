<?php
// app/Services/HorarioGeneratorService.php

namespace App\Services;

use App\Models\Horario;
use App\Models\ProfesorCurso;
use App\Models\Aula;
use App\Models\DisponibilidadProfesor;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HorarioGeneratorService
{
    protected array $config;
    protected array $aulasOcupadas = [];
    protected array $profesoresOcupados = [];
    protected array $gradosOcupados = [];
    protected array $conflictos = [];
    protected array $asignacionesIncompletas = [];
    protected array $estadisticas = [];

    public function __construct()
    {
        $this->config = config('horarios.instituciones');
    }

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
            // 1. CARGAR HORARIOS EXISTENTES (NO SOBRESCRIBIR)
            $this->cargarHorariosExistentes($opciones);

            // 2. Obtener asignaciones activas
            $asignaciones = $this->obtenerAsignaciones($opciones);
            $this->estadisticas['total_asignaciones'] = $asignaciones->count();

            // 3. Ordenar por prioridad (más horas primero, luego obligatorios)
            $asignacionesOrdenadas = $this->ordenarAsignacionesPorPrioridad($asignaciones);

            // 4. Procesar cada asignación
            $horariosCreados = 0;

            foreach ($asignacionesOrdenadas as $asignacion) {
                $resultado = $this->procesarAsignacion($asignacion);

                if ($resultado['creado']) {
                    $horariosCreados += $resultado['horas_asignadas'];
                }

                // Registrar asignaciones incompletas
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

            Log::info('Generación de horarios completada', $this->estadisticas);

            return [
                'success' => true,
                'estadisticas' => $this->estadisticas,
                'conflictos' => $this->conflictos,
                'asignaciones_incompletas' => $this->asignacionesIncompletas,
                'horarios_creados' => $horariosCreados,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en generación de horarios: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'conflictos' => $this->conflictos,
            ];
        }
    }

    /**
     * ========================================
     * CARGAR HORARIOS EXISTENTES
     * ========================================
     * Marca como ocupados los horarios ya programados para
     * impedir que la nueva generación se superponga.
     */
    protected function cargarHorariosExistentes(array $opciones): void
    {
        $query = Horario::where('estado', 'activo');

        // Filtrar por institución si se especifica
        if (!empty($opciones['institucion'])) {
            $query->where('institucion', $opciones['institucion']);
        }

        // Filtrar por período académico
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
        Log::info("Horarios existentes cargados: {$horariosExistentes->count()}");
    }

    /**
     * ========================================
     * ORDENAR ASIGNACIONES POR PRIORIDAD
     * ========================================
     */
    protected function ordenarAsignacionesPorPrioridad(Collection $asignaciones): Collection
    {
        return $asignaciones->sortByDesc(function ($asignacion) {
            // Prioridad 1: Más horas asignadas
            $prioridadHoras = $asignacion->horas_asignadas * 10;

            // Prioridad 2: Cursos obligatorios
            $prioridadTipo = $asignacion->curso->tipo === 'obligatorio' ? 100 : 0;

            // Prioridad 3: Grados con más estudiantes
            $prioridadEstudiantes = ($asignacion->grado->numero_estudiantes ?? 0);

            return $prioridadHoras + $prioridadTipo + $prioridadEstudiantes;
        })->values();
    }

    /**
     * ========================================
     * PROCESAR UNA ASIGNACIÓN
     * ========================================
     */
    protected function procesarAsignacion($asignacion): array
    {
        $profesorId = $asignacion->profesor_id;
        $gradoId = $asignacion->grado_id;
        $institucion = $asignacion->institucion ?? 'colegio';
        $horasNecesarias = $asignacion->horas_asignadas;

        // 1. Obtener la configuración de bloques según institución/nivel
        $configKey = $this->obtenerConfigKey($institucion, $asignacion->grado->nivel, $asignacion->grado->turno);
        $configBloques = $this->config[$configKey] ?? null;

        if (!$configBloques) {
            return [
                'creado' => false,
                'horas_asignadas' => 0,
                'motivo' => "No hay configuración de bloques para {$institucion}/{$asignacion->grado->nivel}/{$asignacion->grado->turno}"
            ];
        }

        // 2. Obtener disponibilidad del profesor
        $disponibilidades = $this->obtenerDisponibilidadProfesor($profesorId, $institucion);

        if ($disponibilidades->isEmpty()) {
            return [
                'creado' => false,
                'horas_asignadas' => 0,
                'motivo' => 'El profesor no tiene disponibilidad registrada'
            ];
        }

        // 3. Obtener aulas adecuadas
        $aulas = $this->obtenerAulasParaGrado($asignacion->grado, $institucion);

        if ($aulas->isEmpty()) {
            return [
                'creado' => false,
                'horas_asignadas' => 0,
                'motivo' => 'No hay aulas disponibles para este grado'
            ];
        }

        // 4. Asignar bloques horarios usando la configuración
        $bloquesAsignados = $this->asignarBloquesConConfiguracion(
            $asignacion,
            $configBloques,
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

        // 5. Crear registros de horario
        $this->crearRegistrosHorario($asignacion, $bloquesAsignados);

        return [
            'creado' => true,
            'horas_asignadas' => count($bloquesAsignados),
        ];
    }

    /**
     * ========================================
     * ASIGNAR BLOQUES USANDO CONFIGURACIÓN
     * ========================================
     * Usa los bloques definidos en config/horarios.php
     * respetando recesos y duración.
     */
    protected function asignarBloquesConConfiguracion(
        $asignacion,
        array $configBloques,
        Collection $disponibilidades,
        Collection $aulas
    ): array {
        $bloquesAsignados = [];
        $horasRestantes = $asignacion->horas_asignadas;
        $profesorId = $asignacion->profesor_id;
        $gradoId = $asignacion->grado_id;

        // Días disponibles del profesor (ordenados)
        $diasDisponibles = $disponibilidades->pluck('dia_semana')->unique()->values();

        foreach ($diasDisponibles as $dia) {
            if ($horasRestantes <= 0) break;

            // Verificar si el grado ya tiene clases ese día
            if ($this->gradoTieneClasesEseDia($gradoId, $dia, $asignacion->institucion)) {
                continue;
            }

            // Obtener bloques de clase (excluyendo recesos)
            $bloquesClase = collect($configBloques['bloques'])
                ->filter(fn($b) => !isset($b['receso']))
                ->values();

            foreach ($bloquesClase as $bloque) {
                if ($horasRestantes <= 0) break;

                $horaInicio = $bloque['inicio'];
                $horaFin = $bloque['fin'];

                // Verificar disponibilidad del profesor en este bloque
                if (!$this->profesorDisponibleEnBloque($disponibilidades, $dia, $horaInicio, $horaFin)) {
                    continue;
                }

                // Verificar que no esté ocupado
                if ($this->horaOcupada($profesorId, $gradoId, $dia, $horaInicio)) {
                    continue;
                }

                // Buscar aula disponible
                $aulaAsignada = $this->buscarAulaDisponible($aulas, $dia, $horaInicio, $gradoId);

                if (!$aulaAsignada) {
                    continue;
                }

                // Asignar bloque
                $bloquesAsignados[] = [
                    'dia_semana' => $dia,
                    'hora_inicio' => $horaInicio,
                    'hora_fin' => $horaFin,
                    'aula_id' => $aulaAsignada->id,
                    'numero_bloque' => $bloque['numero'] ?? null,
                ];

                // Marcar como ocupado
                $this->marcarOcupado($profesorId, $gradoId, $aulaAsignada->id, $dia, $horaInicio);
                $horasRestantes--;
            }
        }

        return $bloquesAsignados;
    }

    /**
     * Verificar disponibilidad del profesor en un bloque
     */
    protected function profesorDisponibleEnBloque(Collection $disponibilidades, string $dia, string $horaInicio, string $horaFin): bool
    {
        $bloquesDia = $disponibilidades->where('dia_semana', $dia);

        foreach ($bloquesDia as $disp) {
            $dispInicio = Carbon::parse($disp->hora_inicio);
            $dispFin = Carbon::parse($disp->hora_fin);
            $bloqueInicio = Carbon::parse($horaInicio);
            $bloqueFin = Carbon::parse($horaFin);

            // El bloque completo debe estar dentro de la disponibilidad
            if ($bloqueInicio >= $dispInicio && $bloqueFin <= $dispFin) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtener la clave de configuración según institución/nivel/turno
     */
    protected function obtenerConfigKey(string $institucion, string $nivel, string $turno): string
    {
        if ($institucion === 'colegio') {
            return "colegio_{$nivel}";
        }

        if ($institucion === 'academia') {
            if ($turno === 'completo') {
                return 'academia_completo';
            }
            return "academia_{$turno}";
        }

        return "colegio_{$nivel}";
    }

    /**
     * Verificar si el grado ya tiene clases ese día
     */
    protected function gradoTieneClasesEseDia(int $gradoId, string $dia, string $institucion): bool
    {
        return Horario::where('grado_id', $gradoId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('estado', 'activo')
            ->exists();
    }

    /**
     * Obtener disponibilidad del profesor por institución
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
     * Obtener aulas adecuadas para un grado
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
     * Buscar un aula disponible
     */
    protected function buscarAulaDisponible(Collection $aulas, string $dia, string $hora, int $gradoId): ?Aula
    {
        foreach ($aulas as $aula) {
            $clave = $aula->id . '_' . $dia . '_' . $hora;

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
        $claveProfesor = $profesorId . '_' . $dia . '_' . $hora;
        $claveGrado = $gradoId . '_' . $dia . '_' . $hora;

        return isset($this->profesoresOcupados[$claveProfesor])
            || isset($this->gradosOcupados[$claveGrado]);
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
