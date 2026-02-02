@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="text-center mb-5">
        <div class="info-banner bg-primary text-white rounded-4 p-4 mb-4">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <h3 class="h5 fw-bold mb-2">Événements Personnalisés</h3>
            <p class="mb-0 small">Pour créer un événement personnalisé, choisissez d'abord une formule adaptée à vos besoins. Après le paiement, vous pourrez créer et gérer votre événement.</p>
        </div>
        <h1 class="display-4 fw-bold text-gradient mb-3">🎟️ Formules MokiliEvent</h1>
        <p class="lead text-muted">Choisissez la formule adaptée à votre événement personnalisé</p>
        <p class="text-muted">Le paiement débloque la création et la gestion complète de votre événement</p>
    </div>

    <!-- Pricing Cards -->
    <div class="row g-4 justify-content-center">
        <!-- Start Plan -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-lg pricing-card pricing-start">
                <div class="card-body p-4 d-flex flex-column text-center">
                    <div class="plan-badge bg-success mb-3">Économique</div>
                    <h3 class="fw-bold mb-2">🟢 Start</h3>
                    <div class="price-section mb-4">
                        <span class="price-amount">30 000</span>
                        <span class="price-currency">FCFA</span>
                    </div>
                    <p class="text-muted mb-4 small">Jusqu'à <strong>100 invités</strong></p>
                    <ul class="plan-features list-unstyled mb-4 text-start">
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Création d'événement</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Envoi SMS automatique</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Tableau de bord de base</span>
                        </li>
                        <li class="feature-item text-muted">
                            <i class="fas fa-times-circle me-2"></i>
                            <span>Pas d'ajout d'invités après</span>
                        </li>
                        <li class="feature-item text-muted">
                            <i class="fas fa-times-circle me-2"></i>
                            <span>Pas d'URL temps réel</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-envelope text-success me-2"></i>
                            <span>Support email</span>
                        </li>
                    </ul>
                    <p class="small text-muted mb-4">Idéale pour un petit anniversaire, un repas familial ou une rencontre privée.</p>
                    <a href="{{ route('custom-offers.payment', ['plan' => 'start']) }}" class="btn btn-success btn-lg w-100 mt-auto">
                        <i class="fas fa-shopping-cart me-2"></i>Choisir Start
                    </a>
                </div>
            </div>
        </div>

        <!-- Standard Plan -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-lg pricing-card pricing-standard">
                <div class="card-body p-4 d-flex flex-column text-center">
                    <div class="plan-badge bg-primary mb-3">Populaire</div>
                    <h3 class="fw-bold mb-2">🟡 Standard</h3>
                    <div class="price-section mb-4">
                        <span class="price-amount">50 000</span>
                        <span class="price-currency">FCFA</span>
                    </div>
                    <p class="text-muted mb-4 small">Jusqu'à <strong>300 invités</strong></p>
                    <ul class="plan-features list-unstyled mb-4 text-start">
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span>Tout de Start +</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span>Ajout d'invités après création</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span>Programmation SMS</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span>Dashboard amélioré</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span>URL suivi temps réel</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-comments text-primary me-2"></i>
                            <span>Support WhatsApp + email</span>
                        </li>
                    </ul>
                    <p class="small text-muted mb-4">Parfait pour un mariage, baptême ou événement moyen.</p>
                    <a href="{{ route('custom-offers.payment', ['plan' => 'standard']) }}" class="btn btn-primary btn-lg w-100 mt-auto">
                        <i class="fas fa-shopping-cart me-2"></i>Choisir Standard
                    </a>
                </div>
            </div>
        </div>

        <!-- Premium Plan -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-lg pricing-card pricing-premium">
                <div class="card-body p-4 d-flex flex-column text-center position-relative">
                    <div class="plan-badge bg-info mb-3">Recommandé</div>
                    <h3 class="fw-bold mb-2">🔵 Premium</h3>
                    <div class="price-section mb-4">
                        <span class="price-amount">75 000</span>
                        <span class="price-currency">FCFA</span>
                    </div>
                    <p class="text-muted mb-4 small">Jusqu'à <strong>800 invités</strong></p>
                    <ul class="plan-features list-unstyled mb-4 text-start">
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-info me-2"></i>
                            <span>Tout de Standard +</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-info me-2"></i>
                            <span>Ajout illimité d'invités</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-info me-2"></i>
                            <span>Dashboard temps réel</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-info me-2"></i>
                            <span>Statistiques automatiques</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-info me-2"></i>
                            <span>Rappel automatique J-1</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-phone text-info me-2"></i>
                            <span>Support WhatsApp + téléphone</span>
                        </li>
                    </ul>
                    <p class="small text-muted mb-4">Idéale pour grands mariages, galas, entreprises.</p>
                    <a href="{{ route('custom-offers.payment', ['plan' => 'premium']) }}" class="btn btn-info btn-lg w-100 mt-auto">
                        <i class="fas fa-shopping-cart me-2"></i>Choisir Premium
                    </a>
                </div>
            </div>
        </div>

        <!-- Ultimate Plan -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-lg pricing-card pricing-ultimate">
                <div class="card-body p-4 d-flex flex-column text-center position-relative">
                    <div class="plan-badge bg-danger mb-3">Premium</div>
                    <h3 class="fw-bold mb-2">🔴 Ultimate</h3>
                    <div class="price-section mb-4">
                        <span class="price-amount">100 000</span>
                        <span class="price-currency">FCFA</span>
                    </div>
                    <p class="text-muted mb-4 small">Jusqu'à <strong>1 500 invités</strong></p>
                    <ul class="plan-features list-unstyled mb-4 text-start">
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-danger me-2"></i>
                            <span>Toutes options Premium +</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-danger me-2"></i>
                            <span>Dashboard mobile/tablette</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-danger me-2"></i>
                            <span>Export Excel / PDF</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-danger me-2"></i>
                            <span>Rappel automatique 24h</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-check-circle text-danger me-2"></i>
                            <span>Assistance dédiée</span>
                        </li>
                        <li class="feature-item">
                            <i class="fas fa-headset text-danger me-2"></i>
                            <span>Support prioritaire 24/7</span>
                        </li>
                    </ul>
                    <p class="small text-muted mb-4">Idéale pour grands événements pro, concerts, mariages VIP.</p>
                    <a href="{{ route('custom-offers.payment', ['plan' => 'ultimate']) }}" class="btn btn-danger btn-lg w-100 mt-auto">
                        <i class="fas fa-shopping-cart me-2"></i>Choisir Ultimate
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center mt-5">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
        </a>
    </div>
</div>

<style>
    /* Gradient Text */
    .text-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Pricing Cards */
    .pricing-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border-radius: 20px;
        overflow: hidden;
        border-top: 4px solid transparent;
    }

    .pricing-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .pricing-start {
        border-top-color: #28a745;
    }

    .pricing-standard {
        border-top-color: #0d6efd;
    }

    .pricing-premium {
        border-top-color: #0dcaf0;
    }

    .pricing-ultimate {
        border-top-color: #dc3545;
    }

    /* Plan Badge */
    .plan-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
        color: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    /* Price Section */
    .price-section {
        padding: 20px 0;
    }

    .price-amount {
        font-size: 2.5rem;
        font-weight: bold;
        color: #333;
    }

    .price-currency {
        font-size: 1.2rem;
        color: #666;
        margin-left: 5px;
    }

    /* Features List */
    .plan-features {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        min-height: 250px;
    }

    .feature-item {
        padding: 8px 0;
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .feature-item:hover {
        transform: translateX(5px);
    }

    .feature-item i {
        font-size: 1rem;
        min-width: 20px;
    }

    /* Buttons */
    .btn-lg {
        padding: 15px 30px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .pricing-card {
            margin-bottom: 2rem;
        }

        .display-4 {
            font-size: 2rem;
        }

        .price-amount {
            font-size: 2rem;
        }
    }

    /* Info Banner */
    .info-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        max-width: 800px;
        margin: 0 auto;
    }

    /* Animation on load */
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

    .pricing-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .pricing-standard {
        animation-delay: 0.1s;
    }

    .pricing-premium {
        animation-delay: 0.2s;
    }

    .pricing-ultimate {
        animation-delay: 0.3s;
    }
</style>
@endsection
