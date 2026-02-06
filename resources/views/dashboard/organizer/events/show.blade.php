@extends('layouts.dashboard')

@section('title', 'Détails de l\'événement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $event->title }}</h1>
        <div>
            <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <a href="{{ route('organizer.events.index') }}" class="modern-btn btn-secondary-modern">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="card-header-modern py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title text-white mb-0">Détails de l'événement</h6>
                    <span class="modern-badge badge-{{ $event->is_published ? 'success' : 'warning' }}">
                        {{ $event->is_published ? 'Publié' : 'Brouillon' }}
                    </span>
                </div>
                <div class="card-body-modern">
                    <div class="mb-4">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="img-fluid rounded mb-3" style="max-height: 300px; width: 100%; object-fit: cover;">
                        @endif
                        <p class="text-muted mb-4">{{ $event->description }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Informations générales</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                    <strong>Date de début :</strong> {{ $event->start_date->format('d/m/Y H:i') }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar-check text-primary me-2"></i>
                                    <strong>Date de fin :</strong> {{ $event->end_date->format('d/m/Y H:i') }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <strong>Lieu :</strong> {{ $event->location->name ?? 'Non spécifié' }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-tag text-primary me-2"></i>
                                    <strong>Catégorie :</strong> {{ $event->category->name }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Statistiques</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-ticket-alt text-primary me-2"></i>
                                    <strong>Billets vendus :</strong> {{ $event->tickets->sum('quantity_sold') }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-money-bill-wave text-primary me-2"></i>
                                    <strong>Revenu total :</strong> {{ number_format($event->tickets->sum(function($ticket) {
                                        return $ticket->quantity_sold * $ticket->price;
                                    }), 0, ',', ' ') }} FCFA
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <strong>Capacité :</strong> {{ $event->capacity ?? 'Illimité' }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modern-card">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-primary">Billets</h6>
                </div>
                <div class="card-body-modern">
                    @if($event->tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prix</th>
                                        <th>Quantité</th>
                                        <th>Vendus</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($event->tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->nom }}</td>
                                            <td>{{ number_format($ticket->price, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ $ticket->quantity_available ?? 'Illimité' }}</td>
                                            <td>{{ $ticket->quantity_sold }}</td>
                                            <td>
                                                @if($ticket->is_active)
                                                    <span class="modern-badge badge-success">Actif</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('organizer.tickets.edit', $ticket) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">Aucun billet n'a été créé pour cet événement.</div>
                    @endif
                    <a href="{{ route('organizer.tickets.create', ['event' => $event->id]) }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Ajouter un billet
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="modern-card">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-primary">Actions rapides</h6>
                </div>
                <div class="card-body-modern">
                    <div class="d-grid gap-2">
                        <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-primary mb-2">
                            <i class="fas fa-edit me-1"></i> Modifier l'événement
                        </a>
                        <a href="{{ route('organizer.tickets.create', ['event' => $event->id]) }}" class="btn btn-success mb-2">
                            <i class="fas fa-ticket-alt me-1"></i> Créer un billet
                        </a>
                        <a href="{{ route('organizer.scans.index', ['event' => $event->id]) }}" class="btn btn-info mb-2">
                            <i class="fas fa-qrcode me-1"></i> Scanner des billets
                        </a>
                        @if($event->is_published)
                            <form action="{{ route('organizer.events.unpublish', $event) }}" method="POST" class="d-grid">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="modern-btn btn-warning-modern">
                                    <i class="fas fa-eye-slash me-1"></i> Dépublier
                                </button>
                            </form>
                        @else
                            <form action="{{ route('organizer.events.publish', $event) }}" method="POST" class="d-grid">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="modern-btn btn-success-modern">
                                    <i class="fas fa-eye me-1"></i> Publier
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modern-card">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-primary">Statut</h6>
                </div>
                <div class="card-body-modern">
                    <div class="mb-3">
                        <strong>Statut :</strong>
                        @if($event->is_published)
                            <span class="modern-badge badge-success">Publié</span>
                            <p class="mt-2">Votre événement est visible par le public.</p>
                        @else
                            <span class="modern-badge badge-warning">Brouillon</span>
                            <p class="mt-2">Votre événement n'est pas encore visible par le public.</p>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>URL de l'événement :</strong>
                        @if($event->is_published)
                            <div class="input-group mt-2">
                                <input type="text" class="form-control" value="{{ route('events.show', $event->slug) }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="copy-url">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <p class="text-muted mt-2">L'URL sera disponible après publication.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Copier l'URL dans le presse-papier
    document.getElementById('copy-url')?.addEventListener('click', function() {
        const urlInput = this.previousElementSibling;
        urlInput.select();
        document.execCommand('copy');
        
        // Afficher un feedback
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-check"></i> Copié!';
        
        setTimeout(() => {
            this.innerHTML = originalText;
        }, 2000);
    });
</script>
@endpush
@endsection
