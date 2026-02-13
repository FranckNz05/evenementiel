@component('mail::message')
# Confirmation de votre reservation

Cher(e) {{ $order->user->name }},

Nous vous remercions pour votre reservation sur MokiliEvent. Voici les détails de votre achat :

**Événement :** {{ $order->event->title }}  
**Date :** {{ $order->event->start_date->format('d/m/Y H:i') }}  
**Lieu :** {{ $order->event->location }}  
**Nombre de billets :** {{ $order->tickets->count() }}  
**Montant total :** {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA

@component('mail::panel')
Vos billets sont joints à cet email. Vous pouvez également les retrouver dans votre espace personnel sur notre site.
@endcomponent

@component('mail::button', ['url' => route('orders.show', $order)])
Voir ma reservation
@endcomponent

Merci de votre confiance,<br>
L'équipe {{ config('app.name') }}
@endcomponent
