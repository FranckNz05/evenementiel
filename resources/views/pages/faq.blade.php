@extends('layouts.app')

@section('title', 'FAQ - Questions fréquentes - MokiliEvent')
@section('description', 'Trouvez les réponses aux questions les plus fréquentes sur MokiliEvent.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="card-title text-center mb-4">Questions fréquentes</h1>
                    
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    Comment créer un compte sur MokiliEvent ?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Pour créer un compte, cliquez sur "S'inscrire" en haut à droite de la page, remplissez le formulaire avec vos informations et confirmez votre email.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                    Comment acheter des billets ?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sélectionnez l'événement de votre choix, choisissez le nombre de billets souhaités, procédez au paiement via nos partenaires sécurisés, et recevez vos billets par email.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                    Comment devenir organisateur ?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Remplissez le formulaire de demande d'organisateur avec vos informations professionnelles. Notre équipe examinera votre demande et vous contactera dans les 48h.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                    Quels sont les modes de paiement acceptés ?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Nous acceptons les paiements par carte bancaire, mobile money (Orange Money, MTN Money, Airtel Money) et paiements électroniques via nos partenaires sécurisés.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                    Comment obtenir un remboursement ?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Les remboursements sont possibles jusqu'à 24h avant l'événement. Contactez notre service client avec votre numéro de commande pour initier le processus.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading6">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                    Comment contacter le support ?
                                </button>
                            </h2>
                            <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Vous pouvez nous contacter via le formulaire de contact, par email à support@mokilievent.com, ou par téléphone au +243 XXX XXX XXX.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p>Vous ne trouvez pas la réponse à votre question ?</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary">Contactez-nous</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
