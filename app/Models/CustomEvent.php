<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomEvent extends Model
{
    protected $fillable = [
        'organizer_id', 'title', 'type', 'category', 'start_date', 'end_date',
        'location', 'description', 'image', 'guest_limit', 'invitation_link',
        'url', 'offer_plan', 'checkin_url'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'guest_limit' => 'integer',
    ];

    public function getOfferCapabilitiesAttribute(): array
    {
        $plan = $this->offer_plan ?: 'start';
        $matrix = [
            'start' => [
                'can_add_guests_after_creation' => false,
                'can_schedule_sms' => false,
                'has_realtime_url' => false,
                'has_stats' => false,
                'has_exports' => false,
                'support' => ['email'],
                'max_guests' => 100,
            ],
            'standard' => [
                'can_add_guests_after_creation' => true,
                'can_schedule_sms' => true,
                'has_realtime_url' => true,
                'has_stats' => true,
                'has_exports' => false,
                'support' => ['email','whatsapp'],
                'max_guests' => 300,
            ],
            'premium' => [
                'can_add_guests_after_creation' => true,
                'can_schedule_sms' => true,
                'has_realtime_url' => true,
                'has_stats' => true,
                'has_exports' => true,
                'support' => ['email','whatsapp','phone'],
                'max_guests' => 800,
            ],
            'ultimate' => [
                'can_add_guests_after_creation' => true,
                'can_schedule_sms' => true,
                'has_realtime_url' => true,
                'has_stats' => true,
                'has_exports' => true,
                'support' => ['email','whatsapp','phone'],
                'max_guests' => 1500,
            ],
        ];
        return $matrix[$plan] ?? $matrix['start'];
    }

    public function canAddGuestsAfterCreation(): bool
    {
        return (bool) ($this->offer_capabilities['can_add_guests_after_creation'] ?? false);
    }

    public function canScheduleSms(): bool
    {
        return (bool) ($this->offer_capabilities['can_schedule_sms'] ?? false);
    }

    public function hasRealtimeUrl(): bool
    {
        return (bool) ($this->offer_capabilities['has_realtime_url'] ?? false);
    }

    public function hasStats(): bool
    {
        return (bool) ($this->offer_capabilities['has_stats'] ?? false);
    }

    public function hasExports(): bool
    {
        return (bool) ($this->offer_capabilities['has_exports'] ?? false);
    }

    public function maxGuests(): int
    {
        return (int) ($this->offer_capabilities['max_guests'] ?? 100);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(CustomEventGuest::class, 'custom_event_id');
    }

    public function generateInvitationLink(): string
    {
        if (!$this->invitation_link) {
            $this->update([
                'invitation_link' => \Str::random(32)
            ]);
        }
        
        // Retourner l'URL de l'invitation (route publique)
        return route('custom-events.invitation', $this->invitation_link);
    }

    /**
     * Génère une URL unique pour la gestion temps réel des check-ins
     */
    public function generateCheckinUrl(): string
    {
        if (!$this->checkin_url) {
            $this->update([
                'checkin_url' => \Str::random(32)
            ]);
            $this->refresh();
        }
        
        return route('checkin.realtime', $this->checkin_url);
    }

    /**
     * Vérifie si l'événement peut utiliser l'URL temps réel
     */
    public function canUseRealtimeCheckin(): bool
    {
        return $this->hasRealtimeUrl();
    }
}