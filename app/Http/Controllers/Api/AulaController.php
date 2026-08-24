<?php
// app/Http/Controllers/Api/AulaController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AulaRequest;
use App\Services\AulaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AulaController extends Controller
{
    protected AulaService $aulaService;

    public function __construct(AulaService $aulaService)
    {
        $this->aulaService = $aulaService;
    }

    /**
     * GET /api/aulas
     * Obtener todas las aulas
     */
    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only(['nivel', 'tipo', 'capacidad']);

        if (!empty($filtros)) {
            if (isset($filtros['nivel'])) {
                $aulas = $this->aulaService->getByNivel($filtros['nivel']);
            } elseif (isset($filtros['tipo'])) {
                $aulas = $this->aulaService->getByTipo($filtros['tipo']);
            } elseif (isset($filtros['capacidad'])) {
                $aulas = $this->aulaService->getConCapacidad((int)$filtros['capacidad']);
            } else {
                $aulas = collect([]);
            }
        } else {
            $aulas = $this->aulaService->getAll();
        }

        return response()->json([
            'success' => true,
            'data' => $aulas,
            'total' => $aulas->count()
        ]);
    }

    /**
     * GET /api/aulas/disponibles/{dia}/{horaInicio}/{horaFin}
     * Obtener aulas disponibles en un día y hora específicos
     */
    public function getDisponibles(string $dia, string $horaInicio, string $horaFin): JsonResponse
    {
        $aulas = $this->aulaService->getDisponibles($dia, $horaInicio, $horaFin);

        return response()->json([
            'success' => true,
            'data' => $aulas,
            'dia' => $dia,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'total' => $aulas->count()
        ]);
    }

    /**
     * GET /api/aulas/{id}
     * Obtener un aula específica
     */
    public function show(int $id): JsonResponse
    {
        $aula = \App\Models\Aula::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $aula
        ]);
    }

    /**
     * POST /api/aulas
     * Crear una nueva aula
     */
    public function store(AulaRequest $request): JsonResponse
    {
        try {
            $aula = $this->aulaService->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Aula creada exitosamente',
                'data' => $aula
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el aula',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * PUT /api/aulas/{id}
     * Actualizar un aula existente
     */
    public function update(AulaRequest $request, int $id): JsonResponse
    {
        try {
            $aula = $this->aulaService->update($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Aula actualizada exitosamente',
                'data' => $aula
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el aula',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * DELETE /api/aulas/{id}
     * Eliminar un aula
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->aulaService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Aula eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el aula',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * GET /api/aulas/estadisticas
     * Obtener estadísticas de aulas
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = $this->aulaService->getEstadisticas();

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
