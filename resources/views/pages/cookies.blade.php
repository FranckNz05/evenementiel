@extends('layouts.app')

@section('title', 'Politique des cookies - MokiliEvent')
@section('description', 'Découvrez comment MokiliEvent utilise les cookies pour améliorer votre expérience utilisateur.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="card-title text-center mb-4">Politique des cookies</h1>
                    
                    <div class="text-muted mb-4">
                        <small>Dernière mise à jour : {{ date('d/m/Y') }}</small>
                    </div>

                    <div class="content">
                        <h2>1. Qu'est-ce qu'un cookie ?</h2>
                        <p>Un cookie est un petit fichier texte stocké sur votre ordinateur ou appareil mobile lorsque vous visitez notre site web. Les cookies nous permettent de reconnaître votre appareil et de mémoriser vos préférences.</p>

                        <h2>2. Comment utilisons-nous les cookies ?</h2>
                        <p>Nous utilisons les cookies pour :</p>
                        <ul>
                            <li>Assurer le bon fonctionnement du site</li>
                            <li>Mémoriser vos préférences de connexion</li>
                            <li>Améliorer votre expérience utilisateur</li>
                            <li>Analyser l'utilisation du site</li>
                            <li>Personnaliser le contenu</li>
                        </ul>

                        <h2>3. Types de cookies utilisés</h2>
                        
                        <h3>3.1 Cookies essentiels</h3>
                        <p>Ces cookies sont nécessaires au fonctionnement du site et ne peuvent pas être désactivés.</p>

                        <h3>3.2 Cookies de performance</h3>
                        <p>Ces cookies nous aident à comprendre comment les visiteurs interagissent avec notre site en collectant des informations de manière anonyme.</p>

                        <h3>3.3 Cookies de fonctionnalité</h3>
                        <p>Ces cookies permettent au site de se souvenir des choix que vous faites et de fournir des fonctionnalités améliorées et plus personnelles.</p>

                        <h3>3.4 Cookies de ciblage</h3>
                        <p>Ces cookies peuvent être définis par nos partenaires publicitaires pour créer un profil de vos intérêts et vous montrer des publicités pertinentes.</p>

                        <h2>4. Gestion des cookies</h2>
                        <p>Vous pouvez contrôler et/ou supprimer les cookies comme vous le souhaitez. Vous pouvez supprimer tous les cookies déjà présents sur votre ordinateur et configurer la plupart des navigateurs pour qu'ils les bloquent.</p>

                        <h2>5. Cookies tiers</h2>
                        <p>Nous utilisons également des cookies de tiers pour analyser l'utilisation du site et améliorer nos services. Ces cookies sont soumis aux politiques de confidentialité de leurs propriétaires respectifs.</p>

                        <h2>6. Contact</h2>
                        <p>Si vous avez des questions sur notre utilisation des cookies, veuillez nous contacter à : <a href="mailto:privacy@mokilievent.com">privacy@mokilievent.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
