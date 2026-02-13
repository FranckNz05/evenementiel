@extends('layouts.app')

@section('title', 'À proposs de MokiliEvent')

@section('content')

<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffffff;
    --blanc-or-fonce: #f8fafc;
}

.about-hero {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: white;
    padding: 4rem 0;
    position: relative;
    overflow: hidden;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.03"><path d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/></g></g></svg>');
    opacity: 0.5;
}

.about-hero-content {
    position: relative;
    z-index: 1;
}


.feature-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    height: 100%;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
    border-color: var(--blanc-or);
}

.feature-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%) !important;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white !important;
    font-size: 2rem;
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.3);
}

.feature-icon i,
.feature-icon .fas,
.feature-icon .fa {
    color: white !important;
}

.feature-icon.yellow {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%) !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.3);
}

.feature-icon.yellow i,
.feature-icon.yellow .fas,
.feature-icon.yellow .fa {
    color: white !important;
}

.feature-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 1rem;
}

.feature-description {
    color: #666;
    line-height: 1.7;
    margin-bottom: 1.5rem;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-list li {
    padding: 0.5rem 0;
    color: #555;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.feature-list li i {
    color: var(--blanc-or);
    margin-top: 0.25rem;
    font-size: 0.875rem;
}


.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--bleu-nuit);
    margin-bottom: 1rem;
    text-align: center;
}

.section-subtitle {
    font-size: 1.125rem;
    color: #666;
    text-align: center;
    margin-bottom: 3rem;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.cta-section {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: white;
    padding: 4rem 0;
    border-radius: 20px;
    margin: 4rem 0;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    color: white !important;
}

.cta-subtitle {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 2rem;
    color: white !important;
}

.btn-cta {
    background: var(--blanc-or);
    color: var(--bleu-nuit);
    padding: 1rem 2.5rem;
    font-size: 1.125rem;
    font-weight: 700;
    border-radius: 50px;
    border: none;
    transition: all 0.3s ease;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
}

.btn-cta:hover,
.btn-cta:focus,
.btn-cta:active {
    background: var(--blanc-or-fonce);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(15, 26, 61, 0.2);
    color: var(--bleu-nuit);
    text-decoration: none !important;
}

.btn-cta[style*="rgba(255,255,255,0.2)"]:hover {
    text-decoration: none !important;
}

.values-section {
    padding: 4rem 0;
}

.value-card {
    text-align: center;
    padding: 2rem;
}

.value-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%) !important;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2.5rem;
    color: white !important;
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.3);
}

.value-icon i,
.value-icon .fas,
.value-icon .fa {
    color: white !important;
}

.value-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 1rem;
}

.value-description {
    color: #666;
    line-height: 1.7;
}

@media (max-width: 768px) {
    .section-title {
        font-size: 2rem;
    }
}
</style>

<!-- Mission & Vision -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="feature-card">
                    <div class="feature-icon yellow">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="feature-title">Notre Mission</h3>
                    <p class="feature-description">
                        Faciliter l'organisation et la participation aux événements culturels, sportifs, 
                        professionnels et sociaux à travers le Congo en offrant une plateforme innovante 
                        et accessible à tous.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="feature-card">
                    <div class="feature-icon yellow">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="feature-title">Notre Vision</h3>
                    <p class="feature-description">
                        Devenir la référence en matière de gestion d'événements en République du Congo, 
                        en connectant les organisateurs avec leur public et en révolutionnant l'expérience de billetterie.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fonctionnalités Principales -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title">Nos Fonctionnalités</h2>
        <p class="section-subtitle">
            Découvrez tous les outils à votre disposition pour créer des événements mémorables
        </p>
        
        <div class="row g-4">
            <!-- Billetterie en ligne -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h3 class="feature-title">Billetterie en ligne</h3>
                    <p class="feature-description">
                        Créez et gérez vos événements facilement avec notre système de billetterie complet.
                    </p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Création d'événements en quelques minutes</li>
                        <li><i class="fas fa-check-circle"></i> Gestion des billets et tarifs</li>
                        <li><i class="fas fa-check-circle"></i> QR codes pour contrôle d'accès</li>
                        <li><i class="fas fa-check-circle"></i> Vérification rapide à l'entrée</li>
                    </ul>
                </div>
            </div>

            <!-- Paiements -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon yellow">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Paiements mobiles</h3>
                    <p class="feature-description">
                        Acceptez les paiements en toute sécurité avec les méthodes de paiement les plus populaires.
                    </p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Airtel Money</li>
                        <li><i class="fas fa-check-circle"></i> MTN Mobile Money</li>
                        
                        <li><i class="fas fa-check-circle"></i> Transactions sécurisées</li>
                    </ul>
                </div>
            </div>

            <!-- Promotion -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3 class="feature-title">Promotion d'événements</h3>
                    <p class="feature-description">
                        Faites connaître vos événements grâce à nos outils de promotion intégrés.
                    </p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Pages d'événements publiques</li>
                        <li><i class="fas fa-check-circle"></i> Optimisation SEO</li>
                        <li><i class="fas fa-check-circle"></i> Partage sur réseaux sociaux</li>
                        <li><i class="fas fa-check-circle"></i> Système de favoris</li>
                    </ul>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon yellow">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Statistiques en temps réel</h3>
                    <p class="feature-description">
                        Suivez les performances de vos événements avec des données détaillées et des rapports.
                    </p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Ventes et réservations</li>
                        <li><i class="fas fa-check-circle"></i> Suivi en temps réel</li>
                        <li><i class="fas fa-check-circle"></i> Exports CSV/PDF</li>
                        <li><i class="fas fa-check-circle"></i> Tableaux de bord interactifs</li>
                    </ul>
                </div>
            </div>

            <!-- Sécurité -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
        </div>
                    <h3 class="feature-title">Sécurité renforcée</h3>
                    <p class="feature-description">
                        Vos données et transactions sont protégées par les meilleures pratiques de sécurité.
                    </p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Comptes vérifiés</li>
                        <li><i class="fas fa-check-circle"></i> Gestion des rôles</li>
                        <li><i class="fas fa-check-circle"></i> Protection anti-fraude</li>
                        <li><i class="fas fa-check-circle"></i> Chiffrement des données</li>
                    </ul>
    </div>
</div>

            <!-- Événements personnalisés -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon yellow">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h3 class="feature-title">Événements personnalisés</h3>
                    <p class="feature-description">
                        Des formules adaptées à vos besoins avec des fonctionnalités avancées.
                    </p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Formules Start/Standard/Premium</li>
                        <li><i class="fas fa-check-circle"></i> Invitations SMS/WhatsApp</li>
                        <li><i class="fas fa-check-circle"></i> Suivi en temps réel</li>
                        <li><i class="fas fa-check-circle"></i> Tableau de bord dédié</li>
            </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Système de Recommandation Intelligent -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title">Système de Recommandation Intelligent</h2>
        <p class="section-subtitle">
            Découvrez comment nous vous proposons les événements qui vous correspondent vraiment
        </p>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="feature-card text-center">
                    <div class="feature-icon" style="margin: 0 auto 2rem;">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="feature-title mb-4">Comment ça fonctionne ?</h3>
                    <p class="feature-description" style="font-size: 1.125rem; line-height: 1.8;">
                        Notre système de recommandation intelligent collecte quelques données de navigation 
                        pour vous proposer le contenu de votre choix. Nous analysons vos préférences et votre 
                        comportement sur la plateforme afin de vous suggérer des événements qui correspondent 
                        à vos goûts et à votre localisation.
                    </p>
                    <p class="feature-description" style="font-size: 1.125rem; line-height: 1.8;">
                        Toutes ces analyses sont effectuées de manière sécurisée et anonyme. Vos données sont 
                        protégées et ne sont jamais vendues à des tiers. Pour en savoir plus sur la collecte et 
                        l'utilisation de vos données, consultez notre 
                        <a href="{{ route('privacy') }}" style="color: var(--bleu-nuit-clair); font-weight: 600;">politique de confidentialité</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pourquoi nous choisir -->
<section class="values-section">
    <div class="container">
        <h2 class="section-title">Pourquoi choisir MokiliEvent ?</h2>
        <p class="section-subtitle">
            Des avantages qui font la différence pour vos événements
        </p>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4 class="value-title">Facilité d'utilisation</h4>
                    <p class="value-description">
                        Interface intuitive pour créer, gérer et promouvoir vos événements en quelques clics.
                        Pas besoin d'être un expert technique !
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h4 class="value-title">Paiement sécurisé</h4>
                    <p class="value-description">
                        Transactions sécurisées avec support des paiements mobiles et .
                        Vos clients peuvent payer en toute confiance.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="value-title">Support client</h4>
                    <p class="value-description">
                        Une équipe dédiée pour vous accompagner à chaque étape de votre projet.
                        Nous sommes là pour vous aider à réussir.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h4 class="value-title">Portée nationale</h4>
                    <p class="value-description">
                        Plateforme disponible à travers toute la République du Congo.
                        Connectez-vous avec un public plus large.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h4 class="value-title">Analyses détaillées</h4>
                    <p class="value-description">
                        Obtenez des insights précieux sur vos événements avec nos rapports détaillés
                        et analyses en temps réel.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4 class="value-title">Formules flexibles</h4>
                    <p class="value-description">
                        Choisissez la formule qui correspond à vos besoins, de la solution basique
                        aux fonctionnalités premium.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5">
    <div class="container">
        <div class="cta-section">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="cta-title" style="color: white;">Prêt à créer votre premier événement ?</h2>
                    <p class="cta-subtitle" style="color: white;">
                        Rejoignez des centaines d'organisateurs qui font confiance à MokiliEvent
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        @auth
                            @if(auth()->user()->hasRole([2,3]))
                                <a href="{{ route('events.wizard.step1') }}" class="btn-cta">
                                    <i class="fas fa-plus-circle"></i>
                                    Créer un événement
                                </a>
                            @else
                                <a href="{{ route('organizer.request.create') }}" class="btn-cta">
                                    <i class="fas fa-user-plus"></i>
                                    Devenir organisateur
                                </a>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="btn-cta">
                                <i class="fas fa-user-plus"></i>
                                Créer un compte
                            </a>
                        @endauth
                        <a href="{{ route('contact') }}" class="btn-cta" style="background: rgba(255,255,255,0.2); color: white; text-decoration: none;">
                            <i class="fas fa-envelope"></i>
                            Nous contacter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
