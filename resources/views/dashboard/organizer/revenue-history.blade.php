@extends('layouts.dashboard')

@section('title', 'Historique des Revenus')

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

.modern-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.modern-table th {
    background: var(--gray-50);
    color: var(--bleu-nuit);
    font-weight: 600;
    padding: 1rem;
    text-align: left;
    border-bottom: 2px solid var(--bleu-nuit);
}

.modern-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.modern-table tbody tr:hover {
    background: var(--gray-50);
}

.payment-method-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.payment-method-mtn {
    background: #ff6b35;
    color: white;
}

.payment-method-airtel {
    background: #e60012;
    color: white;
}

.payment-method-mobile {
    background: var(--info);
    color: white;
}

.revenue-amount {
    font-weight: 700;
    color: var(--success);
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

.modern-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

.modern-pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border: 1px solid var(--gray-300);
    color: var(--gray-700);
    text-decoration: none;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.modern-pagination .page-link:hover {
    background: var(--bleu-nuit);
    color: var(--white);
    border-color: var(--bleu-nuit);
}

.modern-pagination .page-item.active .page-link {
    background: var(--bleu-nuit);
    color: var(--white);
    border-color: var(--bleu-nuit);
}

.modern-pagination .page-item.disabled .page-link {
    background: var(--gray-100);
    color: var(--gray-400);
    cursor: not-allowed;
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
    
    .modern-table {
        font-size: 0.875rem;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 0.75rem 0.5rem;
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
            <i class="fas fa-history"></i>
            Historique des Revenus
        </h1>
        <div class="page-actions">
            <a href="{{ route('organizer.revenue.dashboard') }}" class="btn-modern btn-secondary-modern">
                <i class="fas fa-arrow-left"></i>
                Retour au tableau de bord
            </a>
            <a href="{{ route('organizer.revenue.export') }}" class="btn-modern btn-success-modern">
                <i class="fas fa-download"></i>
                Exporter en CSV
            </a>
        </div>
    </div>

    <!-- Tableau des revenus -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-list"></i>
                Détail des Paiements
            </h5>
        </div>
        <div class="card-body">
            @if($payments->count() > 0)
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Événement</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Méthode</th>
                            <th>Revenu Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong>{{ $payment->event->title ?? 'Événement supprimé' }}</strong>
                            </td>
                            <td>
                                {{ $payment->user->prenom ?? 'N/A' }} {{ $payment->user->nom ?? 'N/A' }}
                                @if($payment->user->email)
                                <br><small class="text-muted">{{ $payment->user->email }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ number_format($payment->montant, 0, ',', ' ') }} FCFA</strong>
                            </td>
                            <td>
                                @if($payment->methode_paiement === 'MTN Mobile Money')
                                    <span class="payment-method-badge payment-method-mtn">MTN</span>
                                @elseif($payment->methode_paiement === 'Airtel Money')
                                    <span class="payment-method-badge payment-method-airtel">Airtel</span>
                                @else
                                    <span class="payment-method-badge payment-method-mobile">{{ $payment->methode_paiement }}</span>
                                @endif
                            </td>
                            <td class="revenue-amount">
                                {{ number_format($payment->net_revenue, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="modern-pagination">
                {{ $payments->links() }}
            </div>
            @else
            <div class="text-center text-muted py-5">
                <i class="fas fa-chart-line fa-4x mb-3"></i>
                <h4>Aucun revenu pour le moment</h4>
                <p>Vos revenus apparaîtront ici une fois que vos événements commenceront à vendre des billets.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
