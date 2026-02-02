<?php

namespace App\Http\Controllers;

use App\Models\EventCreationPayment;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventCreationPaymentController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Affiche la page de paiement pour la création d'événement
     */
    public function showPaymentForm(Request $request)
    {
        $eventType = $request->query('type', 'gratuit');
        
        // Vérifier si le paiement est requis
        if ($this->commissionService->canCreateEventWithoutPayment($eventType)) {
            return redirect()->route('events.create')
                ->with('info', 'Vous pouvez créer cet événement sans paiement.');
        }
        
        $fee = $this->commissionService->getEventCreationFee($eventType);
        
        return view('events.payment-creation', compact('eventType', 'fee'));
    }

    /**
     * Traite le paiement de création d'événement
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'event_type' => 'required|in:gratuit,personnalisé',
            'payment_method' => 'required|in:MTN Mobile Money,Airtel Money',
            'phone_number' => 'required|string|max:20'
        ]);

        $eventType = $request->event_type;
        $paymentMethod = $request->payment_method;
        $phoneNumber = $request->phone_number;
        
        // Bloquer MTN Mobile Money
        if ($paymentMethod === 'MTN Mobile Money') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'MTN Mobile Money n\'est pas encore disponible. Veuillez utiliser Airtel Money.');
        }
        
        // Vérifier si le paiement est requis
        if ($this->commissionService->canCreateEventWithoutPayment($eventType)) {
            return redirect()->route('events.create')
                ->with('info', 'Vous pouvez créer cet événement sans paiement.');
        }
        
        $fee = $this->commissionService->getEventCreationFee($eventType);
        
        // Créer l'enregistrement de paiement
        $payment = EventCreationPayment::create([
            'user_id' => Auth::id(),
            'event_type' => $eventType,
            'amount' => $fee,
            'payment_method' => $paymentMethod,
            'status' => EventCreationPayment::STATUS_PENDING,
            'payment_details' => [
                'phone_number' => $phoneNumber,
                'created_at' => now()
            ]
        ]);

        // Ici, vous intégreriez avec l'API de paiement (MTN/Airtel)
        // Pour l'instant, on simule le processus
        
        return redirect()->route('event-creation-payment.status', $payment->id)
            ->with('success', 'Paiement initié. Veuillez confirmer le paiement sur votre téléphone.');
    }

    /**
     * Affiche le statut du paiement
     */
    public function showPaymentStatus($paymentId)
    {
        $payment = EventCreationPayment::where('user_id', Auth::id())
            ->findOrFail($paymentId);
            
        return view('events.payment-status', compact('payment'));
    }

    /**
     * Webhook pour confirmer le paiement (appelé par l'API de paiement)
     */
    public function confirmPayment(Request $request, $paymentId)
    {
        $payment = EventCreationPayment::findOrFail($paymentId);
        
        // Vérifier la signature du webhook (sécurité)
        // $this->verifyWebhookSignature($request);
        
        // Mettre à jour le statut du paiement
        $payment->update([
            'status' => EventCreationPayment::STATUS_PAID,
            'transaction_id' => $request->transaction_id,
            'paid_at' => now(),
            'payment_details' => array_merge($payment->payment_details ?? [], [
                'transaction_id' => $request->transaction_id,
                'confirmed_at' => now()
            ])
        ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Vérifie si un utilisateur a payé pour créer un type d'événement
     */
    public function hasPaidForEventType($eventType)
    {
        $hasPaid = EventCreationPayment::where('user_id', Auth::id())
            ->where('event_type', $eventType)
            ->where('status', EventCreationPayment::STATUS_PAID)
            ->exists();
            
        return response()->json(['has_paid' => $hasPaid]);
    }

    /**
     * Affiche l'historique des paiements de création d'événements
     */
    public function paymentHistory()
    {
        $payments = EventCreationPayment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('events.payment-history', compact('payments'));
    }
}
