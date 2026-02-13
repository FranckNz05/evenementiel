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

    <title>Réinitialisation de mot de passe - {{ config('app.name', 'MokiliEvent') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bleu-nuit: #0f1a3d;
            --bleu-nuit-clair: #1a237e;
            --bleu-nuit-pale: #f0f2f7;
            --blanc-or: #ffffff;
            --blanc-or-fonce: #f8fafc;
            --blanc-or-pale: #f8fafc;
        }

        /* Loader styles */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out;
        }

        .page-loader.fade-out {
            opacity: 0;
            pointer-events: none;
        }

        .spinner {
            width: 70px;
            text-align: center;
        }

        .spinner > div {
            width: 18px;
            height: 18px;
            border-radius: 100%;
            display: inline-block;
            animation: sk-bouncedelay 1.4s infinite ease-in-out both;
        }

        .spinner .bounce1 {
            background-color: var(--bleu-nuit);
            animation-delay: -0.32s;
        }

        .spinner .bounce2 {
            background-color: var(--blanc-or);
            animation-delay: -0.16s;
        }

        .spinner .bounce3 {
            background-color: var(--bleu-nuit);
        }

        @keyframes sk-bouncedelay {
            0%, 80%, 100% {
                transform: scale(0);
            }
            40% {
                transform: scale(1.0);
            }
        }

        /* Hide content during loading */
        .login-wrapper {
            opacity: 0;
            transition: opacity 0.5s ease-in;
        }

        .login-wrapper.loaded {
            opacity: 1;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bleu-nuit-pale);
            color: var(--bleu-nuit);
            display: flex;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .login-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 40px 20px;
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }

        .card {
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 26, 61, 0.12);
            border: none;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(15, 26, 61, 0.15);
        }

        .card-header {
            background-color: var(--bleu-nuit);
            color: white;
            padding: 28px;
            text-align: center;
            border-bottom: 4px solid var(--blanc-or);
        }

        .card-header h3 {
            font-size: 1.75rem;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .card-header p {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .card-body {
            padding: 40px;
        }

        .logo-container {
            margin: 0 auto 30px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 3px solid var(--blanc-or);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .logo-img {
            max-height: 70px;
            width: auto;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--bleu-nuit);
            font-size: 0.95rem;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: var(--bleu-nuit);
            opacity: 0.7;
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 1rem;
            transition: all 0.3s ease;
            height: 52px;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--bleu-nuit-clair);
            box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
            outline: none;
        }

        .btn-primary {
            background-color: var(--bleu-nuit);
            border: none;
            border-radius: 8px;
            padding: 16px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            height: 52px;
            box-shadow: 0 4px 6px rgba(15, 26, 61, 0.1);
        }

        .btn-primary:hover {
            background-color: var(--bleu-nuit-clair);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(15, 26, 61, 0.15);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .text-center {
            text-align: center;
            margin-top: 24px;
        }

        .text-center p {
            color: var(--bleu-nuit);
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .highlight {
            color: var(--blanc-or-fonce);
            font-weight: 600;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 6px;
            display: block;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
        }

        .link-accent {
            color: var(--bleu-nuit);
            font-weight: 500;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s ease;
            position: relative;
        }

        .link-accent:hover {
            color: var(--bleu-nuit-clair);
        }

        .link-accent::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--blanc-or);
            transition: width 0.3s ease;
        }

        .link-accent:hover::after {
            width: 100%;
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 30px 24px;
            }

            .card-header {
                padding: 20px;
            }

            .card-header h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loader qui disparaît une fois la page chargée -->
    <div class="page-loader" id="pageLoader">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>

    <div class="login-wrapper" id="loginContent">
        <div class="login-container">
            <div class="card">
                <div class="card-header">
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <img src="{{ asset('images/logo.png') }}" alt="MokiliEvent" class="logo-img">
                        </a>
                    </div>
                    <h3>Réinitialisation de mot de passe <span class="highlight">MokiliEvent</span></h3>
                    <p>Retrouvez l'accès à votre compte</p>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <p class="text-center mb-4">Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                       placeholder="exemple@email.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            <span>Envoyer le lien de réinitialisation</span>
                        </button>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="link-accent">
                                <i class="fas fa-arrow-left me-1"></i>Retour à la connexion
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script pour gérer l'affichage du loader
        document.addEventListener('DOMContentLoaded', function() {
            // Cache le loader une fois que tout est chargé
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const pageLoader = document.getElementById('pageLoader');
                    const loginContent = document.getElementById('loginContent');

                    if (pageLoader) {
                        pageLoader.classList.add('fade-out');
                    }

                    if (loginContent) {
                        loginContent.classList.add('loaded');
                    }

                    // Supprime le loader après l'animation
                    setTimeout(function() {
                        if (pageLoader) {
                            pageLoader.remove();
                        }
                    }, 500);
                }, 300); // Petit délai pour s'assurer que tout est bien chargé
            });
        });
    </script>
</body>
</html>