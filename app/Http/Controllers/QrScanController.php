<?php

namespace App\Http\Controllers;

use App\Models\QrScan;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\OrderTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QrScanController extends Controller
{
    /**
     * Affiche la page de scan de QR code
     */
    public function __construct()
{
    $this->middleware(['auth', 'verified', 'role:Organizer']);
}

public function index()
{
    $scans = QrScan::whereHas('ticket.event', function($query) {
            $query->where('organizer_id', auth()->id());
        })
        ->with(['ticket.event', 'order.user'])
        ->latest('scanned_at')
        ->paginate(4);

    return view('dashboard.organizer.scans.index', compact('scans'));
}

/**
 * Vérifie un QR code scanné
 */
public function verify(Request $request)
{
    $request->validate([
        'qr_code' => 'required|string',
    ]);

    $qrCode = $request->input('qr_code');
    
    // Trouver le paiement correspondant au QR code
    $payment = Payment::where('qr_code', $qrCode)
        ->with(['order.tickets.event', 'order.user'])
        ->first();

    if (!$payment) {
        return response()->json([
            'valid' => false,
            'message' => 'Code QR invalide ou expiré'
        ], 404);
    }

    // Vérifier que l'événement appartient à l'organisateur
    if ($payment->order->tickets->first()->event->organizer_id != auth()->id()) {
        return response()->json([
            'valid' => false,
            'message' => 'Vous n\'êtes pas autorisé à scanner ce billet'
        ], 403);
    }

    // Vérifier si le billet a déjà été utilisé
    if ($payment->is_used) {
        return response()->json([
            'valid' => false,
            'message' => 'Ce billet a déjà été utilisé',
            'previous_scan' => $payment->last_scanned_at?->format('d/m/Y H:i:s')
        ], 400);
    }

    // Enregistrer le scan
    $scan = QrScan::create([
        'payment_id' => $payment->id,
        'ticket_id' => $payment->order->tickets->first()->id,
        'order_id' => $payment->order->id,
        'scanned_by' => auth()->id(),
        'is_valid' => true,
        'device_info' => $request->userAgent()
    ]);

    // Mettre à jour le paiement
    $payment->update([
        'is_used' => true,
        'scanned_count' => DB::raw('scanned_count + 1'),
        'last_scanned_at' => now()
    ]);

    return response()->json([
        'valid' => true,
        'message' => 'Billet valide',
        'ticket' => [
            'event' => $payment->order->tickets->first()->event->title,
            'ticket' => $payment->order->tickets->first()->nom,
            'user' => $payment->order->user->name,
            'scanned_at' => now()->format('d/m/Y H:i:s')
        ]
    ]);
}
    
    /**
     * Affiche l'historique complet des scans
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $events = Event::where('organizer_id', $user->id)->get();
        
        $query = QrScan::whereHas('ticket.event', function($query) use ($user) {
                $query->where('organizer_id', $user->id);
            })
            ->with(['ticket.event', 'order.user', 'scannedBy']);
            
        // Filtres
        if ($request->filled('event_id')) {
            $query->whereHas('ticket.event', function($query) use ($request) {
                $query->where('id', $request->event_id);
            });
        }
        if ($request->filled('user_id')) {
            $query->whereHas('order.user', function($query) use ($request) {
                $query->where('id', $request->user_id);
            });
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('scanned_at', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('is_valid')) {
            $query->where('is_valid', $request->is_valid);
        }
        
        $scans = $query->latest('scanned_at')->paginate(50);

        return view('dashboard.organizer.scans.history', compact('scans', 'events'));
    }
}
