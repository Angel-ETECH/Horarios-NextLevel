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

        // Encabezados
        fputcsv($handle, [
            'ID', 'Profesor', 'Curso', 'Grado', 'Aula', 'Día', 'Hora Inicio', 'Hora Fin', 'Turno', 'Estado'
        ]);

        // Datos
        foreach ($horarios as $h) {
            fputcsv($handle, [
                $h->id,
                $h->profesor->nombre_completo ?? 'N/A',
                $h->curso->nombre ?? 'N/A',
                $h->grado->nombre_completo ?? 'N/A',
                $h->aula->nombre ?? 'N/A',
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

    /**
     * Preparar datos para exportación
     */
    public function prepareData(array $params): Collection
    {
        $query = Horario::with(['profesor', 'curso', 'aula', 'grado'])
            ->where('estado', 'activo');

        if (isset($params['profesor_id'])) {
            $query->where('profesor_id', $params['profesor_id']);
        }

        if (isset($params['grado_id'])) {
            $query->where('grado_id', $params['grado_id']);
        }

        if (isset($params['dia_semana'])) {
            $query->where('dia_semana', $params['dia_semana']);
        }

        if (isset($params['turno'])) {
            $query->where('turno', $params['turno']);
        }

        return $query->get();
    }
}
