<?php

namespace App\Livewire;

use App\Models\Assembly;
use App\Models\Citizen;
use Livewire\Component;
use Livewire\Attributes\Layout;

class AssemblyMonitor extends Component
{
    public Assembly $assembly;

    public function mount(Assembly $assembly)
    {
        $this->assembly = $assembly;
    }

    #[Layout('components.layouts.app')] 
    public function render()
    {
        // 1. Total del Padrón (Ciudadanos Activos)
        $totalCitizens = Citizen::where('is_active', true)->count();
        
        // 2. Asistentes (Contamos registros en la tabla pivote)
        $attendeesCount = $this->assembly->citizens()->count();
        
        // 3. Cálculo de Quórum
        $quorumPercentage = $totalCitizens > 0 
            ? round(($attendeesCount / $totalCitizens) * 100, 1) 
            : 0;

        // 4. Últimos 10 registrados (Ordenados por hora de llegada)
        $recentArrivals = $this->assembly->citizens()
            ->orderByPivot('check_in_at', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.assembly-monitor', [
            'totalCitizens' => $totalCitizens,
            'attendeesCount' => $attendeesCount,
            'quorumPercentage' => $quorumPercentage,
            'recentArrivals' => $recentArrivals,
        ]);
    }
}
