<?php
// app/Exports/HorarioExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HorarioExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $horarios;
    protected $titulo;
    protected $filtros;

    public function __construct($horarios, $titulo = 'Horario Académico', $filtros = [])
    {
        $this->horarios = $horarios;
        $this->titulo = $titulo;
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $data = collect();

        // Encabezados de información
        $data->push(['INFORMACIÓN DEL HORARIO', '', '', '', '', '', '', '', '', '', '']);
        $data->push(['Título: ' . $this->titulo, '', '', '', '', '', '', '', '', '', '']);
        $data->push(['Fecha de exportación: ' . now()->format('d/m/Y H:i:s'), '', '', '', '', '', '', '', '', '', '']);

        if (!empty($this->filtros)) {
            $data->push(['Filtros aplicados: ' . json_encode($this->filtros, JSON_UNESCAPED_UNICODE), '', '', '', '', '', '', '', '', '', '']);
        }

        $data->push([]);

        // ✅ Encabezados de la tabla
        $data->push([
            'ID',
            'Profesor',
            'Curso',
            'Grado',
            'Aula',
            'Institución',
            'Día',
            'Hora Inicio',
            'Hora Fin',
            'Turno',
            'Estado',
        ]);

        // Datos
        foreach ($this->horarios as $h) {
            $data->push([
                $h->id,
                $h->profesor->nombre_completo ?? 'N/A',
                $h->curso->nombre ?? 'N/A',
                $h->grado->nombre_completo ?? 'N/A',
                $h->aula->nombre ?? 'N/A',
                ucfirst($h->institucion ?? 'N/A'),
                ucfirst($h->dia_semana),
                $this->formatearHora($h->hora_inicio),
                $this->formatearHora($h->hora_fin),
                ucfirst($h->turno),
                ucfirst($h->estado),
            ]);
        }

        // Pie de página
        $data->push([]);
        $data->push(['Total de clases: ' . $this->horarios->count(), '', '', '', '', '', '', '', '', '', '']);

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            3 => ['font' => ['size' => 11]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $filaEncabezado = $this->filaEncabezadoTabla();
                $ultimaFila = $sheet->getHighestRow();

                // Actualizar rangos: ahora 11 columnas (A-K)
                foreach (range('A', 'K') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('A2:K2');
                $sheet->mergeCells('A3:K3');

                if (!empty($this->filtros)) {
                    $sheet->mergeCells('A4:K4');
                }

                // Encabezados de tabla.
                $sheet->getStyle("A{$filaEncabezado}:K{$filaEncabezado}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2C3E50'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Bordes para la tabla.
                $sheet->getStyle("A{$filaEncabezado}:K{$ultimaFila}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Alternar colores
                for ($i = $filaEncabezado + 1; $i <= $ultimaFila; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A' . $i . ':K' . $i)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F2F2F2'],
                            ],
                        ]);
                    }
                }

                // Centrar
                $sheet->getStyle("A{$filaEncabezado}:K{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }

    private function filaEncabezadoTabla(): int
    {
        return empty($this->filtros) ? 5 : 6;
    }

    private function formatearHora($valor): string
    {
        if (!$valor) {
            return 'N/A';
        }

        try {
            return \Carbon\Carbon::parse($valor)->format('H:i');
        } catch (\Throwable $e) {
            return substr((string) $valor, 0, 5);
        }
    }
}
