@extends('layouts.app')

@section('title', 'Politique de confidentialité - MokiliEvent')

@section('content')
<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffffff;
}

.privacy-hero {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 3rem;
}

.privacy-content {
    background: white;
    border-radius: 16px;
    padding: 3rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

.privacy-content h2 {
    color: var(--bleu-nuit);
    font-size: 1.75rem;
    font-weight: 700;
    margin-top: 2.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e9ecef;
}

.privacy-content h2:first-child {
    margin-top: 0;
}

.privacy-content h3 {
    color: var(--bleu-nuit-clair);
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.privacy-content p {
    color: #555;
    line-height: 1.8;
    margin-bottom: 1rem;
}

.privacy-content ul, .privacy-content ol {
    color: #555;
    line-height: 1.8;
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.privacy-content li {
    margin-bottom: 0.5rem;
}

.privacy-sidebar {
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

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.data-table th,
.data-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.data-table th {
    background-color: #f8f9fa;
    color: var(--bleu-nuit);
    font-weight: 600;
}
</style>

<!-- Hero Section -->
<div class="privacy-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1 class="mb-3" style="font-size: 2.5rem; font-weight: 800;">Politique de confidentialité</h1>
                <p class="mb-0" style="font-size: 1.125rem; opacity: 0.9;">
                    Dernière mise à jour : {{ date('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Content -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="privacy-content">
                <p class="last-updated">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Dernière mise à jour : {{ date('d/m/Y') }}
                </p>

                <h2>1. Introduction</h2>
                <p>
                    MokiliEvent ("nous", "notre", "nos") s'engage à protéger votre vie privée et vos données personnelles. 
                    Cette politique de confidentialité explique comment nous collectons, utilisons, stockons et protégeons 
                    vos informations personnelles lorsque vous utilisez notre plateforme de billetterie en ligne.
                </p>
                <p>
                    Cette politique est conforme à la loi n° 08-2001 du 20 novembre 2001 de la République du Congo 
                    portant protection des données à caractère personnel, ainsi qu'aux meilleures pratiques internationales 
                    en matière de protection des données.
                </p>
                <p>
                    En utilisant notre plateforme, vous acceptez les pratiques décrites dans cette politique. 
                    Si vous n'acceptez pas cette politique, veuillez ne pas utiliser nos services.
                </p>

                <h2>2. Données que nous collectons</h2>
                <h3>2.1 Données que vous nous fournissez directement</h3>
                <p>Lors de votre inscription et de votre utilisation de la plateforme, nous collectons :</p>
                <ul>
                    <li><strong>Informations d'identification :</strong> nom, prénom, adresse email, numéro de téléphone</li>
                    <li><strong>Informations de profil :</strong> photo de profil (optionnelle), genre, tranche d'âge</li>
                    <li><strong>Informations de paiement :</strong> données de transaction via nos partenaires de paiement sécurisés (Airtel Money, MTN Mobile Money). Nous ne stockons pas vos numéros de carte bancaire complets.</li>
                    <li><strong>Informations d'événement :</strong> événements que vous créez, billets que vous achetez, réservations que vous effectuez</li>
                    <li><strong>Communications :</strong> messages que vous nous envoyez via nos formulaires de contact</li>
                </ul>

                <h3>2.2 Données collectées automatiquement</h3>
                <p>Lorsque vous utilisez notre plateforme, nous collectons automatiquement :</p>
                <ul>
                    <li><strong>Données de navigation :</strong> pages visitées, temps passé sur chaque page, liens cliqués</li>
                    <li><strong>Données d'utilisation :</strong> événements consultés, recherches effectuées, préférences affichées</li>
                    <li><strong>Données techniques :</strong> adresse IP, type de navigateur, système d'exploitation, appareil utilisé</li>
                    <li><strong>Cookies et technologies similaires :</strong> voir section 7 pour plus de détails</li>
                </ul>

                <h3>2.3 Données collectées auprès de tiers</h3>
                <p>Nous pouvons recevoir des informations de :</p>
                <ul>
                    <li>Nos partenaires de paiement concernant vos transactions</li>
                    <li>Les organisateurs d'événements concernant votre participation</li>
                    <li>Les services d'authentification sociale si vous vous connectez via ces services</li>
                </ul>

                <h2>3. Utilisation de vos données</h2>
                <p>Nous utilisons vos données personnelles pour les finalités suivantes :</p>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Finalité</th>
                            <th>Données utilisées</th>
                            <th>Base légale</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Gestion de votre compte et authentification</td>
                            <td>Nom, email, mot de passe, téléphone</td>
                            <td>Exécution du contrat</td>
                        </tr>
                        <tr>
                            <td>Traitement des réservations et paiements</td>
                            <td>Informations de paiement, données de transaction</td>
                            <td>Exécution du contrat</td>
                        </tr>
                        <tr>
                            <td>Communication avec vous (confirmations, notifications)</td>
                            <td>Email, téléphone</td>
                            <td>Exécution du contrat, consentement</td>
                        </tr>
                        <tr>
                            <td>Personnalisation de votre expérience (recommandations)</td>
                            <td>Historique de navigation, préférences, localisation</td>
                            <td>Consentement, intérêt légitime</td>
                        </tr>
                        <tr>
                            <td>Amélioration de nos services</td>
                            <td>Données d'utilisation, statistiques anonymisées</td>
                            <td>Intérêt légitime</td>
                        </tr>
                        <tr>
                            <td>Prévention de la fraude et sécurité</td>
                            <td>Adresse IP, données de connexion</td>
                            <td>Intérêt légitime, obligation légale</td>
                        </tr>
                        <tr>
                            <td>Respect des obligations légales</td>
                            <td>Toutes les données pertinentes</td>
                            <td>Obligation légale</td>
                        </tr>
                    </tbody>
                </table>

                <h3>3.1 Système de recommandation intelligent</h3>
                <p>
                    Nous utilisons vos données de navigation (événements consultés, catégories préférées, localisation) 
                    pour vous proposer des recommandations personnalisées sur la page d'accueil. Ces recommandations sont 
                    basées sur :
                </p>
                <ul>
                    <li>Les catégories d'événements que vous consultez le plus</li>
                    <li>Votre localisation et les villes où vous avez déjà consulté des événements</li>
                    <li>Les préférences d'utilisateurs similaires (filtrage collaboratif)</li>
                    <li>Les événements populaires si vous êtes nouveau sur la plateforme</li>
                </ul>
                <p>
                    Ces analyses sont effectuées de manière anonyme et sécurisée. Vous pouvez désactiver les recommandations 
                    personnalisées dans les paramètres de votre compte.
                </p>

                <h2>4. Partage de vos données</h2>
                <p>Nous ne vendons jamais vos données personnelles. Nous partageons vos données uniquement dans les cas suivants :</p>
                
                <h3>4.1 Avec les organisateurs d'événements</h3>
                <p>
                    Lorsque vous réservez un billet, nous partageons avec l'organisateur les informations nécessaires 
                    pour gérer votre participation : nom, prénom, email, numéro de téléphone, type et nombre de billets. 
                    L'organisateur est responsable de la protection de ces données conformément à la loi.
                </p>

                <h3>4.2 Avec nos partenaires de paiement</h3>
                <p>
                    Nous partageons les informations de paiement nécessaires avec nos partenaires agréés (Airtel Money, 
                    MTN Mobile Money) pour traiter vos transactions de manière sécurisée.
                </p>

                <h3>4.3 Avec nos prestataires de services</h3>
                <p>
                    Nous pouvons partager vos données avec des prestataires qui nous aident à exploiter la plateforme 
                    (hébergement, analyse, support client), sous réserve qu'ils respectent des obligations strictes de 
                    confidentialité.
                </p>

                <h3>4.4 Obligations légales</h3>
                <p>
                    Nous pouvons divulguer vos données si la loi l'exige ou en réponse à une demande légale valide 
                    (mandat, ordonnance judiciaire, etc.).
                </p>

                <h2>5. Protection de vos données</h2>
                <h3>5.1 Mesures de sécurité techniques</h3>
                <p>Nous mettons en œuvre des mesures de sécurité appropriées pour protéger vos données :</p>
                <ul>
                    <li><strong>Chiffrement :</strong> toutes les données sensibles sont chiffrées en transit (HTTPS/TLS) et au repos</li>
                    <li><strong>Authentification :</strong> mots de passe hachés, authentification à deux facteurs disponible</li>
                    <li><strong>Contrôle d'accès :</strong> accès restreint aux données personnelles, basé sur le principe du moindre privilège</li>
                    <li><strong>Surveillance :</strong> surveillance continue de nos systèmes pour détecter les intrusions et anomalies</li>
                    <li><strong>Sauvegardes :</strong> sauvegardes régulières et sécurisées de vos données</li>
                </ul>

                <h3>5.2 Mesures organisationnelles</h3>
                <ul>
                    <li>Formation de notre personnel à la protection des données</li>
                    <li>Politiques strictes de confidentialité pour tous les employés</li>
                    <li>Audits réguliers de sécurité</li>
                    <li>Procédures de gestion des incidents de sécurité</li>
                </ul>

                <h2>6. Conservation de vos données</h2>
                <p>Nous conservons vos données personnelles uniquement aussi longtemps que nécessaire pour :</p>
                <ul>
                    <li>Fournir nos services et gérer votre compte</li>
                    <li>Respecter nos obligations légales et réglementaires</li>
                    <li>Résoudre les litiges et faire respecter nos accords</li>
                </ul>
                <p>
                    En général, nous conservons vos données pendant la durée de votre compte plus une période de 3 ans 
                    après la fermeture de votre compte, sauf obligation légale de conservation plus longue. Les données 
                    de transaction sont conservées conformément aux exigences légales (généralement 10 ans).
                </p>

                <h2>7. Cookies et technologies similaires</h2>
                <h3>7.1 Types de cookies utilisés</h3>
                <p>Nous utilisons les types de cookies suivants :</p>
                <ul>
                    <li><strong>Cookies essentiels :</strong> nécessaires au fonctionnement de la plateforme (authentification, sécurité)</li>
                    <li><strong>Cookies de préférences :</strong> mémorisent vos choix (langue, région)</li>
                    <li><strong>Cookies analytiques :</strong> nous aident à comprendre comment vous utilisez la plateforme (Google Analytics)</li>
                    <li><strong>Cookies de fonctionnalité :</strong> permettent d'améliorer votre expérience (recommandations personnalisées)</li>
                </ul>

                <h3>7.2 Gestion des cookies</h3>
                <p>
                    Vous pouvez contrôler et gérer les cookies via les paramètres de votre navigateur. Notez que la 
                    désactivation de certains cookies peut affecter le fonctionnement de la plateforme.
                </p>

                <h2>8. Vos droits</h2>
                <p>Conformément à la loi congolaise sur la protection des données, vous disposez des droits suivants :</p>
                
                <h3>8.1 Droit d'accès</h3>
                <p>
                    Vous avez le droit d'obtenir une copie de vos données personnelles que nous détenons. 
                    Vous pouvez accéder à la plupart de vos données via votre compte, ou nous contacter pour une copie complète.
                </p>

                <h3>8.2 Droit de rectification</h3>
                <p>
                    Vous pouvez corriger ou mettre à jour vos données personnelles à tout moment via votre compte 
                    ou en nous contactant.
                </p>

                <h3>8.3 Droit à l'effacement</h3>
                <p>
                    Vous pouvez demander la suppression de vos données personnelles, sous réserve de nos obligations 
                    légales de conservation.
                </p>

                <h3>8.4 Droit d'opposition</h3>
                <p>
                    Vous pouvez vous opposer au traitement de vos données pour certaines finalités, notamment le marketing 
                    direct et les recommandations personnalisées.
                </p>

                <h3>8.5 Droit à la portabilité</h3>
                <p>
                    Vous pouvez demander à recevoir vos données dans un format structuré et couramment utilisé, 
                    ou demander leur transfert à un autre service.
                </p>

                <h3>8.6 Droit de retirer votre consentement</h3>
                <p>
                    Si le traitement est basé sur votre consentement, vous pouvez le retirer à tout moment. 
                    Cela n'affectera pas la licéité du traitement effectué avant le retrait.
                </p>

                <h3>8.7 Comment exercer vos droits</h3>
                <p>
                    Pour exercer vos droits, vous pouvez :
                </p>
                <ul>
                    <li>Utiliser les fonctionnalités disponibles dans votre compte</li>
                    <li>Nous contacter à l'adresse : <strong>privacy@mokilievent.com</strong></li>
                    <li>Nous écrire à : MokiliEvent, Brazzaville, République du Congo</li>
                </ul>
                <p>
                    Nous répondrons à votre demande dans un délai de 30 jours maximum, conformément à la loi.
                </p>

                <h2>9. Données des mineurs</h2>
                <p>
                    Notre plateforme est destinée aux personnes âgées de 18 ans et plus. Nous ne collectons pas 
                    sciemment de données personnelles de mineurs de moins de 18 ans. Si nous apprenons qu'un mineur 
                    nous a fourni des données personnelles, nous supprimerons ces informations dans les plus brefs délais.
                </p>

                <h2>10. Transferts internationaux</h2>
                <p>
                    Vos données sont principalement stockées et traitées en République du Congo. Si nous devons 
                    transférer vos données vers d'autres pays, nous nous assurons que des garanties appropriées 
                    sont en place pour protéger vos données conformément à cette politique.
                </p>

                <h2>11. Modifications de cette politique</h2>
                <p>
                    Nous pouvons modifier cette politique de confidentialité de temps à autre pour refléter les 
                    changements dans nos pratiques ou pour d'autres raisons opérationnelles, légales ou réglementaires. 
                    Nous vous informerons de tout changement important par email ou via une notification sur la plateforme.
                </p>
                <p>
                    La date de la dernière mise à jour est indiquée en haut de cette page. Nous vous encourageons 
                    à consulter régulièrement cette politique pour rester informé de la façon dont nous protégeons vos données.
                </p>

                <h2>12. Contact et délégué à la protection des données</h2>
                <p>
                    Pour toute question, préoccupation ou demande concernant cette politique de confidentialité ou 
                    le traitement de vos données personnelles, vous pouvez nous contacter :
                </p>
                <ul>
                    <li><strong>Email :</strong> privacy@mokilievent.com</li>
                    <li><strong>Téléphone :</strong> +242 06 408 8868</li>
                    <li><strong>Adresse :</strong> MokiliEvent, Brazzaville, République du Congo</li>
                </ul>
                <p>
                    Nous nous engageons à répondre à toutes vos demandes dans les meilleurs délais et conformément 
                    à la législation applicable.
                </p>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="privacy-sidebar">
                <div class="sidebar-card">
                    <h3>Questions sur vos données ?</h3>
                    <p style="color: #666; line-height: 1.7; margin-bottom: 1.5rem;">
                        Pour toute question concernant vos données personnelles ou pour exercer vos droits, 
                        contactez notre délégué à la protection des données.
                    </p>
                    <a class="btn btn-primary rounded-pill py-3 px-5 w-100" href="{{ route('contact') }}">
                        <i class="fas fa-envelope me-2"></i>Contactez-nous
                    </a>
                </div>
                
                <div class="sidebar-card">
                    <h3>Documents légaux</h3>
                    <div class="d-flex flex-column">
                        <a class="sidebar-link" href="{{ route('terms') }}">
                            <i class="fas fa-file-contract"></i>
                            Conditions d'utilisation
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

