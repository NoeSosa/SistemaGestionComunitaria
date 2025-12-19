<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Credencial</title>
    <style>
        @page {
            margin: 0;
            size: 85.6mm 53.98mm; /* Tamaño estándar Tarjeta CR80 */
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', sans-serif;
            background-color: #fff;
        }

        .container {
            width: 100%;
            height: 100%;
            /* Borde gris muy suave para guiar el corte si se imprime en hoja blanca */
            border: 1px solid #eee; 
        }

        /* Encabezado */
        .header {
            background-color: #1e293b; /* Azul oscuro */
            color: white;
            padding: 8px 10px;
            height: 35px;
        }
        
        .header-table { width: 100%; border-collapse: collapse; }
        .header-logo { width: 30px; vertical-align: middle; }
        .header-text { 
            font-size: 10px; 
            font-weight: bold; 
            text-transform: uppercase; 
            vertical-align: middle;
            text-align: center;
        }

        /* Cuerpo de la tarjeta */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px; /* Un poco más de aire arriba */
        }

        .col-left {
            width: 60%;
            padding-left: 15px;
            vertical-align: top;
        }

        .col-right {
            width: 40%;
            text-align: center;
            vertical-align: middle;
            padding-right: 10px;
        }

        /* Estilos de texto */
        .label {
            font-size: 6px;
            color: #64748b;
            text-transform: uppercase;
            margin-top: 8px; /* Más espacio entre campos */
            font-weight: bold;
        }

        .value {
            font-size: 10px;
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.1; /* Interlineado compacto para nombres largos */
        }

        .curp {
            font-family: 'Courier', monospace;
            font-size: 10px;
            letter-spacing: -0.5px;
        }

        .qr-img {
            width: 95%;
            height: auto;
        }

        .footer {
            position: fixed;
            bottom: 6px;
            left: 15px;
            font-size: 5px;
            color: #94a3b8;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-logo">
                        <img src="{{ public_path('images/logo.png') }}" width="25">
                    </td>
                    <td class="header-text">
                        {{ $town_name }}
                    </td>
                </tr>
            </table>
        </div>

        <table class="content-table">
            <tr>
                <td class="col-left">
                    <div class="label" style="margin-top: 0;">NOMBRE</div>
                    <div class="value">{{ $citizen->name }}</div>

                    <div class="label">CURP</div>
                    <div class="value curp">{{ $citizen->curp }}</div>

                    <div class="label">COLONIA</div>
                    <div class="value">{{ \Illuminate\Support\Str::limit($citizen->neighborhood, 25) }}</div>
                    
                    </td>

                <td class="col-right">
                    <img src="{{ $qrImage }}" class="qr-img">
                </td>
            </tr>
        </table>

        <div class="footer">
            Identificación oficial de asistencia municipal
        </div>
    </div>
</body>
</html>