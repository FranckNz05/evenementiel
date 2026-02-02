@extends('layouts.app')

@section('title', 'Test des logs Elasticsearch/Kibana')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Test des logs Elasticsearch/Kibana
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Statut des services -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-{{ $elasticsearchStatus['status'] === 'connected' ? 'success' : 'danger' }}">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-database"></i>
                                        Elasticsearch
                                    </h5>
                                    <p class="card-text">
                                        <span class="badge badge-{{ $elasticsearchStatus['status'] === 'connected' ? 'success' : 'danger' }}">
                                            {{ $elasticsearchStatus['status'] === 'connected' ? 'Connecté' : 'Erreur' }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $elasticsearchStatus['message'] }}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-{{ $kibanaStatus['status'] === 'connected' ? 'success' : 'danger' }}">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-chart-bar"></i>
                                        Kibana
                                    </h5>
                                    <p class="card-text">
                                        <span class="badge badge-{{ $kibanaStatus['status'] === 'connected' ? 'success' : 'danger' }}">
                                            {{ $kibanaStatus['status'] === 'connected' ? 'Connecté' : 'Erreur' }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $kibanaStatus['message'] }}</small>
                                        @if($kibanaStatus['status'] === 'connected' && isset($kibanaStatus['url']))
                                            <br>
                                            <a href="{{ $kibanaStatus['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="fas fa-external-link-alt"></i> Ouvrir Kibana
                                            </a>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations sur l'index -->
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Informations sur l'index</h5>
                        <p><strong>Index actuel :</strong> <code>{{ $index }}</code></p>
                        <p><strong>Pattern de recherche :</strong> <code>{{ $index }}*</code></p>
                    </div>

                    <!-- Génération de logs de test -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-plus-circle"></i> Générer des logs de test</h5>
                        </div>
                        <div class="card-body">
                            <form id="generateLogsForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="count">Nombre de logs</label>
                                            <select class="form-control" id="count" name="count">
                                                <option value="5">5 logs</option>
                                                <option value="10">10 logs</option>
                                                <option value="20">20 logs</option>
                                                <option value="50">50 logs</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="level">Niveau de log</label>
                                            <select class="form-control" id="level" name="level">
                                                <option value="debug">Debug</option>
                                                <option value="info" selected>Info</option>
                                                <option value="warning">Warning</option>
                                                <option value="error">Error</option>
                                                <option value="critical">Critical</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-play"></i> Générer les logs
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Vérification des logs -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-search"></i> Vérifier les logs dans Elasticsearch</h5>
                            <button class="btn btn-outline-primary" onclick="verifyLogs()">
                                <i class="fas fa-refresh"></i> Actualiser
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="logsResults">
                                <p class="text-muted">Cliquez sur "Actualiser" pour voir les logs récents</p>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions Kibana -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-graduation-cap"></i> Instructions pour Kibana</h5>
                        </div>
                        <div class="card-body">
                            <ol>
                                <li>Ouvrez Kibana dans votre navigateur : <a href="http://localhost:5601" target="_blank">http://localhost:5601</a></li>
                                <li>Allez dans <strong>Stack Management</strong> > <strong>Index Patterns</strong></li>
                                <li>Créez un pattern d'index : <code>{{ $index }}*</code></li>
                                <li>Allez dans <strong>Discover</strong> et sélectionnez votre pattern</li>
                                <li>Filtrez par : <code>service:"mokilievent"</code></li>
                                <li>Vous devriez voir vos logs de test !</li>
                            </ol>
                            
                            <div class="mt-3">
                                <h6>Requête de recherche Kibana :</h6>
                                <div class="bg-light p-3 rounded">
                                    <code>service:"mokilievent" AND @timestamp:[now-1h TO now]</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formulaire de génération de logs
    document.getElementById('generateLogsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const button = this.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération...';
        button.disabled = true;
        
        fetch('{{ route("test-logs.generate") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                // Actualiser automatiquement les logs après génération
                setTimeout(verifyLogs, 2000);
            } else {
                showAlert('danger', 'Erreur : ' + data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Erreur : ' + error.message);
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    });
});

function verifyLogs() {
    const resultsDiv = document.getElementById('logsResults');
    resultsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Vérification en cours...</div>';
    
    fetch('{{ route("test-logs.verify") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = `
                <div class="alert alert-success">
                    <strong>${data.total}</strong> logs trouvés dans l'index <code>${data.index}</code>
                </div>
            `;
            
            if (data.logs.length > 0) {
                html += '<div class="table-responsive"><table class="table table-striped">';
                html += '<thead><tr><th>Niveau</th><th>Message</th><th>Timestamp</th><th>Actions</th></tr></thead><tbody>';
                
                data.logs.forEach(log => {
                    const levelClass = getLevelClass(log.level);
                    html += `
                        <tr>
                            <td><span class="badge badge-${levelClass}">${log.level.toUpperCase()}</span></td>
                            <td>${log.message}</td>
                            <td>${new Date(log.timestamp).toLocaleString()}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" onclick="showLogDetails('${log.id}')">
                                    <i class="fas fa-eye"></i> Détails
                                </button>
                            </td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table></div>';
            }
            
            resultsDiv.innerHTML = html;
        } else {
            resultsDiv.innerHTML = `<div class="alert alert-danger">Erreur : ${data.error}</div>`;
        }
    })
    .catch(error => {
        resultsDiv.innerHTML = `<div class="alert alert-danger">Erreur : ${error.message}</div>`;
    });
}

function getLevelClass(level) {
    const classes = {
        'debug': 'secondary',
        'info': 'info',
        'warning': 'warning',
        'error': 'danger',
        'critical': 'dark'
    };
    return classes[level] || 'secondary';
}

function showLogDetails(logId) {
    // Ici vous pourriez implémenter une modal pour afficher les détails du log
    alert('Détails du log ID: ' + logId);
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endsection
