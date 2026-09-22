<?php
// app/Http/Controllers/Api/HistorialController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HistorialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    protected HistorialService $historialService;

    public function __construct(HistorialService $historialService)
    {
        $this->historialService = $historialService;
    }

    /**
     * GET /api/historial
     * Obtener todo el historial
     */
    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only([
            'accion',
            'usuario_id',
            'fecha_inicio',
            'fecha_fin',
            'profesor_id',
            'grado_id',
            'curso_id',
            'aula_id',
            'institucion',
            'horario_id',
            'limit',
        ]);

        $historial = $this->historialService->getHistorialGeneral($filtros);

        return response()->json([
            'success' => true,
            'data' => $historial,
            'total' => $historial->count(),
            'filtros' => $filtros,
        ]);
    }

    /**
     * GET /api/historial/horario/{horarioId}
     */
    public function getByHorario(int $horarioId): JsonResponse
    {
        $historial = $this->historialService->getHistorialHorario($horarioId);

        return response()->json([
            'success' => true,
            'data' => $historial,
            'horario_id' => $horarioId,
            'total' => $historial->count(),
        ]);
    }

    /**
     * GET /api/historial/versiones/{horarioId}
     */
    public function getVersiones(int $horarioId): JsonResponse
    {
        $versiones = $this->historialService->getVersionesHorario($horarioId);

        return response()->json([
            'success' => true,
            'data' => $versiones,
            'horario_id' => $horarioId,
            'total' => count($versiones),
        ]);
    }

    /**
     * POST /api/historial/revertir/{historialId}
     */
    public function revertir(int $historialId, Request $request): JsonResponse
    {
        try {
            $horario = $this->historialService->revertirCambio(
                $historialId,
                $request->motivo ?? 'Revertido desde historial'
            );

            return response()->json([
                'success' => true,
                'message' => 'Cambio revertido exitosamente',
                'data' => $horario->load(['profesor', 'curso', 'aula', 'grado']),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al revertir el cambio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/historial/restaurar/{horarioId}
     * Restaurar un horario eliminado
     */
    public function restaurar(int $horarioId, Request $request): JsonResponse
    {
        try {
            $horario = $this->historialService->restaurarHorario(
                $horarioId,
                $request->motivo ?? 'Restauración manual'
            );

            return response()->json([
                'success' => true,
                'message' => 'Horario restaurado exitosamente',
                'data' => $horario->load(['profesor', 'curso', 'aula', 'grado']),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar el horario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/historial/estadisticas
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = $this->historialService->getEstadisticas();

        return response()->json([
            'success' => true,
            'data' => $estadisticas,
        ]);
    }

    /**
     * GET /api/historial/{id}
     */
    public function show(int $id): JsonResponse
    {
        $registro = \App\Models\HistorialCambio::with(['horario', 'usuario'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $registro,
        ]);
    }
}
