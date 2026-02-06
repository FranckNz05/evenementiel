@extends('layouts.dashboard')

@section('title', 'Mes Revenus')



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
