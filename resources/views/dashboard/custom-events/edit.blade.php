@extends('layouts.dashboard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h2 class="h5 mb-0">Modifier l'événement : {{ $event->title }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('custom-events.update', $event) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="title" class="form-label">Titre de l'événement *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $event->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="type" class="form-label">Type d'événement *</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="mariage" {{ old('type', $event->type) == 'mariage' ? 'selected' : '' }}>Mariage</option>
                                    <option value="anniversaire" {{ old('type', $event->type) == 'anniversaire' ? 'selected' : '' }}>Anniversaire</option>
                                    <option value="conference" {{ old('type', $event->type) == 'conference' ? 'selected' : '' }}>Conférence</option>
                                    <option value="soiree" {{ old('type', $event->type) == 'soiree' ? 'selected' : '' }}>Soirée</option>
                                    <option value="autre" {{ old('type', $event->type) == 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="guest_limit" class="form-label">Nombre maximum d'invités</label>
                                <input type="number" class="form-control @error('guest_limit') is-invalid @enderror" 
                                       id="guest_limit" name="guest_limit" value="{{ old('guest_limit', $event->guest_limit) }}" min="1">
                                @error('guest_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="start_date" class="form-label">Date et heure de début *</label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" 
                                       value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="end_date" class="form-label">Date et heure de fin</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" 
                                       value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d\TH:i') : '') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="location" class="form-label">Lieu *</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location', $event->location) }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">Image de l'événement</label>
                            
                            @if($event->image)
                                <div class="mb-3">
                                    <img src="{{ Storage::url($event->image) }}" alt="Image actuelle" class="img-thumbnail" style="max-height: 200px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                        <label class="form-check-label text-danger" for="remove_image">
                                            Supprimer l'image actuelle
                                        </label>
                                    </div>
                                </div>
                            @endif
                            
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <div class="form-text">Taille maximale : 2MB. Formats acceptés : jpg, jpeg, png, gif</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <a href="{{ route('custom-events.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Set minimum end date to be after start date
    document.getElementById('start_date').addEventListener('change', function() {
        const endDateInput = document.getElementById('end_date');
        if (!endDateInput.value || new Date(endDateInput.value) <= new Date(this.value)) {
            const endDate = new Date(this.value);
            endDate.setHours(endDate.getHours() + 2); // Default to 2 hours after start
            endDateInput.min = this.value;
            endDateInput.value = endDate.toISOString().slice(0, 16);
        }
    });
</script>
@endpush
@endsection
