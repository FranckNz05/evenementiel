<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    protected $fillable = [
        'guest_id', 'token', 'sent_at', 'responded_at',
        'response_status', 'response_note'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function markAsSent(): void
    {
        $this->update(['sent_at' => now()]);
    }

    public function markAsResponded(string $status, ?string $note = null): void
    {
        $this->update([
            'responded_at' => now(),
            'response_status' => $status,
            'response_note' => $note,
        ]);

        $this->guest->update(['status' => $status]);
    }
}