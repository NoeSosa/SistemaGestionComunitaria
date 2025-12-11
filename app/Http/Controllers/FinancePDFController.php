<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FinancePDFController extends Controller
{
    public function download(Request $request)
    {
        // FORZAMOS LOS LÍMITES DE TIEMPO
        $from = \Carbon\Carbon::parse($request->input('from'))->startOfDay();
        $to = \Carbon\Carbon::parse($request->input('to'))->endOfDay();

        $fines = Fine::whereBetween('paid_at', [$from, $to])
            ->with(['citizen', 'assembly'])
            ->orderBy('paid_at')
            ->get();

        $total = $fines->sum('amount');

        $pdf = Pdf::loadView('pdf.finance-report', [
            'fines' => $fines,
            'total' => $total,
            'from' => $from,
            'to' => $to,
            'town_name' => Setting::get('town_name', 'Municipio'),
            'town_address' => Setting::get('town_address', ''),
        ]);

        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('Corte-Caja-' . $from . '-al-' . $to . '.pdf');
    }
}
