<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class SocialAuthController extends Controller
{
    /**
     * Rediriger vers le provider (Facebook ou Google)
     */
    public function redirect($provider)
    {
        try {
            if (!in_array($provider, ['facebook', 'google'])) {
                return redirect()->route('auth.login')->with('error', 'Provider d\'authentification non supporté.');
            }

            // Configurer les scopes selon le provider
            if ($provider === 'facebook') {
                // Facebook: ne pas spécifier de scopes, utiliser les permissions par défaut
                // L'email sera disponible si l'app Facebook a la permission email activée
                return Socialite::driver('facebook')
                    ->fields(['name', 'email', 'picture'])
                    ->redirect();
            }

            // Google: utiliser les scopes par défaut (email et profile)
            // L'URL de callback est automatiquement construite à partir de la requête
            return Socialite::driver('google')
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Erreur redirection social auth', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('auth.login')->with('error', 'Une erreur est survenue lors de la connexion. Veuillez réessayer.');
        }
    }

    /**
     * Gérer le callback du provider
     */
    public function callback($provider)
    {
        try {
            if (!in_array($provider, ['facebook', 'google'])) {
                return redirect()->route('auth.login')->with('error', 'Provider d\'authentification non supporté.');
            }

            $socialUser = Socialite::driver($provider)->user();

            // Vérifier si l'utilisateur existe déjà
            $user = User::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$user) {
                // Vérifier si un utilisateur avec cet email existe déjà (sans provider social)
                $existingUser = User::where('email', $socialUser->getEmail())->first();

                if ($existingUser) {
                    // Lier le compte social au compte existant
                    $existingUser->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                    ]);
                    $user = $existingUser;
                } else {
                    // Créer un nouvel utilisateur
                    $nameParts = $this->parseName($socialUser->getName());
                    
                    $user = User::create([
                        'prenom' => $nameParts['prenom'],
                        'nom' => $nameParts['nom'],
                        'email' => $socialUser->getEmail(),
                        'password' => Hash::make(Str::random(32)), // Mot de passe aléatoire
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'profil_image' => $this->downloadProfileImage($socialUser->getAvatar(), $provider),
                        'email_verified_at' => now(), // Les comptes sociaux sont considérés comme vérifiés
                        'is_active' => true,
                    ]);

                    // Assigner le rôle Client
                    try {
                        $user->assignRole('Client');
                    } catch (\Exception $e) {
                        Log::error('Erreur assignation rôle social auth', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                        $role = Role::firstOrCreate(['name' => 'Client']);
                        $user->assignRole($role);
                    }
                }
            } else {
                // Mettre à jour l'image de profil si elle a changé
                if ($socialUser->getAvatar() && $socialUser->getAvatar() !== $user->profil_image) {
                    $user->update([
                        'profil_image' => $this->downloadProfileImage($socialUser->getAvatar(), $provider),
                    ]);
                }
            }

            // Connecter l'utilisateur
            Auth::login($user, true);

            // Récupérer les rôles réels de l'utilisateur
            $userRoles = $user->getRoleNames()->toArray();
            $roleIds = $user->roles->pluck('id')->toArray();
            
            // Rediriger en fonction du rôle (priorité à l'administrateur)
            $redirectRoute = route('home');
            // Vérifier d'abord par ID pour être sûr
            if (in_array(3, $roleIds) || in_array('Administrateur', $userRoles) || in_array('administrateur', array_map('strtolower', $userRoles))) {
                $redirectRoute = route('admin.dashboard');
            } elseif (in_array(2, $roleIds) || in_array('Organizer', $userRoles) || in_array('organizer', array_map('strtolower', $userRoles))) {
                $redirectRoute = route('organizer.dashboard');
            }

            return redirect($redirectRoute)->with('success', 'Connexion réussie avec ' . ucfirst($provider) . ' !');

        } catch (\Exception $e) {
            Log::error('Erreur callback social auth', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('auth.login')->with('error', 'Une erreur est survenue lors de la connexion. Veuillez réessayer.');
        }
    }

    /**
     * Parser le nom complet en prénom et nom
     */
    private function parseName($fullName)
    {
        $nameParts = explode(' ', trim($fullName), 2);
        
        return [
            'prenom' => $nameParts[0] ?? 'Utilisateur',
            'nom' => $nameParts[1] ?? 'Social',
        ];
    }

    /**
     * Télécharger et sauvegarder l'image de profil
     */
    private function downloadProfileImage($avatarUrl, $provider)
    {
        try {
            if (!$avatarUrl) {
                return null;
            }

            $imageContent = file_get_contents($avatarUrl);
            if ($imageContent === false) {
                return null;
            }

            $extension = 'jpg';
            $filename = 'profile_' . $provider . '_' . time() . '_' . Str::random(10) . '.' . $extension;
            $path = 'profile-images/' . $filename;

            \Storage::disk('public')->put($path, $imageContent);

            return $path;
        } catch (\Exception $e) {
            Log::error('Erreur téléchargement image profil social', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}

