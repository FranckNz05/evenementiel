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

.scans-table thead th[style*="text-align: center"] {
    text-align: center;
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

.scans-table tbody td[style*="text-align: center"] {
    text-align: center;
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

/* User Info */
.user-info {
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

/* Scanned By */
.scanned-by {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.scanned-by-avatar {
    width: 32px;
    height: 32px;
    border-radius: 9999px;
    background: var(--gray-200);
    color: var(--gray-600);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
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

.status-valid {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.status-invalid {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
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
    
    .scanned-by {
        flex-direction: column;
        align-items: flex-start;
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
.text-success { color: var(--success) !important; }
.text-danger { color: var(--danger) !important; }

.fw-semibold { font-weight: 600; }
.fw-bold { font-weight: 700; }
.small { font-size: 0.75rem; }

.bg-light { background: var(--gray-100); }
.rounded { border-radius: 0.5rem; }
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
            <p>Consultez l'historique complet des validations de billets</p>
        </div>
        <div class="page-actions">
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

    <!-- Statistiques -->
    @php
        $totalScans = $scans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $scans->total() : (is_countable($scans) ? count($scans) : 0);
        $validScans = $scans->where('is_valid', true)->count() ?? 0;
        $invalidScans = $scans->where('is_valid', false)->count() ?? 0;
        $uniqueEvents = $scans->pluck('ticket.event.id')->unique()->filter()->count() ?? 0;
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total scans</div>
                    <div class="stat-number">{{ number_format($totalScans, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-qrcode"></i>
                        Validations effectuées
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
            </div>
            <div class="stat-gradient bg-primary"></div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Billets valides</div>
                    <div class="stat-number">{{ number_format($validScans, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-check-circle text-success"></i>
                        Validés avec succès
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-gradient bg-success"></div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Billets invalides</div>
                    <div class="stat-number">{{ number_format($invalidScans, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-times-circle text-danger"></i>
                        Rejetés
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <div class="stat-gradient bg-danger"></div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Événements</div>
                    <div class="stat-number">{{ number_format($uniqueEvents, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-calendar-alt"></i>
                        Concernés
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <div class="stat-gradient bg-info"></div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
        <form action="{{ route('organizer.scans.history') }}" method="GET" id="filtersForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="event_id" class="filter-label">Événement</label>
                    <select class="form-select" id="event_id" name="event_id">
                        <option value="">Tous les événements</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title ?? $event->name ?? 'Sans titre' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date_from" class="filter-label">Date de début</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label for="date_to" class="filter-label">Date de fin</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="filter-group" style="display: flex; flex-direction: row; align-items: flex-end; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-filter"></i>
                        Filtrer
                    </button>
                    @if(request()->anyFilled(['event_id', 'date_from', 'date_to']))
                    <a href="{{ route('organizer.scans.history') }}" class="btn btn-secondary" title="Réinitialiser">
                        <i class="fas fa-redo"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tableau des scans -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-history"></i>
                Historique complet des scans
            </h5>
            <span style="font-size: 0.875rem; color: var(--gray-600);">
                {{ $totalScans }} résultat(s)
                @if($scans instanceof \Illuminate\Pagination\LengthAwarePaginator && $scans->total() > 0)
                    · Page {{ $scans->currentPage() }}/{{ $scans->lastPage() }}
                @endif
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-wrapper">
                <table class="scans-table" id="dataTable">
                    <thead>
                        <tr>
                            <th>Date/Heure</th>
                            <th>Événement</th>
                            <th>Billet</th>
                            <th>Client</th>
                            <th>Scanné par</th>
                            <th style="text-align: center;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scans as $scan)
                        <tr>
                            <td>
                                <div class="scan-datetime">
                                    <span class="scan-date">{{ $scan->scanned_at ? $scan->scanned_at->format('d/m/Y') : 'N/A' }}</span>
                                    <span class="scan-time">{{ $scan->scanned_at ? $scan->scanned_at->format('H:i:s') : '' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="event-title">{{ $scan->ticket->event->title ?? 'N/A' }}</div>
                                <div class="ticket-name">{{ $scan->ticket->event->start_date ? $scan->ticket->event->start_date->format('d/m/Y') : '' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $scan->ticket->nom ?? $scan->ticket->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $scan->ticket->reference ?? $scan->ticket->id }}</small>
                            </td>
                            <td>
                                <div class="user-info">
                                    <span class="user-name">{{ $scan->order->user->prenom ?? '' }} {{ $scan->order->user->nom ?? $scan->order->user->name ?? 'N/A' }}</span>
                                    <span class="user-email">{{ $scan->order->user->email ?? '' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="scanned-by">
                                    <div class="scanned-by-avatar">
                                        {{ substr($scan->scannedBy->prenom ?? $scan->scannedBy->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $scan->scannedBy->prenom ?? '' }} {{ $scan->scannedBy->nom ?? $scan->scannedBy->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $scan->scannedBy->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                @if($scan->is_valid)
                                    <span class="status-badge status-valid">
                                        <i class="fas fa-check-circle"></i>
                                        Valide
                                    </span>
                                @else
                                    <span class="status-badge status-invalid">
                                        <i class="fas fa-times-circle"></i>
                                        Invalide
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <p class="empty-text">Aucun scan trouvé</p>
                                    <p class="empty-description">
                                        @if(request()->anyFilled(['event_id', 'date_from', 'date_to']))
                                            Aucun scan ne correspond à vos critères de recherche.
                                        @else
                                            Commencez par scanner des billets pour voir l'historique.
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

        <!-- Pagination -->
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
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    // AUTO-SUBMIT SUR ENTRÉE (RECHERCHE)
    // ===========================================
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filtersForm').submit();
            }
        });
    }

    // ===========================================
    // RÉINITIALISATION DES FILTRES
    // ===========================================
    document.querySelectorAll('select[name="event_id"]').forEach(select => {
        select.removeAttribute('onchange');
    });
});
</script>
@endpush