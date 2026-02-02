@component('mail::message')
{{-- Logo centré --}}

# <span style="color: #FFD700;">Nouveau message reçu</span>

Vous avez reçu un nouveau message via le formulaire de contact de **<span style="color: #FFD700;">MokiliEvent</span>** :

---

** Nom :** {{ $data['name'] }}  
** Email :** {{ $data['email'] }}  
** Sujet :** {{ $data['subject'] }}

---

## Message :

<div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #001F3F;">
    {{ $data['message'] }}
</div>

{{-- Facultatif : bouton de réponse --}}
@component('mail::button', ['url' => 'mailto:' . $data['email'], 'color' => 'custom'])
Répondre à l'expéditeur
@endcomponent

---

Cordialement,  
L’équipe **<span style="color: #FFD700;">MokiliEvent</span>**
@endcomponent
