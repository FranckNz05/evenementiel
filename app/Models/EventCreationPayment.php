<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventCreationPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'payment_details',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Constantes pour les types d'événements
    const EVENT_TYPE_FREE = 'gratuit';
    const EVENT_TYPE_CUSTOM = 'personnalisé';

    // Constantes pour les statuts
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';

    // Constantes pour les méthodes de paiement
    const PAYMENT_METHOD_MTN = 'MTN Mobile Money';
    const PAYMENT_METHOD_AIRTEL = 'Airtel Money';
}
