<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

$pageSlug = 'home';
$pageTitle = 'Portail officiel';
$heroCtas = [
    [
        'label' => 'Accéder à la plateforme',
        'href' => $site['platform_url'],
        'class' => '',
        'target' => '_blank',
        'rel' => 'noreferrer',
    ],
    [
        'label' => 'Parler à un expert',
        'href' => 'contact.php#form',
        'class' => 'btn-secondary',
    ],
];

$pillars = [
    [
        'title' => 'Billetterie multicanale',
        'text' => 'Paiement carte, mobile money, cashless et QR check-in, le tout sécurisé.',
    ],
    [
        'title' => 'Pilotage temps réel',
        'text' => 'Dashboards, exports automatisés et API pour vos outils financiers.',
    ],
    [
        'title' => 'Rayonnement panafricain',
        'text' => 'Amplification média, ambassadeurs et mise en avant sur mokilievent.com.',
    ],
];

$insights = [
    [
        'title' => 'Actualités & blog',
        'text' => 'Études de cas, tendances marché, conseils pour les organisateurs et partenaires.',
        'link' => ['label' => 'Explorer les articles', 'href' => 'blog.php'],
    ],
    [
        'title' => 'Documentation légale',
        'text' => 'Conditions d’utilisation, politique de confidentialité, conformité RGPD/NDPR.',
        'link' => ['label' => 'Consulter les conditions', 'href' => 'terms.php'],
    ],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main>
    <section class="hero">
        <div class="container">
            <span class="badge">Portail MokiliEvent</span>
            <h1>Le hub institutionnel de notre écosystème évènementiel</h1>
            <p><?= htmlspecialchars($site['baseline']) ?></p>
            <div class="hero-actions">
                <?php foreach ($heroCtas as $cta): ?>
                    <a
                        class="btn <?= htmlspecialchars($cta['class']) ?>"
                        href="<?= htmlspecialchars($cta['href']) ?>"
                        <?= isset($cta['target']) ? 'target="' . htmlspecialchars($cta['target']) . '"' : '' ?>
                        <?= isset($cta['rel']) ? 'rel="' . htmlspecialchars($cta['rel']) . '"' : '' ?>
                    >
                        <?= htmlspecialchars($cta['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="metrics">
                <?php foreach ($site['highlights'] as $stat): ?>
                    <div class="metric">
                        <strong><?= htmlspecialchars($stat['value']) ?></strong>
                        <span><?= htmlspecialchars($stat['label']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Pourquoi un portail séparé ?</h2>
                <p>Nous dissocions communication institutionnelle et expérience applicative afin de clarifier notre proposition pour les organisateurs, sponsors et médias.</p>
            </div>
            <div class="grid grid-3">
                <?php foreach ($pillars as $pillar): ?>
                    <article class="card">
                        <h3><?= htmlspecialchars($pillar['title']) ?></h3>
                        <p><?= htmlspecialchars($pillar['text']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container grid grid-2">
            <?php foreach ($insights as $card): ?>
                <article class="card">
                    <h3><?= htmlspecialchars($card['title']) ?></h3>
                    <p><?= htmlspecialchars($card['text']) ?></p>
                    <a class="btn btn-secondary" href="<?= htmlspecialchars($card['link']['href']) ?>">
                        <?= htmlspecialchars($card['link']['label']) ?>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>

