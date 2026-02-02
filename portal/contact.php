<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

$pageSlug = 'contact';
$pageTitle = 'Contact';

$channels = [
    ['label' => 'Support général', 'detail' => $site['contact']['email']],
    ['label' => 'Partenariats & sponsoring', 'detail' => 'partners@mokilievent.com'],
    ['label' => 'WhatsApp Business', 'detail' => $site['contact']['whatsapp']],
    ['label' => 'Téléphone', 'detail' => $site['contact']['phone']],
    ['label' => 'Adresse', 'detail' => $site['contact']['address']],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main>
    <section class="hero">
        <div class="container">
            <span class="badge">Entrons en relation</span>
            <h1>Des équipes disponibles pour vos projets évènementiels</h1>
            <p>Nous répondons en moins de 24h ouvrées. Mentionnez le type d’événement, la localisation et vos contraintes pour une réponse ciblée.</p>
        </div>
    </section>
    <section class="section">
        <div class="container contact-grid">
            <article class="contact-card">
                <h2>Coordonnées directes</h2>
                <p>Horaires : <?= htmlspecialchars($site['contact']['support_hours']) ?></p>
                <ul class="footer-links">
                    <?php foreach ($channels as $channel): ?>
                        <li>
                            <strong><?= htmlspecialchars($channel['label']) ?></strong><br>
                            <span><?= htmlspecialchars($channel['detail']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <article class="contact-card" id="form">
                <h2>Écrire à MokiliEvent</h2>
                <form method="post" action="#form">
                    <input type="text" name="name" placeholder="Nom complet" required>
                    <input type="email" name="email" placeholder="Email professionnel" required>
                    <input type="text" name="company" placeholder="Organisation / Marque">
                    <textarea name="message" placeholder="Comment pouvons-nous vous aider ?" required></textarea>
                    <button class="btn" type="submit">Envoyer la demande</button>
                    <small>Les informations saisies servent uniquement à vous recontacter.</small>
                </form>
            </article>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>

