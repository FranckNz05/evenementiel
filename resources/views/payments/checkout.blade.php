@extends('layouts.app')

@section('title', 'Confirmation de paiement')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Confirmation de paiement</h4>
                </div>
                
                <div class="card-body">
                    <!-- Récapitulatif de la commande -->
                    <div class="mb-5">
                        <h5 class="mb-3">Récapitulatif de votre commande</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Billet</th>
                                        <th class="text-end">Quantité</th>
                                        <th class="text-end">Prix unitaire</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->nom }}</td>
                                        <td class="text-end">{{ $ticket->pivot->quantity }}</td>
                                        <td class="text-end">{{ number_format($ticket->pivot->unit_price, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-end">{{ number_format($ticket->pivot->unit_price * $ticket->pivot->quantity, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Total</td>
                                        <td class="text-end">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Formulaire de paiement -->
                    <form action="{{ route('payments.confirm', $payment) }}" method="POST" id="payment-form">
                        @csrf
                        
                        <div class="mb-4">
                            <h5 class="mb-3">Méthode de paiement</h5>
                            
                            <div class="payment-methods">
    <!-- MTN Mobile Money -->
    <div class="payment-method-card mb-3 disabled" data-method="mtn" style="opacity: 0.6; cursor: not-allowed; position: relative;">
        <input type="radio" name="payment_method" id="mtn-money" value="mtn" class="d-none" disabled>
        <label for="mtn-money" class="payment-method-label" style="cursor: not-allowed;">
            <div class="payment-logo-wrapper">
                <div class="payment-logo">
                    <img src="{{ asset('images/mtnmoney.jpg') }}" alt="MTN Mobile Money" class="payment-image">
                </div>
            </div>
            <div class="payment-method-info">
                <h6 class="mb-1" style="color: black;">MTN Mobile Money</h6>
                <span class="payment-method-badge">Non disponible</span>
            </div>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; white-space: nowrap; z-index: 10;">
                <i class="fas fa-exclamation-triangle me-1"></i>Non disponible
            </div>
        </label>
    </div>
    
    <!-- Airtel Money -->
    <div class="payment-method-card mb-3 active" data-method="airtel">
        <input type="radio" name="payment_method" id="airtel-money" value="airtel" class="d-none" checked>
        <label for="airtel-money" class="payment-method-label">
            <div class="payment-logo-wrapper">
                <div class="payment-logo">
                    <img src="{{ asset('images/airtelmoney.jpg') }}" alt="Airtel Money" class="payment-image">
                </div>
            </div>
            <div class="payment-method-info">
                <h6 class="mb-1" style="color: black;">Airtel Money</h6>
                <span class="payment-method-badge">05/04-XXX-XX-XX</span>
            </div>
        </label>
    </div>
    
    <!-- Carte Visa -->
    <div class="payment-method-card disabled" data-method="visa">
        <input type="radio" name="payment_method" id="visa-card" value="visa" class="d-none" disabled>
        <label for="visa-card" class="payment-method-label">
            <div class="payment-logo-wrapper">
                <div class="payment-logo">
                    <img src="{{ asset('images/visa.jpg') }}" alt="Visa Card" class="payment-image">
                </div>
            </div>
            <div class="payment-method-info">
                <h6 class="mb-1" style="color: black;">Carte Visa</h6>
                <span class="payment-method-badge">Bientôt disponible</span>
            </div>
        </label>
    </div>
</div>

                        <!-- Champs Mobile Money -->
<div class="mb-3">
    <label for="phone" class="form-label">Numéro de téléphone</label>
    <div class="input-group">
        <span class="input-group-text">+242</span>
        <input type="tel" class="form-control" id="phone" name="phone" 
               placeholder="06XXXXXXX (MTN) ou 05XXXXXXX (Airtel)" 
               required>
    </div>
    <small class="form-text text-muted">Entrez votre numéro complet avec l'indicatif (ex: 06XXXXXXX)</small>
    <div id="phone-error" class="phone-error-message mt-2"></div>
</div>

                        <!-- Champs Carte Visa (désactivés) -->
                        <div class="mb-4" id="card-fields" style="display: none;">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Le paiement par carte sera disponible prochainement
                            </div>
                            
                            <div class="mb-3">
                                <label for="card-number" class="form-label">Numéro de carte</label>
                                <input type="text" class="form-control" id="card-number" disabled>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="card-expiry" class="form-label">Date d'expiration</label>
                                    <input type="text" class="form-control" id="card-expiry" placeholder="MM/AA" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="card-cvc" class="form-label">CVC</label>
                                    <input type="text" class="form-control" id="card-cvc" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg py-3" id="submit-btn">
                                <i class="fas fa-lock me-2"></i> Confirmer le paiement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.payment-method-card {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.payment-method-card.active[data-method="mtn"] {
    border-color: #ffffff;
    background-color: rgba(255, 215, 0, 0.1);
}

.payment-method-card.active[data-method="airtel"] {
    border-color: #E40C0C;
    background-color: rgba(228, 12, 12, 0.1);
}

.payment-method-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background-color: #f8f9fa;
}

.payment-method-card:not(.disabled):hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

.payment-method-label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.payment-logo {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 1rem;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.payment-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.payment-method-info {
    flex-grow: 1;
}

.payment-method-badge {
    font-size: 0.8rem;
    background-color: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}

.mb-1 {
    color: black;
}

.phone-error-message {
    display: none;
    color: #dc3545;
    font-size: 0.875rem;
}

.shake {
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentCards = document.querySelectorAll('.payment-method-card:not(.disabled)');
    const mobileFields = document.getElementById('mobile-fields');
    const cardFields = document.getElementById('card-fields');
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phone-error');
    const submitBtn = document.getElementById('submit-btn');

    // Gestion de la sélection des méthodes de paiement
    paymentCards.forEach(card => {
        card.addEventListener('click', function() {
            // Ignorer les clics sur les méthodes désactivées
            if (this.classList.contains('disabled')) {
                alert('MTN Mobile Money n\'est pas encore disponible. Veuillez utiliser Airtel Money.');
                return;
            }
            
            paymentCards.forEach(c => {
                c.classList.remove('active');
                if (c.dataset.method === 'mtn') {
                    c.style.borderColor = '#dee2e6';
                } else if (c.dataset.method === 'airtel') {
                    c.style.borderColor = '#dee2e6';
                }
            });
            
            this.classList.add('active');
            const method = this.dataset.method;
            
            // Appliquer les couleurs de bordure spécifiques
            if (method === 'mtn') {
                this.style.borderColor = '#ffffff'; // blanc pour MTN
            } else if (method === 'airtel') {
                this.style.borderColor = '#E40C0C'; // Rouge pour Airtel
            }
            
            // Gestion des champs de formulaire
            if (method === 'visa') {
                mobileFields.style.display = 'none';
                cardFields.style.display = 'block';
                submitBtn.disabled = true;
            } else {
                mobileFields.style.display = 'block';
                cardFields.style.display = 'none';
                submitBtn.disabled = false;
                validatePhoneNumber();
            }
        });
    });

    // Validation du numéro de téléphone
    phoneInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
        validatePhoneNumber();
    });

    function validatePhoneNumber() {
    const phone = phoneInput.value;
    const selectedMethodInput = document.querySelector('input[name="payment_method"]:checked');
    
    if (!selectedMethodInput || selectedMethodInput.disabled) {
        return false;
    }
    
    const selectedMethod = selectedMethodInput.value;
    let isValid = false;
    let errorMessage = '';

    if (phone.length > 0) {
        // MTN n'est plus disponible, donc on ne valide que Airtel
        if (selectedMethod === 'airtel') {
            isValid = /^(05[0-9]{7}|04[0-9]{7})$/.test(phone); // 05 ou 04 + 7 chiffres = 9 chiffres au total
            if (!isValid) {
                errorMessage = 'Le numéro Airtel doit commencer par 05 ou 04 et contenir 9 chiffres (ex: 05XXXXXXX ou 04XXXXXXX)';
            }
        }
    }

    if (!isValid && phone.length > 0) {
        phoneError.textContent = errorMessage;
        phoneError.style.display = 'block';
        phoneInput.classList.add('is-invalid', 'shake');
        setTimeout(() => phoneInput.classList.remove('shake'), 500);
    } else {
        phoneError.style.display = 'none';
        phoneInput.classList.remove('is-invalid', 'shake');
    }

    return isValid;
}

    // Validation du formulaire
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        const selectedMethodInput = document.querySelector('input[name="payment_method"]:checked');
        
        if (!selectedMethodInput || selectedMethodInput.disabled) {
            e.preventDefault();
            alert('MTN Mobile Money n\'est pas encore disponible. Veuillez utiliser Airtel Money.');
            return;
        }
        
        const selectedMethod = selectedMethodInput.value;
        
        if (selectedMethod === 'visa') {
            e.preventDefault();
            return;
        }

        if (!validatePhoneNumber()) {
            e.preventDefault();
            phoneInput.focus();
        }
    });
});
</script>
@endpush