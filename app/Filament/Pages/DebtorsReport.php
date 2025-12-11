<?php

namespace App\Filament\Pages;

use App\Models\Assembly;
use App\Models\Citizen;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class DebtorsReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Tesorería';
    protected static ?string $navigationLabel = 'Reporte Global de Deudas';
    protected static ?string $title = 'Estado de Cuenta de Ciudadanos';
    protected static string $view = 'filament.pages.debtors-report';

    public static function canAccess(): bool
    {
        // Solo entra si tiene permiso de ver multas (Tesorero o Super Admin lo tendrán)
        return auth()->user()->can('view_any_fine');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Citizen::query()->where('is_active', true))
            ->columns([
                // Columna Nombre
            TextColumn::make('name')
                ->label('Ciudadano')
                ->searchable()
                ->sortable()
                ->weight('bold'),
                // --- NUEVA COLUMNA CURP ---
            TextColumn::make('curp')
                ->label('CURP')
                ->searchable()
                ->fontFamily('mono') // Fuente tipo máquina de escribir (más legible)
                ->copyable()         // Permite copiar al portapapeles con un clic
                ->color('gray'),
            // --------------------------

                // Columna Calculada: ASAMBLEAS QUE DEBE
                TextColumn::make('pending_debts')
                    ->label('Asambleas Pendientes')
                    ->wrap()
                    ->state(function (Citizen $record) {
                        // Obtenemos asambleas completadas
                        $completedAssemblies = Assembly::where('status', 'completed')->get();
                        // Contamos el padrón total activo (esto es un estimado actual)
                        $totalActive = Citizen::where('is_active', true)->count();
                        $quorumNeeded = $totalActive / 2; // 50%
                        
                        $pending = [];
                        
                        foreach ($completedAssemblies as $assembly) {
                            // --- NUEVA LÓGICA DE QUÓRUM ---
                            $attendeesCount = $assembly->citizens()->count();
                            
                            // Si NO hubo quórum (asistieron menos de la mitad + 1), esta asamblea se perdona
                            if ($attendeesCount <= $quorumNeeded) {
                                continue; // Saltamos a la siguiente, no se cobra
                            }
                            // -------------------------------

                            $attended = $record->assemblies->contains($assembly->id);
                            $paid = $record->fines->contains($assembly->id);
                            
                            // NUEVO: Verificamos si está justificado
                            $justified = \App\Models\Justification::where('assembly_id', $assembly->id)
                                            ->where('citizen_id', $record->id)
                                            ->exists();
                            
                            // Solo debe si: NO fue, NO pagó y NO justificó
                            if (!$attended && !$paid && !$justified) {
                                $pending[] = $assembly->date->format('d/m/Y');
                            }
                        }
                        
                        return empty($pending) ? 'Al corriente' : implode(', ', $pending);
                    })
                    ->color(fn (string $state): string => $state === 'Al corriente' ? 'success' : 'danger'),

                // Columna Calculada: ASAMBLEAS PAGADAS
                TextColumn::make('paid_history')
                    ->label('Historial Pagos')
                    ->wrap()
                    ->state(function (Citizen $record) {
                        return $record->fines->map(function($assembly) {
                            // $assembly es una instancia de Assembly, con datos pivot
                            return $assembly->date->format('d/m/Y') . " ($" . $assembly->pivot->amount . ")";
                        })->implode(', ');
                    })
                    ->color('gray'),
            ])
            ->filters([
                // Filtro para ver solo deudores
                \Filament\Tables\Filters\Filter::make('only_debtors')
                    ->label('Solo Deudores')
                    ->query(function (Builder $query) {
                        // Esta query es compleja, para simplificar filtramos visualmente o 
                        // usamos lógica avanzada. Por rendimiento, dejémoslo simple por ahora.
                        // Si necesitas filtrar estrictamente por SQL requeriría subconsultas.
                    })
            ]);
    }
}
