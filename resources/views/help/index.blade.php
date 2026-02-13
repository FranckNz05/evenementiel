@extends('layouts.app')

@section('title', 'Aide et Support')

@section('content')
<!-- Page Header Start -->
<div class="container-fluid page-header mb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <h1 class="display-3 mb-3 animated slideInDown">Aide et Support</h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item active" aria-current="page">Aide et Support</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<!-- Help Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                <!-- Guide d'utilisation -->
                <div class="mb-5">
                    <h4 class="mb-3">Guide d'utilisation</h4>
                    <p>Découvrez comment utiliser MokiliEvent efficacement avec notre guide détaillé.</p>
                    <div class="accordion" id="userGuide">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#guide1">
                                    Comment créer un compte ?
                                </button>
                            </h2>
                            <div id="guide1" class="accordion-collapse collapse show" data-bs-parent="#userGuide">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Cliquez sur "S'inscrire" en haut à droite</li>
                                        <li>Remplissez le formulaire avec vos informations</li>
                                        <li>Vérifiez votre email</li>
                                        <li>Connectez-vous à votre compte</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Rapide -->
                <div class="mb-5">
                    <h4 class="mb-3">Questions Fréquentes</h4>
                    <div class="accordion" id="quickFaq">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Comment puis-je devenir organisateur ?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#quickFaq">
                                <div class="accordion-body">
                                    Pour devenir organisateur, connectez-vous à votre compte et cliquez sur "Devenir Organisateur" dans votre tableau de bord. Remplissez le formulaire et notre équipe examinera votre demande.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="bg-light rounded p-4 mb-4 wow fadeInUp" data-wow-delay="0.3s">
                    <h5 class="mb-3">Liens Rapides</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('faq') }}">FAQ</a></li>
                        <li><a href="{{ route('contact') }}">Nous Contacter</a></li>
                        <li><a href="{{ route('terms') }}">Conditions d'utilisation</a></li>
                        <li><a href="{{ route('privacy') }}">Politique de confidentialité</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Help End -->
@endsection
