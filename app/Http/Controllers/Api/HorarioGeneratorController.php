<?php
// app/Http/Controllers/Api/HorarioGeneratorController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HorarioGeneratorService;
use App\Models\Horario;
use App\Models\Profesor;
use App\Models\Grado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ConfiguracionHorario;

class HorarioGeneratorController extends Controller
{
    protected HorarioGeneratorService $generatorService;

    public function __construct(HorarioGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * POST /api/horarios/generar
     * Generar todos los horarios automáticamente
     *
     * Body (opcional):
     * {
     *   "institucion": "colegio|academia",
     *   "periodo_academico": "2026-2027",
     *   "profesor_id": 1,
     *   "grado_id": 1,
     *   "limpiar_anteriores": false,
     *   "respetar_existentes": true
     * }
     */
    public function generar(Request $request): JsonResponse
    {
        try {
            // Validar opciones
            $request->validate([
                'institucion' => 'nullable|in:colegio,academia',
                'periodo_academico' => 'nullable|string|max:20',
                'profesor_id' => 'nullable|exists:profesores,id',
                'grado_id' => 'nullable|exists:grados,id',
                'limpiar_anteriores' => 'nullable|boolean',
                'respetar_existentes' => 'nullable|boolean',
            ]);

            $opciones = [
                'institucion' => $request->institucion,
                'periodo_academico' => $request->periodo_academico ?? $this->obtenerPeriodoActual(),
                'profesor_id' => $request->profesor_id,
                'grado_id' => $request->grado_id,
                'limpiar_anteriores' => $request->limpiar_anteriores ?? false,
                'respetar_existentes' => $request->respetar_existentes ?? true,
            ];

            // Si se solicita limpiar horarios anteriores
            if ($opciones['limpiar_anteriores']) {
                $this->limpiarHorariosPorFiltros($opciones);
            }

            // Ejecutar generación
            $resultado = $this->generatorService->generarHorarios($opciones);

            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Horarios generados exitosamente',
                    'data' => [
                        'estadisticas' => $this->generatorService->obtenerEstadisticas(),
                        'conflictos' => $this->generatorService->obtenerConflictos(),
                        'asignaciones_incompletas' => $this->generatorService->obtenerAsignacionesIncompletas(),
                        'horarios_creados' => $resultado['horarios_creados'],
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al generar horarios',
                    'error' => $resultado['error'] ?? 'Error desconocido',
                    'conflictos' => $resultado['conflictos'] ?? [],
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en generación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar horarios',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/horarios/generar/profesor/{profesorId}
     * Generar horario para un profesor específico
     */
    public function generarPorProfesor(Request $request, int $profesorId): JsonResponse
    {
        try {
            $profesor = Profesor::findOrFail($profesorId);

            if ($profesor->estado !== 'activo') {
                return response()->json([
                    'success' => false,
                    'message' => 'El profesor no está activo',
                ], 422);
            }

            $opciones = [
                'profesor_id' => $profesorId,
                'institucion' => $request->institucion ?? $profesor->institucion,
                'periodo_academico' => $request->periodo_academico ?? $this->obtenerPeriodoActual(),
                'respetar_existentes' => $request->respetar_existentes ?? true,
            ];

            $resultado = $this->generatorService->generarHorarios($opciones);

            return response()->json([
                'success' => $resultado['success'],
                'message' => $resultado['success']
                    ? "Horario generado para el profesor {$profesor->nombre_completo}"
                    : 'Error al generar horario',
                'data' => [
                    'profesor' => [
                        'id' => $profesor->id,
                        'nombre' => $profesor->nombre_completo,
                        'institucion' => $profesor->institucion,
                    ],
                    'estadisticas' => $this->generatorService->obtenerEstadisticas(),
                    'conflictos' => $this->generatorService->obtenerConflictos(),
                    'asignaciones_incompletas' => $this->generatorService->obtenerAsignacionesIncompletas(),
                ],
            ], $resultado['success'] ? 200 : 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar horario del profesor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/horarios/generar/grado/{gradoId}
     * Generar horario para un grado específico
     */
    public function generarPorGrado(Request $request, int $gradoId): JsonResponse
    {
        try {
            $grado = Grado::findOrFail($gradoId);

            $opciones = [
                'grado_id' => $gradoId,
                'institucion' => $request->institucion ?? $this->obtenerInstitucionPorNivel($grado->nivel),
                'periodo_academico' => $request->periodo_academico ?? $this->obtenerPeriodoActual(),
                'respetar_existentes' => $request->respetar_existentes ?? true,
            ];

            $resultado = $this->generatorService->generarHorarios($opciones);

            return response()->json([
                'success' => $resultado['success'],
                'message' => $resultado['success']
                    ? "Horario generado para el grado {$grado->nombre_completo}"
                    : 'Error al generar horario',
                'data' => [
                    'grado' => [
                        'id' => $grado->id,
                        'nombre' => $grado->nombre_completo,
                        'nivel' => $grado->nivel,
                        'turno' => $grado->turno,
                    ],
                    'estadisticas' => $this->generatorService->obtenerEstadisticas(),
                    'conflictos' => $this->generatorService->obtenerConflictos(),
                    'asignaciones_incompletas' => $this->generatorService->obtenerAsignacionesIncompletas(),
                ],
            ], $resultado['success'] ? 200 : 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar horario del grado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/horarios/generar/vista-previa
     * Obtener una vista previa de la configuración de bloques
     * antes de generar los horarios
     */
    public function vistaPrevia(Request $request): JsonResponse
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
                'message' => 'No hay configuración vigente',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $config->id,
                'institucion' => $config->institucion,
                'nivel' => $config->nivel,
                'turno' => $config->turno,
                'nombre' => $config->nombre,
                'hora_inicio' => $config->hora_inicio,
                'hora_fin' => $config->hora_fin,
                'duracion_bloque_minutos' => $config->duracion_bloque_minutos,
                'total_bloques_clase' => $config->bloques->where('tipo', 'clase')->count(),
                'total_recesos' => $config->bloques->where('tipo', 'receso')->count(),
                'bloques' => $config->bloques,
            ],
        ]);
    }

    /**
     * GET /api/horarios/generar/estadisticas
     * Obtener estadísticas detalladas de horarios
     */
    public function estadisticas(Request $request): JsonResponse
    {
        try {
            $query = Horario::where('estado', 'activo');

            if ($request->has('institucion')) {
                $query->where('institucion', $request->institucion);
            }

            if ($request->has('periodo_academico')) {
                $query->where('periodo_academico', $request->periodo_academico);
            }

            $totalHorarios = (clone $query)->count();
            $totalProfesores = (clone $query)->distinct('profesor_id')->count('profesor_id');
            $totalAulas = (clone $query)->distinct('aula_id')->count('aula_id');
            $totalGrados = (clone $query)->distinct('grado_id')->count('grado_id');

            // Horarios por día
            $horariosPorDia = (clone $query)
                ->select('dia_semana', DB::raw('count(*) as total'))
                ->groupBy('dia_semana')
                ->orderBy('dia_semana')
                ->get();

            // Horarios por turno
            $horariosPorTurno = (clone $query)
                ->select('turno', DB::raw('count(*) as total'))
                ->groupBy('turno')
                ->get();

            // Horarios por institución
            $horariosPorInstitucion = Horario::where('estado', 'activo')
                ->select('institucion', DB::raw('count(*) as total'))
                ->groupBy('institucion')
                ->get();

            // Carga horaria por profesor
            $cargaPorProfesor = (clone $query)
                ->select('profesor_id', DB::raw('count(*) as total_horas'))
                ->with('profesor:id,nombre,apellido_paterno,apellido_materno')
                ->groupBy('profesor_id')
                ->orderBy('total_horas', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'resumen' => [
                        'total_horarios' => $totalHorarios,
                        'total_profesores' => $totalProfesores,
                        'total_aulas' => $totalAulas,
                        'total_grados' => $totalGrados,
                    ],
                    'horarios_por_dia' => $horariosPorDia,
                    'horarios_por_turno' => $horariosPorTurno,
                    'horarios_por_institucion' => $horariosPorInstitucion,
                    'carga_por_profesor' => $cargaPorProfesor,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/horarios/generar/limpiar
     * Eliminar horarios generados (soft delete)
     */
    public function limpiar(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'institucion' => 'nullable|in:colegio,academia',
                'periodo_academico' => 'nullable|string',
                'profesor_id' => 'nullable|exists:profesores,id',
                'grado_id' => 'nullable|exists:grados,id',
                'confirmar' => 'required|accepted',
            ]);

            $query = Horario::where('estado', 'activo');

            if ($request->institucion) {
                $query->where('institucion', $request->institucion);
            }

            if ($request->periodo_academico) {
                $query->where('periodo_academico', $request->periodo_academico);
            }

            if ($request->profesor_id) {
                $query->where('profesor_id', $request->profesor_id);
            }

            if ($request->grado_id) {
                $query->where('grado_id', $request->grado_id);
            }

            $cantidad = $query->count();

            // Registrar en historial antes de eliminar
            $horarios = $query->get();
            foreach ($horarios as $horario) {
                \App\Models\HistorialCambio::create([
                    'horario_id' => $horario->id,
                    'usuario_id' => auth()->id(),
                    'accion' => 'eliminar',
                    'datos_anteriores' => $horario->toArray(),
                    'datos_nuevos' => null,
                    'motivo' => 'Limpieza masiva de horarios',
                    'ip_usuario' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            // Eliminar (soft delete)
            $query->delete();

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$cantidad} horarios",
                'data' => [
                    'total_eliminados' => $cantidad,
                    'filtros_aplicados' => [
                        'institucion' => $request->institucion,
                        'periodo_academico' => $request->periodo_academico,
                        'profesor_id' => $request->profesor_id,
                        'grado_id' => $request->grado_id,
                    ],
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar horarios',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/horarios/generar/configuraciones
     * Listar todas las configuraciones de bloques disponibles
     */
    public function configuraciones(): JsonResponse
    {
        $configuraciones = ConfiguracionHorario::withCount([
            'bloques as total_bloques' => fn($q) => $q->where('tipo', 'clase'),
            'bloques as total_recesos' => fn($q) => $q->where('tipo', 'receso'),
        ])
        ->where('activo', true)
        ->orderBy('institucion')
        ->orderBy('nivel')
        ->orderBy('turno')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $configuraciones,
        ]);
    }

    /**
     * ========================================
     * MÉTODOS AUXILIARES
     * ========================================
     */

    /**
     * Obtener el período académico actual
     */
    protected function obtenerPeriodoActual(): string
    {
        $anioActual = date('Y');
        return $anioActual . '-' . ($anioActual + 1);
    }

    /**
     * Obtener institución según el nivel
     */
    protected function obtenerInstitucionPorNivel(string $nivel): string
    {
        return $nivel === 'academia' ? 'academia' : 'colegio';
    }

    /**
     * Obtener config key
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
     * Limpiar horarios por filtros
     */
    protected function limpiarHorariosPorFiltros(array $opciones): void
    {
        $query = Horario::where('estado', 'activo');

        if (!empty($opciones['institucion'])) {
            $query->where('institucion', $opciones['institucion']);
        }

        if (!empty($opciones['periodo_academico'])) {
            $query->where('periodo_academico', $opciones['periodo_academico']);
        }

        if (!empty($opciones['profesor_id'])) {
            $query->where('profesor_id', $opciones['profesor_id']);
        }

        if (!empty($opciones['grado_id'])) {
            $query->where('grado_id', $opciones['grado_id']);
        }

        $query->delete();
    }
}
