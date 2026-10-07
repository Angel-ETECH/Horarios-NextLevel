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

    public function __construct(
        private HistorialService $historialService
    ) {
    }

    /**
     * ========================================
     * MÉTODO PRINCIPAL
     * ========================================
     */
    public function generarHorarios(array $opciones = []): array
    {
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
                    $horariosCreados += $resultado['horarios_creados'] ?? $resultado['horas_asignadas'];
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
                $horario->hora_inicio,
                $horario->hora_fin
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
        $horasRequeridas = $asignacion->horas_asignadas;
        $horasExistentes = Horario::where('estado', 'activo')
            ->where('profesor_id', $profesorId)
            ->where('curso_id', $asignacion->curso_id)
            ->where('grado_id', $gradoId)
            ->where('institucion', $institucion)
            ->count();
        $horasNecesarias = max(0, $horasRequeridas - $horasExistentes);

        if ($horasNecesarias === 0) {
            return [
                'creado' => false,
                'horas_asignadas' => $horasRequeridas,
                'horarios_creados' => 0,
                'motivo' => 'La asignación ya tiene sus horas programadas'
            ];
        }

        // 1. Obtener configuraciones desde BD
        $configuraciones = $this->obtenerConfiguracionesCandidatas(
            $institucion,
            $asignacion->grado->nivel,
            $asignacion->grado->turno
        );

        if ($configuraciones->isEmpty()) {
            return [
                'creado' => false,
                'horas_asignadas' => $horasExistentes,
                'horarios_creados' => 0,
                'motivo' => "No hay configuración activa para {$institucion}/{$asignacion->grado->nivel}/{$asignacion->grado->turno}"
            ];
        }

        // 2. Disponibilidad del profesor
        $disponibilidades = $this->obtenerDisponibilidadProfesor($profesorId, $institucion);

        if ($disponibilidades->isEmpty()) {
            return [
                'creado' => false,
                'horas_asignadas' => $horasExistentes,
                'horarios_creados' => 0,
                'motivo' => 'El profesor no tiene disponibilidad registrada'
            ];
        }

        if (!$asignacion->aula_id) {
            return [
                'creado' => false,
                'horas_asignadas' => $horasExistentes,
                'horarios_creados' => 0,
                'motivo' => 'La asignación no tiene aula definida'
            ];
        }

        // 3. Aula definida en la asignación
        $aulas = $this->obtenerAulasParaAsignacion($asignacion);

        if ($aulas->isEmpty()) {
            return [
                'creado' => false,
                'horas_asignadas' => $horasExistentes,
                'horarios_creados' => 0,
                'motivo' => 'El aula asignada no está activa'
            ];
        }

        // 4. Asignar bloques. Se intenta primero el turno exacto y luego
        // alternativas del mismo nivel cuando la disponibilidad no coincide.
        $bloquesAsignados = [];
        $horasPendientes = $horasNecesarias;

        foreach ($configuraciones as $config) {
            $bloquesConfig = $this->asignarBloquesConConfiguracion(
                $asignacion,
                $config,
                $disponibilidades,
                $aulas,
                $horasPendientes
            );

            if (!empty($bloquesConfig)) {
                $bloquesAsignados = array_merge($bloquesAsignados, $bloquesConfig);
                $horasPendientes -= count($bloquesConfig);
            }

            if ($horasPendientes <= 0) {
                break;
            }
        }

        if (empty($bloquesAsignados)) {
            return [
                'creado' => false,
                'horas_asignadas' => $horasExistentes,
                'horarios_creados' => 0,
                'motivo' => 'No se encontraron bloques horarios disponibles'
            ];
        }

        // 5. Crear registros
        $this->crearRegistrosHorario($asignacion, $bloquesAsignados);

        return [
            'creado' => true,
            'horas_asignadas' => $horasExistentes + count($bloquesAsignados),
            'horarios_creados' => count($bloquesAsignados),
        ];
    }

    /**
     * Obtener configuración desde BD (con cache)
     */
    protected function obtenerConfiguracion(string $institucion, string $nivel, string $turno): ?ConfiguracionHorario
    {
        return $this->obtenerConfiguracionesCandidatas($institucion, $nivel, $turno)->first();
    }

    /**
     * Obtener configuraciones candidatas desde BD (con cache)
     */
    protected function obtenerConfiguracionesCandidatas(string $institucion, string $nivel, string $turno): Collection
    {
        $key = "{$institucion}_{$nivel}_{$turno}";

        if (isset($this->configuracionesCache[$key])) {
            return $this->configuracionesCache[$key];
        }

        $prioridadTurnos = collect([$turno, 'completo', 'tarde', 'mañana'])
            ->filter()
            ->unique()
            ->values();

        $configuraciones = ConfiguracionHorario::with('bloques')
            ->where('institucion', $institucion)
            ->where('nivel', $nivel)
            ->where('activo', true)
            ->whereIn('turno', $prioridadTurnos)
            ->orderBy('año_academico', 'desc')
            ->get()
            ->sortBy(fn ($config) => $prioridadTurnos->search($config->turno))
            ->unique('id')
            ->values();

        $this->configuracionesCache[$key] = $configuraciones;

        return $configuraciones;
    }

    /**
     * Asignar bloques usando la configuración de BD
     */
    protected function asignarBloquesConConfiguracion(
        $asignacion,
        ConfiguracionHorario $config,
        Collection $disponibilidades,
        Collection $aulas,
        int $horasObjetivo
    ): array {
        $bloquesAsignados = [];
        $horasRestantes = $horasObjetivo;
        $profesorId = $asignacion->profesor_id;
        $gradoId = $asignacion->grado_id;

        // Obtener bloques de clase sin huecos antes o después de recesos.
        $bloquesClase = $this->obtenerBloquesClaseContinuos($config);

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
                if ($this->horaOcupada($profesorId, $gradoId, $dia, $horaInicio, $horaFin)) {
                    continue;
                }

                // Buscar aula
                $aulaAsignada = $this->buscarAulaDisponible($aulas, $dia, $horaInicio, $horaFin);

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

                $this->marcarOcupado($profesorId, $gradoId, $aulaAsignada->id, $dia, $horaInicio, $horaFin);
                $horasRestantes--;
            }
        }

        return $bloquesAsignados;
    }

    protected function obtenerBloquesClaseContinuos(ConfiguracionHorario $config): Collection
    {
        $bloquesGuardados = $config->bloques->sortBy('orden')->values();

        if (
            $bloquesGuardados->isNotEmpty()
            && $this->bloquesSonContinuos($config, $bloquesGuardados)
        ) {
            return $config->bloquesClase;
        }

        return $this->generarBloquesClaseDesdeConfiguracion($config);
    }

    protected function bloquesSonContinuos(ConfiguracionHorario $config, Collection $bloques): bool
    {
        if ($bloques->isEmpty()) {
            return false;
        }

        if ($this->minutosHorario($bloques->first()->hora_inicio) !== $this->minutosHorario($config->hora_inicio)) {
            return false;
        }

        if ($this->minutosHorario($bloques->last()->hora_fin) !== $this->minutosHorario($config->hora_fin)) {
            return false;
        }

        for ($index = 1; $index < $bloques->count(); $index++) {
            $anterior = $bloques[$index - 1];
            $actual = $bloques[$index];

            if ($this->minutosHorario($actual->hora_inicio) !== $this->minutosHorario($anterior->hora_fin)) {
                return false;
            }
        }

        return true;
    }

    protected function generarBloquesClaseDesdeConfiguracion(ConfiguracionHorario $config): Collection
    {
        $recesos = $this->recesosParaGeneracion($config);
        $bloques = collect();
        $cursor = $this->minutosHorario($config->hora_inicio);
        $finTurno = $this->minutosHorario($config->hora_fin);
        $duracionClase = max(30, (int) ($config->duracion_bloque_minutos ?: 45));
        $numeroBloque = 1;

        while ($cursor < $finTurno) {
            $recesoActual = $recesos->first(
                fn ($receso) => $receso['inicio_minutos'] === $cursor
            );

            if ($recesoActual) {
                $cursor = $recesoActual['fin_minutos'];
                continue;
            }

            $siguienteReceso = $recesos->first(
                fn ($receso) => $receso['inicio_minutos'] > $cursor
            );

            $finClase = min($cursor + $duracionClase, $finTurno);

            if ($siguienteReceso && $finClase > $siguienteReceso['inicio_minutos']) {
                $finClase = $siguienteReceso['inicio_minutos'];
            }

            if ($finClase <= $cursor) {
                $cursor = $siguienteReceso
                    ? $siguienteReceso['inicio_minutos']
                    : $finTurno;
                continue;
            }

            $bloques->push((object) [
                'hora_inicio' => Carbon::createFromFormat('H:i', $this->horaDesdeMinutos($cursor)),
                'hora_fin' => Carbon::createFromFormat('H:i', $this->horaDesdeMinutos($finClase)),
                'numero_bloque' => $numeroBloque,
            ]);

            $numeroBloque++;
            $cursor = $finClase;
        }

        return $bloques;
    }

    protected function recesosParaGeneracion(ConfiguracionHorario $config): Collection
    {
        $recesos = $config->bloquesReceso
            ->map(fn ($receso) => [
                'hora_inicio' => $this->horaDesdeValor($receso->hora_inicio),
                'hora_fin' => $this->horaDesdeValor($receso->hora_fin),
            ]);

        if ($recesos->isEmpty()) {
            $base = match ($config->turno) {
                'mañana' => array_slice(HorarioRecesoService::RECESOS_BASE, 0, 2),
                'tarde' => array_slice(HorarioRecesoService::RECESOS_BASE, 2, 2),
                default => HorarioRecesoService::RECESOS_BASE,
            };

            $recesos = collect($base);
        }

        return $recesos
            ->map(fn ($receso) => [
                'hora_inicio' => $receso['hora_inicio'],
                'hora_fin' => $receso['hora_fin'],
                'inicio_minutos' => $this->minutosHorario($receso['hora_inicio']),
                'fin_minutos' => $this->minutosHorario($receso['hora_fin']),
            ])
            ->sortBy('inicio_minutos')
            ->values();
    }

    protected function minutosHorario($valor): int
    {
        $hora = $this->horaDesdeValor($valor);
        [$horas, $minutos] = array_map('intval', explode(':', $hora));

        return ($horas * 60) + $minutos;
    }

    protected function horaDesdeValor($valor): string
    {
        return Carbon::parse($valor)->format('H:i');
    }

    protected function horaDesdeMinutos(int $minutos): string
    {
        return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
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
        $query = ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])
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
    protected function obtenerAulasParaAsignacion($asignacion): Collection
    {
        return Aula::where('id', $asignacion->aula_id)
            ->where('activo', true)
            ->get();
    }

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
    protected function buscarAulaDisponible(Collection $aulas, string $dia, string $horaInicio, string $horaFin): ?Aula
    {
        foreach ($aulas as $aula) {
            if (!$this->estaOcupado($this->aulasOcupadas, $aula->id, $dia, $horaInicio, $horaFin)) {
                return $aula;
            }
        }
        return null;
    }

    /**
     * Verificar si una hora está ocupada
     */
    protected function horaOcupada(
        int $profesorId,
        int $gradoId,
        string $dia,
        string $horaInicio,
        string $horaFin
    ): bool
    {
        return $this->estaOcupado($this->profesoresOcupados, $profesorId, $dia, $horaInicio, $horaFin)
            || $this->estaOcupado($this->gradosOcupados, $gradoId, $dia, $horaInicio, $horaFin);
    }

    /**
     * Marcar como ocupado
     */
    protected function marcarOcupado(
        int $profesorId,
        int $gradoId,
        int $aulaId,
        string $dia,
        string $horaInicio,
        string $horaFin
    ): void
    {
        $this->registrarOcupacion($this->profesoresOcupados, $profesorId, $dia, $horaInicio, $horaFin);
        $this->registrarOcupacion($this->gradosOcupados, $gradoId, $dia, $horaInicio, $horaFin);
        $this->registrarOcupacion($this->aulasOcupadas, $aulaId, $dia, $horaInicio, $horaFin);
    }

    protected function estaOcupado(array $ocupados, int $entidadId, string $dia, string $horaInicio, string $horaFin): bool
    {
        $clave = "{$entidadId}_{$dia}";
        $inicio = Carbon::parse($horaInicio);
        $fin = Carbon::parse($horaFin);

        foreach ($ocupados[$clave] ?? [] as $rango) {
            if ($inicio->lt(Carbon::parse($rango['fin'])) && $fin->gt(Carbon::parse($rango['inicio']))) {
                return true;
            }
        }

        return false;
    }

    protected function registrarOcupacion(
        array &$ocupados,
        int $entidadId,
        string $dia,
        string $horaInicio,
        string $horaFin
    ): void {
        $clave = "{$entidadId}_{$dia}";

        $ocupados[$clave][] = [
            'inicio' => $this->normalizarHoraClave($horaInicio),
            'fin' => $this->normalizarHoraClave($horaFin),
        ];
    }

    protected function normalizarHoraClave($hora): string
    {
        if ($hora instanceof Carbon) {
            return $hora->format('H:i');
        }

        try {
            return Carbon::parse($hora)->format('H:i');
        } catch (\Throwable $e) {
            return substr((string) $hora, 0, 5);
        }
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

            $horario->load(['profesor', 'curso', 'grado', 'aula']);

            $this->historialService->registrarCambio(
                $horario->id,
                'crear',
                null,
                $this->snapshotHorario($horario),
                'Horario generado automáticamente'
            );

            $horariosCreados[] = $horario;
        }

        return $horariosCreados;
    }

    private function snapshotHorario(Horario $horario): array
    {
        $horario->loadMissing(['profesor', 'curso', 'grado', 'aula']);

        return array_merge($horario->toArray(), [
            'modulo' => 'horarios',
            'tipo_registro' => 'horario',
            'profesor_nombre' => $horario->profesor?->nombre_completo,
            'curso_nombre' => $horario->curso?->nombre,
            'grado_nombre' => $horario->grado?->nombre_completo,
            'aula_nombre' => $horario->aula?->nombre,
        ]);
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
