<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Billet - {{ $event->title }}</title>
    <style>
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
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(135deg, #232526 0%, #1B1B3A 100%);
            color: #E0E0E0;
            width: 180mm;
            height: 70mm;
            margin: 0;
            padding: 0;
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
            box-shadow: 0 8px 24px 0 rgba(212,175,55,0.15), 0 1.5px 6px 0 rgba(0,0,0,0.25);
            border-radius: 18px;
            border: 2px solid #ffffff;
            position: relative;
            overflow: hidden;
            background: rgba(27,27,58,0.98);
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .ticket-main {
            width: 70%;
            position: relative;
            background-image: url('{{ $event->image ?? $eventImage ?? "" }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
        }
        .ticket-main::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, rgba(27, 27, 58, 0.88), rgba(27, 27, 58, 0.60), rgba(27, 27, 58, 0.40));
            z-index: 1;
        }
        .ticket-content {
            position: relative;
            z-index: 2;
            height: 100%;
            padding: 7mm 7mm 5mm 7mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .ticket-stub {
            width: 30%;
            background: linear-gradient(135deg, #ffffff 0%, #1A365D 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 7mm 5mm 5mm 5mm;
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
        }
        .ticket-divider {
            position: relative;
            height: 100%;
            width: 2.5px;
            background: repeating-linear-gradient(to bottom, transparent, transparent 10px, #ffffff 10px, #ffffff 20px);
        }
        .ticket-divider::before, .ticket-divider::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 22px;
            height: 22px;
            background-color: #232526;
            border-radius: 50%;
            border: 2.5px solid #ffffff;
        }
        .ticket-divider::before {
            top: -11px;
        }
        .ticket-divider::after {
            bottom: -11px;
        }
        .qr-code {
            background: #fffbe6;
            padding: 3px;
            width: 32mm;
            height: 32mm;
            margin: 0 auto;
            border: 2.5px solid #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(212,175,55,0.12);
        }
        .gold-text {
            color: #ffffff;
            text-shadow: 0 1px 2px #232526;
        }
        .event-title {
            font-size: 16pt;
            line-height: 1.2;
            font-weight: bold;
            letter-spacing: 1px;
            color: #ffffff;
            text-shadow: 0 1px 2px #232526;
        }
        .event-details {
            font-size: 9pt;
            color: #E0E0E0;
        }
        .ticket-type {
            font-size: 11pt;
            font-weight: bold;
            color: #ffffff;
        }
        .ticket-price {
            font-size: 18pt;
            font-weight: bold;
            color: #ffffff;
        }
        .logo {
            font-size: 12pt;
            font-weight: bold;
            color: #ffffff;
        }
        .ticket-number {
            font-size: 9pt;
            letter-spacing: 1.5px;
            color: #ffffff;
        }
        .sponsor-icon {
            width: 38px;
            height: 38px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(212,175,55,0.18);
        }
        @media print {
            body {
                background-color: transparent;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .ticket {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket">
            <!-- Partie principale du billet (gauche) -->
            <div class="ticket-main">
                <div class="ticket-content">
                    <!-- En-tête du billet -->
                    <div class="flex justify-between items-start">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mr-3 border-2 border-gold">
                                <span style="font-family: 'Pacifico', cursive; color: #1B1B3A; font-size: 12px;">ME</span>
                            </div>
                        </div>
                        <div style="background-color: rgba(0, 0, 0, 0.3); padding: 2px 12px; border-radius: 9999px; border: 1px solid rgba(212, 175, 55, 0.5);">
                            <p class="gold-text text-xs font-bold">{{ strtoupper($ticket->nom ?? 'STANDARD') }}</p>
                        </div>
                    </div>

                    <!-- Contenu principal -->
                    <div>
                        <!-- Titre de l'événement -->
                        <h1 class="event-title text-white mb-1">{{ $event->title }}</h1>

                        <!-- Détails de l'événement -->
                        <div class="space-y-2 mt-3 event-details">
                            <div class="flex items-center">
                                <span class="text-white">
                                    {{ $event->start_date->format('d M Y') }}
                                </span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-white">
                                    {{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }}
                                </span>
                            </div>
                            <div class="flex items-start">
                                <div>
                                    <p class="text-white">{{ $event->lieu }}</p>
                                    <p class="text-gray-300 text-sm">{{ $event->adresse }}, {{ $event->ville }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pied de page -->
                    <div class="flex justify-between items-center mt-2">
                        <div class="text-white text-xs">
                            <span class="gold-text">Ref:</span> {{ $payment->matricule }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Séparateur -->
            <div class="ticket-divider"></div>

            <!-- Partie technique du billet (droite) -->
            <div class="ticket-stub">
                <div class="h-full flex flex-col justify-between">
                    <div>
                        <div class="text-center mb-3">
                            <p class="text-white font-bold text-lg">BILLET {{ strtoupper($ticket->nom) }}</p>
                            <p class="gold-text text-xs">{{ $ticket->description }}</p>
                        </div>

                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <p class="gold-text text-xs mb-1">PRIX</p>
                                <p class="text-white font-bold text-xl">{{ number_format($ticket->prix, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="text-right">
                                <p class="gold-text text-xs mb-1">PLACE</p>
                                <p class="text-white font-bold">A{{ $index ?? '12' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="qr-code mb-2">
                            <img src="{{ $qrCode }}" alt="QR Code" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <p class="text-center text-white text-xs mb-1">Scannez pour valider votre entrée</p>
                        <p class="text-center text-white font-mono text-xs">{{ $payment->matricule }}-{{ $index ?? '1' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
