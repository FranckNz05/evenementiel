@component('mail::message')
# ❌ Retrait rejeté

Bonjour **{{ $organizer->prenom }} {{ $organizer->nom }}**,

Votre demande de retrait a été rejetée par un administrateur.

## 📋 Détails de la demande

- **Montant :** {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA
- **Méthode de paiement :** {{ $withdrawal->payment_method }}
- **Numéro de téléphone :** {{ $withdrawal->phone_number }}
- **Référence :** {{ $withdrawal->transaction_reference }}
- **Date de la demande :** {{ $withdrawal->created_at->format('d/m/Y à H:i') }}

## 📝 Raison du rejet

{{ $withdrawal->rejection_reason }}

## 🔄 Prochaines étapes

Si vous pensez qu'il s'agit d'une erreur ou si vous souhaitez plus d'informations, veuillez nous contacter.

@component('mail::button', ['url' => route('organizer.withdrawals.index')])
Voir mes retraits
@endcomponent

Pour toute question, n'hésitez pas à nous contacter.

Cordialement,<br>
**L'équipe MokiliEvent**

@endcomponent

