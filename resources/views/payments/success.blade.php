@extends('layouts.app')

@section('title', 'Paiement réussi')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="card-title mb-4">Paiement réussi !</h2>
                    
                    <!-- Message Airtel -->
                    @php
                        $details = json_decode($payment->details ?? '{}', true) ?: [];
                        $airtelMessage = $details['airtel_message'] ?? $details['callback_message'] ?? $details['verification_result']['message'] ?? null;
                        $airtelTransactionStatus = $details['airtel_transaction_status'] ?? $details['callback_status'] ?? null;
                    @endphp

                    @if($airtelMessage)
                        <div class="alert alert-success mb-4">
                            <h6 class="alert-heading">
                                <i class="bi bi-check-circle me-2"></i>
                                Message de confirmation Airtel Money
                            </h6>
                            <p class="mb-0">
                                <strong>{{ is_array($airtelMessage) ? ($airtelMessage['message'] ?? 'Transaction réussie') : $airtelMessage }}</strong>
                            </p>
                            @if($airtelTransactionStatus)
                                <small class="text-muted d-block mt-2">
                                    <strong>Statut transaction :</strong> {{ $airtelTransactionStatus }}
                                </small>
                            @endif
                        </div>
                    @else
                        <p class="lead mb-4">Votre paiement a été traité avec succès.</p>
                    @endif

                    <!-- Détails de la transaction -->
                    <div class="bg-light p-4 rounded mb-4">
                        <div class="row mb-3">
                            <div class="col-sm-6 text-muted text-sm-end">Référence :</div>
                            <div class="col-sm-6 text-sm-start">{{ $payment->matricule }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-6 text-muted text-sm-end">Montant :</div>
                            <div class="col-sm-6 text-sm-start">{{ number_format($payment->montant, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-6 text-muted text-sm-end">Mode de paiement :</div>
                            <div class="col-sm-6 text-sm-start">{{ ucfirst($payment->methode_paiement) }}</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 text-muted text-sm-end">Date :</div>
                            <div class="col-sm-6 text-sm-start">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    @if($payment->qr_code)
                    <div class="mb-4">
                        <h5 class="mb-3">Votre QR Code</h5>
                        <div class="d-flex justify-content-center">
                            <img src="{{ asset($payment->qr_code) }}" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                        </div>
                        <p class="text-muted mt-2">
                            Conservez ce QR Code, il vous sera demandé à l'entrée de l'événement
                        </p>
                    </div>
                    @endif

                    <!-- Détails de l'événement -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h5 class="card-title">{{ $payment->order->evenement->title }}</h5>
                            <p class="card-text mb-1">
                                <i class="bi bi-calendar me-2"></i>
                                {{ $payment->order->evenement->start_date->format('d/m/Y H:i') }}
                            </p>
                            <p class="card-text mb-0">
                                <i class="bi bi-geo-alt me-2"></i>
                                {{ $payment->order->evenement->adresse }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="{{ route('tickets.download', $payment) }}" class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>Télécharger le billet
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list me-2"></i>Mes commandes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
