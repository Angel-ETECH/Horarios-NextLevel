<?php
// app/Http/Controllers/Api/HorarioGeneratorController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HorarioGeneratorService;
use App\Models\Horario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HorarioGeneratorController extends Controller
{
    protected HorarioGeneratorService $generatorService;

    public function __construct(HorarioGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * POST /api/horarios/generar
     * Generar todos los horarios automáticamente
     */
    public function generar(): JsonResponse
    {
        try {
            $resultado = $this->generatorService->generarHorarios();

            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Horarios generados exitosamente',
                    'estadisticas' => $this->generatorService->obtenerEstadisticas(),
                    'conflictos' => $this->generatorService->obtenerConflictos(),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al generar horarios',
                    'error' => $resultado['error'] ?? 'Error desconocido',
                    'conflictos' => $resultado['conflictos'] ?? [],
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar horarios',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/horarios/generar/profesor/{profesorId}
     * Generar horario para un profesor específico
     */
    public function generarPorProfesor(int $profesorId): JsonResponse
    {
        // Implementación específica para un profesor
        // Similar al método general pero filtrando por profesor
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidad en desarrollo',
        ]);
    }

    /**
     * GET /api/horarios/estadisticas
     * Obtener estadísticas de horarios generados
     */
    public function estadisticas(): JsonResponse
    {
        $totalHorarios = Horario::where('estado', 'activo')->count();
        $totalProfesores = Horario::distinct('profesor_id')->count('profesor_id');
        $totalAulas = Horario::distinct('aula_id')->count('aula_id');
        $totalGrados = Horario::distinct('grado_id')->count('grado_id');

        // Horarios por día
        $horariosPorDia = Horario::where('estado', 'activo')
            ->select('dia_semana', \DB::raw('count(*) as total'))
            ->groupBy('dia_semana')
            ->get();

        // Horarios por turno
        $horariosPorTurno = Horario::where('estado', 'activo')
            ->select('turno', \DB::raw('count(*) as total'))
            ->groupBy('turno')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_horarios' => $totalHorarios,
                'total_profesores' => $totalProfesores,
                'total_aulas' => $totalAulas,
                'total_grados' => $totalGrados,
                'horarios_por_dia' => $horariosPorDia,
                'horarios_por_turno' => $horariosPorTurno,
            ]
        ]);
    }

    /**
     * DELETE /api/horarios/limpiar
     * Eliminar todos los horarios generados (soft delete)
     */
    public function limpiar(): JsonResponse
    {
        try {
            $cantidad = Horario::where('estado', 'activo')->delete();

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$cantidad} horarios",
                'total_eliminados' => $cantidad,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar horarios',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
