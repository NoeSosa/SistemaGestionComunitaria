<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Fine extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['assembly_id', 'citizen_id', 'amount', 'notes', 'paid_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'assembly_id',
        'citizen_id',
        'amount',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }
}
