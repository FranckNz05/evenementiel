<p>Bonjour {{ $user->prenom }} {{ $user->nom }},</p>

<p>Votre demande pour devenir influenceur sur MokiliEvent a été refusée.</p>

@if(!empty($reason))
<p>Motif: {{ $reason }}</p>
@endif

<p>Vous pouvez soumettre une nouvelle demande ultérieurement.</p>

<p>Cordialement,<br>L'équipe MokiliEvent</p>


