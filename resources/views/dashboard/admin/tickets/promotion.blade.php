@extends('layouts.dashboard')

@section('title', 'Gestion des promotions - Ticket #' . $ticket->nom)

@php
    $storedPrice = $ticket->prix ?? $ticket->price ?? $ticket->original_price ?? 0;
    $discount = $ticket->montant_promotionnel ?? $ticket->discount_amount ?? 0;
    $currentPrice = $storedPrice;
    $originalPrice = $discount > 0 ? $storedPrice + $discount : $storedPrice;
    $discountPercent = $originalPrice > 0 && $discount > 0 ? ($discount / $originalPrice) * 100 : 0;
@endphp

@section('content')
<div class="dashboard-section">
    <div class="dashboard-head">
        <div>
            <h1>Promotion · {{ $ticket->nom ?? 'Billet #' . $ticket->id }}</h1>
            <p>Code {{ $ticket->ticket_code ?? strtoupper('TCK-' . str_pad($ticket->id, 5, '0', STR_PAD_LEFT)) }}</p>
        </div>
        <div class="dashboard-actions">
            <a href="{{ route('admin.tickets.show', $ticket) }}" class="dashboard-btn">
                <i class="fas fa-eye"></i>
                Voir le billet
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="dashboard-btn">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card__body">
            <div class="promotion-summary">
                <div class="summary-item">
                    <p class="label">Prix actuel</p>
                    <p class="value">{{ number_format($currentPrice, 0, ',', ' ') }} FCFA</p>
                    @if($discount > 0)
                        <p class="muted">
                            Prix initial : {{ number_format($originalPrice, 0, ',', ' ') }} FCFA
                            · {{ number_format($discountPercent, 1) }} %
                        </p>
                    @endif
                </div>
                <div class="summary-item">
                    <p class="label">Période</p>
                    @if($ticket->promotion_start || $ticket->promotion_end)
                        <p class="value">
                            {{ optional($ticket->promotion_start)->format('d/m/Y H:i') ?? '—' }}
                            —
                            {{ optional($ticket->promotion_end)->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    @else
                        <p class="value">Non définie</p>
                    @endif
                    <p class="muted">
                        Événement : {{ $ticket->event->title ?? 'Non défini' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card__header">
            <h2 class="dashboard-card__title">
                <i class="fas fa-edit"></i>
                {{ $discount > 0 ? 'Modifier la promotion' : 'Appliquer une promotion' }}
            </h2>
        </div>
        <div class="dashboard-card__body">
            <form action="{{ route('admin.tickets.apply-promotion', $ticket) }}" method="POST" class="promotion-form">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="montant_promotionnel">Montant de la remise (FCFA)</label>
                        <input type="number"
                               class="form-control @error('montant_promotionnel') is-invalid @enderror"
                               id="montant_promotionnel"
                               name="montant_promotionnel"
                               value="{{ old('montant_promotionnel', $discount) }}"
                               min="0"
                               step="100"
                               placeholder="0">
                        @error('montant_promotionnel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="promotion_start">Date de début</label>
                        <input type="datetime-local"
                               class="form-control @error('promotion_start') is-invalid @enderror"
                               id="promotion_start"
                               name="promotion_start"
                               value="{{ old('promotion_start', optional($ticket->promotion_start)->format('Y-m-d\TH:i')) }}">
                        @error('promotion_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="promotion_end">Date de fin</label>
                        <input type="datetime-local"
                               class="form-control @error('promotion_end') is-invalid @enderror"
                               id="promotion_end"
                               name="promotion_end"
                               value="{{ old('promotion_end', optional($ticket->promotion_end)->format('Y-m-d\TH:i')) }}">
                        @error('promotion_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description"
                              name="description"
                              rows="3"
                              placeholder="Détails ou conditions de la promotion">{{ old('description', $ticket->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="notify_user"
                               name="notify_user"
                               value="1"
                               {{ old('notify_user', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="notify_user">
                            Notifier le client par email
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="dashboard-btn dashboard-btn-primary">
                        <i class="fas fa-save"></i>
                        {{ $discount > 0 ? 'Mettre à jour' : 'Appliquer' }}
                    </button>

                    @if($discount > 0)
                        <button type="button"
                                class="dashboard-btn text-danger"
                                onclick="removePromotion()">
                            <i class="fas fa-trash"></i>
                            Supprimer la promotion
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulaire caché pour supprimer la promotion -->
<form id="removePromotionForm" action="{{ route('admin.tickets.remove-promotion', $ticket) }}" method="POST" style="display: none;">
    @csrf
</form>

@push('styles')
<style>
.dashboard-section {
    background: #f7f8fb;
    min-height: 100vh;
    padding: 2rem 1.5rem;
}

.dashboard-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.dashboard-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.dashboard-btn {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.5rem 1.25rem;
    font-weight: 600;
    text-decoration: none;
    color: #0f172a;
    background: #fff;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.2s ease;
}

.dashboard-btn-primary {
    background: #0f172a;
    color: #fff;
}

.dashboard-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
    margin-bottom: 1.25rem;
}

.dashboard-card__header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.dashboard-card__title {
    font-size: 1.05rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dashboard-card__body {
    padding: 1.5rem;
}

.promotion-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.summary-item {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
}

.summary-item .label {
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.3rem;
}

.summary-item .value {
    font-size: 1.15rem;
    font-weight: 600;
    color: #111827;
}

.summary-item .muted {
    margin: 0;
    color: #6b7280;
    font-size: 0.85rem;
}

.promotion-form .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
}

@media (max-width: 767.98px) {
    .dashboard-section {
        padding: 1.5rem 1rem;
    }

    .promotion-summary {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
function removePromotion() {
    if (confirm('Supprimer cette promotion ?')) {
        document.getElementById('removePromotionForm').submit();
    }
}
</script>
@endpush
@endsection