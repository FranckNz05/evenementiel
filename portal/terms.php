<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

$pageSlug = 'terms';
$pageTitle = 'Conditions d’utilisation';
$lastUpdated = date('d/m/Y');

$termsSections = [
    [
        'title' => '1. Introduction et acceptation',
        'paragraphs' => [
            "Bienvenue sur MokiliEvent, plateforme de billetterie et de gestion d'événements opérant en République du Congo. En accédant à notre portail ou à l'application, vous acceptez les présentes conditions. Si vous ne les acceptez pas, vous devez cesser d'utiliser nos services.",
            "Ces conditions sont régies par les lois de la République du Congo et complètent vos éventuels contrats (licence, personnalisation, régie ou billetterie).",
        ],
    ],
    [
        'title' => '2. Définitions',
        'list' => [
            '"MokiliEvent" désigne la plateforme accessible via mokilievent.com et ses applications.',
            '"Utilisateur" désigne toute personne qui accède aux services, inscrite ou non.',
            '"Organisateur" désigne l’utilisateur qui crée et vend des billets.',
            '"Participant" désigne l’utilisateur qui achète des billets ou réserve des places.',
            '"Événement" désigne tout rendez-vous géré via la plateforme.',
        ],
    ],
    [
        'title' => '3. Inscription et compte utilisateur',
        'subsections' => [
            [
                'subtitle' => '3.1 Création de compte',
                'paragraphs' => [
                    "Pour créer un événement ou acheter un billet, vous devez disposer d'un compte valide : être majeur ou autorisé, fournir des informations exactes et maintenir la sécurité de vos identifiants.",
                ],
            ],
            [
                'subtitle' => '3.2 Vérification',
                'paragraphs' => [
                    "MokiliEvent peut vérifier votre identité, suspendre ou supprimer tout compte fournissant des informations trompeuses ou contraire aux présentes conditions.",
                ],
            ],
        ],
    ],
    [
        'title' => '4. Utilisation de la plateforme',
        'subsections' => [
            [
                'subtitle' => '4.1 Utilisation autorisée',
                'paragraphs' => [
                    "Vous vous engagez à respecter la loi, les droits de propriété intellectuelle, à ne pas utiliser la plateforme à des fins frauduleuses ni perturber son fonctionnement.",
                ],
                'list' => [
                    'Respecter les lois congolaises et internationales applicables.',
                    'Ne pas tenter d’accéder de manière non autorisée aux zones restreintes.',
                    'Ne pas collecter de données personnelles sans consentement.',
                ],
            ],
            [
                'subtitle' => '4.2 Utilisations interdites',
                'list' => [
                    'Vendre des billets contrefaits ou non autorisés.',
                    'Revendre des billets à un prix supérieur sans accord.',
                    'Transmettre des virus ou codes malveillants.',
                    'Harceler ou nuire à d’autres utilisateurs.',
                ],
            ],
        ],
    ],
    [
        'title' => '5. Événements et billetterie',
        'subsections' => [
            [
                'subtitle' => '5.1 Responsabilités des organisateurs',
                'list' => [
                    'Fournir des informations exactes sur les événements.',
                    'Respecter les lois locales et gérer la sécurité.',
                    'Honorer les ventes et remboursements selon la politique affichée.',
                    'Obtenir licences, assurances et autorisations nécessaires.',
                ],
            ],
            [
                'subtitle' => '5.2 Responsabilités des participants',
                'list' => [
                    'Vérifier les détails avant l’achat.',
                    'Présenter un billet valide et respecter les règles de l’événement.',
                    'Ne pas revendre les billets sans autorisation.',
                ],
            ],
        ],
    ],
    [
        'title' => '6. Paiements et transactions',
        'subsections' => [
            [
                'subtitle' => '6.1 Paiements',
                'paragraphs' => [
                    "Les paiements sont traités via Airtel Money, MTN Mobile Money ou tout partenaire agréé. MokiliEvent agit en tant qu'intermédiaire entre organisateurs et participants.",
                ],
            ],
            [
                'subtitle' => '6.2 Frais de service',
                'paragraphs' => [
                    "Des frais peuvent s'appliquer et sont affichés avant la validation. Ils sont non remboursables sauf annulation de l’événement.",
                ],
            ],
            [
                'subtitle' => '6.3 Remboursements',
                'paragraphs' => [
                    "Les politiques de remboursement sont définies par chaque organisateur. En cas d'annulation, les remboursements suivent les lois applicables et la politique communiquée.",
                ],
            ],
        ],
    ],
    [
        'title' => '7. Propriété intellectuelle',
        'paragraphs' => [
            "Les contenus (textes, logos, visuels, bases de données) appartiennent à MokiliEvent ou à ses fournisseurs de contenu. Toute reproduction ou diffusion nécessite une autorisation écrite.",
        ],
    ],
    [
        'title' => '8. Limitation de responsabilité',
        'paragraphs' => [
            "MokiliEvent agit en intermédiaire et n’est pas responsable du déroulé des événements organisés par des tiers, ni des dommages indirects, perte de profits ou données. La responsabilité totale est limitée aux montants payés durant les 12 derniers mois.",
        ],
    ],
    [
        'title' => '9. Protection des données personnelles',
        'paragraphs' => [
            "Nous collectons et traitons vos données conformément à la loi congolaise n° 08-2001 et à notre politique de confidentialité. Consultez la section dédiée pour plus d'informations.",
        ],
    ],
    [
        'title' => '10. Modification des conditions',
        'paragraphs' => [
            "MokiliEvent peut modifier les présentes conditions. Les changements prennent effet dès leur publication. L’usage continu vaut acceptation.",
        ],
    ],
    [
        'title' => '11. Résiliation',
        'paragraphs' => [
            "Nous pouvons suspendre ou résilier un compte en cas de violation des conditions. Vous pouvez supprimer votre compte en nous contactant ou via vos paramètres.",
        ],
    ],
    [
        'title' => '12. Droit applicable et juridiction',
        'paragraphs' => [
            "Le droit congolais s'applique et les tribunaux compétents de Brazzaville sont seuls compétents en cas de litige, sauf accord spécifique.",
        ],
    ],
    [
        'title' => '13. Dispositions générales',
        'subsections' => [
            [
                'subtitle' => '13.1 Intégralité de l’accord',
                'paragraphs' => [
                    "Les présentes conditions et la politique de confidentialité constituent l’accord complet entre vous et MokiliEvent.",
                ],
            ],
            [
                'subtitle' => '13.2 Divisibilité',
                'paragraphs' => [
                    "Si une disposition est jugée invalide, les autres restent en vigueur.",
                ],
            ],
            [
                'subtitle' => '13.3 Non-renonciation',
                'paragraphs' => [
                    "Le fait de ne pas exercer un droit ne vaut pas renonciation.",
                ],
            ],
        ],
    ],
    [
        'title' => '14. Contact',
        'paragraphs' => [
            "Pour toute question relative aux présentes conditions, contactez legal@mokilievent.com ou +242 06 408 8868.",
        ],
    ],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main>
    <section class="hero">
        <div class="container">
            <span class="badge">Cadre contractuel</span>
            <h1>Conditions générales d’utilisation de MokiliEvent</h1>
            <p>Applicables au portail d’information et à la plateforme applicative principale. Elles complètent vos contrats spécifiques (licence, régie, personnalisation).</p>
        </div>
    </section>
    <section class="section">
        <div class="container legal-layout">
            <article class="legal-content">
                <p class="last-updated">Dernière mise à jour : <?= htmlspecialchars($lastUpdated) ?></p>
                <?php foreach ($termsSections as $section): ?>
                    <h2><?= htmlspecialchars($section['title']) ?></h2>
                    <?php if (!empty($section['paragraphs'] ?? [])): ?>
                        <?php foreach ($section['paragraphs'] as $paragraph): ?>
                            <p><?= htmlspecialchars($paragraph) ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (!empty($section['list'] ?? [])): ?>
                        <ul>
                            <?php foreach ($section['list'] as $item): ?>
                                <li><?= htmlspecialchars($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
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
            </article>
            <aside class="legal-sidebar">
                <div class="legal-card">
                    <h3>Besoin d’aide ?</h3>
                    <p>Notre équipe juridique répond à vos questions contractuelles.</p>
                    <a class="btn btn-secondary" href="contact.php">Contacter l’équipe</a>
                </div>
                <div class="legal-card">
                    <h3>Documents légaux</h3>
                    <div class="legal-links">
                        <a href="privacy.php">Politique de confidentialité</a>
                        <a href="faq.php">FAQ</a>
                        <a href="about.php">À propos</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>

