@extends('layouts.dashboard')

@section('title', 'Analytics & Rapports')

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

.analytics-page {
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
    background: white;
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-outline-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-900);
    transform: translateY(-1px);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8125rem;
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
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
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

.stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.stat-number {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.stat-subtitle {
    font-size: 0.75rem;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.stat-subtitle i {
    font-size: 0.75rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: var(--gray-100);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1.25rem;
    transition: all 0.2s;
}

.stat-card:hover .stat-icon {
    background: var(--primary);
    color: white;
}

.stat-gradient {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    opacity: 0.7;
}

.stat-gradient.bg-success { background: var(--success); }
.stat-gradient.bg-primary { background: var(--primary); }
.stat-gradient.bg-info { background: var(--info); }
.stat-gradient.bg-warning { background: var(--warning); }

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

/* Filters Section */
.filters-section {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
    margin-bottom: 1.5rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.form-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
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

/* Table */
.table-wrapper {
    overflow-x: auto;
}

.analytics-table {
    width: 100%;
    border-collapse: collapse;
}

.analytics-table thead {
    background: var(--gray-50);
}

.analytics-table thead th {
    padding: 0.875rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-600);
    border-bottom: 2px solid var(--gray-200);
    white-space: nowrap;
    background: var(--gray-50);
}

.analytics-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.analytics-table tbody tr:hover {
    background: var(--gray-50);
}

.analytics-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

/* Event Image */
.event-image-wrapper {
    width: 50px;
    height: 50px;
    flex-shrink: 0;
}

.event-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0.5rem;
    border: 1px solid var(--gray-200);
}

.event-image-placeholder {
    width: 100%;
    height: 100%;
    background: var(--gray-100);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    border: 1px dashed var(--gray-300);
}

/* Rank Badge */
.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 9999px;
    font-weight: 700;
    font-size: 0.875rem;
}

.rank-badge.first {
    background: linear-gradient(135deg, #ffd700 0%, #fbbf24 100%);
    color: var(--gray-900);
}

.rank-badge.second {
    background: linear-gradient(135deg, #c0c0c0 0%, #9ca3af 100%);
    color: var(--gray-900);
}

.rank-badge.third {
    background: linear-gradient(135deg, #cd7f32 0%, #b45309 100%);
    color: white;
}

.rank-badge.default {
    background: var(--gray-300);
    color: var(--gray-700);
}

/* Progress Bar */
.progress {
    display: flex;
    height: 8px;
    width: 100px;
    background: var(--gray-200);
    border-radius: 9999px;
    overflow: hidden;
}

.progress-bar {
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: white;
    text-align: center;
    white-space: nowrap;
    transition: width 0.6s ease;
}

.progress-bar.bg-success { background: var(--success); }
.progress-bar.bg-warning { background: var(--warning); }
.progress-bar.bg-danger { background: var(--danger); }

/* Chart Containers */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-container-sm {
    position: relative;
    height: 250px;
    width: 100%;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    font-size: 3rem;
    color: var(--gray-400);
    margin-bottom: 1rem;
}

.empty-text {
    color: var(--gray-600);
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.empty-description {
    color: var(--gray-500);
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .analytics-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .chart-container,
    .chart-container-sm {
        height: 250px;
    }
    
    .event-image-wrapper {
        width: 40px;
        height: 40px;
    }
}

/* Utilities */
.d-flex { display: flex; }
.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }
.justify-content-center { justify-content: center; }
.justify-content-end { justify-content: flex-end; }
.flex-wrap { flex-wrap: wrap; }
.flex-column { flex-direction: column; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 1rem; }
.mb-0 { margin-bottom: 0; }
.mb-1 { margin-bottom: 0.25rem; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-4 { margin-bottom: 1.5rem; }
.mb-5 { margin-bottom: 2rem; }
.mt-1 { margin-top: 0.25rem; }
.me-1 { margin-right: 0.25rem; }
.me-2 { margin-right: 0.5rem; }
.me-3 { margin-right: 1rem; }
.ms-auto { margin-left: auto; }

.w-100 { width: 100%; }
.h-100 { height: 100%; }

.text-center { text-align: center; }
.text-muted { color: var(--gray-500) !important; }
.text-success { color: var(--success) !important; }
.text-info { color: var(--info) !important; }
.text-warning { color: var(--warning) !important; }
.text-primary { color: var(--primary) !important; }
.text-gray-800 { color: var(--gray-800) !important; }
.text-gray-700 { color: var(--gray-700) !important; }

.fw-semibold { font-weight: 600; }
.fw-bold { font-weight: 700; }
.small { font-size: 0.75rem; }

.bg-light { background: var(--gray-100); }
.border-0 { border: none; }
.shadow-sm { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
.overflow-hidden { overflow: hidden; }
.position-relative { position: relative; }
</style>
@endpush

@section('content')
<div class="analytics-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-chart-bar"></i>
                Analytics & Rapports
            </h1>
            <p>Analysez les performances de vos événements</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-success" onclick="exportReport()">
                <i class="fas fa-download"></i>
                Exporter le rapport
            </button>
            <button class="btn btn-secondary" onclick="refreshAnalytics()">
                <i class="fas fa-sync-alt"></i>
                Actualiser
            </button>
            <a href="{{ route('organizer.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Filtres de période -->
    <div class="filters-section">
        <form method="GET" action="{{ route('organizer.analytics.index') }}" id="analyticsForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="period" class="filter-label">Période</label>
                    <select class="form-select" id="period" name="period">
                        <option value="7" {{ request('period', '30') == '7' ? 'selected' : '' }}>7 derniers jours</option>
                        <option value="30" {{ request('period', '30') == '30' ? 'selected' : '' }}>30 derniers jours</option>
                        <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>3 derniers mois</option>
                        <option value="365" {{ request('period') == '365' ? 'selected' : '' }}>12 derniers mois</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Période personnalisée</option>
                    </select>
                </div>
                <div class="filter-group" id="customDateFrom" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                    <label for="date_from" class="filter-label">Date de début</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group" id="customDateTo" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                    <label for="date_to" class="filter-label">Date de fin</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="filter-group">
                    <label for="event_id" class="filter-label">Événement</label>
                    <select class="form-select" id="event_id" name="event_id">
                        <option value="">Tous les événements</option>
                        @foreach($events ?? [] as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title ?? $event->name ?? 'Sans titre' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group" style="display: flex; flex-direction: row; align-items: flex-end; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-search"></i>
                        Analyser
                    </button>
                    @if(request()->anyFilled(['period', 'date_from', 'date_to', 'event_id']))
                    <a href="{{ route('organizer.analytics.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Métriques clés -->
    <div class="stats-grid">
        <!-- Revenus totaux -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Revenus totaux</div>
                    <div class="stat-number">{{ number_format($metrics['total_revenue'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-arrow-{{ ($metrics['revenue_change'] ?? 0) >= 0 ? 'up' : 'down' }} text-{{ ($metrics['revenue_change'] ?? 0) >= 0 ? 'success' : 'danger' }}"></i>
                        {{ abs($metrics['revenue_change'] ?? 0) }}% vs période précédente
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <div class="stat-gradient bg-success"></div>
        </div>

        <!-- Tickets vendus -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Tickets vendus</div>
                    <div class="stat-number">{{ number_format($metrics['tickets_sold'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-arrow-{{ ($metrics['tickets_change'] ?? 0) >= 0 ? 'up' : 'down' }} text-{{ ($metrics['tickets_change'] ?? 0) >= 0 ? 'success' : 'danger' }}"></i>
                        {{ abs($metrics['tickets_change'] ?? 0) }}% vs période précédente
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
            <div class="stat-gradient bg-primary"></div>
        </div>

        <!-- Nouveaux clients -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Nouveaux clients</div>
                    <div class="stat-number">{{ number_format($metrics['new_customers'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-arrow-{{ ($metrics['customers_change'] ?? 0) >= 0 ? 'up' : 'down' }} text-{{ ($metrics['customers_change'] ?? 0) >= 0 ? 'success' : 'danger' }}"></i>
                        {{ abs($metrics['customers_change'] ?? 0) }}% vs période précédente
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-gradient bg-info"></div>
        </div>

        <!-- Taux de conversion -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Taux de conversion</div>
                    <div class="stat-number">{{ number_format($metrics['conversion_rate'] ?? 0, 1) }}<small style="font-size: 1rem;">%</small></div>
                    <div class="stat-subtitle">
                        <i class="fas fa-arrow-{{ ($metrics['conversion_change'] ?? 0) >= 0 ? 'up' : 'down' }} text-{{ ($metrics['conversion_change'] ?? 0) >= 0 ? 'success' : 'danger' }}"></i>
                        {{ abs($metrics['conversion_change'] ?? 0) }}% vs période précédente
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
            <div class="stat-gradient bg-warning"></div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="row g-4 mb-5">
        <!-- Évolution des revenus -->
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Évolution des revenus
                    </h5>
                    <span class="small text-muted">Revenus par jour sur la période sélectionnée</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition des événements -->
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie"></i>
                        Performance par événement
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="eventsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques secondaires -->
    <div class="row g-4 mb-5">
        <!-- Ventes par heure -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-clock"></i>
                        Ventes par heure de la journée
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container-sm">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canaux de vente -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-funnel-dollar"></i>
                        Canaux de vente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container-sm">
                        <canvas id="channelsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des événements les plus performants -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-trophy"></i>
                        Événements les plus performants
                    </h5>
                    <span class="small text-muted">Classement par revenus générés</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-wrapper">
                        <table class="analytics-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px; text-align: center;">#</th>
                                    <th>Événement</th>
                                    <th style="text-align: center;">Tickets vendus</th>
                                    <th style="text-align: center;">Revenus</th>
                                    <th style="text-align: center;">Taux de conversion</th>
                                    <th style="text-align: center;">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topEvents ?? [] as $index => $event)
                                <tr>
                                    <td style="text-align: center;">
                                        <span class="rank-badge {{ $index == 0 ? 'first' : ($index == 1 ? 'second' : ($index == 2 ? 'third' : 'default')) }}">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="event-image-wrapper">
                                                @if($event->image)
                                                <img src="{{ asset('storage/'.$event->image) }}" 
                                                     class="event-image" 
                                                     alt="{{ $event->title }}">
                                                @else
                                                <div class="event-image-placeholder">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ Str::limit($event->title, 40) }}</div>
                                                <small class="text-muted">{{ $event->start_date ? $event->start_date->format('d/m/Y') : '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="fw-bold">{{ number_format($event->tickets_sold ?? 0, 0, ',', ' ') }}</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="fw-bold text-success">{{ number_format($event->revenue ?? 0, 0, ',', ' ') }}</span>
                                        <span style="font-size: 0.75rem; color: var(--gray-500);">FCFA</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge" style="background: #dbeafe; color: #1e40af; border: 1px solid #3b82f6; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                            {{ number_format($event->conversion_rate ?? 0, 1) }}%
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="progress">
                                                <div class="progress-bar bg-{{ ($event->performance ?? 0) >= 80 ? 'success' : (($event->performance ?? 0) >= 60 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ min($event->performance ?? 0, 100) }}%"></div>
                                            </div>
                                            <small class="text-muted mt-1">{{ number_format($event->performance ?? 0, 0) }}%</small>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-chart-bar"></i>
                                            </div>
                                            <p class="empty-text">Aucune donnée disponible</p>
                                            <p class="empty-description">Les données d'analytics apparaîtront ici</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===========================================
    // ANIMATION DES COMPTEURS
    // ===========================================
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const text = counter.textContent.replace(/\s/g, '').replace('%', '');
            const target = parseFloat(text) || 0;
            
            if (target === 0) return;
            
            const isPercentage = counter.textContent.includes('%');
            const increment = target / 30;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                if (isPercentage) {
                    counter.innerHTML = current.toFixed(1) + '<small style="font-size: 1rem;">%</small>';
                } else {
                    counter.textContent = Math.floor(current).toLocaleString('fr-FR');
                }
            }, 30);
        });
    }

    animateCounters();

    // ===========================================
    // GESTION DES DATES PERSONNALISÉES
    // ===========================================
    function updateDateRange() {
        const period = document.getElementById('period').value;
        const customFrom = document.getElementById('customDateFrom');
        const customTo = document.getElementById('customDateTo');
        
        if (period === 'custom') {
            customFrom.style.display = 'block';
            customTo.style.display = 'block';
        } else {
            customFrom.style.display = 'none';
            customTo.style.display = 'none';
        }
    }

    window.updateDateRange = updateDateRange;
    
    // Initialiser l'affichage des dates personnalisées
    updateDateRange();
    
    // Ajouter l'écouteur d'événement
    document.getElementById('period').addEventListener('change', updateDateRange);

    // ===========================================
    // VALIDATION DES DATES
    // ===========================================
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    if (dateFrom && dateTo) {
        dateFrom.addEventListener('change', function() {
            if (this.value && dateTo.value) {
                if (dateTo.value < this.value) {
                    dateTo.value = this.value;
                }
            }
        });
    }

    // ===========================================
    // GRAPHIQUE DES REVENUS
    // ===========================================
    @php
        $revenueLabels = $revenueLabels ?? ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $revenueData = $revenueData ?? [0, 0, 0, 0, 0, 0, 0];
    @endphp
    
    const revenueLabels = @json($revenueLabels);
    const revenueData = @json($revenueData);

    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Revenus (FCFA)',
                    data: revenueData,
                    borderColor: '#0f1a3d',
                    backgroundColor: 'rgba(15, 26, 61, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0f1a3d',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'white',
                        titleColor: '#111827',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString('fr-FR') + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('fr-FR') + ' FCFA';
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ===========================================
    // GRAPHIQUE DES ÉVÉNEMENTS
    // ===========================================
    @php
        $eventsLabels = $eventsLabels ?? ['Aucune donnée'];
        $eventsData = $eventsData ?? [1];
    @endphp
    
    const eventsLabels = @json($eventsLabels);
    const eventsData = @json($eventsData);
    
    const eventsCtx = document.getElementById('eventsChart');
    if (eventsCtx && eventsLabels.length > 0 && eventsData.length > 0) {
        new Chart(eventsCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: eventsLabels,
                datasets: [{
                    data: eventsData,
                    backgroundColor: [
                        '#0f1a3d',
                        '#1a237e',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#3b82f6',
                        '#8b5cf6',
                        '#ec4899'
                    ],
                    borderWidth: 0,
                    borderRadius: 4,
                    cutout: '65%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'white',
                        titleColor: '#111827',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ===========================================
    // GRAPHIQUE DES VENTES PAR HEURE
    // ===========================================
    @php
        $hourlyLabels = $hourlyLabels ?? ['0h', '4h', '8h', '12h', '16h', '20h'];
        $hourlyData = $hourlyData ?? [0, 0, 0, 0, 0, 0];
    @endphp
    
    const hourlyLabels = @json($hourlyLabels);
    const hourlyData = @json($hourlyData);
    
    const hourlyCtx = document.getElementById('hourlyChart');
    if (hourlyCtx) {
        new Chart(hourlyCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{
                    label: 'Ventes',
                    data: hourlyData,
                    backgroundColor: 'rgba(15, 26, 61, 0.8)',
                    borderRadius: 8,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'white',
                        titleColor: '#111827',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ===========================================
    // GRAPHIQUE DES CANAUX DE VENTE
    // ===========================================
    @php
        $channelsLabels = $channelsLabels ?? ['Aucune donnée'];
        $channelsData = $channelsData ?? [1];
    @endphp
    
    const channelsLabels = @json($channelsLabels);
    const channelsData = @json($channelsData);
    
    const channelsCtx = document.getElementById('channelsChart');
    if (channelsCtx && channelsLabels.length > 0 && channelsData.length > 0) {
        new Chart(channelsCtx.getContext('2d'), {
            type: 'pie',
            data: {
                labels: channelsLabels,
                datasets: [{
                    data: channelsData,
                    backgroundColor: ['#0f1a3d', '#10b981', '#f59e0b', '#3b82f6'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'white',
                        titleColor: '#111827',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
});

// ===========================================
// FONCTIONS GLOBALES
// ===========================================

function exportReport() {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("organizer.analytics.export") }}';
    form.style.display = 'none';
    
    const params = new URLSearchParams(window.location.search);
    params.forEach((value, key) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function refreshAnalytics() {
    location.reload();
}
</script>
@endpush