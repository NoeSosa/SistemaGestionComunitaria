<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssemblyResource\Pages;
use App\Filament\Resources\AssemblyResource\RelationManagers;
use App\Models\Assembly;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class AssemblyResource extends Resource
{
    protected static ?string $model = Assembly::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Gestión de Eventos';
    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Asamblea';
    protected static ?string $pluralModelLabel = 'Asambleas';
    protected static ?string $navigationLabel = 'Asambleas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Título de la Asamblea')
                    ->required()
                    ->columnSpanFull(), // Ocupa todo el ancho

                Textarea::make('description')
                    ->label('Descripción / Orden del día')
                    ->rows(3)
                    ->columnSpanFull(),

                DateTimePicker::make('date')
                    ->label('Fecha y Hora')
                    ->required(),

                Select::make('status')
                    ->label('Estado Actual')
                    ->options([
                        'pending' => 'Pendiente (Esperando inicio)',
                        'in_progress' => 'En Progreso (Escáner Activo)',
                        'completed' => 'Finalizada (Cerrada)',
                    ])
                    ->default('pending')
                    ->required()
                    ->native(false), // Hace el select más bonito
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Asamblea')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('date')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge() // Lo convierte en una etiqueta de color
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'success',
                        'completed' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'in_progress' => 'En Curso',
                        'completed' => 'Finalizada',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc') // Las más recientes primero
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // --- NUEVO BOTÓN: ABRIR ESCÁNER ---
                Tables\Actions\Action::make('scan')
                    ->label('Escanear')
                    ->icon('heroicon-o-qr-code') // Icono de QR
                    ->url(fn ($record) => route('scan', $record)) // Enlace a tu ruta
                    ->openUrlInNewTab() // Abre pestaña nueva para no perder el admin
                    ->color('primary') // Color Azul
                    // Condición: Solo mostrar si la asamblea está EN PROGRESO
                    ->visible(fn ($record) => $record->status === 'in_progress'),

                Action::make('monitor')
                    ->label('Abrir Monitor')
                    ->icon('heroicon-o-presentation-chart-line')
                    ->url(fn ($record) => route('monitor', $record))
                    ->openUrlInNewTab() // Abre en otra pestaña para tener el admin libre
                    ->color('success'),

                Action::make('pdf')
                    ->label('Descargar Acta')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn ($record) => route('assemblies.report', $record))
                    ->openUrlInNewTab()
                    ->color('warning'), // Color ámbar para distinguir
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssemblies::route('/'),
            'create' => Pages\CreateAssembly::route('/create'),
            'edit' => Pages\EditAssembly::route('/{record}/edit'),
        ];
    }
}
