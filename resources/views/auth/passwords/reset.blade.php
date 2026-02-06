@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (!$token && !old('token'))
                        <div class="alert alert-danger" role="alert">
                            <strong>Erreur :</strong> Le lien de réinitialisation est invalide. Veuillez demander un nouveau lien de réinitialisation.
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token ?? old('token') }}" id="resetToken">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" minlength="8">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">Le mot de passe doit contenir au moins 8 caractères.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password" minlength="8">
                                
                                @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span class="btn-text">{{ __('Reset Password') }}</span>
                                </button>
                            </div>
                        </div>
                        
                        @if(!$token && !old('token'))
                            <div class="row mt-3">
                                <div class="col-md-6 offset-md-4">
                                    <a href="{{ route('password.request') }}" class="btn btn-link">
                                        Demander un nouveau lien de réinitialisation
                                    </a>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetPasswordForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        // Validation côté client avant soumission
        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password-confirm').value;
            const tokenInput = form.querySelector('input[name="token"]');
            const token = tokenInput ? tokenInput.value : '';
            
            // Vérifications
            if (!email) {
                e.preventDefault();
                alert('Veuillez saisir votre adresse email.');
                document.getElementById('email').focus();
                return false;
            }
            
            if (!token) {
                e.preventDefault();
                alert('Erreur : Le lien de réinitialisation est invalide. Veuillez demander un nouveau lien de réinitialisation.');
                return false;
            }
            
            if (!password) {
                e.preventDefault();
                alert('Veuillez saisir un mot de passe.');
                document.getElementById('password').focus();
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 8 caractères.');
                document.getElementById('password').focus();
                return false;
            }
            
            if (!passwordConfirm) {
                e.preventDefault();
                alert('Veuillez confirmer votre mot de passe.');
                document.getElementById('password-confirm').focus();
                return false;
            }
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas. Veuillez vérifier votre saisie.');
                document.getElementById('password-confirm').focus();
                return false;
            }
            
            // Si toutes les validations passent, afficher le spinner et désactiver le bouton
            const spinner = submitBtn.querySelector('.spinner-border');
            const btnText = submitBtn.querySelector('.btn-text');
            const originalText = btnText ? btnText.textContent : submitBtn.textContent;
            
            if (spinner) {
                spinner.classList.remove('d-none');
            }
            if (btnText) {
                btnText.textContent = 'Traitement en cours...';
            }
            submitBtn.disabled = true;
            
            // Log pour déboguer
            console.log('Soumission du formulaire de réinitialisation', {
                email: email,
                hasToken: !!token,
                passwordLength: password.length
            });
            
            // Réactiver le bouton après 15 secondes au cas où la requête échouerait silencieusement
            setTimeout(function() {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    if (btnText) {
                        btnText.textContent = originalText;
                    }
                    if (spinner) {
                        spinner.classList.add('d-none');
                    }
                    console.warn('Le formulaire n\'a pas été soumis après 15 secondes');
                }
            }, 15000);
        });
        
        // Log pour déboguer
        console.log('Formulaire de réinitialisation de mot de passe initialisé');
        console.log('Token présent:', !!form.querySelector('input[name="token"]')?.value);
        console.log('Email présent:', !!document.getElementById('email')?.value);
    }
});
</script>
@endpush
@endsection
