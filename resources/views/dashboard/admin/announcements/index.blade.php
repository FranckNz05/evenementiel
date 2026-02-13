@extends('layouts.dashboard')

@section('title', 'Gestion des annonces')

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

.announcements-page {
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

.btn-warning {
    background: var(--warning);
    color: white;
}

.btn-warning:hover {
    background: #d97706;
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

.btn-outline-success {
    background: transparent;
    border: 1px solid var(--success);
    color: var(--success);
}

.btn-outline-success:hover,
.btn-outline-success.active {
    background: var(--success);
    color: white;
}

.btn-outline-danger {
    background: transparent;
    border: 1px solid var(--danger);
    color: var(--danger);
}

.btn-outline-danger:hover,
.btn-outline-danger.active {
    background: var(--danger);
    color: white;
}

.btn-outline-secondary {
    background: transparent;
    border: 1px solid var(--gray-300);
    color: var(--gray-700);
}

.btn-outline-secondary:hover,
.btn-outline-secondary.active {
    background: var(--gray-700);
    color: white;
    border-color: var(--gray-700);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
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

.filters-row {
    display: flex;
    align-items: flex-end;
    gap: 1rem;
    flex-wrap: wrap;
}

@media (min-width: 768px) {
    .filters-row {
        flex-wrap: nowrap;
    }
}

.filter-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-700);
}

.search-input-group {
    position: relative;
    display: flex;
    width: 100%;
}

.search-input-group .form-control {
    flex: 1;
    padding: 0.625rem 0.875rem;
    padding-left: 2.5rem;
    border: 1px solid var(--gray-300);
    border-radius: 0.5rem 0 0 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.search-input-group .form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
    z-index: 1;
}

.search-input-group .btn {
    border-radius: 0 0.5rem 0.5rem 0;
    margin-left: -1px;
}

.search-icon {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    pointer-events: none;
    z-index: 2;
}

.form-control {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 1px solid var(--gray-300);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.status-filters {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Table */
.table-wrapper {
    overflow-x: auto;
}

.announcements-table {
    width: 100%;
    border-collapse: collapse;
}

.announcements-table thead {
    background: var(--gray-50);
}

.announcements-table thead th {
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

.announcements-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.announcements-table tbody tr:hover {
    background: var(--gray-50);
}

.announcements-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

/* Announcement Info */
.announcement-title {
    font-weight: 600;
    color: var(--gray-900);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.urgent-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: #fee2e2;
    color: #991b1b;
    border-radius: 9999px;
    font-size: 0.625rem;
    font-weight: 600;
    border: 1px solid #ef4444;
}

.announcement-content {
    font-size: 0.8125rem;
    color: var(--gray-600);
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Order Badge */
.order-badge {
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

/* Period Info */
.period-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.period-dates {
    font-weight: 500;
    color: var(--gray-900);
    font-size: 0.8125rem;
}

.period-type {
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

.status-active {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
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

/* Loading State */
.loading-state {
    opacity: 0.7;
    pointer-events: none;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.fa-spinner {
    animation: spin 0.8s linear infinite;
}

/* Responsive */
@media (max-width: 768px) {
    .announcements-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .announcements-table {
        font-size: 0.75rem;
    }
    
    .announcements-table thead th,
    .announcements-table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-input-group {
        width: 100%;
    }
    
    .status-filters {
        width: 100%;
        justify-content: stretch;
    }
    
    .status-filters .btn {
        flex: 1;
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

.mt-3 {
    margin-top: 1rem;
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
<div class="announcements-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Gestion des annonces</h1>
            <p>Créez et gérez les annonces et communications importantes</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nouvelle annonce
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.announcements.index') }}" id="filtersForm">
            <div class="filters-row">
                <div class="filter-group">
                    <label class="filter-label">Recherche</label>
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Titre, contenu..."
                            value="{{ request('search') }}"
                            id="announcementSearch"
                        >
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Statut</label>
                    <div class="status-filters">
                        <a href="{{ route('admin.announcements.index') }}" 
                           class="btn {{ !request('status') ? 'btn-outline-secondary active' : 'btn-outline-secondary' }}">
                            Toutes
                        </a>
                        <a href="{{ route('admin.announcements.index', ['status' => 'active']) }}" 
                           class="btn {{ request('status') == 'active' ? 'btn-outline-success active' : 'btn-outline-success' }}">
                            Actives
                        </a>
                        <a href="{{ route('admin.announcements.index', ['status' => 'inactive']) }}" 
                           class="btn {{ request('status') == 'inactive' ? 'btn-outline-danger active' : 'btn-outline-danger' }}">
                            Inactives
                        </a>
                    </div>
                </div>
                @if(request()->anyFilled(['search', 'status']))
                <div class="filter-group" style="flex: 0 0 auto;">
                    <label class="filter-label">&nbsp;</label>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Réinitialiser
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Liste des annonces -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-bullhorn"></i>
                Liste des annonces
            </h5>
            <span style="font-size: 0.875rem; color: var(--gray-600);">
                {{ $announcements->total() }} résultat(s)
                @if($announcements instanceof \Illuminate\Pagination\LengthAwarePaginator && $announcements->total() > 0)
                    · Page {{ $announcements->currentPage() }}/{{ $announcements->lastPage() }}
                @endif
            </span>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="announcements-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Contenu</th>
                            <th style="text-align: center;">Ordre</th>
                            <th>Période</th>
                            <th style="text-align: center;">Statut</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                        <tr data-announcement-id="{{ $announcement->id }}">
                            <td>
                                <div class="announcement-title">
                                    <span>{{ $announcement->title }}</span>
                                    @if($announcement->is_urgent)
                                        <span class="urgent-badge">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Urgent
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="announcement-content" title="{{ $announcement->content }}">
                                    {{ Str::limit(strip_tags($announcement->content), 50) }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="order-badge">{{ $announcement->display_order ?? $announcement->order ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="period-info">
                                    @if($announcement->start_date && $announcement->end_date)
                                        <span class="period-dates">
                                            Du {{ \Carbon\Carbon::parse($announcement->start_date)->format('d/m/Y') }}
                                            au {{ \Carbon\Carbon::parse($announcement->end_date)->format('d/m/Y') }}
                                        </span>
                                        <span class="period-type">Période définie</span>
                                    @elseif($announcement->start_date)
                                        <span class="period-dates">
                                            À partir du {{ \Carbon\Carbon::parse($announcement->start_date)->format('d/m/Y') }}
                                        </span>
                                        <span class="period-type">Début programmé</span>
                                    @elseif($announcement->end_date)
                                        <span class="period-dates">
                                            Jusqu'au {{ \Carbon\Carbon::parse($announcement->end_date)->format('d/m/Y') }}
                                        </span>
                                        <span class="period-type">Fin programmée</span>
                                    @else
                                        <span class="period-dates">Permanente</span>
                                        <span class="period-type">Sans limite</span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="status-badge status-{{ $announcement->is_active ? 'active' : 'inactive' }}">
                                    <i class="fas fa-{{ $announcement->is_active ? 'check-circle' : 'times-circle' }}"></i>
                                    {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.announcements.edit', $announcement) }}" 
                                       class="action-btn primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="action-btn toggle-status-btn {{ $announcement->is_active ? 'warning' : 'success' }}"
                                            data-id="{{ $announcement->id }}"
                                            data-current-status="{{ $announcement->is_active ? 'active' : 'inactive' }}"
                                            title="{{ $announcement->is_active ? 'Désactiver' : 'Activer' }}">
                                        <i class="fas fa-{{ $announcement->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                    <button type="button" 
                                            class="action-btn danger delete-announcement"
                                            data-id="{{ $announcement->id }}"
                                            data-title="{{ $announcement->title }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                    <p class="empty-text">Aucune annonce trouvée</p>
                                    <p class="empty-description">
                                        @if(request()->anyFilled(['search', 'status']))
                                            Aucune annonce ne correspond à vos critères de recherche.
                                        @else
                                            Commencez par créer votre première annonce.
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
            @if($announcements instanceof \Illuminate\Pagination\LengthAwarePaginator && $announcements->hasPages())
            <div class="pagination-wrapper">
                {{ $announcements->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteAnnouncementModal" tabindex="-1" aria-hidden="true">
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
                    Supprimer l'annonce <span id="announcementTitle" style="color: var(--danger);"></span> ?
                </h6>
                <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 1.5rem;">
                    Cette action est <strong style="color: var(--danger);">irréversible</strong>. 
                    L'annonce sera définitivement supprimée de la plateforme.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Annuler
                </button>
                <form id="deleteAnnouncementForm" method="POST" style="display: inline;">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Script des annonces démarré !');

    // Configuration toastr
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "showDuration": "200",
        "hideDuration": "800",
        "timeOut": "4000",
        "extendedTimeOut": "800",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
    };

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // ===========================================
    // TOGGLE STATUS - Changement de statut AJAX
    // ===========================================
    const toggleButtons = document.querySelectorAll('.toggle-status-btn');
    console.log('🔍 Boutons toggle trouvés:', toggleButtons.length);

    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const announcementId = this.dataset.id;
            const currentStatus = this.dataset.currentStatus;
            const row = this.closest('tr');
            const statusBadge = row.querySelector('.status-badge');
            const icon = this.querySelector('i');
            
            // Sauvegarder l'état original
            const originalHtml = this.innerHTML;
            const originalClass = this.className;
            
            // État de chargement
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;
            this.classList.add('loading-state');
            
            fetch(`/Administrateur/announcements/${announcementId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mettre à jour le badge de statut
                    statusBadge.className = `status-badge status-${data.status_class}`;
                    statusBadge.innerHTML = `<i class="fas fa-${data.status_icon}"></i> ${data.status_text}`;
                    
                    // Mettre à jour le bouton
                    this.className = `action-btn toggle-status-btn ${data.button_class}`;
                    this.innerHTML = `<i class="fas fa-${data.button_icon}"></i>`;
                    this.dataset.currentStatus = data.is_active ? 'active' : 'inactive';
                    this.title = data.is_active ? 'Désactiver' : 'Activer';
                    
                    // Notification
                    toastr.success(data.message);
                } else {
                    throw new Error(data.message || 'Erreur lors du changement de statut');
                }
            })
            .catch(error => {
                console.error('❌ Erreur toggle:', error);
                toastr.error(error.message || 'Erreur lors du changement de statut');
                
                // Restaurer l'état original
                this.innerHTML = originalHtml;
                this.className = originalClass;
                this.disabled = false;
                this.classList.remove('loading-state');
            });
        });
    });

    // ===========================================
    // SUPPRESSION - Confirmation modale
    // ===========================================
    const deleteButtons = document.querySelectorAll('.delete-announcement');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteAnnouncementModal'));
    const announcementTitleSpan = document.getElementById('announcementTitle');
    const deleteForm = document.getElementById('deleteAnnouncementForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const announcementId = this.dataset.id;
            const announcementTitle = this.dataset.title;
            
            announcementTitleSpan.textContent = `"${announcementTitle}"`;
            deleteForm.action = `/Administrateur/announcements/${announcementId}`;
            
            deleteModal.show();
        });
    });

    // Réinitialisation du modal de suppression
    document.getElementById('deleteAnnouncementModal').addEventListener('hidden.bs.modal', function() {
        announcementTitleSpan.textContent = '';
        deleteForm.action = '#';
    });

    // ===========================================
    // RECHERCHE - Auto-submit on Enter
    // ===========================================
    const searchInput = document.getElementById('announcementSearch');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filtersForm').submit();
            }
        });
    }

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

    console.log('✅ Script des annonces chargé complètement !');
});
</script>
@endpush