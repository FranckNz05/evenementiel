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

    <title>Connexion - {{ config('app.name', 'MokiliEvent') }}</title>

    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

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

        /* Loader styles - Spinner moderne */
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

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 54px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--bleu-nuit);
            opacity: 0.7;
            font-size: 1.1rem;
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

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 24px 0;
            flex-wrap: wrap;
        }

        .form-check {
            display: flex;
            align-items: center;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-check-label {
            font-size: 0.95rem;
            color: var(--bleu-nuit);
            cursor: pointer;
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

            .form-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .link-accent {
                margin-top: 8px;
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
    </style>
</head>
<body>
    <!-- Loader qui disparaît une fois la page chargée -->
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

    <div class="login-wrapper" id="loginContent">
        <div class="login-container">
            <div class="card">
                <div class="card-header">
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <img src="{{ asset('images/logo.png') }}" alt="MokiliEvent" class="logo-img">
                        </a>
                    </div>
                    <h3>Accède à tes évènements en un clic. <span class="highlight">MokiliEvent</span></h3>
                    <p>Ta billetterie, ton espace</p>
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autofocus placeholder="exemple@email.com">
                            </div>
                            @error('email')
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
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password" required placeholder="••••••••">
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

                        <div class="form-footer">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Se souvenir de moi
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="link-accent">
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>

                        <!-- Social Login Buttons -->
                        <div class="social-login" style="margin-bottom: 20px;">
                            <div style="text-align: center; margin-bottom: 15px; position: relative;">
                                <span style="background: white; padding: 0 15px; color: #666; font-size: 0.9rem;">Ou continuer avec</span>
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

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Se connecter</span>
                        </button>

                        <div class="text-center">
                            <p>Vous n'avez pas de compte ? <a href="{{ route('register') }}" class="link-accent">Inscrivez-vous</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script pour gérer l'affichage du loader
        function hidePageLoader() {
            const pageLoader = document.getElementById('pageLoader');
            const loginContent = document.getElementById('loginContent');

            if (pageLoader) {
                pageLoader.classList.add('fade-out');
            }

            if (loginContent) {
                loginContent.classList.add('loaded');
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
    </script>
</body>
</html>
