@extends('layouts.app')

@section('title', 'Échec du paiement')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-3 overflow-hidden">
                <!-- En-tête coloré -->
                <div class="bg-danger bg-gradient py-4 text-white text-center">
                    <div class="mb-3">
                        <i class="bi bi-x-circle-fill" style="font-size: 4rem;"></i>
                    </div>
                    <h1 class="h2 fw-bold mb-2">Paiement échoué</h1>
                    <p class="lead mb-0 opacity-75">Votre transaction n'a pas pu aboutir</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- Carte d'erreur -->
                    <div class="alert alert-danger border-0 bg-danger-subtle rounded-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill text-danger me-3 fs-4"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Transaction non aboutie</h5>
                                <p class="mb-0">Veuillez vérifier les détails ci-dessous et réessayer.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Détails de la transaction -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0 fw-semibold">Détails de la tentative</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Référence :</span>
                                        <span class="fw-medium">{{ $payment->matricule }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Montant :</span>
                                        <span class="fw-medium">{{ number_format($payment->montant, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Méthode :</span>
                                        <span class="fw-medium">{{ ucfirst($payment->methode_paiement) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Date :</span>
                                        <span class="fw-medium">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message d'aide -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0 fw-semibold">Solutions possibles</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-start border-0">
                                    <i class="bi bi-wallet2 text-primary me-3 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Vérifiez votre solde</h6>
                                        <p class="mb-0 text-muted small">Assurez-vous d'avoir suffisamment de fonds sur votre compte.</p>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-start border-0">
                                    <i class="bi bi-check-circle text-primary me-3 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Vérifiez les informations</h6>
                                        <p class="mb-0 text-muted small">Confirmez que vos coordonnées de paiement sont exactes.</p>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-start border-0">
                                    <i class="bi bi-arrow-repeat text-primary me-3 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Essayez à nouveau</h6>
                                        <p class="mb-0 text-muted small">Parfois, une seconde tentative fonctionne.</p>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex align-items-start border-0">
                                    <i class="bi bi-credit-card text-primary me-3 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Changez de méthode</h6>
                                        <p class="mb-0 text-muted small">Utilisez un autre moyen de paiement si disponible.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-3 d-md-flex justify-content-md-center mt-4">
                        @if($payment->reservation_id)
                            <a href="{{ route('payments.reservation.process', $payment->reservation) }}">
                        @else
                            <a href="{{ route('payments.process', $payment->order) }}">
                        @endif
                    </div>

                    <!-- Support -->
                    <div class="text-center mt-5">
                        <div class="d-inline-block bg-light rounded-3 p-3">
                            <i class="bi bi-headset text-primary fs-4 mb-2"></i>
                            <h6 class="fw-bold mb-2">Besoin d'aide ?</h6>
                            <p class="text-muted small mb-2">Notre équipe est là pour vous aider</p>
                            <a href="{{ route('contact') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-envelope me-1"></i> Contacter le support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.list-group-item {
    padding: 1rem 0;
    transition: all 0.3s ease;
}
.list-group-item:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.03);
}
</style>
@endpush
@endsection
