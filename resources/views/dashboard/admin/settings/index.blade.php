@extends('layouts.dashboard')

@section('title', 'Paramètres de l\'application')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <p class="text-muted">Gérez les paramètres généraux de votre plateforme</p>
    </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="bg-white p-4 rounded shadow-sm">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="site_name">Nom de l'application <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="site_name" name="site_name"
                               value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}" required>
                        @error('site_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contact_email">Email de contact</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email"
                               value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
                        @error('contact_email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="site_description">Description du site</label>
                <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                @error('site_description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone_number">Numéro de téléphone</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number"
                               value="{{ old('phone_number', $settings['phone_number'] ?? '') }}">
                        @error('phone_number')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address">Adresse</label>
                        <input type="text" class="form-control" id="address" name="address"
                               value="{{ old('address', $settings['address'] ?? '') }}">
                        @error('address')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <h5 class="mt-4 mb-3">Réseaux sociaux</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="facebook_url">Facebook</label>
                        <input type="url" class="form-control" id="facebook_url" name="facebook_url"
                               value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}">
                        @error('facebook_url')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="twitter_url">X (Twitter)</label>
                        <input type="url" class="form-control" id="twitter_url" name="twitter_url"
                               value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}">
                        @error('twitter_url')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="instagram_url">Instagram</label>
                        <input type="url" class="form-control" id="instagram_url" name="instagram_url"
                               value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}">
                        @error('instagram_url')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tiktok_url">TikTok</label>
                        <input type="url" class="form-control" id="tiktok_url" name="tiktok_url"
                               value="{{ old('tiktok_url', $settings['tiktok_url'] ?? '') }}">
                        @error('tiktok_url')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <h5 class="mt-4 mb-3">Apparence</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="logo" name="logo">
                            <label class="custom-file-label" for="logo">Choisir un fichier</label>
                        </div>
                        @error('logo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @if(isset($settings['logo']) && $settings['logo'])
                            <div class="mt-2">
                                <img src="{{ asset($settings['logo']) }}" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="favicon">Favicon</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="favicon" name="favicon">
                            <label class="custom-file-label" for="favicon">Choisir un fichier</label>
                        </div>
                        @error('favicon')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @if(isset($settings['favicon']) && $settings['favicon'])
                            <div class="mt-2">
                                <img src="{{ asset($settings['favicon']) }}" alt="Favicon" class="img-thumbnail" style="max-height: 50px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode"
                           value="1" {{ old('maintenance_mode', $settings['maintenance_mode'] ?? '') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="maintenance_mode">Mode maintenance</label>
                </div>
                <small class="form-text text-muted">Lorsque le mode maintenance est activé, seuls les administrateurs peuvent accéder au site.</small>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Enregistrer les modifications</button>
        </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script pour afficher le nom du fichier sélectionné dans l'input file
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.custom-file-input').forEach(function(input) {
            input.addEventListener('change', function() {
                var fileName = this.files[0].name;
                this.nextElementSibling.textContent = fileName;
            });
        });
    });
</script>
@endpush




