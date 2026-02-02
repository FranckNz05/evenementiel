<?php

declare(strict_types=1);

?>
<header class="site-header">
    <div class="container">
        <a href="index.php" class="branding">
            <span class="logo">
                <img src="<?= asset('assets/images/logo.png') ?>" alt="Logo MokiliEvent" loading="lazy">
            </span>
            <span class="logo-text">
                <?= htmlspecialchars($site['name']) ?>
                <small class="tagline"><?= htmlspecialchars($site['tagline']) ?></small>
            </span>
        </a>
        <nav class="site-nav" aria-label="Navigation principale">
            <button class="nav-toggle" data-js="nav-toggle" aria-label="Ouvrir le menu">☰</button>
            <ul>
                <?php foreach ($navItems as $item): ?>
                    <?php
                        $stateClass = (($pageSlug ?? '') === $item['slug']) ? 'is-active' : '';
                        $itemClass = trim(($item['class'] ?? '') . ' ' . $stateClass);
                        $target = $item['target'] ?? null;
                        $rel = $item['rel'] ?? null;
                    ?>
                    <li <?= $itemClass !== '' ? 'class="' . htmlspecialchars($itemClass) . '"' : '' ?>>
                        <a
                            href="<?= htmlspecialchars($item['href']) ?>"
                            <?= $target ? 'target="' . htmlspecialchars($target) . '"' : '' ?>
                            <?= $rel ? 'rel="' . htmlspecialchars($rel) . '"' : '' ?>
                        >
                            <?= htmlspecialchars($item['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</header>

