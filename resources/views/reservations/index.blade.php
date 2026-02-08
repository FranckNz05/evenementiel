@extends('layouts.dashboard')

@section('title', 'Mes réservations')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Mes réservations</h2>
            <p class="text-muted">Gérez vos réservations d'événements</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="modern-card mb-4">
        <div class="card-body-modern p-3">
            <div class="row g-2">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="status-filter" id="filter-all" autocomplete="off" checked>
                        <label class="btn btn-outline-primary btn-sm" for="filter-all">
                            <i class="fas fa-list me-1"></i> Toutes
                        </label>

                        <input type="radio" class="btn-check" name="status-filter" id="filter-paye" autocomplete="off">
                        <label class="btn btn-outline-success btn-sm" for="filter-paye">
                            <i class="fas fa-check-circle me-1"></i> Payées
                        </label>

                        <input type="radio" class="btn-check" name="status-filter" id="filter-reserve" autocomplete="off">
                        <label class="btn btn-outline-warning btn-sm" for="filter-reserve">
                            <i class="fas fa-clock me-1"></i> En attente
                        </label>

                        <input type="radio" class="btn-check" name="status-filter" id="filter-annule" autocomplete="off">
                        <label class="btn btn-outline-danger btn-sm" for="filter-annule">
                            <i class="fas fa-times-circle me-1"></i> Annulées
                        </label>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <span class="text-muted small">
                        <i class="fas fa-receipt me-1"></i> 
                        {{ $reservations->total() }} réservation{{ $reservations->total() > 1 ? 's' : '' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($reservations->count() > 0)
        <!-- Reservations List -->
        <div class="row g-3">
            @foreach($reservations as $reservation)
                @php
                    // Vérification de sécurité pour éviter les erreurs null
                    $hasTicket = $reservation->ticket !== null;
                    $hasEvent = $hasTicket && $reservation->ticket->event !== null;
                @endphp
                
                @if($hasTicket && $hasEvent)
                    <div class="col-12" data-status="{{ strtolower($reservation->status) }}">
                        <div class="modern-card hover-shadow">
                            <div class="card-body-modern p-4">
                                <div class="row align-items-center">
                                    <!-- Reference & Event Info -->
                                    <div class="col-lg-5">
                                        <div class="d-flex align-items-start mb-2">
                                            <div class="me-3">
                                                @if($reservation->ticket->event->image)
                                                    <img src="{{ asset('storage/' . $reservation->ticket->event->image) }}" 
                                                         alt="{{ $reservation->ticket->event->title }}"
                                                         class="rounded shadow-sm"
                                                         style="width: 64px; height: 64px; object-fit: cover;">
                                                @else
                                                    <div class="rounded bg-gradient-blue d-flex align-items-center justify-content-center shadow-sm"
                                                         style="width: 64px; height: 64px;">
                                                        <i class="fas fa-calendar-alt text-white fa-lg"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-light text-dark me-2 font-monospace">
                                                        {{ $reservation->reference_number }}
                                                    </span>
                                                    @if($reservation->status === 'Payé')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i> Payé
                                                        </span>
                                                    @elseif($reservation->status === 'Réservé')
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="fas fa-clock me-1"></i> En attente
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle me-1"></i> Annulé
                                                        </span>
                                                    @endif
                                                </div>
                                                <h5 class="mb-1">
                                                    <a href="{{ route('events.show', $reservation->ticket->event) }}" 
                                                       class="text-dark text-decoration-none hover-primary">
                                                        {{ $reservation->ticket->event->title }}
                                                    </a>
                                                </h5>
                                                <p class="text-muted small mb-0">
                                                    <i class="fas fa-ticket-alt me-1"></i>
                                                    {{ $reservation->ticket->nom }} × {{ $reservation->quantity }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Date & Amount -->
                                    <div class="col-lg-4">
                                        <div class="mb-2">
                                            <small class="text-muted d-block mb-1">Date de l'événement</small>
                                            <strong class="text-dark">
                                                <i class="fas fa-calendar me-1 text-primary"></i>
                                                {{ $reservation->ticket->event->date_debut->format('d/m/Y à H:i') }}
                                            </strong>
                                        </div>
                                    @if($reservation->status === 'Réservé' && $reservation->expires_at)
                                        <div class="mb-2">
                                            <small class="text-muted d-block mb-1">Expiration</small>
                                            @if($reservation->isExpired())
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Expirée
                                                </span>
                                            @else
                                                <span class="text-warning">
                                                    <i class="fas fa-hourglass-half me-1"></i>
                                                    {{ $reservation->expires_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                    <div>
                                        <small class="text-muted d-block mb-1">Montant total</small>
                                        <h4 class="mb-0 text-success">
                                            {{ number_format($reservation->total_amount, 0, ',', ' ') }} FCFA
                                        </h4>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="col-lg-3 text-end">
                                    @if($reservation->status === 'Réservé' && !$reservation->isExpired())
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('payments.checkout', $reservation->ticket) }}" 
                                               class="btn btn-success">
                                                <i class="fas fa-credit-card me-2"></i>
                                                Payer maintenant
                                            </a>
                                            <form action="{{ route('reservations.cancel', $reservation) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                    <i class="fas fa-times me-1"></i>
                                                    Annuler
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($reservation->status === 'Payé')
                                        <div class="d-grid gap-2">
                                            @if($reservation->payment)
                                                <a href="{{ route('payments.success', $reservation->payment) }}" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-qrcode me-2"></i>
                                                    Voir mon billet
                                                </a>
                                                <a href="{{ route('payments.success', $reservation->payment) }}?download=1" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-download me-1"></i>
                                                    Télécharger PDF
                                                </a>
                                            @endif
                                        </div>
                                    @elseif($reservation->isExpired())
                                        <div class="alert alert-warning mb-0 py-2 px-3">
                                            <small>
                                                <i class="fas fa-info-circle me-1"></i>
                                                Réservation expirée
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $reservations->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="modern-card">
            <div class="card-body-modern text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-calendar-times fa-4x text-muted opacity-50"></i>
                </div>
                <h4>Aucune réservation</h4>
                <p class="text-muted mb-4">Vous n'avez pas encore effectué de réservation d'événement.</p>
                <a href="{{ route('events.index') }}" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>
                    Découvrir les événements
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Filter functionality
document.querySelectorAll('input[name="status-filter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const filter = this.id.replace('filter-', '');
        const cards = document.querySelectorAll('[data-status]');
        
        cards.forEach(card => {
            if (filter === 'all') {
                card.style.display = '';
            } else {
                const status = card.dataset.status;
                card.style.display = status === filter ? '' : 'none';
            }
        });
    });
});
</script>
@endpush
@endsection
