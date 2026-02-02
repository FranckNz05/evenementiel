<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

$pageSlug = 'about';
$pageTitle = 'À propos';

$timeline = [
    ['year' => '2019', 'text' => 'Lancement de MokiliEvent à Brazzaville et premiers événements culturels.'],
    ['year' => '2021', 'text' => 'Extension à 5 pays, introduction du cashless et des APIs partenaires.'],
    ['year' => '2023', 'text' => 'Ouverture de hubs à Kinshasa et Dakar, 50 000 billets émis.'],
    ['year' => '2025', 'text' => 'Portail public, observatoire panafricain et marketplace sponsors.'],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main>
    <section class="hero">
        <div class="container">
            <span class="badge">Manifeste</span>
            <h1>Digitaliser les événements africains sans perdre l’âme locale</h1>
            <p>MokiliEvent connecte organisateurs, sponsors et communautés via une technologie fiable, inclusive et optimisée pour les réalités du terrain.</p>
        </div>
    </section>
    <section class="section">
        <div class="container grid grid-2">
            <article class="card">
                <h2>Mission</h2>
                <p>Offrir aux organisateurs africains les mêmes standards de distribution, d’analyse et d’expérience que les plus grands événements mondiaux.</p>
            </article>
            <article class="card">
                <h2>Vision</h2>
                <p>Devenir la référence panafricaine pour découvrir, réserver et amplifier les expériences culturelles, sportives et professionnelles.</p>
            </article>
        </div>
    </section>
    <section class="section">
        <div class="container card">
            <h2 class="section-title">Nos valeurs</h2>
            <ul>
                <?php foreach ($site['values'] as $value): ?>
                    <li><?= htmlspecialchars($value) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <section class="section">
        <div class="container card">
            <h2 class="section-title">Repères temporels</h2>
            <div class="grid grid-2">
                <?php foreach ($timeline as $entry): ?>
                    <div>
                        <strong><?= htmlspecialchars($entry['year']) ?></strong>
                        <p><?= htmlspecialchars($entry['text']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>

