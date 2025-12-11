<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Justification extends Model
{
    protected $fillable = ['assembly_id', 'citizen_id', 'reason'];

    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }
}
