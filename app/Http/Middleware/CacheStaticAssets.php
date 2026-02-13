<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheStaticAssets
{
    /**
     * Handle an incoming request.
     * Ajoute des headers de cache pour les assets statiques (images, CSS, JS)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Vérifier si c'est un asset statique
        $path = $request->path();
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        // Extensions d'images
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico'];
        // Extensions CSS/JS
        $assetExtensions = ['css', 'js', 'woff', 'woff2', 'ttf', 'eot'];
        
        if (in_array(strtolower($extension), array_merge($imageExtensions, $assetExtensions))) {
            // Cache long terme pour les assets statiques (1 an)
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
            
            // Headers pour la compression
            if (in_array(strtolower($extension), ['css', 'js', 'html', 'svg', 'xml', 'json'])) {
                $response->headers->set('Vary', 'Accept-Encoding');
            }
        }
        
        return $response;
    }
}

