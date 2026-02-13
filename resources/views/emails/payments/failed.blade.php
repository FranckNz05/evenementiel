@component('mail::message')
# Échec du paiement

Cher(e) {{ $order->user->name }},

Nous sommes désolés de vous informer que le paiement pour votre reservation n'a pas pu être traité.

**Événement :** {{ $order->event->title }}  
**Montant :** {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA

@component('mail::panel')
Vous pouvez réessayer le paiement en vous connectant à votre compte MokiliEvent.
@endcomponent

@component('mail::button', ['url' => route('orders.show', $order)])
Réessayer le paiement
@endcomponent

Si vous rencontrez des difficultés, n'hésitez pas à contacter notre service client.

Cordialement,<br>
L'équipe {{ config('app.name') }}
@endcomponent
