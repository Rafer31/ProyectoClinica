<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #4F46E5;
        }
        .header h1 {
            color: #4F46E5;
            font-size: 20px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header .mes {
            color: #666;
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
        }
        .header p {
            color: #666;
            font-size: 9px;
            margin: 2px 0;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            table-layout: fixed;
        }
        .stat-row {
            display: table-row;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 5px;
            text-align: center;
            vertical-align: top;
        }
        .stat-card-inner {
            border-radius: 6px;
            padding: 10px 5px;
            color: white;
        }
        .stat-card.blue .stat-card-inner {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
        }
        .stat-card.green .stat-card-inner {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }
        .stat-card.purple .stat-card-inner {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
        }
        .stat-card.red .stat-card-inner {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        }
        .stat-label {
            font-size: 8px;
            text-transform: uppercase;
            opacity: 0.95;
            margin-bottom: 4px;
            font-weight: bold;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .stat-desc {
            font-size: 7px;
            opacity: 0.9;
        }

        .section-title {
            color: #4F46E5;
            font-size: 12px;
            font-weight: bold;
            margin: 12px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 2px solid #4F46E5;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 9px;
        }
        thead {
            background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
            color: white;
        }
        th {
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
        }
        th.center {
            text-align: center;
        }
        td {
            padding: 8px 6px;
            border-bottom: 1px solid #E5E7EB;
        }
        td.center {
            text-align: center;
        }
        tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        .badge-number {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8px;
            white-space: nowrap;
        }
        .badge-blue { background: #DBEAFE; color: #1E40AF; }
        .badge-green { background: #D1FAE5; color: #065F46; }
        .badge-purple { background: #E9D5FF; color: #6B21A8; }
        .badge-red { background: #FEE2E2; color: #991B1B; }

        .totals-row {
            background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%) !important;
            font-weight: bold;
            border-top: 3px solid #4F46E5;
        }
        .totals-row td {
            font-size: 10px;
            padding: 10px 6px;
        }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 8px;
        }
        .footer p {
            margin: 2px 0;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #9CA3AF;
            font-style: italic;
            border: 2px dashed #E5E7EB;
            border-radius: 6px;
            margin: 15px 0;
        }
        .no-data-icon {
            font-size: 40px;
            color: #D1D5DB;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <div class="mes">{{ $mes }}</div>
        <p>Generado el {{ $fecha }}</p>
        <p>Personal de Imagen - Todos los servicios del mes</p>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-card blue">
                <div class="stat-card-inner">
                    <div class="stat-label">Total General</div>
                    <div class="stat-value">{{ $totalGeneral }}</div>
                    <div class="stat-desc">Servicios Completados</div>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-card-inner">
                    <div class="stat-label">Atendidos</div>
                    <div class="stat-value">{{ $totalAtendidos }}</div>
                    <div class="stat-desc">Terminados</div>
                </div>
            </div>
            <div class="stat-card purple">
                <div class="stat-card-inner">
                    <div class="stat-label">Entregados</div>
                    <div class="stat-value">{{ $totalEntregados }}</div>
                    <div class="stat-desc">Completados</div>
                </div>
            </div>
            <div class="stat-card red">
                <div class="stat-card-inner">
                    <div class="stat-label">Cancelados</div>
                    <div class="stat-value">{{ $totalCancelados }}</div>
                    <div class="stat-desc">No realizados</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Título de Tabla -->
    <div class="section-title">Detalle por Personal</div>

    <!-- Tabla de Personal -->
    @if(count($datosPersonal) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 40%;">Personal</th>
                    <th class="center" style="width: 15%;">Atendidos</th>
                    <th class="center" style="width: 15%;">Entregados</th>
                    <th class="center" style="width: 15%;">Cancelados</th>
                    <th class="center" style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datosPersonal as $personal)
                <tr>
                    <td>
                        <strong>{{ $personal['nombre'] }}</strong>
                    </td>
                    <td class="center">
                        <span class="badge-number badge-green">{{ $personal['atendidos'] }} Atendidos</span>
                    </td>
                    <td class="center">
                        <span class="badge-number badge-purple">{{ $personal['entregados'] }} Entregados</span>
                    </td>
                    <td class="center">
                        <span class="badge-number badge-red">{{ $personal['cancelados'] }} Cancelados</span>
                    </td>
                    <td class="center">
                        <span class="badge-number badge-blue">{{ $personal['total'] }} Completados</span>
                    </td>
                </tr>
                @endforeach

                <!-- Fila de Totales -->
                <tr class="totals-row">
                    <td><strong>TOTALES GENERALES</strong></td>
                    <td class="center"><strong>{{ $totalAtendidos }}</strong></td>
                    <td class="center"><strong>{{ $totalEntregados }}</strong></td>
                    <td class="center"><strong>{{ $totalCancelados }}</strong></td>
                    <td class="center"><strong>{{ $totalGeneral }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="no-data">
            <div class="no-data-icon">📊</div>
            <p><strong>No hay servicios registrados en este periodo</strong></p>
            <p>{{ $mes }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Sistema de Gestión de Servicios de Salud</strong></p>
        <p>Reporte consolidado de servicios del personal de imagen</p>
        <p>Fecha y hora de generación: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
