<!-- Profil -->
<div class="tab-pane fade show active" id="profile">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">Mon profil</h4>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" class="form-control"
                               value="{{ auth()->user()->prenom }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control"
                               value="{{ auth()->user()->nom }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control"
                           value="{{ auth()->user()->email }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="phone" class="form-control"
                           value="{{ auth()->user()->phone }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Genre</label>
                    <select name="genre" class="form-select">
                        <option value="">Sélectionner</option>
                        <option value="M" {{ auth()->user()->genre === 'M' ? 'selected' : '' }}>Homme</option>
                        <option value="F" {{ auth()->user()->genre === 'F' ? 'selected' : '' }}>Féminin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tranche d'âge</label>
                    <select name="tranche_age" class="form-select">
                        <option value="">Sélectionner</option>
                        <option value="0-17" {{ auth()->user()->tranche_age === '0-17' ? 'selected' : '' }}>0-17 ans</option>
                        <option value="18-25" {{ auth()->user()->tranche_age === '18-25' ? 'selected' : '' }}>18-25 ans</option>
                        <option value="26-35" {{ auth()->user()->tranche_age === '26-35' ? 'selected' : '' }}>26-35 ans</option>
                        <option value="36-45" {{ auth()->user()->tranche_age === '36-45' ? 'selected' : '' }}>36-45 ans</option>
                        <option value="46+" {{ auth()->user()->tranche_age === '46+' ? 'selected' : '' }}>46+ ans</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Photo de profil</label>
                    <input type="file" name="profil_image" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
            </form>
        </div>
    </div>
</div>
