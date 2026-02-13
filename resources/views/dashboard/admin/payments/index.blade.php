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

.payments-page {
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

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-outline-primary {
    background: transparent;
    border: 1px solid var(--primary);
    color: var(--primary);
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
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

.stat-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.stat-info {
    flex: 1;
}

.stat-number {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    font-weight: 500;
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

.stat-card.primary .stat-number { color: var(--primary); }
.stat-card.success .stat-number { color: var(--success); }
.stat-card.warning .stat-number { color: var(--warning); }
.stat-card.info .stat-number { color: var(--info); }

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
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

/* User Info */
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

/* Event Title */
.event-title {
    font-weight: 500;
    color: var(--gray-900);
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Amount */
.amount {
    font-weight: 700;
    color: var(--success);
    font-size: 0.9375rem;
}

/* Payment Method */
.payment-method {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.payment-method.airtel {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #dc2626;
}

.payment-method.mtn {
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #f59e0b;
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.status-paid {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #f59e0b;
}

.status-failed {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

/* Date Time */
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

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
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

.action-btn.success:hover {
    background: var(--success);
    border-color: var(--success);
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
    border: 1px solid transparent;
}

.top-item:hover {
    background: var(--gray-100);
    border-color: var(--gray-300);
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
    background: linear-gradient(135deg, #ffd700 0%, #fbbf24 100%);
    color: var(--gray-900);
}

.top-rank.second {
    background: linear-gradient(135deg, #c0c0c0 0%, #9ca3af 100%);
    color: var(--gray-900);
}

.top-rank.third {
    background: linear-gradient(135deg, #cd7f32 0%, #b45309 100%);
    color: white;
}

.top-rank.default {
    background: var(--gray-300);
    color: var(--gray-700);
}

.top-rank.total {
    background: var(--primary);
    color: white;
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
    display: flex;
    align-items: center;
    gap: 0.5rem;
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

/* Pagination */
.pagination-wrapper {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}

.pagination .page-item .page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.25rem;
    height: 2.25rem;
    padding: 0 0.5rem;
    border: 1px solid var(--gray-300);
    background: white;
    color: var(--gray-700);
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    text-decoration: none;
}

.pagination .page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.pagination .page-item .page-link:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
}

.pagination .page-item.disabled .page-link {
    background: var(--gray-100);
    color: var(--gray-400);
    cursor: not-allowed;
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
    
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
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

.gap-2 {
    gap: 0.5rem;
}

.gap-3 {
    gap: 1rem;
}

.mt-1 {
    margin-top: 0.25rem;
}

.mt-2 {
    margin-top: 0.5rem;
}

.mb-2 {
    margin-bottom: 0.5rem;
}

.mb-3 {
    margin-bottom: 1rem;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.me-1 {
    margin-right: 0.25rem;
}

.me-2 {
    margin-right: 0.5rem;
}

.ms-auto {
    margin-left: auto;
}

.p-3 {
    padding: 1rem;
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
<div class="payments-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Gestion des paiements</h1>
            <p>Suivi des transactions, modes de paiement et revenus globaux</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.payments.export') }}" class="btn btn-primary">
                <i class="fas fa-download"></i>
                Exporter
            </a>
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
                    <div class="stat-number">{{ number_format($totalPayments, 0, ',', ' ') }}</div>
                    <div class="stat-label">Total paiements</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ number_format($totalRevenue, 0, ',', ' ') }} <span style="font-size: 1rem; color: var(--gray-500);">FCFA</span></div>
                    <div class="stat-label">Revenus totaux</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ number_format($pendingPayments, 0, ',', ' ') }}</div>
                    <div class="stat-label">En attente</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="stat-card info">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ number_format($paidPayments, 0, ',', ' ') }}</div>
                    <div class="stat-label">Paiements réussis</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
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
                                    placeholder="Référence, utilisateur, événement..."
                                    value="{{ request('search') }}"
                                >
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Mode de paiement</label>
                            <select name="method" class="form-select">
                                <option value="">Tous les modes</option>
                                <option value="MTN Mobile Money" {{ request('method') == 'MTN Mobile Money' ? 'selected' : '' }}>MTN Mobile Money</option>
                                <option value="Airtel Money" {{ request('method') == 'Airtel Money' ? 'selected' : '' }}>Airtel Money</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Statut</label>
                            <select name="status" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="payé" {{ request('status') == 'payé' ? 'selected' : '' }}>Payé</option>
                                <option value="en attente" {{ request('status') == 'en attente' ? 'selected' : '' }}>En attente</option>
                                <option value="échoué" {{ request('status') == 'échoué' ? 'selected' : '' }}>Échoué</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <div style="display: flex; gap: 0.5rem;">
                                <button type="submit" class="btn btn-primary" style="flex: 1;">
                                    <i class="fas fa-search"></i>
                                    Filtrer
                                </button>
                                @if(request()->anyFilled(['search', 'method', 'status']))
                                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Payments Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-credit-card"></i>
                        Liste des paiements
                    </h5>
                    <span style="font-size: 0.875rem; color: var(--gray-600);">
                        {{ $payments->total() }} résultat(s)
                        @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->total() > 0)
                            · Page {{ $payments->currentPage() }}/{{ $payments->lastPage() }}
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="payments-table">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Utilisateur</th>
                                    <th>Événement</th>
                                    <th style="text-align: center;">Montant</th>
                                    <th>Mode</th>
                                    <th style="text-align: center;">Statut</th>
                                    <th>Date</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td>
                                        <code style="background: var(--gray-100); padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">
                                            {{ $payment->matricule ?? 'N/A' }}
                                        </code>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-name">{{ $payment->user->prenom ?? '' }} {{ $payment->user->nom ?? 'N/A' }}</span>
                                            <span class="user-email">{{ $payment->user->email ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="event-title" title="{{ $payment->event->title ?? $payment->order->evenement->title ?? 'N/A' }}">
                                            {{ Str::limit($payment->event->title ?? $payment->order->evenement->title ?? 'N/A', 30) }}
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="amount">{{ number_format($payment->montant, 0, ',', ' ') }}</span>
                                        <span style="font-size: 0.75rem; color: var(--gray-500);">FCFA</span>
                                    </td>
                                    <td>
                                        @php
                                            $method = $payment->methode_paiement ?? $payment->mode_paiement ?? '';
                                        @endphp
                                        @if(stripos($method, 'airtel') !== false)
                                            <span class="payment-method airtel">
                                                <i class="fas fa-mobile-alt"></i>
                                                Airtel
                                            </span>
                                        @elseif(stripos($method, 'mtn') !== false)
                                            <span class="payment-method mtn">
                                                <i class="fas fa-mobile-alt"></i>
                                                MTN
                                            </span>
                                        @else
                                            <span class="payment-method" style="background: var(--gray-200); color: var(--gray-700);">
                                                {{ $method ?: 'N/A' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @php
                                            $status = strtolower($payment->statut ?? '');
                                        @endphp
                                        @if(in_array($status, ['payé', 'paid', 'paye', 'success']))
                                            <span class="status-badge status-paid">
                                                <i class="fas fa-check-circle"></i>
                                                Payé
                                            </span>
                                        @elseif(in_array($status, ['en attente', 'pending', 'en_attente', 'waiting']))
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock"></i>
                                                En attente
                                            </span>
                                        @elseif(in_array($status, ['échoué', 'failed', 'echoue', 'error']))
                                            <span class="status-badge status-failed">
                                                <i class="fas fa-times-circle"></i>
                                                Échoué
                                            </span>
                                        @else
                                            <span class="status-badge" style="background: var(--gray-200); color: var(--gray-700);">
                                                {{ $payment->statut ?? 'N/A' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="date-time">
                                            <span class="date">{{ $payment->created_at ? $payment->created_at->format('d/m/Y') : 'N/A' }}</span>
                                            <span class="time">{{ $payment->created_at ? $payment->created_at->format('H:i') : '' }}</span>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="action-btn primary" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.payments.download', $payment) }}" class="action-btn success" title="Télécharger la facture">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-credit-card"></i>
                                            </div>
                                            <p class="empty-text">Aucun paiement trouvé</p>
                                            <p class="empty-description">
                                                @if(request()->anyFilled(['search', 'method', 'status']))
                                                    Aucun paiement ne correspond à vos critères de recherche.
                                                @else
                                                    Aucun paiement n'a encore été effectué sur la plateforme.
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
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
                    <h5 class="card-title">
                        <i class="fas fa-calendar-alt"></i>
                        Top 5 événements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="top-list">
                        @forelse($topEvents as $index => $item)
                        <div class="top-item">
                            <div class="top-rank {{ $index == 0 ? 'first' : ($index == 1 ? 'second' : ($index == 2 ? 'third' : 'default')) }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="top-info">
                                <div class="top-name" title="{{ $item->event->title ?? 'Événement supprimé' }}">
                                    {{ Str::limit($item->event->title ?? 'Événement supprimé', 25) }}
                                </div>
                                <div class="top-details">
                                    <span>{{ $item->payment_count }} paiement(s)</span>
                                    <span>·</span>
                                    <span>{{ $totalRevenue > 0 ? round(($item->total_revenue / $totalRevenue) * 100, 1) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="top-amount">{{ number_format($item->total_revenue, 0, ',', ' ') }} <span style="font-size: 0.75rem;">FCFA</span></div>
                        </div>
                        @empty
                        <div class="empty-state" style="padding: 2rem 1rem;">
                            <p class="empty-text">Aucune donnée</p>
                            <p class="empty-description">Aucun événement avec paiements</p>
                        </div>
                        @endforelse
                        
                        @if($topEvents->isNotEmpty())
                        <div class="top-item total-item">
                            <div class="top-rank total">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="top-info">
                                <div class="top-name">TOTAL GÉNÉRAL</div>
                                <div class="top-details">{{ $paidPayments }} paiement(s) · 100%</div>
                            </div>
                            <div class="top-amount">{{ number_format($totalRevenue, 0, ',', ' ') }} <span style="font-size: 0.75rem;">FCFA</span></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Users -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-users"></i>
                        Top 5 utilisateurs
                    </h5>
                </div>
                <div class="card-body">
                    <div class="top-list">
                        @forelse($topUsers as $index => $item)
                        <div class="top-item">
                            <div class="top-rank {{ $index == 0 ? 'first' : ($index == 1 ? 'second' : ($index == 2 ? 'third' : 'default')) }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="top-info">
                                <div class="top-name" title="{{ $item->user->prenom ?? '' }} {{ $item->user->nom ?? '' }}">
                                    {{ Str::limit(($item->user->prenom ?? '') . ' ' . ($item->user->nom ?? ''), 20) }}
                                </div>
                                <div class="top-details">
                                    <span>{{ $item->payment_count }} paiement(s)</span>
                                    <span>·</span>
                                    <span>{{ $totalRevenue > 0 ? round(($item->total_spent / $totalRevenue) * 100, 1) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="top-amount">{{ number_format($item->total_spent, 0, ',', ' ') }} <span style="font-size: 0.75rem;">FCFA</span></div>
                        </div>
                        @empty
                        <div class="empty-state" style="padding: 2rem 1rem;">
                            <p class="empty-text">Aucune donnée</p>
                            <p class="empty-description">Aucun utilisateur avec paiements</p>
                        </div>
                        @endforelse
                        
                        @if($topUsers->isNotEmpty())
                        <div class="top-item total-item">
                            <div class="top-rank total">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="top-info">
                                <div class="top-name">TOTAL GÉNÉRAL</div>
                                <div class="top-details">{{ $paidPayments }} paiement(s) · 100%</div>
                            </div>
                            <div class="top-amount">{{ number_format($totalRevenue, 0, ',', ' ') }} <span style="font-size: 0.75rem;">FCFA</span></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    @if(!empty($paymentMethodsData['labels']) || !empty($paymentTrendsData['labels']))
    <div class="charts-grid">
        @if(!empty($paymentMethodsData['labels']))
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Répartition par mode de paiement
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="paymentMethodsChart"></canvas>
                </div>
            </div>
        </div>
        @endif

        @if(!empty($paymentTrendsData['labels']))
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-line"></i>
                    Tendance des paiements (7 jours)
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="paymentTrendsChart"></canvas>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Remove onchange from selects
    document.querySelectorAll('select[name="method"], select[name="status"]').forEach(select => {
        select.removeAttribute('onchange');
    });

    // Chart.js Configuration
    const colorPalette = {
        primary: '#0f1a3d',
        primaryLight: '#1a237e',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        info: '#3b82f6'
    };

    // Payment Methods Chart
    @if(!empty($paymentMethodsData['labels']))
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart');
    if (paymentMethodsCtx) {
        const paymentMethodsData = {
            labels: @json($paymentMethodsData['labels'] ?? []),
            data: @json($paymentMethodsData['data'] ?? [])
        };
        
        const colors = paymentMethodsData.labels.map(label => {
            const labelLower = label.toLowerCase();
            if (labelLower.includes('airtel')) return '#ef4444';
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
                            font: { size: 12, family: "'Inter', sans-serif" }
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

    // Payment Trends Chart
    @if(!empty($paymentTrendsData['labels']))
    const paymentTrendsCtx = document.getElementById('paymentTrendsChart');
    if (paymentTrendsCtx) {
        const paymentTrendsData = {
            labels: @json($paymentTrendsData['labels'] ?? []),
            data: @json($paymentTrendsData['data'] ?? [])
        };
        
        new Chart(paymentTrendsCtx, {
            type: 'line',
            data: {
                labels: paymentTrendsData.labels,
                datasets: [{
                    label: 'Montant (FCFA)',
                    data: paymentTrendsData.data,
                    borderColor: colorPalette.success,
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: colorPalette.success,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: colorPalette.success,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
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
                            font: { size: 11, family: "'Inter', sans-serif" }
                        },
                        grid: { 
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: "'Inter', sans-serif" } }
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

    // Auto-close alerts after 4 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (!alert.classList.contains('alert-permanent')) {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 300);
            }
        });
    }, 4000);
});
</script>
@endpush