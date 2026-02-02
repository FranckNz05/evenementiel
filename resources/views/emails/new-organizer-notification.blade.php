@component('mail::message')
{{-- Logo centré --}}
# <span style="color: #FFD700;">Nouvel organisateur approuvé</span>

Un nouvel organisateur vient d'être **approuvé** sur la plateforme.

## 🧾 Détails de l'organisateur

- **Nom :** {{ $user->getFullNameAttribute() }}
- **Email :** {{ $user->email }}
- **Société :** {{ $request->company_name }}

@component('mail::button', ['url' => route('admin.organizers.index'), 'color' => 'custom'])
Voir tous les organisateurs
@endcomponent

Merci de continuer à faire de **<span style="color: #FFD700;">MokiliEvent</span>** une plateforme de confiance.

Cordialement,  
L’équipe **<span style="color: #FFD700;">MokiliEvent</span>**
@endcomponent
