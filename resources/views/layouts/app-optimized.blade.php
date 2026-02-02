<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Preconnect pour les domaines externes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
    <!-- DNS Prefetch pour les domaines externes -->
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    
    <title>@yield('title', config('app.name', 'MokiliEvent'))</title>
    <meta name="description" content="@yield('description', 'Plateforme de billetterie et gestion d\'événements en ligne')">
    
    <!-- Favicon optimisé -->
    <link rel="icon" href="{{ setting('favicon') ? asset(setting('favicon')) : asset('images/logo.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ setting('logo') ? asset(setting('logo')) : asset('images/logo.png') }}">
    
    <!-- Critical CSS inline pour éviter le FOUC -->
    <style>
        :root {
            --bleu-nuit: #0f1a3d;
            --bleu-nuit-clair: #1a237e;
            --blanc-or: #ffffff;
            --blanc: #ffffff;
            --gris-clair: #f8f9fa;
            --gris-moyen: #6c757d;
            --gris-fonce: #343a40;
        }
        
        /* Critical CSS pour le chargement initial */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--gris-clair);
        }
        
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--blanc);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--gris-clair);
            border-top: 4px solid var(--bleu-nuit);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Masquer le contenu pendant le chargement */
        .main-content {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .main-content.loaded {
            opacity: 1;
        }
    </style>
    
    <!-- Preload des ressources critiques -->
    <link rel="preload" href="{{ asset('css/critical.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/critical.css') }}"></noscript>
    
    <!-- Fonts avec display=swap pour éviter le FOIT -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <!-- Icons avec fallback -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    
    <!-- Bootstrap CSS avec fallback -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></noscript>
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="{{ asset('css/custom.css') }}" rel="stylesheet"></noscript>
    
    @stack('styles')
    
    <!-- Vite Assets -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    
    <!-- Google Analytics optimisé -->
    <script>
        // Google Analytics avec optimisation
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-FRVDJHR200', {
            'send_page_view': false,
            'custom_map': {'dimension1': 'user_type'}
        });
        
        // Charger Google Analytics de manière asynchrone
        (function() {
            var script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id=G-FRVDJHR200';
            document.head.appendChild(script);
        })();
    </script>
    
    <!-- Meta tags pour le SEO -->
    <meta name="robots" content="index, follow">
    <meta name="author" content="MokiliEvent">
    <meta name="keywords" content="billetterie, événements, tickets, réservation">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', config('app.name', 'MokiliEvent'))">
    <meta property="og:description" content="@yield('og_description', 'Plateforme de billetterie et gestion d\'événements en ligne')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', config('app.name', 'MokiliEvent'))">
    <meta name="twitter:description" content="@yield('og_description', 'Plateforme de billetterie et gestion d\'événements en ligne')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "MokiliEvent",
        "url": "{{ config('app.url') }}",
        "logo": "{{ setting('logo') ? asset(setting('logo')) : asset('images/logo.png') }}",
        "description": "Plateforme de billetterie et gestion d'événements en ligne"
    }
    </script>
</head>

<body>
    <!-- Loading Screen -->
    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="main-content">
        @include('layouts.navigation')
        
        <main>
            @yield('content')
        </main>
        
        @include('layouts.footer')
    </div>
    
    <!-- Scripts optimisés -->
    <script>
        // Masquer le loading et afficher le contenu
        window.addEventListener('load', function() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('main-content').classList.add('loaded');
            
            // Envoyer l'événement page_view à Google Analytics
            gtag('event', 'page_view', {
                'page_title': document.title,
                'page_location': window.location.href
            });
        });
        
        // Lazy loading pour les images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>
    
    <!-- Scripts externes avec defer -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    
    <!-- Scripts locaux -->
    <script src="{{ asset('js/custom.js') }}" defer></script>
    <script src="{{ asset('js/ajax-actions.js') }}" defer></script>
    
    @stack('scripts')
</body>
</html>
