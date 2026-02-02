<?php

namespace App\Http\Controllers;

use App\Models\CustomEvent;
use App\Models\CustomEventGuest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEventInvitation;

class CustomEventGuestController extends Controller
{
    /**
     * Ajouter un invité à un événement personnalisé
     */
    public function store(Request $request, CustomEvent $event)
    {
        $this->authorize('update', $event);

        // Vérifier si l'ajout d'invités est autorisé selon la formule
        if (!$event->canAddGuestsAfterCreation()) {
            return back()->with('error', "Votre formule ne permet pas l'ajout d'invités après la création de l'événement.");
        }

        // Vérifier la limite d'invités
        $currentGuestCount = $event->guests()->count();
        $maxGuests = $event->maxGuests();

        if ($currentGuestCount >= $maxGuests) {
            return back()->with('error', "Limite d'invités atteinte pour votre formule (max {$maxGuests}).");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'send_invitation' => 'boolean',
        ]);

        // Convertir 'name' en 'full_name' pour le modèle
        $guestData = [
            'full_name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'status' => 'pending',
            'is_couple' => false,
        ];

        $guest = $event->guests()->create($guestData);

        // Envoyer l'invitation si demandé
        if ($request->has('send_invitation') && $request->send_invitation && $guest->email) {
            try {
                Mail::to($guest->email)->send(new CustomEventInvitation($event, [
                    'full_name' => $guest->full_name,
                    'email' => $guest->email,
                ]));
            } catch (\Exception $e) {
                \Log::error('Erreur envoi mail invitation: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Invité ajouté avec succès!');
    }

    /**
     * Mettre à jour un invité
     */
    public function update(Request $request, $eventId, $guestId)
    {
        $event = CustomEvent::findOrFail($eventId);
        $this->authorize('update', $event);

        $guest = CustomEventGuest::findOrFail($guestId);
        
        // Vérifier que l'invité appartient à l'événement
        if ($guest->custom_event_id != $event->id) {
            abort(403, 'Cet invité n\'appartient pas à cet événement.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:pending,confirmed,cancelled,attended',
            'notes' => 'nullable|string',
        ]);

        // Convertir 'name' en 'full_name'
        $guestData = [
            'full_name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ];

        $guest->update($guestData);

        return back()->with('success', 'Invité mis à jour avec succès!');
    }

    /**
     * Supprimer un invité
     */
    public function destroy($eventId, $guestId)
    {
        $event = CustomEvent::findOrFail($eventId);
        $this->authorize('update', $event);

        $guest = CustomEventGuest::findOrFail($guestId);
        
        // Vérifier que l'invité appartient à l'événement
        if ($guest->custom_event_id != $event->id) {
            abort(403, 'Cet invité n\'appartient pas à cet événement.');
        }

        $guest->delete();

        return back()->with('success', 'Invité supprimé avec succès!');
    }

    /**
     * Importer des invités depuis un fichier CSV
     */
    public function import(Request $request, CustomEvent $event)
    {
        $this->authorize('update', $event);

        // Vérifier si l'ajout d'invités est autorisé
        if (!$event->canAddGuestsAfterCreation()) {
            return back()->with('error', "Votre formule ne permet pas l'ajout d'invités après la création de l'événement.");
        }

        $validated = $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048',
        ]);

        // Lire et traiter le fichier CSV
        $file = $request->file('import_file');
        $hasHeaders = $request->has('has_headers');
        
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        if ($hasHeaders) {
            $header = array_shift($csvData); // Première ligne = en-têtes
        }

        $imported = 0;
        $errors = [];

        foreach ($csvData as $row) {
            if (count($row) < 1) {
                continue;
            }

            // Vérifier la limite d'invités
            $currentGuestCount = $event->guests()->count();
            $maxGuests = $event->maxGuests();

            if ($currentGuestCount >= $maxGuests) {
                $errors[] = "Limite d'invités atteinte. {$imported} invité(s) importé(s).";
                break;
            }

            // Extraire les données (supposer que la première colonne est le nom)
            $fullName = $row[0] ?? '';
            $email = $row[1] ?? null;
            $phone = $row[2] ?? null;

            if (empty($fullName)) {
                continue;
            }

            try {
                // Nettoyer et valider l'email
                $cleanEmail = !empty($email) ? trim($email) : null;
                if ($cleanEmail && !filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Erreur lors de l'importation de {$fullName}: Email invalide ({$cleanEmail})";
                    continue;
                }

                // Vérifier si l'email est vide mais requis dans la base de données
                // Si l'email est requis, on utilise une valeur par défaut ou on skip
                $event->guests()->create([
                    'full_name' => trim($fullName),
                    'email' => $cleanEmail,
                    'phone' => !empty($phone) ? trim($phone) : null,
                    'status' => 'pending',
                    'is_couple' => false,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Erreur lors de l'importation de {$fullName}" . ($email ? " ({$email})" : '') . ": " . $e->getMessage();
            }
        }

        if ($imported > 0) {
            $message = "{$imported} invité(s) importé(s) avec succès!";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " erreur(s) rencontrée(s).";
            }
            return back()->with('success', $message)->with('import_errors', $errors);
        }

        return back()->with('error', 'Aucun invité n\'a pu être importé.')->with('import_errors', $errors);
    }
}

