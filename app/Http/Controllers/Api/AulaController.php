<?php
// app/Http/Controllers/Api/AulaController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AulaRequest;
use App\Models\Aula;
use App\Services\AulaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AulaController extends Controller
{
    protected AulaService $aulaService;

    public function __construct(AulaService $aulaService)
    {
        $this->aulaService = $aulaService;
    }

    /**
     * ========================================
     * CONSULTAS
     * ========================================
     */

    /**
     * GET /api/aulas
     * Obtener todas las aulas con filtros opcionales
     *
     * Filtros soportados:
     * - nivel: primaria, secundaria, academia, todos
     * - tipo: aula, laboratorio, taller, biblioteca, etc.
     * - capacidad: número mínimo de capacidad
     * - institucion: colegio, academia
     * - edificio: nombre del edificio
     * - piso: número de piso
     * - activo: true/false
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nivel' => 'nullable|in:primaria,secundaria,academia,todos',
                'tipo' => 'nullable|string|max:50',
                'capacidad' => 'nullable|integer|min:1',
                'institucion' => 'nullable|in:colegio,academia',
                'edificio' => 'nullable|string|max:100',
                'piso' => 'nullable|string|max:20',
                'activo' => 'nullable|boolean'
            ]);

            // Aplicar filtros según los parámetros recibidos
            if ($request->filled('nivel') && $request->filled('tipo')) {
                // Filtro combinado nivel + tipo
                $aulas = $this->aulaService->getByNivelYTipo(
                    $request->nivel,
                    $request->tipo
                );
            } elseif ($request->filled('nivel')) {
                $aulas = $this->aulaService->getByNivel($request->nivel);
            } elseif ($request->filled('tipo')) {
                $aulas = $this->aulaService->getByTipo($request->tipo);
            } elseif ($request->filled('capacidad')) {
                $aulas = $this->aulaService->getConCapacidad((int) $request->capacidad);
            } elseif ($request->filled('edificio') || $request->filled('piso')) {
                $aulas = $this->aulaService->getByUbicacion(
                    $request->edificio,
                    $request->piso
                );
            } else {
                $aulas = $this->aulaService->getAll();
            }

            // Filtros post-consulta (para parámetros no manejados en el service)
            if ($request->filled('institucion')) {
                $aulas = $aulas->where('institucion', $request->institucion);
            }

            if ($request->has('activo')) {
                $aulas = $aulas->where('activo', $request->boolean('activo'));
            }

            return response()->json([
                'success' => true,
                'data' => $aulas->values(),
                'total' => $aulas->count(),
                'filtros_aplicados' => $request->only([
                    'nivel', 'tipo', 'capacidad', 'institucion',
                    'edificio', 'piso', 'activo'
                ])
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
                'message' => 'Error al obtener las aulas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/aulas/nivel/{nivel}
     * Obtener aulas por nivel específico
     */
    public function getByNivel(string $nivel): JsonResponse
    {
        try {
            // Validar nivel válido
            if (!in_array($nivel, ['primaria', 'secundaria', 'academia', 'todos'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nivel no válido',
                    'error' => 'El nivel debe ser: primaria, secundaria, academia o todos'
                ], 422);
            }

            $aulas = $this->aulaService->getByNivel($nivel);

            return response()->json([
                'success' => true,
                'data' => $aulas,
                'nivel' => $nivel,
                'total' => $aulas->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las aulas por nivel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/aulas/disponibles
     * Obtener aulas disponibles en un día y hora específicos
     *
     * Query params:
     * - dia (required): lunes, martes, etc.
     * - hora_inicio (required): HH:MM
     * - hora_fin (required): HH:MM
     * - nivel (optional): primaria, secundaria, academia
     * - institucion (optional): colegio, academia
     * - exclude_horario_id (optional): ID del horario a excluir (para edición)
     */
    public function getDisponibles(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'dia' => 'required|in:lunes,martes,miércoles,jueves,viernes,sábado',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'nivel' => 'nullable|in:primaria,secundaria,academia,todos',
                'institucion' => 'nullable|in:colegio,academia',
                'exclude_horario_id' => 'nullable|integer|exists:horarios,id'
            ]);

            $aulas = $this->aulaService->getAulasDisponibles(
                $request->dia,
                $request->hora_inicio,
                $request->hora_fin,
                $request->nivel,
                $request->institucion,
                $request->exclude_horario_id
            );

            return response()->json([
                'success' => true,
                'data' => $aulas,
                'dia' => $request->dia,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'nivel' => $request->nivel,
                'institucion' => $request->institucion,
                'total' => $aulas->count()
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
                'message' => 'Error al obtener las aulas disponibles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/aulas/disponibles/{dia}/{horaInicio}/{horaFin}
     * Alias RESTful del método anterior (compatibilidad)
     */
    public function getDisponiblesPorRuta(
        string $dia,
        string $horaInicio,
        string $horaFin
    ): JsonResponse {
        // Reenviar al método principal
        $request = new Request([
            'dia' => $dia,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin
        ]);

        return $this->getDisponibles($request);
    }

    /**
     * GET /api/aulas/verificar-disponibilidad
     * Verificar si un aula específica está disponible
     */
    public function verificarDisponibilidad(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'aula_id' => 'required|exists:aulas,id',
                'dia' => 'required|in:lunes,martes,miércoles,jueves,viernes,sábado',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'institucion' => 'nullable|in:colegio,academia',
                'exclude_horario_id' => 'nullable|integer|exists:horarios,id'
            ]);

            $disponible = $this->aulaService->verificarDisponibilidadAula(
                $request->aula_id,
                $request->dia,
                $request->hora_inicio,
                $request->hora_fin,
                $request->institucion,
                $request->exclude_horario_id
            );

            return response()->json([
                'success' => true,
                'disponible' => $disponible,
                'aula_id' => $request->aula_id,
                'dia' => $request->dia,
                'rango' => [
                    'inicio' => $request->hora_inicio,
                    'fin' => $request->hora_fin
                ],
                'mensaje' => $disponible
                    ? 'El aula está disponible en ese horario'
                    : 'El aula NO está disponible en ese horario'
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
     * GET /api/aulas/{id}
     * Obtener un aula específica con sus relaciones
     */
    public function show(int $id): JsonResponse
    {
        try {
            $aula = Aula::with(['horarios' => function ($query) {
                $query->where('estado', 'activo')
                      ->with(['profesor', 'curso', 'grado']);
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $aula
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aula no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * ========================================
     * CRUD
     * ========================================
     */

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

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el aula',
                'error' => $e->getMessage()
            ], 500);
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

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el aula',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/aulas/{id}
     * Eliminar un aula (soft delete si tiene horarios, hard delete si no)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->aulaService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Aula eliminada exitosamente'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el aula',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el aula',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * ESTADÍSTICAS
     * ========================================
     */

    /**
     * GET /api/aulas/estadisticas
     * Obtener estadísticas de aulas
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $estadisticas = $this->aulaService->getEstadisticas();

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
