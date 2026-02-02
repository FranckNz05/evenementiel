@component('mail::message')
# 🎉 Paiement confirmé !

Bonjour **{{ $payment->user->prenom }} {{ $payment->user->nom }}**,

Nous vous confirmons que votre paiement pour l'événement **{{ $event ? $event->title : 'Événement' }}** a bien été effectué avec succès.

## 🧾 Détails de la transaction

- **Référence :** {{ $payment->matricule }}
- **Montant :** {{ number_format($payment->montant, 0, ',', ' ') }} FCFA
- **Date :** {{ $payment->date_paiement ? $payment->date_paiement->format('d/m/Y à H:i') : $payment->created_at->format('d/m/Y à H:i') }}
- **Méthode :** {{ ucfirst($payment->methode_paiement) }}

@if($event)
## 📅 Détails de l'événement

- **Événement :** {{ $event->title }}
- **Date :** {{ $event->start_date->format('d/m/Y à H:i') }}
- **Lieu :** {{ $event->lieu ?? $event->ville }}
@if($event->adresse)
- **Adresse :** {{ $event->adresse }}
@endif
@endif

## 🎟️ Vos billets

@if($order && $order->tickets->count() > 0)
Vous avez acheté **{{ $order->tickets->sum('pivot.quantity') }} billet(s)** pour cet événement :

@foreach($order->tickets as $ticket)
- **{{ $ticket->nom }}** - Quantité: {{ $ticket->pivot->quantity }} - Prix unitaire: {{ number_format($ticket->pivot->unit_price, 0, ',', ' ') }} FCFA
@endforeach

**Total payé :** {{ number_format($payment->montant, 0, ',', ' ') }} FCFA
@endif

Vous trouverez vos billets en pièce jointe de cet email au format PDF. 

**Important :** Veuillez présenter le QR code de votre billet à l'entrée de l'événement.

@component('mail::button', ['url' => route('tickets.index')])
Voir mes billets
@endcomponent

Pour toute question, n'hésitez pas à nous contacter.

Cordialement,<br>
**L'équipe MokiliEvent**

@endcomponent
