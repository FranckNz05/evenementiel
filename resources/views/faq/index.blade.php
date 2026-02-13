@extends('layouts.app')

@section('title', 'FAQ - Questions fréquentes')

@section('content')

<!-- FAQ Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                <div class="accordion" id="faqAccordion">
                    <!-- Section 1: Questions générales -->
                    <div class="mb-5">
                        <h4 class="mb-4">Questions générales</h4>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Qu'est-ce que MokiliEvent ?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    MokiliEvent est la première plateforme de billetterie en ligne au Congo. Nous permettons aux organisateurs de créer et gérer leurs événements, et aux participants d'acheter facilement leurs billets en ligne.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Comment puis-je acheter des billets ?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Pour acheter des billets, il suffit de créer un compte, de sélectionner l'événement qui vous intéresse, de choisir vos billets et de procéder au paiement en ligne.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Pour les participants -->
                    <div class="mb-5">
                        <h4 class="mb-4">Pour les participants</h4>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Comment recevoir mes billets ?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Après votre achat, vos billets seront disponibles dans votre espace personnel et vous seront également envoyés par email au format PDF.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Puis-je me faire rembourser ?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Les conditions de remboursement dépendent de la politique de chaque organisateur. Veuillez consulter les conditions de vente spécifiques à l'événement.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Pour les organisateurs -->
                    <div class="mb-5">
                        <h4 class="mb-4">Pour les organisateurs</h4>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Comment créer un événement ?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Pour créer un événement, vous devez d'abord vous inscrire comme organisateur. Une fois validé, vous pourrez créer et gérer vos événements depuis votre tableau de bord.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    Quels sont les frais ?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Nous appliquons une commission sur chaque billet vendu. Contactez notre équipe commerciale pour plus de détails sur notre grille tarifaire.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                <div class="border-start border-5 border-primary ps-4 mb-5">
                    <h3 class="mb-4">Vous n'avez pas trouvé votre réponse ?</h3>
                    <p>Notre équipe est là pour vous aider. N'hésitez pas à nous contacter.</p>
                    <a class="btn btn-primary rounded-pill py-3 px-5" href="{{ route('contact') }}">Contactez-nous</a>
                </div>
                <div class="border-start border-5 border-primary ps-4">
                    <h3 class="mb-4">Liens utiles</h3>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-body mb-2" href="{{ route('about') }}"><i class="fa fa-arrow-right text-primary me-2"></i>À propos de nous</a>
                        <a class="text-body mb-2" href="{{ url('/direct-events') }}"><i class="fa fa-arrow-right text-primary me-2"></i>Découvrir nos événements</a>
                        <a class="text-body mb-2" href="{{ route('blogs.index') }}"><i class="fa fa-arrow-right text-primary me-2"></i>Blog</a>
                        <a class="text-body" href="{{ route('contact') }}"><i class="fa fa-arrow-right text-primary me-2"></i>Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FAQ End -->
@endsection
