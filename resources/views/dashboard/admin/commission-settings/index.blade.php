@extends('layouts.dashboard')

@section('title', 'Paramètres de Commission')

@push('styles')
<style>
:root {
    --bleu-nuit: #1e3a8a;
    --bleu-nuit-clair: #3b82f6;
    --blanc-or: #ffffff;
    --blanc-amber: #f59e0b;
    --shadow-gold: rgba(255, 215, 0, 0.2);
    --shadow-blue: rgba(30, 58, 138, 0.1);
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --border-radius: 0.75rem;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --transition: all 0.3s ease;
}

.container-fluid {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    min-height: 100vh;
    padding: 2rem;
    border-top: 4px solid var(--bleu-nuit);
}

.page-header {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    padding: 2rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.page-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    position: relative;
    z-index: 1;
}

.modern-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    transition: var(--transition);
}

.modern-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.card-header-modern {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    border-bottom: 2px solid var(--bleu-nuit);
    padding: 1.5rem;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--bleu-nuit);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.settings-section {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--bleu-nuit);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--gray-200);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--bleu-nuit);
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--bleu-nuit);
    box-shadow: 0 0 0 3px var(--shadow-blue);
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.form-check-input {
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid var(--gray-300);
    border-radius: 0.25rem;
    cursor: pointer;
}

.form-check-input:checked {
    background-color: var(--bleu-nuit);
    border-color: var(--bleu-nuit);
}

.form-check-label {
    font-weight: 500;
    color: var(--gray-700);
    cursor: pointer;
}

.input-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.input-group-text {
    background: var(--gray-100);
    border: 2px solid var(--gray-200);
    border-right: none;
    padding: 0.75rem 1rem;
    border-radius: var(--border-radius) 0 0 var(--border-radius);
    font-weight: 600;
    color: var(--bleu-nuit);
}

.input-group .form-control {
    border-radius: 0 var(--border-radius) var(--border-radius) 0;
    border-left: none;
}

.btn-modern {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition);
    box-shadow: var(--shadow);
    cursor: pointer;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: var(--white);
    text-decoration: none;
}

.btn-secondary-modern {
    background: linear-gradient(135deg, var(--gray-500) 0%, var(--gray-600) 100%);
    color: var(--white);
}

.btn-warning-modern {
    background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
    color: var(--white);
}

.btn-success-modern {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    color: var(--white);
}

.help-text {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-top: 0.25rem;
}

.example-calculation {
    background: var(--gray-50);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-top: 1rem;
    border-left: 4px solid var(--blanc-or);
}

.example-title {
    font-weight: 600;
    color: var(--bleu-nuit);
    margin-bottom: 0.5rem;
}

.example-text {
    font-size: 0.875rem;
    color: var(--gray-700);
    line-height: 1.5;
}

/* Nouveau layout */
.dashboard-section {
    background: #f6f7fb;
    min-height: 100vh;
    padding: 2.5rem 1.5rem;
}

.dashboard-head {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    box-shadow: 0 18px 35px rgba(15, 23, 42, 0.08);
    margin-bottom: 1.75rem;
}

.dashboard-head h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 0.35rem;
}

.dashboard-head p {
    margin: 0;
    color: #6b7280;
}

.dashboard-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.dashboard-btn {
    border: 1px solid #e5e7eb;
    border-radius: 999px;
    padding: 0.5rem 1.3rem;
    background: #ffffff;
    color: #0f172a;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    text-decoration: none;
}

.dashboard-btn.warning {
    background: #b45309;
    color: #fff;
    border-color: #b45309;
}

.dashboard-btn.primary {
    background: #0f172a;
    color: #fff;
    border-color: #0f172a;
}

.dashboard-btn.success {
    background: #0f766e;
    color: #fff;
    border-color: #0f766e;
}

.dashboard-btn.secondary {
    background: #f3f4f6;
    color: #0f172a;
}

.dashboard-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    box-shadow: 0 18px 35px rgba(15, 23, 42, 0.08);
    margin-bottom: 1.5rem;
}

.dashboard-card__header {
    padding: 1.2rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.dashboard-card__title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 600;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dashboard-card__body {
    padding: 1.5rem;
}

.callout {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    background: #f9fafb;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .page-header {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .page-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .input-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .input-group-text {
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        border-right: 2px solid var(--gray-200);
        border-bottom: none;
    }
    
    .input-group .form-control {
        border-radius: 0 0 var(--border-radius) var(--border-radius);
        border-left: 2px solid var(--gray-200);
    }
}
</style>
@endpush

@section('content')
<div class="dashboard-section">
    <div class="dashboard-head">
        <div>
            <h1>Paramètres de commission</h1>
            <p>Définissez les taux des prestataires et les frais appliqués à la plateforme.</p>
        </div>
        <div class="dashboard-actions">
            <button type="button" class="dashboard-btn warning" onclick="resetSettings()">
                <i class="fas fa-undo"></i> Réinitialiser
            </button>
        </div>
    </div>

    <form action="{{ route('admin.commission-settings.update') }}" method="POST" id="settingsForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Paramètres de Commission -->
            <div class="col-lg-6">
                <div class="dashboard-card">
                    <div class="dashboard-card__header">
                        <h5 class="dashboard-card__title"><i class="fas fa-percent"></i> Commissions paiement</h5>
                    </div>
                    <div class="dashboard-card__body">
                        <div class="settings-section">
                            <div class="form-group">
                                <label for="mtn_commission_rate" class="form-label">Commission MTN Mobile Money</label>
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
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="help-text">Commission prélevée par MTN sur chaque transaction</div>
                            </div>

                            <div class="form-group">
                                <label for="airtel_commission_rate" class="form-label">Commission Airtel Money</label>
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
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="help-text">Commission prélevée par Airtel sur chaque transaction</div>
                            </div>

                            <div class="form-group">
                                <label for="mokilievent_commission_rate" class="form-label">Commission MokiliEvent</label>
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
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="help-text">Commission MokiliEvent sur chaque transaction</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paramètres de Création d'Événements -->
            <div class="col-lg-6">
                <div class="dashboard-card">
                    <div class="dashboard-card__header">
                        <h5 class="dashboard-card__title"><i class="fas fa-ticket-alt"></i> Frais de création</h5>
                    </div>
                    <div class="dashboard-card__body">
                        <div class="settings-section">
                            <div class="form-group">
                                <label for="free_event_creation_fee" class="form-label">Frais pour événement gratuit</label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="free_event_creation_fee" 
                                           id="free_event_creation_fee" 
                                           class="form-control" 
                                           value="{{ $settings['free_event_creation_fee'] }}" 
                                           min="0" 
                                           step="100" 
                                           required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                <div class="help-text">Montant à payer pour créer un événement gratuit</div>
                            </div>

                            <div class="form-group">
                                <label for="custom_event_creation_fee" class="form-label">Frais pour événement personnalisé</label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="custom_event_creation_fee" 
                                           id="custom_event_creation_fee" 
                                           class="form-control" 
                                           value="{{ $settings['custom_event_creation_fee'] }}" 
                                           min="0" 
                                           step="100" 
                                           required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                <div class="help-text">Montant à payer pour créer un événement personnalisé</div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h6 class="section-title">Activation des paiements</h6>
                            <div class="switch-list">
                                <label>
                                    <input type="checkbox" name="require_payment_for_free_events" id="require_payment_for_free_events" {{ $settings['require_payment_for_free_events'] ? 'checked' : '' }}>
                                    Exiger le paiement pour les événements gratuits
                                </label>
                                <label>
                                    <input type="checkbox" name="require_payment_for_custom_events" id="require_payment_for_custom_events" {{ $settings['require_payment_for_custom_events'] ? 'checked' : '' }}>
                                    Exiger le paiement pour les événements personnalisés
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="actions-row mt-4">
            <button type="submit" class="dashboard-btn success">
                <i class="fas fa-save"></i> Sauvegarder
            </button>
            <a href="{{ route('admin.dashboard') }}" class="dashboard-btn secondary">
                <i class="fas fa-arrow-left"></i> Tableau de bord
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function resetSettings() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres aux valeurs par défaut ?')) {
        window.location.href = '{{ route("admin.commission-settings.reset") }}';
    }
}

// Validation en temps réel
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const mtnRate = parseFloat(document.getElementById('mtn_commission_rate').value);
    const airtelRate = parseFloat(document.getElementById('airtel_commission_rate').value);
    const mokiliRate = parseFloat(document.getElementById('mokilievent_commission_rate').value);
    
    if (mtnRate + airtelRate + mokiliRate > 50) {
        e.preventDefault();
        alert('Attention : La somme des commissions ne devrait pas dépasser 50% pour maintenir des revenus attractifs pour les organisateurs.');
        return false;
    }
    
    if (mtnRate < 0 || airtelRate < 0 || mokiliRate < 0) {
        e.preventDefault();
        alert('Les taux de commission ne peuvent pas être négatifs.');
        return false;
    }
});
</script>
@endpush
