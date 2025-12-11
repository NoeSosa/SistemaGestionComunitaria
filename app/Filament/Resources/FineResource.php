<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FineResource\Pages;
use App\Models\Assembly;
use App\Models\Citizen;
use App\Models\Fine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FineResource extends Resource
{
    protected static ?string $model = Fine::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Tesorería';
    protected static ?string $navigationLabel = 'Cobro de Multas';
    protected static ?string $modelLabel = 'Multa / Cobro';
    protected static ?string $pluralModelLabel = 'Cobro de Multas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('assembly_id')
                    ->label('Asamblea (Solo Válidas)')
                    ->placeholder('Seleccione una asamblea')
                    ->searchPrompt('Escriba para buscar...')
                    ->options(function () {
                        $totalActive = \App\Models\Citizen::where('is_active', true)->count();
                        $quorumThreshold = $totalActive / 2;

                        // Filtramos: Solo asambleas terminadas Y que superaron el quórum
                        return \App\Models\Assembly::where('status', 'completed')
                            ->get()
                            ->filter(function ($assembly) use ($quorumThreshold) {
                                return $assembly->citizens()->count() > $quorumThreshold;
                            })
                            ->pluck('title', 'id');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('citizen_id', null)),

                Forms\Components\Select::make('citizen_id')
                    ->label('Ciudadano Deudor')
                    ->placeholder('Seleccione un ciudadano')
                    ->searchPrompt('Escriba para buscar...')
                    ->relationship('citizen', 'name') // <--- Agregamos esto como base
                    ->getOptionLabelUsing(fn ($value): ?string => \App\Models\Citizen::find($value)?->name . ' - ' . \App\Models\Citizen::find($value)?->curp) // <--- ESTO ARREGLA LA VISTA EN EDITAR
                    ->options(function (Forms\Get $get) {
                        $assemblyId = $get('assembly_id');
                        if (!$assemblyId) return [];

                        // Tu lógica de filtro existente...
                        return \App\Models\Citizen::whereDoesntHave('assemblies', function ($query) use ($assemblyId) {
                                $query->where('assemblies.id', $assemblyId);
                            })
                            ->whereDoesntHave('fines', function ($query) use ($assemblyId) {
                                $query->where('assemblies.id', $assemblyId);
                            })
                            // NUEVO: Filtramos que NO tenga justificación
                            ->whereDoesntHave('justifications', function ($query) use ($assemblyId) {
                                $query->where('assembly_id', $assemblyId);
                            })
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(function ($citizen) {
                                return [$citizen->id => "{$citizen->name} - {$citizen->curp}"];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Monto Cobrado ($)')
                    ->numeric()
                    ->prefix('$')
                    ->default(50.00) // Pon aquí el costo default de tu multa
                    ->required(),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Notas'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assembly.title')
                    ->label('Asamblea')
                    ->searchable(),
                Tables\Columns\TextColumn::make('citizen.name')
                    ->label('Ciudadano')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('MXN'),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Fecha de Pago')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->defaultSort('paid_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print_receipt')
                    ->label('Imprimir Recibo')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Fine $record) => route('fines.receipt', $record))
                    ->openUrlInNewTab(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFines::route('/'),
            'create' => Pages\CreateFine::route('/create'),
            'edit' => Pages\EditFine::route('/{record}/edit'),
        ];
    }
}
