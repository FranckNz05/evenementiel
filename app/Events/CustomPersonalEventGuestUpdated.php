<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\CustomPersonalEvent;

class CustomPersonalEventGuestUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $eventId;
    public $guests;

    public function __construct(CustomPersonalEvent $event)
    {
        $this->eventId = $event->id;
        $this->guests = $event->guests()->get()->toArray();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('custom-personal-event.' . $this->eventId);
    }
}
