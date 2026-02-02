@extends('layouts.dashboard')

@section('title', 'Créer un événement')

@section('content')
@php
    $user = auth()->user();
    $isOrganizer = $user->hasRole(2) || $user->hasRole(3) || $user->isOrganizer() || $user->isAdmin();
@endphp

<div class="container-fluid px-4 py-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-gradient mb-3">Créer votre événement</h1>
                <p class="lead text-muted">Choisissez le type d'événement qui correspond à vos besoins</p>
                </div>

            <!-- Event Types Cards -->
            <div class="row g-4 justify-content-center">
                        <!-- Événement Standard -->
                <div class="col-lg-5 col-md-6">
                    <div class="card h-100 border-0 shadow-lg event-type-card event-type-standard">
                        <div class="card-body p-5 text-center position-relative">
                            <div class="event-icon-wrapper mb-4">
                                <div class="event-icon bg-primary-gradient">
                                    <i class="fas fa-calendar-alt fa-3x text-white"></i>
                                </div>
                                    </div>
                            <h3 class="h4 fw-bold mb-3">Événement Standard</h3>
                                    <p class="text-muted mb-4">
                                        Créez un événement avec des billets, des catégories et des options de paiement standard.
                                Idéal pour les événements publics avec vente de billets.
                            </p>
                            <div class="features-list mb-4 text-start">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                    <span>Vente de billets en ligne</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                    <span>Gestion des catégories</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                    <span>Paiements sécurisés</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                    <span>Statistiques détaillées</span>
                                </div>
                            </div>
                            @if($isOrganizer)
                                <a href="{{ route('events.wizard.step1') }}" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-rocket me-2"></i>Créer maintenant
                                </a>
                            @else
                                <button type="button" class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#becomeOrganizerModal">
                                    <i class="fas fa-lock me-2"></i>Devenir Organisateur
                                </button>
                            @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Événement Personnalisé -->
                <div class="col-lg-5 col-md-6">
                    <div class="card h-100 border-0 shadow-lg event-type-card event-type-custom">
                        <div class="card-body p-5 text-center position-relative">
                            <div class="badge-popular">Populaire</div>
                            <div class="event-icon-wrapper mb-4">
                                <div class="event-icon bg-warning-gradient">
                                    <i class="fas fa-star fa-3x text-white"></i>
                                </div>
                            </div>
                            <h3 class="h4 fw-bold mb-3">Événement Personnalisé</h3>
                            <p class="text-muted mb-4">
                                Créez un événement personnalisé avec des options avancées de gestion des invités. 
                                Parfait pour les mariages, anniversaires, et événements privés.
                            </p>
                            <div class="features-list mb-4 text-start">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-warning me-2"></i>
                                    <span>Gestion des invités</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-warning me-2"></i>
                                    <span>Invitations personnalisées</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-warning me-2"></i>
                                    <span>Suivi en temps réel</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-warning me-2"></i>
                                    <span>Formules adaptées</span>
                                </div>
                            </div>
                            <a href="{{ route('custom-offers.index') }}" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-shopping-cart me-2"></i>Choisir une offre
                            </a>
                        </div>
                    </div>
                </div>
            </div>
                    
                    <!-- Modal Devenir Organisateur -->
                    <div class="modal fade" id="becomeOrganizerModal" tabindex="-1" aria-labelledby="becomeOrganizerModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="becomeOrganizerModalLabel">
                                        <i class="fas fa-info-circle me-2"></i>Devenir Organisateur
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center mb-4">
                                        <i class="fas fa-shield-alt fa-4x text-primary mb-3"></i>
                                        <h4 class="mb-3">Accès réservé aux organisateurs</h4>
                                        <p class="text-muted">
                                            Pour créer un événement simple avec des billets, vous devez devenir organisateur.
                                            <br><br>
                                            En tant qu'organisateur, vous pourrez :
                                        </p>
                                        <ul class="list-unstyled text-start d-inline-block">
                                            <li><i class="fas fa-check text-success me-2"></i>Créer des événements avec billets</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Gérer les ventes de billets</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Accéder aux statistiques détaillées</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Utiliser toutes les fonctionnalités avancées</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <a href="{{ route('organizer.request.create') }}" class="btn btn-primary">
                                        <i class="fas fa-user-tie me-2"></i>Devenir Organisateur
                                    </a>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        @php
                            $user = auth()->user();
                            $isOrganizer = $user->hasRole(2) || $user->hasRole(3) || $user->isOrganizer() || $user->isAdmin();
                        @endphp
                        @if($isOrganizer)
                        <a href="{{ route('organizer.events.index') }}" class="text-muted">
                            <i class="fas fa-arrow-left me-1"></i> Retour à la liste des événements
                        </a>
                        @else
                            <a href="{{ route('home') }}" class="text-muted">
                                <i class="fas fa-arrow-left me-1"></i> Retour à l'accueil
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Gradient Text */
    .text-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Event Type Cards */
    .event-type-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border-radius: 20px;
        overflow: hidden;
        position: relative;
    }

    .event-type-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .event-type-standard {
        border-top: 4px solid #0d6efd;
    }

    .event-type-custom {
        border-top: 4px solid #ffc107;
    }

    /* Icon Wrapper */
    .event-icon-wrapper {
        position: relative;
        display: inline-block;
    }

    .event-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        position: relative;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .bg-primary-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-warning-gradient {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    /* Popular Badge */
    .badge-popular {
        position: absolute;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    /* Features List */
    .features-list {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
    }

    .feature-item {
        padding: 10px 0;
        display: flex;
        align-items: center;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .feature-item:hover {
        transform: translateX(5px);
        color: #495057;
    }

    .feature-item i {
        font-size: 1.1rem;
    }

    /* Buttons */
    .btn-lg {
        padding: 15px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        color: white;
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        color: white;
    }

    /* Modal Improvements */
    .modal-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .event-type-card {
            margin-bottom: 2rem;
        }

        .display-4 {
            font-size: 2rem;
        }

        .event-icon {
            width: 80px;
            height: 80px;
        }

        .event-icon i {
            font-size: 2rem !important;
        }
    }

    /* Animation on load */
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

    .event-type-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .event-type-custom {
        animation-delay: 0.2s;
    }
</style>
@endsection
