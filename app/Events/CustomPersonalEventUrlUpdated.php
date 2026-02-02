<?php
namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\CustomPersonalEvent;

class CustomPersonalEventUrlUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $eventId;
    public $url;

    public function __construct(CustomPersonalEvent $event)
    {
        $this->eventId = $event->id;
        $this->url = $event->url;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('custom-personal-event.' . $this->eventId);
    }
}
