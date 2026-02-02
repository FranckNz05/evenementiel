@extends('layouts.dashboard')

@section('title', 'Gestion des billets')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des billets</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($events->isEmpty())
        <div class="alert alert-info">
            <p>Vous n'avez pas encore créé d'événements. Créez un événement pour pouvoir gérer des billets.</p>
            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#eventTypeModal">
                <i class="fas fa-plus me-2"></i> Créer un événement
            </button>
        </div>
    @else
        @foreach($events as $event)
            <div class="modern-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $event->title }}</h6>
                    <a href="{{ route('events.tickets.create', $event) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un billet
                    </a>
                </div>
                <div class="card-body-modern">
                    @if($event->tickets->isEmpty())
                        <p class="text-center">Aucun billet n'a été créé pour cet événement.</p>
                    @else
                        <div class="table-responsive">
                            <table class="modern-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prix</th>
                                        <th>Disponibles</th>
                                        <th>Vendus</th>
                                        <th>Promotion</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($event->tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->nom }}</td>
                                            <td>{{ number_format($ticket->prix, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ $ticket->quantite_disponible }}</td>
                                            <td>{{ $ticket->sold_count ?? 0 }}</td>
                                            <td>
                                                @if($ticket->promotion_active)
                                                    <span class="modern-badge badge-success">{{ number_format($ticket->prix_promotion, 0, ',', ' ') }} FCFA</span>
                                                @else
                                                    <span class="badge bg-secondary">Non</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('organizer.tickets.edit', $ticket) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
<!-- Modal de sélection du type d'événement -->
<div class="modal fade" id="eventTypeModal" tabindex="-1" aria-labelledby="eventTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventTypeModalLabel">Choisir le type d'événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-4">Sélectionnez le type d'événement que vous souhaitez créer :</p>
                <div class="d-flex justify-content-center gap-4">
                    <a href="{{ route('events.wizard.step1') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-day me-2"></i>Événement simple
                    </a>
                    <a href="{{ route('events.select-type') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-calendar-plus me-2"></i>Événement personnalisé
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modern-btn btn-secondary-modern" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>

@endsection
