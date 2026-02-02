<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de votre réservation</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <div style="background-color: #4CAF50; color: white; padding: 10px; text-align: center;">
            <h1>Confirmation de votre réservation</h1>
        </div>
        
        <div style="padding: 20px;">
            <p>Bonjour {{ $user->name ?? 'client' }},</p>
            
            @if(isset($event))
                <p>Nous vous confirmons votre réservation pour l'événement : <strong>{{ $event->titre ?? 'Événement' }}</strong></p>
                
                <h3>Détails de votre commande :</h3>
                <p>Date : {{ $event->date_debut->format('d/m/Y H:i') ?? '' }}</p>
                <p>Lieu : {{ $event->lieu ?? '' }}</p>
            @endif
            
            @if(isset($order))
                <p>Référence : <strong>{{ $order->reference ?? '' }}</strong></p>
                
                @if(isset($order->tickets) && $order->tickets->count() > 0)
                    <h4>Billets :</h4>
                    <ul>
                        @foreach($order->tickets as $ticket)
                            <li>
                                {{ $ticket->nom ?? 'Billet' }} x {{ $ticket->pivot->quantity ?? 1 }} - 
                                {{ number_format(($ticket->pivot->unit_price ?? 0) * ($ticket->pivot->quantity ?? 1), 0, ',', ' ') }} FCFA
                            </li>
                        @endforeach
                    </ul>
                @endif
                
                <p>Montant total : <strong>{{ number_format($order->total ?? 0, 0, ',', ' ') }} FCFA</strong></p>
                
                @if(($order->statut ?? '') === 'payé')
                    <p>Votre paiement a bien été reçu. Vous pouvez télécharger vos billets en cliquant sur le bouton ci-dessous :</p>
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{{ route('tickets.download', $order->id) }}" 
                           style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                            Télécharger mes billets
                        </a>
                    </div>
                @else
                    <p>Votre commande est enregistrée. Veuillez finaliser le paiement pour recevoir vos billets.</p>
                @endif
            @endif
        </div>
        
        <div style="margin-top: 20px; font-size: 0.9em; text-align: center; color: #666;">
            <p>Merci pour votre confiance !</p>
            <p>L'équipe {{ config('app.name', 'MokiliEvent') }}</p>
        </div>
    </div>
</body>
</html>