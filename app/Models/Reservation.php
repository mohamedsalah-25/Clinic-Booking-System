<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'doctor_id',
        'appointment_id',
        'status',
    ];
    //relations
     // العلاقات
     public function user() {
        return $this->belongsTo(User::class);
    }

    public function doctor() {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment() {
        return $this->belongsTo(Appointment::class);
    }
}
