@extends('layouts.dashboard')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-navy">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Création d'événement personnalisé
                </h4>
                <a href="{{ route('organizer.events.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
            </div>

            <!-- Barre de progression -->
            <div class="progress mb-4" style="height: 8px;">
                <div class="progress-bar bg-gold" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
                    <form method="POST" action="{{ route('custom-personal-events.store') }}" autocomplete="off">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label text-navy">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" selected disabled>Sélectionnez une catégorie</option>
                                    
                                    <!-- 1. CÉLÉBRATIONS FAMILIALES -->
                                    <optgroup label="🎉 CÉLÉBRATIONS FAMILIALES">
                                        <option value="Mariage">Mariage</option>
                                        <option value="Anniversaire">Anniversaire</option>
                                        <option value="Baptême/Parrainage">Baptême/Parrainage</option>
                                        <option value="Baby shower">Baby shower</option>
                                        <option value="Gender reveal">Gender reveal</option>
                                        <option value="Bar/Bat Mitzvah">Bar/Bat Mitzvah</option>
                                        <option value="Fiançailles">Fiançailles</option>
                                        <option value="Anniversaire de mariage">Anniversaire de mariage</option>
                                        <option value="Réunion de famille">Réunion de famille</option>
                                    </optgroup>

                                    <!-- 2. ÉVÉNEMENTS PROFESSIONNELS -->
                                    <optgroup label="🎓 ÉVÉNEMENTS PROFESSIONNELS">
                                        <option value="Séminaire privé">Séminaire privé</option>
                                        <option value="Retraite d'entreprise">Retraite d'entreprise</option>
                                        <option value="Célébration de départ à la retraite">Célébration de départ à la retraite</option>
                                        <option value="Célébration de promotion">Célébration de promotion</option>
                                        <option value="Événement de remerciement">Événement de remerciement</option>
                                        <option value="Soutenance de thèse">Soutenance de thèse</option>
                                        <option value="Atelier collaboratif">Atelier collaboratif</option>
                                    </optgroup>

                                    <!-- 3. CÉRÉMONIES & RÉCOMPENSES -->
                                    <optgroup label="🏆 CÉRÉMONIES & RÉCOMPENSES">
                                        <option value="Cérémonie de remise de diplômes">Cérémonie de remise de diplômes</option>
                                        <option value="Cérémonie de remise de prix">Cérémonie de remise de prix</option>
                                        <option value="Hommage à une personne">Hommage à une personne</option>
                                        <option value="Cérémonie de commémoration">Cérémonie de commémoration</option>
                                    </optgroup>

                                    <!-- 4. ÉVÉNEMENTS SOCIAUX & GASTRONOMIQUES -->
                                    <optgroup label="🍽 ÉVÉNEMENTS SOCIAUX & GASTRONOMIQUES">
                                        <option value="Dîner gastronomique">Dîner gastronomique</option>
                                        <option value="Dégustation privée">Dégustation privée</option>
                                        <option value="Soirée jeux de société">Soirée jeux de société</option>
                                        <option value="Fête de bienvenue">Fête de bienvenue</option>
                                        <option value="Fête de retrouvailles">Fête de retrouvailles</option>
                                    </optgroup>

                                    <!-- 5. ÉVÉNEMENTS CULTURELS & CRÉATIFS -->
                                    <optgroup label="🎨 ÉVÉNEMENTS CULTURELS & CRÉATIFS">
                                        <option value="Vernissage privé">Vernissage privé</option>
                                        <option value="Lecture littéraire">Lecture littéraire</option>
                                        <option value="Projection privée">Projection privée</option>
                                        <option value="Cercle de poésie">Cercle de poésie</option>
                                    </optgroup>

                                    <!-- 6. ÉVÉNEMENTS BIEN-ÊTRE & SPIRITUELS -->
                                    <optgroup label="🌿 ÉVÉNEMENTS BIEN-ÊTRE & SPIRITUELS">
                                        <option value="Cérémonie de méditation">Cérémonie de méditation</option>
                                        <option value="Retraite spirituelle">Retraite spirituelle</option>
                                        <option value="Célébration solstice/équinoxe">Célébration solstice/équinoxe</option>
                                        <option value="Cercle de parole">Cercle de parole</option>
                                    </optgroup>

                                    <!-- 7. FÊTES & SURPRISES -->
                                    <optgroup label="🎂 FÊTES & SURPRISES">
                                        <option value="Fête surprise">Fête surprise</option>
                                        <option value="Célébration de réussite">Célébration de réussite</option>
                                        <option value="Célébration de guérison">Célébration de guérison</option>
                                        <option value="Célébration de divorce">Célébration de divorce</option>
                                        <option value="Célébration de départ">Célébration de départ</option>
                                    </optgroup>

                                    <!-- 8. ÉVÉNEMENTS COMMÉMORATIFS & TRADITIONNELS -->
                                    <optgroup label="📅 ÉVÉNEMENTS COMMÉMORATIFS & TRADITIONNELS">
                                        <option value="Cérémonie religieuse">Cérémonie religieuse</option>
                                        <option value="Commémoration familiale">Commémoration familiale</option>
                                        <option value="Célébration traditionnelle">Célébration traditionnelle</option>
                                        <option value="Événement saisonnier">Événement saisonnier</option>
                                    </optgroup>

                                    <!-- 9. AUTRES ÉVÉNEMENTS PERSONNALISÉS -->
                                    <optgroup label="🎁 AUTRES ÉVÉNEMENTS PERSONNALISÉS">
                                        <option value="Événement personnalisé">Autre événement personnalisé</option>
                                    </optgroup>
                                </select>
                                <div class="form-text">Sélectionnez la catégorie qui correspond le mieux à votre événement</div>
                            </div>
                            <div class="col-md-6">
                                <label for="title" class="form-label text-navy">Titre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-6">
                                <label for="start_date" class="form-label text-navy">Date et heure de début <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label text-navy">Date et heure de fin <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label text-navy">Lieu de l'événement <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label text-navy">Adresse <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>
                            <div class="col-md-6">
                                <label for="send_at" class="form-label text-navy">Date et heure d'envoi des invitations <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="send_at" name="send_at" required>
                            </div>
                            <div class="col-md-6">
                                <label for="invitation_message" class="form-label text-navy">Texte d'invitation (optionnel)</label>
                                <textarea class="form-control" id="invitation_message" name="invitation_message" rows="2"></textarea>
                                @php
                                    $defaultName = auth()->user()->name ?? "L'organisateur";
                                    $defaultMsg = $defaultName . ' vous invite à ' . old('title') . ' le ' . old('start_date') . ' au ' . old('location');
                                @endphp
                                <small class="form-text text-muted">Par défaut : "{{ $defaultMsg }}"</small>
                            </div>
                        </div>
                        <hr class="my-4">
                        <h4 class="text-navy mb-3"><i class="fas fa-users me-2"></i>Invités</h4>
                        <div class="mb-3">
                            <label class="form-label text-navy">Voulez-vous ajouter vos invités maintenant ? <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="add_guests_now" id="addGuestsNowYes" value="1" checked onclick="toggleGuestsSection(true)">
                                    <label class="form-check-label" for="addGuestsNowYes">Oui, maintenant</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="add_guests_now" id="addGuestsNowNo" value="0" onclick="toggleGuestsSection(false)">
                                    <label class="form-check-label" for="addGuestsNowNo">Plus tard</label>
                                </div>
                            </div>
                        </div>
                        <div id="guests-section">
                            <div id="guests-list"></div>
                            <button type="button" class="btn btn-outline-navy mb-3" onclick="addGuest()">
                                <i class="fas fa-user-plus me-1"></i> Ajouter un invité
                            </button>
                        </div>
            <div class="d-flex justify-content-between gap-2 mt-4">
                <a href="{{ route('organizer.events.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Annuler
                </a>
                <button type="submit" class="btn btn-gold btn-lg">
                    <i class="fas fa-check-circle me-1"></i>Créer l'événement
                </button>
            </div>
                    </form>
        </div>
    </div>
</div>
<style>
    .bg-navy { background-color: #1a2235 !important; }
    .text-navy { color: #1a2235 !important; }
    .bg-gold { background-color: #e6b800 !important; }
    .text-gold { color: #e6b800 !important; }
    .btn-gold { background-color: #e6b800; color: #fff; border: none; }
    .btn-gold:hover { background-color: #cfa600; color: #fff; }
    .btn-outline-navy { border: 1px solid #1a2235; color: #1a2235; }
    .btn-outline-navy:hover { background: #1a2235; color: #fff; }
    .border-navy { border-color: #1a2235 !important; }
    .bg-light-blue { background: #f0f6ff !important; }
</style>
<script>
let guests = [];
function addGuest() {
    let email = prompt("Entrez l'email de l'invité à ajouter :");
    if (!email) return;
    email = email.trim().toLowerCase();
    // Vérifie si l'email existe déjà dans les champs du formulaire
    const existingEmails = Array.from(document.querySelectorAll('input[name^="guests"][name$="[email]"]'))
        .map(input => input.value.trim().toLowerCase());
    if (existingEmails.includes(email)) {
        alert("Cet email est déjà dans la liste des invités !");
        return;
    }
    const idx = guests.length;
    const guestDiv = document.createElement('div');
    guestDiv.className = 'border rounded p-3 mb-3 bg-light';
    guestDiv.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-navy">Nom complet <span class="text-danger">*</span></label>
                <input type="text" class="form-control" placeholder="Nom complet" name="guests[${idx}][full_name]" required>
            </div>
            <div class="col-md-3">
                <label class="form-label text-navy">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" placeholder="Email" name="guests[${idx}][email]" value="${email}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label text-navy">Téléphone</label>
                <input type="text" class="form-control" placeholder="Téléphone" name="guests[${idx}][phone]">
            </div>
            <div class="col-md-2">
                <label class="form-label text-navy">Couple ?</label>
                <select class="form-control" name="guests[${idx}][is_couple]">
                    <option value="0">Non</option>
                    <option value="1">Oui</option>
                </select>
            </div>
        </div>
        <div class="text-end mt-2">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeGuest(this)"><i class="fas fa-trash"></i> Supprimer</button>
        </div>
    `;
    document.getElementById('guests-list').appendChild(guestDiv);
    guests.push({});
}
function removeGuest(btn) {
    btn.closest('.border').remove();
}
function toggleGuestsSection(show) {
    const section = document.getElementById('guests-section');
    if (show) {
        section.style.display = '';
    } else {
        section.style.display = 'none';
        // Optionnel : vider la liste d'invités si on choisit "plus tard"
        document.getElementById('guests-list').innerHTML = '';
        guests = [];
    }
}
// Initialisation : afficher la section invités par défaut
document.addEventListener('DOMContentLoaded', function() {
    toggleGuestsSection(true);
});
</script>
@endsection
