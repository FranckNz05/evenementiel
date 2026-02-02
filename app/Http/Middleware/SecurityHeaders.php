<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prévention du clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Protection XSS du navigateur
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Prévention du MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Référrer Policy pour la vie privée
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Content Security Policy - Stricte mais flexible pour les besoins de l'application
        // Autoriser localhost et 127.0.0.1 pour le développement (Vite)
        $isLocal = app()->environment('local');
        
        // Pour le développement local, autoriser Vite (localhost sur différents ports)
        // Support IPv4 (127.0.0.1) et localhost (qui couvre aussi IPv6)
        if ($isLocal) {
            $csp = [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://www.googletagmanager.com https://maps.googleapis.com https://cdnjs.cloudflare.com https://www.chatbase.co https://code.jquery.com http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com http://localhost:* http://127.0.0.1:*",
                "img-src 'self' data: https: blob:",
                "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
                "connect-src 'self' https://maps.googleapis.com https://www.googletagmanager.com https://stats.g.doubleclick.net https://analytics.google.com https://cdn.jsdelivr.net https://www.chatbase.co http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*",
                "frame-src 'self' https://www.google.com https://maps.google.com",
                "media-src 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'"
            ];
        } else {
            // Production : CSP stricte sans localhost
            $csp = [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://www.googletagmanager.com https://maps.googleapis.com https://cdnjs.cloudflare.com https://www.chatbase.co https://code.jquery.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
                "img-src 'self' data: https: blob:",
                "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
                "connect-src 'self' https://maps.googleapis.com https://www.googletagmanager.com https://stats.g.doubleclick.net https://analytics.google.com https://cdn.jsdelivr.net https://www.chatbase.co",
                "frame-src 'self' https://www.google.com https://maps.google.com",
                "media-src 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
                "upgrade-insecure-requests"
            ];
        }
        
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        
        // Permissions Policy (anciennement Feature Policy)
        $permissionsPolicy = [
            'geolocation=(self)',
            'microphone=()',
            'camera=()',
            'payment=(self)',
            'usb=()',
            'magnetometer=()',
            'gyroscope=()',
            'accelerometer=()'
        ];
        $response->headers->set('Permissions-Policy', implode(', ', $permissionsPolicy));
        
        // Strict Transport Security (HSTS) - À activer seulement en HTTPS
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}

