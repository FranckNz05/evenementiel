@extends('layouts.dashboard')

@section('title', 'Paramètres Système')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Paramètres Système</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item active">Configuration</li>
                    <li class="breadcrumb-item active">Paramètres Système</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card modern-card shadow-sm mb-5">
        <div class="card-header-modern">
            <h5 class="card-title my-1 text-white">
                <i class="fas fa-cog me-2"></i>Configuration Générale
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Section: Informations de base -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h6 class="text-primary fw-bold text-uppercase mb-4 border-bottom pb-2">
                            <i class="fas fa-info-circle me-2"></i>Informations de l'application
                        </h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="site_name" class="form-label fw-bold text-dark">Nom de l'application <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="site_name" name="site_name"
                                   value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}" required>
                            @error('site_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="contact_email" class="form-label fw-bold text-dark">Email de contact</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email"
                                   value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
                            @error('contact_email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <label for="site_description" class="form-label fw-bold text-dark">Description de la plateforme</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                            @error('site_description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="phone_number" class="form-label fw-bold text-dark">Numéro de téléphone</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number"
                                   value="{{ old('phone_number', $settings['phone_number'] ?? '') }}">
                            @error('phone_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="address" class="form-label fw-bold text-dark">Adresse physique</label>
                            <input type="text" class="form-control" id="address" name="address"
                                   value="{{ old('address', $settings['address'] ?? '') }}">
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Réseaux Sociaux -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h6 class="text-primary fw-bold text-uppercase mb-4 border-bottom pb-2">
                            <i class="fas fa-share-alt me-2"></i>Réseaux sociaux
                        </h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="facebook_url" class="form-label fw-bold text-dark">Lien Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fab fa-facebook-f text-primary"></i></span>
                                <input type="url" class="form-control" id="facebook_url" name="facebook_url"
                                       value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}" placeholder="https://facebook.com/...">
                            </div>
                            @error('facebook_url')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="twitter_url" class="form-label fw-bold text-dark">Lien X (Twitter)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fab fa-twitter text-dark"></i></span>
                                <input type="url" class="form-control" id="twitter_url" name="twitter_url"
                                       value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}" placeholder="https://x.com/...">
                            </div>
                            @error('twitter_url')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="instagram_url" class="form-label fw-bold text-dark">Lien Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fab fa-instagram text-danger"></i></span>
                                <input type="url" class="form-control" id="instagram_url" name="instagram_url"
                                       value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" placeholder="https://instagram.com/...">
                            </div>
                            @error('instagram_url')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="tiktok_url" class="form-label fw-bold text-dark">Lien TikTok</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fab fa-tiktok text-dark"></i></span>
                                <input type="url" class="form-control" id="tiktok_url" name="tiktok_url"
                                       value="{{ old('tiktok_url', $settings['tiktok_url'] ?? '') }}" placeholder="https://tiktok.com/@...">
                            </div>
                            @error('tiktok_url')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Apparence (Logo/Favicon) -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h6 class="text-primary fw-bold text-uppercase mb-4 border-bottom pb-2">
                            <i class="fas fa-palette me-2"></i>Identité visuelle
                        </h6>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="logo" class="form-label fw-bold text-dark">Logo principal</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            </div>
                            @error('logo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @if(isset($settings['logo']) && $settings['logo'])
                                <div class="mt-2 p-2 bg-light rounded text-center border">
                                    <p class="small text-muted mb-2">Logo actuel :</p>
                                    <img src="{{ asset($settings['logo']) }}" alt="Logo" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="favicon" class="form-label fw-bold text-dark">Favicon (Icône de navigateur)</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*">
                            </div>
                            @error('favicon')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @if(isset($settings['favicon']) && $settings['favicon'])
                                <div class="mt-2 p-2 bg-light rounded text-center border">
                                    <p class="small text-muted mb-2">Favicon actuel :</p>
                                    <img src="{{ asset($settings['favicon']) }}" alt="Favicon" class="img-fluid" style="max-height: 40px; width: 40px; object-fit: contain;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Section: Maintenance -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-danger fw-bold text-uppercase mb-4 border-bottom pb-2">
                            <i class="fas fa-tools me-2"></i>Maintenance & Sécurité
                        </h6>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded border-start border-danger border-4 shadow-sm">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="maintenance_mode" name="maintenance_mode"
                                       value="1" {{ old('maintenance_mode', $settings['maintenance_mode'] ?? '') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger" for="maintenance_mode">Mode maintenance actif</label>
                            </div>
                            <p class="text-muted small mb-0 mt-2">
                                <i class="fas fa-info-circle me-1"></i> Lorsque ce mode est activé, seuls les administrateurs peuvent accéder au site. Les clients verront une page de maintenance.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bouton Sauvegarder -->
                <div class="row mt-5">
                    <div class="col-12 text-end">
                        <hr class="mb-4">
                        <button type="submit" class="btn btn-primary-modern shadow-sm px-5">
                            <i class="fas fa-save me-2"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Optionnel : Prévisualisation des images avant upload
        const logoInput = document.getElementById('logo');
        const faviconInput = document.getElementById('favicon');
        
        // ... (Logique de prévisualisation si nécessaire)
    });
</script>
@endpush
