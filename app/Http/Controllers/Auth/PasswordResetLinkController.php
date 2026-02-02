<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Affiche la vue pour demander un lien de réinitialisation du mot de passe.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.passwords.email');
    }

    /**
     * Traite une demande de lien de réinitialisation de mot de passe entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Nous enverrons le lien de réinitialisation du mot de passe à cet utilisateur. Une fois
        // que nous l'avons envoyé, nous allons vérifier l'état et continuer.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
