<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\QrScan;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\OrderTicket;
use App\Models\OrganizerAccessCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller as BaseController;

class QrScanController extends BaseController
{
    /**
     * Affiche la page de scan
     */
    public function index()
    {
        return view('scanner.index');
    }

    public function verifyOrganizerCode(Request $request)
{
    $request->validate([
        'access_code' => 'required|string',
        'event_id' => 'required|exists:events,id'
    ]);

    $code = OrganizerAccessCode::with(['organizer', 'event'])
        ->where('code', $request->access_code)
        ->where('event_id', $request->event_id)
        ->first();

    if (!$code) {
        return response()->json([
            'success' => false,
            'message' => 'Code d\'accès invalide'
        ], 401);
    }

    return response()->json([
        'success' => true,
        'message' => 'Accès autorisé',
        'organizer' => [
            'id' => $code->organizer->id,
            'company_name' => $code->organizer->company_name,
            'email' => $code->organizer->email,
            'phone_primary' => $code->organizer->phone_primary,
            'logo' => $code->organizer->logo_path ? url($code->organizer->logo_path) : null,
        ],
        'event' => [
            'id' => $code->event->id,
            'title' => $code->event->title,
            'start_date' => $code->event->start_date,
            'end_date' => $code->event->end_date,
        ]
    ]);
}

    /**
     * Vérifie un QR code
     */
    public function verifyQr(Request $request)
{
    $request->validate([
        'qr_data' => 'required|string',
        'event_id' => 'required|exists:events,id',
        'access_code' => 'required|string'
    ]);

    DB::beginTransaction();
    try {
        // Déchiffrer les données du QR code
        $qrCodeService = app(\App\Services\QrCodeEncryptionService::class);
        $qrData = $qrCodeService->decryptQrData($request->qr_data);

        if (!$qrData || !isset($qrData['payment_id'])) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Format QR code invalide ou corrompu'
            ], 400);
        }

        // Récupération du paiement avec ses relations
        $payment = Payment::with([
                'order.event.organizer', 
                'order.user',
                'orderTicket.ticket' // Relation corrigée
            ])
            ->where('id', $qrData['payment_id'])
            ->first();

        if (!$payment) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Aucun billet trouvé avec ce code'
            ], 404);
        }

        // Vérifier l'intégrité des données (après avoir trouvé le paiement)
        if (!$qrCodeService->validateQrData($qrData, $payment)) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'QR code invalide ou falsifié'
            ], 403);
        }

        // Vérification statut paiement
        if ($payment->statut !== 'payé') {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Billet non payé - Accès refusé'
            ], 403);
        }

        // Vérification événement
        if ($payment->evenement_id != $request->event_id) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ce billet ne correspond pas à cet événement'
            ], 403);
        }

        // Récupérer l'orderTicket associé via order_ticket_id
        $orderTicket = $payment->orderTicket;
        if (!$orderTicket) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Aucun billet associé à ce paiement'
            ], 404);
        }

        // Comptage des scans
        $scanCount = QrScan::where('payment_id', $payment->id)->count();
        $maxScansAllowed = $orderTicket->quantity ?? 1;

        if ($scanCount >= $maxScansAllowed) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Ce billet a déjà été scanné $scanCount fois (maximum $maxScansAllowed autorisés)",
                'scan_count' => $scanCount
            ], 403);
        }

        // Enregistrement du scan
        $userAgent = $request->header('User-Agent') ?? 'App Mobile';
        $scannedBy = auth()->id() ?? $request->input('organizer_id') ?? null;

        $scan = QrScan::create([
            'payment_id' => $payment->id,
            'ticket_id' => $orderTicket->ticket_id,
            'order_id' => $payment->order_id,
            'scanned_by' => $scannedBy,
            'device_info' => $userAgent,
            'is_valid' => true
        ]);

        // Mise à jour des compteurs
        $payment->update([
            'scanned_count' => $scanCount + 1,
            'last_scanned_at' => now(),
            'is_used' => ($scanCount + 1) >= $maxScansAllowed ? 1 : 0
        ]);

        // Mise à jour du OrderTicket
        $orderTicket->update([
            'used_quantity' => $orderTicket->used_quantity + 1,
            'is_fully_used' => ($orderTicket->used_quantity + 1) >= $orderTicket->quantity ? 1 : 0,
            'updated_at' => now() // Mise à jour du champ update_at
        ]);

        // Mise à jour du Ticket
        if ($orderTicket->ticket) {
            $orderTicket->ticket->update([
                'total_scanned' => $orderTicket->ticket->total_scanned + 1
                // Suppression du calcul usage_rate si le champ n'existe pas
            ]);
        }

        DB::commit();

        // Réponse réussie
        return response()->json([
            'success' => true,
            'message' => $scanCount == 0
                ? 'Billet valide - Premier scan'
                : "Billet valide - Scan #" . ($scanCount + 1),
            'scan_count' => $scanCount + 1,
            'first_scan' => $scanCount == 0,
            'ticket_info' => [
                'event' => $payment->order->event->title ?? 'Événement inconnu',
                'date' => $payment->order->event->start_date ?? 'Date inconnue',
                'user' => $payment->order->user->name ?? 'Utilisateur inconnu',
                'ticket_type' => $orderTicket->ticket->nom ?? 'Type inconnu',
                'quantity' => $orderTicket->quantity ?? 1,
                'reference' => $payment->matricule ?? '',
                'scan_count' => $scanCount + 1
            ],
            'organizer' => [
                'company_name' => $payment->order->event->organizer->company_name ?? '',
                'email' => $payment->order->event->organizer->email ?? '',
                'phone' => $payment->order->event->organizer->phone_primary ?? '',
                'logo' => $payment->order->event->organizer->logo ? Storage::url($payment->order->event->organizer->logo) : ''
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la validation: ' . $e->getMessage()
        ], 500);
    }
}
}
