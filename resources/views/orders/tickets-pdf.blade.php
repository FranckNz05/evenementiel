<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Billets - reservation #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .ticket {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .ticket-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .event-title {
            font-size: 20px;
            font-weight: bold;
            color: #000;
        }
        .ticket-info {
            margin-bottom: 15px;
        }
        .qr-code {
            text-align: center;
            margin: 15px 0;
        }
        .qr-code img {
            max-width: 150px;
        }
        .ticket-footer {
            font-size: 12px;
            color: #666;
            text-align: center;
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center; margin-bottom: 30px;">Vos billets - reservation #{{ $order->id }}</h1>

    @foreach($order->tickets as $ticket)
    <div class="ticket">
        <div class="ticket-header">
            <div class="event-title">{{ $order->event->title }}</div>
            <div>{{ $ticket->type->type }}</div>
        </div>

        <div class="ticket-info">
            <p>
                <strong>Date :</strong> {{ $order->event->start_date->format('d M Y H:i') }}<br>
                <strong>Lieu :</strong> {{ $order->event->location }}<br>
                <strong>Code :</strong> {{ $ticket->code }}
            </p>
        </div>

        <div class="qr-code">
            <img src="data:image/png;base64,{{ base64_encode($ticket->qr_code) }}" alt="QR Code">
        </div>

        <div class="ticket-footer">
            <p>
                Participant : {{ $order->billing_name }}<br>
                Email : {{ $order->billing_email }}<br>
                Téléphone : {{ $order->billing_phone }}
            </p>
            <small>Ce billet est personnel et ne peut être revendu.</small>
        </div>
    </div>
    @endforeach
</body>
</html>
