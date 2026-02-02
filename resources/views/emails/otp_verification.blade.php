@component('mail::message')
{{-- Logo centré --}}

# <span style="color: #FFD700;">Bonjour {{ $name }},</span>

Merci de votre inscription sur <strong style="color: #FFD700;">MokiliEvent</strong>.

Votre code de vérification est :

@component('mail::panel')
<h1 style="text-align: center; font-size: 36px; color: #001F3F; letter-spacing: 8px;">{{ $otp }}</h1>
@endcomponent

<p style="text-align: center; color: #555;">
Ce code expire dans <strong>3 minutes</strong>.
</p>

<p style="font-size: 14px; color: #999;">
Si vous n'avez pas demandé ce code, veuillez ignorer cet email.
</p>

Cordialement,  
L’équipe <strong style="color: #FFD700;">MokiliEvent</strong>
@endcomponent
