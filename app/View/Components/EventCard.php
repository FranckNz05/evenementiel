<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Event;

class EventCard extends Component
{
    public Event $event;
    public ?string $linkRoute;

    /**
     * Create a new component instance.
     */
    public function __construct(Event $event, ?string $linkRoute = null)
    {
        $this->event = $event;
        $this->linkRoute = $linkRoute ?? 'direct-events.show';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.event-card');
    }
}
