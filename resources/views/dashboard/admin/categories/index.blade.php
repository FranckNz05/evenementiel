@extends('layouts.dashboard')

@section('title', 'Gestion des catégories')

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

.categories-page {
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
    background: white;
    color: var(--primary);
    border: 1px solid var(--primary);
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
}

/* Stats Grid - Optionnel si vous voulez ajouter des stats */
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

/* Card */
.card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
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
    grid-template-columns: 2fr 1fr;
    gap: 1rem;
    align-items: end;
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

.categories-table {
    width: 100%;
    border-collapse: collapse;
}

.categories-table thead {
    background: var(--gray-50);
}

.categories-table thead th {
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

.categories-table thead th[style*="text-align: center"] {
    text-align: center;
}

.categories-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.categories-table tbody tr:hover {
    background: var(--gray-50);
}

.categories-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

.categories-table tbody td[style*="text-align: center"] {
    text-align: center;
}

/* Category Image */
.category-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 0.5rem;
    border: 1px solid var(--gray-200);
}

.category-image-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 0.5rem;
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    font-size: 1.25rem;
    border: 1px solid var(--gray-200);
}

/* Slug */
.category-slug {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: var(--gray-100);
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-family: monospace;
    color: var(--gray-700);
    border: 1px solid var(--gray-200);
}

/* Events Count Badge */
.events-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.5rem;
    background: var(--gray-100);
    border-radius: 9999px;
    color: var(--gray-700);
    font-weight: 600;
    font-size: 0.75rem;
    border: 1px solid var(--gray-200);
}

.events-detail {
    font-size: 0.6875rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
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
    .categories-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .categories-table {
        font-size: 0.75rem;
    }
    
    .categories-table thead th,
    .categories-table tbody td {
        padding: 0.75rem 0.5rem;
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

.justify-content-end {
    justify-content: flex-end;
}

.gap-2 {
    gap: 0.5rem;
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
<div class="categories-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Gestion des catégories</h1>
            <p>Gérez les catégories d'événements et leurs informations</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nouvelle catégorie
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Alertes -->
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

    <!-- Filtres -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.categories.index') }}" id="filtersForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Recherche</label>
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Nom, slug..."
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>
                <div class="filter-group">
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            <i class="fas fa-search"></i>
                            Filtrer
                        </button>
                        @if(request()->has('search') && request('search') !== '')
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistiques (optionnel - à décommenter si vous avez les données) -->
    @if(isset($totalCategories) || isset($totalEvents))
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-label">Total catégories</div>
            <div class="stat-value">{{ $totalCategories ?? $categories->total() }}</div>
        </div>
        <div class="stat-card info">
            <div class="stat-label">Événements associés</div>
            <div class="stat-value">{{ $totalEvents ?? 0 }}</div>
        </div>
    </div>
    @endif

    <!-- Liste des catégories -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-tags"></i>
                Catégories d'événements
            </h5>
            <span style="font-size: 0.875rem; color: var(--gray-600);">{{ $categories->total() }} résultat(s)</span>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Image</th>
                            <th>Nom</th>
                            <th>Slug</th>
                            <th style="text-align: center;">Événements</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>
                                @if($category->image)
                                    <img src="{{ Storage::disk('public')->url($category->image) }}" 
                                         alt="{{ $category->name }}" 
                                         class="category-image"
                                         onerror="this.src='{{ asset('images/default-category.png') }}'; this.onerror=null;">
                                @else
                                    <div class="category-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--gray-900);">
                                    {{ $category->name }}
                                </div>
                                @if($category->description)
                                <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem; max-width: 250px;" class="text-truncate">
                                    {{ Str::limit($category->description, 50) }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <code class="category-slug">{{ $category->slug }}</code>
                            </td>
                            <td style="text-align: center;">
                                <div>
                                    <span class="events-count">
                                        {{ ($category->events_count ?? 0) + ($category->custom_events_count ?? 0) }}
                                    </span>
                                    @if(($category->events_count ?? 0) > 0 || ($category->custom_events_count ?? 0) > 0)
                                    <div class="events-detail">
                                        {{ $category->events_count ?? 0 }} public · {{ $category->custom_events_count ?? 0 }} privé
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.categories.show', $category) }}" 
                                       class="action-btn primary" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="action-btn warning" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="action-btn danger delete-category" 
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <p class="empty-text">Aucune catégorie trouvée</p>
                                    <p class="empty-description">
                                        @if(request()->has('search') && request('search') !== '')
                                            Aucune catégorie ne correspond à votre recherche.
                                        @else
                                            Commencez par créer votre première catégorie.
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
            @if($categories->hasPages())
            <div class="pagination-wrapper">
                {{ $categories->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
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
                    Supprimer la catégorie <span id="categoryName" style="color: var(--primary);"></span> ?
                </h6>
                <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 1.5rem;">
                    Cette action est <strong style="color: var(--danger);">irréversible</strong>. 
                    Les événements associés à cette catégorie ne seront pas supprimés mais perdront leur catégorisation.
                </p>
                <div class="alert alert-warning" style="text-align: left; margin-bottom: 0;">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ $category->events_count ?? 0 }} événement(s) public(s) et {{ $category->custom_events_count ?? 0 }} événement(s) personnalisé(s) sont associés à cette catégorie.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Annuler
                </button>
                <form id="deleteCategoryForm" method="POST" style="display: inline;">
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

    // Gestion de la suppression avec confirmation modale
    const deleteButtons = document.querySelectorAll('.delete-category');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
    const categoryNameSpan = document.getElementById('categoryName');
    const deleteForm = document.getElementById('deleteCategoryForm');
    const eventsAlert = document.querySelector('#deleteCategoryModal .alert-warning span');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const categoryId = this.dataset.id;
            const categoryName = this.dataset.name;
            
            // Récupérer les compteurs d'événements depuis la ligne
            const row = this.closest('tr');
            const eventsCell = row.querySelector('td:nth-child(4)');
            const eventsCount = eventsCell.querySelector('.events-count')?.textContent?.trim() || '0';
            const eventsDetail = eventsCell.querySelector('.events-detail')?.textContent?.trim() || '0 public · 0 privé';
            
            // Mettre à jour le modal
            categoryNameSpan.textContent = `"${categoryName}"`;
            if (eventsAlert) {
                eventsAlert.textContent = `${eventsCount} événement(s) (${eventsDetail}) sont associés à cette catégorie.`;
            }
            
            // Mettre à jour l'action du formulaire
            deleteForm.action = `/Administrateur/categories/${categoryId}`;
            
            // Afficher le modal
            deleteModal.show();
        });
    });

    // Réinitialisation du modal quand il est fermé
    document.getElementById('deleteCategoryModal').addEventListener('hidden.bs.modal', function() {
        categoryNameSpan.textContent = '';
        deleteForm.action = '#';
    });

    // Système de notification optionnel
    function showNotification(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
        alertDiv.style.marginTop = '1rem';
        alertDiv.style.transition = 'opacity 0.3s ease';
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const pageHeader = document.querySelector('.page-header');
        pageHeader.parentNode.insertBefore(alertDiv, pageHeader.nextSibling);
        
        setTimeout(() => {
            alertDiv.style.opacity = '0';
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 300);
        }, 4000);
    }

    // Suppression automatique des alertes après 4 secondes
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