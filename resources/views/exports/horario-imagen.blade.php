<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Horario Académico' }}</title>
    <style>
        /* Estilos específicos para captura de imagen */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #ffffff;
            padding: 20px;
            width: 1000px;
            margin: 0 auto;
        }

        .container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2C3E50;
            position: relative;
        }

        .header .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2C3E50;
        }

        .header .logo span {
            color: #3498DB;
        }

        .header .subtitle {
            font-size: 18px;
            color: #7F8C8D;
            margin-top: 5px;
        }

        .header .info {
            font-size: 12px;
            color: #95A5A6;
            margin-top: 5px;
        }

        .header .filters {
            background: #F8F9FA;
            padding: 8px 15px;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 12px;
            color: #555;
            border-left: 4px solid #3498DB;
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }

        table thead th {
            background: linear-gradient(135deg, #2C3E50, #34495E);
            color: #FFFFFF;
            padding: 12px 10px;
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        table tbody td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #ECF0F1;
        }

        table tbody tr:nth-child(even) {
            background-color: #F8F9FA;
        }

        table tbody tr:hover {
            background-color: #EBF5FB;
        }

        .badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-activo {
            background: #D4EDDA;
            color: #155724;
        }

        .badge-modificado {
            background: #FFF3CD;
            color: #856404;
        }

        .badge-cancelado {
            background: #F8D7DA;
            color: #721C24;
        }

        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #ECF0F1;
            text-align: center;
            font-size: 11px;
            color: #95A5A6;
        }

        .footer .total {
            font-weight: bold;
            color: #2C3E50;
            font-size: 14px;
        }

        .footer .copyright {
            margin-top: 5px;
        }

        .color-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 3px;
            margin-right: 5px;
            vertical-align: middle;
        }

        .profesor-name {
            font-weight: 600;
            color: #2C3E50;
        }

        .curso-name {
            color: #3498DB;
        }
    </style>
</head>
<body>
    <div class="container" id="horario-container">
        <div class="header">
            <div class="logo">🏫 <span>Next Level</span> School</div>
            <div class="subtitle">{{ $titulo ?? 'Horario Académico' }}</div>
            <div class="info">📅 Generado: {{ now()->format('d/m/Y H:i:s') }}</div>
            @if(!empty($filtros))
                <div class="filters">
                    🔍 Filtros:
                    @foreach($filtros as $key => $value)
                        {{ ucfirst($key) }}: {{ $value }}
                    @endforeach
                </div>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th style="width: 180px;">Profesor</th>
                    <th style="width: 180px;">Curso</th>
                    <th style="width: 140px;">Grado</th>
                    <th style="width: 100px;">Aula</th>
                    <th style="width: 80px;">Día</th>
                    <th style="width: 90px;">Hora Inicio</th>
                    <th style="width: 90px;">Hora Fin</th>
                    <th style="width: 70px;">Turno</th>
                    <th style="width: 80px;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($horarios as $index => $h)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="profesor-name">{{ $h->profesor->nombre_completo ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="curso-name">{{ $h->curso->nombre ?? 'N/A' }}</div>
                            <small style="color: #999; font-size: 10px;">{{ $h->curso->codigo ?? '' }}</small>
                        </td>
                        <td>{{ $h->grado->nombre_completo ?? 'N/A' }}</td>
                        <td>
                            <strong>{{ $h->aula->nombre ?? 'N/A' }}</strong>
                            <br>
                            <small style="color: #999; font-size: 10px;">Cap: {{ $h->aula->capacidad ?? 0 }}</small>
                        </td>
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
                        <td colspan="10" style="text-align: center; padding: 40px; color: #999;">
                            📭 No hay horarios disponibles para exportar
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div class="total">
                📊 Total de clases: {{ $horarios->count() }}
            </div>
            <div class="copyright">
                © {{ date('Y') }} Academia Next Level School - Todos los derechos reservados
            </div>
        </div>
    </div>
</body>
</html>
