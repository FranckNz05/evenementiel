<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    /**
     * Snapshot des valeurs originales pour journaliser les transitions de statut.
     * (Propriété déclarée pour éviter les "dynamic properties" en PHP 8.2+)
     */
    protected array $statusChangeSnapshot = [];

    /**
     * Boot du modèle - Ajoute la validation automatique des statuts
     */
    protected static function boot()
    {
        parent::boot();

        // Valider et synchroniser le statut avant la mise à jour
        static::updating(function ($payment) {
            /** @var self $payment */
            $payment->statusChangeSnapshot = [
                'statut' => $payment->getOriginal('statut'),
                'date_paiement' => $payment->getOriginal('date_paiement'),
                'qr_code' => $payment->getOriginal('qr_code'),
            ];

            $validator = app(\App\Services\PaymentStatusValidator::class);
            $validator->validateAndSync($payment);
        });

        // Logger les changements de statut/date_paiement/qr_code (sécurité + audit)
        static::updated(function ($payment) {
            /** @var self $payment */
            if (!($payment->wasChanged('statut') || $payment->wasChanged('date_paiement') || $payment->wasChanged('qr_code'))) {
                return;
            }

            $from = $payment->statusChangeSnapshot['statut'] ?? null;
            $to = $payment->statut;

            try {
                DB::table('payment_status_changes_log')->insert([
                    'payment_id' => $payment->id,
                    'from_statut' => $from,
                    'to_statut' => $to,
                    'from_date_paiement' => $payment->statusChangeSnapshot['date_paiement'] ?? null,
                    'to_date_paiement' => $payment->date_paiement,
                    'from_qr_code' => $payment->statusChangeSnapshot['qr_code'] ?? null,
                    'to_qr_code' => $payment->qr_code,
                    'source' => data_get(json_decode($payment->details ?? '{}', true) ?: [], 'status_change_source'),
                    'reason' => data_get(json_decode($payment->details ?? '{}', true) ?: [], 'status_change_reason'),
                    'meta' => json_encode([
                        'changes' => $payment->getChanges(),
                    ]),
                    'changed_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Ne jamais bloquer une mise à jour de paiement si l'audit DB échoue
                Log::error('Impossible de journaliser le changement de statut du paiement', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        // Valider le statut avant l'insertion
        static::creating(function ($payment) {
            $validator = app(\App\Services\PaymentStatusValidator::class);
            
            // Valider date_paiement
            if ($payment->date_paiement && $payment->statut !== 'payé') {
                $payment->date_paiement = null;
            }
            
            if ($payment->statut === 'payé' && !$payment->date_paiement) {
                $payment->date_paiement = now();
            }
        });
    }

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
