@extends('layouts.app')

@section('title', 'Échec du paiement')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="card-title mb-4">Échec du paiement</h2>
                    <p class="lead mb-4">Malheureusement, votre paiement n'a pas pu être traité.</p>

                    <!-- Détails de la tentative -->
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

                    <!-- Message d'erreur Airtel -->
                    @php
                        $details = json_decode($payment->details ?? '{}', true) ?: [];
                        $airtelMessage = $details['airtel_message'] ?? $details['callback_message'] ?? $details['error_message'] ?? null;
                        $airtelErrorCode = $details['airtel_error_code'] ?? $details['response_code'] ?? $details['error_code'] ?? null;
                        $airtelResponse = $details['airtel_response'] ?? null;
                    @endphp

                    @if($airtelMessage || $airtelErrorCode)
                        <div class="alert alert-danger mb-4">
                            <h6 class="alert-heading">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Message d'erreur Airtel Money
                            </h6>
                            @if($airtelMessage)
                                <p class="mb-2">
                                    <strong>{{ is_array($airtelMessage) ? ($airtelMessage['message'] ?? 'Erreur de paiement') : $airtelMessage }}</strong>
                                </p>
                            @endif
                            @if($airtelErrorCode)
                                <small class="text-muted d-block mb-2">
                                    <strong>Code d'erreur :</strong> {{ $airtelErrorCode }}
                                </small>
                            @endif
                            @if(isset($airtelResponse['status']['message']) && $airtelResponse['status']['message'] !== $airtelMessage)
                                <p class="mb-0 small">
                                    {{ $airtelResponse['status']['message'] }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <!-- Message d'aide -->
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading mb-3">Que faire maintenant ?</h6>
                        <ul class="text-start mb-0">
                            <li>Vérifiez que vous avez suffisamment de fonds sur votre compte</li>
                            <li>Assurez-vous que vos informations de paiement sont correctes</li>
                            <li>Essayez avec un autre mode de paiement</li>
                            <li>Si le problème persiste, contactez notre support</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="{{ route('payments.process', $payment->order) }}" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Réessayer le paiement
                        </a>
                        <a href="{{ route('orders.show', $payment->order) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Retour à la commande
                        </a>
                    </div>

                    <!-- Support -->
                    <div class="mt-4">
                        <p class="text-muted mb-0">
                            Besoin d'aide ? <a href="{{ route('contact') }}">Contactez notre support</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
