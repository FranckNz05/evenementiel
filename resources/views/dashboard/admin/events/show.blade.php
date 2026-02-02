@extends('layouts.dashboard')

@section('title', 'Détails de l\'événement')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-pagination.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
/* Variables CSS pour design cohérent */
:root {
    --primary: #4f46e5;
    --primary-dark: #4338ca;
    --primary-light: #eef2ff;
    --secondary: #6b7280;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --dark: #1f2937;
    --light: #f8fafc;
    --border: #e5e7eb;
    --text-muted: #6b7280;
    --shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.04);
    --radius: 12px;
    --radius-sm: 8px;
}

.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
}

.card {
    border: none;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    background: white;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.card-body {
    padding: 2rem;
}

.btn {
    border-radius: var(--radius-sm);
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    padding: 0.75rem 1.5rem;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.btn-secondary {
    background: var(--secondary);
    color: white;
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-warning {
    background: var(--warning);
    color: white;
}

.btn-danger {
    background: var(--danger);
    color: white;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
}

.badge-success {
    background: var(--success);
    color: white;
}

.badge-warning {
    background: var(--warning);
    color: white;
}

.badge-danger {
    background: var(--danger);
    color: white;
}

.badge-info {
    background: var(--info);
    color: white;
}

.event-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: var(--radius);
}

.event-details {
    background: var(--light);
    border-radius: var(--radius);
    padding: 1.5rem;
    margin: 1rem 0;
}

.event-details h5 {
    color: var(--primary);
    margin-bottom: 1rem;
}

.event-details p {
    margin-bottom: 0.5rem;
    color: var(--text-muted);
}

.event-details strong {
    color: var(--dark);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 1.5rem 0;
}

.stat-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
    text-align: center;
    box-shadow: var(--shadow);
}

.stat-card h4 {
    color: var(--primary);
    margin-bottom: 0.5rem;
    font-size: 2rem;
}

.stat-card p {
    color: var(--text-muted);
    margin: 0;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 0 0.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête avec navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0">Détails de l'événement</h1>
            <p class="text-muted mb-0">{{ $event->title }}</p>
        </div>
        <div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
        </div>
    </div>

    <!-- Messages d'alerte -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{ $event->title }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Image de l'événement -->
                    @if($event->image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $event->image) }}" 
                                 alt="{{ $event->title }}" 
                                 class="event-image">
                        </div>
                    @endif

                    <!-- Description -->
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p class="text-muted">{{ $event->description ?? 'Aucune description disponible' }}</p>
                    </div>

                    <!-- Détails de l'événement -->
                    <div class="event-details">
                        <h5><i class="fas fa-info-circle me-2"></i>Informations générales</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Date de début :</strong> {{ $event->start_date ? $event->start_date->format('d/m/Y à H:i') : 'Non définie' }}</p>
                                <p><strong>Date de fin :</strong> {{ $event->end_date ? $event->end_date->format('d/m/Y à H:i') : 'Non définie' }}</p>
                                <p><strong>Lieu :</strong> {{ $event->location ?? 'Non spécifié' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Statut :</strong> 
                                    @if($event->status === 'published')
                                        <span class="badge badge-success">Publié</span>
                                    @elseif($event->status === 'Payant')
                                        <span class="badge badge-info">Payant</span>
                                    @elseif($event->status === 'Gratuit')
                                        <span class="badge badge-success">Gratuit</span>
                                    @else
                                        <span class="badge badge-warning">{{ $event->status }}</span>
                                    @endif
                                </p>
                                <p><strong>Prix :</strong> 
                                    @if($event->price)
                                        {{ number_format($event->price, 0, ',', ' ') }} FCFA
                                    @else
                                        Gratuit
                                    @endif
                                </p>
                                <p><strong>Capacité :</strong> {{ $event->capacity ?? 'Illimitée' }} places</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de l'organisateur -->
                    @if($event->user)
                        <div class="event-details">
                            <h5><i class="fas fa-user me-2"></i>Organisateur</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nom :</strong> {{ $event->user->prenom }} {{ $event->user->nom }}</p>
                                    <p><strong>Email :</strong> {{ $event->user->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Téléphone :</strong> {{ $event->user->phone ?? 'Non renseigné' }}</p>
                                    <p><strong>Inscrit le :</strong> {{ $event->user->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Catégorie -->
                    @if($event->category)
                        <div class="event-details">
                            <h5><i class="fas fa-tag me-2"></i>Catégorie</h5>
                            <p><strong>{{ $event->category->name }}</strong></p>
                            @if($event->category->description)
                                <p class="text-muted">{{ $event->category->description }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistiques et actions -->
        <div class="col-lg-4">
            <!-- Statistiques -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h4>{{ $event->tickets ? $event->tickets->count() : 0 }}</h4>
                            <p>Types de billets</p>
                        </div>
                        <div class="stat-card">
                            <h4>{{ $event->tickets ? $event->tickets->sum('quantite_vendue') : 0 }}</h4>
                            <p>Billets vendus</p>
                        </div>
                        <div class="stat-card">
                            <h4>{{ $event->tickets ? $event->tickets->sum('quantite') : 0 }}</h4>
                            <p>Billets disponibles</p>
                        </div>
                        <div class="stat-card">
                            <h4>{{ $event->orders ? $event->orders->count() : 0 }}</h4>
                            <p>Commandes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Modifier l'événement
                        </a>
                        
                        <a href="{{ route('admin.tickets.index', ['event' => $event->id]) }}" class="btn btn-info">
                            <i class="fas fa-ticket-alt me-2"></i> Gérer les billets
                        </a>
                        
                        <a href="{{ route('admin.payments.index', ['event' => $event->id]) }}" class="btn btn-success">
                            <i class="fas fa-credit-card me-2"></i> Voir les paiements
                        </a>
                        
                        @if($event->status === 'published')
                            <form action="{{ route('admin.events.unpublish', $event) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning w-100" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir dépublier cet événement ?')">
                                    <i class="fas fa-eye-slash me-2"></i> Dépublier
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.events.publish', $event) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir publier cet événement ?')">
                                    <i class="fas fa-eye me-2"></i> Publier
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.')">
                                <i class="fas fa-trash me-2"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// Configuration de Toastr
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

// Afficher les messages de session
@if(session('success'))
    toastr.success('{{ session('success') }}');
@endif

@if(session('error'))
    toastr.error('{{ session('error') }}');
@endif
</script>
@endpush
