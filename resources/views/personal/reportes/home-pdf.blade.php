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
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-info {
            font-size: 10px;
            margin: 4px 0;
        }

        .header-info strong {
            font-weight: bold;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
        }

        .info-grid {
            margin: 15px 0;
            border: 1px solid #000;
            padding: 10px;
        }

        .info-row {
            margin: 5px 0;
            font-size: 10px;
        }

        .info-row strong {
            font-weight: bold;
        }

        .summary-box {
            text-align: center;
            border: 2px solid #000;
            padding: 15px;
            margin: 20px auto;
            max-width: 300px;
        }

        .summary-box .numero {
            font-size: 36px;
            font-weight: bold;
            margin: 5px 0;
        }

        .summary-box .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }

        th {
            background: #000;
            color: #fff;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        td {
            padding: 6px;
            border: 1px solid #000;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #000;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 9px;
        }

        .footer-line {
            margin: 3px 0;
        }

        .page-number {
            position: fixed;
            bottom: 10px;
            right: 20px;
            font-size: 9px;
        }

        .watermark {
            position: fixed;
            bottom: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: #f0f0f0;
            opacity: 0.3;
            z-index: -1;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            font-style: italic;
            color: #666;
            border: 1px dashed #000;
            margin: 20px 0;
        }

        small {
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <div class="header-info"><strong>Hospital:</strong> Centro de Salud - Servicio de Imagenología</div>
        <div class="header-info"><strong>Personal:</strong> {{ $personal }}</div>
        <div class="header-info"><strong>Período:</strong> {{ $periodo }}</div>
        <div class="header-info"><strong>Fecha de Generación:</strong> {{ $fecha }}</div>
    </div>

    <!-- Resumen -->
    <div class="summary-box">
        <div class="numero">{{ $total }}</div>
        <div class="label">Total de Pacientes Atendidos</div>
    </div>

    <!-- Información del Reporte -->
    <div class="info-grid">
        <div class="info-row"><strong>Tipo de Reporte:</strong> {{ $titulo }}</div>
        <div class="info-row"><strong>Generado por:</strong> {{ $personal }}</div>
        <div class="info-row"><strong>Fecha y Hora:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</div>
    </div>

    <!-- Título de Tabla -->
    <div class="section-title">Detalle de Servicios</div>

    <!-- Tabla de Servicios -->
    @if($servicios->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">Nro.</th>
                    <th style="width: 22%;">Paciente / HCI</th>
                    <th style="width: 20%;">Tipo de Estudio</th>
                    <th style="width: 12%;">Fecha</th>
                    <th style="width: 8%;">Estado</th>
                    <th style="width: 15%;">Médico</th>
                    <th style="width: 15%;">Diagnóstico</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicios as $servicio)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $servicio->nroServ }}</td>
                        <td>
                            <strong>
                                {{ $servicio->paciente->nomPa ?? '' }}
                                {{ $servicio->paciente->paternoPa ?? '' }}
                                {{ $servicio->paciente->maternoPa ?? '' }}
                            </strong>
                            <br>
                            <small>HCI: {{ $servicio->paciente->nroHCI ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $servicio->tipoEstudio->descripcion ?? 'N/A' }}</td>
                        <td style="text-align: center;">
                            {{ $servicio->fechaAten ? \Carbon\Carbon::parse($servicio->fechaAten)->format('d/m/Y') : 'N/A' }}
                            <br>
                            <small>{{ $servicio->horaAten ?? '' }}</small>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge">{{ $servicio->estado }}</span>
                        </td>
                        <td>
                            <small>
                                {{ $servicio->medico->nomMed ?? '' }}
                                {{ $servicio->medico->paternoMed ?? '' }}
                            </small>
                        </td>
                        <td style="font-size: 9px;">
                            @if($servicio->diagnosticos->count() > 0)
                                {{ Str::limit($servicio->diagnosticos->first()->descripDiag, 60) }}
                            @else
                                <em style="color: #999;">Sin diagnóstico</em>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p><strong>No hay servicios registrados en este período</strong></p>
            <p>{{ $periodo }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="footer-line"><strong>Sistema de Gestión de Imagenología</strong></div>
        <div class="footer-line">Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</div>
        <div class="footer-line">Este documento es de uso interno y confidencial</div>
    </div>

    <!-- Número de página -->
    <div class="page-number">
        Página 1
    </div>
</body>
</html>
