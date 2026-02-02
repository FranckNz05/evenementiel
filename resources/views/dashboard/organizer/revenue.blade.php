@extends('layouts.dashboard')

@section('title', 'Mes Revenus')

@push('styles')
<style>
:root {
    --bleu-nuit: #1e3a8a;
    --bleu-nuit-clair: #3b82f6;
    --blanc-or: #ffffff;
    --blanc-amber: #f59e0b;
    --shadow-gold: rgba(255, 215, 0, 0.2);
    --shadow-blue: rgba(30, 58, 138, 0.1);
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
    --gray-900: #111827;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --border-radius: 0.75rem;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --transition: all 0.3s ease;
}

.container-fluid {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    min-height: 100vh;
    padding: 2rem;
    border-top: 4px solid var(--bleu-nuit);
}

.page-header {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    padding: 2rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.page-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    position: relative;
    z-index: 1;
}

.modern-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    transition: var(--transition);
}

.modern-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.card-header-modern {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    border-bottom: 2px solid var(--bleu-nuit);
    padding: 1.5rem;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--bleu-nuit);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.revenue-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border-left: 4px solid var(--bleu-nuit);
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--gray-600);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-icon {
    font-size: 2rem;
    color: var(--blanc-or);
    margin-bottom: 1rem;
}

.commission-breakdown {
    background: var(--gray-50);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-top: 1rem;
}

.commission-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.commission-item:last-child {
    border-bottom: none;
    font-weight: 600;
    color: var(--bleu-nuit);
}

.btn-modern {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition);
    box-shadow: var(--shadow);
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: var(--white);
    text-decoration: none;
}

.btn-secondary-modern {
    background: linear-gradient(135deg, var(--gray-500) 0%, var(--gray-600) 100%);
    color: var(--white);
}

.btn-success-modern {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    color: var(--white);
}

.event-revenue-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
    transition: var(--transition);
}

.event-revenue-item:hover {
    background: var(--gray-50);
}

.event-revenue-item:last-child {
    border-bottom: none;
}

.event-title {
    font-weight: 600;
    color: var(--bleu-nuit);
    margin-bottom: 0.25rem;
}

.event-meta {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.revenue-amount {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--success);
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .page-header {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .revenue-stats {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .page-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-chart-line"></i>
            Mes Revenus
        </h1>
        <div class="page-actions">
            <a href="{{ route('organizer.revenue.history') }}" class="btn-modern btn-secondary-modern">
                <i class="fas fa-history"></i>
                Historique détaillé
            </a>
            <a href="{{ route('organizer.revenue.export') }}" class="btn-modern btn-success-modern">
                <i class="fas fa-download"></i>
                Exporter en CSV
            </a>
        </div>
    </div>

    <!-- Statistiques des revenus -->
    <div class="revenue-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-number">{{ number_format($revenueData['total_revenue'], 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Revenus Bruts Totaux</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-number">{{ number_format($revenueData['total_commission_paid'], 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Commissions Payées</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-number">{{ number_format($revenueData['net_revenue'], 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Revenus Nets</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="stat-number">{{ $revenueData['breakdown']['total_payments'] }}</div>
            <div class="stat-label">Billets Vendus</div>
        </div>
    </div>

    <div class="row">
        <!-- Détail des commissions -->
        <div class="col-lg-6">
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5 class="card-title">
                        <i class="fas fa-calculator"></i>
                        Détail des Commissions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="commission-breakdown">
                        <div class="commission-item">
                            <span>Commission MTN Mobile Money</span>
                            <span>{{ number_format($revenueData['breakdown']['mtn_commission'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="commission-item">
                            <span>Commission Airtel Money</span>
                            <span>{{ number_format($revenueData['breakdown']['airtel_commission'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="commission-item">
                            <span>Commission MokiliEvent</span>
                            <span>{{ number_format($revenueData['breakdown']['mokilievent_commission'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="commission-item">
                            <span><strong>Total des Commissions</strong></span>
                            <span><strong>{{ number_format($revenueData['total_commission_paid'], 0, ',', ' ') }} FCFA</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenus par événement -->
        <div class="col-lg-6">
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-alt"></i>
                        Revenus par Événement
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($events as $event)
                    <div class="event-revenue-item">
                        <div>
                            <div class="event-title">{{ $event->title }}</div>
                            <div class="event-meta">{{ $event->payments_count }} billets vendus</div>
                        </div>
                        <div class="revenue-amount">{{ number_format($event->net_revenue, 0, ',', ' ') }} FCFA</div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <p>Aucun événement avec des revenus pour le moment</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Actualiser les données toutes les 30 secondes
setInterval(function() {
    fetch('{{ route("organizer.revenue.api") }}')
        .then(response => response.json())
        .then(data => {
            // Mettre à jour les statistiques
            document.querySelector('.stat-card:nth-child(1) .stat-number').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.total_revenue) + ' FCFA';
            document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.total_commission_paid) + ' FCFA';
            document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.net_revenue) + ' FCFA';
        })
        .catch(error => console.error('Erreur lors de la mise à jour:', error));
}, 30000);
</script>
@endpush
