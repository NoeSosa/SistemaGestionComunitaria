<?php

namespace App\Http\Controllers;

use App\Models\Assembly;
use App\Models\Citizen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AssemblyReportController extends Controller
{
    public function download(Assembly $assembly)
    {
        // 1. Ciudadanos Activos
        $totalCitizens = Citizen::where('is_active', true)->get();

        // 2. Asistentes (Los que tienen registro en attendances)
        // Obtenemos sus IDs para filtrar
        $attendeeIds = $assembly->citizens()->pluck('citizens.id')->toArray();
        
        $attendees = $assembly->citizens()
            ->orderBy('name')
            ->get();

        // 3. Ausentes (Están activos pero NO están en la lista de IDs de asistentes)
        // Además cargamos la relación 'fines' para saber si ya pagaron esta asamblea específica
        $absentees = Citizen::where('is_active', true)
            ->whereNotIn('id', $attendeeIds)
            ->with(['fines' => function($q) use ($assembly) {
                // Fix: Use 'assemblies.id' because 'fines' is a belongsToMany relation to Assembly
                $q->where('assemblies.id', $assembly->id);
            }])
            // --- AGREGAMOS ESTO: Cargamos Justificaciones ---
            ->with(['justifications' => function($q) use ($assembly) {
                $q->where('assembly_id', $assembly->id);
            }])
            // -----------------------------------------------
            ->orderBy('name')
            ->get();

        // Estadísticas
        $totalCount = $totalCitizens->count();
        $attendeesCount = $attendees->count();
        $absenteesCount = $absentees->count();
        
        $quorumPercentage = $totalCount > 0 
            ? round(($attendeesCount / $totalCount) * 100, 1) 
            : 0;

        $pdf = Pdf::loadView('pdf.assembly-report', [
            'assembly' => $assembly,
            'attendees' => $attendees,
            'absentees' => $absentees, // <-- Nueva variable enviada
            'stats' => [
                'total' => $totalCount,
                'present' => $attendeesCount,
                'absent' => $absenteesCount,
                'percentage' => $quorumPercentage,
                'is_valid' => $quorumPercentage > 50
            ]
        ]);

        return $pdf->stream('Acta-' . $assembly->date->format('Y-m-d') . '.pdf');
    }
}
