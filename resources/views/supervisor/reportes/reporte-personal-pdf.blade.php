<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4F46E5;
        }
        .header h1 {
            color: #4F46E5;
            font-size: 22px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .info-section {
            background-color: #F3F4F6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #4B5563;
        }
        .info-value {
            color: #1F2937;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-card.green {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }
        .stat-card.blue {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
        }
        .stat-card.purple {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
        }
        .stat-label {
            font-size: 9px;
            text-transform: uppercase;
            opacity: 0.9;
            margin-bottom: 4px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9px;
        }
        thead {
            background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
            color: white;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #E5E7EB;
        }
        tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        tbody tr:hover {
            background-color: #F3F4F6;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-programado { background: #DBEAFE; color: #1E40AF; }
        .badge-enproceso { background: #FEF3C7; color: #92400E; }
        .badge-atendido { background: #D1FAE5; color: #065F46; }
        .badge-entregado { background: #E9D5FF; color: #6B21A8; }
        .badge-cancelado { background: #FEE2E2; color: #991B1B; }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 9px;
        }
        .section-title {
            color: #4F46E5;
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #4F46E5;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #9CA3AF;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Generado el {{ $fecha }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Personal:</span>
            <span class="info-value">{{ $personal }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Periodo:</span>
            <span class="info-value">{{ $periodo }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total de Servicios:</span>
            <span class="info-value">{{ $total }}</span>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $total }}</div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">Atendidos</div>
            <div class="stat-value">{{ $servicios->whereIn('estado', ['Atendido', 'Entregado'])->count() }}</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">En Proceso</div>
            <div class="stat-value">{{ $servicios->where('estado', 'EnProceso')->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Programados</div>
            <div class="stat-value">{{ $servicios->where('estado', 'Programado')->count() }}</div>
        </div>
    </div>

    <div class="section-title">Detalle de Servicios</div>

    @if($servicios->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">Ficha</th>
                    <th style="width: 12%;">Nro. Servicio</th>
                    <th style="width: 25%;">Paciente</th>
                    <th style="width: 20%;">Tipo Estudio</th>
                    <th style="width: 12%;">Estado</th>
                    <th style="width: 11%;">Fecha Sol.</th>
                    <th style="width: 12%;">Fecha Aten.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicios as $servicio)
                <tr>
                    <td style="text-align: center;">{{ $servicio->nroFicha ?? 'N/A' }}</td>
                    <td>{{ $servicio->nroServ }}</td>
                    <td>
                        @if($servicio->paciente)
                            {{ $servicio->paciente->nomPa }} {{ $servicio->paciente->paternoPa }} {{ $servicio->paciente->maternoPa }}
                            @if($servicio->paciente->nroHCI)
                                <br><small style="color: #6B7280;">HCI: {{ $servicio->paciente->nroHCI }}</small>
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $servicio->tipoEstudio ? $servicio->tipoEstudio->descripcion : 'N/A' }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($servicio->estado) }}">
                            {{ $servicio->estado }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($servicio->fechaSol)->format('d/m/Y') }}</td>
                    <td>{{ $servicio->fechaAten ? \Carbon\Carbon::parse($servicio->fechaAten)->format('d/m/Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($servicios->where('estado', 'Atendido')->count() > 0 || $servicios->where('estado', 'Entregado')->count() > 0)
            <div class="section-title">Diagnósticos</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Nro. Servicio</th>
                        <th style="width: 30%;">Paciente</th>
                        <th style="width: 35%;">Diagnóstico</th>
                        <th style="width: 10%;">Tipo</th>
                        <th style="width: 10%;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($servicios->whereIn('estado', ['Atendido', 'Entregado']) as $servicio)
                        @if($servicio->diagnosticos && $servicio->diagnosticos->count() > 0)
                            @foreach($servicio->diagnosticos as $diagnostico)
                            <tr>
                                <td>{{ $servicio->nroServ }}</td>
                                <td>
                                    @if($servicio->paciente)
                                        {{ $servicio->paciente->nomPa }} {{ $servicio->paciente->paternoPa }}
                                    @endif
                                </td>
                                <td>{{ $diagnostico->descripDiag }}</td>
                                <td style="text-align: center;">
                                    <span class="badge" style="background: #E0E7FF; color: #3730A3;">
                                        {{ strtoupper($diagnostico->pivot->tipo) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ strtolower($servicio->estado) }}">
                                        {{ $servicio->estado }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    @else
        <div class="no-data">
            No hay servicios registrados en este periodo
        </div>
    @endif

    <div class="footer">
        <p>Sistema de Gestión de Servicios de Salud - Reporte generado automáticamente</p>
        <p>Fecha y hora de generación: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
