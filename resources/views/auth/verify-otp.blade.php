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

    <title>Vérification OTP - {{ config('app.name', 'MokiliEvent') }}</title>

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
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
            color: var(--bleu-nuit);
            display: flex;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            align-items: center;
            justify-content: center;
        }

        .verify-wrapper {
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
            color: white;
            padding: 32px;
            text-align: center;
        }

        .logo-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .logo-img {
            max-width: 50px;
            height: auto;
        }

        .card-header h3 {
            font-size: 1.5rem;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .card-header p {
            font-size: 0.95rem;
            opacity: 0.9;
            margin: 0;
        }

        .card-body {
            padding: 40px;
        }

        .email-display {
            text-align: center;
            margin-bottom: 32px;
            padding: 16px;
            background: var(--bleu-nuit-pale);
            border-radius: 12px;
        }

        .email-display p {
            margin: 0;
            font-size: 0.9rem;
            color: #64748b;
        }

        .email-display strong {
            color: var(--bleu-nuit);
            font-size: 1rem;
            word-break: break-all;
        }

        .otp-container {
            margin-bottom: 32px;
        }

        .otp-label {
            display: block;
            margin-bottom: 12px;
            font-weight: 600;
            color: var(--bleu-nuit);
            font-size: 0.95rem;
            text-align: center;
        }

        .otp-inputs {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 16px;
        }

        .otp-input {
            width: 56px;
            height: 64px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            text-align: center;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--bleu-nuit);
            transition: all 0.2s ease;
            background: white;
        }

        .otp-input:focus {
            outline: none;
            border-color: var(--bleu-nuit-clair);
            box-shadow: 0 0 0 4px rgba(26, 35, 126, 0.1);
            transform: scale(1.05);
        }

        .otp-input.filled {
            border-color: var(--success);
            background: #f0fdf4;
        }

        .otp-input.error {
            border-color: var(--error);
            background: #fef2f2;
            animation: shake 0.3s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .countdown-container {
            text-align: center;
            margin-bottom: 24px;
        }

        .countdown {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--bleu-nuit-pale);
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--bleu-nuit);
            font-weight: 500;
        }

        .countdown.expired {
            background: #fef2f2;
            color: var(--error);
        }

        .countdown i {
            font-size: 1rem;
        }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 26, 61, 0.3);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-link {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: var(--bleu-nuit-clair);
            border: 2px solid var(--bleu-nuit-clair);
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-link:hover:not(:disabled) {
            background: var(--bleu-nuit-clair);
            color: white;
        }

        .btn-link:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .alert-success {
            background: #f0fdf4;
            color: #065f46;
            border-left: 4px solid var(--success);
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid var(--error);
        }

        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        .alert i {
            font-size: 1.2rem;
        }

        .invalid-feedback {
            color: var(--error);
            font-size: 0.85rem;
            margin-top: 8px;
            text-align: center;
            display: block;
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 24px;
            }

            .card-header {
                padding: 24px;
            }

            .otp-input {
                width: 48px;
                height: 56px;
                font-size: 1.5rem;
            }

            .otp-inputs {
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="verify-wrapper">
        <div class="card">
            <div class="card-header">
                <div class="logo-container">
                    <img src="{{ asset('images/logo.png') }}" alt="MokiliEvent" class="logo-img">
                </div>
                <h3>Vérification OTP</h3>
                <p>Entrez le code à 6 chiffres</p>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('message'))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ session('message') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="email-display">
                    <p>Code envoyé à :</p>
                    <strong>{{ session('email') }}</strong>
                </div>

                <form method="POST" action="{{ route('verify.otp') }}" id="otpForm">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('email') }}">
                    <input type="hidden" name="otp" id="otpValue" required>

                    <div class="otp-container">
                        <label class="otp-label">Code de vérification</label>
                        <div class="otp-inputs" id="otpInputs">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="0">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="1">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="2">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="3">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="4">
                            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off" data-index="5">
                        </div>
                        <div class="invalid-feedback" id="otpError" style="display: none;"></div>
                    </div>

                    <div class="countdown-container">
                        <div class="countdown" id="countdown">
                            <i class="fas fa-clock"></i>
                            <span id="countdownText">04:00</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-check-circle"></i>
                        <span>Vérifier</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('resend.otp') }}" id="resendForm">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('email') }}">
                    <button type="submit" class="btn-link" id="resendBtn">
                        <i class="fas fa-redo"></i>
                        <span>Renvoyer le code</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('.otp-input');
            const otpValueInput = document.getElementById('otpValue');
            const submitBtn = document.getElementById('submitBtn');
            const resendBtn = document.getElementById('resendBtn');
            const otpForm = document.getElementById('otpForm');
            const resendForm = document.getElementById('resendForm');
            const otpError = document.getElementById('otpError');
            const countdownElement = document.getElementById('countdown');
            const countdownText = document.getElementById('countdownText');

            // Calculer le temps d'expiration depuis la base de données
            @php
                // Utiliser la variable passée depuis le contrôleur, sinon la session, sinon null
                $expiresAt = isset($otp_expires_at) && $otp_expires_at 
                    ? \Carbon\Carbon::parse($otp_expires_at)->timestamp 
                    : (session('otp_expires_at') 
                        ? \Carbon\Carbon::parse(session('otp_expires_at'))->timestamp 
                        : null);
            @endphp
            @if($expiresAt)
                let expiresAt = {{ $expiresAt }};
                let countdownInterval;

            // Fonction pour mettre à jour le compte à rebours
            function updateCountdown() {
                const now = Math.floor(Date.now() / 1000);
                const diff = expiresAt - now;

                if (diff <= 0) {
                    countdownText.textContent = 'Code expiré';
                    countdownElement.classList.add('expired');
                    if (resendBtn) resendBtn.disabled = false;
                    if (submitBtn) submitBtn.disabled = true;
                    clearInterval(countdownInterval);
                    return;
                }

                const minutes = Math.floor(diff / 60);
                const seconds = diff % 60;
                countdownText.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }

            // Démarrer le compte à rebours
            updateCountdown();
            countdownInterval = setInterval(updateCountdown, 1000);

            // Gestion de la saisie OTP
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = value;

                    if (value) {
                        e.target.classList.add('filled');
                        e.target.classList.remove('error');
                        
                        // Passer au champ suivant
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    } else {
                        e.target.classList.remove('filled');
                    }

                    updateOTPValue();
                });

                input.addEventListener('keydown', function(e) {
                    // Supprimer et revenir en arrière
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                        otpInputs[index - 1].value = '';
                        otpInputs[index - 1].classList.remove('filled');
                    }
                    
                    // Coller le code complet
                    if (e.key === 'v' && (e.ctrlKey || e.metaKey)) {
                        e.preventDefault();
                    }
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                    
                    if (pastedData.length === 6) {
                        pastedData.split('').forEach((digit, i) => {
                            if (otpInputs[i]) {
                                otpInputs[i].value = digit;
                                otpInputs[i].classList.add('filled');
                            }
                        });
                        updateOTPValue();
                        otpInputs[5].focus();
                    }
                });

                input.addEventListener('focus', function() {
                    this.select();
                });
            });

            // Mettre à jour la valeur OTP cachée
            function updateOTPValue() {
                const otp = Array.from(otpInputs).map(input => input.value).join('');
                otpValueInput.value = otp;
                
                // Activer/désactiver le bouton de soumission
                if (otp.length === 6) {
                    submitBtn.disabled = false;
                    otpError.style.display = 'none';
                } else {
                    submitBtn.disabled = true;
                }
            }

            // Focus sur le premier champ au chargement
            otpInputs[0].focus();

            // Gestion de la soumission du formulaire
            otpForm.addEventListener('submit', function(e) {
                const otp = otpValueInput.value;
                
                if (otp.length !== 6) {
                    e.preventDefault();
                    showError('Veuillez entrer un code à 6 chiffres');
                    return;
                }

                // Vérifier si le code a expiré
                const now = Math.floor(Date.now() / 1000);
                if (now > expiresAt) {
                    e.preventDefault();
                    showError('Le code a expiré. Veuillez demander un nouveau code.');
                    resendBtn.disabled = false;
                    return;
                }

                // Désactiver le bouton pendant la soumission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Vérification...</span>';
            });

            // Gestion du renvoi de code
            resendForm.addEventListener('submit', function(e) {
                const btn = resendBtn;
                if (btn.disabled) {
                    e.preventDefault();
                    return false;
                }

                // Désactiver le bouton pendant l'envoi
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Envoi...</span>';

                // Le formulaire se soumettra normalement et la page se rechargera
                // Le compte à rebours sera mis à jour automatiquement après le rechargement
                return true;
            });

            // Fonction pour afficher les erreurs
            function showError(message) {
                otpError.textContent = message;
                otpError.style.display = 'block';
                otpInputs.forEach(input => {
                    input.classList.add('error');
                });
                
                setTimeout(() => {
                    otpInputs.forEach(input => {
                        input.classList.remove('error');
                    });
                }, 3000);
            }

            // Réinitialiser les champs en cas d'erreur
            @if($errors->has('otp'))
                otpInputs.forEach(input => {
                    input.value = '';
                    input.classList.remove('filled');
                });
                otpInputs[0].focus();
            @endif
            @else
                // Pas d'OTP valide, rediriger vers l'inscription
                alert('Aucun code OTP valide trouvé. Veuillez vous inscrire à nouveau.');
                window.location.href = '{{ route("register") }}';
            @endif
        });
    </script>
</body>
</html>
