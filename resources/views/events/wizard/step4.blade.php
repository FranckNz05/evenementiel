@extends('layouts.dashboard')

@section('content')
<div class="container-fluid dashboard-container">
    {{-- En-tête --}}
    <x-page-header 
        title="Création d'événement" 
        icon="fas fa-star"
        subtitle="Étape 4/4 - Sponsors (optionnel)">
    </x-page-header>

    <!-- Barre de progression -->
    <div class="progress mb-4" style="height: 10px; border-radius: var(--radius-md); background: var(--gray-200); box-shadow: var(--shadow-sm);">
        <div class="progress-bar" role="progressbar" style="width: 100%; background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-light));" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    @if ($errors->any())
        <div class="modern-alert alert-danger-modern mb-4">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    
    <x-content-section title="Sponsors de l'événement" icon="fas fa-handshake">
        <div class="modern-alert alert-info-modern mb-4">
            <i class="fas fa-info-circle"></i>
            <span>Ajoutez des sponsors à votre événement (optionnel, max. 4 sponsors).</span>
        </div>

        <form action="{{ route('events.wizard.post.step4') }}" method="POST" enctype="multipart/form-data" id="sponsorsForm">
            @csrf

            <div id="sponsors-container">
                @if(count($sponsors) > 0)
                    @foreach($sponsors as $index => $sponsor)
                        <div class="sponsor-item p-3 mb-3 rounded" style="background: var(--gray-100); border: 2px solid var(--bleu-nuit);" id="sponsor-{{ $index }}">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 2px solid var(--blanc-or);">
                                <h5 class="mb-0" style="color: var(--bleu-nuit); font-weight: 700;">
                                    <i class="fas fa-award me-2" style="color: var(--blanc-or);"></i>
                                    Sponsor #{{ $index + 1 }}
                                </h5>
                                <button type="button" class="modern-btn btn-sm-modern btn-danger-modern remove-sponsor" data-index="{{ $index }}">
                                    <i class="fas fa-times"></i>
                                    Supprimer
                                </button>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-modern">
                                        <i class="fas fa-building"></i>
                                        Nom du sponsor <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-input-modern" name="sponsors[{{ $index }}][name]" value="{{ $sponsor['name'] }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-modern">
                                        <i class="fas fa-image"></i>
                                        Logo
                                    </label>
                                    <input type="file" class="form-input-modern" name="sponsors[{{ $index }}][logo]" accept="image/*">
                                    @if(isset($sponsor['logo_path']))
                                        <div class="mt-2">
                                            <img src="{{ Storage::url($sponsor['logo_path']) }}" alt="Logo sponsor" class="img-thumbnail" style="max-height: 100px; border-radius: var(--radius-md);">
                                            <input type="hidden" name="sponsors[{{ $index }}][existing_logo]" value="{{ $sponsor['logo_path'] }}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <button type="button" id="add-sponsor" class="modern-btn btn-secondary-modern mb-4">
                <i class="fas fa-plus-circle"></i>
                Ajouter un sponsor
            </button>
            <div id="sponsor-limit-message" class="limit-message" style="color: var(--danger); font-size: 0.9rem; margin-top: 5px; display: none;">
                Limite de 4 sponsors atteinte
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3" style="border-top: 2px solid var(--blanc-or);">
                <a href="{{ route('events.wizard.step3') }}" class="modern-btn btn-secondary-modern">
                    <i class="fas fa-arrow-left"></i>
                    Précédent
                </a>
                <button type="submit" class="modern-btn btn-success-modern">
                    <i class="fas fa-check-circle"></i>
                    Terminer
                </button>
            </div>
        </form>
    </x-content-section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sponsorsContainer = document.getElementById('sponsors-container');
    const addSponsorButton = document.getElementById('add-sponsor');
    let sponsorIndex = {{ count($sponsors) }};
    
    function updateLimitMessage() {
        const limitMessage = document.getElementById('sponsor-limit-message');
        if (sponsorIndex >= 4) {
            addSponsorButton.disabled = true;
            addSponsorButton.classList.add('disabled');
            limitMessage.style.display = 'block';
        } else {
            addSponsorButton.disabled = false;
            addSponsorButton.classList.remove('disabled');
            limitMessage.style.display = 'none';
        }
    }
    
    function addSponsor() {
        if (sponsorIndex >= 4) {
            alert('Vous ne pouvez pas ajouter plus de 4 sponsors.');
            return;
        }

        const sponsorHtml = `
            <div class="sponsor-item p-3 mb-3 rounded" style="background: var(--gray-100); border: 2px solid var(--bleu-nuit);" id="sponsor-${sponsorIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 2px solid var(--blanc-or);">
                    <h5 class="mb-0" style="color: var(--bleu-nuit); font-weight: 700;">
                        <i class="fas fa-award me-2" style="color: var(--blanc-or);"></i>
                        Sponsor #${sponsorIndex + 1}
                    </h5>
                    <button type="button" class="modern-btn btn-sm-modern btn-danger-modern remove-sponsor" data-index="${sponsorIndex}">
                        <i class="fas fa-times"></i> Supprimer
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-modern">
                            <i class="fas fa-building"></i>
                            Nom du sponsor <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-input-modern" name="sponsors[${sponsorIndex}][name]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-modern">
                            <i class="fas fa-image"></i>
                            Logo
                        </label>
                        <input type="file" class="form-input-modern" name="sponsors[${sponsorIndex}][logo]" accept="image/*">
                    </div>
                </div>
            </div>
        `;
        
        sponsorsContainer.insertAdjacentHTML('beforeend', sponsorHtml);
        setupSponsorListeners(sponsorIndex);
        sponsorIndex++;
        updateLimitMessage();
    }
    
    function setupSponsorListeners(index) {
        const removeButton = document.querySelector(`#sponsor-${index} .remove-sponsor`);
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                if (confirm('Voulez-vous vraiment supprimer ce sponsor ?')) {
                    document.getElementById(`sponsor-${index}`).remove();
                    sponsorIndex--;
                    updateLimitMessage();
                }
            });
        }
    }
    
    if (addSponsorButton) {
        addSponsorButton.addEventListener('click', addSponsor);
    }
    
    document.querySelectorAll('.sponsor-item').forEach((sponsor, index) => {
        setupSponsorListeners(index);
    });
    
    updateLimitMessage();
    
    // Protection contre les doubles soumissions
    const sponsorForm = document.querySelector('form');
    let isSubmitting = false;
    
    if (sponsorForm) {
        sponsorForm.addEventListener('submit', function(e) {
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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
            }
            
            return true;
        });
    }
});
</script>
@endpush
