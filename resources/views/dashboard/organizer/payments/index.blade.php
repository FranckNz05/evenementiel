@extends('layouts.dashboard')

@section('title', 'Gestion des Paiements')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-lg-6">
            <h1 class="h3 mb-2 mb-lg-0 text-gray-800 fw-bold">
                <i class="fas fa-credit-card text-primary me-2"></i>
                Gestion des Paiements
            </h1>
            <p class="text-muted mb-0 small">Suivez vos revenus et transactions</p>
        </div>
        <div class="col-12 col-lg-6">
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                <button class="btn btn-success d-flex align-items-center justify-content-center" onclick="exportPayments()">
                    <i class="fas fa-download me-2"></i>
                    <span class="d-none d-sm-inline">Exporter</span>
                </button>
                <button class="btn btn-primary d-flex align-items-center justify-content-center" onclick="refreshData()">
                    <i class="fas fa-sync-alt me-2"></i>
                    <span class="d-none d-sm-inline">Actualiser</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Statistiques des paiements -->
    <div class="row g-4 mb-5">
        <!-- Revenus totaux -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-success text-uppercase mb-2 letter-spacing-1">
                                Revenus totaux
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800 counter-number">
                                {{ number_format($totalRevenue ?? 0, 0, ',', ' ') }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <span class="fw-semibold">FCFA</span> générés
                            </div>
                        </div>
                        <div class="stat-icon bg-success bg-gradient">
                            <i class="fas fa-money-bill-wave text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-gradient bg-success"></div>
            </div>
        </div>

        <!-- Paiements réussis -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-2 letter-spacing-1">
                                Paiements réussis
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800 counter-number">
                                {{ $successfulPayments ?? 0 }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-check-circle text-success me-1"></i>
                                Transactions
                            </div>
                        </div>
                        <div class="stat-icon bg-primary bg-gradient">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-gradient bg-primary"></div>
            </div>
        </div>

        <!-- Paiements en attente -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-2 letter-spacing-1">
                                En attente
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800 counter-number">
                                {{ $pendingPayments ?? 0 }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-clock text-warning me-1"></i>
                                Transactions
                            </div>
                        </div>
                        <div class="stat-icon bg-warning bg-gradient">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-gradient bg-warning"></div>
            </div>
        </div>

        <!-- Taux de conversion -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-info text-uppercase mb-2 letter-spacing-1">
                                Taux de conversion
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800 counter-number">
                                {{ number_format($conversionRate ?? 0, 1) }}%
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-percentage text-info me-1"></i>
                                Réussite
                            </div>
                        </div>
                        <div class="stat-icon bg-info bg-gradient">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-gradient bg-info"></div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('organizer.payments.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label fw-semibold">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="payé" {{ request('status') == 'payé' ? 'selected' : '' }}>Payé</option>
                                <option value="en attente" {{ request('status') == 'en attente' ? 'selected' : '' }}>En attente</option>
                                <option value="échoué" {{ request('status') == 'échoué' ? 'selected' : '' }}>Échoué</option>
                                <option value="remboursé" {{ request('status') == 'remboursé' ? 'selected' : '' }}>Remboursé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="event_id" class="form-label fw-semibold">Événement</label>
                            <select class="form-select" id="event_id" name="event_id">
                                <option value="">Tous les événements</option>
                                @foreach($events ?? [] as $event)
                                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                        {{ $event->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label fw-semibold">Date début</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label fw-semibold">Date fin</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des paiements -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-4">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
                        <div>
                            <h5 class="mb-1 fw-bold text-primary">
                                <i class="fas fa-list me-2"></i>
                                Historique des Paiements
                            </h5>
                            <p class="text-muted mb-0 small">{{ $payments->total() ?? 0 }} paiements trouvés</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="toggleView()">
                                <i class="fas fa-th-large me-1"></i> Vue grille
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle" id="paymentsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0 fw-semibold text-gray-700">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-gray-700">Transaction</th>
                                    <th class="px-3 py-3 border-0 fw-semibold text-gray-700">Client</th>
                                    <th class="px-3 py-3 border-0 fw-semibold text-gray-700">Événement</th>
                                    <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Montant</th>
                                    <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Statut</th>
                                    <th class="px-3 py-3 border-0 fw-semibold text-gray-700 text-center">Date</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-gray-700 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments ?? [] as $payment)
                                <tr class="border-bottom payment-row" data-payment-id="{{ $payment->id }}">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" class="form-check-input payment-checkbox" value="{{ $payment->id }}">
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="payment-icon me-3">
                                                <i class="fas fa-credit-card text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-semibold text-gray-800">
                                                    #{{ $payment->reference_transaction ?? $payment->id }}
                                                </h6>
                                                <small class="text-muted">{{ $payment->methode_paiement ?? 'Mobile Money' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                <div class="avatar-placeholder">
                                                    {{ substr($payment->user->name ?? 'C', 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-semibold text-gray-800">
                                                    {{ $payment->user->name ?? 'Client' }}
                                                </h6>
                                                <small class="text-muted">{{ $payment->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4">
                                        <span class="fw-semibold text-gray-800">
                                            {{ Str::limit($payment->event->title ?? 'N/A', 30) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <span class="h6 mb-0 fw-bold text-success">
                                            {{ number_format($payment->montant ?? 0, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        @if($payment->statut === 'payé')
                                            <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill fw-semibold">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Payé
                                            </span>
                                        @elseif($payment->statut === 'en attente')
                                            <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill fw-semibold">
                                                <i class="fas fa-clock me-1"></i>
                                                En attente
                                            </span>
                                        @elseif($payment->statut === 'échoué')
                                            <span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill fw-semibold">
                                                <i class="fas fa-times-circle me-1"></i>
                                                Échoué
                                            </span>
                                        @elseif($payment->statut === 'remboursé')
                                            <span class="badge bg-info-soft text-info px-3 py-2 rounded-pill fw-semibold">
                                                <i class="fas fa-undo me-1"></i>
                                                Remboursé
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <div class="d-flex flex-column">
                                            <small class="fw-semibold text-gray-800">
                                                {{ $payment->created_at->format('d/m/Y') }}
                                            </small>
                                            <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-outline-info btn-sm" onclick="viewPayment({{ $payment->id }})" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($payment->statut === 'payé')
                                            <button class="btn btn-outline-warning btn-sm" onclick="refundPayment({{ $payment->id }})" title="Rembourser">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucun paiement trouvé</h5>
                                            <p class="text-muted mb-0">Les paiements apparaîtront ici</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if(isset($payments) && $payments->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Affichage de {{ $payments->firstItem() }} à {{ $payments->lastItem() }} sur {{ $payments->total() }} résultats
                        </div>
                        <div>
                            {{ $payments->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de détails du paiement -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="paymentDetails">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>

<style>
/* Styles personnalisés */
.letter-spacing-1 {
    letter-spacing: 0.5px;
}

.stat-card {
    transition: all 0.3s ease;
    border-radius: 12px !important;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.stat-gradient {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    opacity: 0.7;
}

.counter-number {
    font-size: 2rem;
    line-height: 1;
}

.payment-icon {
    width: 40px;
    height: 40px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.avatar {
    width: 40px;
    height: 40px;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
}

.bg-success-soft {
    background-color: #d4edda !important;
}

.bg-warning-soft {
    background-color: #fff3cd !important;
}

.bg-danger-soft {
    background-color: #f8d7da !important;
}

.bg-info-soft {
    background-color: #d1ecf1 !important;
}

.empty-state {
    padding: 2rem;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.card {
    border-radius: 12px;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
    .counter-number {
        font-size: 1.5rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des compteurs
    function animateCounters() {
        const counters = document.querySelectorAll('.counter-number');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/\s/g, ''));
            const increment = target / 50;
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

    // Sélection multiple
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.payment-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});

// Fonctions globales
function viewPayment(paymentId) {
    // Charger les détails du paiement via AJAX
    fetch(`/organizer/payments/${paymentId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('paymentDetails').innerHTML = data.html;
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des détails');
        });
}

function refundPayment(paymentId) {
    if (confirm('Êtes-vous sûr de vouloir rembourser ce paiement ?')) {
        // Logique de remboursement
        fetch(`/organizer/payments/${paymentId}/refund`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors du remboursement');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du remboursement');
        });
    }
}

function exportPayments() {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("organizer.payments.export") }}';
    
    // Ajouter les filtres actuels
    const params = new URLSearchParams(window.location.search);
    params.forEach((value, key) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function refreshData() {
    location.reload();
}

function toggleView() {
    // Basculer entre vue tableau et vue grille
    const table = document.getElementById('paymentsTable');
    const button = event.target;
    
    if (table.classList.contains('table-view')) {
        table.classList.remove('table-view');
        table.classList.add('grid-view');
        button.innerHTML = '<i class="fas fa-list me-1"></i> Vue tableau';
    } else {
        table.classList.remove('grid-view');
        table.classList.add('table-view');
        button.innerHTML = '<i class="fas fa-th-large me-1"></i> Vue grille';
    }
}
</script>
@endpush
@endsection
