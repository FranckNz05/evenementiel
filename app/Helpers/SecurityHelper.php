<?php

namespace App\Helpers;

use HTMLPurifier;
use HTMLPurifier_Config;

class SecurityHelper
{
    /**
     * Nettoie le HTML en supprimant les scripts malveillants mais en conservant le formatage sûr
     *
     * @param string|null $html
     * @return string
     */
    public static function sanitizeHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Configuration de HTMLPurifier pour permettre un HTML basique mais sûr
        $config = HTMLPurifier_Config::createDefault();
        
        // Permettre certaines balises HTML sûres
        $config->set('HTML.Allowed', 'p,br,strong,em,u,h1,h2,h3,h4,h5,h6,ul,ol,li,a[href|title|target],img[src|alt|width|height],blockquote,code,pre,span[style],div[style],table,thead,tbody,tr,th,td');
        
        // Permettre certains attributs CSS sûrs
        $config->set('CSS.AllowedProperties', 'color,background-color,font-size,font-weight,text-align,margin,padding,border');
        
        // Forcer les liens à s'ouvrir dans un nouvel onglet de manière sécurisée
        $config->set('HTML.TargetBlank', true);
        $config->set('HTML.Nofollow', true);
        
        // Permettre les iframes uniquement pour YouTube, Vimeo et Google Maps
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?(\/\/www\.youtube(?:-nocookie)?\.com\/embed\/|\/\/player\.vimeo\.com\/|\/\/www\.google\.com\/maps\/)%');
        
        // Définir le cache - avec gestion des erreurs
        $cacheDir = storage_path('app/purifier');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        // Vérifier et créer le dossier si nécessaire
        if (!is_writable($cacheDir)) {
            // Si on ne peut pas écrire, désactiver le cache
            $config->set('Cache.DefinitionImpl', null);
        } else {
            $config->set('Cache.SerializerPath', $cacheDir);
        }
        
        // Encoder les entités HTML
        $config->set('Core.Encoding', 'UTF-8');
        
        $purifier = new HTMLPurifier($config);
        
        return $purifier->purify($html);
    }

    /**
     * Échappe complètement le HTML (pour les contenus qui ne devraient pas contenir de HTML)
     *
     * @param string|null $text
     * @return string
     */
    public static function escape(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
    }

    /**
     * Nettoie les entrées utilisateur pour SQL LIKE
     *
     * @param string $value
     * @return string
     */
    public static function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    /**
     * Valide et nettoie une URL
     *
     * @param string|null $url
     * @return string|null
     */
    public static function sanitizeUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Valider l'URL
        $cleaned = filter_var($url, FILTER_SANITIZE_URL);
        
        if (filter_var($cleaned, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        // S'assurer que l'URL utilise un protocole sûr
        $parsed = parse_url($cleaned);
        if (!isset($parsed['scheme']) || !in_array($parsed['scheme'], ['http', 'https', 'mailto', 'tel'])) {
            return null;
        }

        return $cleaned;
    }

    /**
     * Génère un token CSRF sécurisé
     *
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Valide un email de manière stricte
     *
     * @param string $email
     * @return bool
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Limite le taux de requêtes (rate limiting personnalisé)
     *
     * @param string $key
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @return bool
     */
    public static function tooManyAttempts(string $key, int $maxAttempts = 5, int $decayMinutes = 1): bool
    {
        $cache = app('cache');
        $attempts = $cache->get($key, 0);
        
        if ($attempts >= $maxAttempts) {
            return true;
        }
        
        $cache->put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        return false;
    }

    /**
     * Nettoie le nom de fichier pour éviter les injections de chemin
     *
     * @param string $filename
     * @return string
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Supprimer les caractères dangereux
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Supprimer les points multiples qui pourraient être utilisés pour traverser les répertoires
        $filename = preg_replace('/\.+/', '.', $filename);
        
        // Limiter la longueur
        return substr($filename, 0, 255);
    }
}

