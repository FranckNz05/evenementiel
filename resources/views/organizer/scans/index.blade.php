@extends('layouts.dashboard')

@section('title', 'Historique des scans')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Historique des scans</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('organizer.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Scans</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Statistiques des scans -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-50 small">Scans aujourd'hui</div>
                            <div class="text-lg fw-bold">{{ $scanStats['today'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-calendar-day fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-50 small">Cette semaine</div>
                            <div class="text-lg fw-bold">{{ $scanStats['week'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-calendar-week fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-50 small">Ce mois</div>
                            <div class="text-lg fw-bold">{{ $scanStats['month'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-50 small">Total des scans</div>
                            <div class="text-lg fw-bold">{{ $scanStats['total'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-qrcode fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <i class="fas fa-filter me-1"></i>
            Filtrer les scans
        </div>
        <div class="card-body">
            <form action="{{ route('organizer.scans.filter') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="event_id" class="form-label">Événement</label>
                    <select name="event_id" id="event_id" class="form-select">
                        <option value="">Tous les événements</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request()->event_id == $event->id ? 'selected' : '' }}>
                                {{ $event->title ?? $event->titre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_start" class="form-label">Date début</label>
                    <input type="date" name="date_start" id="date_start" class="form-control" value="{{ request()->date_start }}">
                </div>
                <div class="col-md-3">
                    <label for="date_end" class="form-label">Date fin</label>
                    <input type="date" name="date_end" id="date_end" class="form-control" value="{{ request()->date_end }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('organizer.scans.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-redo me-1"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des scans -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-qrcode me-1"></i>
                Historique des scans
            </div>
            <div>
                <a href="{{ route('organizer.scans.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-csv me-1"></i> Exporter en CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($scans->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="scansTable">
                    <thead>
                        <tr>
                            <th>Date et heure</th>
                            <th>Événement</th>
                            <th>Type de billet</th>
                            <th>Code QR</th>
                            <th>Scanné par</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scans as $scan)
                        <tr>
                            <td>{{ $scan->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $scan->ticket->event->title ?? $scan->ticket->event->titre ?? 'Événement inconnu' }}</td>
                            <td>{{ $scan->ticket->nom ?? $scan->ticket->name ?? 'Billet inconnu' }}</td>
                            <td><code>{{ substr($scan->qr_code ?? $scan->ticket->qr_code ?? 'N/A', 0, 15) }}...</code></td>
                            <td>
                                @if($scan->scannedBy)
                                    {{ $scan->scannedBy->name ?? ($scan->scannedBy->prenom ?? '') . ' ' . ($scan->scannedBy->nom ?? '') }}
                                @else
                                    Utilisateur inconnu
                                @endif
                            </td>
                            <td>
                                @if(($scan->status ?? $scan->statut) == 'success')
                                    <span class="badge bg-success">Validé</span>
                                @elseif(($scan->status ?? $scan->statut) == 'error')
                                    <span class="badge bg-danger">Erreur</span>
                                @elseif(($scan->status ?? $scan->statut) == 'already_used')
                                    <span class="badge bg-warning">Déjà utilisé</span>
                                @else
                                    <span class="badge bg-secondary">{{ $scan->status ?? $scan->statut ?? 'Inconnu' }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $scans->links() }}
            </div>
            @else
            <div class="alert alert-info">
                Aucun scan enregistré pour le moment.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#scansTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json',
            },
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf'
            ]
        });
    });
</script>
@endsection
