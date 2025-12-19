<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CitizenController extends Controller
{
    public function card(Citizen $citizen)
    {
        // 2. CONFIGURAR EL QR PARA QUE SEA UNA IMAGEN PNG
        $options = new QROptions([
            'version'      => 5,    // Versión del QR (densidad)
            'outputType'   => QRCode::OUTPUT_IMAGE_PNG, // Queremos PNG, no SVG
            'eccLevel'     => QRCode::ECC_L, // Nivel de corrección de error
            'scale'        => 5,    // Tamaño de los pixeles
            'imageBase64'  => true, // Que nos devuelva la cadena 'data:image/png;base64...'
        ]);

        // 3. GENERAR EL CÓDIGO
        $qrImage = (new QRCode($options))->render($citizen->curp);

        // 4. PASAR LA VARIABLE $qrImage A LA VISTA
       $pdf = Pdf::loadView('pdf.id-card', [
    'citizen' => $citizen,
    'qrImage' => $qrImage,
    'town_name' => Setting::get('town_name'),
]);

// CR80 size (85.6mm x 53.98mm) convertido a puntos (1mm = 2.83pt)
// Ancho: ~242, Alto: ~153
$pdf->setPaper([0, 0, 242.6, 153], 'portrait'); 

return $pdf->stream('Credencial-' . $citizen->curp . '.pdf');
    }
}
