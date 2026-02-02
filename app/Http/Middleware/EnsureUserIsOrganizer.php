<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsOrganizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        // Vérifier si l'utilisateur a le rôle organisateur (en minuscules)
        $user = Auth::user();
        if (!$user->hasRole('organizer')) {
            return redirect()->route('home')
                ->with('error', 'Accès refusé. Vous devez être un organisateur pour accéder à cette section.');
        }

        // Vérifier que l'utilisateur est bien un organisateur vérifié
        if (!$user->hasVerifiedEmail()) {
            // Stocker l'email et l'expiration dans la session pour la page OTP
            session([
                'email' => $user->email,
                'otp_expires_at' => $user->otp_expires_at
            ]);
            return redirect()->route('verification.notice')
                ->with('status', 'Veuillez vérifier votre adresse email avant de continuer.');
        }

        return $next($request);
    }
}
