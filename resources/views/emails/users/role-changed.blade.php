@component('mail::message')
# Mise à jour de votre rôle

Cher(e) {{ $user->name }},

Nous vous informons que votre rôle sur MokiliEvent a été mis à jour.

@if($newRole === 'organizer')
**Félicitations !** Vous êtes maintenant un organisateur d'événements sur notre plateforme.

@component('mail::panel')
En tant qu'organisateur, vous pouvez désormais :
- Créer et gérer vos propres événements
- Accéder aux statistiques détaillées
- Gérer vos ventes de billets
@endcomponent

@component('mail::button', ['url' => route('dashboard')])
Accéder à mon tableau de bord
@endcomponent

@else
Votre nouveau rôle : **{{ ucfirst($newRole) }}**

Si vous avez des questions concernant ce changement, n'hésitez pas à contacter notre équipe.
@endif

Cordialement,<br>
L'équipe {{ config('app.name') }}
@endcomponent
