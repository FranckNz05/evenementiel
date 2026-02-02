@extends('layouts.dashboard')

@section('title', 'Codes d\'accès')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-lg-6">
            <h1 class="h3 mb-2 mb-lg-0 text-gray-800 fw-bold">
                <i class="fas fa-key text-primary me-2"></i>
                Codes d'accès
            </h1>
            <p class="text-muted mb-0 small">Gérez les codes d'accès pour vos événements</p>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                <button class="btn btn-primary d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#generateCodeModal">
                    <i class="fas fa-plus me-2"></i>
                    <span class="d-none d-sm-inline">Générer un code</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tableau des codes d'accès -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-4">
                    <h5 class="mb-1 fw-bold text-primary">
                        <i class="fas fa-list me-2"></i>
                        Mes codes d'accès
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($accessCodes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 border-0 fw-semibold text-gray-700">Code</th>
                                        <th class="px-4 py-3 border-0 fw-semibold text-gray-700">Événement</th>
                                        <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Valide du</th>
                                        <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Valide jusqu'au</th>
                                        <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Statut</th>
                                        <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($accessCodes as $code)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-4">
                                            <code class="bg-light px-2 py-1 rounded">{{ $code->access_code }}</code>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <h6 class="mb-1 fw-semibold text-gray-800">
                                                        {{ Str::limit($code->event->title ?? 'Événement supprimé', 40) }}
                                                    </h6>
                                                    @if($code->event)
                                                        <small class="text-muted">{{ $code->event->start_date->format('d/m/Y H:i') }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 text-center">
                                            <span class="text-muted">{{ $code->valid_from->format('d/m/Y H:i') }}</span>
                                        </td>
                                        <td class="px-3 py-4 text-center">
                                            <span class="text-muted">{{ $code->valid_until->format('d/m/Y H:i') }}</span>
                                        </td>
                                        <td class="px-3 py-4 text-center">
                                            @if($code->isValid())
                                                <span class="badge bg-success">Valide</span>
                                            @elseif(!$code->is_active)
                                                <span class="badge bg-secondary">Inactif</span>
                                            @elseif(now()->greaterThan($code->valid_until))
                                                <span class="badge bg-danger">Expiré</span>
                                            @else
                                                <span class="badge bg-warning">En attente</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 text-center">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" onclick="copyCode('{{ $code->access_code }}')" title="Copier">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <form method="POST" action="{{ route('organizer.access-codes.delete', $code) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce code ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center py-3">
                            {{ $accessCodes->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-key fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun code d'accès</h5>
                                <p class="text-muted mb-4">Générez votre premier code d'accès pour un événement</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateCodeModal">
                                    <i class="fas fa-plus me-2"></i>
                                    Générer un code
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de génération de code -->
<div class="modal fade" id="generateCodeModal" tabindex="-1" aria-labelledby="generateCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateCodeModalLabel">
                    <i class="fas fa-key me-2"></i>
                    Générer un code d'accès
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('organizer.access-codes.generate') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="event_id" class="form-label fw-semibold">Événement <span class="text-danger">*</span></label>
                        <select class="form-select" id="event_id" name="event_id" required>
                            <option value="">Sélectionner un événement</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->title }} - {{ $event->start_date->format('d/m/Y H:i') }}</option>
                            @endforeach
                        </select>
                        @error('event_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="valid_until" class="form-label fw-semibold">Valide jusqu'au</label>
                        <input type="datetime-local" class="form-control" id="valid_until" name="valid_until">
                        <div class="form-text">Laisser vide pour utiliser la date de fin de l'événement</div>
                        @error('valid_until')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description (optionnel)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description du code d'accès..."></textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key me-2"></i>
                        Générer le code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.empty-state {
    padding: 2rem;
}

.card {
    border-radius: 12px;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

code {
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
}
</style>

@push('scripts')
<script>
function copyCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        // Afficher une notification de succès
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                Code copié dans le presse-papiers !
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Supprimer la notification après 3 secondes
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }).catch(function(err) {
        console.error('Erreur lors de la copie: ', err);
        alert('Erreur lors de la copie du code');
    });
}

// Auto-remplir la date de fin quand un événement est sélectionné
document.getElementById('event_id').addEventListener('change', function() {
    const eventId = this.value;
    if (eventId) {
        // Ici vous pourriez faire un appel AJAX pour récupérer la date de fin de l'événement
        // Pour l'instant, on laisse l'utilisateur saisir manuellement
    }
});
</script>
@endpush
@endsection
