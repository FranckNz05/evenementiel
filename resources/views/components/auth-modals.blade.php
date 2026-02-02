@guest
@php
    $sessionAuthModal = session('auth_modal');
    $oldAuthModal = old('modal');
    $shouldShowLoginModal = ($sessionAuthModal === 'login') || ($oldAuthModal === 'login');
    $shouldShowRegisterModal = ($sessionAuthModal === 'register') || ($oldAuthModal === 'register');
@endphp

<!-- Login Modal -->
<div class="modal fade" id="authLoginModal" tabindex="-1" aria-labelledby="authLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="authLoginModalLabel">Connexion</h5>
                    <p class="text-muted small mb-0">Retrouvez vos événements et billets en quelques secondes.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body pt-3">
                <form method="POST" action="{{ route('login') }}" id="modalLoginForm">
                    @csrf
                    <input type="hidden" name="modal" value="login">
                    <input type="hidden" name="redirect_to" id="authLoginRedirectInput" value="">

                    <div class="mb-3">
                        <label for="modal_login_email" class="form-label fw-semibold text-muted">Email</label>
                        <input type="email" name="email" id="modal_login_email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="vous@exemple.com" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="modal_login_password" class="form-label fw-semibold text-muted">Mot de passe</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="modal_login_password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="••••••••" required>
                            <span class="position-absolute top-50 end-0 translate-middle-y pe-3 text-muted cursor-pointer"
                                  onclick="window.togglePasswordVisibility('modal_login_password', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="modal_login_remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted" for="modal_login_remember">Se souvenir de moi</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="small text-decoration-none">Mot de passe oublié ?</a>
                        @endif
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-dark rounded-pill py-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </button>
                    </div>

                    <p class="text-center small text-muted mb-0">
                        Pas encore de compte ? <a href="#" data-auth-modal-target="authRegisterModal" class="text-decoration-none fw-semibold">Créer un compte</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="authRegisterModal" tabindex="-1" aria-labelledby="authRegisterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="authRegisterModalLabel">Créer un compte</h5>
                    <p class="text-muted small mb-0">Organisez ou vivez vos événements avec MokiliEvent.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body pt-3">
                <form method="POST" action="{{ route('register') }}" id="modalRegisterForm">
                    @csrf
                    <input type="hidden" name="modal" value="register">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_register_prenom" class="form-label fw-semibold text-muted">Prénom</label>
                            <input type="text" name="prenom" id="modal_register_prenom"
                                   class="form-control @error('prenom') is-invalid @enderror"
                                   value="{{ old('prenom') }}" required>
                            @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_register_nom" class="form-label fw-semibold text-muted">Nom</label>
                            <input type="text" name="nom" id="modal_register_nom"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom') }}" required>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_register_email" class="form-label fw-semibold text-muted">Email</label>
                            <input type="email" name="email" id="modal_register_email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_register_phone" class="form-label fw-semibold text-muted">Téléphone</label>
                            <input type="tel" name="phone" id="modal_register_phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_register_password" class="form-label fw-semibold text-muted">Mot de passe</label>
                            <div class="position-relative">
                                <input type="password" name="password" id="modal_register_password"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                <span class="position-absolute top-50 end-0 translate-middle-y pe-3 text-muted cursor-pointer"
                                      onclick="window.togglePasswordVisibility('modal_register_password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_register_password_confirmation" class="form-label fw-semibold text-muted">Confirmer</label>
                            <input type="password" name="password_confirmation" id="modal_register_password_confirmation"
                                   class="form-control" required>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="modal_register_terms" required>
                        <label class="form-check-label small text-muted" for="modal_register_terms">
                            J'accepte les <a href="{{ route('terms') }}" class="text-decoration-none">conditions d'utilisation</a> et la <a href="{{ route('privacy') }}" class="text-decoration-none">politique de confidentialité</a>.
                        </label>
                    </div>

                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-dark rounded-pill py-2">
                            <i class="fas fa-user-plus me-2"></i>Créer mon compte
                        </button>
                    </div>

                    <p class="text-center small text-muted mb-0 mt-3">
                        Déjà inscrit ? <a href="#" data-auth-modal-target="authLoginModal" class="text-decoration-none fw-semibold">Se connecter</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function setupAuthModalTriggers() {
    const triggers = document.querySelectorAll('[data-auth-modal-target]');
    triggers.forEach(trigger => {
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            const targetId = this.dataset.authModalTarget;
            const redirectUrl = this.dataset.authRedirect || '';
            const modalEl = document.getElementById(targetId);
            if (!modalEl) return;
            if (targetId === 'authLoginModal') {
                const redirectInput = document.getElementById('authLoginRedirectInput');
                if (redirectInput) redirectInput.value = redirectUrl;
            }
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        });
    });
}

function togglePasswordVisibility(fieldId, iconWrapper) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    const icon = iconWrapper.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
window.togglePasswordVisibility = togglePasswordVisibility;

document.addEventListener('DOMContentLoaded', function () {
    setupAuthModalTriggers();
    @if($shouldShowLoginModal)
        const loginModal = document.getElementById('authLoginModal');
        if (loginModal) bootstrap.Modal.getOrCreateInstance(loginModal).show();
    @endif
    @if($shouldShowRegisterModal)
        const registerModal = document.getElementById('authRegisterModal');
        if (registerModal) bootstrap.Modal.getOrCreateInstance(registerModal).show();
    @endif
});
</script>
@endguest