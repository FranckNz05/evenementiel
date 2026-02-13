@extends('layouts.organizer')

@section('title', 'Créer un événement')

@section('content')
<div class="container-fluid">
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
        <div class="card-body-modern">
            <form action="{{ route('organizer.events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Titre de l'événement</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
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

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="location" class="form-label">Lieu</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" required>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="event_type" class="form-label">Type d'événement</label>
                        <select class="form-select @error('event_type') is-invalid @enderror" id="event_type" name="event_type" required>
                            <option value="Espace libre" {{ old('event_type') == 'Espace libre' ? 'selected' : '' }}>Espace libre</option>
                            <option value="Plan de salle" {{ old('event_type') == 'Plan de salle' ? 'selected' : '' }}>Plan de salle</option>
                        </select>
                        @error('event_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

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
                    <div class="col-md-6">
                        <label for="image" class="form-label">Image de l'événement</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Section des billets -->
                <div class="card mt-4 mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Billets</h5>
                    </div>
                    <div class="card-body-modern">
                        <div id="tickets-container">
                            <!-- Les billets seront ajoutés ici dynamiquement -->
                        </div>
                        <button type="button" id="add-ticket" class="btn btn-outline-primary mt-3">
                            <i class="fas fa-plus-circle"></i> Ajouter un billet
                        </button>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="modern-btn btn-primary-modern">Créer l'événement</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Gestion des billets
    document.addEventListener('DOMContentLoaded', function() {
        const ticketsContainer = document.getElementById('tickets-container');
        const addTicketButton = document.getElementById('add-ticket');
        let ticketIndex = 0;

        // Fonction pour ajouter un billet
        function addTicket() {
            const ticketHtml = `
                <div class="ticket-item card mb-3" id="ticket-${ticketIndex}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Billet #${ticketIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-ticket" data-index="${ticketIndex}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="card-body-modern">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom du billet</label>
                                <input type="text" class="form-control" name="tickets[${ticketIndex}][name]" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prix (FCFA)</label>
                                <input type="number" class="form-control" name="tickets[${ticketIndex}][price]" min="0" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Quantité disponible</label>
                                <input type="number" class="form-control" name="tickets[${ticketIndex}][quantity]" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Description (optionnel)</label>
                                <textarea class="form-control" name="tickets[${ticketIndex}][description]" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Date début vente</label>
                                <input type="datetime-local" class="form-control" name="tickets[${ticketIndex}][sale_start_date]">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date fin vente</label>
                                <input type="datetime-local" class="form-control" name="tickets[${ticketIndex}][sale_end_date]">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            ticketsContainer.insertAdjacentHTML('beforeend', ticketHtml);
            ticketIndex++;
            
            // Ajouter les écouteurs d'événements pour les boutons de suppression
            const removeButtons = document.querySelectorAll('.remove-ticket');
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const index = this.getAttribute('data-index');
                    document.getElementById(`ticket-${index}`).remove();
                });
            });
        }
        
        // Ajouter un billet par défaut
        addTicket();
        
        // Écouteur d'événement pour le bouton d'ajout de billet
        addTicketButton.addEventListener('click', addTicket);
    });
</script>
@endpush
@endsection