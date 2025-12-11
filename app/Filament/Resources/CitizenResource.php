<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CitizenResource\Pages;
use App\Filament\Resources\CitizenResource\RelationManagers;
use App\Models\Citizen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use App\Filament\Imports\CitizenImporter;
use Filament\Tables\Actions\ImportAction;

class CitizenResource extends Resource
{
    protected static ?string $model = Citizen::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Padrón Municipal';
    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Ciudadano';
    protected static ?string $pluralModelLabel = 'Ciudadanos';
    protected static ?string $navigationLabel = 'Padrón de Ciudadanos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Personal')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('curp')
                            ->label('CURP')
                            ->required()
                            ->unique(ignoreRecord: true) // Evita duplicados
                            ->maxLength(18)
                            ->minLength(18)
                            ->formatStateUsing(fn (?string $state): ?string => strtoupper($state)), // Auto-mayúsculas
                    ])->columns(2),

                Section::make('Datos de Contacto y Ubicación')
                    ->description('Información para futuros módulos (Agua, Predial)')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel(),
                        
                        TextInput::make('address')
                            ->label('Calle y Número'),
                        
                        TextInput::make('neighborhood')
                            ->label('Colonia / Barrio'),
                    ])->columns(3),

                Toggle::make('is_active')
                    ->label('Ciudadano Activo')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(CitizenImporter::class)
                    ->label('Importar Excel')
                    ->color('primary'),
            ])
            ->columns([
                TextColumn::make('curp')
                    ->label('CURP')
                    ->searchable() // ¡Buscador automático!
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('neighborhood')
                    ->label('Colonia')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                    
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Action::make('qr')
                    ->label('Ver QR')
                    ->icon('heroicon-o-qr-code')
                    ->modalContent(fn ($record) => view('components.qr-card', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalWidth('md'),

                Action::make('credencial')
                    ->label('Credencial')
                    ->icon('heroicon-o-identification')
                    ->url(fn ($record) => route('citizen.card', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListCitizens::route('/'),
            'create' => Pages\CreateCitizen::route('/create'),
            'edit' => Pages\EditCitizen::route('/{record}/edit'),
        ];
    }
}
