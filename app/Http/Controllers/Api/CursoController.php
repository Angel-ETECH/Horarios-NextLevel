<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CursoRequest;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CursoController extends Controller
{
    public function index(Request $request)
    {
        $query = Curso::query();

        if ($request->has('nivel')) {
            $query->whereIn('nivel', [$request->nivel, 'todos']);
        }

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->activo);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        $cursos = $query->orderBy('nombre')
                       ->paginate($request->per_page ?? 15);

        return response()->json([
            'status' => 'success',
            'data' => $cursos
        ]);
    }

    public function show($id)
    {
        $curso = Curso::with(['profesores', 'grados'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $curso
        ]);
    }

    public function store(CursoRequest $request)
    {
        try {
            DB::beginTransaction();

            $curso = Curso::create($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Curso creado exitosamente',
                'data' => $curso
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el curso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(CursoRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $curso = Curso::findOrFail($id);
            $curso->update($request->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Curso actualizado exitosamente',
                'data' => $curso->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el curso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $curso = Curso::findOrFail($id);

            // Verificar si tiene asignaciones activas
            if ($curso->profesores()->wherePivot('activo', true)->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se puede eliminar el curso porque tiene asignaciones activas'
                ], 422);
            }

            $curso->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Curso eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el curso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function estadisticas()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => Curso::count(),
                'activos' => Curso::where('activo', true)->count(),
                'por_nivel' => Curso::select('nivel', DB::raw('count(*) as total'))
                                     ->groupBy('nivel')
                                     ->get(),
                'por_tipo' => Curso::select('tipo', DB::raw('count(*) as total'))
                                   ->groupBy('tipo')
                                   ->get()
            ]
        ]);
    }

    public function restore($id)
    {
        try {
            $curso = Curso::withTrashed()->findOrFail($id);
            $curso->restore();

            return response()->json([
                'status' => 'success',
                'message' => 'Curso restaurado exitosamente',
                'data' => $curso
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al restaurar el curso: ' . $e->getMessage()
            ], 500);
        }
    }
}
