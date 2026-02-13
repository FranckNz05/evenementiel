@extends('layouts.dashboard')

@section('title', 'Paramètres Système')

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

.settings-page {
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

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #059669;
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

/* Section Headers */
.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--gray-200);
}

.section-header i {
    color: var(--primary);
    font-size: 1.125rem;
}

.section-header h6 {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0;
}

.section-header.text-danger i {
    color: var(--danger);
}

.section-header.text-danger h6 {
    color: var(--danger);
}

/* Form */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-label .text-danger {
    color: var(--danger);
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

/* Form Check */
.form-check {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.form-check-input {
    width: 2.5rem;
    height: 1.25rem;
    background-color: var(--gray-300);
    border-radius: 2rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.form-check-input:checked {
    background-color: var(--primary);
}

.form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.form-check-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-700);
    cursor: pointer;
}

/* Alert */
.alert {
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    border-left: 4px solid;
}

.alert-success {
    background: #d1fae5;
    border-left-color: #059669;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    border-left-color: #dc2626;
    color: #991b1b;
}

.alert i {
    font-size: 1rem;
}

.alert .btn-close {
    margin-left: auto;
    background: transparent;
    border: none;
    color: currentColor;
    opacity: 0.7;
    cursor: pointer;
    padding: 0.25rem;
}

.alert .btn-close:hover {
    opacity: 1;
}

/* Maintenance Card */
.maintenance-card {
    background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
    border-left: 6px solid var(--danger);
    border-radius: 0.75rem;
    padding: 1.25rem;
    margin-top: 0.5rem;
}

.maintenance-card p {
    color: #991b1b;
    font-size: 0.8125rem;
    margin-bottom: 0;
    margin-top: 0.5rem;
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
    flex-wrap: wrap;
}

.image-preview-label {
    font-size: 0.75rem;
    color: var(--gray-600);
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.preview-logo {
    max-height: 60px;
    width: auto;
    object-fit: contain;
}

.preview-favicon {
    max-height: 32px;
    width: 32px;
    object-fit: contain;
}

/* Breadcrumb */
.breadcrumb {
    display: flex;
    flex-wrap: wrap;
    padding: 0;
    margin: 0.5rem 0 0 0;
    list-style: none;
    background: transparent;
}

.breadcrumb-item {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.8);
}

.breadcrumb-item a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-item a:hover {
    color: white;
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: white;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    padding: 0 0.5rem;
    color: rgba(255, 255, 255, 0.6);
}

/* Responsive */
@media (max-width: 768px) {
    .settings-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .image-preview {
        flex-direction: column;
        align-items: flex-start;
    }
}

/* Row and Col */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -0.75rem;
}

.col,
.col-12,
.col-md-6 {
    padding: 0 0.75rem;
    box-sizing: border-box;
}

.col-12 {
    width: 100%;
}

.col-md-6 {
    width: 50%;
}

@media (max-width: 768px) {
    .col-md-6 {
        width: 100%;
    }
}

/* Utilities */
.border-bottom {
    border-bottom: 1px solid var(--gray-200) !important;
}

.pb-2 {
    padding-bottom: 0.5rem;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.mb-5 {
    margin-bottom: 2rem;
}

.mt-1 {
    margin-top: 0.25rem;
}

.mt-2 {
    margin-top: 0.5rem;
}

.mt-4 {
    margin-top: 1.5rem;
}

.mt-5 {
    margin-top: 2rem;
}

.me-1 {
    margin-right: 0.25rem;
}

.me-2 {
    margin-right: 0.5rem;
}

.ms-auto {
    margin-left: auto;
}

.p-3 {
    padding: 1rem;
}

.p-4 {
    padding: 1.5rem;
}

.px-5 {
    padding-left: 2rem;
    padding-right: 2rem;
}

.text-end {
    text-align: right;
}

.text-danger {
    color: var(--danger) !important;
}

.text-muted {
    color: var(--gray-500) !important;
}

.small {
    font-size: 0.75rem;
}

.fw-bold {
    font-weight: 600;
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

.w-100 {
    width: 100%;
}

.bg-light {
    background-color: var(--gray-100) !important;
}

.rounded {
    border-radius: 0.5rem;
}

.border {
    border: 1px solid var(--gray-200);
}

.shadow-sm {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}
</style>
@endpush

@section('content')
<div class="settings-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-cog"></i>
                Paramètres Système
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Configuration</li>
                    <li class="breadcrumb-item active" aria-current="page">Paramètres Système</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Formulaire de configuration -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-sliders-h"></i>
                Configuration générale
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Section: Informations de base -->
                <div class="section-header">
                    <i class="fas fa-info-circle"></i>
                    <h6>Informations de l'application</h6>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="site_name" class="form-label">
                                Nom de l'application <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('site_name') is-invalid @enderror" 
                                   id="site_name" 
                                   name="site_name"
                                   value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}" 
                                   required>
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="contact_email" class="form-label">Email de contact</label>
                            <input type="email" 
                                   class="form-control @error('contact_email') is-invalid @enderror" 
                                   id="contact_email" 
                                   name="contact_email"
                                   value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
                            @error('contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <label for="site_description" class="form-label">Description de la plateforme</label>
                            <textarea class="form-control @error('site_description') is-invalid @enderror" 
                                      id="site_description" 
                                      name="site_description" 
                                      rows="3">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                            @error('site_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="phone_number" class="form-label">Numéro de téléphone</label>
                            <input type="text" 
                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" 
                                   name="phone_number"
                                   value="{{ old('phone_number', $settings['phone_number'] ?? '') }}">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="address" class="form-label">Adresse physique</label>
                            <input type="text" 
                                   class="form-control @error('address') is-invalid @enderror" 
                                   id="address" 
                                   name="address"
                                   value="{{ old('address', $settings['address'] ?? '') }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Réseaux sociaux -->
                <div class="section-header mt-5">
                    <i class="fas fa-share-alt"></i>
                    <h6>Réseaux sociaux</h6>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="facebook_url" class="form-label">Lien Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fab fa-facebook-f" style="color: #1877f2;"></i>
                                </span>
                                <input type="url" 
                                       class="form-control @error('facebook_url') is-invalid @enderror" 
                                       id="facebook_url" 
                                       name="facebook_url"
                                       value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}" 
                                       placeholder="https://facebook.com/...">
                            </div>
                            @error('facebook_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="twitter_url" class="form-label">Lien X (Twitter)</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fab fa-x-twitter" style="color: #000000;"></i>
                                </span>
                                <input type="url" 
                                       class="form-control @error('twitter_url') is-invalid @enderror" 
                                       id="twitter_url" 
                                       name="twitter_url"
                                       value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}" 
                                       placeholder="https://x.com/...">
                            </div>
                            @error('twitter_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="instagram_url" class="form-label">Lien Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fab fa-instagram" style="color: #e4405f;"></i>
                                </span>
                                <input type="url" 
                                       class="form-control @error('instagram_url') is-invalid @enderror" 
                                       id="instagram_url" 
                                       name="instagram_url"
                                       value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" 
                                       placeholder="https://instagram.com/...">
                            </div>
                            @error('instagram_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="tiktok_url" class="form-label">Lien TikTok</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fab fa-tiktok" style="color: #000000;"></i>
                                </span>
                                <input type="url" 
                                       class="form-control @error('tiktok_url') is-invalid @enderror" 
                                       id="tiktok_url" 
                                       name="tiktok_url"
                                       value="{{ old('tiktok_url', $settings['tiktok_url'] ?? '') }}" 
                                       placeholder="https://tiktok.com/@...">
                            </div>
                            @error('tiktok_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Identité visuelle -->
                <div class="section-header mt-5">
                    <i class="fas fa-palette"></i>
                    <h6>Identité visuelle</h6>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="logo" class="form-label">Logo principal</label>
                            <input type="file" 
                                   class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" 
                                   name="logo" 
                                   accept="image/*">
                            <small class="text-muted d-block mt-1">Formats acceptés: JPG, PNG, SVG (max 2MB)</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if(isset($settings['logo']) && $settings['logo'])
                            <div class="image-preview">
                                <div>
                                    <div class="image-preview-label">Logo actuel</div>
                                    <img src="{{ asset($settings['logo']) }}" alt="Logo" class="preview-logo">
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="favicon" class="form-label">Favicon (icône de navigateur)</label>
                            <input type="file" 
                                   class="form-control @error('favicon') is-invalid @enderror" 
                                   id="favicon" 
                                   name="favicon" 
                                   accept=".ico,image/x-icon,image/vnd.microsoft.icon,image/png,image/jpeg">
                            <small class="text-muted d-block mt-1">Formats acceptés: ICO, PNG (max 1MB)</small>
                            @error('favicon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if(isset($settings['favicon']) && $settings['favicon'])
                            <div class="image-preview">
                                <div>
                                    <div class="image-preview-label">Favicon actuel</div>
                                    <img src="{{ asset($settings['favicon']) }}" alt="Favicon" class="preview-favicon">
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Section: Maintenance & Sécurité -->
                <div class="section-header mt-5 text-danger">
                    <i class="fas fa-tools"></i>
                    <h6>Maintenance & Sécurité</h6>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="maintenance-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="maintenance_mode" 
                                           name="maintenance_mode"
                                           value="1" 
                                           {{ old('maintenance_mode', $settings['maintenance_mode'] ?? '') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="maintenance_mode">
                                        Mode maintenance actif
                                    </label>
                                </div>
                            </div>
                            <p class="small mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Lorsque ce mode est activé, seuls les administrateurs peuvent accéder au site. 
                                Les visiteurs verront une page de maintenance.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end mt-5 pt-3 border-top">
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
    // PRÉVISUALISATION DU LOGO
    // ===========================================
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Supprimer l'ancien aperçu s'il existe
                    const existingPreview = document.querySelector('#logo + .image-preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    
                    // Créer un nouvel aperçu
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'image-preview';
                    previewContainer.innerHTML = `
                        <div>
                            <div class="image-preview-label">Nouveau logo</div>
                            <img src="${e.target.result}" alt="Aperçu" class="preview-logo">
                        </div>
                    `;
                    
                    logoInput.parentNode.appendChild(previewContainer);
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // ===========================================
    // PRÉVISUALISATION DU FAVICON
    // ===========================================
    const faviconInput = document.getElementById('favicon');
    if (faviconInput) {
        faviconInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Supprimer l'ancien aperçu s'il existe
                    const existingPreview = document.querySelector('#favicon + .image-preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    
                    // Créer un nouvel aperçu
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'image-preview';
                    previewContainer.innerHTML = `
                        <div>
                            <div class="image-preview-label">Nouveau favicon</div>
                            <img src="${e.target.result}" alt="Aperçu" class="preview-favicon">
                        </div>
                    `;
                    
                    faviconInput.parentNode.appendChild(previewContainer);
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // ===========================================
    // AUTO-FERMETURE DES ALERTES
    // ===========================================
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (!alert.classList.contains('alert-permanent')) {
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

    // ===========================================
    // CONFIRMATION POUR LE MODE MAINTENANCE
    // ===========================================
    const maintenanceCheckbox = document.getElementById('maintenance_mode');
    if (maintenanceCheckbox) {
        maintenanceCheckbox.addEventListener('change', function(e) {
            if (this.checked) {
                if (!confirm('⚠️ Attention : Activer le mode maintenance rendra le site inaccessible aux visiteurs. Confirmez-vous ?')) {
                    e.preventDefault();
                    this.checked = false;
                }
            }
        });
    }
});
</script>
@endpush