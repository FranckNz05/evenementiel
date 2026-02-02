@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1>Journaux d'activité</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Journaux</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de recherche</h6>
            <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-outline-secondary">Réinitialiser</a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.logs.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="q" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Message, erreur...">
                </div>
                <div class="col-md-2">
                    <label for="level" class="form-label">Niveau</label>
                    <select class="form-select" id="level" name="level">
                        <option value="">Tous les niveaux</option>
                        @foreach($filters['levels'] as $level)
                            <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="tag" class="form-label">Tag</label>
                    <select class="form-select" id="tag" name="tag">
                        <option value="">Tous les tags</option>
                        @foreach($filters['tags'] as $tag)
                            <option value="{{ $tag }}" {{ request('tag') == $tag ? 'selected' : '' }}>{{ $tag }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="channel" class="form-label">Canal</label>
                    <select class="form-select" id="channel" name="channel">
                        <option value="">Tous les canaux</option>
                        @foreach($filters['channels'] as $channel)
                            <option value="{{ $channel }}" {{ request('channel') == $channel ? 'selected' : '' }}>{{ $channel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_range" class="form-label">Période</label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="De">
                        <span class="input-group-text">à</span>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="À">
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Résultats ({{ $logs->count() }})</h6>
            <div class="btn-group">
                <a href="{{ route('admin.logs.export', request()->all()) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-download fa-sm"></i> Exporter
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($logs->isEmpty())
                <div class="alert alert-info">Aucun log ne correspond à vos critères de recherche.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Niveau</th>
                                <th>Message</th>
                                <th>Utilisateur</th>
                                <th>Tags</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr class="table-{{ $this->getLogLevelClass($log['level'] ?? '') }}">
                                    <td nowrap>{{ \Carbon\Carbon::parse($log['@timestamp'])->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $this->getLogLevelBadgeClass($log['level'] ?? '') }}">
                                            {{ $log['level'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-truncate" style="max-width: 300px;" title="{{ $log['message'] }}">
                                        {{ $log['message'] }}
                                    </td>
                                    <td>
                                        @if(isset($log['user']))
                                            {{ $log['user']['name'] ?? 'N/A' }}
                                            <small class="d-block text-muted">{{ $log['user']['email'] ?? '' }}</small>
                                        @else
                                            Système
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($log['tags']))
                                            @foreach(array_slice($log['tags'], 0, 3) as $tag)
                                                <span class="badge bg-secondary me-1 mb-1">{{ $tag }}</span>
                                            @endforeach
                                            @if(count($log['tags']) > 3)
                                                <span class="badge bg-light text-dark">+{{ count($log['tags']) - 3 }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.logs.show', $log['_id']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if(method_exists($logs, 'links'))
                    <div class="mt-3">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .table .table-success { background-color: rgba(40, 167, 69, 0.1); }
    .table .table-info { background-color: rgba(23, 162, 184, 0.1); }
    .table .table-warning { background-color: rgba(255, 193, 7, 0.1); }
    .table .table-danger { background-color: rgba(220, 53, 69, 0.1); }
    .text-truncate {
        max-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@push('scripts')
<script>
    // Mise à jour de l'URL avec les paramètres de filtre
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('select[data-filter]');
        
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
        
        // Initialisation des sélecteurs de date
        $('input[type="date"]').on('change', function() {
            $(this).closest('form').submit();
        });
    });
</script>
@endpush
@endsection
