<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Citizen extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'curp', 'phone', 'address', 'neighborhood', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = ['name', 'curp', 'phone', 'address', 'neighborhood', 'is_active'];

    // Relación: Un ciudadano va a muchas asambleas
    public function assemblies()
    {
        return $this->belongsToMany(Assembly::class, 'attendances')
                    ->withPivot('check_in_at', 'quorum_check_at')
                    ->withTimestamps();
    }

    public function fines()
    {
        return $this->belongsToMany(Assembly::class, 'fines')
                    ->withPivot('amount', 'paid_at')
                    ->withTimestamps();
    }

    public function justifications()
    {
        return $this->hasMany(Justification::class);
    }
}
