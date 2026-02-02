@extends('layouts.dashboard')

@section('title', 'Rapports')

@push('styles')
<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffd700;
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --success: #10b981;
    --danger: #ef4444;
    --info: #3b82f6;
    --border-radius: 0.75rem;
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --transition: all 0.3s ease;
}

.dashboard-section {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    min-height: 100vh;
    padding: 2rem 1.5rem;
}

.dashboard-head {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
    color: white;
    position: relative;
    overflow: hidden;
}

.dashboard-head::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: var(--blanc-or);
    opacity: 0.1;
    border-radius: 50%;
    transform: rotate(45deg);
}

.dashboard-head h1 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    position: relative;
    z-index: 1;
}

.dashboard-head p {
    margin: 0;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}

.dashboard-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 16px;
    box-shadow: var(--shadow-lg);
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: var(--transition);
}

.dashboard-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-2px);
}

.dashboard-card__header {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    padding: 1.5rem;
    border-bottom: 2px solid var(--bleu-nuit);
}

.dashboard-card__title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--bleu-nuit);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.dashboard-card__body {
    padding: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--bleu-nuit);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-control, .form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: var(--transition);
    background: var(--white);
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: var(--bleu-nuit);
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: var(--danger);
}

.invalid-feedback {
    display: block;
    color: var(--danger);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.custom-date-range {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: var(--border-radius);
    margin-top: 1rem;
    border: 2px dashed var(--gray-200);
}

.dashboard-btn {
    border: none;
    border-radius: 999px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
    font-size: 0.875rem;
}

.dashboard-btn.primary {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--blanc-or);
}

.dashboard-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(15, 26, 61, 0.3);
    color: var(--blanc-or);
}

.dashboard-btn.secondary {
    background: var(--gray-100);
    color: var(--gray-700);
}

.dashboard-btn.secondary:hover {
    background: var(--gray-200);
    color: var(--gray-800);
}

.dashboard-btn.success {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    color: white;
}

.dashboard-btn.success:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
}

.info-card {
    background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);
    color: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.info-card h6 {
    margin: 0 0 1rem 0;
    font-weight: 600;
    font-size: 1rem;
}

.info-card ul {
    margin: 0;
    padding-left: 1.5rem;
}

.info-card li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    text-align: center;
    box-shadow: var(--shadow);
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-card__value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin: 0.5rem 0;
}

.stat-card__label {
    font-size: 0.875rem;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.alert {
    border-radius: var(--border-radius);
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    border: none;
    box-shadow: var(--shadow);
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
}

.alert-info {
    background: #dbeafe;
    color: #1e40af;
}

.alert-dismissible .close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 1rem 1.5rem;
    color: inherit;
    opacity: 0.5;
    cursor: pointer;
}

.alert-dismissible .close:hover {
    opacity: 1;
}

@media (max-width: 768px) {
    .dashboard-section {
        padding: 1rem;
    }
    
    .dashboard-head {
        padding: 1.5rem;
    }
    
    .dashboard-head h1 {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .custom-date-range {
        flex-direction: column;
    }
}
</style>
@endpush

@section('content')
<div class="dashboard-section">
    <div class="dashboard-head">
        <h1>
            <i class="fas fa-chart-bar me-2"></i>
            Rapports et Exports
        </h1>
        <p>Générez et exportez des rapports détaillés sur les activités de la plateforme</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Erreur :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistiques rapides -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card__label">Utilisateurs</div>
            <div class="stat-card__value">{{ number_format($stats['users_count'] ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__label">Événements</div>
            <div class="stat-card__value">{{ number_format($stats['events_count'] ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__label">Réservations</div>
            <div class="stat-card__value">{{ number_format($stats['orders_count'] ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__label">Paiements</div>
            <div class="stat-card__value">{{ number_format($stats['payments_count'] ?? 0) }}</div>
        </div>
    </div>

    <div class="row">
        <!-- Formulaire de génération de rapport -->
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="dashboard-card__header">
                    <h5 class="dashboard-card__title">
                        <i class="fas fa-file-export"></i>
                        Générer un rapport
                    </h5>
                </div>
                <div class="dashboard-card__body">
                    <form action="{{ route('admin.reports.generate') }}" method="POST" id="report-form">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="report_type" class="form-label">
                                    <i class="fas fa-list me-1"></i> Type de rapport
                                </label>
                                <select class="form-select @error('report_type') is-invalid @enderror" 
                                        id="report_type" 
                                        name="report_type" 
                                        required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="events" {{ old('report_type') == 'events' ? 'selected' : '' }}>Événements</option>
                                    <option value="users" {{ old('report_type') == 'users' ? 'selected' : '' }}>Utilisateurs</option>
                                    <option value="orders" {{ old('report_type') == 'orders' ? 'selected' : '' }}>Réservations</option>
                                    <option value="payments" {{ old('report_type') == 'payments' ? 'selected' : '' }}>Paiements</option>
                                </select>
                                @error('report_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="format" class="form-label">
                                    <i class="fas fa-file me-1"></i> Format d'export
                                </label>
                                <select class="form-select @error('format') is-invalid @enderror" 
                                        id="format" 
                                        name="format" 
                                        required>
                                    <option value="pdf" {{ old('format', 'pdf') == 'pdf' ? 'selected' : '' }}>
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </option>
                                </select>
                                @error('format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="date_range" class="form-label">
                                <i class="fas fa-calendar me-1"></i> Période
                            </label>
                            <select class="form-select @error('date_range') is-invalid @enderror" 
                                    id="date_range" 
                                    name="date_range" 
                                    required>
                                <option value="">Sélectionner une période</option>
                                <option value="today" {{ old('date_range') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                                <option value="yesterday" {{ old('date_range') == 'yesterday' ? 'selected' : '' }}>Hier</option>
                                <option value="this_week" {{ old('date_range') == 'this_week' ? 'selected' : '' }}>Cette semaine</option>
                                <option value="last_week" {{ old('date_range') == 'last_week' ? 'selected' : '' }}>Semaine dernière</option>
                                <option value="this_month" {{ old('date_range') == 'this_month' ? 'selected' : '' }}>Ce mois</option>
                                <option value="last_month" {{ old('date_range') == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                                <option value="this_year" {{ old('date_range') == 'this_year' ? 'selected' : '' }}>Cette année</option>
                                <option value="custom" {{ old('date_range') == 'custom' ? 'selected' : '' }}>Période personnalisée</option>
                            </select>
                            @error('date_range')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row custom-date-range" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i> Date de début
                                </label>
                                <input type="date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" 
                                       name="start_date"
                                       value="{{ old('start_date') }}"
                                       min="2020-01-01">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">
                                    <i class="fas fa-calendar-check me-1"></i> Date de fin
                                </label>
                                <input type="date" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" 
                                       name="end_date"
                                       value="{{ old('end_date') }}"
                                       min="2020-01-01">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('admin.dashboard') }}" class="dashboard-btn secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="dashboard-btn success" id="submit-btn">
                                <i class="fas fa-file-export"></i> Générer le rapport
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations sur les rapports -->
        <div class="col-lg-4">
            <div class="dashboard-card">
                <div class="dashboard-card__header">
                    <h5 class="dashboard-card__title">
                        <i class="fas fa-info-circle"></i>
                        À propos des rapports
                    </h5>
                </div>
                <div class="dashboard-card__body">
                    <div class="info-card">
                        <h6><i class="fas fa-list-ul me-2"></i>Types de rapports disponibles :</h6>
                        <ul>
                            <li><strong>Événements</strong> - Liste complète des événements avec leurs détails</li>
                            <li><strong>Utilisateurs</strong> - Liste des utilisateurs inscrits sur la plateforme</li>
                            <li><strong>Réservations</strong> - Historique complet des réservations</li>
                            <li><strong>Paiements</strong> - Historique détaillé des transactions</li>
                        </ul>
                    </div>

                    <div class="info-card" style="background: linear-gradient(135deg, var(--success) 0%, #059669 100%);">
                        <h6><i class="fas fa-file-export me-2"></i>Format d'export :</h6>
                        <ul>
                            <li><strong>PDF</strong> - Document formaté pour impression et archivage avec logo et mise en page professionnelle</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRangeSelect = document.getElementById('date_range');
    const customDateRange = document.querySelector('.custom-date-range');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const form = document.getElementById('report-form');
    const submitBtn = document.getElementById('submit-btn');

    // Afficher/masquer les champs de date personnalisée
    function toggleCustomDateFields() {
        if (dateRangeSelect.value === 'custom') {
            customDateRange.style.display = 'flex';
            startDateInput.setAttribute('required', 'required');
            endDateInput.setAttribute('required', 'required');
            // Définir la date minimale pour end_date basée sur start_date
            if (startDateInput.value) {
                endDateInput.setAttribute('min', startDateInput.value);
            }
        } else {
            customDateRange.style.display = 'none';
            startDateInput.removeAttribute('required');
            endDateInput.removeAttribute('required');
            // Vider les champs pour éviter les problèmes de validation
            startDateInput.value = '';
            endDateInput.value = '';
            endDateInput.removeAttribute('min');
        }
    }

    // Initialiser l'état au chargement
    toggleCustomDateFields();

    // Écouter les changements
    dateRangeSelect.addEventListener('change', toggleCustomDateFields);

    // Validation de la date de fin
    startDateInput.addEventListener('change', function() {
        if (this.value) {
            // Mettre à jour la date minimale pour end_date
            endDateInput.setAttribute('min', this.value);
            // Si end_date est déjà définie et est antérieure à start_date, la réinitialiser
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
        }
    });

    endDateInput.addEventListener('change', function() {
        if (startDateInput.value && this.value) {
            if (this.value < startDateInput.value) {
                this.setCustomValidity('La date de fin doit être supérieure ou égale à la date de début');
            } else {
                this.setCustomValidity('');
            }
        } else {
            this.setCustomValidity('');
        }
    });

    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        // Afficher un message de chargement
        if (submitBtn) {
            const originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération en cours...';
            submitBtn.disabled = true;
            
            // Réactiver le bouton après 30 secondes au cas où il y aurait un problème
            setTimeout(function() {
                submitBtn.innerHTML = originalHtml;
                submitBtn.disabled = false;
            }, 30000);
        }
    });

    // Auto-fermeture des alertes après 5 secondes
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush
