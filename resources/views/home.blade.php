@extends('layouts.app')

@section('content')

<style>
/* Skeleton Loading Styles */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 400% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 4px;
}

@keyframes shimmer {
    0% { background-position: 100% 0; }
    100% { background-position: -100% 0; }
}

.skeleton-text {
    height: 1rem;
    margin-bottom: 0.5rem;
}

.skeleton-image {
    height: 200px;
    width: 100%;
    margin-bottom: 1rem;
}

.skeleton-card {
    height: 300px;
    width: 100%;
    margin-bottom: 1rem;
}
</style>

<!-- Hero Section Start -->
@php
    // Optimisation : utiliser WebP avec fallback, limiter largeur à 1200px
    $heroImageWebP = asset('images/foule-humains-copie.webp');
    $heroImageJPG = asset('images/foule-humains-copie.jpg');
    $heroImageAVIF = asset('images/foule-humains-copie.avif');
@endphp
<div class="container-fluid py-5 hero-header position-relative" style="height: 500px; background-size: cover; background-position: center;">
    <picture>
        <source srcset="{{ $heroImageAVIF }}" type="image/avif">
        <source srcset="{{ $heroImageWebP }}" type="image/webp">
        <img src="{{ $heroImageJPG }}" 
             alt="MokiliEvent - Événements au Congo" 
             class="hero-bg-image"
             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0;"
             fetchpriority="high"
             width="1200"
             height="500"
             loading="eager">
    </picture>
    <div class="overlay position-absolute w-100 h-100" style="background: rgba(0, 0, 0, 0.6); top: 0; left: 0; z-index: 1;"></div>
    <div class="container py-5 position-relative text-center text-white" style="z-index: 2;">
        <h1 class="mb-4 display-3 fw-bold">Le repère incontournable pour tous vos événements !</h1>
        <div class="mx-auto" style="max-width: 600px;">
            <form action="{{ url('/direct-events') }}" method="GET" class="d-flex position-relative">
                <input class="form-control border-0 shadow-lg rounded-start-pill py-3 px-4" type="text" name="search" placeholder="Rechercher un événement..." style="color: #000 !important;">
                <button type="submit" class="btn bg-danger rounded-end-pill px-4 shadow-lg" style="color: white;">Rechercher</button>
            </form>
        </div>
    </div>
</div>
<!-- Hero Section End -->

<!-- Section Événements Populaires - Version Carrousel -->
<section class="container-fluid py-5 bg-white shadow-deep">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 700px;">
            <h2 class="display-5 fw-bold">Nos Événements Populaires</h2>
            <p class="text-muted">Découvrez les événements les plus prisés du moment</p>
        </div>

        <div class="position-relative">
            @if($popularEvents && $popularEvents->count() > 0)
            <div class="owl-carousel popular-events-carousel" id="popular-events-carousel">
                @foreach($popularEvents as $event)
                <div class="item px-2">
                    <x-event-card :event="$event" link-route="direct-events.show" />
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aucun événement disponible pour le moment.</p>
                <p class="text-muted small">Les événements publiés et approuvés apparaîtront ici.</p>
                <a href="{{ route('events.public') }}" class="btn btn-primary mt-3">Voir tous les événements</a>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Section "Pour Vous" - Recommandations personnalisées (uniquement pour les utilisateurs connectés) -->
@auth
@if($recommendedEvents && $recommendedEvents->count() > 0)
<section class="container-fluid py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 700px;">
            <h2 class="display-5 fw-bold">
                <i class="fas fa-heart me-2" style="color: var(--bleu-nuit);"></i>Pour Vous
            </h2>
            <p class="text-muted">Des événements sélectionnés spécialement pour vous</p>
        </div>

        <div class="position-relative">
            <div class="owl-carousel recommended-events-carousel" id="recommended-events-carousel">
                @foreach($recommendedEvents as $event)
                <div class="item px-2">
                    <x-event-card :event="$event" link-route="direct-events.show" />
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
@endauth

<!-- Organisateurs sans arrière-plan -->
<section class="container-fluid py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 700px;">
            <h2 class="display-5 fw-bold">Nos Organisateurs</h2>
            <p class="text-muted">Découvrez les organisateurs qui font la différence</p>
        </div>

        <div class="position-relative">
            @if($organizers && $organizers->count() > 0)
            <div class="owl-carousel organizers-carousel" id="organizers-carousel">
                @foreach($organizers as $organizer)
                <div class="item px-2 text-center">
                    <a href="{{ route('organizers.show', $organizer->slug ?? $organizer->id) }}" class="text-decoration-none text-dark">
                        <div class="mb-3 mx-auto" style="width: 120px; height: 120px;">
                            <div class="rounded-circle overflow-hidden w-100 h-100 border border-3" style="border-color: var(--blanc-or) !important;">
                                @if($organizer->logo)
                                    <img src="{{ asset('storage/' . $organizer->logo) }}"
                                         alt="{{ $organizer->company_name ?? 'Organisateur' }}"
                                         class="img-fluid w-100 h-100 object-fit-cover"
                                         loading="lazy"
                                         decoding="async"
                                         width="120"
                                         height="120">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-building fa-2x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <h5 class="mb-1">{{ $organizer->company_name ?? 'Organisateur' }}</h5>
                        @if($organizer->events_count ?? $organizer->events()->where('is_published', true)->where('is_approved', true)->count() > 0)
                        <p class="text-muted small mb-0">{{ $organizer->events_count ?? $organizer->events()->where('is_published', true)->where('is_approved', true)->count() }} événement(s)</p>
                        @endif
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aucun organisateur disponible pour le moment.</p>
                <p class="text-muted small">Les organisateurs avec des événements publiés apparaîtront ici.</p>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Espace vide avec fond transparent -->
<div class="container-fluid py-3" style="background: transparent;"></div>

<!-- Call to Action -->
@php
    $bgImage = file_exists(public_path('images/texture-billets-2.jpg')) 
        ? asset('images/texture-billets-2.jpg') 
        : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iIzBmMWEzZCIvPjwvc3ZnPg==';
@endphp
<section class="container-fluid py-5 shadow-deep" style="background: linear-gradient(rgba(15, 26, 61, 0.92), rgba(15, 26, 61, 0.92)), url('{{ $bgImage }}'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="mb-4">
                    <i class="fas fa-calendar-plus fa-3x text-blanc-or mb-3"></i>
                    <h2 class="text-white mb-3">Vous organisez un événement ?</h2>
                    <p class="text-white-50 mb-4">MokiliEvent vous offre tous les outils pour gérer et promouvoir vos événements en toute simplicité.</p>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-blanc-or rounded-pill px-4 py-2">Commencer</a>
                    @else
                        @if(auth()->user()->hasRole(2) || auth()->user()->hasRole(3))
                            <a href="{{ route('events.wizard.step1') }}" class="btn btn-blanc-or rounded-pill px-4 py-2">
                                <i class="fas fa-plus me-2"></i>Créer un événement
                            </a>
                        @else
                            <a href="{{ route('organizer.request.create') }}" class="btn btn-blanc-or rounded-pill px-4 py-2">
                                <i class="fas fa-user-cog me-2"></i>Devenir organisateur
                            </a>
                        @endif
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Espace vide avec fond transparent -->
<div class="container-fluid py-3" style="background: transparent;"></div>

<!-- Catégories en grille rectangulaire -->
<section class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 700px;">
            <h2 class="display-5 fw-bold">Explorez par Catégories</h2>
            <p class="text-muted">Découvrez nos événements par centres d'intérêt</p>
        </div>

        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-md-6 col-lg-4">
                <a href="{{ url('/direct-events?category=' . $category->name) }}" class="text-decoration-none">
                    <div class="category-card position-relative rounded overflow-hidden"
                         style="box-shadow: -8px 0 15px -5px rgba(0,0,0,0.2); height: 120px;">
                        <img src="{{ asset($category->image) }}"
                             alt="{{ $category->name }}"
                             class="img-fluid w-100 h-100 object-fit-cover"
                             loading="lazy"
                             decoding="async"
                             width="400"
                             height="120">
                        <div class="category-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center ps-4"
                             style="background: linear-gradient(to right, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);">
                            <h5 class="text-white mb-0">{{ $category->name }}</h5>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Espace vide avec fond transparent -->
<div class="container-fluid py-3" style="background: transparent;"></div>

@endsection

@push('styles')
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"></noscript>
<style>
:root {
    /* Home page palette: emerald/teal, no red/yellow */
    --color-primary: #0f1a3d;         /* emerald 700 */
    --color-primary-light: #2dd4bf;   /* teal 400 */
    --bleu-nuit: var(--color-primary);
    --bleu-nuit-clair: var(--color-primary-light);
    --blanc-or: #ffffff;
    --blanc-or-fonce: #f3f4f6;        /* soft gray instead of yellow */
}

body {
    background-color: #f8f9fa;
}

/* Titres de sections */
h2.display-5.fw-bold {
    color: #111111 !important;
}

/* Barre d'annonces */
.announcement-carousel .owl-item {
    padding: 0 10px;
}

.announcement-carousel .item {
    border-left: 1px solid rgba(15, 118, 110, 0.2);
    height: 60px;
}

.announcement-carousel .item:first-child {
    border-left: none;
}

/* Ombre profonde */
.shadow-deep {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

/* Boutons */
.btn-blanc-or {
    background-color: var(--blanc-or);
    color: var(--bleu-nuit);
    font-weight: 600;
    border: none;
}

.btn-blanc-or:hover {
    background-color: var(--blanc-or-fonce);
    color: var(--bleu-nuit);
}

/* Primary and red utility remap (scoped to this page via pushed styles) */
.btn-primary { background-color: var(--bleu-nuit) !important; color: #ffffff !important; font-weight: 600; border: none; }
.bg-danger { background-color: var(--bleu-nuit) !important; }
.text-danger { color: var(--bleu-nuit) !important; }
.bg-success { background-color: var(--bleu-nuit) !important; }
.text-success { color: var(--bleu-nuit) !important; }

//* Styles pour le carrousel des événements populaires */
.popular-events-carousel .owl-stage {
    display: flex;
    align-items: stretch;
}

.popular-events-carousel .owl-item {
    height: auto;
}

.popular-events-carousel .card {
    min-height: 380px;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.popular-events-carousel .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
}

.popular-events-carousel .card-img-top {
    height: 160px;
    object-fit: cover;
}

.popular-events-carousel .card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.popular-events-carousel .card-body > *:last-child {
    margin-top: auto;
}

/* Styles pour le carousel des recommandations "Pour Vous" */
.recommended-events-carousel .owl-stage {
    display: flex;
    align-items: stretch;
}

.recommended-events-carousel .owl-item {
    height: auto;
}

.recommended-events-carousel .card {
    min-height: 380px;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.recommended-events-carousel .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
}

.recommended-events-carousel .card-img-top {
    height: 160px;
    object-fit: cover;
}

.recommended-events-carousel .card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.recommended-events-carousel .card-body > *:last-child {
    margin-top: auto;
}

/* Images des catégories et organisateurs */
.categories-carousel .rounded-circle,
.organizers-carousel .rounded-circle {
    border: 3px solid var(--blanc-or) !important;
}

/* Navigation des carrousels masquée */
.owl-nav {
    display: none;
}

/* Cartes d'événements de même taille */
.event-card {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.event-card .card-body {
    flex: 1;
    height: 50%;
}

/* Style des catégories et organisateurs */
.categories-carousel .item,
.organizers-carousel .item {
    padding: 0 15px;
}

/* Bouton favori */
.btn-favorite {
    position: absolute;
    top: 15px;
    right: 15px;
    background: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    z-index: 10;
}

.btn-favorite:hover {
    transform: scale(1.1);
    background-color: #fff;
}

.btn-favorite i {
    font-size: 1.2rem;
}

/* Responsive */
@media (max-width: 767.98px) {
    .hero-header {
        height: 400px !important;
    }

    .announcement-carousel .item {
        height: 80px;
        padding: 10px;
    }

    .card {
        margin-bottom: 20px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" defer></script>
<script>
// Vérifier que jQuery est chargé avant d'initialiser les carrousels
if (typeof jQuery === 'undefined') {
    console.error('jQuery n\'est pas chargé ! Les carrousels ne peuvent pas fonctionner.');
} else {
    (function($) {
        $(document).ready(function(){
            // Initialisation des carrousels uniquement s'ils existent et ont du contenu
            if ($('.categories-carousel').length && $('.categories-carousel .item').length > 0) {
                $('.categories-carousel').owlCarousel({
                    loop: true,
                    margin: 20,
                    nav: false,
                    dots: false,
                    responsive: {
                        0: { items: 2 },
                        576: { items: 3 },
                        768: { items: 4 },
                        992: { items: 5 }
                    }
                });
            }

            // Initialiser le carousel des événements populaires
            var $popularCarousel = $('.popular-events-carousel');
            if ($popularCarousel.length && $popularCarousel.find('.item').length > 0) {
                $popularCarousel.owlCarousel({
                    loop: false,
                    margin: 20,
                    nav: false,
                    dots: false,
                    autoWidth: false,
                    responsive: {
                        0: { items: 1 },
                        576: { items: 2 },
                        768: { items: 3 },
                        992: { items: 4 }
                    }
                });
            } else {
                console.warn('Popular events carousel not initialized: no items found');
            }

            // Initialiser le carousel des organisateurs
            var $organizersCarousel = $('.organizers-carousel');
            if ($organizersCarousel.length && $organizersCarousel.find('.item').length > 0) {
                $organizersCarousel.owlCarousel({
                    loop: false,
                    margin: 20,
                    nav: false,
                    dots: false,
                    autoWidth: false,
                    responsive: {
                        0: { items: 2 },
                        576: { items: 3 },
                        768: { items: 4 },
                        992: { items: 5 }
                    }
                });
            } else {
                console.warn('Organizers carousel not initialized: no items found');
            }

            // Initialiser le carousel des recommandations
            var $recommendedCarousel = $('.recommended-events-carousel');
            if ($recommendedCarousel.length && $recommendedCarousel.find('.item').length > 0) {
                $recommendedCarousel.owlCarousel({
                    loop: false,
                    margin: 20,
                    nav: false,
                    dots: false,
                    autoWidth: false,
                    responsive: {
                        0: { items: 1 },
                        576: { items: 2 },
                        768: { items: 3 },
                        992: { items: 4 }
                    }
                });
            } else {
                console.warn('Recommended events carousel not initialized: no items found');
            }

            // Navigation au survol
            $('.categories-carousel, .popular-events-carousel, .organizers-carousel, .recommended-events-carousel').on('mousewheel', function(e) {
                if (e.originalEvent.wheelDelta / 120 > 0) {
                    $(this).trigger('prev.owl');
                } else {
                    $(this).trigger('next.owl');
                }
                e.preventDefault();
            });

            // Gestion des favoris
            $('.btn-favorite').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const eventId = $(this).data('event-id');
                const icon = $(this).find('i');

                $.ajax({
                    url: `/events/${eventId}/favorite`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.status === 'added') {
                            icon.removeClass('far').addClass('fas');
                            alert('Événement ajouté aux favoris');
                        } else if (data.status === 'removed') {
                            icon.removeClass('fas').addClass('far');
                            alert('Événement retiré des favoris');
                        }
                    },
                    error: function() {
                        alert('Une erreur est survenue');
                    }
                });
            });
        }); // Fin de $(document).ready
    })(jQuery); // Passer jQuery comme paramètre pour garantir qu'il est disponible
} // Fin du if jQuery
</script>
@endpush
