<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * Ce middleware redirige les utilisateurs déjà authentifiés
     * loin des pages destinées aux invités (login, register, etc.)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Utilisateur déjà authentifié, rediriger vers la page d'accueil
                return redirect(RouteServiceProvider::HOME);
            }
        }

        // Utilisateur non authentifié, laisser passer la requête
        return $next($request);
    }
}
