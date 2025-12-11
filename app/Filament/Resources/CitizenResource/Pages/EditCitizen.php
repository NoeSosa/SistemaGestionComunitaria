<?php

namespace App\Filament\Resources\CitizenResource\Pages;

use App\Filament\Resources\CitizenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCitizen extends EditRecord
{
    protected static string $resource = CitizenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
