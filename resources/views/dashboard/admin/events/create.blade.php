@extends('layouts.dashboard')

@section('title', 'Créer un événement')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Créer un événement</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Formulaire de création
        </div>
        <div class="card-body">
            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Titre de l'événement</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required maxlength="191">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Catégorie</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Sélectionnez une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if(auth()->user()->hasRole(3))
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="organizer_id" class="form-label">Organisateur</label>
                        <select class="form-select" id="organizer_id" name="organizer_id">
                            <option value="">Sélectionnez un organisateur</option>
                            @foreach($organizers as $organizer)
                                <option value="{{ $organizer->id }}" {{ old('organizer_id') == $organizer->id ? 'selected' : '' }}>
                                    {{ $organizer->company_name ?? $organizer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Date de début</label>
                        <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">Date de fin</label>
                        <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="lieu" class="form-label">Lieu</label>
                        <input type="text" class="form-control @error('lieu') is-invalid @enderror" id="lieu" name="lieu" value="{{ old('lieu') }}" required maxlength="100">
                        @error('lieu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" value="{{ old('adresse') }}" required maxlength="191">
                        @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="ville" class="form-label">Ville</label>
                        <input type="text" class="form-control @error('ville') is-invalid @enderror" id="ville" name="ville" value="{{ old('ville') }}" required maxlength="255">
                        @error('ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="pays" class="form-label">Pays</label>
                        <input type="text" class="form-control @error('pays') is-invalid @enderror" id="pays" name="pays" value="{{ old('pays') }}" required maxlength="255">
                        @error('pays')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="adresse_map" class="form-label">Rechercher l'adresse sur la carte</label>
                        <input type="text" class="form-control @error('adresse_map') is-invalid @enderror" id="adresse_map" name="adresse_map" value="{{ old('adresse_map') }}" maxlength="255" placeholder="Rechercher une adresse...">
                        @error('adresse_map')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div id="map" style="height: 400px; width: 100%;"></div>
                        <small class="text-muted">Cliquez sur la carte ou recherchez une adresse pour positionner le lieu</small>
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="image" class="form-label">Image de l'événement</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="event_type" class="form-label">Type d'événement</label>
                        <select class="form-select @error('event_type') is-invalid @enderror" id="event_type" name="event_type">
                            <option value="Espace libre" {{ old('event_type') == 'Espace libre' ? 'selected' : '' }}>Espace libre</option>
                            <option value="Plan de salle" {{ old('event_type') == 'Plan de salle' ? 'selected' : '' }}>Plan de salle</option>
                            <option value="Mixte" {{ old('event_type') == 'Mixte' ? 'selected' : '' }}>Mixte</option>
                        </select>
                        @error('event_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Type de billet</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="Gratuit" {{ old('status') == 'Gratuit' ? 'selected' : '' }}>Gratuit</option>
                            <option value="Payant" {{ old('status') == 'Payant' ? 'selected' : '' }}>Payant</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="etat" class="form-label">État</label>
                        <select class="form-select @error('etat') is-invalid @enderror" id="etat" name="etat">
                            <option value="En cours" {{ old('etat') == 'En cours' ? 'selected' : '' }}>En cours</option>
                            <option value="Archivé" {{ old('etat') == 'Archivé' ? 'selected' : '' }}>Archivé</option>
                            <option value="Annulé" {{ old('etat') == 'Annulé' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        @error('etat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Section des billets (visible seulement si l'événement est payant) -->
                <div id="tickets-section" style="display: {{ old('status') == 'Payant' ? 'block' : 'none' }};">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h4>Billets</h4>
                            <div id="tickets-container">
                                <!-- Les billets existants seront ajoutés ici -->
                                @if(old('tickets'))
                                    @foreach(old('tickets') as $index => $ticket)
                                        <div class="ticket-item mb-3 border p-3">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="form-label">Nom du billet</label>
                                                    <input type="text" class="form-control" name="tickets[{{ $index }}][nom]" value="{{ $ticket['nom'] ?? '' }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Prix (XAF)</label>
                                                    <input type="number" class="form-control" name="tickets[{{ $index }}][prix]" value="{{ $ticket['prix'] ?? '' }}" min="0" step="0.01" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Quantité</label>
                                                    <input type="number" class="form-control" name="tickets[{{ $index }}][quantite]" value="{{ $ticket['quantite'] ?? '' }}" min="1" required>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger remove-ticket">Supprimer</button>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-12">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control" name="tickets[{{ $index }}][description]" rows="2">{{ $ticket['description'] ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" id="add-ticket" class="btn btn-secondary mt-2">
                                <i class="fas fa-plus"></i> Ajouter un billet
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Événement en vedette</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" value="1" {{ old('is_approved') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_approved">Approuvé</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="schedule_publication" name="schedule_publication" value="1">
                            <label class="form-check-label" for="schedule_publication">Programmer la publication</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3" id="publish_date_container" style="display: none;">
                    <div class="col-md-6">
                        <label for="publish_at" class="form-label">Date de publication</label>
                        <input type="datetime-local" class="form-control @error('publish_at') is-invalid @enderror" id="publish_at" name="publish_at" value="{{ old('publish_at') }}">
                        @error('publish_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="keywords" class="form-label">Mots-clés (séparés par des virgules)</label>
                        <input type="text" class="form-control @error('keywords') is-invalid @enderror" id="keywords" name="keywords" value="{{ old('keywords') }}">
                        @error('keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Créer l'événement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la date de publication
    const schedulePublication = document.getElementById('schedule_publication');
    const publishDateContainer = document.getElementById('publish_date_container');
    const publishAtInput = document.getElementById('publish_at');

    schedulePublication.addEventListener('change', function() {
        if (this.checked) {
            publishDateContainer.style.display = 'block';
            publishAtInput.required = true;
        } else {
            publishDateContainer.style.display = 'none';
            publishAtInput.required = false;
            publishAtInput.value = '';
        }
    });

    // Gestion des billets
    const statusSelect = document.getElementById('status');
    const ticketsSection = document.getElementById('tickets-section');
    const ticketsContainer = document.getElementById('tickets-container');
    const addTicketButton = document.getElementById('add-ticket');

    let ticketIndex = {{ old('tickets') ? count(old('tickets')) : 0 }};

    // Afficher/masquer la section des billets selon le type d'événement
    statusSelect.addEventListener('change', function() {
        if (this.value === 'Payant') {
            ticketsSection.style.display = 'block';
            // Ajouter un billet par défaut si c'est le premier
            if (ticketIndex === 0) {
                addTicket();
            }
        } else {
            ticketsSection.style.display = 'none';
        }
    });

    // Initialiser la section des billets si nécessaire
    if (statusSelect.value === 'Payant' && ticketIndex === 0) {
        addTicket();
    }

    // Fonction pour ajouter un nouveau billet
    function addTicket() {
        const ticketHtml = `
            <div class="ticket-item mb-3 border p-3">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Nom du billet</label>
                        <input type="text" class="form-control" name="tickets[${ticketIndex}][nom]" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Prix (XAF)</label>
                        <input type="number" class="form-control" name="tickets[${ticketIndex}][prix]" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quantité</label>
                        <input type="number" class="form-control" name="tickets[${ticketIndex}][quantite]" min="1" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-ticket">Supprimer</button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="tickets[${ticketIndex}][description]" rows="2"></textarea>
                    </div>
                </div>
            </div>
        `;

        ticketsContainer.insertAdjacentHTML('beforeend', ticketHtml);
        ticketIndex++;
    }

    // Gestion du bouton "Ajouter un billet"
    addTicketButton.addEventListener('click', addTicket);

    // Gestion de la suppression des billets
    ticketsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-ticket')) {
            e.target.closest('.ticket-item').remove();
            // Si on supprime le dernier billet et qu'il n'en reste plus, on en recrée un
            if (ticketsContainer.querySelectorAll('.ticket-item').length === 0 && statusSelect.value === 'Payant') {
                addTicket();
            }
        }
    });

    // Initialisation de la carte Google Maps
    const map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 48.8566, lng: 2.3522 }, // Paris par défaut
        zoom: 12
    });

    // Création de l'autocomplete pour la recherche d'adresse
    const autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('adresse_map'),
        { types: ['geocode'] }
    );

    // Création du marqueur
    const marker = new google.maps.Marker({
        map: map,
        draggable: true
    });

    // Position initiale si des coordonnées existent déjà
    const initialLat = parseFloat(document.getElementById('latitude').value) || 48.8566;
    const initialLng = parseFloat(document.getElementById('longitude').value) || 2.3522;
    const initialPosition = { lat: initialLat, lng: initialLng };

    marker.setPosition(initialPosition);
    map.setCenter(initialPosition);

    // Mise à jour de la carte lors de la sélection d'une adresse
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }

        // Mise à jour de la carte
        map.setCenter(place.geometry.location);
        map.setZoom(15);
        marker.setPosition(place.geometry.location);

        // Mise à jour des champs d'adresse
        updateAddressFields(place);
    });

    // Mise à jour des champs lors du déplacement du marqueur
    marker.addListener('dragend', function() {
        const geocoder = new google.maps.Geocoder();
        const latLng = marker.getPosition();

        document.getElementById('latitude').value = latLng.lat();
        document.getElementById('longitude').value = latLng.lng();

        geocoder.geocode({ location: latLng }, function(results, status) {
            if (status === 'OK' && results[0]) {
                updateAddressFields(results[0]);
            }
        });
    });

    // Gestion du clic sur la carte
    map.addListener('click', function(e) {
        marker.setPosition(e.latLng);
        document.getElementById('latitude').value = e.latLng.lat();
        document.getElementById('longitude').value = e.latLng.lng();

        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: e.latLng }, function(results, status) {
            if (status === 'OK' && results[0]) {
                updateAddressFields(results[0]);
            }
        });
    });

    // Fonction pour mettre à jour les champs d'adresse
    function updateAddressFields(place) {
        // Mise à jour des coordonnées
        document.getElementById('latitude').value = place.geometry.location.lat();
        document.getElementById('longitude').value = place.geometry.location.lng();

        // Mise à jour des champs d'adresse
        const addressComponents = place.address_components;
        let streetNumber = '';
        let route = '';
        let city = '';
        let country = '';
        let postalCode = '';

        for (const component of addressComponents) {
            const type = component.types[0];
            if (type === 'street_number') {
                streetNumber = component.long_name;
            } else if (type === 'route') {
                route = component.long_name;
            } else if (type === 'locality') {
                city = component.long_name;
            } else if (type === 'country') {
                country = component.long_name;
            } else if (type === 'postal_code') {
                postalCode = component.long_name;
            }
        }

        // Mise à jour des champs du formulaire
        document.getElementById('adresse').value = (streetNumber ? streetNumber + ' ' : '') + route;
        document.getElementById('ville').value = city;
        document.getElementById('pays').value = country;
        document.getElementById('lieu').value = place.name || '';
        document.getElementById('adresse_map').value = place.formatted_address || '';
    }

    // Validation du formulaire
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        if (schedulePublication.checked && !publishAtInput.value) {
            e.preventDefault();
            alert('Veuillez sélectionner une date de publication');
            return;
        }

        if (statusSelect.value === 'Payant') {
            const tickets = ticketsContainer.querySelectorAll('.ticket-item');
            if (tickets.length === 0) {
                e.preventDefault();
                alert('Veuillez ajouter au moins un billet pour un événement payant');
                return;
            }
        }
    });
});
</script>
@endpush
