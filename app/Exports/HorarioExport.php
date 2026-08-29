<?php
// app/Exports/HorarioExport.php

namespace App\Exports;

use App\Models\Horario;
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

        // Agregar encabezados de información
        $data->push([
            'INFORMACIÓN DEL HORARIO',
            '',
            '',
            '',
            '',
            '',
        ]);
        $data->push([
            'Título: ' . $this->titulo,
            '',
            '',
            '',
            '',
            '',
        ]);
        $data->push([
            'Fecha de exportación: ' . now()->format('d/m/Y H:i:s'),
            '',
            '',
            '',
            '',
            '',
        ]);

        if (!empty($this->filtros)) {
            $data->push([
                'Filtros aplicados: ' . json_encode($this->filtros),
                '',
                '',
                '',
                '',
                '',
            ]);
        }

        $data->push([]); // Fila vacía

        // Encabezados de la tabla
        $data->push([
            'ID',
            'Profesor',
            'Curso',
            'Grado',
            'Aula',
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
                ucfirst($h->dia_semana),
                $h->hora_inicio,
                $h->hora_fin,
                ucfirst($h->turno),
                ucfirst($h->estado),
            ]);
        }

        // Agregar pie de página
        $data->push([]);
        $data->push([
            'Total de clases: ' . $this->horarios->count(),
            '',
            '',
            '',
            '',
            '',
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para el título
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

                // Autoajustar columnas
                foreach (range('A', 'J') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Estilo para encabezados de tabla (fila 5)
                $sheet->getStyle('A5:J5')->applyFromArray([
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

                // Bordes para la tabla
                $ultimaFila = $sheet->getHighestRow();
                $sheet->getStyle('A5:J' . $ultimaFila)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Alternar colores para filas de datos
                for ($i = 6; $i <= $ultimaFila; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A' . $i . ':J' . $i)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F2F2F2'],
                            ],
                        ]);
                    }
                }

                // Centrar contenido de la tabla
                $sheet->getStyle('A5:J' . $ultimaFila)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }
}
