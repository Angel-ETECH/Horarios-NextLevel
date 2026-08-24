<?php
// app/Http/Controllers/Api/HorarioController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Services\HistorialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    protected HistorialService $historialService;

    public function __construct(HistorialService $historialService)
    {
        $this->historialService = $historialService;
    }

    /**
     * GET /api/horarios
     * Listar horarios con filtros y búsqueda
     */
    public function index(Request $request): JsonResponse
    {
        $query = Horario::with(['profesor', 'curso', 'aula', 'grado'])
            ->where('estado', 'activo');

        // Filtros
        if ($request->has('profesor_id')) {
            $query->where('profesor_id', $request->profesor_id);
        }

        if ($request->has('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        if ($request->has('aula_id')) {
            $query->where('aula_id', $request->aula_id);
        }

        if ($request->has('grado_id')) {
            $query->where('grado_id', $request->grado_id);
        }

        if ($request->has('dia_semana')) {
            $query->where('dia_semana', $request->dia_semana);
        }

        if ($request->has('turno')) {
            $query->where('turno', $request->turno);
        }

        // Búsqueda por texto
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('profesor', function($sub) use ($search) {
                    $sub->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('curso', function($sub) use ($search) {
                    $sub->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('codigo', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('grado', function($sub) use ($search) {
                    $sub->where('nombre_completo', 'LIKE', "%{$search}%");
                });
            });
        }

        // Ordenamiento
        $sortBy = $request->sort_by ?? 'dia_semana';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->per_page ?? 20;
        $horarios = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $horarios->items(),
            'meta' => [
                'current_page' => $horarios->currentPage(),
                'last_page' => $horarios->lastPage(),
                'per_page' => $horarios->perPage(),
                'total' => $horarios->total(),
            ],
            'filtros' => $request->all(),
        ]);
    }

    /**
     * GET /api/horarios/profesor/{profesorId}
     * Obtener horario completo de un profesor (Vista individual)
     */
    public function getByProfesor(int $profesorId): JsonResponse
    {
        $horarios = Horario::with(['curso', 'aula', 'grado'])
            ->where('profesor_id', $profesorId)
            ->where('estado', 'activo')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        // Organizar por días
        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $horariosPorDia = [];

        foreach ($dias as $dia) {
            $horariosPorDia[$dia] = $horarios->where('dia_semana', $dia)->values();
        }

        $profesor = $horarios->first()?->profesor;

        // Calcular carga horaria total
        $cargaTotal = $horarios->sum(function($h) {
            return \Carbon\Carbon::parse($h->hora_inicio)
                ->diffInHours(\Carbon\Carbon::parse($h->hora_fin));
        });

        return response()->json([
            'success' => true,
            'data' => [
                'profesor' => $profesor,
                'carga_horaria_total' => $cargaTotal,
                'horarios' => $horariosPorDia,
                'total_clases' => $horarios->count(),
            ]
        ]);
    }

    /**
     * GET /api/horarios/grado/{gradoId}
     * Obtener horario completo de un grado
     */
    public function getByGrado(int $gradoId): JsonResponse
    {
        $horarios = Horario::with(['profesor', 'curso', 'aula'])
            ->where('grado_id', $gradoId)
            ->where('estado', 'activo')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        // Organizar por días
        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $horariosPorDia = [];

        foreach ($dias as $dia) {
            $horariosPorDia[$dia] = $horarios->where('dia_semana', $dia)->values();
        }

        $grado = $horarios->first()?->grado;

        return response()->json([
            'success' => true,
            'data' => [
                'grado' => $grado,
                'horarios' => $horariosPorDia,
                'total_clases' => $horarios->count(),
            ]
        ]);
    }

    /**
     * GET /api/horarios/dia/{dia}
     * Obtener horarios de un día específico
     */
    public function getByDia(string $dia): JsonResponse
    {
        $horarios = Horario::with(['profesor', 'curso', 'aula', 'grado'])
            ->where('dia_semana', $dia)
            ->where('estado', 'activo')
            ->orderBy('hora_inicio')
            ->get();

        // Agrupar por hora
        $horariosPorHora = $horarios->groupBy('hora_inicio');

        return response()->json([
            'success' => true,
            'data' => [
                'dia' => $dia,
                'horarios' => $horariosPorHora,
                'total' => $horarios->count(),
            ]
        ]);
    }

    /**
     * GET /api/horarios/{id}
     * Obtener un horario específico con su historial
     */
    public function show(int $id): JsonResponse
    {
        $horario = Horario::with(['profesor', 'curso', 'aula', 'grado'])
            ->findOrFail($id);

        // Obtener historial de cambios
        $historial = $this->historialService->getHistorialHorario($id);

        return response()->json([
            'success' => true,
            'data' => [
                'horario' => $horario,
                'historial' => $historial,
                'versiones' => $this->historialService->getVersionesHorario($id),
            ]
        ]);
    }

    /**
     * PUT /api/horarios/{id}
     * Actualizar un horario (con registro de cambios)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $horario = Horario::findOrFail($id);

            // Guardar estado anterior
            $datosAnteriores = $horario->toArray();

            // Actualizar
            $horario->update($request->all());

            // Registrar cambio
            $this->historialService->registrarCambio(
                $horario->id,
                'actualizar',
                $datosAnteriores,
                $horario->toArray(),
                $request->motivo ?? 'Actualización manual',
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Horario actualizado exitosamente',
                'data' => $horario->load(['profesor', 'curso', 'aula', 'grado'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el horario',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * DELETE /api/horarios/{id}
     * Eliminar un horario (soft delete con historial)
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $horario = Horario::findOrFail($id);

            // Guardar datos antes de eliminar
            $datos = $horario->toArray();

            $horario->delete();

            // Registrar cambio
            $this->historialService->registrarCambio(
                $id,
                'eliminar',
                $datos,
                null,
                $request->motivo ?? 'Eliminación manual',
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Horario eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el horario',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
