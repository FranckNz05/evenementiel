@extends('layouts.dashboard')

@section('title', 'Statistiques')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Statistiques</h1>
    <a href="{{ route('admin.dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour au tableau de bord
    </a>
</div>

<!-- Content Row - Stats Cards -->
<div class="row">
    <!-- Utilisateurs -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body-modern">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Utilisateurs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ format_number($stats['users_count'] ?? 0) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Événements -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body-modern">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Événements</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ format_number($stats['events_count'] ?? 0) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets vendus -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body-modern">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Tickets vendus</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ format_number($stats['tickets_sold'] ?? 0) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenus -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body-modern">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Revenus</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ format_number($stats['revenue'] ?? 0) }} FCFA</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques des utilisateurs -->
<div class="modern-card">
    <div class="card-header-modern">
        <h6 class="m-0 font-weight-bold text-primary">Statistiques des utilisateurs</h6>
    </div>
    <div class="card-body-modern">
        <div class="chart-area">
            <canvas id="userStatsChart"></canvas>
        </div>
    </div>
</div>

<!-- Statistiques des événements -->
<div class="modern-card">
    <div class="card-header-modern">
        <h6 class="m-0 font-weight-bold text-primary">Statistiques des événements</h6>
    </div>
    <div class="card-body-modern">
        <div class="row">
            <div class="col-xl-6">
                <div class="chart-pie mb-4">
                    <canvas id="eventCategoryChart"></canvas>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="chart-area">
                    <canvas id="eventMonthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques des paiements -->
<div class="modern-card">
    <div class="card-header-modern">
        <h6 class="m-0 font-weight-bold text-primary">Statistiques des paiements</h6>
    </div>
    <div class="card-body-modern">
        <div class="row">
            <div class="col-xl-6">
                <div class="chart-pie mb-4">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="chart-area">
                    <canvas id="paymentMonthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques des tickets -->
<div class="modern-card">
    <div class="card-header-modern">
        <h6 class="m-0 font-weight-bold text-primary">Statistiques des tickets</h6>
    </div>
    <div class="card-body-modern">
        <div class="row">
            <div class="col-xl-6">
                <div class="chart-bar mb-4">
                    <canvas id="ticketEventChart"></canvas>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="chart-area">
                    <canvas id="ticketDailyChart"></canvas>
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
    // Graphique des utilisateurs par mois
    const userStatsChart = new Chart(document.getElementById('userStatsChart'), {
        type: 'line',
        data: {
            labels: @json($userStats['labels'] ?? []),
            datasets: [{
                label: 'Nouveaux utilisateurs',
                data: @json($userStats['data'] ?? []),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });

    // Graphique des événements par catégorie
    const eventCategoryChart = new Chart(document.getElementById('eventCategoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($eventStats['category_labels'] ?? []),
            datasets: [{
                data: @json($eventStats['category_data'] ?? []),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#6e707e'],
                hoverBorderColor: 'rgba(234, 236, 244, 1)',
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            cutout: '70%'
        }
    });

    // Graphique des événements par mois
    const eventMonthlyChart = new Chart(document.getElementById('eventMonthlyChart'), {
        type: 'line',
        data: {
            labels: @json($eventStats['month_labels'] ?? []),
            datasets: [{
                label: 'Nouveaux événements',
                data: @json($eventStats['month_data'] ?? []),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.05)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });

    // Graphique des paiements par méthode
    const paymentMethodChart = new Chart(document.getElementById('paymentMethodChart'), {
        type: 'doughnut',
        data: {
            labels: @json($paymentStats['method_labels'] ?? []),
            datasets: [{
                data: @json($paymentStats['method_data'] ?? []),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                hoverBorderColor: 'rgba(234, 236, 244, 1)',
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            cutout: '70%'
        }
    });

    // Graphique des paiements par mois
    const paymentMonthlyChart = new Chart(document.getElementById('paymentMonthlyChart'), {
        type: 'line',
        data: {
            labels: @json($paymentStats['month_labels'] ?? []),
            datasets: [{
                label: 'Montant des paiements (FCFA)',
                data: @json($paymentStats['month_data'] ?? []),
                borderColor: '#36b9cc',
                backgroundColor: 'rgba(54, 185, 204, 0.05)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' FCFA';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y.toLocaleString() + ' FCFA';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Graphique des tickets par événement
    const ticketEventChart = new Chart(document.getElementById('ticketEventChart'), {
        type: 'bar',
        data: {
            labels: @json($ticketStats['event_labels'] ?? []),
            datasets: [{
                label: 'Tickets vendus',
                data: @json($ticketStats['event_data'] ?? []),
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });

    // Graphique des tickets par jour
    const ticketDailyChart = new Chart(document.getElementById('ticketDailyChart'), {
        type: 'line',
        data: {
            labels: @json($ticketStats['day_labels'] ?? []),
            datasets: [{
                label: 'Tickets vendus',
                data: @json($ticketStats['day_data'] ?? []),
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.05)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });
});
</script>
@endpush
