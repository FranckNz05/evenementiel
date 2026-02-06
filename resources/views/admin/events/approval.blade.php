@extends('layouts.dashboard')

@section('title', 'Validation des Événements')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Validation des Événements</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item active">Validation</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card modern-card shadow-sm mb-4">
        <div class="card-header-modern">
            <h5 class="card-title my-1 text-white">
                <i class="fas fa-clipboard-check me-2"></i>Événements en attente de vérification
            </h5>
        </div>
        <div class="card-body p-0">
            @if($events->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-4x text-light"></i>
                    </div>
                    <h5 class="text-muted">Tout est à jour !</h5>
                    <p class="text-muted small">Aucun événement ne nécessite de validation pour le moment.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table modern-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Soumission</th>
                                <th>Événement</th>
                                <th>Organisateur</th>
                                <th>Catégorie</th>
                                <th>Date de l'event</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td class="ps-4">
                                        <div class="small fw-bold text-dark">{{ $event->created_at->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $event->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $event->title }}</div>
                                        <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($event->location, 30) }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-medium">{{ $event->organizer->name }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-primary border border-primary-subtle">{{ $event->category->name }}</span>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark">{{ $event->start_date->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $event->start_date->format('H:i') }}</div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group shadow-sm">
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $event->id }}" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <form action="{{ route('admin.events.approve', $event->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approuver">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $event->id }}" title="Rejeter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modals continue... -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@foreach($events as $event)
    <!-- Modal Détails -->
    <div class="modal fade" id="detailsModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-info-circle me-2 text-primary"></i>Détails de l'événement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <div class="mb-4">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Titre & Catégorie</label>
                                <h4 class="text-dark fw-bold mb-1">{{ $event->title }}</h4>
                                <span class="badge bg-primary-light text-primary border-0">{{ $event->category->name }}</span>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="small text-muted text-uppercase fw-bold mb-1">Organisateur</label>
                                    <p class="mb-0 text-dark fw-medium">{{ $event->organizer->name }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted text-uppercase fw-bold mb-1">Lieu</label>
                                    <p class="mb-0 text-dark"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $event->location }}</p>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="small text-muted text-uppercase fw-bold mb-1">Début</label>
                                    <p class="mb-0 text-dark fw-bold">{{ $event->start_date->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted text-uppercase fw-bold mb-1">Fin</label>
                                    <p class="mb-0 text-dark fw-bold">{{ $event->end_date->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            @if($event->image)
                                <div class="rounded shadow-sm overflow-hidden border">
                                    <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="img-fluid w-100" style="height: 200px; object-fit: cover;">
                                </div>
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center border shadow-sm" style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted opacity-25"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3 bg-light rounded border">
                        <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Description</label>
                        <p class="text-dark small mb-0">{{ $event->description }}</p>
                    </div>

                    @if($event->tickets->isNotEmpty())
                        <div class="mt-4">
                            <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Types de Billets</label>
                            <div class="table-responsive rounded border">
                                <table class="table table-sm table-striped mb-0 small">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">Type</th>
                                            <th>Prix</th>
                                            <th class="pe-3">Quantité</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($event->tickets as $ticket)
                                            <tr>
                                                <td class="ps-3 fw-medium">{{ $ticket->type }}</td>
                                                <td><span class="fw-bold">{{ number_format($ticket->price, 0, ',', ' ') }}</span> FCFA</td>
                                                <td class="pe-3">{{ $ticket->quantity }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Fermer</button>
                    <form action="{{ route('admin.events.approve', $event->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            <i class="fas fa-check me-1"></i> Approuver
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rejet -->
    <div class="modal fade" id="rejectModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-times-circle me-2"></i>Rejeter l'événement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.events.reject', $event->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="rejection_reason{{ $event->id }}" class="form-label fw-bold text-dark">Motif du rejet</label>
                            <textarea class="form-control" id="rejection_reason{{ $event->id }}" name="rejection_reason" rows="4" required placeholder="Expliquez pourquoi l'événement est refusé..."></textarea>
                            <div class="form-text mt-2 small text-muted">Ce message sera envoyé à l'organisateur par email.</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger px-4 shadow-sm">Confirmer le rejet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
