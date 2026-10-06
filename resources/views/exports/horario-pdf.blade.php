<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo ?? 'Horario Académico' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 18px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #0f2749;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
        }

        .document {
            width: 100%;
            padding: 4px 0;
        }

        .top-table,
        .meta-table,
        .schedule-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .top-table td {
            vertical-align: middle;
        }

        .brand-title {
            color: #0f2749;
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
        }

        .brand-title span,
        .title-red,
        .footer-brand span {
            color: #d90808;
        }

        .brand-subtitle {
            margin-top: 3px;
            color: #556987;
            font-size: 9px;
            font-weight: 700;
        }

        .generated {
            text-align: right;
            color: #0f2749;
            font-weight: 700;
        }

        .generated-label {
            color: #64748b;
            font-size: 8px;
            font-weight: 700;
        }

        .generated-date {
            margin-top: 2px;
            font-size: 11px;
        }

        .main-title {
            margin: 10px 0 9px;
            color: #0f2749;
            font-size: 19px;
            font-weight: 800;
            line-height: 1.18;
        }

        .meta-wrap {
            margin-bottom: 9px;
            border: 1px solid #dbe5f1;
            border-radius: 9px;
            background: #f4f8fd;
        }

        .meta-table td {
            width: 20%;
            min-height: 34px;
            padding: 9px 10px 10px;
            border-right: 1px solid #cbd8e8;
            vertical-align: top;
        }

        .meta-table td:last-child {
            border-right: 0;
        }

        .meta-label {
            color: #5d708c;
            font-size: 7px;
            font-weight: 700;
            line-height: 1.15;
        }

        .meta-value {
            margin-top: 4px;
            color: #0f2749;
            font-size: 8px;
            font-weight: 800;
            line-height: 1.25;
            word-break: break-word;
        }

        .schedule-table {
            table-layout: fixed;
            border-spacing: 0;
        }

        .schedule-table th {
            height: 32px;
            padding: 8px 4px;
            background: #102d52;
            border: 2px solid #ffffff;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            text-align: center;
            vertical-align: middle;
            line-height: 1.1;
        }

        .schedule-table th.time-cell {
            background: #102d52 !important;
            color: #ffffff;
        }

        .schedule-table td {
            min-height: 48px;
            padding: 5px;
            border: 2px solid #ffffff;
            background: #f4f8fd;
            color: #0f2749;
            vertical-align: middle;
            text-align: center;
        }

        .time-cell {
            width: 11%;
            color: #1f314a;
            background: #eef3f8 !important;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
        }

        .day-cell {
            width: 14.8%;
        }

        .class-card {
            display: block;
            min-height: 42px;
            margin-left: auto;
            margin-right: auto;
            padding: 7px 7px 6px;
            border-radius: 6px;
            border: 1px solid #e5eaf1;
            border-left: 4px solid #2563eb;
            background: #fff7f8;
            line-height: 1.25;
        }

        .class-card + .class-card {
            margin: 4px auto 0;
        }

        .tone-0,
        .tone-1,
        .tone-2 { background: #fff0f3; border-left-color: #e11d48; }
        .tone-3,
        .tone-4,
        .tone-5 { background: #fff9e8; border-left-color: #f59e0b; }

        .class-head {
            display: block;
            text-align: left;
        }

        .course {
            display: inline-block;
            color: #09245a;
            font-size: 9px;
            font-weight: 800;
            line-height: 1.15;
            width: 74%;
        }

        .level-badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 4px;
            background: #fde2e8;
            color: #be123c;
            font-size: 6px;
            font-weight: 800;
            text-align: center;
        }

        .level-sec,
        .level-aca {
            background: #fef3c7;
            color: #92400e;
        }

        .grade {
            display: block;
            margin-top: 3px;
            color: #0f2749;
            font-size: 9px;
        }

        .room {
            display: block;
            margin-top: 5px;
            padding-top: 4px;
            border-top: 1px solid rgba(15, 39, 73, 0.08);
            background: transparent;
            color: #0f2749;
            font-size: 8px;
            font-weight: 800;
        }

        .room-label {
            color: #64748b;
            font-size: 6px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .room-value {
            color: #09245a;
            font-size: 8px;
            font-weight: 800;
        }

        .empty-slot {
            color: #64748b;
            font-size: 15px;
            text-align: center;
        }

        .footer {
            min-height: 34px;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #dbe5f1;
        }

        .footer-left {
            color: #5d708c;
            font-size: 10px;
        }

        .footer-table td {
            vertical-align: middle;
        }

        .footer-brand {
            color: #0f2749;
            font-weight: 800;
        }

        .footer-total {
            text-align: right;
        }

        .total-pill {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 9px;
            background: #102d52;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            line-height: 1.1;
        }
    </style>
</head>
<body>
@php
    $horariosOrdenados = $horarios->sortBy(function ($h) {
        $diaOrden = [
            'lunes' => 1,
            'martes' => 2,
            'miércoles' => 3,
            'miercoles' => 3,
            'jueves' => 4,
            'viernes' => 5,
            'sábado' => 6,
            'sabado' => 6,
        ];

        $dia = strtolower((string) ($h->dia_semana ?? ''));
        $inicio = substr((string) ($h->hora_inicio ?? '99:99'), 0, 5);

        return str_pad((string) ($diaOrden[$dia] ?? 9), 2, '0', STR_PAD_LEFT) . '-' . $inicio;
    })->values();

    $primerHorario = $horariosOrdenados->first();
    $institucionFiltro = $filtros['institucion'] ?? null;
    $nombreInstitucion = match ($institucionFiltro) {
        'colegio' => 'Colegio',
        'academia' => 'Academia',
        default => $primerHorario ? ucfirst($primerHorario->institucion ?? 'No especificada') : 'No especificada',
    };
    $nombreProfesor = $primerHorario?->profesor?->nombre_completo ?? 'No especificado';
    $codigoProfesor = $primerHorario?->profesor?->codigo ?? 'N/A';
    $periodo = $primerHorario?->periodo_academico ?? 'No especificado';
    $tituloBase = $titulo ?? 'Horario de ' . $nombreProfesor;
    $tituloDocumento = preg_match('/-\s*' . preg_quote($nombreInstitucion, '/') . '\s*$/i', $tituloBase)
        ? $tituloBase
        : $tituloBase . ' - ' . $nombreInstitucion;
    $dias = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miércoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sábado' => 'Sábado',
    ];
    $normalizarDia = function ($dia) {
        $dia = strtolower(trim((string) $dia));
        return match ($dia) {
            'miercoles' => 'miércoles',
            'sabado' => 'sábado',
            default => $dia,
        };
    };
    $formatearHora = function ($valor) {
        try {
            return \Carbon\Carbon::parse($valor)->format('H:i');
        } catch (\Exception $e) {
            return substr((string) $valor, 0, 5);
        }
    };
    $bloques = [];
    $celdas = [];

    foreach ($horariosOrdenados as $horario) {
        $inicio = $formatearHora($horario->hora_inicio);
        $fin = $formatearHora($horario->hora_fin);
        $bloqueKey = $inicio . '|' . $fin;
        $diaKey = $normalizarDia($horario->dia_semana ?? '');

        $bloques[$bloqueKey] = [
            'inicio' => $inicio,
            'fin' => $fin,
        ];

        $celdas[$bloqueKey][$diaKey][] = $horario;
    }

    uasort($bloques, fn ($a, $b) => strcmp($a['inicio'], $b['inicio']) ?: strcmp($a['fin'], $b['fin']));

@endphp

<div class="document">
    <table class="top-table">
        <tr>
            <td>
                <div class="brand-title">Next Level <span>School</span></div>
                <div class="brand-subtitle">Sistema de Gestión Académica</div>
            </td>
            <td class="generated">
                <div class="generated-label">Documento generado</div>
                <div class="generated-date">{{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <div class="main-title">
        {{ $tituloDocumento }}
    </div>

    <div class="meta-wrap">
        <table class="meta-table">
            <tr>
                <td>
                    <div class="meta-label">Profesor</div>
                    <div class="meta-value">{{ $nombreProfesor }}</div>
                </td>
                <td>
                    <div class="meta-label">Código</div>
                    <div class="meta-value">{{ $codigoProfesor }}</div>
                </td>
                <td>
                    <div class="meta-label">Institución</div>
                    <div class="meta-value">{{ $nombreInstitucion }}</div>
                </td>
                <td>
                    <div class="meta-label">Periodo académico</div>
                    <div class="meta-value">{{ $periodo }}</div>
                </td>
                <td>
                    <div class="meta-label">Total</div>
                    <div class="meta-value">{{ $horariosOrdenados->count() }} {{ $horariosOrdenados->count() === 1 ? 'clase' : 'clases' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="schedule-table">
        <thead>
            <tr>
                <th class="time-cell">Hora</th>
                @foreach($dias as $nombreDia)
                    <th class="day-cell">{{ $nombreDia }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($bloques as $bloqueKey => $bloque)
                <tr>
                    <td class="time-cell">{{ $bloque['inicio'] }} - {{ $bloque['fin'] }}</td>
                    @foreach($dias as $diaKey => $nombreDia)
                        <td class="day-cell">
                            @forelse(($celdas[$bloqueKey][$diaKey] ?? []) as $item)
                                @php
                                    $tone = abs(crc32((string) ($item->curso_id ?? $item->id ?? 0))) % 6;
                                    $aulaNombre = trim((string) ($item->aula->nombre ?? ''));
                                    $aulaTexto = $aulaNombre !== ''
                                        ? preg_replace('/^\s*aula\s+/i', '', $aulaNombre)
                                        : 'No asignada';
                                    $nivelTexto = strtolower((string) ($item->grado->nombre_completo ?? $item->curso->nombre ?? ''));
                                    $institucionTexto = strtolower((string) ($item->institucion ?? $item->institucion_nombre ?? ''));
                                    $nivelCodigo = str_contains($nivelTexto, 'secundaria')
                                        ? 'SEC'
                                        : (str_contains($nivelTexto, 'academia') || $institucionTexto === 'academia' ? 'ACA' : 'PRI');
                                    $nivelClase = strtolower($nivelCodigo);
                                @endphp
                                <span class="class-card tone-{{ $tone }}">
                                    <span class="class-head">
                                        <span class="course">{{ $item->curso->nombre ?? 'Curso no especificado' }}</span>
                                        <span class="level-badge level-{{ $nivelClase }}">{{ $nivelCodigo }}</span>
                                    </span>
                                    <span class="grade">{{ $item->grado->nombre_completo ?? 'Grado no especificado' }}</span>
                                    <span class="room">
                                        <span class="room-label">Aula</span>
                                        <span class="room-value">{{ $aulaTexto }}</span>
                                    </span>
                                </span>
                            @empty
                                <div class="empty-slot">—</div>
                            @endforelse
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-slot">No existen horarios disponibles para exportar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    <span class="footer-brand">Next Level <span>School</span></span>
                    · Sistema de Gestión de Horarios
                </td>
                <td class="footer-total">
                    <span class="total-pill">Total: {{ $horariosOrdenados->count() }} {{ $horariosOrdenados->count() === 1 ? 'clase' : 'clases' }}</span>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
