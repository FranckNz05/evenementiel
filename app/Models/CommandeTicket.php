<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reservationTicket extends Model
{
    protected $table = 'reservation_tickets';

    protected $fillable = [
        'reservation_id',
        'ticket_id',
        'quantite',
        'prix_unitaire',
        'montant_total'
    ];

    public function reservation()
    {
        return $this->belongsTo(reservation::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
