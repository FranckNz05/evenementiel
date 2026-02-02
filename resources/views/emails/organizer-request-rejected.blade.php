@component('mail::message')
# Demande rejetée

Votre demande pour devenir organisateur sur notre plateforme a été rejetée.

**Raison:**  
{{ $reason }}

Vous pouvez soumettre une nouvelle demande après avoir corrigé les problèmes mentionnés.

@component('mail::button', ['url' => route('organizer-requests.create')])
Soumettre une nouvelle demande
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent