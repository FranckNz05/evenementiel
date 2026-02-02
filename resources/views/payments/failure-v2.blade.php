@extends('layouts.app')

@section('title', 'Paiement échoué - PawaPay')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-danger text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>
                        Paiement échoué
                    </h4>
                </div>
                
                <div class="card-body p-4">
                    <!-- Message d'échec -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-danger mb-3">Paiement non effectué</h5>
                        <p class="lead">Votre paiement n'a pas pu être traité.</p>
                    </div>

                    <!-- Raison de l'échec -->
                    @if($transaction->failure_reason)
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Raison de l'échec
                            </h6>
                            <p class="mb-0">
                                @if(is_array($transaction->failure_reason))
                                    {{ $transaction->failure_reason['message'] ?? 'Paiement refusé' }}
                                @else
                                    {{ $transaction->failure_reason }}
                                @endif
                            </p>
                            @if($transaction->failure_code)
                                <small class="text-muted">Code d'erreur: {{ $transaction->failure_code }}</small>
                            @endif
                        </div>
                    @endif

                    <!-- Détails de la transaction -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-receipt me-2"></i>
                                        Détails du paiement
                                    </h6>
                                    <p class="mb-1"><strong>Montant:</strong> {{ number_format($transaction->amount, 0, ',', ' ') }} FCFA</p>
                                    <p class="mb-1"><strong>Fournisseur:</strong> {{ ucfirst($transaction->provider) }}</p>
                                    <p class="mb-1"><strong>Téléphone:</strong> {{ $transaction->phone_number }}</p>
                                    <p class="mb-0"><strong>Date:</strong> {{ $transaction->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        Informations de la commande
                                    </h6>
                                    @if($order)
                                        <p class="mb-1"><strong>Événement:</strong> {{ $order->event->nom ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Nombre de billets:</strong> {{ $order->tickets->sum('pivot.quantity') }}</p>
                                        <p class="mb-1"><strong>Date de l'événement:</strong> {{ $order->event->date_debut ?? 'N/A' }}</p>
                                        <p class="mb-0"><strong>Référence:</strong> {{ $order->reference }}</p>
                                    @else
                                        <p class="text-muted">Informations de commande non disponibles</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Solutions possibles -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-lightbulb me-2"></i>
                            Que faire maintenant ?
                        </h6>
                        <ul class="mb-0">
                            <li>Vérifiez que votre compte Mobile Money a suffisamment de fonds</li>
                            <li>Assurez-vous que votre numéro de téléphone est correct</li>
                            <li>Vérifiez que votre opérateur Mobile Money est actif</li>
                            <li>Essayez avec un autre numéro de téléphone si possible</li>
                            <li>Contactez votre opérateur Mobile Money si le problème persiste</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <a href="{{ route('pawapay.v2.payment.form', $order->id ?? '#') }}" class="btn btn-primary">
                            <i class="fas fa-redo me-2"></i>
                            Réessayer le paiement
                        </a>
                        
                        <a href="{{ route('cart.show') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Retour au panier
                        </a>
                        
                        <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-calendar me-2"></i>
                            Découvrir d'autres événements
                        </a>
                    </div>

                    <!-- Codes d'erreur courants -->
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-question-circle me-2"></i>
                            Codes d'erreur courants
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li><strong>INSUFFICIENT_FUNDS:</strong> Solde insuffisant</li>
                                    <li><strong>INVALID_MSISDN:</strong> Numéro invalide</li>
                                    <li><strong>USER_CANCELLED:</strong> Annulé par l'utilisateur</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li><strong>PROVIDER_ERROR:</strong> Erreur opérateur</li>
                                    <li><strong>TIMEOUT:</strong> Délai dépassé</li>
                                    <li><strong>UNKNOWN_ERROR:</strong> Erreur inconnue</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Support -->
                    <div class="text-center">
                        <p class="text-muted mb-2">
                            Besoin d'aide ? Contactez notre support :
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="mailto:support@mokilievent.com" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-envelope me-1"></i>
                                Email
                            </a>
                            <a href="tel:+242123456789" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-phone me-1"></i>
                                Téléphone
                            </a>
                            <a href="#" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-comments me-1"></i>
                                Chat
                            </a>
                        </div>
                    </div>

                    <!-- Informations de sécurité -->
                    <div class="text-center text-muted mt-4">
                        <small>
                            <i class="fas fa-shield-alt me-1"></i>
                            Aucun montant n'a été débité de votre compte
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Animation d'échec
document.addEventListener('DOMContentLoaded', function() {
    const failureIcon = document.querySelector('.fa-times-circle');
    if (failureIcon) {
        failureIcon.style.transform = 'scale(0.8)';
        failureIcon.style.transition = 'transform 0.3s ease';
        
        setTimeout(() => {
            failureIcon.style.transform = 'scale(1)';
        }, 300);
    }
});

// Auto-retry après 30 secondes (optionnel)
setTimeout(() => {
    const retryButton = document.querySelector('a[href*="pawapay.v2.payment.form"]');
    if (retryButton && confirm('Voulez-vous réessayer le paiement maintenant ?')) {
        retryButton.click();
    }
}, 30000);
</script>
@endpush
@endsection
