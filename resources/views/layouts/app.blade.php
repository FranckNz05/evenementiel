<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-FRVDJHR200"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-FRVDJHR200');
</script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'mokilievent'))</title>

    <link rel="icon" href="{{ setting('favicon') ? asset(setting('favicon')) : asset('images/logo.png') }}" type="image/png">
    
    <!-- CSS Critique Inline (Above the Fold) -->
    <style>
        /* Styles critiques pour le rendu initial - Above the fold */
        :root {
            --bleu-nuit: #0f1a3d;
            --bleu-nuit-clair: #1a237e;
            --jaune-or: #ffffff;
            --jaune-or-fonce: #f8fafc;
            --text-color: #2d3748;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: var(--bleu-nuit) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            padding: 0.8rem 0;
        }
        .hero-header {
            position: relative;
            height: 500px;
            background-size: cover;
            background-position: center;
        }
        .hero-bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }
        @media (max-width: 767.98px) {
            .hero-header { height: 400px !important; }
        }
    </style>

    <!-- Fonts - Preload et optimisation -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    
    <!-- Icons - Chargement différé -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    <!-- Styles - Bootstrap critique uniquement, reste en différé -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></noscript>
    
    <!-- CSS personnalisé - Chargement différé -->
    <link rel="preload" href="{{ asset('css/custom.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="{{ asset('css/custom.css') }}" rel="stylesheet"></noscript>
    
    <link rel="preload" href="{{ asset('css/theme.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="{{ asset('css/theme.css') }}" rel="stylesheet"></noscript>
    
    <!-- Playfair Display - Police secondaire, chargement différé -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet"></noscript>
    
    @stack('styles')

    <!-- Scripts - Tous en defer pour ne pas bloquer le rendu -->
    <!-- jQuery - Chargement différé car non critique pour le rendu initial -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="{{ asset('js/custom.js') }}" defer></script>
    <script src="{{ asset('js/ajax-actions.js') }}" defer></script>
    @stack('scripts')

    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <style>
        :root {
            --bleu-nuit: #0f1a3d;
            --bleu-nuit-clair: #1a237e;
            --jaune-or: #ffffff;
            --jaune-or-fonce: #f8fafc;
            --text-color: #2d3748;
            --text-light: #f8fafc;
            --background-color: #f8fafc;
            --border-radius: 0.5rem;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --section-spacing: 5rem;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* Uniformisation globale des couleurs - Remplacer vert/rouge par bleu-nuit */
        .bg-success, .bg-success-soft {
            background-color: var(--bleu-nuit) !important;
        }
        .bg-danger, .bg-danger-soft {
            background-color: var(--bleu-nuit) !important;
        }
        .text-success {
            color: var(--bleu-nuit) !important;
        }
        .text-danger {
            color: var(--bleu-nuit) !important;
        }
        .badge.bg-success, .badge.bg-success-soft {
            background-color: var(--bleu-nuit) !important;
            color: white !important;
        }
        .badge.bg-danger, .badge.bg-danger-soft {
            background-color: var(--bleu-nuit) !important;
            color: white !important;
        }
        .alert-success {
            background-color: var(--bleu-nuit) !important;
            color: white !important;
            border-color: var(--bleu-nuit) !important;
        }
        .alert-danger {
            background-color: var(--bleu-nuit) !important;
            color: white !important;
            border-color: var(--bleu-nuit) !important;
        }
        
        /* Textes en noir par défaut */
        body, p, span, div, h1, h2, h3, h4, h5, h6, label, input, textarea, select {
            color: var(--text-color) !important;
        }
        
        /* Force le texte des inputs de recherche à être noir */
        input[type="text"],
        input[type="search"],
        input.form-control,
        input.form-control-lg,
        textarea.form-control,
        select.form-control,
        select.form-select {
            color: #000 !important;
        }
        
        /* Placeholder en gris */
        input::placeholder,
        textarea::placeholder {
            color: #6b7280 !important;
            opacity: 1;
        }
        
        /* Textes en blanc sur fond bleu */
        .navbar, .footer-modern, [style*="background-color: var(--bleu-nuit)"], 
        [style*="background: var(--bleu-nuit)"], 
        [class*="bg-"][style*="--bleu-nuit"],
        .btn-primary, .btn[style*="--bleu-nuit"] {
            color: white !important;
        }
        .navbar *, .footer-modern *, 
        [style*="background-color: var(--bleu-nuit)"] *,
        [style*="background: var(--bleu-nuit)"] * {
            color: white !important;
        }
        
        /* Surcharger toutes les définitions de couleurs dans les pages */
        [style*="--bleu-nuit: #"], [style*="--bleu-nuit:#"] {
            --bleu-nuit: #0f1a3d !important;
        }
        [style*="--bleu-nuit-clair: #"], [style*="--bleu-nuit-clair:#"] {
            --bleu-nuit-clair: #1a237e !important;
        }

        /* Styles pour la barre d'annonces globale */
        .announcements-bar {
            height: 55px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.3);
            overflow: hidden;
            position: relative;
        }

        .announcements-bar:hover .ticker-track {
            animation-play-state: paused !important;
        }

        .ticker-track {
            display: inline-block;
            white-space: nowrap;
            animation: ticker-scroll 90s linear infinite;
            padding-right: 100%;
        }

        .ticker-item {
            display: inline-block;
            padding: 0 40px;
            color: white;
            line-height: 55px;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .ticker-item.announcement {
            background-color: rgba(155, 5, 5, 0.2);
            border-radius: 4px;
            padding: 0 15px;
        }

        .ticker-item.announcement .fw-bold {
            color: var(--jaune-or);
        }

        .ticker-item.event-popular {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.2) 0%, rgba(255, 140, 0, 0.3) 100%);
            border-radius: 4px;
            padding: 0 15px;
        }

        .ticker-item.event-popular .fw-bold {
            color: #ff8c00;
        }

        .ticker-item.urgent {
            background: linear-gradient(135deg, #8B0000 0%, #DC143C 100%);
            border-radius: 4px;
            padding: 0 15px;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
            animation: urgent-pulse 2s ease-in-out infinite;
        }

        .ticker-item.urgent .fw-bold {
            color: var(--jaune-or);
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
        }

        .pulse-icon {
            animation: icon-pulse 1.5s ease-in-out infinite;
        }

        @keyframes urgent-pulse {
            0%, 100% { box-shadow: 0 0 15px rgba(255, 215, 0, 0.5); }
            50% { box-shadow: 0 0 25px rgba(255, 215, 0, 0.9); }
        }

        @keyframes icon-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        @keyframes ticker-scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Styles généraux améliorés */
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Navbar améliorée */
        .navbar {
            background-color: var(--bleu-nuit) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            padding: 0.8rem 0;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--jaune-or) !important;
        }

        .navbar-brand img {
            height: 45px;
            width: auto;
            margin-right: 12px;
        }

        /* Bouton hamburger blanc sur mobile */
        .navbar-toggler {
            border: none;
            color: white !important;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Liens de navigation améliorés */
        .nav-link {
            color: var(--jaune-or) !important;
            font-weight: 500;
            padding: 0.6rem 1.2rem !important;
            margin: 0 0.2rem;
            border-radius: 50px;
            text-decoration: none !important;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .nav-link:hover {
            background-color: rgba(255, 215, 0, 0.15);
            transform: translateY(-2px);
        }

        .nav-link.active {
            background-color: var(--jaune-or) !important;
            color: var(--bleu-nuit) !important;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(255, 215, 0, 0.3);
        }

        /* Icônes toujours blanches dans les nav-link (sauf si actif) */
        .nav-link i {
            color: white !important;
        }

        .nav-link.active i {
            color: var(--bleu-nuit) !important;
        }

        /* Boutons connexion/inscription améliorés */
        .btn-login, .btn-register {
            border-radius: 50px !important;
            padding: 0.6rem 1.5rem !important;
            font-weight: 600;
            text-decoration: none !important;
            transition: var(--transition);
        }

        .btn-login {
            color: var(--jaune-or) !important;
            border: 2px solid var(--jaune-or) !important;
            background: transparent !important;
        }

        .btn-login:hover {
            background: var(--jaune-or) !important;
            color: var(--bleu-nuit) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 215, 0, 0.3);
        }

        .btn-register {
            background-color: var(--jaune-or-fonce) !important;
            color: var(--bleu-nuit) !important;
            border: none !important;
        }

        .btn-register:hover {
            background-color: var(--jaune-or) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 215, 0, 0.3);
        }

        /* Dropdown menus améliorés */
        .dropdown-menu {
            background-color: white;
            border: none;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            z-index: 1050 !important;
        }

        .dropdown-item {
            padding: 0.6rem 1.5rem;
            color: var(--text-color) !important;
            font-weight: 500;
            transition: var(--transition);
        }

        .dropdown-item:hover, 
        .dropdown-item:focus {
            background-color: var(--bleu-nuit) !important;
            color: white !important;
            padding-left: 1.8rem;
        }

        .dropdown-item.active, 
        .dropdown-item:active {
            background-color: var(--bleu-nuit) !important;
            color: white !important;
        }

        .dropdown-divider {
            border-color: rgba(0, 0, 0, 0.1);
            margin: 0.25rem 0;
        }

        .dropdown-item.text-danger:hover {
            background-color: #dc3545 !important;
            color: white !important;
        }

        /* Profile image améliorée */
        .profile-image {
            border: 2px solid var(--jaune-or);
            transition: var(--transition);
        }

        .nav-link.dropdown-toggle:hover .profile-image {
            border-color: white;
            transform: scale(1.05);
        }

        /* ============================================
           FOOTER OPTIMISÉ ET COMPACT
        ============================================ */
        .footer-modern {
            background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: auto;
            position: relative;
            overflow: hidden;
        }

        /* Filigrane SVG pour le footer (plein écran, partie basse) */
        .footer-modern::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image: url('{{ asset('images/4481626_2375581.svg') }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            opacity: 0.14; /* plus visible mais discret */
            mix-blend-mode: overlay;
            z-index: 0;
        }
        
        /* Responsive pour mobile */
        @media (max-width: 768px) {
            .footer-modern::before {
                background-size: cover;
                background-position: center center;
                min-height: 100%;
            }
        }
        
        .footer-modern .container { 
            position: relative;
            z-index: 1;
        }

        /* Bordure dorée animée */
        .footer-modern::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--jaune-or), transparent);
            animation: border-slide 3s ease-in-out infinite;
            z-index: 2;
        }

        @keyframes border-slide {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }

        /* Container principal */
        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        /* Colonne À propos */
        .footer-brand {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--jaune-or);
            margin-bottom: 0.5rem;
        }

        .footer-description {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }

        /* Méthodes de paiement compactes */
        .payment-methods-compact {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .payment-icon {
            width: 45px;
            height: 30px;
            background: white;
            border-radius: 6px;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .payment-icon:hover {
            transform: translateY(-3px);
        }

        .payment-icon img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Sections des liens */
        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .footer-links h6 {
            color: var(--jaune-or);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .footer-links a:hover {
            color: var(--jaune-or);
            transform: translateX(5px);
        }

        /* Section contact */
        .footer-contact {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white !important;
            font-size: 0.9rem;
        }

        .contact-item i {
            width: 20px;
            color: var(--jaune-or);
            font-size: 1rem;
        }

        .contact-item span {
            color: white !important;
        }

        /* Réseaux sociaux */
        .social-links-compact {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .social-icon:hover {
            background: var(--jaune-or);
            color: var(--bleu-nuit);
            transform: translateY(-5px) rotate(5deg);
        }

        /* Newsletter compacte */
        .newsletter-compact {
            margin-top: 1rem;
        }

        .newsletter-input-group {
            display: flex;
            gap: 0.5rem;
        }

        .newsletter-input-group input {
            flex: 1;
            padding: 0.6rem 1rem;
            border: none;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 0.9rem;
        }

        .newsletter-input-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .newsletter-input-group input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
        }

        .newsletter-input-group button {
            padding: 0.6rem 1.5rem;
            background: var(--jaune-or);
            color: var(--bleu-nuit);
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .newsletter-input-group button:hover {
            background: var(--jaune-or-fonce);
            transform: scale(1.05);
        }

        /* Boutons app stores compacts */
        .app-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .app-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .app-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--jaune-or);
            color: white;
            transform: translateY(-3px);
        }

        .app-btn i {
            font-size: 1.5rem;
        }

        /* Footer bottom */
        .footer-bottom {
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .footer-bottom-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-bottom-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-bottom-links a:hover {
            color: var(--jaune-or);
        }

        /* ============================================
           LOADER MODERNE ET UNIQUE
        ============================================ */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .page-loader.fade-out {
            opacity: 0;
            transform: scale(1.1);
            pointer-events: none;
        }


        /* Spinner principal - Cercles concentriques */
        .modern-spinner {
            position: relative;
            width: 150px;
            height: 150px;
            margin-bottom: 2rem;
        }

        .spinner-circle {
            position: absolute;
            border-radius: 50%;
            border: 3px solid transparent;
        }

        .spinner-circle-1 {
            width: 150px;
            height: 150px;
            border-top-color: var(--jaune-or);
            border-right-color: var(--jaune-or);
            animation: rotate 1.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
        }

        .spinner-circle-2 {
            width: 120px;
            height: 120px;
            top: 15px;
            left: 15px;
            border-bottom-color: white;
            border-left-color: white;
            animation: rotate-reverse 2s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
        }

        .spinner-circle-3 {
            width: 90px;
            height: 90px;
            top: 30px;
            left: 30px;
            border-top-color: var(--jaune-or-fonce);
            border-right-color: var(--jaune-or-fonce);
            animation: rotate 2.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
        }

        /* Logo central */
        .spinner-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            transform: translate(-50%, -50%);
            animation: pulse 1.5s ease-in-out infinite;
        }

        .spinner-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 0 3px var(--jaune-or));
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes rotate-reverse {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }

        @keyframes pulse {
            0%, 100% { 
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
            50% { 
                transform: translate(-50%, -50%) scale(1.5);
                opacity: 0.7;
            }
        }



        /* Particules flottantes */
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--jaune-or);
            border-radius: 50%;
            animation: particle-float 3s ease-in-out infinite;
            opacity: 0.6;
        }

        @keyframes particle-float {
            0%, 100% { 
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            50% { 
                opacity: 0.8;
            }
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 3s; }
        .particle:nth-child(2) { left: 30%; animation-delay: 0.5s; animation-duration: 3.5s; }
        .particle:nth-child(3) { left: 50%; animation-delay: 1s; animation-duration: 4s; }
        .particle:nth-child(4) { left: 70%; animation-delay: 1.5s; animation-duration: 3.2s; }
        .particle:nth-child(5) { left: 90%; animation-delay: 2s; animation-duration: 3.8s; }

        #app {
            opacity: 1;
            transition: opacity 0.5s ease-in;
        }

        /* Alertes améliorées */
        .alert {
            border: none;
            border-radius: 8px;
            box-shadow: var(--box-shadow);
        }

        /* Toast Notifications Styles */
        .toast-container {
            z-index: 9999;
        }

        .toast {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border: none;
            backdrop-filter: blur(10px);
            animation: slideInRight 0.3s ease-out;
        }

        .toast-header {
            border-radius: 12px 12px 0 0;
            border-bottom: none;
            padding: 12px 16px;
            font-weight: 600;
        }

        .toast-body {
            padding: 12px 16px;
            font-size: 14px;
            line-height: 1.4;
        }

        .toast.show {
            opacity: 1;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .toast.fade-out {
            animation: slideOutRight 0.3s ease-in;
        }

        /* Main content amélioré */
        main {
            min-height: calc(100vh - 400px);
        }

        /* Responsive amélioré */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: var(--bleu-nuit);
                padding: 1rem;
                margin-top: 1rem;
                border-radius: 0.5rem;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }

            .nav-link {
                margin: 0.3rem 0;
                display: block;
                text-align: center;
            }

            .navbar-nav.ms-auto {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .btn-login, .btn-register {
                display: block;
                text-align: center;
                margin: 0.5rem 0;
            }

            /* Footer responsive moderne */
            .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 767.98px) {
            .navbar-brand {
                font-size: 1.2rem;
            }

            .navbar-brand img {
                height: 35px;
            }

            /* Loader responsive */

            .modern-spinner {
                width: 100px;
                height: 100px;
            }

            .spinner-circle-1 {
                width: 100px;
                height: 100px;
            }

            .spinner-circle-2 {
                width: 80px;
                height: 80px;
                top: 10px;
                left: 10px;
            }

            .spinner-circle-3 {
                width: 60px;
                height: 60px;
                top: 20px;
                left: 20px;
            }


            .spinner-logo {
                width: 30px;
                height: 30px;
            }

            /* Footer responsive */
            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .footer-bottom-links {
                flex-direction: column;
                gap: 0.75rem;
            }

            .newsletter-input-group {
                flex-direction: column;
            }

            .app-buttons {
                flex-direction: column;
            }
        }

        @media (max-width: 575.98px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }

            .payment-methods-compact {
                flex-wrap: wrap;
            }

            .social-links-compact {
                justify-content: center;
            }
        }

        /* Animation pour le contenu */
        .animate-fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Styles pour la modale */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            background-color: var(--bleu-nuit);
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .modal-title {
            color: var(--jaune-or);
        }

        .btn-close {
            filter: invert(1);
        }

        /* Styles pour la modale de création d'événement */
        .event-type-card-modal {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .event-type-card-modal:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .event-icon-modal {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        #createEventModal .modal-content {
            border-radius: 15px;
        }

        #createEventModal .modal-header {
            background-color: var(--bleu-nuit);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        #createEventModal .modal-title {
            color: var(--jaune-or);
        }

        #createEventModal .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        #createEventModal .btn-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
        }

        #createEventModal .btn-warning:hover {
            background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
            color: white;
        }
    </style>
</head>
<body class="font-sans antialiased">
        <div class="page-loader" id="pageLoader">
        <!-- Particules flottantes -->
        <div class="particle" style="top: 20%;"></div>
        <div class="particle" style="top: 40%;"></div>
        <div class="particle" style="top: 60%;"></div>
        <div class="particle" style="top: 80%;"></div>
        <div class="particle" style="top: 50%;"></div>

        <!-- Spinner moderne -->
        <div class="modern-spinner">
            <div class="spinner-circle spinner-circle-1"></div>
            <div class="spinner-circle spinner-circle-2"></div>
            <div class="spinner-circle spinner-circle-3"></div>
            <div class="spinner-logo">
                <img src="{{ setting('logo') ? asset(setting('logo')) : asset('images/logo.png') }}" alt="Logo">
            </div>
        </div>

    </div>
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg sticky-top" style="z-index: 1040 !important;">
            <div class="container">
            <div class="navbar-left">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ setting('logo') ? asset(setting('logo')) : asset('images/logo.png') }}" alt="MokiliEvent" class="img" style="width: 70px; height: 70px;">
                </a>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link rounded-pill px-3 mx-1 {{ request()->routeIs('events.public') || request()->routeIs('events.index') || request()->routeIs('direct-events.*') ? 'active' : '' }}" href="{{ route('events.public') }}">
                                Trouver un évènement
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded-pill px-3 mx-1 {{ request()->routeIs('organizers.index') ? 'active' : '' }}" href="{{ route('organizers.index') }}">
                                Organisateurs
                            </a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a class="nav-link rounded-pill px-3 mx-1 {{ request()->routeIs('events.select-type') || request()->routeIs('events.create') || request()->routeIs('organizer.events.*') || request()->routeIs('events.wizard.*') || request()->routeIs('custom-offers.*') || request()->routeIs('custom-events.*') ? 'active' : '' }}" href="#" data-bs-toggle="modal" data-bs-target="#createEventModal">
                                    Créer un évènement
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded-pill px-3 mx-1 {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                    Mon compte
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded-pill px-3 mx-1 {{ request()->routeIs('reservations.*') ? 'active' : '' }}" href="{{ route('reservations.index') }}">
                                    Mes réservations
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link rounded-pill px-3 mx-1" href="{{ route('login') }}?redirect=events.create">
                                    Créer un évènement
                                </a>
                            </li>
                        @endauth
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link btn-login rounded-pill me-2" href="{{ route('login') }}">Connexion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-register rounded-pill" href="{{ route('register') }}">Inscription</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                                    <img src="{{ Auth::user()->getProfilePhotoUrlAttribute() }}"
                                         alt="Photo de profil"
                                         class="rounded-circle me-2 profile-image"
                                         style="width: 40px; height: 40px; object-fit: cover;"
                                         onerror="this.onerror=null; this.src='{{ asset('images/default-profile.png') }}'">
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold" style="font-size: 0.9rem; line-height: 1.2; max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ Auth::user()->nom }}
                                        </span>
                                        @if(Auth::user()->hasRole(2) && Auth::user()->company_name)
                                            <small class="text-muted" style="font-size: 0.7rem; line-height: 1.2; max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ Auth::user()->company_name }}
                                            </small>
                                        @endif
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @php
                                        $userRoleId = DB::table('model_has_roles')
                                            ->where('model_id', Auth::id())
                                            ->where('model_type', 'App\\Models\\User')
                                            ->value('role_id');
                                    @endphp

                                    @if($userRoleId == 3) {{-- Administrateur --}}
                                        <li>
                                            <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('profile.tickets') }}" class="dropdown-item">
                                                <i class="fas fa-ticket-alt me-2"></i>Mes billets
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('payments.history') }}" class="dropdown-item">
                                                <i class="fas fa-history me-2"></i>Historique de paiement
                                            </a>
                                        </li>
                                    @elseif($userRoleId == 2) {{-- Organisateur --}}
                                        <li>
                                            <a href="{{ route('dashboard') }}" class="dropdown-item">
                                                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('organizer.tickets.index') }}" class="dropdown-item">
                                                <i class="fas fa-ticket-alt me-2"></i>Mes billets
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('payments.history') }}" class="dropdown-item">
                                                <i class="fas fa-history me-2"></i>Historique de paiement
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('custom-events.index') }}" class="dropdown-item">
                                                <i class="fas fa-calendar-plus me-2"></i>Mes événements personnalisés
                                            </a>
                                        </li>
                                    @else {{-- Client --}}
                                        <li>
                                            <a href="{{ route('profile.tickets') }}" class="dropdown-item">
                                                <i class="fas fa-ticket-alt me-2"></i>Mes billets
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('payments.history') }}" class="dropdown-item">
                                                <i class="fas fa-history me-2"></i>Historique de paiement
                                            </a>
                                        </li>
                                    @endif

                                    <li>
                                        @php
                                            $hasUnusedPurchase = DB::table('custom_offer_purchases')
                                                ->where('user_id', Auth::id())
                                                ->whereNull('used_at')
                                                ->exists();
                                            $hasHistory = Auth::user()->customEvents()->exists();
                                            $customLink = ($hasUnusedPurchase || $hasHistory)
                                                ? route('custom-events.index')
                                                : route('custom-offers.index');
                                        @endphp
                                        <a href="{{ $customLink }}" class="dropdown-item">
                                            <i class="fas fa-calendar-plus me-2"></i>Événement personnalisé
                                        </a>
                                    </li>

                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Barre d'annonces globale -->
        @php
            try {
                $globalAnnouncements = \App\Models\Announcement::active()
                    ->orderBy('display_order')
                    ->orderByDesc('is_urgent')
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();

                // Récupérer les événements populaires avec scoring composite
                $globalPopularEvents = \App\Models\Event::with(['category', 'organizer', 'tickets'])
                    ->withCount(['views', 'comments', 'shares'])
                    ->where('etat', '!=', 'Archivé')
                    ->where('is_approved', true)
                    ->where('is_published', true)
                    ->where('is_featured', true)
                    ->latest()
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                // En cas d'erreur, utiliser des collections vides
                $globalAnnouncements = collect();
                $globalPopularEvents = collect();
                \Log::error('Erreur lors du chargement des annonces/événements dans le layout: ' . $e->getMessage());
            }
        @endphp

        @if($globalAnnouncements->count() > 0 || $globalPopularEvents->count() > 0)
        <div class="container-fluid announcements-bar" style="position: sticky; top: 70px; z-index: 1035; background: var(--bleu-nuit);">
            <div class="ticker-track">
                <!-- Annonces -->
                @foreach($globalAnnouncements as $announcement)
                <div class="ticker-item {{ $announcement->is_urgent ? 'urgent' : 'announcement' }}">
                    <span class="fw-bold">
                        <i class="fas fa-{{ $announcement->is_urgent ? 'exclamation-triangle' : 'bullhorn' }} me-2 {{ $announcement->is_urgent ? 'pulse-icon' : '' }}"></i>
                        {{ $announcement->title }}
                    </span>
                    <span class="px-2">|</span>
                    <span>{{ Str::limit($announcement->content, 100) }}</span>
                </div>
                @endforeach

                <!-- Événements populaires -->
                @foreach($globalPopularEvents as $event)
                <div class="ticker-item event-popular">
                    <span class="fw-bold">
                        <i class="fas fa-fire me-2"></i>{{ $event->title }}
                    </span>
                    <span class="px-2">|</span>
                    <span>{{ Str::limit(strip_tags($event->description), 70) }}</span>
                    <span class="px-2">|</span>
                    <span>{{ \Carbon\Carbon::parse($event->start_date)->isoFormat('D MMMM YYYY') }}</span>
                </div>
                @endforeach

                <!-- Répétition pour continuité -->
                @foreach($globalAnnouncements as $announcement)
                <div class="ticker-item {{ $announcement->is_urgent ? 'urgent' : 'announcement' }}">
                    <span class="fw-bold">
                        <i class="fas fa-{{ $announcement->is_urgent ? 'exclamation-triangle' : 'bullhorn' }} me-2 {{ $announcement->is_urgent ? 'pulse-icon' : '' }}"></i>
                        {{ $announcement->title }}
                    </span>
                    <span class="px-2">|</span>
                    <span>{{ Str::limit($announcement->content, 100) }}</span>
                </div>
                @endforeach

                @foreach($globalPopularEvents as $event)
                <div class="ticker-item event-popular">
                    <span class="fw-bold">
                        <i class="fas fa-fire me-2"></i>{{ $event->title }}
                    </span>
                    <span class="px-2">|</span>
                    <span>{{ Str::limit(strip_tags($event->description), 70) }}</span>
                    <span class="px-2">|</span>
                    <span>{{ \Carbon\Carbon::parse($event->start_date)->isoFormat('D MMMM YYYY') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Alerte pour les utilisateurs non vérifiés -->
        @auth
            @if(!auth()->user()->email_verified_at)
            <div class="alert alert-warning py-2 mb-0 text-center">
                <div class="container">
                    <span class="small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Votre email n'est pas vérifié.
                        <a href="{{ route('verification.notice') }}" class="alert-link">Cliquez ici pour le vérifier</a>.
                    </span>
                </div>
            </div>
            @endif
        @endauth

        <!-- Page Header -->
        @section('page_header')
        @include('layouts.partials.page-header')
        @endsection

        <!-- Main Content -->
        <main class="animate-fade-in">
            <!-- Notifications Toast Container -->
            <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                @if (session('success'))
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
                        <div class="toast-header" style="background-color: var(--bleu-nuit); color: white;">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong class="me-auto">Succès</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body" style="color: #2d3748;">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="7000">
                        <div class="toast-header" style="background-color: var(--bleu-nuit); color: white;">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong class="me-auto">Erreur</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body" style="color: #2d3748;">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4000">
                        <div class="toast-header" style="background-color: var(--bleu-nuit); color: white;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong class="me-auto">Information</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            {{ session('info') }}
                        </div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="6000">
                        <div class="toast-header bg-warning text-dark">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong class="me-auto">Attention</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            {{ session('warning') }}
                        </div>
                    </div>
                @endif
            </div>

            @yield('content')
        </main>

        <!-- FOOTER OPTIMISÉ -->
        <footer class="footer-modern">
            <div class="container">
                <div class="footer-content">
                    <!-- Colonne 1: Brand & Paiements -->
                    <div class="footer-brand">
                        <h5 class="footer-logo">{{ DB::table('settings')->where('key', 'site_name')->value('value') ?? 'MokiliEvent' }}</h5>
                        <p class="footer-description">
                            {{ DB::table('settings')->where('key', 'site_description')->value('value') ?? 'Votre plateforme de billetterie d\'événements au Congo.' }}
                        </p>
                        <div class="payment-methods-compact">
                            <div class="payment-icon">
                                <img src="{{ asset('images/airtelmoney.jpg') }}" alt="Airtel">
                            </div>
                            <div class="payment-icon">
                                <img src="{{ asset('images/mtnmoney.jpg') }}" alt="MTN">
                            </div>
                            <div class="payment-icon">
                                <img src="{{ asset('images/visa.jpg') }}" alt="Visa">
                            </div>
                        </div>
                    </div>

                    <!-- Colonne 2: Liens rapides -->
                    <div class="footer-links">
                        <h6>Navigation</h6>
                        <a href="{{ route('home') }}">Accueil</a>
                        <a href="/direct-events">Événements</a>
                        <a href="{{ route('blogs.index') }}">Blog</a>
                        <a href="{{ route('about') }}">À propos</a>
                        <a href="{{ route('contact') }}">Contact</a>
                    </div>

                    <!-- Colonne 3: Contact -->
                    <div class="footer-contact">
                        <h6>Contact</h6>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ DB::table('settings')->where('key', 'address')->value('value') ?? 'Brazzaville, Congo' }}</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>{{ DB::table('settings')->where('key', 'phone_number')->value('value') ?? '+242 06 123 4567' }}</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>{{ DB::table('settings')->where('key', 'contact_email')->value('value') ?? 'contact@mokilievent.cg' }}</span>
                        </div>
                    </div>

                    <!-- Colonne 4: Newsletter & Réseaux -->
                    <div>
                        <h6 style="color: var(--jaune-or); margin-bottom: 1rem;">Restez connecté</h6>
                        
                        <!-- Newsletter -->
                        <div class="newsletter-compact">
                            <form action="{{ route('newsletter.subscribe') }}" method="POST">
                                @csrf
                                <div class="newsletter-input-group">
                                    <input type="email" name="email" placeholder="Votre email" required>
                                    <button type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Réseaux sociaux -->
                        <div class="social-links-compact">
                            @if(DB::table('settings')->where('key', 'facebook_url')->value('value'))
                            <a href="{{ DB::table('settings')->where('key', 'facebook_url')->value('value') }}" class="social-icon" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            @endif
                            @if(DB::table('settings')->where('key', 'twitter_url')->value('value'))
                            <a href="{{ DB::table('settings')->where('key', 'twitter_url')->value('value') }}" class="social-icon" target="_blank">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                            @endif
                            @if(DB::table('settings')->where('key', 'instagram_url')->value('value'))
                            <a href="{{ DB::table('settings')->where('key', 'instagram_url')->value('value') }}" class="social-icon" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                            @endif
                            @if(DB::table('settings')->where('key', 'tiktok_url')->value('value'))
                            <a href="{{ DB::table('settings')->where('key', 'tiktok_url')->value('value') }}" class="social-icon" target="_blank">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            @endif
                        </div>

                        <!-- App Stores -->
                        <div class="app-buttons">
                            <a href="#" class="app-btn" data-bs-toggle="modal" data-bs-target="#googlePlayModal">
                                <i class="fab fa-google-play"></i>
                            </a>
                            <a href="#" class="app-btn" data-bs-toggle="modal" data-bs-target="#appStoreModal">
                                <i class="fab fa-apple"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div class="footer-bottom">
                    <div>&copy; {{ date('Y') }} {{ DB::table('settings')->where('key', 'site_name')->value('value') ?? 'MokiliEvent' }}. Tous droits réservés.</div>
                    <div class="footer-bottom-links">
                        <a href="{{ route('terms') }}">Conditions</a>
                        <a href="{{ route('privacy') }}">Confidentialité</a>
                        <a href="{{ route('faq') }}">FAQ</a>
                    </div>
                </div>
            </div>
        </footer>

    <!-- Modal Google Play -->
    <div class="modal fade" id="googlePlayModal" tabindex="-1" aria-labelledby="googlePlayModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="googlePlayModalLabel">Google Play</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body" style="color: black;">
            Le téléchargement via Google Play sera disponible très bientôt. Restez connecté !
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-jaune-or" data-bs-dismiss="modal">Fermer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal App Store -->
    <div class="modal fade" id="appStoreModal" tabindex="-1" aria-labelledby="appStoreModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="appStoreModalLabel">App Store</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body" style="color: black;">
            Le téléchargement via l'App Store sera disponible très bientôt. Merci de votre patience !
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-jaune-or" data-bs-dismiss="modal">Fermer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Créer un événement -->
    <div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createEventModalLabel">
              <i class="fas fa-plus-circle me-2"></i>Créer un événement
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <p class="text-center mb-4" style="color: var(--text-color);">
              Choisissez le type d'événement que vous souhaitez créer
            </p>
            <div class="row g-3">
              @php
                $user = auth()->user();
                $isOrganizer = false;
                
                if ($user) {
                  $roleIds = $user->roles ? $user->roles->pluck('id')->toArray() : [];
                  $userRoles = $user->getRoleNames()->toArray();
                  $isOrganizer = in_array(3, $roleIds) || in_array(2, $roleIds);
                  if (!$isOrganizer) {
                    $isOrganizer = in_array('Administrateur', $userRoles) || 
                                  in_array('administrateur', array_map('strtolower', $userRoles)) ||
                                  in_array('Organizer', $userRoles) || 
                                  in_array('organizer', array_map('strtolower', $userRoles)) ||
                                  (method_exists($user, 'isOrganizer') && $user->isOrganizer()) || 
                                  (method_exists($user, 'isAdmin') && $user->isAdmin());
                  }
                }
              @endphp
              
              @if($isOrganizer)
              <!-- Événement Simple (avec billets) - Seulement pour organisateurs et admins -->
              <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm event-type-card-modal" style="cursor: pointer; transition: all 0.3s ease;" onclick="window.location.href='{{ route('events.wizard.step1') }}'">
                  <div class="card-body p-4 text-center">
                    <div class="mb-3">
                      <div class="event-icon-modal bg-primary-gradient" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-calendar-alt fa-2x text-white"></i>
                      </div>
                    </div>
                    <h5 class="fw-bold mb-2">Événement Simple</h5>
                    <p class="text-muted small mb-3">
                      Créez un événement avec des billets, des catégories et des options de paiement standard.
                    </p>
                    <ul class="list-unstyled text-start small mb-3" style="color: var(--text-color);">
                      <li><i class="fas fa-check-circle text-primary me-2"></i>Vente de billets en ligne</li>
                      <li><i class="fas fa-check-circle text-primary me-2"></i>Gestion des catégories</li>
                      <li><i class="fas fa-check-circle text-primary me-2"></i>Paiements sécurisés</li>
                      <li><i class="fas fa-check-circle text-primary me-2"></i>Statistiques détaillées</li>
                    </ul>
                    <button type="button" class="btn btn-primary w-100">
                      <i class="fas fa-rocket me-2"></i>Créer maintenant
                    </button>
                  </div>
                </div>
              </div>
              @endif
              
              <!-- Événement Personnalisé - Pour tous les utilisateurs -->
              <div class="col-md-{{ $isOrganizer ? '6' : '12' }}">
                <div class="card h-100 border-0 shadow-sm event-type-card-modal" style="cursor: pointer; transition: all 0.3s ease; border-top: 3px solid #ffc107 !important;" onclick="window.location.href='{{ route('custom-offers.index') }}'">
                  <div class="card-body p-4 text-center position-relative">
                    @if($isOrganizer)
                    <span class="badge bg-warning text-dark position-absolute" style="top: 10px; right: 10px;">Populaire</span>
                    @endif
                    <div class="mb-3">
                      <div class="event-icon-modal bg-warning-gradient" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="fas fa-star fa-2x text-white"></i>
                      </div>
                    </div>
                    <h5 class="fw-bold mb-2">Événement Personnalisé</h5>
                    <p class="text-muted small mb-3">
                      Créez un événement personnalisé avec des options avancées de gestion des invités.
                    </p>
                    <ul class="list-unstyled text-start small mb-3" style="color: var(--text-color);">
                      <li><i class="fas fa-check-circle text-warning me-2"></i>Gestion des invités</li>
                      <li><i class="fas fa-check-circle text-warning me-2"></i>Invitations personnalisées</li>
                      <li><i class="fas fa-check-circle text-warning me-2"></i>Suivi en temps réel</li>
                      <li><i class="fas fa-check-circle text-warning me-2"></i>Formules adaptées</li>
                    </ul>
                    <button type="button" class="btn btn-warning w-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; color: white;">
                      <i class="fas fa-shopping-cart me-2"></i>Choisir une offre
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-2"></i>Annuler
            </button>
          </div>
        </div>
      </div>
    </div>
</footer>
    </div>
        <script>
        (function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="0vQL1GfVjku9H12unrCQo";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
        
        // Bloquer le menu contextuel (clic droit)
document.addEventListener("contextmenu", event => {
    event.preventDefault();
    afficherMessageAlerte();
    return false;
  });
        </script>
        <script>
        // Gestion du loader moderne
        document.addEventListener('DOMContentLoaded', function() {
            // Cache le loader une fois que tout est chargé
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const pageLoader = document.getElementById('pageLoader');
                    const app = document.getElementById('app');

                    if (pageLoader) {
                        pageLoader.classList.add('fade-out');
                    }

                    if (app) {
                        app.style.opacity = '1';
                    }

                    // Supprime le loader après l'animation
                    setTimeout(function() {
                        if (pageLoader) {
                            pageLoader.remove();
                        }
                    }, 600);
                }, 1000); // 1 seconde de délai
            });

            // Toast Notifications Enhancement
            var toastElements = document.querySelectorAll('.toast');
            toastElements.forEach(function(toastElement) {
                var toast = new bootstrap.Toast(toastElement, {
                    autohide: true,
                    delay: parseInt(toastElement.getAttribute('data-bs-delay')) || 5000
                });
                
                // Show toast
                toast.show();
                
                // Add fade-out animation when hiding
                toastElement.addEventListener('hidden.bs.toast', function() {
                    toastElement.classList.add('fade-out');
                    setTimeout(function() {
                        toastElement.remove();
                    }, 300);
                });
            });
        });

        // Function to show custom toast notifications
        function showToast(message, type = 'info', duration = 5000) {
            const toastContainer = document.getElementById('toast-container');
            const toastId = 'toast-' + Date.now();
            
            const typeConfig = {
                success: { icon: 'fas fa-check-circle', bg: 'var(--bleu-nuit)', text: 'text-white', title: 'Succès' },
                error: { icon: 'fas fa-exclamation-circle', bg: 'var(--bleu-nuit)', text: 'text-white', title: 'Erreur' },
                warning: { icon: 'fas fa-exclamation-triangle', bg: 'bg-warning', text: 'text-dark', title: 'Attention' },
                info: { icon: 'fas fa-info-circle', bg: 'bg-info', text: 'text-white', title: 'Information' }
            };
            
            const config = typeConfig[type] || typeConfig.info;
            
            const toastHTML = `
                <div id="${toastId}" class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="${duration}">
                    <div class="toast-header" style="background-color: ${config.bg === 'var(--bleu-nuit)' ? config.bg : 'var(--bleu-nuit)'}; color: white;">
                        <i class="${config.icon} me-2"></i>
                        <strong class="me-auto">${config.title}</strong>
                        <button type="button" class="btn-close ${config.text.includes('white') ? 'btn-close-white' : ''}" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: duration
            });
            
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.classList.add('fade-out');
                setTimeout(function() {
                    toastElement.remove();
                }, 300);
            });
        }
    </script>
    @include('components.auth-modals')
    @stack('modals')
</body>
</html>