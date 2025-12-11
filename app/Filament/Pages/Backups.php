<?php

namespace App\Filament\Pages;

use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackupsPage;

class Backups extends BaseBackupsPage
{
    // Este método es el candado. Si devuelve false, la página y el menú desaparecen.
    public static function canAccess(): bool
    {
        // Solo el Super Admin puede ver y descargar respaldos
        return auth()->user()->hasRole('super_admin');
    }
}
