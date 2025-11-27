<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Cita - {{ $nroFicha }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 20mm;
            size: A4;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
            background-color: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 20pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            color: #333;
        }

        /* INFORMACIÓN DESTACADA */
        .destacado-principal {
            border: 3px double #000;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f8f8f8;
        }

        .destacado-principal h2 {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .destacado-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .destacado-item {
            display: table-cell;
            width: 33.33%;
            border: 2px solid #000;
            padding: 12px;
            text-align: center;
            background: white;
            vertical-align: middle;
        }

        .destacado-item .label {
            font-size: 8pt;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-weight: bold;
            color: #444;
            letter-spacing: 0.3px;
        }

        .destacado-item .valor {
            font-size: 16pt;
            font-weight: bold;
            margin-top: 4px;
        }

        .section {
            border: 2px solid #000;
            padding: 12px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .info-row {
            display: table-row;
        }

        .info-item {
            display: table-cell;
            padding: 8px;
            border: 1px solid #666;
            width: 50%;
            vertical-align: top;
        }

        .info-label {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
            color: #444;
            display: block;
        }

        .info-value {
            font-size: 10pt;
            font-weight: normal;
            color: #000;
        }

        /* ESTUDIO DESTACADO */
        .estudio-destacado {
            border: 2px solid #000;
            padding: 12px;
            background-color: #f8f8f8;
            text-align: center;
            margin-bottom: 10px;
        }

        .estudio-destacado .info-label {
            text-align: center;
            margin-bottom: 5px;
        }

        .estudio-destacado .info-value {
            font-size: 12pt;
            font-weight: bold;
        }

        /* REQUISITOS */
        .requisitos-section {
            border: 2px solid #000;
            padding: 12px;
            margin-top: 10px;
            background-color: #fafafa;
            page-break-inside: avoid;
        }

        .requisitos-section h4 {
            font-size: 10pt;
            margin-bottom: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .requisito-item {
            padding: 8px 8px 8px 25px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 0;
            background: white;
            font-size: 9pt;
            position: relative;
            line-height: 1.4;
        }

        .requisito-item:last-child {
            border-bottom: none;
        }

        .requisito-item::before {
            content: "•";
            position: absolute;
            left: 10px;
            font-weight: bold;
            font-size: 14pt;
        }

        .observacion-box {
            border: 2px solid #000;
            padding: 10px;
            margin-top: 10px;
            background: white;
        }

        .observacion-box strong {
            font-size: 9pt;
            text-transform: uppercase;
            display: block;
            margin-bottom: 5px;
        }

        .observacion-box p {
            font-size: 9pt;
            line-height: 1.4;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #000;
            font-size: 8pt;
        }

        .footer-line {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>FICHA DE CITA</h1>
        <p>Centro de Salud - Servicio de Diagnóstico por Imágenes</p>
    </div>

    <!-- INFORMACIÓN MUY DESTACADA -->
    <div class="destacado-principal">
        <h2>Información de su Cita</h2>

        <div class="destacado-grid">
            <div class="destacado-item">
                <div class="label">Fecha de Cita</div>
                <div class="valor">{{ $fechaCita }}</div>
            </div>
            <div class="destacado-item">
                <div class="label">Hora de Cita</div>
                <div class="valor">{{ $horaCita }}</div>
            </div>
            <div class="destacado-item">
                <div class="label">Nro. Ficha</div>
                <div class="valor">{{ $nroFicha }}</div>
            </div>
        </div>
    </div>

    <!-- INFORMACIÓN DEL PACIENTE -->
    <div class="section">
        <div class="section-title">Datos del Paciente</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">Nombre Completo</span>
                    <span class="info-value">{{ $paciente ? ($paciente->nomPa . ' ' . $paciente->paternoPa . ' ' . $paciente->maternoPa) : 'No registrado' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">N° Historia Clínica (HCI)</span>
                    <span class="info-value">{{ $paciente->nroHCI ?? 'Sin HCI' }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">Sexo</span>
                    <span class="info-value">{{ $paciente->sexo === 'M' ? 'Masculino' : 'Femenino' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipo de Paciente</span>
                    <span class="info-value">{{ $paciente->tipoPac ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- DETALLES DEL SERVICIO -->
    <div class="section">
        <div class="section-title">Detalles del Servicio</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">Nro. Servicio</span>
                    <span class="info-value">{{ $nroServicio }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Médico Solicitante</span>
                    <span class="info-value">{{ $medico ? ($medico->nomMed . ' ' . $medico->paternoMed) : 'No asignado' }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">Fecha de Solicitud</span>
                    <span class="info-value">{{ $fechaSolicitud }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Hora de Solicitud</span>
                    <span class="info-value">{{ $horaSolicitud }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TIPO DE ESTUDIO -->
    <div class="section">
        <div class="section-title">Tipo de Estudio</div>
        <div class="estudio-destacado">
            <span class="info-label">Estudio a Realizar</span>
            <div class="info-value">
                {{ $tipoEstudio->descripcion ?? 'No especificado' }}
            </div>
        </div>

        @if($tipoEstudio && $tipoEstudio->observacion)
        <div class="observacion-box">
            <strong>Observación Importante:</strong>
            <p>{{ $tipoEstudio->observacion }}</p>
        </div>
        @endif

        <!-- REQUISITOS -->
        @if($requisitos && $requisitos->isNotEmpty())
        <div class="requisitos-section">
            <h4>Requisitos que debe cumplir:</h4>
            @foreach($requisitos as $requisito)
            <div class="requisito-item">
                {{ $requisito->descripReq ?? $requisito->descripRequisito ?? 'Requisito no especificado' }}
            </div>
            @endforeach
        </div>
        @else
        <div class="requisitos-section">
            <h4>Requisitos:</h4>
            <p style="font-size: 9pt; margin-top: 5px; padding-left: 10px;">No se requieren requisitos especiales para este estudio.</p>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-line"><strong>Sistema de Gestión de Imagenología</strong></div>
        <div class="footer-line">Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</div>
        <div class="footer-line">Por favor, llegar 15 minutos antes de su hora de cita</div>
        <div class="footer-line">Este documento es de uso interno y confidencial</div>
    </div>
</body>
</html>
