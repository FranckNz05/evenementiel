<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedOTP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            // Stocker l'email et l'expiration dans la session pour la page OTP
            session([
                'email' => $user->email,
                'otp_expires_at' => $user->otp_expires_at
            ]);
            
            // Rediriger vers la page OTP au lieu de la page de vérification email classique
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}

