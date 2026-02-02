<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

$pageSlug = 'privacy';
$pageTitle = 'Politique de confidentialité';
$lastUpdated = date('d/m/Y');

$privacySections = [
    [
        'title' => '1. Introduction',
        'paragraphs' => [
            "MokiliEvent (« nous ») protège votre vie privée et vos données personnelles. Cette politique explique comment nous collectons, utilisons et sécurisons vos informations lorsque vous utilisez notre plateforme de billetterie.",
            "Elle respecte la loi congolaise n° 08-2001, ainsi que les réglementations RGPD (UE) et NDPR (Nigeria) lorsque celles-ci sont applicables.",
        ],
    ],
    [
        'title' => '2. Données collectées',
        'subsections' => [
            [
                'subtitle' => '2.1 Données fournies directement',
                'list' => [
                    "Informations d’identification : nom, prénom, email, téléphone.",
                    "Informations de profil : entreprise, rôle, photo (optionnelle).",
                    "Informations de paiement : données de transaction via nos partenaires (nous ne stockons pas vos numéros de carte).",
                    "Informations évènementielles : événements créés, billets achetés, réservations.",
                    "Communications : messages envoyés à notre support.",
                ],
            ],
            [
                'subtitle' => '2.2 Données collectées automatiquement',
                'list' => [
                    "Données techniques : adresse IP, navigateur, OS, appareil.",
                    "Données d’utilisation : pages consultées, recherches, préférences.",
                    "Cookies et traceurs similaires (voir section 7).",
                ],
            ],
            [
                'subtitle' => '2.3 Données issues de tiers',
                'list' => [
                    "Partenaires de paiement pour confirmer une transaction.",
                    "Organisateurs d’événements pour gérer votre participation.",
                    "Services d’authentification sociale lorsque vous vous connectez via ces services.",
                ],
            ],
        ],
    ],
    [
        'title' => '3. Utilisation de vos données',
        'paragraphs' => [
            "Nous utilisons vos données uniquement pour fournir et améliorer nos services, assurer la sécurité et respecter nos obligations légales.",
        ],
    ],
];

$purposes = [
    ['purpose' => 'Gestion de compte et authentification', 'data' => 'Nom, email, téléphone, mot de passe', 'legal' => 'Exécution du contrat'],
    ['purpose' => 'Traitement des réservations/paiements', 'data' => 'Informations de paiement, transactions', 'legal' => 'Exécution du contrat'],
    ['purpose' => 'Communications (notifications, support)', 'data' => 'Email, téléphone', 'legal' => 'Exécution du contrat / Consentement'],
    ['purpose' => 'Recommandations personnalisées', 'data' => 'Historique de navigation, localisation', 'legal' => 'Consentement / intérêt légitime'],
    ['purpose' => 'Amélioration produit et statistiques', 'data' => 'Données d’usage anonymisées', 'legal' => 'Intérêt légitime'],
    ['purpose' => 'Prévention de la fraude et sécurité', 'data' => 'Adresse IP, données de connexion', 'legal' => 'Intérêt légitime / obligation légale'],
    ['purpose' => 'Respect des obligations légales', 'data' => 'Toutes données pertinentes', 'legal' => 'Obligation légale'],
];

$rights = [
    'Droit d’accès' => "Obtenir une copie de vos données personnelles.",
    'Droit de rectification' => "Mettre à jour des informations inexactes.",
    'Droit à l’effacement' => "Demander la suppression lorsque la loi le permet.",
    'Droit d’opposition' => "Vous opposer à certains traitements (marketing, recommandations).",
    'Droit à la portabilité' => "Recevoir vos données dans un format structuré.",
    'Retrait du consentement' => "Retirer votre consentement à tout moment pour les traitements basés sur celui-ci.",
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main>
    <section class="hero">
        <div class="container">
            <span class="badge">Protection des données</span>
            <h1>Politique de confidentialité MokiliEvent</h1>
            <p>Nous appliquons les réglementations locales et internationales pour garantir la sécurité de vos données.</p>
        </div>
    </section>
    <section class="section">
        <div class="container legal-layout">
            <article class="legal-content">
                <p class="last-updated">Dernière mise à jour : <?= htmlspecialchars($lastUpdated) ?></p>
                <?php foreach ($privacySections as $section): ?>
                    <h2><?= htmlspecialchars($section['title']) ?></h2>
                    <?php foreach ($section['paragraphs'] ?? [] as $paragraph): ?>
                        <p><?= htmlspecialchars($paragraph) ?></p>
                    <?php endforeach; ?>
                    <?php if (!empty($section['subsections'] ?? [])): ?>
                        <?php foreach ($section['subsections'] as $sub): ?>
                            <h3><?= htmlspecialchars($sub['subtitle']) ?></h3>
                            <?php foreach ($sub['paragraphs'] ?? [] as $paragraph): ?>
                                <p><?= htmlspecialchars($paragraph) ?></p>
                            <?php endforeach; ?>
                            <?php if (!empty($sub['list'] ?? [])): ?>
                                <ul>
                                    <?php foreach ($sub['list'] as $item): ?>
                                        <li><?= htmlspecialchars($item) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>

                <h2>3.1 Tableau des finalités</h2>
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Finalité</th>
                                <th>Données utilisées</th>
                                <th>Base légale</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purposes as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                                    <td><?= htmlspecialchars($row['data']) ?></td>
                                    <td><?= htmlspecialchars($row['legal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <h2>4. Partage de données</h2>
                <p>Nous ne vendons jamais vos données. Elles sont partagées uniquement avec les organisateurs concernés, nos partenaires de paiement, des prestataires techniques soumis à des clauses de confidentialité, ou sur obligation légale.</p>

                <h2>5. Sécurité</h2>
                <p>Chiffrement TLS, contrôle d’accès strict, surveillance active et sauvegardes régulières protègent vos informations. Notre personnel est formé aux bonnes pratiques de sécurité.</p>

                <h2>6. Conservation</h2>
                <p>Les données sont conservées pendant la durée de votre compte plus trois ans, sauf obligations légales plus longues (ex. comptabilité : 10 ans).</p>

                <h2>7. Cookies</h2>
                <p>Nous utilisons des cookies essentiels, de préférences, analytiques et fonctionnels. Vous pouvez les gérer via les paramètres de votre navigateur, en sachant que certaines fonctionnalités pourraient être limitées.</p>

                <h2>8. Vos droits</h2>
                <ul>
                    <?php foreach ($rights as $title => $detail): ?>
                        <li><strong><?= htmlspecialchars($title) ?> :</strong> <?= htmlspecialchars($detail) ?></li>
                    <?php endforeach; ?>
                </ul>
                <p>Pour exercer vos droits, contactez <strong>privacy@mokilievent.com</strong> ou adressez un courrier à MokiliEvent, Brazzaville (République du Congo). Nous répondons sous 30 jours.</p>

                <h2>9. Données des mineurs</h2>
                <p>La plateforme s’adresse aux personnes majeures. Nous supprimons toute donnée fournie par un mineur dès que cela est porté à notre connaissance.</p>

                <h2>10. Transferts internationaux</h2>
                <p>Les données sont principalement traitées en République du Congo. En cas de transfert vers un autre pays, nous appliquons des garanties contractuelles appropriées.</p>

                <h2>11. Modifications de cette politique</h2>
                <p>Nous pouvons mettre à jour cette politique. Toute modification importante est communiquée par email ou notification sur la plateforme.</p>

                <h2>12. Contact DPO</h2>
                <p>Pour toute question : <strong>privacy@mokilievent.com</strong> ou +242 06 408 8868.</p>
            </article>
            <aside class="legal-sidebar">
                <div class="legal-card">
                    <h3>Question sur vos données ?</h3>
                    <p>Contactez notre délégué à la protection des données.</p>
                    <a class="btn btn-secondary" href="contact.php">Contact direct</a>
                </div>
                <div class="legal-card">
                    <h3>Documents liés</h3>
                    <div class="legal-links">
                        <a href="terms.php">Conditions d’utilisation</a>
                        <a href="faq.php">FAQ</a>
                        <a href="about.php">À propos</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>

