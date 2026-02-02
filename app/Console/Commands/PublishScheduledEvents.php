<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Carbon\Carbon;

class PublishScheduledEvents extends Command
{
    protected $signature = 'events:publish-scheduled';
    protected $description = 'Publie les événements programmés dont la date de publication est arrivée';

    public function handle()
    {
        $events = Event::where('is_approved', true)
            ->where('is_published', false)
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', Carbon::now())
            ->get();

        foreach ($events as $event) {
            $event->update([
                'is_published' => true,
                'published_at' => Carbon::now(),
            ]);

            $this->info("Événement '{$event->title}' publié avec succès.");
        }
    }
}
