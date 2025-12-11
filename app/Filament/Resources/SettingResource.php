<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    
    // Icono de engranaje
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth'; 
    
    // Lo agrupamos en "Sistema" o lo dejamos suelto
    protected static ?string $navigationGroup = 'Configuración';
    
    protected static ?string $navigationLabel = 'Datos del Municipio';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // La CLAVE (Key) la bloqueamos para que no rompan el sistema
                Forms\Components\TextInput::make('key')
                    ->label('Parámetro de Sistema')
                    ->disabled() 
                    ->dehydrated() // Para que se envíe aunque esté disabled
                    ->required(),

                // El VALOR (Value) es lo que pueden editar
                Forms\Components\Textarea::make('value')
                    ->label('Valor / Contenido')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Configuración')
                    ->badge() // Se ve bonito como etiqueta
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'town_name' => 'Nombre del Pueblo',
                        'town_address' => 'Dirección Oficial',
                        'receipt_footer' => 'Pie de Recibo',
                        default => $state,
                    })
                    ->color('gray'),

                Tables\Columns\TextColumn::make('value')
                    ->label('Valor Actual')
                    ->limit(50)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Último Cambio')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->paginated(false); // Son pocos registros, no necesitamos paginación
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'), // Opcional: podrías quitar esto si no quieres que creen nuevas keys
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
    
    // Opcional: Evitar que creen o borren registros para no romper el código
    public static function canCreate(): bool
    {
       return false; 
    }
    
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}