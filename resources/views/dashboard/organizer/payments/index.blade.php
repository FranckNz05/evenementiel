@extends('layouts.dashboard')

@section('title', 'Gestion des Paiements')

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
    display: flex;
    align-items: center;
    gap: 0.75rem;
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

.btn-outline-secondary {
    background: white;
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-outline-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-900);
    transform: translateY(-1px);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8125rem;
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
    position: relative;
    overflow: hidden;
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
    margin-bottom: 0.25rem;
}

.stat-subtitle {
    font-size: 0.75rem;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.stat-subtitle i {
    font-size: 0.75rem;
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

.stat-gradient {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    opacity: 0.7;
}

.stat-gradient.bg-success { background: var(--success); }
.stat-gradient.bg-primary { background: var(--primary); }
.stat-gradient.bg-warning { background: var(--warning); }
.stat-gradient.bg-info { background: var(--info); }

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

.card-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
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
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

@media (max-width: 768px) {
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
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.form-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
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
    background: var(--gray-50);
}

.payments-table thead th[style*="text-align: center"] {
    text-align: center;
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

.payments-table tbody td[style*="text-align: center"] {
    text-align: center;
}

/* Checkbox */
.form-check-input {
    width: 1.125rem;
    height: 1.125rem;
    border: 2px solid var(--gray-300);
    border-radius: 0.25rem;
    cursor: pointer;
    transition: all 0.2s;
}

.form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

.form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

/* Transaction Info */
.transaction-ref {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.transaction-method {
    font-size: 0.75rem;
    color: var(--gray-500);
}

/* User Info */
.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 9999px;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
    text-transform: uppercase;
}

.user-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.user-name {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 0.875rem;
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

.status-refunded {
    background: #e0e7ff;
    color: #4338ca;
    border: 1px solid #6366f1;
}

/* Date */
.date-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date {
    font-weight: 500;
    color: var(--gray-900);
    font-size: 0.8125rem;
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

.action-btn.info:hover {
    background: var(--info);
    border-color: var(--info);
    color: white;
}

.action-btn.warning:hover {
    background: var(--warning);
    border-color: var(--warning);
    color: white;
}

.action-btn.primary:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.action-btn.danger:hover {
    background: var(--danger);
    border-color: var(--danger);
    color: white;
}

.action-btn.success:hover {
    background: var(--success);
    border-color: var(--success);
    color: white;
}

/* Payment Icon */
.payment-icon {
    width: 40px;
    height: 40px;
    background: var(--gray-100);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1rem;
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
    margin-top: 1rem;
    display: flex;
    justify-content: flex-end;
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

/* Modal */
.modal-content {
    border: none;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    padding: 1.25rem 1.5rem;
    border-bottom: none;
}

.modal-title {
    color: white;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-title i {
    color: white;
}

.modal-header .btn-close {
    color: white;
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
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
    
    .user-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}

@media (max-width: 1024px) {
    .hide-md {
        display: none;
    }
}

/* Utilities */
.d-flex { display: flex; }
.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }
.justify-content-end { justify-content: flex-end; }
.flex-wrap { flex-wrap: wrap; }
.flex-column { flex-direction: column; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 1rem; }
.mb-0 { margin-bottom: 0; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-4 { margin-bottom: 1.5rem; }
.mb-5 { margin-bottom: 2rem; }
.mt-1 { margin-top: 0.25rem; }
.mt-2 { margin-top: 0.5rem; }
.mt-4 { margin-top: 1.5rem; }
.me-1 { margin-right: 0.25rem; }
.me-2 { margin-right: 0.5rem; }
.me-3 { margin-right: 1rem; }
.ms-auto { margin-left: auto; }
.p-4 { padding: 1.5rem; }
.px-4 { padding-left: 1.5rem; padding-right: 1.5rem; }
.py-4 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
.p-0 { padding: 0; }

.w-100 { width: 100%; }
.h-100 { height: 100%; }

.text-center { text-align: center; }
.text-muted { color: var(--gray-500) !important; }
.text-success { color: var(--success) !important; }
.text-warning { color: var(--warning) !important; }
.text-danger { color: var(--danger) !important; }
.text-info { color: var(--info) !important; }
.text-primary { color: var(--primary) !important; }

.fw-semibold { font-weight: 600; }
.fw-bold { font-weight: 700; }
.small { font-size: 0.75rem; }

.bg-light { background: var(--gray-100); }
.border-0 { border: none; }
.shadow-sm { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
.overflow-hidden { overflow: hidden; }
.position-relative { position: relative; }

/* Correction pour la faute */
.text-gray-800 { color: var(--gray-800) !important; }
.text-gray-700 { color: var(--gray-700) !important; }
</style>
@endpush

@section('content')
<div class="payments-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-credit-card"></i>
                Gestion des paiements
            </h1>
            <p>Suivez vos revenus et transactions</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-success" onclick="exportPayments()">
                <i class="fas fa-download"></i>
                Exporter
            </button>
            <button class="btn btn-secondary" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i>
                Actualiser
            </button>
            <a href="{{ route('organizer.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    @php
        // Calcul du taux de conversion
        $totalPayments = ($successfulPayments ?? 0) + ($pendingPayments ?? 0);
        $conversionRate = $totalPayments > 0 ? round(($successfulPayments ?? 0) / $totalPayments * 100, 1) : 0;
    @endphp
    
    <div class="stats-grid">
        <!-- Revenus totaux -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Revenus totaux</div>
                    <div class="stat-number">{{ number_format($totalRevenue ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <span class="fw-semibold">FCFA</span> générés
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <div class="stat-gradient bg-success"></div>
        </div>

        <!-- Paiements réussis -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Paiements réussis</div>
                    <div class="stat-number">{{ number_format($successfulPayments ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-check-circle text-success"></i>
                        Transactions
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-gradient bg-primary"></div>
        </div>

        <!-- Paiements en attente -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">En attente</div>
                    <div class="stat-number">{{ number_format($pendingPayments ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-clock text-warning"></i>
                        Transactions
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-gradient bg-warning"></div>
        </div>

        <!-- Taux de conversion -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Taux de conversion</div>
                    <div class="stat-number">{{ number_format($conversionRate, 1) }}<small style="font-size: 1rem;">%</small></div>
                    <div class="stat-subtitle">
                        <i class="fas fa-percentage text-info"></i>
                        Réussite
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-gradient bg-info"></div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
        <form method="GET" action="{{ route('organizer.payments.index') }}" id="filtersForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="status" class="filter-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="payé" {{ request('status') == 'payé' ? 'selected' : '' }}>Payé</option>
                        <option value="en attente" {{ request('status') == 'en attente' ? 'selected' : '' }}>En attente</option>
                        <option value="échoué" {{ request('status') == 'échoué' ? 'selected' : '' }}>Échoué</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="event_id" class="filter-label">Événement</label>
                    <select class="form-select" id="event_id" name="event_id">
                        <option value="">Tous les événements</option>
                        @foreach($events ?? [] as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title ?? $event->name ?? 'Sans titre' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date_from" class="filter-label">Date début</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label for="date_to" class="filter-label">Date fin</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="filter-group" style="display: flex; flex-direction: row; align-items: flex-end; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-search"></i>
                        Filtrer
                    </button>
                    @if(request()->anyFilled(['status', 'event_id', 'date_from', 'date_to']))
                    <a href="{{ route('organizer.payments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tableau des paiements -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-list"></i>
                Historique des paiements
            </h5>
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <span style="font-size: 0.875rem; color: var(--gray-600);">
                    {{ $payments instanceof \Illuminate\Pagination\LengthAwarePaginator ? $payments->total() : (is_countable($payments) ? count($payments) : 0) }} paiement(s)
                </span>
                <button class="btn btn-outline-secondary btn-sm" onclick="toggleView(event)">
                    <i class="fas fa-th-large me-1"></i>
                    Vue grille
                </button>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-wrapper">
                <table class="payments-table" id="paymentsTable">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Transaction</th>
                            <th>Client</th>
                            <th>Événement</th>
                            <th style="text-align: center;">Montant</th>
                            <th style="text-align: center;">Statut</th>
                            <th>Date</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments ?? [] as $payment)
                        <tr data-payment-id="{{ $payment->id }}">
                            <td style="width: 40px;">
                                <input type="checkbox" class="form-check-input payment-checkbox" value="{{ $payment->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="payment-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div>
                                        <div class="transaction-ref">
                                            #{{ $payment->reference_transaction ?? $payment->matricule ?? $payment->id }}
                                        </div>
                                        <small class="transaction-method">{{ $payment->methode_paiement ?? 'Mobile Money' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="avatar-placeholder">
                                        {{ substr($payment->user->prenom ?? $payment->user->name ?? 'C', 0, 1) }}
                                    </div>
                                    <div class="user-details">
                                        <span class="user-name">{{ $payment->user->prenom ?? '' }} {{ $payment->user->nom ?? $payment->user->name ?? 'Client' }}</span>
                                        <span class="user-email">{{ $payment->user->email ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="event-title" title="{{ $payment->event->title ?? $payment->order->event->title ?? 'N/A' }}">
                                    {{ Str::limit($payment->event->title ?? $payment->order->event->title ?? 'N/A', 30) }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="amount">{{ number_format($payment->montant ?? $payment->amount ?? 0, 0, ',', ' ') }}</span>
                                <span style="font-size: 0.75rem; color: var(--gray-500);">FCFA</span>
                            </td>
                            <td style="text-align: center;">
                                @php
                                    $status = strtolower($payment->statut ?? $payment->status ?? '');
                                @endphp
                                @if(in_array($status, ['payé', 'paid', 'paye', 'success']))
                                    <span class="status-badge status-paid">
                                        <i class="fas fa-check-circle"></i>
                                        Payé
                                    </span>
                                @elseif(in_array($status, ['en attente', 'pending', 'waiting']))
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-clock"></i>
                                        En attente
                                    </span>
                                @elseif(in_array($status, ['échoué', 'failed', 'error']))
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
                                <div class="date-info">
                                    <span class="date">{{ $payment->created_at ? $payment->created_at->format('d/m/Y') : 'N/A' }}</span>
                                    <span class="time">{{ $payment->created_at ? $payment->created_at->format('H:i') : '' }}</span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div class="action-buttons">
                                    <button class="action-btn info" onclick="viewPayment({{ $payment->id }})" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <p class="empty-text">Aucun paiement trouvé</p>
                                    <p class="empty-description">Les paiements apparaîtront ici</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if(isset($payments) && $payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div style="font-size: 0.875rem; color: var(--gray-600);">
                    Affichage de {{ $payments->firstItem() ?? 0 }} à {{ $payments->lastItem() ?? 0 }} sur {{ $payments->total() }} résultats
                </div>
                <div class="pagination-wrapper">
                    {{ $payments->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal de détails du paiement -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-credit-card"></i>
                    Détails du paiement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="paymentDetails">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===========================================
    // ANIMATION DES COMPTEURS
    // ===========================================
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const text = counter.textContent.replace(/\s/g, '').replace('%', '');
            const target = parseFloat(text) || 0;
            
            if (target === 0) return;
            
            const isPercentage = counter.textContent.includes('%');
            const increment = target / 30;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                if (isPercentage) {
                    counter.innerHTML = Math.floor(current) + '<small style="font-size: 1rem;">%</small>';
                } else {
                    counter.textContent = Math.floor(current).toLocaleString('fr-FR');
                }
            }, 30);
        });
    }

    animateCounters();

    // ===========================================
    // SÉLECTION MULTIPLE
    // ===========================================
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.payment-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // ===========================================
    // VALIDATION DES DATES
    // ===========================================
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    if (dateFrom && dateTo) {
        dateFrom.addEventListener('change', function() {
            if (this.value && dateTo.value) {
                if (dateTo.value < this.value) {
                    dateTo.value = this.value;
                }
            }
        });
    }

    // ===========================================
    // RÉINITIALISATION DES FILTRES
    // ===========================================
    document.querySelectorAll('select[name="status"], select[name="event_id"]').forEach(select => {
        select.removeAttribute('onchange');
    });

    // ===========================================
    // AUTO-FERMETURE DES ALERTES
    // ===========================================
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

// ===========================================
// FONCTIONS GLOBALES
// ===========================================

/**
 * Afficher les détails d'un paiement
 */
function viewPayment(paymentId) {
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const detailsDiv = document.getElementById('paymentDetails');
    
    detailsDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>';
    modal.show();
    
    fetch(`/organizer/payments/${paymentId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.json();
    })
    .then(data => {
        if (data.html) {
            detailsDiv.innerHTML = data.html;
        } else {
            detailsDiv.innerHTML = '<div class="alert alert-danger">Impossible de charger les détails du paiement.</div>';
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        detailsDiv.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des détails.</div>';
    });
}

/**
 * Exporter les paiements
 */
function exportPayments() {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("organizer.payments.export") }}';
    form.style.display = 'none';
    
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

/**
 * Rafraîchir les données
 */
function refreshData() {
    location.reload();
}

/**
 * Basculer entre vue tableau et vue grille
 */
function toggleView(event) {
    const table = document.getElementById('paymentsTable');
    const button = event.currentTarget;
    
    if (!table.classList.contains('table-view')) {
        table.classList.add('table-view');
        table.classList.remove('grid-view');
        button.innerHTML = '<i class="fas fa-th-large me-1"></i> Vue grille';
    } else {
        table.classList.remove('table-view');
        table.classList.add('grid-view');
        button.innerHTML = '<i class="fas fa-list me-1"></i> Vue tableau';
    }
}
</script>
@endpush