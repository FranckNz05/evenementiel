@extends('layouts.dashboard')

@section('title', 'Gestion des Tickets')

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

.tickets-page {
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

.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
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

.btn-outline-danger {
    background: transparent;
    border: 1px solid var(--danger);
    color: var(--danger);
}

.btn-outline-danger:hover {
    background: var(--danger);
    color: white;
}

.btn-outline-success {
    background: transparent;
    border: 1px solid var(--success);
    color: var(--success);
}

.btn-outline-success:hover {
    background: var(--success);
    color: white;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
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
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
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

.tickets-table {
    width: 100%;
    border-collapse: collapse;
}

.tickets-table thead {
    background: var(--gray-50);
}

.tickets-table thead th {
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

.tickets-table thead th[style*="text-align: center"] {
    text-align: center;
}

.tickets-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.tickets-table tbody tr:hover {
    background: var(--gray-50);
}

.tickets-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

.tickets-table tbody td[style*="text-align: center"] {
    text-align: center;
}

/* Ticket Info */
.ticket-name {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.event-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

.event-link:hover {
    color: var(--primary-light);
    text-decoration: underline;
}

/* Price */
.ticket-price {
    font-weight: 700;
    color: var(--gray-900);
}

.original-price {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-decoration: line-through;
    margin-right: 0.5rem;
}

.promotion-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: #fef3c7;
    color: #92400e;
    border-radius: 9999px;
    font-size: 0.6875rem;
    font-weight: 600;
    margin-left: 0.5rem;
    border: 1px solid #f59e0b;
}

.promotion-badge i {
    color: #92400e;
}

.price-currency {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-left: 0.25rem;
}

/* Quantity Badge */
.quantity-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.5rem;
    background: var(--gray-100);
    border-radius: 0.375rem;
    color: var(--gray-700);
    font-weight: 600;
    font-size: 0.8125rem;
    border: 1px solid var(--gray-200);
}

/* Sold Count */
.sold-count {
    font-weight: 700;
    color: var(--primary);
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

.status-available {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.status-sold-out {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

.status-promotion {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #f59e0b;
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

.action-btn.success:hover {
    background: var(--success);
    border-color: var(--success);
    color: white;
}

.action-btn.danger:hover {
    background: var(--danger);
    border-color: var(--danger);
    color: white;
}

.action-btn.primary:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
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

/* Promotion Button */
.action-btn.promotion {
    background: white;
    border-color: var(--warning);
    color: var(--warning);
}

.action-btn.promotion:hover {
    background: var(--warning);
    border-color: var(--warning);
    color: white;
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

.alert-warning {
    background: #fef3c7;
    border-left-color: #d97706;
    color: #92400e;
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
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
    margin: 0;
    padding: 0;
    list-style: none;
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
    border-color: var(--gray-200);
}

/* Organizer Info */
.organizer-info {
    font-size: 0.8125rem;
    color: var(--gray-500);
}

/* Promotion Info */
.promotion-info {
    font-size: 0.75rem;
    color: var(--gray-600);
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.promotion-info i {
    color: var(--warning);
    font-size: 0.6875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .tickets-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .tickets-table {
        font-size: 0.75rem;
    }
    
    .tickets-table thead th,
    .tickets-table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .card-footer {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .hide-sm {
        display: none;
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

.justify-content-center {
    justify-content: center;
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
<div class="tickets-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Gestion des tickets</h1>
            <p>Gérez tous les tickets configurés pour les événements et leurs promotions</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nouveau ticket
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    @php
        $totalTickets = $tickets instanceof \Illuminate\Pagination\LengthAwarePaginator 
            ? $tickets->total() 
            : (is_countable($tickets) ? count($tickets) : 0);
            
        $totalAvailableTickets = \App\Models\Ticket::whereColumn('quantite', '>', 'quantite_vendue')->count();
        $totalSoldTickets = \App\Models\Ticket::sum('quantite_vendue');
        $totalPromoTickets = \App\Models\Ticket::whereNotNull('montant_promotionnel')->count();
    @endphp

    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $totalTickets }}</div>
                    <div class="stat-label">Total tickets</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $totalAvailableTickets }}</div>
                    <div class="stat-label">Tickets disponibles</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $totalSoldTickets }}</div>
                    <div class="stat-label">Tickets vendus</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="stat-card info">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $totalPromoTickets }}</div>
                    <div class="stat-label">En promotion</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.tickets.index') }}" id="filtersForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Recherche</label>
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Nom du ticket, événement, organisateur..."
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                        <option value="sold_out" {{ request('status') == 'sold_out' ? 'selected' : '' }}>Épuisé</option>
                        <option value="promotion" {{ request('status') == 'promotion' ? 'selected' : '' }}>En promotion</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Événement</label>
                    <select name="event_id" class="form-select">
                        <option value="">Tous les événements</option>
                        @foreach(\App\Models\Event::orderBy('title')->limit(50)->get() as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ Str::limit($event->title ?? $event->name ?? 'Sans titre', 40) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            <i class="fas fa-search"></i>
                            Filtrer
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'event_id']))
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
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

    <!-- Liste des tickets -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-ticket-alt"></i>
                Liste des tickets
            </h5>
            <span style="font-size: 0.875rem; color: var(--gray-600);">
                {{ $totalTickets }} résultat(s)
                @if($tickets instanceof \Illuminate\Pagination\LengthAwarePaginator && $tickets->total() > 0)
                    · Page {{ $tickets->currentPage() }}/{{ $tickets->lastPage() }}
                @endif
            </span>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="tickets-table">
                    <thead>
                        <tr>
                            <th>Nom du ticket</th>
                            <th>Événement</th>
                            <th style="text-align: center;">Prix</th>
                            <th style="text-align: center;">Capacité</th>
                            <th style="text-align: center;">Vendus</th>
                            <th class="hide-sm">Organisateur</th>
                            <th style="text-align: center;">Statut</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr data-ticket-id="{{ $ticket->id }}">
                                <td>
                                    <div class="ticket-name">{{ $ticket->nom ?? $ticket->name ?? 'Ticket sans nom' }}</div>
                                    @if($ticket->description)
                                    <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem; max-width: 200px;" class="text-truncate">
                                        {{ Str::limit($ticket->description, 40) }}
                                    </div>
                                    @endif
                                    @if($ticket->montant_promotionnel)
                                    <div class="promotion-info">
                                        <i class="fas fa-tag"></i>
                                        Promotion: {{ number_format($ticket->montant_promotionnel, 0, ',', ' ') }} FCFA de réduction
                                        @if($ticket->promotion_end)
                                            <span style="color: var(--gray-500);">jusqu'au {{ \Carbon\Carbon::parse($ticket->promotion_end)->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->event)
                                        <a href="{{ route('events.show', $ticket->event) }}" class="event-link">
                                            {{ Str::limit($ticket->event->title ?? $ticket->event->name ?? 'Événement', 30) }}
                                        </a>
                                        <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                                            {{ $ticket->event->start_date ? \Carbon\Carbon::parse($ticket->event->start_date)->format('d/m/Y') : 'Date N/A' }}
                                        </div>
                                    @else
                                        <span style="color: var(--gray-500);">Événement supprimé</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($ticket->montant_promotionnel)
                                        <span class="original-price">{{ number_format($ticket->prix + $ticket->montant_promotionnel, 0, ',', ' ') }} FCFA</span>
                                        <span class="ticket-price">{{ number_format($ticket->prix, 0, ',', ' ') }}</span>
                                        <span class="price-currency">FCFA</span>
                                        <span class="promotion-badge">
                                            <i class="fas fa-tag"></i>
                                            -{{ number_format($ticket->montant_promotionnel, 0, ',', ' ') }}
                                        </span>
                                    @else
                                        <span class="ticket-price">{{ number_format($ticket->prix, 0, ',', ' ') }}</span>
                                        <span class="price-currency">FCFA</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span class="quantity-badge">{{ $ticket->quantite ?? $ticket->quantity ?? 0 }}</span>
                                </td>
                                <td style="text-align: center;">
                                    @php 
                                        $soldCount = $ticket->quantite_vendue ?? $ticket->sold_quantity ?? 0;
                                    @endphp
                                    <span class="sold-count">{{ $soldCount }}</span>
                                    @if(($ticket->quantite ?? $ticket->quantity ?? 0) > 0)
                                    <div style="font-size: 0.6875rem; color: var(--gray-500); margin-top: 0.125rem;">
                                        {{ round(($soldCount / ($ticket->quantite ?? $ticket->quantity ?? 1)) * 100) }}%
                                    </div>
                                    @endif
                                </td>
                                <td class="hide-sm">
                                    @if($ticket->event && $ticket->event->user)
                                        <span class="organizer-info">{{ $ticket->event->user->name ?? 'N/A' }}</span>
                                    @elseif($ticket->event && $ticket->event->organizer)
                                        <span class="organizer-info">{{ $ticket->event->organizer->company_name ?? 'N/A' }}</span>
                                    @else
                                        <span class="organizer-info">N/A</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @php 
                                        $quantity = $ticket->quantite ?? $ticket->quantity ?? 0;
                                        $sold = $ticket->quantite_vendue ?? $ticket->sold_quantity ?? 0;
                                    @endphp
                                    @if($ticket->montant_promotionnel)
                                        <span class="status-badge status-promotion">
                                            <i class="fas fa-tag"></i>
                                            Promotion
                                        </span>
                                    @elseif($quantity > $sold)
                                        <span class="status-badge status-available">
                                            <i class="fas fa-check-circle"></i>
                                            Disponible
                                        </span>
                                    @else
                                        <span class="status-badge status-sold-out">
                                            <i class="fas fa-times-circle"></i>
                                            Épuisé
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.tickets.edit', $ticket) }}" 
                                           class="action-btn warning" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Bouton Promotion -->
                                        <a href="{{ route('admin.tickets.promotion-form', $ticket) }}" 
                                           class="action-btn promotion" 
                                           title="{{ $ticket->montant_promotionnel ? 'Gérer la promotion' : 'Ajouter une promotion' }}">
                                            <i class="fas fa-tags"></i>
                                        </a>
                                        
                                        <!-- Bouton Supprimer -->
                                        <button type="button" 
                                                class="action-btn danger delete-ticket" 
                                                data-id="{{ $ticket->id }}"
                                                data-name="{{ $ticket->nom ?? $ticket->name ?? 'ce ticket' }}"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <p class="empty-text">Aucun ticket trouvé</p>
                                        <p class="empty-description">
                                            @if(request()->anyFilled(['search', 'status', 'event_id']))
                                                Aucun ticket ne correspond à vos critères de recherche.
                                            @else
                                                Aucun ticket n'a encore été créé sur la plateforme.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($tickets instanceof \Illuminate\Pagination\LengthAwarePaginator && $tickets->hasPages())
        <div class="card-footer">
            <div style="font-size: 0.875rem; color: var(--gray-600);">
                Affichage de {{ $tickets->firstItem() ?? 0 }} à {{ $tickets->lastItem() ?? 0 }} sur {{ $tickets->total() }} tickets
            </div>
            <div class="pagination-wrapper">
                {{ $tickets->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);">
                <h5 class="modal-title" style="color: white;">
                    <i class="fas fa-trash-alt"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="font-size: 3rem; color: var(--warning); margin-bottom: 1rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h6 style="font-weight: 600; color: var(--gray-900); margin-bottom: 1rem;">
                    Supprimer <span id="ticketName" style="color: var(--danger);"></span> ?
                </h6>
                <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 1.5rem;">
                    Cette action est <strong style="color: var(--danger);">irréversible</strong>. 
                    Toutes les ventes associées à ce ticket seront également impactées.
                </p>
                <div class="alert alert-warning" style="text-align: left; margin-bottom: 0;">
                    <i class="fas fa-info-circle"></i>
                    <span>Les utilisateurs ayant déjà acheté ce ticket ne pourront plus y accéder.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Annuler
                </button>
                <form id="deleteTicketForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'information sur la promotion -->
<div class="modal fade" id="promotionInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--warning) 0%, #f59e0b 100%);">
                <h5 class="modal-title" style="color: white;">
                    <i class="fas fa-tags"></i>
                    Gestion des promotions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body py-4">
                <div class="text-center mb-4">
                    <div style="font-size: 2.5rem; color: var(--warning); margin-bottom: 1rem;">
                        <i class="fas fa-percent"></i>
                    </div>
                    <h6 style="font-weight: 600; color: var(--gray-900);">
                        Vous allez être redirigé vers la page de gestion des promotions
                    </h6>
                </div>
                <p style="color: var(--gray-600); font-size: 0.875rem; text-align: center; margin-bottom: 0;">
                    Vous pourrez y définir le montant de la remise, la période de validité et une description.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Fermer
                </button>
                <a href="#" id="promotionLink" class="btn btn-warning" style="color: white;">
                    <i class="fas fa-arrow-right me-1"></i>
                    Continuer
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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

    // Remove onchange from selects to prevent automatic submission
    document.querySelectorAll('select[name="status"], select[name="event_id"]').forEach(select => {
        select.removeAttribute('onchange');
    });

    // Gestion de la suppression avec confirmation modale
    const deleteButtons = document.querySelectorAll('.delete-ticket');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteTicketModal'));
    const ticketNameSpan = document.getElementById('ticketName');
    const deleteForm = document.getElementById('deleteTicketForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const ticketId = this.dataset.id;
            const ticketName = this.dataset.name;
            
            // Mettre à jour le modal
            ticketNameSpan.textContent = `"${ticketName}"`;
            
            // Mettre à jour l'action du formulaire
            deleteForm.action = `/Administrateur/tickets/${ticketId}`;
            
            // Afficher le modal
            deleteModal.show();
        });
    });

    // Réinitialisation du modal quand il est fermé
    document.getElementById('deleteTicketModal').addEventListener('hidden.bs.modal', function() {
        ticketNameSpan.textContent = '';
        deleteForm.action = '#';
    });

    // Gestion des boutons de promotion avec modal d'information
    const promotionButtons = document.querySelectorAll('.action-btn.promotion');
    const promotionModal = new bootstrap.Modal(document.getElementById('promotionInfoModal'));
    const promotionLink = document.getElementById('promotionLink');

    promotionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const href = this.getAttribute('href');
            promotionLink.setAttribute('href', href);
            
            promotionModal.show();
        });
    });

    // Auto-fermeture des alertes après 4 secondes
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

    // Afficher un message si des promotions sont actives
    const promoCount = {{ $totalPromoTickets }};
    if (promoCount > 0) {
        console.log(`${promoCount} ticket(s) en promotion actuellement`);
    }
});
</script>
@endpush