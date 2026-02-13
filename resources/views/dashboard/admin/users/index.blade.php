@extends('layouts.dashboard')

@section('title', 'Gestion des utilisateurs')

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

.users-page {
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

.btn-info {
    background: var(--info);
    color: white;
}

.btn-info:hover {
    background: #2563eb;
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
.stat-card.success .stat-value { color: var(--success); }
.stat-card.warning .stat-value { color: var(--warning); }
.stat-card.info .stat-value { color: var(--info); }

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

.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table thead {
    background: var(--gray-50);
}

.users-table thead th {
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

.users-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.users-table tbody tr:hover {
    background: var(--gray-50);
}

.users-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

/* User Info */
.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.avatar-placeholder {
    width: 2.5rem;
    height: 2.5rem;
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

.avatar-placeholder img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 9999px;
}

.user-details {
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

/* Company Info */
.company-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.company-name {
    font-weight: 600;
    color: var(--gray-900);
}

.company-address {
    font-size: 0.75rem;
    color: var(--gray-500);
}

/* Contact Info */
.contact-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: var(--gray-600);
}

.contact-item i {
    font-size: 0.75rem;
    color: var(--primary);
    width: 14px;
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

.status-verified {
    background: #d1fae5;
    color: #065f46;
}

.status-unverified {
    background: #fef3c7;
    color: #92400e;
}

.status-admin {
    background: #fee2e2;
    color: #991b1b;
}

.status-organizer {
    background: #fef3c7;
    color: #92400e;
}

.status-client {
    background: #dbeafe;
    color: #1e40af;
}

.status-influencer {
    background: #e0e7ff;
    color: #4338ca;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
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

/* Alert */
.alert {
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
}

.alert-success {
    background: #d1fae5;
    border-left: 4px solid #059669;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    border-left: 4px solid #dc2626;
    color: #991b1b;
}

.alert i {
    font-size: 1rem;
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
    .users-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .users-table {
        font-size: 0.75rem;
    }
    
    .users-table thead th,
    .users-table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .hide-mobile {
        display: none;
    }
}

@media (max-width: 1024px) {
    .hide-tablet {
        display: none;
    }
}

/* Desktop hide */
@media (min-width: 769px) {
    .hide-desktop {
        display: none !important;
    }
}

/* Section spacer */
.section-spacer {
    margin-top: 2.5rem;
}
</style>
@endpush

@section('content')
<div class="users-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Gestion des utilisateurs</h1>
            <p>Gérez les comptes utilisateurs, les rôles et les permissions</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
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
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-label">Total utilisateurs</div>
            <div class="stat-value">{{ $users->total() }}</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-label">Demandes organisateur</div>
            <div class="stat-value">{{ $organizerRequests->total() }}</div>
        </div>
        <div class="stat-card info">
            <div class="stat-label">Demandes influenceur</div>
            <div class="stat-value">{{ isset($influencerRequests) ? $influencerRequests->total() : 0 }}</div>
        </div>
        <div class="stat-card success">
            <div class="stat-label">Utilisateurs vérifiés</div>
            <div class="stat-value">{{ $verifiedCount ?? $users->where('email_verified_at', '!=', null)->count() ?? 0 }}</div>
        </div>
    </div>

    <!-- Users List Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users"></i>
                Liste des utilisateurs
            </h3>
            <span style="font-size: 0.875rem; color: var(--gray-600);">{{ $users->total() }} résultat(s)</span>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="filters-section" style="margin-bottom: 1.5rem;">
                <form method="GET" action="{{ route('admin.users.index') }}" id="filtersForm">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">Recherche</label>
                            <div class="search-input-group">
                                <i class="fas fa-search search-icon"></i>
                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="Nom, prénom ou email..."
                                    value="{{ request('search') }}"
                                >
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Rôle</label>
                            <select name="role" class="form-select">
                                <option value="">Tous les rôles</option>
                                <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Client</option>
                                <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Organisateur</option>
                                <option value="3" {{ request('role') == '3' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Genre</label>
                            <select name="genre" class="form-select">
                                <option value="">Tous</option>
                                <option value="Homme" {{ request('genre') == 'Homme' ? 'selected' : '' }}>Homme</option>
                                <option value="Femme" {{ request('genre') == 'Femme' ? 'selected' : '' }}>Femme</option>
                                <option value="Autre" {{ request('genre') == 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Influenceur</label>
                            <select name="influencer" class="form-select">
                                <option value="">Tous</option>
                                <option value="yes" {{ request('influencer') == 'yes' ? 'selected' : '' }}>Oui</option>
                                <option value="no" {{ request('influencer') == 'no' ? 'selected' : '' }}>Non</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Appliquer les filtres
                        </button>
                        @if(request()->anyFilled(['search', 'role', 'genre', 'influencer']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="margin-left: 0.5rem;">
                            <i class="fas fa-times"></i>
                            Réinitialiser
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th class="hide-mobile">Email</th>
                            <th>Rôle</th>
                            <th class="hide-tablet">Statut</th>
                            <th class="hide-mobile">Inscription</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="avatar-placeholder">
                                        @if($user->profil_image)
                                            <img src="{{ Storage::disk('public')->exists($user->profil_image) ? asset('storage/' . $user->profil_image) : asset('images/default-avatar.png') }}" alt="{{ $user->nom . ' ' . $user->prenom }}" onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                        @else
                                            {{ strtoupper(substr($user->prenom ?? '', 0, 1) . substr($user->nom ?? '', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="user-details">
                                        <span class="user-name">{{ $user->nom ?? '' }} {{ $user->prenom ?? '' }}</span>
                                        <span class="user-email hide-desktop">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="hide-mobile">
                                <span style="font-size: 0.875rem; color: var(--gray-600);">{{ $user->email }}</span>
                            </td>
                            <td>
                                @if($user->hasRole(3))
                                    <span class="status-badge status-admin">
                                        <i class="fas fa-shield-alt"></i>
                                        Admin
                                    </span>
                                @elseif($user->hasRole(2))
                                    <span class="status-badge status-organizer">
                                        <i class="fas fa-calendar-alt"></i>
                                        Organisateur
                                    </span>
                                @elseif($user->hasRole(1))
                                    <span class="status-badge status-client">
                                        <i class="fas fa-user"></i>
                                        Client
                                    </span>
                                @else
                                    <span class="status-badge" style="background: var(--gray-200); color: var(--gray-700);">
                                        <i class="fas fa-user-circle"></i>
                                        Utilisateur
                                    </span>
                                @endif
                                
                                @if($user->is_influencer ?? false)
                                    <span class="status-badge status-influencer" style="margin-left: 0.25rem;">
                                        <i class="fas fa-star"></i>
                                        Influenceur
                                    </span>
                                @endif
                            </td>
                            <td class="hide-tablet">
                                @if($user->email_verified_at)
                                    <span class="status-badge status-verified">
                                        <i class="fas fa-check-circle"></i>
                                        Vérifié
                                    </span>
                                @else
                                    <span class="status-badge status-unverified">
                                        <i class="fas fa-clock"></i>
                                        Non vérifié
                                    </span>
                                @endif
                            </td>
                            <td class="hide-mobile">
                                <span style="font-size: 0.8125rem; color: var(--gray-600);">
                                    {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.users.show', $user) }}" class="action-btn primary" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="action-btn warning" title="Modifier">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <button type="button" class="action-btn danger delete-user" data-id="{{ $user->id }}" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <p class="empty-text">Aucun utilisateur trouvé</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="pagination-wrapper">
                {{ $users->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Organizer Requests Section -->
    <div style="margin-top: 2.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock"></i>
                    Demandes d'organisateur en attente
                </h3>
                @if($organizerRequests->count() > 0)
                <span class="status-badge status-pending">{{ $organizerRequests->count() }} demande(s)</span>
                @endif
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th class="hide-mobile">Société</th>
                                <th class="hide-tablet">Contact</th>
                                <th class="hide-mobile">Date de demande</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($organizerRequests as $request)
                            <tr data-request-id="{{ $request->id }}">
                                <td>
                                    <div class="user-info">
                                        <div class="avatar-placeholder">
                                            @if($request->user->profil_image)
                                                <img src="{{ Storage::disk('public')->exists($request->user->profil_image) ? Storage::disk('public')->url($request->user->profil_image) : asset('images/default-avatar.png') }}" alt="{{ $request->user->nom . ' ' . $request->user->prenom }}" onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                            @else
                                                {{ strtoupper(substr($request->user->prenom ?? '', 0, 1) . substr($request->user->nom ?? '', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name">{{ $request->user->nom ?? '' }} {{ $request->user->prenom ?? '' }}</span>
                                            <span class="user-email">{{ $request->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-mobile">
                                    <div class="company-info">
                                        <span class="company-name">{{ $request->company_name }}</span>
                                        <span class="company-address">{{ $request->address }}</span>
                                    </div>
                                </td>
                                <td class="hide-tablet">
                                    <div class="contact-info">
                                        <div class="contact-item">
                                            <i class="fas fa-envelope"></i>
                                            <span>{{ $request->email }}</span>
                                        </div>
                                        <div class="contact-item">
                                            <i class="fas fa-phone"></i>
                                            <span>{{ $request->phone_primary }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-mobile">
                                    <span style="font-size: 0.8125rem; color: var(--gray-600);">
                                        {{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="action-btn info" onclick="showMotivationAndExperience({{ $request->id }}, '{{ addslashes($request->motivation) }}', '{{ addslashes($request->experience) }}')" title="Voir motivation">
                                            <i class="fas fa-file-alt"></i>
                                        </button>
                                        <button type="button" class="action-btn success" onclick="approveRequest({{ $request->id }})" title="Approuver">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="action-btn danger" onclick="showRejectModal({{ $request->id }})" title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <p class="empty-text">Aucune demande d'organisateur en attente</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($organizerRequests->hasPages())
                <div class="pagination-wrapper">
                    {{ $organizerRequests->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Influencer Requests Section - TOUJOURS AFFICHÉE -->
    <div style="margin-top: 2.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-star"></i>
                    Demandes influenceur en attente
                </h3>
                @if(isset($influencerRequests) && $influencerRequests->count() > 0)
                <span class="status-badge status-pending">{{ $influencerRequests->total() }} demande(s)</span>
                @else
                <span class="status-badge" style="background: var(--gray-200); color: var(--gray-700);">0 demande</span>
                @endif
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th class="hide-mobile">Email</th>
                                <th class="hide-mobile">Inscription</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($influencerRequests ?? [] as $u)
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="avatar-placeholder">
                                            @if($u->profil_image)
                                                <img src="{{ asset('storage/' . $u->profil_image) }}" alt="{{ $u->nom . ' ' . $u->prenom }}" onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                            @else
                                                {{ strtoupper(substr($u->prenom ?? '', 0, 1) . substr($u->nom ?? '', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name">{{ $u->nom ?? '' }} {{ $u->prenom ?? '' }}</span>
                                            <span class="user-email hide-desktop">{{ $u->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-mobile">
                                    <span style="font-size: 0.875rem; color: var(--gray-600);">{{ $u->email }}</span>
                                </td>
                                <td class="hide-mobile">
                                    <span style="font-size: 0.8125rem; color: var(--gray-600);">
                                        {{ $u->created_at ? $u->created_at->format('d/m/Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <form method="POST" action="{{ route('admin.users.influencers.approve', $u) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="action-btn success" title="Approuver">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="action-btn danger" onclick="showInfluencerRejectModal({{ $u->id }})" title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <p class="empty-text">Aucune demande d'influenceur en attente</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($influencerRequests) && $influencerRequests->hasPages())
                <div class="pagination-wrapper">
                    {{ $influencerRequests->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Motivation & Experience Modal -->
<div class="modal fade" id="motivationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt"></i>
                    Motivation et Expérience
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 style="color: var(--primary); font-weight: 600; margin-bottom: 1rem;">
                            <i class="fas fa-lightbulb me-2"></i>Motivation
                        </h6>
                        <div id="motivationContent" style="background: var(--gray-50); padding: 1rem; border-radius: 0.5rem; min-height: 150px; white-space: pre-wrap; color: var(--gray-700);"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 style="color: var(--primary); font-weight: 600; margin-bottom: 1rem;">
                            <i class="fas fa-award me-2"></i>Expérience
                        </h6>
                        <div id="experienceContent" style="background: var(--gray-50); padding: 1rem; border-radius: 0.5rem; min-height: 150px; white-space: pre-wrap; color: var(--gray-700);"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal (Organizer) -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle"></i>
                    Rejeter la demande d'organisateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    @csrf
                    <input type="hidden" id="requestId" name="requestId">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label" style="font-weight: 600; color: var(--gray-700);">
                            Motif du rejet
                        </label>
                        <textarea 
                            class="form-control" 
                            id="rejectionReason" 
                            name="rejection_reason" 
                            rows="4" 
                            required
                            placeholder="Veuillez expliquer la raison du rejet..."
                            style="resize: vertical;"
                        ></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">
                    <i class="fas fa-ban me-2"></i>Confirmer le rejet
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal (Influencer) -->
<div class="modal fade" id="rejectInfluencerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle"></i>
                    Rejeter la demande d'influenceur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectInfluencerForm" method="POST">
                    @csrf
                    <input type="hidden" id="influencerUserId" name="user_id">
                    <div class="mb-3">
                        <label for="influencerRejectionReason" class="form-label" style="font-weight: 600; color: var(--gray-700);">
                            Motif du rejet
                        </label>
                        <textarea 
                            class="form-control" 
                            id="influencerRejectionReason" 
                            name="reason" 
                            rows="4" 
                            required
                            placeholder="Veuillez expliquer la raison du rejet..."
                            style="resize: vertical;"
                        ></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmInfluencerReject()">
                    <i class="fas fa-ban me-2"></i>Confirmer le rejet
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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

// Prevent automatic form submission on select change - we'll use a button
document.addEventListener('DOMContentLoaded', function() {
    // Remove onchange attributes from selects
    const selects = document.querySelectorAll('select[name="role"], select[name="genre"], select[name="influencer"]');
    selects.forEach(select => {
        select.removeAttribute('onchange');
    });
});

// Delete User
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-user');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
                fetch(`/Administrateur/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = this.closest('tr');
                        row.style.transition = 'all 0.3s ease';
                        row.style.backgroundColor = '#fee2e2';
                        setTimeout(() => {
                            row.remove();
                            showNotification('Utilisateur supprimé avec succès', 'success');
                            
                            // Check if table is empty
                            const tbody = document.querySelector('.users-table tbody');
                            if (tbody && tbody.children.length === 0) {
                                location.reload(); // Reload to show empty state
                            }
                        }, 300);
                    } else {
                        showNotification('Erreur lors de la suppression: ' + (data.message || 'Erreur inconnue'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Erreur lors de la suppression', 'error');
                });
            }
        });
    });
});

// Show Motivation and Experience
function showMotivationAndExperience(id, motivation, experience) {
    const motivationEl = document.getElementById('motivationContent');
    const experienceEl = document.getElementById('experienceContent');
    
    motivationEl.textContent = motivation || 'Aucune motivation fournie';
    experienceEl.textContent = experience || 'Aucune expérience fournie';
    
    const modal = new bootstrap.Modal(document.getElementById('motivationModal'));
    modal.show();
}

// Show Reject Modal (Organizer)
function showRejectModal(requestId) {
    document.getElementById('requestId').value = requestId;
    document.getElementById('rejectionReason').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

// Show Reject Modal (Influencer)
function showInfluencerRejectModal(userId) {
    document.getElementById('influencerUserId').value = userId;
    document.getElementById('influencerRejectionReason').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('rejectInfluencerModal'));
    modal.show();
}

// Approve Request (Organizer)
function approveRequest(requestId) {
    if (!confirm('Êtes-vous sûr de vouloir approuver cette demande ?')) {
        return;
    }

    const requestRow = document.querySelector(`tr[data-request-id="${requestId}"]`);
    const approveBtn = requestRow.querySelector('.action-btn.success');
    const rejectBtn = requestRow.querySelector('.action-btn.danger');
    
    const originalApproveContent = approveBtn.innerHTML;
    const originalRejectContent = rejectBtn.innerHTML;
    
    approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    approveBtn.disabled = true;
    rejectBtn.disabled = true;

    fetch(`/admin/organizer-requests/${requestId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            requestRow.style.transition = 'all 0.3s ease';
            requestRow.style.backgroundColor = '#d1fae5';
            
            setTimeout(() => {
                requestRow.remove();
                showNotification('Demande approuvée avec succès', 'success');
                
                // Update pending count badge
                updatePendingCounts();
            }, 500);
        } else {
            showNotification('Erreur lors de l\'approbation: ' + (data.message || 'Erreur inconnue'), 'error');
            approveBtn.innerHTML = originalApproveContent;
            approveBtn.disabled = false;
            rejectBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors de l\'approbation', 'error');
        approveBtn.innerHTML = originalApproveContent;
        approveBtn.disabled = false;
        rejectBtn.disabled = false;
    });
}

// Confirm Reject (Organizer)
function confirmReject() {
    const requestId = document.getElementById('requestId').value;
    const reason = document.getElementById('rejectionReason').value;

    if (!reason.trim()) {
        showNotification('Veuillez saisir un motif de rejet', 'error');
        return;
    }

    const requestRow = document.querySelector(`tr[data-request-id="${requestId}"]`);
    const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
    
    fetch(`/admin/organizer-requests/${requestId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ rejection_reason: reason })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            modal.hide();
            
            requestRow.style.transition = 'all 0.3s ease';
            requestRow.style.backgroundColor = '#fee2e2';
            
            setTimeout(() => {
                requestRow.remove();
                showNotification('Demande rejetée avec succès', 'success');
                
                // Update pending count badge
                updatePendingCounts();
            }, 500);
        } else {
            showNotification('Erreur lors du rejet: ' + (data.message || 'Erreur inconnue'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors du rejet', 'error');
    });
}

// Confirm Reject (Influencer)
function confirmInfluencerReject() {
    const userId = document.getElementById('influencerUserId').value;
    const reason = document.getElementById('influencerRejectionReason').value;

    if (!reason.trim()) {
        showNotification('Veuillez saisir un motif de rejet', 'error');
        return;
    }

    const form = document.getElementById('rejectInfluencerForm');
    const action = `/admin/users/influencers/reject/${userId}`;
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('rejectInfluencerModal'));
    
    fetch(action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            modal.hide();
            
            // Find and remove the row
            const row = document.querySelector(`button[onclick="showInfluencerRejectModal(${userId})"]`).closest('tr');
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.backgroundColor = '#fee2e2';
                setTimeout(() => {
                    row.remove();
                    showNotification('Demande d\'influenceur rejetée avec succès', 'success');
                }, 300);
            } else {
                location.reload();
            }
        } else {
            showNotification('Erreur lors du rejet: ' + (data.message || 'Erreur inconnue'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors du rejet', 'error');
    });
}

// Update Pending Counts (Organizer)
function updatePendingCounts() {
    const pendingRows = document.querySelectorAll('tr[data-request-id]');
    const pendingCount = pendingRows.length;
    
    const badge = document.querySelector('.card-header .status-badge.status-pending');
    if (badge) {
        if (pendingCount > 0) {
            badge.textContent = `${pendingCount} demande(s)`;
        } else {
            badge.remove();
        }
    }
}

// Notification System
function showNotification(message, type) {
    // Check if notification already exists and remove it
    const existingAlert = document.querySelector('.alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
    alertDiv.style.marginTop = '1rem';
    alertDiv.style.transition = 'opacity 0.3s ease';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
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
    }, 3000);
}
</script>
@endpush
@endsection