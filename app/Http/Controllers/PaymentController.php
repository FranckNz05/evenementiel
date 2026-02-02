<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\PaymentConfirmation;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\AirtelMoneyGateway;
use Exception;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche le formulaire de paiement simulé
     */
    public function process(Order $order)
    {
        // Vérifier que l'utilisateur est propriétaire de la commande
        if (auth()->id() !== $order->user_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la commande n'est pas déjà payée
        if ($order->statut === 'payé') {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Cette commande est déjà payée.');
        }

        $order->load(['event', 'tickets']);

        return view('payments.process', compact('order'));
    }

    /**
     * Traite le paiement avec Airtel Money
     */
    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'operator' => 'required|in:airtel,mtn',
            'phone' => ['required','regex:/^0[456][0-9]{7}$/'],
            'confirmation' => 'required|accepted'
        ], [
            'phone.regex' => 'Veuillez respecter le format requis: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx (9 chiffres).',
            'phone.required' => 'Le numéro de téléphone est requis.'
        ]);

        // Vérifier que l'utilisateur est propriétaire de la commande
        if (auth()->id() !== $order->user_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la commande n'est pas déjà payée
        if ($order->statut === 'payé') {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Cette commande est déjà payée.');
        }

        DB::beginTransaction();

        try {
            $methodLabel = $request->operator === 'airtel' ? 'Airtel Money' : 'MTN Mobile Money';

            // Pour le moment, nous ne supportons que Airtel Money
            if ($request->operator !== 'airtel') {
                return redirect()->back()
                    ->with('error', 'MTN Mobile Money n\'est pas encore disponible. Veuillez utiliser Airtel Money.');
            }

            // Générer une référence unique pour la transaction
            $transactionReference = 'ORD-' . $order->id . '-' . time();

            // Créer d'abord le paiement en attente
            $payment = Payment::create([
                'matricule' => 'PAY-' . strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'evenement_id' => $order->evenement_id,
                'montant' => $order->montant_total,
                'statut' => 'en attente', // En attente de confirmation de l'API
                'methode_paiement' => $methodLabel,
                'reference_transaction' => $transactionReference,
                'numero_telephone' => $request->phone,
                'details' => json_encode([
                    'gateway' => 'airtel_money',
                    'phone' => $request->phone,
                    'operator' => $request->operator,
                    'initiated_at' => now()->toISOString(),
                    'amount' => $order->montant_total
                ])
            ]);

            Log::info('Paiement Airtel Money initié pour commande', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'amount' => $order->montant_total,
                'phone' => $request->phone
            ]);

            // Initier le paiement avec Airtel Money
            $gateway = new AirtelMoneyGateway();

            $paymentResult = $gateway->createPaymentSession([
                'phone' => $request->phone,
                'amount' => $order->montant_total,
                'reference' => $transactionReference,
                'transaction_id' => $transactionReference
            ]);

            // Traiter le résultat du paiement Airtel Money
            // Le statut "pending" est aussi un succès (paiement en attente de confirmation)
            if ($paymentResult['success'] || ($paymentResult['status'] ?? null) === 'pending') {
                // Mettre à jour le paiement avec les informations d'Airtel
                $payment->update([
                    'reference_paiement' => $paymentResult['transaction_id'] ?? $transactionReference,
                    'details' => json_encode(array_merge(
                        json_decode($payment->details, true),
                        [
                            'airtel_transaction_id' => $paymentResult['transaction_id'] ?? null,
                            'airtel_reference' => $paymentResult['reference'] ?? null,
                            'api_response' => $paymentResult,
                            'status' => $paymentResult['status'] ?? 'pending'
                        ]
                    ))
                ]);

                Log::info('Paiement Airtel Money créé avec succès pour commande', [
                    'payment_id' => $payment->id,
                    'airtel_transaction_id' => $paymentResult['transaction_id'] ?? null,
                    'status' => $paymentResult['status'] ?? 'pending'
                ]);

                // Le paiement est en attente - mettre à jour le statut de la commande
                $order->update([
                    'statut' => 'en_attente'
                ]);

                DB::commit();

                // Rediriger vers la page de chargement/attente
                return redirect()->route('payments.waiting', $payment)
                    ->with('info', 'Paiement initié ! Veuillez confirmer sur votre téléphone Airtel Money.');

            } else {
                // Échec réel de l'initiation du paiement
                Log::error('Échec initiation paiement Airtel Money pour commande', [
                    'payment_id' => $payment->id,
                    'error' => $paymentResult['message'] ?? 'Erreur inconnue',
                    'api_response' => $paymentResult
                ]);

                // Marquer le paiement comme échoué
                $payment->update([
                    'statut' => 'échoué',
                    'details' => json_encode(array_merge(
                        json_decode($payment->details, true),
                        [
                            'error' => $paymentResult['message'] ?? 'Erreur inconnue',
                            'api_response' => $paymentResult,
                            'failed_at' => now()->toISOString()
                        ]
                    ))
                ]);

                DB::rollBack();

                return redirect()->back()
                    ->with('error', 'Impossible d\'initier le paiement : ' . ($paymentResult['message'] ?? 'Erreur technique. Veuillez réessayer.'));
            }

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors du paiement Airtel Money pour commande', [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors du traitement du paiement. Veuillez réessayer.');
        }
    }

    /**
     * Page de succès du paiement
     */
    public function success(Payment $payment)
    {
        // Vérifier que l'utilisateur est propriétaire du paiement
        if (auth()->id() !== $payment->user_id) {
            abort(403, 'Accès non autorisé');
        }

        $payment->load(['order.event', 'order.tickets']);

        return view('payments.success', compact('payment'));
    }

    /**
     * Page d'attente de confirmation du paiement
     */
    public function waiting(Payment $payment)
    {
        // Vérifier que l'utilisateur est propriétaire du paiement
        if (auth()->id() !== $payment->user_id) {
            abort(403, 'Accès non autorisé');
        }

        $payment->load(['order.event', 'order.tickets']);

        return view('payments.waiting', compact('payment'));
    }

    /**
     * Page d'échec du paiement
     */
    public function failure(Payment $payment)
    {
        // Vérifier que l'utilisateur est propriétaire du paiement
        if (auth()->id() !== $payment->user_id) {
            abort(403, 'Accès non autorisé');
        }

        $payment->load(['order.event', 'order.tickets']);

        return view('payments.failure', compact('payment'));
    }

    /**
     * Historique des paiements de l'utilisateur
     */
    public function history()
    {
        $payments = Payment::with(['order.event', 'order.tickets'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('payments.history', compact('payments'));
    }

    /**
     * Détails d'un paiement
     */
    public function show(Payment $payment)
    {
        // Vérifier que l'utilisateur est propriétaire du paiement
        if (auth()->id() !== $payment->user_id) {
            abort(403, 'Accès non autorisé');
        }

        $payment->load(['order.event', 'order.tickets']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Vérifie le statut d'un paiement (pour les paiements Airtel Money)
     * 
     * @param Payment $payment
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Payment $payment)
    {
        // Vérifier que l'utilisateur est propriétaire du paiement
        if (auth()->id() !== $payment->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        // Vérifier que c'est un paiement Airtel Money avec une référence
        if (stripos($payment->methode_paiement, 'Airtel') === false || !$payment->reference_transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Ce paiement ne peut pas être vérifié via l\'API Airtel'
            ], 400);
        }

        try {
            // Utiliser directement AirtelMoneyService pour vérifier le statut
            $airtelService = app(\App\Services\AirtelMoneyService::class);
            
            // Récupérer l'ID de transaction Airtel depuis les détails ou la référence
            $details = json_decode($payment->details ?? '{}', true) ?: [];
            $airtelTransactionId = $details['airtel_transaction_id'] ?? $payment->reference_transaction;
            
            $result = $airtelService->checkTransactionStatus($airtelTransactionId);
            
            // Récupérer le statut de transaction Airtel (TS, TF, TA, TIP, TE)
            $transactionStatus = $result['transaction_status'] ?? null;
            $isSuccess = ($transactionStatus === 'TS');
            $isFailed = in_array($transactionStatus, ['TF', 'TE']);
            $isPending = in_array($transactionStatus, ['TA', 'TIP']);
            
            // Mettre à jour le paiement selon le statut
            $payment->refresh();
            $existingDetails = json_decode($payment->details ?? '{}', true) ?: [];
            $emailAlreadySent = ($existingDetails['confirmation_email_sent'] ?? false) === true;
            
            // Si le paiement est réussi (TS) et n'a pas encore été finalisé
            if ($isSuccess && $payment->statut !== 'payé') {
                DB::beginTransaction();
                try {
                    // Traduire le message Airtel en français
                    $translatedMessage = $this->translateAirtelMessage($result['message'] ?? 'Transaction réussie');
                    
                    // Mettre à jour le paiement
                    $payment->update([
                        'statut' => 'payé',
                        'date_paiement' => now(),
                        'details' => json_encode(array_merge(
                            $existingDetails,
                            [
                                'airtel_transaction_status' => $transactionStatus,
                                'airtel_message' => $translatedMessage,
                                'callback_message' => $translatedMessage,
                                'verified_at' => now()->toISOString(),
                                'verification_result' => $result
                            ]
                        ))
                    ]);
                    
                    // Mettre à jour la commande
                    if ($payment->order) {
                        $payment->order->update([
                            'statut' => 'payé',
                            'payment_status' => 'payé',
                            'paid_at' => now(),
                        ]);
                        
                        // Mettre à jour les quantités de tickets
                        foreach ($payment->order->tickets as $ticket) {
                            $quantity = $ticket->pivot->quantity ?? 1;
                            $ticket->increment('quantite_vendue', $quantity);
                        }
                    }
                    
                    // Envoyer l'email UNIQUEMENT si pas déjà envoyé
                    if (!$emailAlreadySent) {
                        try {
                            $payment->load(['order.event.organizer', 'order.event.sponsors', 'order.tickets', 'user']);
                            
                            $pdfContent = $this->generateTicketsPdf($payment);
                            
                            if ($pdfContent) {
                                Mail::to($payment->user->email)
                                    ->send(new PaymentConfirmation($payment, $pdfContent));
                            } else {
                                Mail::to($payment->user->email)
                                    ->send(new PaymentConfirmation($payment));
                            }
                            
                            // Marquer que l'email a été envoyé
                            $updatedDetails = json_decode($payment->fresh()->details ?? '{}', true) ?: [];
                            $payment->update([
                                'details' => json_encode(array_merge(
                                    $updatedDetails,
                                    [
                                        'confirmation_email_sent' => true,
                                        'email_sent_at' => now()->toISOString()
                                    ]
                                ))
                            ]);
                            
                            Log::info('Email de confirmation envoyé après vérification du paiement', [
                                'payment_id' => $payment->id,
                                'user_email' => $payment->user->email
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Erreur lors de l\'envoi de l\'email de confirmation', [
                                'payment_id' => $payment->id,
                                'error' => $e->getMessage()
                            ]);
                            // Ne pas bloquer le processus si l'email échoue
                        }
                    }
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Erreur lors de la finalisation du paiement', [
                        'payment_id' => $payment->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            // Si le paiement a échoué (TF ou TE) et n'a pas encore été marqué comme échoué
            elseif ($isFailed && $payment->statut !== 'échoué') {
                // Traduire le message Airtel en français
                $translatedMessage = $this->translateAirtelMessage($result['message'] ?? 'Transaction échouée');
                
                $payment->update([
                    'statut' => 'échoué',
                    'details' => json_encode(array_merge(
                        $existingDetails,
                        [
                            'airtel_transaction_status' => $transactionStatus,
                            'airtel_message' => $translatedMessage,
                            'airtel_error_code' => $result['error_code'] ?? null,
                            'error_message' => $translatedMessage,
                            'verified_at' => now()->toISOString(),
                            'verification_result' => $result
                        ]
                    ))
                ]);
            }
            // Si le paiement est en attente (TA ou TIP), juste mettre à jour les détails
            elseif ($isPending) {
                $payment->update([
                    'details' => json_encode(array_merge(
                        $existingDetails,
                        [
                            'airtel_transaction_status' => $transactionStatus,
                            'verified_at' => now()->toISOString(),
                            'verification_result' => $result
                        ]
                    ))
                ]);
            }
            
            // Préparer la réponse JSON
            $response = [
                'success' => $result['success'] ?? false,
                'status' => $result['status'] ?? 'unknown',
                'transaction_status' => $transactionStatus, // TS, TF, TA, TIP, TE
                'message' => $result['message'] ?? 'Statut vérifié',
                'payment_status' => $payment->fresh()->statut,
                'retry' => $result['retry'] ?? false,
            ];
            
            // Ajouter l'URL de redirection selon le statut
            if ($isSuccess) {
                $response['redirect_url'] = route('payments.success', $payment);
            } elseif ($isFailed) {
                $response['redirect_url'] = route('payments.failure', $payment);
            }
            
            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du statut du paiement', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'transaction_status' => null,
                'message' => 'Erreur lors de la vérification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifie les informations d'un utilisateur Airtel Money
     * Utile pour valider un numéro avant un paiement ou un retrait
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateAirtelUser(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^0(4|5|6)\d{7}$/',
        ], [
            'phone.regex' => 'Le numéro doit commencer par 05, 06 ou 04 et contenir 9 chiffres',
        ]);

        try {
            $airtelService = app(\App\Services\AirtelMoneyService::class);
            
            $result = $airtelService->getUserInfo($request->phone);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'user' => $result['user'],
                    'message' => 'Utilisateur Airtel Money trouvé',
                    'is_valid' => true,
                    'can_receive_payment' => !($result['user']['is_barred'] ?? false) && ($result['user']['is_pin_set'] ?? false),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Utilisateur Airtel Money introuvable ou erreur',
                    'is_valid' => false,
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors de la validation de l\'utilisateur Airtel Money', [
                'phone' => $request->phone,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Annuler un paiement en attente
     */
    public function cancel(Payment $payment)
    {
        // Vérifier que l'utilisateur est propriétaire du paiement
        if (auth()->id() !== $payment->user_id) {
            abort(403, 'Accès non autorisé');
        }

        // Seuls les paiements en attente peuvent être annulés
        if ($payment->statut !== 'en attente') {
            return redirect()->back()
                ->with('error', 'Seuls les paiements en attente peuvent être annulés.');
        }

        try {
            $payment->update(['statut' => 'annulé']);
            
            // Remettre les tickets en stock
            foreach ($payment->order->tickets as $ticket) {
                $quantity = $ticket->pivot->quantity ?? 1;
                $ticket->decrement('quantite_vendue', $quantity);
            }

            Log::info('Paiement annulé', [
                'payment_id' => $payment->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('payments.history')
                ->with('success', 'Paiement annulé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'annulation du paiement', [
                'payment_id' => $payment->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'annulation.');
        }
    }

    /**
     * Finalise un paiement réussi - met à jour les statuts et envoie les emails
     */
    private function finalizeSuccessfulPayment(Payment $payment, Order $order)
    {
        try {
            // Mettre à jour le paiement
            $payment->update([
                'statut' => 'payé',
                'date_paiement' => now()
            ]);

            // Mettre à jour la commande
            $order->update([
                'statut' => 'payé',
                'payment_status' => 'payé',
                'paid_at' => now()
            ]);

            // Mettre à jour les quantités vendues des tickets
            foreach ($order->tickets as $ticket) {
                $quantity = $ticket->pivot->quantity ?? 1;
                $ticket->increment('quantite_vendue', $quantity);
            }

            // Générer le PDF des billets et l'envoyer par email
            $pdfContent = $this->generateTicketsPdf($payment);

            // Envoyer l'email avec les billets en pièce jointe
            Mail::to($payment->user->email)->send(new PaymentConfirmation($payment, $pdfContent));

            Log::info('Paiement finalisé avec succès pour commande', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'user_email' => $payment->user->email
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la finalisation du paiement pour commande', [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Ne pas lever d'exception pour ne pas bloquer le processus
        }
    }

    /**
     * Génère le PDF des billets pour un paiement avec template.blade.php
     * Version optimisée pour préserver le design
     */
    private function generateTicketsPdf(Payment $payment)
    {
        // Charger toutes les relations nécessaires
        $payment->load(['order.event.organizer', 'order.event.sponsors', 'order.tickets', 'user']);
        
        // S'assurer que evenement_id est défini
        if (!$payment->evenement_id && $payment->order) {
            $payment->evenement_id = $payment->order->evenement_id;
        }
        
        $order = $payment->order;
        $event = $order->event;
        
        // Convertir l'image de l'événement en base64
        $eventImageUrl = null;
        if ($event->image) {
            $eventImageUrl = $this->getImageAsBase64($event->image);
        }
        
        // Image de foule pour la partie droite
        $fouleImageUrl = $this->getImageAsBase64('images/logo.png');
        if (!$fouleImageUrl) {
            Log::warning('Image de foule non trouvée ou non convertie en base64');
        }
        
        // Préparer les données pour chaque billet
        $ticketsData = [];
        $ticketIndex = 1;
        
        foreach ($order->tickets as $ticket) {
            $quantity = $ticket->pivot->quantity ?? 1;
            
            // Créer un billet pour chaque quantité achetée
            for ($i = 0; $i < $quantity; $i++) {
                // Crypter les données du QR code
                $qrCodeService = app(\App\Services\QrCodeEncryptionService::class);
                $encryptedQrData = $qrCodeService->encryptQrData($payment, $ticketIndex);
                $qrCodeUrl = $this->generateQrCodeBase64($encryptedQrData);
                
                $ticketsData[] = [
                    'event' => $event,
                    'payment' => $payment,
                    'eventImageUrl' => $eventImageUrl,
                    'qrCodeUrl' => $qrCodeUrl,
                    'ticketType' => strtoupper($ticket->nom ?? 'VIP'),
                    'ticketPrice' => $ticket->prix ?? 0,
                    'fouleImageUrl' => $fouleImageUrl,
                    'ticketIndex' => $ticketIndex
                ];
                
                $ticketIndex++;
            }
        }
        
        // Utiliser une vue dédiée pour le PDF qui génère tous les billets
        $pdf = Pdf::loadView('tickets.pdf-template', [
            'ticketsData' => $ticketsData
        ]);
        
        // Configuration optimisée pour DomPDF avec options simplifiées
        $pdf->setPaper([0, 0, 481.89, 175.75], 'landscape'); // 170mm x 62mm en points
        $pdf->setOptions([
            'dpi' => 96,
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => false, // Désactivé pour éviter les erreurs de cellmap
            'isFontSubsettingEnabled' => false,
            'isPhpEnabled' => false,
            'enable-local-file-access' => true,
            'chroot' => realpath(base_path()),
            'debugKeepTemp' => false,
            'debugCss' => false,
            'logOutputFile' => storage_path('logs/dompdf.log'),
            'tempDir' => storage_path('app/temp'),
        ]);
        
        // Retourner le contenu du PDF
        return $pdf->output();
    }

    /**
     * Génère un QR code en base64
     */
    private function generateQrCodeBase64($data)
    {
        try {
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($renderer);
            $qrCodeSvg = $writer->writeString($data);
            
            return 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
        } catch (\Exception $e) {
            Log::error('Erreur génération QR code', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Convertit une image en base64 pour l'intégrer dans le PDF
     */
    private function getImageAsBase64($imagePath)
    {
        try {
            // Si c'est déjà une URL complète, télécharger l'image
            if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $imageContent = @file_get_contents($imagePath);
                if (!$imageContent) {
                    Log::warning('Impossible de télécharger l\'image depuis l\'URL', ['url' => $imagePath]);
                    return null;
                }
                $mimeType = 'image/jpeg';
            } else {
                // Essayer plusieurs emplacements possibles
                $paths = [
                    storage_path('app/public/' . $imagePath),
                    public_path('storage/' . $imagePath),
                    public_path($imagePath),
                    base_path($imagePath)
                ];
                
                $fullPath = null;
                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        $fullPath = $path;
                        break;
                    }
                }
                
                if (!$fullPath) {
                    Log::warning('Image introuvable dans tous les chemins', [
                        'path' => $imagePath,
                        'tried_paths' => $paths
                    ]);
                    return null;
                }
                
                $imageContent = file_get_contents($fullPath);
                $mimeType = mime_content_type($fullPath);
            }
            
            return 'data:' . $mimeType . ';base64,' . base64_encode($imageContent);
        } catch (\Exception $e) {
            Log::error('Erreur conversion image en base64', [
                'path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
