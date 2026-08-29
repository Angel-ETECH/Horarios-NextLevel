<?php
// app/Http/Controllers/Api/DisponibilidadController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisponibilidadRequest;
use App\Services\DisponibilidadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisponibilidadController extends Controller
{
    protected DisponibilidadService $disponibilidadService;

    public function __construct(DisponibilidadService $disponibilidadService)
    {
        $this->disponibilidadService = $disponibilidadService;
    }

    /**
     * GET /api/disponibilidades
     * Obtener todas las disponibilidades
     */
    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only(['profesor_id', 'dia', 'turno']);

        if (!empty($filtros)) {
            // Filtrar por profesor
            if (isset($filtros['profesor_id'])) {
                $disponibilidades = $this->disponibilidadService->getByProfesor($filtros['profesor_id']);
            }
            // Filtrar por día
            elseif (isset($filtros['dia'])) {
                $disponibilidades = $this->disponibilidadService->getByDia($filtros['dia']);
            }
            // Filtrar por turno
            elseif (isset($filtros['turno'])) {
                $disponibilidades = $this->disponibilidadService->getByTurno($filtros['turno']);
            }
        } else {
            // Sin filtros, obtener todas
            $disponibilidades = $this->disponibilidadService->getAll();
        }

        return response()->json([
            'success' => true,
            'data' => $disponibilidades,
            'total' => $disponibilidades->count()
        ]);
    }

    /**
     * GET /api/disponibilidades/profesor/{profesorId}
     * Obtener disponibilidades de un profesor específico
     */
    public function getByProfesor(int $profesorId): JsonResponse
    {
        $disponibilidades = $this->disponibilidadService->getByProfesor($profesorId);

        return response()->json([
            'success' => true,
            'data' => $disponibilidades,
            'profesor_id' => $profesorId,
            'total' => $disponibilidades->count()
        ]);
    }

    /**
     * GET /api/disponibilidades/bloques/{profesorId}/{dia}
     * Obtener bloques disponibles de un profesor en un día específico
     */
    public function getBloquesDisponibles(int $profesorId, string $dia): JsonResponse
    {
        $bloques = $this->disponibilidadService->getBloquesDisponibles($profesorId, $dia);

        return response()->json([
            'success' => true,
            'profesor_id' => $profesorId,
            'dia' => $dia,
            'bloques' => $bloques
        ]);
    }

    /**
     * GET /api/disponibilidades/{id}
     * Obtener una disponibilidad específica
     */
    public function show(int $id): JsonResponse
    {
        $disponibilidad = \App\Models\DisponibilidadProfesor::with('profesor')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $disponibilidad
        ]);
    }

    /**
     * POST /api/disponibilidades
     * Crear una nueva disponibilidad
     */
    public function store(DisponibilidadRequest $request): JsonResponse
    {
        try {
            $disponibilidad = $this->disponibilidadService->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Disponibilidad creada exitosamente',
                'data' => $disponibilidad
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la disponibilidad',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * PUT /api/disponibilidades/{id}
     * Actualizar una disponibilidad existente
     */
    public function update(DisponibilidadRequest $request, int $id): JsonResponse
    {
        try {
            $disponibilidad = $this->disponibilidadService->update($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Disponibilidad actualizada exitosamente',
                'data' => $disponibilidad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la disponibilidad',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * DELETE /api/disponibilidades/{id}
     * Eliminar una disponibilidad (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->disponibilidadService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Disponibilidad eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la disponibilidad',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * DELETE /api/disponibilidades/profesor/{profesorId}
     * Eliminar todas las disponibilidades de un profesor
     */
    public function destroyByProfesor(int $profesorId): JsonResponse
    {
        try {
            $cantidad = $this->disponibilidadService->deleteByProfesor($profesorId);

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron $cantidad disponibilidades del profesor",
                'total_eliminados' => $cantidad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar las disponibilidades',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * POST /api/disponibilidades/verificar
     * Verificar si un profesor está disponible en un día y hora específicos
     */
    public function verificarDisponibilidad(Request $request): JsonResponse
    {
        $request->validate([
            'profesor_id' => 'required|exists:profesores,id',
            'dia_semana' => 'required|in:lunes,martes,miércoles,jueves,viernes,sábado,domingo',
            'hora' => 'required|date_format:H:i'
        ]);

        $disponible = $this->disponibilidadService->verificarDisponibilidad(
            $request->profesor_id,
            $request->dia_semana,
            $request->hora
        );

        return response()->json([
            'success' => true,
            'disponible' => $disponible,
            'profesor_id' => $request->profesor_id,
            'dia' => $request->dia_semana,
            'hora' => $request->hora
        ]);
    }
}
