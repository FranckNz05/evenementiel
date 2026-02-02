<?php

declare(strict_types=1);

require __DIR__ . '/config.php';
$posts = require __DIR__ . '/data/blog-posts.php';

$pageSlug = 'blog';
$pageTitle = 'Blog & insights';

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main>
    <section class="hero">
        <div class="container">
            <span class="badge">Blog MokiliEvent</span>
            <h1>Analyses, tendances et retours terrain</h1>
            <p>Produit, Customer Success, Observatoire : nos équipes partagent les bonnes pratiques pour réussir vos événements en Afrique.</p>
        </div>
    </section>
    <section class="section">
        <div class="container blog-list grid grid-2">
            <?php foreach ($posts as $post): ?>
                <article class="card">
                    <p class="blog-meta">
                        <span><?= htmlspecialchars($post['category']) ?></span>
                        <span>•</span>
                        <span><?= htmlspecialchars($post['date']) ?></span>
                        <span>•</span>
                        <span><?= htmlspecialchars($post['reading_time']) ?></span>
                    </p>
                    <h2><?= htmlspecialchars($post['title']) ?></h2>
                    <p><?= htmlspecialchars($post['excerpt']) ?></p>
                    <div class="blog-meta">
                        <span>Par <?= htmlspecialchars($post['author']) ?></span>
                    </div>
                    <a class="btn btn-secondary" href="#!" role="button">Lire l’article complet</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>

