@extends('layouts.app')

@section('title', 'Paiement pour la création d\'événement')

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

.payment-container {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    padding: 2rem 0;
}

.payment-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    max-width: 600px;
    margin: 0 auto;
}

.payment-header {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    padding: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.payment-header::before {
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

.payment-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.payment-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0;
    position: relative;
    z-index: 1;
}

.payment-body {
    padding: 2rem;
}

.payment-amount {
    text-align: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--gray-50);
    border-radius: var(--border-radius);
    border: 2px solid var(--blanc-or);
}

.amount-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 0.5rem;
}

.amount-label {
    color: var(--gray-600);
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.payment-method {
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    position: relative;
}

.payment-method:hover {
    border-color: var(--bleu-nuit);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.payment-method.selected {
    border-color: var(--bleu-nuit);
    background: var(--shadow-blue);
}

.payment-method input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.payment-method-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.payment-method-mtn .payment-method-icon {
    color: #ff6b35;
}

.payment-method-airtel .payment-method-icon {
    color: #e60012;
}

.payment-method-name {
    font-weight: 600;
    color: var(--bleu-nuit);
    margin-bottom: 0.25rem;
}

.payment-method-desc {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--bleu-nuit);
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--bleu-nuit);
    box-shadow: 0 0 0 3px var(--shadow-blue);
}

.btn-payment {
    width: 100%;
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    border: none;
    padding: 1rem 2rem;
    border-radius: var(--border-radius);
    font-size: 1.125rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-payment:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-payment:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.payment-info {
    background: var(--gray-50);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-top: 1.5rem;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.payment-info i {
    color: var(--info);
    margin-right: 0.5rem;
}

@media (max-width: 768px) {
    .payment-container {
        padding: 1rem 0;
    }
    
    .payment-header {
        padding: 1.5rem;
    }
    
    .payment-title {
        font-size: 1.5rem;
    }
    
    .payment-body {
        padding: 1.5rem;
    }
    
    .payment-methods {
        grid-template-columns: 1fr;
    }
    
    .amount-value {
        font-size: 2rem;
    }
}
</style>
@endpush

@section('content')
<div class="payment-container">
    <div class="payment-card">
        <div class="payment-header">
            <h1 class="payment-title">
                <i class="fas fa-credit-card"></i>
                Paiement pour la création d'événement
            </h1>
            <p class="payment-subtitle">
                @if($eventType === 'gratuit')
                    Événement Gratuit
                @else
                    Événement Personnalisé
                @endif
            </p>
        </div>
        
        <div class="payment-body">
            <div class="payment-amount">
                <div class="amount-value">{{ number_format($fee, 0, ',', ' ') }} FCFA</div>
                <div class="amount-label">Montant à payer</div>
            </div>

            <form action="{{ route('event-creation-payment.process') }}" method="POST" id="paymentForm">
                @csrf
                <input type="hidden" name="event_type" value="{{ $eventType }}">
                
                <div class="form-group">
                    <label class="form-label">Méthode de paiement</label>
                    <div class="payment-methods">
                        <div class="payment-method payment-method-mtn" style="opacity: 0.6; cursor: not-allowed; position: relative;" onclick="event.preventDefault(); alert('MTN Mobile Money n\'est pas encore disponible. Veuillez utiliser Airtel Money.');">
                            <input type="radio" name="payment_method" value="MTN Mobile Money" id="mtn" disabled>
                            <div class="payment-method-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="payment-method-name">MTN Mobile Money</div>
                            <div class="payment-method-desc">Paiement via MTN</div>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; white-space: nowrap; z-index: 10;">
                                <i class="fas fa-exclamation-triangle me-1"></i>Non disponible
                            </div>
                        </div>
                        
                        <div class="payment-method payment-method-airtel" onclick="selectPaymentMethod('Airtel Money', this)">
                            <input type="radio" name="payment_method" value="Airtel Money" id="airtel" required checked>
                            <div class="payment-method-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="payment-method-name">Airtel Money</div>
                            <div class="payment-method-desc">Paiement via Airtel</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone_number" class="form-label">Numéro de téléphone</label>
                    <input type="tel" 
                           name="phone_number" 
                           id="phone_number" 
                           class="form-control" 
                           placeholder="Ex: +243 123 456 789"
                           required>
                </div>

                <button type="submit" class="btn-payment" id="submitBtn">
                    <i class="fas fa-credit-card"></i>
                    Procéder au paiement
                </button>
            </form>

            <div class="payment-info">
                <i class="fas fa-info-circle"></i>
                <strong>Information importante :</strong> Vous recevrez une demande de paiement sur votre téléphone. 
                Veuillez confirmer le paiement pour finaliser la création de votre événement.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectPaymentMethod(method, element) {
    // Désélectionner toutes les méthodes
    document.querySelectorAll('.payment-method').forEach(el => {
        el.classList.remove('selected');
        el.querySelector('input[type="radio"]').checked = false;
    });
    
    // Sélectionner la méthode cliquée
    element.classList.add('selected');
    element.querySelector('input[type="radio"]').checked = true;
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Désactiver le bouton et afficher le loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';
    
    // Soumettre le formulaire
    this.submit();
});

// Validation du numéro de téléphone
document.getElementById('phone_number').addEventListener('input', function(e) {
    const phone = e.target.value;
    const phoneRegex = /^(\+243|243|0)?[1-9][0-9]{8}$/;
    
    if (phone && !phoneRegex.test(phone.replace(/\s/g, ''))) {
        e.target.style.borderColor = 'var(--danger)';
    } else {
        e.target.style.borderColor = 'var(--gray-200)';
    }
});
</script>
@endpush
