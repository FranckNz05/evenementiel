@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">Journaux</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Détails du log</li>
                </ol>
            </nav>
            <h1>Détails du log</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Message</h6>
                    <span class="badge bg-{{ $this->getLogLevelBadgeClass($log['level'] ?? '') }}">
                        {{ $log['level'] ?? 'N/A' }}
                    </span>
                </div>
                <div class="card-body">
                    <h5>{{ $log['message'] ?? 'Aucun message' }}</h5>
                    
                    @if(isset($log['context']['exception']) && is_string($log['context']['exception']))
                        <div class="mt-3">
                            <h6>Exception:</h6>
                            <pre class="bg-light p-3 rounded">{{ $log['context']['exception'] }}</pre>
                        </div>
                    @endif
                    
                    @if(!empty($log['context']['trace']))
                        <div class="mt-3">
                            <h6>Stack trace:</h6>
                            <div class="bg-dark text-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                <pre class="text-white-50 mb-0">{{ is_array($log['context']['trace']) ? implode("\n", $log['context']['trace']) : $log['context']['trace'] }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            @if(!empty($log['context']) && (!isset($log['context']['exception']) && !isset($log['context']['trace'])))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Contexte</h6>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded">{{ json_encode($log['context'], JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Métadonnées</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Date et heure</span>
                            <span>{{ \Carbon\Carbon::parse($log['@timestamp'])->format('d/m/Y H:i:s') }}</span>
                        </li>
                        
                        @if(isset($log['channel']))
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Canal</span>
                                <span class="badge bg-secondary">{{ $log['channel'] }}</span>
                            </li>
                        @endif
                        
                        @if(!empty($log['tags']))
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tags</span>
                                </div>
                                <div>
                                    @foreach($log['tags'] as $tag)
                                        <span class="badge bg-secondary me-1 mb-1">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </li>
                        @endif
                        
                        @if(isset($log['environment']))
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Environnement</span>
                                <span class="badge bg-info">{{ $log['environment'] }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            @if(isset($log['user']))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Utilisateur</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-circle fa-3x text-gray-300"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">{{ $log['user']['name'] ?? 'Utilisateur inconnu' }}</h6>
                                <small class="text-muted">{{ $log['user']['email'] ?? '' }}</small>
                                @if(isset($log['user']['id']))
                                    <div class="mt-1">
                                        <a href="{{ route('admin.users.show', $log['user']['id']) }}" class="btn btn-sm btn-outline-primary">
                                            Voir le profil
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if(!empty($log['user']['roles']))
                            <div class="mt-3">
                                <h6>Rôles</h6>
                                <div>
                                    @foreach($log['user']['roles'] as $role)
                                        <span class="badge bg-primary me-1">{{ $role }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            @if(isset($log['request']))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Requête</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Méthode</span>
                                <span class="badge bg-{{ $this->getRequestMethodBadgeClass($log['request']['method'] ?? '') }}">
                                    {{ $log['request']['method'] ?? 'N/A' }}
                                </span>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="mb-1">URL</div>
                                <div class="text-truncate" title="{{ $log['request']['url'] ?? '' }}">
                                    <a href="{{ $log['request']['url'] ?? '#' }}" target="_blank" class="text-decoration-none">
                                        {{ $log['request']['url'] ?? 'N/A' }}
                                    </a>
                                </div>
                            </li>
                            @if(isset($log['request']['ip']))
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Adresse IP</span>
                                    <span>{{ $log['request']['ip'] }}</span>
                                </li>
                            @endif
                            @if(isset($log['request']['user_agent']))
                                <li class="list-group-item px-0">
                                    <div class="mb-1">User Agent</div>
                                    <div class="small text-muted">{{ $log['request']['user_agent'] }}</div>
                                </li>
                            @endif
                            @if(isset($log['request']['referer']))
                                <li class="list-group-item px-0">
                                    <div class="mb-1">Referer</div>
                                    <div class="text-truncate">
                                        <a href="{{ $log['request']['referer'] }}" target="_blank" class="text-decoration-none">
                                            {{ $log['request']['referer'] }}
                                        </a>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif
            
            @if(isset($log['server']))
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Serveur</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @if(isset($log['server']['hostname']))
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Nom d'hôte</span>
                                    <span>{{ $log['server']['hostname'] }}</span>
                                </li>
                            @endif
                            @if(isset($log['server']['ip']))
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>IP du serveur</span>
                                    <span>{{ $log['server']['ip'] }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge {
        font-weight: 500;
    }
    .badge.bg-debug { background-color: #6c757d; }
    .badge.bg-info { background-color: #17a2b8; }
    .badge.bg-notice { background-color: #20c997; }
    .badge.bg-warning { background-color: #ffc107; color: #000; }
    .badge.bg-error { background-color: #fd7e14; }
    .badge.bg-critical { background-color: #dc3545; }
    .badge.bg-alert { background-color: #dc3545; }
    .badge.bg-emergency { background-color: #dc3545; }
    
    .badge.bg-get { background-color: #28a745; }
    .badge.bg-post { background-color: #007bff; }
    .badge.bg-put { background-color: #6f42c1; }
    .badge.bg-delete { background-color: #dc3545; }
    .badge.bg-patch { background-color: #17a2b8; }
    .badge.bg-options { background-color: #6c757d; }
    .badge.bg-head { background-color: #6c757d; }
    
    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
        margin-bottom: 0;
    }
    
    .text-truncate {
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@push('scripts')
<script>
    // Fonction pour copier le contenu d'un élément dans le presse-papier
    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        const text = element.textContent || element.innerText;
        
        navigator.clipboard.writeText(text).then(() => {
            // Afficher un message de succès
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 m-3 p-3 bg-success text-white rounded shadow';
            toast.style.zIndex = '9999';
            toast.textContent = 'Copié dans le presse-papier !';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 2000);
        }).catch(err => {
            console.error('Erreur lors de la copie : ', err);
        });
    }
    
    // Initialisation des tooltips Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection
