<?php

namespace App\Filament\Imports;

use App\Models\Citizen;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CitizenImporter extends Importer
{
    protected static ?string $model = Citizen::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nombre Completo')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('curp')
                ->label('CURP')
                ->requiredMapping()
                ->rules(['required', 'max:18', 'unique:citizens,curp']), // Valida únicos

            ImportColumn::make('address')
                ->label('Dirección'),

            ImportColumn::make('neighborhood')
                ->label('Colonia'),

            ImportColumn::make('phone')
                ->label('Teléfono'),
        ];
    }

    public function resolveRecord(): ?Citizen
    {
        // Busca si existe por CURP, si no, crea uno nuevo.
        return Citizen::firstOrNew([
            'curp' => $this->data['curp'],
        ]);

        // Opcional: Si quieres actualizar datos existentes usa:
        // return Citizen::updateOrCreate(
        //     ['curp' => $this->data['curp']],
        //     $this->data
        // );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'La importación de ciudadanos se ha completado. ' . number_format($import->successful_rows) . ' ' . str('fila')->plural($import->successful_rows) . ' importadas.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
