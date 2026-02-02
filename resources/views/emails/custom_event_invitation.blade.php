@component('mail::message')
# Vous êtes invité à l'événement : {{ $event->title }}

Bonjour {{ $guest['full_name'] ?? ($guest->full_name ?? '') }},

Vous êtes invité à participer à l'événement **{{ $event->title }}**.

@if($event->start_date)
**Date :** {{ $event->start_date->format('d/m/Y à H:i') }}
@if($event->end_date)
 - {{ $event->end_date->format('d/m/Y à H:i') }}
@endif
@endif

@if($event->location)
**Lieu :** {{ $event->location }}
@endif

@if($event->description)
**Description :**  
{{ $event->description }}
@endif

@if($event->invitation_link)
@component('mail::button', ['url' => route('custom-events.invitation', $event->invitation_link)])
Voir les détails de l'événement
@endcomponent
@endif

Merci et à bientôt !
@endcomponent