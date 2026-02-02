@component('mail::message')
# Nouvelle demande d'organisateur

Une nouvelle demande pour devenir organisateur a été soumise.

**Informations de l'utilisateur :**
- Nom : {{ $user->name }}
- Email : {{ $user->email }}

**Motivation :**
{{ $motivation }}

**Expérience :**
{{ $experience }}

@component('mail::button', ['url' => $url])
Voir la demande
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent 