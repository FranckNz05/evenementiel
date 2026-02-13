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

    <title>Inscription - {{ config('app.name', 'MokiliEvent') }}</title>

    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>

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

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bleu-nuit-pale);
            color: var(--bleu-nuit);
            display: flex;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .register-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 40px 20px;
        }

        .register-container {
            width: 100%;
            max-width: 520px;
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
            text-decoration: underline;
            text-decoration-color: var(--bleu-nuit);
            text-underline-offset: 2px;
            text-decoration-thickness: 2px;
        }

        .link-accent::after { display: none; }

        .link-accent:hover::after { display: none; }

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

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 32px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--bleu-nuit);
            opacity: 0.7;
            font-size: 1.1rem;
            padding: 8px;
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

        .terms-container {
            background-color: var(--bleu-nuit-pale);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            border-left: 4px solid var(--blanc-or);
        }

        .terms-text {
            font-size: 0.9rem;
            color: var(--bleu-nuit);
            line-height: 1.5;
        }

        .terms-links {
            display: flex;
            gap: 16px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .terms-links a {
            color: var(--bleu-nuit);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .terms-links a:hover {
            color: var(--bleu-nuit-clair);
        }

        .terms-links a i {
            font-size: 0.8rem;
        }

        .form-check {
            display: flex;
            align-items: flex-start;
            margin-top: 12px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            margin-top: 3px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: var(--bleu-nuit);
            cursor: pointer;
            line-height: 1.4;
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
            color: #ffffff;
            font-weight: 700;
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
            box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1) !important;
        }

        .btn-social {
            transition: all 0.3s ease;
        }

        .btn-social:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-facebook:hover {
            background: #166fe5 !important;
        }

        .btn-google:hover {
            background: #f8f9fa !important;
            border-color: #dadce0 !important;
        }

        /* Styles améliorés pour le champ téléphone */
.phone-input-container {
    display: flex;
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 52px;
}

.phone-input-container:hover {
    border-color: var(--bleu-nuit-clair);
}

.phone-input-container:focus-within {
    border-color: var(--bleu-nuit-clair);
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.phone-input-container .iti {
    width: 100%;
    height: 100%;
}

.iti__selected-flag {
    padding: 0 8px 0 12px !important;
    height: 100%;
    display: flex !important;
    align-items: center;
    background-color: #f8fafc;
    border-right: 1px solid #e2e8f0;
    min-width: 100px;
}

.iti__flag-container {
    padding: 0 !important;
    margin-right: 6px;
    display: flex !important;
    align-items: center;
}

.iti__selected-dial-code {
    font-size: 1rem !important;
    color: var(--bleu-nuit) !important;
    margin-right: 4px !important;
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.iti__arrow {
    border-top: 4px solid var(--bleu-nuit);
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    margin-left: 6px;
}

#phone {
    padding: 14px 16px !important;
    height: 100%;
    font-size: 1rem;
    padding-left: 120px !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

/* Style du dropdown des pays */
.iti__country-list {
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(15, 26, 61, 0.15);
    border: none;
    margin-top: 8px;
    width: 350px;
    max-height: 300px;
    overflow-y: auto;
}

.iti__country {
    padding: 8px 12px;
    display: flex;
    align-items: center;
}

.iti__country:hover, .iti__country.iti__highlight {
    background-color: var(--bleu-nuit-pale);
}

.iti__country-name, .iti__dial-code {
    font-size: 0.95rem;
}

.iti__divider {
    padding: 8px 12px;
    font-size: 0.9rem;
    color: var(--bleu-nuit);
    font-weight: 500;
    border-bottom: 1px solid #e2e8f0;
}

/* Style pour le placeholder */
.iti__flag-container::after {
    content: "Ex: +242 06 123 4567";
    position: absolute;
    left: 120px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.9rem;
    pointer-events: none;
    opacity: 0.7;
}

.iti--show-flags .iti__flag-container::after {
    display: none;
}

/* Style pour la version mobile */
@media (max-width: 576px) {
    .iti__country-list {
        width: 100%;
        left: 0 !important;
    }

    .iti__flag-container::after {
        content: "Ex: 06 123 4567";
        left: 90px;
        font-size: 0.8rem;
    }
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

            .terms-links {
                flex-direction: column;
                gap: 8px;
            }

            .iti__selected-dial-code {
                font-size: 0.9rem;
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
        }

        /* Styles pour le loader - Spinner moderne */
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
            border-top-color: var(--blanc-or);
            border-right-color: var(--blanc-or);
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
            border-top-color: var(--blanc-or);
            border-right-color: var(--blanc-or);
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
            filter: drop-shadow(0 0 3px var(--blanc-or));
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
            background: var(--blanc-or);
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
    </style>
</head>
<body>
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
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>
        </div>
    </div>
    <div class="register-wrapper">
        <div class="register-container">
            <div class="card">
                <div class="card-header">
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <img src="{{ asset('images/logo.png') }}" alt="MokiliEvent" class="logo-img">
                        </a>
                    </div>
                    <h3>Crée ton compte et entre dans la vibe de <span class="highlight">MokiliEvent</span></h3>
                    <p>Inscris-toi et vis les grands moments</p>
                </div>
                <div class="card-body">
                    <!-- Affichage des erreurs globales -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs de validation</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" id="registerForm">
                        @csrf

                        <div class="row">
                            <!-- Prénom -->
                            <div class="col-md-6 form-group">
                                <label for="prenom" class="form-label">Prénom</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-user"></i>
                                    <input id="prenom" type="text" class="form-control @error('prenom') is-invalid @enderror" name="prenom" value="{{ old('prenom') }}" required autofocus placeholder="Votre prénom">
                                </div>
                                @error('prenom')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Nom -->
                            <div class="col-md-6 form-group">
                                <label for="nom" class="form-label">Nom</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-user-tag"></i>
                                    <input id="nom" type="text" class="form-control @error('nom') is-invalid @enderror" name="nom" value="{{ old('nom') }}" required placeholder="Votre nom">
                                </div>
                                @error('nom')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="exemple@email.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone" class="form-label">Téléphone</label>
                            <div class="phone-input-container">
                                <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    name="phone" value="{{ old('phone') }}" required>
                            </div>
                            <span id="phone-error" class="invalid-feedback d-none" role="alert"></span>
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="••••••••">
                                <span class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="••••••••">
                                <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="terms-container">
                            <p class="terms-text">
                                En vous inscrivant, vous acceptez nos conditions d'utilisation et notre politique de confidentialité.
                            </p>
                            <div class="terms-links">
                                <a href="{{ route('terms') }}">
                                    <i class="fas fa-file-contract"></i>
                                    <span>Conditions d'utilisation</span>
                                </a>
                                <a href="{{ route('privacy') }}">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>Politique de confidentialité</span>
                                </a>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" value="1" required>
                            <label class="form-check-label" for="terms">
                                Je reconnais avoir lu et j'accepte les conditions mentionnées ci-dessus, y compris la collecte et l'utilisation de mes données personnelles
                            </label>
                            @error('terms')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Social Login Buttons -->
                        <div class="social-login" style="margin-bottom: 20px; margin-top: 20px;">
                            <div style="text-align: center; margin-bottom: 15px; position: relative;">
                                <span style="background: white; padding: 0 15px; color: #666; font-size: 0.9rem;">Ou s'inscrire avec</span>
                                <hr style="position: absolute; top: 50%; left: 0; right: 0; margin: 0; border: none; border-top: 1px solid #e0e0e0; z-index: -1;">
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('social.redirect', 'facebook') }}" class="btn-social btn-facebook" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; background: #1877f2; color: white; text-decoration: none; font-weight: 500; transition: all 0.3s;">
                                    <i class="fab fa-facebook-f"></i>
                                    <span>Facebook</span>
                                </a>
                                <a href="{{ route('social.redirect', 'google') }}" class="btn-social btn-google" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; background: white; color: #333; text-decoration: none; font-weight: 500; transition: all 0.3s;">
                                    <i class="fab fa-google"></i>
                                    <span>Google</span>
                                </a>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4">
                            <i class="fas fa-user-plus"></i>
                            <span>S'inscrire</span>
                        </button>

                        <div class="text-center">
                            <p>Déjà un compte ? <a href="{{ route('login') }}" class="link-accent">Connectez-vous</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js" onload="window.intlTelInputLoaded = true"></script>

    <script>
        // Fonction pour basculer la visibilité du mot de passe
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');

            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                field.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // Initialisation améliorée de intl-tel-input
        function initializePhoneInput() {
            const phoneInput = document.querySelector("#phone");
            if (!phoneInput) {
                // Réessayer si le DOM n'est pas encore prêt
                setTimeout(initializePhoneInput, 100);
                return;
            }

            // Vérifier que intlTelInput est disponible
            if (typeof window.intlTelInput === 'undefined' || !window.intlTelInputLoaded) {
                // Réessayer après un court délai
                setTimeout(initializePhoneInput, 100);
                return;
            }

            const phoneError = document.querySelector("#phone-error");
            const errorMap = ["Numéro invalide", "Code pays invalide", "Trop court", "Trop long", "Numéro invalide"];

            // Initialiser avec un pays par défaut (Congo) en cas d'échec de la détection
            let defaultCountry = "cg";
            let geoIpTimeout;

            const iti = window.intlTelInput(phoneInput, {
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                preferredCountries: ['cg', 'fr', 'be', 'ch', 'ca', 'us'],
                separateDialCode: true,
                initialCountry: "auto",
                autoPlaceholder: "polite",
                customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                    return "Ex: " + selectedCountryPlaceholder.replace(/\d/g, 'X');
                },
                dropdownContainer: document.body,
                formatOnDisplay: true,
                geoIpLookup: function(callback) {
                    // Timeout pour éviter que le callback ne soit jamais appelé
                    geoIpTimeout = setTimeout(function() {
                        callback(defaultCountry);
                    }, 2000);

                    fetch("https://ipapi.co/json")
                        .then(res => {
                            clearTimeout(geoIpTimeout);
                            if (res.ok) {
                                return res.json();
                            }
                            throw new Error('Network response was not ok');
                        })
                        .then(data => {
                            clearTimeout(geoIpTimeout);
                            if (data && data.country_code) {
                                callback(data.country_code.toLowerCase());
                            } else {
                                callback(defaultCountry);
                            }
                        })
                        .catch(() => {
                            clearTimeout(geoIpTimeout);
                            callback(defaultCountry);
                        });
                }
            });

            // Stocker l'instance pour l'utiliser lors de la soumission
            phoneInputInstance = iti;
            phoneInput.intlTelInputInstance = iti;

            // Forcer l'affichage du code pays après initialisation
            setTimeout(function() {
                if (iti && phoneInput) {
                    const countryData = iti.getSelectedCountryData();
                    if (countryData && countryData.dialCode) {
                        // S'assurer que le code pays est visible
                        const flagContainer = phoneInput.closest('.phone-input-container')?.querySelector('.iti__selected-flag');
                        if (flagContainer) {
                            flagContainer.style.display = 'flex';
                        }
                        // Mettre à jour le placeholder
                        if (countryData.placeholder) {
                            phoneInput.placeholder = "Ex: " + countryData.placeholder.replace(/\d/g, 'X');
                        }
                    }
                }
            }, 500);

            // Gestion du placeholder dynamique
            phoneInput.addEventListener('countrychange', function() {
                const countryData = iti.getSelectedCountryData();
                if (countryData && countryData.placeholder) {
                    phoneInput.placeholder = "Ex: " + countryData.placeholder.replace(/\d/g, 'X');
                }
            });

            // Validation améliorée
            function validatePhoneNumber() {
                if (phoneInput.value.trim()) {
                    if (iti.isValidNumber()) {
                        phoneInput.classList.remove("is-invalid");
                        if (phoneError) {
                            phoneError.classList.add("d-none");
                        }
                        return true;
                    } else {
                        const errorCode = iti.getValidationError();
                        if (phoneError) {
                            phoneError.textContent = errorMap[errorCode] || "Numéro de téléphone invalide";
                            phoneError.classList.remove("d-none");
                        }
                        phoneInput.classList.add("is-invalid");
                        return false;
                    }
                }
                return false;
            }

            // Événements de validation
            phoneInput.addEventListener('blur', validatePhoneNumber);
            phoneInput.addEventListener('change', validatePhoneNumber);
            phoneInput.addEventListener('keyup', function() {
                if (phoneInput.classList.contains("is-invalid")) {
                    validatePhoneNumber();
                }
            });

            // Focus sur le champ numéro quand on clique sur le container
            const phoneContainer = document.querySelector('.phone-input-container');
            if (phoneContainer) {
                phoneContainer.addEventListener('click', function() {
                    phoneInput.focus();
                });
            }
        }

        // Initialiser après le chargement complet
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializePhoneInput);
        } else {
            // DOM déjà chargé, initialiser immédiatement
            initializePhoneInput();
        }


        // Script pour gérer l'affichage du loader
        function hidePageLoader() {
            const pageLoader = document.getElementById('pageLoader');

            if (pageLoader) {
                pageLoader.classList.add('fade-out');
            }

            // Supprime le loader après l'animation
            setTimeout(() => {
                if (pageLoader) {
                    pageLoader.style.display = 'none';
                }
            }, 600);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Masquer le loader quand la page est chargée
            if (document.readyState === 'complete') {
                hidePageLoader();
            } else {
                window.addEventListener('load', hidePageLoader);
                setTimeout(hidePageLoader, 5000); // Timeout de secours
            }
        });

        // Protection contre les doubles soumissions et formatage du téléphone
        const registerForm = document.getElementById('registerForm');
        let isSubmitting = false;
        let phoneInputInstance = null;

        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                // Empêcher les doubles soumissions
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }

                // Formater le numéro de téléphone avec le code pays avant soumission
                const phoneInput = document.querySelector("#phone");
                if (phoneInput && window.intlTelInput) {
                    // Récupérer l'instance intlTelInput si elle existe
                    const itiInstance = phoneInput.intlTelInputInstance || phoneInputInstance;
                    if (itiInstance) {
                        // Obtenir le numéro complet avec le code pays
                        const fullNumber = itiInstance.getNumber();
                        if (fullNumber) {
                            phoneInput.value = fullNumber;
                        }
                    }
                }

                // Marquer comme en cours de soumission
                isSubmitting = true;

                // Désactiver le bouton de soumission
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalHTML = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><span>Inscription en cours...</span>';
                    
                    // Réactiver si la validation échoue (au cas où)
                    setTimeout(() => {
                        if (!document.hidden) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalHTML;
                            isSubmitting = false;
                        }
                    }, 5000);
                }

                return true;
            });
        }
    </script>
</body>
</html>
