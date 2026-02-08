<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Mail\ReservationConfirmation;
use App\Mail\ReservationCancelled;
use App\Mail\ReservationExpired;
use App\Mail\PaymentConfirmation;
use App\Models\Payment;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Services\AirtelMoneyGateway;
use Exception;
use Illuminate\Support\Facades\Log;


class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'event_id' => 'required|exists:events,id',
        'tickets' => 'required|array|min:1',
        'tickets.*.id' => 'required|exists:tickets,id',
        'tickets.*.quantity' => 'required|integer|min:1',
    ]);

    // Vérifier que l'événement existe
    $event = Event::findOrFail($validated['event_id']);

    // Empêcher les réservations 7 jours avant la date de début
    if ($event->start_date) {
        $sevenDaysBefore = Carbon::parse($event->start_date)->subDays(7);
        if (Carbon::now()->isAfter($sevenDaysBefore)) {
            return back()->with('error', 'Les réservations ne sont plus possibles. Il reste moins de 7 jours avant le début de l\'événement.');
        }
    }

    // Créer la réservation
    $reservation = $this->createReservation($validated);
    
    // Rediriger vers la page de confirmation de réservation
    return redirect()->route('reservations.show', $reservation)
        ->with('success', 'Réservation créée avec succès ! Vous devez payer dans les 7 jours avant le début de l\'événement, sinon elle sera automatiquement annulée.');
}

private function createReservation($data)
{
    return DB::transaction(function () use ($data) {
        $event = Event::findOrFail($data['event_id']);
        
        // Calculer le total et vérifier la disponibilité
        $total = 0;
        foreach ($data['tickets'] as $ticketData) {
            $ticket = Ticket::findOrFail($ticketData['id']);
            $quantity = $ticketData['quantity'];
            
            // Vérifier la disponibilité
            $available = $ticket->quantite - $ticket->quantite_vendue;
            if ($quantity > $available) {
                throw new \Exception("La quantité demandée pour le ticket {$ticket->nom} n'est plus disponible. Il reste {$available} billet(s).");
            }
            
            $total += $ticket->prix * $quantity;
        }
        
        // Calculer la date d'expiration : 7 jours avant l'événement ou maintenant + 7 jours si l'événement est plus loin
        $eventStartDate = Carbon::parse($event->start_date);
        $sevenDaysBefore = $eventStartDate->copy()->subDays(7);
        $expiresAt = Carbon::now()->addDays(7);
        
        if ($sevenDaysBefore->isFuture() && $sevenDaysBefore->lt($expiresAt)) {
            $expiresAt = $sevenDaysBefore;
        }
        
        // Créer la réservation
        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'event_id' => $data['event_id'],
            'status' => 'Réservé',
            'expires_at' => $expiresAt
        ]);
        
        // Attacher les tickets et mettre à jour les quantités vendues
        foreach ($data['tickets'] as $ticketData) {
            $ticket = Ticket::findOrFail($ticketData['id']);
            $quantity = $ticketData['quantity'];
            
            $reservation->tickets()->attach($ticket->id, [
                'quantity' => $quantity,
                'price' => $ticket->prix
            ]);
            
            // Réserver les billets (incrémenter quantite_vendue)
            $ticket->increment('quantite_vendue', $quantity);
        }

        return $reservation;
    });
}

public function index()
{
    $reservations = auth()->user()->reservations()
        ->with(['ticket.event', 'payment'])
        ->latest()
        ->paginate(10);

    return view('reservations.index', compact('reservations'));
}

    public function show(Reservation $reservation)
{
    // Vérifier que l'utilisateur est autorisé à voir cette réservation
    if (auth()->id() !== $reservation->user_id) {
        abort(403, 'Accès non autorisé à cette réservation');
    }

    // Charger les relations nécessaires
    $reservation->load(['ticket.event']);

    // Vérifier si l'événement existe via ticket
    if (!$reservation->ticket || !$reservation->ticket->event) {
        abort(404, 'Événement non trouvé pour cette réservation');
    }

    return view('reservations.show', compact('reservation'));
}

    public function cancel(Reservation $reservation)
    {
        // Vérifier que l'utilisateur est autorisé
        if (auth()->id() !== $reservation->user_id) {
            abort(403, 'Accès non autorisé');
        }

        if ($reservation->status !== 'Réservé') {
            return back()->with('error', 'Cette réservation ne peut pas être annulée.');
        }

        DB::transaction(function () use ($reservation) {
            // Libérer les tickets (décrémenter quantite_vendue)
            foreach ($reservation->tickets as $ticket) {
                $quantity = $ticket->pivot->quantity ?? 0;
                if ($quantity > 0) {
                    $ticket->decrement('quantite_vendue', $quantity);
                }
            }

            $reservation->status = 'Annulé';
            $reservation->save();

            // Envoyer un email de notification
            try {
                Mail::to($reservation->user->email)->send(new ReservationCancelled($reservation));
            } catch (\Exception $e) {
                \Log::error('Erreur envoi email annulation réservation: ' . $e->getMessage());
            }
        });

        return back()->with('success', 'Votre réservation a été annulée. Les billets sont maintenant disponibles.');
    }

    public function checkExpiredReservations()
    {
        // Annuler les réservations expirées (date d'expiration passée)
        $expiredReservations = Reservation::where('status', 'Réservé')
            ->where('expires_at', '<', Carbon::now())
            ->whereDoesntHave('payment')
            ->get();

        foreach ($expiredReservations as $reservation) {
            DB::transaction(function () use ($reservation) {
                // Libérer les tickets
                foreach ($reservation->tickets as $ticket) {
                    $quantity = $ticket->pivot->quantity ?? 0;
                    if ($quantity > 0) {
                        $ticket->decrement('quantite_vendue', $quantity);
                    }
                }

                $reservation->status = 'Annulé';
                $reservation->save();

                // Envoyer un email de notification
                try {
                    Mail::to($reservation->user->email)->send(new ReservationExpired($reservation));
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi email expiration réservation: ' . $e->getMessage());
                }
            });
        }

        // Annuler les réservations non payées 7 jours avant l'événement
        $reservationsToCancel = Reservation::where('status', 'Réservé')
            ->whereDoesntHave('payment')
            ->with('ticket.event')
            ->get()
            ->filter(function ($reservation) {
                return $reservation->shouldBeCancelled();
            });

        foreach ($reservationsToCancel as $reservation) {
            DB::transaction(function () use ($reservation) {
                // Libérer les tickets
                foreach ($reservation->tickets as $ticket) {
                    $quantity = $ticket->pivot->quantity ?? 0;
                    if ($quantity > 0) {
                        $ticket->decrement('quantite_vendue', $quantity);
                    }
                }

                $reservation->status = 'Annulé';
                $reservation->save();

                // Envoyer un email de notification
                try {
                    Mail::to($reservation->user->email)->send(new ReservationExpired($reservation));
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi email annulation auto réservation: ' . $e->getMessage());
                }
            });
        }
    }

    /**
     * Affiche le formulaire de paiement pour une réservation
     */
    public function pay(Reservation $reservation)
    {
        // Vérifier que l'utilisateur est autorisé
        if (auth()->id() !== $reservation->user_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la réservation peut être payée
        if (!$reservation->canBePaid()) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Cette réservation ne peut pas être payée.');
        }

        // Charger les relations nécessaires
        $reservation->load(['ticket.event']);

        return view('reservations.pay', compact('reservation'));
    }

    /**
     * Traite le paiement d'une réservation
     */
    public function processPayment(Request $request, Reservation $reservation)
    {
        // Vérifier que l'utilisateur est autorisé
        if (auth()->id() !== $reservation->user_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la réservation peut être payée
        if (!$reservation->canBePaid()) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Cette réservation ne peut pas être payée.');
        }

        $validated = $request->validate([
            'operator' => 'required|in:airtel,mtn',
            'phone' => ['required', 'regex:/^0[456][0-9]{7}$/'],
            'confirmation' => 'required|accepted'
        ], [
            'phone.regex' => 'Veuillez respecter le format requis: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx (9 chiffres).',
            'phone.required' => 'Le numéro de téléphone est requis.'
        ]);

        DB::beginTransaction();

        try {
            $methodLabel = $validated['operator'] === 'airtel' ? 'Airtel Money' : 'MTN Mobile Money';

            // Pour le moment, nous ne supportons que Airtel Money
            if ($validated['operator'] !== 'airtel') {
                return back()->with('error', 'MTN Mobile Money n\'est pas encore disponible. Veuillez utiliser Airtel Money.');
            }

            // Générer une référence unique pour la transaction
            $transactionReference = 'RSV-' . $reservation->id . '-' . time();

            // Créer d'abord le paiement en attente
            $payment = Payment::create([
                'matricule' => 'PAY-' . strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'reservation_id' => $reservation->id,
                'evenement_id' => $reservation->event_id,
                'montant' => $reservation->total_amount,
                'statut' => 'en_attente', // En attente de confirmation de l'API
                'methode_paiement' => $methodLabel,
                'reference_transaction' => $transactionReference,
                'numero_telephone' => $validated['phone'],
                'details' => json_encode([
                    'gateway' => 'airtel_money',
                    'phone' => $validated['phone'],
                    'operator' => $validated['operator'],
                    'initiated_at' => now()->toISOString(),
                    'amount' => $reservation->total_amount
                ])
            ]);

            Log::info('Paiement Airtel Money initié', [
                'payment_id' => $payment->id,
                'reservation_id' => $reservation->id,
                'amount' => $reservation->total_amount,
                'phone' => $validated['phone']
            ]);

            // Initier le paiement avec Airtel Money
            $gateway = new AirtelMoneyGateway();

            $paymentResult = $gateway->createPaymentSession([
                'phone' => $validated['phone'],
                'amount' => $reservation->total_amount,
                'reference' => $transactionReference,
                'transaction_id' => $transactionReference
            ]);

            if ($paymentResult['success']) {
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

                Log::info('Paiement Airtel Money créé avec succès', [
                    'payment_id' => $payment->id,
                    'airtel_transaction_id' => $paymentResult['transaction_id'] ?? null,
                    'status' => $paymentResult['status'] ?? 'pending'
                ]);

                // Le paiement est en attente - mettre à jour le statut
                $reservation->status = 'pending';
                $reservation->save();

                DB::commit();

                // Rediriger vers une page d'attente ou de confirmation
                return redirect()->route('reservations.show', $reservation)
                    ->with('success', 'Paiement initié avec succès ! Veuillez confirmer le paiement sur votre téléphone Airtel Money.');

            } else {
                // Échec de l'initiation du paiement
                Log::error('Échec initiation paiement Airtel Money', [
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

                return back()->with('error', 'Impossible d\'initier le paiement : ' . ($paymentResult['message'] ?? 'Erreur technique. Veuillez réessayer.'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur paiement réservation: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du paiement. Veuillez réessayer.');
        }
    }

    /**
     * Finalise un paiement réussi - met à jour les statuts et envoie les emails
     */
    private function finalizeSuccessfulPayment(Payment $payment, Reservation $reservation)
    {
        try {
            // Mettre à jour le paiement
            $payment->update([
                'statut' => 'payé',
                'date_paiement' => now()
            ]);

            // Mettre à jour la réservation
            $reservation->status = 'Payé';
            $reservation->save();

            // Générer le PDF des billets et l'envoyer par email
            $pdfContent = $this->generateReservationTicketsPdf($payment, $reservation);

            // Envoyer l'email avec les billets en pièce jointe
            Mail::to($reservation->user->email)->send(new PaymentConfirmation($payment, $pdfContent));

            Log::info('Paiement finalisé avec succès', [
                'payment_id' => $payment->id,
                'reservation_id' => $reservation->id,
                'user_email' => $reservation->user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la finalisation du paiement', [
                'payment_id' => $payment->id,
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Ne pas lever d'exception pour ne pas bloquer le processus
        }
    }

    /**
     * Vérifie le statut d'un paiement de réservation
     */
    public function checkPaymentStatus(Reservation $reservation)
    {
        // Vérifier que l'utilisateur est autorisé
        if (auth()->id() !== $reservation->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        try {
            // Trouver le paiement associé à la réservation
            $payment = $reservation->payments()->latest()->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun paiement trouvé pour cette réservation'
                ], 404);
            }

            // Vérifier que c'est un paiement Airtel Money
            if (stripos($payment->methode_paiement, 'Airtel') === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce paiement ne peut pas être vérifié via l\'API Airtel'
                ], 400);
            }

            // Utiliser le service de paiement pour vérifier le statut
            $paymentService = app(\App\Services\PaymentService::class);
            $result = $paymentService->verifyPayment($payment);

            // Mettre à jour le paiement si le statut a changé
            if (($result['verified'] ?? false) && ($result['status'] ?? null) === 'success') {
                if ($payment->statut !== 'payé') {
                    $payment->refresh();

                    // Finaliser le paiement si nécessaire
                    if ($payment->statut === 'payé') {
                        $this->finalizeSuccessfulPayment($payment, $reservation);
                    }
                }
            }

            return response()->json([
                'success' => $result['verified'] ?? false,
                'status' => $result['status'] ?? 'unknown',
                'message' => $result['message'] ?? 'Statut vérifié',
                'payment_status' => $payment->fresh()->statut,
                'reservation_status' => $reservation->fresh()->status,
                'retry' => $result['retry'] ?? false,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du statut du paiement de réservation', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère le PDF des billets pour une réservation payée
     */
    private function generateReservationTicketsPdf(Payment $payment, Reservation $reservation)
    {
        try {
            // Charger toutes les relations nécessaires
            $reservation->load(['ticket.event.organizer', 'ticket.event.sponsors', 'user']);
            $payment->load(['order', 'reservation']); // S'assurer que les relations sont chargées
            
            // S'assurer que evenement_id est défini sur le paiement
            if (!$payment->evenement_id && $reservation->ticket && $reservation->ticket->event) {
                $payment->evenement_id = $reservation->ticket->event->id;
            }
            
            $event = $reservation->ticket->event;
            
            if (!$event) {
                throw new \Exception('Événement non trouvé pour cette réservation');
            }
        
        // Convertir l'image de l'événement en base64
        $eventImageUrl = null;
        if ($event->image) {
            $eventImageUrl = $this->getImageAsBase64($event->image);
        }
        
        // Récupérer et convertir les logos des sponsors
        $sponsorLogos = [];
        
        // Ajouter le logo de l'organisateur en premier dans les sponsors
        if ($event->organizer) {
            if ($event->organizer->logo) {
                $orgLogo = $this->getImageAsBase64($event->organizer->logo);
                if ($orgLogo) {
                    $sponsorLogos[] = $orgLogo;
                }
            } else {
                // Si pas de logo, générer un avatar avec les initiales
                $initials = strtoupper(
                    mb_substr($event->organizer->prenom ?? '', 0, 1) . 
                    mb_substr($event->organizer->nom ?? '', 0, 1)
                );
                $sponsorLogos[] = $this->generateInitialsAvatar($initials, '#FFD700', '#1B1B3A');
            }
        }
        
        // Ajouter les logos des sponsors
        if ($event->sponsors && $event->sponsors->count() > 0) {
            foreach ($event->sponsors->take(3) as $sponsor) {
                if ($sponsor->logo_path) {
                    $logo = $this->getImageAsBase64($sponsor->logo_path);
                    if ($logo) {
                        $sponsorLogos[] = $logo;
                    }
                }
            }
        }
        
        // Logo de l'organisateur
        $organizerLogoUrl = null;
        if ($event->organizer) {
            if ($event->organizer->logo) {
                $organizerLogoUrl = $this->getImageAsBase64($event->organizer->logo);
            }
            
            if (!$organizerLogoUrl) {
                $initials = strtoupper(
                    mb_substr($event->organizer->prenom ?? '', 0, 1) . 
                    mb_substr($event->organizer->nom ?? '', 0, 1)
                );
                $organizerLogoUrl = $this->generateInitialsAvatar($initials);
            }
        }
        
        // Logo MokiliEvent par défaut si pas d'organisateur
        if (!$organizerLogoUrl) {
            $organizerLogoUrl = $this->getMokiliEventLogoBase64();
        }
        
        // Image de foule pour la partie droite
        $fouleImageUrl = $this->getImageAsBase64('images/foule-humains-copie.jpg');
        if (!$fouleImageUrl) {
            Log::warning('Image de foule non trouvée ou non convertie en base64');
        }
        
        // Générer un HTML par billet
        $pdfPages = [];
        $ticketIndex = 1;
        
        // Le ticket est unique pour cette réservation
        $ticket = $reservation->ticket;
        $quantity = $reservation->quantity;
        
        // Créer un billet pour chaque quantité réservée
        for ($i = 0; $i < $quantity; $i++) {
            // Crypter les données du QR code
            $qrCodeService = app(\App\Services\QrCodeEncryptionService::class);
            $encryptedQrData = $qrCodeService->encryptQrData($payment, $ticketIndex);
            $qrCodeUrl = $this->generateQrCodeBase64($encryptedQrData);
            $ticketType = strtoupper($ticket->nom ?? 'STANDARD');
            $ticketPrice = number_format($ticket->prix ?? 0, 0, ',', ' ') . ' FCFA';
            $eventDate = $event->start_date ? $event->start_date->format('d M Y') : now()->format('d M Y');
            
            $html = view('tickets.template', [
                'event' => $event,
                'payment' => $payment,
                'eventImageUrl' => $eventImageUrl,
                'qrCodeUrl' => $qrCodeUrl,
                'ticketType' => $ticketType,
                'ticketPrice' => $ticketPrice,
                'eventDate' => $eventDate,
                'sponsorLogos' => $sponsorLogos,
                'organizerLogoUrl' => $organizerLogoUrl,
                'fouleImageUrl' => $fouleImageUrl,
            ])->render();
            
            $pdfPages[] = $html;
            $ticketIndex++;
        }
        
        // Extraire le contenu du body de chaque page et les combiner
        $bodyContents = [];
        $headContent = '';
        
        foreach ($pdfPages as $index => $pageHtml) {
            // Extraire le head de la première page seulement
            if ($index === 0) {
                if (preg_match('/<head[^>]*>(.*?)<\/head>/is', $pageHtml, $headMatches)) {
                    $headContent = $headMatches[1];
                }
            }
            
            // Extraire le contenu entre <body> et </body> de manière plus robuste
            $bodyStart = stripos($pageHtml, '<body');
            if ($bodyStart !== false) {
                $bodyTagEnd = strpos($pageHtml, '>', $bodyStart);
                if ($bodyTagEnd !== false) {
                    $bodyEnd = stripos($pageHtml, '</body>', $bodyTagEnd);
                    if ($bodyEnd !== false) {
                        $bodyContent = substr($pageHtml, $bodyTagEnd + 1, $bodyEnd - $bodyTagEnd - 1);
                        $bodyContents[] = trim($bodyContent);
                    } else {
                        // Pas de balise fermante, prendre tout après <body>
                        $bodyContents[] = trim(substr($pageHtml, $bodyTagEnd + 1));
                    }
                } else {
                    $bodyContents[] = $pageHtml;
                }
            } else {
                // Si pas de body trouvé, utiliser le HTML tel quel
                $bodyContents[] = $pageHtml;
            }
        }
        
        // Combiner toutes les pages dans une structure HTML valide
        $combinedHtml = '<!DOCTYPE html>' . "\n";
        $combinedHtml .= '<html>' . "\n";
        $combinedHtml .= '<head>' . "\n";
        $combinedHtml .= '<meta charset="utf-8">' . "\n";
        if ($headContent) {
            $combinedHtml .= $headContent . "\n";
        }
        $combinedHtml .= '</head>' . "\n";
        $combinedHtml .= '<body>' . "\n";
        
        foreach ($bodyContents as $index => $bodyContent) {
            $combinedHtml .= $bodyContent . "\n";
            // Ajouter un saut de page seulement entre les tickets, pas après le dernier
            if ($index < count($bodyContents) - 1) {
                $combinedHtml .= '<div style="page-break-after: always; height: 0;"></div>' . "\n";
            }
        }
        
        $combinedHtml .= '</body>' . "\n";
        $combinedHtml .= '</html>';
        
        // Générer le PDF
        try {
            // Log pour déboguer (à retirer en production si nécessaire)
            Log::debug('Génération PDF réservation', [
                'pages_count' => count($bodyContents),
                'html_length' => strlen($combinedHtml),
                'first_100_chars' => substr($combinedHtml, 0, 100)
            ]);
            
            $pdf = Pdf::loadHTML($combinedHtml);
            $pdf->setPaper([0, 0, 481.89, 175.75], 'landscape');
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
            
            return $pdf->output();
        } catch (\Exception $e) {
            Log::error('Erreur génération PDF DomPDF réservation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'html_preview' => substr($combinedHtml, 0, 500)
            ]);
            throw $e;
        }
        } catch (\Exception $e) {
            Log::error('Erreur génération PDF réservation (niveau supérieur)', [
                'reservation_id' => $reservation->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
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
     * Génère le logo MokiliEvent par défaut en base64
     */
    private function getMokiliEventLogoBase64()
    {
        $logoPath = public_path('images/logo (3).png');
        
        if (file_exists($logoPath)) {
            try {
                $imageContent = file_get_contents($logoPath);
                $mimeType = mime_content_type($logoPath);
                return 'data:' . $mimeType . ';base64,' . base64_encode($imageContent);
            } catch (\Exception $e) {
                Log::error('Erreur chargement logo MokiliEvent', ['error' => $e->getMessage()]);
            }
        }
        
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
    <circle cx="50" cy="50" r="48" fill="#FFD700"/>
    <text x="50" y="45" font-family="Arial, sans-serif" font-size="24" font-weight="bold" text-anchor="middle" fill="#1B1B3A">M</text>
    <text x="50" y="70" font-family="Arial, sans-serif" font-size="18" font-weight="bold" text-anchor="middle" fill="#1B1B3A">E</text>
</svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    
    /**
     * Génère un avatar SVG avec les initiales
     */
    private function generateInitialsAvatar($initials, $bgColor = '#667eea', $textColor = '#ffffff')
    {
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
    <circle cx="50" cy="50" r="48" fill="' . $bgColor . '"/>
    <text x="50" y="62" font-family="Arial, sans-serif" font-size="36" font-weight="bold" text-anchor="middle" fill="' . $textColor . '">' . htmlspecialchars($initials) . '</text>
</svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    
    /**
     * Convertit une image en base64 pour l'intégrer dans le PDF
     */
    private function getImageAsBase64($imagePath)
    {
        try {
            if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $imageContent = @file_get_contents($imagePath);
                if (!$imageContent) {
                    Log::warning('Impossible de télécharger l\'image depuis l\'URL', ['url' => $imagePath]);
                    return null;
                }
                $mimeType = 'image/jpeg';
            } else {
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
                    Log::warning('Image introuvable', ['path' => $imagePath, 'tried_paths' => $paths]);
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

    /**
     * Télécharge les billets PDF d'une réservation payée
     */
    public function downloadTickets(Reservation $reservation)
    {
        // Vérifier que l'utilisateur est autorisé
        if (auth()->id() !== $reservation->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la réservation est payée
        if ($reservation->status !== 'Payé' || !$reservation->payment) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Les billets ne peuvent être téléchargés que pour les réservations payées.');
        }

        try {
            $payment = $reservation->payment;
            $pdfContent = $this->generateReservationTicketsPdf($payment, $reservation);
            
            $ticketCount = $reservation->quantity;
            $filename = $ticketCount > 1 
                ? "billets-{$payment->matricule}.pdf" 
                : "billet-{$payment->matricule}.pdf";

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Content-Length' => strlen($pdfContent),
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur téléchargement tickets réservation', [
                'reservation_id' => $reservation->id,
                'payment_id' => $reservation->payment->id ?? null,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Erreur lors de la génération des tickets : ' . $e->getMessage() . '. Veuillez réessayer plus tard.');
        }
    }
}
