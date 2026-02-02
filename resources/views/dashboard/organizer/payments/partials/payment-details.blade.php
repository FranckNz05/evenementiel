<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold text-primary mb-3">Informations de la transaction</h6>
        <table class="table table-sm">
            <tr>
                <td class="fw-semibold">ID Transaction:</td>
                <td>#{{ $payment->reference_transaction ?? $payment->id }}</td>
            </tr>
            <tr>
                <td class="fw-semibold">Montant:</td>
                <td class="text-success fw-bold">{{ number_format($payment->montant ?? 0, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td class="fw-semibold">Statut:</td>
                <td>
                    @if($payment->statut === 'payé')
                        <span class="badge bg-success">Payé</span>
                    @elseif($payment->statut === 'en attente')
                        <span class="badge bg-warning">En attente</span>
                    @elseif($payment->statut === 'échoué')
                        <span class="badge bg-danger">Échoué</span>
                    @elseif($payment->statut === 'remboursé')
                        <span class="badge bg-info">Remboursé</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="fw-semibold">Méthode de paiement:</td>
                <td>{{ $payment->methode_paiement ?? 'Mobile Money' }}</td>
            </tr>
            <tr>
                <td class="fw-semibold">Date de création:</td>
                <td>{{ $payment->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="fw-bold text-primary mb-3">Informations du client</h6>
        <table class="table table-sm">
            <tr>
                <td class="fw-semibold">Nom:</td>
                <td>{{ $payment->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="fw-semibold">Email:</td>
                <td>{{ $payment->user->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="fw-semibold">Téléphone:</td>
                <td>{{ $payment->user->phone ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <h6 class="fw-bold text-primary mb-3">Détails de l'événement</h6>
        <div class="card bg-light">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="fw-bold">{{ $payment->event->title ?? 'N/A' }}</h6>
                        <p class="text-muted mb-2">{{ $payment->event->description ?? 'N/A' }}</p>
                        <div class="d-flex gap-3">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ $payment->event->start_date->format('d/m/Y H:i') ?? 'N/A' }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $payment->event->lieu ?? 'N/A' }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        @if($payment->event->image ?? false)
                        <img src="{{ asset('storage/'.$payment->event->image) }}" 
                             class="img-fluid rounded" 
                             style="max-height: 100px; max-width: 150px; object-fit: cover;"
                             alt="{{ $payment->event->title }}">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <h6 class="fw-bold text-primary mb-3">Détails du billet</h6>
        <table class="table table-sm">
            <tr>
                <td class="fw-semibold">Type de billet:</td>
                <td>Billet standard</td>
            </tr>
            <tr>
                <td class="fw-semibold">Prix unitaire:</td>
                <td>{{ number_format($payment->montant ?? 0, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td class="fw-semibold">Quantité:</td>
                <td>1</td>
            </tr>
            <tr>
                <td class="fw-semibold">Total:</td>
                <td class="text-success fw-bold">{{ number_format($payment->montant ?? 0, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>
</div>

@if($payment->statut === 'payé')
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Attention:</strong> Le remboursement de ce paiement réduira automatiquement le nombre de billets vendus pour cet événement.
        </div>
    </div>
</div>
@endif
