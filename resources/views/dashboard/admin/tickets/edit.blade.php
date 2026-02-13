@extends('layouts.dashboard')

@section('title', 'Modifier un ticket')

@push('styles')
<style>
:root {
    --primary: #0f1a3d;
    --primary-light: #1a237e;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
}

.ticket-page {
    min-height: 100vh;
    background: var(--gray-50);
    padding: 2rem;
}

/* Header - Section bleue */
.page-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 0.75rem;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.15);
}

.page-title-section h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-title-section p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.page-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-light);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 26, 61, 0.2);
}

.btn-secondary {
    background: white;
    color: var(--primary);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.95);
    border-color: rgba(255, 255, 255, 0.5);
    color: var(--primary-light);
}

.btn-outline-secondary {
    background: transparent;
    border: 1px solid var(--gray-300);
    color: var(--gray-700);
}

.btn-outline-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
    color: var(--gray-900);
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #059669;
}

/* Card */
.card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-title i {
    color: var(--primary);
}

.card-body {
    padding: 1.5rem;
}

/* Form */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-label i {
    margin-right: 0.375rem;
    color: var(--primary);
}

.form-control,
.form-select {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 1px solid var(--gray-300);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    background: white;
}

.form-control:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.form-control.is-invalid,
.form-select.is-invalid {
    border-color: var(--danger);
}

.invalid-feedback {
    display: block;
    color: var(--danger);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.form-text {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

/* Form Check (Switch) */
.form-check {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.form-switch {
    padding-left: 0;
}

.form-check-input {
    width: 2.5rem;
    height: 1.25rem;
    background-color: var(--gray-300);
    border-radius: 2rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    margin: 0;
}

.form-check-input:checked {
    background-color: var(--primary);
}

.form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.form-check-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-700);
    cursor: pointer;
}

/* Row and Col */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -0.75rem;
}

.col,
.col-12,
.col-md-4,
.col-md-6 {
    padding: 0 0.75rem;
    box-sizing: border-box;
}

.col-12 {
    width: 100%;
}

.col-md-6 {
    width: 50%;
}

.col-md-4 {
    width: 33.333%;
}

@media (max-width: 768px) {
    .col-md-6,
    .col-md-4 {
        width: 100%;
    }
}

/* Utilities */
.mb-3 {
    margin-bottom: 1rem;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.me-1 {
    margin-right: 0.25rem;
}

.me-2 {
    margin-right: 0.5rem;
}

.ms-auto {
    margin-left: auto;
}

.p-3 {
    padding: 1rem;
}

.d-flex {
    display: flex;
}

.align-items-center {
    align-items: center;
}

.justify-content-between {
    justify-content: space-between;
}

.justify-content-end {
    justify-content: flex-end;
}

.flex-wrap {
    flex-wrap: wrap;
}

.w-100 {
    width: 100%;
}

/* Gap */
.gap-2 {
    gap: 0.5rem;
}

.gap-3 {
    gap: 1rem;
}

/* Border */
.border-top {
    border-top: 1px solid var(--gray-200);
}

.mt-4 {
    margin-top: 1.5rem;
}

.mt-5 {
    margin-top: 2rem;
}

.pt-3 {
    padding-top: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .ticket-page {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .d-flex.justify-content-end {
        flex-direction: column;
    }
    
    .d-flex.justify-content-end .btn {
        width: 100%;
        justify-content: center;
    }
}

/* Readonly and disabled states */
.form-control[readonly] {
    background-color: var(--gray-100);
    cursor: not-allowed;
}

/* Currency symbol */
.input-group {
    display: flex;
    width: 100%;
}

.input-group-text {
    display: flex;
    align-items: center;
    padding: 0.625rem 0.875rem;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-radius: 0 0.5rem 0.5rem 0;
    color: var(--gray-700);
    font-size: 0.875rem;
    font-weight: 600;
    border-left: none;
}

.input-group .form-control {
    border-radius: 0.5rem 0 0 0.5rem;
}
</style>
@endpush

@section('content')
<div class="ticket-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>
                <i class="fas fa-ticket-alt"></i>
                Modifier un ticket
            </h1>
            <p>Modifiez les informations du ticket "{{ $ticket->nom ?? $ticket->name ?? 'Sans nom' }}"</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-edit"></i>
                Informations du ticket
            </h5>
            @if($ticket->montant_promotionnel)
            <span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                <i class="fas fa-tag me-1"></i>
                Promotion active: -{{ number_format($ticket->montant_promotionnel, 0, ',', ' ') }} FCFA
            </span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Événement et Nom -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="event_id" class="form-label">
                                <i class="fas fa-calendar-alt"></i>
                                Événement associé
                            </label>
                            <select class="form-select @error('event_id') is-invalid @enderror" 
                                    id="event_id" 
                                    name="event_id" 
                                    required>
                                <option value="">Sélectionner un événement</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" 
                                        {{ old('event_id', $ticket->event_id) == $event->id ? 'selected' : '' }}>
                                        {{ $event->title ?? $event->name ?? 'Sans titre' }}
                                        @if($event->start_date)
                                            ({{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('event_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                L'événement auquel ce ticket est associé.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nom" class="form-label">
                                <i class="fas fa-tag"></i>
                                Nom du ticket
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom"
                                   value="{{ old('nom', $ticket->nom ?? $ticket->name) }}" 
                                   placeholder="Ex: Standard, VIP, Early Bird..."
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Nom du ticket tel qu'affiché aux utilisateurs.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group mb-4">
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left"></i>
                        Description
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description"
                              name="description" 
                              rows="3"
                              placeholder="Décrivez les avantages et conditions de ce ticket...">{{ old('description', $ticket->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Optionnel. Description détaillée du ticket.
                    </div>
                </div>

                <!-- Prix, Quantité, Statut -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="prix" class="form-label">
                                <i class="fas fa-coins"></i>
                                Prix
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       step="100" 
                                       min="0"
                                       class="form-control @error('prix') is-invalid @enderror"
                                       id="prix" 
                                       name="prix" 
                                       value="{{ old('prix', $ticket->prix ?? $ticket->price) }}" 
                                       required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                            @error('prix')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Prix unitaire du ticket.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="quantite" class="form-label">
                                <i class="fas fa-cubes"></i>
                                Quantité disponible
                            </label>
                            <input type="number" 
                                   min="1"
                                   step="1"
                                   class="form-control @error('quantite') is-invalid @enderror"
                                   id="quantite" 
                                   name="quantite" 
                                   value="{{ old('quantite', $ticket->quantite ?? $ticket->quantity) }}" 
                                   required>
                            @error('quantite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Nombre total de tickets disponibles.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="statut" class="form-label">
                                <i class="fas fa-circle"></i>
                                Statut
                            </label>
                            <select class="form-select @error('statut') is-invalid @enderror" 
                                    id="statut" 
                                    name="statut" 
                                    required>
                                <option value="disponible" {{ old('statut', $ticket->statut) == 'disponible' ? 'selected' : '' }}>
                                    <i class="fas fa-check-circle"></i> Disponible
                                </option>
                                <option value="épuisé" {{ old('statut', $ticket->statut) == 'épuisé' ? 'selected' : '' }}>
                                    <i class="fas fa-times-circle"></i> Épuisé
                                </option>
                                <option value="archivé" {{ old('statut', $ticket->statut) == 'archivé' ? 'selected' : '' }}>
                                    <i class="fas fa-archive"></i> Archivé
                                </option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Statut actuel du ticket.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Options de réservation -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="form-group h-100 d-flex flex-column justify-content-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="reservable" 
                                       name="reservable"
                                       value="1" 
                                       {{ old('reservable', $ticket->reservable ?? $ticket->is_reservable) ? 'checked' : '' }}>
                                <label class="form-check-label" for="reservable">
                                    <i class="fas fa-calendar-check"></i>
                                    Réservable
                                </label>
                            </div>
                            <div class="form-text" style="margin-left: 2.5rem;">
                                Permet aux utilisateurs de réserver ce ticket sans paiement immédiat.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reservation_deadline" class="form-label">
                                <i class="fas fa-hourglass-half"></i>
                                Délai de réservation
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       min="0"
                                       step="1"
                                       class="form-control @error('reservation_deadline') is-invalid @enderror"
                                       id="reservation_deadline" 
                                       name="reservation_deadline"
                                       value="{{ old('reservation_deadline', $ticket->reservation_deadline) }}"
                                       placeholder="0">
                                <span class="input-group-text">jours</span>
                            </div>
                            @error('reservation_deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Nombre de jours avant l'événement pour confirmer la réservation. 0 = désactivé.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations sur les ventes (lecture seule) -->
                @if($ticket->quantite_vendue > 0)
                <div class="alert alert-info mb-4" style="background: #dbeafe; border-left-color: #2563eb; color: #1e40af;">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-info-circle" style="font-size: 1.25rem;"></i>
                        <div>
                            <strong>Informations sur les ventes</strong><br>
                            <span>{{ $ticket->quantite_vendue }} ticket(s) déjà vendu(s) sur {{ $ticket->quantite ?? $ticket->quantity }} disponibles.</span>
                            @if(($ticket->quantite ?? $ticket->quantity) > $ticket->quantite_vendue)
                                <span> Il reste {{ ($ticket->quantite ?? $ticket->quantity) - $ticket->quantite_vendue }} ticket(s) disponibles.</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="d-flex gap-2 justify-content-end mt-5 pt-3 border-top">
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Section Promotion (si applicable) -->
    @if($ticket->montant_promotionnel)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-tags"></i>
                Promotion active
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div style="background: #fef3c7; padding: 0.75rem; border-radius: 0.5rem;">
                            <i class="fas fa-tag" style="color: #f59e0b; font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--gray-900); margin-bottom: 0.25rem;">
                                Réduction de {{ number_format($ticket->montant_promotionnel, 0, ',', ' ') }} FCFA
                            </div>
                            <div style="font-size: 0.875rem; color: var(--gray-600);">
                                Prix original: <span style="text-decoration: line-through;">{{ number_format($ticket->prix + $ticket->montant_promotionnel, 0, ',', ' ') }} FCFA</span>
                                &nbsp;→&nbsp; 
                                <span style="font-weight: 700; color: var(--success);">{{ number_format($ticket->prix, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @if($ticket->promotion_start || $ticket->promotion_end)
                            <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                                <i class="fas fa-calendar-alt me-1"></i>
                                @if($ticket->promotion_start && $ticket->promotion_end)
                                    Du {{ \Carbon\Carbon::parse($ticket->promotion_start)->format('d/m/Y') }} 
                                    au {{ \Carbon\Carbon::parse($ticket->promotion_end)->format('d/m/Y') }}
                                @elseif($ticket->promotion_start)
                                    À partir du {{ \Carbon\Carbon::parse($ticket->promotion_start)->format('d/m/Y') }}
                                @elseif($ticket->promotion_end)
                                    Jusqu'au {{ \Carbon\Carbon::parse($ticket->promotion_end)->format('d/m/Y') }}
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-md-end mt-3 mt-md-0">
                    <form action="{{ route('admin.tickets.promotion.remove', $ticket) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?')">
                            <i class="fas fa-times me-1"></i>
                            Supprimer la promotion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===========================================
    // VALIDATION DU PRIX ET DE LA QUANTITÉ
    // ===========================================
    const prixInput = document.getElementById('prix');
    const quantiteInput = document.getElementById('quantite');
    const reservationDeadline = document.getElementById('reservation_deadline');

    if (prixInput) {
        prixInput.addEventListener('input', function() {
            let value = parseFloat(this.value);
            if (isNaN(value) || value < 0) {
                this.value = 0;
            }
        });
    }

    if (quantiteInput) {
        quantiteInput.addEventListener('input', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            }
        });
    }

    if (reservationDeadline) {
        reservationDeadline.addEventListener('input', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 0) {
                this.value = 0;
            }
        });
    }

    // ===========================================
    // CONFIRMATION POUR ANNULATION
    // ===========================================
    const cancelBtn = document.querySelector('a.btn-outline-secondary');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input:not([type="checkbox"]), select, textarea');
            let isDirty = false;
            
            inputs.forEach(input => {
                if (input.value !== input.defaultValue) {
                    isDirty = true;
                }
            });
            
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked !== checkbox.defaultChecked) {
                    isDirty = true;
                }
            });
            
            if (isDirty) {
                if (!confirm('Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter ?')) {
                    e.preventDefault();
                }
            }
        });
    }

    // ===========================================
    // AUTO-FERMETURE DES ALERTES
    // ===========================================
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (!alert.classList.contains('alert-permanent')) {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 300);
            }
        });
    }, 5000);
});
</script>
@endpush