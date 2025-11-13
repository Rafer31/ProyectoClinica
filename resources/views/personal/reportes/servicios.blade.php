<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #2563eb;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-box {
            background: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-row {
            margin: 5px 0;
        }
        .info-row strong {
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-programado { background: #fef3c7; color: #92400e; }
        .badge-proceso { background: #dbeafe; color: #1e40af; }
        .badge-atendido { background: #d1fae5; color: #065f46; }
        .badge-entregado { background: #e9d5ff; color: #6b21a8; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .resumen {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px;
            background: #eff6ff;
            border-radius: 5px;
        }
        .resumen-item {
            text-align: center;
        }
        .resumen-item .numero {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
        }
        .resumen-item .label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p><strong>Personal:</strong> {{ $personal }}</p>
        <p><strong>Período:</strong> {{ $periodo }}</p>
        <p><strong>Fecha de Generación:</strong> {{ $fecha }}</p>
    </div>

    <!-- Resumen -->
    <div class="resumen">
        <div class="resumen-item">
            <div class="numero">{{ $total }}</div>
            <div class="label">Total de Pacientes Atendidos</div>
        </div>
    </div>

    <!-- Información General -->
    <div class="info-box">
        <div class="info-row">
            <strong>Hospital:</strong> Centro de Salud - Servicio de Imagenología
        </div>
        <div class="info-row">
            <strong>Generado por:</strong> {{ $personal }}
        </div>
    </div>

    <!-- Tabla de Servicios -->
    @if($servicios->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Nro. Servicio</th>
                    <th style="width: 25%;">Paciente</th>
                    <th style="width: 20%;">Tipo de Estudio</th>
                    <th style="width: 15%;">Fecha Atención</th>
                    <th style="width: 10%;">Estado</th>
                    <th style="width: 20%;">Diagnóstico</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicios as $servicio)
                    <tr>
                        <td>{{ $servicio->nroServ }}</td>
                        <td>
                            {{ $servicio->paciente->nomPa ?? '' }}
                            {{ $servicio->paciente->paternoPa ?? '' }}
                            {{ $servicio->paciente->maternoPa ?? '' }}
                            <br>
                            <small style="color: #666;">HCI: {{ $servicio->paciente->nroHCI ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $servicio->tipoEstudio->descripcion ?? 'N/A' }}</td>
                        <td>
                            {{ $servicio->fechaAten ? \Carbon\Carbon::parse($servicio->fechaAten)->format('d/m/Y') : 'N/A' }}
                            <br>
                            <small style="color: #666;">{{ $servicio->horaAten ?? '' }}</small>
                        </td>
                        <td>
                            @php
                                $badgeClass = '';
                                if($servicio->estado === 'Programado') $badgeClass = 'badge-programado';
                                elseif($servicio->estado === 'EnProceso') $badgeClass = 'badge-proceso';
                                elseif($servicio->estado === 'Atendido') $badgeClass = 'badge-atendido';
                                elseif($servicio->estado === 'Entregado') $badgeClass = 'badge-entregado';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $servicio->estado }}</span>
                        </td>
                        <td style="font-size: 10px;">
                            @if($servicio->diagnosticos->count() > 0)
                                {{ Str::limit($servicio->diagnosticos->first()->descripDiag, 50) }}
                            @else
                                <em style="color: #999;">Sin diagnóstico</em>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>No hay servicios registrados en este período</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema de Gestión de Imagenología - Hospital</p>
    </div>
</body>
</html>
