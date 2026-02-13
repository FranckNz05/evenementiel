@extends('layouts.dashboard')

@section('title', 'Historique des scans')

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

.scans-page {
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

.stat-gradient.bg-primary { background: var(--primary); }
.stat-gradient.bg-success { background: var(--success); }
.stat-gradient.bg-warning { background: var(--warning); }
.stat-gradient.bg-info { background: var(--info); }
.stat-gradient.bg-danger { background: var(--danger); }

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

/* Filters Section */
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

.scans-table {
    width: 100%;
    border-collapse: collapse;
}

.scans-table thead {
    background: var(--gray-50);
}

.scans-table thead th {
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

.scans-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.scans-table tbody tr:hover {
    background: var(--gray-50);
}

.scans-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

/* Scan Info */
.scan-datetime {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.scan-date {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 0.875rem;
}

.scan-time {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.event-title {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.ticket-name {
    font-size: 0.75rem;
    color: var(--gray-500);
}

/* QR Code */
.qr-code {
    font-family: monospace;
    background: var(--gray-100);
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    color: var(--gray-700);
    border: 1px solid var(--gray-200);
}

/* Scanned By */
.scanned-by {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.scanner-name {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 0.875rem;
}

.scanner-email {
    font-size: 0.75rem;
    color: var(--gray-500);
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

.status-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.status-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

.status-already-used {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #f59e0b;
}

.status-unknown {
    background: #f3f4f6;
    color: #4b5563;
    border: 1px solid #9ca3af;
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

/* Alert */
.alert {
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    border-left: 4px solid;
}

.alert-success {
    background: #d1fae5;
    border-left-color: #059669;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    border-left-color: #dc2626;
    color: #991b1b;
}

.alert-info {
    background: #dbeafe;
    border-left-color: #2563eb;
    color: #1e40af;
}

.alert i {
    font-size: 1rem;
}

.alert .btn-close {
    margin-left: auto;
    background: transparent;
    border: none;
    color: currentColor;
    opacity: 0.7;
    cursor: pointer;
    padding: 0.25rem;
}

.alert .btn-close:hover {
    opacity: 1;
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

/* Breadcrumb */
.breadcrumb {
    display: flex;
    flex-wrap: wrap;
    padding: 0;
    margin: 0.5rem 0 0 0;
    list-style: none;
    background: transparent;
}

.breadcrumb-item {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.8);
}

.breadcrumb-item a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-item a:hover {
    color: white;
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: white;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    padding: 0 0.5rem;
    color: rgba(255, 255, 255, 0.6);
}

/* Responsive */
@media (max-width: 768px) {
    .scans-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .scans-table {
        font-size: 0.75rem;
    }
    
    .scans-table thead th,
    .scans-table tbody td {
        padding: 0.75rem 0.5rem;
    }
}

/* Utilities */
.d-flex { display: flex; }
.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }
.justify-content-end { justify-content: flex-end; }
.flex-wrap { flex-wrap: wrap; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 1rem; }
.mb-0 { margin-bottom: 0; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-4 { margin-bottom: 1.5rem; }
.mt-4 { margin-top: 1.5rem; }
.me-1 { margin-right: 0.25rem; }
.me-2 { margin-right: 0.5rem; }
.ms-auto { margin-left: auto; }

.w-100 { width: 100%; }
.h-100 { height: 100%; }

.text-center { text-align: center; }
.text-muted { color: var(--gray-500) !important; }
.text-white-50 { color: rgba(255, 255, 255, 0.5) !important; }

.fw-semibold { font-weight: 600; }
.fw-bold { font-weight: 700; }
.small { font-size: 0.75rem; }

.bg-light { background: var(--gray-100); }
.border-0 { border: none; }
.shadow-sm { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
.overflow-hidden { overflow: hidden; }
.position-relative { position: relative; }

.text-lg { font-size: 1.25rem; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="scans-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-history"></i>
                Historique des scans
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('organizer.dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Historique des scans</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="{{ route('organizer.scans.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="btn btn-success">
                <i class="fas fa-file-csv"></i>
                Exporter en CSV
            </a>
            <a href="{{ route('organizer.scans.index') }}" class="btn btn-primary">
                <i class="fas fa-qrcode"></i>
                Scanner des billets
            </a>
            <a href="{{ route('organizer.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Scans aujourd'hui</div>
                    <div class="stat-number">{{ number_format($scanStats['today'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-calendar-day"></i>
                        Dernières 24h
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
            <div class="stat-gradient bg-primary"></div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Cette semaine</div>
                    <div class="stat-number">{{ number_format($scanStats['week'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-calendar-week"></i>
                        7 derniers jours
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
            </div>
            <div class="stat-gradient bg-success"></div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Ce mois</div>
                    <div class="stat-number">{{ number_format($scanStats['month'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-calendar-alt"></i>
                        30 derniers jours
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <div class="stat-gradient bg-warning"></div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total des scans</div>
                    <div class="stat-number">{{ number_format($scanStats['total'] ?? 0, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-qrcode"></i>
                        Depuis le début
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
            </div>
            <div class="stat-gradient bg-info"></div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
        <form action="{{ route('organizer.scans.filter') }}" method="GET" id="filtersForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="event_id" class="filter-label">Événement</label>
                    <select class="form-select" id="event_id" name="event_id">
                        <option value="">Tous les événements</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request()->event_id == $event->id ? 'selected' : '' }}>
                                {{ $event->title ?? $event->titre ?? 'Sans titre' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date_start" class="filter-label">Date début</label>
                    <input type="date" class="form-control" id="date_start" name="date_start" value="{{ request()->date_start }}">
                </div>
                <div class="filter-group">
                    <label for="date_end" class="filter-label">Date fin</label>
                    <input type="date" class="form-control" id="date_end" name="date_end" value="{{ request()->date_end }}">
                </div>
                <div class="filter-group" style="display: flex; flex-direction: row; align-items: flex-end; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-search"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('organizer.scans.index') }}" class="btn btn-secondary" title="Réinitialiser">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Liste des scans -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-qrcode"></i>
                Historique des scans
            </h5>
            <span style="font-size: 0.875rem; color: var(--gray-600);">
                {{ $scans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $scans->total() : (is_countable($scans) ? count($scans) : 0) }} scan(s)
                @if($scans instanceof \Illuminate\Pagination\LengthAwarePaginator && $scans->total() > 0)
                    · Page {{ $scans->currentPage() }}/{{ $scans->lastPage() }}
                @endif
            </span>
        </div>
        <div class="card-body p-0">
            @if($scans->count() > 0)
            <div class="table-wrapper">
                <table class="scans-table" id="scansTable">
                    <thead>
                        <tr>
                            <th>Date et heure</th>
                            <th>Événement</th>
                            <th>Type de billet</th>
                            <th>Code QR</th>
                            <th>Scanné par</th>
                            <th style="text-align: center;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scans as $scan)
                        <tr>
                            <td>
                                <div class="scan-datetime">
                                    <span class="scan-date">{{ $scan->created_at ? $scan->created_at->format('d/m/Y') : 'N/A' }}</span>
                                    <span class="scan-time">{{ $scan->created_at ? $scan->created_at->format('H:i:s') : '' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="event-title">{{ $scan->ticket->event->title ?? $scan->ticket->event->titre ?? 'Événement inconnu' }}</div>
                                <div class="ticket-name">{{ $scan->ticket->event->start_date ? $scan->ticket->event->start_date->format('d/m/Y') : '' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $scan->ticket->nom ?? $scan->ticket->name ?? 'Billet inconnu' }}</div>
                                <small class="text-muted">{{ $scan->ticket->reference ?? $scan->ticket->id }}</small>
                            </td>
                            <td>
                                <code class="qr-code">{{ substr($scan->qr_code ?? $scan->ticket->qr_code ?? 'N/A', 0, 15) }}...</code>
                            </td>
                            <td>
                                @if($scan->scannedBy)
                                <div class="scanned-by">
                                    <span class="scanner-name">{{ $scan->scannedBy->name ?? ($scan->scannedBy->prenom ?? '') . ' ' . ($scan->scannedBy->nom ?? '') }}</span>
                                    <span class="scanner-email">{{ $scan->scannedBy->email ?? '' }}</span>
                                </div>
                                @else
                                <span class="text-muted">Utilisateur inconnu</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @php
                                    $status = $scan->status ?? $scan->statut ?? '';
                                @endphp
                                @if($status == 'success')
                                    <span class="status-badge status-success">
                                        <i class="fas fa-check-circle"></i>
                                        Validé
                                    </span>
                                @elseif($status == 'error')
                                    <span class="status-badge status-error">
                                        <i class="fas fa-times-circle"></i>
                                        Erreur
                                    </span>
                                @elseif($status == 'already_used')
                                    <span class="status-badge status-already-used">
                                        <i class="fas fa-clock"></i>
                                        Déjà utilisé
                                    </span>
                                @else
                                    <span class="status-badge status-unknown">
                                        {{ $status ?: 'Inconnu' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($scans instanceof \Illuminate\Pagination\LengthAwarePaginator && $scans->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div style="font-size: 0.875rem; color: var(--gray-600);">
                        Affichage de {{ $scans->firstItem() ?? 0 }} à {{ $scans->lastItem() ?? 0 }} sur {{ $scans->total() }} résultats
                    </div>
                    <div class="pagination-wrapper">
                        {{ $scans->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
            @endif
            @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <p class="empty-text">Aucun scan enregistré</p>
                <p class="empty-description">
                    Commencez par scanner des billets pour voir l'historique.
                </p>
            </div>
            @endif
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
            const text = counter.textContent.replace(/\s/g, '');
            const target = parseFloat(text) || 0;
            
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

    // ===========================================
    // VALIDATION DES DATES
    // ===========================================
    const dateStart = document.getElementById('date_start');
    const dateEnd = document.getElementById('date_end');
    
    if (dateStart && dateEnd) {
        dateStart.addEventListener('change', function() {
            if (this.value && dateEnd.value) {
                if (dateEnd.value < this.value) {
                    dateEnd.value = this.value;
                }
            }
        });
    }

    // ===========================================
    // RÉINITIALISATION DES FILTRES
    // ===========================================
    document.querySelectorAll('select[name="event_id"]').forEach(select => {
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

    // ===========================================
    // DATATABLES
    // ===========================================
    if (typeof $.fn !== 'undefined' && typeof $.fn.DataTable !== 'undefined' && $('#scansTable').length && $('#scansTable tbody tr').length > 1) {
        $('#scansTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json',
            },
            pageLength: 25,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tous"]],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> Copier',
                    className: 'btn btn-sm btn-outline-secondary'
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-sm btn-outline-success'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-outline-danger'
                }
            ],
            order: [[0, 'desc']]
        });
    }
});
</script>
@endpush