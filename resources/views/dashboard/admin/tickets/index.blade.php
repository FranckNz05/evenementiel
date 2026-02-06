@extends('layouts.dashboard')

@section('title', 'Gestion des Tickets')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Gestion des Tickets</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item active">Événements</li>
                    <li class="breadcrumb-item active">Tickets</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <!-- Optionnel : bouton pour créer un ticket si la route existe -->
        </div>
    </div>

    <div class="card modern-card shadow-sm mb-4">
        <div class="card-header-modern">
            <h5 class="card-title my-1 text-white">
                <i class="fas fa-ticket-alt me-2"></i>Liste des tickets configurés
            </h5>
        </div>
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 m-3 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table modern-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Nom du Ticket</th>
                            <th>Événement</th>
                            <th>Prix Unitaire</th>
                            <th>Capacité</th>
                            <th>Ventes</th>
                            <th>Organisateur</th>
                            <th>Statut</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $ticket->name }}</td>
                                <td>
                                    <a href="{{ route('events.show', $ticket->event) }}" class="text-primary text-decoration-none fw-medium">
                                        {{ Str::limit($ticket->event->name, 30) }}
                                    </a>
                                </td>
                                <td><span class="fw-bold">{{ number_format($ticket->price, 0, ',', ' ') }}</span> <small>FCFA</small></td>
                                <td><span class="badge bg-light text-dark border">{{ $ticket->quantity }}</span></td>
                                <td>
                                    @php $soldCount = $ticket->orders->sum('pivot.quantity'); @endphp
                                    <span class="fw-bold text-primary">{{ $soldCount }}</span>
                                </td>
                                <td><span class="small text-muted">{{ $ticket->event->user->name }}</span></td>
                                <td>
                                    @if($ticket->quantity > $soldCount)
                                        <span class="modern-badge bg-success">Disponible</span>
                                    @else
                                        <span class="modern-badge bg-danger">Épuisé</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce ticket ?')" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($tickets->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-ticket-alt fa-3x text-light mb-3"></i>
                    <p class="text-muted">Aucun ticket trouvé.</p>
                </div>
            @endif
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Affichage de {{ $tickets->firstItem() ?? 0 }} à {{ $tickets->lastItem() ?? 0 }} sur {{ $tickets->total() }} tickets
                </div>
                <div>
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
