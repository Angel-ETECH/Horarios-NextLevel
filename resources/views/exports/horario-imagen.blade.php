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
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #F8FAFC;
            padding: 24px;
            width: 1200px;
            margin: 0 auto;
            color: #1E293B;
        }

        .container {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(15, 39, 73, 0.08);
            padding: 32px;
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .header {
            margin-bottom: 24px;
        }

        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            font-size: 30px;
            font-weight: 800;
            color: #0F2749;
            letter-spacing: -0.5px;
        }

        .brand span {
            color: #DB0808;
        }

        .brand-subtitle {
            margin-top: 5px;
            font-size: 12px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            font-weight: 700;
        }

        .generated {
            text-align: right;
        }

        .generated-label {
            color: #94A3B8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }

        .generated-date {
            margin-top: 4px;
            color: #0F2749;
            font-size: 13px;
            font-weight: 700;
        }

        .brand-line {
            width: 100%;
            height: 5px;
            margin-top: 18px;
            background: #0F2749;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }

        .brand-line::before {
            content: "";
            display: block;
            width: 120px;
            height: 100%;
            background: #DB0808;
        }

        /* =========================================================
           TITLE
        ========================================================= */

        .title-section {
            margin-bottom: 20px;
        }

        .title-label {
            color: #DB0808;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .title {
            margin-top: 5px;
            color: #0F2749;
            font-size: 26px;
            line-height: 1.2;
            font-weight: 800;
        }

        .description {
            margin-top: 6px;
            color: #64748B;
            font-size: 13px;
        }

        /* =========================================================
           INFO CARD
        ========================================================= */

        .info-card {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            padding: 16px 18px;
            margin-bottom: 22px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-left: 5px solid #1B3A6B;
            border-radius: 12px;
        }

        .info-item {
            min-width: 0;
        }

        .info-label {
            color: #64748B;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            margin-top: 4px;
            color: #0F2749;
            font-size: 13px;
            font-weight: 800;
            word-break: break-word;
        }

        /* =========================================================
           TABLE
        ========================================================= */

        .table-wrapper {
            width: 100%;
            overflow: hidden;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead th {
            background: #0F2749;
            color: #FFFFFF;
            padding: 11px 6px;
            text-align: center;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-right: 1px solid rgba(255,255,255,0.12);
        }

        thead th:last-child {
            border-right: none;
        }

        tbody td {
            padding: 10px 6px;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #E2E8F0;
            border-right: 1px solid #E2E8F0;
            font-size: 11px;
            color: #334155;
            word-break: break-word;
        }

        tbody td:last-child {
            border-right: none;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:nth-child(even) {
            background: #F8FAFC;
        }

        .col-numero {
            width: 4%;
        }

        .col-profesor {
            width: 15%;
        }

        .col-curso {
            width: 15%;
        }

        .col-grado {
            width: 13%;
        }

        .col-aula {
            width: 10%;
        }

        .col-institucion {
            width: 9%;
        }

        .col-dia {
            width: 8%;
        }

        .col-hora {
            width: 7%;
        }

        .col-turno {
            width: 6%;
        }

        .col-estado {
            width: 6%;
        }

        /* =========================================================
           CONTENT
        ========================================================= */

        .profesor-name {
            color: #0F2749;
            font-weight: 800;
        }

        .curso-name {
            color: #1B3A6B;
            font-weight: 800;
        }

        .aula-name {
            color: #334155;
            font-weight: 800;
        }

        .codigo {
            display: block;
            margin-top: 3px;
            color: #94A3B8;
            font-size: 9px;
            font-weight: 600;
        }

        .hora {
            color: #0F2749;
            font-weight: 800;
            white-space: nowrap;
        }

        /* =========================================================
           BADGES
        ========================================================= */

        .badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .badge-activo {
            background: #DCFCE7;
            color: #166534;
        }

        .badge-modificado {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-cancelado {
            background: #FEE2E2;
            color: #991B1B;
        }

        .badge-default {
            background: #E2E8F0;
            color: #475569;
        }

        /* =========================================================
           EMPTY
        ========================================================= */

        .empty {
            padding: 40px !important;
            color: #94A3B8 !important;
            font-size: 13px !important;
            text-align: center !important;
        }

        /* =========================================================
           FOOTER
        ========================================================= */

        .footer {
            margin-top: 22px;
            padding-top: 16px;
            border-top: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .footer-left {
            color: #64748B;
            font-size: 11px;
        }

        .footer-left strong {
            color: #0F2749;
        }

        .footer-total {
            background: #0F2749;
            color: #FFFFFF;
            padding: 9px 14px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 800;
        }

        .footer-total span {
            font-size: 16px;
        }
    </style>
</head>

<body>

@php
    $primerHorario = $horarios->first();

    $institucionFiltro = $filtros['institucion'] ?? null;

    $nombreInstitucion = match($institucionFiltro) {
        'colegio' => 'Colegio',
        'academia' => 'Academia',
        default => $primerHorario
            ? ucfirst($primerHorario->institucion ?? 'No especificada')
            : 'No especificada',
    };

    $nombreProfesor =
        $primerHorario?->profesor?->nombre_completo ?? 'No especificado';

    $codigoProfesor =
        $primerHorario?->profesor?->codigo ?? 'N/A';

    $periodo =
        $primerHorario?->periodo_academico ?? 'No especificado';
@endphp

<div
    class="container"
    id="horario-container"
>

    {{-- =====================================================
         HEADER
    ====================================================== --}}

    <div class="header">

        <div class="header-top">

            <div>

                <div class="brand">
                    Next Level <span>School</span>
                </div>

                <div class="brand-subtitle">
                    Sistema de Gestión Académica
                </div>

            </div>


            <div class="generated">

                <div class="generated-label">
                    Documento generado
                </div>

                <div class="generated-date">
                    {{ now()->format('d/m/Y H:i') }}
                </div>

            </div>

        </div>


        <div class="brand-line"></div>

    </div>


    {{-- =====================================================
         TITLE
    ====================================================== --}}

    <div class="title-section">

        <div class="title-label">
            Horario académico
        </div>

        <div class="title">
            {{ $titulo ?? 'Horario Académico' }}
        </div>

        <div class="description">
            Distribución oficial de clases registrada en el sistema Next Level.
        </div>

    </div>


    {{-- =====================================================
         INFO
    ====================================================== --}}

    <div class="info-card">

        <div class="info-item">

            <div class="info-label">
                Profesor
            </div>

            <div class="info-value">
                {{ $nombreProfesor }}
            </div>

        </div>


        <div class="info-item">

            <div class="info-label">
                Código
            </div>

            <div class="info-value">
                {{ $codigoProfesor }}
            </div>

        </div>


        <div class="info-item">

            <div class="info-label">
                Institución
            </div>

            <div class="info-value">
                {{ $nombreInstitucion }}
            </div>

        </div>


        <div class="info-item">

            <div class="info-label">
                Periodo académico
            </div>

            <div class="info-value">
                {{ $periodo }}
            </div>

        </div>


        <div class="info-item">

            <div class="info-label">
                Total de clases
            </div>

            <div class="info-value">
                {{ $horarios->count() }}
            </div>

        </div>

    </div>


    {{-- =====================================================
         TABLE
    ====================================================== --}}

    <div class="table-wrapper">

        <table>

            <thead>

                <tr>

                    <th class="col-numero">
                        #
                    </th>

                    <th class="col-profesor">
                        Profesor
                    </th>

                    <th class="col-curso">
                        Curso
                    </th>

                    <th class="col-grado">
                        Grado
                    </th>

                    <th class="col-aula">
                        Aula
                    </th>

                    <th class="col-institucion">
                        Institución
                    </th>

                    <th class="col-dia">
                        Día
                    </th>

                    <th class="col-hora">
                        Inicio
                    </th>

                    <th class="col-hora">
                        Fin
                    </th>

                    <th class="col-turno">
                        Turno
                    </th>

                    <th class="col-estado">
                        Estado
                    </th>

                </tr>

            </thead>


            <tbody>

            @forelse($horarios as $index => $h)

                @php
                    $estado = strtolower($h->estado ?? '');

                    $badgeClass = match($estado) {
                        'activo' => 'badge-activo',
                        'modificado' => 'badge-modificado',
                        'cancelado' => 'badge-cancelado',
                        default => 'badge-default',
                    };

                    try {
                        $horaInicio = \Carbon\Carbon::parse(
                            $h->hora_inicio
                        )->format('H:i');
                    } catch (\Exception $e) {
                        $horaInicio = substr(
                            (string) $h->hora_inicio,
                            0,
                            5
                        );
                    }

                    try {
                        $horaFin = \Carbon\Carbon::parse(
                            $h->hora_fin
                        )->format('H:i');
                    } catch (\Exception $e) {
                        $horaFin = substr(
                            (string) $h->hora_fin,
                            0,
                            5
                        );
                    }
                @endphp


                <tr>

                    <td>
                        {{ $index + 1 }}
                    </td>


                    <td>

                        <span class="profesor-name">
                            {{ $h->profesor->nombre_completo ?? 'N/A' }}
                        </span>

                        @if(!empty($h->profesor->codigo))

                            <span class="codigo">
                                {{ $h->profesor->codigo }}
                            </span>

                        @endif

                    </td>


                    <td>

                        <span class="curso-name">
                            {{ $h->curso->nombre ?? 'N/A' }}
                        </span>

                        @if(!empty($h->curso->codigo))

                            <span class="codigo">
                                {{ $h->curso->codigo }}
                            </span>

                        @endif

                    </td>


                    <td>

                        {{ $h->grado->nombre_completo ?? 'N/A' }}

                        @if(!empty($h->grado->codigo))

                            <span class="codigo">
                                {{ $h->grado->codigo }}
                            </span>

                        @endif

                    </td>


                    <td>

                        <span class="aula-name">
                            {{ $h->aula->nombre ?? 'N/A' }}
                        </span>

                        @if(!empty($h->aula->codigo))

                            <span class="codigo">
                                {{ $h->aula->codigo }}
                            </span>

                        @endif

                    </td>


                    <td>
                        {{ ucfirst($h->institucion ?? 'N/A') }}
                    </td>


                    <td>
                        {{ ucfirst($h->dia_semana ?? 'N/A') }}
                    </td>


                    <td class="hora">
                        {{ $horaInicio }}
                    </td>


                    <td class="hora">
                        {{ $horaFin }}
                    </td>


                    <td>
                        {{ ucfirst($h->turno ?? 'N/A') }}
                    </td>


                    <td>

                        <span class="badge {{ $badgeClass }}">
                            {{ ucfirst($h->estado ?? 'N/A') }}
                        </span>

                    </td>

                </tr>


            @empty

                <tr>

                    <td
                        colspan="11"
                        class="empty"
                    >
                        No existen horarios disponibles para exportar.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    {{-- =====================================================
         FOOTER
    ====================================================== --}}

    <div class="footer">

        <div class="footer-left">

            <strong>
                Next Level School
            </strong>

            · Sistema de Gestión de Horarios

            <br>

            © {{ date('Y') }} Next Level School

        </div>


        <div class="footer-total">

            Total:

            <span>
                {{ $horarios->count() }}
            </span>

            {{ $horarios->count() === 1 ? 'clase' : 'clases' }}

        </div>

    </div>

</div>

</body>
</html>