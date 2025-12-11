<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    public function card(Citizen $citizen)
    {
        $pdf = Pdf::loadView('pdf.id-card', [
            'citizen' => $citizen,
            'town_name' => Setting::get('town_name', 'Municipio'),
        ]);

        $pdf->setPaper([0, 0, 255.118, 155.906], 'landscape'); // 90mm x 55mm in points (1mm = 2.83465pt)
        // 90mm * 2.83465 = 255.1185
        // 55mm * 2.83465 = 155.90575
        // Wait, user said 90mm x 50mm in text but 90mm x 55mm in CSS. I'll stick to CSS 90mm x 55mm.
        // Actually, setPaper accepts 'letter', 'a4', or array [0, 0, width, height] in points.
        // Let's rely on the CSS @page size if possible, or set it here.
        // The user provided CSS: @page { margin: 0; size: 90mm 55mm; }
        // DomPDF usually respects @page size if enabled, but setting it here is safer.
        // 90mm = 255.12 pt, 55mm = 155.91 pt.
        
        return $pdf->stream('Credencial-' . $citizen->curp . '.pdf');
    }
}
