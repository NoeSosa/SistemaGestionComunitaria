<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class FinanceReport extends Page implements HasForms
{
    use InteractsWithForms;
    
    // Variables para el formulario
    public ?string $from_date = null;
    public ?string $to_date = null;

    protected static string $view = 'filament.pages.finance-report';
    protected static ?string $navigationGroup = 'Tesorería';
    protected static ?string $title = 'Corte de Caja General';

    public static function canAccess(): bool
    {
        // Solo Super Admin y Tesorero pueden ver el dinero
        return auth()->user()->hasAnyRole(['super_admin', 'tesorero']);
    }

    // Definir acciones de la página
    protected function getActions(): array
    {
        return [
            Action::make('print_today')
                ->label('Imprimir Corte de HOY')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn() => route('report.finance', [
                    'from' => now()->format('Y-m-d'),
                    'to' => now()->format('Y-m-d')
                ]))
                ->openUrlInNewTab(),
        ];
    }

    // Definir el formulario
    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('from_date')->label('Desde')->required(),
            DatePicker::make('to_date')->label('Hasta')->required(),
        ];
    }

    // Definir la acción del botón
    public function generateAction(): Action
    {
        return Action::make('generate')
            ->label('Generar Reporte PDF')
            ->action(function () {
                // Validación
                $this->validate();
                
                // Redirigir al controlador de PDF enviando las fechas
                return redirect()->route('report.finance', [
                    'from' => $this->from_date,
                    'to' => $this->to_date
                ]);
            });
    }
}
