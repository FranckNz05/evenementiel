@extends('layouts.dashboard')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Historique des paiements</h2>

            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Événement</th>
                                <th>Ticket</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Méthode</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>
                                        @if($payment->event)
                                            <a href="{{ route('events.show', $payment->event) }}">
                                                {{ $payment->event->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">Événement supprimé</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->orderTicket && $payment->orderTicket->ticket)
                                            {{ $payment->orderTicket->ticket->nom }}
                                        @else
                                            <span class="text-muted">Ticket non disponible</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($payment->montant, 2) }} FC</td>
                                    <td>
                                        @if($payment->statut === 'payé')
                                            <span class="badge bg-success">Payé</span>
                                        @elseif($payment->statut === 'en attente')
                                            <span class="badge bg-warning">En attente</span>
                                        @else
                                            <span class="badge bg-danger">Échoué</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->methode }}</td>
                                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($payment->statut === 'payé')
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="window.location.href='{{ route('payments.success', $payment) }}'">
                                                Voir le ticket
                                            </button>
                                        @elseif($payment->statut === 'en attente')
                                            <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="window.location.href='{{ route('payments.checkout', $payment->ticket) }}'">
                                                Finaliser
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="window.location.href='{{ route('payments.checkout', $payment->ticket) }}'">
                                                Réessayer
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    Vous n'avez pas encore effectué de paiement.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

