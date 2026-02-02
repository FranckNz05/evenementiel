@component('mail::message')
# 💰 Demande de retrait soumise

Bonjour **{{ $organizer->prenom }} {{ $organizer->nom }}**,

Votre demande de retrait a été soumise avec succès et sera traitée par un administrateur dans les plus brefs délais.

## 📋 Détails de votre demande

- **Montant :** {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA
- **Méthode de paiement :** {{ $withdrawal->payment_method }}
- **Numéro de téléphone :** {{ $withdrawal->phone_number }}
- **Référence :** {{ $withdrawal->transaction_reference }}
- **Date de la demande :** {{ $withdrawal->created_at->format('d/m/Y à H:i') }}

## ⏳ Prochaines étapes

Votre demande est maintenant en attente de validation par un administrateur. Vous recevrez un email de confirmation une fois que votre retrait aura été approuvé et traité.

@component('mail::button', ['url' => route('organizer.withdrawals.index')])
Voir mes retraits
@endcomponent

Pour toute question, n'hésitez pas à nous contacter.

Cordialement,<br>
**L'équipe MokiliEvent**

@endcomponent

