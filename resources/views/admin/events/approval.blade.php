@extends('layouts.Administrateur')

@section('title', 'Validation des événements')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Validation des événements</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Événements en attente de validation
        </div>
        <div class="card-body">
            @if($events->isEmpty())
                <div class="alert alert-info">
                    Aucun événement en attente de validation.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Titre</th>
                                <th>Organisateur</th>
                                <th>Catégorie</th>
                                <th>Date événement</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>{{ $event->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->organizer->name }}</td>
                                    <td>{{ $event->category->name }}</td>
                                    <td>{{ $event->start_date->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $event->id }}">
                                            <i class="fas fa-eye"></i> Détails
                                        </button>
                                        <form action="{{ route('admin.events.approve', $event->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Approuver
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $event->id }}">
                                            <i class="fas fa-times"></i> Rejeter
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Détails -->
                                <div class="modal fade" id="detailsModal{{ $event->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Détails de l'événement</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Informations générales</h6>
                                                        <p><strong>Titre :</strong> {{ $event->title }}</p>
                                                        <p><strong>Catégorie :</strong> {{ $event->category->name }}</p>
                                                        <p><strong>Organisateur :</strong> {{ $event->organizer->name }}</p>
                                                        <p><strong>Date de début :</strong> {{ $event->start_date->format('d/m/Y H:i') }}</p>
                                                        <p><strong>Date de fin :</strong> {{ $event->end_date->format('d/m/Y H:i') }}</p>
                                                        <p><strong>Lieu :</strong> {{ $event->location }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        @if($event->image)
                                                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="img-fluid">
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <h6 class="mt-4">Description</h6>
                                                <p>{{ $event->description }}</p>

                                                @if($event->tickets->isNotEmpty())
                                                    <h6 class="mt-4">Billets</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Type</th>
                                                                    <th>Prix</th>
                                                                    <th>Quantité</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($event->tickets as $ticket)
                                                                    <tr>
                                                                        <td>{{ $ticket->type }}</td>
                                                                        <td>{{ $ticket->price }} €</td>
                                                                        <td>{{ $ticket->quantity }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Rejet -->
                                <div class="modal fade" id="rejectModal{{ $event->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Rejeter l'événement</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.events.reject', $event->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="rejection_reason" class="form-label">Motif du rejet</label>
                                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-danger">Rejeter</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
