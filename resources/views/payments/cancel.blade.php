@extends('layouts.app')

@section('title', 'Paiement annulé')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#f44336" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <h2 class="mb-3">Paiement annulé</h2>
                    <p class="lead">Votre paiement a été annulé. Aucun montant n'a été débité.</p>
                    
                    <!-- Affichage des détails d'échec -->
                    <div class="alert alert-danger mt-4">
                        <h5 class="alert-heading">Raison de l'échec :</h5>
                        <p><strong>Message :</strong> {{ $failureMessage }}</p>
                        <p><strong>Code :</strong> {{ $failureCode }}</p>
                    </div>
                    
                    @if(isset($payment))
                        <div class="payment-details mt-4 p-3 bg-light rounded">
                            <p class="mb-1"><strong>Référence:</strong> {{ $payment->matricule }}</p>
                            <p class="mb-1"><strong>Montant:</strong> {{ number_format($payment->montant, 0, ',', ' ') }} FCFA</p>
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('events.index') }}" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-calendar-alt me-1"></i>Voir les événements
                        </a>
                        
                        @if(isset($payment) && $payment->statut === 'en attente')
                            <a href="{{ route('payments.process', $payment->order) }}" class="btn btn-primary ms-2">
                                <i class="fas fa-credit-card me-1"></i>Réessayer le paiement
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection