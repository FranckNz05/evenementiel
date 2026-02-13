@extends('layouts.dashboard')

@section('title', 'Mes Événements')

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

.events-page {
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

.btn-outline-primary {
    background: white;
    color: var(--primary);
    border: 1px solid var(--primary);
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
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

.events-table {
    width: 100%;
    border-collapse: collapse;
}

.events-table thead {
    background: var(--gray-50);
}

.events-table thead th {
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

.events-table thead th[style*="text-align: center"] {
    text-align: center;
}

.events-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.events-table tbody tr:hover {
    background: var(--gray-50);
}

.events-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

.events-table tbody td[style*="text-align: center"] {
    text-align: center;
}

/* Event Info */
.event-title {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.event-meta {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.event-date {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 0.875rem;
}

.event-time {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.event-location {
    font-size: 0.75rem;
    color: var(--gray-500);
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-archived {
    background: #f3f4f6;
    color: #4b5563;
}

.status-cancelled {
    background: #fee2e2;
    color: #991b1b;
}

.status-private {
    background: #e0e7ff;
    color: #4338ca;
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

/* Guest Count Badge */
.guest-badge {
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

.empty-description {
    color: var(--gray-500);
    font-size: 0.875rem;
    margin-top: 0.5rem;
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

/* Event Type Card */
.event-type-card {
    border: 2px solid var(--gray-200);
    border-radius: 0.75rem;
    transition: all 0.2s;
    height: 100%;
    cursor: pointer;
}

.event-type-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(15, 26, 61, 0.1);
    border-color: var(--primary);
}

/* Responsive */
@media (max-width: 768px) {
    .events-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .events-table {
        font-size: 0.75rem;
    }
    
    .events-table thead th,
    .events-table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .hide-sm {
        display: none;
    }
}

@media (max-width: 1024px) {
    .hide-md {
        display: none;
    }
}

@media (min-width: 1025px) {
    .hide-lg {
        display: table-cell;
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
<div class="events-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-calendar-alt"></i>
                Mes événements
            </h1>
            <p>Gérez vos événements publics et personnalisés</p>
        </div>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#chooseEventTypeModal">
                <i class="fas fa-plus"></i>
                Nouvel événement
            </button>
            <a href="{{ route('organizer.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    @php
        $totalPublicEvents = $events instanceof \Illuminate\Pagination\LengthAwarePaginator ? $events->total() : (is_countable($events) ? count($events) : 0);
        $totalCustomEvents = isset($customEvents) 
            ? ($customEvents instanceof \Illuminate\Pagination\LengthAwarePaginator ? $customEvents->total() : (is_countable($customEvents) ? count($customEvents) : 0))
            : 0;
        $publishedEvents = is_countable($events) ? collect($events)->where('is_published', true)->count() : 0;
        $draftEvents = is_countable($events) ? collect($events)->where('is_published', false)->count() : 0;
    @endphp

    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Événements publics</div>
                    <div class="stat-value">{{ $totalPublicEvents }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
        <div class="stat-card info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Événements personnalisés</div>
                    <div class="stat-value">{{ $totalCustomEvents }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Publiés</div>
                    <div class="stat-value">{{ $publishedEvents }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Brouillons</div>
                    <div class="stat-value">{{ $draftEvents }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
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

    <!-- Événements publics -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-calendar-alt"></i>
                Événements publics
            </h5>
            <span style="font-size: 0.875rem; color: var(--gray-600);">{{ $totalPublicEvents }} résultat(s)</span>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th class="hide-md">Date</th>
                            <th class="hide-sm">Statut</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td>
                                <div class="event-title">{{ $event->title }}</div>
                                <div class="d-flex d-md-none mt-1">
                                    <span class="status-badge {{ $event->is_published ? 'status-active' : 'status-pending' }}" style="margin-right: 0.25rem;">
                                        <i class="fas fa-{{ $event->is_published ? 'check-circle' : 'clock' }}"></i>
                                        {{ $event->is_published ? 'Publié' : 'Brouillon' }}
                                    </span>
                                    <small class="text-muted d-block mt-1">
                                        {{ $event->start_date ? $event->start_date->format('d/m/Y') : 'N/A' }}
                                    </small>
                                </div>
                            </td>
                            <td class="hide-md">
                                <div class="event-meta">
                                    <span class="event-date">{{ $event->start_date ? $event->start_date->format('d/m/Y') : 'N/A' }}</span>
                                    <span class="event-time">{{ $event->start_date ? $event->start_date->format('H:i') : '' }}</span>
                                </div>
                            </td>
                            <td class="hide-sm">
                                @if($event->is_published)
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle"></i>
                                        Publié
                                    </span>
                                @else
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-clock"></i>
                                        Brouillon
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('organizer.events.show', $event) }}" class="action-btn primary" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('organizer.events.edit', $event) }}" class="action-btn warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$event->is_published)
                                    <button type="button" class="action-btn danger delete-event" data-id="{{ $event->id }}" data-title="{{ $event->title }}" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <p class="empty-text">Aucun événement public</p>
                                    <p class="empty-description">Commencez par créer votre premier événement</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($events instanceof \Illuminate\Pagination\LengthAwarePaginator && $events->hasPages())
            <div class="pagination-wrapper">
                {{ $events->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Événements personnalisés -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-lock"></i>
                Événements personnalisés
            </h5>
            <span style="font-size: 0.875rem; color: var(--gray-600);">{{ $totalCustomEvents }} résultat(s)</span>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Période</th>
                            <th class="hide-lg">Lieu</th>
                            <th>Invités</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customEvents ?? [] as $event)
                        @php
                            $owner = $event->organizer;
                            $ownerName = collect([optional($owner)->prenom, optional($owner)->nom])
                                ->filter()
                                ->join(' ') ?: optional($owner)->name ?: 'N/A';
                        @endphp
                        <tr>
                            <td>
                                <div class="event-title">{{ $event->title }}</div>
                                <div class="d-md-none mt-1">
                                    <small class="text-muted">{{ Str::limit($ownerName, 20) }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="event-meta">
                                    <span class="event-date">{{ $event->start_date }}</span>
                                    <span class="event-time">au {{ $event->end_date }}</span>
                                </div>
                            </td>
                            <td class="hide-lg">
                                <div class="event-location" title="{{ $event->location }}">
                                    {{ Str::limit($event->location, 12) }}
                                </div>
                            </td>
                            <td>
                                <span class="guest-badge">
                                    {{ $event->guests->count() }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('custom-personal-events.show', $event->url) }}" class="action-btn primary" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('custom-personal-events.edit', $event->id) }}" class="action-btn warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="action-btn danger delete-custom-event" data-id="{{ $event->id }}" data-title="{{ $event->title }}" title="Supprimer">
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
                                        <i class="fas fa-user-lock"></i>
                                    </div>
                                    <p class="empty-text">Aucun événement personnalisé</p>
                                    <p class="empty-description">Les événements privés apparaîtront ici</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($customEvents) && $customEvents instanceof \Illuminate\Pagination\LengthAwarePaginator && $customEvents->hasPages())
            <div class="pagination-wrapper">
                {{ $customEvents->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Choix Type d'Événement -->
<div class="modal fade" id="chooseEventTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus"></i>
                    Créer un nouvel événement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div style="font-size: 2.5rem; color: var(--primary); opacity: 0.7; margin-bottom: 1rem;">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h6 style="font-weight: 600; color: var(--gray-900); margin-bottom: 0.5rem;">
                        Quel type d'événement souhaitez-vous créer ?
                    </h6>
                    <p style="color: var(--gray-500); font-size: 0.875rem; margin: 0;">
                        Choisissez le type qui correspond le mieux à votre événement
                    </p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('events.wizard.step1') }}" class="text-decoration-none">
                            <div class="event-type-card p-3">
                                <div class="text-center">
                                    <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem;">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h6 style="font-weight: 600; color: var(--primary); margin-bottom: 0.5rem;">
                                        Événement public
                                    </h6>
                                    <p style="color: var(--gray-500); font-size: 0.8125rem; margin-bottom: 1rem;">
                                        Événement ouvert à tous, visible dans la liste publique
                                    </p>
                                    <div class="d-flex justify-content-center gap-1 mb-3 flex-wrap">
                                        <span style="background: var(--primary); color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.625rem;">Public</span>
                                        <span style="background: var(--info); color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.625rem;">Visible</span>
                                        <span style="background: var(--success); color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.625rem;">Payant</span>
                                    </div>
                                    <span class="btn btn-primary w-100" style="font-size: 0.8125rem;">
                                        <i class="fas fa-arrow-right me-1"></i>
                                        Créer
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-6">
                        <a href="{{ route('custom-personal-events.create') }}" class="text-decoration-none">
                            <div class="event-type-card p-3">
                                <div class="text-center">
                                    <div style="font-size: 2.5rem; color: var(--success); margin-bottom: 1rem;">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <h6 style="font-weight: 600; color: var(--success); margin-bottom: 0.5rem;">
                                        Événement personnalisé
                                    </h6>
                                    <p style="color: var(--gray-500); font-size: 0.8125rem; margin-bottom: 1rem;">
                                        Événement privé sur invitation : mariage, anniversaire...
                                    </p>
                                    <div class="d-flex justify-content-center gap-1 mb-3 flex-wrap">
                                        <span style="background: var(--success); color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.625rem;">Privé</span>
                                        <span style="background: var(--warning); color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.625rem;">Invitation</span>
                                        <span style="background: var(--gray-600); color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.625rem;">Gratuit</span>
                                    </div>
                                    <span class="btn btn-success w-100" style="font-size: 0.8125rem;">
                                        <i class="fas fa-arrow-right me-1"></i>
                                        Créer
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression (événement public) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
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
                    Supprimer <span id="eventTitle" style="color: var(--danger);"></span> ?
                </h6>
                <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 1.5rem;">
                    Cette action est <strong style="color: var(--danger);">irréversible</strong>. L'événement sera définitivement supprimé.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Annuler
                </button>
                <form id="deleteEventForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression pour événement personnalisé -->
<div class="modal fade" id="deleteCustomModal" tabindex="-1" aria-hidden="true">
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
                    Supprimer <span id="customEventTitle" style="color: var(--danger);"></span> ?
                </h6>
                <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 1.5rem;">
                    Cette action est <strong style="color: var(--danger);">irréversible</strong>. Toutes les invitations et données associées seront perdues.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Annuler
                </button>
                <form id="deleteCustomForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Supprimer
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ===========================================
    // SUPPRESSION D'ÉVÉNEMENT PUBLIC
    // ===========================================
    const deleteButtons = document.querySelectorAll('.delete-event');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const eventTitleSpan = document.getElementById('eventTitle');
    const deleteForm = document.getElementById('deleteEventForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const eventId = this.dataset.id;
            const eventTitle = this.dataset.title;
            
            eventTitleSpan.textContent = `"${eventTitle}"`;
            deleteForm.action = `/organizer/events/${eventId}`;
            
            deleteModal.show();
        });
    });

    // ===========================================
    // SUPPRESSION D'ÉVÉNEMENT PERSONNALISÉ
    // ===========================================
    const deleteCustomButtons = document.querySelectorAll('.delete-custom-event');
    const deleteCustomModal = new bootstrap.Modal(document.getElementById('deleteCustomModal'));
    const customEventTitleSpan = document.getElementById('customEventTitle');
    const deleteCustomForm = document.getElementById('deleteCustomForm');

    deleteCustomButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const eventId = this.dataset.id;
            const eventTitle = this.dataset.title;
            
            customEventTitleSpan.textContent = `"${eventTitle}"`;
            deleteCustomForm.action = `/custom-personal-events/${eventId}`;
            
            deleteCustomModal.show();
        });
    });

    // ===========================================
    // RÉINITIALISATION DES MODALS
    // ===========================================
    document.getElementById('deleteModal')?.addEventListener('hidden.bs.modal', function() {
        eventTitleSpan.textContent = '';
        deleteForm.action = '#';
    });

    document.getElementById('deleteCustomModal')?.addEventListener('hidden.bs.modal', function() {
        customEventTitleSpan.textContent = '';
        deleteCustomForm.action = '#';
    });

    // ===========================================
    // SYSTÈME DE NOTIFICATION
    // ===========================================
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

    // ===========================================
    // AUTO-FERMETURE DES ALERTES EXISTANTES
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