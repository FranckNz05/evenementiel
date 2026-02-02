<?php

namespace App\Http\Controllers;

use App\Models\CustomEvent;
use App\Models\CustomEventGuest;
use App\Services\InvitationQrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CheckInController extends Controller
{
    protected InvitationQrCodeService $qrCodeService;

    public function __construct(InvitationQrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Affiche la page de gestion temps réel des check-ins
     */
    public function realtime(string $checkinUrl)
    {
        $event = CustomEvent::where('checkin_url', $checkinUrl)->firstOrFail();

        // Vérifier que l'événement peut utiliser l'URL temps réel
        if (!$event->canUseRealtimeCheckin()) {
            abort(403, 'Cette fonctionnalité n\'est pas disponible pour votre formule.');
        }

        // Charger les invités avec leurs relations
        $guests = $event->guests()->orderBy('full_name')->get();

        return view('checkin.realtime', compact('event', 'guests'));
    }

    /**
     * API pour récupérer la liste des invités (pour mise à jour temps réel)
     */
    public function getGuests(string $checkinUrl): JsonResponse
    {
        $event = CustomEvent::where('checkin_url', $checkinUrl)->firstOrFail();

        if (!$event->canUseRealtimeCheckin()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $guests = $event->guests()->orderBy('full_name')->get()->map(function ($guest) {
            return [
                'id' => $guest->id,
                'full_name' => $guest->full_name,
                'email' => $guest->email,
                'phone' => $guest->phone,
                'status' => $guest->status,
                'checked_in_at' => $guest->checked_in_at ? $guest->checked_in_at->format('d/m/Y à H:i') : null,
                'checked_in_via' => $guest->checked_in_via,
                'is_checked_in' => $guest->checked_in_at !== null,
            ];
        });

        $stats = [
            'total' => $guests->count(),
            'checked_in' => $guests->where('is_checked_in', true)->count(),
            'pending' => $guests->where('status', 'pending')->where('is_checked_in', false)->count(),
        ];

        return response()->json([
            'guests' => $guests,
            'stats' => $stats,
        ]);
    }

    /**
     * Recherche un invité
     */
    public function searchGuest(string $checkinUrl, Request $request): JsonResponse
    {
        $event = CustomEvent::where('checkin_url', $checkinUrl)->firstOrFail();

        if (!$event->canUseRealtimeCheckin()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $query = $request->input('q', '');
        
        if (empty($query)) {
            return response()->json(['guests' => []]);
        }

        $guests = $event->guests()
            ->where(function ($q) use ($query) {
                $q->where('full_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->orderBy('full_name')
            ->get()
            ->map(function ($guest) {
                return [
                    'id' => $guest->id,
                    'full_name' => $guest->full_name,
                    'email' => $guest->email,
                    'phone' => $guest->phone,
                    'status' => $guest->status,
                    'checked_in_at' => $guest->checked_in_at ? $guest->checked_in_at->format('d/m/Y à H:i') : null,
                    'checked_in_via' => $guest->checked_in_via,
                    'is_checked_in' => $guest->checked_in_at !== null,
                ];
            });

        return response()->json(['guests' => $guests]);
    }

    /**
     * Marque un invité comme entré manuellement
     */
    public function manualCheckIn(string $checkinUrl, Request $request): JsonResponse
    {
        $event = CustomEvent::where('checkin_url', $checkinUrl)->firstOrFail();

        if (!$event->canUseRealtimeCheckin()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'guest_id' => 'required|exists:custom_event_guests,id',
        ]);

        $guest = CustomEventGuest::findOrFail($request->guest_id);

        // Vérifier que l'invité appartient à l'événement
        if ($guest->custom_event_id != $event->id) {
            return response()->json(['error' => 'Cet invité n\'appartient pas à cet événement'], 403);
        }

        // Vérifier si déjà entré
        if ($guest->checked_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Invité déjà marqué comme entré le ' . $guest->checked_in_at->format('d/m/Y à H:i'),
                'guest' => [
                    'id' => $guest->id,
                    'full_name' => $guest->full_name,
                    'email' => $guest->email,
                    'phone' => $guest->phone,
                    'checked_in_at' => $guest->checked_in_at->format('d/m/Y à H:i'),
                    'checked_in_via' => $guest->checked_in_via,
                    'is_checked_in' => true,
                ],
            ]);
        }

        // Marquer comme entré
        $guest->update([
            'checked_in_at' => now(),
            'checked_in_via' => 'manual',
            'status' => 'arrived',
        ]);

        $guest = $guest->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Invité marqué comme entré avec succès',
            'guest' => [
                'id' => $guest->id,
                'full_name' => $guest->full_name,
                'email' => $guest->email,
                'phone' => $guest->phone,
                'checked_in_at' => $guest->checked_in_at->format('d/m/Y à H:i'),
                'checked_in_via' => $guest->checked_in_via,
                'is_checked_in' => true,
            ],
        ]);
    }

    /**
     * Scan un QR code et marque l'invité comme entré
     */
    public function scanQrCode(string $checkinUrl, Request $request): JsonResponse
    {
        $event = CustomEvent::where('checkin_url', $checkinUrl)->firstOrFail();

        if (!$event->canUseRealtimeCheckin()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $encryptedCode = $request->input('qr_code');

        // Valider et marquer comme entré
        $result = $this->qrCodeService->validateAndCheckIn($encryptedCode, $event->id);

        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'fraudulent' => $result['fraudulent'] ?? false,
                'message' => $result['message'],
                'guest' => $result['guest'] ? [
                    'id' => $result['guest']->id,
                    'full_name' => $result['guest']->full_name,
                ] : null,
            ], $result['fraudulent'] ? 400 : 422);
        }

        $guestData = null;
        if ($result['guest']) {
            $guest = $result['guest'];
            $guestData = [
                'id' => $guest->id,
                'full_name' => $guest->full_name,
                'email' => $guest->email,
                'phone' => $guest->phone,
                'checked_in_at' => $guest->checked_in_at ? $guest->checked_in_at->format('d/m/Y à H:i') : null,
                'checked_in_via' => $guest->checked_in_via,
            ];
        }

        return response()->json([
            'success' => true,
            'valid' => true,
            'fraudulent' => false,
            'message' => $result['message'],
            'already_checked_in' => $result['already_checked_in'] ?? false,
            'guest' => $guestData,
        ]);
    }
}
