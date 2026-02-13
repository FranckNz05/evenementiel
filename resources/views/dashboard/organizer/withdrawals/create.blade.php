@extends('layouts.dashboard')

@section('title', 'Demande de retrait')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Demande de retrait
                        </h5>
                        <a href="{{ route('organizer.withdrawals.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Retour
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Statistiques des revenus -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                                    <p class="text-muted mb-1 small">Revenus totaux</p>
                                    <h4 class="mb-0 fw-bold">
                                        {{ number_format($revenueData['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-percent fa-2x text-danger mb-2"></i>
                                    <p class="text-muted mb-1 small">Commission MokiliEvent</p>
                                    <h4 class="mb-0 fw-bold">
                                        {{ number_format($revenueData['total_commission_paid'] ?? 0, 0, ',', ' ') }} FCFA
                                    </h4>
                                    <small class="text-muted">(10%)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-wallet fa-2x mb-2"></i>
                                    <p class="mb-1 small">Solde disponible</p>
                                    <h4 class="mb-0 fw-bold">
                                        {{ number_format($availableBalance, 0, ',', ' ') }} FCFA
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Note informative -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Calcul du solde :</strong> Revenus totaux ({{ number_format($revenueData['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA) 
                        - Commission MokiliEvent 10% ({{ number_format($revenueData['total_commission_paid'] ?? 0, 0, ',', ' ') }} FCFA) 
                        = Solde disponible ({{ number_format($availableBalance, 0, ',', ' ') }} FCFA)
                    </div>

                    @if($availableBalance < 100)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Le montant minimum pour effectuer un retrait est de 100 FCFA.
                        </div>
                    @else
                        @if($availableBalance > 50000000)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note importante :</strong> Votre solde disponible est de {{ number_format($availableBalance, 0, ',', ' ') }} FCFA. 
                                La limite maximale par transaction est de 50 000 000 FCFA. 
                                Vous devrez effectuer plusieurs retraits pour retirer tout votre solde.
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('organizer.withdrawals.store') }}">
                            @csrf

                            <!-- Montant -->
                            <div class="mb-4">
                                <label for="amount" class="form-label fw-semibold">
                                    Montant à retirer <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <input type="number" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" 
                                           name="amount" 
                                           min="100"
                                           max="{{ $maxWithdrawableAmount }}"
                                           value="{{ old('amount') }}"
                                           placeholder="Entrez le montant"
                                           required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    Montant minimum : 100 FCFA | Maximum : {{ number_format($maxWithdrawableAmount, 0, ',', ' ') }} FCFA
                                    @if($availableBalance > 50000000)
                                        <br><small class="text-muted">(Limite système: 50 000 000 FCFA par transaction. Solde total disponible: {{ number_format($availableBalance, 0, ',', ' ') }} FCFA)</small>
                                    @endif
                                </div>
                            </div>

                            <!-- Méthode de paiement -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Méthode de paiement <span class="text-danger">*</span>
                                </label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check card-payment-method">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="payment_method" 
                                                   id="mtn" 
                                                   value="MTN Mobile Money" 
                                                   {{ old('payment_method') == 'MTN Mobile Money' ? 'checked' : '' }}
                                                   required>
                                            <label class="form-check-label w-100" for="mtn">
                                                <div class="card h-100 cursor-pointer">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-mobile-alt fa-2x text-warning mb-2"></i>
                                                        <h6 class="mb-0">MTN Mobile Money</h6>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check card-payment-method">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="payment_method" 
                                                   id="airtel" 
                                                   value="Airtel Money"
                                                   {{ old('payment_method') == 'Airtel Money' ? 'checked' : '' }}
                                                   required>
                                            <label class="form-check-label w-100" for="airtel">
                                                <div class="card h-100 cursor-pointer">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-mobile-alt fa-2x text-danger mb-2"></i>
                                                        <h6 class="mb-0">Airtel Money</h6>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('payment_method')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Numéro de téléphone -->
                            <div class="mb-4">
                                <label for="phone_number" class="form-label fw-semibold">
                                    Numéro de téléphone <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" 
                                           class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" 
                                           name="phone_number" 
                                           value="{{ old('phone_number', auth()->user()->phone) }}"
                                           placeholder="XXXXXXXXX"
                                           pattern="^0(4|5|6)\d{7}$"
                                           required>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    Entrez votre numéro sans le préfixe (+242 ou 242). Format: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx
                                </div>
                            </div>

                            <!-- Informations importantes -->
                            <div class="alert alert-warning mb-4">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    Informations importantes
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Votre demande sera traitée dans un délai de 24 à 48 heures</li>
                                    <li>Assurez-vous que le numéro fourni est correct et actif</li>
                                    <li>Le montant sera versé après validation par l'équipe MokiliEvent</li>
                                    <li>Vous recevrez une notification une fois le retrait effectué</li>
                                </ul>
                            </div>

                            <!-- Boutons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('organizer.withdrawals.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Soumettre la demande
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-payment-method .form-check-input {
        position: absolute;
        opacity: 0;
    }
    
    .card-payment-method .card {
        border: 2px solid #e0e0e0;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .card-payment-method input:checked ~ label .card,
    .card-payment-method .card.active {
        border-color: #0d6efd;
        background-color: #f0f7ff;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .card-payment-method .card:hover {
        border-color: #0d6efd;
        cursor: pointer;
    }
    
    .card-payment-method .form-check-input {
        position: absolute;
        opacity: 0;
        z-index: 1;
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    
    // Améliorer la sélection visuelle des méthodes de paiement
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Retirer la classe active de toutes les cartes
            document.querySelectorAll('.card-payment-method .card').forEach(card => {
                card.classList.remove('active');
            });
            
            // Ajouter la classe active à la carte sélectionnée
            if (this.checked) {
                const card = this.closest('.form-check').querySelector('.card');
                if (card) {
                    card.classList.add('active');
                }
            }
        });
        
        // Initialiser l'état visuel au chargement
        if (method.checked) {
            const card = method.closest('.form-check').querySelector('.card');
            if (card) {
                card.classList.add('active');
            }
        }
    });
    
    // Permettre de cliquer sur toute la carte pour sélectionner
    document.querySelectorAll('.card-payment-method .card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Ne pas déclencher si on clique directement sur le radio
            if (e.target.type !== 'radio') {
                const radio = this.closest('.form-check').querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            }
        });
    });
});
</script>
@endpush
@endsection

