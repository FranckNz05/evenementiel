@component('mail::message')
# Félicitations !

Votre demande pour devenir organisateur sur notre plateforme a été approuvée.

Vous pouvez maintenant créer et gérer vos événements.

@component('mail::button', ['url' => route('organizer.dashboard')])
Accéder à votre tableau de bord
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent