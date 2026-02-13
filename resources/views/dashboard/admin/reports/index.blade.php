@extends('layouts.dashboard')

@section('title', 'Rapports')

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

.reports-page {
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
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: rotate(45deg);
}

.page-title-section h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.5rem 0;
    position: relative;
    z-index: 1;
}

.page-title-section p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    position: relative;
    z-index: 1;
}

.page-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
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

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
}

.btn-outline-secondary {
    background: transparent;
    border: 1px solid var(--gray-300);
    color: var(--gray-700);
}

.btn-outline-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-800);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-4px);
}

.stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1.2;
}

.stat-value small {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-500);
    margin-left: 0.25rem;
}

.stat-card.primary .stat-value { color: var(--primary); }
.stat-card.success .stat-value { color: var(--success); }
.stat-card.info .stat-value { color: var(--info); }
.stat-card.warning .stat-value { color: var(--warning); }

/* Card */
.card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: all 0.2s;
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

/* Form */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}

.form-label i {
    color: var(--primary);
    width: 16px;
    text-align: center;
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

/* Custom Date Range */
.custom-date-range {
    background: var(--gray-50);
    padding: 1.25rem;
    border-radius: 0.5rem;
    margin-top: 0.5rem;
    border: 2px dashed var(--gray-300);
    display: none;
}

.custom-date-range.show {
    display: flex;
}

/* Info Card */
.info-card {
    background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);
    color: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.info-card:last-child {
    margin-bottom: 0;
}

.info-card h6 {
    margin: 0 0 1rem 0;
    font-weight: 600;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-card ul {
    margin: 0;
    padding-left: 1.5rem;
}

.info-card li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
    font-size: 0.875rem;
}

.info-card li strong {
    font-weight: 700;
}

/* Alert */
.alert {
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    font-size: 0.875rem;
    border-left: 4px solid;
    position: relative;
}

.alert-success {
    background: #d1fae5;
    border-left-color: #059669;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    border-left-color: #dc2626;
    color: #991b1b;
}

.alert-info {
    background: #dbeafe;
    border-left-color: #2563eb;
    color: #1e40af;
}

.alert i {
    font-size: 1rem;
    flex-shrink: 0;
    margin-top: 2px;
}

.alert ul {
    margin: 0.5rem 0 0 0;
    padding-left: 1.25rem;
}

.alert .btn-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: transparent;
    border: none;
    color: currentColor;
    opacity: 0.7;
    cursor: pointer;
    padding: 0.25rem;
}

.alert .btn-close:hover {
    opacity: 1;
}

/* Row and Col */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -0.75rem;
}

.col-lg-8,
.col-lg-4,
.col-md-6 {
    padding: 0 0.75rem;
    box-sizing: border-box;
}

.col-lg-8 {
    width: 66.666%;
}

.col-lg-4 {
    width: 33.333%;
}

.col-md-6 {
    width: 50%;
}

@media (max-width: 992px) {
    .col-lg-8,
    .col-lg-4 {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .col-md-6 {
        width: 100%;
    }
}

/* Gap utilities */
.gap-2 {
    gap: 0.5rem;
}

.gap-3 {
    gap: 1rem;
}

.mb-3 {
    margin-bottom: 1rem;
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

.me-1 {
    margin-right: 0.25rem;
}

.me-2 {
    margin-right: 0.5rem;
}

.ms-auto {
    margin-left: auto;
}

.p-3 {
    padding: 1rem;
}

/* Flex utilities */
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

/* Text utilities */
.text-center {
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .reports-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
    
    .custom-date-range.show {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="reports-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-chart-bar"></i>
                Rapports et exports
            </h1>
            <p>Générez et exportez des rapports détaillés sur les activités de la plateforme</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <div>
            <strong>Succès !</strong>
            <p class="mb-0">{{ session('success') }}</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            <strong>Erreur !</strong>
            <p class="mb-0">{{ session('error') }}</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <strong>Erreur de validation</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Statistiques rapides -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Utilisateurs</div>
            <div class="stat-value">{{ number_format($stats['users_count'] ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Événements</div>
            <div class="stat-value">{{ number_format($stats['events_count'] ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Réservations</div>
            <div class="stat-value">{{ number_format($stats['orders_count'] ?? 0, 0, ',', ' ') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Paiements</div>
            <div class="stat-value">{{ number_format($stats['payments_count'] ?? 0, 0, ',', ' ') }}</div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="row">
        <!-- Formulaire de génération de rapport -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-file-export"></i>
                        Générer un rapport
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.generate') }}" method="POST" id="report-form">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="report_type" class="form-label">
                                    <i class="fas fa-list"></i>
                                    Type de rapport
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
                                    <i class="fas fa-file"></i>
                                    Format d'export
                                </label>
                                <select class="form-select @error('format') is-invalid @enderror" 
                                        id="format" 
                                        name="format" 
                                        required>
                                    <option value="pdf" {{ old('format', 'pdf') == 'pdf' ? 'selected' : '' }}>
                                        PDF - Document portable
                                    </option>
                                </select>
                                @error('format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="date_range" class="form-label">
                                <i class="fas fa-calendar"></i>
                                Période
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

                        <div class="custom-date-range {{ old('date_range') == 'custom' ? 'show' : '' }}" id="customDateRange">
                            <div class="row w-100">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="start_date" class="form-label">
                                        <i class="fas fa-calendar-alt"></i>
                                        Date de début
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date"
                                           value="{{ old('start_date') }}"
                                           min="2020-01-01"
                                           max="{{ date('Y-m-d') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">
                                        <i class="fas fa-calendar-check"></i>
                                        Date de fin
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" 
                                           name="end_date"
                                           value="{{ old('end_date') }}"
                                           min="2020-01-01"
                                           max="{{ date('Y-m-d') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <button type="submit" class="btn btn-success" id="submit-btn">
                                <i class="fas fa-file-export"></i>
                                Générer le rapport
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations sur les rapports -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        À propos des rapports
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-card">
                        <h6>
                            <i class="fas fa-list-ul"></i>
                            Types de rapports disponibles
                        </h6>
                        <ul>
                            <li><strong>Événements</strong> - Liste complète des événements avec leurs détails</li>
                            <li><strong>Utilisateurs</strong> - Liste des utilisateurs inscrits sur la plateforme</li>
                            <li><strong>Réservations</strong> - Historique complet des réservations</li>
                            <li><strong>Paiements</strong> - Historique détaillé des transactions</li>
                        </ul>
                    </div>

                    <div class="info-card" style="background: linear-gradient(135deg, var(--success) 0%, #059669 100%);">
                        <h6>
                            <i class="fas fa-file-export"></i>
                            Format d'export
                        </h6>
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
    console.log('🚀 Page Rapports initialisée');

    // ===========================================
    // GESTION DES DATES PERSONNALISÉES
    // ===========================================
    const dateRangeSelect = document.getElementById('date_range');
    const customDateRange = document.getElementById('customDateRange');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const form = document.getElementById('report-form');
    const submitBtn = document.getElementById('submit-btn');

    function toggleCustomDateFields() {
        if (dateRangeSelect.value === 'custom') {
            customDateRange.classList.add('show');
            startDateInput.setAttribute('required', 'required');
            endDateInput.setAttribute('required', 'required');
            
            // Définir la date minimale pour end_date basée sur start_date
            if (startDateInput.value) {
                endDateInput.setAttribute('min', startDateInput.value);
            }
        } else {
            customDateRange.classList.remove('show');
            startDateInput.removeAttribute('required');
            endDateInput.removeAttribute('required');
            startDateInput.value = '';
            endDateInput.value = '';
            endDateInput.removeAttribute('min');
        }
    }

    // Initialiser l'état
    toggleCustomDateFields();

    // Écouter les changements
    if (dateRangeSelect) {
        dateRangeSelect.addEventListener('change', toggleCustomDateFields);
    }

    // Validation des dates
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            if (this.value) {
                const today = new Date().toISOString().split('T')[0];
                this.setAttribute('max', today);
                endDateInput.setAttribute('min', this.value);
                
                if (endDateInput.value && endDateInput.value < this.value) {
                    endDateInput.value = this.value;
                }
            }
        });
    }

    if (endDateInput) {
        endDateInput.addEventListener('change', function() {
            if (this.value) {
                const today = new Date().toISOString().split('T')[0];
                this.setAttribute('max', today);
                
                if (startDateInput.value && this.value < startDateInput.value) {
                    this.setCustomValidity('La date de fin doit être supérieure ou égale à la date de début');
                } else {
                    this.setCustomValidity('');
                }
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // ===========================================
    // GESTION DE LA SOUMISSION DU FORMULAIRE
    // ===========================================
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validation supplémentaire pour les dates personnalisées
            if (dateRangeSelect.value === 'custom') {
                if (!startDateInput.value || !endDateInput.value) {
                    e.preventDefault();
                    alert('Veuillez sélectionner les dates de début et de fin');
                    return;
                }
                
                if (endDateInput.value < startDateInput.value) {
                    e.preventDefault();
                    alert('La date de fin doit être supérieure à la date de début');
                    return;
                }
            }

            // Afficher l'état de chargement
            if (submitBtn) {
                const originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération en cours...';
                submitBtn.disabled = true;
                
                // Réactiver après 30 secondes si problème
                setTimeout(function() {
                    submitBtn.innerHTML = originalHtml;
                    submitBtn.disabled = false;
                }, 30000);
            }
        });
    }

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

    console.log('✅ Gestionnaires d\'événements attachés');
});
</script>
@endpush