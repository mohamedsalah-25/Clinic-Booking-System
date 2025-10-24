<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Doctor;


class Appointment extends Model
{
    protected $fillable = ['doctor_id', 'day', 'date', 'time_start', 'time_end'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
