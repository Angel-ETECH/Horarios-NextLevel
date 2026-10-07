<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Horario Académico' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            width: 1680px;
            margin: 0 auto;
            padding: 0;
            background: #f4f8fc;
            color: #0f2749;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .document {
            width: 100%;
            padding: 30px;
            border: 1px solid #dbe5f1;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: none;
        }

        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
            filter: drop-shadow(0 8px 18px rgba(15, 39, 73, 0.12));
        }

        .brand-title {
            color: #0f2749;
            font-size: 38px;
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: -0.8px;
        }

        .brand-title span,
        .title-red,
        .footer-brand span {
            color: #d90808;
        }

        .brand-subtitle {
            margin-top: 8px;
            color: #526884;
            font-size: 20px;
            font-weight: 500;
        }

        .generated {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #0f2749;
            text-align: left;
        }

        .generated-icon,
        .meta-icon,
        .footer-icon {
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #eef5ff;
            color: #0f2749;
            font-size: 20px;
            font-weight: 900;
        }

        .generated-icon svg,
        .meta-icon svg,
        .footer-icon svg {
            width: 24px;
            height: 24px;
            stroke: currentColor;
            stroke-width: 2.4;
            fill: none;
        }

        .generated-label {
            color: #334155;
            font-size: 18px;
            font-weight: 700;
        }

        .generated-date {
            margin-top: 4px;
            color: #0f2749;
            font-size: 22px;
            font-weight: 800;
        }

        .main-title {
            margin: 12px 0 18px;
            color: #0f2749;
            font-size: 42px;
            font-weight: 900;
            line-height: 1.12;
            letter-spacing: -0.9px;
        }

        .meta-card {
            display: grid;
            grid-template-columns: 1.55fr 1fr 1.15fr 1.35fr 1fr;
            gap: 0;
            margin-bottom: 14px;
            border: 1px solid #dbe5f1;
            border-radius: 14px;
            background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            min-height: 74px;
            padding: 16px 20px;
            border-right: 1px solid #cbd8e8;
        }

        .meta-item:last-child {
            border-right: none;
        }

        .meta-label {
            color: #526884;
            font-size: 15px;
            font-weight: 600;
        }

        .meta-value {
            margin-top: 3px;
            color: #0f2749;
            font-size: 17px;
            font-weight: 850;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .schedule {
            width: 100%;
            border-collapse: separate;
            border-spacing: 3px;
            table-layout: fixed;
        }

        .schedule th {
            height: 48px;
            line-height: 1.1;
            border-radius: 7px;
            background: linear-gradient(180deg, #173b68 0%, #0f2749 100%);
            color: #ffffff;
            font-size: 24px;
            font-weight: 850;
            text-align: center;
        }

        .schedule th:first-child {
            width: 172px;
        }

        .schedule th {
            vertical-align: middle;
        }

        .schedule td {
            height: 86px;
            padding: 7px;
            border-radius: 7px;
            background: #f1f6fb;
            vertical-align: top;
            text-align: center;
        }

        .schedule tbody tr {
            height: 86px;
        }

        .time-cell {
            color: #223650;
            font-size: 22px;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
        }

        .class-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            min-height: 72px;
            margin-left: auto;
            margin-right: auto;
            padding: 10px 12px 9px 14px;
            text-align: left;
            border-radius: 9px;
            border: 1px solid #e5eaf1;
            border-left: 5px solid #2563eb;
            background: #fff7f8;
            box-shadow: 0 1px 2px rgba(15, 39, 73, 0.06);
            overflow: hidden;
        }

        .class-card + .class-card {
            margin: 7px auto 0;
        }

        .tone-0,
        .tone-1,
        .tone-2 { background: #fff0f3; border-left-color: #e11d48; }
        .tone-3,
        .tone-4,
        .tone-5 { background: #fff9e8; border-left-color: #f59e0b; }

        .class-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            text-align: left;
        }

        .course {
            display: block;
            color: #071f4f;
            font-size: 17px;
            font-weight: 900;
            line-height: 1.08;
            letter-spacing: -0.15px;
            max-width: 78%;
        }

        .level-badge {
            display: inline-block;
            min-width: 34px;
            padding: 4px 7px;
            border-radius: 6px;
            background: rgba(225, 29, 72, 0.09);
            color: #be123c;
            font-size: 12px;
            font-weight: 900;
            line-height: 1;
            text-align: center;
        }

        .level-sec,
        .level-aca {
            background: rgba(245, 158, 11, 0.14);
            color: #a16207;
        }

        .grade {
            display: block;
            margin-top: 7px;
            color: #0f2749;
            font-size: 17px;
            font-weight: 500;
        }

        .room {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 9px;
            padding-top: 8px;
            border-top: 1px solid rgba(15, 39, 73, 0.08);
            background: transparent;
            color: #0f2749;
        }

        .room-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }

        .room-value {
            color: #071f4f;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.12;
            text-align: right;
            overflow-wrap: break-word;
        }

        .empty-slot {
            color: #64748b;
            font-size: 28px;
            text-align: center;
        }

        .footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            min-height: 58px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid #dbe5f1;
        }

        .footer-left {
            color: #526884;
            font-size: 20px;
        }

        .footer-brand {
            color: #0f2749;
            font-weight: 900;
        }

        .total-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            line-height: 1;
            padding: 12px 18px;
            border-radius: 12px;
            background: linear-gradient(180deg, #12355f 0%, #0f2749 100%);
            color: #ffffff;
            font-size: 19px;
            font-weight: 900;
            box-shadow: none;
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

    $logoPath = public_path('images/logo-next-level.png');
    $logoSrc = is_file($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp

<div class="document" id="horario-container">
    <div class="top">
        <div class="brand">
            @if($logoSrc)
                <img class="brand-logo" src="{{ $logoSrc }}" alt="">
            @endif

            <div>
                <div class="brand-title">Next Level <span>School</span></div>
                <div class="brand-subtitle">Sistema de Gestión Académica</div>
            </div>
        </div>

        <div class="generated">
            <div class="generated-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 3h9l4 4v14H6z"></path>
                    <path d="M14 3v5h5"></path>
                    <path d="M9 13h6"></path>
                    <path d="M9 17h6"></path>
                </svg>
            </div>
            <div>
                <div class="generated-label">Documento generado</div>
                <div class="generated-date">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="main-title">
        {{ $tituloDocumento }}
    </div>

    <div class="meta-card">
        <div class="meta-item">
            <div class="meta-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20 21a8 8 0 0 0-16 0"></path>
                    <circle cx="12" cy="8" r="4"></circle>
                </svg>
            </div>
            <div>
                <div class="meta-label">Profesor</div>
                <div class="meta-value">{{ $nombreProfesor }}</div>
            </div>
        </div>

        <div class="meta-item">
            <div class="meta-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                    <path d="M9 8h6"></path>
                    <path d="M9 12h6"></path>
                    <path d="M9 16h4"></path>
                </svg>
            </div>
            <div>
                <div class="meta-label">Código</div>
                <div class="meta-value">{{ $codigoProfesor }}</div>
            </div>
        </div>

        <div class="meta-item">
            <div class="meta-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 21h18"></path>
                    <path d="M5 21V8l7-4 7 4v13"></path>
                    <path d="M9 21v-6h6v6"></path>
                    <path d="M9 10h.01"></path>
                    <path d="M15 10h.01"></path>
                </svg>
            </div>
            <div>
                <div class="meta-label">Institución</div>
                <div class="meta-value">{{ $nombreInstitucion }}</div>
            </div>
        </div>

        <div class="meta-item">
            <div class="meta-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </div>
            <div>
                <div class="meta-label">Periodo académico</div>
                <div class="meta-value">{{ $periodo }}</div>
            </div>
        </div>

        <div class="meta-item">
            <div class="meta-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3 3 7l9 4 9-4z"></path>
                    <path d="M3 12l9 4 9-4"></path>
                    <path d="M3 17l9 4 9-4"></path>
                </svg>
            </div>
            <div>
                <div class="meta-label">Total</div>
                <div class="meta-value">{{ $horariosOrdenados->count() }} {{ $horariosOrdenados->count() === 1 ? 'clase' : 'clases' }}</div>
            </div>
        </div>
    </div>

    <table class="schedule">
        <thead>
            <tr>
                <th>Hora</th>
                @foreach($dias as $nombreDia)
                    <th>{{ $nombreDia }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($bloques as $bloqueKey => $bloque)
                <tr>
                    <td class="time-cell">{{ $bloque['inicio'] }} - {{ $bloque['fin'] }}</td>
                    @foreach($dias as $diaKey => $nombreDia)
                        <td>
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
                                <div class="class-card tone-{{ $tone }}">
                                    <span class="class-head">
                                        <span class="course">{{ $item->curso->nombre ?? 'Curso no especificado' }}</span>
                                        <span class="level-badge level-{{ $nivelClase }}">{{ $nivelCodigo }}</span>
                                    </span>
                                    <span class="grade">{{ $item->grado->nombre_completo ?? 'Grado no especificado' }}</span>
                                    <span class="room">
                                        <span class="room-label">Aula</span>
                                        <span class="room-value">{{ $aulaTexto }}</span>
                                    </span>
                                </div>
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
        <div class="footer-left">
            <span class="footer-brand">Next Level <span>School</span></span>
            · Sistema de Gestión de Horarios
        </div>

        <div class="total-pill">
            <span class="footer-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3 3 7l9 4 9-4z"></path>
                    <path d="M3 12l9 4 9-4"></path>
                    <path d="M3 17l9 4 9-4"></path>
                </svg>
            </span>
            Total: {{ $horariosOrdenados->count() }} {{ $horariosOrdenados->count() === 1 ? 'clase' : 'clases' }}
        </div>
    </div>
</div>
</body>
</html>
