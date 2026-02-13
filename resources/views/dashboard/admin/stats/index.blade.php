@extends('layouts.dashboard')

@section('title', 'Statistiques')

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

.stats-page {
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

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
}

.stat-number small {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-500);
    margin-left: 0.25rem;
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

.stat-card.primary .stat-icon { color: var(--primary); }
.stat-card.success .stat-icon { color: var(--success); }
.stat-card.info .stat-icon { color: var(--info); }
.stat-card.warning .stat-icon { color: var(--warning); }

.stat-card.primary:hover .stat-icon { background: var(--primary); color: white; }
.stat-card.success:hover .stat-icon { background: var(--success); color: white; }
.stat-card.info:hover .stat-icon { background: var(--info); color: white; }
.stat-card.warning:hover .stat-icon { background: var(--warning); color: white; }

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

/* Chart Grid */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 1024px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}

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

.chart-container-lg {
    position: relative;
    height: 350px;
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
    .stats-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .chart-container,
    .chart-container-sm,
    .chart-container-lg {
        height: 250px;
    }
}

/* Row and Col utilities */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -0.75rem;
}

.col-xl-3,
.col-xl-6,
.col-md-6 {
    padding: 0 0.75rem;
    box-sizing: border-box;
}

.col-xl-3 {
    width: 25%;
}

.col-xl-6 {
    width: 50%;
}

.col-md-6 {
    width: 50%;
}

@media (max-width: 1200px) {
    .col-xl-3 {
        width: 50%;
    }
}

@media (max-width: 768px) {
    .col-xl-3,
    .col-xl-6,
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

.mb-4 {
    margin-bottom: 1.5rem;
}

.mt-4 {
    margin-top: 1.5rem;
}

/* Text utilities */
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.text-muted {
    color: var(--gray-500) !important;
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
</style>
@endpush

@section('content')
<div class="stats-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Statistiques</h1>
            <p>Analyse complète des performances de la plateforme</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Utilisateurs</div>
                    <div class="stat-number">{{ number_format($stats['users_count'] ?? 0, 0, ',', ' ') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Événements</div>
                    <div class="stat-number">{{ number_format($stats['events_count'] ?? 0, 0, ',', ' ') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card info">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Tickets vendus</div>
                    <div class="stat-number">{{ number_format($stats['tickets_sold'] ?? 0, 0, ',', ' ') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Revenus</div>
                    <div class="stat-number">{{ number_format($stats['revenue'] ?? 0, 0, ',', ' ') }} <small>FCFA</small></div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des utilisateurs -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-users"></i>
                Statistiques des utilisateurs
            </h5>
            @if(!empty($userStats['labels']) && !empty($userStats['data']))
                @php
                    // Conversion sécurisée de Collection en array si nécessaire
                    $userData = $userStats['data'];
                    if ($userData instanceof \Illuminate\Support\Collection) {
                        $userData = $userData->toArray();
                    }
                    $totalNewUsers = is_array($userData) ? array_sum($userData) : 0;
                @endphp
                <span style="font-size: 0.875rem; color: var(--gray-600);">
                    {{ number_format($totalNewUsers, 0, ',', ' ') }} nouveaux utilisateurs
                </span>
            @endif
        </div>
        <div class="card-body">
            @if(!empty($userStats['labels']) && !empty($userStats['data']))
            <div class="chart-container">
                <canvas id="userStatsChart"></canvas>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <p class="empty-text">Aucune donnée disponible</p>
                <p class="empty-description">Les statistiques utilisateurs apparaîtront ici</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Statistiques des événements -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-calendar-alt"></i>
                Statistiques des événements
            </h5>
        </div>
        <div class="card-body">
            <div class="charts-grid">
                @if(!empty($eventStats['category_labels']) && !empty($eventStats['category_data']))
                <div>
                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: var(--gray-700);">Répartition par catégorie</span>
                    </div>
                    <div class="chart-container-sm">
                        <canvas id="eventCategoryChart"></canvas>
                    </div>
                </div>
                @else
                <div>
                    <div class="empty-state" style="padding: 2rem 1rem;">
                        <div class="empty-icon" style="font-size: 2rem;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <p class="empty-text">Aucune catégorie</p>
                    </div>
                </div>
                @endif

                @if(!empty($eventStats['month_labels']) && !empty($eventStats['month_data']))
                <div>
                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: var(--gray-700);">Évolution mensuelle</span>
                    </div>
                    <div class="chart-container-sm">
                        <canvas id="eventMonthlyChart"></canvas>
                    </div>
                </div>
                @else
                <div>
                    <div class="empty-state" style="padding: 2rem 1rem;">
                        <div class="empty-icon" style="font-size: 2rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <p class="empty-text">Aucune évolution</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistiques des paiements -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-credit-card"></i>
                Statistiques des paiements
            </h5>
        </div>
        <div class="card-body">
            <div class="charts-grid">
                @if(!empty($paymentStats['method_labels']) && !empty($paymentStats['method_data']))
                <div>
                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: var(--gray-700);">Répartition par méthode</span>
                    </div>
                    <div class="chart-container-sm">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
                @else
                <div>
                    <div class="empty-state" style="padding: 2rem 1rem;">
                        <div class="empty-icon" style="font-size: 2rem;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <p class="empty-text">Aucune méthode</p>
                    </div>
                </div>
                @endif

                @if(!empty($paymentStats['month_labels']) && !empty($paymentStats['month_data']))
                <div>
                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: var(--gray-700);">Évolution mensuelle</span>
                    </div>
                    <div class="chart-container-sm">
                        <canvas id="paymentMonthlyChart"></canvas>
                    </div>
                </div>
                @else
                <div>
                    <div class="empty-state" style="padding: 2rem 1rem;">
                        <div class="empty-icon" style="font-size: 2rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <p class="empty-text">Aucune évolution</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistiques des tickets -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-ticket-alt"></i>
                Statistiques des tickets
            </h5>
        </div>
        <div class="card-body">
            <div class="charts-grid">
                @if(!empty($ticketStats['event_labels']) && !empty($ticketStats['event_data']))
                <div>
                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: var(--gray-700);">Top événements</span>
                    </div>
                    <div class="chart-container-sm">
                        <canvas id="ticketEventChart"></canvas>
                    </div>
                </div>
                @else
                <div>
                    <div class="empty-state" style="padding: 2rem 1rem;">
                        <div class="empty-icon" style="font-size: 2rem;">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <p class="empty-text">Aucun événement</p>
                    </div>
                </div>
                @endif

                @if(!empty($ticketStats['day_labels']) && !empty($ticketStats['day_data']))
                <div>
                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: var(--gray-700);">Ventes quotidiennes</span>
                    </div>
                    <div class="chart-container-sm">
                        <canvas id="ticketDailyChart"></canvas>
                    </div>
                </div>
                @else
                <div>
                    <div class="empty-state" style="padding: 2rem 1rem;">
                        <div class="empty-icon" style="font-size: 2rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <p class="empty-text">Aucune vente</p>
                    </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Configuration globale de Chart.js
    Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#4b5563';

    const colorPalette = {
        primary: '#0f1a3d',
        primaryLight: '#1a237e',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        info: '#3b82f6',
        purple: '#8b5cf6',
        pink: '#ec4899',
        teal: '#14b8a6',
        orange: '#f97316',
        gray: '#6b7280'
    };

    // ===========================================
    // GRAPHIQUE UTILISATEURS
    // ===========================================
    @if(!empty($userStats['labels']) && !empty($userStats['data']))
    const userStatsCtx = document.getElementById('userStatsChart');
    if (userStatsCtx) {
        new Chart(userStatsCtx, {
            type: 'line',
            data: {
                labels: @json($userStats['labels'] ?? []),
                datasets: [{
                    label: 'Nouveaux utilisateurs',
                    data: @json($userStats['data'] ?? []),
                    borderColor: colorPalette.primary,
                    backgroundColor: 'rgba(15, 26, 61, 0.05)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: colorPalette.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: { size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'white',
                        titleColor: '#111827',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        titleFont: { weight: '600', size: 13 },
                        bodyFont: { size: 12 }
                    }
                }
            }
        });
    }
    @endif

    // ===========================================
    // GRAPHIQUE ÉVÉNEMENTS - CATÉGORIES
    // ===========================================
    @if(!empty($eventStats['category_labels']) && !empty($eventStats['category_data']))
    const eventCategoryCtx = document.getElementById('eventCategoryChart');
    if (eventCategoryCtx) {
        new Chart(eventCategoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json($eventStats['category_labels'] ?? []),
                datasets: [{
                    data: @json($eventStats['category_data'] ?? []),
                    backgroundColor: [
                        colorPalette.primary,
                        colorPalette.success,
                        colorPalette.info,
                        colorPalette.warning,
                        colorPalette.purple,
                        colorPalette.pink,
                        colorPalette.teal,
                        colorPalette.orange
                    ],
                    hoverBackgroundColor: [
                        colorPalette.primaryLight,
                        '#059669',
                        '#2563eb',
                        '#d97706',
                        '#7c3aed',
                        '#db2777',
                        '#0d9488',
                        '#ea580c'
                    ],
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                cutout: '70%',
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
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // ===========================================
    // GRAPHIQUE ÉVÉNEMENTS - MENSUEL
    // ===========================================
    @if(!empty($eventStats['month_labels']) && !empty($eventStats['month_data']))
    const eventMonthlyCtx = document.getElementById('eventMonthlyChart');
    if (eventMonthlyCtx) {
        new Chart(eventMonthlyCtx, {
            type: 'line',
            data: {
                labels: @json($eventStats['month_labels'] ?? []),
                datasets: [{
                    label: 'Nouveaux événements',
                    data: @json($eventStats['month_data'] ?? []),
                    borderColor: colorPalette.success,
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: colorPalette.success,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: { size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                },
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
                }
            }
        });
    }
    @endif

    // ===========================================
    // GRAPHIQUE PAIEMENTS - MÉTHODES
    // ===========================================
    @if(!empty($paymentStats['method_labels']) && !empty($paymentStats['method_data']))
    const paymentMethodCtx = document.getElementById('paymentMethodChart');
    if (paymentMethodCtx) {
        new Chart(paymentMethodCtx, {
            type: 'doughnut',
            data: {
                labels: @json($paymentStats['method_labels'] ?? []),
                datasets: [{
                    data: @json($paymentStats['method_data'] ?? []),
                    backgroundColor: [
                        colorPalette.primary,
                        colorPalette.warning,
                        colorPalette.success,
                        colorPalette.info,
                        colorPalette.purple
                    ],
                    hoverBackgroundColor: [
                        colorPalette.primaryLight,
                        '#d97706',
                        '#059669',
                        '#2563eb',
                        '#7c3aed'
                    ],
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                cutout: '70%',
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
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // ===========================================
    // GRAPHIQUE PAIEMENTS - MENSUEL
    // ===========================================
    @if(!empty($paymentStats['month_labels']) && !empty($paymentStats['month_data']))
    const paymentMonthlyCtx = document.getElementById('paymentMonthlyChart');
    if (paymentMonthlyCtx) {
        new Chart(paymentMonthlyCtx, {
            type: 'line',
            data: {
                labels: @json($paymentStats['month_labels'] ?? []),
                datasets: [{
                    label: 'Montant des paiements (FCFA)',
                    data: @json($paymentStats['month_data'] ?? []),
                    borderColor: colorPalette.info,
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: colorPalette.info,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('fr-FR') + ' FCFA';
                            },
                            font: { size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                },
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
                }
            }
        });
    }
    @endif

    // ===========================================
    // GRAPHIQUE TICKETS - ÉVÉNEMENTS
    // ===========================================
    @if(!empty($ticketStats['event_labels']) && !empty($ticketStats['event_data']))
    const ticketEventCtx = document.getElementById('ticketEventChart');
    if (ticketEventCtx) {
        new Chart(ticketEventCtx, {
            type: 'bar',
            data: {
                labels: @json($ticketStats['event_labels'] ?? []),
                datasets: [{
                    label: 'Tickets vendus',
                    data: @json($ticketStats['event_data'] ?? []),
                    backgroundColor: colorPalette.primary,
                    borderRadius: 6,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: { size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11 },
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
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
                }
            }
        });
    }
    @endif

    // ===========================================
    // GRAPHIQUE TICKETS - QUOTIDIEN
    // ===========================================
    @if(!empty($ticketStats['day_labels']) && !empty($ticketStats['day_data']))
    const ticketDailyCtx = document.getElementById('ticketDailyChart');
    if (ticketDailyCtx) {
        new Chart(ticketDailyCtx, {
            type: 'line',
            data: {
                labels: @json($ticketStats['day_labels'] ?? []),
                datasets: [{
                    label: 'Tickets vendus',
                    data: @json($ticketStats['day_data'] ?? []),
                    borderColor: colorPalette.warning,
                    backgroundColor: 'rgba(245, 158, 11, 0.05)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: colorPalette.warning,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: { size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                },
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
                }
            }
        });
    }
    @endif
});
</script>
@endpush