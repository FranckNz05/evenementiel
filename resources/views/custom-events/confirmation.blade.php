@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 confirmation-card">
                <div class="card-body p-5 text-center">
                    <!-- Success Icon Animation -->
                    <div class="success-icon-wrapper mb-4">
                        <div class="success-icon-circle">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>

                    <h2 class="display-5 fw-bold text-gradient mb-3">Paiement confirmé !</h2>
                    <p class="lead text-muted mb-4">
                        Votre formule <strong class="text-primary">{{ $plan['label'] }}</strong> 
                        ({{ number_format($plan['price'], 0, ',', ' ') }} FCFA) est activée.
                    </p>

                    <!-- Payment Method Badge -->
                    <div class="payment-badge-wrapper mb-4">
                        <div class="d-inline-flex align-items-center gap-3 p-3 bg-light rounded-pill">
                            @if($operator === 'airtel')
                                <img src="{{ asset('images/airtelmoney.jpg') }}" alt="Airtel Money" style="height: 32px;">
                            @elseif($operator === 'mtn')
                                <img src="{{ asset('images/mtnmoney.jpg') }}" alt="MTN Mobile Money" style="height: 32px;">
                            @endif
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="fas fa-check me-2"></i>Paiement simulé
                            </span>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="next-steps-card bg-light rounded-4 p-4 mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-rocket text-primary me-2"></i>Prochaine étape
                        </h5>
                        <p class="mb-0">
                            Vous pouvez maintenant créer votre événement personnalisé selon les fonctionnalités de la formule <strong>{{ $plan['label'] }}</strong>.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('custom-offers.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Retour aux formules
                        </a>
                        <a href="{{ route('custom-events.wizard.step1', ['purchase' => $purchaseId]) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-magic me-2"></i>Créer mon événement
                        </a>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center">
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Ceci est une confirmation de paiement simulée. Intégrez votre passerelle réelle ici plus tard.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .text-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .confirmation-card {
        border-radius: 20px;
        overflow: hidden;
    }

    .success-icon-wrapper {
        position: relative;
        display: inline-block;
    }

    .success-icon-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        animation: scaleIn 0.6s ease-out;
    }

    .success-icon-circle i {
        font-size: 4rem;
        color: white;
        animation: checkmark 0.6s ease-out 0.3s both;
    }

    @keyframes scaleIn {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes checkmark {
        from {
            transform: scale(0) rotate(-45deg);
        }
        to {
            transform: scale(1) rotate(0deg);
        }
    }

    .payment-badge-wrapper {
        display: flex;
        justify-content: center;
    }

    .next-steps-card {
        border-left: 4px solid #0d6efd;
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

    @media (max-width: 768px) {
        .display-5 {
            font-size: 2rem;
        }

        .success-icon-circle {
            width: 100px;
            height: 100px;
        }

        .success-icon-circle i {
            font-size: 3rem;
        }
    }
</style>
@endsection


