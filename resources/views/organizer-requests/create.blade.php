@extends('layouts.dashboard')

@section('content')
<div class="container py-5">
    <!-- En-tête -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold mb-3">
            <i class="fas fa-user-tie text-primary me-3"></i>Devenir Organisateur
        </h1>
        <p class="lead text-muted">Rejoignez notre plateforme et commencez à organiser des événements exceptionnels</p>
    </div>

    <!-- Alertes -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Erreurs détectées :</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulaire -->
    <form method="POST" action="{{ route('organizer.request.store') }}" id="organizerRequestForm" class="organizer-form">
        @csrf

        <!-- Section 1: Informations de base -->
        <div class="form-section mb-5">
            <h4 class="section-title mb-4">
                <i class="fas fa-building text-primary me-2"></i>Informations de l'entreprise
            </h4>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="company_name" class="form-label fw-semibold">
                        <i class="fas fa-briefcase me-2 text-muted"></i>Nom de l'entreprise <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control form-control-lg @error('company_name') is-invalid @enderror" 
                           id="company_name" 
                           name="company_name" 
                           value="{{ old('company_name') }}" 
                           required
                           minlength="3"
                           maxlength="255"
                           placeholder="Ex: Events Company">
                    @error('company_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">3-255 caractères</small>
                </div>

                <div class="col-md-6">
                    <label for="phone_primary" class="form-label fw-semibold">
                        <i class="fas fa-phone me-2 text-muted"></i>Téléphone <span class="text-danger">*</span>
                    </label>
                    <input type="tel" 
                           class="form-control form-control-lg @error('phone_primary') is-invalid @enderror" 
                           id="phone_primary" 
                           name="phone_primary" 
                           value="{{ old('phone_primary') }}" 
                           required
                           pattern="[0-9+\-\s()]+"
                           placeholder="+242 06 123 45 67"
                           title="Veuillez entrer un numéro de téléphone valide">
                    @error('phone_primary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Format international recommandé</small>
                </div>
            </div>

            <div class="row g-4 mt-3">
                <div class="col-md-12">
                    <label for="email" class="form-label fw-semibold">
                        <i class="fas fa-envelope me-2 text-muted"></i>Email professionnel <span class="text-danger">*</span>
                    </label>
                    <input type="email" 
                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', Auth::user()->email) }}" 
                           required
                           placeholder="contact@entreprise.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Utilisez une adresse email professionnelle</small>
                </div>
            </div>
        </div>

        <!-- Section 2: Localisation -->
        <div class="form-section mb-5">
            <h4 class="section-title mb-4">
                <i class="fas fa-map-marker-alt text-primary me-2"></i>Localisation
            </h4>
            
            <div class="row g-4">
                <div class="col-md-12">
                    <label for="address" class="form-label fw-semibold">
                        <i class="fas fa-location-dot me-2 text-muted"></i>Adresse complète <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                             id="address" 
                             name="address" 
                             rows="3" 
                             required
                             minlength="10"
                             maxlength="500"
                             placeholder="Numéro, rue, code postal, ville">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">10-500 caractères</small>
                </div>
            </div>
        </div>

        <!-- Section 3: Motivation et expérience -->
        <div class="form-section mb-5">
            <h4 class="section-title mb-4">
                <i class="fas fa-star text-primary me-2"></i>Votre profil
            </h4>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="motivation" class="form-label fw-semibold">
                        <i class="fas fa-lightbulb me-2 text-muted"></i>Motivation <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('motivation') is-invalid @enderror" 
                             id="motivation" 
                             name="motivation" 
                             rows="8" 
                             required
                             minlength="100"
                             maxlength="2000"
                             placeholder="Pourquoi souhaitez-vous devenir organisateur sur notre plateforme ?">{{ old('motivation') }}</textarea>
                    @error('motivation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">100-2000 caractères</small>
                    <div class="progress mt-2" style="height: 6px; border-radius: 10px;">
                        <div id="motivationProgress" class="progress-bar bg-warning" role="progressbar" style="width: 0%;"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="experience" class="form-label fw-semibold">
                        <i class="fas fa-award me-2 text-muted"></i>Expérience <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('experience') is-invalid @enderror" 
                             id="experience" 
                             name="experience" 
                             rows="8" 
                             required
                             minlength="100"
                             maxlength="2000"
                             placeholder="Parlez-nous de votre expérience dans l'organisation d'événements...">{{ old('experience') }}</textarea>
                    @error('experience')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">100-2000 caractères</small>
                    <div class="progress mt-2" style="height: 6px; border-radius: 10px;">
                        <div id="experienceProgress" class="progress-bar bg-warning" role="progressbar" style="width: 0%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-5">
            <a href="{{ url()->previous() }}" class="btn btn-lg btn-outline-secondary px-5">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
            <button type="submit" class="btn btn-lg btn-primary px-5 shadow-lg">
                <i class="fas fa-paper-plane me-2"></i>Soumettre la demande
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des barres de progression pour les textareas
    const motivationTextarea = document.getElementById('motivation');
    const experienceTextarea = document.getElementById('experience');
    
    if (motivationTextarea) {
        motivationTextarea.addEventListener('input', function() {
            updateProgressBar(this, 'motivationProgress');
        });
        // Initialiser la barre de progression
        updateProgressBar(motivationTextarea, 'motivationProgress');
    }
    
    if (experienceTextarea) {
        experienceTextarea.addEventListener('input', function() {
            updateProgressBar(this, 'experienceProgress');
        });
        // Initialiser la barre de progression
        updateProgressBar(experienceTextarea, 'experienceProgress');
    }
    
    function updateProgressBar(textarea, progressBarId) {
        const minLength = parseInt(textarea.getAttribute('minlength')) || 0;
        const maxLength = parseInt(textarea.getAttribute('maxlength')) || 2000;
        const currentLength = textarea.value.length;
        const progressBar = document.getElementById(progressBarId);
        
        if (!progressBar) return;
        
        let percentage = (currentLength / maxLength) * 100;
        percentage = Math.min(100, percentage); // Ne pas dépasser 100%
        
        // Mettre à jour la largeur de la barre de progression
        progressBar.style.width = percentage + '%';
        
        // Changer la couleur en fonction du pourcentage et des limites
        if (currentLength < minLength) {
            progressBar.className = 'progress-bar bg-warning';
            progressBar.setAttribute('aria-valuenow', percentage);
        } else if (currentLength >= minLength && currentLength <= maxLength) {
            progressBar.className = 'progress-bar bg-success';
            progressBar.setAttribute('aria-valuenow', percentage);
        } else {
            progressBar.className = 'progress-bar bg-danger';
            progressBar.setAttribute('aria-valuenow', 100);
        }
    }
    
    // Validation personnalisée du formulaire
    const form = document.getElementById('organizerRequestForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Valider la longueur minimale pour la motivation
            if (motivationTextarea && motivationTextarea.value.length < 100) {
                isValid = false;
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Le champ motivation doit contenir au moins 100 caractères.';
                
                // Supprimer les messages d'erreur existants
                const existingError = motivationTextarea.nextElementSibling;
                if (existingError && existingError.classList.contains('invalid-feedback')) {
                    existingError.remove();
                }
                
                motivationTextarea.classList.add('is-invalid');
                motivationTextarea.insertAdjacentElement('afterend', errorDiv);
            }
            
            // Valider la longueur minimale pour l'expérience
            if (experienceTextarea && experienceTextarea.value.length < 100) {
                isValid = false;
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Le champ expérience doit contenir au moins 100 caractères.';
                
                // Supprimer les messages d'erreur existants
                const existingError = experienceTextarea.nextElementSibling;
                if (existingError && existingError.classList.contains('invalid-feedback')) {
                    existingError.remove();
                }
                
                experienceTextarea.classList.add('is-invalid');
                experienceTextarea.insertAdjacentElement('afterend', errorDiv);
            }
            
            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
                
                // Faire défiler jusqu'au premier champ invalide
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
/* Design moderne pour le formulaire organisateur */
.organizer-form {
    max-width: 1200px;
    margin: 0 auto;
}

.form-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.form-section:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.section-title {
    font-weight: 700;
    color: #2c3e50;
    padding-bottom: 1rem;
    border-bottom: 3px solid #e9ecef;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #0d6efd 0%, #0dcaf0 100%);
    border-radius: 10px;
}

.form-control,
.form-control-lg {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    padding: 0.875rem 1.25rem;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.form-control:focus,
.form-control-lg:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.3rem rgba(13, 110, 253, 0.15);
    transform: translateY(-2px);
}

.form-label {
    font-size: 0.95rem;
    color: #495057;
    margin-bottom: 0.75rem;
}

.form-label i {
    font-size: 0.9rem;
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.btn-lg {
    padding: 0.875rem 2.5rem;
    font-weight: 600;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0a58ca 0%, #0bb5d8 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    transform: translateY(-2px);
}

.alert {
    border-radius: 12px;
    border: none;
}

.progress {
    background-color: #e9ecef;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.4s ease, background-color 0.3s ease;
}

/* Animation d'entrée */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-section {
    animation: fadeInUp 0.6s ease-out;
}

.form-section:nth-child(2) {
    animation-delay: 0.1s;
}

.form-section:nth-child(3) {
    animation-delay: 0.2s;
}

/* Responsive */
@media (max-width: 768px) {
    .form-section {
        padding: 1.5rem;
    }
    
    .section-title {
        font-size: 1.25rem;
    }
    
    .btn-lg {
        width: 100%;
        padding: 1rem;
    }
}
</style>
@endpush

@endsection
