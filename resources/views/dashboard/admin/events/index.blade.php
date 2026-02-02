@extends('layouts.dashboard')

@section('title', 'Gestion des événements')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-pagination.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
/* ===============================================
   SYSTÈME DE DESIGN ÉPURÉ & MODERNE
   Design minimaliste avec touches subtiles
   =============================================== */

:root {
    /* Palette principale - tons apaisants */
    --primary-dark: #1e293b;
    --primary-medium: #334155;
    --primary-light: #475569;
    
    /* Couleurs d'état - douces */
    --success: #10b981;
    --success-bg: #d1fae5;
    --warning: #1a237e;
    --warning-bg: #e8eaf6;
    --danger: #ef4444;
    --danger-bg: #fee2e2;
    --info: #3b82f6;
    --info-bg: #dbeafe;
    
    /* Nuances neutres */
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
    
    /* Ombres subtiles */
    --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.08);
    --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.1);
    
    /* Bordures */
    --radius-sm: 6px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
    --radius-full: 9999px;
    
    /* Transitions fluides */
    --transition: 200ms cubic-bezier(0.4, 0, 0.2, 1);
    
    /* Espacements cohérents */
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    
    /* Couleurs spécifiques pour la cohérence */
    --primary: #0f1a3d;
    --secondary: #6c757d;
}

/* ===============================================
   LAYOUT PRINCIPAL
   =============================================== */

.container-fluid {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
    background: var(--gray-50);
    min-height: 100vh;
}

@media (min-width: 768px) {
    .container-fluid {
        padding: 2rem;
    }
}

/* ===============================================
   STATISTIQUES - Design épuré
   =============================================== */

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
    transition: all var(--transition);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
}

.stat-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.stat-info {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-dark);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--gray-600);
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: capitalize;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    background: var(--info-bg);
    color: var(--primary);
    transition: all var(--transition);
}

.stat-card:hover .stat-icon {
    background: var(--primary);
    color: var(--white);
    transform: scale(1.05);
}

/* ===============================================
   CARDS MODERNES
   =============================================== */

.modern-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.card-header-modern {
    background: linear-gradient(135deg, var(--primary) 0%, #1a237e 100%) !important;
    padding: var(--spacing-lg);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--spacing-md);
    flex-wrap: wrap;
    border-bottom: 3px solid var(--white);
}

.card-header-modern .card-title {
    font-size: clamp(0.9375rem, 2.5vw, 1.125rem);
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    color: var(--white) !important;
}

.card-header-modern .card-title i,
.card-header-modern .card-title i.fas,
.card-header-modern .card-title i.far,
.card-header-modern .card-title i.fal,
.card-header-modern i,
.card-header-modern i.fas,
.card-header-modern i.far,
.card-header-modern i.fal {
    color: var(--white) !important;
    font-size: clamp(0.875rem, 2.25vw, 1rem);
    filter: drop-shadow(0 2px 4px rgba(255, 255, 255, 0.3));
}

.card-body-modern {
    padding: 0;
    background: var(--white);
}

/* ===============================================
   TABLEAUX ÉPURÉS
   =============================================== */

.table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.modern-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.875rem;
    min-width: 800px;
    table-layout: auto;
}

.modern-table thead th {
    background: var(--gray-50);
    color: var(--gray-700);
    font-weight: 600;
    font-size: 0.8125rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    padding: 1rem 1.25rem;
    text-align: left;
    border-bottom: 2px solid var(--gray-200);
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 10;
}

.modern-table thead th[style*="text-align: center"] {
    text-align: center;
}

.modern-table tbody tr {
    transition: background var(--transition);
    border-bottom: 1px solid var(--gray-100);
}

.modern-table tbody tr:hover {
    background: var(--gray-50);
}

.modern-table td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
    color: var(--gray-700);
}

.modern-table td[style*="text-align: center"] {
    text-align: center;
}

/* ===============================================
   CONTENU CELLULES
   =============================================== */

.event-title {
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.event-meta {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.event-date {
    font-weight: 600;
    color: var(--primary-dark);
    font-size: 0.875rem;
}

.event-time {
    color: var(--gray-500);
    font-size: 0.8125rem;
}

.event-location {
    max-width: 140px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.8125rem;
    color: var(--gray-600);
}

/* ===============================================
   BADGES MINIMALISTES
   =============================================== */

.modern-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
    line-height: 1.5;
    border: 1px solid transparent;
}

.badge-success {
    background: var(--success-bg);
    color: #065f46;
    border-color: var(--success);
}

.badge-warning {
    background: var(--warning-bg);
    color: #92400e;
    border-color: var(--warning);
}

.badge-danger {
    background: var(--danger-bg);
    color: #991b1b;
    border-color: var(--danger);
}

.badge-info {
    background: var(--info-bg);
    color: #1e40af;
    border-color: var(--info);
}

.badge-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
    border-color: var(--gray-300);
}

/* ===============================================
   BOUTONS ÉPURÉS
   =============================================== */

.modern-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    font-weight: 500;
    font-size: 0.875rem;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all var(--transition);
    text-decoration: none;
    white-space: nowrap;
}

.modern-btn:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}

.modern-btn:active {
    transform: translateY(0);
}

.btn-primary-modern {
    background: var(--primary-dark);
    color: var(--white);
    border-color: var(--primary-dark);
}

.btn-primary-modern:hover {
    background: var(--primary-medium);
}

.btn-success-modern {
    background: var(--success);
    color: var(--white);
    border-color: var(--success);
}

.btn-success-modern:hover {
    background: #059669;
}

.btn-warning-modern {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary);
}

.btn-warning-modern:hover {
    background: #1a237e;
}

.btn-danger-modern {
    background: var(--danger);
    color: var(--white);
    border-color: var(--danger);
}

.btn-danger-modern:hover {
    background: #dc2626;
}

.btn-info-modern {
    background: var(--info);
    color: var(--white);
    border-color: var(--info);
}

.btn-info-modern:hover {
    background: #2563eb;
}

.btn-secondary-modern {
    background: var(--white);
    color: var(--gray-700);
    border-color: var(--gray-300);
}

.btn-secondary-modern:hover {
    background: var(--gray-50);
}

/* BOUTONS ICÔNES UNIFORMISÉS */
.btn-icon-modern,
.btn-info-modern,
.btn-warning-modern, 
.btn-success-modern,
.btn-danger-modern {
    background: var(--primary) !important;
    color: var(--white) !important;
    border-color: var(--primary) !important;
}

.btn-icon-modern:hover,
.btn-info-modern:hover,
.btn-warning-modern:hover,
.btn-success-modern:hover,
.btn-danger-modern:hover {
    background: #1a237e !important;
    color: var(--white) !important;
    border-color: #1a237e !important;
}

/* Icônes toujours blanches - FORCE absolue */
.btn-icon-modern,
.btn-icon-modern *,
.btn-icon-modern i,
.btn-icon-modern i.fas,
.btn-icon-modern i.far,
.btn-icon-modern i.fal,
.btn-icon-modern svg,
.btn-info-modern,
.btn-info-modern *,
.btn-info-modern i,
.btn-info-modern i.fas,
.btn-info-modern svg,
.btn-warning-modern,
.btn-warning-modern *,
.btn-warning-modern i,
.btn-warning-modern i.fas,
.btn-warning-modern svg,
.btn-success-modern,
.btn-success-modern *,
.btn-success-modern i,
.btn-success-modern i.fas,
.btn-success-modern svg,
.btn-danger-modern,
.btn-danger-modern *,
.btn-danger-modern i,
.btn-danger-modern i.fas,
.btn-danger-modern svg {
    color: var(--white) !important;
    fill: var(--white) !important;
}

/* Icônes sur fond bleu - FORCE blanche */
.modern-table .btn-icon-modern i,
.modern-table .btn-icon-modern i.fas,
.modern-table .btn-icon-modern i.far,
.modern-table .btn-icon-modern i.fal,
.modern-table .btn-info-modern i,
.modern-table .btn-warning-modern i,
.modern-table .btn-success-modern i,
.modern-table .btn-danger-modern i {
    color: var(--white) !important;
}

/* Hover - icônes restent blanches */
.btn-icon-modern:hover i,
.btn-icon-modern:hover i.fas,
.btn-info-modern:hover i,
.btn-info-modern:hover i.fas,
.btn-warning-modern:hover i,
.btn-warning-modern:hover i.fas,
.btn-success-modern:hover i,
.btn-success-modern:hover i.fas,
.btn-danger-modern:hover i,
.btn-danger-modern:hover i.fas {
    color: var(--white) !important;
    fill: var(--white) !important;
}

.btn-group-modern {
    display: flex;
    gap: var(--spacing-xs);
    align-items: center;
    flex-wrap: nowrap;
    justify-content: center;
}

.btn-sm-modern {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

.btn-icon-modern {
    width: clamp(36px, 7vw, 40px);
    height: clamp(36px, 7vw, 40px);
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-md);
    font-size: clamp(0.875rem, 1.75vw, 0.9375rem);
    transition: all var(--transition);
    flex-shrink: 0;
    border: 2px solid transparent;
    cursor: pointer;
    text-decoration: none;
}

.btn-icon-modern:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ===============================================
   ALERTES SUBTILES
   =============================================== */

.modern-alert {
    padding: 0.875rem 1rem;
    border-radius: var(--radius-md);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    border-left: 3px solid;
}

.alert-success-modern {
    background: var(--success-bg);
    color: #065f46;
    border-left-color: var(--success);
}

.alert-danger-modern {
    background: var(--danger-bg);
    color: #991b1b;
    border-left-color: var(--danger);
}

.alert-warning-modern {
    background: var(--warning-bg);
    color: #92400e;
    border-left-color: var(--warning);
}

/* ===============================================
   MODALS ÉPURÉS
   =============================================== */

.modern-modal .modal-content {
    border: none;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
}

.modern-modal .modal-header {
    background: var(--white);
    border-bottom: 1px solid var(--gray-200);
    padding: 1.25rem 1.5rem;
}

.modern-modal .modal-title {
    color: var(--primary-dark);
    font-weight: 600;
}

.modern-modal .modal-title i {
    color: var(--primary);
}

.modern-modal .modal-body {
    padding: 1.5rem;
    background: var(--white);
}

.modern-modal .modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
}

.event-type-card {
    transition: all var(--transition);
    cursor: pointer;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
}

.event-type-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary);
}

/* ===============================================
   FORMS ÉPURÉS
   =============================================== */

.modern-form-control {
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    transition: all var(--transition);
    background: var(--white);
    color: var(--gray-700);
}

.modern-form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
    outline: none;
}

.modern-form-control.is-valid {
    border-color: var(--success);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.modern-form-control.is-invalid {
    border-color: var(--danger);
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

/* ===============================================
   PAGINATION BLEUE
   =============================================== */

.pagination-container {
    margin-top: var(--spacing-lg);
    padding: var(--spacing-md) 0;
    display: flex;
    justify-content: center;
}

.pagination {
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.25rem;
}

.pagination .page-item {
    margin: 0;
}

.pagination .page-link {
    color: var(--white);
    background-color: var(--primary);
    border: 1px solid var(--primary);
    border-radius: var(--radius-md);
    padding: 0.5rem 0.9rem;
    transition: var(--transition);
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    font-weight: 500;
}

.pagination .page-link:hover {
    background-color: #1a237e;
    color: var(--white);
    border-color: #1a237e;
}

.pagination .page-item.active .page-link {
    background-color: #1a237e;
    border-color: #1a237e;
    color: var(--white);
}

.pagination .page-item.disabled .page-link {
    background-color: var(--gray-400);
    color: var(--white);
    border-color: var(--gray-400);
    pointer-events: none;
    cursor: not-allowed;
    opacity: 0.6;
}

/* ===============================================
   EMPTY STATES
   =============================================== */

.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    color: var(--gray-500);
}

.empty-state-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--gray-300);
}

.empty-state-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}

.empty-state-description {
    font-size: 0.875rem;
    max-width: 400px;
    margin: 0 auto;
    color: var(--gray-500);
}

/* ===============================================
   ANIMATIONS SUBTILES
   =============================================== */

.fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.loading-state {
    position: relative;
    pointer-events: none;
    opacity: 0.7;
}

.spinner {
    width: 14px;
    height: 14px;
    border: 2px solid var(--gray-300);
    border-top: 2px solid var(--accent-gold);
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ===============================================
   RESPONSIVE COHÉRENT
   =============================================== */

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .modern-table {
        font-size: 0.8125rem;
        min-width: 600px;
    }
    
    .card-header-modern {
        flex-direction: column;
        align-items: stretch;
        gap: var(--spacing-sm);
    }
    
    .btn-group-modern {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .btn-group-modern .modern-btn {
        padding: 0.375rem 0.625rem;
        font-size: 0.8125rem;
    }
    
    /* Masquage cohérent des colonnes */
    .hide-sm,
    .hide-md,
    .hide-lg,
    .hide-xl {
        display: none !important;
    }
}

@media (max-width: 640px) {
    .stat-card {
        padding: 1.25rem;
    }
    
    .stat-number {
        font-size: 1.75rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 1.125rem;
    }
    
    .btn-group-modern {
        gap: 0.25rem;
    }
    
    .btn-icon-modern {
        width: 35px;
        height: 35px;
        font-size: 0.8rem;
    }
}

/* Affichage conditionnel pour les écrans larges */
@media (min-width: 769px) {
    .hide-md { display: table-cell !important; }
}

@media (min-width: 1024px) {
    .hide-lg { display: table-cell !important; }
}

@media (min-width: 1280px) {
    .hide-xl { display: table-cell !important; }
}

/* ===============================================
   ACCESSIBILITÉ
   =============================================== */

*:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

::selection {
    background: var(--info-bg);
    color: var(--gray-800);
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* ===============================================
   SCROLLBAR ÉPURÉE
   =============================================== */

* {
    scrollbar-width: thin;
    scrollbar-color: var(--gray-300) var(--gray-100);
}

*::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

*::-webkit-scrollbar-track {
    background: var(--gray-100);
}

*::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: var(--radius-full);
}

*::-webkit-scrollbar-thumb:hover {
    background: var(--gray-400);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card fade-in">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $events->total() }}</div>
                    <div class="stat-label">Événements publics</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
        <div class="stat-card fade-in" style="animation-delay: 0.1s;">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $customEvents->total() }}</div>
                    <div class="stat-label">Événements personnalisés</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-lock"></i>
                </div>
            </div>
        </div>
        <div class="stat-card fade-in" style="animation-delay: 0.2s;">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $pendingEvents->total() }}</div>
                    <div class="stat-label">En attente validation</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="modern-alert alert-success-modern fade-in">
            <i class="fas fa-check-circle"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="modern-alert alert-danger-modern fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Événements publics -->
    <div class="modern-card fade-in" style="animation-delay: 0.1s;">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-calendar-alt"></i>
                Événements publics ({{ $events->total() }})
            </h5>
            <button type="button" class="modern-btn btn-primary-modern btn-sm-modern" data-bs-toggle="modal" data-bs-target="#chooseEventTypeModal">
                <i class="fas fa-plus"></i>
                Nouvel événement
            </button>
        </div>
        <div class="card-body-modern">
            <div class="p-3">
                <form method="GET" action="{{ route('admin.events.index') }}" class="row g-3 align-items-end">
                    <div class="col-12 col-md-4 col-lg-4">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="modern-form-control" placeholder="Titre, description ou ville">
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label">Catégorie</label>
                        <select name="category" class="modern-form-control">
                            <option value="">Toutes</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label">Statut</label>
                        <select name="status" class="modern-form-control">
                            <option value="">Tous</option>
                            <option value="En cours" {{ request('status') == 'En cours' ? 'selected' : '' }}>En cours</option>
                            <option value="En attente" {{ request('status') == 'En attente' ? 'selected' : '' }}>En attente</option>
                            <option value="Archivé" {{ request('status') == 'Archivé' ? 'selected' : '' }}>Archivé</option>
                            <option value="Annulé" {{ request('status') == 'Annulé' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 col-lg-2 d-flex gap-2">
                        <button type="submit" class="modern-btn btn-primary-modern w-100">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        @if(request()->hasAny(['search', 'category', 'status']))
                            <a href="{{ route('admin.events.index') }}" class="modern-btn btn-secondary-modern">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th class="hide-lg">Catégorie</th>
                            <th class="hide-xl">Organisateur</th>
                            <th>Date</th>
                            <th class="hide-sm" style="padding-left: 1.5rem;">Statut</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr data-event-id="{{ $event->id }}">
                                <td>
                                    <div class="event-title">{{ $event->title }}</div>
                                    <div class="d-lg-none mt-1">
                                        <span class="modern-badge badge-info me-1">{{ Str::limit($event->category->name ?? 'N/A', 15) }}</span>
                                        <div class="d-xl-none">
                                            <small class="text-muted">{{ Str::limit($event->organizer->company_name ?? 'N/A', 20) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-lg">
                                    <span class="modern-badge badge-info">{{ Str::limit($event->category->name ?? 'N/A', 12) }}</span>
                                </td>
                                <td class="hide-xl">
                                    <div class="text-truncate" style="max-width: 100px;" title="{{ $event->organizer->company_name ?? 'Non défini' }}">
                                        {{ Str::limit($event->organizer->company_name ?? 'N/A', 15) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="event-meta">
                                        <div class="event-date">{{ $event->start_date ? $event->start_date->format('d/m/Y') : 'N/A' }}</div>
                                        <div class="event-time">{{ $event->start_date ? $event->start_date->format('H:i') : '' }}</div>
                                    </div>
                                </td>
                                <td class="hide-sm" style="padding-left: 1.5rem;">
                                    @if($event->etat === 'En cours')
                                        <span class="modern-badge badge-success">Actif</span>
                                    @elseif($event->etat === 'En attente')
                                        <span class="modern-badge badge-warning">Attente</span>
                                    @elseif($event->etat === 'Archivé')
                                        <span class="modern-badge badge-secondary">Archivé</span>
                                    @elseif($event->etat === 'Annulé')
                                        <span class="modern-badge badge-danger">Annulé</span>
                                    @else
                                        <span class="modern-badge badge-danger">Inactif</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div class="btn-group-modern" style="justify-content: center;">
                                        <a href="{{ route('admin.events.show', $event) }}" class="btn-icon-modern btn-info-modern" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.events.edit', $event) }}" class="btn-icon-modern btn-warning-modern" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn-icon-modern btn-danger-modern delete-event" data-id="{{ $event->id }}" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center;">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-calendar-times"></i>
                                        </div>
                                        <div class="empty-state-title">Aucun événement public</div>
                                        <div class="empty-state-description">Commencez par créer votre premier événement</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($events->hasPages())
                <div class="pagination-container">
                    {{ $events->links('vendor.pagination.bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Événements personnalisés -->
    <div class="modern-card fade-in" style="animation-delay: 0.2s;">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-user-lock"></i>
                Événements personnalisés ({{ $customEvents->total() }})
            </h5>
            <a href="{{ route('custom-personal-events.create') }}" class="modern-btn btn-success-modern btn-sm-modern">
                <i class="fas fa-plus"></i>
                Nouvel événement personnalisé
            </a>
        </div>
        <div class="card-body-modern">
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th class="hide-md">Organisateur</th>
                            <th>Période</th>
                            <th class="hide-lg">Lieu</th>
                            <th>Invités</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customEvents as $event)
                            <tr data-event-id="{{ $event->id }}">
                                @php
                                    $owner = $event->organizer;
                                    $ownerOrganization = optional($owner)->organizer;
                                    $ownerNameParts = collect([
                                        optional($owner)->prenom,
                                        optional($owner)->nom,
                                    ])->filter();
                                    $ownerName = $ownerNameParts->isNotEmpty()
                                        ? $ownerNameParts->join(' ')
                                        : (
                                            optional($owner)->name
                                            ?? optional($ownerOrganization)->company_name
                                            ?? optional($owner)->email
                                            ?? 'N/A'
                                        );
                                @endphp
                                <td>
                                    <div class="event-title">{{ $event->title }}</div>
                                    <div class="d-md-none mt-1">
                                        <small class="text-muted">{{ Str::limit($ownerName, 20) }}</small>
                                    </div>
                                </td>
                                <td class="hide-md">
                                    <div class="text-truncate" style="max-width: 100px;" title="{{ $ownerName }}">
                                        {{ Str::limit($ownerName, 15) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="event-meta">
                                        <div class="event-date">{{ $event->start_date }}</div>
                                        <div class="event-time">au {{ $event->end_date }}</div>
                                    </div>
                                </td>
                                <td class="hide-lg">
                                    <div class="event-location" title="{{ $event->location }}">{{ Str::limit($event->location, 12) }}</div>
                                </td>
                                <td>
                                    <span class="modern-badge badge-info">
                                        {{ $event->guests->count() }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="btn-group-modern" style="justify-content: center;">
                                        <a href="{{ route('custom-personal-events.show', $event->url) }}" class="btn-icon-modern btn-info-modern" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('custom-personal-events.edit', $event->id) }}" class="btn-icon-modern btn-warning-modern" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('custom-personal-events.destroy', $event->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon-modern btn-danger-modern" title="Supprimer" onclick="return confirm('Êtes-vous sûr ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center;">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-user-slash"></i>
                                        </div>
                                        <div class="empty-state-title">Aucun événement personnalisé</div>
                                        <div class="empty-state-description">Les événements privés apparaîtront ici</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customEvents->hasPages())
                <div class="pagination-container">
                    {{ $customEvents->links('vendor.pagination.bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Événements en attente de validation -->
    <div class="modern-card fade-in" style="animation-delay: 0.3s;">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-clock"></i>
                Événements en attente ({{ $pendingEvents->total() }})
            </h5>
        </div>
        <div class="card-body-modern">
            <div class="table-container">
                <table class="modern-table" id="pending-events-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th class="hide-md">Organisateur</th>
                            <th class="hide-lg">Catégorie</th>
                            <th>Date</th>
                            <th class="hide-xl">Lieu</th>
                            <th class="hide-md">Demandé</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingEvents as $event)
                            <tr data-event-id="{{ $event->id }}">
                                <td>
                                    <div class="event-title">{{ $event->title }}</div>
                                    <div class="d-md-none mt-1">
                                        <div class="mb-1">
                                            <span class="modern-badge badge-info">{{ Str::limit($event->category->name ?? 'N/A', 8) }}</span>
                                        </div>
                                        <small class="text-muted d-block">{{ Str::limit($event->organizer->company_name ?? $event->user->nom, 20) }}</small>
                                        <small class="text-muted d-block d-xl-none">{{ Str::limit($event->ville, 15) }}, {{ Str::limit($event->pays, 10) }}</small>
                                    </div>
                                </td>
                                <td class="hide-md">
                                    <div class="text-truncate" style="max-width: 90px;" title="{{ $event->organizer->company_name ?? $event->user->nom }}">
                                        {{ Str::limit($event->organizer->company_name ?? $event->user->nom, 12) }}
                                    </div>
                                </td>
                                <td class="hide-lg">
                                    <span class="modern-badge badge-info">{{ Str::limit($event->category->name ?? 'N/A', 8) }}</span>
                                </td>
                                <td>
                                    <div class="event-meta">
                                        <div class="event-date">{{ $event->start_date->format('d/m/Y') }}</div>
                                        <div class="event-time">{{ $event->start_date->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="hide-xl">
                                    <div class="event-location" title="{{ $event->ville }}, {{ $event->pays }}">
                                        {{ Str::limit($event->ville, 8) }}, {{ Str::limit($event->pays, 6) }}
                                    </div>
                                </td>
                                <td class="hide-md">
                                    <small class="text-muted">{{ $event->publicationRequest->created_at->diffForHumans() ?? 'N/A' }}</small>
                                </td>
                                <td style="text-align: center;">
                                    <div class="btn-group-modern" style="justify-content: center;">
                                        <button class="btn-icon-modern btn-success-modern approve-btn" 
                                                data-id="{{ $event->id }}" title="Approuver">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn-icon-modern btn-danger-modern reject-btn" 
                                                data-id="{{ $event->id }}" title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <a href="{{ route('admin.events.show', $event) }}" 
                                           class="btn-icon-modern btn-info-modern" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center;">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="empty-state-title">Aucun événement en attente</div>
                                        <div class="empty-state-description">Tous les événements ont été traités</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($pendingEvents->hasPages())
                <div class="pagination-container">
                    {{ $pendingEvents->links('vendor.pagination.bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('modals')
<!-- Modal Choix Type d'Événement -->
<div class="modal fade modern-modal" id="chooseEventTypeModal" tabindex="-1" aria-labelledby="chooseEventTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="chooseEventTypeModalLabel">
                    <i class="fas fa-calendar-plus"></i>
                    Créer un nouvel événement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-question-circle" style="font-size: 2.5rem; color: var(--primary); opacity: 0.7;"></i>
                    </div>
                    <h6 class="mb-2">Quel type d'événement souhaitez-vous créer ?</h6>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Choisissez le type qui correspond le mieux à votre événement</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('events.wizard.step1') }}" class="text-decoration-none">
                            <div class="card h-100 border-2 border-primary event-type-card" style="transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-3">
                                    <div class="mb-3">
                                        <i class="fas fa-users" style="font-size: 2.5rem; color: var(--primary);"></i>
                                    </div>
                                    <h6 class="card-title text-primary mb-2">Événement public</h6>
                                    <p class="card-text text-muted mb-3" style="font-size: 0.8rem;">
                                        Événement ouvert à tous, visible dans la liste publique des événements
                                    </p>
                                    <div class="d-flex justify-content-center gap-1 mb-3 flex-wrap">
                                        <span class="badge bg-primary" style="font-size: 0.65rem;">Public</span>
                                        <span class="badge bg-info" style="font-size: 0.65rem;">Visible</span>
                                        <span class="badge bg-success" style="font-size: 0.65rem;">Payant</span>
                                    </div>
                                    <div class="modern-btn btn-primary-modern w-100" style="font-size: 0.75rem;">
                                        <i class="fas fa-arrow-right me-1"></i>
                                        Créer événement public
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-6">
                        <a href="{{ route('custom-personal-events.create') }}" class="text-decoration-none">
                            <div class="card h-100 border-2 border-success event-type-card" style="transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-3">
                                    <div class="mb-3">
                                        <i class="fas fa-lock" style="font-size: 2.5rem; color: var(--success);"></i>
                                    </div>
                                    <h6 class="card-title text-success mb-2">Événement personnalisé</h6>
                                    <p class="card-text text-muted mb-3" style="font-size: 0.8rem;">
                                        Événement privé sur invitation : mariage, anniversaire, réunion familiale...
                                    </p>
                                    <div class="d-flex justify-content-center gap-1 mb-3 flex-wrap">
                                        <span class="badge bg-success" style="font-size: 0.65rem;">Privé</span>
                                        <span class="badge bg-warning" style="font-size: 0.65rem;">Invitation</span>
                                        <span class="badge bg-secondary" style="font-size: 0.65rem;">Gratuit</span>
                                    </div>
                                    <div class="modern-btn btn-success-modern w-100" style="font-size: 0.75rem;">
                                        <i class="fas fa-arrow-right me-1"></i>
                                        Créer événement privé
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 0.75rem 1.25rem;">
                <button type="button" class="modern-btn btn-secondary-modern" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation pour le rejet -->
<div class="modal fade modern-modal" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--danger), #dc2626);">
                <h5 class="modal-title d-flex align-items-center gap-2 text-white" id="rejectModalLabel">
                    <i class="fas fa-times-circle"></i>
                    Rejeter l'événement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modern-alert alert-warning-modern">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Attention :</strong> Cette action enverra une notification à l'organisateur avec la raison du rejet.
                    </div>
                </div>
                
                <form id="rejectForm">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label d-flex align-items-center gap-2 fw-bold">
                            <i class="fas fa-comment-alt text-primary"></i>
                            Raison du rejet <span class="text-danger">*</span>
                        </label>
                        <textarea class="modern-form-control" id="rejectionReason" name="rejection_reason" rows="4" 
                                  placeholder="Expliquez clairement pourquoi cet événement ne peut pas être publié. Soyez précis pour aider l'organisateur à comprendre et corriger les problèmes..." required></textarea>
                        <div class="form-text d-flex align-items-center gap-1 mt-2">
                            <i class="fas fa-info-circle text-info"></i>
                            <small>Minimum 10 caractères requis. Soyez constructif dans vos commentaires.</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="modern-btn btn-secondary-modern" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-left me-1"></i>Annuler
                </button>
                <button type="button" class="modern-btn btn-danger-modern" id="confirmReject" disabled>
                    <i class="fas fa-times me-1"></i>Rejeter l'événement
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation pour suppression -->
<div class="modal fade modern-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--danger), #dc2626);">
                <h5 class="modal-title d-flex align-items-center gap-2 text-white" id="deleteModalLabel">
                    <i class="fas fa-trash-alt"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-3">
                <div class="mb-3">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--warning);"></i>
                </div>
                <h6 class="mb-3">Êtes-vous absolument sûr ?</h6>
                <p class="text-muted mb-3" style="font-size: 0.85rem;">
                    Cette action est <strong>irréversible</strong>. L'événement sera définitivement supprimé 
                    de la base de données ainsi que toutes les données associées.
                </p>
                <div class="modern-alert alert-danger-modern">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>Toutes les inscriptions et données liées seront également perdues.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modern-btn btn-secondary-modern" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
                <button type="button" class="modern-btn btn-danger-modern" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i>Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// Configuration toastr
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
    "showDuration": "200",
    "hideDuration": "800",
    "timeOut": "4000",
    "extendedTimeOut": "800",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "slideDown",
    "hideMethod": "slideUp"
};

// Variables globales
let eventToReject = null;
let eventToDelete = null;

// Fonction utilitaire pour récupérer le token CSRF
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.content : '';
}

// Fonction pour approuver un événement
function approveEvent(eventId, button) {
    if (!eventId || !button) {
        console.error('Paramètres manquants pour approveEvent');
        toastr.error('Erreur: paramètres manquants');
        return;
    }

    const row = button.closest('tr');
    const url = `/Administrateur/events/${eventId}/approve`;
    
    // État de chargement
    button.classList.add('loading-state');
    button.innerHTML = '<div class="spinner"></div>';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            toastr.success(data.message || 'Événement approuvé avec succès');
            // Animation de disparition
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(-100px)';
                setTimeout(() => {
                    if (row.parentNode) {
                        row.remove();
                    }
                    // Mettre à jour le compteur
                    updatePendingCount(-1);
                }, 300);
            }
        } else {
            throw new Error(data.message || 'Erreur lors de l\'approbation');
        }
    })
    .catch(error => {
        console.error('Erreur approbation:', error);
        toastr.error(error.message || 'Erreur lors de l\'approbation');
        // Restaurer le bouton
        button.classList.remove('loading-state');
        button.innerHTML = '<i class="fas fa-check"></i>';
    });
}

// Fonction pour rejeter un événement
function rejectEvent(eventId, reason) {
    if (!eventId || !reason || reason.length < 10) {
        toastr.error('Raison de rejet invalide');
        return;
    }

    const button = document.querySelector(`button[data-id="${eventId}"].reject-btn`);
    const row = button ? button.closest('tr') : null;
    const url = `/Administrateur/events/${eventId}/reject`;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ rejection_reason: reason })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            toastr.warning(data.message || 'Événement rejeté avec succès');
            // Animation de disparition
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(100px)';
                setTimeout(() => {
                    if (row.parentNode) {
                        row.remove();
                    }
                    // Mettre à jour le compteur
                    updatePendingCount(-1);
                }, 300);
            }
        } else {
            throw new Error(data.message || 'Erreur lors du rejet');
        }
    })
    .catch(error => {
        console.error('Erreur rejet:', error);
        toastr.error(error.message || 'Erreur lors du rejet');
    });
}

// Mettre à jour le compteur d'événements en attente
function updatePendingCount(change) {
    const pendingTitle = document.querySelector('.card-title:contains("Événements en attente")');
    if (pendingTitle) {
        const text = pendingTitle.textContent;
        const match = text.match(/\((\d+)\)/);
        if (match) {
            const currentCount = parseInt(match[1]);
            const newCount = Math.max(0, currentCount + change);
            pendingTitle.textContent = text.replace(/\(\d+\)/, `(${newCount})`);
            
            // Mettre à jour également la carte de statistiques
            const statNumber = document.querySelector('.stat-card:nth-child(3) .stat-number');
            if (statNumber) {
                statNumber.textContent = newCount;
            }
        }
    }
}

// Forcer les icônes blanches dans les boutons
function forceWhiteIcons() {
    // Boutons d'action
    const buttons = document.querySelectorAll('.btn-icon-modern, .btn-info-modern, .btn-warning-modern, .btn-success-modern, .btn-danger-modern');
    buttons.forEach(button => {
        const icons = button.querySelectorAll('i, svg');
        icons.forEach(icon => {
            icon.style.color = '#ffffff';
            icon.style.setProperty('color', '#ffffff', 'important');
            if (icon.tagName === 'svg') {
                icon.style.fill = '#ffffff';
                icon.style.setProperty('fill', '#ffffff', 'important');
            }
        });
    });
    
    // Icônes dans les en-têtes de cartes (fond bleu)
    const cardHeaders = document.querySelectorAll('.card-header-modern');
    cardHeaders.forEach(header => {
        const icons = header.querySelectorAll('i, svg');
        icons.forEach(icon => {
            icon.style.color = '#ffffff';
            icon.style.setProperty('color', '#ffffff', 'important');
            if (icon.tagName === 'svg') {
                icon.style.fill = '#ffffff';
                icon.style.setProperty('fill', '#ffffff', 'important');
            }
        });
    });
}

// Initialisation quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de la gestion des événements');
    
    // Boutons d'approbation
    document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const eventId = this.getAttribute('data-id');
            if (eventId) {
                approveEvent(eventId, this);
            } else {
                console.error('ID d\'événement manquant pour l\'approbation');
                toastr.error('Erreur: ID d\'événement manquant');
            }
        });
    });
    
    // Boutons de rejet
    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const eventId = this.getAttribute('data-id');
            if (eventId) {
                eventToReject = eventId;
                const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                modal.show();
            } else {
                console.error('ID d\'événement manquant pour le rejet');
                toastr.error('Erreur: ID d\'événement manquant');
            }
        });
    });
    
    // Gestion du modal de rejet
    const confirmRejectBtn = document.getElementById('confirmReject');
    const rejectionReason = document.getElementById('rejectionReason');
    
    if (confirmRejectBtn && rejectionReason) {
        // Validation en temps réel
        rejectionReason.addEventListener('input', function() {
            const value = this.value.trim();
            const isValid = value.length >= 10;
            
            confirmRejectBtn.disabled = !isValid;
            
            // Feedback visuel
            this.classList.remove('is-invalid', 'is-valid');
            if (value.length > 0) {
                this.classList.add(isValid ? 'is-valid' : 'is-invalid');
            }
        });
        
        // Réinitialiser le modal quand il est fermé
        document.getElementById('rejectModal').addEventListener('hidden.bs.modal', function() {
            rejectionReason.value = '';
            rejectionReason.classList.remove('is-invalid', 'is-valid');
            confirmRejectBtn.disabled = true;
            confirmRejectBtn.classList.remove('loading-state');
            confirmRejectBtn.innerHTML = '<i class="fas fa-times me-1"></i>Rejeter l\'événement';
        });
        
        confirmRejectBtn.addEventListener('click', function() {
            const reason = rejectionReason.value.trim();
            if (reason.length >= 10 && eventToReject) {
                this.classList.add('loading-state');
                this.innerHTML = '<div class="spinner"></div> Rejet...';
                rejectEvent(eventToReject, reason);
                const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
                if (modal) modal.hide();
            } else {
                toastr.error('Veuillez saisir une raison d\'au moins 10 caractères');
            }
        });
    }
    
    // Gestion des boutons de suppression
    document.querySelectorAll('.delete-event').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const eventId = this.getAttribute('data-id');
            if (eventId) {
                eventToDelete = eventId;
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            }
        });
    });
    
    // Confirmation de suppression
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (eventToDelete) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/Administrateur/events/${eventToDelete}`;
                form.style.display = 'none';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = getCsrfToken();
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    // Forcer les icônes blanches
    forceWhiteIcons();
    setTimeout(forceWhiteIcons, 100);
    
    // Observer pour les nouveaux éléments ajoutés dynamiquement
    const observer = new MutationObserver(function(mutations) {
        forceWhiteIcons();
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Gestion des erreurs globales
window.addEventListener('error', function(e) {
    console.error('Erreur globale:', e.error);
});

// Fonctions de débogage
function debugEvents() {
    console.log('=== DEBUG ÉVÉNEMENTS ===');
    console.log('Boutons approbation:', document.querySelectorAll('.approve-btn').length);
    console.log('Boutons rejet:', document.querySelectorAll('.reject-btn').length);
    console.log('Token CSRF:', getCsrfToken() ? 'Présent' : 'Manquant');
    console.log('Modal rejet:', document.getElementById('rejectModal') ? 'Présent' : 'Manquant');
}

// Test simple pour vérifier que Bootstrap fonctionne
setTimeout(() => {
    console.log('🔍 Test Bootstrap Modal...');
    const testModal = document.getElementById('rejectModal');
    if (testModal) {
        console.log('✅ Modal rejectModal trouvé');
    } else {
        console.log('❌ Modal rejectModal NON TROUVÉ');
    }
}, 2000);
</script>
@endpush