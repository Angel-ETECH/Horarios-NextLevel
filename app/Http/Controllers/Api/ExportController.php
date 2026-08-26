<?php
// app/Http/Controllers/Api/ExportController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * POST /api/exportar/excel
     * Exportar a Excel
     */
    public function exportExcel(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only(['profesor_id', 'grado_id', 'dia_semana', 'turno']);
            $horarios = $this->exportService->prepareData($filtros);

            if ($horarios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay horarios para exportar',
                ], 404);
            }

            $titulo = $request->titulo ?? 'Horario Académico';
            $archivo = $this->exportService->toExcel($horarios, $titulo, $filtros);

            return response()->json([
                'success' => true,
                'message' => 'Excel generado exitosamente',
                'data' => [
                    'url' => $archivo,
                    'formato' => 'excel',
                    'total_registros' => $horarios->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar Excel',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/exportar/excel/download
     * Descargar Excel directamente
     */
    public function downloadExcel(Request $request)
    {
        try {
            $filtros = $request->only(['profesor_id', 'grado_id', 'dia_semana', 'turno']);
            $horarios = $this->exportService->prepareData($filtros);

            if ($horarios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay horarios para exportar',
                ], 404);
            }

            $titulo = $request->titulo ?? 'Horario Académico';
            return $this->exportService->downloadExcel($horarios, $titulo, $filtros);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al descargar Excel',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/exportar/pdf
     * Exportar a PDF
     */
    public function exportPdf(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only(['profesor_id', 'grado_id', 'dia_semana', 'turno']);
            $horarios = $this->exportService->prepareData($filtros);

            if ($horarios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay horarios para exportar',
                ], 404);
            }

            $titulo = $request->titulo ?? 'Horario Académico';
            $archivo = $this->exportService->toPdf($horarios, $titulo, $filtros);

            return response()->json([
                'success' => true,
                'message' => 'PDF generado exitosamente',
                'data' => [
                    'url' => $archivo,
                    'formato' => 'pdf',
                    'total_registros' => $horarios->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/exportar/pdf/download
     * Descargar PDF directamente
     */
    public function downloadPdf(Request $request)
    {
        try {
            $filtros = $request->only(['profesor_id', 'grado_id', 'dia_semana', 'turno']);
            $horarios = $this->exportService->prepareData($filtros);

            if ($horarios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay horarios para exportar',
                ], 404);
            }

            $titulo = $request->titulo ?? 'Horario Académico';
            return $this->exportService->downloadPdf($horarios, $titulo, $filtros);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al descargar PDF',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/exportar/imagen/html
     * Obtener HTML para captura de imagen (frontend)
     */
    public function getHtmlForImage(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only(['profesor_id', 'grado_id', 'dia_semana', 'turno']);
            $horarios = $this->exportService->prepareData($filtros);

            if ($horarios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay horarios para exportar',
                ], 404);
            }

            $titulo = $request->titulo ?? 'Horario Académico';
            $html = $this->exportService->getHtmlForImage($horarios, $titulo, $filtros);

            return response()->json([
                'success' => true,
                'data' => [
                    'html' => $html,
                    'total_registros' => $horarios->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar HTML para imagen',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/exportar/csv
     * Exportar a CSV
     */
    public function exportCsv(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only(['profesor_id', 'grado_id', 'dia_semana', 'turno']);
            $horarios = $this->exportService->prepareData($filtros);

            if ($horarios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay horarios para exportar',
                ], 404);
            }

            $titulo = $request->titulo ?? 'Horario Académico';
            $archivo = $this->exportService->toCsv($horarios, $titulo);

            return response()->json([
                'success' => true,
                'message' => 'CSV generado exitosamente',
                'data' => [
                    'url' => $archivo,
                    'formato' => 'csv',
                    'total_registros' => $horarios->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar CSV',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
