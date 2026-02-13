<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';

    public function tickets()
    {
        return $this->hasMany(ReservationTicket::class, 'reservation_id');
    }

    public function getMontantTotalAttribute()
    {
        return $this->tickets->sum('montant_total');
    }
}
