<?php

// Définir la fonction helper globale dans le namespace global
if (!function_exists('setting')) {
    /**
     * Récupère un paramètre du site depuis la base de données
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        return \App\Helpers\SettingsHelper::getSetting($key, $default);
    }
}

if (!function_exists('settings')) {
    /**
     * Récupère un paramètre du site depuis la configuration
     *
     * @param string $group
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function settings($group, $key, $default = null)
    {
        return config("settings.{$group}.{$key}", $default);
    }
}

