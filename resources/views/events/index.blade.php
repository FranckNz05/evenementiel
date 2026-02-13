@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Bannière de recherche -->
    <div class="search-banner mb-5" style="background-color: var(--bleu-nuit); border-radius: var(--border-radius);">
        <div class="p-4 p-md-5 text-white">
            <!-- Ligne titre et tri -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Trouvez votre prochain événement</h2>
                <div class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle sort-dropdown-btn" type="button" id="sortDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-sort me-2" style="color: white;"></i>
                        @if(request('sort') == 'date_asc') Date (croissant)
                        @elseif(request('sort') == 'date_desc') Date (décroissant)
                        @elseif(request('sort') == 'title') Titre
                        @else Trier par
                        @endif
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li><a class="dropdown-item" href="{{ url('/direct-events') }}?{{ http_build_query(array_merge(request()->except('sort'), ['sort' => 'date_asc'])) }}">Date (croissant)</a></li>
                        <li><a class="dropdown-item" href="{{ url('/direct-events') }}?{{ http_build_query(array_merge(request()->except('sort'), ['sort' => 'date_desc'])) }}">Date (décroissant)</a></li>
                        <li><a class="dropdown-item" href="{{ url('/direct-events') }}?{{ http_build_query(array_merge(request()->except('sort'), ['sort' => 'title'])) }}">Titre</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Ligne recherche et créer événement -->
            <div class="row align-items-center">
                <div class="col-md-8">
                    <form action="{{ url('/direct-events') }}" method="GET">
                        <div class="input-group" style="gap: 8px;">
                            <input type="text" class="form-control form-control-lg" name="search"
                                   value="{{ request('search') }}" placeholder="Rechercher un événement, organisateur..." style="margin-right: 8px; color: #000 !important;">
                            <button class="btn" type="submit" style="background-color: var(--bleu-nuit); color: white; border: none;">
                                <i class="fas fa-search" style="color: white;"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @auth
                        @if(auth()->user()->isOrganizer() || auth()->user()->hasRole(2))
                            <a href="{{ route('events.select-type') }}" class="btn btn-primary btn-sm btn-create-event" style="text-decoration: none !important;">
                                <i class="fas fa-plus me-md-1" style="color: white !important;"></i>
                                <span class="d-none d-md-inline">Créer un événement</span>
                            </a>
                        @elseif(auth()->user()->isAdmin() || auth()->user()->hasRole(3))
                            <a href="{{ route('events.wizard.step1') }}" class="btn btn-primary btn-sm btn-create-event" style="text-decoration: none !important;">
                                <i class="fas fa-plus me-md-1" style="color: white !important;"></i>
                                <span class="d-none d-md-inline">Créer un événement</span>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu réel -->
    <div class="row">
        <!-- Filtres améliorés -->
        <div class="col-lg-3">
            <div class="card border-0 bg shadow-sm mb-4 filter-card">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4 d-flex align-items-center">
                        <i class="fas fa-sliders-h me-2" style="color: white;"></i>
                        <span>Filtrer par</span>
                    </h5>

                    <!-- Période -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 d-flex align-items-center">
                                <i class="far fa-calendar-alt me-2" style="color: white;"></i>
                                Période
                            </h6>
                            <i class="fas fa-chevron-down filter-toggle" style="color: white;"></i>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                @php
                                    $query = request()->query();
                                    $toggleFilter = function($key, $value) use ($query) {
                                        if (isset($query[$key]) && $query[$key] == $value) {
                                            unset($query[$key]);
                                        } else {
                                            $query[$key] = $value;
                                        }
                                        return url()->current() . '?' . http_build_query($query);
                                    };
                                @endphp
                                <a href="{{ $toggleFilter('periode', 'today') }}"
                                   class="filter-chip {{ request('periode') == 'today' ? 'active' : '' }}">
                                    <i class="far fa-sun me-2"></i> Aujourd'hui
                                </a>
                                <a href="{{ $toggleFilter('periode', 'week') }}"
                                   class="filter-chip {{ request('periode') == 'week' ? 'active' : '' }}">
                                    <i class="far fa-calendar me-2"></i> Cette semaine
                                </a>
                                <a href="{{ $toggleFilter('periode', 'month') }}"
                                   class="filter-chip {{ request('periode') == 'month' ? 'active' : '' }}">
                                    <i class="fas fa-calendar-week me-2"></i> Ce mois-ci
                                </a>
                                <a href="{{ $toggleFilter('periode', 'upcoming') }}"
                                   class="filter-chip {{ request('periode') == 'upcoming' ? 'active' : '' }}">
                                    <i class="fas fa-clock me-2"></i> À venir
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Catégories -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 d-flex align-items-center">
                                <i class="fas fa-tags me-2" style="color: white;"></i>
                                Catégories
                            </h6>
                            <i class="fas fa-chevron-down filter-toggle" style="color: white;"></i>
                        </div>
                        <div class="filter-content">
                            <div class="category-list d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto;">
                                @foreach($categories as $category)
                                <a href="{{ $toggleFilter('category', $category->slug) }}"
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
                                <i class="fas fa-map-marker-alt me-2" style="color: white;"></i>
                                Villes
                            </h6>
                            <i class="fas fa-chevron-down filter-toggle" style="color: white;"></i>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                @foreach($villes as $ville)
                                <a href="{{ $toggleFilter('ville', $ville) }}"
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
                                <i class="fas fa-calendar-alt me-2" style="color: white;"></i>
                                Type
                            </h6>
                            <i class="fas fa-chevron-down filter-toggle" style="color: white;"></i>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ $toggleFilter('event_type', 'Espace libre') }}"
                                   class="filter-chip {{ request('event_type') == 'Espace libre' ? 'active' : '' }}">
                                    <i class="fas fa-chair me-2"></i> Espace libre
                                </a>
                                <a href="{{ $toggleFilter('event_type', 'Plan de salle') }}"
                                   class="filter-chip {{ request('event_type') == 'Plan de salle' ? 'active' : '' }}">
                                    <i class="fas fa-th-large me-2"></i> Plan de salle
                                </a>
                                <a href="{{ $toggleFilter('event_type', 'Mixte') }}"
                                   class="filter-chip {{ request('event_type') == 'Mixte' ? 'active' : '' }}">
                                    <i class="fas fa-blender-phone me-2"></i> Mixte
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="filter-section mb-4">
                        <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 d-flex align-items-center">
                                <i class="fas fa-ticket-alt me-2" style="color: white;"></i>
                                Statut
                            </h6>
                            <i class="fas fa-chevron-down filter-toggle" style="color: white;"></i>
                        </div>
                        <div class="filter-content">
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ $toggleFilter('status', 'Gratuit') }}"
                                   class="filter-chip {{ request('status') == 'Gratuit' ? 'active' : '' }}">
                                    <span class="status-indicator bg-success me-2"></span> Gratuit
                                </a>
                                <a href="{{ $toggleFilter('status', 'Payant') }}"
                                   class="filter-chip {{ request('status') == 'Payant' ? 'active' : '' }}">
                                    <span class="status-indicator bg-warning me-2"></span> Payant
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Reset -->
                    @if(request()->except('page'))
                    <div class="sticky-bottom pt-3">
                        <a href="{{ url('/direct-events') }}" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
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
                        Tous les événements
                    @endif
                </h2>

                <!-- Sélecteur de vue -->
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary view-btn" data-view="grid" id="gridViewBtn">
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
                        $minPrice = $event->tickets->min('prix');
                        $allTickets = $event->tickets;
                        $hasTickets = $allTickets->isNotEmpty();
                        
                        // Vérifier si TOUS les billets sont épuisés
                        $allTicketsSoldOut = true;
                        $hasAvailableTickets = false;
                        
                        if ($hasTickets) {
                            foreach ($allTickets as $ticket) {
                                $remaining = ($ticket->quantite ?? 0) - ($ticket->quantite_vendue ?? 0);
                                if ($remaining > 0) {
                                    $allTicketsSoldOut = false;
                                    $hasAvailableTickets = true;
                                    break;
                                }
                            }
                        } else {
                            $allTicketsSoldOut = false;
                        }
                        
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
                                        <img src="{{ Storage::url($event->image) }}" 
                                             class="img-fluid w-100 h-100" 
                                             alt="{{ $event->title }}" 
                                             style="object-fit: cover;"
                                             loading="lazy"
                                             decoding="async"
                                             width="400"
                                             height="200">
                                    @else
                                        <div class="h-100 d-flex align-items-center justify-content-center" style="background-color: var(--bleu-nuit-clair); min-height: 200px;">
                                            <i class="fas fa-calendar-alt fa-3x text-white"></i>
                                        </div>
                                    @endif

                                    <!-- Badges (État, Type) -->
                                    <div class="position-absolute top-0 start-0 m-2">
                                        @if($event->etat == 'En cours')
                                            <span class="badge" style="background-color: var(--bleu-nuit);">{{ $event->etat }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $event->etat }}</span>
                                        @endif

                                        @if($event->event_type == 'Espace libre')
                                            <span class="badge mt-1" style="background-color: var(--bleu-nuit-clair); color: white;">{{ $event->event_type }}</span>
                                        @elseif($event->event_type == 'Plan de salle')
                                            <span class="badge mt-1" style="background-color: var(--bleu-nuit); color: white;">{{ $event->event_type }}</span>
                                        @else
                                            <span class="badge mt-1 bg-dark" style="color: white;">{{ $event->event_type }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column h-100">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-2">
                                            @if($event->title)
                                                {{ $event->title }}
                                            @else
                                                Événement sans titre
                                            @endif
                                        </h5>

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

                                    <!-- Prix, Vues et Billets -->
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="text-muted small">
                                                <i class="far fa-eye me-1"></i> {{ $viewsCount }}
                                            </div>
                                            @if($minPrice === null || $minPrice == 0)
                                                <span class="badge bg-success">Gratuit</span>
                                            @else
                                                <span class="badge" style="background-color: var(--blanc-or); color: #000;">Payant</span>
                                            @endif
                                        </div>
                                        @if($minPrice !== null && $minPrice > 0 && $hasTickets)
                                            @if($allTicketsSoldOut)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-ban me-1"></i>Complet
                                                </span>
                                            @elseif($hasAvailableTickets)
                                                <div class="text-muted small">
                                                    <i class="fas fa-ticket-alt"></i>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <a href="{{ route('direct-events.show', $event) }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination améliorée -->
                <div class="mt-5">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <!-- Premier -->
                            <li class="page-item {{ $events->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $events->url(1) }}" aria-label="First">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <!-- Précédent -->
                            <li class="page-item {{ $events->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $events->previousPageUrl() }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Pages -->
                            @foreach(range(1, $events->lastPage()) as $i)
                                @if($i == 1 || $i == $events->lastPage() || ($i >= $events->currentPage() - 2 && $i <= $events->currentPage() + 2))
                                    <li class="page-item {{ $events->currentPage() == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $events->url($i) }}">{{ $i }}</a>
                                    </li>
                                @elseif(($i == $events->currentPage() - 3 || $i == $events->currentPage() + 3) && $events->lastPage() > 7)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endforeach

                            <!-- Suivant -->
                            <li class="page-item {{ !$events->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $events->nextPageUrl() }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <!-- Dernier -->
                            <li class="page-item {{ !$events->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $events->url($events->lastPage()) }}" aria-label="Last">
                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Aucun événement ne correspond à vos critères de recherche.
                    <a href="{{ url('/direct-events') }}" class="alert-link">Réinitialiser les filtres</a>
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
    background-color: var(--bleu-nuit, #0f1a3d) !important;
    border-color: var(--bleu-nuit, #0f1a3d) !important;
    color: #ffffff !important;
    text-decoration: none !important;
}

.btn-create-event:hover,
.btn-create-event:focus,
.btn-create-event:active {
    background-color: var(--bleu-nuit-clair, #1a237e) !important;
    border-color: var(--bleu-nuit-clair, #1a237e) !important;
    color: #ffffff !important;
    text-decoration: none !important;
}

.btn-create-event i {
    color: #ffffff !important;
}

.btn-create-event:hover i,
.btn-create-event:focus i,
.btn-create-event:active i {
    color: #ffffff !important;
}

.sort-dropdown-btn {
    border-color: #ffffff !important;
    color: #ffffff !important;
    transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
}

.sort-dropdown-btn i {
    color: #ffffff !important;
    transition: color 0.3s ease;
}

.sort-dropdown-btn:hover,
.sort-dropdown-btn:focus {
    background-color: #ffffff !important;
    color: var(--bleu-nuit) !important;
    border-color: #ffffff !important;
}

.sort-dropdown-btn:hover i,
.sort-dropdown-btn:focus i {
    color: var(--bleu-nuit) !important;
}

/* Forcer toutes les icônes en bleu dans le contenu principal sauf celles sur fond bleu (qui doivent être blanches) */
.container i.fas,
.container i.far,
.container i.fab,
.container i.fal,
.search-banner ~ * i.fas,
.search-banner ~ * i.far,
.search-banner ~ * i.fab,
.search-banner ~ * i.fal,
.filter-card i.fas,
.filter-card i.far,
.filter-card i.fab,
.filter-card i.fal,
.event-card i.fas,
.event-card i.far,
.event-card i.fab,
.event-card i.fal {
    color: var(--bleu-nuit, #0f1a3d) !important;
}

/* Icônes blanches sur fond bleu */
.search-banner i,
.search-banner .btn i,
[style*="background-color: var(--bleu-nuit)"] i,
[style*="background: var(--bleu-nuit)"] i,
.btn-primary i,
.btn-primary:hover i,
.btn-primary:focus i,
.btn-primary:active i {
    color: #ffffff !important;
}

/* Force le texte des inputs de recherche à être noir */
.search-banner input[type="text"],
.search-banner input.form-control,
.search-banner input.form-control-lg,
.search-banner textarea,
.search-banner select {
    color: #000000 !important;
    background-color: #ffffff !important;
}

.search-banner input::placeholder,
.search-banner textarea::placeholder {
    color: #6b7280 !important;
    opacity: 1;
}

/* Protéger le footer - ne pas affecter les icônes du footer */
footer i.fas,
footer i.far,
footer i.fab,
footer i.fal,
.footer-modern i.fas,
.footer-modern i.far,
.footer-modern i.fab,
.footer-modern i.fal,
body > footer i.fas,
body > footer i.far,
body > footer i.fab,
body > footer i.fal {
    color: inherit !important;
}

/* Version mobile */
@media (max-width: 767.98px) {
    .search-banner .col-md-4 {
        margin-top: 1rem;
    }

    .search-banner .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
    }

    .search-banner .d-flex.justify-content-between .dropdown {
        width: 100%;
    }

    .search-banner .d-flex.justify-content-between .dropdown button {
        width: 100%;
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
    background-color: #f8f9fa;
    color: #000;
    border: none;
    box-shadow: none;
}

.filter-card .filter-chip:hover {
    background-color: #e9ecef;
    color: #000;
}

.filter-card .filter-chip.active {
    background-color: var(--blanc-or);
    color: var(--bleu-nuit);
    border-color: var(--blanc-or);
}

.filter-card .filter-chip i {
    color: var(--bleu-nuit) !important;
}

.filter-card .filter-chip.active i {
    color: var(--bleu-nuit);
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
    padding-bottom: 1rem;
    transition: all 0.3s ease;
}

.filter-card .filter-section:last-child {
    border-bottom: none !important;
}

.filter-card .btn-outline-primary {
    color: var(--bleu-nuit);
    border-color: var(--bleu-nuit);
    background-color: transparent;
}

.filter-card .btn-outline-primary:hover {
    background-color: var(--bleu-nuit);
    color: #ffffff;
}

/* Style des sections de filtre */
.filter-card .filter-section .filter-content {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, opacity 0.3s ease;
}

.filter-card .filter-section.active .filter-content {
    max-height: 1000px;
    opacity: 1;
}

.filter-card .filter-header {
    cursor: pointer;
    padding: 0.5rem 0;
    transition: all 0.3s ease;
}

.filter-card .filter-header:hover {
    opacity: 0.9;
}

.filter-card .filter-section.active .filter-toggle {
    transform: rotate(180deg);
}

/* Indicateur de statut */
.status-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
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

.btn-favorite {
    position: absolute;
    top: 15px;
    right: 15px;
    background: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    z-index: 10;
}

.btn-favorite:hover {
    transform: scale(1.1);
    background-color: #fff;
}

.btn-favorite i {
    font-size: 1.2rem;
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
.view-btn {
    transition: color 0.3s ease, border-color 0.3s ease, background-color 0.3s ease;
}

.view-btn i {
    transition: color 0.3s ease;
}

.view-btn:hover,
.view-btn:focus {
    color: var(--bleu-nuit) !important;
    border-color: var(--bleu-nuit) !important;
    background-color: #ffffff;
}

.view-btn:hover i,
.view-btn:focus i {
    color: var(--bleu-nuit) !important;
}

.view-btn.active {
    background-color: var(--bleu-nuit);
    color: white;
    border-color: var(--bleu-nuit);
}

.view-btn.active:hover,
.view-btn.active:focus {
    background-color: var(--bleu-nuit);
    color: #ffffff !important;
    border-color: var(--bleu-nuit);
}

.view-btn.active:hover i,
.view-btn.active:focus i {
    color: #ffffff !important;
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
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation du contenu
    initContent();
});

// Initialisation du contenu
function initContent() {
    const filterSections = document.querySelectorAll('.filter-section');
    const filterHeaders = document.querySelectorAll('.filter-header');
    const filterChips = document.querySelectorAll('.filter-chip');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const gridView = document.querySelector('.grid-view');
    const listView = document.querySelector('.list-view');

    // Animation des accordéons
    filterSections.forEach(section => {
        const header = section.querySelector('.filter-header');
        const content = section.querySelector('.filter-content');

        header.addEventListener('click', (e) => {
            if (e.target.tagName === 'A') return;

            if (section.classList.contains('active')) {
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                section.classList.remove('active');
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                section.classList.add('active');
            }
        });
    });

    // Animation au survol des chips
    filterChips.forEach(chip => {
        chip.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px)';
        });
        chip.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateX(0)';
            }
        });
    });

    // Fonction pour changer la vue
    function switchView(view) {
        if (view === 'grid') {
            // Activer la vue grille
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-outline-secondary');
            });

            gridViewBtn.classList.add('active');
            gridViewBtn.classList.remove('btn-outline-secondary');
            gridViewBtn.classList.add('btn-secondary');

            gridView.style.display = 'flex';
            listView.style.display = 'none';
        } else {
            // Activer la vue liste
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-outline-secondary');
            });

            listViewBtn.classList.add('active');
            listViewBtn.classList.remove('btn-outline-secondary');
            listViewBtn.classList.add('btn-secondary');

            gridView.style.display = 'none';
            listView.style.display = 'block';
        }

        // Stocker la préférence de vue
        localStorage.setItem('preferredView', view);
    }

    // Récupérer la préférence de vue sauvegardée
    const preferredView = localStorage.getItem('preferredView') || 'grid';

    // Initialiser avec la vue préférée ou la vue grille par défaut
    switchView(preferredView);

    // Écouteurs d'événements
    gridViewBtn.addEventListener('click', () => switchView('grid'));
    listViewBtn.addEventListener('click', () => switchView('list'));
}

// Gestion des favoris
document.querySelectorAll('.btn-favorite').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const eventId = this.getAttribute('data-event-id');
        const icon = this.querySelector('i');
        
        fetch(`/events/${eventId}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'added') {
                icon.classList.remove('far');
                icon.classList.add('fas');
                Toastify({
                    text: "Événement ajouté aux favoris",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                }).showToast();
            } else if (data.status === 'removed') {
                icon.classList.remove('fas');
                icon.classList.add('far');
                Toastify({
                    text: "Événement retiré des favoris",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ff416c, #ff4b2b)",
                }).showToast();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});

function toggleFavorite(event, eventId) {
    event.preventDefault();
    event.stopPropagation();

    fetch(`/events/${eventId}/favorite`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const button = event.currentTarget;
        const icon = button.querySelector('i');
        if (icon.classList.contains('far')) {
            icon.classList.remove('far');
            icon.classList.add('fas');
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
        }
    })
    .catch(error => console.error('Erreur:', error));
}
</script>
@endpush







