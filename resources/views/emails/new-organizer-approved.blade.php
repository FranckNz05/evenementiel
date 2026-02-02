@component('mail::message')
{{-- Logo centré --}}
# <span style="color: #FFD700;">Nouvel organisateur approuvé</span>

Un nouvel organisateur vient d'être **approuvé** par {{ $approver->getFullNameAttribute() }}.

## 🧾 Détails de l'organisateur

- **Nom :** {{ $request->user->getFullNameAttribute() }}
- **Email :** {{ $request->user->email }}
- **Société :** {{ $request->company_name }}
- **Approuvé par :** {{ $approver->getFullNameAttribute() }} ({{ $approver->email }})
- **Date d'approbation :** {{ $request->updated_at->format('d/m/Y H:i') }}

@component('mail::button', ['url' => route('admin.organizers.index'), 'color' => 'custom'])
Voir tous les organisateurs
@endcomponent

Merci de continuer à faire de **<span style="color: #FFD700;">MokiliEvent</span>** une plateforme de confiance.

Cordialement,  
L'équipe **<span style="color: #FFD700;">MokiliEvent</span>**
@endcomponent