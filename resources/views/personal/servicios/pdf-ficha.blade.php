<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Servicio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            padding: 20px;
            background-color: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .hospital-name {
            font-size: 18pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }

        .subtitle {
            font-size: 11pt;
            color: #555;
            margin-bottom: 5px;
        }

        .document-info {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            color: #555;
            margin-top: 8px;
        }

        .title-section {
            text-align: center;
            margin: 15px 0 10px 0;
            padding: 8px;
            border: 1px solid #000;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .title-section h1 {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
        }

        .info-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background-color: #fff;
        }

        .info-box-title {
            font-weight: bold;
            font-size: 11pt;
            color: #000;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        .info-row {
            display: flex;
            margin: 3px 0;
            font-size: 10pt;
        }

        .info-label {
            font-weight: bold;
            min-width: 140px;
            color: #000;
        }

        .info-value {
            color: #000;
        }

        .content-section {
            margin: 10px 0;
            border: 1px solid #000;
            border-radius: 4px;
            padding: 10px;
            background-color: #fff;
        }

        .section-header {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        .observacion-text {
            font-size: 10pt;
            line-height: 1.4;
            text-align: justify;
            white-space: pre-line;
            padding: 8px;
            background-color: #fff;
            border-left: 3px solid #000;
            border-radius: 3px;
        }

        .requisitos-list {
            margin-top: 8px;
        }

        .requisito-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 6px;
            padding: 6px;
            border-left: 3px solid #000;
            border-radius: 3px;
            background-color: #fff;
        }

        .requisito-text {
            font-size: 10pt;
            color: #000;
            flex-grow: 1;
        }

        .diagnostico-box {
            background-color: #fff;
            border: 1px solid #000;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .diagnostico-tipo {
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            font-size: 10pt;
        }

        .diagnostico-texto {
            color: #000;
            font-size: 10pt;
            line-height: 1.4;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 8pt;
            color: #333;
        }

        .footer-note {
            margin-top: 5px;
            font-style: italic;
            color: #555;
        }

        @page {
            margin: 20px;
            size: letter;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <div class="hospital-name">CENTRO DE DIAGNÓSTICO POR IMAGEN</div>
        <div class="subtitle">Servicio de Ecografía</div>
        <div class="document-info">
            <span>Nro. Servicio: {{ $servicio->nroServ }}</span>
            <span>Fecha de emisión: {{ $fecha }}</span>
            <span>Hora: {{ $hora }}</span>
        </div>
    </div>

    <!-- Título -->
    <div class="title-section">
        <h1>FICHA DE SERVICIO</h1>
    </div>

    <!-- Información del Paciente -->
    <div class="info-box">
        <div class="info-box-title">Información del Paciente</div>
        <div class="info-row">
            <span class="info-label">Nombre Completo:</span>
            <span class="info-value">{{ $servicio->paciente->nomPa }} {{ $servicio->paciente->paternoPa }} {{ $servicio->paciente->maternoPa }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">N° Historia Clínica:</span>
            <span class="info-value">{{ $servicio->paciente->nroHCI }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha Nacimiento:</span>
            <span class="info-value">
                {{ \Carbon\Carbon::parse($servicio->paciente->fechaNac)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Sexo:</span>
            <span class="info-value">{{ $servicio->paciente->sexoPa == 'M' ? 'Masculino' : 'Femenino' }}</span>
        </div>
    </div>

    <!-- Información del Médico Solicitante -->
    <div class="info-box">
        <div class="info-box-title">Médico Solicitante</div>
        <div class="info-row">
            <span class="info-label">Nombre:</span>
            <span class="info-value">{{ $servicio->medico->nomMed }} {{ $servicio->medico->paternoMed }} {{ $servicio->medico->maternoMed }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tipo:</span>
            <span class="info-value">{{ $servicio->medico->tipoMed ?? 'No especificado' }}</span>
        </div>
    </div>

    <!-- Tipo de Estudio -->
    <div class="info-box">
        <div class="info-box-title">Tipo de Estudio</div>
        <div class="info-row">
            <span class="info-label">Descripción:</span>
            <span class="info-value">{{ $servicio->tipoEstudio->descripcion }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha Programada:</span>
            <span class="info-value">
                @if($servicio->cronograma)
                    {{ \Carbon\Carbon::parse($servicio->cronograma->fechaCrono)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                @else
                    No programado
                @endif
            </span>
        </div>
    </div>

    <!-- Instrucciones para el Paciente -->
    @if($servicio->tipoEstudio->observacion)
    <div class="content-section">
        <div class="section-header">INSTRUCCIONES PARA EL PACIENTE</div>
        <div class="observacion-text">{{ $servicio->tipoEstudio->observacion }}</div>
    </div>
    @endif

    <!-- Requisitos Específicos -->
    @if($servicio->tipoEstudio->requisitos->isNotEmpty())
    <div class="content-section">
        <div class="section-header">REQUISITOS ESPECÍFICOS</div>
        <div class="requisitos-list">
            @foreach($servicio->tipoEstudio->requisitos as $requisito)
            <div class="requisito-item">
                <span class="requisito-text">• {{ $requisito->descripRequisito }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Diagnósticos -->
    @if($servicio->diagnosticos->isNotEmpty())
    <div class="content-section">
        <div class="section-header">DIAGNÓSTICOS</div>
        @foreach($servicio->diagnosticos as $diagnostico)
        <div class="diagnostico-box">
            <div class="diagnostico-tipo">
                {{ $diagnostico->pivot->tipo == 'sol' ? 'Diagnóstico Solicitado' : 'Diagnóstico Ecográfico' }}
            </div>
            <div class="diagnostico-texto">{{ $diagnostico->descripDiag }}</div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Pie -->
    <div class="footer">
        <div>Centro de Diagnóstico por Imagen - Servicio de Ecografía</div>
        <div class="footer-note">
            Este documento contiene información completa sobre el servicio y preparación necesaria para el estudio.
        </div>
        <div style="margin-top: 5px;">
            Generado el {{ $fecha }} a las {{ $hora }}
        </div>
    </div>
</body>
</html>
