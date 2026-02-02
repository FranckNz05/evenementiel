@extends('layouts.app')

@section('title', 'Paiement réussi - PawaPay')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Paiement effectué avec succès !
                    </h4>
                </div>
                
                <div class="card-body p-4">
                    <!-- Message de succès -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-success mb-3">Félicitations !</h5>
                        <p class="lead">Votre paiement a été traité avec succès.</p>
                    </div>

                    <!-- Détails de la transaction -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-receipt me-2"></i>
                                        Détails du paiement
                                    </h6>
                                    <p class="mb-1"><strong>Montant:</strong> {{ number_format($transaction->amount, 0, ',', ' ') }} FCFA</p>
                                    <p class="mb-1"><strong>Fournisseur:</strong> {{ ucfirst($transaction->provider) }}</p>
                                    <p class="mb-1"><strong>Téléphone:</strong> {{ $transaction->phone_number }}</p>
                                    <p class="mb-0"><strong>Date:</strong> {{ $transaction->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        Informations de la commande
                                    </h6>
                                    @if($order)
                                        <p class="mb-1"><strong>Événement:</strong> {{ $order->event->nom ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Nombre de billets:</strong> {{ $order->tickets->sum('pivot.quantity') }}</p>
                                        <p class="mb-1"><strong>Date de l'événement:</strong> {{ $order->event->date_debut ?? 'N/A' }}</p>
                                        <p class="mb-0"><strong>Référence:</strong> {{ $order->reference }}</p>
                                    @else
                                        <p class="text-muted">Informations de commande non disponibles</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ID de transaction -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            Référence de transaction
                        </h6>
                        <p class="mb-0">
                            <strong>ID Transaction:</strong> 
                            <code>{{ $transaction->deposit_id }}</code>
                        </p>
                        <small class="text-muted">
                            Conservez cette référence pour vos dossiers.
                        </small>
                    </div>

                    <!-- Prochaines étapes -->
                    <div class="alert alert-success">
                        <h6 class="alert-heading">
                            <i class="fas fa-list-check me-2"></i>
                            Prochaines étapes
                        </h6>
                        <ul class="mb-0">
                            <li>Vous recevrez un email de confirmation sous peu</li>
                            <li>Vos billets seront disponibles dans votre compte</li>
                            <li>Présentez votre billet à l'entrée de l'événement</li>
                            <li>Gardez votre téléphone chargé pour les notifications</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <a href="{{ route('orders.show', $order->id ?? '#') }}" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>
                            Voir ma commande
                        </a>
                        
                        <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-calendar me-2"></i>
                            Découvrir d'autres événements
                        </a>
                        
                        <button type="button" class="btn btn-outline-success" onclick="downloadReceipt()">
                            <i class="fas fa-download me-2"></i>
                            Télécharger le reçu
                        </button>
                    </div>

                    <!-- Informations de sécurité -->
                    <div class="text-center text-muted">
                        <small>
                            <i class="fas fa-shield-alt me-1"></i>
                            Transaction sécurisée et cryptée - PawaPay
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function downloadReceipt() {
    // Créer un reçu simple
    const receiptContent = `
        RECU DE PAIEMENT PAWAPAY
        =========================
        
        Date: {{ $transaction->updated_at->format('d/m/Y H:i') }}
        Montant: {{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
        Fournisseur: {{ ucfirst($transaction->provider) }}
        Téléphone: {{ $transaction->phone_number }}
        ID Transaction: {{ $transaction->deposit_id }}
        
        @if($order)
        Événement: {{ $order->event->nom ?? 'N/A' }}
        Nombre de billets: {{ $order->tickets->sum('pivot.quantity') }}
        Référence commande: {{ $order->reference }}
        @endif
        
        Merci pour votre achat !
    `;
    
    // Créer et télécharger le fichier
    const blob = new Blob([receiptContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'recu-paiement-{{ $transaction->deposit_id }}.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Confetti animation pour célébrer le succès
document.addEventListener('DOMContentLoaded', function() {
    // Simple animation de célébration
    setTimeout(() => {
        const successIcon = document.querySelector('.fa-check-circle');
        if (successIcon) {
            successIcon.style.transform = 'scale(1.2)';
            successIcon.style.transition = 'transform 0.3s ease';
            
            setTimeout(() => {
                successIcon.style.transform = 'scale(1)';
            }, 300);
        }
    }, 500);
});
</script>
@endpush
@endsection
