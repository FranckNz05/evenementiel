<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmation;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('auth');
        $this->paymentService = $paymentService;
    }

    public function checkout(Request $request, Ticket $ticket)
    {
        // Bloquer l'achat si l'événement est passé
        $event = Event::find($ticket->event_id);
        if ($event && $event->end_date && \Carbon\Carbon::parse($event->end_date)->isPast()) {
            return back()->with('error', "Cet événement est terminé. L'achat de billets n'est plus possible.");
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $ticket->getAvailableQuantityAttribute(),
            'payment_method' => 'required|string'
        ]);

        $amount = $ticket->prix * $request->quantity;

        // Créer une commande
        $order = Order::create([
            'user_id' => auth()->id(),
            'evenement_id' => $ticket->event_id,
            'montant_total' => $amount,
            'statut' => 'en_attente',
            'expires_at' => now()->addMinutes(30)
        ]);

        // Attacher le ticket à la commande
        $order->tickets()->attach($ticket->id, [
            'quantity' => $request->quantity,
            'unit_price' => $ticket->prix,
            'total_amount' => $amount,
            'used_quantity' => 0,
            'is_fully_used' => false
        ]);

        // Récupérer l'OrderTicket créé
        $orderTicket = $order->orderTickets()->where('ticket_id', $ticket->id)->first();

        // Créer un paiement en attente
        $payment = Payment::create([
            'matricule' => 'PAY-' . strtoupper(Str::random(8)),
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'order_ticket_id' => $orderTicket->id,
            'evenement_id' => $ticket->event_id,
            'montant' => $amount,
            'statut' => Payment::STATUS_PENDING,
            'methode_paiement' => $request->payment_method,
            'numero_telephone' => $request->numero_telephone ?? '',
            'reference_transaction' => 'TX-' . strtoupper(Str::random(12))
        ]);

        return view('payments.checkout', compact('payment', 'order', 'ticket'));
    }

    public function process(Request $request, Order $order)
    {
        // Bloquer le paiement si l'événement est passé
        $event = Event::find($order->evenement_id);
        if ($event && $event->end_date && \Carbon\Carbon::parse($event->end_date)->isPast()) {
            return back()->with('error', "Cet événement est terminé. Le paiement n'est plus possible.");
        }

        if (auth()->id() !== $order->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à payer cette reservation.');
        }

        if (!in_array($order->statut, ['en_attente', 'échoué'])) {
            return redirect()->back()->with('error', 'Cette reservation a déjà été traitée');
        }

        if (!$order->montant_total) {
            $order->montant_total = $order->tickets->sum(function($ticket) {
                return $ticket->pivot->total_amount ?? ($ticket->pivot->quantity * $ticket->pivot->unit_price);
            });
        }

        return view('payments.process', [
            'order' => $order->load('tickets', 'event')
        ]);
    }

    public function store(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'methode_paiement' => 'required|in:MTN Mobile Money,Airtel Money,Visa',
                'numero_telephone' => 'required|string|min:9|max:10'
            ]);

            DB::beginTransaction();

            $totalAmount = $order->montant_total ?? $order->tickets->sum(function($ticket) {
                return $ticket->pivot->total_amount ?? ($ticket->pivot->quantity * $ticket->pivot->unit_price);
            });

            $payment = Payment::create([
                'matricule' => 'PAY-' . strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'order_ticket_id' => $order->id,
                'evenement_id' => $order->evenement_id,
                'montant' => $totalAmount,
                'methode_paiement' => $validated['methode_paiement'],
                'numero_telephone' => $validated['numero_telephone'],
                'statut' => Payment::STATUS_PENDING,
                'reference_transaction' => 'TX-' . strtoupper(Str::random(12)),
            ]);

            $paymentResult = $this->paymentService->processPayment($payment);

            if (!$paymentResult['success']) {
                throw new \Exception($paymentResult['message'] ?? 'Échec du paiement');
            }

            $payment->update([
                'statut' => Payment::STATUS_PAID,
                'qr_code' => $paymentResult['qr_code'] ?? null,
                'date_paiement' => now()
            ]);

            Log::info('QR Code généré', [
                'payment_id' => $payment->id,
                'qr_code_path' => $paymentResult['qr_code'] ?? 'null',
                'qr_code_saved' => $payment->fresh()->qr_code ?? 'null'
            ]);

            $order->update(['statut' => 'payé']);

            foreach ($order->tickets as $ticket) {
                $ticket->increment('quantite_vendue', $ticket->pivot->quantity);
            }

            DB::commit();

            // Générer le PDF des billets et envoyer l'email
            try {
                $payment->load(['order.event.organizer', 'order.tickets', 'user']);
                
                // Utiliser la méthode de PaymentController pour générer le PDF
                $paymentController = app(\App\Http\Controllers\PaymentController::class);
                $reflection = new \ReflectionClass($paymentController);
                $method = $reflection->getMethod('generateTicketsPdf');
                $method->setAccessible(true);
                
                $pdfContent = $method->invoke($paymentController, $payment);
                
                if ($pdfContent) {
                    // Envoyer l'email avec les billets en pièce jointe
                    Mail::to($payment->user->email)
                        ->send(new PaymentConfirmation($payment, $pdfContent));
                    
                    Log::info('Email de confirmation envoyé après paiement', [
                        'payment_id' => $payment->id,
                        'user_email' => $payment->user->email
                    ]);
                } else {
                    // Envoyer l'email sans PDF si la génération a échoué
                    Mail::to($payment->user->email)
                        ->send(new PaymentConfirmation($payment));
                }
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de l\'email après paiement', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Continuer même si l'email échoue
            }

            return redirect()->route('payments.success', $payment)
                ->with('success', 'Paiement effectué avec succès ! Vos billets ont été envoyés par email.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur de paiement détaillée: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'method' => $request->methode_paiement ?? 'non spécifiée',
                'stack_trace' => $e->getTraceAsString()
            ]);

            $errorMessage = config('app.debug')
                ? 'Erreur: ' . $e->getMessage()
                : 'Une erreur est survenue lors du paiement. Veuillez réessayer.';

            return back()->with('error', $errorMessage);
        }
    }

    public function callback(Request $request, Order $order)
    {
        $payment = $order->paiement;

        if (!$payment) {
            \Log::error('Callback: Paiement non trouvé pour la reservation', [
                'order_id' => $order->id
            ]);
            abort(404, 'Payment not found');
        }

        try {
            $payment->load('user', 'order.event', 'order.tickets');

            $verificationResult = $this->paymentService->verifyPayment($payment);

            if ($verificationResult['verified']) {
                DB::transaction(function () use ($payment, $request) {
                    // Guard: Vérifier que le paiement peut recevoir un QR code
                    $validator = app(\App\Services\PaymentStatusValidator::class);
                    
                    try {
                        // Valider que le paiement est payé avant de générer le QR code
                        if ($payment->statut !== Payment::STATUS_PAID) {
                            throw new \Exception("Impossible de générer un QR code: statut = '{$payment->statut}' (attendu: 'payé')");
                        }
                        
                        $validator->validateQrCodeGeneration($payment);
                        
                        // Generate QR code
                        $qrCode = $this->paymentService->generateQrCode($payment->matricule);
                        $qrCodePath = 'qrcodes/' . $payment->matricule . '.png';
                        Storage::put('public/' . $qrCodePath, $qrCode);

                        // Update payment
                        $payment->update([
                            'statut' => Payment::STATUS_PAID,
                            'reference_transaction' => $request->input('reference', $payment->reference_transaction),
                            'qr_code' => $qrCodePath
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de la génération du QR code', [
                            'payment_id' => $payment->id,
                            'error' => $e->getMessage()
                        ]);
                        throw $e;
                    }

                    // Update order
                    $payment->order->update(['statut' => 'payé']);

                    // Générer le PDF des billets et envoyer l'email
                    try {
                        // Utiliser la méthode de PaymentController pour générer le PDF
                        $paymentController = app(\App\Http\Controllers\PaymentController::class);
                        $reflection = new \ReflectionClass($paymentController);
                        $method = $reflection->getMethod('generateTicketsPdf');
                        $method->setAccessible(true);
                        
                        $pdfContent = $method->invoke($paymentController, $payment);
                        
                        if ($pdfContent) {
                            // Envoyer l'email avec les billets en pièce jointe
                            Mail::to($payment->user->email)
                                ->send(new PaymentConfirmation($payment, $pdfContent));
                            
                            Log::info('Email de confirmation envoyé dans callback', [
                                'payment_id' => $payment->id,
                                'user_email' => $payment->user->email
                            ]);
                        } else {
                            // Envoyer l'email sans PDF si la génération a échoué
                            Mail::to($payment->user->email)
                                ->send(new PaymentConfirmation($payment));
                        }
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'envoi de l\'email dans callback', [
                            'payment_id' => $payment->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        // Continuer même si l'email échoue
                    }
                });

                // Rediriger vers la page de succès sans vérification d'autorisation
                return redirect()->route('payments.success', $payment);
            }

            \Log::warning('Vérification du paiement échouée', [
                'payment_id' => $payment->id,
                'order_id' => $order->id
            ]);

            return redirect()->route('payments.failed', $payment)
                ->with('error', 'Payment verification failed');

        } catch (\Exception $e) {
            \Log::error('Callback error: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'exception' => $e->getTraceAsString()
            ]);

            return redirect()->route('payments.failed', $payment)
                ->with('error', 'Error processing payment confirmation');
        }
    }

    public function success(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $payment->load(['order.event', 'order.tickets', 'user']);

        if (!$payment->order) {
            return redirect()->route('home')->with('error', 'Détails de reservation non disponibles');
        }

        if (!$payment->qr_code && $payment->statut === Payment::STATUS_PAID) {
            // Guard: Vérifier que le paiement peut recevoir un QR code
            $validator = app(\App\Services\PaymentStatusValidator::class);
            
            try {
                $validator->validateQrCodeGeneration($payment);
                
                $qrCode = $this->paymentService->generateQrCode($payment->matricule);
                $qrCodePath = 'qrcodes/' . $payment->matricule . '.png';
                Storage::put('public/' . $qrCodePath, $qrCode);

                $payment->update(['qr_code' => $qrCodePath]);
            } catch (\Exception $e) {
                Log::error('Impossible de générer le QR code dans success()', [
                    'payment_id' => $payment->id,
                    'statut' => $payment->statut,
                    'error' => $e->getMessage()
                ]);
                // Ne pas bloquer l'affichage de la page de succès
            }

            $payment = $payment->fresh();

            Log::info('QR Code généré dans success()', [
                'payment_id' => $payment->id,
                'qr_code_path' => $qrCodePath,
                'qr_code_saved' => $payment->qr_code ?? 'null'
            ]);
        }

        return view('payments.success', compact('payment'));
    }

    public function failed(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $payment->load(['order.event', 'order.tickets']);

        return view('payments.failed', compact('payment'));
    }

    public function history()
    {
        $payments = Payment::with(['order.tickets'])
            ->where('user_id', auth()->id())
            ->where('statut', 'payé')
            ->latest()
            ->paginate(10);

        $totalTickets = Order::where('user_id', auth()->id())
            ->where('statut', 'payé')
            ->with('tickets')
            ->get()
            ->sum(function($order) {
                return $order->tickets->sum('pivot.quantity');
            });

        return view('payments.history', compact('payments', 'totalTickets'));
    }
}


