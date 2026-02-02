@extends('layouts.dashboard')

@section('title', 'Historique des scans')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="fas fa-history me-2 text-primary"></i>
            Historique complet des scans
        </h5>
        <a href="{{ route('organizer.scans.index') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-qrcode me-1"></i>Scanner des billets
        </a>
    </div>

    <!-- Filtres -->
    <div class="mb-4 p-3 bg-light rounded">
        <form action="{{ route('scans.history') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="event_id" class="form-label fw-semibold">Événement</label>
                    <select class="form-select" id="event_id" name="event_id">
                        <option value="">Tous les événements</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label fw-semibold">Date de début</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label fw-semibold">Date de fin</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i>Filtrer
                        </button>
                        <a href="{{ route('scans.history') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Tableau -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="dataTable">
            <thead class="table-light">
                <tr>
                    <th>Date/Heure</th>
                    <th>Événement</th>
                    <th>Billet</th>
                    <th>Client</th>
                    <th>Scanné par</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scans as $scan)
                <tr>
                    <td>{{ $scan->scanned_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $scan->ticket->event->title ?? 'N/A' }}</td>
                    <td>{{ $scan->ticket->name ?? 'N/A' }}</td>
                    <td>{{ $scan->order->user->name ?? 'N/A' }}</td>
                    <td>{{ $scan->scannedBy->name ?? 'N/A' }}</td>
                    <td>
                        @if($scan->is_valid)
                            <span class="badge bg-success">Valide</span>
                        @else
                            <span class="badge bg-danger">Invalide</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        Aucun scan trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($scans->hasPages())
    <div class="mt-4">
        {{ $scans->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection