<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShareController extends Controller
{
    /**
     * Enregistre un partage d'événement
     */
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'platform' => 'required|string|in:whatsapp,facebook,snapchat,telegram,copy_link',
        ]);

        $user = auth()->user();
        $platform = $request->input('platform');
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Enregistrer le partage (on permet plusieurs partages par jour car différentes plateformes)
        Share::create([
            'event_id' => $event->id,
            'user_id' => $user?->id,
            'platform' => $platform,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'shared_at' => now(),
        ]);

        // Retourner le nombre total de partages pour cet événement
        $totalShares = $event->shares()->count();

        return response()->json([
            'success' => true,
            'message' => 'Partage enregistré avec succès',
            'total_shares' => $totalShares,
        ], 200);
    }
}
