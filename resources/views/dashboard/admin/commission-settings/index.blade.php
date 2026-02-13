@extends('layouts.dashboard')

@section('title', 'Commissions & Frais')

@push('styles')
<style>
:root {
    --primary: #0f1a3d;
    --primary-light: #1a237e;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
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
}

.commissions-page {
    min-height: 100vh;
    background: var(--gray-50);
    padding: 2rem;
}

/* Header - Section bleue */
.page-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 0.75rem;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.15);
}

.page-title-section h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-title-section p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.page-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-light);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.2);
}

.btn-secondary {
    background: white;
    color: var(--primary);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(255, 255, 255, 0.5);
    color: var(--primary-light);
}

.btn-warning {
    background: var(--warning);
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.btn-light {
    background: white;
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-light:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-900);
}

.btn-outline-secondary {
    background: transparent;
    border: 1px solid var(--gray-300);
    color: var(--gray-700);
}

.btn-outline-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-900);
}

/* Card */
.card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: all 0.2s;
    height: 100%;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-title i {
    color: var(--primary);
}

.card-body {
    padding: 1.5rem;
}

/* Section Header */
.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--gray-200);
}

.section-header i {
    color: var(--primary);
    font-size: 1.125rem;
}

.section-header h6 {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0;
}

/* Form */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-control,
.form-select {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 1px solid var(--gray-300);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    background: white;
}

.form-control:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.form-control.is-invalid,
.form-select.is-invalid {
    border-color: var(--danger);
}

.invalid-feedback {
    display: block;
    color: var(--danger);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.form-text {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

/* Input Group */
.input-group {
    display: flex;
    width: 100%;
}

.input-group-text {
    display: flex;
    align-items: center;
    padding: 0.625rem 0.875rem;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-radius: 0 0.5rem 0.5rem 0;
    color: var(--gray-700);
    font-size: 0.875rem;
    font-weight: 600;
    border-left: none;
}

.input-group .form-control {
    border-radius: 0.5rem 0 0 0.5rem;
}

/* Form Check */
.form-check {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.form-check-input {
    width: 2.5rem;
    height: 1.25rem;
    background-color: var(--gray-300);
    border-radius: 2rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.form-check-input:checked {
    background-color: var(--primary);
}

.form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.form-check-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-700);
    cursor: pointer;
}

/* Callout */
.callout {
    background: var(--gray-50);
    border-left: 4px solid var(--primary);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 1.5rem;
}

.callout h6 {
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.callout p {
    font-size: 0.8125rem;
    color: var(--gray-600);
    margin-bottom: 0;
}

/* Divider */
hr {
    border: 0;
    border-top: 1px solid var(--gray-200);
    margin: 1.5rem 0;
}

/* Breadcrumb */
.breadcrumb {
    display: flex;
    flex-wrap: wrap;
    padding: 0;
    margin: 0.5rem 0 0 0;
    list-style: none;
    background: transparent;
}

.breadcrumb-item {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.8);
}

.breadcrumb-item a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-item a:hover {
    color: white;
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: white;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    padding: 0 0.5rem;
    color: rgba(255, 255, 255, 0.6);
}

/* Row and Col */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -0.75rem;
}

.col,
.col-12,
.col-lg-6,
.col-auto {
    padding: 0 0.75rem;
    box-sizing: border-box;
}

.col-12 {
    width: 100%;
}

.col-lg-6 {
    width: 50%;
}

.col-auto {
    width: auto;
}

@media (max-width: 992px) {
    .col-lg-6 {
        width: 100%;
    }
}

/* Utilities */
.h-100 {
    height: 100%;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.mt-2 {
    margin-top: 0.5rem;
}

.mt-4 {
    margin-top: 1.5rem;
}

.mb-0 {
    margin-bottom: 0;
}

.me-1 {
    margin-right: 0.25rem;
}

.me-2 {
    margin-right: 0.5rem;
}

.px-5 {
    padding-left: 2rem;
    padding-right: 2rem;
}

.pb-5 {
    padding-bottom: 2rem;
}

.p-3 {
    padding: 1rem;
}

.p-4 {
    padding: 1.5rem;
}

.border {
    border: 1px solid var(--gray-200);
}

.border-start-0 {
    border-left: none !important;
}

.rounded {
    border-radius: 0.5rem;
}

.bg-light {
    background-color: var(--gray-100) !important;
}

.text-end {
    text-align: right;
}

.text-primary {
    color: var(--primary) !important;
}

.text-muted {
    color: var(--gray-500) !important;
}

.small {
    font-size: 0.75rem;
}

.fw-bold {
    font-weight: 600;
}

.d-flex {
    display: flex;
}

.align-items-center {
    align-items: center;
}

.justify-content-between {
    justify-content: space-between;
}

.justify-content-end {
    justify-content: flex-end;
}

.flex-wrap {
    flex-wrap: wrap;
}

.w-100 {
    width: 100%;
}

.shadow-sm {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* Responsive */
@media (max-width: 768px) {
    .commissions-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .px-5 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="commissions-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-percent"></i>
                Commissions & Frais
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Configuration</li>
                    <li class="breadcrumb-item active" aria-current="page">Commissions & Frais</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <button type="button" class="btn btn-warning" onclick="resetSettings()">
                <i class="fas fa-undo"></i>
                Réinitialiser par défaut
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('admin.commission-settings.update') }}" method="POST" id="settingsForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Paramètres de Commission -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-percent"></i>
                            Commissions Prestataires
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label for="mtn_commission_rate" class="form-label">Commission MTN Mobile Money</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="mtn_commission_rate" 
                                       id="mtn_commission_rate" 
                                       class="form-control" 
                                       value="{{ old('mtn_commission_rate', $settings['mtn_commission_rate'] ?? 1.5) }}" 
                                       min="0" 
                                       max="100" 
                                       step="0.01" 
                                       required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Commission prélevée par MTN sur chaque transaction.</div>
                            @error('mtn_commission_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="airtel_commission_rate" class="form-label">Commission Airtel Money</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="airtel_commission_rate" 
                                       id="airtel_commission_rate" 
                                       class="form-control" 
                                       value="{{ old('airtel_commission_rate', $settings['airtel_commission_rate'] ?? 1.5) }}" 
                                       min="0" 
                                       max="100" 
                                       step="0.01" 
                                       required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Commission prélevée par Airtel sur chaque transaction.</div>
                            @error('airtel_commission_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="mokilievent_commission_rate" class="form-label">Commission MokiliEvent</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="mokilievent_commission_rate" 
                                       id="mokilievent_commission_rate" 
                                       class="form-control" 
                                       value="{{ old('mokilievent_commission_rate', $settings['mokilievent_commission_rate'] ?? 5.0) }}" 
                                       min="0" 
                                       max="100" 
                                       step="0.01" 
                                       required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Frais de service MokiliEvent sur chaque transaction.</div>
                            @error('mokilievent_commission_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="callout">
                            <h6>
                                <i class="fas fa-info-circle"></i>
                                Note sur les calculs
                            </h6>
                            <p class="mb-0">
                                La somme de ces trois commissions sera déduite du montant total payé par le client avant le reversement à l'organisateur.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paramètres de Création d'Événements -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-coins"></i>
                            Frais de Création
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label for="free_event_creation_fee" class="form-label">Frais pour événement gratuit</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="free_event_creation_fee" 
                                       id="free_event_creation_fee" 
                                       class="form-control" 
                                       value="{{ old('free_event_creation_fee', $settings['free_event_creation_fee'] ?? 0) }}" 
                                       min="0" 
                                       step="100" 
                                       required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                            <div class="form-text">Montant forfaitaire pour la publication d'un événement gratuit.</div>
                            @error('free_event_creation_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="custom_event_creation_fee" class="form-label">Frais pour événement personnalisé</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="custom_event_creation_fee" 
                                       id="custom_event_creation_fee" 
                                       class="form-control" 
                                       value="{{ old('custom_event_creation_fee', $settings['custom_event_creation_fee'] ?? 5000) }}" 
                                       min="0" 
                                       step="100" 
                                       required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                            <div class="form-text">Frais appliqués aux événements demandant une gestion spécifique.</div>
                            @error('custom_event_creation_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <h6 class="fw-bold text-primary mb-3" style="font-size: 0.875rem;">
                            <i class="fas fa-toggle-on me-1"></i>
                            Activation des paiements requis
                        </h6>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   name="require_payment_for_free_events" 
                                   id="require_payment_for_free_events" 
                                   {{ old('require_payment_for_free_events', $settings['require_payment_for_free_events'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="require_payment_for_free_events">
                                Exiger le paiement pour les événements gratuits
                            </label>
                            <div class="form-text" style="margin-left: 2.5rem;">
                                Les organisateurs devront payer les frais avant publication.
                            </div>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   name="require_payment_for_custom_events" 
                                   id="require_payment_for_custom_events" 
                                   {{ old('require_payment_for_custom_events', $settings['require_payment_for_custom_events'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="require_payment_for_custom_events">
                                Exiger le paiement pour les événements personnalisés
                            </label>
                            <div class="form-text" style="margin-left: 2.5rem;">
                                Les organisateurs devront payer les frais avant création.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="row mt-4">
            <div class="col-12 text-end">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light me-2">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fas fa-save"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal de confirmation pour réinitialisation -->
<div class="modal fade" id="resetSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);">
                <h5 class="modal-title" style="color: white;">
                    <i class="fas fa-undo-alt"></i>
                    Réinitialiser les paramètres
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="font-size: 3rem; color: var(--warning); margin-bottom: 1rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h6 style="font-weight: 600; color: var(--gray-900); margin-bottom: 1rem;">
                    Réinitialiser tous les paramètres ?
                </h6>
                <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 0;">
                    Cette action est <strong style="color: var(--danger);">irréversible</strong>. 
                    Toutes les valeurs de commissions et frais seront remplacées par les valeurs par défaut.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Annuler
                </button>
                <a href="{{ route('admin.commission-settings.reset') }}" class="btn btn-warning" style="color: white;">
                    <i class="fas fa-undo me-1"></i>
                    Réinitialiser
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===========================================
    // RÉINITIALISATION AVEC MODAL DE CONFIRMATION
    // ===========================================
    window.resetSettings = function() {
        const modal = new bootstrap.Modal(document.getElementById('resetSettingsModal'));
        modal.show();
    };

    // ===========================================
    // VALIDATION EN TEMPS RÉEL
    // ===========================================
    const settingsForm = document.getElementById('settingsForm');
    
    if (settingsForm) {
        settingsForm.addEventListener('submit', function(e) {
            const mtnRate = parseFloat(document.getElementById('mtn_commission_rate').value) || 0;
            const airtelRate = parseFloat(document.getElementById('airtel_commission_rate').value) || 0;
            const mokiliRate = parseFloat(document.getElementById('mokilievent_commission_rate').value) || 0;
            const totalCommission = mtnRate + airtelRate + mokiliRate;
            
            // Alerte si commission totale trop élevée
            if (totalCommission > 30) {
                if (!confirm(`⚠️ Attention : La somme des commissions est de ${totalCommission.toFixed(1)}%. Cela dépasse 30%.\n\nVoulez-vous vraiment enregistrer ces taux très élevés ?`)) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Alerte si commission totale > 50%
            if (totalCommission > 50) {
                if (!confirm(`🚨 ALERTE : La somme des commissions est de ${totalCommission.toFixed(1)}%. Cela dépasse 50%.\n\nCela pourrait rendre votre plateforme trop coûteuse.\n\nConfirmez-vous quand même ?`)) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Désactiver le bouton de soumission
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
            }
        });
    }

    // ===========================================
    // VALIDATION DES CHAMPS NUMÉRIQUES
    // ===========================================
    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            // Empêcher la saisie de caractères non numériques
            if (!/[0-9.]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' && e.key !== 'Tab') {
                e.preventDefault();
            }
        });
        
        input.addEventListener('blur', function() {
            let value = parseFloat(this.value);
            const min = parseFloat(this.min) || 0;
            const max = parseFloat(this.max) || 100;
            
            if (isNaN(value)) {
                this.value = min;
            } else if (value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
            }
        });
    });

    // ===========================================
    // AUTO-FERMETURE DES ALERTES
    // ===========================================
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (!alert.classList.contains('alert-permanent')) {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 300);
            }
        });
    }, 5000);
});
</script>
@endpush