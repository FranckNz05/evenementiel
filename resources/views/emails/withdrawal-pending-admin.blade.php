@component('mail::message')
# ⚠️ Nouvelle demande de retrait en attente

Bonjour Administrateur,

Une nouvelle demande de retrait nécessite votre attention.

## 📋 Détails de la demande

- **Organisateur :** {{ $organizer->prenom }} {{ $organizer->nom }}
- **Email :** {{ $organizer->email }}
- **Montant :** {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA
- **Méthode de paiement :** {{ $withdrawal->payment_method }}
- **Numéro de téléphone :** {{ $withdrawal->phone_number }}
- **Référence :** {{ $withdrawal->transaction_reference }}
- **Date de la demande :** {{ $withdrawal->created_at->format('d/m/Y à H:i') }}

## ✅ Action requise

Veuillez examiner cette demande et l'approuver ou la rejeter depuis votre tableau de bord administrateur.

@component('mail::button', ['url' => route('admin.withdrawals.index')])
Gérer les retraits
@endcomponent

Merci,<br>
**L'équipe MokiliEvent**

@endcomponent

