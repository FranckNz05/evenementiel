@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Bannière spécifique aux événements passés -->
    <div class="search-banner mb-5" style="background-color: var(--bleu-nuit); border-radius: var(--border-radius);">
        <div class="p-4 p-md-5 text-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-3">Événements passés</h2>
                    <p class="mb-0">Revivez les moments forts de nos précédents événements</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-secondary fs-5">
                        <i class="fas fa-history me-2"></i> Archives
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton Loading -->
    <div class="row skeleton-content">
        <!-- Filtres Skeleton -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4" id="filters-skeleton">
                <div class="card-body p-4">
                    <div class="skeleton skeleton-header skeleton-dark mb-4"></div>

                    <!-- Période -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <div class="skeleton skeleton-line short skeleton-dark" style="width: 40%"></div>
                            <div class="skeleton skeleton-line skeleton-dark" style="width: 15px; height: 15px;"></div>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Catégories -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <div class="skeleton skeleton-line short skeleton-dark" style="width: 40%"></div>
                            <div class="skeleton skeleton-line skeleton-dark" style="width: 15px; height: 15px;"></div>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Villes -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <div class="skeleton skeleton-line short skeleton-dark" style="width: 40%"></div>
                            <div class="skeleton skeleton-line skeleton-dark" style="width: 15px; height: 15px;"></div>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <div class="skeleton skeleton-line short skeleton-dark" style="width: 40%"></div>
                            <div class="skeleton skeleton-line skeleton-dark" style="width: 15px; height: 15px;"></div>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                                <div class="skeleton skeleton-chip skeleton-dark"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Reset -->
                    <div class="skeleton skeleton-chip skeleton-dark mt-4" style="height: 40px;"></div>
                </div>
            </div>
        </div>

        <!-- Événements Skeleton -->
        <div class="col-lg-9">
            <!-- Résultats et compteur -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="skeleton skeleton-line skeleton-dark" style="width: 30%; height: 20px;"></div>
                <div class="skeleton skeleton-view-toggle skeleton-dark"></div>
            </div>

            <!-- Vue Grille Skeleton -->
            <div class="row g-4 grid-view" id="events-grid-skeleton">
                @for($i = 0; $i < 6; $i++)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card skeleton-card">
                        <div class="skeleton skeleton-img"></div>
                        <div class="card-body">
                            <div class="skeleton skeleton-line mb-3"></div>
                            <div class="skeleton skeleton-line short mb-3"></div>
                            <div class="skeleton skeleton-line medium mb-3"></div>
                            <div class="d-flex justify-content-between">
                                <div class="skeleton skeleton-badge"></div>
                                <div class="skeleton skeleton-line-xs"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>

            <!-- Vue Liste Skeleton -->
            <div class="list-view" style="display: none;" id="events-list-skeleton">
                @for($i = 0; $i < 3; $i++)
                <div class="skeleton skeleton-list-item mb-3"></div>
                @endfor
            </div>

            <!-- Pagination Skeleton -->
            <div class="skeleton skeleton-pagination mt-5"></div>
        </div>
    </div>

    <!-- Contenu réel (masqué initialement) -->
    <div class="row real-content" style="display: none;">
        <!-- Filtres améliorés -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4 filter-card">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4 d-flex align-items-center">
                        <i class="fas fa-sliders-h me-2 text-blanc-or"></i>
                        <span>Filtrer par</span>
                    </h5>

                    <!-- Période -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 d-flex align-items-center">
                                <i class="far fa-calendar-alt me-2 text-blanc-or"></i>
                                Période
                            </h6>
                            <i class="fas fa-chevron-down filter-toggle"></i>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ app('toggleFilter')('periode', 'last_week') }}"
                                   class="filter-chip {{ request('periode') == 'last_week' ? 'active' : '' }}">
                                    <i class="fas fa-calendar-week me-2"></i> La semaine dernière
                                </a>
                                <a href="{{ app('toggleFilter')('periode', 'last_month') }}"
                                   class="filter-chip {{ request('periode') == 'last_month' ? 'active' : '' }}">
                                    <i class="fas fa-calendar me-2"></i> Le mois dernier
                                </a>
                                <a href="{{ app('toggleFilter')('periode', 'last_year') }}"
                                   class="filter-chip {{ request('periode') == 'last_year' ? 'active' : '' }}">
                                    <i class="fas fa-calendar-alt me-2"></i> L'année dernière
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Catégories -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 d-flex align-items-center">
                                <i class="fas fa-tags me-2 text-blanc-or"></i>
                                Catégories
                            </h6>
                            <i class="fas fa-chevron-down filter-toggle"></i>
                        </div>
                        <div class="filter-content">
                            <div class="category-list d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto;">
                                @foreach($categories as $category)
                                <a href="{{ app('toggleFilter')('category', $category->slug) }}"
                                   class="filter-chip {{ request('category') == $category->slug ? 'active' : '' }}">
                                    <i class="fas fa-{{ $category->icon ?? 'circle' }} me-2"></i>
                                    {{ $category->name }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Villes -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 d-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-2 text-blanc-or"></i>
                                Villes
                            </h6>
                            <i class="fas fa-chevron-down filter-toggle"></i>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                @foreach($villes as $ville)
                                <a href="{{ app('toggleFilter')('ville', $ville) }}"
                                   class="filter-chip {{ request('ville') == $ville ? 'active' : '' }}">
                                    <i class="fas fa-city me-2"></i> {{ $ville }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Type d'événement -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 d-flex align-items-center">
                                <i class="fas fa-calendar-alt me-2 text-blanc-or"></i>
                                Type
                            </h6>
                            <i class="fas fa-chevron-down filter-toggle"></i>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ app('toggleFilter')('event_type', 'Espace libre') }}"
                                   class="filter-chip {{ request('event_type') == 'Espace libre' ? 'active' : '' }}">
                                    <i class="fas fa-chair me-2"></i> Espace libre
                                </a>
                                <a href="{{ app('toggleFilter')('event_type', 'Plan de salle') }}"
                                   class="filter-chip {{ request('event_type') == 'Plan de salle' ? 'active' : '' }}">
                                    <i class="fas fa-th-large me-2"></i> Plan de salle
                                </a>
                                <a href="{{ app('toggleFilter')('event_type', 'Mixte') }}"
                                   class="filter-chip {{ request('event_type') == 'Mixte' ? 'active' : '' }}">
                                    <i class="fas fa-blender-phone me-2"></i> Mixte
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Reset -->
                    @if(request()->except('page'))
                    <div class="sticky-bottom pt-3">
                        <a href="{{ route('events.past') }}" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                    <i class="fas fa-sync-alt me-2"></i> Réinitialiser
                </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Liste des événements -->
        <div class="col-lg-9">
            <!-- Résultats et compteur -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h5 mb-0">
                    @if(request()->except('page'))
                        Résultats ({{ $events->total() }})
                    @else
                        Événements passés ({{ $events->total() }})
                    @endif
                </h2>

                <!-- Sélecteur de vue -->
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary view-btn active" data-view="grid" id="gridViewBtn">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary view-btn d-none d-md-block" data-view="list" id="listViewBtn">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            @if($events->count() > 0)
                <!-- Vue Grille -->
                <div class="row g-4 grid-view">
                    @foreach($events as $event)
                    <div class="col-12 col-md-6 col-lg-4">
                        <x-event-card :event="$event" link-route="direct-events.show" />
                    </div>
                    @endforeach
                </div>

                <!-- Vue Liste -->
                <div class="list-view" style="display: none;">
                    @foreach($events as $event)
                    @php
                        $viewsCountRaw = $event->views_count ?? $event->views()->count();
                        $viewsCount = format_number($viewsCountRaw);
                        $description = strip_tags($event->description ?? '');
                        $descriptionLimited = Str::limit($description, 150);
                    @endphp
                    <div class="card event-card list-event-card mb-3 border-2" style="border-color: var(--blanc-or);">
                        <div class="row g-0 h-100">
                            <div class="col-md-4">
                                <div class="position-relative h-100" style="min-height: 200px;">
                                    @if($event->image)
                                        <img src="{{ Storage::url($event->image) }}" class="img-fluid w-100 h-100" alt="{{ $event->title }}" style="object-fit: cover;">
                                    @else
                                        <div class="h-100 d-flex align-items-center justify-content-center" style="background-color: var(--bleu-nuit-clair); min-height: 200px;">
                                            <i class="fas fa-calendar-alt fa-3x text-white"></i>
                                        </div>
                                    @endif

                                    <!-- Badges (Archivé, Type) -->
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-secondary">Passé</span>
                                        @if($event->event_type == 'Espace libre')
                                            <span class="badge mt-1" style="background-color: var(--bleu-nuit-clair); color: white;">{{ $event->event_type }}</span>
                                        @else
                                            <span class="badge mt-1 bg-dark">{{ $event->event_type }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column h-100">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-2">{{ $event->title }}</h5>

                                        <!-- Organisateur -->
                                        @if($event->organizer)
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-building me-1"></i>
                                                <strong>Organisateur:</strong> {{ $event->organizer->company_name ?? 'Organisateur inconnu' }}
                                            </small>
                                        </div>
                                        @endif

                                        <!-- Description -->
                                        @if($descriptionLimited)
                                        <p class="text-muted small mb-3" style="line-height: 1.5;">
                                            {{ $descriptionLimited }}
                                            @if(strlen($description) > 150)
                                                <span class="text-primary">...</span>
                                            @endif
                                        </p>
                                        @endif

                                        <!-- Date et Heure -->
                                        @if($event->start_date)
                                            <div class="mb-2 text-muted small">
                                                <i class="far fa-calendar-alt me-2"></i>
                                                {{ Carbon\Carbon::parse($event->start_date)->isoFormat('D MMMM YYYY [à] HH[h]mm') }}
                                            </div>
                                        @endif

                                        <!-- Lieu -->
                                        @if($event->ville)
                                            <div class="mb-3 text-muted small">
                                                <i class="fas fa-map-marker-alt me-2"></i>
                                                {{ $event->ville }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Statut et Participants -->
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="text-muted small">
                                                <i class="far fa-eye me-1"></i> {{ $viewsCount }}
                                            </div>
                                            <span class="badge bg-secondary">Terminé</span>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $event->tickets->sum('quantite_vendue') }} participants
                                        </div>
                                    </div>
                                    <a href="{{ route('events.show', $event->slug) }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination améliorée -->
                <div class="mt-5">
                    <x-pagination :paginator="$events" />
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Aucun événement passé ne correspond à vos critères de recherche.
                    <a href="{{ url('/past-events') }}" class="alert-link">Réinitialiser les filtres</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.event-card .card-title {
    color: #212529 !important; /* Couleur Bootstrap standard */
}
/* Styles pour le bouton Créer un événement */
.btn-create-event {
    transition: all 0.3s ease;
}

/* Version mobile */
@media (max-width: 767.98px) {
    .search-banner .col-md-4 {
        margin-top: 1rem;
    }

    .search-banner .d-flex {
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-create-event {
        order: -1;
        align-self: flex-start;
        padding: 0.5rem;
    }
}

/* Version très petits écrans */
@media (max-width: 400px) {
    .btn-create-event {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Correction des vues grille/liste */
.grid-view {
    display: flex;
    flex-wrap: wrap;
}

.list-view {
    display: none;
}

/* Désactiver la vue liste sur mobile */
@media (max-width: 767.98px) {
    .view-btn[data-view="list"] {
        display: none !important;
    }

    .list-view {
        display: none !important;
    }

    .grid-view {
        display: flex !important;
    }

    /* Ajustement des cartes en grille pour mobile */
    .event-card {
        margin-bottom: 1.5rem;
        width: 100%;
    }

    .card-img-top {
        height: 180px !important;
    }
}

/* Skeleton Loading Styles */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 4px;
    display: inline-block;
    overflow: hidden;
    position: relative;
}

.skeleton-dark {
    background: linear-gradient(90deg, #2c3e50 25%, #34495e 50%, #2c3e50 75%);
    background-size: 200% 100%;
}

@keyframes shimmer {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

.skeleton-card {
    height: 100%;
    border-radius: var(--border-radius);
    overflow: hidden;
}

.skeleton-img {
    width: 100%;
    height: 200px;
    margin-bottom: 1rem;
}

.skeleton-line {
    height: 12px;
    margin-bottom: 10px;
    width: 100%;
}

.skeleton-line.short {
    width: 60%;
}

.skeleton-line.medium {
    width: 80%;
}

.skeleton-line-xs {
    height: 8px;
    margin-bottom: 6px;
    width: 50%;
}

.skeleton-badge {
    width: 60px;
    height: 20px;
    border-radius: 20px;
    display: inline-block;
    margin-right: 8px;
}

.skeleton-chip {
    width: 100%;
    height: 40px;
    border-radius: 50px;
    margin-bottom: 8px;
}

.skeleton-header {
    height: 30px;
    width: 70%;
    margin-bottom: 20px;
}

.skeleton-search {
    height: 50px;
    width: 100%;
    margin-bottom: 20px;
}

.skeleton-view-toggle {
    width: 60px;
    height: 32px;
    border-radius: 4px;
}

.skeleton-list-item {
    height: 150px;
    width: 100%;
    margin-bottom: 15px;
    border-radius: var(--border-radius);
}

.skeleton-pagination {
    height: 40px;
    width: 300px;
    margin: 30px auto;
    border-radius: 4px;
}

.search-banner {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    box-shadow: var(--box-shadow);
}

/* Style pour le sidebar de filtre */
.filter-card {
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    box-shadow: var(--box-shadow);
    color: #000 !important;
}

.filter-card .card-body {
    background: #ffffff !important;
}

.filter-card .card-title, .filter-card .filter-header h6 {
    color: #000 !important;
}

.filter-card a { text-decoration: none !important; }
.filter-card a:hover { text-decoration: none !important; }
.filter-card .filter-chip {
    background-color: #f8f9fa !important;
    color: #000 !important;
    border: none !important;
    box-shadow: none !important;
}

.filter-card .filter-chip:hover {
    background-color: #e9ecef !important;
    color: #000 !important;
}

.filter-card .filter-chip.active {
    background-color: var(--blanc-or) !important;
    color: var(--bleu-nuit) !important;
    border-color: var(--blanc-or) !important;
}

.filter-card .filter-chip i {
    color: var(--bleu-nuit) !important;
}

.filter-card .filter-chip.active i {
    color: var(--bleu-nuit) !important;
}

.filter-card .filter-header h6 i,
.filter-card .card-title i {
    color: var(--bleu-nuit) !important;
}

.filter-card .filter-toggle {
    color: var(--bleu-nuit) !important;
}

.filter-card .filter-section {
    border-bottom: 1px solid #e0e0e0 !important;
}

.filter-card .btn-outline-primary {
    color: var(--bleu-nuit) !important;
    border-color: var(--bleu-nuit) !important;
    background-color: transparent !important;
}

.filter-card .btn-outline-primary:hover {
    background-color: var(--bleu-nuit) !important;
    color: #ffffff !important;
}

/* Style des sections de filtre */
.filter-section .filter-content {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, opacity 0.3s ease;
}

.filter-section.active .filter-content {
    max-height: 1000px;
    opacity: 1;
}

.filter-section {
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    padding-bottom: 1rem;
    transition: all 0.3s ease;
}

.filter-section:last-child {
    border-bottom: none;
}

.filter-header {
    cursor: pointer;
    padding: 0.5rem 0;
    transition: all 0.3s ease;
}

.filter-header:hover {
    opacity: 0.9;
}

.filter-section.active .filter-toggle {
    transform: rotate(180deg);
}

/* Animation des sections */
.filter-content {
    overflow: hidden;
    transition: max-height 0.3s ease, opacity 0.3s ease;
    background-color: transparent;
}

/* Cartes événement */
.event-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--box-shadow);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

/* Style pour la vue liste */
.list-view .event-card {
    border-radius: var(--border-radius);
    overflow: hidden;
}

.list-view .list-event-card {
    min-height: 200px;
    height: 100%;
}

.list-view .card-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
}

.list-view .list-event-card .col-md-4 {
    min-height: 200px;
}

.list-view .list-event-card .col-md-8 {
    display: flex;
    flex-direction: column;
}

/* Style pour les boutons de vue */
.view-btn.active {
    background-color: var(--bleu-nuit);
    color: white;
    border-color: var(--bleu-nuit);
}

/* Pagination améliorée */
.pagination .page-item {
    margin: 0 3px;
}

.pagination .page-link {
    color: var(--bleu-nuit);
    border: 1px solid #dee2e6;
    border-radius: var(--border-radius);
    padding: 0.5rem 0.9rem;
    transition: var(--transition);
}

.pagination .page-item.active .page-link {
    background-color: var(--bleu-nuit);
    border-color: var(--bleu-nuit);
    color: white;
}

.pagination .page-link:hover {
    background-color: #f0f0f0;
    color: var(--bleu-nuit-clair);
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    cursor: not-allowed;
}

/* Responsive */
@media (max-width: 991.98px) {
    .filter-section {
        padding-bottom: 0.5rem;
    }

    .filter-chip {
        padding: 0.5rem 0.8rem;
        font-size: 0.9rem;
    }

    .col-md-6 {
        margin-bottom: 1.5rem;
    }

    .search-banner {
        padding: 1.5rem !important;
    }

    .search-banner h2 {
        font-size: 1.5rem;
    }

    .card {
        border-radius: var(--border-radius);
    }
}
</style>
@endpush

@push('scripts')
<script>
window.onload = function() {
    // Cache le skeleton et affiche le vrai contenu
    const skeleton = document.querySelector('.skeleton-content');
    const realContent = document.querySelector('.real-content');

    if (skeleton) skeleton.style.display = 'none';
    if (realContent) realContent.style.display = 'flex';

    // Initialise le toggle des vues
    initViewToggle();

    // Initialise les filtres accordéon
    initFilters();
};

function initViewToggle() {
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');

    if (!gridViewBtn || !listViewBtn) return;

    const gridView = document.querySelector('.grid-view');
    const listView = document.querySelector('.list-view');

    function setActiveView(view) {
        // Basculer les classes des boutons
        gridViewBtn.classList.toggle('active', view === 'grid');
        gridViewBtn.classList.toggle('btn-secondary', view === 'grid');
        gridViewBtn.classList.toggle('btn-outline-secondary', view !== 'grid');

        listViewBtn.classList.toggle('active', view === 'list');
        listViewBtn.classList.toggle('btn-secondary', view === 'list');
        listViewBtn.classList.toggle('btn-outline-secondary', view !== 'list');

        // Basculer l'affichage des vues
        if (gridView) gridView.style.display = view === 'grid' ? 'flex' : 'none';
        if (listView) listView.style.display = view === 'list' ? 'block' : 'none';

        // Sauvegarder la préférence
        localStorage.setItem('preferredView', view);
    }

    // Appliquer la vue sauvegardée ou 'grid' par défaut
    const savedView = localStorage.getItem('preferredView') || 'grid';
    setActiveView(savedView);

    // Écouteurs d'événements
    gridViewBtn.addEventListener('click', () => setActiveView('grid'));
    listViewBtn.addEventListener('click', () => setActiveView('list'));
}

function initFilters() {
    document.querySelectorAll('.filter-header').forEach(header => {
        header.addEventListener('click', function() {
            const section = this.closest('.filter-section');
            const content = section.querySelector('.filter-content');
            const icon = this.querySelector('.filter-toggle');

            section.classList.toggle('active');

            if (section.classList.contains('active')) {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        });
    });
}
</script>
@endpush
