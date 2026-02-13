@component('mail::message')
<div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ $logoUrl }}" alt="MokiliEvent Logo" style="max-width: 200px; height: auto;">
</div>

# Vérification de votre compte

Bienvenue sur MokiliEvent ! Avant de commencer, nous devons vérifier votre adresse email.

Voici votre code de vérification :

@component('mail::panel')
<div style="text-align: center; font-family: monospace; font-weight: bold; font-size: 32px; letter-spacing: 3px; color: #333;">
{{ $otp }}
</div>
@endcomponent

Ce code est valable pendant 10 minutes. Si vous ne l'utilisez pas dans ce délai, vous pourrez en demander un nouveau.

@component('mail::button', ['url' => route('verification.notice')])
Vérifier mon compte
@endcomponent

Si vous n'avez pas créé de compte sur MokiliEvent, vous pouvez ignorer cet email en toute sécurité.

Merci,<br>
L'équipe {{ config('app.name') }}
@endcomponent
