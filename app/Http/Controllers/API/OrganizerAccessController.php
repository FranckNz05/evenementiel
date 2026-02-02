<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrganizerAccessCode;
use App\Models\Organizer;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller as BaseController;

class OrganizerAccessController extends BaseController
{
    public function verifyCode(Request $request)
{
    // Validation
    $validator = \Validator::make($request->all(), [
        'access_code' => 'required|string',
        'event_id' => 'required|integer|exists:events,id'
    ], [
        'event_id.exists' => 'L\'événement spécifié n\'existe pas.',
        'event_id.required' => 'L\'ID de l\'événement est requis.',
        'access_code.required' => 'Le code d\'accès est requis.'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Données invalides',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $accessCode = OrganizerAccessCode::with(['organizer' => function($query) {
            $query->withoutGlobalScopes(); // Désactive tous les global scopes
        }])
        ->where('access_code', $request->access_code)
        ->where('event_id', $request->event_id)
        ->first();

        if (!$accessCode) {
            return response()->json([
                'success' => false,
                'message' => 'Code d\'accès invalide pour cet événement'
            ], 404);
        }

        // Chargement manuel de l'organisateur si nécessaire
        if (!$accessCode->relationLoaded('organizer')) {
            $accessCode->load('organizer');
        }

        if (!$accessCode->organizer) {
            // Debug: Affiche l'ID de l'organisateur pour vérification
            \Log::error('Organizer missing', [
                'organizer_id' => $accessCode->organizer_id,
                'access_code_id' => $accessCode->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Aucun organisateur associé à ce code',
                'debug' => [
                    'organizer_id' => $accessCode->organizer_id,
                    'access_code' => $accessCode->toArray()
                ]
            ], 404);
        }

        // Vérifications
        $now = Carbon::now();
        $isValid = $accessCode->is_active && 
                  $now->between($accessCode->valid_from, $accessCode->valid_until);

        $event = Event::find($request->event_id);
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Événement introuvable'
            ], 404);
        }

        $eventInProgress = $now->between($event->start_date, $event->end_date);

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Code d\'accès non valide ou période expirée'
            ], 403);
        }

        if (!$eventInProgress) {
            return response()->json([
                'success' => false,
                'message' => 'L\'événement n\'est pas en cours'
            ], 403);
        }

        if ($event->organizer_id !== $accessCode->organizer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à accéder à cet événement'
            ], 403);
        }

        // Réponse réussie
        return response()->json([
            'success' => true,
            'message' => 'Code d\'accès valide',
            'organizer' => [
                'id' => $accessCode->organizer->id,
                'company_name' => $accessCode->organizer->company_name,
                'email' => $accessCode->organizer->email,
                'logo' => $accessCode->organizer->logo,
            ],
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue: ' . $e->getMessage()
        ], 500);
    }
}
}


