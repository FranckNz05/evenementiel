@extends('layouts.dashboard')

@section('title', 'Modifier mon profil organisateur')

@push('styles')
<style>
:root {
    --primary: #0f1a3d;
    --primary-light: #1a237e;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
}

.organizer-profile-page {
    min-height: 100vh;
    background: var(--gray-50);
    padding: 2rem;
}

/* Header - Section bleue */
.page-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 0.75rem;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.15);
}

.page-title-section h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-title-section p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.page-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-light);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.2);
}

.btn-secondary {
    background: white;
    color: var(--primary);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(255, 255, 255, 0.5);
    color: var(--primary-light);
}

.btn-outline-primary {
    background: transparent;
    border: 2px solid var(--primary);
    color: var(--primary);
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8125rem;
}

/* Card */
.card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-title i {
    color: var(--primary);
}

.card-body {
    padding: 1.5rem;
}

/* Alert - Progression */
.alert-progress {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    border-left: 4px solid var(--info);
}

.alert-progress-icon {
    width: 48px;
    height: 48px;
    background: var(--info-bg);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--info);
    font-size: 1.25rem;
    flex-shrink: 0;
}

.alert-progress-content {
    flex: 1;
}

.alert-progress-title {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.alert-progress-text {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin: 0;
}

.alert-progress-text strong {
    color: var(--info);
}

.progress {
    width: 200px;
    height: 8px;
    background: var(--gray-200);
    border-radius: 9999px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: var(--success);
    border-radius: 9999px;
    transition: width 0.3s ease;
}

/* Form */
.form-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-label i {
    margin-right: 0.375rem;
    color: var(--primary);
}

.form-control,
.form-select {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 1px solid var(--gray-300);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    background: white;
}

.form-control:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.form-control.is-invalid,
.form-select.is-invalid {
    border-color: var(--danger);
}

.invalid-feedback {
    display: block;
    color: var(--danger);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

/* Input Group */
.input-group {
    display: flex;
    width: 100%;
}

.input-group-text {
    display: flex;
    align-items: center;
    padding: 0.625rem 0.875rem;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-radius: 0.5rem 0 0 0.5rem;
    color: var(--gray-600);
    font-size: 0.875rem;
    border-right: none;
}

.input-group .form-control {
    border-radius: 0 0.5rem 0.5rem 0;
}

/* Image Preview */
.image-preview {
    margin-top: 0.75rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: 0.5rem;
    border: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.preview-logo {
    max-height: 60px;
    width: auto;
    object-fit: contain;
}

.preview-banner {
    max-height: 80px;
    width: 100%;
    object-fit: cover;
    border-radius: 0.5rem;
}

/* Row and Col */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -0.75rem;
}

.col,
.col-12,
.col-md-4,
.col-md-6,
.col-md-9 {
    padding: 0 0.75rem;
    box-sizing: border-box;
}

.col-12 { width: 100%; }
.col-md-4 { width: 33.333%; }
.col-md-6 { width: 50%; }
.col-md-9 { width: 75%; }

@media (max-width: 768px) {
    .col-md-4,
    .col-md-6,
    .col-md-9 {
        width: 100%;
    }
}

/* Utilities */
.mb-3 { margin-bottom: 1rem; }
.mb-4 { margin-bottom: 1.5rem; }
.mb-0 { margin-bottom: 0; }
.mt-2 { margin-top: 0.5rem; }
.mt-4 { margin-top: 1.5rem; }
.me-1 { margin-right: 0.25rem; }
.me-2 { margin-right: 0.5rem; }
.me-3 { margin-right: 1rem; }
.ms-3 { margin-left: 1rem; }

.d-flex { display: flex; }
.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }
.flex-wrap { flex-wrap: wrap; }
.flex-shrink-0 { flex-shrink: 0; }
.flex-grow-1 { flex-grow: 1; }

.w-25 { width: 25%; }
.w-100 { width: 100%; }

.bg-white { background: white; }
.bg-light { background: var(--gray-50); }
.border-0 { border: none; }
.shadow-sm { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }

.text-primary { color: var(--primary) !important; }
.text-success { color: var(--success) !important; }
.text-info { color: var(--info) !important; }
.text-muted { color: var(--gray-500) !important; }

.fw-semibold { font-weight: 600; }
.fw-bold { font-weight: 700; }
.small { font-size: 0.75rem; }

/* Responsive */
@media (max-width: 768px) {
    .organizer-profile-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .alert-progress {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .progress {
        width: 100%;
        margin-left: 0 !important;
        margin-top: 0.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="organizer-profile-page">
    @php
        $user = auth()->user();
        $organizer = $user->organizer;

        // Calcul du pourcentage de complétion
        $requiredFields = [
            'company_name', 'email', 'phone_primary', 'address',
            'city', 'country', 'description', 'logo'
        ];

        $completed = 0;
        foreach ($requiredFields as $field) {
            if (!empty($organizer->$field)) $completed++;
        }

        $completionPercentage = round(($completed / count($requiredFields)) * 100);
        
        // Vérifier si $organizer existe avant d'appeler only()
        $organizerData = $organizer ? $organizer->toArray() : [];
        $missingFields = array_diff($requiredFields, array_keys(array_filter(
            array_intersect_key($organizerData, array_flip($requiredFields))
        )));
    @endphp

    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-user-tie"></i>
                Profil organisateur
            </h1>
            <p>Gérez vos informations professionnelles et votre image de marque</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('organizer.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    @if($completionPercentage < 100)
    <!-- Alert - Progression du profil -->
    <div class="alert-progress">
        <div class="alert-progress-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="alert-progress-content">
            <div class="alert-progress-title">
                Complétez votre profil ({{ $completionPercentage }}%)
            </div>
            <p class="alert-progress-text">
                Pour une meilleure visibilité, complétez :
                <strong>
                    @foreach($missingFields as $field)
                        @lang('organizer.fields.'.$field)@if(!$loop->last), @endif
                    @endforeach
                </strong>
            </p>
        </div>
        <div class="progress ms-3">
            <div class="progress-bar" role="progressbar" style="width: {{ $completionPercentage }}%"></div>
        </div>
    </div>
    @endif

    <!-- Formulaire organisateur -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-building"></i>
                Informations professionnelles
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('organizer.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Nom société et slogan -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="company_name" class="form-label">
                                <i class="fas fa-building"></i>
                                Nom de la société <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('company_name') is-invalid @enderror"
                                   id="company_name" 
                                   name="company_name"
                                   value="{{ old('company_name', $organizer->company_name) }}" 
                                   required>
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slogan" class="form-label">
                                <i class="fas fa-quote-right"></i>
                                Slogan
                            </label>
                            <input type="text" 
                                   class="form-control @error('slogan') is-invalid @enderror"
                                   id="slogan" 
                                   name="slogan"
                                   value="{{ old('slogan', $organizer->slogan) }}"
                                   placeholder="Votre phrase d'accroche">
                            @error('slogan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group mb-4">
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left"></i>
                        Description <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" 
                              name="description" 
                              rows="4" 
                              placeholder="Présentez votre entreprise, vos activités et vos valeurs..."
                              required>{{ old('description', $organizer->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="fas fa-info-circle"></i>
                        Une description complète améliore votre crédibilité auprès des clients.
                    </div>
                </div>

                <!-- Email et Site web -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i>
                                Email professionnel <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email" 
                                   name="email"
                                   value="{{ old('email', $organizer->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="website" class="form-label">
                                <i class="fas fa-globe"></i>
                                Site web
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-link"></i>
                                </span>
                                <input type="url" 
                                       class="form-control @error('website') is-invalid @enderror"
                                       id="website" 
                                       name="website"
                                       value="{{ old('website', $organizer->website) }}"
                                       placeholder="https://www.example.com">
                            </div>
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Téléphones -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="phone_primary" class="form-label">
                                <i class="fas fa-phone"></i>
                                Téléphone principal <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-mobile-alt"></i>
                                </span>
                                <input type="tel" 
                                       class="form-control @error('phone_primary') is-invalid @enderror"
                                       id="phone_primary" 
                                       name="phone_primary"
                                       value="{{ old('phone_primary', $organizer->phone_primary) }}" 
                                       required>
                            </div>
                            @error('phone_primary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone_secondary" class="form-label">
                                <i class="fas fa-phone-alt"></i>
                                Téléphone secondaire
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-mobile-alt"></i>
                                </span>
                                <input type="tel" 
                                       class="form-control @error('phone_secondary') is-invalid @enderror"
                                       id="phone_secondary" 
                                       name="phone_secondary"
                                       value="{{ old('phone_secondary', $organizer->phone_secondary) }}"
                                       placeholder="Optionnel">
                            </div>
                            @error('phone_secondary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Adresse <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('address') is-invalid @enderror"
                                   id="address" 
                                   name="address"
                                   value="{{ old('address', $organizer->address) }}" 
                                   required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="city" class="form-label">
                                <i class="fas fa-city"></i>
                                Ville <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('city') is-invalid @enderror"
                                   id="city" 
                                   name="city"
                                   value="{{ old('city', $organizer->city) }}" 
                                   required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="country" class="form-label">
                                <i class="fas fa-flag"></i>
                                Pays <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('country') is-invalid @enderror"
                                   id="country" 
                                   name="country"
                                   value="{{ old('country', $organizer->country) }}" 
                                   required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Images (Logo et Bannière) -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="logo" class="form-label">
                                <i class="fas fa-image"></i>
                                Logo
                            </label>
                            <input type="file" 
                                   class="form-control @error('logo') is-invalid @enderror"
                                   id="logo" 
                                   name="logo" 
                                   accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Formats: JPG, PNG, SVG (max 2MB)
                            </div>
                            @if($organizer->logo)
                                <div class="image-preview">
                                    <div>
                                        <div class="small text-muted mb-1">Logo actuel</div>
                                        <img src="{{ asset('storage/'.$organizer->logo) }}" 
                                             alt="Logo" 
                                             class="preview-logo"
                                             id="logo-preview">
                                    </div>
                                </div>
                            @else
                                <div class="image-preview" id="logo-preview-container" style="display: none;">
                                    <div>
                                        <div class="small text-muted mb-1">Aperçu</div>
                                        <img src="" alt="Aperçu logo" class="preview-logo" id="logo-preview-new">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="banner_image" class="form-label">
                                <i class="fas fa-images"></i>
                                Bannière
                            </label>
                            <input type="file" 
                                   class="form-control @error('banner_image') is-invalid @enderror"
                                   id="banner_image" 
                                   name="banner_image" 
                                   accept="image/*">
                            @error('banner_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Formats: JPG, PNG (max 5MB, dimensions recommandées: 1200x300px)
                            </div>
                            @if($organizer->banner_image)
                                <div class="image-preview">
                                    <div class="w-100">
                                        <div class="small text-muted mb-1">Bannière actuelle</div>
                                        <img src="{{ asset('storage/'.$organizer->banner_image) }}" 
                                             alt="Bannière" 
                                             class="preview-banner"
                                             id="banner-preview">
                                    </div>
                                </div>
                            @else
                                <div class="image-preview" id="banner-preview-container" style="display: none;">
                                    <div class="w-100">
                                        <div class="small text-muted mb-1">Aperçu</div>
                                        <img src="" alt="Aperçu bannière" class="preview-banner" id="banner-preview-new">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Réseaux sociaux -->
                <div class="card mb-4 border-0 bg-light">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0" style="font-size: 1rem;">
                            <i class="fas fa-share-alt"></i>
                            Réseaux sociaux
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="facebook" class="form-label">
                                    <i class="fab fa-facebook-f" style="color: #1877f2;"></i>
                                    Facebook
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fab fa-facebook-f" style="color: #1877f2;"></i>
                                    </span>
                                    <input type="url" 
                                           class="form-control" 
                                           id="facebook" 
                                           name="facebook"
                                           value="{{ old('facebook', $socialMedia['facebook'] ?? '') }}" 
                                           placeholder="https://facebook.com/votrepage">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="twitter" class="form-label">
                                    <i class="fab fa-x-twitter" style="color: #000000;"></i>
                                    X (Twitter)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fab fa-x-twitter" style="color: #000000;"></i>
                                    </span>
                                    <input type="url" 
                                           class="form-control" 
                                           id="twitter" 
                                           name="twitter"
                                           value="{{ old('twitter', $socialMedia['twitter'] ?? '') }}" 
                                           placeholder="https://x.com/votrecompte">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="instagram" class="form-label">
                                    <i class="fab fa-instagram" style="color: #e4405f;"></i>
                                    Instagram
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fab fa-instagram" style="color: #e4405f;"></i>
                                    </span>
                                    <input type="url" 
                                           class="form-control" 
                                           id="instagram" 
                                           name="instagram"
                                           value="{{ old('instagram', $socialMedia['instagram'] ?? '') }}" 
                                           placeholder="https://instagram.com/votrecompte">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="linkedin" class="form-label">
                                    <i class="fab fa-linkedin-in" style="color: #0a66c2;"></i>
                                    LinkedIn
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fab fa-linkedin-in" style="color: #0a66c2;"></i>
                                    </span>
                                    <input type="url" 
                                           class="form-control" 
                                           id="linkedin" 
                                           name="linkedin"
                                           value="{{ old('linkedin', $socialMedia['linkedin'] ?? '') }}" 
                                           placeholder="https://linkedin.com/company/votresociete">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-2"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===========================================
    // PRÉVISUALISATION DES IMAGES
    // ===========================================
    
    // Fonction de prévisualisation
    function previewImage(input, previewId, containerId = null) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    
                    // Afficher le conteneur si nécessaire
                    if (containerId) {
                        document.getElementById(containerId).style.display = 'flex';
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    }

    // Prévisualisation du logo
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function() {
            if (document.getElementById('logo-preview')) {
                previewImage(this, 'logo-preview');
            } else {
                previewImage(this, 'logo-preview-new', 'logo-preview-container');
            }
        });
    }

    // Prévisualisation de la bannière
    const bannerInput = document.getElementById('banner_image');
    if (bannerInput) {
        bannerInput.addEventListener('change', function() {
            if (document.getElementById('banner-preview')) {
                previewImage(this, 'banner-preview');
            } else {
                previewImage(this, 'banner-preview-new', 'banner-preview-container');
            }
        });
    }

    // ===========================================
    // VALIDATION DES CHAMPS REQUIS
    // ===========================================
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const companyName = document.getElementById('company_name');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone_primary');
            const address = document.getElementById('address');
            const city = document.getElementById('city');
            const country = document.getElementById('country');
            const description = document.getElementById('description');

            let isValid = true;

            // Vérification simple des champs requis
            [companyName, email, phone, address, city, country, description].forEach(field => {
                if (field && !field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else if (field) {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires marqués d\'un astérisque (*)');
            }
        });
    }

    // ===========================================
    // AUTO-FERMETURE DES ALERTES
    // ===========================================
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (!alert.classList.contains('alert-progress')) {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 300);
            }
        });
    }, 5000);
});
</script>
@endpush