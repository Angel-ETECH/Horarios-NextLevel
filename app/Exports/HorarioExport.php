<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class HorarioExport implements WithMultipleSheets
{
    protected $horarios;
    protected string $titulo;
    protected array $filtros;

    public function __construct($horarios, $titulo = 'Horario Academico', $filtros = [])
    {
        $this->horarios = $horarios;
        $this->titulo = $titulo;
        $this->filtros = $filtros;
    }

    public function sheets(): array
    {
        return [
            new HorarioMatrizSheet($this->horarios, $this->titulo, $this->filtros),
            new HorarioDetalleSheet($this->horarios, $this->titulo, $this->filtros),
        ];
    }
}

class HorarioMatrizSheet implements FromArray, WithEvents, WithTitle
{
    private const HEADER_ROW = 7;
    private const FIRST_DATA_ROW = 8;

    private array $dias = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miércoles' => 'Miercoles',
        'miercoles' => 'Miercoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sábado' => 'Sabado',
        'sabado' => 'Sabado',
    ];

    private array $cellStyles = [];
    private array $rowHeights = [];

    public function __construct(
        private $horarios,
        private string $titulo = 'Horario Academico',
        private array $filtros = []
    ) {
    }

    public function title(): string
    {
        return 'Horario';
    }

    public function array(): array
    {
        $this->cellStyles = [];
        $this->rowHeights = [];

        $rows = [
            ['Next Level School', '', '', '', '', 'Generado', now()->format('d/m/Y H:i')],
            ['Sistema de Gestion Academica', '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
            [$this->titulo, '', '', '', '', '', ''],
            [
                "Consulta\n" . $this->valorPrincipal(),
                '',
                "Institucion\n" . $this->valorInstitucion(),
                '',
                "Periodo academico\n" . $this->valorPeriodo(),
                '',
                "Total\n" . $this->horarios->count() . ' clases',
            ],
            ['', '', '', '', '', '', ''],
            ['Hora', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
        ];

        $slots = $this->obtenerFranjas();
        $horariosPorDiaYHora = $this->agruparHorarios();

        foreach ($slots as $slot) {
            $rowNumber = count($rows) + 1;
            $row = [$slot['label']];
            $maxItems = 1;

            foreach (array_keys($this->diasCanonicos()) as $index => $dia) {
                $items = $horariosPorDiaYHora[$dia][$slot['key']] ?? [];
                $columna = Coordinate::stringFromColumnIndex($index + 2);
                $coordenada = $columna . $rowNumber;

                if (empty($items)) {
                    $row[] = '-';
                    $this->cellStyles[$coordenada] = [
                        'type' => 'empty',
                    ];
                    continue;
                }

                $maxItems = max($maxItems, count($items));
                $row[] = collect($items)
                    ->map(fn ($horario) => $this->textoBloque($horario))
                    ->implode("\n\n");

                $this->cellStyles[$coordenada] = [
                    'type' => 'class',
                    'palette' => $this->paletaHorario($items[0]),
                    'count' => count($items),
                ];
            }

            $alturaBase = $this->esExportacionProfesor() ? 62 : 74;
            $this->rowHeights[$rowNumber] = min(132, $alturaBase + (($maxItems - 1) * 32));
            $rows[] = $row;
        }

        if (empty($slots)) {
            $rows[] = ['Sin horarios', '-', '-', '-', '-', '-', '-'];
            $this->rowHeights[self::FIRST_DATA_ROW] = 58;
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->setShowGridlines(false);
                $sheet->setSelectedCell('A1');
                $sheet->unfreezePane();
                $sheet->getTabColor()->setRGB('102A4C');

                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);
                $sheet->getPageMargins()
                    ->setTop(0.35)
                    ->setRight(0.25)
                    ->setBottom(0.35)
                    ->setLeft(0.25);

                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('F1:G1');
                $sheet->mergeCells('A4:G4');
                $sheet->mergeCells('A5:B5');
                $sheet->mergeCells('C5:D5');
                $sheet->mergeCells('E5:F5');

                $this->aplicarAnchos($sheet);
                $this->aplicarEstilosBase($sheet, $highestRow);
                $this->aplicarEstilosCeldas($sheet);
            },
        ];
    }

    private function aplicarAnchos($sheet): void
    {
        $sheet->getColumnDimension('A')->setWidth(20);

        foreach (range('B', 'G') as $columna) {
            $sheet->getColumnDimension($columna)->setWidth(34);
        }

        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(21);
        $sheet->getRowDimension(4)->setRowHeight(36);
        $sheet->getRowDimension(5)->setRowHeight(48);
        $sheet->getRowDimension(self::HEADER_ROW)->setRowHeight(34);

        foreach ($this->rowHeights as $row => $height) {
            $sheet->getRowDimension($row)->setRowHeight($height);
        }
    }

    private function aplicarEstilosBase($sheet, int $highestRow): void
    {
        $sheet->getStyle("A1:G{$highestRow}")->applyFromArray([
            'font' => [
                'name' => 'Aptos',
                'color' => ['rgb' => '0B2345'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 24, 'color' => ['rgb' => '0B2345']],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 12, 'color' => ['rgb' => '60738F']],
        ]);
        $sheet->getStyle('F1:G1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->getStyle('G1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        $sheet->getStyle('A4:G4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 22, 'color' => ['rgb' => '0B2345']],
        ]);

        $sheet->getStyle('A5:G5')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F7FC'],
            ],
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D8E3F2'],
                ],
            ],
        ]);

        $sheet->getStyle('A' . self::HEADER_ROW . ':G' . self::HEADER_ROW)->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '102A4C'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        $sheet->getStyle('A' . self::FIRST_DATA_ROW . ':A' . $highestRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEF4FA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A' . self::FIRST_DATA_ROW . ':G' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);
    }

    private function aplicarEstilosCeldas($sheet): void
    {
        foreach ($this->cellStyles as $coordenada => $style) {
            if (($style['type'] ?? '') === 'empty') {
                $sheet->getStyle($coordenada)->applyFromArray([
                    'font' => ['size' => 12, 'color' => ['rgb' => '7D8EA6']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'EEF4FA'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                continue;
            }

            $palette = $style['palette'];
            $sheet->getStyle($coordenada)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 10,
                    'color' => ['rgb' => '0B2345'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $palette['fill']],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['rgb' => $palette['accent']],
                    ],
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => $palette['border']],
                    ],
                ],
            ]);
        }
    }

    private function agruparHorarios(): array
    {
        $agrupados = [];

        foreach ($this->horarios as $horario) {
            $dia = $this->normalizarDia($horario->dia_semana ?? '');
            $slot = $this->slotKey($horario);

            if (!isset($this->dias[$dia]) || !$slot) {
                continue;
            }

            $agrupados[$dia][$slot][] = $horario;
        }

        foreach ($agrupados as $dia => $porHora) {
            foreach ($porHora as $slot => $items) {
                usort($items, fn ($a, $b) => strcmp(
                    $this->nombreCurso($a) . $this->nombreProfesor($a),
                    $this->nombreCurso($b) . $this->nombreProfesor($b)
                ));
                $agrupados[$dia][$slot] = $items;
            }
        }

        return $agrupados;
    }

    private function obtenerFranjas(): array
    {
        return $this->horarios
            ->map(function ($horario) {
                $inicio = $this->hora($horario->hora_inicio);
                $fin = $this->hora($horario->hora_fin);

                if (!$inicio || !$fin) {
                    return null;
                }

                return [
                    'key' => $inicio . '-' . $fin,
                    'label' => $inicio . ' - ' . $fin,
                    'orden' => $this->minutos($inicio),
                ];
            })
            ->filter()
            ->unique('key')
            ->sortBy(fn ($slot) => sprintf('%04d-%s', $slot['orden'], $slot['label']))
            ->values()
            ->all();
    }

    private function textoBloque($horario): string
    {
        $lineas = [
            $this->nombreCurso($horario),
            $this->nombreGrado($horario),
        ];

        if (!$this->esExportacionProfesor()) {
            $lineas[] = $this->nombreProfesor($horario);
        }

        $lineas[] = 'Aula: ' . $this->nombreAula($horario);

        return implode("\n", array_filter($lineas));
    }

    private function esExportacionProfesor(): bool
    {
        return !empty($this->filtros['profesor_id']);
    }

    private function paletaHorario($horario): array
    {
        $nivel = strtolower((string) ($horario->grado->nivel ?? $horario->grado->nombre ?? ''));

        if (str_contains($nivel, 'sec')) {
            return ['fill' => 'FFF7DF', 'border' => 'F4D58A', 'accent' => 'F59E0B'];
        }

        if (str_contains($nivel, 'academ')) {
            return ['fill' => 'ECF2FF', 'border' => 'B9C8FF', 'accent' => '4F46E5'];
        }

        return ['fill' => 'FFF1F4', 'border' => 'F8BBD0', 'accent' => 'E11D48'];
    }

    private function diasCanonicos(): array
    {
        return [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miércoles' => 'Miercoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sábado' => 'Sabado',
        ];
    }

    private function valorPrincipal(): string
    {
        $profesores = $this->horarios
            ->map(fn ($h) => $this->nombreProfesor($h))
            ->filter(fn ($nombre) => $nombre !== 'Sin profesor')
            ->unique()
            ->values();

        if ($profesores->count() === 1) {
            return $profesores->first();
        }

        $grados = $this->horarios
            ->map(fn ($h) => $this->nombreGrado($h))
            ->filter(fn ($nombre) => $nombre !== 'Sin grado')
            ->unique()
            ->values();

        if ($grados->count() === 1) {
            return $grados->first();
        }

        $aulas = $this->horarios
            ->map(fn ($h) => $this->nombreAula($h))
            ->filter(fn ($nombre) => $nombre !== 'Sin aula')
            ->unique()
            ->values();

        return $aulas->count() === 1 ? $aulas->first() : 'Consulta general';
    }

    private function valorInstitucion(): string
    {
        $instituciones = $this->horarios
            ->pluck('institucion')
            ->filter()
            ->map(fn ($institucion) => ucfirst((string) $institucion))
            ->unique()
            ->values();

        if (!empty($this->filtros['institucion'])) {
            return ucfirst((string) $this->filtros['institucion']);
        }

        return $instituciones->count() === 1 ? $instituciones->first() : 'General';
    }

    private function valorPeriodo(): string
    {
        if (!empty($this->filtros['periodo_academico'])) {
            return (string) $this->filtros['periodo_academico'];
        }

        $periodos = $this->horarios
            ->pluck('periodo_academico')
            ->filter()
            ->unique()
            ->values();

        return $periodos->count() === 1 ? $periodos->first() : 'Varios';
    }

    private function slotKey($horario): ?string
    {
        $inicio = $this->hora($horario->hora_inicio);
        $fin = $this->hora($horario->hora_fin);

        return $inicio && $fin ? $inicio . '-' . $fin : null;
    }

    private function normalizarDia(string $dia): string
    {
        $dia = strtolower(trim($dia));

        return match ($dia) {
            'miercoles' => 'miércoles',
            'sabado' => 'sábado',
            default => $dia,
        };
    }

    private function hora($valor): ?string
    {
        if (!$valor) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($valor)->format('H:i');
        } catch (\Throwable $e) {
            return substr((string) $valor, 0, 5) ?: null;
        }
    }

    private function minutos(string $hora): int
    {
        [$h, $m] = array_map('intval', explode(':', $hora));

        return ($h * 60) + $m;
    }

    private function nombreProfesor($horario): string
    {
        return $horario->profesor->nombre_completo ?? 'Sin profesor';
    }

    private function nombreCurso($horario): string
    {
        return $horario->curso->nombre ?? 'Sin curso';
    }

    private function nombreGrado($horario): string
    {
        return $horario->grado->nombre_completo ?? 'Sin grado';
    }

    private function nombreAula($horario): string
    {
        return $horario->aula->nombre ?? 'Sin aula';
    }
}

class HorarioDetalleSheet implements FromArray, WithEvents, WithTitle
{
    private const HEADER_ROW = 6;

    public function __construct(
        private $horarios,
        private string $titulo = 'Horario Academico',
        private array $filtros = []
    ) {
    }

    public function title(): string
    {
        return 'Detalle';
    }

    public function array(): array
    {
        $rows = [
            ['Next Level School', '', '', '', '', '', '', 'Generado', now()->format('d/m/Y H:i'), ''],
            ['Sistema de Gestion Academica', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            [$this->titulo, '', '', '', '', '', '', '', '', ''],
            ['Institucion: ' . $this->valorInstitucion(), '', 'Total: ' . $this->horarios->count() . ' clases', '', 'Periodo: ' . $this->valorPeriodo(), '', '', '', '', ''],
            ['Institucion', 'Dia', 'Hora inicio', 'Hora fin', 'Profesor', 'Curso', 'Grado', 'Aula', 'Turno', 'Estado'],
        ];

        foreach ($this->horarios as $h) {
            $rows[] = [
                ucfirst($h->institucion ?? 'N/A'),
                ucfirst($h->dia_semana ?? 'N/A'),
                $this->hora($h->hora_inicio),
                $this->hora($h->hora_fin),
                $h->profesor->nombre_completo ?? 'N/A',
                $h->curso->nombre ?? 'N/A',
                $h->grado->nombre_completo ?? 'N/A',
                $h->aula->nombre ?? 'N/A',
                ucfirst($h->turno ?? 'N/A'),
                ucfirst($h->estado ?? 'N/A'),
            ];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->setShowGridlines(false);
                $sheet->setSelectedCell('A1');
                $sheet->unfreezePane();
                $sheet->getTabColor()->setRGB('1D4ED8');
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->mergeCells('A4:J4');
                $sheet->mergeCells('A5:B5');
                $sheet->mergeCells('C5:D5');
                $sheet->mergeCells('E5:F5');
                $sheet->setAutoFilter('A' . self::HEADER_ROW . ':J' . $highestRow);

                $sheet->getStyle("A1:J{$highestRow}")->applyFromArray([
                    'font' => [
                        'name' => 'Aptos',
                        'color' => ['rgb' => '0B2345'],
                    ],
                ]);

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 21],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 11, 'color' => ['rgb' => '60738F']],
                ]);
                $sheet->getStyle('A4:J4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '0B2345']],
                ]);
                $sheet->getStyle('A5:J5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '0B2345']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F7FC'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D8E3F2'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A' . self::HEADER_ROW . ':J' . self::HEADER_ROW)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '102A4C'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A' . self::HEADER_ROW . ':J' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'DDE6F2'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                for ($row = self::HEADER_ROW + 1; $row <= $highestRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F6F9FC'],
                            ],
                        ]);
                    }
                }

                $widths = [
                    'A' => 16,
                    'B' => 14,
                    'C' => 13,
                    'D' => 13,
                    'E' => 32,
                    'F' => 30,
                    'G' => 24,
                    'H' => 30,
                    'I' => 14,
                    'J' => 14,
                ];

                foreach ($widths as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(4)->setRowHeight(30);
                $sheet->getRowDimension(5)->setRowHeight(34);
                $sheet->getRowDimension(self::HEADER_ROW)->setRowHeight(26);

                for ($row = self::HEADER_ROW + 1; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(26);
                }
            },
        ];
    }

    private function hora($valor): string
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

    private function valorInstitucion(): string
    {
        if (!empty($this->filtros['institucion'])) {
            return ucfirst((string) $this->filtros['institucion']);
        }

        $instituciones = $this->horarios
            ->pluck('institucion')
            ->filter()
            ->map(fn ($institucion) => ucfirst((string) $institucion))
            ->unique()
            ->values();

        return $instituciones->count() === 1 ? $instituciones->first() : 'General';
    }

    private function valorPeriodo(): string
    {
        if (!empty($this->filtros['periodo_academico'])) {
            return (string) $this->filtros['periodo_academico'];
        }

        $periodos = $this->horarios
            ->pluck('periodo_academico')
            ->filter()
            ->unique()
            ->values();

        return $periodos->count() === 1 ? $periodos->first() : 'Varios';
    }
}
