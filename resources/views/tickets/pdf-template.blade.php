<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@page { 
    size: 170mm 62mm landscape; 
    margin: 0; 
}
body { 
    margin: 0; 
    padding: 0; 
    font-family: 'DejaVu Sans', 'Arial', 'Helvetica', sans-serif; 
    background: #fff; 
    width: 170mm;
    height: 62mm;
}

.container {
    width: 170mm;
    height: 62mm;
    position: relative;
    background: #ffffff;
    overflow: hidden;
    page-break-inside: avoid;
    page-break-after: avoid;
}

/* PARTIE GAUCHE - Dégradé bleu */
.left {
    position: absolute;
    left: 0;
    top: 0;
    width: 119mm;
    height: 62mm;
    background: #0f3460;
    overflow: hidden;
}

/* Image de fond */
.bg-image {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.25;
}

/* Overlay dégradé bleu */
.overlay {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: #0f3460;
    opacity: 0.85;
    z-index: 1;
}

.content {
    position: relative;
    z-index: 2;
    width: 100%;
    height: 100%;
}

/* HEADER - Lieu de l'événement */
.header-bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    background: #16213e;
    padding: 8px 15px;
    border-bottom: 2px solid #1a5490;
    display: table;
    width: 100%;
}

.header-content {
    display: table-row;
}

.venue-info {
    display: table-cell;
    vertical-align: middle;
    width: 50%;
    color: #ffffff;
    font-size: 10px;
    line-height: 1.4;
}

.event-title-header {
    display: table-cell;
    vertical-align: middle;
    width: 50%;
    text-align: right;
    padding-left: 10px;
}

.event-title-small {
    color: #ffffff;
    font-size: 13px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    line-height: 1.3;
}

.event-title-small.medium-title {
    font-size: 11px;
}

.event-title-small.small-title {
    font-size: 9px;
}

.venue-name {
    font-weight: 900;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #5dade2;
    margin-bottom: 2px;
    overflow: hidden;
    word-wrap: break-word;
}

.venue-info div {
    overflow: hidden;
    word-wrap: break-word;
}

/* Zone centrale - Titre et infos */
.center-zone {
    position: absolute;
    left: 15px;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
}

/* Titre principal */
.title {
    color: #ffffff;
    font-family: 'DejaVu Sans', 'Arial Black', sans-serif;
    font-size: 28px;
    font-weight: 900;
    text-transform: uppercase;
    line-height: 1.2;
    letter-spacing: 1.5px;
    text-align: center;
    margin-bottom: 15px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    max-width: 100%;
    overflow: hidden;
    word-wrap: break-word;
}

/* Titre long - réduction automatique */
.title.long-title {
    font-size: 22px;
    letter-spacing: 1px;
    line-height: 1.15;
}

.title.very-long-title {
    font-size: 18px;
    letter-spacing: 0.5px;
    line-height: 1.1;
}

/* Informations événement */
.event-info {
    display: table;
    width: 100%;
    margin-top: 12px;
}

.info-row {
    display: table-row;
}

.info-cell {
    display: table-cell;
    vertical-align: middle;
    padding: 8px 0;
}

.info-cell.date {
    width: 45%;
    text-align: left;
    padding-left: 15px;
}

.info-cell.price {
    width: 55%;
    text-align: right;
    padding-right: 15px;
}

/* Style date */
.date-container {
    display: inline-block;
    background: rgba(255,255,255,0.1);
    padding: 8px 15px;
    border-radius: 8px;
    border-left: 4px solid #5dade2;
}

.date-row {
    display: block;
    margin: 2px 0;
}

.date-label {
    color: #85c1e9;
    font-size: 8px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.date-value {
    color: #ffffff;
    font-size: 14px;
    font-weight: 900;
    letter-spacing: 0.5px;
}

.time-value {
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
}

/* Style prix */
.price-container {
    display: inline-block;
    background: #1a5490;
    padding: 10px 20px;
    border-radius: 8px;
    border: 2px solid #5dade2;
}

.price-label {
    color: #85c1e9;
    font-size: 9px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
}

.price-amount {
    color: #ffffff;
    font-size: 22px;
    font-weight: 900;
    letter-spacing: 1px;
}

.price-currency {
    color: #5dade2;
    font-size: 11px;
    font-weight: bold;
    margin-left: 3px;
}

/* FOOTER - Type de billet */
.footer-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: #16213e;
    padding: 10px 15px;
    border-top: 2px solid #1a5490;
}

.ticket-type-badge {
    display: inline-block;
    background: #5dade2;
    color: #0f3460;
    font-size: 11px;
    font-weight: 900;
    padding: 6px 16px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

/* PARTIE DROITE - Section QR Code */
.right {
    position: absolute;
    left: 119mm;
    top: 0;
    width: 51mm;
    height: 62mm;
    background: #ffffff;
    border-left: 2px dashed #1a5490;
    overflow: hidden;
}

/* Image de fond droite */
.right-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.08;
    z-index: 1;
}

.right-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: table;
    width: 100%;
}

.right-inner {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    padding: 12px;
}

/* Badge type de billet en haut */
.ticket-badge-top {
    background: #0f3460;
    color: #ffffff;
    font-size: 12px;
    font-weight: 900;
    padding: 8px 16px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    display: inline-block;
    margin-bottom: 12px;
}

/* QR Code */
.qr-wrapper {
    background: #ffffff;
    padding: 10px;
    border-radius: 12px;
    border: 3px solid #0f3460;
    display: inline-block;
    margin: 0 auto 10px auto;
}

.qr {
    width: 90px;
    height: 90px;
    display: block;
}

/* Scan instruction */
.scan-instruction {
    background: #0f3460;
    color: #ffffff;
    font-size: 9px;
    font-weight: bold;
    padding: 5px 12px;
    border-radius: 15px;
    display: inline-block;
    margin-bottom: 8px;
    letter-spacing: 0.8px;
}

/* Numéro de référence */
.reference-number {
    color: #0f3460;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 1px;
    margin-top: 6px;
}

.ref-label {
    color: #1a5490;
    font-size: 8px;
    display: block;
    margin-bottom: 2px;
}

/* Décoration - coins arrondis visuels */
.corner-decoration {
    position: absolute;
    width: 12px;
    height: 12px;
    background: #ffffff;
    border-radius: 50%;
}

.corner-top-left {
    top: -6px;
    left: 119mm;
    margin-left: -6px;
}

.corner-bottom-left {
    bottom: -6px;
    left: 119mm;
    margin-left: -6px;
}

</style>
</head>
<body>
@foreach($ticketsData as $ticketData)
    @php
        $event = $ticketData['event'] ?? null;
        $payment = $ticketData['payment'] ?? null;
        $eventImageUrl = $ticketData['eventImageUrl'] ?? null;
        $qrCodeUrl = $ticketData['qrCodeUrl'] ?? null;
        $ticketType = $ticketData['ticketType'] ?? 'BILLET';
        $ticketPrice = $ticketData['ticketPrice'] ?? 0;
        $fouleImageUrl = $ticketData['fouleImageUrl'] ?? null;
    @endphp
    
    @if($event)
    <div class="container">
        <!-- Coins décoratifs -->
        <div class="corner-decoration corner-top-left"></div>
        <div class="corner-decoration corner-bottom-left"></div>
        
        <div class="left">
            <!-- Image de fond -->
            @if(!empty($eventImageUrl) && $eventImageUrl !== '' && str_starts_with($eventImageUrl, 'data:'))
            <img src="{{ $eventImageUrl }}" class="bg-image" alt="Event Background">
            @endif
            
            <div class="overlay"></div>
            
            <div class="content">
                <!-- Header - Lieu et Titre -->
                <div class="header-bar">
                    <div class="header-content">
                        <div class="venue-info">
                            @php
                                $venueName = strtoupper($event->lieu ?? 'LIEU DE L\'ÉVÉNEMENT');
                            // Tronquer le nom du lieu si trop long
                            if (mb_strlen($venueName) > 25) {
                                $venueName = mb_substr($venueName, 0, 25) . '...';
                            }
                            
                            $venueAddress = $event->ville ?? 'Ville';
                            if ($event->adresse) {
                                $fullAddress = $venueAddress . ' • ' . $event->adresse;
                                // Tronquer l'adresse complète si trop longue
                                if (mb_strlen($fullAddress) > 40) {
                                    $venueAddress = mb_substr($fullAddress, 0, 40) . '...';
                                } else {
                                    $venueAddress = $fullAddress;
                                }
                            }
                        @endphp
                        
                        <div class="venue-name">{{ $venueName }}</div>
                        <div>{{ $venueAddress }}</div>
                    </div>
                    
                    <div class="event-title-header">
                        @php
                            $eventTitleHeader = strtoupper($event->title ?? 'ÉVÉNEMENT');
                            $titleHeaderLength = mb_strlen($eventTitleHeader);
                            $titleHeaderClass = 'event-title-small';
                            
                            if ($titleHeaderLength > 40) {
                                $titleHeaderClass .= ' small-title';
                                $eventTitleHeader = mb_substr($eventTitleHeader, 0, 35) . '...';
                            } elseif ($titleHeaderLength > 25) {
                                $titleHeaderClass .= ' medium-title';
                            }
                        @endphp
                        
                        <div class="{{ $titleHeaderClass }}">{{ $eventTitleHeader }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Zone centrale - Titre -->
            <div class="center-zone">
                @php
                    $eventTitle = strtoupper($event->title ?? 'ÉVÉNEMENT');
                    $titleLength = mb_strlen($eventTitle);
                    $titleClass = 'title';
                    
                    // Gestion adaptative de la taille selon la longueur
                    if ($titleLength > 50) {
                        $titleClass .= ' very-long-title';
                        $eventTitle = mb_substr($eventTitle, 0, 65) . '...';
                    } elseif ($titleLength > 35) {
                        $titleClass .= ' long-title';
                    }
                @endphp
                
                <div class="{{ $titleClass }}">{{ $eventTitle }}</div>
                
                <!-- Informations structurées -->
                <div class="event-info">
                    <div class="info-row">
                        <!-- Date et heure -->
                        <div class="info-cell date">
                            <div class="date-container">
                                @php
                                    $day = $event->start_date ? $event->start_date->format('d') : '31';
                                    $month = $event->start_date ? strtoupper($event->start_date->locale('fr')->format('M')) : 'OCT';
                                    $year = $event->start_date ? $event->start_date->format('Y') : '2025';
                                    $time = $event->start_date ? $event->start_date->format('H\hi') : '20h00';
                                @endphp
                                
                                <div class="date-row">
                                    <span class="date-label">DATE</span>
                                    <span class="date-value"> {{ $day }} {{ $month }} {{ $year }}</span>
                                </div>
                                <div class="date-row">
                                    <span class="date-label">HEURE</span>
                                    <span class="time-value"> {{ $time }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Prix -->
                        <div class="info-cell price">
                            <div class="price-container">
                                @php
                                    $priceOnly = preg_replace('/[^0-9]/', '', $ticketPrice ?? '0');
                                    $priceFormatted = number_format((int)$priceOnly, 0, ' ', ' ');
                                @endphp
                                
                                <div class="price-label">TARIF</div>
                                <div>
                                    <span class="price-amount">{{ $priceFormatted }}</span>
                                    <span class="price-currency">FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer - Date et Prix -->
            <div class="footer-bar">
                <div class="footer-content">
                    <div class="footer-cell left">
                        @php
                            $day = $event->start_date ? $event->start_date->format('d') : '31';
                            $month = $event->start_date ? strtoupper($event->start_date->locale('fr')->format('M')) : 'OCT';
                            $year = $event->start_date ? $event->start_date->format('Y') : '2025';
                            $time = $event->start_date ? $event->start_date->format('H\hi') : '20h00';
                        @endphp
                        
                        <div class="footer-date">
                            <span class="footer-date-text">{{ $day }} {{ $month }} {{ $year }}</span>
                            <span class="footer-date-time">{{ $time }}</span>
                        </div>
                    </div>
                    
                    <div class="footer-cell right">
                        @php
                            $priceOnly = preg_replace('/[^0-9]/', '', $ticketPrice ?? '0');
                            $priceFormatted = number_format((int)$priceOnly, 0, ' ', ' ');
                        @endphp
                        
                        <div class="footer-price">
                            <span class="footer-price-amount">{{ $priceFormatted }}</span>
                            <span class="footer-price-currency">FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="right">
        <!-- Image de foule en arrière-plan -->
        @if(!empty($fouleImageUrl))
        <img src="{{ $fouleImageUrl }}" class="right-bg" alt="Background">
        @endif
        
        <div class="right-content">
            <div class="right-inner">
                <!-- Badge type de billet -->
                @php
                    $ticketTypeBadge = $ticketType ?? 'BILLET';
                    // Tronquer le badge si trop long
                    if (mb_strlen($ticketTypeBadge) > 12) {
                        $ticketTypeBadge = mb_substr($ticketTypeBadge, 0, 12) . '...';
                    }
                @endphp
                <div class="ticket-badge-top">{{ $ticketTypeBadge }}</div>
                
                <!-- QR Code -->
                <div class="qr-wrapper">
                    @if(!empty($qrCodeUrl))
                    <img src="{{ $qrCodeUrl }}" class="qr" alt="QR Code">
                    @else
                    <div class="qr" style="background:#f0f0f0; display:table-cell; vertical-align:middle; text-align:center; font-size:18px; color:#999;">QR</div>
                    @endif
                </div>
                
                <!-- Numéro de référence -->
                <div class="reference-number">
                    <span class="ref-label">RÉFÉRENCE</span>
                    <div>#{{ $payment->matricule ?? '001' }}</div>
                </div>
                
                <!-- Instruction scan -->
                <div class="scan-instruction">SCANNER POUR VALIDER</div>
            </div>
        </div>
    </div>
    </div>
    @endif
@endforeach
</body>
</html>