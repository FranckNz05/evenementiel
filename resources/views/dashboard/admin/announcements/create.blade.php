@extends('layouts.dashboard')

@section('title', 'Créer une annonce')

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

.announcement-page {
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

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #059669;
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

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

/* Card */
.card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-label i {
    margin-right: 0.375rem;
    color: var(--primary);
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

/* Form Check */
.form-check {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.form-check-input {
    width: 1.25rem;
    height: 1.25rem;
    background-color: white;
    border: 2px solid var(--gray-300);
    border-radius: 0.25rem;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
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

.form-check-label i {
    margin-right: 0.25rem;
}

/* Urgent Checkbox */
.form-check-label.text-danger {
    color: var(--danger) !important;
}

.form-check-input:checked + .form-check-label i {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Alert */
.alert {
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    border-left: 4px solid;
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

.alert i {
    font-size: 1rem;
}

.alert .btn-close {
    margin-left: auto;
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

.col,
.col-12,
.col-md-4,
.col-md-6 {
    padding: 0 0.75rem;
    box-sizing: border-box;
}

.col-12 {
    width: 100%;
}

.col-md-6 {
    width: 50%;
}

.col-md-4 {
    width: 33.333%;
}

@media (max-width: 768px) {
    .col-md-6,
    .col-md-4 {
        width: 100%;
    }
}

/* Utilities */
.mb-3 {
    margin-bottom: 1rem;
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
.text-muted {
    color: var(--gray-500) !important;
}

.text-danger {
    color: var(--danger) !important;
}

.small {
    font-size: 0.75rem;
}

.fw-bold {
    font-weight: 600;
}

/* D-grid */
.d-grid {
    display: grid;
}

.gap-2 {
    gap: 0.5rem;
}

.d-md-flex {
    display: flex;
}

@media (max-width: 768px) {
    .d-md-flex {
        display: grid;
    }
}

.justify-content-md-end {
    justify-content: flex-end;
}

/* Responsive */
@media (max-width: 768px) {
    .announcement-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .card-body {
        padding: 1.25rem;
    }
}

/* Flatpickr Customization */
.flatpickr-calendar {
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    font-family: inherit;
}

.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange {
    background: var(--primary);
    border-color: var(--primary);
}

.flatpickr-day.selected:hover,
.flatpickr-day.startRange:hover,
.flatpickr-day.endRange:hover {
    background: var(--primary-light);
    border-color: var(--primary-light);
}

.flatpickr-input[readonly] {
    background-color: white;
    cursor: pointer;
}
</style>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
@endpush

@section('content')
<div class="announcement-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-bullhorn"></i>
                Créer une annonce
            </h1>
            <p>Créez une nouvelle annonce pour communiquer avec les utilisateurs</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-bullhorn"></i>
                Nouvelle annonce
            </h5>
        </div>
        <div class="card-body">
            <!-- Notifications -->
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
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

            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf

                <!-- Titre -->
                <div class="form-group mb-4">
                    <label for="title" class="form-label">
                        <i class="fas fa-heading"></i>
                        Titre de l'annonce
                    </label>
                    <input type="text" 
                           class="form-control @error('title') is-invalid @enderror" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}" 
                           placeholder="Ex: Maintenance programmée, Nouvelle fonctionnalité..."
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Contenu -->
                <div class="form-group mb-4">
                    <label for="content" class="form-label">
                        <i class="fas fa-align-left"></i>
                        Contenu de l'annonce
                    </label>
                    <textarea class="form-control @error('content') is-invalid @enderror" 
                              id="content" 
                              name="content" 
                              rows="4" 
                              placeholder="Détaillez ici le message à communiquer..."
                              required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="fas fa-info-circle"></i>
                        Le contenu sera affiché dans la barre d'annonces sur la page d'accueil.
                    </div>
                </div>

                <!-- Dates -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="start_date" class="form-label">
                                <i class="fas fa-calendar-alt"></i>
                                Date de début
                            </label>
                            <input type="text" 
                                   class="form-control datepicker @error('start_date') is-invalid @enderror" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date') }}" 
                                   placeholder="jj/mm/aaaa">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Laissez vide pour une annonce sans date de début.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date" class="form-label">
                                <i class="fas fa-calendar-check"></i>
                                Date de fin
                            </label>
                            <input type="text" 
                                   class="form-control datepicker @error('end_date') is-invalid @enderror" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date') }}" 
                                   placeholder="jj/mm/aaaa">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Laissez vide pour une annonce sans date de fin.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="display_order" class="form-label">
                                <i class="fas fa-sort-numeric-down"></i>
                                Ordre d'affichage
                            </label>
                            <input type="number" 
                                   class="form-control @error('display_order') is-invalid @enderror" 
                                   id="display_order" 
                                   name="display_order" 
                                   value="{{ old('display_order', 0) }}" 
                                   min="0" 
                                   step="1">
                            @error('display_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Ordre croissant (0, 1, 2...). Plus le chiffre est petit, plus l'annonce est prioritaire.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="form-group h-100 d-flex flex-column justify-content-end">
                            <div class="form-check mt-4">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-toggle-on"></i>
                                    Activer l'annonce
                                </label>
                                <div class="form-text" style="margin-left: 2rem;">
                                    L'annonce sera visible immédiatement.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group h-100 d-flex flex-column justify-content-end">
                            <div class="form-check mt-4">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_urgent" 
                                       name="is_urgent" 
                                       value="1"
                                       {{ old('is_urgent') ? 'checked' : '' }}>
                                <label class="form-check-label text-danger fw-bold" for="is_urgent">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Urgent
                                </label>
                                <div class="form-text text-danger" style="margin-left: 2rem;">
                                    <i class="fas fa-bell"></i>
                                    Style rouge avec effet d'attention
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2 justify-content-end mt-5 pt-3 border-top">
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save"></i>
                        Créer l'annonce
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===========================================
    // INITIALISATION DU SÉLECTEUR DE DATE
    // ===========================================
    flatpickr(".datepicker", {
        locale: "fr",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        allowInput: true,
        disableMobile: true,
        minDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            // Validation : date de fin >= date de début
            const startDate = document.getElementById('start_date')._flatpickr;
            const endDate = document.getElementById('end_date')._flatpickr;
            
            if (startDate && endDate) {
                if (startDate.selectedDates[0] && endDate.selectedDates[0]) {
                    if (endDate.selectedDates[0] < startDate.selectedDates[0]) {
                        endDate.setDate(startDate.selectedDates[0]);
                    }
                }
            }
        }
    });

    // ===========================================
    // VALIDATION DES DATES
    // ===========================================
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            if (endDateInput.value && this.value) {
                if (endDateInput.value < this.value) {
                    endDateInput.value = this.value;
                }
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

    // ===========================================
    // CONFIRMATION POUR ANNULATION
    // ===========================================
    const cancelBtn = document.querySelector('a.btn-outline-secondary');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            const form = document.querySelector('form');
            const isDirty = Array.from(form.querySelectorAll('input, textarea')).some(
                input => input.value !== input.defaultValue
            );
            
            if (isDirty) {
                if (!confirm('Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter ?')) {
                    e.preventDefault();
                }
            }
        });
    }
});
</script>
@endpush