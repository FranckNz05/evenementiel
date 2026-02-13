@extends('layouts.dashboard')

@section('title', 'Tableau de bord Organisateur')

@php
    // Initialisation des variables avec des valeurs par défaut
    $upcomingEvents = $upcomingEvents ?? collect();
    $recentPayments = $recentPayments ?? collect();
    $revenueData = $revenueData ?? [];
    $revenueLabels = $revenueLabels ?? [];
    
    // Définition des statistiques par défaut
    $defaultStats = [
        'events_count' => 0,
        'active_events' => 0,
        'published_events' => 0,
        'draft_events' => 0,
        'completed_events' => 0,
        'cancelled_events' => 0,
        'tickets_sold' => 0,
        'total_revenue' => 0,
        'wallet_balance' => 0
    ];
    
    // Fusion avec les statistiques existantes
    $stats = array_merge($defaultStats, (array)($stats ?? []));
@endphp

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

.organizer-dashboard {
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

.btn-outline-primary {
    background: transparent;
    border: 2px solid var(--primary);
    color: var(--primary);
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-1px);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8125rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
    font-size: 2rem;
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
    font-size: 0.625rem;
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

.stat-gradient.bg-primary { background: var(--primary); }
.stat-gradient.bg-success { background: var(--success); }
.stat-gradient.bg-info { background: var(--info); }
.stat-gradient.bg-warning { background: var(--warning); }

/* Wallet Card */
.wallet-card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
    transition: all 0.2s;
}

.wallet-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.wallet-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.wallet-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.wallet-icon {
    width: 48px;
    height: 48px;
    background: var(--gray-100);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1.25rem;
}

.wallet-details h6 {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.wallet-amount {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-900);
}

.wallet-actions {
    display: flex;
    gap: 0.75rem;
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

/* Table */
.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead th {
    padding: 0.875rem 1.5rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
}

.table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.2s;
}

.table tbody tr:hover {
    background: var(--gray-50);
}

.table tbody td {
    padding: 1.25rem 1.5rem;
    font-size: 0.875rem;
    color: var(--gray-700);
    vertical-align: middle;
}

/* Event Image */
.event-image-wrapper {
    width: 60px;
    height: 60px;
    flex-shrink: 0;
}

.event-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0.5rem;
    border: 1px solid var(--gray-200);
}

.event-image-placeholder {
    width: 100%;
    height: 100%;
    background: var(--gray-100);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    border: 1px dashed var(--gray-300);
}

.event-info h6 {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
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
}

/* Badges */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

/* Amount */
.amount {
    font-weight: 700;
    color: var(--success);
}

/* Action Buttons */
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 0.375rem;
    border: 1px solid var(--gray-300);
    background: white;
    color: var(--gray-600);
    transition: all 0.2s;
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

.action-btn.primary:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

/* Sales Info */
.sales-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.sales-number {
    font-weight: 700;
    color: var(--gray-900);
    font-size: 1rem;
}

.sales-label {
    font-size: 0.75rem;
    color: var(--gray-500);
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

/* Chart Containers */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
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

.modal-header .btn-close {
    color: white;
    filter: brightness(0) invert(1);
    opacity: 0.8;
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
    .organizer-dashboard {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .wallet-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .wallet-actions {
        width: 100%;
    }
    
    .wallet-actions .btn {
        flex: 1;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .event-image-wrapper {
        width: 50px;
        height: 50px;
    }
}

/* Row and Col */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -0.75rem;
}

.col,
.col-12,
.col-lg-4,
.col-lg-6,
.col-lg-8,
.col-sm-6,
.col-xl-3 {
    padding: 0 0.75rem;
    box-sizing: border-box;
}

.col-12 { width: 100%; }
.col-sm-6 { width: 50%; }
.col-lg-4 { width: 33.333%; }
.col-lg-6 { width: 50%; }
.col-lg-8 { width: 66.666%; }
.col-xl-3 { width: 25%; }

@media (max-width: 992px) {
    .col-lg-4,
    .col-lg-6,
    .col-lg-8,
    .col-xl-3 {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .col-sm-6 {
        width: 100%;
    }
}

/* Utilities */
.g-4 { gap: 1.5rem; }
.mb-4 { margin-bottom: 1.5rem; }
.mb-5 { margin-bottom: 2rem; }
.mt-3 { margin-top: 1rem; }
.me-1 { margin-right: 0.25rem; }
.me-2 { margin-right: 0.5rem; }
.me-3 { margin-right: 1rem; }
.ms-2 { margin-left: 0.5rem; }
.p-4 { padding: 1.5rem; }
.px-4 { padding-left: 1.5rem; padding-right: 1.5rem; }
.py-4 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
.px-3 { padding-left: 1rem; padding-right: 1rem; }
.py-3 { padding-top: 1rem; padding-bottom: 1rem; }

.d-flex { display: flex; }
.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }
.justify-content-end { justify-content: flex-end; }
.flex-wrap { flex-wrap: wrap; }
.flex-column { flex-direction: column; }

.w-100 { width: 100%; }
.h-100 { height: 100%; }

.border-bottom { border-bottom: 1px solid var(--gray-200); }
.rounded-3 { border-radius: 0.5rem; }
.object-fit-cover { object-fit: cover; }

.text-center { text-align: center; }
.text-muted { color: var(--gray-500) !important; }
.text-success { color: var(--success) !important; }
.text-warning { color: var(--warning) !important; }
.text-danger { color: var(--danger) !important; }

.fw-semibold { font-weight: 600; }
.fw-bold { font-weight: 700; }
.small { font-size: 0.75rem; }

.bg-light { background: var(--gray-100); }
.shadow-sm { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
.border-0 { border: none; }
.overflow-hidden { overflow: hidden; }
.position-relative { position: relative; }
</style>
@endpush

@section('content')
<div class="organizer-dashboard">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                Tableau de bord
            </h1>
            <p>Gérez vos événements et suivez vos performances</p>
        </div>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventTypeModal">
                <i class="fas fa-plus"></i>
                Créer un événement
            </button>
            <a href="{{ route('organizer.scans.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-qrcode"></i>
                Scanner QR Code
            </a>
            <a href="{{ route('organizer.access-codes.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-key"></i>
                Codes d'accès
            </a>
        </div>
    </div>

    <!-- Wallet - Solde disponible -->
    <div class="wallet-card">
        <div class="wallet-content">
            <div class="wallet-info">
                <div class="wallet-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="wallet-details">
                    <h6>Solde disponible</h6>
                    <div class="wallet-amount">
                        {{ number_format($stats['wallet_balance'] ?? 0, 0, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>
            <div class="wallet-actions">
                <a href="{{ route('organizer.withdrawals.create') }}" class="btn btn-primary">
                    <i class="fas fa-money-bill-wave"></i>
                    Retirer
                </a>
                <a href="{{ route('organizer.withdrawals.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-history"></i>
                    Historique
                </a>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
        <!-- Événements -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Événements</div>
                    <div class="stat-number">{{ number_format($stats['events_count'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-arrow-up text-success"></i>
                        Total créés
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <div class="stat-gradient bg-primary"></div>
        </div>

        <!-- Revenus -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Revenus</div>
                    <div class="stat-number">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <span class="fw-semibold">FCFA</span> générés
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-gradient bg-success"></div>
        </div>

        <!-- Tickets vendus -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Tickets vendus</div>
                    <div class="stat-number">{{ number_format($stats['tickets_sold'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-ticket-alt"></i>
                        Confirmés
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
            <div class="stat-gradient bg-info"></div>
        </div>

        <!-- Événements actifs -->
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Événements actifs</div>
                    <div class="stat-number">{{ number_format($stats['active_events'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-circle text-success" style="font-size: 8px;"></i>
                        En cours
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="stat-gradient bg-warning"></div>
        </div>
    </div>

    <!-- Section Graphiques et Analytics -->
    <div class="row g-4 mb-5">
        <!-- Graphique des revenus -->
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Évolution des revenus
                    </h5>
                    <span class="text-muted small">Revenus des 30 derniers jours</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques des événements -->
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie"></i>
                        Répartition des événements
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center">
                    <div class="chart-container">
                        <canvas id="eventsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Paiements récents -->
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-credit-card"></i>
                        Paiements récents
                    </h5>
                    <a href="{{ route('organizer.payments.index') }}" class="btn btn-outline-primary btn-sm">
                        Voir tout
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Transaction</th>
                                    <th>Événement</th>
                                    <th class="text-center">Montant</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="payment-icon">
                                                <i class="fas fa-credit-card"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">
                                                    #{{ $payment->reference_transaction ?? $payment->id }}
                                                </div>
                                                <small class="text-muted">{{ $payment->user->name ?? 'Client' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">
                                            {{ Str::limit($payment->event->title ?? 'N/A', 30) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="amount fw-bold">
                                            {{ number_format($payment->montant ?? 0, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $status = strtolower($payment->statut ?? '');
                                        @endphp
                                        @if(in_array($status, ['payé', 'paid', 'paye', 'success']))
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i>
                                                Payé
                                            </span>
                                        @elseif(in_array($status, ['en attente', 'pending', 'waiting']))
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock"></i>
                                                En attente
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times-circle"></i>
                                                Échoué
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">
                                            {{ $payment->created_at ? $payment->created_at->format('d/m/Y H:i') : 'N/A' }}
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-credit-card"></i>
                                            </div>
                                            <p class="empty-text">Aucun paiement récent</p>
                                            <p class="empty-description">Les paiements apparaîtront ici</p>
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

    <!-- Section Événements -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-alt"></i>
                        Mes événements
                    </h5>
                    <a href="{{ route('organizer.events.index') }}" class="btn btn-outline-primary btn-sm">
                        Voir tout
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Événement</th>
                                    <th class="d-none d-md-table-cell">Date & Lieu</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center d-none d-lg-table-cell">Ventes</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingEvents as $event)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="event-image-wrapper">
                                                @if($event->image)
                                                <img src="{{ asset('storage/'.$event->image) }}" 
                                                     class="event-image" 
                                                     alt="{{ $event->title }}">
                                                @else
                                                <div class="event-image-placeholder">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($event->title, 40) }}</h6>
                                                <div class="d-flex d-md-none flex-column">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        {{ $event->start_date ? $event->start_date->format('d/m/Y H:i') : 'N/A' }}
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ Str::limit($event->lieu ?? $event->ville ?? 'N/A', 30) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold mb-1">
                                                <i class="fas fa-calendar-alt me-2" style="color: var(--primary);"></i>
                                                {{ $event->start_date ? $event->start_date->format('d/m/Y') : 'N/A' }}
                                            </span>
                                            <small class="text-muted mb-2">{{ $event->start_date ? $event->start_date->format('H:i') : '' }}</small>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1" style="color: var(--primary);"></i>
                                                {{ Str::limit($event->lieu ?? $event->ville ?? 'N/A', 25) }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($event->is_published)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i>
                                                Publié
                                            </span>
                                        @else
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock"></i>
                                                Brouillon
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center d-none d-lg-table-cell">
                                        <div class="sales-info">
                                            <span class="sales-number">
                                                {{ $event->tickets->sum('quantite_vendue') ?? $event->tickets_sold ?? 0 }}
                                            </span>
                                            <span class="sales-label">tickets</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('organizer.events.show', $event) }}" 
                                               class="action-btn info" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('organizer.events.edit', $event) }}" 
                                               class="action-btn primary" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <p class="empty-text">Aucun événement à venir</p>
                                            <p class="empty-description">Créez votre premier événement pour commencer</p>
                                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#eventTypeModal">
                                                <i class="fas fa-plus me-2"></i>
                                                Créer un événement
                                            </button>
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

<!-- Modal de sélection du type d'événement -->
<div class="modal fade" id="eventTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus"></i>
                    Choisir le type d'événement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-4">Sélectionnez le type d'événement que vous souhaitez créer :</p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="{{ route('events.wizard.step1') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-day me-2"></i>
                        Événement simple
                    </a>
                    <a href="{{ route('events.select-type') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Événement personnalisé
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration de Chart.js
    Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#4b5563';

    const colorPalette = {
        primary: '#0f1a3d',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        info: '#3b82f6',
        gray: '#6b7280'
    };

    // ===========================================
    // GRAPHIQUE DES REVENUS
    // ===========================================
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueLabels = @json($revenueLabels ?? []);
        const revenueData = @json($revenueData ?? []);
        
        // Données par défaut si vides
        const labels = revenueLabels.length ? revenueLabels : (() => {
            const dates = [];
            for (let i = 29; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                dates.push(date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }));
            }
            return dates;
        })();
        
        const data = revenueData.length ? revenueData : new Array(labels.length).fill(0);

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenus (FCFA)',
                    data: data,
                    borderColor: colorPalette.primary,
                    backgroundColor: 'rgba(15, 26, 61, 0.05)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: colorPalette.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
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
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: { display: false }
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
                                return 'Revenus: ' + context.parsed.y.toLocaleString('fr-FR') + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
    }

    // ===========================================
    // GRAPHIQUE DES ÉVÉNEMENTS
    // ===========================================
    const eventsCtx = document.getElementById('eventsChart');
    if (eventsCtx) {
        const published = {{ $stats['published_events'] ?? 0 }};
        const draft = {{ $stats['draft_events'] ?? 0 }};
        const completed = {{ $stats['completed_events'] ?? 0 }};
        const cancelled = {{ $stats['cancelled_events'] ?? 0 }};
        
        const hasData = published + draft + completed + cancelled > 0;

        new Chart(eventsCtx, {
            type: 'doughnut',
            data: {
                labels: hasData ? ['Publiés', 'Brouillons', 'Terminés', 'Annulés'] : ['Aucune donnée'],
                datasets: [{
                    data: hasData ? [published, draft, completed, cancelled] : [1],
                    backgroundColor: hasData 
                        ? [colorPalette.success, colorPalette.warning, colorPalette.gray, colorPalette.danger]
                        : ['#e5e7eb'],
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        enabled: hasData,
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
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ===========================================
    // ANIMATION DES COMPTEURS
    // ===========================================
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/\s/g, '').replace(/FCFA/g, '')) || 0;
            if (target === 0) return;
            
            const increment = target / 30;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current).toLocaleString('fr-FR');
            }, 30);
        });
    }
    
    animateCounters();
});
</script>
@endpush