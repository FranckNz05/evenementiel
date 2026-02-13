@extends('layouts.dashboard')

@section('title', 'Mon compte')

@section('content')
<div class="container-xxl py-5" style="margin-top: 4rem;">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar - Profil utilisateur -->
            <div class="col-lg-4">
                <!-- Carte Profil -->
                <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                    <div class="card-header bg-gradient-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Mon Profil</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="position-relative mb-3 mx-auto" style="width: fit-content;">
                            @if(Auth::user()->profile_photo_path)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                     alt="{{ Auth::user()->name }}"
                                     class="rounded-circle shadow"
                                     style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #fff;">
                            @else
                                <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center shadow"
                                     style="width: 120px; height: 120px;">
                                    <span class="text-white display-6 fw-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <h4 class="h5 mb-1">{{ Auth::user()->name }}</h4>
                        <p class="text-muted mb-3">
                            <i class="fas fa-envelope me-1"></i> {{ Auth::user()->email }}
                        </p>

                        <div class="d-grid gap-2">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary rounded-pill">
                                <i class="fas fa-user me-1"></i> Mon compte
                            </a>
                            @if(!Auth::user()->hasRole(2))
                                <a href="{{ route('organizer.request.create') }}" class="btn btn-outline-primary rounded-pill">
                                    <i class="fas fa-plus-circle me-1"></i> Devenir organisateur
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-light-primary py-3">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Mes Statistiques</h5>
                    </div>
                    <div class="card-body-modern">
                        <div class="stat-card bg-primary-soft mb-3 p-3 rounded-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Billets achetés</h6>
                                    <span class="text-muted small">{{ $stats['tickets_count'] }} billets</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card bg-success-soft mb-3 p-3 rounded-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-success text-white me-3">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Événements participés</h6>
                                    <span class="text-muted small">{{ $stats['events_attended'] }} événements</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card bg-warning-soft p-3 rounded-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Total dépensé</h6>
                                    <span class="text-muted small">{{ number_format($stats['total_spent'], 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="col-lg-8">
                <!-- Événements à venir -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-light-primary py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i>Mes prochains événements</h5>
                            <a href="{{ route('profile.tickets') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                Voir tous <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @forelse($upcomingEvents as $event)
                            <div class="border-bottom p-3 d-flex align-items-center hover-highlight">
                                <div class="flex-shrink-0">
                                    @if($event->image)
                                        <img src="{{ asset('storage/' . $event->image) }}"
                                             alt="{{ $event->title }}"
                                             class="rounded-3 shadow-sm"
                                             style="width: 64px; height: 64px; object-fit: cover;">
                                    @else
                                        <div class="rounded-3 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center"
                                             style="width: 64px; height: 64px;">
                                            <i class="fas fa-calendar-day text-primary"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ Str::limit($event->title, 40) }}</h6>
                                    <div class="d-flex flex-wrap gap-3 small">
                                        <span class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i> {{ $event->ville }}
                                        </span>
                                        <span class="text-muted">
                                            <i class="fas fa-clock me-1"></i> {{ $event->start_date->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-shrink-0">
                                    <a href="{{ route('events.show', $event) }}"
                                       class="btn btn-sm btn-icon btn-primary rounded-circle"
                                       data-bs-toggle="tooltip"
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-calendar-plus fa-3x text-muted opacity-50"></i>
                                </div>
                                <h5 class="h6 text-muted mb-3">Aucun événement à venir</h5>
                                <a href="{{ route('events.index') }}" class="btn btn-primary rounded-pill px-4">
                                    <i class="fas fa-search me-1"></i> Découvrir des événements
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- reservations récentes (5 dernières) -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-light-primary py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Mes 5 dernières reservations</h5>
                            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                Voir historique complet <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @forelse($orders->take(5) as $order)
                            <div class="border-bottom p-3 hover-highlight">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 text-primary">#{{ $order->matricule }}</h6>
                                    <span class="badge rounded-pill py-2 px-3 bg-{{ $order->isPending() ? 'warning' : ($order->isPaid() ? 'success' : 'danger') }}-soft text-{{ $order->isPending() ? 'warning' : ($order->isPaid() ? 'success' : 'danger') }}">
                                        <i class="fas {{ $order->isPending() ? 'fa-clock' : ($order->isPaid() ? 'fa-check-circle' : 'fa-times-circle') }} me-1"></i>
                                        {{ $order->isPending() ? 'En attente' : ($order->isPaid() ? 'Payée' : 'Annulée') }}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="small text-muted mb-1">
                                            <i class="fas fa-calendar-day me-1"></i>
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </p>
                                        <p class="small mb-0">
                                            <i class="fas fa-ticket-alt me-1"></i>
                                            {{ $order->tickets_count }} billet(s) - {{ $order->event ? $order->event->title : 'Événement non disponible' }}
                                        </p>
                                    </div>

                                    <div class="text-end">
                                        <h6 class="mb-0 fw-bold">
                                            @php
                                                // Calculer le montant total
                                                $montantTotal = 0;
                                                if ($order->montant_total > 0) {
                                                    $montantTotal = $order->montant_total;
                                                } elseif ($order->payment) {
                                                    $montantTotal = $order->payment->montant;
                                                } else {
                                                    // Calculer à partir des tickets
                                                    foreach ($order->tickets as $ticket) {
                                                        $montantTotal += $ticket->pivot->total_amount;
                                                    }
                                                }
                                            @endphp
                                            {{ number_format($montantTotal, 0, ',', ' ') }} FCFA
                                        </h6>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('orders.show', $order) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill"
                                               data-bs-toggle="tooltip"
                                               title="Détails">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($order->isPending())
                                                <a href="{{ route('payments.process', $order) }}"
                                                   class="btn btn-sm btn-success rounded-pill"
                                                   data-bs-toggle="tooltip"
                                                   title="Payer maintenant">
                                                    <i class="fas fa-credit-card me-1"></i> Payer
                                                </a>
                                            @endif

                                            @if($order->isPaid())
                                                <a href="{{ route('orders.tickets-pdf', $order) }}"
                                                   class="btn btn-sm btn-outline-secondary rounded-pill"
                                                   data-bs-toggle="tooltip"
                                                   title="Télécharger les billets">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-shopping-basket fa-3x text-muted opacity-50"></i>
                                </div>
                                <h5 class="h6 text-muted mb-3">Aucune reservation récente</h5>
                                <a href="{{ route('events.index') }}" class="btn btn-primary rounded-pill px-4">
                                    <i class="fas fa-ticket-alt me-1"></i> Acheter un billet
                                </a>
                            </div>
                        @endforelse
                    </div>

                    @if($orders->count() > 5)
                    <div class="card-footer bg-light border-0 py-3 text-center">
                        <a href="{{ route('orders.index') }}" class="btn btn-link">
                            Voir toutes mes reservations ({{ $orders->count() }}) <i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>

/* Variables CSS pour design cohérent */
/* ===============================================
   SYSTÈME DE DESIGN - BLEU NUIT & blanc OR
   Design Premium avec Excellence UX/UI
   =============================================== */

:root {
    /* Palette Principale - Bleu MokiliEvent & blanc Or */
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --bleu-nuit-lighter: #2c3e8f;
    --bleu-royal: #3b4f9a;
    --bleu-soft: #5a6ba8;
    --bleu-light: #7c8db8;
    
    --blanc-or: #ffd700;
    --blanc-or-light: #ffe44d;
    --blanc-or-dark: #e6c200;
    --blanc-amber: #ffb700;
    --blanc-warm: #ffdb58;
    
    /* Couleurs d'état */
    --success: #10b981;
    --success-bg: #d1fae5;
    --warning: #ffd700;
    --warning-bg: #fff8dc;
    --danger: #ef4444;
    --danger-bg: #fee2e2;
    --info: #3b82f6;
    --info-bg: #dbeafe;
    
    /* Nuances neutres */
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    
    /* Ombres et effets */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --shadow-gold: 0 10px 25px -5px rgba(255, 215, 0, 0.3);
    --shadow-blue: 0 10px 25px -5px rgba(15, 26, 61, 0.3);
    
    --border-radius: 0.75rem;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Container principal */
.container-fluid {
    padding: 1rem;
    max-width: 1600px;
    margin: 0 auto;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    min-height: 100vh;
    position: relative;
}

.container-fluid::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--bleu-nuit), var(--blanc-or), var(--bleu-nuit-clair));
    z-index: 1;
}

/* En-tête de page */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    border-radius: var(--border-radius);
    color: white;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: var(--blanc-or);
    opacity: 0.1;
    border-radius: 50%;
    transform: rotate(45deg);
}

.page-title {
    font-size: 1.875rem;
    font-weight: 700;
    margin: 0;
    position: relative;
    z-index: 2;
}

.page-actions {
    display: flex;
    gap: 1rem;
    position: relative;
    z-index: 2;
}

/* Cartes modernes */
.modern-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    border: none;
    overflow: hidden;
    transition: var(--transition);
}

.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

.card-header-modern {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    border-bottom: 3px solid var(--blanc-or);
    padding: 1.5rem;
    color: white;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-body-modern {
    padding: 1.5rem;
}

/* Tableaux modernes */
.modern-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.modern-table thead th {
    background: linear-gradient(135deg, var(--bleu-nuit-light), var(--bleu-nuit));
    color: var(--blanc-or);
    padding: 1rem;
    text-align: left;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}

.modern-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: var(--transition);
}

.modern-table tbody tr:hover {
    background: var(--gray-50);
    transform: scale(1.01);
}

.modern-table td {
    padding: 1rem;
    vertical-align: middle;
}

/* Boutons modernes */
.modern-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-primary-modern {
    background: linear-gradient(135deg, var(--bleu-nuit), var(--bleu-nuit-clair));
    color: var(--blanc-or);
}

.btn-primary-modern:hover {
    background: linear-gradient(135deg, var(--bleu-nuit-clair), var(--bleu-nuit-lighter));
    transform: translateY(-2px);
    box-shadow: var(--shadow-blue);
}

.btn-success-modern {
    background: linear-gradient(135deg, var(--success), #059669);
    color: white;
}

.btn-success-modern:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-warning-modern {
    background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-dark));
    color: var(--bleu-nuit);
}

.btn-warning-modern:hover {
    background: linear-gradient(135deg, var(--blanc-or-dark), var(--blanc-amber));
    transform: translateY(-2px);
    box-shadow: var(--shadow-gold);
}

.btn-danger-modern {
    background: linear-gradient(135deg, var(--danger), #dc2626);
    color: white;
}

.btn-danger-modern:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-secondary-modern {
    background: linear-gradient(135deg, var(--gray-500), var(--gray-600));
    color: white;
}

.btn-secondary-modern:hover {
    background: linear-gradient(135deg, var(--gray-600), var(--gray-700));
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-sm-modern {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

/* Badges modernes */
.modern-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background: var(--success-bg);
    color: var(--success);
}

.badge-warning {
    background: var(--warning-bg);
    color: var(--blanc-or);
}

.badge-danger {
    background: var(--danger-bg);
    color: var(--danger);
}

.badge-info {
    background: var(--info-bg);
    color: var(--info);
}

/* Responsive Design */
@media (max-width: 768px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    .page-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
        padding: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .page-actions {
        width: 100%;
        justify-content: stretch;
        flex-wrap: wrap;
    }
    
    .modern-table {
        font-size: 0.75rem;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 0.5rem;
    }
}

@media (max-width: 480px) {
    .modern-table {
        font-size: 0.7rem;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 0.375rem;
    }
    
    .modern-btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Animation au survol des cartes de stats
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transition = 'all 0.2s ease';
            card.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
});
</script>
@endpush
