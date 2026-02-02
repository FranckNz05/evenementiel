<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPersonalEvent extends Model
{
    use HasFactory;

    protected $table = 'custom_events';

    protected $fillable = [
        'organizer_id',
        'category',
        'title',
        'start_date',
        'end_date',
        'location',
        'invitation_message',
        'url',
        'is_public',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function guests()
    {
        return $this->hasMany(CustomPersonalEventGuest::class, 'custom_event_id');
    }
}
