@extends('layouts.app')

@section('title', 'Paiement en cours - PawaPay')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-dark text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Paiement en cours
                    </h4>
                </div>
                
                <div class="card-body p-4 text-center">
                    <!-- Animation de chargement -->
                    <div class="mb-4">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>

                    <h5 class="mb-3">Vérification de votre paiement</h5>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            Instructions importantes
                        </h6>
                        <ul class="mb-0 text-start">
                            <li>Vérifiez votre téléphone pour les notifications de paiement</li>
                            <li>Confirmez le paiement dans votre application Mobile Money</li>
                            <li>Cette page se mettra à jour automatiquement</li>
                            <li>Ne fermez pas cette page pendant le traitement</li>
                        </ul>
                    </div>

                    <!-- Informations de la transaction -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-receipt me-2"></i>
                                        Détails de la commande
                                    </h6>
                                    <p class="mb-1"><strong>Événement:</strong> {{ $order->event->nom ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Montant:</strong> {{ number_format($transaction->amount, 0, ',', ' ') }} FCFA</p>
                                    <p class="mb-0"><strong>Statut:</strong> 
                                        <span class="badge bg-warning" id="statusBadge">
                                            {{ ucfirst(strtolower($transaction->status)) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-mobile-alt me-2"></i>
                                        Informations de paiement
                                    </h6>
                                    <p class="mb-1"><strong>Fournisseur:</strong> {{ ucfirst($transaction->provider) }}</p>
                                    <p class="mb-1"><strong>Téléphone:</strong> {{ $transaction->phone_number }}</p>
                                    <p class="mb-0"><strong>ID Transaction:</strong> 
                                        <small class="text-muted">{{ Str::limit($transaction->deposit_id, 20) }}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages de statut -->
                    <div id="statusMessages" class="mb-4">
                        <div class="alert alert-warning" id="pendingMessage">
                            <i class="fas fa-hourglass-half me-2"></i>
                            Votre paiement est en attente de confirmation...
                        </div>
                        
                        <div class="alert alert-info d-none" id="processingMessage">
                            <i class="fas fa-cog fa-spin me-2"></i>
                            Traitement de votre paiement en cours...
                        </div>
                        
                        <div class="alert alert-success d-none" id="successMessage">
                            <i class="fas fa-check-circle me-2"></i>
                            Paiement effectué avec succès ! Redirection en cours...
                        </div>
                        
                        <div class="alert alert-danger d-none" id="failureMessage">
                            <i class="fas fa-times-circle me-2"></i>
                            Le paiement a échoué. Veuillez réessayer.
                        </div>
                        
                        <div class="alert alert-info d-none" id="reconciliationMessage">
                            <i class="fas fa-balance-scale me-2"></i>
                            Votre paiement est en cours de réconciliation...
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-outline-primary" onclick="checkStatus()">
                            <i class="fas fa-sync-alt me-2"></i>
                            Vérifier le statut
                        </button>
                        
                        <a href="{{ route('cart.show') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Retour au panier
                        </a>
                    </div>
                </div>
                
                <div class="card-footer text-center text-muted">
                    <small>
                        <i class="fas fa-shield-alt me-1"></i>
                        Transaction sécurisée - Timeout: 15 minutes
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let checkInterval;
let checkCount = 0;
const maxChecks = 60; // 5 minutes maximum (5 secondes * 60)

document.addEventListener('DOMContentLoaded', function() {
    // Démarrer la vérification automatique
    startStatusCheck();
    
    // Vérifier le statut toutes les 5 secondes
    checkInterval = setInterval(checkStatus, 5000);
    
    // Arrêter la vérification après 5 minutes
    setTimeout(() => {
        if (checkInterval) {
            clearInterval(checkInterval);
            showTimeoutMessage();
        }
    }, 300000); // 5 minutes
});

function checkStatus() {
    if (checkCount >= maxChecks) {
        clearInterval(checkInterval);
        showTimeoutMessage();
        return;
    }
    
    checkCount++;
    
    fetch('{{ route("pawapay.v2.check-status", $order) }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Erreur:', data.error);
            return;
        }
        
        updateStatusDisplay(data);
        
        // Si le paiement est terminé, arrêter la vérification
        if (data.completed || data.failed) {
            clearInterval(checkInterval);
            
            if (data.completed && data.redirect_url) {
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 2000);
            } else if (data.failed && data.redirect_url) {
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 3000);
            }
        }
    })
    .catch(error => {
        console.error('Erreur lors de la vérification:', error);
    });
}

function updateStatusDisplay(data) {
    const statusBadge = document.getElementById('statusBadge');
    const pendingMessage = document.getElementById('pendingMessage');
    const processingMessage = document.getElementById('processingMessage');
    const successMessage = document.getElementById('successMessage');
    const failureMessage = document.getElementById('failureMessage');
    const reconciliationMessage = document.getElementById('reconciliationMessage');
    
    // Masquer tous les messages
    [pendingMessage, processingMessage, successMessage, failureMessage, reconciliationMessage]
        .forEach(msg => msg.classList.add('d-none'));
    
    // Mettre à jour le badge de statut
    if (statusBadge) {
        statusBadge.textContent = data.status || 'En attente';
        statusBadge.className = 'badge ' + getStatusClass(data.status);
    }
    
    // Afficher le message approprié
    if (data.completed) {
        successMessage.classList.remove('d-none');
    } else if (data.failed) {
        failureMessage.classList.remove('d-none');
    } else if (data.in_reconciliation) {
        reconciliationMessage.classList.remove('d-none');
    } else if (data.status === 'PROCESSING') {
        processingMessage.classList.remove('d-none');
    } else {
        pendingMessage.classList.remove('d-none');
    }
}

function getStatusClass(status) {
    switch(status) {
        case 'COMPLETED': return 'bg-success';
        case 'FAILED': return 'bg-danger';
        case 'PROCESSING': return 'bg-info';
        case 'IN_RECONCILIATION': return 'bg-warning';
        default: return 'bg-warning';
    }
}

function showTimeoutMessage() {
    const statusMessages = document.getElementById('statusMessages');
    statusMessages.innerHTML = `
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Le délai d'attente a été dépassé. Veuillez vérifier manuellement le statut de votre paiement 
            ou contacter le support si nécessaire.
        </div>
    `;
}

// Fonction globale pour les boutons
window.checkStatus = checkStatus;
</script>
@endpush
@endsection
