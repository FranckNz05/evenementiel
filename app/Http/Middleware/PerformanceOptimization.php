<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class PerformanceOptimization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Ajouter les headers de performance
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Cache headers pour les pages statiques
        if ($this->shouldCache($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=3600');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
        }
        
        // Compression
        if ($this->shouldCompress($request)) {
            $response->headers->set('Vary', 'Accept-Encoding');
        }
        
        return $response;
    }
    
    /**
     * Détermine si la réponse doit être mise en cache
     */
    private function shouldCache(Request $request): bool
    {
        // Ne pas cacher les pages avec des données utilisateur
        if ($request->user() || $request->has('_token')) {
            return false;
        }
        
        // Cacher les pages publiques
        $cacheableRoutes = [
            'home',
            'events.index',
            'events.show',
            'about',
            'contact',
            'terms',
            'privacy'
        ];
        
        return in_array($request->route()?->getName(), $cacheableRoutes);
    }
    
    /**
     * Détermine si la réponse doit être compressée
     */
    private function shouldCompress(Request $request): bool
    {
        $acceptEncoding = $request->header('Accept-Encoding', '');
        return strpos($acceptEncoding, 'gzip') !== false || strpos($acceptEncoding, 'br') !== false;
    }
}
