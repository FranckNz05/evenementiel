<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderTicket extends Pivot
{
    use HasFactory;

    // Définir le nom correct de la table
    protected $table = 'orders_tickets';

    // Indiquer que la table a des timestamps
    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'ticket_id',
        'quantity',
        'unit_price',
        'total_amount',
        'used_quantity',
        'is_fully_used'
    ];

    // Convertir les types de données
    protected $casts = [
        'is_fully_used' => 'boolean',
        'quantity' => 'integer',
        'used_quantity' => 'integer'
    ];

    /**
     * Relation avec l'ordre
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relation avec le ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Relation avec les paiements
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_ticket_id');
    }

    public function payment()
{
    return $this->belongsTo(Payment::class);
}

    /**
     * Vérifie si tous les billets ont été utilisés
     */
    public function checkIfFullyUsed()
    {
        $this->is_fully_used = ($this->used_quantity >= $this->quantity);
        $this->save();
        return $this->is_fully_used;
    }

    /**
     * Incrémente le nombre de billets utilisés
     */
    public function incrementUsedQuantity($count = 1)
    {
        $this->used_quantity += $count;
        if ($this->used_quantity >= $this->quantity) {
            $this->is_fully_used = true;
        }
        $this->save();
    }
}

