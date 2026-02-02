<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    // Constantes de statut
    const STATUS_PENDING = 'en attente';
    const STATUS_PAID = 'payé';
    const STATUS_FAILED = 'échoué';
    const STATUS_CANCELLED = 'annulé';

    protected $table = 'paiements';

    protected $fillable = [
        'matricule',
        'user_id',
        'order_id',
        'evenement_id',
        'reservation_id',
        'order_ticket_id',
        'montant',
        'methode_paiement',
        'statut',
        'qr_code',
        'reference_transaction',
        'reference_paiement',
        'numero_telephone',
        'date_paiement',
        'details'
    ];

    protected $casts = [
        'montant' => 'decimal:2'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsToThrough(Ticket::class, Order::class);
    }

    /**
     * Relation directe vers l'événement via evenement_id
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'evenement_id');
    }

    /**
     * Relation vers l'événement via Order (pour compatibilité)
     */
    public function eventThroughOrder()
    {
        return $this->belongsToThrough(Event::class, Order::class, 'evenement_id');
    }

    public function isPending()
    {
        return $this->statut === self::STATUS_PENDING;
    }

    public function isPaid()
    {
        return $this->statut === self::STATUS_PAID;
    }

    public function isFailed()
    {
        return $this->statut === self::STATUS_FAILED;
    }

    public function isCancelled()
    {
        return $this->statut === self::STATUS_CANCELLED;
    }
}
