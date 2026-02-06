@extends('layouts.dashboard')

@section('content')
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-info">
                <div class="stat-number">{{ $stats['events_count'] ?? 0 }}</div>
                <div class="stat-label">Événements</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-info">
                <div class="stat-number">{{ $stats['tickets_count'] ?? 0 }}</div>
                <div class="stat-label">Tickets vendus</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['revenue'] ?? 0, 0, ',', ' ') }} €</div>
                <div class="stat-label">Revenus</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-euro-sign"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-info">
                <div class="stat-number">{{ $stats['participants_count'] ?? 0 }}</div>
                <div class="stat-label">Participants</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Events -->
<div class="modern-card mb-4">
    <div class="card-header-modern">
        <div class="card-title">
            <i class="fas fa-calendar-alt"></i>
            Événements à venir
        </div>
        <a href="#" class="btn btn-sm btn-primary-modern">
            <i class="fas fa-plus"></i> Créer un événement
        </a>
    </div>
    <div class="card-body-modern p-4">
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Événement</th>
                        <th>Date</th>
                        <th>Lieu</th>
                        <th>Tickets vendus</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events ?? [] as $event)
                        <tr>
                            <td><span class="fw-bold text-dark">{{ $event->title }}</span></td>
                            <td>{{ $event->start_date }}</td>
                            <td>{{ $event->location }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($event->tickets_sold / max($event->tickets_available, 1)) * 100 }}%"></div>
                                    </div>
                                    <small>{{ $event->tickets_sold }}/{{ $event->tickets_available }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group-modern">
                                    <a href="#" class="btn btn-sm btn-primary-modern">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-info-modern">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Aucun événement à venir</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Sales -->
<div class="modern-card">
    <div class="card-header-modern">
        <div class="card-title">
            <i class="fas fa-history"></i>
            Ventes récentes
        </div>
    </div>
    <div class="card-body-modern p-4">
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Événement</th>
                        <th>Tickets</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales ?? [] as $sale)
                        <tr>
                            <td>{{ $sale->created_at }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-xs bg-light rounded-circle p-1 text-center" style="width: 24px; height: 24px;">
                                        <i class="fas fa-user text-muted small"></i>
                                    </div>
                                    {{ $sale->customer_name }}
                                </div>
                            </td>
                            <td>{{ $sale->event_title }}</td>
                            <td><span class="modern-badge badge-info">{{ $sale->tickets_count }}</span></td>
                            <td><span class="fw-bold text-success">{{ number_format($sale->amount, 0, ',', ' ') }} FCFA</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Aucune vente récente</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
