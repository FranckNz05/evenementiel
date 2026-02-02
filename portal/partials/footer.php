<?php

declare(strict_types=1);

?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-column">
            <strong><?= htmlspecialchars($site['name']) ?></strong>
            <p style="margin:0.6rem 0 0; max-width:260px;"><?= htmlspecialchars($site['baseline']) ?></p>
        </div>

        <div class="footer-column">
            <h4>Contact</h4>
            <ul class="footer-links">
                <li><a href="mailto:<?= htmlspecialchars($site['contact']['email']) ?>"><?= htmlspecialchars($site['contact']['email']) ?></a></li>
                <li><a href="tel:<?= htmlspecialchars($site['contact']['phone']) ?>"><?= htmlspecialchars($site['contact']['phone']) ?></a></li>
                <li><?= htmlspecialchars($site['contact']['address']) ?></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Ressources</h4>
            <ul class="footer-links">
                <?php foreach ($site['legal_links'] as $link): ?>
                    <li><a href="<?= htmlspecialchars($link['href']) ?>"><?= htmlspecialchars($link['label']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Suivez-nous</h4>
            <ul class="social-links">
                <?php foreach ($site['social'] as $label => $url): ?>
                    <li><a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noreferrer"><?= ucfirst($label) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="container footer-bottom">
        <div>© <?= date('Y') ?> <?= htmlspecialchars($site['name']) ?> — Tous droits réservés.</div>
        <div>Portail d’information officiel, distinct de l’application évènementielle.</div>
    </div>
</footer>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-js=\"nav-toggle\"]');
    const nav = document.querySelector('.site-nav ul');
    if (toggle && nav) {
        toggle.addEventListener('click', () => nav.classList.toggle('is-open'));
    }
});
</script>
</body>
</html>

