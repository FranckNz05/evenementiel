<?php

use App\Helpers\SecurityHelper;

if (!function_exists('sanitize_html')) {
    /**
     * Nettoie le HTML en supprimant les scripts malveillants
     *
     * @param string|null $html
     * @return string
     */
    function sanitize_html(?string $html): string
    {
        return SecurityHelper::sanitizeHtml($html);
    }
}

if (!function_exists('secure_output')) {
    /**
     * Échappe complètement le HTML pour un affichage sécurisé
     *
     * @param string|null $text
     * @return string
     */
    function secure_output(?string $text): string
    {
        return SecurityHelper::escape($text);
    }
}

/**
 * Fonction pour générer des liens de filtrage avec basculement (toggle)
 * Si le filtre est déjà actif avec la même valeur, il sera supprimé
 * Sinon, il sera ajouté ou remplacé
 */
if (!function_exists('toggleFilter')) {
    function toggleFilter($filterName, $filterValue)
    {
        $query = request()->query();
        unset($query['page']);
        
        // Si le filtre est déjà présent avec la même valeur, on le supprime
        if (request()->query($filterName) == $filterValue) {
            unset($query[$filterName]);
        } else {
            // Sinon on l'ajoute ou le remplace
            $query[$filterName] = $filterValue;
        }

        return url()->current() . '?' . http_build_query($query);
    }
}

if (!function_exists('format_number')) {
    /**
     * Formate un nombre avec K/M suffix (ex: 1.2K, 25.6K, 3.5M)
     * 
     * Exemples:
     * - 1000 → 1K
     * - 1500 → 1.5K
     * - 1000000 → 1M
     * - 3516204 → 3.5M
     *
     * @param int|float $number
     * @param int $decimals Nombre de décimales (par défaut: 1)
     * @return string
     */
    function format_number($number, $decimals = 1): string
    {
        // Nettoyer le nombre : retirer tous les caractères non numériques
        if (is_string($number)) {
            // Retirer tous les espaces
            $number = str_replace(' ', '', $number);
            
            // Détecter si c'est un format avec virgules de séparation des milliers
            // Si le nombre a des virgules mais pas de point, ce sont des séparateurs de milliers
            if (strpos($number, ',') !== false && strpos($number, '.') === false) {
                // Retirer les virgules (séparateurs de milliers)
                $number = str_replace(',', '', $number);
            } elseif (strpos($number, ',') !== false && strpos($number, '.') !== false) {
                // Il y a à la fois des virgules et des points
                // Si le dernier séparateur est une virgule, c'est probablement une décimale (format européen)
                // Sinon, les virgules sont des séparateurs de milliers
                $lastComma = strrpos($number, ',');
                $lastDot = strrpos($number, '.');
                if ($lastComma > $lastDot) {
                    // La virgule est après le point, donc c'est une décimale (format européen)
                    $number = str_replace('.', '', $number); // Retirer les points (séparateurs de milliers)
                    $number = str_replace(',', '.', $number); // Remplacer la virgule par un point
                } else {
                    // Le point est après la virgule, donc le point est la décimale
                    $number = str_replace(',', '', $number); // Retirer les virgules (séparateurs de milliers)
                }
            } elseif (strpos($number, '.') !== false) {
                // Il y a un point, vérifier si c'est une décimale ou un séparateur de milliers
                $parts = explode('.', $number);
                if (count($parts) == 2 && strlen($parts[1]) <= 2) {
                    // Probablement une décimale (2 chiffres après le point max)
                    // Garder tel quel
                } else {
                    // Probablement des séparateurs de milliers, retirer les points
                    $number = str_replace('.', '', $number);
                }
            }
        }
        
        $number = (float) $number;
        
        // Si le nombre est 0 ou négatif, retourner 0
        if ($number <= 0) {
            return '0';
        }
        
        if ($number >= 1000000) {
            // Millions
            $formatted = $number / 1000000;
            // Si c'est un nombre entier, ne pas afficher de décimales
            if (abs($formatted - floor($formatted)) < 0.0001) {
                return number_format($formatted, 0, '.', '') . 'M';
            }
            $result = number_format($formatted, $decimals, '.', '');
            // Retirer les zéros inutiles à la fin
            $result = rtrim(rtrim($result, '0'), '.');
            return $result . 'M';
        } elseif ($number >= 1000) {
            // Milliers
            $formatted = $number / 1000;
            // Si c'est un nombre entier, ne pas afficher de décimales
            if (abs($formatted - floor($formatted)) < 0.0001) {
                return number_format($formatted, 0, '.', '') . 'K';
            }
            $result = number_format($formatted, $decimals, '.', '');
            // Retirer les zéros inutiles à la fin
            $result = rtrim(rtrim($result, '0'), '.');
            return $result . 'K';
        }
        
        return number_format($number, 0, '', '');
    }
}




