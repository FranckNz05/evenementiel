@extends('layouts.app')

@section('title', 'Statut du paiement')

@push('styles')
<style>
:root {
    --bleu-nuit: #1e3a8a;
    --bleu-nuit-clair: #3b82f6;
    --blanc-or: #ffffff;
    --blanc-amber: #f59e0b;
    --shadow-gold: rgba(255, 215, 0, 0.2);
    --shadow-blue: rgba(30, 58, 138, 0.1);
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --border-radius: 0.75rem;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --transition: all 0.3s ease;
}

.status-container {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    padding: 2rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.status-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    max-width: 500px;
    width: 100%;
    text-align: center;
}

.status-header {
    padding: 2rem;
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    position: relative;
    overflow: hidden;
}

.status-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
}

.status-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
}

.status-icon.pending {
    color: var(--warning);
    animation: pulse 2s infinite;
}

.status-icon.paid {
    color: var(--success);
}

.status-icon.failed {
    color: var(--danger);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.status-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.status-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0;
    position: relative;
    z-index: 1;
}

.status-body {
    padding: 2rem;
}

.payment-details {
    background: var(--gray-50);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: var(--bleu-nuit);
}

.detail-value {
    color: var(--gray-700);
}

.btn-modern {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition);
    box-shadow: var(--shadow);
    margin: 0.5rem;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: var(--white);
    text-decoration: none;
}

.btn-secondary-modern {
    background: linear-gradient(135deg, var(--gray-500) 0%, var(--gray-600) 100%);
    color: var(--white);
}

.btn-success-modern {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    color: var(--white);
}

.loading-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid var(--gray-300);
    border-radius: 50%;
    border-top-color: var(--bleu-nuit);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .status-container {
        padding: 1rem 0;
    }
    
    .status-header {
        padding: 1.5rem;
    }
    
    .status-body {
        padding: 1.5rem;
    }
    
    .status-icon {
        font-size: 3rem;
    }
}
</style>
@endpush

@section('content')
<div class="status-container">
    <div class="status-card">
        <div class="status-header">
            <div class="status-icon {{ $payment->status }}">
                @if($payment->status === 'pending')
                    <i class="fas fa-clock"></i>
                @elseif($payment->status === 'paid')
                    <i class="fas fa-check-circle"></i>
                @else
                    <i class="fas fa-times-circle"></i>
                @endif
            </div>
            <h1 class="status-title">
                @if($payment->status === 'pending')
                    Paiement en attente
                @elseif($payment->status === 'paid')
                    Paiement confirmé
                @else
                    Paiement échoué
                @endif
            </h1>
            <p class="status-subtitle">
                @if($payment->status === 'pending')
                    Veuillez confirmer le paiement sur votre téléphone
                @elseif($payment->status === 'paid')
                    Votre paiement a été traité avec succès
                @else
                    Une erreur s'est produite lors du traitement
                @endif
            </p>
        </div>
        
        <div class="status-body">
            <div class="payment-details">
                <div class="detail-item">
                    <span class="detail-label">Type d'événement :</span>
                    <span class="detail-value">
                        @if($payment->event_type === 'gratuit')
                            Événement Gratuit
                        @else
                            Événement Personnalisé
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Montant :</span>
                    <span class="detail-value">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Méthode de paiement :</span>
                    <span class="detail-value">{{ $payment->payment_method }}</span>
                </div>
                @if($payment->transaction_id)
                <div class="detail-item">
                    <span class="detail-label">ID de transaction :</span>
                    <span class="detail-value">{{ $payment->transaction_id }}</span>
                </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Date de création :</span>
                    <span class="detail-value">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($payment->paid_at)
                <div class="detail-item">
                    <span class="detail-label">Date de paiement :</span>
                    <span class="detail-value">{{ $payment->paid_at->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>

            @if($payment->status === 'pending')
            <div class="text-center">
                <p class="text-muted mb-3">
                    <i class="fas fa-mobile-alt"></i>
                    Vérifiez votre téléphone et confirmez le paiement
                </p>
                <button class="btn-modern" onclick="checkPaymentStatus()">
                    <span id="checkText">Vérifier le statut</span>
                    <span id="checkSpinner" class="loading-spinner" style="display: none;"></span>
                </button>
            </div>
            @elseif($payment->status === 'paid')
            <div class="text-center">
                <p class="text-success mb-3">
                    <i class="fas fa-check-circle"></i>
                    Vous pouvez maintenant créer votre événement
                </p>
                <a href="{{ route('organizer.events.create') }}" class="btn-modern btn-success-modern">
                    <i class="fas fa-plus"></i>
                    Créer mon événement
                </a>
            </div>
            @else
            <div class="text-center">
                <p class="text-danger mb-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    Le paiement n'a pas pu être traité
                </p>
                <a href="{{ route('event-creation-payment.form', ['type' => $payment->event_type]) }}" class="btn-modern">
                    <i class="fas fa-redo"></i>
                    Réessayer
                </a>
            </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('event-creation-payment.history') }}" class="btn-modern btn-secondary-modern">
                    <i class="fas fa-history"></i>
                    Voir l'historique
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function checkPaymentStatus() {
    const checkText = document.getElementById('checkText');
    const checkSpinner = document.getElementById('checkSpinner');
    
    checkText.style.display = 'none';
    checkSpinner.style.display = 'inline-block';
    
    // Simuler une vérification (dans un vrai projet, vous feriez un appel AJAX)
    setTimeout(() => {
        location.reload();
    }, 2000);
}

// Vérifier automatiquement le statut toutes les 10 secondes si en attente
@if($payment->status === 'pending')
setInterval(() => {
    location.reload();
}, 10000);
@endif
</script>
@endpush
