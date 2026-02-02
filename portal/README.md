# Portail MokiliEvent

Petit site PHP statique qui présente les informations publiques de MokiliEvent (accueil, blog, à propos, contact, conditions et confidentialité). Il permet de dissocier la communication institutionnelle de l’application principale.

## Structure

- `index.php` : page d’accueil/portail.
- `blog.php` : liste d’articles statiques.
- `about.php` : mission, vision, valeurs.
- `contact.php` : coordonnées + formulaire basique.
- `terms.php`, `privacy.php` et `faq.php` : sections légales/FAQ.
- `config.php`, `partials/`, `data/` : configuration et templates partagés.
- `assets/` : styles (`assets/css/`) et médias (logo dans `assets/images/`).

## Utilisation

1. Héberger le dossier `portal` sur un serveur PHP (>= 8.1 recommandé).
2. Configurer votre serveur web pour pointer vers `portal/`.
3. Adapter les textes ou liens dans `config.php`, `data/blog-posts.php` et les sections légales/FAQ si nécessaire.
4. Mettre à jour les médias dans `assets/images/` si vous souhaitez remplacer le logo.

Aucune dépendance externe n’est requise, tout est géré via PHP natif et CSS.

