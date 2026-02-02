@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>
                        Paiement Échoué
                    </h4>
                </div>
                
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <h3 class="text-danger mb-3">Paiement échoué</h3>
                    <p class="lead">Votre paiement n'a pas pu être traité.</p>

                    <!-- Informations du paiement -->
                    <div class="alert alert-danger">
                        <h5 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Détails de l'échec
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Référence :</strong> {{ $payment->matricule }}<br>
                                <strong>Montant :</strong> {{ number_format($payment->montant, 0, ',', ' ') }} XAF<br>
                                <strong>Méthode :</strong> {{ ucfirst($payment->methode_paiement) }}
                            </div>
                            <div class="col-md-6">
                                <strong>Date :</strong> {{ $payment->created_at->format('d/m/Y à H:i') }}<br>
                                <strong>Statut :</strong> 
                                <span class="badge bg-danger">{{ ucfirst($payment->statut) }}</span><br>
                                <strong>Événement :</strong> {{ $payment->order->event->title }}
                            </div>
                        </div>
                    </div>

                    <!-- Raison de l'échec -->
                    @php
                        $details = json_decode($payment->details ?? '{}', true) ?: [];
                        $airtelMessage = $details['airtel_message'] ?? $details['callback_message'] ?? $details['error_message'] ?? $details['verification_result']['message'] ?? null;
                        $airtelErrorCode = $details['airtel_error_code'] ?? $details['response_code'] ?? $details['error_code'] ?? $details['callback_status'] ?? null;
                        $airtelTransactionStatus = $details['airtel_transaction_status'] ?? $details['callback_status'] ?? null;
                        $airtelResponse = $details['airtel_response'] ?? $details['verification_result'] ?? null;
                    @endphp

                    @if($airtelMessage || $airtelErrorCode)
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Message d'erreur Airtel Money
                            </h6>
                            @if($airtelMessage)
                                <p class="mb-2">
                                    <strong>{{ is_array($airtelMessage) ? ($airtelMessage['message'] ?? 'Erreur de paiement') : $airtelMessage }}</strong>
                                </p>
                            @endif
                            @if($airtelTransactionStatus)
                                <small class="text-muted d-block mb-2">
                                    <strong>Statut transaction :</strong> {{ $airtelTransactionStatus }}
                                    @if($airtelTransactionStatus === 'TF')
                                        (Transaction Failed)
                                    @elseif($airtelTransactionStatus === 'TE')
                                        (Transaction Expired)
                                    @endif
                                </small>
                            @endif
                            @if($airtelErrorCode)
                                <small class="text-muted d-block mb-2">
                                    <strong>Code d'erreur :</strong> {{ $airtelErrorCode }}
                                </small>
                            @endif
                            @if(isset($airtelResponse['status']['message']) && $airtelResponse['status']['message'] !== $airtelMessage)
                                <p class="mb-0 small mt-2">
                                    <em>{{ $airtelResponse['status']['message'] }}</em>
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-info-circle me-2"></i>Raisons possibles de l'échec</h6>
                        <p class="mb-0">
                            Le paiement peut échouer pour plusieurs raisons :
                        </p>
                        <ul class="mb-0 mt-2 text-start">
                            <li>Fonds insuffisants</li>
                            <li>Problème de connexion</li>
                            <li>Erreur de saisie</li>
                            <li>Problème technique temporaire</li>
                            <li>Transaction refusée par votre opérateur</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('payments.process', $payment->order) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-redo me-2"></i>Réessayer le paiement
                        </a>
                        <a href="{{ route('orders.show', $payment->order) }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-eye me-2"></i>Voir la commande
                        </a>
                    </div>

                    <!-- Note -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-lightbulb me-2"></i>Note</h6>
                        <p class="mb-0">
                            Vous pouvez réessayer le paiement à tout moment. Vos billets restent réservés 
                            temporairement en attendant le paiement.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection