@extends('layouts.dashboard')

@section('title', 'Mes Événements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mes Événements</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#chooseEventTypeModal">
            <i class="fas fa-plus"></i> Créer un événement
        </button>
    </div>

    <!-- Événements publics -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                Événements publics
            </h5>
            <a href="{{ route('events.wizard.step1') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nouvel événement public
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="eventsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->formatted_date }}</td>
                            <td>
                                <span class="badge {{ $event->status_badge }}">
                                    {{ $event->status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                                Aucun événement public. Créez votre premier événement !
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Événements personnalisés -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-user-lock me-2 text-success"></i>
                Événements personnalisés
            </h5>
            <a href="{{ route('custom-personal-events.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Nouvel événement personnalisé
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="customEventsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Période</th>
                            <th>Lieu</th>
                            <th>Invités</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customEvents ?? [] as $event)
                        <tr>
                            <td>{{ $event->title }}</td>
                            <td>
                                <div>{{ $event->start_date }} au {{ $event->end_date }}</div>
                            </td>
                            <td>{{ $event->location }}</td>
                            <td>
                                <span class="badge bg-info">{{ $event->guests->count() }}</span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('custom-personal-events.show', $event->url) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('custom-personal-events.edit', $event->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-user-slash fa-3x mb-3 d-block"></i>
                                Aucun événement personnalisé. Créez un événement privé pour vos invités !
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Choix Type d'Événement -->
<div class="modal fade" id="chooseEventTypeModal" tabindex="-1" aria-labelledby="chooseEventTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chooseEventTypeModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Créer un nouvel événement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <p class="text-muted">Quel type d'événement souhaitez-vous créer ?</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('events.wizard.step1') }}" class="text-decoration-none">
                            <div class="card h-100 border-2 border-primary" style="transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-users fa-4x text-primary"></i>
                                    </div>
                                    <h6 class="card-title text-primary mb-2">Événement public</h6>
                                    <p class="card-text text-muted mb-3" style="font-size: 0.9rem;">
                                        Événement ouvert à tous, visible dans la liste publique des événements
                                    </p>
                                    <div class="d-flex justify-content-center gap-2 mb-3">
                                        <span class="badge bg-primary">Public</span>
                                        <span class="badge bg-info">Visible</span>
                                        <span class="badge bg-success">Payant</span>
                                    </div>
                                    <div class="btn btn-primary w-100">
                                        <i class="fas fa-arrow-right me-1"></i>
                                        Créer événement public
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-6">
                        <a href="{{ route('custom-personal-events.create') }}" class="text-decoration-none">
                            <div class="card h-100 border-2 border-success" style="transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-lock fa-4x text-success"></i>
                                    </div>
                                    <h6 class="card-title text-success mb-2">Événement personnalisé</h6>
                                    <p class="card-text text-muted mb-3" style="font-size: 0.9rem;">
                                        Événement privé sur invitation : mariage, anniversaire, réunion familiale...
                                    </p>
                                    <div class="d-flex justify-content-center gap-2 mb-3">
                                        <span class="badge bg-success">Privé</span>
                                        <span class="badge bg-warning">Invitation</span>
                                        <span class="badge bg-secondary">Gratuit</span>
                                    </div>
                                    <div class="btn btn-success w-100">
                                        <i class="fas fa-arrow-right me-1"></i>
                                        Créer événement privé
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#eventsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            }
        });
        
        $('#customEventsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            }
        });
    });
</script>
@endpush