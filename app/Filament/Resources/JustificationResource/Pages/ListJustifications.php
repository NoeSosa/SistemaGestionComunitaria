<?php

namespace App\Filament\Resources\JustificationResource\Pages;

use App\Filament\Resources\JustificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJustifications extends ListRecords
{
    protected static string $resource = JustificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
