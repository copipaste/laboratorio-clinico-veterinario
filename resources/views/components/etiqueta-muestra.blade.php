@props(['muestra'])

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Etiqueta - {{ $muestra->codigo_muestra }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .etiqueta {
            width: 10cm;
            height: 5cm;
            border: 2px solid #333;
            padding: 0.5cm;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 0.2cm;
            margin-bottom: 0.2cm;
        }
        
        .header h1 {
            font-size: 10pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }
        
        .header p {
            font-size: 7pt;
            color: #666;
            margin: 0;
        }
        
        .barcode-section {
            text-align: center;
            margin: 0.1cm 0;
        }
        
        .barcode-section svg {
            width: 100%;
            max-width: 8cm;
            height: auto;
        }
        
        .codigo {
            font-size: 9pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            margin-top: 0.1cm;
            letter-spacing: 0.5px;
        }
        
        .info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.15cm;
            font-size: 7pt;
            line-height: 1.3;
        }
        
        .info-item {
            display: flex;
            gap: 0.1cm;
        }
        
        .info-label {
            font-weight: bold;
            color: #333;
        }
        
        .info-value {
            color: #000;
        }
        
        .info-full {
            grid-column: span 2;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .etiqueta {
                border: 1px solid #000;
                page-break-after: avoid;
            }
            
            @page {
                size: 10cm 5cm;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="etiqueta">
        <div class="header">
            <h1>LABORATORIO CLÍNICO VETERINARIO</h1>
            <p>{{ $muestra->sucursal->nombre ?? 'Sucursal Principal' }}</p>
        </div>
        
        <div class="barcode-section">
            {!! $muestra->generarCodigoBarras() !!}
            <div class="codigo">{{ $muestra->codigo_muestra }}</div>
        </div>
        
        <div class="info">
            <div class="info-item">
                <span class="info-label">Paciente:</span>
                <span class="info-value">{{ Str::limit($muestra->paciente_nombre, 20) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Especie:</span>
                <span class="info-value">{{ $muestra->especie->nombre ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Propietario:</span>
                <span class="info-value">{{ Str::limit($muestra->propietario_nombre, 18) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Fecha:</span>
                <span class="info-value">{{ $muestra->fecha_recepcion->format('d/m/Y') }}</span>
            </div>
            <div class="info-item info-full">
                <span class="info-label">Tipo:</span>
                <span class="info-value">{{ $muestra->tipo_muestra }}</span>
            </div>
        </div>
    </div>
</body>
</html>
