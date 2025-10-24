<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'price',
        'image',
    ];
    
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function reservations()
{
    return $this->hasMany(Reservation::class);
}
}
