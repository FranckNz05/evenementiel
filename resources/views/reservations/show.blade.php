@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Détails de la réservation #{{ $reservation->id }}</h2>
                </div>

                <div class="card-body">
                    @if($reservation->event)
                        <div class="mb-4">
                            <h4>Événement</h4>
                            <p class="mb-1"><strong>Nom :</strong> {{ $reservation->event->title ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Date :</strong> {{ $reservation->event->start_date ? $reservation->event->start_date->format('d/m/Y H:i') : 'Non spécifiée' }}</p>
                            <p class="mb-1"><strong>Lieu :</strong> {{ $reservation->event->lieu ?? 'N/A' }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h4>Détails de la réservation</h4>
                        <p class="mb-1"><strong>Référence :</strong> {{ $reservation->reference ?? '-' }}</p>
                        <p class="mb-1"><strong>Statut :</strong> 
                            <span class="badge bg-{{ $reservation->status === 'Réservé' ? 'success' : ($reservation->status === 'Annulé' ? 'danger' : 'secondary') }}">
                                {{ $reservation->status }}
                            </span>
                        </p>
                        <p class="mb-1"><strong>Date de réservation :</strong> {{ $reservation->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($reservation->tickets->count() > 0)
                        <div class="mb-4">
                            <h4>Billets</h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Type de billet</th>
                                            <th>Quantité</th>
                                            <th>Prix unitaire</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reservation->tickets as $ticket)
                                            <tr>
                                                <td>{{ $ticket->nom }}</td>
                                                <td>{{ $ticket->pivot->quantity }}</td>
                                                <td>{{ number_format($ticket->pivot->price ?? 0, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ number_format(($ticket->pivot->quantity * ($ticket->pivot->price ?? 0)), 0, ',', ' ') }} FCFA</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total :</th>
                                            <th>
                                                {{ number_format($reservation->tickets->sum(function($t){ return ($t->pivot->quantity * ($t->pivot->price ?? 0)); }), 0, ',', ' ') }} FCFA
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    @php
                        $total = $reservation->tickets->sum(function($t) { 
                            return ($t->pivot->quantity ?? 0) * ($t->pivot->price ?? 0); 
                        });
                        $canPay = $reservation->status === 'Réservé' && !$reservation->payment;
                        $eventStartDate = $reservation->event && $reservation->event->start_date 
                            ? \Carbon\Carbon::parse($reservation->event->start_date) 
                            : null;
                        $sevenDaysBefore = $eventStartDate ? $eventStartDate->copy()->subDays(7) : null;
                        $daysRemaining = $sevenDaysBefore ? \Carbon\Carbon::now()->diffInDays($sevenDaysBefore, false) : null;
                    @endphp

                    @if($canPay && $daysRemaining !== null)
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important :</strong> Vous devez payer cette réservation dans les <strong>{{ $daysRemaining }} jour(s)</strong> avant le début de l'événement ({{ $eventStartDate->format('d/m/Y H:i') }}), sinon elle sera automatiquement annulée.
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('reservations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                        </a>

                        <div>
                            @if($canPay)
                                <a href="{{ route('reservations.pay', $reservation) }}" class="btn btn-success me-2">
                                    <i class="fas fa-credit-card me-1"></i> Payer {{ number_format($total, 0, ',', ' ') }} FCFA
                                </a>
                            @endif

                            @if($reservation->status === 'Payé' && $reservation->payment)
                                <a href="{{ route('reservations.downloadTickets', $reservation) }}" class="btn btn-primary me-2" target="_blank">
                                    <i class="fas fa-download me-1"></i> Télécharger les billets
                                </a>
                            @endif

                            @if($reservation->status === 'Réservé' && !$reservation->payment)
                                <form action="{{ route('reservations.cancel', $reservation) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                        <i class="fas fa-times me-1"></i> Annuler la réservation
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection