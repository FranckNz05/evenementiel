@extends('layouts.dashboard')

@section('title', 'Codes d\'accès')

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

.access-codes-page {
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

.btn-outline-primary {
    background: white;
    color: var(--primary);
    border: 1px solid var(--primary);
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
}

.btn-outline-danger {
    background: white;
    color: var(--danger);
    border: 1px solid var(--danger);
}

.btn-outline-danger:hover {
    background: var(--danger);
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
.stat-gradient.bg-danger { background: var(--danger); }
.stat-gradient.bg-info { background: var(--info); }
.stat-gradient.bg-secondary { background: var(--gray-500); }

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

.codes-table {
    width: 100%;
    border-collapse: collapse;
}

.codes-table thead {
    background: var(--gray-50);
}

.codes-table thead th {
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

.codes-table thead th[style*="text-align: center"] {
    text-align: center;
}

.codes-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.codes-table tbody tr:hover {
    background: var(--gray-50);
}

.codes-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

.codes-table tbody td[style*="text-align: center"] {
    text-align: center;
}

/* Code Display */
.code-display {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    background: var(--gray-100);
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    color: var(--primary);
    border: 1px solid var(--gray-200);
    display: inline-block;
    font-size: 0.875rem;
    letter-spacing: 1px;
}

/* Event Info */
.event-title {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.event-date {
    font-size: 0.75rem;
    color: var(--gray-500);
}

/* Date Info */
.date-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date-label {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 0.8125rem;
}

.date-value {
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

.status-valid {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.status-inactive {
    background: #f3f4f6;
    color: #4b5563;
    border: 1px solid #9ca3af;
}

.status-expired {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

.status-pending {
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

.action-btn.primary:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.action-btn.danger:hover {
    background: var(--danger);
    border-color: var(--danger);
    color: white;
}

.action-btn.success:hover {
    background: var(--success);
    border-color: var(--success);
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
    margin-bottom: 1.5rem;
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

.modal-header .modal-title {
    color: white;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-header .modal-title i {
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

/* Form */
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

.form-text {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

.text-danger {
    color: var(--danger) !important;
}

/* Toast Notification */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Pagination */
.pagination-wrapper {
    margin-top: 1rem;
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
    .access-codes-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .codes-table {
        font-size: 0.75rem;
    }
    
    .codes-table thead th,
    .codes-table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .toast-notification {
        top: 10px;
        right: 10px;
        left: 10px;
    }
}

/* Utilities */
.d-flex { display: flex; }
.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }
.justify-content-center { justify-content: center; }
.justify-content-end { justify-content: flex-end; }
.flex-wrap { flex-wrap: wrap; }
.flex-column { flex-direction: column; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 1rem; }
.mb-0 { margin-bottom: 0; }
.mb-1 { margin-bottom: 0.25rem; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-4 { margin-bottom: 1.5rem; }
.mt-1 { margin-top: 0.25rem; }
.me-1 { margin-right: 0.25rem; }
.me-2 { margin-right: 0.5rem; }
.ms-auto { margin-left: auto; }

.w-100 { width: 100%; }
.h-100 { height: 100%; }

.text-center { text-align: center; }
.text-muted { color: var(--gray-500) !important; }
.text-gray-800 { color: var(--gray-800) !important; }
.text-gray-700 { color: var(--gray-700) !important; }

.fw-semibold { font-weight: 600; }
.fw-bold { font-weight: 700; }
.small { font-size: 0.75rem; }

.bg-light { background: var(--gray-100); }
.border-0 { border: none; }
.shadow-sm { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
.rounded { border-radius: 0.5rem; }
.overflow-hidden { overflow: hidden; }
.position-relative { position: relative; }
</style>
@endpush

@section('content')
<div class="access-codes-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-key"></i>
                Codes d'accès
            </h1>
            <p>Gérez les codes d'accès pour vos événements</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateCodeModal">
                <i class="fas fa-plus"></i>
                Générer un code
            </button>
            <a href="{{ route('organizer.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    @php
        $totalCodes = $accessCodes instanceof \Illuminate\Pagination\LengthAwarePaginator ? $accessCodes->total() : (is_countable($accessCodes) ? count($accessCodes) : 0);
        $validCodes = $accessCodes->filter(function($code) { return $code->isValid(); })->count();
        $expiredCodes = $accessCodes->filter(function($code) { 
            return !$code->is_active || now()->greaterThan($code->valid_until); 
        })->count();
        $uniqueEvents = $accessCodes->pluck('event_id')->unique()->filter()->count();
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total codes</div>
                    <div class="stat-number">{{ number_format($totalCodes, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-key"></i>
                        Générés
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-key"></i>
                </div>
            </div>
            <div class="stat-gradient bg-primary"></div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Codes valides</div>
                    <div class="stat-number">{{ number_format($validCodes, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-check-circle text-success"></i>
                        Utilisables
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
                    <div class="stat-label">Codes expirés</div>
                    <div class="stat-number">{{ number_format($expiredCodes, 0, ',', ' ') }}</div>
                    <div class="stat-subtitle">
                        <i class="fas fa-clock text-warning"></i>
                        Inactifs
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-gradient bg-warning"></div>
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

    <!-- Tableau des codes d'accès -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-list"></i>
                Mes codes d'accès
            </h5>
            <span style="font-size: 0.875rem; color: var(--gray-600);">
                {{ $totalCodes }} résultat(s)
                @if($accessCodes instanceof \Illuminate\Pagination\LengthAwarePaginator && $accessCodes->total() > 0)
                    · Page {{ $accessCodes->currentPage() }}/{{ $accessCodes->lastPage() }}
                @endif
            </span>
        </div>
        <div class="card-body p-0">
            @if($accessCodes->count() > 0)
                <div class="table-wrapper">
                    <table class="codes-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Événement</th>
                                <th style="text-align: center;">Valide du</th>
                                <th style="text-align: center;">Valide jusqu'au</th>
                                <th style="text-align: center;">Statut</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accessCodes as $code)
                            <tr>
                                <td>
                                    <span class="code-display">{{ $code->access_code }}</span>
                                    @if($code->description)
                                        <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                                            {{ Str::limit($code->description, 30) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="event-title">{{ $code->event->title ?? 'Événement supprimé' }}</div>
                                    @if($code->event)
                                        <div class="event-date">{{ $code->event->start_date ? $code->event->start_date->format('d/m/Y H:i') : '' }}</div>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div class="date-info">
                                        <span class="date-value">{{ $code->valid_from->format('d/m/Y') }}</span>
                                        <span style="font-size: 0.6875rem; color: var(--gray-500);">{{ $code->valid_from->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="date-info">
                                        <span class="date-value">{{ $code->valid_until->format('d/m/Y') }}</span>
                                        <span style="font-size: 0.6875rem; color: var(--gray-500);">{{ $code->valid_until->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    @if($code->isValid())
                                        <span class="status-badge status-valid">
                                            <i class="fas fa-check-circle"></i>
                                            Valide
                                        </span>
                                    @elseif(!$code->is_active)
                                        <span class="status-badge status-inactive">
                                            <i class="fas fa-ban"></i>
                                            Inactif
                                        </span>
                                    @elseif(now()->greaterThan($code->valid_until))
                                        <span class="status-badge status-expired">
                                            <i class="fas fa-clock"></i>
                                            Expiré
                                        </span>
                                    @else
                                        <span class="status-badge status-pending">
                                            <i class="fas fa-hourglass-half"></i>
                                            En attente
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-buttons">
                                        <button class="action-btn primary" onclick="copyCode('{{ $code->access_code }}')" title="Copier le code">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <form method="POST" action="{{ route('organizer.access-codes.delete', $code) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce code ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($accessCodes instanceof \Illuminate\Pagination\LengthAwarePaginator && $accessCodes->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="font-size: 0.875rem; color: var(--gray-600);">
                            Affichage de {{ $accessCodes->firstItem() ?? 0 }} à {{ $accessCodes->lastItem() ?? 0 }} sur {{ $accessCodes->total() }} résultats
                        </div>
                        <div class="pagination-wrapper">
                            {{ $accessCodes->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <p class="empty-text">Aucun code d'accès</p>
                    <p class="empty-description">Générez votre premier code d'accès pour un événement</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateCodeModal">
                        <i class="fas fa-plus me-2"></i>
                        Générer un code
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de génération de code -->
<div class="modal fade" id="generateCodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-key"></i>
                    Générer un code d'accès
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('organizer.access-codes.generate') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="event_id" class="form-label">
                            Événement <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('event_id') is-invalid @enderror" id="event_id" name="event_id" required>
                            <option value="">Sélectionner un événement</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">
                                    {{ $event->title }} - {{ $event->start_date ? $event->start_date->format('d/m/Y H:i') : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('event_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="valid_until" class="form-label">Valide jusqu'au</label>
                        <input type="datetime-local" class="form-control @error('valid_until') is-invalid @enderror" id="valid_until" name="valid_until">
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Laisser vide pour utiliser la date de fin de l'événement
                        </div>
                        @error('valid_until')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optionnel)</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Description du code d'accès..."></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Ex: Équipe VIP, Personnel, Partenaires...
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key me-2"></i>
                        Générer le code
                    </button>
                </div>
            </form>
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
    // COPIE DU CODE AVEC NOTIFICATION
    // ===========================================
    window.copyCode = function(code) {
        navigator.clipboard.writeText(code).then(function() {
            // Créer la notification
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    Code copié dans le presse-papiers !
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(toast);
            
            // Supprimer la notification après 3 secondes
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 3000);
        }).catch(function(err) {
            console.error('Erreur lors de la copie: ', err);
            
            // Fallback pour les navigateurs qui ne supportent pas clipboard
            const textarea = document.createElement('textarea');
            textarea.value = code;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            // Notification de succès avec fallback
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    Code copié dans le presse-papiers !
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 3000);
        });
    };

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