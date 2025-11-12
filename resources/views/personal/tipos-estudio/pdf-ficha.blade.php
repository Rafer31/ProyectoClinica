<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha - {{ $tipoEstudio->descripcion }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #6366f1;
            padding-bottom: 20px;
        }

        .logo-container {
            margin-bottom: 15px;
        }

        .hospital-name {
            font-size: 22pt;
            font-weight: bold;
            color: #6366f1;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 14pt;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .document-info {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 10pt;
            color: #6b7280;
        }

        .title-section {
            background-color: #6366f1;
            color: white;
            padding: 15px;
            margin: 25px 0;
            border-radius: 5px;
            text-align: center;
        }

        .title-section h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
        }

        .content-section {
            margin: 25px 0;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            background-color: #f9fafb;
        }

        .section-header {
            font-size: 14pt;
            font-weight: bold;
            color: #6366f1;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #6366f1;
            display: flex;
            align-items: center;
        }

        .section-icon {
            width: 20px;
            height: 20px;
            background-color: #6366f1;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .observacion-text {
            font-size: 11pt;
            line-height: 1.8;
            text-align: justify;
            padding: 15px;
            background-color: white;
            border-left: 4px solid #10b981;
            border-radius: 4px;
            white-space: pre-line;
        }

        .requisitos-list {
            margin-top: 15px;
        }

        .requisito-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            border-left: 4px solid #8b5cf6;
        }

        .requisito-icon {
            width: 18px;
            height: 18px;
            background-color: #8b5cf6;
            border-radius: 50%;
            margin-right: 12px;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .requisito-text {
            font-size: 11pt;
            color: #374151;
            flex-grow: 1;
        }

        .info-box {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .info-box-title {
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 8px;
            font-size: 12pt;
        }

        .info-row {
            display: flex;
            margin: 5px 0;
            font-size: 11pt;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #1e40af;
        }

        .info-value {
            color: #374151;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
        }

        .footer-note {
            margin-top: 10px;
            font-style: italic;
        }

        .page-number {
            position: fixed;
            bottom: 20px;
            right: 30px;
            font-size: 10pt;
            color: #6b7280;
        }

        @page {
            margin: 20px;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <div class="hospital-name">CENTRO DE DIAGNÓSTICO POR IMAGEN</div>
        <div class="subtitle">Servicio de Ecografía</div>
        <div class="document-info">
            <span>Fecha de emisión: {{ $fecha }}</span>
            <span>Hora: {{ $hora }}</span>
        </div>
    </div>

    <!-- Título del estudio -->
    <div class="title-section">
        <h1>{{ $tipoEstudio->codTest }}. {{ $tipoEstudio->descripcion }}</h1>
    </div>

    <!-- Información General -->
    <div class="info-box">
        <div class="info-box-title">Información del Estudio</div>
        <div class="info-row">
            <span class="info-label">Código:</span>
            <span class="info-value">{{ $tipoEstudio->codTest }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tipo de Estudio:</span>
            <span class="info-value">{{ $tipoEstudio->descripcion }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total de Requisitos:</span>
            <span class="info-value">{{ $tipoEstudio->requisitos->count() }}</span>
        </div>
    </div>

    <!-- Instrucciones -->
    <div class="content-section">
        <div class="section-header">
            <span class="section-icon"></span>
            INSTRUCCIONES PARA EL PACIENTE
        </div>
        <div class="observacion-text">{{ $observacion }}</div>
    </div>

    <!-- Requisitos -->
    @if($tipoEstudio->requisitos->isNotEmpty())
    <div class="content-section">
        <div class="section-header">
            <span class="section-icon"></span>
            REQUISITOS ESPECÍFICOS
        </div>
        <div class="requisitos-list">
            @foreach($tipoEstudio->requisitos as $requisito)
            <div class="requisito-item">
                <span class="requisito-icon"></span>
                <span class="requisito-text">{{ $requisito->descripRequisito }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Pie de página -->
    <div class="footer">
        <div>Centro de Diagnóstico por Imagen - Servicio de Ecografía</div>
        <div class="footer-note">
            Este documento contiene información sobre los requisitos y preparación necesaria para el estudio.
        </div>
        <div style="margin-top: 15px; font-size: 8pt;">
            Generado el {{ $fecha }} a las {{ $hora }}
        </div>
    </div>

    <!-- Número de página -->
    <div class="page-number">Página 1 de 1</div>
</body>
</html>
