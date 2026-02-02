<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrScan extends Model
{
    use HasFactory;

    protected $table = 'qr_scans';

    protected $fillable = [
        'payment_id',
        'ticket_id',
        'order_id',
        'scanned_at',
        'scanned_by',
        'is_valid',
        'device_info'
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'is_valid' => 'boolean'
    ];

    /**
     * Relation avec le paiement
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Relation avec le ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Relation avec la commande
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relation avec l'utilisateur qui a scanné
     */
    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}