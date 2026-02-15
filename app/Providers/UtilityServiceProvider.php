<?php

namespace App\Providers;

use App\Models\Translation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class UtilityServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Enregistre le service de configuration
        $this->app->singleton('settings', function ($app) {
            return new class {
                public function get($group, $name, $default = null)
                {
                    $key = "{$group}.{$name}";
                    return Config::get($key, $default);
                }

                public function set($group, $name, $value, $type = 'string')
                {
                    $key = "{$group}.{$name}";
                    Config::set($key, $value);
                    return true;
                }
            };
        });

        // Enregistre le service de traduction
        $this->app->singleton('translations', function ($app) {
            return new class {
                public function get($key, $locale = null, $group = 'general')
                {
                    return Translation::get($key, $locale, $group);
                }

                public function set($key, $text, $locale = null, $group = 'general')
                {
                    return Translation::set($key, $text, $locale, $group);
                }
            };
        });
    }

    public function boot()
    {
        // Charge les paramètres globaux au démarrage
        $this->loadGlobalSettings();

        // Ajoute les helpers de traduction
        require_once app_path('Helpers/translation_helpers.php');
        
        // Ajoute les helpers généraux
        require_once app_path('Helpers/helpers.php');
    }

    protected function loadGlobalSettings()
    {
        // Charge les paramètres depuis le fichier de configuration
        $settings = config('settings.global', []);
        config(['global_settings' => $settings]);
    }
}
