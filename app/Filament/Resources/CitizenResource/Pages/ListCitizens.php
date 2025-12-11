<?php

namespace App\Filament\Resources\CitizenResource\Pages;

use App\Filament\Resources\CitizenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCitizens extends ListRecords
{
    protected static string $resource = CitizenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
