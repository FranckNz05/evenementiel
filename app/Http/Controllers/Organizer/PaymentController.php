<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $organizer = $user->organizer;
        
        if (!$organizer) {
            return redirect()->route('organizer.profile.create')
                ->with('error', 'Vous devez d\'abord créer votre profil organisateur.');
        }
        
        // Récupérer les événements de l'organisateur
        $events = Event::where('organizer_id', $organizer->id)
            ->orderBy('title')
            ->get();
        
        // Construire la requête des paiements
        $paymentsQuery = Payment::whereHas('event', function($query) use ($organizer) {
                $query->where('organizer_id', $organizer->id);
            })
            ->with(['user', 'event']);
        
        // Appliquer les filtres
        if ($request->filled('status')) {
            $paymentsQuery->where('statut', $request->status);
        }
        
        if ($request->filled('event_id')) {
            $paymentsQuery->where('evenement_id', $request->event_id);
        }
        
        if ($request->filled('date_from')) {
            $paymentsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $paymentsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Récupérer les paiements avec pagination
        $payments = $paymentsQuery->latest()->paginate(20);
        
        // Calculer les statistiques
        $totalRevenue = Payment::whereHas('event', function($query) use ($organizer) {
                $query->where('organizer_id', $organizer->id);
            })
            ->where('statut', 'payé')
            ->sum('montant');
            
        $successfulPayments = Payment::whereHas('event', function($query) use ($organizer) {
                $query->where('organizer_id', $organizer->id);
            })
            ->where('statut', 'payé')
            ->count();
            
        $pendingPayments = Payment::whereHas('event', function($query) use ($organizer) {
                $query->where('organizer_id', $organizer->id);
            })
            ->where('statut', 'en attente')
            ->count();
            
        $totalPayments = Payment::whereHas('event', function($query) use ($organizer) {
                $query->where('organizer_id', $organizer->id);
            })
            ->count();
            
        $conversionRate = $totalPayments > 0 ? ($successfulPayments / $totalPayments) * 100 : 0;
        
        $stats = [
            'total_revenue' => $totalRevenue,
            'successful_payments' => $successfulPayments,
            'pending_payments' => $pendingPayments,
            'conversion_rate' => $conversionRate
        ];

        return view('dashboard.organizer.payments.index', compact(
            'payments',
            'events',
            'stats'
        ));
    }
    
    public function show($id)
    {
        $user = Auth::user();
        $organizer = $user->organizer;
        
        $payment = Payment::whereHas('event', function($query) use ($organizer) {
                $query->where('organizer_id', $organizer->id);
            })
            ->with(['user', 'event'])
            ->findOrFail($id);
        
        $html = view('dashboard.organizer.payments.partials.payment-details', compact('payment'))->render();
        
        return response()->json(['html' => $html]);
    }
    
    public function refund($id)
    {
        $user = Auth::user();
        $organizer = $user->organizer;
        
        $payment = Payment::whereHas('event', function($query) use ($organizer) {
                $query->where('organizer_id', $organizer->id);
            })
            ->findOrFail($id);
        
        if ($payment->statut !== 'payé') {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les paiements complétés peuvent être remboursés.'
            ]);
        }
        
        // Mettre à jour le statut du paiement
        $payment->update(['statut' => 'remboursé']);
        
        return response()->json([
            'success' => true,
            'message' => 'Paiement remboursé avec succès.'
        ]);
    }
    
    /**
     * Process a payment for an order
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function process(Order $order)
    {
        // Vérifier que l'utilisateur est autorisé à effectuer ce paiement
        if (auth()->id() !== $order->user_id) {
            abort(403, 'Non autorisé.');
        }
        
        // Vérifier si la commande est déjà payée
        if ($order->status === 'paid') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Cette commande a déjà été payée.');
        }
        
        // Vérifier si la commande a expiré
        if ($order->expires_at && $order->expires_at->isPast()) {
            return redirect()->route('cart.show')
                ->with('error', 'Le délai de paiement pour cette commande a expiré. Veuillez réessayer.');
        }
        
        // Rediriger vers le formulaire de paiement PawaPay
        return redirect()->route('pawapay.payment.form', ['order' => $order->id]);
    }
    
    /**
     * Export payments to CSV
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $organizer = $user->organizer;
        
        // Construire la requête des paiements
        $paymentsQuery = Payment::whereHas('event', function($query) use ($organizer) {
                $query->where('organizer_id', $organizer->id);
            })
            ->with(['user', 'event']);
        
        // Appliquer les mêmes filtres que dans index()
        if ($request->filled('status')) {
            $paymentsQuery->where('statut', $request->status);
        }
        
        if ($request->filled('event_id')) {
            $paymentsQuery->where('evenement_id', $request->event_id);
        }
        
        if ($request->filled('date_from')) {
            $paymentsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $paymentsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        $payments = $paymentsQuery->latest()->get();
        
        // Générer le CSV
        $filename = 'paiements_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Transaction ID',
                'Client',
                'Email',
                'Événement',
                'Billet',
                'Montant (FCFA)',
                'Statut',
                'Méthode de paiement',
                'Date de création'
            ]);
            
            // Données
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->reference_transaction ?? $payment->id,
                    $payment->user->name ?? 'N/A',
                    $payment->user->email ?? 'N/A',
                    $payment->event->title ?? 'N/A',
                    'Billet standard',
                    $payment->montant ?? 0,
                    $payment->statut ?? 'N/A',
                    $payment->methode_paiement ?? 'N/A',
                    $payment->created_at->format('d/m/Y H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
