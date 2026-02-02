<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomEventGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_event_id',
        'full_name',
        'email',
        'phone',
        'is_couple',
        'status',
        'invitation_code',
        'scheduled_at',
        'sent_at',
        'sent_via',
        'invitation_message',
        'checked_in_at',
        'checked_in_via',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(CustomEvent::class, 'custom_event_id');
    }
}
