@extends('layouts.dashboard')

@section('title', 'Gestion des paiements')

@push('styles')
<style>
:root {
    --primary: #0f1a3d;
    --primary-light: #1a237e;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
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

.payments-page {
    min-height: 100vh;
    background: var(--gray-50);
    padding: 2rem;
}

/* Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title-section h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 0.5rem 0;
}

.page-title-section p {
    color: var(--gray-600);
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
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
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
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1.2;
}

.stat-card.primary .stat-value { color: var(--primary); }
.stat-card.success .stat-value { color: var(--success); }
.stat-card.warning .stat-value { color: var(--warning); }
.stat-card.danger .stat-value { color: var(--danger); }

/* Main Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (max-width: 1200px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

/* Card */
.card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

/* Filters */
.filters-section {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
    margin-bottom: 1.5rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 1rem;
    align-items: end;
}

@media (max-width: 1024px) {
    .filters-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 640px) {
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
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-700);
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

.search-input-group {
    position: relative;
}

.search-input-group .form-control {
    padding-left: 2.5rem;
}

.search-icon {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    pointer-events: none;
}

/* Table */
.table-wrapper {
    overflow-x: auto;
}

.payments-table {
    width: 100%;
    border-collapse: collapse;
}

.payments-table thead {
    background: var(--gray-50);
}

.payments-table thead th {
    padding: 0.875rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-600);
    border-bottom: 2px solid var(--gray-200);
    white-space: nowrap;
}

.payments-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.payments-table tbody tr:hover {
    background: var(--gray-50);
}

.payments-table tbody td {
    padding: 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
}

.user-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.user-name {
    font-weight: 600;
    color: var(--gray-900);
}

.user-email {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.event-title {
    font-weight: 500;
    color: var(--gray-900);
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.amount {
    font-weight: 700;
    color: var(--success);
    font-size: 0.9375rem;
}

.payment-method {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.payment-method.airtel {
    background: #fee2e2;
    color: #dc2626;
}

.payment-method.mtn {
    background: #fef3c7;
    color: #d97706;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-paid {
    background: #d1fae5;
    color: #065f46;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-failed {
    background: #fee2e2;
    color: #991b1b;
}

.date-time {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date {
    font-weight: 500;
    color: var(--gray-900);
}

.time {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 0.375rem;
    border: 1px solid var(--gray-300);
    background: white;
    color: var(--gray-600);
    transition: all 0.2s;
    cursor: pointer;
    text-decoration: none;
}

.action-btn:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-900);
}

.action-btn.primary:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

/* Top Lists */
.top-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.top-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 0.5rem;
    background: var(--gray-50);
    transition: all 0.2s;
}

.top-item:hover {
    background: var(--gray-100);
    transform: translateX(4px);
}

.top-rank {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
}

.top-rank.first {
    background: #ffd700;
    color: var(--gray-900);
}

.top-rank.second {
    background: #c0c0c0;
    color: var(--gray-900);
}

.top-rank.third {
    background: #cd7f32;
    color: white;
}

.top-rank.default {
    background: var(--gray-300);
    color: var(--gray-700);
}

.top-info {
    flex: 1;
    min-width: 0;
}

.top-name {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.top-details {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.top-amount {
    font-weight: 700;
    color: var(--success);
    font-size: 1rem;
    white-space: nowrap;
}

.total-item {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid var(--gray-300);
    background: var(--gray-100) !important;
}

.total-item .top-rank {
    background: var(--primary);
    color: white;
}

.total-item .top-name {
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Charts */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-top: 1.5rem;
}

@media (max-width: 1024px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}

.chart-container {
    position: relative;
    height: 300px;
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
}

/* Pagination */
.pagination-wrapper {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
    .payments-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .payments-table {
        font-size: 0.75rem;
    }
    
    .payments-table thead th,
    .payments-table tbody td {
        padding: 0.75rem 0.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="payments-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Gestion des paiements</h1>
            <p>Suivi des transactions, modes de paiement et revenus globaux</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
            <a href="{{ route('admin.payments.export') }}" class="btn btn-primary">
                <i class="fas fa-download"></i>
                Exporter
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-label">Total des paiements</div>
            <div class="stat-value">{{ format_number($totalPayments) }}</div>
        </div>
        <div class="stat-card success">
            <div class="stat-label">Revenus totaux</div>
            <div class="stat-value">{{ number_format($totalRevenue, 0, ',', ' ') }} <span style="font-size: 1rem; font-weight: 500; color: var(--gray-500);">FCFA</span></div>
        </div>
        <div class="stat-card warning">
            <div class="stat-label">En attente</div>
            <div class="stat-value">{{ format_number($pendingPayments) }}</div>
        </div>
        <div class="stat-card success">
            <div class="stat-label">Paiements réussis</div>
            <div class="stat-value">{{ format_number($paidPayments) }}</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-grid">
        <!-- Payments List -->
        <div>
            <!-- Filters -->
            <div class="filters-section">
                <form method="GET" action="{{ route('admin.payments.index') }}" id="filtersForm">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">Recherche</label>
                            <div class="search-input-group">
                                <i class="fas fa-search search-icon"></i>
                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="Rechercher par utilisateur, événement..."
                                    value="{{ request('search') }}"
                                >
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Mode de paiement</label>
                            <select name="method" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous les modes</option>
                                <option value="MTN Mobile Money" {{ request('method') == 'MTN Mobile Money' ? 'selected' : '' }}>MTN Mobile Money</option>
                                <option value="Airtel Money" {{ request('method') == 'Airtel Money' ? 'selected' : '' }}>Airtel Money</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Statut</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                <option value="payé" {{ request('status') == 'payé' ? 'selected' : '' }}>Payé</option>
                                <option value="en attente" {{ request('status') == 'en attente' ? 'selected' : '' }}>En attente</option>
                                <option value="échoué" {{ request('status') == 'échoué' ? 'selected' : '' }}>Échoué</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Trier par</label>
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date (récent)</option>
                                <option value="montant" {{ request('sort') == 'montant' ? 'selected' : '' }}>Montant (croissant)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Payments Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des paiements</h3>
                    <span class="text-sm text-gray-600">{{ $payments->total() }} résultat(s)</span>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="payments-table">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Événement</th>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-name">{{ $payment->user->prenom ?? 'N/A' }} {{ $payment->user->nom ?? 'N/A' }}</span>
                                            <span class="user-email">{{ $payment->user->email ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="event-title" title="{{ $payment->event->title ?? 'N/A' }}">
                                            {{ $payment->event->title ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="amount">{{ number_format($payment->montant, 0, ',', ' ') }} <span style="font-size: 0.75rem; color: var(--gray-500);">FCFA</span></div>
                                    </td>
                                    <td>
                                        @if(stripos($payment->methode_paiement, 'airtel') !== false)
                                            <span class="payment-method airtel">
                                                <i class="fas fa-mobile-alt"></i>
                                                Airtel
                                            </span>
                                        @elseif(stripos($payment->methode_paiement, 'mtn') !== false)
                                            <span class="payment-method mtn">
                                                <i class="fas fa-mobile-alt"></i>
                                                MTN
                                            </span>
                                        @else
                                            <span class="payment-method" style="background: var(--gray-200); color: var(--gray-700);">
                                                {{ $payment->methode_paiement }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->statut == 'payé')
                                            <span class="status-badge status-paid">Payé</span>
                                        @elseif($payment->statut == 'en attente')
                                            <span class="status-badge status-pending">En attente</span>
                                        @elseif($payment->statut == 'échoué')
                                            <span class="status-badge status-failed">Échoué</span>
                                        @else
                                            <span class="status-badge" style="background: var(--gray-200); color: var(--gray-700);">{{ $payment->statut }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="date-time">
                                            <span class="date">{{ $payment->created_at->format('d/m/Y') }}</span>
                                            <span class="time">{{ $payment->created_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="action-btn primary" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.payments.download', $payment) }}" class="action-btn" title="Télécharger">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-inbox"></i>
                                            </div>
                                            <p class="empty-text">Aucun paiement trouvé</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($payments->hasPages())
                    <div class="pagination-wrapper">
                        {{ $payments->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Stats -->
        <div>
            <!-- Top Events -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Top 5 événements</h3>
                </div>
                <div class="card-body">
                    <div class="top-list">
                        @forelse($topEvents as $index => $event)
                        <div class="top-item">
                            <div class="top-rank {{ $index == 0 ? 'first' : ($index == 1 ? 'second' : ($index == 2 ? 'third' : 'default')) }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="top-info">
                                <div class="top-name" title="{{ $event->event->title ?? 'Événement supprimé' }}">
                                    {{ $event->event->title ?? 'Événement supprimé' }}
                                </div>
                                <div class="top-details">{{ $event->payment_count }} paiement(s)</div>
                            </div>
                            <div class="top-amount">{{ number_format($event->total_revenue, 0, ',', ' ') }} <span style="font-size: 0.75rem;">FCFA</span></div>
                        </div>
                        @empty
                        <div class="empty-state" style="padding: 2rem 1rem;">
                            <p class="empty-text">Aucune donnée</p>
                        </div>
                        @endforelse
                        
                        <div class="top-item total-item">
                            <div class="top-rank">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="top-info">
                                <div class="top-name">TOTAL GÉNÉRAL</div>
                                <div class="top-details">{{ $paidPayments }} paiement(s)</div>
                            </div>
                            <div class="top-amount">{{ number_format($totalRevenue, 0, ',', ' ') }} <span style="font-size: 0.75rem;">FCFA</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Users -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 5 utilisateurs</h3>
                </div>
                <div class="card-body">
                    <div class="top-list">
                        @forelse($topUsers as $index => $user)
                        <div class="top-item">
                            <div class="top-rank {{ $index == 0 ? 'first' : ($index == 1 ? 'second' : ($index == 2 ? 'third' : 'default')) }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="top-info">
                                <div class="top-name">{{ $user->user->prenom ?? 'N/A' }} {{ $user->user->nom ?? 'N/A' }}</div>
                                <div class="top-details">{{ $user->payment_count }} paiement(s)</div>
                            </div>
                            <div class="top-amount">{{ number_format($user->total_spent, 0, ',', ' ') }} <span style="font-size: 0.75rem;">FCFA</span></div>
                        </div>
                        @empty
                        <div class="empty-state" style="padding: 2rem 1rem;">
                            <p class="empty-text">Aucune donnée</p>
                        </div>
                        @endforelse
                        
                        <div class="top-item total-item">
                            <div class="top-rank">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="top-info">
                                <div class="top-name">TOTAL GÉNÉRAL</div>
                                <div class="top-details">{{ $paidPayments }} paiement(s)</div>
                            </div>
                            <div class="top-amount">{{ number_format($totalRevenue, 0, ',', ' ') }} <span style="font-size: 0.75rem;">FCFA</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Répartition par mode de paiement</h3>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="paymentMethodsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tendance des paiements (7 jours)</h3>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="paymentTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
(function() {
    const colorPalette = {
        primary: '#0f1a3d',
        primaryLight: '#1a237e',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
    };
    
    function loadPaymentMethodsData() {
        return {
            labels: @json($paymentMethodsData['labels'] ?? []),
            data: @json($paymentMethodsData['data'] ?? [])
        };
    }
    
    function loadPaymentTrendsData() {
        return {
            labels: @json($paymentTrendsData['labels'] ?? []),
            data: @json($paymentTrendsData['data'] ?? [])
        };
    }
    
    // Payment Methods Chart
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart');
    if (paymentMethodsCtx) {
        const paymentMethodsData = loadPaymentMethodsData();
        const colors = paymentMethodsData.labels.map(label => {
            const labelLower = label.toLowerCase();
            if (labelLower.includes('airtel')) return '#dc2626';
            if (labelLower.includes('mtn')) return '#f59e0b';
            return colorPalette.primary;
        });
        
        new Chart(paymentMethodsCtx, {
            type: 'doughnut',
            data: {
                labels: paymentMethodsData.labels,
                datasets: [{
                    data: paymentMethodsData.data,
                    backgroundColor: colors,
                    hoverBackgroundColor: colors.map(c => c + 'dd'),
                    borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
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
                cutout: '70%'
            }
        });
    }
    
    // Payment Trends Chart
    const paymentTrendsCtx = document.getElementById('paymentTrendsChart');
    if (paymentTrendsCtx) {
        const paymentTrendsData = loadPaymentTrendsData();
        new Chart(paymentTrendsCtx, {
            type: 'line',
            data: {
                labels: paymentTrendsData.labels,
                datasets: [{
                    label: 'Montant (FCFA)',
                    data: paymentTrendsData.data,
                    borderColor: colorPalette.success,
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: colorPalette.success,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
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
                            }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
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
    
    // Auto-submit search on Enter
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filtersForm').submit();
            }
        });
    }
})();
</script>
@endpush
