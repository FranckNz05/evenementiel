<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['total_users'] ?? 0) }}</div>
                <div class="stat-label">Utilisateurs</div>
                <div class="mt-2 text-muted small">
                    <i class="fas fa-user-tie me-1"></i> {{ number_format($stats['total_organizers'] ?? 0) }} Orga.
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['total_events'] ?? 0) }}</div>
                <div class="stat-label">Événements</div>
                <div class="mt-2 text-muted small">
                    <i class="fas fa-eye me-1"></i> {{ number_format($stats['total_views'] ?? 0) }} Vues
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['total_tickets_sold'] ?? 0) }}</div>
                <div class="stat-label">Tickets Vendus</div>
                <div class="mt-2 text-muted small">
                    <i class="fas fa-chart-line me-1"></i> {{ round(($stats['total_tickets_sold'] ?? 0 / max($stats['total_events'] ?? 0, 1)) * 100, 1) }}% Taux
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['revenue'] ?? 0) }} <span class="fs-6">FCFA</span></div>
                <div class="stat-label">Revenus</div>
                <div class="mt-2 text-muted small">
                    <i class="fas fa-calculator me-1"></i> Moy. / ticket
                </div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-xl-8">
        <div class="modern-card mb-4">
            <div class="card-header-modern">
                <div class="card-title">
                    <i class="fas fa-chart-line"></i>
                    Évolution des Ventes et Revenus
                </div>
            </div>
            <div class="card-body-modern p-4">
                <canvas id="salesChart" width="100%" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="modern-card mb-4">
            <div class="card-header-modern">
                <div class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Événements par Catégorie
                </div>
            </div>
            <div class="card-body-modern p-4">
                <canvas id="categoryChart" width="100%" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="modern-card mb-4">
    <div class="card-header-modern">
        <div class="card-title">
            <i class="fas fa-list"></i>
            Activité Récente
        </div>
    </div>
    <div class="card-body-modern p-4">
        <div class="row">
            <div class="col-md-6">
                <h5 class="section-title">Événements Populaires</h5>
                <div class="list-group">
                    @forelse($popularEvents ?? collect() as $event)
                        <a href="{{ route('events.show', $event) }}" class="list-group-item list-group-item-action border-0 mb-2 rounded shadow-xs">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 fw-bold text-dark">{{ $event->title }}</h6>
                                <span class="modern-badge badge-info">{{ number_format($event->tickets_sold ?? 0) }} tickets</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>{{ $event->user->name ?? 'N/A' }}
                                </small>
                                <small class="text-success fw-bold">{{ number_format($event->revenue ?? 0) }} FCFA</small>
                            </div>
                        </a>
                    @empty
                        <div class="text-muted text-center py-3">Aucun événement à afficher</div>
                    @endforelse
                </div>
            </div>
            <div class="col-md-6">
                <h5 class="section-title">Articles Populaires</h5>
                <div class="list-group">
                    @forelse($popularBlogs ?? collect() as $blog)
                        <a href="{{ route('blogs.show', $blog) }}" class="list-group-item list-group-item-action border-0 mb-2 rounded shadow-xs">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 fw-bold text-dark">{{ $blog->title }}</h6>
                                <span class="modern-badge badge-primary">{{ number_format($blog->views ?? 0) }} vues</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>{{ $blog->user->name ?? 'N/A' }}
                                </small>
                                <small>
                                    <i class="fas fa-heart text-danger me-1"></i>{{ $blog->likes_count ?? 0 }}
                                    <i class="fas fa-comment text-primary ms-2 me-1"></i>{{ $blog->comments_count ?? 0 }}
                                </small>
                            </div>
                        </a>
                    @empty
                        <div class="text-muted text-center py-3">Aucun article à afficher</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyStats['labels'] ?? []) !!},
        datasets: [{
            label: 'Ventes',
            data: {!! json_encode($monthlyStats['sales'] ?? []) !!},
            borderColor: '#0f1a3d',
            backgroundColor: 'rgba(15, 26, 61, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Revenus (FCFA)',
            data: {!! json_encode($monthlyStats['revenue'] ?? []) !!},
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'revenue'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Nombre de ventes'
                },
                grid: {
                    borderDash: [2, 4],
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            revenue: {
                position: 'right',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Revenus (FCFA)'
                },
                grid: {
                    display: false
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    boxWidth: 8
                }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoryStats['labels'] ?? []) !!},
        datasets: [{
            data: {!! json_encode($categoryStats['counts'] ?? []) !!},
            backgroundColor: [
                '#0f1a3d',
                '#1a237e',
                '#3b4f9a',
                '#5a6ba8',
                '#7c8db8',
                '#cbd5e1'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            }
        }
    }
});
</script>
@endpush
