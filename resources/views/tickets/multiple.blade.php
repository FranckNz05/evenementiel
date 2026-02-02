<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Billets - {{ $event->title }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Playfair+Display:wght@700;900&display=swap');
        
        @page {
            size: 180mm 70mm landscape;
            margin: 0;
        }
        @page :first {
            margin: 0;
        }
        @page :left {
            margin: 0;
        }
        @page :right {
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            margin: 0;
            padding: 0;
        }
        
        .ticket-page {
            width: 180mm;
            height: 70mm;
            position: relative;
            page-break-inside: avoid;
            break-inside: avoid;
            margin: 0;
            padding: 0;
        }
        
        .ticket-page:not(:last-child) {
            page-break-after: always;
        }
        
        .ticket-page:last-child {
            page-break-after: avoid;
        }
        
        .ticket-container {
            width: 180mm;
            height: 70mm;
            position: relative;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        
        .ticket {
            display: flex;
            height: 100%;
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            position: relative;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        
        /* Effet de fond animé */
        .ticket::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 25px 25px;
            opacity: 0.4;
            pointer-events: none;
        }
        
        /* Partie principale (gauche) */
        .ticket-main {
            width: 68%;
            position: relative;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 2;
        }
        
        .ticket-main::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, 
                rgba(15, 12, 41, 0.95) 0%, 
                rgba(15, 12, 41, 0.85) 40%,
                rgba(15, 12, 41, 0.75) 70%,
                rgba(15, 12, 41, 0.5) 100%
            );
            backdrop-filter: blur(2px);
            z-index: 1;
        }
        
        .ticket-content {
            position: relative;
            z-index: 2;
            height: 100%;
            padding: 22px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        /* En-tête */
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        
        .logo-circle {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #ffd89b 0%, #fc6d47 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            color: white;
            box-shadow: 0 6px 20px rgba(252, 109, 71, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .logo-circle::before {
            content: '';
            position: absolute;
            width: 150%;
            height: 150%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%) translateY(-100%) rotate(0deg); }
            50% { transform: translateX(100%) translateY(100%) rotate(360deg); }
        }
        
        .ticket-badge {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(10px);
            padding: 7px 18px;
            border-radius: 50px;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .ticket-badge-text {
            font-weight: 800;
            font-size: 10px;
            letter-spacing: 2px;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        /* Titre de l'événement */
        .event-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -0.5px;
            color: #ffffff;
            text-shadow: 0 3px 15px rgba(0, 0, 0, 0.5);
            margin-bottom: 12px;
            margin-top: 6px;
        }
        
        /* Détails de l'événement */
        .event-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .detail-row {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.95);
            font-size: 12px;
            font-weight: 500;
        }
        
        .detail-icon {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.35), rgba(118, 75, 162, 0.35));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .detail-location {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 2px;
        }
        
        /* Pied de page */
        .ticket-footer {
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .sponsors-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
        }
        
        .sponsor-logo {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            background: white;
            padding: 3px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .organizer-logo {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            background: white;
            padding: 4px;
            border: 2.5px solid rgba(255, 215, 0, 0.6);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.4);
        }
        
        .reference-text {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.6);
            font-family: 'Courier New', monospace;
            text-align: center;
            letter-spacing: 0.5px;
        }
        
        .reference-label {
            color: rgba(255, 215, 0, 0.9);
            font-weight: 600;
        }
        
        /* Séparateur */
        .ticket-divider {
            width: 3px;
            position: relative;
            background: linear-gradient(to bottom, 
                transparent 0%, 
                transparent 8%,
                rgba(255, 255, 255, 0.35) 8%, 
                rgba(255, 255, 255, 0.35) 15%,
                transparent 15%,
                transparent 23%,
                rgba(255, 255, 255, 0.35) 23%,
                rgba(255, 255, 255, 0.35) 30%
            );
            background-size: 100% 20px;
            z-index: 3;
        }
        
        .ticket-divider::before,
        .ticket-divider::after {
            content: '';
            position: absolute;
            width: 28px;
            height: 28px;
            background: #0f0c29;
            border-radius: 50%;
            left: 50%;
            transform: translateX(-50%);
            border: 3px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 0 0 6px rgba(102, 126, 234, 0.1);
        }
        
        .ticket-divider::before {
            top: -14px;
        }
        
        .ticket-divider::after {
            bottom: -14px;
        }
        
        /* Partie stub (droite) */
        .ticket-stub {
            width: 32%;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.98) 0%, 
                rgba(240, 242, 255, 0.98) 100%
            );
            backdrop-filter: blur(10px);
            padding: 22px 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        /* Effet holographique */
        .ticket-stub::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                135deg,
                rgba(102, 126, 234, 0.08) 0%,
                transparent 50%,
                rgba(118, 75, 162, 0.08) 100%
            );
            pointer-events: none;
        }
        
        .stub-header {
            text-align: center;
            width: 100%;
        }
        
        .stub-title {
            font-weight: 800;
            font-size: 15px;
            color: #667eea;
            letter-spacing: 1.5px;
            margin-bottom: 3px;
        }
        
        .stub-subtitle {
            font-size: 9px;
            color: #764ba2;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        /* Prix et Place */
        .price-section {
            display: flex;
            justify-content: space-around;
            width: 100%;
            margin: 14px 0;
            gap: 8px;
        }
        
        .price-box,
        .seat-box {
            text-align: center;
            flex: 1;
        }
        
        .price-label,
        .seat-label {
            font-size: 8px;
            color: #667eea;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 4px;
        }
        
        .price-value {
            font-size: 17px;
            font-weight: 800;
            color: #764ba2;
            line-height: 1.1;
        }
        
        .price-currency {
            font-size: 10px;
            font-weight: 700;
        }
        
        .seat-value {
            font-size: 22px;
            font-weight: 800;
            color: #667eea;
            line-height: 1;
        }
        
        /* QR Code */
        .qr-section {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .qr-code {
            width: 110px;
            height: 110px;
            background: white;
            border-radius: 14px;
            padding: 8px;
            box-shadow: 
                0 10px 30px rgba(102, 126, 234, 0.25),
                0 0 0 3px rgba(102, 126, 234, 0.1);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .qr-instruction {
            font-size: 9px;
            color: #667eea;
            text-align: center;
            margin-bottom: 6px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        
        .ticket-code {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            color: #764ba2;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        @media print {
            body {
                background: white;
            }
            .ticket {
                box-shadow: none;
            }
            .ticket-page:not(:last-child) {
                page-break-after: always;
            }
            .ticket-page:last-child {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    @foreach($tickets as $ticketData)
        @if(isset($ticketData['ticket']))
            @php
                $ticket = $ticketData['ticket'];
                $qrCode = $ticketData['qrCode'];
                $ticketIndex = $ticketData['index'];
            @endphp
        @elseif(isset($ticketData['ticketType']))
            @php
                $priceString = $ticketData['ticketPrice'] ?? '0';
                $priceNumeric = (float) preg_replace('/[^0-9.]/', '', $priceString);
                
                $ticket = (object) [
                    'nom' => $ticketData['ticketType'] ?? 'STANDARD',
                    'description' => 'Billet d\'événement',
                    'prix' => $priceNumeric
                ];
                $qrCode = $ticketData['qrCode'] ?? '';
                $ticketIndex = $ticketData['currentTicket'] ?? 1;
            @endphp
        @else
            @php
                $ticket = $ticketData;
                $qrCode = $ticketData->qrCode ?? '';
                $ticketIndex = $ticketData->currentTicket ?? 1;
            @endphp
        @endif
        
        <div class="ticket-page">
            <div class="ticket-container">
                <div class="ticket">
                    <!-- Partie principale (gauche) -->
                    <div class="ticket-main" @if(isset($ticketData['eventImage']) && $ticketData['eventImage']) style="background-image: url('{{ $ticketData['eventImage'] }}');" @endif>
                        <div class="ticket-content">
                            <div>
                                <!-- En-tête -->
                                <div class="ticket-header">
                                    <div class="logo-circle">ME</div>
                                    <div class="ticket-badge">
                                        <span class="ticket-badge-text">{{ strtoupper($ticket->nom ?? 'STANDARD') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Titre -->
                                <h1 class="event-title">{{ $event->title }}</h1>
                                
                                <!-- Détails -->
                                <div class="event-details">
                                    <div class="detail-row">
                                        <div class="detail-icon">📅</div>
                                        <strong>{{ $event->start_date->format('d M Y') }}</strong>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-icon">🕐</div>
                                        <span>{{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-icon">📍</div>
                                        <div>
                                            <strong>{{ $event->lieu }}</strong>
                                            <div class="detail-location">{{ $event->adresse }}, {{ $event->ville }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pied de page -->
                            <div class="ticket-footer">
                                @if((isset($ticketData['sponsorLogos']) && count($ticketData['sponsorLogos']) > 0) || isset($ticketData['organizerLogo']))
                                <div class="sponsors-section">
                                    @php
                                        $sponsors = $ticketData['sponsorLogos'] ?? [];
                                        $organizerLogo = $ticketData['organizerLogo'] ?? null;
                                        $firstSponsors = array_slice($sponsors, 0, 2);
                                        $lastSponsors = array_slice($sponsors, 2, 2);
                                    @endphp
                                    
                                    @foreach($firstSponsors as $sponsorLogo)
                                        <img src="{{ $sponsorLogo }}" alt="Sponsor" class="sponsor-logo">
                                    @endforeach
                                    
                                    @if($organizerLogo)
                                        <img src="{{ $organizerLogo }}" alt="Organisateur" class="organizer-logo">
                                    @endif
                                    
                                    @foreach($lastSponsors as $sponsorLogo)
                                        <img src="{{ $sponsorLogo }}" alt="Sponsor" class="sponsor-logo">
                                    @endforeach
                                </div>
                                @endif
                                
                                <div class="reference-text">
                                    <span class="reference-label">REF:</span> {{ $payment->matricule }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Séparateur -->
                    <div class="ticket-divider"></div>
                    
                    <!-- Partie stub (droite) -->
                    <div class="ticket-stub">
                        <div class="stub-header">
                            <div class="stub-title">{{ strtoupper($ticket->nom) }}</div>
                            <div class="stub-subtitle">{{ $ticket->description }}</div>
                        </div>
                        
                        <div class="price-section">
                            <div class="price-box">
                                <div class="price-label">Prix</div>
                                <div class="price-value">
                                    {{ number_format((float)$ticket->prix, 0, ',', ' ') }}
                                    <span class="price-currency">FCFA</span>
                                </div>
                            </div>
                            <div class="seat-box">
                                <div class="seat-label">Place</div>
                                <div class="seat-value">A{{ $ticketIndex ?? '12' }}</div>
                            </div>
                        </div>
                        
                        <div class="qr-section">
                            <div class="qr-code">
                                <img src="{{ $qrCode }}" alt="QR Code">
                            </div>
                            <div class="qr-instruction">Scannez pour valider votre entrée</div>
                            <div class="ticket-code">{{ $payment->matricule }}-{{ $ticketIndex ?? '1' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>