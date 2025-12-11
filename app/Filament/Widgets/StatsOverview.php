<?php

namespace App\Filament\Widgets;

use App\Models\Assembly;
use App\Models\Citizen;
use App\Models\Fine;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Esto define qué tan rápido se refresca (opcional)
    protected static ?string $pollingInterval = '15s';

    public static function canView(): bool
    {
        // Lógica inteligente: 
        // Muestra las estadísticas si el usuario tiene permiso para ver Ciudadanos O Multas.
        return auth()->user()->can('view_any_citizen') || auth()->user()->can('view_any_fine');
    }

    protected function getStats(): array
    {
        return [
            // Tarjeta 1: Población
            Stat::make('Padrón Activo', Citizen::where('is_active', true)->count())
                ->description('Ciudadanos registrados')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Gráfica decorativa

            // Tarjeta 2: Asambleas este mes
            Stat::make('Asambleas (Este Mes)', Assembly::whereMonth('date', now()->month)->count())
                ->description('Eventos programados')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),

            // Tarjeta 3: Corte de Caja (HOY)
            Stat::make('Corte de Caja (HOY)', '$' . number_format(Fine::whereDate('paid_at', today())->sum('amount'), 2))
                ->description('Cobrado el ' . now()->format('d/m/Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([Fine::whereDate('paid_at', today())->sum('amount') > 0 ? 10 : 0]),
        ];
    }
}
