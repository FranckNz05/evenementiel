@extends('layouts.dashboard')

@section('title', 'Modifier mon profil organisateur')

@section('content')
<div class="container py-5">
    <div class="row">

        <!-- Contenu principal -->
        <div class="col-md-9">
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

            @if($completionPercentage < 100)
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading">Complétez votre profil ({{ $completionPercentage }}%)</h5>
                            <p class="mb-0">Pour une meilleure visibilité, complétez :
                                @foreach($missingFields as $field)
                                    @lang('organizer.fields.'.$field)@if(!$loop->last), @endif
                                @endforeach
                            </p>
                        </div>
                        <div class="progress w-25 ms-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercentage }}%"></div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulaire organisateur -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Informations Professionnelles</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('organizer.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Nom de la société*</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                    id="company_name" name="company_name"
                                    value="{{ old('company_name', $organizer->company_name) }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="slogan" class="form-label">Slogan</label>
                                <input type="text" class="form-control @error('slogan') is-invalid @enderror"
                                    id="slogan" name="slogan"
                                    value="{{ old('slogan', $organizer->slogan) }}">
                                @error('slogan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description*</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                    id="description" name="description" rows="4" required>{{ old('description', $organizer->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email professionnel*</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email"
                                    value="{{ old('email', $organizer->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="website" class="form-label">Site web</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror"
                                    id="website" name="website"
                                    value="{{ old('website', $organizer->website) }}">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone_primary" class="form-label">Téléphone principal*</label>
                                <input type="tel" class="form-control @error('phone_primary') is-invalid @enderror"
                                    id="phone_primary" name="phone_primary"
                                    value="{{ old('phone_primary', $organizer->phone_primary) }}" required>
                                @error('phone_primary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone_secondary" class="form-label">Téléphone secondaire</label>
                                <input type="tel" class="form-control @error('phone_secondary') is-invalid @enderror"
                                    id="phone_secondary" name="phone_secondary"
                                    value="{{ old('phone_secondary', $organizer->phone_secondary) }}">
                                @error('phone_secondary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="address" class="form-label">Adresse*</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    id="address" name="address"
                                    value="{{ old('address', $organizer->address) }}" required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="city" class="form-label">Ville*</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    id="city" name="city"
                                    value="{{ old('city', $organizer->city) }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="country" class="form-label">Pays*</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                    id="country" name="country"
                                    value="{{ old('country', $organizer->country) }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                    id="logo" name="logo" accept="image/*">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($organizer->logo)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$organizer->logo) }}" alt="Logo" style="max-height: 100px;" id="logo-preview">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="banner_image" class="form-label">Bannière</label>
                                <input type="file" class="form-control @error('banner_image') is-invalid @enderror"
                                    id="banner_image" name="banner_image" accept="image/*">
                                @error('banner_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($organizer->banner_image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$organizer->banner_image) }}" alt="Bannière" style="max-height: 100px;" id="banner-preview">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Réseaux sociaux</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="facebook" class="form-label">Facebook</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                            <input type="url" class="form-control" id="facebook" name="facebook"
                                                value="{{ old('facebook', $socialMedia['facebook'] ?? '') }}" placeholder="https://facebook.com/votrepage">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="twitter" class="form-label">Twitter</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                            <input type="url" class="form-control" id="twitter" name="twitter"
                                                value="{{ old('twitter', $socialMedia['twitter'] ?? '') }}" placeholder="https://twitter.com/votrecompte">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="instagram" class="form-label">Instagram</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                            <input type="url" class="form-control" id="instagram" name="instagram"
                                                value="{{ old('instagram', $socialMedia['instagram'] ?? '') }}" placeholder="https://instagram.com/votrecompte">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="linkedin" class="form-label">LinkedIn</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-linkedin-in"></i></span>
                                            <input type="url" class="form-control" id="linkedin" name="linkedin"
                                                value="{{ old('linkedin', $socialMedia['linkedin'] ?? '') }}" placeholder="https://linkedin.com/company/votresociete">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prévisualisation des images
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    document.getElementById('logo').addEventListener('change', function() {
        previewImage(this, 'logo-preview');
    });

    document.getElementById('banner_image').addEventListener('change', function() {
        previewImage(this, 'banner-preview');
    });
});
</script>
@endpush
