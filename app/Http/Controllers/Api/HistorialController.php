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
     * Obtener todo el historial de cambios
     */
    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only(['accion', 'usuario_id', 'fecha_inicio', 'fecha_fin', 'profesor_id', 'limit']);

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
     * Obtener historial de un horario específico
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
     * Obtener versiones de un horario
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
     * Revertir a una versión anterior
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al revertir el cambio',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * GET /api/historial/estadisticas
     * Obtener estadísticas de cambios
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
     * Obtener un registro de historial específico
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
