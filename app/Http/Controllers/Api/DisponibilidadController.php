<?php
// app/Http/Controllers/Api/DisponibilidadController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisponibilidadRequest;
use App\Services\DisponibilidadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DisponibilidadController extends Controller
{
    protected DisponibilidadService $disponibilidadService;

    public function __construct(DisponibilidadService $disponibilidadService)
    {
        $this->disponibilidadService = $disponibilidadService;
    }

    /**
     * GET /api/disponibilidades
     * Obtener todas las disponibilidades con filtros opcionales
     */
    public function index(Request $request): JsonResponse
    {
        $filtros = $request->only(['profesor_id', 'dia', 'turno', 'institucion']);

        try {
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
                // Filtrar por institución
                elseif (isset($filtros['institucion'])) {
                    $disponibilidades = $this->disponibilidadService->getByInstitucion($filtros['institucion']);
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las disponibilidades',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/disponibilidades/profesor/{profesorId}
     * Obtener disponibilidades de un profesor específico
     */
    public function getByProfesor(int $profesorId): JsonResponse
    {
        try {
            $disponibilidades = $this->disponibilidadService->getByProfesor($profesorId);

            return response()->json([
                'success' => true,
                'data' => $disponibilidades,
                'profesor_id' => $profesorId,
                'total' => $disponibilidades->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las disponibilidades del profesor',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * GET /api/disponibilidades/profesor/{profesorId}/institucion/{institucion}
     * Obtener disponibilidades de un profesor por institución
     */
    public function getByProfesorEInstitucion(int $profesorId, string $institucion): JsonResponse
    {
        try {
            $disponibilidades = $this->disponibilidadService->getByProfesorEInstitucion($profesorId, $institucion);

            return response()->json([
                'success' => true,
                'data' => $disponibilidades,
                'profesor_id' => $profesorId,
                'institucion' => $institucion,
                'total' => $disponibilidades->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las disponibilidades',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * GET /api/disponibilidades/bloques/{profesorId}
     * Obtener bloques disponibles de un profesor con parámetros
     */
    public function getBloquesDisponibles(Request $request, int $profesorId): JsonResponse
    {
        try {
            $request->validate([
                'dia' => 'required|in:lunes,martes,miércoles,jueves,viernes,sábado,domingo',
                'institucion' => 'nullable|in:colegio,academia',
                'duracion' => 'nullable|integer|in:30,45,60,90,120',
                'intervalo' => 'nullable|integer|in:15,30'
            ]);

            $dia = $request->input('dia');
            $institucion = $request->input('institucion', 'colegio');
            $duracion = $request->input('duracion', 60);
            $intervalo = $request->input('intervalo', 30);

            // Usar método con intervalo si se especifica
            if ($request->has('intervalo')) {
                $bloques = $this->disponibilidadService->obtenerBloquesConIntervalos(
                    $profesorId,
                    $dia,
                    $institucion,
                    $intervalo
                );
            } else {
                $bloques = $this->disponibilidadService->obtenerBloquesDisponibles(
                    $profesorId,
                    $dia,
                    $institucion,
                    $duracion
                );
            }

            return response()->json([
                'success' => true,
                'profesor_id' => $profesorId,
                'dia' => $dia,
                'institucion' => $institucion,
                'duracion' => $duracion,
                'total_bloques' => count($bloques),
                'data' => $bloques
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
                'message' => 'Error al obtener los bloques disponibles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/disponibilidades/agrupadas/{profesorId}
     * Obtener disponibilidades agrupadas por día
     */
    public function getAgrupadasPorDia(int $profesorId, Request $request): JsonResponse
    {
        try {
            $institucion = $request->input('institucion', 'colegio');

            $agrupado = $this->disponibilidadService->getAgrupadoPorDia($profesorId, $institucion);

            return response()->json([
                'success' => true,
                'profesor_id' => $profesorId,
                'institucion' => $institucion,
                'data' => $agrupado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las disponibilidades agrupadas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/disponibilidades/{id}
     * Obtener una disponibilidad específica
     */
    public function show(int $id): JsonResponse
    {
        try {
            $disponibilidad = \App\Models\DisponibilidadProfesor::with('profesor')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $disponibilidad
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Disponibilidad no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
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

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la disponibilidad',
                'error' => $e->getMessage()
            ], 500);
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

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la disponibilidad',
                'error' => $e->getMessage()
            ], 500);
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
                'message' => "Se eliminaron {$cantidad} disponibilidades del profesor",
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
     * DELETE /api/disponibilidades/profesor/{profesorId}/institucion/{institucion}
     * Eliminar disponibilidades por institución
     */
    public function destroyByInstitucion(int $profesorId, string $institucion): JsonResponse
    {
        try {
            $cantidad = $this->disponibilidadService->deleteByInstitucion($profesorId, $institucion);

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$cantidad} disponibilidades de {$institucion}",
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
        try {
            $request->validate([
                'profesor_id' => 'required|exists:profesores,id',
                'dia_semana' => 'required|in:lunes,martes,miércoles,jueves,viernes,sábado,domingo',
                'hora' => 'required|date_format:H:i',
                'institucion' => 'nullable|in:colegio,academia'
            ]);

            $institucion = $request->input('institucion', 'colegio');

            $disponible = $this->disponibilidadService->verificarDisponibilidadPuntual(
                $request->profesor_id,
                $request->dia_semana,
                $request->hora,
                $institucion
            );

            return response()->json([
                'success' => true,
                'disponible' => $disponible,
                'profesor_id' => $request->profesor_id,
                'dia' => $request->dia_semana,
                'hora' => $request->hora,
                'institucion' => $institucion,
                'mensaje' => $disponible
                    ? 'El profesor está disponible en ese horario'
                    : 'El profesor NO está disponible en ese horario'
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
                'message' => 'Error al verificar disponibilidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/disponibilidades/verificar-rango
     * Verificar disponibilidad para un rango completo
     */
    public function verificarRango(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'profesor_id' => 'required|exists:profesores,id',
                'dia_semana' => 'required|in:lunes,martes,miércoles,jueves,viernes,sábado,domingo',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'institucion' => 'nullable|in:colegio,academia'
            ]);

            $institucion = $request->input('institucion', 'colegio');

            $esValida = $this->disponibilidadService->validarDisponibilidadParaClase(
                $request->profesor_id,
                $request->dia_semana,
                $request->hora_inicio,
                $request->hora_fin,
                $institucion
            );

            return response()->json([
                'success' => true,
                'disponible' => true,
                'mensaje' => 'El profesor está disponible en ese rango horario',
                'profesor_id' => $request->profesor_id,
                'dia' => $request->dia_semana,
                'rango' => [
                    'inicio' => $request->hora_inicio,
                    'fin' => $request->hora_fin
                ],
                'institucion' => $institucion
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'disponible' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'disponible' => false,
                'message' => 'Error al verificar disponibilidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/disponibilidades/estadisticas
     * Obtener estadísticas de disponibilidad
     */
    public function getEstadisticas(): JsonResponse
    {
        try {
            $estadisticas = $this->disponibilidadService->getEstadisticas();

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
