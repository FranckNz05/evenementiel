<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon;

class ArchivePastEvents extends Command
{
    protected $signature = 'events:archive';
    protected $description = 'Archive les événements passés';

    public function handle()
    {
        // Archiver les événements dont la date de fin est passée (pas seulement la date, mais aussi l'heure)
        $count = Event::where('end_date', '<', now())
            ->where('etat', '!=', 'Archivé')
            ->update(['etat' => 'Archivé']);

        if ($count > 0) {
            $this->info("$count événement(s) archivé(s) avec succès.");
            \Illuminate\Support\Facades\Log::info("Archivage automatique: {$count} événement(s) archivé(s).");
        } else {
            $this->info('Aucun événement à archiver.');
        }

        return Command::SUCCESS;
    }
}
