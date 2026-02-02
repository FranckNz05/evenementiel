@extends('layouts.dashboard')

@section('content')
<div class="container-fluid dashboard-container">
    {{-- En-tête --}}
    <x-page-header 
        title="Création d'événement" 
        icon="fas fa-ticket-alt"
        subtitle="Étape 3/4 - Configuration des billets">
    </x-page-header>

    <!-- Barre de progression -->
    <div class="progress mb-4" style="height: 10px; border-radius: var(--radius-md); background: var(--gray-200); box-shadow: var(--shadow-sm);">
        <div class="progress-bar" role="progressbar" style="width: 75%; background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-light));" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    @if($errors->any())
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
    
    <x-content-section title="Billets de l'événement" icon="fas fa-ticket">
        <form action="{{ route('events.wizard.post.step3') }}" method="POST" id="ticketsForm">
            @csrf
            <input type="hidden" name="event_status" value="{{ $event['status'] ?? 'Payant' }}">
            <input type="hidden" name="event_start_date" value="{{ $event['start_date'] ?? '' }}">
            
            @if(($event['status'] ?? 'Payant') === 'Gratuit')
                <div class="modern-alert alert-info-modern mb-4">
                    <i class="fas fa-info-circle"></i>
                    <span>Cet événement est gratuit. Aucun billet n'est nécessaire.</span>
                </div>
            @else
                <div class="modern-alert alert-info-modern mb-4">
                    <i class="fas fa-info-circle"></i>
                    <span>Configurez les différents types de billets pour votre événement.</span>
                </div>
                
                <div id="tickets-container">
                    @if(!empty($tickets) && count($tickets) > 0)
                        @foreach($tickets as $index => $ticket)
                            <div class="ticket-item p-3 mb-3 rounded" style="background: var(--gray-100); border: 2px solid var(--bleu-nuit);" id="ticket-{{ $index }}">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 2px solid var(--blanc-or);">
                                    <h5 class="mb-0" style="color: var(--bleu-nuit); font-weight: 700;">
                                        <i class="fas fa-ticket me-2" style="color: var(--blanc-or);"></i>
                                        Billet #{{ $index + 1 }}
                                    </h5>
                                    <button type="button" class="modern-btn btn-sm-modern btn-danger-modern remove-ticket" data-index="{{ $index }}">
                                        <i class="fas fa-times"></i>
                                        Supprimer
                                    </button>
                                </div>
                                <div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label-modern">
                                                <i class="fas fa-tag"></i>
                                                Nom du billet <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-input-modern @error("tickets.$index.nom") is-invalid @enderror" 
                                                   name="tickets[{{ $index }}][nom]" 
                                                   value="{{ old("tickets.$index.nom", $ticket['nom'] ?? '') }}" 
                                                   required>
                                            @error("tickets.$index.nom")
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-modern">
                                                <i class="fas fa-money-bill"></i>
                                                Prix ({{ config('settings.global.currency') }}) <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-input-modern @error("tickets.$index.prix") is-invalid @enderror" 
                                                   name="tickets[{{ $index }}][prix]" 
                                                   value="{{ old("tickets.$index.prix", $ticket['prix'] ?? '') }}" 
                                                   min="0" step="0.01" required>
                                            @error("tickets.$index.prix")
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label-modern">
                                                <i class="fas fa-sort-numeric-up"></i>
                                                Quantité <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-input-modern @error("tickets.$index.quantite") is-invalid @enderror" 
                                                   name="tickets[{{ $index }}][quantite]" 
                                                   value="{{ old("tickets.$index.quantite", $ticket['quantite'] ?? '') }}" 
                                                   min="1" required>
                                            @error("tickets.$index.quantite")
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-modern">
                                            <i class="fas fa-align-left"></i>
                                            Description
                                        </label>
                                        <textarea class="form-textarea-modern" name="tickets[{{ $index }}][description]" rows="2">{{ old("tickets.$index.description", $ticket['description'] ?? '') }}</textarea>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label-modern">
                                                <i class="fas fa-percent"></i>
                                                Prix promotionnel
                                            </label>
                                            <input type="number" class="form-input-modern @error("tickets.$index.montant_promotionnel") is-invalid @enderror" 
                                                   name="tickets[{{ $index }}][montant_promotionnel]" 
                                                   value="{{ old("tickets.$index.montant_promotionnel", $ticket['montant_promotionnel'] ?? '') }}" 
                                                   min="0" step="0.01">
                                            @error("tickets.$index.montant_promotionnel")
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-modern">
                                                <i class="fas fa-calendar-check"></i>
                                                Début promotion
                                            </label>
                                            <input type="datetime-local" class="form-input-modern @error("tickets.$index.promotion_start") is-invalid @enderror" 
                                                   name="tickets[{{ $index }}][promotion_start]" 
                                                   value="{{ old("tickets.$index.promotion_start", $ticket['promotion_start'] ?? '') }}">
                                            @error("tickets.$index.promotion_start")
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-modern">
                                                <i class="fas fa-calendar-times"></i>
                                                Fin promotion
                                            </label>
                                            <input type="datetime-local" class="form-input-modern @error("tickets.$index.promotion_end") is-invalid @enderror" 
                                                   name="tickets[{{ $index }}][promotion_end]" 
                                                   value="{{ old("tickets.$index.promotion_end", $ticket['promotion_end'] ?? '') }}">
                                            @error("tickets.$index.promotion_end")
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" 
                                               name="tickets[{{ $index }}][reservable]" 
                                               id="reservable-{{ $index }}" 
                                               {{ old("tickets.$index.reservable", $ticket['reservable'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="reservable-{{ $index }}">
                                            <i class="fas fa-bookmark me-1"></i>
                                            Permettre la réservation (sans paiement immédiat)
                                        </label>
                                    </div>
                                    <div class="mb-3 reservation-deadline-container" 
                                         style="{{ old("tickets.$index.reservable", $ticket['reservable'] ?? false) ? '' : 'display: none;' }}">
                                        <label class="form-label-modern">
                                            <i class="fas fa-clock"></i>
                                            Date limite de réservation
                                        </label>
                                        <input type="datetime-local" class="form-input-modern @error("tickets.$index.reservation_deadline") is-invalid @enderror" 
                                               name="tickets[{{ $index }}][reservation_deadline]" 
                                               value="{{ old("tickets.$index.reservation_deadline", $ticket['reservation_deadline'] ?? '') }}">
                                        @error("tickets.$index.reservation_deadline")
                                            <div class="invalid-feedback-modern">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text-modern">Après cette date, les réservations non confirmées seront annulées.</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                
                <button type="button" id="add-ticket" class="modern-btn btn-secondary-modern mb-4">
                    <i class="fas fa-plus-circle"></i>
                    Ajouter un type de billet
                </button>
                <div id="ticket-limit-message" class="limit-message" style="color: var(--danger); font-size: 0.9rem; margin-top: 5px; display: none;">
                    Limite de 5 billets atteinte
                </div>
            @endif
            
            <div class="d-flex justify-content-between mt-4 pt-3" style="border-top: 2px solid var(--blanc-or);">
                <a href="{{ route('events.wizard.step2') }}" class="modern-btn btn-secondary-modern">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ticketsContainer = document.getElementById('tickets-container');
    const addTicketButton = document.getElementById('add-ticket');
    let ticketIndex = {{ !empty($tickets) ? count($tickets) : 0 }};
    
    function addTicket() {
        if (ticketIndex >= 5) {
            alert('Vous ne pouvez pas ajouter plus de 5 types de billets.');
            return;
        }

        const ticketHtml = `
            <div class="ticket-item p-3 mb-3 rounded" style="background: var(--gray-100); border: 2px solid var(--bleu-nuit);" id="ticket-${ticketIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 2px solid var(--blanc-or);">
                    <h5 class="mb-0" style="color: var(--bleu-nuit); font-weight: 700;">
                        <i class="fas fa-ticket me-2" style="color: var(--blanc-or);"></i>
                        Billet #${ticketIndex + 1}
                    </h5>
                    <button type="button" class="modern-btn btn-sm-modern btn-danger-modern remove-ticket" data-index="${ticketIndex}">
                        <i class="fas fa-times"></i> Supprimer
                    </button>
                </div>
                <div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-modern"><i class="fas fa-tag"></i> Nom du billet <span class="text-danger">*</span></label>
                            <input type="text" class="form-input-modern" name="tickets[${ticketIndex}][nom]" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-modern"><i class="fas fa-money-bill"></i> Prix ({{ config('settings.global.currency') }}) <span class="text-danger">*</span></label>
                            <input type="number" class="form-input-modern" name="tickets[${ticketIndex}][prix]" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-modern"><i class="fas fa-sort-numeric-up"></i> Quantité <span class="text-danger">*</span></label>
                            <input type="number" class="form-input-modern" name="tickets[${ticketIndex}][quantite]" min="1" value="100" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-modern"><i class="fas fa-align-left"></i> Description</label>
                        <textarea class="form-textarea-modern" name="tickets[${ticketIndex}][description]" rows="2"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label-modern"><i class="fas fa-percent"></i> Prix promotionnel</label>
                            <input type="number" class="form-input-modern" name="tickets[${ticketIndex}][montant_promotionnel]" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-modern"><i class="fas fa-calendar-check"></i> Début promotion</label>
                            <input type="datetime-local" class="form-input-modern" name="tickets[${ticketIndex}][promotion_start]">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-modern"><i class="fas fa-calendar-times"></i> Fin promotion</label>
                            <input type="datetime-local" class="form-input-modern" name="tickets[${ticketIndex}][promotion_end]">
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="tickets[${ticketIndex}][reservable]" id="reservable-${ticketIndex}">
                        <label class="form-check-label" for="reservable-${ticketIndex}">
                            <i class="fas fa-bookmark me-1"></i> Permettre la réservation (sans paiement immédiat)
                        </label>
                    </div>
                    <div class="mb-3 reservation-deadline-container" style="display: none;">
                        <label class="form-label-modern"><i class="fas fa-clock"></i> Date limite de réservation</label>
                        <input type="datetime-local" class="form-input-modern" name="tickets[${ticketIndex}][reservation_deadline]">
                        <small class="form-text-modern">Après cette date, les réservations non confirmées seront annulées.</small>
                    </div>
                </div>
            </div>
        `;
        
        ticketsContainer.insertAdjacentHTML('beforeend', ticketHtml);
        setupTicketListeners(ticketIndex);
        ticketIndex++;

        if (ticketIndex >= 5) {
            addTicketButton.disabled = true;
            addTicketButton.classList.add('disabled');
            document.getElementById('ticket-limit-message').style.display = 'block';
        }
    }
    
    function setupTicketListeners(index) {
        const removeButton = document.querySelector(`#ticket-${index} .remove-ticket`);
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                if (confirm('Voulez-vous vraiment supprimer ce billet ?')) {
                    document.getElementById(`ticket-${index}`).remove();
                    ticketIndex--;
                    
                    if (ticketIndex < 5) {
                        addTicketButton.disabled = false;
                        addTicketButton.classList.remove('disabled');
                        document.getElementById('ticket-limit-message').style.display = 'none';
                    }
                }
            });
        }
        
        const reservableCheckbox = document.getElementById(`reservable-${index}`);
        if (reservableCheckbox) {
            reservableCheckbox.addEventListener('change', function() {
                const ticketItem = this.closest('.ticket-item');
                const deadlineContainer = ticketItem.querySelector('.reservation-deadline-container');
                deadlineContainer.style.display = this.checked ? 'block' : 'none';
            });
        }
    }
    
    if (addTicketButton) {
        addTicketButton.addEventListener('click', addTicket);
    }
    
    document.querySelectorAll('.ticket-item').forEach((ticket, index) => {
        setupTicketListeners(index);
    });
    
    const ticketsForm = document.getElementById('ticketsForm');
    let isSubmitting = false;
    
    ticketsForm?.addEventListener('submit', function(e) {
        // Empêcher les doubles soumissions
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        
        const eventStatus = document.querySelector('input[name="event_status"]').value;
        
        if (eventStatus === 'Payant') {
            const ticketItems = document.querySelectorAll('.ticket-item');
            
            if (ticketItems.length === 0) {
                e.preventDefault();
                alert('Veuillez ajouter au moins un type de billet pour cet événement payant.');
                return false;
            }
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
});
</script>
@endpush
