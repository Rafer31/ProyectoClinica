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
            margin: 15mm;
            size: A4;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            max-width: 700px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #000;
        }

        .header h1 {
            font-size: 16px;
            margin-bottom: 3px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .header p {
            font-size: 9px;
            color: #333;
        }

        /* INFORMACIÓN DESTACADA - FECHA Y HORA */
        .destacado-principal {
            border: 3px solid #000;
            padding: 12px;
            margin-bottom: 15px;
            background: #f5f5f5;
        }

        .destacado-principal h2 {
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .destacado-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .destacado-item {
            border: 2px solid #000;
            padding: 8px;
            text-align: center;
            background: white;
        }

        .destacado-item .label {
            font-size: 8px;
            text-transform: uppercase;
            margin-bottom: 4px;
            font-weight: bold;
            color: #333;
        }

        .destacado-item .valor {
            font-size: 14px;
            font-weight: bold;
        }

        .section {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .info-item {
            padding: 6px;
            border: 1px solid #ccc;
        }

        .info-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            color: #333;
        }

        .info-value {
            font-size: 10px;
            font-weight: 600;
        }

        /* REQUISITOS */
        .requisitos-lista {
            border: 2px solid #000;
            padding: 10px;
            margin-top: 8px;
            background: #f9f9f9;
        }

        .requisitos-lista h4 {
            font-size: 10px;
            margin-bottom: 8px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #666;
            padding-bottom: 4px;
        }

        .requisito-item {
            padding: 5px 8px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 4px;
            background: white;
            font-size: 9px;
            position: relative;
            padding-left: 20px;
        }

        .requisito-item:last-child {
            border-bottom: none;
        }

        .requisito-item::before {
            content: "•";
            position: absolute;
            left: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        .observacion-box {
            border: 2px dashed #000;
            padding: 8px;
            margin-top: 8px;
            background: white;
        }

        .observacion-box strong {
            font-size: 9px;
            text-transform: uppercase;
        }

        .observacion-box p {
            font-size: 9px;
            margin-top: 4px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 8px;
            border-top: 2px solid #000;
            font-size: 8px;
        }

        .footer-line {
            margin: 2px 0;
        }

        .estudio-destacado {
            border: 2px solid #000;
            padding: 8px;
            background: #f5f5f5;
            text-align: center;
            margin-bottom: 8px;
        }

        .estudio-destacado .info-value {
            font-size: 12px;
            font-weight: bold;
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
            <div class="info-item">
                <div class="info-label">Nombre Completo</div>
                <div class="info-value">{{ $paciente ? ($paciente->nomPa . ' ' . $paciente->paternoPa . ' ' . $paciente->maternoPa) : 'No registrado' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">HCI</div>
                <div class="info-value">{{ $paciente->nroHCI ?? 'Sin HCI' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Sexo</div>
                <div class="info-value">{{ $paciente->sexo === 'M' ? 'Masculino' : 'Femenino' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Tipo de Paciente</div>
                <div class="info-value">{{ $paciente->tipoPac ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- DETALLES DEL SERVICIO -->
    <div class="section">
        <div class="section-title">Detalles del Servicio</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Nro. Servicio</div>
                <div class="info-value">{{ $nroServicio }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Médico Solicitante</div>
                <div class="info-value">{{ $medico ? ($medico->nomMed . ' ' . $medico->paternoMed) : 'No asignado' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Fecha de Solicitud</div>
                <div class="info-value">{{ $fechaSolicitud }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Hora de Solicitud</div>
                <div class="info-value">{{ $horaSolicitud }}</div>
            </div>
        </div>
    </div>

    <!-- TIPO DE ESTUDIO -->
    <div class="section">
        <div class="section-title">Tipo de Estudio</div>
        <div class="estudio-destacado">
            <div class="info-label">Estudio a Realizar</div>
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
        @if(isset($requisitos) && count($requisitos) > 0)
        <div class="requisitos-lista">
            <h4>Requisitos que debe cumplir:</h4>
            @foreach($requisitos as $requisito)
            <div class="requisito-item">
                {{ $requisito->descripReq }}
            </div>
            @endforeach
        </div>
        @else
        <div class="requisitos-lista">
            <h4>Requisitos:</h4>
            <p style="font-size: 9px; margin-top: 5px;">No se requieren requisitos especiales para este estudio.</p>
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
