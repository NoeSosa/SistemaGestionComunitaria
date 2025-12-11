<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255),

                // --- CAMPO DE CONTRASEÑA INTELIGENTE ---
                Forms\Components\TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)) // Encriptar al guardar
                    ->dehydrated(fn ($state) => filled($state)) // Solo enviar si se escribió algo
                    ->required(fn (string $context): bool => $context === 'create'), // Solo obligatoria al crear

                // --- SELECTOR DE ROLES (FILAMENT SHIELD) ---
                Forms\Components\Select::make('roles')
                    ->label('Rol de Usuario')
                    ->relationship('roles', 'name') // Relación con la tabla de roles
                    ->multiple() // Un usuario puede tener varios roles
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                // Ver qué rol tiene asignado
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge() // Se ve como etiqueta de color
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger', // Rojo
                        'tesorero' => 'success',   // Verde
                        'escaneador' => 'info',    // Azul
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
