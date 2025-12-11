<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JustificationResource\Pages;
use App\Filament\Resources\JustificationResource\RelationManagers;
use App\Models\Justification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JustificationResource extends Resource
{
    protected static ?string $modelLabel = 'Justificante';
    protected static ?string $pluralModelLabel = 'Justificaciones';
    protected static ?string $navigationGroup = 'Gestión de Eventos'; // Junto a Asambleas
    protected static ?int $navigationSort = 3;
    protected static ?string $model = Justification::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('assembly_id')
                    ->label('Asamblea')
                    ->options(\App\Models\Assembly::where('status', '!=', 'pending')->pluck('title', 'id'))
                    ->required(),

                Forms\Components\Select::make('citizen_id')
                    ->label('Ciudadano')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => \App\Models\Citizen::where('name', 'like', "%{$search}%")
                        ->orWhere('curp', 'like', "%{$search}%")
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn ($citizen) => [$citizen->id => "{$citizen->name} - {$citizen->curp}"]))
                    ->getOptionLabelUsing(function ($value) {
                        $citizen = \App\Models\Citizen::find($value);
                        return $citizen ? "{$citizen->name} - {$citizen->curp}" : null;
                    })
                    ->required(),

                Forms\Components\Textarea::make('reason')
                    ->label('Motivo de la Falta')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assembly.title')->label('Asamblea'),
                Tables\Columns\TextColumn::make('citizen.name')->label('Ciudadano'),
                Tables\Columns\TextColumn::make('reason')->label('Motivo')->limit(30),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListJustifications::route('/'),
            'create' => Pages\CreateJustification::route('/create'),
            'edit' => Pages\EditJustification::route('/{record}/edit'),
        ];
    }
}
