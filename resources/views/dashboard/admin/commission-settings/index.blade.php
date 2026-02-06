@extends('layouts.dashboard')

@section('title', 'Commissions & Frais')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Commissions & Frais</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item active">Configuration</li>
                    <li class="breadcrumb-item active">Commissions & Frais</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-warning-modern shadow-sm" onclick="resetSettings()">
                <i class="fas fa-undo me-1"></i> Réinitialiser par défaut
            </button>
        </div>
    </div>

    <form action="{{ route('admin.commission-settings.update') }}" method="POST" id="settingsForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Paramètres de Commission -->
            <div class="col-lg-6 mb-4">
                <div class="card modern-card h-100">
                    <div class="card-header-modern">
                        <h5 class="card-title my-1 text-white">
                            <i class="fas fa-percent me-2"></i>Commissions Prestataires
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="settings-section">
                            <div class="mb-4">
                                <label for="mtn_commission_rate" class="form-label fw-bold text-dark">Commission MTN Mobile Money</label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="mtn_commission_rate" 
                                           id="mtn_commission_rate" 
                                           class="form-control" 
                                           value="{{ $settings['mtn_commission_rate'] }}" 
                                           min="0" 
                                           max="100" 
                                           step="0.01" 
                                           required>
                                    <span class="input-group-text bg-light text-dark fw-bold border-start-0">%</span>
                                </div>
                                <div class="form-text text-muted small mt-1">Commission prélevée par MTN sur chaque transaction.</div>
                            </div>

                            <div class="mb-4">
                                <label for="airtel_commission_rate" class="form-label fw-bold text-dark">Commission Airtel Money</label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="airtel_commission_rate" 
                                           id="airtel_commission_rate" 
                                           class="form-control" 
                                           value="{{ $settings['airtel_commission_rate'] }}" 
                                           min="0" 
                                           max="100" 
                                           step="0.01" 
                                           required>
                                    <span class="input-group-text bg-light text-dark fw-bold border-start-0">%</span>
                                </div>
                                <div class="form-text text-muted small mt-1">Commission prélevée par Airtel sur chaque transaction.</div>
                            </div>

                            <div class="mb-0">
                                <label for="mokilievent_commission_rate" class="form-label fw-bold text-dark">Commission MokiliEvent (Plateforme)</label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="mokilievent_commission_rate" 
                                           id="mokilievent_commission_rate" 
                                           class="form-control" 
                                           value="{{ $settings['mokilievent_commission_rate'] }}" 
                                           min="0" 
                                           max="100" 
                                           step="0.01" 
                                           required>
                                    <span class="input-group-text bg-light text-dark fw-bold border-start-0">%</span>
                                </div>
                                <div class="form-text text-muted small mt-1">Frais de service MokiliEvent sur chaque transaction.</div>
                            </div>
                        </div>

                        <div class="callout border rounded p-3 bg-light mt-4">
                            <h6 class="text-primary fw-bold mb-2"><i class="fas fa-info-circle me-1"></i> Note sur les calculs</h6>
                            <p class="small text-muted mb-0">La somme de ces trois commissions sera déduite du montant total payé par le client avant le reversement à l'organisateur.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paramètres de Création d'Événements -->
            <div class="col-lg-6 mb-4">
                <div class="card modern-card h-100">
                    <div class="card-header-modern">
                        <h5 class="card-title my-1 text-white">
                            <i class="fas fa-coins me-2"></i>Frais de Création
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="free_event_creation_fee" class="form-label fw-bold text-dark">Frais pour événement gratuit</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="free_event_creation_fee" 
                                       id="free_event_creation_fee" 
                                       class="form-control" 
                                       value="{{ $settings['free_event_creation_fee'] }}" 
                                       min="0" 
                                       step="100" 
                                       required>
                                <span class="input-group-text bg-light text-dark fw-bold border-start-0">FCFA</span>
                            </div>
                            <div class="form-text text-muted small mt-1">Montant forfaitaire pour la publication d'un événement gratuit.</div>
                        </div>

                        <div class="mb-4">
                            <label for="custom_event_creation_fee" class="form-label fw-bold text-dark">Frais pour événement personnalisé</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="custom_event_creation_fee" 
                                       id="custom_event_creation_fee" 
                                       class="form-control" 
                                       value="{{ $settings['custom_event_creation_fee'] }}" 
                                       min="0" 
                                       step="100" 
                                       required>
                                <span class="input-group-text bg-light text-dark fw-bold border-start-0">FCFA</span>
                            </div>
                            <div class="form-text text-muted small mt-1">Frais appliqués aux événements demandant une gestion spécifique.</div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold text-primary mb-3">Activation des paiements requis</h6>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="require_payment_for_free_events" id="require_payment_for_free_events" {{ $settings['require_payment_for_free_events'] ? 'checked' : '' }}>
                            <label class="form-check-label text-dark" for="require_payment_for_free_events">
                                Exiger le paiement pour les événements gratuits
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="require_payment_for_custom_events" id="require_payment_for_custom_events" {{ $settings['require_payment_for_custom_events'] ? 'checked' : '' }}>
                            <label class="form-check-label text-dark" for="require_payment_for_custom_events">
                                Exiger le paiement pour les événements personnalisés
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12 text-end pb-5">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light shadow-sm me-2 border">
                    <i class="fas fa-times me-1"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary-modern shadow-sm px-5">
                    <i class="fas fa-save me-2"></i> Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function resetSettings() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres aux valeurs par défaut ? Cette action est immédiate.')) {
        window.location.href = '{{ route("admin.commission-settings.reset") }}';
    }
}

// Validation en temps réel
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const mtnRate = parseFloat(document.getElementById('mtn_commission_rate').value);
    const airtelRate = parseFloat(document.getElementById('airtel_commission_rate').value);
    const mokiliRate = parseFloat(document.getElementById('mokilievent_commission_rate').value);
    
    // Suggestion : somme raisonnable
    if (mtnRate + airtelRate + mokiliRate > 50) {
        if (!confirm('Attention : La somme des commissions dépasse 50%. Voulez-vous vraiment enregistrer ces taux très élevés ?')) {
            e.preventDefault();
            return false;
        }
    }
});
</script>
@endpush
