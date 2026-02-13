@extends('layouts.dashboard')

@section('title', 'Mon Profil')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Carte profil -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <div class="profile-avatar mb-3">
                        <img src="{{ $user->getProfilePhotoUrlAttribute() }}" 
                             alt="{{ $user->prenom }}" 
                             class="rounded-circle">
                    </div>
                    <h5 class="fw-semibold mb-1">{{ $user->prenom }} {{ $user->nom }}</h5>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $user->city }}, {{ $user->country }}
                    </p>
                    @if($user->organizer)
                        <span class="badge bg-primary-subtle text-primary mb-3">
                            <i class="fas fa-star me-1"></i>Organisateur
                        </span>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">
                        <i class="fas fa-edit me-2"></i>Modifier le profil
                    </a>
                </div>
            </div>

            <!-- Menu navigation -->
            <div class="card border-0 shadow-sm">
                <div class="list-group list-group-flush">
                    <a href="{{ route('profile.tickets') }}" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="fas fa-ticket-alt text-primary me-3"></i>
                        <span class="fw-medium">Mes billets</span>
                    </a>
                    <a href="{{ route('reservations.index') }}" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="fas fa-bookmark text-primary me-3"></i>
                        <span class="fw-medium">Mes réservations</span>
                    </a>
                    @if(auth()->user()->isOrganizer())
                        <a href="{{ route('profile.events') }}" class="list-group-item list-group-item-action border-0 py-3">
                            <i class="fas fa-calendar text-primary me-3"></i>
                            <span class="fw-medium">Mes événements</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="col-lg-8">
            <!-- Statistiques -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary-subtle text-primary rounded-3 p-3 me-3">
                                    <i class="fas fa-ticket-alt fs-4"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0 fw-bold">{{ method_exists($user, 'tickets') ? ($user->tickets()->count() ?? 0) : 0 }}</h3>
                                    <p class="text-muted small mb-0">Événements participés</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($user->organizer)
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success-subtle text-success rounded-3 p-3 me-3">
                                    <i class="fas fa-calendar-check fs-4"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0 fw-bold">{{ method_exists($user, 'events') ? ($user->events()->count() ?? 0) : 0 }}</h3>
                                    <p class="text-muted small mb-0">Événements organisés</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Informations personnelles -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-4">Informations personnelles</h5>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="text-muted small mb-1">Email</label>
                            <p class="mb-0 fw-medium">{{ $user->email }}</p>
                        </div>
                        
                        <div class="info-item">
                            <label class="text-muted small mb-1">Téléphone</label>
                            <p class="mb-0">{{ $user->phone ?: '—' }}</p>
                        </div>
                        
                        <div class="info-item">
                            <label class="text-muted small mb-1">Genre</label>
                            <p class="mb-0">{{ $user->genre ?: '—' }}</p>
                        </div>
                        
                        <div class="info-item">
                            <label class="text-muted small mb-1">Tranche d'âge</label>
                            <p class="mb-0">{{ $user->tranche_age ?: '—' }}</p>
                        </div>
                        
                        <div class="info-item full-width">
                            <label class="text-muted small mb-1">Adresse</label>
                            <p class="mb-0">
                                @if($user->address && $user->city && $user->country)
                                    {{ $user->address }}, {{ $user->city }}, {{ $user->country }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations organisateur -->
            @if($user->organizer)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-4">Informations organisateur</h5>
                    
                    <div class="info-grid">
                        <div class="info-item full-width">
                            <label class="text-muted small mb-1">Nom de l'organisation</label>
                            <p class="mb-0 fw-medium">{{ $user->organizer->organization_name }}</p>
                        </div>
                        
                        <div class="info-item full-width">
                            <label class="text-muted small mb-1">Description</label>
                            <p class="mb-0">{{ $user->organizer->description ?: 'Aucune description' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Variables */
:root {
    --primary-rgb: 13, 110, 253;
    --success-rgb: 25, 135, 84;
}

/* Carte générale */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
}

/* Avatar profil */
.profile-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    position: relative;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 4px solid rgba(var(--primary-rgb), 0.1);
}

/* Navigation */
.list-group-item-action {
    transition: all 0.2s ease;
}

.list-group-item-action:hover {
    background-color: rgba(var(--primary-rgb), 0.05);
    padding-left: 1.5rem !important;
}

/* Icônes statistiques */
.stat-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Badge personnalisé */
.bg-primary-subtle {
    background-color: rgba(var(--primary-rgb), 0.1) !important;
}

.bg-success-subtle {
    background-color: rgba(var(--success-rgb), 0.1) !important;
}

/* Grille d'informations */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-item label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-item p {
    color: #212529;
}

/* Responsive */
@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
    }
}

/* Boutons */
.btn {
    padding: 0.625rem 1.25rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-primary {
    box-shadow: 0 2px 8px rgba(var(--primary-rgb), 0.2);
}

.btn-primary:hover {
    box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
    transform: translateY(-1px);
}

/* Ombres personnalisées */
.shadow-sm {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
}

/* Espacement */
.g-4 {
    --bs-gutter-x: 1.5rem;
    --bs-gutter-y: 1.5rem;
}
</style>
@endpush