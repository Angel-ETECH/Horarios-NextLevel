<?php
// app/Services/ExportService.php

namespace App\Services;

use App\Exports\HorarioExport;
use App\Models\Horario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class ExportService
{
    protected string $path = 'exports/';

    /**
     * Preparar datos para la exportación
     */
    public function prepareData(array $params): Collection
    {
        $query = Horario::with(['profesor', 'curso', 'aula', 'grado'])
            ->where('estado', 'activo');

        // ========== FILTROS EXISTENTES ==========
        if (!empty($params['profesor_id'])) {
            $query->where('profesor_id', $params['profesor_id']);
        }

        if (!empty($params['grado_id'])) {
            $query->where('grado_id', $params['grado_id']);
        }

        if (!empty($params['dia_semana'])) {
            $query->where('dia_semana', $params['dia_semana']);
        }

        if (!empty($params['turno'])) {
            $query->where('turno', $params['turno']);
        }

        // ========== NUEVOS FILTROS ==========
        if (!empty($params['curso_id'])) {
            $query->where('curso_id', $params['curso_id']);
        }

        if (!empty($params['aula_id'])) {
            $query->where('aula_id', $params['aula_id']);
        }

        if (!empty($params['institucion'])) {
            $query->where('institucion', $params['institucion']);
        }

        if (!empty($params['periodo_academico'])) {
            $query->where('periodo_academico', $params['periodo_academico']);
        }

        if (!empty($params['tipo'])) {
            $query->where('tipo', $params['tipo']);
        }

        // Filtro por rango de fechas
        if (!empty($params['fecha_inicio']) && !empty($params['fecha_fin'])) {
            $query->whereBetween('created_at', [$params['fecha_inicio'], $params['fecha_fin']]);
        }

        // Búsqueda general
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('profesor', function ($sub) use ($search) {
                    $sub->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('curso', function ($sub) use ($search) {
                    $sub->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('codigo', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('grado', function ($sub) use ($search) {
                    $sub->where('nombre_completo', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('aula', function ($sub) use ($search) {
                    $sub->where('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('codigo', 'LIKE', "%{$search}%");
                });
            });
        }

        // Ordenamiento
        $sortBy = $params['sort_by'] ?? 'dia_semana';
        $sortOrder = $params['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder)
              ->orderBy('hora_inicio', 'asc');

        return $query->get();
    }

    /**
     * Obtener los filtros disponibles
     */
    public function getFiltrosDisponibles(): array
    {
        return [
            'profesor_id' => ['tipo' => 'integer', 'descripcion' => 'ID del profesor'],
            'curso_id' => ['tipo' => 'integer', 'descripcion' => 'ID del curso'],
            'grado_id' => ['tipo' => 'integer', 'descripcion' => 'ID del grado'],
            'aula_id' => ['tipo' => 'integer', 'descripcion' => 'ID del aula'],
            'dia_semana' => ['tipo' => 'string', 'valores' => ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado']],
            'turno' => ['tipo' => 'string', 'valores' => ['mañana', 'tarde', 'noche']],
            'institucion' => ['tipo' => 'string', 'valores' => ['colegio', 'academia']],
            'periodo_academico' => ['tipo' => 'string', 'ejemplo' => '2026-2027'],
            'tipo' => ['tipo' => 'string', 'valores' => ['regular', 'recuperacion', 'cambio']],
            'fecha_inicio' => ['tipo' => 'date', 'formato' => 'Y-m-d'],
            'fecha_fin' => ['tipo' => 'date', 'formato' => 'Y-m-d'],
            'search' => ['tipo' => 'string', 'descripcion' => 'Búsqueda general'],
            'sort_by' => ['tipo' => 'string', 'valores' => ['dia_semana', 'hora_inicio', 'profesor_id', 'grado_id']],
            'sort_order' => ['tipo' => 'string', 'valores' => ['asc', 'desc']],
        ];
    }

    /**
     * Exportar a Excel
     */
    public function toExcel(Collection $horarios, string $titulo = 'Horario Académico', array $filtros = []): string
    {
        $export = new HorarioExport($horarios, $titulo, $filtros);

        $nombre = 'horario_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $ruta = $this->path . $nombre;

        Excel::store($export, $ruta, 'public');

        return Storage::url($ruta);
    }

    /**
     * Exportar a Excel y descargar directamente
     */
    public function downloadExcel(Collection $horarios, string $titulo = 'Horario Académico', array $filtros = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $export = new HorarioExport($horarios, $titulo, $filtros);
        $nombre = 'horario_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download($export, $nombre);
    }

    /**
     * Exportar a PDF
     */
    public function toPdf(Collection $horarios, string $titulo = 'Horario Académico', array $filtros = []): string
    {
        $pdf = PDF::loadView('exports.horario-pdf', [
            'horarios' => $horarios,
            'titulo' => $titulo,
            'filtros' => $filtros,
        ]);

        $nombre = 'horario_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        $ruta = $this->path . $nombre;

        Storage::put('public/' . $ruta, $pdf->output());

        return Storage::url($ruta);
    }

    /**
     * Exportar a PDF y descargar directamente
     */
    public function downloadPdf(Collection $horarios, string $titulo = 'Horario Académico', array $filtros = []): \Illuminate\Http\Response
    {
        $pdf = PDF::loadView('exports.horario-pdf', [
            'horarios' => $horarios,
            'titulo' => $titulo,
            'filtros' => $filtros,
        ]);

        $nombre = 'horario_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($nombre);
    }

    /**
     * Obtener HTML para captura de imagen (frontend)
     */
    public function getHtmlForImage(Collection $horarios, string $titulo = 'Horario Académico', array $filtros = []): string
    {
        return View::make('exports.horario-imagen', [
            'horarios' => $horarios,
            'titulo' => $titulo,
            'filtros' => $filtros,
        ])->render();
    }

    /**
     * Exportar a CSV
     */
    public function toCsv(Collection $horarios, string $titulo = 'Horario Académico'): string
    {
        $nombre = 'horario_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $ruta = $this->path . $nombre;

        $handle = fopen(storage_path('app/public/' . $ruta), 'w');

        // Encabezados de la tabla
        fputcsv($handle, [
            'ID', 'Profesor', 'Curso', 'Grado', 'Aula', 'Institución', 'Día', 'Hora Inicio', 'Hora Fin', 'Turno', 'Estado'
        ]);

        // Datos
        foreach ($horarios as $h) {
            fputcsv($handle, [
                $h->id,
                $h->profesor->nombre_completo ?? 'N/A',
                $h->curso->nombre ?? 'N/A',
                $h->grado->nombre_completo ?? 'N/A',
                $h->aula->nombre ?? 'N/A',
                ucfirst($h->institucion ?? 'N/A'),
                $h->dia_semana,
                $h->hora_inicio,
                $h->hora_fin,
                $h->turno,
                $h->estado,
            ]);
        }

        fclose($handle);

        return Storage::url($ruta);
    }

}
