<?php
// app/Http/Controllers/Api/ConfiguracionHorarioController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionHorario;
use App\Models\Horario;
use App\Models\ProfesorCurso;
use App\Services\HistorialService;
use App\Services\HorarioGeneratorService;
use App\Services\HorarioRecesoService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionHorarioController extends Controller
{
    public function __construct(
        private HorarioRecesoService $recesoService,
        private HorarioGeneratorService $generatorService,
        private HistorialService $historialService
    ) {
    }

    /**
     * GET /api/configuraciones-horario
     */
    public function index(Request $request): JsonResponse
    {
        $query = ConfiguracionHorario::with('bloques');

        if ($request->has('institucion')) {
            $query->where('institucion', $request->institucion);
        }

        if ($request->has('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        if ($request->has('turno')) {
            $query->where('turno', $request->turno);
        }

        if ($request->has('año_academico')) {
            $query->where('año_academico', $request->año_academico);
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('institucion')
                           ->orderBy('nivel')
                           ->orderBy('turno')
                           ->get(),
        ]);
    }

    /**
     * GET /api/configuraciones-horario/{id}
     */
    public function show(int $id): JsonResponse
    {
        $config = ConfiguracionHorario::with('bloques')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $config,
        ]);
    }

    /**
     * POST /api/configuraciones-horario
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'required|in:colegio,academia',
            'nivel' => 'required|in:primaria,secundaria,academia',
            'turno' => 'required|in:mañana,tarde,completo',
            'nombre' => 'required|string|max:100',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_bloque_minutos' => 'required|integer|min:30|max:120',
            'año_academico' => 'required|integer|min:2020|max:2100',
            'bloques' => 'required|array|min:1',
            'bloques.*.orden' => 'required|integer',
            'bloques.*.hora_inicio' => 'required|date_format:H:i',
            'bloques.*.hora_fin' => 'required|date_format:H:i',
            'bloques.*.tipo' => 'required|in:clase,receso',
        ]);

        $this->recesoService->validarBloques(
            $request->input('bloques', []),
            $request->input('turno')
        );

        $config = ConfiguracionHorario::create($request->except('bloques'));

        foreach ($request->bloques as $bloque) {
            $config->bloques()->create($bloque);
        }

        $reprogramacion = $this->reprogramarHorariosAfectados(
            $config->fresh()->load('bloques')
        );

        return response()->json([
            'success' => true,
            'message' => 'Configuración creada exitosamente',
            'data' => $config->load('bloques'),
            'reprogramacion_horarios' => $reprogramacion,
        ], 201);
    }

    /**
     * PUT /api/configuraciones-horario/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $config = ConfiguracionHorario::findOrFail($id);

        $request->validate([
            'institucion' => 'sometimes|in:colegio,academia',
            'nivel' => 'sometimes|in:primaria,secundaria,academia',
            'turno' => 'sometimes|in:mañana,tarde,completo',
            'nombre' => 'sometimes|string|max:100',
            'hora_inicio' => 'sometimes|date_format:H:i',
            'hora_fin' => 'sometimes|date_format:H:i|after:hora_inicio',
            'duracion_bloque_minutos' => 'sometimes|integer|min:30|max:120',
            'año_academico' => 'sometimes|integer',
            'bloques' => 'sometimes|array',
            'bloques.*.orden' => 'required_with:bloques|integer',
            'bloques.*.hora_inicio' => 'required_with:bloques|date_format:H:i',
            'bloques.*.hora_fin' => 'required_with:bloques|date_format:H:i',
            'bloques.*.tipo' => 'required_with:bloques|in:clase,receso',
        ]);

        $turnoFinal = $request->input('turno', $config->turno);
        $bloquesFinales = $request->input('bloques');

        if ($bloquesFinales !== null) {
            $this->recesoService->validarBloques($bloquesFinales, $turnoFinal);
        }

        $config->update($request->except('bloques'));

        if ($request->has('bloques')) {
            $config->bloques()->delete();
            foreach ($request->bloques as $bloque) {
                $config->bloques()->create($bloque);
            }
        }

        $configActualizada = $config->fresh()->load('bloques');
        $reprogramacion = $this->reprogramarHorariosAfectados($configActualizada);

        return response()->json([
            'success' => true,
            'message' => 'Configuración actualizada exitosamente',
            'data' => $configActualizada,
            'reprogramacion_horarios' => $reprogramacion,
        ]);
    }

    /**
     * DELETE /api/configuraciones-horario/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $config = ConfiguracionHorario::findOrFail($id);

        // Verificar que no tenga horarios generados
        // (opcional: podrías bloquear la eliminación si hay horarios asociados)

        $config->delete();

        return response()->json([
            'success' => true,
            'message' => 'Configuración eliminada exitosamente',
        ]);
    }

    /**
     * GET /api/configuraciones-horario/vigente
     * Obtener la configuración vigente para una institución/nivel/turno
     */
    public function vigente(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'required|in:colegio,academia',
            'nivel' => 'required|in:primaria,secundaria,academia',
            'turno' => 'required|in:mañana,tarde,completo',
        ]);

        $config = ConfiguracionHorario::with('bloques')
            ->where('institucion', $request->institucion)
            ->where('nivel', $request->nivel)
            ->where('turno', $request->turno)
            ->where('activo', true)
            ->orderBy('año_academico', 'desc')
            ->first();

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'No hay configuración vigente para estos parámetros',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $config,
        ]);
    }

    /**
     * GET /api/configuraciones-horario/recesos
     */
    public function recesos(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'sometimes|in:colegio,academia',
            'nivel' => 'sometimes|in:primaria,secundaria,academia',
            'turno' => 'sometimes|in:mañana,tarde,completo',
        ]);

        $recesos = $this->recesoService
            ->recesosVigentes(
                $request->input('institucion'),
                $request->input('nivel'),
                $request->input('turno')
            )
            ->map(fn ($receso) => [
                'id' => $receso->id,
                'nombre' => $receso->nombre ?: 'Receso',
                'hora_inicio' => Carbon::parse($receso->hora_inicio)->format('H:i'),
                'hora_fin' => Carbon::parse($receso->hora_fin)->format('H:i'),
            ])
            ->values();

        return response()->json([
            'success' => true,
            'data' => $recesos,
            'total' => $recesos->count(),
        ]);
    }

    private function reprogramarHorariosAfectados(ConfiguracionHorario $config): array
    {
        $periodoAcademico = $this->obtenerPeriodoActual();

        $horarios = $this->queryHorariosAfectados($config, $periodoAcademico)
            ->get();

        if ($horarios->isEmpty()) {
            return [
                'total_eliminados' => 0,
                'horarios_creados' => 0,
                'asignaciones_incompletas' => [],
                'mensaje' => 'No había horarios activos que adaptar.',
            ];
        }

        $gradoIds = $horarios
            ->pluck('grado_id')
            ->unique()
            ->values();

        $this->sincronizarAulasAsignacionDesdeHorarios($horarios);

        DB::transaction(function () use ($horarios): void {
            foreach ($horarios as $horario) {
                $this->historialService->registrarCambio(
                    $horario->id,
                    'eliminar',
                    $this->snapshotHorario($horario),
                    null,
                    'Horario reprogramado automáticamente por cambio de recesos'
                );

                $horario->forceDelete();
            }
        });

        $horariosCreados = 0;
        $errores = [];

        foreach ($gradoIds as $gradoId) {
            $resultado = $this->generatorService->generarHorarios([
                'institucion' => $config->institucion,
                'grado_id' => $gradoId,
                'periodo_academico' => $periodoAcademico,
                'respetar_existentes' => true,
            ]);

            if (($resultado['success'] ?? false) !== true) {
                $errores[] = [
                    'grado_id' => $gradoId,
                    'motivo' => $resultado['error'] ?? 'No se pudo regenerar este grado.',
                ];

                continue;
            }

            $horariosCreados += (int) ($resultado['horarios_creados'] ?? 0);
        }

        $incompletas = $this->generatorService->obtenerAsignacionesIncompletas();

        return [
            'total_eliminados' => $horarios->count(),
            'horarios_creados' => $horariosCreados,
            'asignaciones_incompletas' => $incompletas,
            'errores' => $errores,
            'mensaje' => empty($incompletas) && empty($errores)
                ? 'Los horarios afectados se adaptaron a los recesos.'
                : 'Se adaptaron horarios, pero quedaron asignaciones por revisar.',
        ];
    }

    private function sincronizarAulasAsignacionDesdeHorarios($horarios): void
    {
        $horarios
            ->groupBy(
                fn (Horario $horario) =>
                    "{$horario->profesor_id}-{$horario->curso_id}-{$horario->grado_id}-{$horario->institucion}"
            )
            ->each(function ($grupo): void {
                $aulaId = $grupo
                    ->pluck('aula_id')
                    ->filter()
                    ->countBy()
                    ->sortDesc()
                    ->keys()
                    ->first();

                if (!$aulaId) {
                    return;
                }

                $primero = $grupo->first();

                ProfesorCurso::where('profesor_id', $primero->profesor_id)
                    ->where('curso_id', $primero->curso_id)
                    ->where('grado_id', $primero->grado_id)
                    ->where('institucion', $primero->institucion)
                    ->where('activo', true)
                    ->whereNull('aula_id')
                    ->update([
                        'aula_id' => $aulaId,
                    ]);
            });
    }

    private function queryHorariosAfectados(ConfiguracionHorario $config, string $periodoAcademico)
    {
        return Horario::with(['profesor', 'curso', 'grado', 'aula'])
            ->where('estado', 'activo')
            ->where('institucion', $config->institucion)
            ->where('periodo_academico', $periodoAcademico)
            ->whereHas('grado', function ($query) use ($config) {
                $query->where('nivel', $config->nivel);

                if ($config->turno !== 'completo') {
                    $query->where(function ($subQuery) use ($config) {
                        $subQuery
                            ->where('turno', $config->turno)
                            ->orWhereNull('turno')
                            ->orWhere('turno', '');
                    });
                }
            });
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

    private function obtenerPeriodoActual(): string
    {
        $anioActual = (int) date('Y');

        return $anioActual . '-' . ($anioActual + 1);
    }
}
