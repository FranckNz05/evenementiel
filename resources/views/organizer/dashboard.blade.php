@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec boutons d'action -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de bord</h1>
        <div>
            <a href="{{ route('events.select-type') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus"></i> Créer un événement
            </a>
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#scanQrModal">
                <i class="fas fa-qrcode"></i> Scanner QR Code
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Événements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_events'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Réservations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_bookings'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Revenus</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Événements actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_events'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques de scan et codes d'accès -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques de scan</h6>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#generateCodeModal">
                        <i class="fas fa-key"></i> Générer code d'accès
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div class="h1 mb-0 font-weight-bold text-primary">{{ $stats['total_scans'] ?? 0 }}</div>
                            <div class="small text-gray-600">Total des scans</div>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="h1 mb-0 font-weight-bold text-success">{{ $stats['valid_scans'] ?? 0 }}</div>
                            <div class="small text-gray-600">Scans valides</div>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="h1 mb-0 font-weight-bold text-danger">{{ $stats['invalid_scans'] ?? 0 }}</div>
                            <div class="small text-gray-600">Scans invalides</div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="font-weight-bold">Derniers codes d'accès générés</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Événement</th>
                                        <th>Expire le</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($accessCodes ?? [] as $code)
                                    <tr>
                                        <td><code>{{ $code->code }}</code></td>
                                        <td>{{ $code->event->title }}</td>
                                        <td>{{ $code->expires_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($code->status === 'active' && !$code->isExpired())
                                                <span class="badge bg-success">Actif</span>
                                            @elseif($code->status === 'revoked')
                                                <span class="badge bg-danger">Révoqué</span>
                                            @elseif($code->isExpired())
                                                <span class="badge bg-secondary">Expiré</span>
                                            @else
                                                <span class="badge bg-warning">{{ $code->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Aucun code d'accès généré</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#scanStatsModal">
                            <i class="fas fa-chart-bar"></i> Voir toutes les statistiques
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Promotions actives</h6>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPromotionModal">
                        <i class="fas fa-plus"></i> Ajouter une promotion
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Événement</th>
                                    <th>Réduction</th>
                                    <th>Expire le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($promotions ?? [] as $promo)
                                <tr>
                                    <td><code>{{ $promo->code }}</code></td>
                                    <td>{{ $promo->event->title }}</td>
                                    <td>{{ $promo->discount_percentage }}%</td>
                                    <td>{{ $promo->expires_at->format('d/m/Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Aucune promotion active</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list"></i> Voir toutes les promotions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Événements récents -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Événements récents</h6>
                    <a href="{{ route('organizer.events.index') }}" class="btn btn-sm btn-primary">Voir tous</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Date</th>
                                    <th>Lieu</th>
                                    <th>Réservations</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentEvents as $event)
                                <tr>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->start_date->format('d/m/Y H:i') }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>{{ $event->bookings_count }}</td>
                                    <td>
                                        <span class="badge {{ $event->status === 'published' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $event->status === 'published' ? 'Publié' : 'Brouillon' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#qrCodeModal" data-event-id="{{ $event->id }}">
                                                <i class="fas fa-qrcode"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Réservations récentes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Réservations récentes</h6>
                    <a href="{{ route('organizer.bookings.index') }}" class="btn btn-sm btn-primary">Voir tous</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Événement</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td>{{ $booking->event->title }}</td>
                                    <td>{{ $booking->user->name }}</td>
                                    <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge {{ $booking->status === 'completed' ? 'bg-success' : ($booking->status === 'confirmed' ? 'bg-primary' : 'bg-warning text-dark') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('organizer.bookings.show', $booking) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#ticketDetailsModal" data-booking-id="{{ $booking->id }}">
                                                <i class="fas fa-ticket-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique des revenus -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenus par mois</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition des ventes par type de billet</h6>
                </div>
                <div class="card-body">
                    <canvas id="ticketTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Scanner QR Code -->
<div class="modal fade" id="scanQrModal" tabindex="-1" aria-labelledby="scanQrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanQrModalLabel">Scanner un QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="scan_event_id" class="form-label">Sélectionnez l'événement</label>
                            <select class="form-select" id="scan_event_id" required>
                                <option value="">Choisir un événement</option>
                                @foreach($events ?? [] as $event)
                                    <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->start_date->format('d/m/Y') }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div id="reader-container" class="mb-3">
                            <div class="ratio ratio-1x1 border rounded mb-3" style="max-width: 300px; margin: 0 auto;">
                                <div id="reader"></div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="button" id="start-scanner" class="btn btn-primary">
                                <i class="fas fa-camera me-2"></i> Démarrer le scanner
                            </button>
                            <button type="button" id="stop-scanner" class="btn btn-secondary d-none">
                                <i class="fas fa-stop me-2"></i> Arrêter le scanner
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div id="scan-result" class="d-none">
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle me-2"></i> <span id="scan-message"></span>
                            </div>
                            <div class="mb-3">
                                <h6>Informations du billet:</h6>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Événement
                                        <span id="ticket-event" class="badge bg-primary rounded-pill"></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Nom
                                        <span id="ticket-name" class="badge bg-secondary rounded-pill"></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Statut
                                        <span id="ticket-status" class="badge rounded-pill"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Générer Code d'Accès -->
<div class="modal fade" id="generateCodeModal" tabindex="-1" aria-labelledby="generateCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateCodeModalLabel">Générer un Code d'Accès</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="generateCodeForm">
                    <div class="mb-3">
                        <label for="event_id" class="form-label">Sélectionnez l'événement</label>
                        <select class="form-select" id="event_id" name="event_id" required>
                            <option value="">Choisir un événement</option>
                            @foreach($events ?? [] as $event)
                                <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->start_date->format('d/m/Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Code d'Accès</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Date d'expiration</label>
                        <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Actif</option>
                            <option value="revoked">Révoqué</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Générer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Statistiques de Scan -->
<div class="modal fade" id="scanStatsModal" tabindex="-1" aria-labelledby="scanStatsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanStatsModalLabel">Statistiques de Scan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <canvas id="scanStatsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Promotion -->
<div class="modal fade" id="addPromotionModal" tabindex="-1" aria-labelledby="addPromotionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPromotionModalLabel">Ajouter une Promotion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPromotionForm">
                    <div class="mb-3">
                        <label for="event_id" class="form-label">Sélectionnez l'événement</label>
                        <select class="form-select" id="event_id" name="event_id" required>
                            <option value="">Choisir un événement</option>
                            @foreach($events ?? [] as $event)
                                <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->start_date->format('d/m/Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Code de Promotion</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="discount_percentage" class="form-label">Pourcentage de Réduction</label>
                        <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" min="1" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Date d'expiration</label>
                        <input type="date" class="form-control" id="expires_at" name="expires_at" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR Code de l'événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="qr-code-container" class="d-flex justify-content-center align-items-center" style="height: 300px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Détails du Ticket -->
<div class="modal fade" id="ticketDetailsModal" tabindex="-1" aria-labelledby="ticketDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketDetailsModalLabel">Détails du Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="ticket-details-container" class="d-flex justify-content-center align-items-center" style="height: 300px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const monthlyRevenue = @json($monthlyRevenue);
    
    const labels = monthlyRevenue.map(item => {
        const date = new Date(item.year, item.month - 1);
        return date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
    }).reverse();
    
    const data = monthlyRevenue.map(item => item.revenue).reverse();
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenus (€)',
                data: data,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ticketTypeCtx = document.getElementById('ticketTypeChart').getContext('2d');
    const ticketTypes = @json($ticketTypes);
    
    const ticketLabels = Object.keys(ticketTypes);
    const ticketData = Object.values(ticketTypes);
    
    new Chart(ticketTypeCtx, {
        type: 'pie',
        data: {
            labels: ticketLabels,
            datasets: [{
                data: ticketData,
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ],
                hoverBackgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });

    const scanStatsCtx = document.getElementById('scanStatsChart').getContext('2d');
    const scanStats = @json($scanStats);
    
    const scanLabels = Object.keys(scanStats);
    const scanData = Object.values(scanStats);
    
    new Chart(scanStatsCtx, {
        type: 'bar',
        data: {
            labels: scanLabels,
            datasets: [{
                label: 'Nombre de scans',
                data: scanData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    document.getElementById('generateCodeForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('{{ route('organizer.access_codes.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Code d\'accès généré avec succès !');
                location.reload();
            } else {
                alert('Erreur lors de la génération du code d\'accès : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur :', error);
            alert('Une erreur s\'est produite lors de la génération du code d\'accès.');
        });
    });

    document.getElementById('addPromotionForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('{{ route('organizer.promotions.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Promotion ajoutée avec succès !');
                location.reload();
            } else {
                alert('Erreur lors de l\'ajout de la promotion : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur :', error);
            alert('Une erreur s\'est produite lors de l\'ajout de la promotion.');
        });
    });

    document.getElementById('qrCodeModal').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const eventId = button.getAttribute('data-event-id');
        const qrCodeContainer = document.getElementById('qr-code-container');
        qrCodeContainer.innerHTML = ''; // Clear previous QR code
        const qr = qrcode(0, 'L');
        qr.addData(`{{ url('organizer/events') }}/${eventId}`);
        qr.make();
        const qrCodeImage = qr.createDataURL();
        const img = document.createElement('img');
        img.src = qrCodeImage;
        img.style.maxWidth = '100%';
        img.style.maxHeight = '100%';
        qrCodeContainer.appendChild(img);
    });

    document.getElementById('ticketDetailsModal').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const bookingId = button.getAttribute('data-booking-id');
        const ticketDetailsContainer = document.getElementById('ticket-details-container');
        ticketDetailsContainer.innerHTML = ''; // Clear previous ticket details
        fetch(`{{ route('organizer.bookings.ticket', ['booking' => ':id']) }}`.replace(':id', bookingId))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const ticketDetails = data.ticket;
                    const ticketHtml = `
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">${ticketDetails.event_title}</h5>
                                <p class="card-text">Date: ${ticketDetails.event_date}</p>
                                <p class="card-text">Lieu: ${ticketDetails.event_location}</p>
                                <p class="card-text">Nom du client: ${ticketDetails.client_name}</p>
                                <p class="card-text">Statut: ${ticketDetails.status}</p>
                                <p class="card-text">Code de réservation: ${ticketDetails.reservation_code}</p>
                            </div>
                        </div>
                    `;
                    ticketDetailsContainer.innerHTML = ticketHtml;
                } else {
                    ticketDetailsContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Erreur :', error);
                ticketDetailsContainer.innerHTML = `<div class="alert alert-danger">Une erreur s'est produite lors du chargement des détails du ticket.</div>`;
            });
    });

    const reader = new QuaggaJS();
    const startScanner = () => {
        Quagga.init({
            inputStream: {
                type: "LiveStream",
                target: document.querySelector('#reader')
            },
            decoder: {
                readers: ["code_128_reader"]
            }
        }, function(err) {
            if (err) {
                console.error(err);
                return;
            }
            Quagga.start();
        });
    };

    const stopScanner = () => {
        Quagga.stop();
    };

    document.getElementById('start-scanner').addEventListener('click', () => {
        const selectedEventId = document.getElementById('scan_event_id').value;
        if (selectedEventId) {
            startScanner();
            document.getElementById('stop-scanner').classList.remove('d-none');
        } else {
            alert('Veuillez sélectionner un événement.');
        }
    });

    document.getElementById('stop-scanner').addEventListener('click', () => {
        stopScanner();
        document.getElementById('stop-scanner').classList.add('d-none');
    });

    Quagga.onDetected((data) => {
        const code = data.codeResult.code;
        const selectedEventId = document.getElementById('scan_event_id').value;
        fetch(`{{ route('organizer.scans.validate') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                code: code,
                event_id: selectedEventId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const scanResult = document.getElementById('scan-result');
                scanResult.classList.remove('d-none');
                document.getElementById('scan-message').textContent = data.message;
                document.getElementById('ticket-event').textContent = data.event_title;
                document.getElementById('ticket-name').textContent = data.client_name;
                document.getElementById('ticket-status').textContent = data.status;
                document.getElementById('ticket-status').className = `badge ${data.status === 'Validé' ? 'bg-success' : 'bg-danger'}`;
                stopScanner();
                document.getElementById('stop-scanner').classList.add('d-none');
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur :', error);
            alert('Une erreur s\'est produite lors de la validation du scan.');
        });
    });
</script>
@endpush 
