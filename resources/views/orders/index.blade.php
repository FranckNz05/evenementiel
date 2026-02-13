@extends('layouts.dashboard')

@section('title', 'Mes reservations')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-primary text-white py-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0"><i class="fas fa-receipt me-2"></i>Mes reservations</h2>
                        <span class="badge bg-white text-primary">{{ $orders->total() }} reservation(s)</span>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($orders->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-shopping-basket fa-4x text-muted"></i>
                            </div>
                            <h4 class="h5 text-muted mb-3">Vous n'avez pas encore de reservation</h4>
                            <a href="{{ route('events.index') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-alt me-2"></i>Voir les événements
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">N° reservation</th>
                                        <th>Date</th>
                                        <th>Événement</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr class="align-middle">
                                            <td class="ps-4 fw-bold text-primary">{{ $order->matricule }}</td>
                                            <td>
                                                <small class="text-muted">{{ $order->created_at->format('d/m/Y') }}</small><br>
                                                <small>{{ $order->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <a href="{{ route('events.show', $order->event ?? '#') }}" class="text-decoration-none">
                                                            <h6 class="mb-1">{{ $order->event ? $order->event->title : 'Événement non disponible' }}</h6>
                                                        </a>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            {{ $order->event ? $order->event->start_date->format('d/m/Y H:i') : 'Date non disponible' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-bold">
                                                @php
                                                    // Calculer le montant total
                                                    $montantTotal = 0;
                                                    if ($order->montant_total > 0) {
                                                        $montantTotal = $order->montant_total;
                                                    } elseif ($order->payment) {
                                                        $montantTotal = $order->payment->montant;
                                                    } else {
                                                        // Calculer à partir des tickets
                                                        foreach ($order->tickets as $ticket) {
                                                            $montantTotal += $ticket->pivot->total_amount;
                                                        }
                                                    }
                                                @endphp
                                                {{ number_format($montantTotal, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td>
                                                @if($order->isPending())
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-clock me-1"></i> En attente
                                                    </span>
                                                @elseif($order->isPaid())
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i> Payée
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i> Annulée
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('orders.show', $order) }}"
                                                       class="btn btn-sm btn-outline-primary rounded-pill"
                                                       data-bs-toggle="tooltip"
                                                       title="Détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    @if($order->isPending())
                                                        <a href="{{ route('payments.process', $order) }}"
                                                           class="btn btn-sm btn-success rounded-pill"
                                                           data-bs-toggle="tooltip"
                                                           title="Payer maintenant">
                                                            <i class="fas fa-credit-card me-1"></i> Payer
                                                        </a>
                                                    @endif

                                                    @if($order->isPaid())
                                                        @if($order->payment)
                                                        <a href="{{ route('tickets.download', $order->payment) }}"
                                                           class="btn btn-sm btn-outline-secondary rounded-pill"
                                                           data-bs-toggle="tooltip"
                                                           title="Télécharger le ticket">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        @else
                                                        <button type="button"
                                                           class="btn btn-sm btn-outline-secondary rounded-pill disabled"
                                                           data-bs-toggle="tooltip"
                                                           title="Paiement en cours de traitement">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                @if($orders->hasPages())
                <div class="card-footer bg-light border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Affichage de {{ $orders->firstItem() }} à {{ $orders->lastItem() }} sur {{ $orders->total() }} reservations
                        </div>
                        <div>
                            {{ $orders->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(15, 26, 61, 0.03);
    }
    .card {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
    }
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    .rounded-pill {
        padding-left: 1rem;
        padding-right: 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activer les tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
