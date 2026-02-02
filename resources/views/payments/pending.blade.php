@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#FFC107" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h2 class="mb-3">Paiement en attente</h2>
                    <p class="lead">Votre paiement est en cours de traitement. Veuillez patienter quelques instants.</p>
                    <p>Vous recevrez une notification par email dès que le paiement sera confirmé.</p>
                    
                    @if($payment)
                        <div class="payment-details mt-4 p-3 bg-light rounded">
                            <p class="mb-1"><strong>Référence:</strong> {{ $payment->matricule }}</p>
                            <p class="mb-1"><strong>Montant:</strong> {{ number_format($payment->montant, 0, ',', ' ') }} XAF</p>
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Vérifier périodiquement l'état du paiement
    document.addEventListener('DOMContentLoaded', function() {
        function checkPaymentStatus() {
            fetch(`/api/payments/{{ $payment->id }}/status`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'paid') {
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Vérifier toutes les 5 secondes
        setInterval(checkPaymentStatus, 5000);
    });
</script>
@endpush
@endsection