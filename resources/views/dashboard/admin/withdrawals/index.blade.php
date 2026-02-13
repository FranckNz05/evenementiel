@extends('layouts.dashboard')

@section('title', 'Gestion des retraits')

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

.withdrawals-page {
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

.withdrawals-table {
    width: 100%;
    border-collapse: collapse;
}

.withdrawals-table thead {
    background: var(--gray-50);
}

.withdrawals-table thead th {
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

.withdrawals-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.withdrawals-table tbody tr:hover {
    background: var(--gray-50);
}

.withdrawals-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

.organizer-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.organizer-name {
    font-weight: 600;
    color: var(--gray-900);
}

.organizer-email {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.amount {
    font-weight: 700;
    color: var(--success);
    font-size: 0.9375rem;
}

.payment-method {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.payment-method.airtel {
    background: #fee2e2;
    color: #dc2626;
}

.payment-method.mtn {
    background: #fef3c7;
    color: #d97706;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-processing {
    background: #dbeafe;
    color: #1e40af;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.date-time {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date {
    font-weight: 500;
    color: var(--gray-900);
}

.time {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
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

/* Payment Methods Stats */
.payment-methods-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.method-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: 0.5rem;
}

.method-stat-label {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.method-stat-value {
    font-weight: 700;
    color: var(--gray-900);
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

/* Responsive */
@media (max-width: 768px) {
    .withdrawals-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .withdrawals-table {
        font-size: 0.75rem;
    }
    
    .withdrawals-table thead th,
    .withdrawals-table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .otp-input {
        width: 50px !important;
        height: 50px !important;
        font-size: 1.25rem !important;
    }
}

/* Champs OTP pour le code PIN */
.otp-input {
    border: 2px solid var(--gray-300);
    border-radius: 0.5rem;
    transition: all 0.2s;
    text-align: center;
}

.otp-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem rgba(15, 26, 61, 0.25);
    outline: none;
}

.otp-input:not(:placeholder-shown) {
    border-color: var(--primary);
    background-color: #f8f9fa;
}
</style>
@endpush

@section('content')
<div class="withdrawals-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Gestion des retraits</h1>
            <p>Validez et gérez les demandes de retrait des organisateurs</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-label">Total retraits</div>
            <div class="stat-value">{{ format_number($stats['total_withdrawals']) }}</div>
        </div>
        <div class="stat-card success">
            <div class="stat-label">Montant total retiré</div>
            <div class="stat-value">{{ format_number($stats['total_amount']) }} <span style="font-size: 1rem; font-weight: 500; color: var(--gray-500);">FCFA</span></div>
        </div>
        <div class="stat-card info">
            <div class="stat-label">Retraits aujourd'hui</div>
            <div class="stat-value">{{ format_number($stats['today_count']) }}</div>
            <div style="font-size: 0.875rem; color: var(--gray-500); margin-top: 0.25rem;">{{ format_number($stats['today_amount']) }} FCFA</div>
        </div>
        <div class="stat-card success">
            <div class="stat-label">Retraits complétés</div>
            <div class="stat-value">{{ format_number($stats['completed_count']) }}</div>
        </div>
    </div>

    <!-- Payment Methods Stats -->
    @if($paymentMethodsStats->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Répartition par méthode de paiement</h3>
        </div>
        <div class="card-body">
            <div class="payment-methods-stats">
                @foreach($paymentMethodsStats as $method)
                <div class="method-stat">
                    <div>
                        <div class="method-stat-label">
                            @if($method->payment_method == 'MTN Mobile Money')
                                <span class="payment-method mtn">
                                    <i class="fas fa-mobile-alt"></i>
                                    MTN Mobile Money
                                </span>
                            @else
                                <span class="payment-method airtel">
                                    <i class="fas fa-mobile-alt"></i>
                                    Airtel Money
                                </span>
                            @endif
                        </div>
                        <div class="method-stat-value">{{ format_number($method->total) }} FCFA</div>
                        <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">{{ $method->count }} retrait(s)</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.withdrawals.index') }}" id="filtersForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Recherche</label>
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Rechercher par organisateur, téléphone, référence..."
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Statut</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En cours</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Méthode de paiement</label>
                    <select name="payment_method" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les méthodes</option>
                        <option value="MTN Mobile Money" {{ request('payment_method') == 'MTN Mobile Money' ? 'selected' : '' }}>MTN Mobile Money</option>
                        <option value="Airtel Money" {{ request('payment_method') == 'Airtel Money' ? 'selected' : '' }}>Airtel Money</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-search"></i>
                        Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Withdrawals Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Liste des retraits</h3>
            <span style="font-size: 0.875rem; color: var(--gray-600);">{{ $withdrawals->total() }} résultat(s)</span>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="withdrawals-table">
                    <thead>
                        <tr>
                            <th>Organisateur</th>
                            <th>Montant</th>
                            <th>Méthode</th>
                            <th>Téléphone</th>
                            <th>Référence</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdrawals as $withdrawal)
                        <tr>
                            <td>
                                <div class="organizer-info">
                                    <span class="organizer-name">{{ $withdrawal->organizer->prenom ?? 'N/A' }} {{ $withdrawal->organizer->nom ?? 'N/A' }}</span>
                                    <span class="organizer-email">{{ $withdrawal->organizer->email ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="amount">{{ format_number($withdrawal->amount) }} <span style="font-size: 0.75rem; color: var(--gray-500);">FCFA</span></div>
                            </td>
                            <td>
                                @if(stripos($withdrawal->payment_method, 'airtel') !== false)
                                    <span class="payment-method airtel">
                                        <i class="fas fa-mobile-alt"></i>
                                        Airtel
                                    </span>
                                @elseif(stripos($withdrawal->payment_method, 'mtn') !== false)
                                    <span class="payment-method mtn">
                                        <i class="fas fa-mobile-alt"></i>
                                        MTN
                                    </span>
                                @else
                                    <span class="payment-method" style="background: var(--gray-200); color: var(--gray-700);">
                                        {{ $withdrawal->payment_method }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="font-family: monospace; font-size: 0.875rem;">{{ $withdrawal->phone_number }}</span>
                            </td>
                            <td>
                                @if($withdrawal->transaction_reference)
                                    <code style="font-size: 0.75rem; background: var(--gray-100); padding: 0.25rem 0.5rem; border-radius: 0.25rem;">{{ $withdrawal->transaction_reference }}</code>
                                @else
                                    <span style="color: var(--gray-400);">-</span>
                                @endif
                            </td>
                            <td>
                                @if($withdrawal->status == 'pending')
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-clock"></i>
                                        En attente
                                    </span>
                                @elseif($withdrawal->status == 'processing')
                                    <span class="status-badge status-processing">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        En cours
                                    </span>
                                @elseif($withdrawal->status == 'completed')
                                    <span class="status-badge status-completed">
                                        <i class="fas fa-check"></i>
                                        Complété
                                    </span>
                                @elseif($withdrawal->status == 'rejected')
                                    <span class="status-badge status-rejected">
                                        <i class="fas fa-times"></i>
                                        Rejeté
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="date-time">
                                    <span class="date">{{ $withdrawal->created_at->format('d/m/Y') }}</span>
                                    <span class="time">{{ $withdrawal->created_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                @if($withdrawal->status == 'pending')
                                    <div class="action-buttons">
                                        <button type="button" class="action-btn success" onclick="approveWithdrawal({{ $withdrawal->id }})" title="Approuver">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="action-btn danger" onclick="rejectWithdrawal({{ $withdrawal->id }})" title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @else
                                    <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="action-btn primary" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <p class="empty-text">Aucun retrait trouvé</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($withdrawals->hasPages())
            <div class="pagination-wrapper">
                {{ $withdrawals->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'approbation -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approuver le retrait</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir approuver ce retrait ?</p>
                    <div id="withdrawalDetails" class="mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label">
                            <strong>Code PIN Airtel Money</strong>
                            <small class="text-muted d-block">Requis pour effectuer le retrait</small>
                        </label>
                        <div class="d-flex justify-content-center gap-2 mb-2" id="pin-container">
                            <input type="text"
                                   class="form-control text-center otp-input"
                                   id="pin1"
                                   maxlength="1"
                                   pattern="\d"
                                   autocomplete="off"
                                   style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                            <input type="text"
                                   class="form-control text-center otp-input"
                                   id="pin2"
                                   maxlength="1"
                                   pattern="\d"
                                   autocomplete="off"
                                   style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                            <input type="text"
                                   class="form-control text-center otp-input"
                                   id="pin3"
                                   maxlength="1"
                                   pattern="\d"
                                   autocomplete="off"
                                   style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                            <input type="text"
                                   class="form-control text-center otp-input"
                                   id="pin4"
                                   maxlength="1"
                                   pattern="\d"
                                   autocomplete="off"
                                   style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                        </div>
                        <input type="hidden" id="pin" name="pin" required>
                        <div class="form-text text-center">4 chiffres</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Approuver et traiter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de rejet -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Rejeter le retrait</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir rejeter ce retrait ?</p>
                    <div id="rejectWithdrawalDetails" class="mb-3"></div>

                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">
                            <strong>Raison du rejet</strong>
                        </label>
                        <textarea class="form-control"
                                  id="rejection_reason"
                                  name="rejection_reason"
                                  rows="3"
                                  required
                                  placeholder="Expliquez pourquoi ce retrait est rejeté..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Rejeter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function approveWithdrawal(withdrawalId) {
    // Récupérer les détails du retrait
    fetch(`{{ route('admin.withdrawals.show', ['withdrawal' => ':id']) }}`.replace(':id', withdrawalId), {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur lors de la récupération des détails');
            }
            return response.json();
        })
        .then(data => {
            if (data.withdrawal) {
                const withdrawal = data.withdrawal;
                document.getElementById('withdrawalDetails').innerHTML = `
                    <div style="border: 1px solid var(--gray-200); border-radius: 0.5rem; padding: 1rem; background: var(--gray-50);">
                        <strong>Organisateur:</strong> ${withdrawal.organizer.prenom} ${withdrawal.organizer.nom}<br>
                        <strong>Montant:</strong> ${parseFloat(withdrawal.amount).toLocaleString('fr-FR')} FCFA<br>
                        <strong>Méthode:</strong> ${withdrawal.payment_method}<br>
                        <strong>Téléphone:</strong> ${withdrawal.phone_number}
                    </div>
                `;

                document.getElementById('approveForm').action = `{{ url('/Administrateur/withdrawals') }}/${withdrawalId}/approve`;
                new bootstrap.Modal(document.getElementById('approveModal')).show();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la récupération des détails du retrait');
        });
}

function rejectWithdrawal(withdrawalId) {
    // Récupérer les détails du retrait
    fetch(`{{ route('admin.withdrawals.show', ['withdrawal' => ':id']) }}`.replace(':id', withdrawalId), {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur lors de la récupération des détails');
            }
            return response.json();
        })
        .then(data => {
            if (data.withdrawal) {
                const withdrawal = data.withdrawal;
                document.getElementById('rejectWithdrawalDetails').innerHTML = `
                    <div style="border: 1px solid var(--gray-200); border-radius: 0.5rem; padding: 1rem; background: var(--gray-50);">
                        <strong>Organisateur:</strong> ${withdrawal.organizer.prenom} ${withdrawal.organizer.nom}<br>
                        <strong>Montant:</strong> ${parseFloat(withdrawal.amount).toLocaleString('fr-FR')} FCFA<br>
                        <strong>Méthode:</strong> ${withdrawal.payment_method}<br>
                        <strong>Téléphone:</strong> ${withdrawal.phone_number}
                    </div>
                `;

                document.getElementById('rejectForm').action = `{{ url('/Administrateur/withdrawals') }}/${withdrawalId}/reject`;
                new bootstrap.Modal(document.getElementById('rejectModal')).show();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la récupération des détails du retrait');
        });
}

// Gestion des champs PIN OTP (4 carrés)
document.addEventListener('DOMContentLoaded', function() {
    const pinInputs = document.querySelectorAll('.otp-input');
    const pinHidden = document.getElementById('pin');
    
    if (pinInputs.length === 4 && pinHidden) {
        // Fonction pour mettre à jour le champ caché
        function updatePinValue() {
            const pinValue = Array.from(pinInputs).map(input => input.value).join('');
            pinHidden.value = pinValue;
        }
        
        // Gérer la saisie dans chaque champ
        pinInputs.forEach((input, index) => {
            // Ne permettre que les chiffres
            input.addEventListener('input', function(e) {
                const value = e.target.value.replace(/\D/g, '');
                e.target.value = value.substring(0, 1);
                updatePinValue();
                
                // Passer au champ suivant si un chiffre est saisi
                if (value && index < pinInputs.length - 1) {
                    pinInputs[index + 1].focus();
                }
            });
            
            // Gérer la suppression (Backspace)
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    pinInputs[index - 1].focus();
                    pinInputs[index - 1].value = '';
                    updatePinValue();
                }
            });
            
            // Gérer le collage (paste)
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').substring(0, 4);
                
                for (let i = 0; i < pinInputs.length; i++) {
                    if (i < pastedData.length) {
                        pinInputs[i].value = pastedData[i];
                    } else {
                        pinInputs[i].value = '';
                    }
                }
                
                updatePinValue();
                
                // Focus sur le dernier champ rempli ou le premier vide
                const lastFilledIndex = Math.min(pastedData.length, pinInputs.length - 1);
                pinInputs[lastFilledIndex].focus();
            });
            
            // Empêcher la saisie de caractères non numériques
            input.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                    e.preventDefault();
                }
            });
        });
        
        // Réinitialiser les champs quand le modal s'ouvre
        const approveModal = document.getElementById('approveModal');
        if (approveModal) {
            approveModal.addEventListener('show.bs.modal', function() {
                pinInputs.forEach(input => {
                    input.value = '';
                });
                pinHidden.value = '';
                // Focus sur le premier champ
                setTimeout(() => pinInputs[0].focus(), 300);
            });
        }
    }
});

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
</script>
@endpush
@endsection
