@extends('layouts.dashboard')

@section('title', 'Analytique Plateforme')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Analytique Plateforme</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item active">Rapports & Audience</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <div class="btn-group shadow-sm bg-white rounded p-1 border">
                <button type="button" class="btn btn-sm {{ $selectedDays == 7 ? 'btn-primary shadow-sm' : 'btn-light border-0' }} px-3" onclick="updateStats(7)">7j</button>
                <button type="button" class="btn btn-sm {{ $selectedDays == 30 ? 'btn-primary shadow-sm' : 'btn-light border-0' }} px-3" onclick="updateStats(30)">30j</button>
                <button type="button" class="btn btn-sm {{ $selectedDays == 90 ? 'btn-primary shadow-sm' : 'btn-light border-0' }} px-3" onclick="updateStats(90)">90j</button>
            </div>
        </div>
    </div>

    <!-- Stat Cards Summary -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100 border-start border-primary border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-light text-primary p-3 rounded-circle me-3">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted small text-uppercase">Visiteurs Totaux</h6>
                            <h3 class="card-title mb-0 fw-bold" id="totalVisitors">{{ number_format($stats['visitors']['total']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100 border-start border-success border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-light text-success p-3 rounded-circle me-3">
                            <i class="fas fa-user-plus fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted small text-uppercase">Nouveaux</h6>
                            <h3 class="card-title mb-0 fw-bold" id="newVisitors">{{ number_format($stats['visitors']['new']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100 border-start border-info border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info-light text-info p-3 rounded-circle me-3">
                            <i class="fas fa-file-alt fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted small text-uppercase">Pages Vues</h6>
                            <h3 class="card-title mb-0 fw-bold" id="totalPageViews">{{ number_format(array_sum($stats['pageViews'])) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100 border-start border-warning border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-light text-warning p-3 rounded-circle me-3">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-1 text-muted small text-uppercase">Taux Activity</h6>
                            <h3 class="card-title mb-0 fw-bold" id="avgTimeOnSite">{{ number_format(count($stats['pageViews']) > 0 ? array_sum($stats['pageViews']) / count($stats['pageViews']) : 0, 1) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card modern-card shadow-sm h-100">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-chart-area me-2"></i>Volumes des pages vues par jour</h6>
                </div>
                <div class="card-body p-4">
                    <div class="chart-area" style="height: 350px;">
                        <canvas id="pageViewsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card modern-card shadow-sm h-100">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-mobile-alt me-2"></i>Appareils & Mobiles</h6>
                </div>
                <div class="card-body p-4">
                    <div class="chart-pie flex-grow-1" style="height: 350px;">
                        <canvas id="devicesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4 mb-5">
        <div class="col-xl-6">
            <div class="card modern-card shadow-sm">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-star me-2"></i>Pages les plus consultées</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table modern-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">URL / Titre de la page</th>
                                    <th class="pe-4 text-end">Vues</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['topPages'] as $page)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $page['title'] }}</div>
                                        <small class="text-muted text-truncate d-block" style="max-width: 300px;">{{ $page['url'] ?? '' }}</small>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <span class="badge bg-primary px-3 shadow-sm">{{ number_format($page['views']) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card modern-card shadow-sm">
                <div class="card-header-modern">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-globe me-2"></i>Provenance géographique (Top pays)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table modern-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Pays</th>
                                    <th class="pe-4 text-end">Visteurs unique</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['userCountries'] as $country => $visitors)
                                <tr>
                                    <td class="ps-4 fw-medium text-dark">
                                        <i class="fas fa-map-pin text-danger me-2 small"></i>{{ $country }}
                                    </td>
                                    <td class="pe-4 text-end">
                                        <span class="fw-bold text-dark">{{ number_format($visitors) }}</span>
                                    </td>
                                </tr>
                                @endforeach
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    let pageViewsChart;
    let devicesChart;

    const chartFonts = { family: 'Inter, sans-serif', size: 12 };

    function initCharts(data) {
        // Page Views Chart
        const pageViewsCtx = document.getElementById('pageViewsChart');
        const dates = Object.keys(data.pageViews);
        const views = Object.values(data.pageViews);

        if (pageViewsChart) pageViewsChart.destroy();

        pageViewsChart = new Chart(pageViewsCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Pages vues',
                    data: views,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4e73df',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { borderDash: [2], color: 'rgba(0,0,0,0.05)', drawBorder: false },
                        ticks: { font: chartFonts }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: chartFonts }
                    }
                }
            }
        });

        // Devices Chart
        const devicesCtx = document.getElementById('devicesChart');
        const deviceLabels = Object.keys(data.userDevices);
        const deviceData = Object.values(data.userDevices);

        if (devicesChart) devicesChart.destroy();

        devicesChart = new Chart(devicesCtx, {
            type: 'doughnut',
            data: {
                labels: deviceLabels,
                datasets: [{
                    data: deviceData,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    hoverOffset: 15,
                    borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { usePointStyle: true, font: chartFonts, padding: 20 }
                    }
                },
                cutout: '70%',
            }
        });
    }

    function updateStats(days) {
        // Simple loading state
        const container = document.querySelector('.container-fluid');
        container.style.opacity = '0.6';

        fetch(`/admin/analytics/stats?days=${days}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalVisitors').textContent = new Intl.NumberFormat().format(data.visitors.total);
                document.getElementById('newVisitors').textContent = new Intl.NumberFormat().format(data.visitors.new);
                
                const totalViews = Object.values(data.pageViews).reduce((a, b) => a + b, 0);
                document.getElementById('totalPageViews').textContent = new Intl.NumberFormat().format(totalViews);
                
                const avg = Object.keys(data.pageViews).length > 0 ? totalViews / Object.keys(data.pageViews).length : 0;
                document.getElementById('avgTimeOnSite').textContent = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 1 }).format(avg);
                
                initCharts(data);

                // Update UI buttons
                document.querySelectorAll('.btn-group .btn').forEach(btn => {
                    btn.classList.remove('btn-primary', 'shadow-sm');
                    btn.classList.add('btn-light', 'border-0');
                    if (btn.innerText.includes(days + 'j')) {
                        btn.classList.remove('btn-light', 'border-0');
                        btn.classList.add('btn-primary', 'shadow-sm');
                    }
                });

                container.style.opacity = '1';

                // Update URL
                const url = new URL(window.location);
                url.searchParams.set('days', days);
                window.history.pushState({}, '', url);
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initCharts(@json($stats));
    });
</script>
@endpush
