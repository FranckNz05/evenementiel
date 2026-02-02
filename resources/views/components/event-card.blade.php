@php
    $minPrice = $event->tickets->min('prix');
    $allTickets = $event->tickets;
    $hasTickets = $allTickets->isNotEmpty();
    
    // Vérifier si TOUS les billets sont épuisés
    $allTicketsSoldOut = true;
    $hasAvailableTickets = false;
    
    if ($hasTickets) {
        foreach ($allTickets as $ticket) {
            $remaining = ($ticket->quantite ?? 0) - ($ticket->quantite_vendue ?? 0);
            if ($remaining > 0) {
                $allTicketsSoldOut = false;
                $hasAvailableTickets = true;
                break;
            }
        }
    } else {
        $allTicketsSoldOut = false; // Pas de billets = pas "complet"
    }
    
    $viewsCountRaw = $event->views_count ?? $event->views()->count();
    $viewsCount = format_number($viewsCountRaw);
@endphp

<div class="card event-card h-100 border-2 shadow-sm" style="border-color: var(--blanc-or);">
        <div class="position-relative">
            @if($event->image)
                <img src="{{ Storage::url($event->image) }}"
                     alt="{{ $event->title }}"
                     class="card-img-top"
                     style="height: 200px; object-fit: cover;">
            @else
                <div class="card-img-top d-flex align-items-center justify-content-center"
                     style="height: 200px; background-color: var(--bleu-nuit-clair);">
                    <i class="fas fa-calendar-alt fa-3x text-white"></i>
                </div>
            @endif

            <!-- Badges (État, Type) -->
            <div class="position-absolute top-0 start-0 m-2">
                @if($event->etat == 'En cours')
                    <span class="badge" style="background-color: var(--bleu-nuit); color: white;">{{ $event->etat }}</span>
                @else
                    <span class="badge bg-secondary" style="color: white;">{{ $event->etat }}</span>
                @endif

                @if($event->event_type == 'Espace libre')
                    <span class="badge mt-1" style="background-color: var(--bleu-nuit-clair); color: white;">{{ $event->event_type }}</span>
                @elseif($event->event_type == 'Plan de salle')
                    <span class="badge mt-1" style="background-color: var(--bleu-nuit); color: white;">{{ $event->event_type }}</span>
                @elseif($event->event_type == 'Mixte' || $event->event_type == 'mixte')
                    <span class="badge mt-1" style="background-color: var(--bleu-nuit); color: white;">{{ $event->event_type }}</span>
                @else
                    <span class="badge mt-1 bg-dark" style="color: white;">{{ $event->event_type }}</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title mb-3 fw-bold" style="color:#111;">
                {{ $event->title ? Str::limit($event->title, 50) : 'Événement sans titre' }}
            </h5>

            <!-- Date et Heure -->
            @if($event->start_date)
                <div class="mb-2 text-muted small">
                    <i class="far fa-calendar-alt me-2" style="color: var(--bleu-nuit);"></i>
                    {{ Carbon\Carbon::parse($event->start_date)->isoFormat('D MMMM YYYY [à] HH[h]mm') }}
                </div>
            @endif

            <!-- Lieu -->
            @if($event->ville)
                <div class="mb-3 text-muted small">
                    <i class="fas fa-map-marker-alt me-2" style="color: var(--bleu-nuit);"></i>
                    {{ $event->ville }}
                </div>
            @endif

            <!-- Prix et Billets disponibles -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @if($minPrice === null || $minPrice == 0)
                        <span class="badge" style="background-color: #28a745; color: white !important;">Gratuit</span>
                    @else
                        <span class="badge" style="background-color: var(--bleu-nuit); color: #fff;">Payant</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="text-muted small">
                        <i class="far fa-eye me-1" style="color: var(--bleu-nuit);"></i> {{ $viewsCount }}
                    </div>
                    @if($minPrice !== null && $minPrice > 0 && $hasTickets)
                        @if($allTicketsSoldOut)
                            <span class="badge" style="background-color: var(--bleu-nuit); color: white;">
                                <i class="fas fa-ban me-1"></i>Complet
                            </span>
                        @elseif($hasAvailableTickets)
                            <div class="text-muted small">
                                <i class="fas fa-ticket-alt" style="color: var(--bleu-nuit);"></i>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ route($linkRoute, $event) }}" class="stretched-link"></a>
    </div>
