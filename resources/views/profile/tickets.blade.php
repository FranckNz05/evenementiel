@extends('layouts.dashboard')

@section('title', 'Mes billets')

@section('content')
<div class="container py-5">

    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">Mes billets</h2>
            <p class="text-muted">Gérez tous vos billets d'événements</p>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Événement</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Type de billet</th>
                                        <th scope="col">Quantité</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $orderTicket)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($orderTicket->ticket && $orderTicket->ticket->event)
                                                    <img src="{{ asset('storage/' . $orderTicket->ticket->event->image) }}" alt="{{ $orderTicket->ticket->event->title }}" class="rounded me-3" width="50">
                                                @endif
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-0">{{ $orderTicket->ticket ? $orderTicket->ticket->nom : 'Type de billet non disponible' }}</h6>
                                                            <small class="text-muted">
                                                                {{ $orderTicket->ticket && $orderTicket->ticket->event ? $orderTicket->ticket->event->title : 'Événement non disponible' }}
                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="fw-bold">{{ number_format($orderTicket->total_amount, 0, ',', ' ') }} FCFA</div>
                                                            <small class="text-muted">Quantité: {{ $orderTicket->quantity }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($orderTicket->ticket && $orderTicket->ticket->event)
                                                {{ \Carbon\Carbon::parse($orderTicket->ticket->event->start_date)->format('d/m/Y H:i') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $orderTicket->ticket_type }}</td>
                                        <td>{{ $orderTicket->quantity }}</td>
                                        <td>
                                            @if($orderTicket->payment && $orderTicket->payment->status === 'completed')
                                                <span class="badge bg-success">Payé</span>
                                            @elseif($orderTicket->payment && $orderTicket->payment->status === 'pending')
                                                <span class="badge bg-warning">En attente</span>
                                            @elseif($orderTicket->payment && $orderTicket->payment->status === 'failed')
                                                <span class="badge bg-danger">Échoué</span>
                                            @else
                                                <span class="badge bg-secondary">Inconnu</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @if($orderTicket->payment && $orderTicket->payment->status === 'completed')
                                                    <a href="{{ route('tickets.download', $orderTicket->payment->id) }}" class="btn btn-sm btn-outline-primary" title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($orderTicket->quantity > 1)
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton{{ $orderTicket->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-caret-down"></i>
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $orderTicket->id }}">
                                                                <li><a class="dropdown-item" href="{{ route('tickets.download', $orderTicket->payment->id) }}">Télécharger un seul billet</a></li>
                                                                <li><a class="dropdown-item" href="{{ route('tickets.download', $orderTicket->payment->id) }}?all=true">Télécharger tous les billets ({{ $orderTicket->quantity }})</a></li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                @endif

                                                @if($orderTicket->ticket && $orderTicket->ticket->event && \Carbon\Carbon::parse($orderTicket->ticket->event->start_date) > now())
                                                    <a href="{{ route('events.show', $orderTicket->ticket->event->slug) }}" class="btn btn-sm btn-outline-info" title="Voir l'événement">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                    @if(isset($reservationTickets) && $reservationTickets->count() > 0)
                                        @foreach($reservationTickets as $reservation)
                                            @foreach($reservation->tickets as $ticket)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($reservation->event && $reservation->event->image)
                                                                <img src="{{ asset('storage/' . $reservation->event->image) }}" alt="{{ $reservation->event->title }}" class="rounded me-3" width="50">
                                                            @endif
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <h6 class="mb-0">{{ $ticket->nom }}</h6>
                                                                        <small class="text-muted">
                                                                            {{ $reservation->event ? $reservation->event->title : 'Événement non disponible' }}
                                                                        </small>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <div class="fw-bold">{{ number_format(($ticket->pivot->quantity * ($ticket->pivot->price ?? 0)), 0, ',', ' ') }} FCFA</div>
                                                                        <small class="text-muted">Quantité: {{ $ticket->pivot->quantity }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($reservation->event && $reservation->event->start_date)
                                                            {{ \Carbon\Carbon::parse($reservation->event->start_date)->format('d/m/Y H:i') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ $ticket->nom }}</td>
                                                    <td>{{ $ticket->pivot->quantity }}</td>
                                                    <td>
                                                        <span class="badge bg-success">Payé</span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('reservations.downloadTickets', $reservation) }}" class="btn btn-sm btn-outline-primary" title="Télécharger" target="_blank">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            @if($reservation->event && $reservation->event->start_date && \Carbon\Carbon::parse($reservation->event->start_date) > now())
                                                                <a href="{{ route('events.show', $reservation->event->slug) }}" class="btn btn-sm btn-outline-info" title="Voir l'événement">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">
                            {{ $tickets->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    @elseif(isset($reservationTickets) && $reservationTickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Événement</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Type de billet</th>
                                        <th scope="col">Quantité</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservationTickets as $reservation)
                                        @foreach($reservation->tickets as $ticket)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($reservation->event && $reservation->event->image)
                                                            <img src="{{ asset('storage/' . $reservation->event->image) }}" alt="{{ $reservation->event->title }}" class="rounded me-3" width="50">
                                                        @endif
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-0">{{ $ticket->nom }}</h6>
                                                                    <small class="text-muted">
                                                                        {{ $reservation->event ? $reservation->event->title : 'Événement non disponible' }}
                                                                    </small>
                                                                </div>
                                                                <div class="text-end">
                                                                    <div class="fw-bold">{{ number_format(($ticket->pivot->quantity * ($ticket->pivot->price ?? 0)), 0, ',', ' ') }} FCFA</div>
                                                                    <small class="text-muted">Quantité: {{ $ticket->pivot->quantity }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($reservation->event && $reservation->event->start_date)
                                                        {{ \Carbon\Carbon::parse($reservation->event->start_date)->format('d/m/Y H:i') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $ticket->nom }}</td>
                                                <td>{{ $ticket->pivot->quantity }}</td>
                                                <td>
                                                    <span class="badge bg-success">Payé</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('reservations.downloadTickets', $reservation) }}" class="btn btn-sm btn-outline-primary" title="Télécharger" target="_blank">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        @if($reservation->event && $reservation->event->start_date && \Carbon\Carbon::parse($reservation->event->start_date) > now())
                                                            <a href="{{ route('events.show', $reservation->event->slug) }}" class="btn btn-sm btn-outline-info" title="Voir l'événement">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <div class="mb-4">
                                <i class="fas fa-ticket-alt fa-3x text-muted"></i>
                            </div>
                            <h5>Vous n'avez pas encore de billets</h5>
                            <p class="text-muted">Explorez nos événements et achetez des billets pour commencer.</p>
                            <a href="{{ route('events.index') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-search me-2"></i>Découvrir les événements
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
