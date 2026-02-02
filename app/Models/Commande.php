<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reservation extends Model
{
    public function tickets()
    {
        return $this->hasMany(reservationTicket::class, 'reservation_id');
    }

    public function getMontantTotalAttribute()
    {
        return $this->tickets->sum('montant_total');
    }
}
