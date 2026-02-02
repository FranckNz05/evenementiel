@extends('layouts.dashboard')

@section('title', 'Mes événements')

@section('content')
<div class="container py-5">

    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">Mes événements</h2>
            <p class="text-muted">Gérez tous les événements que vous avez créés</p>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($events->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Événement</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Vues</th>
                                        <th scope="col">Billets vendus</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($event->image)
                                                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="rounded me-3" width="50">
                                                @else
                                                    <div class="bg-light rounded me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-calendar-alt text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $event->title }}</h6>
                                                    <span class="text-muted small">
                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $event->location ?? 'Localisation non spécifiée' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            @if($event->is_published && $event->is_approved)
                                                <span class="badge bg-success">Publié</span>
                                            @elseif($event->is_published && !$event->is_approved)
                                                <span class="badge bg-warning">En attente d'approbation</span>
                                            @else
                                                <span class="badge bg-secondary">Brouillon</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $event->views_count ?? 0 }}
                                        </td>
                                        <td>
                                            {{ $event->tickets_count ?? 0 }}
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @if(!$event->is_published)
                                                    <form action="{{ route('events.publish', $event->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Publier">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('events.unpublish', $event->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Dépublier">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">
                            {{ $events->links() }}
                        </div>
                    @else
                        <div class="text-center p-5">
                            <div class="mb-4">
                                <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                            </div>
                            <h5>Vous n'avez pas encore créé d'événements</h5>
                            <p class="text-muted">Commencez par créer votre premier événement.</p>
                            <a href="{{ route('events.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-2"></i>Créer un événement
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
