<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CommissionSettingsController extends Controller
{
    /**
     * Affiche la page de gestion des paramètres de commission
     */
    public function index()
    {
        $settings = $this->getCommissionSettings();
        return view('dashboard.admin.commission-settings.index', compact('settings'));
    }

    /**
     * Met à jour les paramètres de commission
     */
    public function update(Request $request)
    {
        $request->validate([
            'mtn_commission_rate' => 'required|numeric|min:0|max:100',
            'airtel_commission_rate' => 'required|numeric|min:0|max:100',
            'mokilievent_commission_rate' => 'required|numeric|min:0|max:100',
            'free_event_creation_fee' => 'required|numeric|min:0',
            'custom_event_creation_fee' => 'required|numeric|min:0',
            'require_payment_for_free_events' => 'boolean',
            'require_payment_for_custom_events' => 'boolean',
        ]);

        try {
            DB::transaction(function() use ($request) {
                // Mettre à jour les paramètres de commission
                $this->updateSetting('mtn_commission_rate', $request->mtn_commission_rate);
                $this->updateSetting('airtel_commission_rate', $request->airtel_commission_rate);
                $this->updateSetting('mokilievent_commission_rate', $request->mokilievent_commission_rate);
                
                // Mettre à jour les paramètres de création d'événements
                $this->updateSetting('free_event_creation_fee', $request->free_event_creation_fee);
                $this->updateSetting('custom_event_creation_fee', $request->custom_event_creation_fee);
                $this->updateSetting('require_payment_for_free_events', $request->has('require_payment_for_free_events') ? 1 : 0);
                $this->updateSetting('require_payment_for_custom_events', $request->has('require_payment_for_custom_events') ? 1 : 0);
            });

            // Vider le cache
            Cache::forget('commission_settings');
            Cache::forget('event_creation_settings');

            return redirect()->route('admin.commission-settings.index')
                ->with('success', 'Paramètres de commission mis à jour avec succès !');

        } catch (\Exception $e) {
            return redirect()->route('admin.commission-settings.index')
                ->with('error', 'Erreur lors de la mise à jour des paramètres : ' . $e->getMessage());
        }
    }

    /**
     * Récupère les paramètres de commission
     */
    private function getCommissionSettings()
    {
        return Cache::remember('commission_settings_full', 3600, function() {
            $settings = DB::table('settings')->get()->keyBy('key');
            
            return [
                'mtn_commission_rate' => $settings->get('mtn_commission_rate', (object)['value' => 3.00])->value ?? 3.00,
                'airtel_commission_rate' => $settings->get('airtel_commission_rate', (object)['value' => 3.00])->value ?? 3.00,
                'mokilievent_commission_rate' => $settings->get('mokilievent_commission_rate', (object)['value' => 10.00])->value ?? 10.00,
                'free_event_creation_fee' => $settings->get('free_event_creation_fee', (object)['value' => 25000.00])->value ?? 25000.00,
                'custom_event_creation_fee' => $settings->get('custom_event_creation_fee', (object)['value' => 50000.00])->value ?? 50000.00,
                'require_payment_for_free_events' => $settings->get('require_payment_for_free_events', (object)['value' => 1])->value ?? 1,
                'require_payment_for_custom_events' => $settings->get('require_payment_for_custom_events', (object)['value' => 1])->value ?? 1,
            ];
        });
    }

    /**
     * Met à jour un paramètre spécifique
     */
    private function updateSetting($key, $value)
    {
        $exists = DB::table('settings')->where('key', $key)->exists();
        
        if ($exists) {
            DB::table('settings')->where('key', $key)->update([
                'value' => $value,
                'updated_at' => now()
            ]);
        } else {
            DB::table('settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * API pour récupérer les paramètres (pour les autres contrôleurs)
     */
    public function getSettings()
    {
        return response()->json($this->getCommissionSettings());
    }

    /**
     * Réinitialise les paramètres aux valeurs par défaut
     */
    public function reset()
    {
        try {
            DB::transaction(function() {
                $defaults = [
                    'mtn_commission_rate' => 3.00,
                    'airtel_commission_rate' => 3.00,
                    'mokilievent_commission_rate' => 10.00,
                    'free_event_creation_fee' => 25000.00,
                    'custom_event_creation_fee' => 50000.00,
                    'require_payment_for_free_events' => 1,
                    'require_payment_for_custom_events' => 1,
                ];

                foreach ($defaults as $key => $value) {
                    $this->updateSetting($key, $value);
                }
            });

            // Vider le cache
            Cache::forget('commission_settings');
            Cache::forget('event_creation_settings');

            return redirect()->route('admin.commission-settings.index')
                ->with('success', 'Paramètres réinitialisés aux valeurs par défaut !');

        } catch (\Exception $e) {
            return redirect()->route('admin.commission-settings.index')
                ->with('error', 'Erreur lors de la réinitialisation : ' . $e->getMessage());
        }
    }
}
