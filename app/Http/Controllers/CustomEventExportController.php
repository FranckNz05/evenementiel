<?php

namespace App\Http\Controllers;

use App\Models\CustomEvent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomEventExportController extends Controller
{
    public function exportGuests(Request $request, CustomEvent $event): StreamedResponse
    {
        $this->authorize('view', $event);

        if (!$event->hasExports()) {
            abort(403, 'Votre formule ne permet pas l’export des invités.');
        }

        $filename = 'invites_' . $event->id . '.csv';

        return response()->streamDownload(function () use ($event) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Nom', 'Email', 'Téléphone', 'Statut']);
            foreach ($event->guests as $guest) {
                fputcsv($output, [
                    $guest->name,
                    $guest->email,
                    $guest->phone,
                    $guest->status,
                ]);
            }
            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}


