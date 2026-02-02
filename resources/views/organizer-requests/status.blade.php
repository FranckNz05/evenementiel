@extends('layouts.dashboard')

@section('content')
<div class="container py-5">
    <!-- En-tête -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold mb-3">
            <i class="fas fa-clipboard-check text-primary me-3"></i>Statut de votre demande
        </h1>
        <p class="lead text-muted">Suivez l'évolution de votre candidature</p>
    </div>

    <div class="status-container">
        @if($request)
            <!-- Badge de statut -->
            <div class="status-badge-wrapper text-center mb-5">
                @php
                    $statusConfig = [
                        'pending' => [
                            'icon' => 'fa-hourglass-half',
                            'color' => 'warning',
                            'text' => 'En cours de traitement',
                            'message' => 'Votre demande est actuellement examinée par notre équipe'
                        ],
                        'approved' => [
                            'icon' => 'fa-check-circle',
                            'color' => 'success',
                            'text' => 'Approuvée',
                            'message' => 'Félicitations ! Votre demande a été approuvée'
                        ],
                        'rejected' => [
                            'icon' => 'fa-times-circle',
                            'color' => 'danger',
                            'text' => 'Rejetée',
                            'message' => 'Votre demande n\'a malheureusement pas été retenue'
                        ]
                    ];
                    $status = $statusConfig[$request->status] ?? $statusConfig['pending'];
                @endphp
                
                <div class="status-icon-large mb-4">
                    <i class="fas {{ $status['icon'] }} text-{{ $status['color'] }}"></i>
                </div>
                <h3 class="status-title text-{{ $status['color'] }} mb-2">{{ $status['text'] }}</h3>
                <p class="text-muted mb-4">{{ $status['message'] }}</p>
                <small class="text-muted">
                    <i class="far fa-calendar me-2"></i>Soumise le {{ $request->created_at->format('d/m/Y à H:i') }}
                </small>
            </div>

            <!-- Informations de la demande -->
            <div class="info-section mb-5">
                <h4 class="section-title mb-4">
                    <i class="fas fa-building text-primary me-2"></i>Informations de votre entreprise
                </h4>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-briefcase me-2"></i>Entreprise
                            </label>
                            <p class="info-value">{{ $request->company_name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <p class="info-value">{{ $request->email }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-phone me-2"></i>Téléphone
                            </label>
                            <p class="info-value">{{ $request->phone_primary }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-map-marker-alt me-2"></i>Adresse
                            </label>
                            <p class="info-value">{{ $request->address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motivation et Expérience -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="content-section">
                        <h5 class="content-title">
                            <i class="fas fa-lightbulb me-2"></i>Motivation
                        </h5>
                        <p class="content-text">{{ $request->motivation }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="content-section">
                        <h5 class="content-title">
                            <i class="fas fa-award me-2"></i>Expérience
                        </h5>
                        <p class="content-text">{{ $request->experience }}</p>
                    </div>
                </div>
            </div>

            @if($request->status === 'rejected' && $request->rejection_reason)
                <div class="rejection-section mb-5">
                    <h5 class="rejection-title">
                        <i class="fas fa-info-circle me-2"></i>Raison du rejet
                    </h5>
                    <p class="rejection-text">{{ $request->rejection_reason }}</p>
                </div>
            @endif
        @else
            <!-- Aucune demande -->
            <div class="no-request-section text-center">
                <div class="no-request-icon mb-4">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="mb-3">Aucune demande en cours</h3>
                <p class="text-muted mb-4">Vous n'avez pas encore soumis de demande pour devenir organisateur sur notre plateforme.</p>
            </div>
        @endif

        <!-- Boutons d'action -->
        <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-5">
            @if(!$request)
                <a href="{{ route('organizer.request.create') }}" class="btn btn-lg btn-primary px-5 shadow-lg">
                    <i class="fas fa-plus me-2"></i>Soumettre une demande
                </a>
            @elseif($request->status === 'rejected')
                <a href="{{ route('organizer.request.create') }}" class="btn btn-lg btn-primary px-5 shadow-lg">
                    <i class="fas fa-redo me-2"></i>Soumettre une nouvelle demande
                </a>
            @endif
            <a href="{{ route('profile.edit') }}" class="btn btn-lg btn-outline-secondary px-5">
                <i class="fas fa-arrow-left me-2"></i>Retour au profil
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Design moderne pour la page de statut */
.status-container {
    max-width: 1000px;
    margin: 0 auto;
}

.status-icon-large {
    font-size: 5rem;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.status-title {
    font-weight: 700;
    font-size: 2rem;
}

.status-badge-wrapper {
    padding: 3rem 2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.info-section, .content-section, .rejection-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.section-title {
    font-weight: 700;
    color: #2c3e50;
    padding-bottom: 1rem;
    border-bottom: 3px solid #e9ecef;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #0d6efd 0%, #0dcaf0 100%);
    border-radius: 10px;
}

.info-item {
    margin-bottom: 1.5rem;
}

.info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    display: block;
}

.info-value {
    font-size: 1rem;
    color: #2c3e50;
    margin: 0;
    padding-left: 1.75rem;
}

.content-section {
    height: 100%;
}

.content-title {
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.content-text {
    color: #495057;
    line-height: 1.7;
    text-align: justify;
}

.rejection-section {
    background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
    border-left: 4px solid #dc3545;
}

.rejection-title {
    font-weight: 700;
    color: #dc3545;
    margin-bottom: 1rem;
}

.rejection-text {
    color: #721c24;
    line-height: 1.7;
}

.no-request-section {
    padding: 5rem 2rem;
}

.no-request-icon {
    font-size: 5rem;
    color: #dee2e6;
}

.btn-lg {
    padding: 0.875rem 2.5rem;
    font-weight: 600;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0a58ca 0%, #0bb5d8 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    transform: translateY(-2px);
}

/* Animation d'entrée */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.status-badge-wrapper,
.info-section,
.content-section,
.no-request-section {
    animation: fadeInUp 0.6s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .status-icon-large {
        font-size: 3.5rem;
    }
    
    .status-title {
        font-size: 1.5rem;
    }
    
    .info-section, .content-section, .rejection-section {
        padding: 1.5rem;
    }
    
    .btn-lg {
        width: 100%;
        padding: 1rem;
    }
}
</style>
@endpush
@endsection
