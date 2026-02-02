@extends('layouts.dashboard')

@section('content')
<div class="container-fluid dashboard-container">
    {{-- En-tête --}}
    <x-page-header 
        title="Création d'événement" 
        icon="fas fa-map-marked-alt"
        subtitle="Étape 2/4 - Localisation">
    </x-page-header>

    <!-- Barre de progression -->
    <div class="progress mb-4" style="height: 10px; border-radius: var(--radius-md); background: var(--gray-200); box-shadow: var(--shadow-sm);">
        <div class="progress-bar" role="progressbar" style="width: 50%; background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-light));" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    
    <div class="modern-alert alert-info-modern mb-4">
        <i class="fas fa-info-circle"></i>
        <span>Renseignez les informations sur le lieu de votre événement</span>
    </div>
    
    <x-content-section title="Lieu de l'événement" icon="fas fa-location-dot">
        <form action="{{ route('events.wizard.post.step2') }}" method="POST">
            @csrf
            <div class="row g-3">
                            <!-- Lieu -->
                            <div class="col-md-6">
                                <label for="lieu" class="form-label text-navy">Lieu/Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lieu') is-invalid @enderror" 
                                       id="lieu" name="lieu" 
                                       value="{{ old('lieu', $event['lieu'] ?? '') }}" 
                                       placeholder="Ex: Salle des fêtes, Hôtel XYZ..." required>
                                @error('lieu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Adresse -->
                            <div class="col-md-6">
                                <label for="adresse" class="form-label text-navy">Adresse <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('adresse') is-invalid @enderror" 
                                       id="adresse" name="adresse" 
                                       value="{{ old('adresse', $event['adresse'] ?? '') }}" required>
                                @error('adresse')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Ville -->
                            <div class="col-md-6">
                                <label for="ville" class="form-label text-navy">Ville <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ville') is-invalid @enderror" 
                                       id="ville" name="ville" 
                                       value="{{ old('ville', $event['ville'] ?? '') }}" required>
                                @error('ville')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Pays -->
                            <div class="col-md-6">
                                <label for="pays" class="form-label text-navy">Pays <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('pays') is-invalid @enderror" 
                                       id="pays" name="pays" 
                                       value="{{ old('pays', $event['pays'] ?? '') }}" required>
                                @error('pays')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Adresse pour la carte -->
                            <div class="col-md-12">
                                <label for="adresse_map" class="form-label text-navy">Intégration carte (optionnel)</label>
                                <textarea class="form-control @error('adresse_map') is-invalid @enderror" 
                                          id="adresse_map" name="adresse_map" 
                                          rows="3" placeholder="Code iframe Google Maps ou lien">{{ old('adresse_map', $event['adresse_map'] ?? '') }}</textarea>
                                @error('adresse_map')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Collez ici le code iframe de Google Maps ou un lien vers la localisation</small>
                            </div>
                            
                            <!-- Aperçu carte -->
                            @if(isset($event['adresse_map']) && !empty($event['adresse_map']))
                            <div class="col-md-12">
                                <div class="p-3 rounded" style="background: var(--gray-100); border: 2px solid var(--blanc-or);">
                                    <h6 class="mb-3" style="color: var(--bleu-nuit); font-weight: 700;">
                                        <i class="fas fa-map-marked me-2" style="color: var(--blanc-or);"></i>
                                        Aperçu de la carte
                                    </h6>
                                    <div style="border-radius: var(--radius-md); overflow: hidden;">
                                        {!! sanitize_html($event['adresse_map']) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4 pt-3" style="border-top: 2px solid var(--blanc-or);">
                            <a href="{{ route('events.wizard.step1') }}" class="modern-btn btn-secondary-modern">
                                <i class="fas fa-arrow-left"></i>
                                Précédent
                            </a>
                            <button type="submit" class="modern-btn btn-primary-modern">
                                Suivant
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
    </x-content-section>
</div>

@push('styles')
<style>
    /* Style pour l'aperçu de la carte */
    iframe {
        width: 100%;
        height: 300px;
        border: none;
    }
</style>
@endpush

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
@endsection