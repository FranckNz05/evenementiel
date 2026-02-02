@extends('layouts.dashboard')

@section('title', 'Historique des retraits')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-history me-2"></i>
                        Historique des retraits
                    </h4>
                    <p class="text-muted mb-0 small">Suivez vos demandes de retrait</p>
                </div>
                <a href="{{ route('organizer.withdrawals.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Nouvelle demande
                </a>
            </div>
        </div>
    </div>

    <!-- Résumé du wallet -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Solde disponible</p>
                            <h3 class="mb-0 fw-bold text-success">
                                {{ number_format($availableBalance, 0, ',', ' ') }} FCFA
                            </h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-wallet fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Revenus totaux</p>
                            <h3 class="mb-0 fw-bold">
                                {{ number_format($revenueData['net_revenue'], 0, ',', ' ') }} FCFA
                            </h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Commission MokiliEvent</p>
                            <h3 class="mb-0 fw-bold text-warning">
                                {{ number_format($revenueData['total_commission_paid'], 0, ',', ' ') }} FCFA
                            </h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-percent fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des retraits -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($withdrawals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Méthode</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $withdrawal)
                                <tr>
                                    <td>
                                        <div class="text-muted small">
                                            {{ $withdrawal->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="small">
                                            {{ $withdrawal->created_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                            {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                    <td>
                                        @if($withdrawal->payment_method == 'MTN Mobile Money')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-mobile-alt me-1"></i>
                                                MTN
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-mobile-alt me-1"></i>
                                                Airtel
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-monospace">{{ $withdrawal->phone_number }}</span>
                                    </td>
                                    <td>
                                        @if($withdrawal->status == 'pending')
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-clock me-1"></i>
                                                En attente
                                            </span>
                                        @elseif($withdrawal->status == 'processing')
                                            <span class="badge bg-info">
                                                <i class="fas fa-spinner me-1"></i>
                                                En traitement
                                            </span>
                                        @elseif($withdrawal->status == 'completed')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>
                                                Complété
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>
                                                Rejeté
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($withdrawal->status == 'completed' && $withdrawal->transaction_reference)
                                            <span class="badge bg-light text-dark" title="Référence">
                                                {{ $withdrawal->transaction_reference }}
                                            </span>
                                        @elseif($withdrawal->status == 'rejected' && $withdrawal->rejection_reason)
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="tooltip" 
                                                    title="{{ $withdrawal->rejection_reason }}">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @elseif($withdrawal->status == 'processing' && $withdrawal->payment_method == 'Airtel Money' && $withdrawal->transaction_id)
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary check-status-btn" 
                                                    data-withdrawal-id="{{ $withdrawal->id }}"
                                                    data-bs-toggle="tooltip" 
                                                    title="Vérifier le statut">
                                                <i class="fas fa-sync-alt"></i>
                                                Vérifier
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $withdrawals->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune demande de retrait pour le moment</p>
                    <a href="{{ route('organizer.withdrawals.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Faire une demande
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkStatusButtons = document.querySelectorAll('.check-status-btn');
    
    checkStatusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const withdrawalId = this.getAttribute('data-withdrawal-id');
            const originalIcon = this.querySelector('i');
            const originalText = this.textContent.trim();
            
            // Désactiver le bouton et afficher le chargement
            this.disabled = true;
            originalIcon.className = 'fas fa-spinner fa-spin';
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vérification...';
            
            // Faire la requête AJAX
            fetch(`/organizer/withdrawals/${withdrawalId}/check-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher un message de succès
                    showToast('Statut vérifié: ' + data.message, 'success');
                    
                    // Recharger la page après 1 seconde pour voir le nouveau statut
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Afficher un message d'erreur
                    showToast(data.message || 'Erreur lors de la vérification', 'error');
                    
                    // Réactiver le bouton
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Erreur lors de la vérification du statut', 'error');
                
                // Réactiver le bouton
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    });
    
    // Fonction pour afficher des toasts (si disponible)
    function showToast(message, type = 'info') {
        // Utiliser la fonction showToast globale si disponible
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            // Sinon, utiliser alert
            alert(message);
        }
    }
});
</script>
@endpush
@endsection

