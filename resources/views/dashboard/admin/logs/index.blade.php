@extends('layouts.app')

@section('title', 'Logs - Administration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt me-2"></i>
                        Logs de l'application
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.logs.stats') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Statistiques
                        </a>
                    </div>
                </div>

                <div class="card-body-modern">
                    <!-- Filtres -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="level" class="form-label">Niveau</label>
                                <select name="level" id="level" class="form-select">
                                    <option value="">Tous les niveaux</option>
                                    <option value="ERROR" {{ request('level') == 'ERROR' ? 'selected' : '' }}>ERROR</option>
                                    <option value="WARNING" {{ request('level') == 'WARNING' ? 'selected' : '' }}>WARNING</option>
                                    <option value="INFO" {{ request('level') == 'INFO' ? 'selected' : '' }}>INFO</option>
                                    <option value="DEBUG" {{ request('level') == 'DEBUG' ? 'selected' : '' }}>DEBUG</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="service" class="form-label">Service</label>
                                <select name="service" id="service" class="form-select">
                                    <option value="">Tous les services</option>
                                    <option value="mokilievent" {{ request('service') == 'mokilievent' ? 'selected' : '' }}>MokiliEvent</option>
                                    <option value="docker" {{ request('service') == 'docker' ? 'selected' : '' }}>Docker</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Date de début</label>
                                <input type="datetime-local" name="date_from" id="date_from" 
                                       class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Date de fin</label>
                                <input type="datetime-local" name="date_to" id="date_to" 
                                       class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-8">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Rechercher dans les messages..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                                <a href="{{ route('admin.logs.index') }}" class="modern-btn btn-secondary-modern">
                                    <i class="fas fa-times"></i> Effacer
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Résultats -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Niveau</th>
                                    <th>Service</th>
                                    <th>Message</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    @php
                                        $logData = $log['_source'];
                                        $level = $logData['level'] ?? 'UNKNOWN';
                                        $levelClass = match($level) {
                                            'ERROR' => 'danger',
                                            'WARNING' => 'warning',
                                            'INFO' => 'info',
                                            'DEBUG' => 'secondary',
                                            default => 'light'
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($logData['@timestamp'])->format('d/m/Y H:i:s') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $levelClass }}">
                                                {{ $level }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $logData['service'] ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="log-message" style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $logData['message'] ?? 'N/A' }}
                                            </div>
                                            @if(isset($logData['tags']) && count($logData['tags']) > 0)
                                                <div class="mt-1">
                                                    @foreach($logData['tags'] as $tag)
                                                        <span class="badge bg-light text-dark me-1">{{ $tag }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.logs.show', $log['_id']) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Aucun log trouvé
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($total > 50)
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                Affichage de 50 logs sur {{ $total }} au total
                            </small>
                            <button class="btn btn-outline-primary btn-sm" onclick="loadMore()">
                                <i class="fas fa-plus"></i> Charger plus
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadMore() {
    // Implémentation pour charger plus de logs
    console.log('Chargement de plus de logs...');
}
</script>
@endpush
@endsection
