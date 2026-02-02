@component('mail::message')
# ✅ Retrait approuvé et traité

Bonjour **{{ $organizer->prenom }} {{ $organizer->nom }}**,

Votre demande de retrait a été approuvée et traitée avec succès. L'argent a été envoyé sur votre numéro de téléphone.

## 📋 Détails du retrait

- **Montant :** {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA
- **Méthode de paiement :** {{ $withdrawal->payment_method }}
- **Numéro de téléphone :** {{ $withdrawal->phone_number }}
- **Référence :** {{ $withdrawal->transaction_reference }}
@if($withdrawal->transaction_id)
- **ID Transaction :** {{ $withdrawal->transaction_id }}
@endif
- **Date de traitement :** {{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d/m/Y à H:i') : $withdrawal->updated_at->format('d/m/Y à H:i') }}

## 💳 Vérification

Veuillez vérifier votre compte mobile money pour confirmer la réception du montant.

@component('mail::button', ['url' => route('organizer.withdrawals.index')])
Voir mes retraits
@endcomponent

Pour toute question, n'hésitez pas à nous contacter.

Cordialement,<br>
**L'équipe MokiliEvent**

@endcomponent

