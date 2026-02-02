@extends('layouts.app')

@section('title', 'Centre d\'aide - MokiliEvent')
@section('description', 'Obtenez de l\'aide pour utiliser MokiliEvent et résoudre vos problèmes.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="card-title text-center mb-4">Centre d'aide</h1>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Nouvel utilisateur</h5>
                                    <p class="card-text">Découvrez comment créer votre compte et commencer à utiliser MokiliEvent.</p>
                                    <a href="#" class="btn btn-outline-primary">Guide de démarrage</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-ticket-alt fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Acheter des billets</h5>
                                    <p class="card-text">Apprenez à rechercher et acheter des billets pour vos événements favoris.</p>
                                    <a href="#" class="btn btn-outline-success">Guide d'achat</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-plus fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Créer un événement</h5>
                                    <p class="card-text">Organisez et gérez vos événements avec notre plateforme.</p>
                                    <a href="#" class="btn btn-outline-warning">Guide organisateur</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-credit-card fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Paiements</h5>
                                    <p class="card-text">Informations sur les modes de paiement et la sécurité.</p>
                                    <a href="#" class="btn btn-outline-info">Guide paiement</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h3>Ressources utiles</h3>
                        <div class="list-group">
                            <a href="{{ route('faq') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-question-circle me-2"></i>
                                Questions fréquentes
                            </a>
                            <a href="{{ route('contact') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-envelope me-2"></i>
                                Contacter le support
                            </a>
                            <a href="{{ route('terms') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-contract me-2"></i>
                                Conditions d'utilisation
                            </a>
                            <a href="{{ route('privacy') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-shield-alt me-2"></i>
                                Politique de confidentialité
                            </a>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <h4>Besoin d'aide immédiate ?</h4>
                        <p>Notre équipe de support est disponible 24h/24, 7j/7</p>
                        <div class="row">
                            <div class="col-md-4">
                                <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                                <p><strong>Email</strong><br>support@mokilievent.com</p>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-phone fa-2x text-success mb-2"></i>
                                <p><strong>Téléphone</strong><br>+243 XXX XXX XXX</p>
                            </div>
                            <div class="col-md-4">
                                <i class="fab fa-whatsapp fa-2x text-success mb-2"></i>
                                <p><strong>WhatsApp</strong><br>+243 XXX XXX XXX</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
