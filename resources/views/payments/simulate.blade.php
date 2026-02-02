@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="page-header mb-4">
        <h1 class="page-title mb-0"><i class="fas fa-credit-card me-2"></i>Paiement de la commande</h1>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Simulation de Paiement
                    </h4>
                </div>
                
                <div class="card-body">
                    <!-- Informations de la commande -->
                    <div class="alert alert-info">
                        <h5 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            Informations de la commande
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Événement :</strong> {{ $order->event->title }}<br>
                                <strong>Date :</strong> {{ \Carbon\Carbon::parse($order->event->start_date)->format('d/m/Y à H:i') }}<br>
                                <strong>Lieu :</strong> {{ $order->event->lieu }}
                            </div>
                            <div class="col-md-6">
                                <strong>Référence :</strong> {{ $order->matricule }}<br>
                                <strong>Date de commande :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}<br>
                                <strong>Statut :</strong> 
                                <span class="badge bg-warning">{{ ucfirst($order->statut) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Détails des billets -->
                    <div class="mb-4">
                        <h5><i class="fas fa-ticket-alt me-2"></i>Détails des billets</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Type de billet</th>
                                        <th>Quantité</th>
                                        <th>Prix unitaire</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->nom }}</td>
                                        <td>{{ $ticket->pivot->quantity }}</td>
                                        <td>{{ number_format($ticket->prix, 0, ',', ' ') }} XAF</td>
                                        <td>{{ number_format($ticket->prix * $ticket->pivot->quantity, 0, ',', ' ') }} XAF</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <th colspan="3">Total à payer</th>
                                        <th>{{ number_format($order->montant_total, 0, ',', ' ') }} XAF</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Formulaire de paiement (sélection opérateur + simulation) -->
                    <form action="{{ route('payments.simulate', $order) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-mobile-alt me-2"></i>Choisissez votre opérateur</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="w-100 border rounded p-3 d-flex align-items-center gap-3" style="cursor: pointer;">
                                        <input type="radio" name="operator" value="airtel" class="form-check-input me-2" required>
                                        <img src="{{ asset('images/airtelmoney.jpg') }}" alt="Airtel Money" style="height: 28px;">
                                        <span class="fw-semibold">Airtel Money</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="w-100 border rounded p-3 d-flex align-items-center gap-3" style="cursor: pointer;">
                                        <input type="radio" name="operator" value="mtn" class="form-check-input me-2" required>
                                        <img src="{{ asset('images/mtnmoney.jpg') }}" alt="MTN Mobile Money" style="height: 28px;">
                                        <span class="fw-semibold">MTN Mobile Money</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label fw-semibold">Numéro Mobile Money</label>
                            <input type="text" inputmode="numeric" pattern="^0(4|5|6)\\d{7}$" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Ex: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx" value="{{ old('phone') }}" required>
                            <div class="form-text">Formats acceptés: 05xxxxxxx, 06xxxxxxx ou 04xxxxxxx (9 chiffres).</div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <h5><i class="fas fa-cogs me-2"></i>Options de simulation</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="success" value="simulation_success" checked>
                                        <label class="form-check-label" for="success">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Simulation de succès
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="failure" value="simulation_failure">
                                        <label class="form-check-label" for="failure">
                                            <i class="fas fa-times-circle text-danger me-2"></i>
                                            Simulation d'échec
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="confirmation" 
                                       id="confirmation" required>
                                <label class="form-check-label" for="confirmation">
                                    Je confirme que ceci est une simulation de paiement et que je comprends 
                                    qu'aucun vrai paiement ne sera effectué.
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la commande
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-play me-2"></i>Lancer la simulation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Note importante -->
            <div class="alert alert-warning mt-4">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Note importante</h6>
                <p class="mb-0">
                    Ce système de paiement est en mode simulation. Aucun vrai paiement ne sera effectué. 
                    Cette fonctionnalité est destinée aux tests et au développement.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Protection contre les doubles soumissions
    const paymentForm = document.querySelector('form');
    let isSubmitting = false;
    
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            // Vérifier la case de confirmation
            const confirmationCheckbox = document.getElementById('confirmation');
            if (confirmationCheckbox && !confirmationCheckbox.checked) {
                e.preventDefault();
                alert('Veuillez confirmer que vous comprenez qu\'il s\'agit d\'une simulation.');
                return false;
            }
            
            // Empêcher les doubles soumissions
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            // Marquer comme en cours de soumission
            isSubmitting = true;
            
            // Désactiver le bouton de soumission
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement en cours...';
            }
            
            return true;
        });
    }
});
</script>
@endpush