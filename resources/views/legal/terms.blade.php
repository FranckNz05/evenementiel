@extends('layouts.app')

@section('title', 'Conditions d\'utilisation - MokiliEvent')

@section('content')
<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffffff;
}

.terms-hero {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 3rem;
}

.terms-content {
    background: white;
    border-radius: 16px;
    padding: 3rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

.terms-content h2 {
    color: var(--bleu-nuit);
    font-size: 1.75rem;
    font-weight: 700;
    margin-top: 2.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e9ecef;
}

.terms-content h2:first-child {
    margin-top: 0;
}

.terms-content h3 {
    color: var(--bleu-nuit-clair);
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.terms-content p {
    color: #555;
    line-height: 1.8;
    margin-bottom: 1rem;
}

.terms-content ul, .terms-content ol {
    color: #555;
    line-height: 1.8;
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.terms-content li {
    margin-bottom: 0.5rem;
}

.terms-sidebar {
    position: sticky;
    top: 20px;
}

.sidebar-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.sidebar-card h3 {
    color: var(--bleu-nuit);
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.sidebar-link {
    color: #555;
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    transition: color 0.3s ease;
}

.sidebar-link:hover {
    color: var(--bleu-nuit-clair);
    text-decoration: none;
}

.sidebar-link i {
    margin-right: 0.75rem;
    color: var(--bleu-nuit-clair);
}

.last-updated {
    color: #888;
    font-size: 0.875rem;
    font-style: italic;
    margin-bottom: 2rem;
}
</style>

<!-- Hero Section -->
<div class="terms-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1 class="mb-3" style="font-size: 2.5rem; font-weight: 800;">Conditions d'utilisation</h1>
                <p class="mb-0" style="font-size: 1.125rem; opacity: 0.9;">
                    Dernière mise à jour : {{ date('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Terms Content -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="terms-content">
                <p class="last-updated">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Dernière mise à jour : {{ date('d/m/Y') }}
                </p>

                <h2>1. Introduction et acceptation</h2>
                <p>
                    Bienvenue sur MokiliEvent, une plateforme de billetterie et de gestion d'événements en ligne 
                    opérant en République du Congo. En accédant et en utilisant notre site web et nos services, 
                    vous acceptez d'être lié par les présentes conditions d'utilisation. Si vous n'acceptez pas 
                    ces conditions, veuillez ne pas utiliser nos services.
                </p>
                <p>
                    Ces conditions d'utilisation sont régies par les lois de la République du Congo. 
                    Toute utilisation de notre plateforme implique l'acceptation pleine et entière de ces conditions.
                </p>

                <h2>2. Définitions</h2>
                <ul>
                    <li><strong>"MokiliEvent"</strong> désigne la plateforme de billetterie en ligne accessible via le site web mokilievent.com et ses applications associées.</li>
                    <li><strong>"Utilisateur"</strong> désigne toute personne physique ou morale qui accède et utilise la plateforme, qu'elle soit inscrite ou non.</li>
                    <li><strong>"Organisateur"</strong> désigne tout utilisateur qui crée, gère et/ou vend des billets pour des événements via la plateforme.</li>
                    <li><strong>"Participant"</strong> désigne tout utilisateur qui achète des billets ou réserve des places pour un événement.</li>
                    <li><strong>"Événement"</strong> désigne tout spectacle, conférence, concert, manifestation sportive ou tout autre rassemblement organisé via la plateforme.</li>
                </ul>

                <h2>3. Inscription et compte utilisateur</h2>
                <h3>3.1 Création de compte</h3>
                <p>
                    Pour utiliser certaines fonctionnalités de notre plateforme, notamment pour créer des événements 
                    ou acheter des billets, vous devez créer un compte. Vous devez :
                </p>
                <ul>
                    <li>Être âgé d'au moins 18 ans ou avoir l'autorisation d'un représentant légal</li>
                    <li>Fournir des informations exactes, complètes et à jour</li>
                    <li>Maintenir la sécurité de votre compte et de votre mot de passe</li>
                    <li>Notifier immédiatement MokiliEvent de toute utilisation non autorisée de votre compte</li>
                    <li>Être responsable de toutes les activités qui se produisent sous votre compte</li>
                </ul>

                <h3>3.2 Vérification</h3>
                <p>
                    Nous nous réservons le droit de vérifier l'identité de nos utilisateurs et de suspendre ou 
                    supprimer tout compte qui ne respecte pas nos conditions d'utilisation ou qui fournit des 
                    informations fausses ou trompeuses.
                </p>

                <h2>4. Utilisation de la plateforme</h2>
                <h3>4.1 Utilisation autorisée</h3>
                <p>Vous vous engagez à utiliser la plateforme uniquement à des fins légales et conformément à ces conditions. Vous acceptez de :</p>
                <ul>
                    <li>Respecter tous les lois et règlements applicables en République du Congo</li>
                    <li>Respecter les droits de propriété intellectuelle d'autrui</li>
                    <li>Ne pas utiliser la plateforme à des fins frauduleuses ou illégales</li>
                    <li>Ne pas perturber ou interférer avec le fonctionnement de la plateforme</li>
                    <li>Ne pas tenter d'accéder de manière non autorisée à des parties restreintes de la plateforme</li>
                </ul>

                <h3>4.2 Utilisations interdites</h3>
                <p>Il est strictement interdit de :</p>
                <ul>
                    <li>Utiliser la plateforme pour vendre des billets contrefaits ou non autorisés</li>
                    <li>Revendre des billets à des prix supérieurs au prix de vente initial sans autorisation</li>
                    <li>Utiliser des robots, scripts automatisés ou autres moyens pour accéder à la plateforme</li>
                    <li>Transmettre des virus, codes malveillants ou tout autre élément nuisible</li>
                    <li>Collecter des données personnelles d'autres utilisateurs sans leur consentement</li>
                    <li>Utiliser la plateforme pour harceler, menacer ou nuire à autrui</li>
                </ul>

                <h2>5. Événements et billetterie</h2>
                <h3>5.1 Responsabilités des organisateurs</h3>
                <p>En tant qu'organisateur, vous êtes responsable de :</p>
                <ul>
                    <li>L'exactitude et la complétude des informations concernant vos événements</li>
                    <li>Le respect de toutes les lois et réglementations applicables à votre événement</li>
                    <li>La gestion des billets, des accès et de la sécurité de votre événement</li>
                    <li>L'honneur de toutes les réservations et ventes de billets effectuées via la plateforme</li>
                    <li>Le remboursement des participants en cas d'annulation ou de modification de l'événement, conformément à votre politique de remboursement</li>
                    <li>L'obtention de toutes les autorisations, licences et assurances nécessaires</li>
                </ul>

                <h3>5.2 Responsabilités des participants</h3>
                <p>En tant que participant, vous êtes responsable de :</p>
                <ul>
                    <li>Vérifier les détails de l'événement avant d'acheter des billets</li>
                    <li>Arriver à l'heure à l'événement avec votre billet valide</li>
                    <li>Respecter les règles et conditions spécifiques à chaque événement</li>
                    <li>Ne pas revendre vos billets à des prix supérieurs sans autorisation</li>
                </ul>

                <h2>6. Paiements et transactions</h2>
                <h3>6.1 Paiements</h3>
                <p>
                    Les paiements sont traités de manière sécurisée via nos partenaires de paiement agréés 
                    (Airtel Money, MTN Mobile Money). MokiliEvent agit en tant qu'intermédiaire 
                    de paiement entre les organisateurs et les participants.
                </p>

                <h3>6.2 Frais de service</h3>
                <p>
                    Des frais de service peuvent s'appliquer aux transactions. Ces frais sont clairement indiqués 
                    avant la finalisation de votre achat. Les frais de service sont non remboursables sauf en cas 
                    d'annulation de l'événement par l'organisateur.
                </p>

                <h3>6.3 Remboursements</h3>
                <p>
                    Les conditions de remboursement sont définies par chaque organisateur et sont clairement indiquées 
                    lors de l'achat. En cas d'annulation d'un événement par l'organisateur, les remboursements seront 
                    traités conformément à la politique de remboursement de l'organisateur et aux lois applicables.
                </p>

                <h2>7. Propriété intellectuelle</h2>
                <p>
                    Tous les contenus de la plateforme MokiliEvent, y compris mais sans s'y limiter, les textes, 
                    graphiques, logos, icônes, images, clips audio, téléchargements numériques et compilations de données, 
                    sont la propriété de MokiliEvent ou de ses fournisseurs de contenu et sont protégés par les lois 
                    congolaises et internationales sur le droit d'auteur.
                </p>
                <p>
                    Vous ne pouvez pas reproduire, distribuer, modifier, créer des œuvres dérivées, afficher publiquement, 
                    représenter publiquement, republier, télécharger, stocker ou transmettre tout matériel de notre 
                    plateforme sans notre autorisation écrite préalable.
                </p>

                <h2>8. Limitation de responsabilité</h2>
                <p>
                    MokiliEvent agit en tant qu'intermédiaire entre les organisateurs et les participants. 
                    Nous ne sommes pas responsables :
                </p>
                <ul>
                    <li>Du déroulement, de la qualité ou de l'annulation des événements organisés par des tiers</li>
                    <li>Des dommages directs, indirects, accessoires, spéciaux ou consécutifs résultant de l'utilisation de la plateforme</li>
                    <li>Des pertes de données, de profits ou d'autres pertes intangibles</li>
                    <li>Des actes ou omissions des organisateurs ou des participants</li>
                </ul>
                <p>
                    Dans la mesure permise par la loi, la responsabilité totale de MokiliEvent envers vous ne dépassera 
                    jamais le montant que vous avez payé pour utiliser nos services au cours des 12 mois précédant 
                    la réclamation.
                </p>

                <h2>9. Protection des données personnelles</h2>
                <p>
                    Nous collectons et traitons vos données personnelles conformément à notre politique de confidentialité 
                    et aux lois applicables en République du Congo, notamment la loi n° 08-2001 du 20 novembre 2001 
                    portant protection des données à caractère personnel.
                </p>
                <p>
                    En utilisant notre plateforme, vous consentez à la collecte, au traitement et à l'utilisation de 
                    vos données personnelles comme décrit dans notre politique de confidentialité. Pour plus d'informations, 
                    consultez notre <a href="{{ route('privacy') }}" style="color: var(--bleu-nuit-clair); font-weight: 600;">politique de confidentialité</a>.
                </p>

                <h2>10. Modification des conditions</h2>
                <p>
                    Nous nous réservons le droit de modifier ces conditions d'utilisation à tout moment. 
                    Les modifications prendront effet dès leur publication sur cette page. Nous vous encourageons 
                    à consulter régulièrement cette page pour prendre connaissance des éventuelles modifications.
                </p>
                <p>
                    Votre utilisation continue de la plateforme après la publication de modifications constitue 
                    votre acceptation de ces modifications. Si vous n'acceptez pas les modifications, vous devez 
                    cesser d'utiliser la plateforme.
                </p>

                <h2>11. Résiliation</h2>
                <p>
                    Nous nous réservons le droit de suspendre ou de résilier votre accès à la plateforme à tout moment, 
                    sans préavis, pour violation de ces conditions d'utilisation ou pour toute autre raison que nous 
                    jugeons appropriée.
                </p>
                <p>
                    Vous pouvez résilier votre compte à tout moment en nous contactant ou en utilisant les fonctionnalités 
                    de suppression de compte disponibles dans vos paramètres.
                </p>

                <h2>12. Droit applicable et juridiction</h2>
                <p>
                    Ces conditions d'utilisation sont régies et interprétées conformément aux lois de la République du Congo. 
                    Tout litige découlant de ces conditions ou de l'utilisation de la plateforme sera soumis à la juridiction 
                    exclusive des tribunaux compétents de Brazzaville, République du Congo.
                </p>

                <h2>13. Dispositions générales</h2>
                <h3>13.1 Intégralité de l'accord</h3>
                <p>
                    Ces conditions d'utilisation, ainsi que notre politique de confidentialité, constituent l'intégralité 
                    de l'accord entre vous et MokiliEvent concernant l'utilisation de la plateforme.
                </p>

                <h3>13.2 Divisibilité</h3>
                <p>
                    Si une disposition de ces conditions est jugée invalide ou inapplicable, les autres dispositions 
                    resteront en vigueur.
                </p>

                <h3>13.3 Non-renonciation</h3>
                <p>
                    Le fait que MokiliEvent n'exerce pas un droit ou une disposition de ces conditions ne constitue 
                    pas une renonciation à ce droit ou à cette disposition.
                </p>

                <h2>14. Contact</h2>
                <p>
                    Pour toute question concernant ces conditions d'utilisation, vous pouvez nous contacter :
                </p>
                <ul>
                    <li><strong>Email :</strong> legal@mokilievent.com</li>
                    <li><strong>Téléphone :</strong> +242 06 408 8868</li>
                    <li><strong>Adresse :</strong> Brazzaville, République du Congo</li>
                </ul>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="terms-sidebar">
                <div class="sidebar-card">
                    <h3>Besoin d'aide ?</h3>
                    <p style="color: #666; line-height: 1.7; margin-bottom: 1.5rem;">
                        Notre équipe est là pour répondre à vos questions concernant nos conditions d'utilisation.
                    </p>
                    <a class="btn btn-primary rounded-pill py-3 px-5 w-100" href="{{ route('contact') }}">
                        <i class="fas fa-envelope me-2"></i>Contactez-nous
                    </a>
                </div>
                
                <div class="sidebar-card">
                    <h3>Documents légaux</h3>
                    <div class="d-flex flex-column">
                        <a class="sidebar-link" href="{{ route('privacy') }}">
                            <i class="fas fa-shield-alt"></i>
                            Politique de confidentialité
                        </a>
                        <a class="sidebar-link" href="{{ route('faq') }}">
                            <i class="fas fa-question-circle"></i>
                            FAQ
                        </a>
                        <a class="sidebar-link" href="{{ route('about') }}">
                            <i class="fas fa-info-circle"></i>
                            À propos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

