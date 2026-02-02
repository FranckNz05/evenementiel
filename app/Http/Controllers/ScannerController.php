<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Payment;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class ScannerController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        try {
            // Décrypter les données du QR code
            $decrypted = Crypt::decryptString($request->qr_data);
            $qrData = json_decode($decrypted, true);

            // Vérifier les données
            if (!isset($qrData['payment_id'], $qrData['matricule'])) {
                return response()->json(['error' => 'Données QR code invalides'], 400);
            }

            // Récupérer le paiement
            $payment = Payment::with(['event', 'user', 'order.tickets'])
                ->findOrFail($qrData['payment_id']);

            // Vérifier que le matricule correspond
            if ($payment->matricule !== $qrData['matricule']) {
                return response()->json(['error' => 'Billet invalide'], 403);
            }

            // Vérifier si le billet a déjà été utilisé
            if ($payment->is_used) {
                return response()->json([
                    'error' => 'Billet déjà utilisé',
                    'last_scanned_at' => $payment->last_scanned_at,
                    'scanned_count' => $payment->scanned_count
                ], 409);
            }

            // Marquer le billet comme utilisé
            $payment->update([
                'is_used' => true,
                'scanned_count' => $payment->scanned_count + 1,
                'last_scanned_at' => now()
            ]);

            // Incrémenter le compteur de scans pour le type de billet
            foreach ($payment->order->tickets as $ticket) {
                $ticket->increment('total_scanned');
            }

            return response()->json([
                'success' => true,
                'event' => $payment->event->only(['title', 'lieu', 'start_date']),
                'ticket' => $payment->order->tickets->first()->only(['nom', 'prix']),
                'user' => $payment->user->only(['name', 'email']),
                'scan_time' => now()->toDateTimeString()
            ]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Erreur de décryptage QR code', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'QR code invalide'], 400);
        } catch (\Exception $e) {
            Log::error('Erreur de scan', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors du traitement du billet'], 500);
        }
    }
}