{{--
| Componente Blade: etiqueta-muestra
| Recibe: $muestra (App\Models\Muestra)
| Objetivo: Renderizar una etiqueta imprimible de 30mm x 20mm.
--}}
@props(['muestra'])

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Etiqueta - {{ $muestra->codigo_muestra }}</title>
    <style>
        /* RESET BÁSICO */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        /* TAMAÑO DE DOCUMENTO 30mm x 20mm */
        html, body {
            width: 30mm;
            height: 20mm;
            font-family: Arial, sans-serif;
            background-color: white; /* SIEMPRE BLANCO PARA TÉRMICA */
        }
        
        /* CONTENEDOR PRINCIPAL */
        .etiqueta {
            width: 30mm;
            height: 20mm;
            display: flex;
            flex-direction: row;
            background: white; /* CORREGIDO: Quitamos el rojo */
            overflow: hidden; /* Evita que si algo se sale, cree una segunda página */
        }
        
        /* COLUMNA DEL CÓDIGO DE BARRAS 
           Aquí controlamos la alineación horizontal del bloque rotado.
        */
        .barcode-section {
            width: 14mm; /* Le damos un poco más de espacio */
            height: 20mm;
            display: flex;
            
            /* CENTRADO VERTICAL (eje Y) */
            align-items: center; 
            
            /* ALINEACIÓN HORIZONTAL (eje X) */
            /* Si quieres que se pegue a la derecha (hacia el texto), usa flex-end */
            /* Si quieres que se pegue a la izquierda (borde papel), usa flex-start */
            justify-content: center; 
            
            background: white; /* CORREGIDO: Quitamos el verde */
        }

        /* ENVOLTORIO QUE ROTA */
        .barcode-wrap {
            width: 16mm; /* Ancho visual tras rotar */
            height: 12mm; /* Alto visual tras rotar */
            display: flex;
            align-items: center;
            justify-content: center;
            
            /* Rotación */
            transform: rotate(90deg);
            /* Importante: Mantenerlo en el centro para no perder referencia */
            transform-origin: center;
            
            background: transparent; /* CORREGIDO: Quitamos el magenta */
        }
        
        /* COLUMNA DE INFORMACIÓN */
        .info-section {
            width: 16mm; /* Resto del espacio */
            height: 20mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center; /* Centrado horizontal del texto */
            text-align: center;
            padding-right: 1mm; /* Margen de seguridad derecho */
        }
        
        .titulo {
            font-size: 8pt; /* Subido un poco para legibilidad */
            font-weight: 900; /* Extra bold para térmica */
            color: black;
            margin-bottom: 2mm;
            text-transform: uppercase;
        }
        
        .codigo {
            font-size: 6pt; /* 4.5pt es muy arriesgado, probamos con 6pt */
            font-weight: bold;
            font-family: 'Courier New', monospace; /* Fuente monoespaciada ayuda a leer códigos */
            color: black;
        }
        
        /* REGLAS DE IMPRESIÓN */
        @media print {
            @page {
                size: 30mm 20mm;
                margin: 0;
            }
            html, body {
                width: 30mm;
                height: 20mm;
            }
        }
        
        /* VISTA EN PANTALLA (DEBUG) */
        @media screen {
            html {
                background: #333; /* Fondo oscuro para contrastar la etiqueta blanca */
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }
            body {
                box-shadow: 0 0 10px rgba(0,0,0,0.5); /* Sombra para ver los bordes */
            }
        }
    </style>
</head>
<body>
    <div class="etiqueta">
        
        {{-- SECCIÓN BARCODE (IZQUIERDA) --}}
        <div class="barcode-section">
            <div class="barcode-wrap">
                {{-- Asegúrate que tu generador no inyecte estilos inline con colores --}}
                {!! $muestra->generarCodigoBarras() !!}
            </div>
        </div>
        
        {{-- SECCIÓN TEXTO (DERECHA) --}}
        <div class="info-section">
            <div class="titulo">LABVET</div>
            <div class="codigo">{{ $muestra->codigo_muestra }}</div>
        </div>

    </div>
</body>
</html>