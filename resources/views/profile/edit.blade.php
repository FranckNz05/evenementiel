@extends('layouts.dashboard')

@section('title', 'Modifier mon profil')

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

.profile-page {
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

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-outline-primary {
    background: white;
    color: var(--primary);
    border: 1px solid var(--primary);
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
}

/* Card */
.card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: all 0.2s;
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

/* Profile Photo */
.profile-photo-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.profile-photo-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
    flex-shrink: 0;
}

.profile-photo {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid white;
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.15);
}

.profile-photo-edit {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 36px;
    height: 36px;
    background: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    border: 3px solid white;
    transition: all 0.2s;
}

.profile-photo-edit:hover {
    background: var(--primary-light);
    transform: scale(1.1);
}

.profile-photo-info h6 {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.profile-photo-info p {
    color: var(--gray-500);
    font-size: 0.75rem;
    margin: 0;
}

/* Progress Circle */
.progress-circle-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
    text-align: center;
}

.progress-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin: 0 auto 1rem;
    background: conic-gradient(
        var(--primary) calc(var(--progress) * 1%),
        var(--gray-200) calc(var(--progress) * 1%)
    );
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.progress-circle::before {
    content: '';
    position: absolute;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: white;
}

.progress-value {
    position: relative;
    z-index: 1;
    font-weight: 700;
    font-size: 1.25rem;
    color: var(--primary);
}

.progress-title {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.progress-text {
    color: var(--gray-500);
    font-size: 0.75rem;
    margin: 0;
}

/* Quick Actions */
.quick-actions-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.25rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.quick-actions-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 1rem;
    padding-left: 0.5rem;
}

.quick-action-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: var(--gray-700);
    text-decoration: none;
    border-radius: 0.5rem;
    transition: all 0.2s;
    font-weight: 500;
    font-size: 0.875rem;
}

.quick-action-item i {
    width: 1.25rem;
    text-align: center;
    color: var(--primary);
}

.quick-action-item:hover {
    background: var(--gray-50);
    color: var(--primary);
    transform: translateX(4px);
}

.quick-action-btn {
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    border: none;
    background: var(--gray-100);
    color: var(--gray-700);
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s;
    cursor: pointer;
    margin-top: 0.5rem;
}

.quick-action-btn:hover {
    background: var(--warning);
    color: var(--gray-900);
}

/* Badges */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-influencer {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #f59e0b;
}

.badge-verified {
    background: #d1fae5;
    color: #065f46;
}

/* Status Alert */
.status-alert {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid;
    background: white;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.status-alert-warning {
    border-left-color: var(--warning);
}

.status-alert-danger {
    border-left-color: var(--danger);
}

.status-alert i {
    font-size: 1.125rem;
}

.status-alert-warning i {
    color: var(--warning);
}

.status-alert-danger i {
    color: var(--danger);
}

.status-alert-content {
    flex: 1;
    color: var(--gray-700);
    font-size: 0.875rem;
}

.status-alert-close {
    background: none;
    border: none;
    color: var(--gray-400);
    cursor: pointer;
    padding: 0.25rem;
    transition: color 0.2s;
}

.status-alert-close:hover {
    color: var(--gray-600);
}

/* Form */
.form-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-600);
    margin-bottom: 0.375rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
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
    color: var(--gray-900);
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
    color: var(--danger);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.form-text {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

/* Password Icon */
.password-icon {
    width: 48px;
    height: 48px;
    background: rgba(15, 26, 61, 0.1);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1.25rem;
}

/* Layout */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -0.75rem;
}

.col-lg-3,
.col-lg-9,
.col-md-6 {
    padding: 0 0.75rem;
    box-sizing: border-box;
}

.col-lg-3 {
    width: 25%;
}

.col-lg-9 {
    width: 75%;
}

.col-md-6 {
    width: 50%;
}

@media (max-width: 992px) {
    .col-lg-3,
    .col-lg-9 {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .profile-page {
        padding: 1rem;
    }
    
    .col-md-6 {
        width: 100%;
    }
    
    .profile-photo-section {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-photo-wrapper {
        margin: 0 auto;
    }
}

/* Gap utilities */
.gap-3 {
    gap: 1rem;
}

.g-3 {
    gap: 1rem;
}

.g-4 {
    gap: 1.5rem;
}

.mb-3 {
    margin-bottom: 1rem;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.me-2 {
    margin-right: 0.5rem;
}

.me-3 {
    margin-right: 1rem;
}

.me-4 {
    margin-right: 1.5rem;
}

.pb-4 {
    padding-bottom: 1.5rem;
}

.px-4 {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.py-4 {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
}

/* Border */
.border-bottom {
    border-bottom: 1px solid var(--gray-200);
}

/* Text */
.text-muted {
    color: var(--gray-500) !important;
}

.small {
    font-size: 0.75rem;
}

.fw-semibold {
    font-weight: 600;
}

.text-center {
    text-align: center;
}

.d-flex {
    display: flex;
}

.align-items-center {
    align-items: center;
}

.justify-content-between {
    justify-content: space-between;
}

.justify-content-end {
    justify-content: flex-end;
}

.flex-wrap {
    flex-wrap: wrap;
}

/* Shadow */
.shadow-sm {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
}

/* Influencer badge in header */
.influencer-header-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 9999px;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    backdrop-filter: blur(4px);
}

.influencer-header-badge i {
    color: #ffd700;
}

.d-none {
    display: none !important;
}
</style>
@endpush

@section('content')
<div class="profile-page">
    @auth
        @php
            $user = auth()->user();
            $completionPercentage = $user->getProfileCompletionPercentageAttribute();
        @endphp

        <!-- Header -->
        <div class="page-header">
            <div class="page-title-section">
                <h1>Mon profil</h1>
                <p>Gérez vos informations personnelles et les paramètres de votre compte</p>
            </div>
            <div class="page-actions">
                @if($user->isInfluencer())
                    <span class="influencer-header-badge">
                        <i class="fas fa-star"></i>
                        Influenceur vérifié
                    </span>
                @endif
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Tableau de bord
                </a>
            </div>
        </div>

        <!-- Alertes de statut organisateur -->
        @if($user->organizerRequest)
            @if($user->organizerRequest->status === 'en attente')
                <div class="status-alert status-alert-warning">
                    <i class="fas fa-hourglass-half"></i>
                    <div class="status-alert-content">
                        Votre demande d'organisateur est en cours de traitement
                    </div>
                    <button type="button" class="status-alert-close" data-bs-dismiss="alert">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @elseif($user->organizerRequest->status === 'rejeté')
                <div class="status-alert status-alert-danger">
                    <i class="fas fa-times-circle"></i>
                    <div class="status-alert-content">
                        <strong>Demande rejetée :</strong> {{ $user->organizerRequest->rejection_reason }}
                    </div>
                    <button type="button" class="status-alert-close" data-bs-dismiss="alert">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
        @endif

        @if($user->hasRole(2))
            @include('profile.organizer-edit')
        @else
            <div class="row g-4">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <!-- Progression du profil -->
                    @if($completionPercentage < 100)
                        <div class="progress-circle-card">
                            <div class="progress-circle" style="--progress: {{ $completionPercentage }}">
                                <span class="progress-value">{{ $completionPercentage }}%</span>
                            </div>
                            <h6 class="progress-title">Profil complété</h6>
                            <p class="progress-text">Complétez votre profil pour une meilleure expérience</p>
                        </div>
                    @endif

                    <!-- Actions rapides -->
                    <div class="quick-actions-card">
                        <h6 class="quick-actions-title">Actions rapides</h6>
                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                            @if(!$user->isOrganizer() && !$user->hasPendingOrganizerRequest() && !$user->isAdmin())
                                <a href="{{ route('organizer.request.create') }}" class="quick-action-item">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Devenir organisateur</span>
                                </a>
                                @if(!$user->isInfluencer())
                                    <form method="POST" action="{{ route('influencers.request') }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="quick-action-btn">
                                            <i class="fas fa-star"></i>
                                            Demande Influenceur
                                        </button>
                                    </form>
                                @endif
                            @elseif($user->hasPendingOrganizerRequest())
                                <a href="{{ route('organizer.request.status') }}" class="quick-action-item">
                                    <i class="fas fa-hourglass-half"></i>
                                    <span>Statut de ma demande</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contenu principal -->
                <div class="col-lg-9">
                    <!-- Notifications -->
                    @if(session('status'))
                        <div class="status-alert" style="border-left-color: var(--success);">
                            <i class="fas fa-check-circle" style="color: var(--success);"></i>
                            <div class="status-alert-content">{{ session('status') }}</div>
                            <button type="button" class="status-alert-close" data-bs-dismiss="alert">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="status-alert" style="border-left-color: var(--danger);">
                            <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i>
                            <div class="status-alert-content">{{ session('error') }}</div>
                            <button type="button" class="status-alert-close" data-bs-dismiss="alert">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    <!-- Formulaire de profil -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-user-circle"></i>
                                Informations personnelles
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <!-- Photo de profil -->
                                <div class="profile-photo-section">
                                    <div class="profile-photo-wrapper">
                                        <img src="{{ $user->getProfilePhotoUrlAttribute() }}"
                                             alt="Photo de profil" 
                                             class="profile-photo"
                                             id="profile-image-preview">
                                        <label for="profil_photo" class="profile-photo-edit">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                        <input type="file" name="profil_photo" id="profil_photo" class="d-none" accept="image/*">
                                    </div>
                                    <div class="profile-photo-info">
                                        <h6>Photo de profil</h6>
                                        <p>JPG, PNG. 5MB max.</p>
                                        @error('profil_photo')
                                            <span style="color: var(--danger); font-size: 0.75rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nom et prénom -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="prenom" class="form-label">Prénom</label>
                                        <input type="text" 
                                               class="form-control @error('prenom') is-invalid @enderror"
                                               id="prenom" 
                                               name="prenom" 
                                               value="{{ old('prenom', $user->prenom) }}" 
                                               required>
                                        @error('prenom')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nom" class="form-label">Nom</label>
                                        <input type="text" 
                                               class="form-control @error('nom') is-invalid @enderror"
                                               id="nom" 
                                               name="nom" 
                                               value="{{ old('nom', $user->nom) }}" 
                                               required>
                                        @error('nom')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email et téléphone -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Adresse e-mail</label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror"
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="tel" 
                                               class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone', $user->phone) }}" 
                                               required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Genre et tranche d'âge -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="genre" class="form-label">Genre</label>
                                        <select class="form-select @error('genre') is-invalid @enderror" id="genre" name="genre">
                                            <option value="" disabled {{ old('genre', $user->genre) ? '' : 'selected' }}>Sélectionnez</option>
                                            <option value="Homme" {{ old('genre', $user->genre) == 'Homme' ? 'selected' : '' }}>Homme</option>
                                            <option value="Femme" {{ old('genre', $user->genre) == 'Femme' ? 'selected' : '' }}>Femme</option>
                                            <option value="Autre" {{ old('genre', $user->genre) == 'Autre' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        @error('genre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tranche_age" class="form-label">Tranche d'âge</label>
                                        <select class="form-select @error('tranche_age') is-invalid @enderror" id="tranche_age" name="tranche_age">
                                            <option value="" disabled {{ old('tranche_age', $user->tranche_age) ? '' : 'selected' }}>Sélectionnez</option>
                                            <option value="0-17" {{ old('tranche_age', $user->tranche_age) == '0-17' ? 'selected' : '' }}>0-17 ans</option>
                                            <option value="18-25" {{ old('tranche_age', $user->tranche_age) == '18-25' ? 'selected' : '' }}>18-25 ans</option>
                                            <option value="26-35" {{ old('tranche_age', $user->tranche_age) == '26-35' ? 'selected' : '' }}>26-35 ans</option>
                                            <option value="36-45" {{ old('tranche_age', $user->tranche_age) == '36-45' ? 'selected' : '' }}>36-45 ans</option>
                                            <option value="46+" {{ old('tranche_age', $user->tranche_age) == '46+' ? 'selected' : '' }}>46+ ans</option>
                                        </select>
                                        @error('tranche_age')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Adresse -->
                                <div class="mb-3">
                                    <label for="address" class="form-label">Adresse</label>
                                    <input type="text" 
                                           class="form-control @error('address') is-invalid @enderror"
                                           id="address" 
                                           name="address" 
                                           value="{{ old('address', $user->address) }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ville et pays -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="city" class="form-label">Ville</label>
                                        <input type="text" 
                                               class="form-control @error('city') is-invalid @enderror"
                                               id="city" 
                                               name="city" 
                                               value="{{ old('city', $user->city) }}">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="country" class="form-label">Pays</label>
                                        <input type="text" 
                                               class="form-control @error('country') is-invalid @enderror"
                                               id="country" 
                                               name="country" 
                                               value="{{ old('country', $user->country) }}">
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Formulaire de changement de mot de passe -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-lock"></i>
                                Sécurité
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="password-icon me-3">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div>
                                    <h6 style="font-weight: 600; color: var(--gray-900); margin-bottom: 0.25rem;">Modifier votre mot de passe</h6>
                                    <p style="color: var(--gray-500); font-size: 0.75rem; margin: 0;">Choisissez un mot de passe sécurisé</p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mot de passe actuel</label>
                                    <input type="password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" 
                                           name="current_password" 
                                           required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required>
                                    <div class="form-text">Minimum 8 caractères</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-key me-2"></i>Mettre à jour
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prévisualisation de l'image de profil
    const input = document.getElementById('profil_photo');
    const preview = document.getElementById('profile-image-preview');

    if (input && preview) {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Fermeture des alertes
    const closeButtons = document.querySelectorAll('.status-alert-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.status-alert');
            if (alert) {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        });
    });
});
</script>
@endpush