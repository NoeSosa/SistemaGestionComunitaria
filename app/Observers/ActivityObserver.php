<?php

namespace App\Observers;

use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Filament\Notifications\Notification;

class ActivityObserver
{
    public function created(Activity $activity): void
    {
        // Solo notificar si la acción es "deleted" (eliminar)
        if ($activity->description === 'deleted') {
            // Buscar a los Super Admins
            $admins = User::role('super_admin')->get();
            foreach ($admins as $admin) {
                // Notificación nativa de Filament
                Notification::make()
                    ->title('⚠️ ALERTA DE SEGURIDAD')
                    ->body("El usuario " . ($activity->causer->name ?? 'Sistema') . " eliminó un registro en {$activity->subject_type}.")
                    ->danger()
                    ->sendToDatabase($admin);
            }
        }
    }
}
