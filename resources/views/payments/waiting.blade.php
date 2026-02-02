@extends('layouts.app')

@section('title', 'Confirmation du paiement en cours')

@section('content')
<div class="payment-waiting-container">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-gradient-primary text-white p-4" style="background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);">
                        <h4 class="mb-1 fw-bold">Confirmation du paiement</h4>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-mobile-alt me-2"></i>
                            <span>Paiement #{{ $payment->matricule }}</span>
                        </div>
                    </div>

                    <div class="card-body p-4 p-lg-5 text-center">
                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show mb-4" style="background-color: rgba(15, 26, 61, 0.1); color: var(--bleu-nuit); border: 1px solid rgba(15, 26, 61, 0.2);">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>{{ session('info') }}</div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Animation de chargement -->
                        <div class="waiting-animation mb-4">
                            <div class="spinner-container">
                                <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                            <div class="phone-icon">
                                <i class="fas fa-mobile-alt fa-3x text-primary"></i>
                                <div class="notification-pulse"></div>
                            </div>
                        </div>

                        <!-- Informations du paiement -->
                        <div class="payment-info mb-4">
                            <h5 class="mb-3">Vérifiez votre téléphone Airtel Money</h5>
                            <div class="alert alert-light border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong>Montant:</strong><br>
                                        <span class="h5 text-primary">{{ number_format($payment->montant, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Numéro:</strong><br>
                                        <span class="h6">{{ $payment->numero_telephone }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="instructions mb-4">
                            <div class="instruction-steps">
                                <div class="step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <strong>Ouvrez votre application Airtel Money</strong><br>
                                        <small>Déverrouillez votre téléphone</small>
                                    </div>
                                </div>
                                <div class="step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <strong>Acceptez le paiement</strong><br>
                                        <small>Confirmez avec votre code PIN</small>
                                    </div>
                                </div>
                                <div class="step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <strong>Attendez la confirmation</strong><br>
                                        <small>La page se mettra à jour automatiquement</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Barre de progression -->
                        <div class="progress-container mb-4">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                     role="progressbar"
                                     style="width: 0%"
                                     id="progressBar"></div>
                            </div>
                            <small class="text-muted mt-2 d-block" id="progressText">Vérification en cours...</small>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="action-buttons">
                            <a href="{{ route('payments.check-status', $payment) }}"
                               class="btn btn-outline-primary me-2"
                               id="checkStatusBtn"
                               style="color: var(--bleu-nuit); border-color: var(--bleu-nuit);">
                                <i class="fas fa-sync-alt me-2"></i>Vérifier maintenant
                            </a>
                            <a href="{{ route('orders.show', $payment->order) }}"
                               class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la commande
                            </a>
                        </div>

                        <!-- Message de statut -->
                        <div id="statusMessage" class="mt-4" style="display: none;">
                            <div class="alert" id="statusAlert"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffffff;
}

.payment-waiting-container {
    background-color: #f8f9fc;
    min-height: 100vh;
}

/* Animation de chargement */
.waiting-animation {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.spinner-container {
    position: relative;
}

.phone-icon {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.notification-pulse {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(15, 26, 61, 0.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

/* Étapes d'instructions */
.instruction-steps {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    max-width: 150px;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 0.5rem;
    box-shadow: 0 4px 8px rgba(15, 26, 61, 0.2);
}

.step-content {
    line-height: 1.4;
}

/* Barre de progression */
.progress-container {
    max-width: 300px;
    margin: 0 auto;
}

/* Animation de succès */
.success-animation {
    animation: bounceIn 0.8s ease-out;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Bouton Vérifier maintenant - Bleu MokiliEvent */
#checkStatusBtn {
    color: var(--bleu-nuit) !important;
    border-color: var(--bleu-nuit) !important;
}

#checkStatusBtn:hover {
    background-color: var(--bleu-nuit) !important;
    color: white !important;
    border-color: var(--bleu-nuit) !important;
}

#checkStatusBtn i {
    color: var(--bleu-nuit) !important;
}

#checkStatusBtn:hover i {
    color: white !important;
}

/* Responsive */
@media (max-width: 768px) {
    .instruction-steps {
        flex-direction: column;
        gap: 1rem;
    }

    .step {
        max-width: 100%;
        flex-direction: row;
        text-align: left;
    }

    .step-number {
        margin-bottom: 0;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .waiting-animation {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let checkInterval;
    let progressBar = document.getElementById('progressBar');
    let progressText = document.getElementById('progressText');
    let checkStatusBtn = document.getElementById('checkStatusBtn');
    let statusMessage = document.getElementById('statusMessage');
    let statusAlert = document.getElementById('statusAlert');

    const paymentId = {{ $payment->id }};
    const maxChecks = 30; // 30 vérifications max (environ 1 minute)
    let checkCount = 0;

    // Démarrer la vérification automatique
    startAutoCheck();

    // Fonction de vérification du statut
    function checkPaymentStatus() {
        fetch(`{{ route('payments.check-status', $payment) }}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            checkCount++;

            // Mettre à jour la barre de progression
            const progress = Math.min((checkCount / maxChecks) * 100, 90);
            progressBar.style.width = progress + '%';
            progressText.textContent = `Vérification ${checkCount}/${maxChecks}...`;

            // Utiliser transaction_status (TS, TF, TIP, TA, TE) comme critère principal
            const transactionStatus = data.transaction_status || null;

            // TS = Transaction Success - Paiement réussi
            if (transactionStatus === 'TS' || (data.success && data.status === 'success')) {
                clearInterval(checkInterval);
                showSuccess(data.message || 'Paiement confirmé avec succès !');

                // Rediriger vers la page de succès après 2 secondes
                setTimeout(() => {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = `{{ route('payments.success', $payment) }}`;
                    }
                }, 2000);
                return; // Arrêter ici
            }

            // TF = Transaction Failed - Paiement échoué
            if (transactionStatus === 'TF' || data.status === 'failed') {
                clearInterval(checkInterval);
                showError(data.message || 'Le paiement a échoué.');

                // Rediriger vers la page d'échec après 3 secondes
                setTimeout(() => {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = `{{ route('payments.failure', $payment) }}`;
                    }
                }, 2000);
                return; // Arrêter ici
            }

            // TE = Transaction Expired - Expiré
            if (transactionStatus === 'TE') {
                clearInterval(checkInterval);
                showError(data.message || 'La transaction a expiré.');

                setTimeout(() => {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = `{{ route('payments.failure', $payment) }}`;
                    }
                }, 2000);
                return; // Arrêter ici
            }

            // TIP = Transaction in Progress ou TA = Transaction Ambiguous - En attente
            if (transactionStatus === 'TIP' || transactionStatus === 'TA' || data.status === 'pending') {
                progressText.textContent = 'Paiement en cours... (' + checkCount + '/' + maxChecks + ')';
                // Continuer la vérification
            } else {
                // Statut inconnu, continuer
                progressText.textContent = 'Vérification en cours... (' + checkCount + '/' + maxChecks + ')';
            }

            // Arrêter après le maximum de vérifications
            if (checkCount >= maxChecks) {
                clearInterval(checkInterval);
                showWarning('Délai d\'attente dépassé. Veuillez vérifier manuellement le statut du paiement.');
                progressText.textContent = 'Vérification terminée - vérifiez manuellement';
            }
        })
        .catch(error => {
            console.error('Erreur lors de la vérification:', error);
            checkCount++;

            if (checkCount >= maxChecks) {
                clearInterval(checkInterval);
                showError('Erreur de connexion. Veuillez vérifier manuellement.');
            }
        });
    }

    // Démarrer la vérification automatique toutes les 2 secondes
    function startAutoCheck() {
        checkInterval = setInterval(checkPaymentStatus, 2000);

        // Première vérification immédiate
        setTimeout(checkPaymentStatus, 500);
    }

    // Fonction pour vérifier manuellement
    checkStatusBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkStatusBtn.disabled = true;
        checkStatusBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Vérification...';

        checkPaymentStatus();

        setTimeout(() => {
            checkStatusBtn.disabled = false;
            checkStatusBtn.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Vérifier maintenant';
        }, 1000);
    });

    // Fonctions d'affichage des messages
    function showSuccess(message) {
        statusMessage.style.display = 'block';
        statusAlert.className = 'alert alert-success success-animation';
        statusAlert.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + message;
        progressBar.style.width = '100%';
        progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
        progressBar.classList.add('bg-success');
        progressText.textContent = 'Paiement confirmé !';
    }

    function showError(message) {
        statusMessage.style.display = 'block';
        statusAlert.className = 'alert alert-danger';
        statusAlert.innerHTML = '<i class="fas fa-times-circle me-2"></i>' + message;
        progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
        progressBar.classList.add('bg-danger');
        progressText.textContent = 'Paiement échoué';
    }

    function showWarning(message) {
        statusMessage.style.display = 'block';
        statusAlert.className = 'alert alert-warning';
        statusAlert.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + message;
        progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
        progressBar.classList.add('bg-warning');
        progressText.textContent = 'Vérification manuelle requise';
    }
});
</script>
@endpush