<?php
// app/Http/Controllers/Api/HorarioController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grado;
use App\Models\Horario;
use App\Models\Profesor;
use App\Services\HistorialService;
use App\Services\HorarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HorarioController extends Controller
{
    protected HistorialService $historialService;
    protected HorarioService $horarioService;

    public function __construct(
        HistorialService $historialService,
        HorarioService $horarioService
    )
    {
        $this->historialService = $historialService;
        $this->horarioService = $horarioService;
    }

    private function normalizarDiaSemana(string $dia): string
    {
        $dia = strtolower(trim($dia));

        return match ($dia) {
            'miercoles' => 'miércoles',
            'sabado' => 'sábado',
            default => $dia,
        };
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
            $query->where('dia_semana', $this->normalizarDiaSemana($request->dia_semana));
        }

        if ($request->has('turno')) {
            $query->where('turno', $request->turno);
        }

        if ($request->has('institucion')) {
            $query->where('institucion', $request->institucion);
        }

        // Búsqueda por texto
        if ($request->filled('search')) {
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
        $sortBy = $request->input('sort_by', 'dia_semana');
        $sortOrder = strtolower((string) $request->input('sort_order', 'asc')) === 'desc'
            ? 'desc'
            : 'asc';

        $sortPermitidos = [
            'dia_semana',
            'hora_inicio',
            'hora_fin',
            'turno',
            'institucion',
            'created_at',
            'updated_at',
        ];

        if (!in_array($sortBy, $sortPermitidos, true)) {
            $sortBy = 'dia_semana';
        }

        if ($sortBy === 'dia_semana') {
            $query->orderByRaw(
                "FIELD(dia_semana, 'lunes', 'martes', 'miércoles', 'miercoles', 'jueves', 'viernes', 'sábado', 'sabado') {$sortOrder}"
            )->orderBy('hora_inicio');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Paginación
        $perPage = max(1, min(100, (int) $request->input('per_page', 20)));
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
    public function getByProfesor(Request $request, int $profesorId): JsonResponse
    {
        $profesor = Profesor::where('estado', 'activo')->find($profesorId);

        if (!$profesor) {
            return response()->json([
                'success' => false,
                'message' => 'Profesor no encontrado o inactivo',
            ], 404);
        }

        $horarios = Horario::with(['curso', 'aula', 'grado'])
            ->where('profesor_id', $profesorId)
            ->where('estado', 'activo');

        if ($request->filled('institucion')) {
            $horarios->where('institucion', $request->institucion);
        }

        $horarios = $horarios
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        // Organizar por días
        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $horariosPorDia = [];

        foreach ($dias as $dia) {
            $horariosPorDia[$dia] = $horarios->where('dia_semana', $dia)->values();
        }

        // Calcular carga horaria total
        $cargaTotal = $horarios->sum(function($h) {
            return \Carbon\Carbon::parse($h->hora_inicio)
                ->diffInMinutes(\Carbon\Carbon::parse($h->hora_fin)) / 60;
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
    public function getByGrado(Request $request, int $gradoId): JsonResponse
    {
        $grado = Grado::where('activo', true)->find($gradoId);

        if (!$grado) {
            return response()->json([
                'success' => false,
                'message' => 'Grado no encontrado o inactivo',
            ], 404);
        }

        $horarios = Horario::with(['profesor', 'curso', 'aula'])
            ->where('grado_id', $gradoId)
            ->where('estado', 'activo');

        if ($request->filled('institucion')) {
            $horarios->where('institucion', $request->institucion);
        }

        $horarios = $horarios
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        // Organizar por días
        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $horariosPorDia = [];

        foreach ($dias as $dia) {
            $horariosPorDia[$dia] = $horarios->where('dia_semana', $dia)->values();
        }

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
        $dia = $this->normalizarDiaSemana($dia);

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
            $horario = Horario::with(['profesor', 'curso', 'grado', 'aula'])->findOrFail($id);

            // Guardar estado anterior
            $datosAnteriores = $this->crearSnapshotHistorial($horario);

            $horarioActualizado = $this->horarioService->update($id, $request->except('motivo'));

            // Registrar cambio
            $this->historialService->registrarCambio(
                $horarioActualizado->id,
                'actualizar',
                $datosAnteriores,
                $this->crearSnapshotHistorial($horarioActualizado),
                $request->motivo ?? 'Actualización manual',
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Horario actualizado exitosamente',
                'data' => $horarioActualizado
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
                'message' => 'Error al actualizar el horario',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * DELETE /api/horarios/{id}
     * Eliminar un horario y liberar su bloque
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $horario = Horario::with(['profesor', 'curso', 'grado', 'aula'])->findOrFail($id);

            // Guardar datos antes de eliminar
            $datos = $this->crearSnapshotHistorial($horario);

            // Registrar cambio
            $this->historialService->registrarCambio(
                $id,
                'eliminar',
                $datos,
                null,
                $request->motivo ?? 'Eliminación manual',
                auth()->id()
            );

            $horario->forceDelete();

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

    private function crearSnapshotHistorial(Horario $horario): array
    {
        $datos = $horario->toArray();

        $profesor = $horario->profesor;

        $datos['profesor_nombre'] = $profesor
            ? trim(implode(' ', array_filter([
                $profesor->nombre ?? null,
                $profesor->apellido_paterno ?? null,
                $profesor->apellido_materno ?? null,
            ])))
            : null;

        $datos['curso_nombre'] = $horario->curso?->nombre;
        $datos['grado_nombre'] = $horario->grado?->nombre_completo;
        $datos['aula_nombre'] = $horario->aula?->nombre;

        return $datos;
    }
}
