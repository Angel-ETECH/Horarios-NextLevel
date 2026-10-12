<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfesorRequest;
use App\Models\Profesor;
use App\Services\HistorialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfesorController extends Controller
{

    public function __construct(
        private HistorialService $historialService
    ) {
    }

    /**
     * Listar todos los profesores con filtros
     */
    public function index(Request $request)
    {
        $query = Profesor::query();

        // Filtros
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('institucion')) {
            $query->whereIn('institucion', [$request->institucion, 'ambos']);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                  ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                  ->orWhere('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('dni', 'LIKE', "%{$search}%");
            });
        }

        $profesores = $query->orderBy('apellido_paterno')
                           ->orderBy('nombre')
                           ->paginate($request->per_page ?? 15);

        return response()->json([
            'status' => 'success',
            'data' => $profesores
        ]);
    }

    /**
     * Obtener un profesor específico
     */
    public function show($id)
    {
        $profesor = Profesor::with(['cursos', 'disponibilidades'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $profesor
        ]);
    }

    /**
     * Crear un nuevo profesor
     */
    public function store(ProfesorRequest $request)
    {
        try {
            DB::beginTransaction();

            $profesor = Profesor::create($request->validated());

            // Calcular carga horaria inicial
            $profesor->carga_horaria_actual = 0;
            $profesor->save();

            DB::commit();

            $this->historialService->registrarEvento(
                'profesores',
                'crear',
                null,
                $this->snapshotProfesor($profesor),
                'Profesor creado'
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Profesor creado exitosamente',
                'data' => $profesor
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el profesor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un profesor
     */
    public function update(ProfesorRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $profesor = Profesor::findOrFail($id);
            $datosAnteriores = $this->snapshotProfesor($profesor);

            $profesor->update($request->validated());

            DB::commit();

            $this->historialService->registrarEvento(
                'profesores',
                'actualizar',
                $datosAnteriores,
                $this->snapshotProfesor($profesor),
                'Profesor actualizado'
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Profesor actualizado exitosamente',
                'data' => $profesor->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el profesor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar (soft delete) un profesor
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $profesor = Profesor::findOrFail($id);

            // Verificar si tiene horarios activos
            if ($profesor->horarios()->where('estado', 'activo')->count() > 0) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'No se puede eliminar el profesor porque tiene horarios activos'
                ], 422);
            }

            $datosAnteriores = $this->snapshotProfesor($profesor);

            $profesor->delete();

            DB::commit();

            $this->historialService->registrarEvento(
                'profesores',
                'eliminar',
                $datosAnteriores,
                null,
                'Profesor eliminado'
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Profesor eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el profesor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de profesores
     */
    public function estadisticas()
    {
        $total = Profesor::count();
        $activos = Profesor::where('estado', 'activo')->count();
        $porInstitucion = Profesor::select('institucion', DB::raw('count(*) as total'))
                                  ->groupBy('institucion')
                                  ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $total,
                'activos' => $activos,
                'inactivos' => Profesor::where('estado', 'inactivo')->count(),
                'licencia' => Profesor::where('estado', 'licencia')->count(),
                'por_institucion' => $porInstitucion
            ]
        ]);
    }

    /**
     * Restaurar un profesor eliminado
     */
    public function restore($id)
    {
        try {
            $profesor = Profesor::withTrashed()->findOrFail($id);
            $datosAnteriores = $this->snapshotProfesor($profesor);

            $profesor->restore();

            $this->historialService->registrarEvento(
                'profesores',
                'restaurar',
                $datosAnteriores,
                $this->snapshotProfesor($profesor),
                'Profesor restaurado'
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Profesor restaurado exitosamente',
                'data' => $profesor
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al restaurar el profesor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Snapshot del profesor para el historial.
     */
    private function snapshotProfesor(Profesor $profesor): array
    {
        return array_merge($profesor->toArray(), [
            'modulo' => 'profesores',
            'tipo_registro' => 'profesor',
            'profesor_nombre' => $profesor->nombre_completo,
            'institucion' => $profesor->institucion,
        ]);
    }
}
