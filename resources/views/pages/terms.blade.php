@extends('layouts.app')

@section('title', 'Conditions d\'utilisation - MokiliEvent')
@section('description', 'Consultez nos conditions d\'utilisation pour utiliser la plateforme MokiliEvent.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="card-title text-center mb-4">Conditions d'utilisation</h1>
                    
                    <div class="text-muted mb-4">
                        <small>Dernière mise à jour : {{ date('d/m/Y') }}</small>
                    </div>

                    <div class="content">
                        <h2>1. Acceptation des conditions</h2>
                        <p>En utilisant la plateforme MokiliEvent, vous acceptez d'être lié par ces conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser notre service.</p>

                        <h2>2. Description du service</h2>
                        <p>MokiliEvent est une plateforme de billetterie et de gestion d'événements en ligne qui permet aux utilisateurs de créer, gérer et vendre des billets pour des événements.</p>

                        <h2>3. Utilisation du service</h2>
                        <h3>3.1 Utilisateurs</h3>
                        <ul>
                            <li>Vous devez avoir au moins 18 ans pour utiliser notre service</li>
                            <li>Vous êtes responsable de la sécurité de votre compte</li>
                            <li>Vous ne devez pas utiliser le service à des fins illégales</li>
                        </ul>

                        <h3>3.2 Organisateurs</h3>
                        <ul>
                            <li>Vous êtes responsable de la qualité et de la légalité de vos événements</li>
                            <li>Vous devez respecter les lois locales et nationales</li>
                            <li>Vous êtes responsable du remboursement en cas d'annulation</li>
                        </ul>

                        <h2>4. Paiements et remboursements</h2>
                        <p>Les paiements sont traités par nos partenaires de confiance. Les remboursements sont soumis à notre politique de remboursement.</p>

                        <h2>5. Propriété intellectuelle</h2>
                        <p>Le contenu de MokiliEvent est protégé par les droits d'auteur. Vous ne pouvez pas reproduire, distribuer ou modifier notre contenu sans autorisation.</p>

                        <h2>6. Limitation de responsabilité</h2>
                        <p>MokiliEvent ne peut être tenu responsable des dommages indirects résultant de l'utilisation de notre service.</p>

                        <h2>7. Modification des conditions</h2>
                        <p>Nous nous réservons le droit de modifier ces conditions à tout moment. Les modifications prendront effet dès leur publication sur notre site.</p>

                        <h2>8. Contact</h2>
                        <p>Pour toute question concernant ces conditions d'utilisation, veuillez nous contacter à : <a href="mailto:legal@mokilievent.com">legal@mokilievent.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
