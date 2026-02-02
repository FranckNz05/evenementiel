<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Billet VIP - {{ $event->title }}</title>
    <style>
        @page {
            size: {{ $dimensions['width'] }} {{ $dimensions['height'] }};
            margin: 0;
        }
        body {
            width: {{ $dimensions['width'] }};
            height: {{ $dimensions['height'] }};
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            overflow: hidden;
            background: white;
            position: relative;
        }

        .content-rotated {
            transform: rotate(90deg);
            transform-origin: left top;
            width: {{ $dimensions['height'] }};
            height: {{ $dimensions['width'] }};
            position: absolute;
            left: {{ $dimensions['width'] }};
            top: 0;
        }

        .ticket-container {
            display: flex;
            height: 100%;
            width: 100%;
            position: relative;
            background: linear-gradient(135deg, #1A1B41 0%, #4a148c 100%);
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .ticket-info {
            width: 70%;
            padding: 5mm;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .ticket-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(26,27,65,0.8) 0%, rgba(74,20,140,0.8) 100%);
            z-index: 1;
        }

        .event-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.2;
            z-index: 0;
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3mm;
            z-index: 2;
        }

        .event-title {
            font-size: 8mm;
            font-weight: 800;
            color: #FFD700;
            line-height: 1.1;
            text-transform: uppercase;
            margin: 0;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }

        .event-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 3mm;
            z-index: 2;
            margin: 3mm 0;
            font-size: 4mm;
        }

        .detail-item {
            display: flex;
            align-items: center;
        }

        .detail-icon {
            color: #FFD700;
            margin-right: 2mm;
            font-size: 5mm;
            min-width: 6mm;
            text-align: center;
        }

        .sponsors-container {
            display: flex;
            flex-wrap: wrap;
            gap: 2mm;
            margin-top: 3mm;
            align-items: center;
            justify-content: center;
        }

        .sponsor-logo {
            max-height: 8mm;
            max-width: 20mm;
            object-fit: contain;
        }

        .ticket-qr {
            width: 30%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5mm;
            background: white;
            border-left: 2px dashed #FFD700;
        }

        .qr-code {
            width: 40mm;
            height: 40mm;
            border: 2px solid #FFD700;
            padding: 2mm;
            background: white;
        }

        .ticket-type {
            font-size: 6mm;
            font-weight: 700;
            padding: 2mm 4mm;
            border-radius: 4mm;
            text-transform: uppercase;
            margin-bottom: 3mm;
            background: #FFD700;
            color: #1A1B41;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .ticket-price {
            font-size: 5mm;
            font-weight: bold;
            margin-top: 3mm;
            color: #1A1B41;
        }

        .info-line {
            position: absolute;
            bottom: 2mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 3mm;
            color: #FFD700;
            padding: 0 5mm;
        }

        .vip-badge {
            position: absolute;
            top: 5mm;
            right: 5mm;
            background: #FFD700;
            color: #1A1B41;
            padding: 1mm 3mm;
            border-radius: 3mm;
            font-weight: bold;
            font-size: 4mm;
            z-index: 3;
        }
    </style>
</head>
<body>
    <div class="content-rotated">
        <div class="ticket-container">
            <!-- Partie gauche - Informations -->
            <div class="ticket-info">
                <div class="vip-badge">VIP</div>
                
                @if($eventImage)
                <img src="{{ $eventImage }}" class="event-image" alt="{{ $event->title }}">
                @endif
                
                <div class="event-header">
                    <h1 class="event-title">{{ Str::limit($event->title, 50) }}</h1>
                </div>
                
                <div class="event-details">
                    <div class="detail-item">
                        <i class="fas fa-calendar-alt detail-icon"></i>
                        <span>{{ $event->start_date->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt detail-icon"></i>
                        <span>{{ Str::limit($event->lieu ?? 'Lieu non spécifié', 30) }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <i class="fas fa-user-tie detail-icon"></i>
                        <span>{{ Str::limit($organizer->company_name ?? 'Organisateur', 30) }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <i class="fas fa-ticket-alt detail-icon"></i>
                        <span>{{ $ticket->nom }}</span>
                    </div>
                </div>

                @if(count($sponsorLogos) > 0)
                <div class="sponsors-container">
                    @foreach($sponsorLogos as $logo)
                    <img src="{{ $logo }}" class="sponsor-logo" alt="Sponsor">
                    @endforeach
                </div>
                @endif
                
                <div class="info-line">
                    {{ $infoLine['billetterie'] }} 
                    @if($infoLine['organizer'])
                    • {{ $infoLine['organizer'] }}
                    @endif
                </div>
            </div>

            <!-- Partie droite - QR Code -->
            <div class="ticket-qr">
                <div class="ticket-type">
                    {{ strtoupper($ticket->nom) }}
                </div>
                
                <img src="{{ $qrCode }}" class="qr-code" alt="QR Code">
                
                <div class="ticket-price">
                    {{ number_format($ticket->pivot->unit_price ?? $ticket->prix, 0, ',', ' ') }} FCFA
                </div>
            </div>
        </div>
    </div>
</body>
</html>