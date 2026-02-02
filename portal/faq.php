<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

$pageSlug = 'faq';
$pageTitle = 'FAQ';

$faqSections = [
    [
        'title' => 'Questions générales',
        'items' => [
            [
                'question' => "Qu'est-ce que MokiliEvent ?",
                'answer' => "MokiliEvent est la première plateforme de billetterie et de gestion d’événements au Congo. Elle permet aux organisateurs de créer leurs événements, de vendre des billets et aux participants d’acheter en ligne en toute sécurité.",
            ],
            [
                'question' => 'Comment acheter un billet ?',
                'answer' => "Créez un compte, choisissez l’événement qui vous intéresse, sélectionnez vos billets puis validez le paiement par mobile money ou carte.",
            ],
        ],
    ],
    [
        'title' => 'Pour les participants',
        'items' => [
            [
                'question' => 'Comment recevoir mes billets ?',
                'answer' => "Après l’achat, vos billets sont disponibles dans votre espace personnel et envoyés par email au format PDF avec QR code.",
            ],
            [
                'question' => 'Puis-je être remboursé ?',
                'answer' => "Les remboursements dépendent de la politique définie par l’organisateur. Elle est affichée avant le paiement.",
            ],
        ],
    ],
    [
        'title' => 'Pour les organisateurs',
        'items' => [
            [
                'question' => 'Comment créer un événement ?',
                'answer' => "Demandez un accès organisateur. Une fois validé, vous pourrez créer des événements, configurer vos billets et suivre les ventes depuis votre tableau de bord.",
            ],
            [
                'question' => 'Quels sont les frais ?',
                'answer' => "Nous appliquons une commission sur chaque billet vendu. Contactez notre équipe commerciale pour obtenir la grille personnalisée.",
            ],
        ],
    ],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main>
    <section class="hero">
        <div class="container">
            <span class="badge">FAQ</span>
            <h1>Questions fréquentes</h1>
            <p>Retrouvez les réponses aux questions les plus courantes sur la plateforme MokiliEvent, côté participants et organisateurs.</p>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <?php foreach ($faqSections as $section): ?>
                <div class="section-header" style="text-align:left;">
                    <h2><?= htmlspecialchars($section['title']) ?></h2>
                </div>
                <div class="accordion">
                    <?php foreach ($section['items'] as $item): ?>
                        <details class="accordion-item">
                            <summary><?= htmlspecialchars($item['question']) ?></summary>
                            <div class="content">
                                <p><?= htmlspecialchars($item['answer']) ?></p>
                            </div>
                        </details>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="section">
        <div class="container card">
            <h2>Vous n’avez pas trouvé votre réponse ?</h2>
            <p>Notre équipe reste disponible pour toute question complémentaire.</p>
            <a class="btn" href="contact.php">Contactez-nous</a>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>

