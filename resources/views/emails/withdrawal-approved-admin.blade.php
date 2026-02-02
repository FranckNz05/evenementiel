@component('mail::message')
# ✅ Retrait approuvé et traité

Bonjour Administrateur,

Un retrait a été approuvé et traité avec succès.

## 📋 Détails du retrait

- **Organisateur :** {{ $organizer->prenom }} {{ $organizer->nom }}
- **Email :** {{ $organizer->email }}
- **Montant :** {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA
- **Méthode de paiement :** {{ $withdrawal->payment_method }}
- **Numéro de téléphone :** {{ $withdrawal->phone_number }}
- **Référence :** {{ $withdrawal->transaction_reference }}
@if($withdrawal->transaction_id)
- **ID Transaction :** {{ $withdrawal->transaction_id }}
@endif
- **Traité par :** {{ $admin ? $admin->prenom . ' ' . $admin->nom : 'Administrateur' }}
- **Date de traitement :** {{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d/m/Y à H:i') : $withdrawal->updated_at->format('d/m/Y à H:i') }}

@component('mail::button', ['url' => route('admin.withdrawals.index')])
Voir les retraits
@endcomponent

Merci,<br>
**L'équipe MokiliEvent**

@endcomponent

