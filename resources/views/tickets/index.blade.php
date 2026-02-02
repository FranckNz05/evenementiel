@extends('layouts.app')
@push('styles')
<style>
/* tickets.css */
.ticket-card {
    border-radius: 10px;
    overflow: hidden;
}

.ticket-table th {
    white-space: nowrap;
    position: sticky;
    top: 0;
    background: white;
}

.ticket-badge {
    font-family: monospace;
    padding: 5px 8px;
}

.action-btn {
    min-width: 120px;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}
</style>
@endpush
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h2 class="h5 mb-0">Mes Billets</h2>
        </div>
        
        <div class="card-body">
            @if($payments->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Vous n'avez aucun billet pour le moment.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Événement</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th class="text-center">Quantité</th>
                                <th>Référence</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                @foreach($payment->order->tickets as $ticket)
                                    <tr>
                                        <td>
                                            <a href="{{ route('events.show', $payment->order->event) }}" class="text-decoration-none">
                                                <strong>{{ $payment->order->event->title }}</strong>
                                                <div class="text-muted small">{{ $payment->order->event->lieu }}</div>
                                            </a>
                                        </td>
                                        <td>
                                            {{ $payment->order->event->start_date->isoFormat('LL') }}
                                            <div class="text-muted small">{{ $payment->order->event->start_date->format('H:i') }}</div>
                                        </td>
                                        <td>{{ $ticket->nom }}</td>
                                        <td class="text-center">{{ $ticket->pivot->quantity }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $payment->matricule }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('tickets.download', $payment) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Télécharger les billets">
                                                <i class="fas fa-download"></i> Télécharger
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $payments->links('vendor.pagination.bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
