<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPersonalEventGuest extends Model
{
    use HasFactory;

    protected $table = 'custom_event_guests';

    protected $fillable = [
        'custom_event_id',
        'full_name',
        'email',
        'phone',
        'is_couple',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(CustomPersonalEvent::class, 'custom_event_id');
    }
}
