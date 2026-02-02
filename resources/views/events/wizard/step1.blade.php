@extends('layouts.dashboard')

@section('content')
<div class="container-fluid dashboard-container">
    {{-- En-tête --}}
    <x-page-header 
        title="Création d'événement" 
        icon="fas fa-calendar-plus"
        subtitle="Étape 1/4 - Informations de base">
    </x-page-header>

    <!-- Barre de progression -->
    <div class="progress mb-4" style="height: 10px; border-radius: var(--radius-md); background: var(--gray-200); box-shadow: var(--shadow-sm);">
        <div class="progress-bar" role="progressbar" style="width: 25%; background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-light));" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    
    <div class="modern-alert alert-info-modern mb-4">
        <i class="fas fa-info-circle"></i>
        <span>Remplissez les informations de base de votre événement</span>
    </div>
    
    <x-content-section title="Informations de l'événement" icon="fas fa-info-circle">
        <form action="{{ route('events.wizard.post.step1') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                            <!-- Titre -->
                            <div class="col-md-12">
                                <label for="title" class="form-label">Titre de l'événement <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" 
                                       value="{{ old('title', $event['title'] ?? '') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Description -->
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="5" required>{{ old('description', $event['description'] ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Catégorie -->
                            <div class="col-md-6">
                                <label for="category_id" class="form-label ">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $event['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Type d'événement -->
                            <div class="col-md-6">
                                <label for="event_type" class="form-label ">Type d'événement <span class="text-danger">*</span></label>
                                <select class="form-select @error('event_type') is-invalid @enderror" 
                                        id="event_type" name="event_type" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="Espace libre" {{ old('event_type', $event['event_type'] ?? '') == 'Espace libre' ? 'selected' : '' }}>Espace libre</option>
                                    <option value="Plan de salle" {{ old('event_type', $event['event_type'] ?? '') == 'Plan de salle' ? 'selected' : '' }}>Plan de salle</option>
                                    <option value="Mixte" {{ old('event_type', $event['event_type'] ?? '') == 'Mixte' ? 'selected' : '' }}>Mixte</option>
                                </select>
                                @error('event_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Dates -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label ">Date de début <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" 
                                       value="{{ old('start_date', isset($event['start_date']) ? Carbon\Carbon::parse($event['start_date'])->format('Y-m-d\TH:i') : '') }}" 
                                       required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_date" class="form-label ">Date de fin <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" 
                                       value="{{ old('end_date', isset($event['end_date']) ? Carbon\Carbon::parse($event['end_date'])->format('Y-m-d\TH:i') : '') }}" 
                                       required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Statut -->
                            <div class="col-md-6">
                                <label for="status" class="form-label ">Statut <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="Payant" {{ old('status', $event['status'] ?? '') == 'Payant' ? 'selected' : '' }}>Payant</option>
                                    <option value="Gratuit" {{ old('status', $event['status'] ?? '') == 'Gratuit' ? 'selected' : '' }}>Gratuit</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Mots-clés -->
                            <div class="col-md-6">
                                <label for="keywords" class="form-label ">Mots-clés</label>
                                <input type="text" class="form-control @error('keywords') is-invalid @enderror" 
                                       id="keywords" name="keywords" 
                                       value="{{ old('keywords', $event['keywords'] ?? '') }}" 
                                       placeholder="Séparés par des virgules">
                                @error('keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Image -->
                            <div class="col-md-12">
                                <label for="image" class="form-label ">Image de couverture</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                @if(isset($event['image']) && Storage::exists('public/'.$event['image']))
                                    <div class="mt-3">
                                        <img src="{{ asset('storage/'.$event['image']) }}" 
                                             alt="Image actuelle" 
                                             class="img-thumbnail" 
                                             style="max-height: 200px;">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="remove_image" name="remove_image">
                                            <label class="form-check-label " for="remove_image">
                                                Supprimer cette image
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4 pt-3" style="border-top: 2px solid var(--blanc-or);">
                            <a href="{{ route('organizer.events.index') }}" class="modern-btn btn-secondary-modern">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                            <button type="submit" class="modern-btn btn-primary-modern">
                                Suivant
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
    </x-content-section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Protection contre les doubles soumissions
    const eventForm = document.querySelector('form');
    let isSubmitting = false;
    
    if (eventForm) {
        eventForm.addEventListener('submit', function(e) {
            // Empêcher les doubles soumissions
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            // Marquer comme en cours de soumission
            isSubmitting = true;
            
            // Désactiver le bouton de soumission
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            }
            
            return true;
        });
    }
});
</script>
@endpush
