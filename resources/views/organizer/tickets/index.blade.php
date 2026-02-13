@extends('layouts.dashboard')

@section('title', 'Billets')

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

.btn-info {
    background: var(--info);
    color: white;
}

.btn-info:hover {
    background: #2563eb;
}

.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.btn-outline-primary {
    background: white;
    color: var(--primary);
    border: 1px solid var(--primary);
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
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
}

.stat-number small {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-500);
    margin-left: 0.25rem;
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

.event-name {
    font-size: 0.75rem;
    color: var(--gray-500);
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Price */
.ticket-price {
    font-weight: 700;
    color: var(--success);
    font-size: 0.9375rem;
}

.price-currency {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-left: 0.25rem;
}

/* Quantity Badges */
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

.sold-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.5rem;
    background: var(--primary);
    border-radius: 0.375rem;
    color: white;
    font-weight: 600;
    font-size: 0.8125rem;
}

.available-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.available-high {
    background: #d1fae5;
    color: #065f46;
}

.available-medium {
    background: #fef3c7;
    color: #92400e;
}

.available-low {
    background: #fee2e2;
    color: #991b1b;
}

/* Promotion Info */
.promotion-info {
    font-size: 0.75rem;
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin-top: 0.25rem;
}

.promotion-info i {
    color: var(--warning);
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

.action-btn.danger:hover {
    background: var(--danger);
    border-color: var(--danger);
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

.alert-link {
    color: var(--primary);
    font-weight: 600;
    text-decoration: underline;
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

.modal-header.bg-danger {
    background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%) !important;
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
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 1rem; }
.mb-0 { margin-bottom: 0; }
.mb-4 { margin-bottom: 1.5rem; }
.mt-4 { margin-top: 1.5rem; }
.me-1 { margin-right: 0.25rem; }
.me-2 { margin-right: 0.5rem; }
.ms-auto { margin-left: auto; }
.p-3 { padding: 1rem; }
.w-100 { width: 100%; }
.text-center { text-align: center; }
.text-muted { color: var(--gray-500) !important; }
.small { font-size: 0.75rem; }
</style>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="tickets-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-ticket-alt"></i>
                Billets
            </h1>
            <p>Gérez les billets et tarifs de vos événements</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('organizer.events.index') }}" class="btn btn-secondary">
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
    @php
        $totalTickets = $tickets->total ?? (is_countable($tickets) ? $tickets->count() : 0);
        $totalSold = $tickets->sum(function($ticket) {
            return $ticket->sold_count ?? $ticket->quantite_vendue ?? 0;
        });
        $totalRevenue = $tickets->sum(function($ticket) {
            $sold = $ticket->sold_count ?? $ticket->quantite_vendue ?? 0;
            $price = $ticket->prix ?? $ticket->price ?? 0;
            return $sold * $price;
        });
        $totalPromo = $tickets->where('montant_promotionnel', '>', 0)->count();
    @endphp

    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total billets</div>
                    <div class="stat-number">{{ number_format($totalTickets, 0, ',', ' ') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Billets vendus</div>
                    <div class="stat-number">{{ number_format($totalSold, 0, ',', ' ') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card info">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Revenus</div>
                    <div class="stat-number">{{ number_format($totalRevenue, 0, ',', ' ') }} <small>FCFA</small></div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">En promotion</div>
                    <div class="stat-number">{{ number_format($totalPromo, 0, ',', ' ') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des billets -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-ticket-alt"></i>
                Liste des billets
            </h5>
            <span style="font-size: 0.875rem; color: var(--gray-600);">
                {{ $totalTickets }} résultat(s)
                @if($tickets instanceof \Illuminate\Pagination\LengthAwarePaginator && $tickets->total() > 0)
                    · Page {{ $tickets->currentPage() }}/{{ $tickets->lastPage() }}
                @endif
            </span>
        </div>
        <div class="card-body">
            @if($tickets->count() > 0)
            <div class="table-wrapper">
                <table class="tickets-table" id="ticketsTable">
                    <thead>
                        <tr>
                            <th>Nom du billet</th>
                            <th>Événement</th>
                            <th style="text-align: center;">Prix</th>
                            <th style="text-align: center;">Quantité</th>
                            <th style="text-align: center;">Vendus</th>
                            <th style="text-align: center;">Disponibles</th>
                            <th class="hide-md">Période de vente</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>
                                <div class="ticket-name">{{ $ticket->nom ?? $ticket->name ?? 'Sans nom' }}</div>
                                @if($ticket->montant_promotionnel > 0)
                                <div class="promotion-info">
                                    <i class="fas fa-tag"></i>
                                    Promotion: -{{ number_format($ticket->montant_promotionnel, 0, ',', ' ') }} FCFA
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="ticket-name">{{ $ticket->event->title ?? $ticket->event->titre ?? 'Événement inconnu' }}</div>
                                <div class="event-name">{{ $ticket->event->start_date ? $ticket->event->start_date->format('d/m/Y') : '' }}</div>
                            </td>
                            <td style="text-align: center;">
                                @if($ticket->montant_promotionnel > 0)
                                    <span style="text-decoration: line-through; color: var(--gray-400); font-size: 0.75rem; margin-right: 0.25rem;">
                                        {{ number_format(($ticket->prix ?? $ticket->price ?? 0) + $ticket->montant_promotionnel, 0, ',', ' ') }}
                                    </span>
                                @endif
                                <span class="ticket-price">
                                    {{ number_format($ticket->prix ?? $ticket->price ?? 0, 0, ',', ' ') }}
                                </span>
                                <span class="price-currency">FCFA</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="quantity-badge">{{ $ticket->quantite ?? $ticket->quantity ?? 0 }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="sold-badge">{{ $ticket->sold_count ?? $ticket->quantite_vendue ?? 0 }}</span>
                            </td>
                            <td style="text-align: center;">
                                @php
                                    $total = $ticket->quantite ?? $ticket->quantity ?? 0;
                                    $sold = $ticket->sold_count ?? $ticket->quantite_vendue ?? 0;
                                    $available = $total - $sold;
                                    $percentage = $total > 0 ? ($available / $total) * 100 : 0;
                                @endphp
                                <span class="available-badge 
                                    @if($percentage >= 50) available-high
                                    @elseif($percentage >= 20) available-medium
                                    @else available-low
                                    @endif">
                                    <i class="fas fa-{{ $percentage >= 50 ? 'check-circle' : ($percentage >= 20 ? 'clock' : 'exclamation-circle') }}"></i>
                                    {{ $available }}
                                </span>
                            </td>
                            <td class="hide-md">
                                <small>
                                    @if($ticket->promotion_start)
                                        <span style="font-weight: 600; color: var(--warning);">
                                            <i class="fas fa-calendar-alt"></i> Promo
                                        </span><br>
                                        du {{ $ticket->promotion_start instanceof \Carbon\Carbon ? $ticket->promotion_start->format('d/m/Y') : $ticket->promotion_start }}<br>
                                        au {{ $ticket->promotion_end ? ($ticket->promotion_end instanceof \Carbon\Carbon ? $ticket->promotion_end->format('d/m/Y') : $ticket->promotion_end) : 'Non défini' }}
                                    @else
                                        <span style="color: var(--gray-500);">
                                            <i class="fas fa-calendar"></i> Vente continue
                                        </span>
                                    @endif
                                </small>
                            </td>
                            <td style="text-align: center;">
                                <div class="action-buttons">
                                    <a href="{{ route('organizer.tickets.show', $ticket) }}" class="action-btn info" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('organizer.tickets.edit', $ticket) }}" class="action-btn primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="action-btn danger delete-ticket" 
                                            data-id="{{ $ticket->id }}" 
                                            data-name="{{ $ticket->nom ?? $ticket->name ?? 'ce billet' }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($tickets instanceof \Illuminate\Pagination\LengthAwarePaginator && $tickets->hasPages())
            <div class="pagination-wrapper">
                {{ $tickets->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
            </div>
            @endif
            @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <p class="empty-text">Aucun billet créé</p>
                <p class="empty-description">
                    Vous n'avez pas encore créé de billets.
                    <a href="{{ route('organizer.events.index') }}" style="color: var(--primary); font-weight: 600;">Créez d'abord un événement</a>, 
                    puis ajoutez des billets à cet événement.
                </p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    Toutes les ventes associées à ce billet seront également impactées.
                </p>
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
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===========================================
    // INITIALISATION DATATABLES
    // ===========================================
    if (typeof $.fn.DataTable !== 'undefined' && $('#ticketsTable').length && $('#ticketsTable tbody tr').length > 1) {
        $('#ticketsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json',
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tous"]],
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [7] }
            ]
        });
    }

    // ===========================================
    // CSRF TOKEN
    // ===========================================
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ===========================================
    // SUPPRESSION DE BILLET - MODAL
    // ===========================================
    const deleteButtons = document.querySelectorAll('.delete-ticket');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteTicketModal'));
    const ticketNameSpan = document.getElementById('ticketName');
    const deleteForm = document.getElementById('deleteTicketForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const ticketId = this.dataset.id;
            const ticketName = this.dataset.name;
            
            ticketNameSpan.textContent = `"${ticketName}"`;
            deleteForm.action = `/organizer/tickets/${ticketId}`;
            
            deleteModal.show();
        });
    });

    // ===========================================
    // RÉINITIALISATION DU MODAL
    // ===========================================
    document.getElementById('deleteTicketModal').addEventListener('hidden.bs.modal', function() {
        ticketNameSpan.textContent = '';
        deleteForm.action = '#';
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
</script>
@endpush