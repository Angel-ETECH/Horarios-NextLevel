<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradoRequest;
use App\Models\Grado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradoController extends Controller
{
    public function index(Request $request)
    {
        $query = Grado::query();

        if ($request->has('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        if ($request->has('turno')) {
            $query->where('turno', $request->turno);
        }

        if ($request->has('año_academico')) {
            $query->where('año_academico', $request->año_academico);
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->activo);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'LIKE', "%{$search}%")
                  ->orWhere('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('grado', 'LIKE', "%{$search}%")
                  ->orWhere('seccion', 'LIKE', "%{$search}%");
            });
        }

        $grados = $query->orderBy('nivel')
                       ->orderBy('grado')
                       ->orderBy('seccion')
                       ->paginate($request->per_page ?? 15);

        return response()->json([
            'status' => 'success',
            'data' => $grados
        ]);
    }

    public function show($id)
    {
        $grado = Grado::with(['alumnos', 'cursos', 'horarios'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $grado
        ]);
    }

    public function store(GradoRequest $request)
    {
        try {
            DB::beginTransaction();

            // Generar nombre completo automáticamente
            $data = $request->validated();
            $data['nombre_completo'] = $this->generarNombreCompleto($data);

            $grado = Grado::create($data);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Grado creado exitosamente',
                'data' => $grado
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el grado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(GradoRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $grado = Grado::findOrFail($id);

            $data = $request->validated();
            $data['nombre_completo'] = $this->generarNombreCompleto($data);

            $grado->update($data);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Grado actualizado exitosamente',
                'data' => $grado->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el grado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $grado = Grado::findOrFail($id);

            // Verificar si tiene alumnos
            if ($grado->alumnos()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se puede eliminar el grado porque tiene alumnos asignados'
                ], 422);
            }

            $grado->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Grado eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el grado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function estadisticas()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => Grado::count(),
                'activos' => Grado::where('activo', true)->count(),
                'por_nivel' => Grado::select('nivel', DB::raw('count(*) as total'))
                                    ->groupBy('nivel')
                                    ->get(),
                'por_turno' => Grado::select('turno', DB::raw('count(*) as total'))
                                    ->groupBy('turno')
                                    ->get(),
                'total_estudiantes' => Grado::sum('numero_estudiantes'),
                'capacidad_total' => Grado::sum('capacidad_maxima')
            ]
        ]);
    }

    public function restore($id)
    {
        try {
            $grado = Grado::withTrashed()->findOrFail($id);
            $grado->restore();

            return response()->json([
                'status' => 'success',
                'message' => 'Grado restaurado exitosamente',
                'data' => $grado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al restaurar el grado: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generarNombreCompleto($data)
    {
        $niveles = [
            'primaria' => 'Primaria',
            'secundaria' => 'Secundaria',
            'academia' => 'Academia'
        ];

        return $data['grado'] . ' ' . $niveles[$data['nivel']] . ' ' . $data['seccion'];
    }
}
