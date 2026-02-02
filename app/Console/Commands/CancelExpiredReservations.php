<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReservationController;

class CancelExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Annule automatiquement les réservations expirées et non payées';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des réservations expirées...');
        
        $controller = new ReservationController();
        $controller->checkExpiredReservations();
        
        $this->info('Vérification terminée.');
        
        return 0;
    }
}

