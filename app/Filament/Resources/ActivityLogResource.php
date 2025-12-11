<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Spatie\Activitylog\Models\Activity; // <-- Apuntamos al Modelo de Spatie
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    // Conectamos con el modelo oficial de la librería
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?string $navigationLabel = 'Bitácora';
    protected static ?string $modelLabel = 'Movimiento';
    protected static ?string $pluralModelLabel = 'Bitácora de Movimientos';

    public static function canViewAny(): bool
    {
        // Solo el Super Admin debería ver la auditoría de seguridad
        return auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sección: Quién y Cuándo
                Forms\Components\Section::make('Detalles del Evento')
                    ->schema([
                        Forms\Components\TextInput::make('causer.name')
                            ->label('Usuario Responsable')
                            ->placeholder('Sistema / Anónimo'),
                        
                        Forms\Components\TextInput::make('event')
                            ->label('Acción')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'created' => 'Creó',
                                'updated' => 'Actualizó',
                                'deleted' => 'Eliminó',
                                default => $state,
                            }),

                        Forms\Components\TextInput::make('created_at')
                            ->label('Fecha y Hora')
                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d/m/Y H:i:s')),
                    ])->columns(3),

                // Sección: Qué cambió (El JSON de cambios)
                Forms\Components\Section::make('Cambios Registrados')
                    ->schema([
                        Forms\Components\KeyValue::make('properties.attributes')
                            ->label('Datos Nuevos / Actuales')
                            ->columnSpanFull(),
                            
                        Forms\Components\KeyValue::make('properties.old')
                            ->label('Datos Anteriores')
                            ->visible(fn ($record) => isset($record->properties['old']))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Evento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Creado',
                        'updated' => 'Actualizado',
                        'deleted' => 'Eliminado',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Módulo')
                    ->formatStateUsing(fn ($state) => class_basename($state)), // Muestra "Citizen" en vez de "App\Models\Citizen"

                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Usuario')
                    ->default('Sistema'),
            ])
            ->defaultSort('created_at', 'desc') // Lo más reciente primero
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver Detalles'),
            ])
            ->filters([
                // Filtro por acción
                Tables\Filters\SelectFilter::make('event')
                    ->label('Tipo de Acción')
                    ->options([
                        'created' => 'Creaciones',
                        'updated' => 'Actualizaciones',
                        'deleted' => 'Eliminaciones',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageActivityLogs::route('/'),
        ];
    }
    
    // Bloqueamos que nadie pueda editar o borrar la bitácora (Solo lectura)
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }
}
