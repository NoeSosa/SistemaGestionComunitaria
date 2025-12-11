<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Assembly extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'date', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = ['title', 'description', 'date', 'status'];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function citizens()
    {
        return $this->belongsToMany(Citizen::class, 'attendances')
                    ->withPivot('check_in_at', 'quorum_check_at')
                    ->withTimestamps();
    }
}
