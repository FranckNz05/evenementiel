@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-calendar-plus me-2"></i>Créer un événement personnalisé</h1>
        <div class="page-actions">
            <a href="{{ route('custom-events.index') }}" class="modern-btn btn-secondary-modern">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                    <form action="{{ route('custom-events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="title" class="form-label">Titre de l'événement *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="type" class="form-label">Type d'événement *</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="" disabled {{ !old('type') ? 'selected' : '' }}>Sélectionnez un type</option>
                                    <option value="mariage" {{ old('type') == 'mariage' ? 'selected' : '' }}>Mariage</option>
                                    <option value="anniversaire" {{ old('type') == 'anniversaire' ? 'selected' : '' }}>Anniversaire</option>
                                    <option value="conference" {{ old('type') == 'conference' ? 'selected' : '' }}>Conférence</option>
                                    <option value="soiree" {{ old('type') == 'soiree' ? 'selected' : '' }}>Soirée</option>
                                    <option value="autre" {{ old('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="guest_limit" class="form-label">Nombre maximum d'invités</label>
                                <input type="number" class="form-control @error('guest_limit') is-invalid @enderror" 
                                       id="guest_limit" name="guest_limit" value="{{ old('guest_limit', 50) }}" min="1">
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
                                       value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="end_date" class="form-label">Date et heure de fin</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="location" class="form-label">Lieu *</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location') }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">Image de l'événement</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <div class="form-text">Taille maximale : 2MB. Formats acceptés : jpg, jpeg, png, gif</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end mt-5 gap-2">
                            <a href="{{ route('custom-events.index') }}" class="modern-btn btn-secondary-modern">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="modern-btn btn-success-modern">
                                <i class="fas fa-save"></i> Créer l'événement
                            </button>
                        </div>
                    </form>
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
