<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Nettoyer toutes les entrées de la requête
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Supprimer les caractères nuls qui peuvent être utilisés pour contourner les filtres
                $value = str_replace("\0", '', $value);
                
                // Supprimer les espaces invisibles et caractères de contrôle
                $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
                
                // Trim les espaces
                $value = trim($value);
            }
        });
        
        // Remplacer les entrées de la requête par les entrées nettoyées
        $request->merge($input);
        
        return $next($request);
    }
}

