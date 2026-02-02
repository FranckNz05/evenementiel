<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DashboardController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->hasVerifiedEmail()) {
            // Si l'utilisateur est connecté mais n'a pas vérifié son email
            // Et n'est pas déjà sur la page de vérification OTP
            if (!$request->is('verification/otp') && !$request->is('verification/otp/*')) {
                // Stocker l'email et l'expiration dans la session pour la page OTP
                $user = Auth::user();
                session([
                    'email' => $user->email,
                    'otp_expires_at' => $user->otp_expires_at
                ]);
                return redirect()->route('verification.notice');
            }
        } else if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            // Si l'utilisateur est vérifié mais tente d'accéder à la page de vérification
            if ($request->is('verification/otp') || $request->is('verification/otp/*')) {
                // Rediriger vers le dashboard approprié selon le rôle
                $user = Auth::user();
                if ($user->hasRole('Administrateur')) {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->hasRole('Organizer')) {
                    return redirect()->route('organizer.dashboard');
                }
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}
