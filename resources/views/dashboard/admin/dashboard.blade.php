@extends('layouts.dashboard')

@section('title', 'Tableau de bord administrateur')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<style>
    /* Container adjustment */
    .main-content {
        padding: 1.5rem !important;
    }
    /* Variables CSS personnalisées - Design MokiliEvent */
    :root {
        /* Palette Principale - Bleu MokiliEvent & blanc Or */
        --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
        --bleu-nuit-lighter: #2c3e8f;
        --bleu-royal: #3b4f9a;
        --bleu-soft: #5a6ba8;
        --bleu-light: #7c8db8;
        
        --blanc-or: #ffffff;
        --blanc-or-light: #ffe44d;
        --blanc-or-dark: #e6c200;
        --blanc-amber: #ffb700;
        --blanc-warm: #ffdb58;
        
        /* Couleurs d'état */
        --success: #10b981;
        --success-bg: #d1fae5;
        --warning: #ffffff;
        --warning-bg: #fff8dc;
        --danger: #ef4444;
        --danger-bg: #fee2e2;
        /* Retrait du bleu: info passe en gris neutre */
        --info: #6b7280;
        --info-bg: #f3f4f6;
        
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
        
        /* Ombres et effets */
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --shadow-gold: 0 10px 25px -5px rgba(255, 255, 255, 0.3);
        --shadow-blue: 0 10px 25px -5px rgba(185, 28, 28, 0.3);
        
        --border-radius: 0.75rem;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Améliorations des cartes statistiques */
    .stat-card {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: var(--shadow-lg);
        transition: var(--transition);
        overflow: hidden;
        position: relative;
        background: linear-gradient(135deg, var(--white) 0%, var(--gray-50) 100%);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--bleu-nuit) 0%, var(--blanc-or) 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: var(--transition);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-xl);
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    .stat-card .card-body {
        padding: 1rem 1.25rem;
        position: relative;
        z-index: 2;
    }

    /* Icônes animées - Taille réduite */
    .stat-icon {
        font-size: 2rem;
        opacity: 1;
        transition: var(--transition);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-card:hover .stat-icon {
        opacity: 1 !important;
        transform: scale(1.08);
    }
    
    /* Ensure stat-card content is visible */
    .stat-card .card-body {
        color: var(--gray-800);
    }
    
    .stat-card h1, .stat-card h2, .stat-card h3, .stat-card h4, .stat-card h5 {
        color: inherit;
    }

    /* Indicateurs de bordure colorés */
    .border-left-primary { border-left: 4px solid var(--bleu-nuit) !important; }
    .border-left-success { border-left: 4px solid var(--success) !important; }
    .border-left-info { border-left: 4px solid var(--info) !important; }
    .border-left-warning { border-left: 4px solid var(--blanc-or) !important; }
    .border-left-danger { border-left: 4px solid var(--danger) !important; }
    .border-left-secondary { border-left: 4px solid var(--gray-500) !important; }

    /* Couleurs de texte */
    .text-primary { color: var(--bleu-nuit) !important; }
    .text-success { color: var(--success) !important; }
    .text-info { color: var(--info) !important; }
    .text-warning { color: var(--blanc-or) !important; }
    .text-danger { color: var(--danger) !important; }
    .text-secondary { color: var(--gray-500) !important; }

    /* En-tête amélioré - Barre en bleu */
    .dashboard-header {
        background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
        color: #ffffff;
        border: none;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        opacity: 0.3;
        border-radius: 50%;
        transform: rotate(45deg);
    }

    .dashboard-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
        color: #ffffff !important;
    }

    .dashboard-header p {
        font-size: 1.1rem;
        opacity: 0.95;
        margin: 0;
        position: relative;
        z-index: 2;
        color: rgba(255, 255, 255, 0.9) !important;
    }

    /* Sidebar admin en bleu avec texte blanc et icônes blanches */
    #sidebar-wrapper {
        background: var(--bleu-nuit, #0f1a3d) !important;
        border-right: 1px solid rgba(15, 26, 61, 0.2) !important;
    }
    #sidebar-wrapper .sidebar-heading span { color: #ffffff !important; }
    #sidebar-wrapper .list-group-item {
        background: transparent !important;
        color: #ffffff !important;
        border: none !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    #sidebar-wrapper .list-group-item i { color: #ffffff !important; }
    #sidebar-wrapper .list-group-item:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
    }
    #sidebar-wrapper .list-group-item.active {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
    }
    #sidebar-wrapper .list-group-item.active i {
        color: #ffffff !important;
    }
    .sidebar-section-title {
        color: rgba(255, 255, 255, 0.7) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    
    /* Icônes bleues sur fond blanc dans le contenu principal */
    .main-content i.fas,
    .main-content i.far,
    .main-content i.fab,
    .main-content i.fal,
    .topbar i.fas,
    .topbar i.far,
    .topbar i.fab,
    .topbar i.fal,
    .card i.fas,
    .card i.far,
    .card i.fab,
    .card i.fal {
        color: var(--bleu-nuit, #0f1a3d) !important;
    }

    /* Neutraliser le bleu restant */
    .text-info { color: #6b7280 !important; }
    .border-left-info { border-left: 4px solid #6b7280 !important; }

    .generate-report-btn {
        background: var(--bleu-nuit, #0f1a3d);
        color: #ffffff !important;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: var(--transition);
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        z-index: 2;
    }
    
    .generate-report-btn i {
        color: #ffffff !important;
    }

    .generate-report-btn:hover {
        background: var(--bleu-nuit-clair, #1a237e);
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 26, 61, 0.3);
        text-decoration: none !important;
    }
    
    .generate-report-btn:hover i {
        color: #ffffff !important;
    }

    /* Boutons blancs pour "Générer un rapport" et "Voir les logs" */
    .generate-report-btn.btn-white {
        background: #ffffff !important;
        color: var(--bleu-nuit, #0f1a3d) !important;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .generate-report-btn.btn-white i {
        color: var(--bleu-nuit, #0f1a3d) !important;
    }

    .generate-report-btn.btn-white:hover {
        background: rgba(255, 255, 255, 0.95) !important;
        color: var(--bleu-nuit-clair, #1a237e) !important;
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }
    
    .generate-report-btn.btn-white:hover i {
        color: var(--bleu-nuit-clair, #1a237e) !important;
    }

    /* Cartes de graphiques améliorées */
    .chart-card {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: var(--shadow-lg);
        transition: var(--transition);
        overflow: hidden;
    }

    .chart-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
    }

    .chart-card .card-header {
        background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
        border-bottom: 1px solid var(--gray-200);
        padding: 1rem 1.25rem;
    }

    .chart-card .card-header h6 {
        font-weight: 700;
        color: var(--bleu-nuit) !important;
        margin: 0;
        font-size: 1rem;
    }
    
    /* Ensure all text in cards is visible */
    .chart-card {
        background: white;
    }
    
    .chart-card .card-body {
        color: var(--gray-800);
    }

    /* Zones de graphiques */
    .chart-area, .chart-pie {
        position: relative;
        height: 20rem;
        padding: 1rem;
    }

    .chart-bar {
        position: relative;
        height: 15rem;
        padding: 1rem;
    }

    /* Tableau des activités récentes */
    .activities-table {
        width: 100%;
        border-collapse: collapse;
    }

    .activities-table thead {
        background: var(--gray-50);
        border-bottom: 2px solid var(--gray-200);
    }

    .activities-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--gray-700) !important;
        border-bottom: 2px solid var(--gray-200);
    }

    .activities-table tbody tr {
        border-bottom: 1px solid var(--gray-200);
        transition: var(--transition);
    }

    .activities-table tbody tr:hover {
        background: var(--gray-50);
    }

    .activities-table tbody tr:last-child {
        border-bottom: none;
    }

    .activities-table td {
        padding: 1rem;
        vertical-align: middle;
        color: var(--gray-800) !important;
    }

    .activity-icon-cell {
        width: 50px;
        text-align: center;
    }

    .activity-icon-table {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(15, 26, 61, 0.15);
    }

    .activity-icon-table i {
        color: #ffffff !important;
        font-size: 0.875rem;
    }

    .activity-description-cell {
        font-weight: 600;
        color: #000000 !important;
        font-size: 0.95rem;
    }

    .activity-user-cell {
        color: var(--gray-600) !important;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .activity-user-cell i {
        color: var(--bleu-nuit) !important;
        font-size: 0.75rem;
    }

    .activity-time-cell {
        color: var(--gray-500) !important;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .activity-properties-cell {
        font-size: 0.8rem;
        color: var(--gray-600) !important;
    }

    .activity-properties-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: var(--gray-100);
        border-radius: 0.375rem;
        font-size: 0.75rem;
        margin-right: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .activity-properties-badge strong {
        color: var(--bleu-nuit) !important;
        font-weight: 600;
        margin-right: 0.25rem;
    }
    
    /* Textes en noir pour les statistiques */
    .stat-label {
        color: #000000 !important;
        font-weight: 600;
    }
    
    .stat-number {
        color: #000000 !important;
        -webkit-text-fill-color: #000000 !important;
        background: none !important;
    }
    
    /* Icônes en bleu pour les statistiques - sans arrière-plan */
    .stat-icon,
    .stat-icon.text-primary,
    .stat-icon.text-success,
    .stat-icon.text-info,
    .stat-icon.text-warning,
    .stat-icon.text-secondary {
        color: var(--bleu-nuit, #0f1a3d) !important;
        background: none !important;
        opacity: 1 !important;
        box-shadow: none !important;
    }
    
    .stat-icon i,
    .stat-icon.text-primary i,
    .stat-icon.text-success i,
    .stat-icon.text-info i,
    .stat-icon.text-warning i,
    .stat-icon.text-secondary i {
        color: var(--bleu-nuit, #0f1a3d) !important;
        background: none !important;
        opacity: 1 !important;
    }
    
    /* Icônes dans les cartes statistiques - toutes en bleu sans arrière-plan */
    .stat-card .stat-icon,
    .stat-card .stat-icon i {
        color: var(--bleu-nuit, #0f1a3d) !important;
        background: none !important;
        background-color: transparent !important;
    }
    
    /* Pourcentages et bénéfices en bleu sans arrière-plan */
    .stat-card .stat-number.text-primary,
    .stat-card .stat-number.text-success,
    .stat-card .stat-number.text-warning {
        color: var(--bleu-nuit, #0f1a3d) !important;
        background: none !important;
        -webkit-text-fill-color: var(--bleu-nuit, #0f1a3d) !important;
    }
    
    .stat-card .stat-label.text-primary,
    .stat-card .stat-label.text-success,
    .stat-card .stat-label.text-warning {
        color: var(--bleu-nuit, #0f1a3d) !important;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .chart-area, .chart-pie {
            height: 18rem;
        }
    }

    @media (max-width: 992px) {
        .dashboard-header {
            text-align: center;
            padding: 1.5rem;
        }
        
        .dashboard-header h1 {
            font-size: 1.75rem;
        }
        
        .stat-card .card-body {
            padding: 1rem;
        }
        
        .chart-area, .chart-pie {
            height: 16rem;
        }
    }

    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1rem;
        }
        
        .dashboard-header h1 {
            font-size: 1.5rem;
        }
        
        .dashboard-header p {
            font-size: 1rem;
        }
        
        .stat-card {
            margin-bottom: 1rem;
        }
        
        .stat-card .card-body {
            padding: 0.875rem;
            text-align: center;
        }
        
        .stat-icon {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-number {
            font-size: 2rem;
        }
        
        .chart-area, .chart-pie {
            height: 14rem;
        }
        
        .chart-bar {
            height: 12rem;
        }
    }

    @media (max-width: 576px) {
        .dashboard-header h1 {
            font-size: 1.25rem;
        }
        
        .stat-card .card-body {
            padding: 0.75rem;
        }
        
        .stat-number {
            font-size: 1.75rem;
        }
        
        .stat-icon {
            font-size: 1.5rem;
        }
        
        .chart-area, .chart-pie {
            height: 12rem;
        }
        
        .chart-card .card-header {
            padding: 0.75rem 1rem;
        }
        
        /* Tableau activités récentes responsive */
        .activities-table {
            font-size: 0.875rem;
        }
        
        .activities-table th,
        .activities-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .activities-table th:nth-child(4),
        .activities-table td:nth-child(4) {
            display: none;
        }
    }
    
    @media (max-width: 768px) {
        .activities-table th:nth-child(3),
        .activities-table td:nth-child(3) {
            display: none;
        }
    }

    /* Animations et micro-interactions - Nombres plus grands */
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        transition: var(--transition);
        color: #000000 !important;
        line-height: 1.2;
    }

    /* Tous les stat-numbers en noir - Nombres plus grands */
    .stat-number:not([class*="text-"]),
    .stat-number.text-success,
    .stat-number.text-info,
    .stat-number.text-warning,
    .stat-number.text-primary,
    .stat-number.text-secondary {
        -webkit-text-fill-color: #000000 !important;
        background: none !important;
        color: #000000 !important;
        font-size: 2.5rem;
    }

    .stat-card:hover .stat-number {
        transform: scale(1.03);
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        opacity: 0.8;
    }

    /* Loader personnalisé */
    .chart-loading {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
        color: var(--secondary-color);
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid var(--gray-200);
        border-top: 4px solid var(--bleu-nuit);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Amélioration des tooltips */
    .tooltip-inner {
        background-color: var(--bleu-nuit);
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }

    /* Styles pour les badges de statut */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-success {
        background-color: var(--success-bg);
        color: var(--success);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .status-warning {
        background-color: var(--warning-bg);
        color: var(--blanc-or);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .status-danger {
        background-color: var(--danger-bg);
        color: var(--danger);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
</style>
@endpush

@section('content')
<!-- En-tête du tableau de bord -->
<div class="dashboard-header">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
            <h1>Tableau de bord</h1>
            <p>Vue d'ensemble de votre plateforme d'événements</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0 flex-wrap">
            <a href="{{ route('admin.withdrawals.index') }}" class="generate-report-btn" style="background-color: #10b981; border-color: #10b981;">
                <i class="fas fa-money-bill-transfer"></i>
                Gérer les retraits
            </a>
            <a href="{{ route('admin.reports.index') }}" class="generate-report-btn btn-white">
                <i class="fas fa-download"></i>
                Générer un rapport
            </a>
            <a href="{{ url('/log-viewer') }}" class="generate-report-btn btn-white">
                <i class="fas fa-file-alt"></i>
                Voir les logs
            </a>
        </div>
    </div>
</div>

<!-- Cartes statistiques principales -->
<div class="row g-4 mb-4">
    <!-- Utilisateurs -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label text-primary">Utilisateurs</div>
                        <div class="stat-number">{{ format_number($stats['users_count'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Événements -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-left-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label text-success">Événements</div>
                        <div class="stat-number text-success">{{ format_number($stats['events_count'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets vendus -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-left-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label text-info">Tickets vendus</div>
                        <div class="stat-number text-info">{{ format_number($stats['total_tickets_sold'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenus -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-left-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label text-warning">Revenus</div>
                        <div class="stat-number text-warning">{{ format_number($stats['revenue'] ?? 0) }}</div>
                        <small class="text-muted">FCFA</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cartes statistiques secondaires -->
<div class="row g-4 mb-4">
    <!-- Blogs publiés -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-left-secondary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label text-secondary">Blogs publiés</div>
                        <div class="stat-number text-secondary">{{ format_number($stats['blogs_count'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-blog"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paiements MTN -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Paiements MTN</div>
                        <div class="stat-number">{{ $stats['mtn_payments_percentage'] ?? 0 }}%</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paiements Airtel -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Paiements Airtel</div>
                        <div class="stat-number">{{ $stats['airtel_payments_percentage'] ?? 0 }}%</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bénéfices MokiliEvent -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Bénéfices MokiliEvent</div>
                        <div class="stat-number">{{ format_number($stats['benefits'] ?? 0) }} FCFA</div>
                        <small class="text-muted">10% commission</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="row g-4 mb-4">
    <!-- Ventes de tickets -->
    <div class="col-xl-8">
        <div class="card chart-card h-100">
            <div class="card-header">
                <h6><i class="fas fa-chart-line me-2"></i>Ventes de tickets (7 derniers jours)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="ticketSalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution par catégorie -->
    <div class="col-xl-4">
        <div class="card chart-card h-100">
            <div class="card-header">
                <h6><i class="fas fa-chart-pie me-2"></i>Distribution par catégorie</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie">
                    <canvas id="categoryDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques de paiement -->
<div class="row g-4 mb-4">
    <!-- Méthodes de paiement -->
    <div class="col-xl-6">
        <div class="card chart-card h-100">
            <div class="card-header">
                <h6><i class="fas fa-credit-card me-2"></i>Paiements par méthode</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie">
                    <canvas id="paymentMethodsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tendances de paiement -->
    <div class="col-xl-6">
        <div class="card chart-card h-100">
            <div class="card-header">
                <h6><i class="fas fa-trending-up me-2"></i>Tendance des paiements</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="paymentTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activités récentes -->
<div class="row g-4">
    <div class="col-12">
        <div class="card chart-card">
            <div class="card-header">
                <h6><i class="fas fa-history me-2"></i>Activités récentes</h6>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="activities-table">
                        <thead>
                            <tr>
                                <th class="activity-icon-cell"></th>
                                <th>Description</th>
                                <th>Utilisateur</th>
                                <th>Propriétés</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(collect($recentActivities)->take(5) as $activity)
                                <tr>
                                    <td class="activity-icon-cell">
                                        <div class="activity-icon-table">
                                            @if(str_contains(strtolower($activity['description'] ?? ''), 'créé') || str_contains(strtolower($activity['description'] ?? ''), 'création'))
                                                <i class="fas fa-plus-circle"></i>
                                            @elseif(str_contains(strtolower($activity['description'] ?? ''), 'modifié') || str_contains(strtolower($activity['description'] ?? ''), 'mise à jour'))
                                                <i class="fas fa-edit"></i>
                                            @elseif(str_contains(strtolower($activity['description'] ?? ''), 'supprimé') || str_contains(strtolower($activity['description'] ?? ''), 'suppression'))
                                                <i class="fas fa-trash-alt"></i>
                                            @elseif(str_contains(strtolower($activity['description'] ?? ''), 'paiement') || str_contains(strtolower($activity['description'] ?? ''), 'payé'))
                                                <i class="fas fa-money-bill-wave"></i>
                                            @elseif(str_contains(strtolower($activity['description'] ?? ''), 'retrait') || str_contains(strtolower($activity['description'] ?? ''), 'withdrawal'))
                                                <i class="fas fa-hand-holding-usd"></i>
                                            @else
                                                <i class="fas fa-circle"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="activity-description-cell">
                                        {{ $activity['description'] }}
                                    </td>
                                    <td>
                                        <div class="activity-user-cell">
                                            <i class="fas fa-user"></i>
                                            <span>{{ $activity['user_name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="activity-properties-cell">
                                        @if(!empty($activity['properties']))
                                            @foreach($activity['properties'] as $key => $value)
                                                <span class="activity-properties-badge">
                                                    <strong>{{ ucfirst($key) }}:</strong>
                                                    {{ is_array($value) ? json_encode($value) : (strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value) }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="activity-time-cell">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $activity['created_at']->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="fas fa-inbox fa-3x" style="color: var(--gray-400) !important;"></i>
                                        </div>
                                        <p class="text-muted mb-0">Aucune activité récente</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(count($recentActivities) > 5)
                    <div class="text-center p-3 border-top">
                        <a href="{{ route('admin.activities.index') }}" class="btn btn-primary" style="text-decoration: none !important;">
                            <i class="fas fa-eye me-2" style="color: #ffffff !important;"></i>Voir plus
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Configuration globale des graphiques
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#858796';
Chart.defaults.plugins.legend.labels.usePointStyle = true;

// Palette de couleurs cohérente - Design MokiliEvent
const colorPalette = {
    primary: '#b91c1c',
    primaryLight: '#dc2626',
    gold: '#ffd700',
    goldDark: '#e6c200',
    success: '#10b981',
    info: '#3b82f6',
    warning: '#ffd700',
    danger: '#ef4444',
    secondary: '#6b7280'
};

// Fonction pour charger les données des méthodes de paiement avec gestion d'erreur améliorée
async function loadPaymentMethodsData() {
    try {
        const response = await fetch('{{ route("admin.api.payments.methods") }}');
        if (!response.ok) throw new Error('Erreur réseau');
        return await response.json();
    } catch (error) {
        console.error('Erreur lors du chargement des méthodes de paiement:', error);
        // Retourner des données vides si aucun paiement
        return {
            labels: ['Aucun paiement'],
            data: [1]
        };
    }
}

// Fonction pour charger les données des tendances de paiement
async function loadPaymentTrendsData() {
    try {
        const response = await fetch('{{ route("admin.api.payments.trends") }}');
        if (!response.ok) throw new Error('Erreur réseau');
        return await response.json();
    } catch (error) {
        console.error('Erreur lors du chargement des tendances de paiement:', error);
        // Retourner des données vides si aucun paiement
        return {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        };
    }
}

// Initialisation des graphiques
document.addEventListener('DOMContentLoaded', async function() {
    // Configuration responsive commune
    const responsiveOptions = {
        maintainAspectRatio: false,
        responsive: true,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            tooltip: {
                backgroundColor: colorPalette.primary,
                titleColor: '#fff',
                bodyColor: '#fff',
                cornerRadius: 8,
                padding: 12
            }
        }
    };

    // Graphique des ventes de tickets
    const ticketSalesChart = new Chart(document.getElementById('ticketSalesChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($ticketSalesData['labels']) !!},
            datasets: [{
                label: 'Tickets vendus',
                data: {!! json_encode($ticketSalesData['data']) !!},
                borderColor: colorPalette.primary,
                backgroundColor: `${colorPalette.primary}15`,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: colorPalette.gold,
                pointBorderColor: colorPalette.primary,
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            ...responsiveOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: '#f1f1f1'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                ...responsiveOptions.plugins,
                legend: {
                    display: false
                }
            }
        }
    });

    // Graphique de distribution par catégorie
    const categoryDistributionChart = new Chart(document.getElementById('categoryDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryDistributionData['labels']) !!},
            datasets: [{
                data: {!! json_encode($categoryDistributionData['data']) !!},
                backgroundColor: [
                    colorPalette.primary,
                    colorPalette.success,
                    colorPalette.info,
                    colorPalette.warning,
                    colorPalette.danger,
                    colorPalette.secondary
                ],
                hoverBackgroundColor: [
                    colorPalette.primaryLight,
                    '#17a673',
                    '#2c9faf',
                    '#dda20a',
                    '#be2617',
                    '#6e707e'
                ],
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            ...responsiveOptions,
            plugins: {
                ...responsiveOptions.plugins,
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });

    // Graphique des méthodes de paiement
    try {
        const paymentMethodsData = await loadPaymentMethodsData();
        
        // Définir les couleurs selon la méthode de paiement
        const getPaymentMethodColor = (label) => {
            const labelLower = label.toLowerCase();
            if (labelLower.includes('airtel')) {
                return '#dc3545'; // Rouge pour Airtel Money
            } else if (labelLower.includes('mtn')) {
                return '#ffc107'; // Jaune pour MTN Mobile Money
            } else {
                // Couleurs par défaut pour les autres méthodes
                return colorPalette.primary;
            }
        };
        
        const getPaymentMethodHoverColor = (label) => {
            const labelLower = label.toLowerCase();
            if (labelLower.includes('airtel')) {
                return '#c82333'; // Rouge foncé au survol
            } else if (labelLower.includes('mtn')) {
                return '#e0a800'; // Jaune foncé au survol
            } else {
                return colorPalette.primaryLight;
            }
        };
        
        const backgroundColor = paymentMethodsData.labels.map(label => getPaymentMethodColor(label));
        const hoverBackgroundColor = paymentMethodsData.labels.map(label => getPaymentMethodHoverColor(label));
        
        const paymentMethodsChart = new Chart(document.getElementById('paymentMethodsChart'), {
            type: 'doughnut',
            data: {
                labels: paymentMethodsData.labels,
                datasets: [{
                    data: paymentMethodsData.data,
                    backgroundColor: backgroundColor,
                    hoverBackgroundColor: hoverBackgroundColor,
                    borderWidth: 0,
                    hoverBorderWidth: 2,
                    hoverBorderColor: '#fff'
                }]
            },
            options: {
                ...responsiveOptions,
                plugins: {
                    ...responsiveOptions.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        ...responsiveOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${percentage}% (${value})`;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    } catch (error) {
        console.error('Erreur lors de l\'initialisation du graphique des méthodes de paiement:', error);
    }

    // Graphique des tendances de paiement
    try {
        const paymentTrendsData = await loadPaymentTrendsData();
        const paymentTrendsChart = new Chart(document.getElementById('paymentTrendsChart'), {
            type: 'line',
            data: {
                labels: paymentTrendsData.labels,
                datasets: [{
                    label: 'Montant des paiements (FCFA)',
                    data: paymentTrendsData.data,
                    borderColor: colorPalette.success,
                    backgroundColor: `${colorPalette.success}15`,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: colorPalette.gold,
                    pointBorderColor: colorPalette.success,
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                ...responsiveOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                            }
                        },
                        grid: {
                            color: '#f1f1f1'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    ...responsiveOptions.plugins,
                    legend: {
                        display: false
                    },
                    tooltip: {
                        ...responsiveOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Erreur lors de l\'initialisation du graphique des tendances de paiement:', error);
    }

    // Animation au défilement
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observer les cartes pour l'animation
    document.querySelectorAll('.stat-card, .chart-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease-out';
        observer.observe(card);
    });

    // Fonction JavaScript pour formater les nombres en format abrégé (K, M)
    function formatNumberAbbreviated(number, decimals = 1) {
        if (number <= 0) return '0';
        
        if (number >= 1000000) {
            const formatted = number / 1000000;
            if (Math.abs(formatted - Math.floor(formatted)) < 0.0001) {
                return Math.floor(formatted) + 'M';
            }
            return parseFloat(formatted.toFixed(decimals)).toString().replace(/\.?0+$/, '') + 'M';
        } else if (number >= 1000) {
            const formatted = number / 1000;
            if (Math.abs(formatted - Math.floor(formatted)) < 0.0001) {
                return Math.floor(formatted) + 'K';
            }
            return parseFloat(formatted.toFixed(decimals)).toString().replace(/\.?0+$/, '') + 'K';
        }
        
        return Math.floor(number).toString();
    }

    // Animation des nombres (compteur)
    function animateNumbers() {
        document.querySelectorAll('.stat-number').forEach(element => {
            const originalText = element.textContent.trim();
            
            // Si le texte contient déjà K ou M (avec ou sans espace après), ne pas animer (garder tel quel)
            // Exemples: "3.5M", "333.6K", "3.5M FCFA", "333.6K FCFA"
            if (originalText.match(/[\d.]+[KM]/i)) {
                return;
            }
            
            // Si c'est un pourcentage, extraire le nombre avant le %
            let isPercentage = false;
            let suffix = '';
            if (originalText.includes('%')) {
                isPercentage = true;
                suffix = '%';
            } else if (originalText.includes('FCFA')) {
                suffix = ' FCFA';
            }
            
            // Extraire le nombre final - gérer les nombres avec espaces/virgules
            const numberMatch = originalText.match(/[\d\s,\.]+/);
            if (!numberMatch) return;
            
            // Nettoyer le nombre (retirer espaces, virgules, mais garder le point pour les décimales)
            let numberStr = numberMatch[0].replace(/[\s,]/g, '');
            const finalNumber = parseFloat(numberStr) || 0;
            if (finalNumber === 0) return;

            let currentNumber = 0;
            const increment = finalNumber / 50; // 50 étapes d'animation
            const duration = 1500; // 1.5 secondes
            const stepTime = duration / 50;

            const timer = setInterval(() => {
                currentNumber += increment;
                if (currentNumber >= finalNumber) {
                    currentNumber = finalNumber;
                    clearInterval(timer);
                }
                
                // Formater le nombre selon le type
                let formattedNumber;
                if (isPercentage) {
                    formattedNumber = Math.floor(currentNumber).toLocaleString('fr-FR') + suffix;
                } else if (suffix === ' FCFA') {
                    // Pour les montants, utiliser le format abrégé
                    formattedNumber = formatNumberAbbreviated(Math.floor(currentNumber)) + suffix;
                } else {
                    // Pour les autres nombres, utiliser le format abrégé
                    formattedNumber = formatNumberAbbreviated(Math.floor(currentNumber));
                }
                
                element.textContent = formattedNumber;
            }, stepTime);
        });
    }

    // Démarrer l'animation des nombres après un court délai
    setTimeout(animateNumbers, 500);

    // Gestion responsive des graphiques
    function handleResize() {
        Object.values(Chart.instances).forEach(chart => {
            if (chart && typeof chart.resize === 'function') {
                chart.resize();
            }
        });
    }

    // Debounced resize handler
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(handleResize, 300);
    });

    // Mise à jour automatique des données (optionnel)
    setInterval(() => {
        // Ici vous pourriez recharger certaines données
        console.log('Vérification des mises à jour...');
    }, 300000); // Toutes les 5 minutes
});

// Fonction utilitaire pour formater les nombres
function formatNumber(num, locale = 'fr-FR') {
    return new Intl.NumberFormat(locale).format(num);
}

// Fonction utilitaire pour formater les devises
function formatCurrency(num, currency = 'FCFA', locale = 'fr-FR') {
    if (currency === 'FCFA') {
        return formatNumber(num) + ' ' + currency;
    }
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency
    }).format(num);
}

// Export des fonctions pour utilisation externe si nécessaire
window.dashboardUtils = {
    formatNumber,
    formatCurrency,
    colorPalette
};
</script>
@endpush