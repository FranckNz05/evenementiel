@extends('layouts.app')

@section('title', 'Paiement')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="card-title text-center mb-4">Paiement de votre commande</h2>

                    <!-- Résumé de la commande -->
                    <div class="mb-4">
                        <h5 class="mb-3">Résumé de votre commande</h5>
                        <div class="bg-light p-3 rounded">
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Événement :</div>
                                <div class="col-sm-8">{{ $order->evenement->title ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Billets :</div>
                                <div class="col-sm-8">
                                    @if($order->tickets && $order->tickets->count() > 0)
                                        <ul class="mb-0 ps-3">
                                            @foreach($order->tickets as $ticket)
                                                <li>{{ $ticket->nom ?? $ticket->name ?? 'Billet' }} (x{{ $ticket->pivot->quantity ?? 1 }})</li>
                                            @endforeach
                                        </ul>
                                    @elseif($order->ticket)
                                        {{ $order->ticket->nom ?? $order->ticket->name ?? 'Billet' }} (x{{ $order->quantity ?? 1 }})
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Quantité totale :</div>
                                <div class="col-sm-8">
                                    @if($order->tickets && $order->tickets->count() > 0)
                                        {{ $order->tickets->sum(function($ticket) { return $ticket->pivot->quantity ?? 1; }) }}
                                    @else
                                        {{ $order->quantity ?? 1 }}
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 text-muted">Montant total :</div>
                                <div class="col-sm-8">
                                    <strong class="text-primary">{{ number_format($order->montant_total, 0, ',', ' ') }} FCFA</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Options de paiement -->
                    <div class="mb-4">
                        <h5 class="mb-3">Choisissez votre mode de paiement</h5>
                        <form action="{{ route('payments.process.post', $order) }}" method="POST" id="payment-form">
                            @csrf
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="payment-option border rounded p-3">
                                        <input type="radio" class="btn-check" name="operator" id="airtel" value="airtel" checked required>
                                        <label class="btn btn-outline-primary w-100 h-100" for="airtel">
                                            <img src="{{ asset('images/airtelmoney.jpg') }}" alt="Airtel Money" class="img-fluid mb-2" style="height: 40px;" onerror="this.style.display='none'">
                                            <span class="d-block">Airtel Money</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="payment-option border rounded p-3">
                                        <input type="radio" class="btn-check" name="operator" id="mtn" value="mtn" required>
                                        <label class="btn btn-outline-primary w-100 h-100" for="mtn">
                                            <img src="{{ asset('images/mtn-momo.png') }}" alt="MTN MoMo" class="img-fluid mb-2" style="height: 40px;" onerror="this.style.display='none'">
                                            <span class="d-block">MTN Mobile Money</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Champ numéro de téléphone -->
                            <div class="mb-4">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-2"></i>Numéro de téléphone
                                </label>
                                <input type="tel" 
                                       class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="Ex: 061234567" 
                                       value="{{ old('phone') }}" 
                                       required
                                       pattern="^0[456][0-9]{7}$"
                                       maxlength="9">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Format requis: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx (9 chiffres)</small>
                            </div>

                            <!-- Confirmation -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('confirmation') is-invalid @enderror" 
                                           type="checkbox" 
                                           name="confirmation" 
                                           id="confirmation" 
                                           value="1" 
                                           required>
                                    <label class="form-check-label" for="confirmation">
                                        Je confirme que je souhaite procéder au paiement de <strong>{{ number_format($order->montant_total, 0, ',', ' ') }} FCFA</strong>
                                    </label>
                                    @error('confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Messages d'erreur -->
                            @if ($errors->any())
                                <div class="alert alert-danger mt-3">
                                    <h6 class="alert-heading">Erreurs de validation :</h6>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Bouton de paiement -->
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-credit-card me-2"></i>Payer {{ number_format($order->montant_total, 0, ',', ' ') }} FCFA
                                </button>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Instructions -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Instructions :</h6>
                        <ol class="mb-0">
                            <li>Sélectionnez votre mode de paiement préféré</li>
                            <li>Cliquez sur le bouton "Payer"</li>
                            <li>Suivez les instructions sur votre téléphone pour valider le paiement</li>
                            <li>Une fois le paiement effectué, vous recevrez une confirmation par email</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.payment-option {
    cursor: pointer;
    transition: all 0.3s ease;
}
.payment-option:hover {
    border-color: var(--bs-primary) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}
.btn-check:checked + .btn {
    border-color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}
</style>
@endpush
@endsection
