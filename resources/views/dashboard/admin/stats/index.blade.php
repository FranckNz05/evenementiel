@extends('layouts.dashboard')

@section('title', 'Statistiques Globales')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Statistiques Globales</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item active">Analytique</li>
                    <li class="breadcrumb-item active">Statistiques</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light shadow-sm border">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Content Row - Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Utilisateurs -->
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-light text-primary p-3 rounded-3 me-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted small text-uppercase fw-bold">Utilisateurs</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ format_number($stats['users_count'] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Événements -->
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-light text-success p-3 rounded-3 me-3">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted small text-uppercase fw-bold">Événements</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ format_number($stats['events_count'] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets vendus -->
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info-light text-info p-3 rounded-3 me-3">
                            <i class="fas fa-ticket-alt fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted small text-uppercase fw-bold">Tickets vendus</h6>
                            <h3 class="card-title mb-0 fw-bold">{{ format_number($stats['tickets_sold'] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenus -->
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-light text-warning p-3 rounded-3 me-3">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted small text-uppercase fw-bold">Revenus</h6>
                            <h3 class="card-title mb-0 fw-bold text-nowrap">{{ format_number($stats['revenue'] ?? 0) }} FCFA</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Graphique des utilisateurs -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card modern-card h-100 shadow-sm">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-chart-line me-2"></i>Croissance des utilisateurs</h6>
                </div>
                <div class="card-body p-4">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="userStatsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique des catégories d'événements -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card modern-card h-100 shadow-sm">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-chart-pie me-2"></i>Répartition par Catégorie</h6>
                </div>
                <div class="card-body p-4">
                    <div class="chart-pie pt-4 pb-2" style="height: 320px;">
                        <canvas id="eventCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques des événements -->
        <div class="col-xl-6 mb-4">
            <div class="card modern-card h-100 shadow-sm">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-calendar-plus me-2"></i>Événements créés par mois</h6>
                </div>
                <div class="card-body p-4">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="eventMonthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques des paiements -->
        <div class="col-xl-6 mb-4">
            <div class="card modern-card h-100 shadow-sm">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-wallet me-2"></i>Répartition des Paiements (Méthodes)</h6>
                </div>
                <div class="card-body p-4">
                    <div class="chart-pie" style="height: 300px;">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Évolution des revenus -->
        <div class="col-12 mb-4">
            <div class="card modern-card shadow-sm">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-chart-area me-2"></i>Évolution mensuelle des revenus</h6>
                </div>
                <div class="card-body p-4">
                    <div class="chart-area" style="height: 350px;">
                        <canvas id="paymentMonthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques des tickets -->
        <div class="col-xl-6 mb-4">
            <div class="card modern-card h-100 shadow-sm">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-ticket-alt me-2"></i>Ventes par événement top</h6>
                </div>
                <div class="card-body p-4">
                    <div class="chart-bar" style="height: 300px;">
                        <canvas id="ticketEventChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card modern-card h-100 shadow-sm">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-history me-2"></i>Tickets vendus par jour (30j)</h6>
                </div>
                <div class="card-body p-4">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="ticketDailyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared chart options
    const commonOptions = {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: { family: 'Inter, sans-serif', size: 12 }
                }
            }
        }
    };

    // Graphique des utilisateurs par mois
    new Chart(document.getElementById('userStatsChart'), {
        type: 'line',
        data: {
            labels: @json($userStats['labels'] ?? []),
            datasets: [{
                label: 'Nouveaux utilisateurs',
                data: @json($userStats['data'] ?? []),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // Graphique des événements par catégorie
    new Chart(document.getElementById('eventCategoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($eventStats['category_labels'] ?? []),
            datasets: [{
                data: @json($eventStats['category_data'] ?? []),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                hoverOffset: 15,
                borderWidth: 0
            }]
        },
        options: {
            ...commonOptions,
            cutout: '75%'
        }
    });

    // Graphique des événements par mois
    new Chart(document.getElementById('eventMonthlyChart'), {
        type: 'line',
        data: {
            labels: @json($eventStats['month_labels'] ?? []),
            datasets: [{
                label: 'Nouveaux événements',
                data: @json($eventStats['month_data'] ?? []),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // Graphique des paiements par méthode
    new Chart(document.getElementById('paymentMethodChart'), {
        type: 'doughnut',
        data: {
            labels: @json($paymentStats['method_labels'] ?? []),
            datasets: [{
                data: @json($paymentStats['method_data'] ?? []),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                borderWidth: 0
            }]
        },
        options: {
            ...commonOptions,
            cutout: '70%'
        }
    });

    // Graphique des paiements par mois
    new Chart(document.getElementById('paymentMonthlyChart'), {
        type: 'line',
        data: {
            labels: @json($paymentStats['month_labels'] ?? []),
            datasets: [{
                label: 'Montant des paiements (FCFA)',
                data: @json($paymentStats['month_data'] ?? []),
                borderColor: '#36b9cc',
                backgroundColor: 'rgba(54, 185, 204, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return value.toLocaleString() + ' FCFA'; }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) { return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' FCFA'; }
                    }
                }
            }
        }
    });

    // Graphique des tickets par événement
    new Chart(document.getElementById('ticketEventChart'), {
        type: 'bar',
        data: {
            labels: @json($ticketStats['event_labels'] ?? []),
            datasets: [{
                label: 'Tickets vendus',
                data: @json($ticketStats['event_data'] ?? []),
                backgroundColor: '#4e73df',
                borderRadius: 5
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // Graphique des tickets par jour
    new Chart(document.getElementById('ticketDailyChart'), {
        type: 'line',
        data: {
            labels: @json($ticketStats['day_labels'] ?? []),
            datasets: [{
                label: 'Tickets vendus',
                data: @json($ticketStats['day_data'] ?? []),
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
});
</script>
@endpush
