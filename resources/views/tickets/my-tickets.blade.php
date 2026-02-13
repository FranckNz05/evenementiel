@extends('layouts.app')

@section('title', 'Mes billets')

@section('content')
<div class="container-xxl py-5" style="margin-top: 12rem;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 bg-light shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-4">Mes billets</h4>

                        @if($tickets->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                <h5>Vous n'avez pas encore de billets</h5>
                                <p class="text-muted">Découvrez nos <a href="{{ url('/direct-events') }}" class="text-primary">événements</a> et réservez vos premiers billets.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Événement</th>
                                            <th>Date</th>
                                            <th>Type de billet</th>
                                            <th>Prix</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tickets as $ticket)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($ticket->event->image)
                                                            <img src="{{ asset('storage/' . $ticket->event->image) }}"
                                                                alt="{{ $ticket->event->title }}"
                                                                class="rounded me-3"
                                                                style="width: 48px; height: 48px; object-fit: cover;">
                                                        @else
                                                            <div class="rounded bg-secondary d-flex align-items-center justify-content-center me-3"
                                                                style="width: 48px; height: 48px;">
                                                                <i class="bi bi-calendar-event text-white"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $ticket->event->title }}</h6>
                                                            <small class="text-muted">{{ $ticket->event->location }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $ticket->event->start_date->format('d/m/Y H:i') }}</td>
                                                <td>{{ $ticket->type }}</td>
                                                <td>{{ number_format($ticket->price, 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    @if($ticket->event->start_date->isPast())
                                                        <span class="badge bg-secondary">Terminé</span>
                                                    @elseif($ticket->order && $ticket->order->status === 'completed')
                                                        <span class="badge bg-success">Validé</span>
                                                    @else
                                                        <span class="badge bg-warning">En attente</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('events.show', $ticket->event) }}"
                                                           class="btn btn-sm btn-outline-primary">
                                                            Voir l'événement
                                                        </a>
                                                        @if($ticket->order && $ticket->order->status === 'pending')
                                                            <a href="{{ route('orders.pay', $ticket->order) }}"
                                                               class="btn btn-sm btn-primary">
                                                                Payer
                                                            </a>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
