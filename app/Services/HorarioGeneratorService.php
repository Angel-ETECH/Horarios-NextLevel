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
    protected string $periodoAcademico;

    public function __construct(
        private HistorialService $historialService,
        private DisponibilidadService $disponibilidadService
    ) {
    }

    /**
     * ========================================
     * MÉTODO PRINCIPAL
     * ========================================
     */
    public function generarHorarios(array $opciones = []): array
    {
        $this->aulasOcupadas = [];
        $this->profesoresOcupados = [];
        $this->gradosOcupados = [];
        $this->conflictos = [];
        $this->asignacionesIncompletas = [];
        $this->estadisticas = [];
        $this->configuracionesCache = [];
        $this->periodoAcademico = $opciones['periodo_academico'] ?? (date('Y') . '-' . (date('Y') + 1));
        $opciones['periodo_academico'] = $this->periodoAcademico;
        DB::beginTransaction();

        try {
            app(RecuperacionHorarioService::class)->bloquearAgenda();
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
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
        if (app(HorarioCalendarioService::class)->instalado()) {
            $reservas = \App\Models\RecuperacionHorario::where('estado', 'programada')->whereDate('fecha_recuperacion', '>=', today())
                ->whereHas('horario', fn ($q) => $q->where('estado', 'activo')->when(!empty($opciones['periodo_academico']), fn ($q) => $q->where('periodo_academico', $opciones['periodo_academico'])))->get();
            foreach ($reservas as $r) {
                $this->marcarOcupado($r->original['profesor_id'], $r->original['grado_id'], $r->aula_id,
                    HorarioCalendarioService::DIAS[$r->fecha_recuperacion->dayOfWeekIso], $r->hora_inicio, $r->hora_fin);
            }
        }
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
            return ($asignacion->modo_programacion === 'manual' ? 100000 : 0) + $prioridadHoras + $prioridadTipo + $prioridadEstudiantes;
        })->values();
    }

    /**
     * Procesar una asignación
     */
    public function opcionesManuales(array $data): array
    {
        $grado = \App\Models\Grado::findOrFail($data['grado_id']);
        $periodo = $data['periodo_academico'] ?? (date('Y').'-'.(date('Y') + 1));
        $this->configuracionesCache = [];
        $configs = $this->obtenerConfiguracionesCandidatas($data['institucion'], $grado->nivel, $grado->turno);
        $disponibilidad = $this->obtenerDisponibilidadProfesor($data['profesor_id'], $data['institucion']);
        $existentes = Horario::where('estado', 'activo')->where('periodo_academico', $periodo)->get();
        $asignacion = !empty($data['asignacion_id']) ? ProfesorCurso::find($data['asignacion_id']) : null;
        if ($asignacion) {
            $existentes = $existentes->reject(fn ($h) =>
                $h->profesor_id === $asignacion->profesor_id && $h->curso_id === $asignacion->curso_id &&
                $h->grado_id === $asignacion->grado_id && $h->institucion === $asignacion->institucion);
        }
        $opciones = [];
        foreach (['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'] as $dia) {
            foreach ($configs as $config) {
                foreach ($this->obtenerBloquesClaseContinuos($config) as $bloque) {
                    $inicio = $bloque->hora_inicio->format('H:i');
                    $fin = $bloque->hora_fin->format('H:i');
                    // Preferir el bloque del turno del grado cuando otra jornada tiene el mismo inicio.
                    if (isset($opciones[$dia.'|'.$inicio])) continue;
                    if (!$this->profesorDisponibleEnBloque($disponibilidad, $dia, $inicio, $fin)) continue;
                    $motivo = null;
                    foreach ($existentes as $h) {
                        if ($h->dia_semana !== $dia || $h->hora_inicio->format('H:i') >= $fin || $h->hora_fin->format('H:i') <= $inicio) continue;
                        if ((int) $h->profesor_id === (int) $data['profesor_id']) $motivo = 'Profesor ocupado';
                        elseif ((int) $h->aula_id === (int) $data['aula_id']) $motivo = 'Aula ocupada';
                        elseif ((int) $h->grado_id === (int) $data['grado_id']) $motivo = 'Grado ocupado';
                        if ($motivo) break;
                    }
                    if (!$motivo) {
                        try {
                            app(RecuperacionHorarioService::class)->validarNuevaPlantilla(array_merge($data, [
                                'dia_semana' => $dia, 'hora_inicio' => $inicio, 'hora_fin' => $fin, 'periodo_academico' => $periodo,
                            ]));
                        } catch (\Illuminate\Validation\ValidationException $e) {
                            $motivo = 'Reservado para una recuperación';
                        }
                    }
                    $opciones[$dia.'|'.$inicio] = [
                        'dia_semana' => $dia, 'hora_inicio' => $inicio, 'hora_fin' => $fin,
                        'disponible' => $motivo === null, 'motivo' => $motivo,
                    ];
                }
            }
        }
        return array_values($opciones);
    }

    private function procesarManual(ProfesorCurso $asignacion): array
    {
        $plan = $asignacion->bloques_manuales ?? [];
        if (count($plan) !== $asignacion->horas_asignadas) {
            throw \Illuminate\Validation\ValidationException::withMessages(['bloques_manuales' => 'Completa la selección manual de esta asignación.']);
        }
        $opciones = collect($this->opcionesManuales(array_merge($asignacion->toArray(), [
            'asignacion_id' => $asignacion->id, 'periodo_academico' => $this->periodoAcademico,
        ])))->keyBy(fn ($b) => $b['dia_semana'].'|'.$b['hora_inicio'].'|'.$b['hora_fin']);
        $creados = 0;
        foreach ($plan as $bloque) {
            $clave = $bloque['dia_semana'].'|'.$bloque['hora_inicio'].'|'.$bloque['hora_fin'];
            $opcion = $opciones->get($clave);
            if (!$opcion || !$opcion['disponible']) {
                $motivo = $opcion['motivo'] ?? 'Fuera de la disponibilidad o de los bloques lectivos del grado';
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'bloques_manuales' => ucfirst($bloque['dia_semana']).' '.$bloque['hora_inicio'].'–'.$bloque['hora_fin'].': '.$motivo.'. No se guardó ningún cambio.',
                ]);
            }
            $existe = Horario::where('profesor_id', $asignacion->profesor_id)->where('curso_id', $asignacion->curso_id)
                ->where('grado_id', $asignacion->grado_id)->where('institucion', $asignacion->institucion)
                ->where('periodo_academico', $this->periodoAcademico)->where('estado', 'activo')
                ->where('dia_semana', $bloque['dia_semana'])->get()->contains(fn ($h) =>
                    $h->hora_inicio->format('H:i') === $bloque['hora_inicio'] && $h->hora_fin->format('H:i') === $bloque['hora_fin']);
            if ($existe) continue;
            $h = app(HorarioService::class)->create(array_merge($bloque, [
                'profesor_id' => $asignacion->profesor_id, 'curso_id' => $asignacion->curso_id,
                'grado_id' => $asignacion->grado_id, 'aula_id' => $asignacion->aula_id,
                'institucion' => $asignacion->institucion, 'periodo_academico' => $this->periodoAcademico,
                'turno' => $this->determinarTurno($bloque['hora_inicio']), 'tipo' => 'regular',
                'semana' => 1, 'estado' => 'activo', 'version' => 1, 'creado_por' => 'asignacion_manual',
            ]));
            $this->historialService->registrarCambio($h->id, 'crear', null, $this->snapshotHorario($h), 'Horario elegido en la asignación');
            $this->marcarOcupado($h->profesor_id, $h->grado_id, $h->aula_id, $h->dia_semana, $h->hora_inicio, $h->hora_fin);
            $creados++;
        }
        return ['creado' => $creados > 0, 'horas_asignadas' => count($plan), 'horarios_creados' => $creados];
    }

    protected function procesarAsignacion($asignacion): array
    {
        if ($asignacion->modo_programacion === 'manual') {
            return $this->procesarManual($asignacion);
        }
        $profesorId = $asignacion->profesor_id;
        $gradoId = $asignacion->grado_id;
        $institucion = $asignacion->institucion ?? 'colegio';
        $horasRequeridas = $asignacion->horas_asignadas;
        $horasExistentes = Horario::where('estado', 'activo')
            ->where('periodo_academico', $this->periodoAcademico)
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
            ->map(fn ($config) => app(HorarioRecesoService::class)->normalizarConfiguracion($config))
            ->sortBy(fn ($config) => $prioridadTurnos->search($config->turno))
            ->unique('turno')
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
        $diasDisponibles = $disponibilidades->pluck('dia_semana')->unique()
            ->intersect(['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'])->values();

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
        return app(HorarioRecesoService::class)->bloquesFijos($config)
            ->where('tipo', 'clase')->values();
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

        return $this->disponibilidadService->rangoEstaCubierto($bloquesDia, $horaInicio, $horaFin);
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
            ->where('activo', true)
            ->whereHas('profesor', fn ($query) => $query->where('estado', 'activo'))
            ->whereHas('curso', fn ($query) => $query->where('activo', true))
            ->whereHas('grado', fn ($query) => $query->where('activo', true));

        if (!empty($opciones['asignacion_id'])) {
            $query->whereKey($opciones['asignacion_id']);
        }
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
            ->whereIn('institucion', [$asignacion->institucion ?? 'colegio', 'ambos'])
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
        $periodoAcademico = $this->periodoAcademico;

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
