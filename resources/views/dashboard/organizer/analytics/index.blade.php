@extends('layouts.dashboard')

@section('title', 'Analytics & Rapports')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-lg-6">
            <h1 class="h3 mb-2 mb-lg-0 text-gray-800 fw-bold">
                <i class="fas fa-chart-bar text-primary me-2"></i>
                Analytics & Rapports
            </h1>
            <p class="text-muted mb-0 small">Analysez les performances de vos événements</p>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                <button class="btn btn-success d-flex align-items-center justify-content-center" onclick="exportReport()">
                    <i class="fas fa-download me-2"></i>
                    <span class="d-none d-sm-inline">Exporter le rapport</span>
                </button>
                <button class="btn btn-primary d-flex align-items-center justify-content-center" onclick="refreshAnalytics()">
                    <i class="fas fa-sync-alt me-2"></i>
                    <span class="d-none d-sm-inline">Actualiser</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filtres de période -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('organizer.analytics.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="period" class="form-label fw-semibold">Période</label>
                            <select class="form-select" id="period" name="period" onchange="updateDateRange()">
                                <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>7 derniers jours</option>
                                <option value="30" {{ request('period') == '30' ? 'selected' : '' }}>30 derniers jours</option>
                                <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>3 derniers mois</option>
                                <option value="365" {{ request('period') == '365' ? 'selected' : '' }}>12 derniers mois</option>
                                <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Période personnalisée</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="customDateFrom" style="display: none;">
                            <label for="date_from" class="form-label fw-semibold">Date de début</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3" id="customDateTo" style="display: none;">
                            <label for="date_to" class="form-label fw-semibold">Date de fin</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="event_id" class="form-label fw-semibold">Événement</label>
                            <select class="form-select" id="event_id" name="event_id">
                                <option value="">Tous les événements</option>
                                @foreach($events ?? [] as $event)
                                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                        {{ $event->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Analyser
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Métriques clés -->
    <div class="row g-4 mb-5">
        <!-- Revenus totaux -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-success text-uppercase mb-2 letter-spacing-1">
                                Revenus totaux
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800 counter-number">
                                {{ number_format($metrics['total_revenue'] ?? 0, 0, ',', ' ') }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-arrow-{{ ($metrics['revenue_change'] ?? 0) >= 0 ? 'up' : 'down' }} text-{{ ($metrics['revenue_change'] ?? 0) >= 0 ? 'success' : 'danger' }} me-1"></i>
                                {{ abs($metrics['revenue_change'] ?? 0) }}% vs période précédente
                            </div>
                        </div>
                        <div class="stat-icon bg-success bg-gradient">
                            <i class="fas fa-money-bill-wave text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-gradient bg-success"></div>
            </div>
        </div>

        <!-- Tickets vendus -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-2 letter-spacing-1">
                                Tickets vendus
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800 counter-number">
                                {{ number_format($metrics['tickets_sold'] ?? 0, 0, ',', ' ') }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-arrow-{{ ($metrics['tickets_change'] ?? 0) >= 0 ? 'up' : 'down' }} text-{{ ($metrics['tickets_change'] ?? 0) >= 0 ? 'success' : 'danger' }} me-1"></i>
                                {{ abs($metrics['tickets_change'] ?? 0) }}% vs période précédente
                            </div>
                        </div>
                        <div class="stat-icon bg-primary bg-gradient">
                            <i class="fas fa-ticket-alt text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-gradient bg-primary"></div>
            </div>
        </div>

        <!-- Nouveaux clients -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-info text-uppercase mb-2 letter-spacing-1">
                                Nouveaux clients
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800 counter-number">
                                {{ number_format($metrics['new_customers'] ?? 0, 0, ',', ' ') }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-arrow-{{ ($metrics['customers_change'] ?? 0) >= 0 ? 'up' : 'down' }} text-{{ ($metrics['customers_change'] ?? 0) >= 0 ? 'success' : 'danger' }} me-1"></i>
                                {{ abs($metrics['customers_change'] ?? 0) }}% vs période précédente
                            </div>
                        </div>
                        <div class="stat-icon bg-info bg-gradient">
                            <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-gradient bg-info"></div>
            </div>
        </div>

        <!-- Taux de conversion -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-2 letter-spacing-1">
                                Taux de conversion
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800 counter-number">
                                {{ number_format($metrics['conversion_rate'] ?? 0, 1) }}%
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-arrow-{{ ($metrics['conversion_change'] ?? 0) >= 0 ? 'up' : 'down' }} text-{{ ($metrics['conversion_change'] ?? 0) >= 0 ? 'success' : 'danger' }} me-1"></i>
                                {{ abs($metrics['conversion_change'] ?? 0) }}% vs période précédente
                            </div>
                        </div>
                        <div class="stat-icon bg-warning bg-gradient">
                            <i class="fas fa-percentage text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-gradient bg-warning"></div>
            </div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="row g-4 mb-5">
        <!-- Évolution des revenus -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-4">
                    <h5 class="mb-1 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>
                        Évolution des revenus
                    </h5>
                    <p class="text-muted mb-0 small">Revenus par jour sur la période sélectionnée</p>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height: 300px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition des événements -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-4">
                    <h5 class="mb-1 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>
                        Performance par événement
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height: 300px;">
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
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-4">
                    <h5 class="mb-1 fw-bold text-primary">
                        <i class="fas fa-clock me-2"></i>
                        Ventes par heure de la journée
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height: 250px;">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canaux de vente -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-4">
                    <h5 class="mb-1 fw-bold text-primary">
                        <i class="fas fa-funnel-dollar me-2"></i>
                        Canaux de vente
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="position: relative; height: 250px;">
                        <canvas id="channelsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des événements les plus performants -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-4">
                    <h5 class="mb-1 fw-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>
                        Événements les plus performants
                    </h5>
                    <p class="text-muted mb-0 small">Classement par revenus générés</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0 fw-semibold text-gray-700">#</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-gray-700">Événement</th>
                                    <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Tickets vendus</th>
                                    <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Revenus</th>
                                    <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Taux de conversion</th>
                                    <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topEvents ?? [] as $index => $event)
                                <tr class="border-bottom">
                                    <td class="px-4 py-4 text-center">
                                        <span class="badge bg-{{ $index < 3 ? 'primary' : 'secondary' }} rounded-pill">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="event-image-wrapper me-3">
                                                @if($event->image)
                                                <img src="{{ asset('storage/'.$event->image) }}" 
                                                     class="event-image rounded-3 object-cover" 
                                                     alt="{{ $event->title }}">
                                                @else
                                                <div class="event-image-placeholder rounded-3 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-semibold text-gray-800">
                                                    {{ Str::limit($event->title, 40) }}
                                                </h6>
                                                <small class="text-muted">{{ $event->start_date->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <span class="h6 mb-0 fw-bold text-gray-800">
                                            {{ number_format($event->tickets_sold ?? 0, 0, ',', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <span class="h6 mb-0 fw-bold text-success">
                                            {{ number_format($event->revenue ?? 0, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <span class="badge bg-info-soft text-info px-3 py-2 rounded-pill fw-semibold">
                                            {{ number_format($event->conversion_rate ?? 0, 1) }}%
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <div class="progress" style="height: 8px; width: 100px;">
                                            <div class="progress-bar bg-{{ $event->performance >= 80 ? 'success' : ($event->performance >= 60 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $event->performance ?? 0 }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($event->performance ?? 0, 0) }}%</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucune donnée disponible</h5>
                                            <p class="text-muted mb-0">Les données d'analytics apparaîtront ici</p>
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

<style>
/* Styles personnalisés */
.letter-spacing-1 {
    letter-spacing: 0.5px;
}

.stat-card {
    transition: all 0.3s ease;
    border-radius: 12px !important;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.stat-gradient {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    opacity: 0.7;
}

.counter-number {
    font-size: 2rem;
    line-height: 1;
}

.event-image-wrapper {
    width: 50px;
    height: 50px;
    flex-shrink: 0;
}

.event-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-image-placeholder {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
}

.bg-info-soft {
    background-color: #d1ecf1 !important;
}

.empty-state {
    padding: 2rem;
}

.card {
    border-radius: 12px;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
    .counter-number {
        font-size: 1.5rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des compteurs
    function animateCounters() {
        const counters = document.querySelectorAll('.counter-number');
        counters.forEach(counter => {
            const target = parseFloat(counter.textContent.replace(/[^\d.-]/g, ''));
            const increment = target / 50;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                if (counter.textContent.includes('%')) {
                    counter.textContent = current.toFixed(1) + '%';
                } else {
                    counter.textContent = Math.floor(current).toLocaleString('fr-FR');
                }
            }, 30);
        });
    }

    animateCounters();

    // Graphique des revenus
    const revenueLabels = {!! json_encode($revenueLabels ?? ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']) !!};
    const revenueData = {!! json_encode($revenueData ?? [0, 0, 0, 0, 0, 0, 0]) !!};
    
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Revenus (FCFA)',
                data: revenueData,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#007bff',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });

    // Graphique des événements
    const eventsLabels = {!! json_encode($eventsLabels ?? ['Aucune donnée']) !!};
    const eventsData = {!! json_encode($eventsData ?? [1]) !!};
    
    const eventsCtx = document.getElementById('eventsChart').getContext('2d');
    const eventsChart = new Chart(eventsCtx, {
        type: 'doughnut',
        data: {
            labels: eventsLabels,
            datasets: [{
                data: eventsData,
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#6c757d',
                    '#17a2b8'
                ],
                borderWidth: 2,
                borderColor: '#fff',
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
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8
                }
            }
        }
    });

    // Graphique des ventes par heure
    const hourlyLabels = {!! json_encode($hourlyLabels ?? ['0h', '4h', '8h', '12h', '16h', '20h']) !!};
    const hourlyData = {!! json_encode($hourlyData ?? [0, 0, 0, 0, 0, 0]) !!};
    
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    const hourlyChart = new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: hourlyLabels,
            datasets: [{
                label: 'Ventes',
                data: hourlyData,
                backgroundColor: 'rgba(0, 123, 255, 0.8)',
                borderColor: '#007bff',
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });

    // Graphique des canaux
    const channelsLabels = {!! json_encode($channelsLabels ?? ['Aucune donnée']) !!};
    const channelsData = {!! json_encode($channelsData ?? [1]) !!};
    
    const channelsCtx = document.getElementById('channelsChart').getContext('2d');
    const channelsChart = new Chart(channelsCtx, {
        type: 'pie',
        data: {
            labels: channelsLabels,
            datasets: [{
                data: channelsData,
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
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
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8
                }
            }
        }
    });
});

// Fonctions globales
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

function exportReport() {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("organizer.analytics.export") }}';
    
    // Ajouter les filtres actuels
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

// Initialiser l'affichage des dates personnalisées
updateDateRange();
</script>
@endpush
@endsection
