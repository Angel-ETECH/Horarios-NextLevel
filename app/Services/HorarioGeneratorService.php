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
    protected array $diasSemana = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    protected array $horasDisponibles = [];
    protected array $aulasOcupadas = [];
    protected array $profesoresOcupados = [];
    protected array $gradosOcupados = [];
    protected array $asignacionesPendientes = [];
    protected array $conflictos = [];
    protected array $estadisticas = [];

    /**
     * Configurar el generador
     */
    public function __construct()
    {
        // Generar bloques horarios de 1 hora (07:00 a 18:00)
        for ($hora = 7; $hora < 18; $hora++) {
            $this->horasDisponibles[] = sprintf('%02d:00', $hora);
        }
    }

    /**
     * Método principal: Generar todos los horarios
     */
    public function generarHorarios(): array
    {
        Log::info('Iniciando generación de horarios...');

        DB::beginTransaction();

        try {
            // 1. Limpiar horarios anteriores (opcional)
            // Horario::where('estado', 'activo')->update(['estado' => 'cancelado']);

            // 2. Obtener todas las asignaciones activas
            $asignaciones = $this->obtenerAsignaciones();
            $this->estadisticas['total_asignaciones'] = $asignaciones->count();

            // 3. Obtener disponibilidades de profesores
            $disponibilidades = $this->obtenerDisponibilidades();

            // 4. Obtener aulas disponibles
            $aulas = $this->obtenerAulas();

            // 5. Procesar cada asignación
            $horariosCreados = 0;
            $this->asignacionesPendientes = $asignaciones->toArray();

            // Ordenar asignaciones: primero las que tienen más horas (prioridad)
            $asignacionesOrdenadas = $asignaciones->sortByDesc(function($asignacion) {
                return $asignacion->horas_asignadas;
            });

            foreach ($asignacionesOrdenadas as $asignacion) {
                $resultado = $this->procesarAsignacion($asignacion, $disponibilidades, $aulas);

                if ($resultado['creado']) {
                    $horariosCreados++;
                    $this->estadisticas['horarios_creados'] = $horariosCreados;
                } else {
                    $this->conflictos[] = [
                        'asignacion_id' => $asignacion->id,
                        'profesor' => $asignacion->profesor->nombre_completo ?? 'N/A',
                        'curso' => $asignacion->curso->nombre ?? 'N/A',
                        'grado' => $asignacion->grado->nombre_completo ?? 'N/A',
                        'motivo' => $resultado['motivo'] ?? 'No se pudo asignar',
                    ];
                }
            }

            $this->estadisticas['total_conflictos'] = count($this->conflictos);
            $this->estadisticas['asignaciones_pendientes'] = count($this->asignacionesPendientes);

            DB::commit();

            Log::info('Generación de horarios completada', $this->estadisticas);

            return [
                'success' => true,
                'estadisticas' => $this->estadisticas,
                'conflictos' => $this->conflictos,
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
     * Obtener todas las asignaciones activas
     */
    protected function obtenerAsignaciones(): Collection
    {
        return ProfesorCurso::with(['profesor', 'curso', 'grado', 'grado.alumnos'])
            ->where('activo', true)
            ->get();
    }

    /**
     * Obtener disponibilidades de profesores
     */
    protected function obtenerDisponibilidades(): Collection
    {
        return DisponibilidadProfesor::with('profesor')
            ->where('tipo', 'disponible')
            ->get()
            ->groupBy('profesor_id');
    }

    /**
     * Obtener aulas activas
     */
    protected function obtenerAulas(): Collection
    {
        return Aula::where('activo', true)
            ->orderBy('capacidad', 'desc')
            ->get();
    }

    /**
     * Procesar una asignación individual
     */
    protected function procesarAsignacion($asignacion, $disponibilidades, $aulas): array
    {
        $profesorId = $asignacion->profesor_id;
        $cursoId = $asignacion->curso_id;
        $gradoId = $asignacion->grado_id;
        $horasNecesarias = $asignacion->horas_asignadas;

        // Verificar que el profesor tenga disponibilidad
        if (!$this->profesorTieneDisponibilidad($profesorId, $disponibilidades)) {
            return [
                'creado' => false,
                'motivo' => 'El profesor no tiene disponibilidad registrada'
            ];
        }

        // Obtener disponibilidad del profesor
        $disponibilidadProfesor = $disponibilidades[$profesorId] ?? collect();

        // Obtener aulas adecuadas para este grado
        $aulasDisponibles = $this->obtenerAulasParaGrado($aulas, $asignacion->grado);

        if ($aulasDisponibles->isEmpty()) {
            return [
                'creado' => false,
                'motivo' => 'No hay aulas disponibles para este grado'
            ];
        }

        // Intentar asignar los bloques horarios
        $bloquesAsignados = $this->asignarBloquesHorarios(
            $profesorId,
            $gradoId,
            $horasNecesarias,
            $disponibilidadProfesor,
            $aulasDisponibles
        );

        if (empty($bloquesAsignados)) {
            return [
                'creado' => false,
                'motivo' => 'No se encontraron bloques horarios disponibles'
            ];
        }

        // Crear los registros de horario
        $horariosCreados = $this->crearRegistrosHorario(
            $asignacion,
            $bloquesAsignados
        );

        // Marcar como procesado
        $this->asignacionesPendientes = array_filter($this->asignacionesPendientes, function($item) use ($asignacion) {
            return $item['id'] !== $asignacion->id;
        });

        return [
            'creado' => true,
            'horarios' => $horariosCreados,
        ];
    }

    /**
     * Verificar si un profesor tiene disponibilidad
     */
    protected function profesorTieneDisponibilidad($profesorId, $disponibilidades): bool
    {
        return isset($disponibilidades[$profesorId]) && $disponibilidades[$profesorId]->isNotEmpty();
    }

    /**
     * Obtener aulas adecuadas para un grado específico
     */
    protected function obtenerAulasParaGrado($aulas, $grado): Collection
    {
        $capacidadNecesaria = $grado->numero_estudiantes ?? 0;

        return $aulas->filter(function($aula) use ($capacidadNecesaria, $grado) {
            // Verificar capacidad
            if ($aula->capacidad < $capacidadNecesaria) {
                return false;
            }

            // Verificar nivel
            if ($aula->nivel !== 'todos' && $aula->nivel !== $grado->nivel) {
                return false;
            }

            return true;
        });
    }

    /**
     * Asignar bloques horarios para una asignación
     */
    protected function asignarBloquesHorarios(
        int $profesorId,
        int $gradoId,
        int $horasNecesarias,
        Collection $disponibilidadProfesor,
        Collection $aulasDisponibles
    ): array {
        $bloquesAsignados = [];
        $horasRestantes = $horasNecesarias;
        $intentos = 0;
        $maxIntentos = 50;

        // Intentar en diferentes días
        $diasDisponibles = $disponibilidadProfesor->pluck('dia_semana')->unique()->shuffle();

        foreach ($diasDisponibles as $dia) {
            if ($horasRestantes <= 0) break;

            // Obtener bloques de disponibilidad para este día
            $bloquesDia = $disponibilidadProfesor
                ->where('dia_semana', $dia)
                ->sortBy('hora_inicio');

            foreach ($bloquesDia as $bloque) {
                if ($horasRestantes <= 0) break;

                // Obtener horas disponibles dentro del bloque
                $horasBloque = $this->obtenerHorasEnBloque(
                    $bloque->hora_inicio,
                    $bloque->hora_fin,
                    $dia,
                    $profesorId,
                    $gradoId,
                    $aulasDisponibles
                );

                // Asignar horas disponibles
                foreach ($horasBloque as $hora) {
                    if ($horasRestantes <= 0) break;

                    // Buscar un aula disponible para esta hora
                    $aulaAsignada = $this->buscarAulaDisponible(
                        $aulasDisponibles,
                        $dia,
                        $hora,
                        $gradoId
                    );

                    if ($aulaAsignada) {
                        $bloquesAsignados[] = [
                            'dia_semana' => $dia,
                            'hora_inicio' => $hora,
                            'hora_fin' => date('H:i', strtotime($hora . ' +1 hour')),
                            'aula_id' => $aulaAsignada->id,
                        ];

                        // Marcar como ocupado
                        $this->marcarOcupado($profesorId, $gradoId, $aulaAsignada->id, $dia, $hora);
                        $horasRestantes--;
                    }
                }
            }

            $intentos++;
            if ($intentos > $maxIntentos) break;
        }

        return $bloquesAsignados;
    }

    /**
     * Obtener horas disponibles dentro de un bloque
     */
    protected function obtenerHorasEnBloque(
        string $horaInicio,
        string $horaFin,
        string $dia,
        int $profesorId,
        int $gradoId,
        Collection $aulasDisponibles
    ): array {
        $horasDisponibles = [];
        $inicio = Carbon::parse($horaInicio);
        $fin = Carbon::parse($horaFin);

        while ($inicio < $fin) {
            $hora = $inicio->format('H:i');

            // Verificar si esta hora está ocupada
            if (!$this->horaOcupada($profesorId, $gradoId, $dia, $hora)) {
                $horasDisponibles[] = $hora;
            }

            $inicio->addHour();
        }

        return $horasDisponibles;
    }

    /**
     * Buscar un aula disponible para una hora específica
     */
    protected function buscarAulaDisponible(
        Collection $aulas,
        string $dia,
        string $hora,
        int $gradoId
    ): ?Aula {
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
        // Verificar profesor
        $claveProfesor = $profesorId . '_' . $dia . '_' . $hora;
        if (isset($this->profesoresOcupados[$claveProfesor])) {
            return true;
        }

        // Verificar grado
        $claveGrado = $gradoId . '_' . $dia . '_' . $hora;
        if (isset($this->gradosOcupados[$claveGrado])) {
            return true;
        }

        return false;
    }

    /**
     * Marcar como ocupado
     */
    protected function marcarOcupado(int $profesorId, int $gradoId, int $aulaId, string $dia, string $hora): void
    {
        $claveProfesor = $profesorId . '_' . $dia . '_' . $hora;
        $claveGrado = $gradoId . '_' . $dia . '_' . $hora;
        $claveAula = $aulaId . '_' . $dia . '_' . $hora;

        $this->profesoresOcupados[$claveProfesor] = true;
        $this->gradosOcupados[$claveGrado] = true;
        $this->aulasOcupadas[$claveAula] = true;
    }

    /**
     * Crear registros de horario en la base de datos
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
                'tipo' => 'regular',
                'semana' => 1,
                'periodo_academico' => $periodoAcademico,
                'estado' => 'activo',
                'version' => 1,
                'creado_por' => 'sistema_automatico',
                'observacion' => 'Generado automáticamente por el sistema',
            ]);

            $horariosCreados[] = $horario;
        }

        return $horariosCreados;
    }

    /**
     * Determinar el turno según la hora
     */
    protected function determinarTurno(string $hora): string
    {
        $horaInt = (int) explode(':', $hora)[0];

        if ($horaInt < 12) {
            return 'mañana';
        } elseif ($horaInt < 18) {
            return 'tarde';
        } else {
            return 'noche';
        }
    }

    /**
     * Obtener estadísticas detalladas
     */
    public function obtenerEstadisticas(): array
    {
        return [
            'horarios_creados' => $this->estadisticas['horarios_creados'] ?? 0,
            'total_asignaciones' => $this->estadisticas['total_asignaciones'] ?? 0,
            'total_conflictos' => $this->estadisticas['total_conflictos'] ?? 0,
            'asignaciones_pendientes' => $this->estadisticas['asignaciones_pendientes'] ?? 0,
            'tasa_exito' => $this->estadisticas['total_asignaciones'] > 0
                ? round((($this->estadisticas['horarios_creados'] ?? 0) / $this->estadisticas['total_asignaciones']) * 100, 2)
                : 0,
        ];
    }

    /**
     * Obtener conflictos registrados
     */
    public function obtenerConflictos(): array
    {
        return $this->conflictos;
    }
}
