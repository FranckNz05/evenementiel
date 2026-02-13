@extends('layouts.app')

@section('title', 'Paiement de la reservation')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 bg-light shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">Détails de la reservation #{{ $order->order_number }}</h4>

                        <!-- Détails de l'événement -->
                        <div class="mb-4">
                            <h5>{{ $order->event->title }}</h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-calendar me-2"></i>
                                {{ $order->event->start_date->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-muted mb-0">
                                <i class="bi bi-geo-alt me-2"></i>
                                {{ $order->event->location }}
                            </p>
                        </div>

                        <!-- Détails des billets -->
                        <div class="mb-4">
                            <h6 class="mb-3">Billets sélectionnés</h6>
                            @foreach($order->tickets as $ticket)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="fw-medium">{{ $ticket->title }}</span>
                                        <small class="text-muted d-block">{{ $ticket->pivot->quantity }}x {{ number_format($ticket->price, 0, ',', ' ') }} FCFA</small>
                                    </div>
                                    <span>{{ number_format($ticket->price * $ticket->pivot->quantity, 0, ',', ' ') }} FCFA</span>
                                </div>
                            @endforeach
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>

                        <!-- Date limite de paiement -->
                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-clock me-2"></i>
                            Date limite de paiement : {{ $order->created_at->addDays(2)->format('d/m/Y H:i') }}
                        </div>

                        <!-- Options de paiement -->
                        <div class="mb-4">
                            <h6 class="mb-3">Choisir le mode de paiement</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check payment-option border rounded p-3">
                                        <input class="form-check-input" type="radio" name="payment_method" id="mobile_money" value="mobile_money" checked>
                                        <label class="form-check-label w-100" for="mobile_money">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-phone fs-4 me-2"></i>
                                                <div>
                                                    <span class="d-block">Mobile Money</span>
                                                    <small class="text-muted">MTN, Airtel, Orange</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check payment-option border rounded p-3">
                                        <input class="form-check-input" type="radio" name="payment_method" id="bank_card" value="bank_card">
                                        <label class="form-check-label w-100" for="bank_card">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-credit-card fs-4 me-2"></i>
                                                <div>
                                                    <span class="d-block">Carte bancaire</span>
                                                    <small class="text-muted">Visa, Mastercard</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton de paiement -->
                        <div class="d-grid">
                            <form action="{{ route('orders.process-payment', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_method" value="mobile_money">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Payer {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
                                </button>
                            </form>
                        </div>
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
    .payment-option .form-check-input:checked + .form-check-label {
        color: var(--bs-primary);
    }
    .payment-option .form-check-input:checked + .form-check-label i {
        color: var(--bs-primary);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentForm = document.querySelector('form');
    const paymentMethodInput = paymentForm.querySelector('input[name="payment_method"]');

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            paymentMethodInput.value = this.value;
        });
    });
});
</script>
@endpush
@endsection
