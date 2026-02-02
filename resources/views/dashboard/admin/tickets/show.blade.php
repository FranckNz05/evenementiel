@extends('layouts.dashboard')

@section('title', 'Détail du billet')

@push('styles')
<style>
:root {
    --admin-primary: #0f172a;
    --admin-muted: #6b7280;
    --admin-border: #e5e7eb;
    --admin-radius: 12px;
    --admin-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
}

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

.dashboard-head h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--admin-primary);
}

.dashboard-head p {
    margin: 0;
    color: var(--admin-muted);
    font-size: 0.9rem;
}

.dashboard-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.dashboard-btn {
    background: #fff;
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 0.5rem 1.25rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
    color: var(--admin-primary);
    transition: all 0.2s ease;
}

.dashboard-btn-primary {
    background: var(--admin-primary);
    color: #fff;
}

.dashboard-btn:hover {
    color: var(--admin-primary);
    border-color: var(--admin-primary);
}

.dashboard-btn-primary:hover {
    color: #fff;
    background: #111827;
    border-color: #111827;
}

.dashboard-card {
    background: #fff;
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    box-shadow: var(--admin-shadow);
    margin-bottom: 1.5rem;
}

.dashboard-card__header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--admin-border);
}

.dashboard-card__title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--admin-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dashboard-card__body {
    padding: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.info-item {
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1rem;
    background: #fff;
}

.info-item__label {
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 0.75rem;
    color: var(--admin-muted);
    margin-bottom: 0.25rem;
}

.info-item__value {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

.badge-success,
.badge-warning,
.badge-danger {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.75rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.badge-success { background: #d1fae5; color: #15803d; }
.badge-warning { background: #fef3c7; color: #b45309; }
.badge-danger  { background: #fee2e2; color: #b91c1c; }

.meta-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.meta-table tr {
    border-bottom: 1px solid var(--admin-border);
}

.meta-table td {
    padding: 0.75rem 0;
    vertical-align: top;
}

.meta-table td:first-child {
    width: 30%;
    color: var(--admin-muted);
    font-weight: 600;
}

@media (max-width: 767.98px) {
    .dashboard-section {
        padding: 1.5rem 1rem;
    }

    .dashboard-card__body {
        padding: 1.25rem;
    }
}
</style>
@endpush

@section('content')
@php
    $event = $ticket->event;
    $displayCode = $ticket->ticket_code ?: strtoupper('TCK-' . str_pad($ticket->id, 5, '0', STR_PAD_LEFT));
    $organizerName = optional(optional($event)->organizer)->company_name
        ?? optional(optional($event)->user)->full_name
        ?? optional(optional($event)->user)->name
        ?? optional(optional($event)->user)->email
        ?? 'Organisateur non défini';
    $clientName = optional($ticket->user)->full_name
        ?? optional($ticket->user)->name
        ?? optional($ticket->user)->email;
    $clientEmail = optional($ticket->user)->email;
    $ticketLabel = $ticket->nom ?? $ticket->ticket_type ?? 'Billet #' . $ticket->id;
    $validUntil = $ticket->valid_until ? $ticket->valid_until->format('d/m/Y H:i') : 'Non défini';
    $promotionPeriod = $ticket->promotion_start && $ticket->promotion_end
        ? $ticket->promotion_start->format('d/m/Y H:i') . ' - ' . $ticket->promotion_end->format('d/m/Y H:i')
        : null;
    $basePrice = $ticket->prix ?? $ticket->price ?? $ticket->original_price ?? 0;
    $promoAmount = $ticket->montant_promotionnel ?? $ticket->discount_amount ?? null;
    $currentPrice = $promoAmount ? max($basePrice - $promoAmount, 0) : $basePrice;
    $quantityTotal = $ticket->quantite ?? $ticket->quantity ?? 0;
    $quantitySoldRaw = $ticket->quantite_vendue ?? $ticket->quantity_sold ?? 0;
    $salesOverflow = $quantityTotal && $quantitySoldRaw > $quantityTotal;
    $quantitySold = $salesOverflow ? $quantityTotal : $quantitySoldRaw;
@endphp

<div class="dashboard-section">
    <div class="dashboard-head">
        <div>
            <h1>Détail du billet · {{ $ticketLabel }}</h1>
            <p>Code #{{ $displayCode }}</p>
        </div>
        <div class="dashboard-actions">
            <a href="{{ route('admin.tickets.index') }}" class="dashboard-btn">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
            <a href="{{ route('admin.tickets.edit', $ticket) }}" class="dashboard-btn dashboard-btn-primary">
                <i class="fas fa-edit"></i>
                Modifier
            </a>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card__header">
            <h2 class="dashboard-card__title">
                <i class="fas fa-ticket-alt"></i>
                Informations principales
            </h2>
        </div>
        <div class="dashboard-card__body">
            <div class="info-grid">
                <div class="info-item">
                    <p class="info-item__label">Événement</p>
                    <p class="info-item__value">{{ $event->title ?? 'Non défini' }}</p>
                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                        {{ $organizerName }}
                    </p>
                    @if(optional($event)->start_date)
                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">
                            {{ $event->start_date->format('d/m/Y H:i') }}
                            @if(optional($event)->end_date)
                                – {{ $event->end_date->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    @endif
                </div>
                @if($clientName || $clientEmail)
                    <div class="info-item">
                        <p class="info-item__label">Client</p>
                        <p class="info-item__value">
                            {{ $clientName ?? 'Non attribué' }}
                        </p>
                        @if($clientEmail)
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                {{ $clientEmail }}
                            </p>
                        @endif
                    </div>
                @endif
                <div class="info-item">
                    <p class="info-item__label">Statut</p>
                    <p class="info-item__value">
                        @if(!$ticket->is_used && $ticket->valid_until >= now())
                            <span class="badge-success">Valide</span>
                        @elseif($ticket->is_used)
                            <span class="badge-danger">Utilisé</span>
                        @else
                            <span class="badge-warning">Expiré</span>
                        @endif
                    </p>
                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                        Valide jusqu'au {{ $validUntil }}
                    </p>
                </div>
                <div class="info-item">
                    <p class="info-item__label">Type de billet</p>
                    <p class="info-item__value">{{ $ticketLabel }}</p>
                    @if($ticket->ticket_type && $ticket->nom)
                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">
                            Type interne : {{ $ticket->ticket_type }}
                        </p>
                    @endif
                    <p class="mb-0 text-muted" style="font-size: 0.8rem;">
                        Code : {{ $displayCode }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card__header">
            <h2 class="dashboard-card__title">
                <i class="fas fa-info-circle"></i>
                Détails complémentaires
            </h2>
        </div>
        <div class="dashboard-card__body">
            <table class="meta-table">
                <tr>
                    <td>Prix actuel</td>
                    <td>
                        <strong>{{ number_format($currentPrice, 2, ',', ' ') }} FCFA</strong>
                        @if($promoAmount)
                            <span class="text-muted ms-2" style="text-decoration: line-through;">
                                {{ number_format($basePrice, 2, ',', ' ') }} FCFA
                            </span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Promotion</td>
                    <td>
                        @if($promoAmount)
                            {{ number_format($promoAmount, 2, ',', ' ') }} FCFA
                            @if($promotionPeriod)
                                <small class="text-muted d-block">
                                    {{ $promotionPeriod }}
                                </small>
                            @endif
                        @else
                            <span class="text-muted">Aucune promotion active</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Quantité</td>
                    <td>
                        {{ $quantityTotal }} billets
                        <small class="text-muted ms-2">Vendus : {{ $quantitySoldRaw }}</small>
                        @if($salesOverflow)
                            <small class="text-danger d-block">
                                Les ventes dépassent le stock déclaré (affichage limité à {{ $quantityTotal }}).
                            </small>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td>{{ $ticket->description ?? 'Aucune description' }}</td>
                </tr>
                <tr>
                    <td>Date de création</td>
                    <td>{{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Dernière mise à jour</td>
                    <td>{{ $ticket->updated_at ? $ticket->updated_at->format('d/m/Y H:i') : 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

