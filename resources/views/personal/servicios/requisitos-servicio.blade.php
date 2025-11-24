<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Requisitos del Servicio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background: #2563eb;
            color: white;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 8px;
            width: 35%;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
        }
        .info-value {
            display: table-cell;
            padding: 8px;
            border: 1px solid #e5e7eb;
        }
        .requisitos-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-top: 10px;
        }
        .requisito-item {
            padding: 8px 0;
            border-bottom: 1px dashed #e5e7eb;
        }
        .requisito-item:last-child {
            border-bottom: none;
        }
        .importante {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .importante-title {
            font-weight: bold;
            color: #991b1b;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .highlight {
            background: #dbeafe;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">CLÍNICA SANTA LUCÍA</div>
        <div class="subtitle">Sistema de Gestión Médica</div>
        <div style="margin-top: 10px; font-size: 16px; font-weight: bold; color: #2563eb;">
            REQUISITOS PARA ESTUDIO
        </div>
    </div>

    <!-- Información del Servicio -->
    <div class="section">
        <div class="section-title">INFORMACIÓN DEL SERVICIO</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">N° Servicio:</div>
                <div class="info-value">{{ $servicio->nroServ }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">N° Ficha:</div>
                <div class="info-value">{{ $servicio->nroFicha }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tipo de Estudio:</div>
                <div class="info-value"><strong>{{ $servicio->tipoEstudio->descripcion }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Atención:</div>
                <div class="info-value">
                    <span class="highlight">{{ \Carbon\Carbon::parse($servicio->fechaCrono)->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Hora de Atención:</div>
                <div class="info-value">
                    <span class="highlight">{{ \Carbon\Carbon::parse($servicio->horaCrono)->format('H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Paciente -->
    <div class="section">
        <div class="section-title">DATOS DEL PACIENTE</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nombre Completo:</div>
                <div class="info-value">
                    {{ $servicio->paciente->nomPa }} {{ $servicio->paciente->paternoPa }} {{ $servicio->paciente->maternoPa }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">N° HCI:</div>
                <div class="info-value">{{ $servicio->paciente->nroHCI }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">CI:</div>
                <div class="info-value">{{ $servicio->paciente->ciPa }} {{ $servicio->paciente->expPa }}</div>
            </div>
        </div>
    </div>

    <!-- Requisitos -->
    <div class="section">
        <div class="section-title">REQUISITOS PARA EL ESTUDIO</div>
        @if($servicio->tipoEstudio->requisitos && $servicio->tipoEstudio->requisitos->count() > 0)
            <div class="requisitos-box">
                <p style="font-weight: bold; margin-bottom: 10px; color: #92400e;">
                    Por favor, cumpla con los siguientes requisitos para su estudio:
                </p>
                @foreach($servicio->tipoEstudio->requisitos as $requisito)
                    <div class="requisito-item">
                        <strong>{{ $loop->iteration }}.</strong> {{ $requisito->descripReq }}
                    </div>
                @endforeach
            </div>
        @else
            <div class="requisitos-box">
                <p>No se requieren preparaciones especiales para este estudio.</p>
            </div>
        @endif
    </div>

    <!-- Información Importante -->
    <div class="importante">
        <div class="importante-title">⚠️ INFORMACIÓN IMPORTANTE</div>
        <ul style="margin-left: 20px; line-height: 1.8;">
            <li>Debe presentarse <strong>15 minutos antes</strong> de su hora programada.</li>
            <li>Traer documento de identidad original.</li>
            <li>Si tiene estudios previos relacionados, por favor tráigalos.</li>
            <li>En caso de no poder asistir, comunicarse con anticipación.</li>
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Clínica Santa Lucía</strong></p>
        <p>Teléfono: (123) 456-7890 | Email: info@clinicasantalucia.com</p>
        <p>Documento generado el {{ $fecha }} a las {{ $hora }}</p>
    </div>
</body>
</html>
