<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Billet - {{ $event->title ?? 'Événement' }}</title>
    <style>
        @page {
            size: 170mm 62mm landscape;
            margin: 0;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #000000;
        }
        
        .ticket-container {
            width: 170mm;
            height: 62mm;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        
        .ticket-table {
            width: 100%;
            height: 62mm;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            border: none;
        }
        
        .left-cell {
            width: 119mm;
            height: 62mm;
            background-color: #0f3460;
            padding: 0;
            vertical-align: top;
            border: none;
        }
        
        .right-cell {
            width: 51mm;
            height: 62mm;
            background-color: #ffffff;
            padding: 8px;
            vertical-align: middle;
            border-left: 2px dashed #1a5490;
            text-align: center;
        }
        
        .header-table {
            width: 100%;
            background-color: #16213e;
            border-collapse: collapse;
        }
        
        .header-table td {
            padding: 8px 15px;
            color: #ffffff;
            font-size: 10px;
            vertical-align: middle;
        }
        
        .venue-name {
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            color: #5dade2;
        }
        
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .content-table td {
            padding: 10px 15px;
            vertical-align: middle;
        }
        
        .event-title {
            color: #5dade2;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            text-align: center;
        }
        
        .footer-table {
            width: 100%;
            background-color: #16213e;
            border-collapse: collapse;
        }
        
        .footer-table td {
            padding: 10px 15px;
            color: #ffffff;
            font-size: 12px;
            vertical-align: middle;
        }
        
        .date-value {
            font-size: 14px;
            font-weight: bold;
        }
        
        .time-value {
            font-size: 12px;
            color: #85c1e9;
        }
        
        .price-amount {
            font-size: 18px;
            font-weight: bold;
        }
        
        .price-currency {
            font-size: 12px;
            color: #5dade2;
        }
        
        .qr-wrapper {
            background: #ffffff;
            padding: 8px;
            border: 3px solid #0f3460;
            display: inline-block;
            margin-bottom: 8px;
        }
        
        .qr-code {
            width: 90px;
            height: 90px;
        }
        
        .ticket-badge {
            background: #0f3460;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            padding: 6px 12px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 8px;
        }
        
        .scan-instruction {
            background: #0f3460;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            padding: 4px 10px;
            display: inline-block;
            margin-bottom: 6px;
        }
        
        .reference-number {
            color: #0f3460;
            font-size: 10px;
            font-weight: bold;
            margin-top: 6px;
        }
        
        .ref-label {
            color: #1a5490;
            font-size: 8px;
        }
        
        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <table class="ticket-table" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <!-- Partie gauche -->
                <td class="left-cell" valign="top" style="background-color: #0f3460;">
                    <table style="width: 100%; height: 100%; background-color: #0f3460;" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td valign="top" style="padding: 0; background-color: #0f3460;">
                                <!-- Header -->
                                <table class="header-table" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="width: 50%;">
                                            @php
                                                $venueName = strtoupper($event->lieu ?? $event->location ?? 'LIEU DE L\'ÉVÉNEMENT');
                                                if (mb_strlen($venueName) > 30) {
                                                    $venueName = mb_substr($venueName, 0, 30) . '...';
                                                }
                                                $venueAddress = $event->ville ?? $event->city ?? 'Ville';
                                                if (isset($event->adresse) && $event->adresse) {
                                                    $fullAddress = $venueAddress . ' • ' . $event->adresse;
                                                    if (mb_strlen($fullAddress) > 50) {
                                                        $venueAddress = mb_substr($fullAddress, 0, 50) . '...';
                                                    } else {
                                                        $venueAddress = $fullAddress;
                                                    }
                                                }
                                            @endphp
                                            <div class="venue-name">{{ $venueName }}</div>
                                            <div style="font-size: 9px; color: #ffffff;">
                                                {{ $venueAddress }}
                                            </div>
                                        </td>
                                        <td style="width: 50%; text-align: right;">
                                            @php
                                                $eventTitleHeader = strtoupper($event->title ?? $event->nom ?? 'ÉVÉNEMENT');
                                                if (mb_strlen($eventTitleHeader) > 40) {
                                                    $eventTitleHeader = mb_substr($eventTitleHeader, 0, 40) . '...';
                                                }
                                            @endphp
                                            <div style="font-size: 11px; color: #ffffff; font-weight: bold;">
                                                {{ $eventTitleHeader }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                
                                <!-- Titre principal -->
                                <table class="content-table" cellpadding="0" cellspacing="0" border="0" style="height: 100%;">
                                    <tr>
                                        <td style="text-align: center; padding: 15px;">
                                            @php
                                                $eventTitle = strtoupper($event->title ?? $event->nom ?? 'ÉVÉNEMENT');
                                                $titleLength = mb_strlen($eventTitle);
                                                if ($titleLength > 50) {
                                                    $eventTitle = mb_substr($eventTitle, 0, 50) . '...';
                                                }
                                                $titleSize = $titleLength > 35 ? '20px' : ($titleLength > 25 ? '22px' : '24px');
                                            @endphp
                                            <div class="event-title" style="font-size: {{ $titleSize }};">
                                                {{ $eventTitle }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                
                                <!-- Footer -->
                                <table class="footer-table" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="width: 50%; text-align: left;">
                                            @php
                                                $startDate = $event->start_date ?? $event->startDate ?? now();
                                                if (is_string($startDate)) {
                                                    try {
                                                        $startDate = \Carbon\Carbon::parse($startDate);
                                                    } catch (\Exception $e) {
                                                        $startDate = now();
                                                    }
                                                }
                                                $day = $startDate->format('d');
                                                try {
                                                    $month = strtoupper($startDate->locale('fr')->format('M'));
                                                } catch (\Exception $e) {
                                                    $month = strtoupper($startDate->format('M'));
                                                }
                                                $year = $startDate->format('Y');
                                                $time = $startDate->format('H\hi');
                                            @endphp
                                            <div class="date-value">{{ $day }} {{ $month }} {{ $year }}</div>
                                            <div class="time-value">{{ $time }}</div>
                                        </td>
                                        <td style="width: 50%; text-align: right;">
                                            @php
                                                $priceValue = $ticketPriceRaw ?? (isset($ticket) && isset($ticket->prix) ? $ticket->prix : 0);
                                                if (is_string($priceValue)) {
                                                    $priceOnly = preg_replace('/[^0-9]/', '', $priceValue);
                                                    $priceValue = (int)$priceOnly;
                                                }
                                                if (!is_numeric($priceValue)) {
                                                    $priceValue = 0;
                                                }
                                                $priceFormatted = number_format((float)$priceValue, 0, ' ', ' ');
                                            @endphp
                                            <span class="price-amount">{{ $priceFormatted }}</span>
                                            <span class="price-currency">FCFA</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                
                <!-- Partie droite -->
                <td class="right-cell" valign="middle">
                    <table style="width: 100%;" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="text-align: center;">
                                <div class="ticket-badge">{{ $ticketType ?? 'BILLET' }}</div>
                                
                                <div class="qr-wrapper">
                                    @if(!empty($qrCodeUrl))
                                        <img src="{{ $qrCodeUrl }}" class="qr-code" alt="QR Code" />
                                    @else
                                        <div style="width: 90px; height: 90px; background:#f0f0f0; color:#999; text-align:center; line-height:90px; font-size:12px;">QR</div>
                                    @endif
                                </div>
                                
                                <div class="scan-instruction">SCANNER POUR VALIDER</div>
                                
                                <div class="reference-number">
                                    <span class="ref-label">RÉFÉRENCE</span>
                                    <div>#{{ $payment->matricule ?? $payment->id ?? '001' }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>