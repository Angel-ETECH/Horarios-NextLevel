<?php
// app/Http/Controllers/Api/HistorialController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HistorialService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
            $resultado = $this->historialService->revertirCambio(
                $historialId,
                $request->motivo ?? 'Revertido desde historial'
            );

            return response()->json([
                'success' => true,
                'message' => 'Cambio revertido exitosamente',
                'data' => $resultado,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $this->primerMensajeDeError($e),
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El registro indicado ya no existe.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al revertir el cambio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Primer mensaje legible de un error de validación.
     */
    private function primerMensajeDeError(ValidationException $e): string
    {
        return collect($e->errors())->flatten()->first()
            ?? 'No se puede revertir este cambio.';
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

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $this->primerMensajeDeError($e),
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El horario indicado ya no existe.',
            ], 404);
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
