<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// Supprimez cette condition pour éviter la redéclaration
// if (!function_exists('setting')) {
//     function setting($key, $default = null) { ... }
// }

/**
 * Classe SettingsHelper pour gérer les paramètres du site
 */
class SettingsHelper
{
    /**
     * Récupère tous les paramètres du site
     *
     * @return array
     */
    public static function getAllSettings()
    {
        try {
            return Cache::remember('site_settings', 3600, function () {
                try {
                    $settings = DB::table('settings')->get();
                    $result = [];
                    
                    foreach ($settings as $setting) {
                        $result[$setting->key] = $setting->value;
                    }
                    
                    return $result;
                } catch (\Exception $e) {
                    // Si la table n'existe pas ou erreur DB, retourner un tableau vide
                    \Log::warning('Erreur lors de la récupération des settings: ' . $e->getMessage());
                    return [];
                }
            });
        } catch (\Exception $e) {
            \Log::warning('Erreur lors de la récupération des settings (cache): ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère un paramètre spécifique
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getSetting($key, $default = null)
    {
        try {
            $settings = self::getAllSettings();
            return $settings[$key] ?? $default;
        } catch (\Exception $e) {
            \Log::warning('Erreur lors de la récupération du setting ' . $key . ': ' . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Vide le cache des paramètres
     */
    public static function clearCache()
    {
        Cache::forget('site_settings');
    }
}


