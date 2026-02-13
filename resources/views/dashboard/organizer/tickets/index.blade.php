@extends('layouts.dashboard')

@section('title', 'Gestion des billets')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-table-pages.css') }}">
<style>
.tickets-page {
    min-height: 100vh;
    background: var(--gray-50);
    padding: 2rem;
}

.tickets-table {
    width: 100%;
    border-collapse: collapse;
}

.tickets-table thead {
    background: var(--gray-50);
}

.tickets-table thead th {
    padding: 0.875rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-600);
    border-bottom: 2px solid var(--gray-200);
    white-space: nowrap;
}

.tickets-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.tickets-table tbody tr:hover {
    background: var(--gray-50);
}

.tickets-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

.badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-success {
    background: var(--success);
    color: white;
}

.badge-secondary {
    background: var(--gray-500);
    color: white;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

.btn-primary {
    background: var(--primary);
    color: white;
    border: none;
}

.btn-primary:hover {
    background: var(--primary-light);
    color: white;
}
</style>
@endpush

@section('content')
<div class="tickets-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Gestion des billets</h1>
            <p>Gérez les billets de vos événements</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('organizer.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 1.5rem; background: var(--success); color: white; border: none;">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($events->isEmpty())
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 3rem;">
                <i class="fas fa-calendar-times" style="font-size: 3rem; color: var(--gray-300); margin-bottom: 1rem;"></i>
                <p style="color: var(--gray-500); margin-bottom: 1.5rem;">Vous n'avez pas encore créé d'événements. Créez un événement pour pouvoir gérer des billets.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventTypeModal">
                    <i class="fas fa-plus"></i> Créer un événement
                </button>
            </div>
        </div>
    @else
        @foreach($events as $event)
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">{{ $event->title }}</h3>
                    <a href="{{ route('events.tickets.create', $event) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Ajouter un billet
                    </a>
                </div>
                <div class="card-body">
                    @if($event->tickets->isEmpty())
                        <div style="text-align: center; padding: 2rem;">
                            <p style="color: var(--gray-500);">Aucun billet n'a été créé pour cet événement.</p>
                        </div>
                    @else
                        <div class="table-wrapper">
                            <table class="tickets-table">
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
                                            <td>
                                                <span style="font-weight: 600; color: var(--gray-900);">{{ $ticket->nom }}</span>
                                            </td>
                                            <td>
                                                <span style="font-weight: 600;">{{ number_format($ticket->prix, 0, ',', ' ') }} FCFA</span>
                                            </td>
                                            <td>
                                                <span class="badge" style="background: var(--gray-100); color: var(--gray-700);">{{ $ticket->quantite_disponible }}</span>
                                            </td>
                                            <td>
                                                <span style="font-weight: 600; color: var(--primary);">{{ $ticket->sold_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                @if($ticket->promotion_active)
                                                    <span class="badge badge-success">{{ number_format($ticket->prix_promotion, 0, ',', ' ') }} FCFA</span>
                                                @else
                                                    <span class="badge badge-secondary">Non</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('organizer.tickets.edit', $ticket) }}" class="btn btn-sm btn-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>

@endsection
