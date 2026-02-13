@extends('layouts.app')

@section('content')
<!-- Hero Banner avec meilleure accessibilité -->
<section class="organizer-hero position-relative" aria-label="Bannière de l'organisateur">
    @if($organizer->banner_image)
    <div class="hero-banner" 
         style="background-image: url('{{ Storage::url($organizer->banner_image) }}');"
         role="img"
         aria-label="Image de bannière de {{ $organizer->company_name }}"></div>
    @else
    <div class="hero-banner hero-banner-default" role="img" aria-label="Bannière par défaut"></div>
    @endif
    <div class="hero-overlay" aria-hidden="true"></div>
    
    <div class="container position-relative hero-content">
        <div class="row align-items-center py-4 py-lg-5">
            <div class="col-lg-10 col-xl-8 mx-auto text-center">
                <h1 class="display-4 display-md-3 fw-bold text-white mb-3 hero-title">
                    {{ $organizer->company_name }}
                </h1>
                @if($organizer->slogan)
                <p class="lead text-white mb-0 hero-slogan">{{ $organizer->slogan }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Contenu Principal -->
<div class="container main-content">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Carte Profil Améliorée -->
            <article class="profile-card card border-0 shadow-lg">
                <div class="card-body p-4 p-lg-5">
                    <!-- En-tête du profil -->
                    <header class="profile-header text-center mb-4">
                        <!-- Logo avec meilleure présentation -->
                        <div class="logo-wrapper mx-auto mb-4">
                            @if($organizer->logo)
                            <img src="{{ Storage::url($organizer->logo) }}" 
                                 alt="Logo de {{ $organizer->company_name }}"
                                 class="profile-logo"
                                 width="150"
                                 height="150"
                                 loading="eager">
                            @else
                            <div class="profile-logo-placeholder" role="img" aria-label="Logo par défaut">
                                <span class="placeholder-text" aria-hidden="true">
                                    {{ substr($organizer->company_name, 0, 2) }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <!-- Bouton Suivre/Ne plus suivre optimisé avec AJAX -->
                        @auth
                            <div id="follow-section" class="mb-4">
                                @if(auth()->user()->isFollowing($organizer))
                                <button type="button" 
                                        class="btn btn-outline-primary btn-lg rounded-pill px-5 follow-btn organizer-follow-btn-outline"
                                        id="unfollow-btn"
                                        data-organizer-slug="{{ $organizer->slug }}" 
                                        data-organizer-name="{{ $organizer->company_name }}"
                                        aria-label="Ne plus suivre {{ $organizer->company_name }}">
                                    <i class="fas fa-check me-2" aria-hidden="true"></i>
                                    <span>Abonné</span>
                                </button>
                                @else
                                <button type="button" 
                                        class="btn btn-primary btn-lg rounded-pill px-5 follow-btn organizer-follow-btn"
                                        id="follow-btn"
                                        data-organizer-slug="{{ $organizer->slug }}" 
                                        data-organizer-name="{{ $organizer->company_name }}"
                                        aria-label="Suivre {{ $organizer->company_name }}">
                                    <i class="fas fa-user-plus me-2" aria-hidden="true"></i>
                                    <span>Suivre</span>
                                </button>
                                @endif
                            </div>
                        @else
                            <div class="mb-4">
                                <a href="{{ route('login') }}" 
                                   class="btn btn-primary btn-lg rounded-pill px-5 organizer-follow-btn"
                                   aria-label="Se connecter pour suivre">
                                    <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>
                                    <span>Connexion pour suivre</span>
                                </a>
                            </div>
                        @endauth

                        <!-- Statistiques améliorées -->
                        <div class="stats-container" role="group" aria-label="Statistiques de l'organisateur">
                            <div class="row g-0 justify-content-center">
                                <div class="col-4 col-md-auto stat-item">
                                    <div class="stat-content">
                                        <div class="stat-number">{{ number_format($organizer->followers_count) }}</div>
                                        <div class="stat-label">{{ Str::plural('Abonné', $organizer->followers_count) }}</div>
                                    </div>
                                </div>
                                <div class="col-4 col-md-auto stat-item">
                                    <div class="stat-content">
                                        <div class="stat-number">{{ number_format($events->count()) }}</div>
                                        <div class="stat-label">{{ Str::plural('Événement', $events->count()) }}</div>
                                    </div>
                                </div>
                                <div class="col-4 col-md-auto stat-item">
                                    <div class="stat-content">
                                        <div class="stat-number">{{ number_format($blogPosts->count()) }}</div>
                                        <div class="stat-label">{{ Str::plural('Article', $blogPosts->count()) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>

                    <!-- Description -->
                    @if($organizer->description)
                    <section class="description-section mb-4" aria-labelledby="about-heading">
                        <h2 id="about-heading" class="section-title h5 fw-bold mb-3">
                            <i class="fas fa-info-circle me-2 text-primary" aria-hidden="true"></i>
                            À propos
                        </h2>
                        <div class="description-content text-muted">
                            {!! nl2br(e($organizer->description)) !!}
                        </div>
                    </section>
                    @endif

                    <!-- Informations de contact optimisées -->
                    @if($organizer->email || $organizer->phone_primary || $organizer->website || $organizer->address || $organizer->city)
                    <section class="contact-section mb-4" aria-labelledby="contact-heading">
                        <h2 id="contact-heading" class="section-title h5 fw-bold mb-3">
                            <i class="fas fa-address-card me-2 text-primary" aria-hidden="true"></i>
                            Informations de contact
                        </h2>
                        <div class="contact-grid">
                            @if($organizer->email)
                            <a href="mailto:{{ $organizer->email }}" 
                               class="contact-item"
                               aria-label="Envoyer un email à {{ $organizer->email }}">
                                <i class="fas fa-envelope contact-icon" aria-hidden="true"></i>
                                <span class="contact-text">{{ $organizer->email }}</span>
                            </a>
                            @endif

                            @if($organizer->phone_primary)
                            <a href="tel:{{ $organizer->phone_primary }}" 
                               class="contact-item"
                               aria-label="Appeler {{ $organizer->phone_primary }}">
                                <i class="fas fa-phone contact-icon" aria-hidden="true"></i>
                                <span class="contact-text">{{ $organizer->phone_primary }}</span>
                            </a>
                            @endif

                            @if($organizer->website)
                            <a href="{{ $organizer->website }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="contact-item"
                               aria-label="Visiter le site web (ouvre dans un nouvel onglet)">
                                <i class="fas fa-globe contact-icon" aria-hidden="true"></i>
                                <span class="contact-text">Visiter le site web</span>
                                <i class="fas fa-external-link-alt ms-2 text-muted small" aria-hidden="true"></i>
                            </a>
                            @endif

                            @if($organizer->address || $organizer->city || $organizer->country)
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt contact-icon" aria-hidden="true"></i>
                                <span class="contact-text">
                                    @if($organizer->address){{ $organizer->address }}, @endif
                                    {{ $organizer->city }}@if($organizer->country), {{ $organizer->country }}@endif
                                </span>
                            </div>
                            @endif
                        </div>
                    </section>
                    @endif

                    <!-- Réseaux sociaux -->
                    @php
                        $socialMedia = $organizer->social_media ? json_decode($organizer->social_media, true) : [];
                    @endphp
                    @if(!empty(array_filter($socialMedia)))
                    <section class="social-section" aria-labelledby="social-heading">
                        <h2 id="social-heading" class="section-title h5 fw-bold mb-3">
                            <i class="fas fa-share-alt me-2 text-primary" aria-hidden="true"></i>
                            Suivez-nous sur les réseaux
                        </h2>
                        <div class="social-links d-flex flex-wrap justify-content-center gap-3">
                            @foreach($socialMedia as $platform => $url)
                                @if($url)
                                    @php
                                        // Remplacer X/Twitter par LinkedIn
                                        $iconClass = $platform;
                                        $platformName = ucfirst($platform);
                                        
                                        if (in_array(strtolower($platform), ['twitter', 'x', 'x-twitter'])) {
                                            $iconClass = 'linkedin';
                                            $platformName = 'LinkedIn';
                                        }
                                    @endphp
                                <a href="{{ $url }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="social-icon"
                                   aria-label="Suivre sur {{ $platformName }} (ouvre dans un nouvel onglet)"
                                   data-bs-toggle="tooltip"
                                   title="{{ $platformName }}">
                                    <i class="fab fa-{{ $iconClass }}" aria-hidden="true"></i>
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </section>
                    @endif
                </div>
            </article>

            <!-- Onglets de contenu améliorés -->
            <section class="content-tabs mt-4 mt-lg-5" aria-label="Contenu de l'organisateur">
                <ul class="nav nav-tabs custom-tabs" 
                    id="organizerTabs" 
                    role="tablist"
                    aria-label="Navigation du contenu">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" 
                                id="events-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#events" 
                                type="button" 
                                role="tab" 
                                aria-controls="events" 
                                aria-selected="true">
                            <i class="fas fa-calendar-alt me-2" aria-hidden="true"></i>
                            <span>Événements</span>
                            <span class="badge bg-primary ms-2">{{ $events->count() }}</span>
                        </button>
                    </li>
                    @if($blogPosts->count() > 0)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" 
                                id="articles-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#articles" 
                                type="button" 
                                role="tab" 
                                aria-controls="articles" 
                                aria-selected="false">
                            <i class="fas fa-newspaper me-2" aria-hidden="true"></i>
                            <span>Articles</span>
                            <span class="badge bg-secondary ms-2">{{ $blogPosts->count() }}</span>
                        </button>
                    </li>
                    @endif
                </ul>

                <div class="tab-content custom-tab-content" id="organizerTabsContent">
                    <!-- Onglet Événements -->
                    <div class="tab-pane fade show active" 
                         id="events" 
                         role="tabpanel" 
                         aria-labelledby="events-tab"
                         tabindex="0">
                        @if($events->count() > 0)
                            <div class="row g-4 mt-2">
                                @foreach($events as $event)
                                <div class="col-12 col-md-6">
                                    <article class="event-card card h-100 border-0 shadow-sm">
                                        <div class="event-image-wrapper position-relative">
                                            <img src="{{ $event->image ? Storage::url($event->image) : asset('images/default-event.jpg') }}"
                                                 class="event-image card-img-top"
                                                 alt="{{ $event->title }}"
                                                 loading="lazy"
                                                 width="400"
                                                 height="240">
                                            <div class="event-badge position-absolute top-0 end-0 m-3">
                                                @if($event->etat === 'Archivé')
                                                    <span class="badge badge-lg bg-secondary">
                                                        <i class="fas fa-archive me-1"></i>Archivé
                                                    </span>
                                                @elseif($event->start_date > now())
                                                    <span class="badge badge-lg bg-success">
                                                        <i class="fas fa-calendar-check me-1"></i>À venir
                                                    </span>
                                                @else
                                                    <span class="badge badge-lg bg-secondary">
                                                        <i class="fas fa-clock me-1"></i>Passé
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <h3 class="event-title h5 mb-3">
                                                {{ Str::limit($event->title, 60) }}
                                            </h3>
                                            <p class="event-description text-muted small mb-3 flex-grow-1">
                                                {{ Str::limit($event->description, 120) }}
                                            </p>
                                            <div class="event-meta d-flex justify-content-between align-items-center mt-auto">
                                                <div class="event-date d-flex align-items-center text-muted small">
                                                    <i class="far fa-calendar-alt me-2" aria-hidden="true"></i>
                                                    <time datetime="{{ $event->start_date->toIso8601String() }}">
                                                        {{ $event->start_date->format('d M Y') }}
                                                    </time>
                                                </div>
                                                @php
                                                    if ($event->etat === 'Archivé') {
                                                        $eventRoute = route('events.past') . '?search=' . urlencode($event->slug ?? $event->title);
                                                    } else {
                                                        $eventRoute = route('direct-events.show', $event->slug ?? $event);
                                                    }
                                                @endphp
                                                <a href="{{ $eventRoute }}" 
                                                   class="btn btn-sm btn-primary organizer-event-btn"
                                                   aria-label="Voir les détails de {{ $event->title }}">
                                                    <span>Voir plus</span>
                                                    <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state text-center py-5">
                                <div class="empty-icon mb-4" aria-hidden="true">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <h3 class="h5 mb-3">Aucun événement à venir</h3>
                                <p class="text-muted mb-0">
                                    Cet organisateur n'a pas d'événements programmés pour le moment.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Onglet Articles -->
                    @if($blogPosts->count() > 0)
                    <div class="tab-pane fade" 
                         id="articles" 
                         role="tabpanel" 
                         aria-labelledby="articles-tab"
                         tabindex="0">
                        <div class="row g-4 mt-2">
                            @foreach($blogPosts as $post)
                            <div class="col-12">
                                <article class="blog-card card border-0 shadow-sm h-100">
                                    <div class="row g-0">
                                        @if($post->image)
                                        <div class="col-md-4">
                                            <div class="blog-image-wrapper">
                                                <img src="{{ Storage::url($post->image) }}"
                                                     class="blog-image"
                                                     alt="{{ $post->title }}"
                                                     loading="lazy"
                                                     width="400"
                                                     height="300">
                                            </div>
                                        </div>
                                        @endif
                                        <div class="{{ $post->image ? 'col-md-8' : 'col-12' }}">
                                            <div class="card-body d-flex flex-column h-100">
                                                <h3 class="blog-title h5 mb-3">{{ $post->title }}</h3>
                                                <p class="blog-excerpt text-muted mb-4 flex-grow-1">
                                                    {{ Str::limit(strip_tags($post->content), 180) }}
                                                </p>
                                                <div class="blog-meta d-flex justify-content-between align-items-center mt-auto">
                                                    <div class="blog-date text-muted small">
                                                        <i class="far fa-clock me-2" aria-hidden="true"></i>
                                                        <time datetime="{{ $post->created_at->toIso8601String() }}">
                                                            {{ $post->created_at->diffForHumans() }}
                                                        </time>
                                                    </div>
                                                    <a href="{{ route('blogs.show', $post->slug) }}" 
                                                       class="btn btn-sm btn-outline-primary organizer-blog-btn"
                                                       aria-label="Lire l'article {{ $post->title }}">
                                                        <span>Lire l'article</span>
                                                        <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Variables CSS */
:root {
    --hero-height: 400px;
    --hero-height-mobile: 280px;
    --profile-logo-size: 150px;
    --profile-logo-size-mobile: 120px;
    --profile-card-offset: -100px;
    --profile-card-offset-mobile: -60px;
    --stat-border-color: #e9ecef;
    --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.12);
}

/* Hero Section */
.organizer-hero {
    position: relative;
    overflow: hidden;
    height: var(--hero-height);
    background-color: #f8f9fa;
}

.hero-banner {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: 0;
    transition: var(--transition-smooth);
}

.hero-banner-default {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.7) 100%);
    z-index: 1;
}

.hero-content {
    z-index: 2;
    height: 100%;
    display: flex;
    align-items: center;
}

.hero-title {
    text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
    animation: fadeInUp 0.6s ease;
}

.hero-slogan {
    text-shadow: 0 1px 8px rgba(0, 0, 0, 0.3);
    animation: fadeInUp 0.8s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Main Content - UI/UX optimisé */
.main-content {
    padding-top: 2rem;
    padding-bottom: 3rem;
    min-height: auto;
}

/* Assurer que le footer utilise uniquement les styles du layout - Forcer les styles identiques à l'accueil */
footer.footer-modern,
.footer-modern {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%) !important;
    background-color: transparent !important;
    color: white !important;
    padding: 3rem 0 1rem !important;
    margin-top: auto !important;
    position: relative !important;
    overflow: hidden !important;
}

/* Filigrane SVG pour le footer */
.footer-modern::before {
    content: "" !important;
    position: absolute !important;
    inset: 0 !important;
    pointer-events: none !important;
    background-image: url('{{ asset('images/4481626_2375581.svg') }}') !important;
    background-size: 100% auto !important;
    background-position: center 33% !important;
    background-repeat: no-repeat !important;
    opacity: 0.14 !important;
    mix-blend-mode: overlay !important;
    z-index: 0 !important;
}

/* Bordure dorée animée */
.footer-modern::after {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 3px !important;
    background: linear-gradient(90deg, transparent, var(--blanc-or), transparent) !important;
    animation: border-slide 3s ease-in-out infinite !important;
    z-index: 2 !important;
}

.footer-modern .container {
    position: relative !important;
    z-index: 1 !important;
}

@keyframes border-slide {
    0%, 100% { transform: translateX(-100%); }
    50% { transform: translateX(100%); }
}

/* Surcharger tous les styles .footer globaux qui pourraient affecter .footer-modern */
footer.footer-modern .footer-content,
.footer-modern .footer-content {
    display: grid !important;
    grid-template-columns: 2fr 1fr 1fr 1.5fr !important;
    gap: 2rem !important;
    margin-bottom: 2rem !important;
}

footer.footer-modern .footer-brand,
.footer-modern .footer-brand {
    display: flex !important;
    flex-direction: column !important;
    gap: 1rem !important;
}

footer.footer-modern .footer-logo,
.footer-modern .footer-logo {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: var(--blanc-or) !important;
    margin-bottom: 0.5rem !important;
}

footer.footer-modern .footer-description,
.footer-modern .footer-description {
    font-size: 0.9rem !important;
    color: rgba(255, 255, 255, 0.8) !important;
    line-height: 1.6 !important;
}

footer.footer-modern .footer-links,
.footer-modern .footer-links {
    display: flex !important;
    flex-direction: column !important;
    gap: 0.75rem !important;
    list-style: none !important;
    padding: 0 !important;
}

footer.footer-modern .footer-links h6,
.footer-modern .footer-links h6 {
    color: var(--blanc-or) !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    margin-bottom: 0.5rem !important;
}

footer.footer-modern .footer-links a,
.footer-modern .footer-links a {
    color: rgba(255, 255, 255, 0.8) !important;
    text-decoration: none !important;
    font-size: 0.9rem !important;
    transition: all 0.3s ease !important;
    display: inline-block !important;
}

footer.footer-modern .footer-links a:hover,
.footer-modern .footer-links a:hover {
    color: var(--blanc-or) !important;
    transform: translateX(5px) !important;
}

footer.footer-modern .footer-contact,
.footer-modern .footer-contact {
    display: flex !important;
    flex-direction: column !important;
    gap: 0.75rem !important;
}

footer.footer-modern .footer-contact h6,
.footer-modern .footer-contact h6 {
    color: var(--blanc-or) !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    margin-bottom: 0.5rem !important;
}

footer.footer-modern .footer-contact .contact-item,
.footer-modern .footer-contact .contact-item {
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
    color: white !important;
    font-size: 0.9rem !important;
}

footer.footer-modern .footer-contact .contact-item i,
.footer-modern .footer-contact .contact-item i {
    width: 20px !important;
    color: var(--blanc-or) !important;
    font-size: 1rem !important;
}

footer.footer-modern .footer-bottom,
.footer-modern .footer-bottom {
    padding-top: 1.5rem !important;
    border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    font-size: 0.85rem !important;
    color: rgba(255, 255, 255, 0.7) !important;
}


/* Profile Card - Design optimisé */
.profile-card {
    border-radius: 16px;
    overflow: visible;
    margin-top: var(--profile-card-offset);
    position: relative;
    z-index: 10;
    animation: slideUp 0.5s ease;
    margin-bottom: 2rem;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Logo */
.logo-wrapper {
    margin-top: calc(var(--profile-logo-size) / -2);
    position: relative;
}

.profile-logo,
.profile-logo-placeholder {
    width: var(--profile-logo-size);
    height: var(--profile-logo-size);
    border-radius: 50%;
    border: 5px solid white;
    box-shadow: var(--shadow-lg);
    object-fit: cover;
    background-color: white;
}

.profile-logo-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.placeholder-text {
    font-size: 3rem;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
}

/* Follow Button */
.follow-form {
    animation: fadeIn 0.6s ease 0.3s both;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.follow-btn {
    min-height: 48px;
    font-weight: 600;
    transition: var(--transition-smooth);
    box-shadow: var(--shadow-sm);
}

.follow-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.follow-btn:active {
    transform: translateY(0);
}

/* Correction boutons bleus avec texte bleu */
.organizer-follow-btn,
.organizer-follow-btn:hover,
.organizer-follow-btn:focus,
.organizer-follow-btn:active {
    background-color: var(--bleu-nuit, #0d6efd) !important;
    border-color: var(--bleu-nuit, #0d6efd) !important;
    color: #ffffff !important;
}

.organizer-follow-btn i,
.organizer-follow-btn span {
    color: #ffffff !important;
}

.organizer-follow-btn-outline {
    color: var(--bleu-nuit, #0d6efd) !important;
    border-color: var(--bleu-nuit, #0d6efd) !important;
    background-color: transparent !important;
}

.organizer-follow-btn-outline:hover {
    background-color: var(--bleu-nuit, #0d6efd) !important;
    border-color: var(--bleu-nuit, #0d6efd) !important;
    color: #ffffff !important;
}

.organizer-follow-btn-outline i {
    color: inherit !important;
}

.organizer-event-btn,
.organizer-event-btn:hover,
.organizer-event-btn:focus {
    background-color: var(--bleu-nuit, #0d6efd) !important;
    border-color: var(--bleu-nuit, #0d6efd) !important;
    color: #ffffff !important;
}

.organizer-event-btn i,
.organizer-event-btn span {
    color: #ffffff !important;
}

.organizer-blog-btn {
    color: var(--bleu-nuit, #0d6efd) !important;
    border-color: var(--bleu-nuit, #0d6efd) !important;
}

.organizer-blog-btn:hover {
    background-color: var(--bleu-nuit, #0d6efd) !important;
    color: #ffffff !important;
}

.organizer-blog-btn i {
    color: inherit !important;
}

/* Stats - Espacement optimisé */
.stats-container {
    padding: 1rem 0;
    margin-bottom: 1.5rem;
    animation: fadeIn 0.6s ease 0.4s both;
}

.stat-item {
    padding: 0 1.5rem;
    position: relative;
}

.stat-item:not(:last-child)::after {
    content: "";
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 1px;
    height: 40px;
    background-color: var(--stat-border-color);
}

.stat-content {
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #0d6efd;
    line-height: 1.2;
    display: block;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-top: 0.25rem;
}

/* Sections - Espacement optimisé */
.section-title {
    color: #212529;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f8f9fa;
}

.description-section,
.contact-section,
.social-section {
    margin-bottom: 2rem;
}

.description-content {
    line-height: 1.8;
    font-size: 1rem;
}

/* Contact Section */
.contact-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

.contact-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background-color: transparent !important;
    background: transparent !important;
    border-radius: 10px;
    color: #495057 !important;
    text-decoration: none;
    transition: var(--transition-smooth);
    border: 2px solid transparent;
}

.contact-item:hover {
    background-color: transparent !important;
    background: transparent !important;
    border-color: transparent;
    transform: translateX(4px);
    color: #0d6efd !important;
}

.contact-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: transparent !important;
    background: transparent !important;
    color: var(--bleu-nuit, #0d6efd) !important;
    border-radius: 8px;
    margin-right: 1rem;
    flex-shrink: 0;
    font-size: 1.125rem;
}

.contact-text {
    font-size: 0.95rem;
    font-weight: 500;
    word-break: break-word;
    color: #495057 !important;
}

/* S'assurer que tous les éléments de contact sont en gris */
.contact-item span.contact-text,
.contact-item .contact-text {
    color: #495057 !important;
    display: inline-block;
}

/* Les icônes restent en bleu de nuit */
.contact-item .contact-icon,
.contact-item i.contact-icon {
    color: var(--bleu-nuit, #0d6efd) !important;
}

.contact-item:hover,
.contact-item:hover span.contact-text,
.contact-item:hover .contact-text {
    color: #0d6efd !important;
}

.contact-item:hover .contact-icon,
.contact-item:hover i.contact-icon {
    color: var(--bleu-nuit, #0d6efd) !important;
}

/* Social Links */
.social-links {
    padding: 1.5rem;
    background-color: transparent !important;
    background: transparent !important;
    border-radius: 12px;
}

.social-icon {
    width: 48px;
    height: 48px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none !important;
    border-radius: 50%;
    color: #0d6efd;
    background-color: transparent;
    text-decoration: none;
    transition: var(--transition-smooth);
    font-size: 1.25rem;
}

.social-icon:hover {
    background-color: var(--blanc-or, #ffc107) !important;
    color: #000000 !important;
    transform: translateY(-4px) scale(1.1);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.social-icon:hover i {
    color: #000000 !important;
}

/* Tabs - Espacement optimisé */
.custom-tabs {
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 0;
    gap: 0.5rem;
}

.content-tabs {
    margin-top: 2rem;
}

.custom-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    padding: 1rem 1.5rem;
    border-radius: 8px 8px 0 0;
    transition: var(--transition-smooth);
    display: flex;
    align-items: center;
    position: relative;
}

.custom-tabs .nav-link:hover {
    background-color: #f8f9fa;
    color: #0d6efd;
}

.custom-tabs .nav-link.active {
    color: #0d6efd;
    background-color: transparent;
}

.custom-tabs .nav-link.active::after {
    content: "";
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background-color: #0d6efd;
    border-radius: 2px 2px 0 0;
}

.custom-tab-content {
    padding: 1.5rem;
    background-color: white;
    border-radius: 0 0 12px 12px;
    box-shadow: var(--shadow-sm);
}

/* Event Cards - Espacement optimisé */
.event-card {
    border-radius: 12px;
    overflow: hidden;
    transition: var(--transition-smooth);
    margin-bottom: 0;
}

.event-card .card-body {
    padding: 1.25rem;
}

.event-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-lg);
}

.event-image-wrapper {
    overflow: hidden;
    aspect-ratio: 16 / 10;
    background-color: #f8f9fa;
}

.event-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition-smooth);
}

.event-card:hover .event-image {
    transform: scale(1.08);
}

.event-badge .badge-lg {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
}

.event-title {
    color: #212529;
    line-height: 1.4;
    font-weight: 600;
}

.event-description {
    line-height: 1.6;
}

/* Blog Cards */
.blog-card {
    border-radius: 12px;
    overflow: hidden;
    transition: var(--transition-smooth);
}

.blog-card:hover {
    box-shadow: var(--shadow-lg);
}

.blog-image-wrapper {
    height: 100%;
    min-height: 250px;
    overflow: hidden;
    background-color: #f8f9fa;
}

.blog-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition-smooth);
}

.blog-card:hover .blog-image {
    transform: scale(1.05);
}

.blog-title {
    color: #212529;
    line-height: 1.4;
    font-weight: 600;
}

.blog-excerpt {
    line-height: 1.7;
}

/* Empty State */
.empty-state {
    padding: 4rem 2rem;
    animation: fadeIn 0.5s ease;
}

.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 50%;
}

.empty-icon i {
    font-size: 2.5rem;
    color: #adb5bd;
}

/* Responsive Design */
@media (max-width: 991.98px) {
    .custom-tab-content {
        padding: 1.5rem;
    }
    
    .stat-item {
        padding: 0 1rem;
    }
}

@media (max-width: 767.98px) {
    :root {
        --hero-height: var(--hero-height-mobile);
        --profile-logo-size: var(--profile-logo-size-mobile);
        --profile-card-offset: var(--profile-card-offset-mobile);
    }
    
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-slogan {
        font-size: 1rem;
    }
    
    .logo-wrapper {
        margin-top: calc(var(--profile-logo-size-mobile) / -2);
    }
    
    .stat-item {
        padding: 0 0.75rem;
    }
    
    .stat-item:not(:last-child)::after {
        height: 30px;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .stat-label {
        font-size: 0.7rem;
    }
    
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .custom-tabs .nav-link {
        padding: 0.875rem 1rem;
        font-size: 0.9rem;
    }
    
    .custom-tabs .nav-link .badge {
        font-size: 0.7rem;
    }
    
    .custom-tab-content {
        padding: 1rem;
        border-radius: 0 0 8px 8px;
    }
    
    .blog-image-wrapper {
        min-height: 200px;
    }
    
    .placeholder-text {
        font-size: 2.5rem;
    }
}

@media (max-width: 575.98px) {
    .follow-btn {
        font-size: 0.9rem;
        padding: 0.75rem 2rem;
    }
    
    .stats-container {
        padding: 1rem 0;
    }
}

/* Accessibilité */
.btn:focus-visible,
a:focus-visible,
button:focus-visible {
    outline: 3px solid rgba(13, 110, 253, 0.5);
    outline-offset: 3px;
}

/* Animations de chargement */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.loading {
    animation: pulse 1.5s ease-in-out infinite;
}

/* Amélioration des performances */
* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

img {
    max-width: 100%;
    height: auto;
}

/* Print styles */
@media print {
    .organizer-hero,
    .follow-form,
    .custom-tabs,
    .social-links {
        display: none;
    }
    
    .profile-card {
        margin-top: 0;
        box-shadow: none;
        border: 1px solid #dee2e6;
    }
    
    .main-content {
        padding: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // Initialisation des tooltips Bootstrap 5
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover focus'
        });
    });

    // Gestion optimisée des onglets
    const tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (event) {
            // Animer les cartes lors du changement d'onglet
            const targetPane = document.querySelector(event.target.getAttribute('data-bs-target'));
            if (targetPane) {
                const cards = targetPane.querySelectorAll('.card');
                cards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            }
            
            // Sauvegarder l'onglet actif dans localStorage
            try {
                localStorage.setItem('activeOrganizerTab', event.target.id);
            } catch (e) {
                // Ignore si localStorage n'est pas disponible
            }
        });
    });

    // Restaurer l'onglet actif depuis localStorage
    try {
        const activeTab = localStorage.getItem('activeOrganizerTab');
        if (activeTab) {
            const tabButton = document.getElementById(activeTab);
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }
    } catch (e) {
        // Ignore si localStorage n'est pas disponible
    }

    // Gestion du formulaire de suivi avec confirmation
    const followForms = document.querySelectorAll('.follow-form');
    followForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const organizerName = this.dataset.organizer;
            
            // Confirmation pour se désabonner
            if (this.querySelector('[name="_method"]')?.value === 'DELETE') {
                if (!confirm(`Êtes-vous sûr de vouloir vous désabonner de ${organizerName} ?`)) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Désactiver le bouton et afficher un spinner
            if (button) {
                button.disabled = true;
                const originalContent = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span>Chargement...</span>';
                button.setAttribute('aria-busy', 'true');
                
                // En cas d'échec, restaurer le bouton après 5 secondes
                setTimeout(() => {
                    if (button.disabled) {
                        button.disabled = false;
                        button.innerHTML = originalContent;
                        button.setAttribute('aria-busy', 'false');
                    }
                }, 5000);
            }
        });
    });

    // Parallax léger sur le hero banner
    const heroBanner = document.querySelector('.hero-banner');
    if (heroBanner && window.innerWidth > 768) {
        let ticking = false;
        
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    const scrolled = window.pageYOffset;
                    const rate = scrolled * 0.5;
                    
                    if (scrolled < 500) { // Limiter l'effet
                        heroBanner.style.transform = `translateY(${rate}px)`;
                    }
                    
                    ticking = false;
                });
                
                ticking = true;
            }
        });
    }

    // Animation des statistiques au scroll (compteur animé)
    const stats = document.querySelectorAll('.stat-number');
    const animateStats = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const finalValue = parseInt(target.textContent.replace(/\s/g, ''));
                let currentValue = 0;
                const increment = Math.ceil(finalValue / 30);
                const duration = 1000;
                const stepTime = duration / 30;
                
                const counter = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        target.textContent = finalValue.toLocaleString('fr-FR');
                        clearInterval(counter);
                    } else {
                        target.textContent = currentValue.toLocaleString('fr-FR');
                    }
                }, stepTime);
                
                observer.unobserve(target);
            }
        });
    };
    
    if ('IntersectionObserver' in window && stats.length > 0) {
        const statsObserver = new IntersectionObserver(animateStats, {
            threshold: 0.5,
            rootMargin: '0px'
        });
        
        stats.forEach(stat => statsObserver.observe(stat));
    }

    // Lazy loading amélioré pour les images (fallback si pas de support natif)
    if ('loading' in HTMLImageElement.prototype === false) {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        
        if (lazyImages.length > 0) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        img.classList.remove('loading');
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px'
            });
            
            lazyImages.forEach(img => {
                img.classList.add('loading');
                imageObserver.observe(img);
            });
        }
    }

    // Copier les informations de contact au clic
    const contactItems = document.querySelectorAll('.contact-item');
    contactItems.forEach(item => {
        // Ajouter un tooltip "Copié !" lors du clic sur email ou téléphone
        if (item.href && (item.href.startsWith('mailto:') || item.href.startsWith('tel:'))) {
            item.addEventListener('click', function(e) {
                const text = item.href.startsWith('mailto:') 
                    ? item.href.replace('mailto:', '') 
                    : item.href.replace('tel:', '');
                
                // Copier dans le presse-papiers
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(() => {
                        // Afficher un feedback visuel
                        const originalText = item.querySelector('.contact-text').textContent;
                        item.querySelector('.contact-text').textContent = '✓ Copié !';
                        
                        setTimeout(() => {
                            item.querySelector('.contact-text').textContent = originalText;
                        }, 2000);
                    }).catch(() => {
                        // Silencieusement ignorer les erreurs
                    });
                }
            });
        }
    });

    // Smooth scroll pour les ancres
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Gestion du clavier pour l'accessibilité des cartes
    const eventCards = document.querySelectorAll('.event-card, .blog-card');
    eventCards.forEach(card => {
        card.setAttribute('tabindex', '0');
        
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const link = this.querySelector('a');
                if (link) link.click();
            }
        });
    });

    // Performance: Réduire les animations si l'utilisateur préfère
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('*').forEach(el => {
            el.style.animation = 'none';
            el.style.transition = 'none';
        });
    }
});
</script>
@endpush