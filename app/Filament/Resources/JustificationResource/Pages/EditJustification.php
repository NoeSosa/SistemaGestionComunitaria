<?php

namespace App\Filament\Resources\JustificationResource\Pages;

use App\Filament\Resources\JustificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJustification extends EditRecord
{
    protected static string $resource = JustificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
