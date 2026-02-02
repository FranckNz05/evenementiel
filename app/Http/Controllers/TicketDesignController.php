<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketDesignController extends Controller
{
    /**
     * Affiche une prévisualisation du design du billet
     * Accessible sans authentification pour faciliter le développement
     */
    public function preview(Request $request)
    {
        // Récupérer un paiement existant ou utiliser des données de test
        $paymentId = $request->query('payment_id');
        
        if ($paymentId) {
            $payment = Payment::with(['order.event.organizer', 'order.event.sponsors', 'order.tickets', 'user'])
                ->find($paymentId);
                
            if (!$payment) {
                return $this->generateTestData();
            }
            
            return $this->renderPreview($payment);
        }
        
        return $this->generateTestData();
    }
    
    /**
     * Génère des données de test pour la prévisualisation
     */
    private function generateTestData()
    {
        // Récupérer le premier événement avec un billet ou créer des données fictives
        $event = Event::with(['organizer', 'sponsors'])->first();
        
        if (!$event) {
            // Créer un événement fictif pour la prévisualisation
            $event = new Event([
                'title' => 'Concert de Gala - Festival MokiliEvent 2025',
                'description' => 'Une soirée inoubliable avec les meilleurs artistes',
                'lieu' => 'Stade des Martyrs',
                'ville' => 'Kinshasa',
                'adresse' => 'Avenue de la Libération, Kinshasa, RDC',
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(30)->addHours(5),
                'image' => 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=800',
            ]);
        }
        
        // Créer un ticket fictif
        $ticket = Ticket::first() ?? new Ticket([
            'nom' => 'VIP GOLD',
            'description' => 'Accès VIP avec tous les avantages',
            'prix' => 50000,
            'quantite' => 100,
            'quantite_vendue' => 45,
        ]);
        
        // Créer un utilisateur fictif
        $user = User::first() ?? new User([
            'prenom' => 'John',
            'nom' => 'DOE',
            'email' => 'john.doe@example.com',
        ]);
        
        // Créer une commande fictive
        $order = new Order([
            'id' => 1,
            'montant_total' => 50000,
            'statut' => 'payé',
        ]);
        $order->setRelation('event', $event);
        $order->setRelation('tickets', collect([$ticket]));
        $order->setRelation('user', $user);
        
        // Créer un paiement fictif
        $payment = new Payment([
            'id' => 1,
            'matricule' => 'PAY-TEST-' . strtoupper(substr(md5(time()), 0, 8)),
            'montant' => 50000,
            'statut' => 'payé',
            'methode_paiement' => 'Test',
            'date_paiement' => now(),
        ]);
        $payment->setRelation('order', $order);
        $payment->setRelation('user', $user);
        
        return $this->renderPreview($payment, true);
    }
    
    /**
     * Affiche la prévisualisation du billet
     */
    private function renderPreview($payment, $isTestData = false)
    {
        $order = $payment->order;
        $event = $order->event;
        
        // Convertir l'image de l'événement en base64
        $eventImageUrl = null;
        if ($event->image) {
            $eventImageUrl = $this->getImageAsBase64($event->image);
            
            // Log pour debug
            if (!$eventImageUrl) {
                \Log::warning('Image événement non convertie', [
                    'image_path' => $event->image,
                    'is_url' => filter_var($event->image, FILTER_VALIDATE_URL)
                ]);
            }
        }
        
        $ticket = $order->tickets->first();
        if (!$ticket->pivot) {
            $ticket->pivot = (object)['quantity' => 1, 'unit_price' => $ticket->prix ?? 50000];
        }
        
        // Générer le QR code
        $qrCodeUrl = $this->generateQrCodeBase64($payment->matricule . '-1');
        
        // Formater les données pour le nouveau template
        $ticketType = strtoupper($ticket->nom ?? 'VIP');
        $ticketPrice = number_format($ticket->prix ?? 50000, 0, ',', ' ') . ' FCFA';
        
        // Logs pour debug
        \Log::info('Données prévisualisation', [
            'event_title' => $event->title ?? 'N/A',
            'event_image' => $event->image ?? 'N/A',
            'has_eventImageUrl' => !empty($eventImageUrl),
            'has_qrCodeUrl' => !empty($qrCodeUrl),
            'ticketType' => $ticketType,
        ]);
        
        // Image de foule pour la partie droite (optionnelle)
        $fouleImageUrl = $this->getImageAsBase64('images/foule-humains-copie.jpg');
        
        return view('tickets.preview-design', [
            'event' => $event,
            'payment' => $payment,
            'order' => $order,
            'ticket' => $ticket,
            'eventImageUrl' => $eventImageUrl,
            'qrCodeUrl' => $qrCodeUrl,
            'ticketType' => $ticketType,
            'ticketPrice' => $ticketPrice,
            'fouleImageUrl' => $fouleImageUrl,
            'isTestData' => $isTestData
        ]);
    }
    
    /**
     * Télécharge le PDF du billet en prévisualisation
     */
    public function downloadPreview(Request $request)
    {
        $paymentId = $request->query('payment_id');
        
        if ($paymentId) {
            $payment = Payment::with(['order.event.organizer', 'order.event.sponsors', 'order.tickets', 'user'])
                ->find($paymentId);
        }
        
        if (!isset($payment) || !$payment) {
            // Utiliser les données de test
            $payment = $this->createTestPayment();
        }
        
        $pdfContent = $this->generateTicketsPdf($payment);
        
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview-billet.pdf"',
        ]);
    }
    
    /**
     * Crée un paiement de test pour la prévisualisation PDF
     */
    private function createTestPayment()
    {
        $event = Event::with(['organizer', 'sponsors'])->first();
        
        if (!$event) {
            $event = new Event([
                'title' => 'Concert de Gala - Festival MokiliEvent 2025',
                'description' => 'Une soirée inoubliable avec les meilleurs artistes',
                'lieu' => 'Stade des Martyrs',
                'ville' => 'Kinshasa',
                'adresse' => 'Avenue de la Libération, Kinshasa, RDC',
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(30)->addHours(5),
                'image' => 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=800',
            ]);
        }
        
        $ticket = Ticket::first() ?? new Ticket([
            'nom' => 'VIP GOLD',
            'description' => 'Accès VIP avec tous les avantages',
            'prix' => 50000,
        ]);
        $ticket->pivot = (object)['quantity' => 1, 'unit_price' => 50000];
        
        $user = User::first() ?? new User([
            'prenom' => 'John',
            'nom' => 'DOE',
            'email' => 'john.doe@example.com',
        ]);
        
        $order = new Order(['montant_total' => 50000, 'statut' => 'payé']);
        $order->setRelation('event', $event);
        $order->setRelation('tickets', collect([$ticket]));
        $order->setRelation('user', $user);
        
        $payment = new Payment([
            'matricule' => 'PAY-TEST-' . strtoupper(substr(md5(time()), 0, 8)),
            'montant' => 50000,
            'statut' => 'payé',
        ]);
        $payment->setRelation('order', $order);
        $payment->setRelation('user', $user);
        
        return $payment;
    }
    
    /**
     * Génère le PDF des billets avec le nouveau template
     */
    private function generateTicketsPdf($payment)
    {
        $order = $payment->order;
        $event = $order->event;
        
        $eventImageUrl = null;
        if ($event->image) {
            $eventImageUrl = $this->getImageAsBase64($event->image);
        }
        
        // Image de foule pour la partie droite
        $fouleImageUrl = $this->getImageAsBase64('images/foule-humains-copie.jpg');
        
        // Générer un PDF par billet
        $pdfPages = [];
        $ticketIndex = 1;
        
        foreach ($order->tickets as $ticket) {
            $quantity = $ticket->pivot->quantity ?? 1;
            
            for ($i = 0; $i < $quantity; $i++) {
                // Crypter les données du QR code
                $qrCodeService = app(\App\Services\QrCodeEncryptionService::class);
                $encryptedQrData = $qrCodeService->encryptQrData($payment, $ticketIndex);
                $qrCodeUrl = $this->generateQrCodeBase64($encryptedQrData);
                $ticketType = strtoupper($ticket->nom ?? 'VIP');
                $ticketPrice = number_format($ticket->prix ?? 0, 0, ',', ' ') . ' FCFA';
                
                $html = view('tickets.template', [
                    'event' => $event,
                    'payment' => $payment,
                    'eventImageUrl' => $eventImageUrl,
                    'qrCodeUrl' => $qrCodeUrl,
                    'ticketType' => $ticketType,
                    'ticketPrice' => $ticketPrice,
                    'fouleImageUrl' => $fouleImageUrl,
                ])->render();
                
                $pdfPages[] = $html;
                $ticketIndex++;
            }
        }
        
        // Combiner toutes les pages avec séparateur de page (pas après le dernier)
        $combinedHtml = '';
        foreach ($pdfPages as $index => $pageHtml) {
            $combinedHtml .= $pageHtml;
            // Ajouter un saut de page seulement entre les tickets, pas après le dernier
            if ($index < count($pdfPages) - 1) {
                $combinedHtml .= '<div style="page-break-after: always;"></div>';
            }
        }
        
        $pdf = Pdf::loadHTML($combinedHtml);
        $pdf->setPaper([0, 0, 481.89, 175.75], 'landscape'); // 170mm x 62mm
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'debugKeepTemp' => false,
            'debugCss' => false,
        ]);
        
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
     * Convertit une image en base64
     */
    private function getImageAsBase64($imagePath)
    {
        try {
            // Si c'est déjà en base64, retourner tel quel
            if (str_starts_with($imagePath, 'data:')) {
                return $imagePath;
            }
            
            if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                // C'est une URL complète
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'user_agent' => 'Mozilla/5.0'
                    ]
                ]);
                $imageContent = @file_get_contents($imagePath, false, $context);
                
                if (!$imageContent) {
                    Log::warning('Impossible de télécharger l\'image depuis l\'URL', ['url' => $imagePath]);
                    return null;
                }
                
                // Détecter le type MIME
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($imageContent);
                
                if (!$mimeType || !str_starts_with($mimeType, 'image/')) {
                    Log::warning('Type MIME invalide pour l\'image', ['mime' => $mimeType]);
                    return null;
                }
            } else {
                // C'est un chemin local - essayer plusieurs emplacements
                $paths = [
                    storage_path('app/public/' . ltrim($imagePath, '/')),
                    public_path('storage/' . ltrim($imagePath, '/')),
                    public_path(ltrim($imagePath, '/')),
                    base_path(ltrim($imagePath, '/'))
                ];
                
                $fullPath = null;
                foreach ($paths as $path) {
                    if (file_exists($path) && is_file($path)) {
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
                
                // Vérifier la taille du fichier
                $fileSize = filesize($fullPath);
                if ($fileSize > 2000000) { // Limite à 2MB
                    Log::warning('Image trop volumineuse', [
                        'path' => $imagePath,
                        'size' => $fileSize
                    ]);
                    return null;
                }
                
                $imageContent = file_get_contents($fullPath);
                if ($imageContent === false) {
                    return null;
                }
                
                $mimeType = mime_content_type($fullPath);
                if (!$mimeType || !str_starts_with($mimeType, 'image/')) {
                    return null;
                }
            }
            
            return 'data:' . $mimeType . ';base64,' . base64_encode($imageContent);
            
        } catch (\Exception $e) {
            Log::error('Erreur conversion image en base64', [
                'path' => $imagePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}