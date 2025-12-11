<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Pago</title>
    <style>
        /* 1. Definimos el margen de la hoja PDF en 0 para controlarlo nosotros desde el body */
        @page { margin: 0px; }

        body { 
            font-family: 'Courier New', Courier, monospace; 
            font-size: 13px; /* Bajamos un punto la letra para asegurar ajuste */
            line-height: 1.3;
            color: #000;
            /* 2. Aquí damos el margen real de seguridad (2cm aprox) */
            padding: 40px; 
        }

        /* Contenedor Principal */
        .receipt-box {
            /* 3. Borde punteado */
            border: 2px dashed #000;
            
            /* 4. IMPORTANTE: Ancho al 95% para que no toque los bordes */
            width: 95%; 
            margin: 0 auto; /* Centrado horizontalmente */
            
            padding: 25px;
            box-sizing: border-box; /* Para que el padding no agrande la caja */
            
            /* Evita que se parta en dos hojas si el contenido crece */
            page-break-inside: avoid; 
        }

        .header { text-align: center; margin-bottom: 25px; }
        .town-name { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .town-address { font-size: 11px; margin-bottom: 15px; }
        .receipt-title { font-size: 15px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .folio { font-size: 11px; color: #444; }

        .content { margin-bottom: 20px; }
        .row { margin-bottom: 12px; overflow: hidden; }
        
        /* Ajustamos anchos para que quepan bien */
        .label { float: left; width: 110px; font-weight: bold; }
        .value { float: left; width: 380px; text-transform: uppercase; }

        /* Caja del Total */
        .total-box {
            border: 2px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 25px 0;
            background-color: #f5f5f5;
        }

        /* Limpiar flotados */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .signatures { margin-top: 50px; text-align: center; }
        .sign-line { border-top: 1px solid #000; width: 220px; margin: 0 auto 5px auto; }
        .sign-title { font-size: 11px; text-transform: uppercase; }

        .footer { 
            margin-top: 30px; 
            text-align: center; 
            font-size: 9px; 
            color: #666;
        }
    </style>
</head>
<body>
    <div class="receipt-box">
        <div class="header">
            <img src="{{ public_path('images/logo.png') }}" style="height: 60px; margin-bottom: 10px;">
            
            <div class="town-name">{{ $town_name }}</div>
            <div class="town-address">{{ $town_address }}</div>
            <div class="receipt-title">RECIBO DE PAGO DE MULTA</div>
            <div class="folio">Folio: #{{ str_pad($fine->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>

        <div class="content">
            <div class="row clearfix">
                <div class="label">Fecha:</div>
                <div class="value">{{ $fine->paid_at->format('d/m/Y h:i A') }}</div>
            </div>
            <div class="row clearfix">
                <div class="label">Recibimos de:</div>
                <div class="value">{{ $fine->citizen->name }}</div>
            </div>
            <div class="row clearfix">
                <div class="label">Concepto:</div>
                <div class="value">
                    Inasistencia a la Asamblea "{{ $fine->assembly->title }}" <br>
                    del {{ $fine->assembly->date->format('d/m/Y') }}.
                </div>
            </div>
        </div>

        <div class="total-box">
            TOTAL PAGADO: ${{ number_format($fine->amount, 2) }} MXN
        </div>

        @if($fine->notes)
        <div class="content">
            <div class="row clearfix">
                <div class="label">Notas:</div>
                <div class="value">{{ $fine->notes }}</div>
            </div>
        </div>
        @endif

        <div class="signatures">
            <div class="sign-line"></div>
            <div class="sign-title">Firma del Tesorero/Cobrador</div>
        </div>

        <div class="footer">
            {{ $footer }}<br>
            Este recibo sirve como comprobante de regularización de falta.
        </div>
    </div>
</body>
</html>