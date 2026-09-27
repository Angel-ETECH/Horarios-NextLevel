<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>{{ $titulo ?? 'Horario Académico' }}</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1E293B;
            background: #FFFFFF;
            font-size: 9px;
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .header {
            width: 100%;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .brand-cell {
            width: 55%;
        }

        .date-cell {
            width: 45%;
            text-align: right;
        }

        .brand {
            font-size: 23px;
            font-weight: bold;
            color: #0F2749;
            line-height: 1.1;
        }

        .brand-red {
            color: #DB0808;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: #64748B;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .generated-label {
            color: #94A3B8;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .generated-date {
            margin-top: 3px;
            color: #0F2749;
            font-size: 9px;
            font-weight: bold;
        }

        .line-blue {
            height: 4px;
            margin-top: 10px;
            background: #0F2749;
        }

        .line-red {
            width: 90px;
            height: 4px;
            margin-top: -4px;
            background: #DB0808;
        }

        /* =========================================================
           TITLE
        ========================================================= */

        .title-section {
            margin-top: 13px;
            margin-bottom: 12px;
        }

        .document-label {
            color: #DB0808;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .document-title {
            margin: 4px 0 0 0;
            color: #0F2749;
            font-size: 18px;
            font-weight: bold;
            line-height: 1.2;
        }

        .document-description {
            margin-top: 5px;
            color: #64748B;
            font-size: 8px;
        }

        /* =========================================================
           INFORMATION
        ========================================================= */

        .info-box {
            width: 100%;
            margin-bottom: 13px;
            padding: 8px 10px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-left: 4px solid #1B3A6B;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 1px 8px 1px 0;
            vertical-align: top;
        }

        .info-label {
            color: #64748B;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .info-value {
            margin-top: 2px;
            color: #0F2749;
            font-size: 8.5px;
            font-weight: bold;
        }

        /* =========================================================
           SCHEDULE TABLE
        ========================================================= */

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .schedule-table thead {
            display: table-header-group;
        }

        .schedule-table tr {
            page-break-inside: avoid;
        }

        .schedule-table th {
            padding: 7px 3px;
            background: #0F2749;
            border: 1px solid #1B3A6B;
            color: #FFFFFF;
            text-align: center;
            vertical-align: middle;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .schedule-table td {
            padding: 7px 3px;
            border: 1px solid #E2E8F0;
            color: #334155;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5px;
            line-height: 1.25;
            word-break: break-word;
        }

        .schedule-table tbody tr:nth-child(even) {
            background: #F8FAFC;
        }

        /* =========================================================
           COLUMN WIDTHS
        ========================================================= */

        .col-number {
            width: 3%;
        }

        .col-profesor {
            width: 15%;
        }

        .col-curso {
            width: 14%;
        }

        .col-grado {
            width: 12%;
        }

        .col-aula {
            width: 10%;
        }

        .col-institucion {
            width: 8%;
        }

        .col-dia {
            width: 7%;
        }

        .col-hora {
            width: 7%;
        }

        .col-turno {
            width: 7%;
        }

        .col-estado {
            width: 7%;
        }

        /* =========================================================
           CONTENT
        ========================================================= */

        .profesor-name {
            color: #0F2749;
            font-weight: bold;
        }

        .curso-name {
            color: #1B3A6B;
            font-weight: bold;
        }

        .aula-name {
            color: #334155;
            font-weight: bold;
        }

        .codigo {
            display: block;
            margin-top: 2px;
            color: #94A3B8;
            font-size: 6px;
        }

        .hora {
            color: #0F2749;
            font-size: 8px;
            font-weight: bold;
            white-space: nowrap;
        }

        /* =========================================================
           BADGES
        ========================================================= */

        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 10px;
            font-size: 6.5px;
            font-weight: bold;
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
           SUMMARY
        ========================================================= */

        .summary {
            margin-top: 12px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-left {
            width: 65%;
            color: #64748B;
            font-size: 7px;
        }

        .summary-right {
            width: 35%;
            text-align: right;
        }

        .summary-total {
            display: inline-block;
            padding: 6px 11px;
            background: #0F2749;
            color: #FFFFFF;
            font-size: 8px;
            font-weight: bold;
        }

        .summary-total strong {
            font-size: 11px;
        }

        /* =========================================================
           FOOTER
        ========================================================= */

        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #E2E8F0;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            width: 60%;
            color: #94A3B8;
            font-size: 6.5px;
        }

        .footer-right {
            width: 40%;
            color: #64748B;
            font-size: 6.5px;
            text-align: right;
        }

        .footer-brand {
            color: #0F2749;
            font-weight: bold;
        }

        .empty {
            padding: 25px !important;
            color: #94A3B8 !important;
            text-align: center !important;
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
        $primerHorario?->profesor?->nombre_completo ?? null;

    $codigoProfesor =
        $primerHorario?->profesor?->codigo ?? null;

    $periodo =
        $primerHorario?->periodo_academico ?? null;
@endphp


{{-- =========================================================
     HEADER
========================================================= --}}

<div class="header">

    <table class="header-table">

        <tr>

            <td class="brand-cell">

                <div class="brand">
                    Next Level
                    <span class="brand-red">School</span>
                </div>

                <div class="brand-subtitle">
                    Sistema de Gestión Académica
                </div>

            </td>


            <td class="date-cell">

                <div class="generated-label">
                    Documento generado
                </div>

                <div class="generated-date">
                    {{ now()->format('d/m/Y H:i') }}
                </div>

            </td>

        </tr>

    </table>


    <div class="line-blue"></div>

    <div class="line-red"></div>

</div>


{{-- =========================================================
     TITLE
========================================================= --}}

<div class="title-section">

    <div class="document-label">
        Horario académico
    </div>

    <h1 class="document-title">
        {{ $titulo ?? 'Horario Académico' }}
    </h1>

    <div class="document-description">
        Distribución oficial de clases registrada en el sistema Next Level.
    </div>

</div>


{{-- =========================================================
     INFORMATION
========================================================= --}}

<div class="info-box">

    <table class="info-table">

        <tr>

            @if($nombreProfesor)

                <td>

                    <div class="info-label">
                        Profesor
                    </div>

                    <div class="info-value">
                        {{ $nombreProfesor }}
                    </div>

                </td>

            @endif


            @if($codigoProfesor)

                <td>

                    <div class="info-label">
                        Código
                    </div>

                    <div class="info-value">
                        {{ $codigoProfesor }}
                    </div>

                </td>

            @endif


            <td>

                <div class="info-label">
                    Institución
                </div>

                <div class="info-value">
                    {{ $nombreInstitucion }}
                </div>

            </td>


            @if($periodo)

                <td>

                    <div class="info-label">
                        Periodo académico
                    </div>

                    <div class="info-value">
                        {{ $periodo }}
                    </div>

                </td>

            @endif


            <td>

                <div class="info-label">
                    Total de clases
                </div>

                <div class="info-value">
                    {{ $horarios->count() }}
                </div>

            </td>

        </tr>

    </table>

</div>


{{-- =========================================================
     TABLE
========================================================= --}}

<table class="schedule-table">

    <thead>

        <tr>

            <th class="col-number">
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

            /*
             * IMPORTANTE:
             * hora_inicio y hora_fin pueden llegar como Carbon
             * o como fecha/hora completa.
             *
             * Aquí dejamos únicamente HH:mm.
             */

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


{{-- =========================================================
     SUMMARY
========================================================= --}}

<div class="summary">

    <table class="summary-table">

        <tr>

            <td class="summary-left">

                Los datos corresponden a los horarios activos
                registrados en el sistema Next Level.

            </td>


            <td class="summary-right">

                <span class="summary-total">

                    Total:

                    <strong>
                        {{ $horarios->count() }}
                    </strong>

                    {{ $horarios->count() === 1 ? 'clase' : 'clases' }}

                </span>

            </td>

        </tr>

    </table>

</div>


{{-- =========================================================
     FOOTER
========================================================= --}}

<div class="footer">

    <table class="footer-table">

        <tr>

            <td class="footer-left">

                <span class="footer-brand">
                    Next Level School
                </span>

                · Sistema de Gestión de Horarios

            </td>


            <td class="footer-right">

                © {{ date('Y') }} Next Level School

            </td>

        </tr>

    </table>

</div>

</body>
</html>