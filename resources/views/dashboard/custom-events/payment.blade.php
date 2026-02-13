@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-lg border-0 payment-card">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="h3 fw-bold mb-0">
                        <i class="fas fa-credit-card me-2"></i>Paiement de la formule {{ $plan['label'] }}
                    </h2>
                </div>
                <div class="card-body p-5">
                    <div class="price-display text-center mb-4 p-4 bg-light rounded-4">
                        <p class="text-muted mb-2 small">Montant à payer</p>
                        <h3 class="display-6 fw-bold text-primary mb-0">
                            {{ number_format($plan['price'], 0, ',', ' ') }} <small class="fs-5">FCFA</small>
                        </h3>
                    </div>

                    <form action="{{ route('custom-offers.payment.process') }}" method="POST" novalidate>
                        @csrf
                        <input type="hidden" name="plan" value="{{ $planKey }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">
                                <i class="fas fa-mobile-alt me-2 text-primary"></i>Opérateur de paiement
                            </label>
                            <div class="d-flex gap-3 flex-wrap justify-content-center">
                                <label class="operator-option border-2 rounded-4 p-4 d-flex align-items-center gap-3" style="cursor: pointer; min-width: 220px; transition: all 0.3s;">
                                    <input type="radio" name="operator" value="airtel" class="form-check-input me-2" {{ old('operator') === 'airtel' ? 'checked' : '' }} required>
                                    <img src="{{ asset('images/airtelmoney.jpg') }}" alt="Airtel Money" style="height: 32px;">
                                    <span class="fw-semibold">Airtel Money</span>
                                </label>
                                <label class="operator-option border-2 rounded-4 p-4 d-flex align-items-center gap-3" style="cursor: not-allowed; min-width: 220px; opacity: 0.6; position: relative;">
                                    <input type="radio" name="operator" value="mtn" class="form-check-input me-2" disabled>
                                    <img src="{{ asset('images/mtnmoney.jpg') }}" alt="MTN Mobile Money" style="height: 32px;">
                                    <span class="fw-semibold">MTN Mobile Money</span>
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; white-space: nowrap; z-index: 10;">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Non disponible
                                    </div>
                                </label>
                            </div>
                            @error('operator')
                                <div class="text-danger small mt-2 text-center">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label fw-bold">
                                <i class="fas fa-phone me-2 text-primary"></i>Numéro Mobile Money
                            </label>
                            <input type="text" inputmode="numeric" pattern="^0(4|5|6)\\d{7}$" class="form-control form-control-lg @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Ex: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx" value="{{ old('phone') }}" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Formats acceptés: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx (9 chiffres).
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="{{ route('custom-offers.index', ['plan' => $planKey]) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock me-2"></i>Payer {{ number_format($plan['price'], 0, ',', ' ') }} FCFA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="card border-0 shadow-lg h-100 plan-details-card">
                <div class="card-header bg-gradient-primary text-white text-center py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-info-circle me-2"></i>Détails de la formule
                    </h5>
                </div>
                <div class="card-body p-4">
                    <ul class="plan-features-list mb-0">
                        @if($planKey === 'start')
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Jusqu'à 100 invités</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Invitations SMS automatiques</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Dashboard de base</span>
                            </li>
                            <li class="feature-item text-muted">
                                <i class="fas fa-times-circle me-2"></i>
                                <span>Pas d'ajout d'invités après création</span>
                            </li>
                            <li class="feature-item text-muted">
                                <i class="fas fa-times-circle me-2"></i>
                                <span>Pas d'URL de suivi temps réel</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-envelope text-success me-2"></i>
                                <span>Support email</span>
                            </li>
                        @elseif($planKey === 'standard')
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                <span>Jusqu'à 300 invités</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                <span>Ajout d'invités après création</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                <span>Programmation SMS</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                <span>Dashboard amélioré</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-primary me-2"></i>
                                <span>URL de suivi temps réel</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-comments text-primary me-2"></i>
                                <span>Support WhatsApp + email</span>
                            </li>
                        @elseif($planKey === 'premium')
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-info me-2"></i>
                                <span>Jusqu'à 800 invités</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-info me-2"></i>
                                <span>Ajout illimité</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-info me-2"></i>
                                <span>Dashboard temps réel</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-info me-2"></i>
                                <span>Statistiques automatiques</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-info me-2"></i>
                                <span>Rappels SMS J-1</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-phone text-info me-2"></i>
                                <span>Support WhatsApp + téléphone</span>
                            </li>
                        @else
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-danger me-2"></i>
                                <span>Jusqu'à 1 500 invités</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-danger me-2"></i>
                                <span>Dashboard mobile/tablette</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-danger me-2"></i>
                                <span>Exports Excel/PDF</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check-circle text-danger me-2"></i>
                                <span>Rappels 24h avant</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-headset text-danger me-2"></i>
                                <span>Assistance technique dédiée</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-card {
        border-radius: 20px;
        overflow: hidden;
    }

    .card-header.bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    .price-display {
        border: 2px dashed #0d6efd;
    }

    .operator-option {
        border-color: #dee2e6 !important;
    }

    .operator-option:hover {
        border-color: #0d6efd !important;
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .operator-option input[type="radio"]:checked + img + span,
    .operator-option:has(input[type="radio"]:checked) {
        border-color: #0d6efd !important;
        background-color: #e7f1ff;
    }

    .plan-details-card {
        border-radius: 20px;
    }

    .plan-features-list {
        list-style: none;
        padding: 0;
    }

    .plan-features-list .feature-item {
        padding: 12px 0;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f0f0f0;
    }

    .plan-features-list .feature-item:last-child {
        border-bottom: none;
    }

    .plan-features-list .feature-item i {
        font-size: 1.1rem;
        min-width: 24px;
    }

    .btn-lg {
        padding: 15px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .form-control-lg {
        padding: 15px;
        font-size: 1.1rem;
        border-radius: 12px;
    }

    @media (max-width: 768px) {
        .display-6 {
            font-size: 2rem;
        }

        .operator-option {
            min-width: 100% !important;
        }
    }
</style>
@endsection


