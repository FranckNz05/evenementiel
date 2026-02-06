<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    /**
     * Affiche la vue de réinitialisation du mot de passe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $token = $request->route('token') ?? $request->query('token');
        $email = $request->email ?? $request->query('email');
        
        \Log::info('Password reset form accessed', [
            'token' => $token ? 'present' : 'missing',
            'email' => $email,
            'url' => $request->fullUrl(),
        ]);
        
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Traite une demande de réinitialisation de mot de passe entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // Log immédiat pour voir si la méthode est appelée
        \Log::info('=== PASSWORD RESET STORE METHOD CALLED ===', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'email' => $request->email,
            'has_token' => !empty($request->token),
            'has_password' => !empty($request->password),
            'has_password_confirmation' => !empty($request->password_confirmation),
            'all_input' => array_keys($request->all()),
        ]);

        // Log pour déboguer
        \Log::info('Password reset validation - before validation', [
            'password_length' => strlen($request->password ?? ''),
            'password_bytes' => strlen(utf8_encode($request->password ?? '')),
            'password_value_preview' => substr($request->password ?? '', 0, 10) . '...', // Premiers caractères seulement pour sécurité
            'has_password' => !empty($request->password),
            'request_method' => $request->method(),
            'all_input_keys' => array_keys($request->all()),
        ]);

        // Validation avec messages personnalisés
        $passwordLength = mb_strlen($request->password ?? '', 'UTF-8');
        $passwordBytes = strlen($request->password ?? '');
        
        \Log::info('Password details before validation', [
            'password_length_mb' => $passwordLength,
            'password_bytes' => $passwordBytes,
            'password_is_empty' => empty($request->password),
            'password_is_null' => is_null($request->password),
        ]);
        
        $validator = \Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => "Le mot de passe doit contenir au moins 8 caractères. (Vous avez saisi {$passwordLength} caractère" . ($passwordLength > 1 ? 's' : '') . ")",
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        if ($validator->fails()) {
            \Log::error('Password reset validation failed', [
                'errors' => $validator->errors()->all(),
                'password_length_mb' => $passwordLength,
                'password_bytes' => $passwordBytes,
                'password_preview' => substr($request->password ?? '', 0, 5) . '...',
                'all_input_keys' => array_keys($request->all()),
                'raw_password_received' => !empty($request->password),
                'validation_rules_failed' => $validator->failed(),
            ]);
            
            return back()
                ->withInput($request->only('email', 'token'))
                ->withErrors($validator);
        }
        
        \Log::info('Password validation passed', [
            'password_length' => $passwordLength,
        ]);

        // Ici, nous allons tenter de réinitialiser le mot de passe de l'utilisateur. Si c'est réussi,
        // nous mettrons à jour le mot de passe sur un vrai modèle utilisateur et le persisterons
        // dans la base de données, sinon nous analyserons l'erreur et renverrons la réponse.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        \Log::info('Password reset status', ['status' => $status]);

        // Si le mot de passe a été réinitialisé avec succès, nous redirigerons l'utilisateur
        // vers la vue de connexion authentifiée. Si il y a une erreur, nous
        // traiterons cette erreur en tant que message de validation.
        if ($status == Password::PASSWORD_RESET) {
            \Log::info('Password reset successful', ['email' => $request->email]);
            return redirect()->route('login')->with('status', __($status));
        }

        // Gestion des erreurs avec messages plus explicites
        $errorMessage = __($status);
        
        // Messages d'erreur personnalisés selon le type d'erreur
        if ($status == Password::INVALID_TOKEN) {
            $errorMessage = 'Ce lien de réinitialisation est invalide ou a expiré. Veuillez demander un nouveau lien.';
        } elseif ($status == Password::INVALID_USER) {
            $errorMessage = 'Aucun utilisateur trouvé avec cette adresse e-mail.';
        } elseif ($status == Password::RESET_THROTTLED) {
            $errorMessage = 'Trop de tentatives. Veuillez réessayer plus tard.';
        }

        \Log::warning('Password reset failed', [
            'status' => $status,
            'error_message' => $errorMessage,
            'email' => $request->email,
        ]);

        return back()
            ->withInput($request->only('email', 'token'))
            ->withErrors(['email' => $errorMessage]);
    }
}

