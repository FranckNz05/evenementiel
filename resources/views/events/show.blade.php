@extends('layouts.app')

@section('title', $event->title)

@section('content')

<!-- Hero Section with Gradient Overlay -->
<div class="event-hero-container">
    <div class="event-hero-image">
        <img src="{{ Storage::url($event->image) }}" 
             alt="{{ $event->title }}" 
             class="img-fluid w-100"
             fetchpriority="high"
             loading="eager"
             decoding="async"
             width="1200"
             height="450">
        <div class="hero-gradient"></div>
        <div class="hero-content container">
            <div class="hero-badges">
                <span class="hero-badge badge-status">
                    <i class="fas {{ $event->status === 'Gratuit' ? 'fa-gift' : 'fa-ticket-alt' }}"></i>
                    {{ $event->status }}
                </span>
                <span class="hero-badge badge-type">
                    <i class="fas fa-chair"></i>
                    {{ $event->event_type }}
                </span>
                <span class="hero-badge badge-state badge-state-{{ strtolower($event->etat) }}">
                    <i class="fas fa-circle pulse-dot"></i>
                    {{ $event->etat }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Event Details -->
<div class="container py-5">
    <div class="row g-4">
        <!-- Main Column -->
        <div class="col-lg-8">
            <!-- Event Header -->
            <div class="event-header mb-4">
                <h1 class="event-title mb-2">{{ $event->title }}</h1>
                @php
                    $attendees = $event->attendingInfluencers()->select('users.id','users.prenom','users.nom','users.profil_image')->latest('influencer_event_attendances.created_at')->get();
                    $isAttending = auth()->check() ? auth()->user()->influencerAttendances()->where('event_id',$event->id)->exists() : false;
                @endphp
                @if($attendees->count() > 0)
                <a href="{{ route('events.influencers', $event) }}" class="d-flex align-items-center mb-4 text-decoration-none">
                    <div class="position-relative" style="height:36px;">
                        @foreach($attendees->take(7) as $idx => $u)
                            @php
                                $img = $u->profil_image;
                                $url = $img ? (str_starts_with($img,'http') ? $img : asset('storage/'.$img)) : 'https://ui-avatars.com/api/?name='.urlencode(($u->prenom.' '.$u->nom));
                            @endphp
                            <img src="{{ $url }}" 
                                 alt="{{ $u->prenom }}" 
                                 class="rounded-circle border border-white" 
                                 style="width:36px;height:36px;object-fit:cover;position:absolute;left:{{ $idx*22 }}px;"
                                 loading="lazy"
                                 decoding="async"
                                 width="36"
                                 height="36">
                        @endforeach
                    </div>
                    @if($attendees->count() > 7)
                        <span class="ms-3 text-muted">+{{ $attendees->count() - 7 }} autres</span>
                    @endif
                </a>
                @endif

                <!-- Quick Info Cards -->
                <div class="info-cards-grid mb-5">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Début</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($event->start_date)->locale('fr_FR')->isoFormat('D MMM YYYY') }}</span>
                            <span class="info-time">{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }}</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-icon">
                            <i class="far fa-calendar-check"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Fin</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($event->end_date)->locale('fr_FR')->isoFormat('D MMM YYYY') }}</span>
                            <span class="info-time">{{ \Carbon\Carbon::parse($event->end_date)->format('H:i') }}</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Lieu</span>
                            <span class="info-value">{{ $event->ville }}</span>
                            <span class="info-time">{{ $event->pays }}</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Informations de l'événement - Carte unique -->
            <div class="content-card mb-4">
                <div class="card-header-custom">
                    <h2 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informations de l'événement
                    </h2>
                </div>
                <div class="card-body-custom">
                    <!-- À propos de l'événement -->
                    <div class="event-info-section mb-4">
                        <h3 class="subsection-title">
                            <i class="fas fa-align-left"></i>
                            À propos de l'événement
                        </h3>
                        <div class="event-description">
                            {!! sanitize_html($event->description) !!}
                        </div>
                    </div>

                    <hr class="section-divider">

                    <!-- Localisation -->
                    <div class="event-info-section mb-4">
                        <h3 class="subsection-title">
                            <i class="fas fa-map-marker-alt"></i>
                            Localisation
                        </h3>
                        <div class="location-info">
                            <div class="location-item">
                                <i class="fas fa-map-pin"></i>
                                <div>
                                    <strong>{{ $event->adresse }}</strong>
                                    <p class="text-muted mb-0">{{ $event->ville }}, {{ $event->pays }}</p>
                                </div>
                            </div>
                            @if($event->adresse_map)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($event->adresse_map) }}"
                               class="btn-map" target="_blank">
                                <i class="fas fa-directions"></i>
                                <span>Itinéraire</span>
                            </a>
                            @endif
                        </div>
                    </div>

                    @if($event->organizer)
                    <hr class="section-divider">

                    <!-- Organisateur -->
                    <div class="event-info-section">
                        <h3 class="subsection-title">
                            <i class="fas fa-user-tie"></i>
                            Organisateur
                        </h3>
                        <div class="organizer-card">
                            <div class="organizer-avatar">
                                <img src="{{ $event->organizer && $event->organizer->logo ? asset('storage/' . $event->organizer->logo) : asset('images/default-profile.jpg') }}"
                                     alt="{{ $event->organizer->company_name ?? 'Organisateur' }}"
                                     loading="lazy"
                                     decoding="async"
                                     width="80"
                                     height="80">
                                @if($event->organizer->is_verified)
                                    <span class="verified-badge" title="Organisateur vérifié">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="organizer-info">
                                <h4 class="organizer-name">
                                    @if($event->organizer)
                                        <a href="{{ route('organizers.show', $event->organizer->slug) }}">
                                            {{ $event->organizer->company_name }}
                                        </a>
                                    @else
                                        <span>Organisateur non défini</span>
                                    @endif
                                </h4>
                                <div class="organizer-contacts">
                                    @if($event->organizer->phone_primary)
                                        <a href="tel:{{ $event->organizer->phone_primary }}" class="contact-item" style="color: #000000 !important; text-decoration: none !important;">
                                            <i class="fas fa-phone"></i>
                                            <span style="color: #000000 !important;">{{ $event->organizer->phone_primary }}</span>
                                        </a>
                                    @endif
                                    @if($event->organizer->email)
                                        <a href="mailto:{{ $event->organizer->email }}" class="contact-item" style="color: #000000 !important; text-decoration: none !important;">
                                            <i class="fas fa-envelope"></i>
                                            <span style="color: #000000 !important;">{{ $event->organizer->email }}</span>
                                        </a>
                                    @endif
                                    @if($event->organizer->website)
                                        <a href="{{ $event->organizer->website }}" target="_blank" class="contact-item" style="color: #000000 !important; text-decoration: none !important;">
                                            <i class="fas fa-globe"></i>
                                            <span style="color: #000000 !important;">Site web</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Sponsors Section -->
            @if($event->sponsors->count() > 0)
            <div class="content-card mb-4">
                <div class="card-header-custom">
                    <h2 class="section-title">
                        <i class="fas fa-handshake"></i>
                        Nos Sponsors
                    </h2>
                </div>
                <div class="card-body-custom">
                    <div class="sponsors-grid">
                        @foreach($event->sponsors as $sponsor)
                        <div class="sponsor-item" data-bs-toggle="tooltip" title="{{ $sponsor->name }}">
                            @if($sponsor->logo_path)
                                <img src="{{ Storage::url($sponsor->logo_path) }}" 
                                     alt="{{ $sponsor->name }}"
                                     loading="lazy"
                                     decoding="async"
                                     width="100"
                                     height="100">
                            @else
                                <div class="sponsor-placeholder">
                                    <span>{{ substr($sponsor->name, 0, 2) }}</span>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Keywords Section -->
            @if($event->keywords)
            <div class="content-card mb-4">
                <div class="card-header-custom">
                    <h2 class="section-title">
                        <i class="fas fa-tags"></i>
                        Mots-clés
                    </h2>
                </div>
                <div class="card-body-custom">
                    <div class="keywords-list">
                        @foreach(json_decode($event->keywords, true) as $keyword)
                            <span class="keyword-tag">{{ trim($keyword) }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <div class="sticky-sidebar">
                @auth
                @if(auth()->user()->is_influencer)
                <div class="content-card mb-4">
                    <div class="card-header-custom">
                        <h2 class="section-title"><i class="fas fa-star"></i> Présence influenceur</h2>
                    </div>
                    <div class="card-body-custom">
                        <form method="POST" action="{{ route('events.influencers.toggleAttend', $event) }}">
                            @csrf
                            <button type="submit" class="btn btn-{{ $isAttending ? 'success' : 'outline-primary' }} w-100" style="border-radius:12px;">
                                <i class="fas fa-calendar-check me-2"></i>
                                {{ $isAttending ? "J'y suis" : "J'y serais" }}
                            </button>
                        </form>
                    </div>
                </div>
                @endif
                @endauth

                <!-- Share Section -->
                <div class="content-card mb-4">
                    <div class="card-header-custom">
                        <h2 class="section-title">
                            <i class="fas fa-share-alt"></i>
                            Partager l'événement
                        </h2>
                    </div>
                    <div class="card-body-custom">
                        @php
                            $eventUrl = route('direct-events.show', $event);
                            $eventTitle = $event->title;
                            $shareText = urlencode("🎉 $eventTitle - Découvrez cet événement sur MokiliEvent !");
                            $sharesCountRaw = $event->shares()->count();
                            $sharesCount = format_number($sharesCountRaw);
                        @endphp
                        
                        <div class="share-buttons-grid mb-3">
                            <button type="button" class="btn btn-share btn-whatsapp" data-platform="whatsapp" 
                                    onclick="shareOnWhatsApp('{{ $eventUrl }}', '{{ $eventTitle }}')">
                                <i class="fab fa-whatsapp"></i>
                                <span>WhatsApp</span>
                            </button>
                            
                            <button type="button" class="btn btn-share btn-facebook" data-platform="facebook"
                                    onclick="shareOnFacebook('{{ $eventUrl }}')">
                                <i class="fab fa-facebook-f"></i>
                                <span>Facebook</span>
                            </button>
                            
                            <button type="button" class="btn btn-share btn-snapchat" data-platform="snapchat"
                                    onclick="shareOnSnapchat('{{ $eventUrl }}')">
                                <i class="fab fa-snapchat"></i>
                                <span>Snapchat</span>
                            </button>
                            
                            <button type="button" class="btn btn-share btn-telegram" data-platform="telegram"
                                    onclick="shareOnTelegram('{{ $eventUrl }}', '{{ $shareText }}')">
                                <i class="fab fa-telegram-plane"></i>
                                <span>Telegram</span>
                            </button>
                            
                            <button type="button" class="btn btn-share btn-copy" data-platform="copy_link" id="copy-link-btn"
                                    onclick="copyEventLink('{{ $eventUrl }}')">
                                <i class="fas fa-link"></i>
                                <span>Copier le lien</span>
                            </button>
                        </div>
                        
                        <div class="share-count text-center text-muted small">
                            <i class="fas fa-share me-1"></i>
                            <span id="share-count">{{ $sharesCount }}</span> partage{{ $sharesCountRaw > 1 ? 's' : '' }}
                        </div>
                    </div>
                </div>

                <!-- Tickets Section - Only show if event is not free -->
                @if($event->status !== 'Gratuit')
                @php
                    $allTicketsSoldOut = $event->tickets->every(function($ticket) {
                        return $ticket->quantite_vendue >= $ticket->quantite;
                    });
                    $isPast = $event->end_date && \Carbon\Carbon::parse($event->end_date)->isPast();
                    $eventStartDate = $event->start_date ? \Carbon\Carbon::parse($event->start_date) : null;
                    $sevenDaysBefore = $eventStartDate ? $eventStartDate->copy()->subDays(7) : null;
                    $canReserve = $eventStartDate && \Carbon\Carbon::now()->isBefore($sevenDaysBefore);
                @endphp
                <div class="tickets-card mb-4 {{ $allTicketsSoldOut ? 'tickets-sold-out-section' : '' }} {{ $isPast ? 'tickets-disabled' : '' }}">
                    @if($allTicketsSoldOut)
                    <div class="sold-out-stamp-overlay">
                        <div class="sold-out-stamp">
                            <span>SOLD OUT</span>
                        </div>
                    </div>
                    @endif
                    <div class="tickets-header {{ $allTicketsSoldOut ? 'sold-out-header' : '' }}">
                        <h3>
                            <i class="fas fa-ticket-alt" style="color: white !important;"></i>
                            Billets
                        </h3>
                    </div>

                    @if($isPast)
                    <div class="alert alert-secondary mb-0 rounded-0" role="alert" style="border-left: 4px solid var(--bleu-nuit);">
                        <i class="fas fa-history me-2"></i>
                        Cet événement est terminé. L'achat et la réservation de billets ne sont plus possibles.
                    </div>
                    @elseif(!$canReserve && $eventStartDate)
                    <div class="alert alert-warning mb-0 rounded-0" role="alert" style="border-left: 4px solid var(--bleu-nuit);">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Information importante :</strong> Les réservations ne sont plus possibles. Il reste moins de 7 jours avant le début de l'événement ({{ $eventStartDate->format('d/m/Y H:i') }}). Vous pouvez toujours acheter vos billets directement.
                    </div>
                    @elseif($canReserve)
                    <div class="alert alert-info mb-0 rounded-0" role="alert" style="border-left: 4px solid var(--bleu-nuit);">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Réservation :</strong> Vous pouvez réserver vos billets jusqu'à 7 jours avant le début de l'événement. Les réservations non payées seront automatiquement annulées 7 jours avant l'événement.
                    </div>
                    @endif

                    <form action="{{ route('orders.store') }}" method="POST" id="tickets-form">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        
                        <div class="tickets-list">
                            @foreach($event->tickets as $index => $ticket)
                                @php
                                    $remainingTickets = $ticket->quantite - $ticket->quantite_vendue;
                                    $isCompletelySoldOut = $ticket->quantite_vendue >= $ticket->quantite;
                                    $isPromo = $ticket->montant_promotionnel && now()->between($ticket->promotion_start, $ticket->promotion_end);
                                    $price = $isPromo ? $ticket->montant_promotionnel : $ticket->prix;
                                @endphp
                                <div class="ticket-item @if($isCompletelySoldOut) ticket-sold-out @endif">
                                    @if($isCompletelySoldOut && !$allTicketsSoldOut)
                                    <div class="ticket-unavailable-overlay">
                                        <div class="unavailable-stamp">
                                            <i class="fas fa-ban"></i>
                                            <span>INDISPONIBLE</span>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="ticket-header">
                                        <h4>{{ $ticket->nom }}</h4>
                                        @if($isPromo)
                                            <span class="promo-badge">
                                                <i class="fas fa-fire"></i>
                                                Promo
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <p class="ticket-description">{{ $ticket->description }}</p>

                                    <div class="ticket-price">
                                        @if($isPromo)
                                            <span class="price-old">{{ number_format($ticket->prix, 0, ',', ' ') }} FCFA</span>
                                            <span class="price-current promo">{{ number_format($price, 0, ',', ' ') }} FCFA</span>
                                        @else
                                            <span class="price-current">{{ number_format($price, 0, ',', ' ') }} FCFA</span>
                                        @endif
                                    </div>

                                    <div class="ticket-availability">
                                        <div class="availability-bar">
                                            <div class="availability-fill" style="width: {{ ($remainingTickets / $ticket->quantite) * 100 }}%"></div>
                                        </div>
                                        <span class="availability-text">
                                            <i class="fas fa-users"></i>
                                            {{ $remainingTickets }} places restantes
                                        </span>
                                    </div>

                                    @if($isPromo)
                                        <div class="promo-info">
                                            <i class="far fa-clock"></i>
                                            Jusqu'au {{ \Carbon\Carbon::parse($ticket->promotion_end)->format('d/m/Y') }}
                                        </div>
                                    @endif

                                    <div class="ticket-quantity">
                                        <input type="hidden" name="tickets[{{ $index }}][id]" value="{{ $ticket->id }}">
                                        <button type="button" class="qty-btn qty-minus" {{ ($remainingTickets <= 0 || $isPast) ? 'disabled' : '' }}>
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number"
                                            name="tickets[{{ $index }}][quantity]"
                                            class="quantity-input"
                                            min="0"
                                            max="{{ $remainingTickets }}"
                                            value="0"
                                            data-price="{{ $price }}"
                                            data-ticket-id="{{ $ticket->id }}"
                                            {{ ($remainingTickets <= 0 || $isPast) ? 'disabled' : '' }}>
                                        <button type="button" class="qty-btn qty-plus" {{ ($remainingTickets <= 0 || $isPast) ? 'disabled' : '' }}>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="tickets-total">
                            <span>Total</span>
                            <span class="total-amount" id="total-amount">0 FCFA</span>
                        </div>

                        <div class="tickets-actions">
                            @auth
                                @if(Auth::user()->hasVerifiedEmail())
                                    <button type="submit" class="btn-primary-custom" id="purchase-button" {{ $isPast ? 'disabled' : 'disabled' }}>
                                        <i class="fas fa-shopping-cart"></i>
                                        <span>Acheter maintenant</span>
                                    </button>

                                    <a href="{{ route('orders.index') }}" class="btn-secondary-custom">
                                        <i class="fas fa-history"></i>
                                        <span>Mes réservations</span>
                                    </a>

                                    @if($event->tickets->contains('reservable', true))
                                        <button type="button" class="btn-outline-custom" id="reserve-button" {{ ($isPast || !$canReserve) ? 'disabled' : 'disabled' }} title="{{ !$canReserve && $eventStartDate ? 'Les réservations ne sont plus possibles. Il reste moins de 7 jours avant le début de l\'événement.' : '' }}">
                                            <i class="fas fa-bookmark"></i>
                                            <span>Réserver</span>
                                        </button>
                                    @endif
                                @else
                                    <div class="alert alert-warning mb-3" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Email non vérifié :</strong> Veuillez vérifier votre email pour acheter des billets.
                                        <a href="{{ route('verification.notice') }}" class="alert-link">Vérifier maintenant</a>
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn-primary-custom">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Connectez-vous pour acheter</span>
                                </a>
                            @endauth
                        </div>
                    </form>
                </div>
                @endif

                <!-- Important Info -->
                <div class="info-box">
                    <h4>
                        <i class="fas fa-info-circle"></i>
                        Informations
                    </h4>
                    <ul class="info-list">
                        <li>
                            <i class="fas fa-ban"></i>
                            <span>Les billets ne sont pas remboursables</span>
                        </li>
                        <li>
                            <i class="fas fa-qrcode"></i>
                            <span>QR code envoyé par email</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Arrivée 30 min avant le début</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-section mt-5">
        <h2 class="section-title-large mb-4">
            <i class="fas fa-comments"></i>
            Commentaires ({{ $event->comments_count ?? $event->comments()->count() }})
        </h2>

        <!-- Comment Form -->
        @auth
        <div class="content-card mb-4">
            <div class="card-body-custom">
                <form id="comment-form" method="POST" action="{{ route('events.comments.store', $event) }}">
                    @csrf
                    <div class="d-flex gap-3">
                        <img src="{{ auth()->user()->getProfilePhotoUrlAttribute() }}" 
                             alt="{{ auth()->user()->nom }}" 
                             class="rounded-circle"
                             style="width: 40px; height: 40px; object-fit: cover;"
                             loading="lazy"
                             decoding="async"
                             width="40"
                             height="40">
                        <div class="flex-grow-1">
                            <textarea name="content" 
                                      id="comment-content" 
                                      class="form-control"
                                      rows="3"
                                      placeholder="Écrivez un commentaire..."
                                      required></textarea>
                            <button type="submit" class="btn btn-primary mt-2" style="background-color: var(--bleu-nuit); border: none;">
                                <i class="fas fa-paper-plane me-2" style="color: white;"></i>Publier
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="alert alert-info">
            <a href="{{ route('login') }}" class="alert-link">Connectez-vous</a> pour laisser un commentaire.
        </div>
        @endauth

        <!-- Comments List -->
        <div class="content-card">
            <div class="card-body-custom">
                <div id="comments-list">
                    @forelse($comments as $comment)
                    <div class="comment-item {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}" data-comment-id="{{ $comment->id }}">
                        <div class="d-flex gap-3">
                            <img src="{{ $comment->user->getProfilePhotoUrlAttribute() }}" 
                                 alt="{{ $comment->user->nom }}" 
                                 class="rounded-circle"
                                 style="width: 40px; height: 40px; object-fit: cover;"
                                 loading="lazy"
                                 decoding="async"
                                 width="40"
                                 height="40">
                                 style="width: 35px; height: 35px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <strong class="small">{{ $comment->user->nom }}</strong>
                                        @php
                                            $badgeType = 'client';
                                            if ($comment->user->isAdmin()) {
                                                $badgeType = 'admin';
                                            } elseif ($comment->user->isOrganizer()) {
                                                $badgeType = 'organizer';
                                            } elseif ($comment->user->isInfluencer()) {
                                                $badgeType = 'influencer';
                                            }
                                        @endphp
                                        @if($badgeType !== 'client')
                                        <span class="badge badge-{{ $badgeType }} ms-2">
                                            @if($badgeType === 'admin')
                                                <i class="fas fa-crown"></i> Admin
                                            @elseif($badgeType === 'organizer')
                                                <i class="fas fa-certificate"></i> Organisateur
                                            @elseif($badgeType === 'influencer')
                                                <i class="fas fa-star"></i> Influenceur
                                            @endif
                                        </span>
                                        @endif
                                        <span class="text-muted small ms-2">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    @auth
                                    @if($comment->user_id === auth()->id())
                                    <button class="btn btn-sm btn-link p-0 delete-comment-btn" style="color: var(--bleu-nuit);" 
                                            data-comment-id="{{ $comment->id }}">
                                        <i class="fas fa-trash fa-xs"></i>
                                    </button>
                                    @endif
                                    @endauth
                                </div>
                                <p class="mb-1 small">{{ $comment->content }}</p>
                                
                                <!-- Reply Toggle Button -->
                                @if($comment->replies->count() > 0)
                                <button class="btn btn-sm btn-link p-0 toggle-replies-btn" 
                                        data-comment-id="{{ $comment->id }}">
                                    <i class="fas fa-comments me-1"></i>
                                    <span class="replies-count">{{ $comment->replies->count() }}</span> réponse{{ $comment->replies->count() > 1 ? 's' : '' }}
                                </button>
                                @endif
                                
                                <!-- Reply Button (for users to reply) -->
                                @auth
                                <button class="btn btn-sm btn-link p-0 toggle-reply-form-btn" 
                                        data-comment-id="{{ $comment->id }}">
                                    <i class="fas fa-reply me-1"></i>
                                    Répondre
                                </button>
                                @endauth
                            </div>
                        </div>

                        <!-- Reply Form (Hidden by default) -->
                        <div class="reply-form-container" id="reply-form-{{ $comment->id }}" style="display: none;">
                            <div class="ms-5 mt-2">
                                @auth
                                <form class="reply-form mb-2" data-parent-id="{{ $comment->id }}" 
                                      action="{{ route('events.comments.store', $event) }}">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="d-flex gap-2">
                                        <img src="{{ auth()->user()->getProfilePhotoUrlAttribute() }}" 
                                             alt="{{ auth()->user()->nom }}" 
                                             class="rounded-circle"
                                             style="width: 28px; height: 28px; object-fit: cover;">
                                        <input type="text" 
                                               name="content" 
                                               class="form-control form-control-sm" 
                                               placeholder="Répondre..." 
                                               required>
                                        <button type="submit" class="btn btn-sm btn-primary" style="background-color: var(--bleu-nuit); border: none;">
                                            <i class="fas fa-paper-plane" style="color: white;"></i>
                                        </button>
                                    </div>
                                </form>
                                @endauth
                            </div>
                        </div>

                        <!-- Replies (Hidden by default) -->
                        <div class="replies-container" id="replies-{{ $comment->id }}" style="display: none;">
                            <div class="ms-5 mt-2">
                                <!-- Replies List -->
                                <div class="replies-list">
                                    @foreach($comment->replies as $reply)
                                    <div class="reply-item mb-2" data-comment-id="{{ $reply->id }}">
                                        <div class="d-flex gap-2">
                                            <img src="{{ $reply->user->getProfilePhotoUrlAttribute() }}" 
                                                 alt="{{ $reply->user->nom }}" 
                                                 class="rounded-circle"
                                                 style="width: 28px; height: 28px; object-fit: cover;"
                                                 loading="lazy"
                                                 decoding="async"
                                                 width="28"
                                                 height="28">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong class="small">{{ $reply->user->nom }}</strong>
                                                        @php
                                                            $badgeTypeReply = 'client';
                                                            if ($reply->user->isAdmin()) {
                                                                $badgeTypeReply = 'admin';
                                                            } elseif ($reply->user->isOrganizer()) {
                                                                $badgeTypeReply = 'organizer';
                                                            } elseif ($reply->user->isInfluencer()) {
                                                                $badgeTypeReply = 'influencer';
                                                            }
                                                        @endphp
                                                        @if($badgeTypeReply !== 'client')
                                                        <span class="badge badge-{{ $badgeTypeReply }} ms-2">
                                                            @if($badgeTypeReply === 'admin')
                                                                <i class="fas fa-crown"></i> Admin
                                                            @elseif($badgeTypeReply === 'organizer')
                                                                <i class="fas fa-certificate"></i> Organisateur
                                                            @elseif($badgeTypeReply === 'influencer')
                                                                <i class="fas fa-star"></i> Influenceur
                                                            @endif
                                                        </span>
                                                        @endif
                                                        <span class="text-muted small ms-2">
                                                            {{ $reply->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    @auth
                                                    @if($reply->user_id === auth()->id())
                                                    <button class="btn btn-sm btn-link p-0 delete-comment-btn" style="color: var(--bleu-nuit);" 
                                                            data-comment-id="{{ $reply->id }}">
                                                        <i class="fas fa-trash fa-xs"></i>
                                                    </button>
                                                    @endif
                                                    @endauth
                                                </div>
                                                <p class="mb-0 small">{{ $reply->content }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comment-slash fa-2x mb-2"></i>
                        <p class="mb-0">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Load More Button -->
        @if($event->comments_count > 7)
        <div class="text-center mt-4">
            <button class="btn btn-outline-primary" id="load-more-comments">
                <i class="fas fa-chevron-down me-2"></i>
                Voir plus de commentaires
            </button>
        </div>
        @endif
    </div>

    <!-- Similar Events -->
    @if($similarEvents->count() > 0)
    <div class="similar-events-section mt-5">
        <h2 class="section-title-large mb-4">
            <i class="fas fa-calendar-week"></i>
            Événements similaires
        </h2>
        <div class="row g-4">
            @foreach($similarEvents as $relatedEvent)
            <div class="col-12 col-md-6 col-lg-4">
                <x-event-card :event="$relatedEvent" link-route="direct-events.show" />
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffffff;
    --blanc-or-fonce: #f8fafc;
    --text-primary: #1a202c;
    --text-secondary: #718096;
    --bg-light: #f7fafc;
    --border-color: #e2e8f0;
    --shadow-sm: 0 2px 8px rgba(15, 26, 61, 0.08);
    --shadow-md: 0 4px 16px rgba(15, 26, 61, 0.12);
    --shadow-lg: 0 8px 24px rgba(15, 26, 61, 0.16);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Toutes les icônes en bleu */
i.fas, i.far, i.fab, i.fal {
    color: var(--bleu-nuit) !important;
}

/* Exception pour les icônes dans les sections avec fond bleu */
.card-header-custom i, .hero-badge i, .btn-copy i, .btn-share i {
    color: white !important;
}

/* Retirer tous les soulignements au survol */
a:hover, button:hover, .btn:hover {
    text-decoration: none !important;
    border-bottom: none !important;
}

/* Hero Section */
.event-hero-container {
    position: relative;
    width: 100%;
    height: 450px;
    overflow: hidden;
    margin-bottom: 2rem;
}

.event-hero-image {
    position: relative;
    width: 100%;
    height: 100%;
}

.event-hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-gradient {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60%;
    background: linear-gradient(to top, rgba(15, 26, 61, 0.6), transparent);
}

.hero-content {
    position: absolute;
    bottom: 2rem;
    left: 0;
    right: 0;
}

.hero-badges {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.875rem;
    backdrop-filter: blur(10px);
    box-shadow: var(--shadow-md);
}

.badge-status {
    color: var(--bleu-nuit);
}

.badge-type {
    color: var(--bleu-nuit-clair);
}

.badge-state {
    position: relative;
}

.badge-state-en.cours {
    color: #10b981;
}

.badge-state-archivé {
    color: #6b7280;
}

.pulse-dot {
    font-size: 0.5rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Event Header */
.event-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--bleu-nuit);
    line-height: 1.2;
    margin-bottom: 2rem;
}

/* Info Cards Grid */
.info-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-card {
    display: flex;
    gap: 1rem;
    padding: 1.25rem;
    background: white;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--blanc-or);
}

.info-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-fonce));
    border-radius: 12px;
    color: var(--bleu-nuit);
    font-size: 1.25rem;
    flex-shrink: 0;
}

.info-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
}

.info-time {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

/* Content Cards */
.content-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.content-card:hover {
    box-shadow: var(--shadow-md);
}

.card-header-custom {
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--bleu-nuit), var(--bleu-nuit-clair));
    border-bottom: 3px solid var(--blanc-or);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
    margin: 0;
}

.section-title i {
    color: var(--blanc-or);
}

.card-body-custom {
    padding: 1.5rem;
}

/* Event Info Sections */
.event-info-section {
    margin-bottom: 2rem;
}

.event-info-section:last-child {
    margin-bottom: 0;
}

.subsection-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 1.25rem;
}

.subsection-title i {
    color: var(--blanc-or);
    font-size: 1rem;
}

.section-divider {
    border: none;
    border-top: 2px solid var(--border-color);
    margin: 2rem 0;
    opacity: 0.5;
}

/* Event Description */
.event-description {
    font-size: 1.05rem;
    line-height: 1.8;
    color: var(--text-primary);
}

.event-description img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 1.5rem 0;
}

/* Location Info */
.location-info {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.location-item {
    display: flex;
    gap: 1rem;
    align-items: start;
}

.location-item i {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-light);
    border-radius: 10px;
    color: var(--blanc-or);
    flex-shrink: 0;
    font-size: 1.25rem;
}

.btn-map {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--bleu-nuit);
    color: white !important;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.btn-map i {
    color: white !important;
}

.btn-map span {
    color: white !important;
}

.btn-map:hover {
    background: var(--bleu-nuit-clair);
    transform: translateX(5px);
    color: white !important;
}

.btn-map:hover i,
.btn-map:hover span {
    color: white !important;
}

/* Organizer Card */
.organizer-card {
    display: flex;
    gap: 1.5rem;
    align-items: start;
}

.organizer-avatar {
    position: relative;
    flex-shrink: 0;
}

.organizer-avatar img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--blanc-or);
}

.verified-badge {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 32px;
    height: 32px;
    background: #ef4444;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 3px solid white;
}

.organizer-name {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.organizer-name a {
    color: var(--bleu-nuit);
    text-decoration: none;
    transition: var(--transition);
}

.organizer-name a:hover {
    color: var(--blanc-or);
}

.organizer-contacts {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #000000 !important;
    text-decoration: none !important;
    transition: var(--transition);
    padding: 0.5rem;
    border-radius: 8px;
    background: transparent;
}

.contact-item:hover {
    background: var(--bg-light);
    color: #000000 !important;
    transform: translateX(5px);
}

.contact-item:visited {
    color: #000000 !important;
}

.contact-item:active {
    color: #000000 !important;
}

.contact-item span {
    color: #000000 !important;
    font-weight: 500;
}

.contact-item:hover span {
    color: #000000 !important;
}

.contact-item:visited span {
    color: #000000 !important;
}

.contact-item:active span {
    color: #000000 !important;
}

.contact-item i {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-light);
    border-radius: 8px;
    color: var(--blanc-or);
    flex-shrink: 0;
}

/* Sponsors Grid */
.sponsors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 1.5rem;
    justify-items: center;
}

.sponsor-item {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    background: white;
    border: 2px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    transition: var(--transition);
    cursor: pointer;
}

.sponsor-item:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: var(--shadow-lg);
    border-color: var(--blanc-or);
}

.sponsor-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.sponsor-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--bleu-nuit), var(--bleu-nuit-clair));
    font-weight: 700;
    color: white;
    font-size: 1.5rem;
    border-radius: 50%;
}

/* Keywords */
.keywords-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.keyword-tag {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: var(--bg-light);
    color: var(--bleu-nuit);
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.keyword-tag:hover {
    background: var(--bleu-nuit);
    color: white;
    border-color: var(--bleu-nuit);
    transform: translateY(-2px);
}

/* Sticky Sidebar */
.sticky-sidebar {
    position: sticky;
    top: 20px;
}

/* Tickets Card */
.tickets-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 2px solid var(--blanc-or);
    box-shadow: var(--shadow-lg);
}

.tickets-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--bleu-nuit), var(--bleu-nuit-clair));
    text-align: center;
}

.tickets-header h3 {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.tickets-header i {
    color: white !important;
}

/* Sold Out Section */
.tickets-sold-out-section {
    position: relative;
    overflow: visible;
}

.sold-out-header {
    opacity: 0.5;
}

.sold-out-stamp-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 16px;
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}

.sold-out-stamp {
    background: var(--blanc-or);
    color: #000;
    padding: 1rem 2.5rem;
    border-radius: 12px;
    font-weight: 900;
    font-size: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    border: 4px solid #000;
    transform: rotate(-15deg);
    text-transform: uppercase;
    letter-spacing: 3px;
}

.sold-out-stamp span {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

/* Tickets List */
.tickets-list {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-height: 500px;
    overflow-y: auto;
}

.tickets-list::-webkit-scrollbar {
    width: 6px;
}

.tickets-list::-webkit-scrollbar-track {
    background: var(--bg-light);
}

.tickets-list::-webkit-scrollbar-thumb {
    background: var(--blanc-or);
    border-radius: 3px;
}

.ticket-item {
    padding: 1.25rem;
    background: var(--bg-light);
    border-radius: 12px;
    border: 2px solid transparent;
    transition: var(--transition);
    position: relative;
}

.ticket-item:hover:not(.ticket-sold-out) {
    border-color: var(--blanc-or);
    background: white;
    box-shadow: var(--shadow-sm);
}

.ticket-sold-out {
    position: relative;
    pointer-events: none;
}

.ticket-unavailable-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 12px;
    z-index: 5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.unavailable-stamp {
    background: var(--blanc-or);
    color: #000;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: 900;
    font-size: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    border: 3px solid #000;
    transform: rotate(-15deg);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.unavailable-stamp i {
    font-size: 2rem;
}

.unavailable-stamp span {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 0.75rem;
    gap: 0.5rem;
}

.ticket-header h4 {
    font-size: 1.125rem;
    font-weight: 800;
    color: #111111;
    margin: 0;
}

.ticket-badges {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
}

.unavailable-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.375rem 0.75rem;
    background: var(--blanc-or);
    color: #000;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.4);
    border: 2px solid #000;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.promo-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    animation: pulse-promo 2s infinite;
}

@keyframes pulse-promo {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.ticket-description {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.ticket-price {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.price-old {
    font-size: 0.875rem;
    color: var(--text-secondary);
    text-decoration: line-through;
}

.price-current {
    font-size: 1.25rem;
    font-weight: 800;
    color: #111111;
}

.price-current.promo {
    color: #111111;
}

.ticket-availability {
    margin-bottom: 1rem;
}

.availability-bar {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.availability-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--blanc-or), var(--blanc-or-fonce));
    border-radius: 3px;
    transition: width 0.5s ease;
}

.availability-text {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.promo-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: #fef2f2;
    border-radius: 8px;
    font-size: 0.75rem;
    color: #ef4444;
    margin-bottom: 1rem;
}

.ticket-quantity {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.qty-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: var(--bleu-nuit);
    color: white !important;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.qty-btn i {
    color: white !important;
}

.qty-btn:hover:not(:disabled) {
    background: var(--bleu-nuit-clair);
    color: white !important;
    transform: scale(1.1);
}

.qty-btn:hover:not(:disabled) i {
    color: white !important;
}

.qty-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.quantity-input {
    width: 60px;
    height: 36px;
    text-align: center;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-weight: 700;
    color: var(--bleu-nuit);
    font-size: 1rem;
}

.quantity-input:focus {
    outline: none;
    border-color: var(--blanc-or);
}

/* Tickets Total */
.tickets-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(153, 27, 27, 0.05), rgba(255, 215, 0, 0.05));
    border-top: 2px solid var(--border-color);
    border-bottom: 2px solid var(--border-color);
}

.tickets-total span:first-child {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
}

.total-amount {
    font-size: 1.75rem;
    font-weight: 900;
    color: #111111;
}

/* Tickets Actions */
.tickets-actions {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.btn-primary-custom {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-fonce));
    color: var(--bleu-nuit);
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.125rem;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.2);
    text-decoration: none !important;
}

.btn-primary-custom:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.2);
    text-decoration: none !important;
}

.btn-primary-custom:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.btn-secondary-custom {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    background: white;
    color: var(--bleu-nuit);
    border: 2px solid var(--bleu-nuit);
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
}

.btn-secondary-custom:hover {
    background: var(--bleu-nuit);
    color: white;
}

.btn-outline-custom {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    background: transparent;
    color: var(--bleu-nuit);
    border: 2px solid var(--blanc-or);
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.btn-outline-custom:hover:not(:disabled) {
    background: var(--blanc-or);
    color: var(--bleu-nuit);
}

/* Icônes en rouge dans la section billets (hors en-tête) */
.tickets-list i,
.ticket-price i,
.availability-text i {
    color: var(--bleu-nuit);
}

.btn-outline-custom:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Info Box */
.info-box {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
}

.info-box h4 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 1rem;
}

.info-box h4 i {
    color: var(--blanc-or);
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-list li {
    display: flex;
    align-items: start;
    gap: 0.75rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.info-list li i {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-light);
    border-radius: 6px;
    color: var(--blanc-or);
    flex-shrink: 0;
    margin-top: 2px;
}

/* Similar Events */
.similar-events-section {
    margin-top: 4rem;
}

.section-title-large {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 2rem;
    font-weight: 800;
    color: var(--bleu-nuit);
    margin-bottom: 2rem;
}

.section-title-large i {
    color: var(--blanc-or);
}

.similar-events-swiper {
    padding: 1rem 0 3rem;
}

.similar-events-swiper .swiper-wrapper {
    display: flex;
    align-items: stretch;
}

.similar-events-swiper .swiper-slide {
    display: flex;
    height: auto;
}

.similar-events-swiper .swiper-slide .event-card-mini {
    width: 100%;
}

.event-card-mini {
    display: block;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 2px solid var(--border-color);
    text-decoration: none;
    transition: var(--transition);
    position: relative;
}

.event-card-mini:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
    border-color: var(--blanc-or);
}

.sold-out-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.sold-out-badge {
    padding: 0.75rem 1.5rem;
    background: #ef4444;
    color: white;
    font-weight: 800;
    font-size: 1.25rem;
    border-radius: 8px;
    transform: rotate(-15deg);
    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
}

.event-image-mini {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.event-image-mini img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.event-card-mini:hover .event-image-mini img {
    transform: scale(1.1);
}

.event-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--bleu-nuit), var(--bleu-nuit-clair));
    color: white;
    font-size: 3rem;
}

.event-badges-mini {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.badge-mini {
    padding: 0.375rem 0.75rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    backdrop-filter: blur(10px);
}

.badge-state-en.cours {
    color: #10b981;
}

.badge-state-archivé {
    color: #6b7280;
}

.event-content-mini {
    padding: 1.25rem;
}

.event-content-mini h5 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 1rem;
    line-height: 1.3;
}

.event-meta-mini {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.event-meta-mini i {
    color: var(--blanc-or);
}

.event-price-mini {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.price-free {
    display: inline-block;
    padding: 0.375rem 0.875rem;
    background: #10b981;
    color: white;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 700;
}

.price-paid {
    display: inline-block;
    padding: 0.375rem 0.875rem;
    background: var(--blanc-or);
    color: var(--bleu-nuit);
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 700;
}

/* Swiper Navigation */
.swiper-button-next,
.swiper-button-prev {
    width: 44px;
    height: 44px;
    background: white;
    border-radius: 50%;
    border: 2px solid var(--blanc-or);
    box-shadow: var(--shadow-md);
    transition: var(--transition);
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
    background: var(--blanc-or);
    transform: scale(1.1);
}

.swiper-button-next::after,
.swiper-button-prev::after {
    font-size: 1.25rem;
    font-weight: 900;
    color: var(--bleu-nuit);
}

/* Responsive Styles */
@media (max-width: 992px) {
    .event-title {
        font-size: 2rem;
    }

    .sticky-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .event-hero-container {
        height: 300px;
    }

    .event-title {
        font-size: 1.75rem;
    }

    .info-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .organizer-card {
        flex-direction: column;
        text-align: center;
    }

    .organizer-avatar {
        margin: 0 auto;
    }

    .sponsors-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    }

    .sponsor-item {
        width: 80px;
        height: 80px;
    }

    .swiper-button-next,
    .swiper-button-prev {
        display: none;
    }
    
    /* Titres en blanc sur mobile */
    .section-title {
        color: white !important;
    }
    
    .section-title i {
        color: white !important;
    }
    
    .section-title-large {
        color: white !important;
    }
    
    .section-title-large i {
        color: white !important;
    }
}

@media (max-width: 576px) {
    .hero-badges {
        gap: 0.5rem;
    }

    .hero-badge {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    .info-cards-grid {
        grid-template-columns: 1fr;
    }

    .event-title {
        font-size: 1.5rem;
    }

    .section-title-large {
        font-size: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Ticket Management
    const form = document.getElementById('tickets-form');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const totalAmount = document.getElementById('total-amount');
    const purchaseButton = document.getElementById('purchase-button');
    const reserveButton = document.getElementById('reserve-button');

    // Quantity buttons functionality
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const currentValue = parseInt(input.value) || 0;
            const max = parseInt(input.max);

            if (this.classList.contains('qty-plus') && currentValue < max) {
                input.value = currentValue + 1;
            } else if (this.classList.contains('qty-minus') && currentValue > 0) {
                input.value = currentValue - 1;
            }

            input.dispatchEvent(new Event('change'));
        });
    });

    function updateTotal() {
        let total = 0;
        let hasTickets = false;

        quantityInputs.forEach(input => {
            const quantity = parseInt(input.value) || 0;
            const price = parseFloat(input.dataset.price);
            total += quantity * price;
            if (quantity > 0) hasTickets = true;
        });

        if (totalAmount) {
            totalAmount.textContent = total.toLocaleString('fr-FR') + ' FCFA';
        }
        
        if (purchaseButton) {
            purchaseButton.disabled = !hasTickets;
        }
        
        if (reserveButton) {
            reserveButton.disabled = !hasTickets;
        }

        // Animation sur le total
        if (hasTickets && totalAmount) {
            totalAmount.style.transform = 'scale(1.1)';
            setTimeout(() => {
                totalAmount.style.transform = 'scale(1)';
            }, 200);
        }
    }

    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const max = parseInt(this.max);
            const value = parseInt(this.value) || 0;

            if (value < 0) {
                this.value = 0;
            } else if (value > max) {
                this.value = max;
            }

            updateTotal();
        });
    });

    // Réservation
    if (reserveButton) {
        reserveButton.addEventListener('click', function() {
            const selectedTickets = [];
            quantityInputs.forEach(input => {
                if (parseInt(input.value) > 0) {
                    selectedTickets.push({
                        id: input.dataset.ticketId,
                        quantity: parseInt(input.value)
                    });
                }
            });

            if (selectedTickets.length === 0) {
                alert('Veuillez sélectionner au moins un billet à réserver.');
                return;
            }

            const reserveForm = document.createElement('form');
            reserveForm.method = 'POST';
            reserveForm.action = '{{ route("reservations.store") }}';
            reserveForm.innerHTML = `
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <div class="tickets-container"></div>
            `;

            const ticketsContainer = reserveForm.querySelector('.tickets-container');
            selectedTickets.forEach((ticket, index) => {
                ticketsContainer.innerHTML += `
                    <input type="hidden" name="tickets[${index}][id]" value="${ticket.id}">
                    <input type="hidden" name="tickets[${index}][quantity]" value="${ticket.quantity}">
                `;
            });

            document.body.appendChild(reserveForm);
            reserveForm.submit();
        });
    }

    // Mise à jour en temps réel des billets disponibles
    function updateTicketsAvailability() {
        fetch(`/api/v1/events/{{ $event->id }}/tickets`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    data.data.forEach(apiTicket => {
                        const input = document.querySelector(`input[data-ticket-id="${apiTicket.id}"]`);
                        if (input) {
                            const remainingTickets = apiTicket.quantite - apiTicket.quantite_vendue;
                            const oldMax = parseInt(input.max);
                            
                            // Mettre à jour le max
                            input.max = remainingTickets;
                            
                            // Mettre à jour l'affichage de disponibilité
                            const ticketItem = input.closest('.ticket-item');
                            if (ticketItem) {
                                const availabilityText = ticketItem.querySelector('.availability-text');
                                if (availabilityText) {
                                    availabilityText.innerHTML = `<i class="fas fa-users"></i> ${remainingTickets} places restantes`;
                                }
                                
                                const availabilityFill = ticketItem.querySelector('.availability-fill');
                                if (availabilityFill && apiTicket.quantite > 0) {
                                    availabilityFill.style.width = `${(remainingTickets / apiTicket.quantite) * 100}%`;
                                }
                                
                                // Désactiver si plus de billets disponibles
                                if (remainingTickets <= 0) {
                                    input.disabled = true;
                                    input.value = 0;
                                    ticketItem.classList.add('ticket-sold-out');
                                    const minusBtn = ticketItem.querySelector('.qty-minus');
                                    const plusBtn = ticketItem.querySelector('.qty-plus');
                                    if (minusBtn) minusBtn.disabled = true;
                                    if (plusBtn) plusBtn.disabled = true;
                                } else {
                                    input.disabled = false;
                                    ticketItem.classList.remove('ticket-sold-out');
                                    const minusBtn = ticketItem.querySelector('.qty-minus');
                                    const plusBtn = ticketItem.querySelector('.qty-plus');
                                    if (minusBtn) minusBtn.disabled = false;
                                    if (plusBtn) plusBtn.disabled = false;
                                }
                                
                                // Ajuster la valeur si elle dépasse le nouveau max
                                if (parseInt(input.value) > remainingTickets) {
                                    input.value = remainingTickets;
                                    input.dispatchEvent(new Event('change'));
                                }
                            }
                        }
                    });
                    
                    // Mettre à jour le total
                    updateTotal();
                }
            })
            .catch(error => {
                console.error('Erreur lors de la mise à jour des billets:', error);
            });
    }

    // Mettre à jour toutes les 5 secondes
    setInterval(updateTicketsAvailability, 5000);

    // Soumission du formulaire - Vérifier que le formulaire existe
    let isSubmittingOrder = false;
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

        // Empêcher les doubles soumissions
        if (isSubmittingOrder) {
            return false;
        }

        let hasTickets = false;
        let selectedTickets = [];

        quantityInputs.forEach(input => {
            const quantity = parseInt(input.value) || 0;
            const ticketId = input.dataset.ticketId;

            if (quantity > 0) {
                hasTickets = true;
                selectedTickets.push({
                    id: ticketId,
                    quantity: quantity
                });
            }
        });

        if (!hasTickets) {
            alert('Veuillez sélectionner au moins un billet.');
            return;
        }

        // Marquer comme en cours de soumission
        isSubmittingOrder = true;
        
        // Désactiver le bouton
        if (purchaseButton) {
            purchaseButton.disabled = true;
            purchaseButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Traitement en cours...</span>';
        }

        const submitForm = document.createElement('form');
        submitForm.method = 'POST';
        submitForm.action = '{{ route("orders.store") }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        submitForm.appendChild(csrfToken);

        const eventId = document.createElement('input');
        eventId.type = 'hidden';
        eventId.name = 'event_id';
        eventId.value = '{{ $event->id }}';
        submitForm.appendChild(eventId);

        selectedTickets.forEach((ticket, index) => {
            const ticketId = document.createElement('input');
            ticketId.type = 'hidden';
            ticketId.name = `tickets[${index}][id]`;
            ticketId.value = ticket.id;
            submitForm.appendChild(ticketId);

            const ticketQty = document.createElement('input');
            ticketQty.type = 'hidden';
            ticketQty.name = `tickets[${index}][quantity]`;
            ticketQty.value = ticket.quantity;
            submitForm.appendChild(ticketQty);
        });

        document.body.appendChild(submitForm);
        submitForm.submit();
        });
    }

    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Fonctions de partage
function shareOnWhatsApp(url, title) {
    const text = encodeURIComponent(`🎉 ${title} - Découvrez cet événement sur MokiliEvent !\n${url}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
    recordShare('whatsapp');
}

function shareOnFacebook(url) {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank', 'width=600,height=400');
    recordShare('facebook');
}

function shareOnSnapchat(url) {
    window.open(`https://www.snapchat.com/scan?attachmentUrl=${encodeURIComponent(url)}`, '_blank');
    recordShare('snapchat');
}

function shareOnTelegram(url, text) {
    window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${text}`, '_blank');
    recordShare('telegram');
}

function copyEventLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.getElementById('copy-link-btn');
        const originalText = btn.querySelector('span').textContent;
        btn.querySelector('span').textContent = 'Lien copié !';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-copy');
        
        setTimeout(() => {
            btn.querySelector('span').textContent = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-copy');
        }, 2000);
    }).catch(err => {
        console.error('Erreur lors de la copie:', err);
        alert('Erreur lors de la copie du lien');
    });
    recordShare('copy_link');
}

function recordShare(platform) {
    fetch('{{ route("events.share", $event) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            platform: platform
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const shareCountEl = document.getElementById('share-count');
            if (shareCountEl) {
                shareCountEl.textContent = data.total_shares;
                const parent = shareCountEl.parentElement;
                const plural = data.total_shares > 1 ? 's' : '';
                parent.innerHTML = `<i class="fas fa-share me-1"></i><span id="share-count">${data.total_shares}</span> partage${plural}`;
            }
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'enregistrement du partage:', error);
    });
}

// Gestion des commentaires
document.addEventListener('DOMContentLoaded', function() {
    // Toggle replies
    document.querySelectorAll('.toggle-replies-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const repliesContainer = document.getElementById(`replies-${commentId}`);
            
            if (repliesContainer.style.display === 'none') {
                repliesContainer.style.display = 'block';
            } else {
                repliesContainer.style.display = 'none';
            }
        });
    });

    // Toggle reply form
    document.querySelectorAll('.toggle-reply-form-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const replyFormContainer = document.getElementById(`reply-form-${commentId}`);
            
            if (replyFormContainer.style.display === 'none') {
                replyFormContainer.style.display = 'block';
            } else {
                replyFormContainer.style.display = 'none';
            }
        });
    });

    // Delete comment
    document.querySelectorAll('.delete-comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
                return;
            }
            
            const commentId = this.dataset.commentId;
            
            fetch(`/events/{{ $event->id }}/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-comment-id="${commentId}"]`).closest('.comment-item, .reply-item').remove();
                } else {
                    alert('Erreur lors de la suppression');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression');
            });
        });
    });

    // Reply form submission
    document.querySelectorAll('.reply-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const content = this.querySelector('input[name="content"]').value;
            const parentId = this.dataset.parentId;
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    content: content,
                    parent_id: parentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour afficher la nouvelle réponse
                    window.location.reload();
                } else {
                    alert('Erreur lors de l\'envoi de la réponse');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'envoi de la réponse');
            });
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.share-buttons-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.btn-share {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-share i {
    font-size: 1.1rem;
}

.btn-share:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-whatsapp {
    background-color: #25D366;
    color: white;
}

.btn-whatsapp:hover {
    background-color: #20BA5A;
    color: white;
}

.btn-facebook {
    background-color: #1877F2;
    color: white;
}

.btn-facebook:hover {
    background-color: #166FE5;
    color: white;
}

.btn-snapchat {
    background-color: #FFFC00;
    color: #000;
}

.btn-snapchat:hover {
    background-color: #FFEE00;
    color: #000;
}

.btn-telegram {
    background-color: #0088cc;
    color: white;
}

.btn-telegram:hover {
    background-color: #0077B5;
    color: white;
}

.btn-copy {
    background-color: var(--bleu-nuit);
    color: white !important;
}
.btn-copy span {
    color: white !important;
}

.btn-copy:hover {
    background-color: var(--bleu-nuit-clair);
    color: white;
}

.share-count {
    padding-top: 0.5rem;
    border-top: 1px solid #e9ecef;
    margin-top: 0.5rem;
}

/* Commentaires - Style Facebook */
.comments-section {
    margin-top: 3rem;
}

.comment-item {
    transition: all 0.3s ease;
}

.replies-container {
    border-left: 3px solid var(--bleu-nuit-clair);
    padding-left: 1rem;
    margin-top: 1rem;
}

.reply-item {
    background-color: #f8f9fa;
    padding: 0.75rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.reply-item:hover {
    background-color: #e9ecef;
}

.toggle-replies-btn {
    font-size: 0.9rem;
    color: var(--bleu-nuit);
}

.toggle-replies-btn:hover {
    color: var(--bleu-nuit-clair);
    text-decoration: none !important;
}

.reply-form input[type="text"] {
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 0.5rem 1rem;
}

.reply-form input[type="text"]:focus {
    border-color: var(--bleu-nuit);
    box-shadow: 0 0 0 0.2rem rgba(153, 27, 27, 0.25);
}

.replies-count {
    font-weight: 600;
}

.reply-item {
    transition: all 0.2s ease;
}

#comment-form textarea {
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 0.75rem;
}

#comment-form textarea:focus {
    border-color: var(--bleu-nuit);
    box-shadow: 0 0 0 0.2rem rgba(153, 27, 27, 0.25);
}

#load-more-comments {
    border-color: var(--bleu-nuit);
    color: var(--bleu-nuit);
}

#load-more-comments:hover {
    background-color: var(--bleu-nuit);
    color: white;
}

/* Badges de rôle */
.badge-admin {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    font-weight: 600;
}

.badge-organizer {
    background: linear-gradient(135deg, var(--bleu-nuit), var(--bleu-nuit-clair));
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    font-weight: 600;
}

.badge-influencer {
    background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-fonce));
    color: var(--bleu-nuit);
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    font-weight: 600;
}

.badge-admin i,
.badge-organizer i,
.badge-influencer i {
    margin-right: 0.25rem;
}
</style>
@endpush