@extends('layouts.dashboard')

@section('title', 'Gestion des blogs')

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

.blogs-page {
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

.btn-info {
    background: var(--info);
    color: white;
}

.btn-info:hover {
    background: #2563eb;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
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

/* Table */
.table-wrapper {
    overflow-x: auto;
}

.blogs-table {
    width: 100%;
    border-collapse: collapse;
}

.blogs-table thead {
    background: var(--gray-50);
}

.blogs-table thead th {
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

.blogs-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.blogs-table tbody tr:hover {
    background: var(--gray-50);
}

.blogs-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

/* Blog Image */
.blog-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 0.5rem;
    border: 1px solid var(--gray-200);
}

.blog-image-placeholder {
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

/* Blog Info */
.blog-title {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.blog-excerpt {
    font-size: 0.75rem;
    color: var(--gray-500);
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Author Info */
.author-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.author-name {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 0.875rem;
}

.author-email {
    font-size: 0.75rem;
    color: var(--gray-500);
}

/* Category Badge */
.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    background: var(--info-bg);
    color: #1e40af;
    border: 1px solid var(--info);
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

.status-deleted {
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

.action-btn.info:hover {
    background: var(--info);
    border-color: var(--info);
    color: white;
}

/* Categories Table */
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
}

.categories-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.categories-table tbody tr:hover {
    background: var(--gray-50);
}

.categories-table tbody td {
    padding: 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

.category-name {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.category-slug {
    font-size: 0.75rem;
    color: var(--gray-500);
    font-family: monospace;
}

.category-description {
    font-size: 0.8125rem;
    color: var(--gray-600);
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.articles-count {
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
    .blogs-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .blogs-table {
        font-size: 0.75rem;
    }
    
    .blogs-table thead th,
    .blogs-table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-input-group {
        width: 100%;
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

.justify-content-center {
    justify-content: center;
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

.mt-4 {
    margin-top: 1.5rem;
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
<div class="blogs-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Gestion des blogs</h1>
            <p>Gérez les articles de blog et leurs catégories</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nouvel article
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    @php
        $totalBlogs = $blogs instanceof \Illuminate\Pagination\LengthAwarePaginator ? $blogs->total() : (is_countable($blogs) ? count($blogs) : 0);
        $totalCategories = $blog_categories instanceof \Illuminate\Pagination\LengthAwarePaginator ? $blog_categories->total() : (is_countable($blog_categories) ? count($blog_categories) : 0);
        $activeBlogs = $blogs instanceof \Illuminate\Pagination\LengthAwarePaginator 
            ? $blogs->where('deleted_at', null)->count() 
            : (is_countable($blogs) ? collect($blogs)->where('deleted_at', null)->count() : 0);
    @endphp

    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $totalBlogs }}</div>
                    <div class="stat-label">Total articles</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-blog"></i>
                </div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $activeBlogs }}</div>
                    <div class="stat-label">Articles actifs</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="stat-card info">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $totalCategories }}</div>
                    <div class="stat-label">Catégories</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
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

    <!-- Filtres -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.blogs.index') }}" id="filtersForm">
            <div class="filters-row">
                <div class="filter-group">
                    <label class="filter-label">Recherche</label>
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Titre, contenu, auteur..."
                            value="{{ request('search') }}"
                            id="blogSearch"
                        >
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Catégorie</label>
                    <select name="category" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach($blog_categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Supprimé</option>
                    </select>
                </div>
                <div class="filter-group" style="flex: 0 0 auto;">
                    <label class="filter-label">&nbsp;</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        @if(request()->anyFilled(['search', 'category', 'status']))
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Liste des articles -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-blog"></i>
                Articles de blog
            </h5>
            <span style="font-size: 0.875rem; color: var(--gray-600);">
                {{ $totalBlogs }} résultat(s)
                @if($blogs instanceof \Illuminate\Pagination\LengthAwarePaginator && $blogs->total() > 0)
                    · Page {{ $blogs->currentPage() }}/{{ $blogs->lastPage() }}
                @endif
            </span>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="blogs-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Image</th>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Auteur</th>
                            <th>Date</th>
                            <th style="text-align: center;">Statut</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $blog)
                        <tr>
                            <td>
                                @if($blog->image)
                                    <img src="{{ asset('storage/' . $blog->image) }}" 
                                         alt="{{ $blog->title }}" 
                                         class="blog-image"
                                         onerror="this.src='{{ asset('images/default-blog.png') }}'; this.onerror=null;">
                                @else
                                    <div class="blog-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="blog-title">{{ $blog->title }}</div>
                                <div class="blog-excerpt" title="{{ strip_tags($blog->content) }}">
                                    {{ Str::limit(strip_tags($blog->content), 50) }}
                                </div>
                            </td>
                            <td>
                                @if($blog->blogcategories)
                                    <span class="category-badge">
                                        <i class="fas fa-tag"></i>
                                        {{ $blog->blogcategories->name }}
                                    </span>
                                @else
                                    <span class="category-badge" style="background: var(--gray-200); color: var(--gray-700); border-color: var(--gray-400);">
                                        <i class="fas fa-tag"></i>
                                        Sans catégorie
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="author-info">
                                    <span class="author-name">{{ $blog->user->nom ?? 'Auteur inconnu' }}</span>
                                    <span class="author-email">{{ $blog->user->email ?? '' }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.8125rem; color: var(--gray-600);">
                                    {{ $blog->created_at ? $blog->created_at->format('d/m/Y') : 'N/A' }}
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">
                                        {{ $blog->created_at ? $blog->created_at->format('H:i') : '' }}
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                @if($blog->deleted_at)
                                    <span class="status-badge status-deleted">
                                        <i class="fas fa-trash"></i>
                                        Supprimé
                                    </span>
                                @else
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle"></i>
                                        Actif
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.blogs.show', $blog) }}" 
                                       class="action-btn primary" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.blogs.edit', $blog) }}" 
                                       class="action-btn warning" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$blog->deleted_at)
                                    <button type="button" 
                                            class="action-btn danger delete-blog" 
                                            data-id="{{ $blog->id }}"
                                            data-title="{{ $blog->title }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-blog"></i>
                                    </div>
                                    <p class="empty-text">Aucun article trouvé</p>
                                    <p class="empty-description">
                                        @if(request()->anyFilled(['search', 'category', 'status']))
                                            Aucun article ne correspond à vos critères de recherche.
                                        @else
                                            Commencez par créer votre premier article de blog.
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
            @if($blogs instanceof \Illuminate\Pagination\LengthAwarePaginator && $blogs->hasPages())
            <div class="pagination-wrapper">
                {{ $blogs->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Gestion des catégories -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-tags"></i>
                Catégories de blog
            </h5>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus"></i>
                    Nouvelle catégorie
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th style="text-align: center;">Articles</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blog_categories as $category)
                        <tr>
                            <td>
                                <div class="category-name">{{ $category->name }}</div>
                                <div class="category-slug">{{ $category->slug }}</div>
                            </td>
                            <td>
                                <div class="category-description" title="{{ $category->description }}">
                                    {{ Str::limit($category->description, 100) ?: 'Aucune description' }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="articles-count">{{ $category->blogs_count ?? 0 }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="action-buttons">
                                    <button type="button" 
                                            class="action-btn warning edit-category" 
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-description="{{ $category->description }}"
                                            title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="action-btn danger delete-category" 
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-count="{{ $category->blogs_count ?? 0 }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state" style="padding: 2rem 1rem;">
                                    <div class="empty-icon" style="font-size: 2rem;">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <p class="empty-text">Aucune catégorie</p>
                                    <p class="empty-description">Créez votre première catégorie de blog</p>
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

<!-- Modal Ajout Catégorie -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i>
                    Nouvelle catégorie
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.blog-categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label" style="font-weight: 600; color: var(--gray-700);">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label" style="font-weight: 600; color: var(--gray-700);">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Édition Catégorie -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i>
                    Modifier la catégorie
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label" style="font-weight: 600; color: var(--gray-700);">Nom</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label" style="font-weight: 600; color: var(--gray-700);">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmation Suppression Blog -->
<div class="modal fade" id="deleteBlogModal" tabindex="-1" aria-hidden="true">
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
                    Supprimer l'article <span id="blogTitle" style="color: var(--danger);"></span> ?
                </h6>
                <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 1.5rem;">
                    Cette action est <strong style="color: var(--danger);">irréversible</strong>. 
                    L'article sera définitivement supprimé de la plateforme.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Annuler
                </button>
                <form id="deleteBlogForm" method="POST" style="display: inline;">
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

<!-- Modal Confirmation Suppression Catégorie -->
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
                    Supprimer la catégorie <span id="categoryName" style="color: var(--danger);"></span> ?
                </h6>
                <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 1.5rem;">
                    Cette action est <strong style="color: var(--danger);">irréversible</strong>.
                    <span id="categoryArticlesCount"></span>
                </p>
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
    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // ===========================================
    // RECHERCHE - Auto-submit on Enter
    // ===========================================
    const searchInput = document.getElementById('blogSearch');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filtersForm').submit();
            }
        });
    }

    // Remove onchange from selects
    document.querySelectorAll('select[name="category"], select[name="status"]').forEach(select => {
        select.removeAttribute('onchange');
    });

    // ===========================================
    // SUPPRESSION BLOG - Confirmation modale
    // ===========================================
    const deleteBlogButtons = document.querySelectorAll('.delete-blog');
    const deleteBlogModal = new bootstrap.Modal(document.getElementById('deleteBlogModal'));
    const blogTitleSpan = document.getElementById('blogTitle');
    const deleteBlogForm = document.getElementById('deleteBlogForm');

    deleteBlogButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const blogId = this.dataset.id;
            const blogTitle = this.dataset.title;
            
            blogTitleSpan.textContent = `"${blogTitle}"`;
            deleteBlogForm.action = `/Administrateur/blogs/${blogId}`;
            
            deleteBlogModal.show();
        });
    });

    // Réinitialisation du modal de suppression blog
    document.getElementById('deleteBlogModal').addEventListener('hidden.bs.modal', function() {
        blogTitleSpan.textContent = '';
        deleteBlogForm.action = '#';
    });

    // ===========================================
    // ÉDITION CATÉGORIE
    // ===========================================
    const editCategoryButtons = document.querySelectorAll('.edit-category');
    const editCategoryModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));

    editCategoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description || '';
            document.getElementById('editCategoryForm').action = `/Administrateur/blog-categories/${categoryId}`;

            editCategoryModal.show();
        });
    });

    // ===========================================
    // SUPPRESSION CATÉGORIE - Confirmation modale
    // ===========================================
    const deleteCategoryButtons = document.querySelectorAll('.delete-category');
    const deleteCategoryModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
    const categoryNameSpan = document.getElementById('categoryName');
    const categoryArticlesSpan = document.getElementById('categoryArticlesCount');
    const deleteCategoryForm = document.getElementById('deleteCategoryForm');

    deleteCategoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.dataset.id;
            const categoryName = this.dataset.name;
            const articlesCount = parseInt(this.dataset.count || 0);

            categoryNameSpan.textContent = `"${categoryName}"`;
            
            if (articlesCount > 0) {
                categoryArticlesSpan.innerHTML = `${articlesCount} article(s) sont associés à cette catégorie et seront également affectés.`;
            } else {
                categoryArticlesSpan.innerHTML = 'Aucun article n\'est associé à cette catégorie.';
            }
            
            deleteCategoryForm.action = `/Administrateur/blog-categories/${categoryId}`;
            
            deleteCategoryModal.show();
        });
    });

    // Réinitialisation du modal de suppression catégorie
    document.getElementById('deleteCategoryModal').addEventListener('hidden.bs.modal', function() {
        categoryNameSpan.textContent = '';
        categoryArticlesSpan.innerHTML = '';
        deleteCategoryForm.action = '#';
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