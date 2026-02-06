<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'user_id',
        'ticket_id',
        'evenement_id',
        'quantity',
        'statut',
        'reservation_id',
        'reference',
        'payment_status',
        'paid_at'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'paid_at' => 'datetime'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'orders_tickets', 'order_id', 'ticket_id')
            ->withPivot(['quantity', 'unit_price', 'total_amount', 'used_quantity', 'is_fully_used'])
            ->withTimestamps()
            ->using(OrderTicket::class);
    }

    public function evenement()
    {
        return $this->belongsTo(Event::class, 'evenement_id');
    }

    /**
     * Alias pour evenement() pour cohérence avec le code existant
     */
    public function event()
    {
        return $this->evenement();
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function paiement()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Alias pour paiement() pour compatibilité avec AdminDashboardController
     */
    public function payments()
    {
        return $this->paiement();
    }

    // Helpers
    public function isPending()
    {
        return $this->statut === 'en_attente';
    }

    public function isPaid()
    {
        return $this->statut === 'payé';
    }

    public function isCancelled()
    {
        return $this->statut === 'annulé';
    }

    /**
     * Calcule le montant total de la commande à partir des tickets associés
     * 
     * @return float
     */
    public function getMontantTotalAttribute()
    {
        // Calculer le montant total depuis la table pivot orders_tickets
        return \DB::table('orders_tickets')
            ->where('order_id', $this->id)
            ->sum('total_amount') ?? 0;
    }
}
