@extends('layouts.app')

@section('title', 'Paiement de la réservation')

@section('content')
<div class="payment-page-container">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white p-4" style="background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);">
                    <h4 class="mb-1 fw-bold">Paiement de votre réservation #{{ $reservation->id }}</h4>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-event me-2"></i>
                        <span>{{ $reservation->event->title }}</span>
                    </div>
                </div>

                <div class="card-body p-4 p-lg-5">
                    @if(session('error'))
                        <div class="alert alert-info alert-dismissible fade show" style="background-color: var(--bleu-nuit); color: white; border: none;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-info alert-dismissible fade show" style="background-color: var(--bleu-nuit); color: white; border: none;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <div>
                                    <strong>Erreurs de validation :</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @php
                        $totalAmount = $reservation->tickets->sum(function($t) { 
                            return ($t->pivot->quantity ?? 0) * ($t->pivot->price ?? 0); 
                        });
                        $totalQuantity = $reservation->tickets->sum(function($t) { 
                            return $t->pivot->quantity ?? 0; 
                        });
                        $eventStartDate = $reservation->event && $reservation->event->start_date 
                            ? \Carbon\Carbon::parse($reservation->event->start_date) 
                            : null;
                        $sevenDaysBefore = $eventStartDate ? $eventStartDate->copy()->subDays(7) : null;
                        $daysRemaining = $sevenDaysBefore ? \Carbon\Carbon::now()->diffInDays($sevenDaysBefore, false) : null;
                    @endphp

                    @if($daysRemaining !== null && $daysRemaining > 0)
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important :</strong> Vous devez payer cette réservation dans les <strong>{{ $daysRemaining }} jour(s)</strong> avant le début de l'événement ({{ $eventStartDate->format('d/m/Y H:i') }}), sinon elle sera automatiquement annulée.
                        </div>
                    @endif

                    <!-- Résumé de la réservation -->
                    <div class="ticket-summary mb-5">
                        <div class="ticket-summary-card">
                            <div class="ticket-info">
                                <div class="ticket-icon">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div class="ticket-details">
                                    <h5 class="mb-2">{{ $reservation->event->title }}</h5>
                                    <div class="ticket-meta">
                                        <span><i class="fas fa-ticket-alt me-1"></i>{{ $totalQuantity }} billet(s)</span>
                                        @foreach($reservation->tickets as $ticket)
                                            <span class="ticket-name">{{ $ticket->nom }} (x{{ $ticket->pivot->quantity }})</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="ticket-amount">
                                    <div class="amount-label">Montant total</div>
                                    <div class="amount-value">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de paiement Airtel Money -->
                    <form action="{{ route('reservations.processPayment', $reservation) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <h5 class="mb-4"><i class="fas fa-mobile-alt me-2" style="color: var(--bleu-nuit);"></i>Choisissez votre mode de paiement</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="payment-option-card">
                                        <input type="radio" name="operator" value="airtel" class="payment-option-input" required>
                                        <div class="payment-option-content">
                                            <div class="payment-option-header">
                                                <img src="{{ asset('images/airtelmoney.jpg') }}" alt="Airtel Money" class="payment-option-logo">
                                                <span class="payment-option-name">Airtel Money</span>
                                            </div>
                                            <div class="payment-option-check">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="payment-option-card" style="opacity: 0.6; cursor: not-allowed; position: relative;">
                                        <input type="radio" name="operator" value="mtn" class="payment-option-input" disabled>
                                        <div class="payment-option-content" style="position: relative;">
                                            <div class="payment-option-header">
                                                <img src="{{ asset('images/mtnmoney.jpg') }}" alt="MTN Mobile Money" class="payment-option-logo">
                                                <span class="payment-option-name">MTN Mobile Money</span>
                                            </div>
                                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; white-space: nowrap; z-index: 10;">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Non disponible
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label fw-semibold">Numéro Mobile Money</label>
                            <input type="text" inputmode="numeric" pattern="^0[456][0-9]{7}$" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Ex: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx" value="{{ old('phone') }}" required>
                            <div class="form-text">Formats acceptés: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx (9 chiffres).</div>
                            @error('phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback" style="display: none;">Veuillez respecter le format requis: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx (9 chiffres)</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="confirmation"
                                       id="confirmation" required>
                                <label class="form-check-label" for="confirmation">
                                    Je confirme vouloir effectuer ce paiement avec Airtel Money et j'accepte
                                    de recevoir une demande de confirmation sur mon téléphone.
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-outline-secondary me-md-2" onclick="return confirm('Êtes-vous sûr de vouloir annuler le paiement ?');">
                                <i class="fas fa-times me-2"></i>Annuler le paiement
                            </a>
                            <button type="submit" class="btn btn-primary btn-pay">
                                <i class="fas fa-mobile-alt me-2"></i>Initier le paiement Airtel Money
                            </button>
                        </div>
                    </form>
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
    --blanc-or-fonce: #f8fafc;
}

/* Scoper TOUS les styles uniquement au contenu de cette page */
.payment-page-container {
    background-color: #f8f9fc;
}

/* Les styles de cette page sont tous scopés avec .payment-page-container */
/* Le footer est dans le layout et n'est pas affecté */

/* Forcer toutes les icônes en bleu dans le contenu de cette page uniquement - EXCLURE le footer */
.payment-page-container i.fas:not(footer i):not(.footer-modern i),
.payment-page-container i.far:not(footer i):not(.footer-modern i),
.payment-page-container i.fab:not(footer i):not(.footer-modern i),
.payment-page-container i.fal:not(footer i):not(.footer-modern i),
.payment-page-container .card i.fas:not(footer i):not(.footer-modern i),
.payment-page-container .card i.far:not(footer i):not(.footer-modern i),
.payment-page-container .card i.fab:not(footer i):not(.footer-modern i),
.payment-page-container .card i.fal:not(footer i):not(.footer-modern i) {
    color: var(--bleu-nuit, #0f1a3d) !important;
}

/* Exception pour les icônes dans le header de la card et les boutons */
.payment-page-container .card-header.bg-gradient-primary i,
.payment-page-container .card-header.bg-gradient-primary .btn i,
.payment-page-container .btn-primary i,
.payment-page-container .btn-primary:hover i,
.payment-page-container .btn-primary:focus i,
.payment-page-container .btn-primary:active i,
.payment-page-container [style*="background-color: var(--bleu-nuit)"] i,
.payment-page-container [style*="background: var(--bleu-nuit)"] i {
    color: #ffffff !important;
}

/* Card styles - scoper à cette page uniquement */
.payment-page-container .card {
    border-radius: 1rem;
    border: none;
}

.payment-page-container .card-header.bg-gradient-primary,
.payment-page-container .bg-primary {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%) !important;
    padding: 1.5rem;
    color: white !important;
}

.payment-page-container .card-header.bg-gradient-primary i,
.payment-page-container .bg-primary i {
    color: white !important;
}

/* Table styles - scoper à cette page uniquement */
.payment-page-container .table {
    margin-bottom: 0;
}

.payment-page-container .table th {
    font-weight: 600;
    color: var(--bleu-nuit);
}

.payment-page-container .table-hover tbody tr:hover {
    background-color: rgba(15, 26, 61, 0.03);
    transition: background-color 0.2s ease;
}

/* Payment method cards - scoper à cette page uniquement */
.payment-page-container .payment-method-card {
    border-radius: 1rem;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    height: 100%;
    min-height: 160px;
    box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    background-color: white;
    position: relative;
}

/* Ajout d'une image de fond qui prend tout l'espace */
.payment-page-container .payment-method-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0.08;
    transition: opacity 0.3s ease;
    z-index: 1;
}

.payment-page-container .payment-method-card[data-method="mtn"]::before {
    background-image: url("{{ asset('images/mtnmoney.jpg') }}");
}

.payment-page-container .payment-method-card[data-method="airtel"]::before {
    background-image: url("{{ asset('images/airtelmoney.jpg') }}");
}

.payment-page-container .payment-method-card[data-method="visa"]::before {
    background-image: url("{{ asset('images/visa.jpg') }}");
}

.payment-page-container .payment-method-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
}

.payment-page-container .payment-method-card:hover::before {
    opacity: 0.15;
}

.payment-page-container .payment-method-card.active {
    border-color: var(--blanc-or);
    box-shadow: 0 0 0 2px var(--blanc-or);
}

.payment-page-container .payment-method-card.active::before {
    opacity: 0.2;
}

.payment-page-container .payment-method-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    height: 100%;
    width: 100%;
    padding: 1.5rem;
    cursor: pointer;
    color: var(--bleu-nuit);
    position: relative;
    z-index: 2;
}

.payment-page-container .payment-logo-wrapper {
    margin-bottom: 1rem;
}

.payment-page-container .payment-logo {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--bleu-nuit);
    margin: 0 auto;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.payment-page-container .payment-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.payment-page-container .payment-method-info {
    width: 100%;
}

.payment-page-container .payment-method-badge {
    display: inline-block;
    padding: 0.35em 0.65em;
    background-color: #f0f2f5;
    color: var(--bleu-nuit);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

/* Payment sections - scoper à cette page uniquement */
.payment-page-container .payment-section {
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 1rem;
    border-left: 4px solid var(--bleu-nuit);
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
}

/* Payment summary - scoper à cette page uniquement */
.payment-page-container .payment-summary {
    background-color: var(--bleu-nuit);
    color: white;
}

.payment-page-container .amount-display {
    background-color: rgba(255, 255, 255, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
}

/* Form controls - scoper à cette page uniquement */
.payment-page-container .form-control {
    border: 1px solid #e1e5eb;
    padding: 0.75rem 1rem;
}

.payment-page-container .form-control:focus {
    border-color: var(--bleu-nuit-clair);
    box-shadow: 0 0 0 0.25rem rgba(15, 26, 61, 0.15);
}

.payment-page-container .input-group-text {
    border: none;
}

/* Button styles - scoper à cette page uniquement */
.payment-page-container .btn-primary,
.payment-page-container .btn-primary:not(:disabled):not(.disabled) {
    background-color: var(--bleu-nuit) !important;
    border-color: var(--bleu-nuit) !important;
    color: white !important;
    transition: all 0.3s ease;
}

.payment-page-container .btn-primary:hover,
.payment-page-container .btn-primary:focus,
.payment-page-container .btn-primary:active,
.payment-page-container .btn-primary:not(:disabled):not(.disabled):hover,
.payment-page-container .btn-primary:not(:disabled):not(.disabled):focus,
.payment-page-container .btn-primary:not(:disabled):not(.disabled):active {
    background-color: var(--bleu-nuit-clair) !important;
    border-color: var(--bleu-nuit-clair) !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(15, 26, 61, 0.2);
}

.payment-page-container .btn-primary i,
.payment-page-container .btn-primary:hover i,
.payment-page-container .btn-primary:focus i,
.payment-page-container .btn-primary:active i {
    color: white !important;
}

.payment-page-container .payment-btn {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.payment-page-container .payment-btn::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.6s ease;
}

.payment-page-container .payment-btn:hover::after {
    left: 100%;
}

/* Text colors - scoper à cette page uniquement */
.payment-page-container .text-primary {
    color: var(--bleu-nuit) !important;
}

.payment-page-container .text-warning {
    color: var(--blanc-or) !important;
}

.payment-page-container .text-success {
    color: var(--bleu-nuit) !important;
}

.payment-page-container .text-danger {
    color: var(--bleu-nuit) !important;
}

.payment-page-container .bg-success {
    background-color: var(--bleu-nuit) !important;
}

.payment-page-container .bg-danger {
    background-color: var(--bleu-nuit) !important;
}

/* Phone error message style - scoper à cette page uniquement */
.payment-page-container .phone-error-message {
    display: none;
    color: var(--bleu-nuit, #0f1a3d);
    font-size: 0.875rem;
    margin-top: 0.5rem;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.payment-page-container .shake-input {
    animation: shake 0.6s cubic-bezier(.36,.07,.19,.97) both;
    border-color: var(--bleu-nuit, #0f1a3d) !important;
    box-shadow: 0 0 0 0.25rem rgba(15, 26, 61, 0.25) !important;
}

/* Utilities - scoper à cette page uniquement */
.payment-page-container .border-bottom {
    border-color: rgba(15, 26, 61, 0.1) !important;
}

/* Ticket summary card - scoper à cette page uniquement */
.payment-page-container .ticket-summary-card {
    background: linear-gradient(135deg, rgba(15, 26, 61, 0.05) 0%, rgba(26, 35, 126, 0.05) 100%);
    border: 2px solid rgba(15, 26, 61, 0.1);
    border-radius: 1rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.payment-page-container .ticket-summary-card:hover {
    border-color: var(--bleu-nuit);
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.1);
}

.payment-page-container .ticket-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.payment-page-container .ticket-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.payment-page-container .ticket-details {
    flex: 1;
}

.payment-page-container .ticket-details h5 {
    color: var(--bleu-nuit);
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.payment-page-container .ticket-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    color: #6b7280;
    font-size: 0.9rem;
}

.payment-page-container .ticket-meta i {
    color: var(--bleu-nuit);
}

.payment-page-container .ticket-name {
    background: rgba(15, 26, 61, 0.1);
    padding: 0.25rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.85rem;
    color: var(--bleu-nuit);
    font-weight: 600;
}

.payment-page-container .ticket-amount {
    text-align: right;
}

.payment-page-container .amount-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.payment-page-container .amount-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--bleu-nuit);
}

/* Payment option cards - scoper à cette page uniquement */
.payment-page-container .payment-option-card {
    position: relative;
    display: block;
    cursor: pointer;
}

.payment-page-container .payment-option-input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.payment-page-container .payment-option-content {
    border: 2px solid #e1e5eb;
    border-radius: 1rem;
    padding: 1.5rem;
    background: white;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.payment-page-container .payment-option-card:hover .payment-option-content {
    border-color: var(--bleu-nuit);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.1);
}

.payment-page-container .payment-option-input:checked + .payment-option-content {
    border-color: var(--bleu-nuit);
    background: linear-gradient(135deg, rgba(15, 26, 61, 0.05) 0%, rgba(26, 35, 126, 0.05) 100%);
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.payment-page-container .payment-option-header {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.payment-page-container .payment-option-logo {
    height: 40px;
    width: auto;
    object-fit: contain;
}

.payment-page-container .payment-option-name {
    font-weight: 600;
    color: var(--bleu-nuit);
    font-size: 1.1rem;
}

.payment-page-container .payment-option-check {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 30px;
    height: 30px;
    background: var(--bleu-nuit);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s ease;
}

.payment-page-container .payment-option-input:checked + .payment-option-content .payment-option-check {
    opacity: 1;
    transform: scale(1);
}

.payment-page-container .btn-pay {
    min-width: 200px;
    font-weight: 600;
    font-size: 1.1rem;
    padding: 0.75rem 2rem;
}

.payment-page-container .btn-outline-secondary {
    border-color: #e1e5eb;
    color: #6b7280;
}

.payment-page-container .btn-outline-secondary:hover {
    background-color: #f3f4f6;
    border-color: #d1d5db;
    color: #374151;
}

/* Alert styles - scoper à cette page uniquement */
.payment-page-container .alert-info {
    background-color: var(--bleu-nuit) !important;
    color: white !important;
    border: none !important;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .payment-page-container .payment-logo {
        width: 60px;
        height: 60px;
    }

    .payment-page-container .payment-method-card {
        min-height: 140px;
    }
    
    .payment-page-container .ticket-info {
        flex-direction: column;
        text-align: center;
    }
    
    .payment-page-container .ticket-amount {
        text-align: center;
    }
    
    .payment-page-container .btn-pay {
        width: 100%;
        min-width: auto;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du numéro de téléphone
    const phoneInput = document.getElementById('phone');
    const phoneError = document.querySelector('.invalid-feedback');
    const formText = phoneInput?.nextElementSibling;
    
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            // Nettoyer le numéro (ne garder que les chiffres)
            this.value = this.value.replace(/\D/g, '');
            
            // Valider le format
            const phoneRegex = /^0[456][0-9]{7}$/;
            const isValid = phoneRegex.test(this.value);
            
            if (this.value.length > 0 && !isValid) {
                this.classList.add('is-invalid');
                if (phoneError) {
                    phoneError.textContent = 'Veuillez respecter le format requis: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx (9 chiffres)';
                    phoneError.style.display = 'block';
                }
            } else {
                this.classList.remove('is-invalid');
                if (phoneError) {
                    phoneError.style.display = 'none';
                }
            }
        });
        
        phoneInput.addEventListener('blur', function() {
            const phoneRegex = /^0[456][0-9]{7}$/;
            const isValid = phoneRegex.test(this.value);
            
            if (this.value.length > 0 && !isValid) {
                this.classList.add('is-invalid');
                if (phoneError) {
                    phoneError.textContent = 'Veuillez respecter le format requis: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx (9 chiffres)';
                    phoneError.style.display = 'block';
                }
            }
        });
    }
    
    // Protection contre les doubles soumissions
    const paymentForm = document.querySelector('form');
    let isSubmitting = false;
    
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            // Vérifier qu'un opérateur est sélectionné
            const operatorSelected = document.querySelector('input[name="operator"]:checked');
            if (!operatorSelected) {
                e.preventDefault();
                alert('Veuillez sélectionner un mode de paiement (Airtel Money ou MTN Mobile Money).');
                return false;
            }
            
            // Valider le numéro de téléphone avant soumission
            if (phoneInput) {
                const phoneRegex = /^0[456][0-9]{7}$/;
                const isValid = phoneRegex.test(phoneInput.value);
                
                if (!phoneInput.value || !isValid) {
                    e.preventDefault();
                    phoneInput.classList.add('is-invalid');
                    if (phoneError) {
                        phoneError.textContent = 'Veuillez respecter le format requis: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx (9 chiffres)';
                        phoneError.style.display = 'block';
                    }
                    phoneInput.focus();
                    return false;
                }
            }
            
            // Vérifier la case de confirmation
            const confirmationCheckbox = document.getElementById('confirmation');
            if (confirmationCheckbox && !confirmationCheckbox.checked) {
                e.preventDefault();
                alert('Veuillez confirmer vouloir effectuer ce paiement avec Airtel Money.');
                return false;
            }
            
            // Empêcher les doubles soumissions
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            // Marquer comme en cours de soumission
            isSubmitting = true;
            
            // Désactiver le bouton de soumission
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement en cours...';
            }
            
            return true;
        });
    }
});
</script>
@endpush
