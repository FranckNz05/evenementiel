<?php

declare(strict_types=1);

$site = [
    'name' => 'MokiliEvent',
    'tagline' => 'Le portail officiel des expériences africaines',
    'baseline' => 'Découvrez, partagez et faites rayonner les événements culturels, professionnels et festifs de tout le continent.',
    'platform_url' => 'https://mokilievent.com',
    'contact' => [
        'email' => 'contact@mokilievent.com',
        'phone' => '+242 06 555 0101',
        'whatsapp' => '+242 06 222 0000',
        'address' => 'Immeuble Kasaï, Boulevard Denis Sassou Nguesso, Brazzaville, Congo',
        'support_hours' => 'Lun - Ven, 9h00 - 18h00 (GMT+1)',
    ],
    'social' => [
        'facebook' => 'https://www.facebook.com/mokilievent',
        'instagram' => 'https://www.instagram.com/mokilievent',
        'linkedin' => 'https://www.linkedin.com/company/mokilievent',
        'youtube' => 'https://www.youtube.com/@mokilievent',
    ],
    'values' => [
        'Transparence et sécurité pour les organisateurs comme pour les participants.',
        'Technologie pensée pour la mobilité et des connexions même à faible bande passante.',
        'Accompagnement humain avec des experts évènementiels basés en Afrique et dans la diaspora.',
    ],
    'highlights' => [
        ['value' => '2 300+', 'label' => 'événements répertoriés'],
        ['value' => '120 000+', 'label' => 'billets générés'],
        ['value' => '14 pays', 'label' => 'déjà couverts'],
        ['value' => '98%', 'label' => 'satisfaction partenaires'],
    ],
    'legal_links' => [
        ['label' => 'Conditions d’utilisation', 'href' => 'terms.php'],
        ['label' => 'Politique de confidentialité', 'href' => 'privacy.php'],
        ['label' => 'FAQ', 'href' => 'faq.php'],
    ],
];

$navItems = [
    ['label' => 'Accueil', 'href' => 'index.php', 'slug' => 'home'],
    ['label' => 'Blog', 'href' => 'blog.php', 'slug' => 'blog'],
    ['label' => 'À propos', 'href' => 'about.php', 'slug' => 'about'],
    ['label' => 'Contact', 'href' => 'contact.php', 'slug' => 'contact'],
    ['label' => 'Conditions', 'href' => 'terms.php', 'slug' => 'terms'],
    ['label' => 'Confidentialité', 'href' => 'privacy.php', 'slug' => 'privacy'],
    ['label' => 'FAQ', 'href' => 'faq.php', 'slug' => 'faq'],
    [
        'label' => 'Accéder à la plateforme évènementielle',
        'href' => 'https://mokilievent.com',
        'slug' => 'platform',
        'target' => '_blank',
        'class' => 'nav-cta',
        'rel' => 'noreferrer',
    ],
];

function asset(string $path): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if ($base === '.' || $base === '\\') {
        $base = '';
    }

    return $base . '/' . ltrim($path, '/');
}

