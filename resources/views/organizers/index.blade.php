@extends('layouts.app')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <!-- En-tête optimisé avec hiérarchie visuelle claire -->
            <header class="page-header mb-4 mb-md-5 organizer-header-banner">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-8">
                        <div class="organizer-header-content">
                            <h1 class="h2 h1-md fw-bold text-white mb-2">Organisateurs d'Événements</h1>
                            <p class="text-white-50 mb-0 fs-6">Découvrez les organisateurs professionnels et suivez leurs événements</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 text-md-end">
                        @auth
                            @if(!auth()->user()->hasRole([2,3]))
                                <a href="{{ route('profile.edit') }}" 
                                   class="btn btn-warning btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-2 organizer-btn-header"
                                   aria-label="Devenir organisateur d'événements">
                                    <i class="fas fa-user-plus" aria-hidden="true"></i>
                                    <span>Devenir organisateur</span>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" 
                               class="btn btn-warning btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-2 organizer-btn-header"
                               aria-label="Se connecter pour accéder aux fonctionnalités">
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                                <span>Connexion</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Messages flash avec meilleure accessibilité -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" 
                     role="alert"
                     aria-live="polite">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fas fa-check-circle mt-1 flex-shrink-0" aria-hidden="true"></i>
                        <div class="flex-grow-1">{{ session('success') }}</div>
                        <button type="button" 
                                class="btn-close" 
                                data-bs-dismiss="alert" 
                                aria-label="Fermer le message"></button>
                    </div>
                </div>
            @endif

            <!-- Barre de recherche et filtres (optionnel) -->
            <div class="search-filter-section mb-4 d-none" id="search-section">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-8">
                                <label for="search-organizers" class="form-label small text-muted mb-2">
                                    Rechercher un organisateur
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control border-start-0" 
                                           id="search-organizers"
                                           placeholder="Nom, ville, type d'événement..."
                                           aria-label="Rechercher un organisateur">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="button" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-filter me-2"></i>Filtres
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des organisateurs avec grid responsive -->
            <div class="organizers-list" role="region" aria-label="Liste des organisateurs">
                <div class="row g-4" id="organizers-container">
                    @forelse($organizers as $organizer)
                        <div class="col-12 col-sm-6 col-lg-4 organizer-card">
                            <article class="card border-0 shadow-sm h-100 organizer-card-item">
                                <!-- Image de profil ronde -->
                                <div class="organizer-profile-image-wrapper text-center pt-4">
                                    @if($organizer->logo)
                                        <img src="{{ asset('storage/' . $organizer->logo) }}" 
                                             class="organizer-profile-image rounded-circle" 
                                             alt="Profil de {{ $organizer->company_name }}"
                                             loading="lazy"
                                             width="120"
                                             height="120">
                                    @else
                                        <div class="organizer-profile-placeholder rounded-circle d-inline-flex align-items-center justify-content-center" 
                                             role="img"
                                             aria-label="Profil par défaut pour {{ $organizer->company_name }}">
                                            <span class="placeholder-initial-profile" aria-hidden="true">
                                                {{ substr($organizer->company_name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Nom de l'organisateur -->
                                <div class="text-center mb-3 mt-2">
                                    <h2 class="card-title h5 fw-bold text-dark mb-0">
                                        {{ $organizer->company_name }}
                                    </h2>
                                </div>

                                <!-- Contenu de la carte -->
                                <div class="card-body d-flex flex-column">
                                    <!-- Stats de l'organisateur -->
                                    <div class="organizer-stats-container d-flex gap-3 mb-3">
                                        <div class="organizer-stats-item d-flex align-items-center gap-2">
                                            <div class="stat-icon" aria-hidden="true">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-label small text-muted">Événements</div>
                                                <div class="stat-value fw-semibold">
                                                    {{ $organizer->events_count ?? 0 }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="organizer-stats-item d-flex align-items-center gap-2">
                                            <div class="stat-icon stat-icon-followers" aria-hidden="true">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-label small text-muted">Abonnés</div>
                                                <div class="stat-value fw-semibold">
                                                    {{ format_number($organizer->followers_count ?? 0) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    @if($organizer->description)
                                        <p class="card-text text-muted small mb-4 flex-grow-1">
                                            {{ Str::limit($organizer->description, 100) }}
                                        </p>
                                    @endif

                                    <!-- Actions -->
                                    <div class="card-actions d-flex gap-2 mt-auto">
                                        <a href="{{ route('organizers.show', $organizer->slug) }}" 
                                           class="btn btn-primary flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2 organizer-btn-primary"
                                           aria-label="Voir le profil de {{ $organizer->company_name }}">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                            <span>Voir le profil</span>
                                        </a>
                                        @auth
                                            @if(!auth()->user()->hasRole([2,3]))
                                                <form action="{{ route('organizers.follow', $organizer->slug) }}" 
                                                      method="POST" 
                                                      class="follow-form flex-shrink-0">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center"
                                                            style="width: 44px; height: 44px;"
                                                            aria-label="Suivre {{ $organizer->company_name }}"
                                                            data-bs-toggle="tooltip"
                                                            title="Suivre">
                                                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" 
                                               class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center"
                                               style="width: 44px; height: 44px;"
                                               aria-label="Se connecter pour suivre"
                                               data-bs-toggle="tooltip"
                                               title="Suivre">
                                                <i class="fas fa-user-plus" aria-hidden="true"></i>
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <!-- État vide amélioré -->
                        <div class="col-12">
                            <div class="empty-state text-center py-5">
                                <div class="empty-state-icon mb-4" aria-hidden="true">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3 class="h4 text-dark mb-3">Aucun organisateur disponible</h3>
                                <p class="text-muted mb-4">
                                    Soyez le premier à rejoindre notre communauté d'organisateurs professionnels
                                </p>
                                @auth
                                    @if(!auth()->user()->hasRole([2,3]))
                                        <a href="{{ route('organizer.request.create') }}" 
                                           class="btn btn-primary btn-lg shadow-sm d-inline-flex align-items-center gap-2 organizer-btn-primary">
                                            <i class="fas fa-user-plus" aria-hidden="true"></i>
                                            <span>Devenir organisateur</span>
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="btn btn-primary btn-lg shadow-sm d-inline-flex align-items-center gap-2 organizer-btn-primary">
                                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                                        <span>Se connecter</span>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Skeleton Loading avec meilleure UX -->
                <div class="row g-4 d-none" id="skeleton-loading" aria-hidden="true">
                    @for($i = 0; $i < 6; $i++)
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="skeleton skeleton-img"></div>
                                <div class="card-body">
                                    <div class="skeleton skeleton-line skeleton-title mb-3"></div>
                                    <div class="skeleton skeleton-line skeleton-text mb-2"></div>
                                    <div class="skeleton skeleton-line skeleton-text w-75 mb-4"></div>
                                    <div class="d-flex gap-2">
                                        <div class="skeleton skeleton-button flex-grow-1"></div>
                                        <div class="skeleton skeleton-button-icon"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Pagination améliorée -->
            @if($organizers->hasPages())
                <div class="mt-5">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <!-- Premier -->
                            <li class="page-item {{ $organizers->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $organizers->url(1) }}" aria-label="First">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <!-- Précédent -->
                            <li class="page-item {{ $organizers->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $organizers->previousPageUrl() }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Pages -->
                            @foreach(range(1, $organizers->lastPage()) as $i)
                                @if($i == 1 || $i == $organizers->lastPage() || ($i >= $organizers->currentPage() - 2 && $i <= $organizers->currentPage() + 2))
                                    <li class="page-item {{ $organizers->currentPage() == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $organizers->url($i) }}">{{ $i }}</a>
                                    </li>
                                @elseif(($i == $organizers->currentPage() - 3 || $i == $organizers->currentPage() + 3) && $organizers->lastPage() > 7)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endforeach

                            <!-- Suivant -->
                            <li class="page-item {{ !$organizers->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $organizers->nextPageUrl() }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <!-- Dernier -->
                            <li class="page-item {{ !$organizers->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $organizers->url($organizers->lastPage()) }}" aria-label="Last">
                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Variables CSS pour cohérence */
    :root {
        --organizer-card-radius: 12px;
        --organizer-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        --organizer-shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.12);
        --organizer-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --organizer-gradient: linear-gradient(135deg, var(--bleu-nuit, #1a365d) 0%, var(--bleu-nuit-clair, #2c5282) 100%);
        --organizer-primary: var(--bleu-nuit, #0d6efd);
        --organizer-primary-hover: var(--bleu-nuit-clair, #0056b3);
    }

    /* En-tête avec bannière bleue */
    .organizer-header-banner {
        background: var(--bleu-nuit, #1a365d) !important;
        padding: 2.5rem 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        animation: fadeInDown 0.5s ease;
        position: relative;
        overflow: hidden;
        border: none !important;
    }

    /* Retirer tout logo en arrière-plan */
    .organizer-header-banner::before,
    .organizer-header-banner::after {
        display: none !important;
        content: none !important;
        background-image: none !important;
    }

    .organizer-header-banner {
        background-image: none !important;
    }

    .page-header h1 {
        color: #ffffff !important;
        font-weight: 700;
    }

    .page-header p {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    /* Contenu du header sans fond supplémentaire */
    .organizer-header-content {
        padding: 0;
        background: transparent;
    }

    /* Bouton header amélioré */
    .organizer-btn-header {
        background-color: var(--blanc-or, #ffc107) !important;
        border-color: var(--blanc-or, #ffc107) !important;
        color: #000000 !important;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .organizer-btn-header:hover {
        background-color: #e6a800 !important;
        border-color: #e6a800 !important;
        color: #000000 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
    }

    .organizer-btn-header i {
        color: #000000 !important;
    }

    /* Assurer qu'aucun élément enfant n'a de logo en arrière-plan */
    .organizer-header-banner * {
        background-image: none !important;
    }

    .organizer-header-banner [class*="logo"],
    .organizer-header-banner [id*="logo"] {
        display: none !important;
    }

    /* Responsive pour l'en-tête */
    @media (max-width: 767.98px) {
        .organizer-header-banner {
            padding: 1.5rem 1.25rem;
        }

        .organizer-header-banner .col-md-4 {
            text-align: left !important;
            margin-top: 1rem;
        }

        .organizer-btn-header {
            width: 100%;
            justify-content: center;
        }
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Cartes organisateurs optimisées */
    .organizer-card-item {
        border-radius: var(--organizer-card-radius);
        transition: var(--organizer-transition);
        overflow: hidden;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }

    .organizer-card-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        border-color: rgba(13, 110, 253, 0.2) !important;
    }

    .organizer-card-item:focus-within {
        outline: 3px solid rgba(13, 110, 253, 0.25);
        outline-offset: 2px;
    }

    /* Image de profil ronde */
    .organizer-profile-image-wrapper {
        margin-bottom: 1rem;
    }

    .organizer-profile-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: var(--organizer-transition);
    }

    .organizer-card-item:hover .organizer-profile-image {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    /* Placeholder pour profil */
    .organizer-profile-placeholder {
        width: 120px;
        height: 120px;
        background: var(--organizer-gradient);
        border: 4px solid #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .placeholder-initial-profile {
        font-size: 3rem;
        font-weight: 700;
        color: white;
        text-transform: uppercase;
    }

    /* Stats de l'organisateur */
    .organizer-stats-container {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .organizer-stats-item {
        flex: 1;
        min-width: 120px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .organizer-stats {
        padding: 0.75rem;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--bleu-nuit, #1a365d) 0%, var(--bleu-nuit-clair, #2c5282) 100%);
        color: white;
        border-radius: 8px;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .stat-icon-followers {
        background: linear-gradient(135deg, var(--blanc-or, #ffc107) 0%, #e6a800 100%) !important;
    }
    /* Icônes des stats (événements, abonnés) en blanc */
    .organizer-stats-item .stat-icon i {
        color: #ffffff !important;
    }

    .stat-label {
        line-height: 1.2;
    }

    .stat-value {
        font-size: 1.125rem;
        line-height: 1.2;
        color: #212529;
    }

    /* Actions de carte */
    .card-actions .btn {
        min-height: 44px;
        font-weight: 500;
    }

    /* Correction des boutons bleus avec texte blanc */
    .organizer-btn-primary,
    .organizer-btn-primary:hover,
    .organizer-btn-primary:focus,
    .organizer-btn-primary:active {
        background-color: var(--bleu-nuit, #0d6efd) !important;
        border-color: var(--bleu-nuit, #0d6efd) !important;
        color: #ffffff !important;
        text-decoration: none;
    }

    .organizer-btn-primary:hover {
        background-color: var(--bleu-nuit-clair, #0056b3) !important;
        border-color: var(--bleu-nuit-clair, #0056b3) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .organizer-btn-primary:active {
        transform: translateY(0);
    }

    .organizer-btn-primary i {
        color: #ffffff !important;
    }
    /* Forcer le texte du bouton "Voir le profil" en blanc */
    .organizer-btn-primary span {
        color: #ffffff !important;
    }

    /* Amélioration des boutons outline */
    .btn-outline-primary {
        color: var(--bleu-nuit, #0d6efd) !important;
        border-color: var(--bleu-nuit, #0d6efd) !important;
        background-color: transparent !important;
    }

    .btn-outline-primary:hover {
        background-color: var(--bleu-nuit, #0d6efd) !important;
        border-color: var(--bleu-nuit, #0d6efd) !important;
        color: #ffffff !important;
    }

    .btn-outline-primary i {
        color: inherit !important;
    }

    /* État vide */
    .empty-state {
        animation: fadeIn 0.5s ease;
    }

    .empty-state-icon i {
        font-size: 4rem;
        color: #adb5bd;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* Skeleton loading amélioré */
    .skeleton {
        background: linear-gradient(
            90deg,
            #f0f0f0 0%,
            #e8e8e8 20%,
            #e8e8e8 40%,
            #f0f0f0 100%
        );
        background-size: 200% 100%;
        animation: shimmer 1.8s infinite ease-in-out;
        border-radius: 8px;
    }

    .skeleton-img {
        aspect-ratio: 16 / 10;
        border-radius: var(--organizer-card-radius) var(--organizer-card-radius) 0 0;
    }

    .skeleton-line {
        height: 14px;
        margin-bottom: 0.5rem;
    }

    .skeleton-title {
        height: 20px;
        width: 70%;
    }

    .skeleton-text {
        width: 100%;
    }

    .skeleton-button {
        height: 44px;
        border-radius: 6px;
    }

    .skeleton-button-icon {
        width: 44px;
        height: 44px;
        border-radius: 6px;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    /* Responsive */
    @media (max-width: 767.98px) {
        .page-header {
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.75rem;
        }

        .card-actions {
            flex-direction: column;
        }
        
        .card-actions .follow-form {
            width: 100%;
        }
        
        .card-actions .follow-form button {
            width: 100% !important;
        }

        .organizer-btn-primary {
            width: 100%;
            justify-content: center;
        }
    }

    /* Focus visible pour accessibilité */
    .btn:focus-visible,
    a:focus-visible {
        outline: 3px solid rgba(13, 110, 253, 0.5);
        outline-offset: 3px;
    }

    /* Pagination améliorée comme /direct-events */
    .pagination .page-item {
        margin: 0 3px;
    }

    .pagination .page-link {
        color: var(--bleu-nuit, #0d6efd);
        border: 1px solid #dee2e6;
        border-radius: var(--border-radius);
        padding: 0.5rem 0.9rem;
        transition: var(--transition);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--bleu-nuit, #0d6efd);
        border-color: var(--bleu-nuit, #0d6efd);
        color: white;
    }

    .pagination .page-link:hover {
        background-color: #f0f0f0;
        color: var(--bleu-nuit-clair, #0056b3);
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        cursor: not-allowed;
    }

    /* Amélioration des transitions */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Espacement des gaps pour anciens navigateurs */
    @supports not (gap: 1rem) {
        .d-flex.gap-2 > * + * {
            margin-left: 0.5rem;
        }
        .d-flex.gap-3 > * + * {
            margin-left: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // Initialisation des tooltips Bootstrap 5
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Gestion du skeleton loading
    const container = document.getElementById('organizers-container');
    const skeleton = document.getElementById('skeleton-loading');
    
    if (container && skeleton && container.children.length === 0) {
        skeleton.classList.remove('d-none');
        skeleton.setAttribute('aria-busy', 'true');
        
        // Simulation du chargement (remplacer par votre logique AJAX)
        setTimeout(() => {
            skeleton.classList.add('d-none');
            skeleton.setAttribute('aria-busy', 'false');
        }, 1500);
    }
    
    // Animation des cartes au scroll (Intersection Observer)
    if ('IntersectionObserver' in window) {
        const cards = document.querySelectorAll('.organizer-card');
        
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 100);
                    cardObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });
        
        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            cardObserver.observe(card);
        });
    }
    
    // Gestion optimisée des formulaires de suivi
    const followForms = document.querySelectorAll('.follow-form');
    
    followForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            
            if (button) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                button.setAttribute('aria-busy', 'true');
            }
        });
    });
    
    // Gestion de la recherche (si activée)
    const searchInput = document.getElementById('search-organizers');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.toLowerCase().trim();
                filterOrganizers(searchTerm);
            }, 300);
        });
    }
    
    function filterOrganizers(searchTerm) {
        const cards = document.querySelectorAll('.organizer-card');
        let visibleCount = 0;
        
        cards.forEach(card => {
            const cardText = card.textContent.toLowerCase();
            const shouldShow = !searchTerm || cardText.includes(searchTerm);
            
            card.style.display = shouldShow ? '' : 'none';
            if (shouldShow) visibleCount++;
        });
        
        // Afficher un message si aucun résultat
        const noResults = document.getElementById('no-results-message');
        if (visibleCount === 0 && !noResults) {
            const message = document.createElement('div');
            message.id = 'no-results-message';
            message.className = 'col-12 text-center py-5';
            message.innerHTML = `
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Aucun résultat trouvé</h4>
                <p class="text-muted">Essayez avec d'autres mots-clés</p>
            `;
            container.appendChild(message);
        } else if (visibleCount > 0 && noResults) {
            noResults.remove();
        }
    }
    
    // Performance: Lazy loading des images (si pas natif)
    if ('loading' in HTMLImageElement.prototype === false) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
});
</script>
@endpush