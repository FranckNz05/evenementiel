@extends('layouts.Administrateur')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tableau de bord analytique</h1>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary {{ $selectedDays == 7 ? 'active' : '' }}" onclick="updateStats(7)">7 jours</button>
                        <button type="button" class="btn btn-outline-primary {{ $selectedDays == 30 ? 'active' : '' }}" onclick="updateStats(30)">30 jours</button>
                        <button type="button" class="btn btn-outline-primary {{ $selectedDays == 90 ? 'active' : '' }}" onclick="updateStats(90)">90 jours</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h4 class="mb-0" id="totalVisitors">{{ number_format($stats['visitors']['total']) }}</h4>
                    <div>Visiteurs totaux</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h4 class="mb-0" id="newVisitors">{{ number_format($stats['visitors']['new']) }}</h4>
                    <div>Nouveaux visiteurs</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <h4 class="mb-0" id="totalPageViews">{{ number_format(array_sum($stats['pageViews'])) }}</h4>
                    <div>Pages vues</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h4 class="mb-0" id="avgTimeOnSite">{{ number_format(array_sum($stats['pageViews']) / count($stats['pageViews']), 1) }}</h4>
                    <div>Pages vues/jour (moyenne)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Pages vues par jour
                </div>
                <div class="card-body">
                    <canvas id="pageViewsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Appareils utilisés
                </div>
                <div class="card-body">
                    <canvas id="devicesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Pages les plus visitées
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Page</th>
                                    <th>Vues</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['topPages'] as $page)
                                <tr>
                                    <td>{{ $page['title'] }}</td>
                                    <td>{{ number_format($page['views']) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-globe me-1"></i>
                    Top 10 des pays
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Pays</th>
                                    <th>Visiteurs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['userCountries'] as $country => $visitors)
                                <tr>
                                    <td>{{ $country }}</td>
                                    <td>{{ number_format($visitors) }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let pageViewsChart;
    let devicesChart;

    function initCharts(data) {
        // Page Views Chart
        const pageViewsCtx = document.getElementById('pageViewsChart');
        const dates = Object.keys(data.pageViews);
        const views = Object.values(data.pageViews);

        if (pageViewsChart) {
            pageViewsChart.destroy();
        }

        pageViewsChart = new Chart(pageViewsCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Pages vues',
                    data: views,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Devices Chart
        const devicesCtx = document.getElementById('devicesChart');
        const deviceLabels = Object.keys(data.userDevices);
        const deviceData = Object.values(data.userDevices);

        if (devicesChart) {
            devicesChart.destroy();
        }

        devicesChart = new Chart(devicesCtx, {
            type: 'pie',
            data: {
                labels: deviceLabels,
                datasets: [{
                    data: deviceData,
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    function updateStats(days) {
        fetch(`/admin/analytics/stats?days=${days}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalVisitors').textContent = new Intl.NumberFormat().format(data.visitors.total);
                document.getElementById('newVisitors').textContent = new Intl.NumberFormat().format(data.visitors.new);
                document.getElementById('totalPageViews').textContent = new Intl.NumberFormat().format(Object.values(data.pageViews).reduce((a, b) => a + b, 0));
                document.getElementById('avgTimeOnSite').textContent = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 1 }).format(Object.values(data.pageViews).reduce((a, b) => a + b, 0) / Object.keys(data.pageViews).length);
                
                initCharts(data);

                // Update URL without reloading the page
                const url = new URL(window.location);
                url.searchParams.set('days', days);
                window.history.pushState({}, '', url);

                // Update active button state
                document.querySelectorAll('.btn-group .btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                document.querySelector(`.btn-group .btn:nth-child(${days === 7 ? 1 : days === 30 ? 2 : 3})`).classList.add('active');
            });
    }

    // Initialize charts with initial data
    document.addEventListener('DOMContentLoaded', () => {
        initCharts(@json($stats));
    });
</script>
@endpush
