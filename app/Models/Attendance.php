<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['assembly_id', 'citizen_id', 'check_in_at', 'quorum_check_at'];
}