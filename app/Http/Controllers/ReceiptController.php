<?php
namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function download(Fine $fine)
    {
        $pdf = Pdf::loadView('pdf.receipt', [
            'fine' => $fine,
            'town_name' => Setting::get('town_name'),
            'town_address' => Setting::get('town_address'),
            'footer' => Setting::get('receipt_footer'),
        ]);

    // Tamaño Carta Vertical
    $pdf->setPaper('letter', 'portrait'); 

    return $pdf->stream('Recibo-' . $fine->id . '.pdf');
    }
}
