<?php
// app/Http/Controllers/Api/AsignacionController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AsignacionRequest;
use App\Services\AsignacionService;
use App\Services\HorarioGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AsignacionController extends Controller
{
    protected AsignacionService $asignacionService;
    protected HorarioGeneratorService $horarioGeneratorService;

    public function __construct(
        AsignacionService $asignacionService,
        HorarioGeneratorService $horarioGeneratorService
    )
    {
        $this->asignacionService = $asignacionService;
        $this->horarioGeneratorService = $horarioGeneratorService;
    }

    private function generarHorarioDeAsignacion($asignacion): ?array
    {
        if (!$asignacion->activo) {
            return null;
        }

        return $this->horarioGeneratorService->generarHorarios([
            'profesor_id' => $asignacion->profesor_id,
            'institucion' => $asignacion->institucion ?? 'colegio',
            'respetar_existentes' => true,
        ]);
    }

    /**
     * GET /api/asignaciones
     * Obtener todas las asignaciones
     */
    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only(['profesor_id', 'curso_id', 'grado_id', 'institucion']);

        if (!empty($filtros)) {
            $query = \App\Models\ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula']);

            foreach ($filtros as $campo => $valor) {
                if ($valor !== null && $valor !== '') {
                    $query->where($campo, $valor);
                }
            }

            $asignaciones = $query
                ->orderBy('institucion')
                ->orderBy('profesor_id')
                ->get();
        } else {
            $asignaciones = $this->asignacionService->getAll();
        }

        return response()->json([
            'success' => true,
            'data' => $asignaciones,
            'total' => $asignaciones->count()
        ]);
    }

    /**
     * GET /api/asignaciones/profesor/{profesorId}
     * Obtener asignaciones de un profesor
     */
    public function getByProfesor(int $profesorId): JsonResponse
    {
        $asignaciones = $this->asignacionService->getByProfesor($profesorId);

        return response()->json([
            'success' => true,
            'data' => $asignaciones,
            'profesor_id' => $profesorId,
            'total' => $asignaciones->count()
        ]);
    }

    /**
     * GET /api/asignaciones/curso/{cursoId}
     * Obtener asignaciones de un curso
     */
    public function getByCurso(int $cursoId): JsonResponse
    {
        $asignaciones = $this->asignacionService->getByCurso($cursoId);

        return response()->json([
            'success' => true,
            'data' => $asignaciones,
            'curso_id' => $cursoId,
            'total' => $asignaciones->count()
        ]);
    }

    /**
     * GET /api/asignaciones/grado/{gradoId}
     * Obtener asignaciones de un grado
     */
    public function getByGrado(int $gradoId): JsonResponse
    {
        $asignaciones = $this->asignacionService->getByGrado($gradoId);

        return response()->json([
            'success' => true,
            'data' => $asignaciones,
            'grado_id' => $gradoId,
            'total' => $asignaciones->count()
        ]);
    }

    /**
     * GET /api/asignaciones/profesores-disponibles/{cursoId}/{gradoId}
     * Obtener profesores disponibles para un curso y grado
     */
    public function getProfesoresDisponibles(int $cursoId, int $gradoId): JsonResponse
    {
        $profesores = $this->asignacionService->getProfesoresDisponibles(
            $cursoId,
            $gradoId,
            request('institucion')
        );

        return response()->json([
            'success' => true,
            'data' => $profesores,
            'curso_id' => $cursoId,
            'grado_id' => $gradoId,
            'total' => $profesores->count()
        ]);
    }

    /**
     * GET /api/asignaciones/{id}
     * Obtener una asignación específica
     */
    public function show(int $id): JsonResponse
    {
        $asignacion = \App\Models\ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $asignacion
        ]);
    }

    /**
     * POST /api/asignaciones
     * Crear una nueva asignación
     */
    public function store(AsignacionRequest $request): JsonResponse
    {
        try {
            $asignacion = $this->asignacionService->create($request->validated());
            $generacion = $this->generarHorarioDeAsignacion($asignacion);

            return response()->json([
                'success' => true,
                'message' => 'Asignación creada exitosamente',
                'data' => $asignacion,
                'generacion_horarios' => $generacion,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la asignación',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * PUT /api/asignaciones/{id}
     * Actualizar una asignación existente
     */
    public function update(AsignacionRequest $request, int $id): JsonResponse
    {
        try {
            $asignacion = $this->asignacionService->update($id, $request->validated());
            $generacion = $this->generarHorarioDeAsignacion($asignacion);

            return response()->json([
                'success' => true,
                'message' => 'Asignación actualizada exitosamente',
                'data' => $asignacion,
                'generacion_horarios' => $generacion,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la asignación',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * DELETE /api/asignaciones/{id}
     * Desactivar una asignación por compatibilidad
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->asignacionService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Asignación desactivada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la asignación',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * DELETE /api/asignaciones/{id}/permanente
     * Eliminar definitivamente una asignación
     */
    public function destroyPermanent(int $id): JsonResponse
    {
        try {
            $this->asignacionService->deletePermanent($id);

            return response()->json([
                'success' => true,
                'message' => 'Asignación eliminada definitivamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar definitivamente la asignación',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * POST /api/asignaciones/{id}/desactivar
     * Desactivar una asignación
     */
    public function desactivar(int $id): JsonResponse
    {
        try {
            $asignacion = $this->asignacionService->desactivar($id);

            return response()->json([
                'success' => true,
                'message' => 'Asignación desactivada exitosamente',
                'data' => $asignacion
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar la asignación',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * POST /api/asignaciones/{id}/activar
     * Activar una asignación desactivada
     */
    public function activar(int $id): JsonResponse
    {
        try {
            $asignacion = $this->asignacionService->activar($id);
            $generacion = $this->generarHorarioDeAsignacion($asignacion);

            return response()->json([
                'success' => true,
                'message' => 'Asignación activada exitosamente',
                'data' => $asignacion,
                'generacion_horarios' => $generacion,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al activar la asignación',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * GET /api/asignaciones/estadisticas
     * Obtener estadísticas de asignaciones
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = $this->asignacionService->getEstadisticas();

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
