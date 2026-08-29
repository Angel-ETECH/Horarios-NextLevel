<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Horario Académico' }}</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2C3E50;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #2C3E50;
            font-size: 24px;
            margin: 0;
        }

        .header .subtitle {
            color: #7F8C8D;
            font-size: 14px;
            margin: 5px 0;
        }

        .header .info {
            color: #95A5A6;
            font-size: 11px;
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead th {
            background-color: #2C3E50;
            color: #FFFFFF;
            padding: 10px 8px;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table tbody td {
            padding: 8px 6px;
            text-align: center;
            border-bottom: 1px solid #ECF0F1;
        }

        table tbody tr:nth-child(even) {
            background-color: #F9F9F9;
        }

        table tbody tr:hover {
            background-color: #ECF0F1;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #BDC3C7;
            text-align: center;
            font-size: 10px;
            color: #95A5A6;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-activo {
            background-color: #27AE60;
            color: #FFFFFF;
        }

        .badge-modificado {
            background-color: #F39C12;
            color: #FFFFFF;
        }

        .badge-cancelado {
            background-color: #E74C3C;
            color: #FFFFFF;
        }

        .filter-info {
            background-color: #F8F9FA;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 11px;
            border-left: 4px solid #3498DB;
        }

        .total-info {
            font-weight: bold;
            color: #2C3E50;
            margin-top: 10px;
            padding: 8px;
            background-color: #ECF0F1;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏫 Next Level School</h1>
        <div class="subtitle">{{ $titulo ?? 'Horario Académico' }}</div>
        <div class="info">Fecha de exportación: {{ now()->format('d/m/Y H:i:s') }}</div>
        @if(!empty($filtros))
            <div class="filter-info">
                <strong>Filtros aplicados:</strong>
                @foreach($filtros as $key => $value)
                    {{ ucfirst($key) }}: {{ $value }}
                @endforeach
            </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Profesor</th>
                <th>Curso</th>
                <th>Grado</th>
                <th>Aula</th>
                <th>Día</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Turno</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($horarios as $index => $h)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $h->profesor->nombre_completo ?? 'N/A' }}</td>
                    <td>{{ $h->curso->nombre ?? 'N/A' }}</td>
                    <td>{{ $h->grado->nombre_completo ?? 'N/A' }}</td>
                    <td>{{ $h->aula->nombre ?? 'N/A' }}</td>
                    <td>{{ ucfirst($h->dia_semana) }}</td>
                    <td>{{ $h->hora_inicio }}</td>
                    <td>{{ $h->hora_fin }}</td>
                    <td>{{ ucfirst($h->turno) }}</td>
                    <td>
                        <span class="badge badge-{{ $h->estado }}">
                            {{ ucfirst($h->estado) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">
                        No hay horarios disponibles
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-info">
        Total de clases: {{ $horarios->count() }}
    </div>

    <div class="footer">
        Generado por Academia Next Level School - Sistema de Gestión de Horarios
        <br>
        © {{ date('Y') }} - Todos los derechos reservados
    </div>
</body>
</html>
