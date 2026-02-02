<!-- Formulaire de profil -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0">Modifier mon profil</h4>
    </div>
    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="row mb-4">
                <div class="col-md-3 text-center">
                    <div class="position-relative mb-3">
                        <img src="{{ $user->getProfilePhotoUrlAttribute() }}"
                             alt="Photo de profil" class="rounded-circle img-thumbnail"
                             style="width: 150px; height: 150px; object-fit: cover;" id="profile-image-preview">
                        <label for="profil_photo" class="btn btn-sm btn-primary position-absolute bottom-0 end-0">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" name="profil_photo" id="profil_photo" class="d-none" accept="image/*">
                    </div>
                    @error('profil_photo')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-9">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                            @error('prenom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Autres champs du formulaire -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="genre" class="form-label">Genre</label>
                    <select class="form-select @error('genre') is-invalid @enderror" id="genre" name="genre">
                        <option value="" disabled {{ old('genre', $user->genre) ? '' : 'selected' }}>Sélectionnez votre genre</option>
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
                        <option value="" disabled {{ old('tranche_age', $user->tranche_age) ? '' : 'selected' }}>Sélectionnez votre tranche d'âge</option>
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

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="address" class="form-label">Adresse</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                        id="address" name="address" value="{{ old('address', $user->address) }}">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="city" class="form-label">Ville</label>
                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                        id="city" name="city" value="{{ old('city', $user->city) }}">
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="country" class="form-label">Pays</label>
                    <input type="text" class="form-control @error('country') is-invalid @enderror"
                        id="country" name="country" value="{{ old('country', $user->country) }}">
                    @error('country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>