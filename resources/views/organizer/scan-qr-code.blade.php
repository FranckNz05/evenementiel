@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        @include('organizer.partials.sidebar')
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Scanner des QR Codes</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Vous pouvez scanner des QR codes directement depuis cette page ou générer un code d'accès pour l'application mobile.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Scanner un QR Code</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3">Utilisez votre webcam pour scanner un QR code de billet.</p>
                                    
                                    <div class="mb-3">
                                        <label for="event_id_scanner" class="form-label">Sélectionnez l'événement</label>
                                        <select class="form-select" id="event_id_scanner" required>
                                            <option value="">Choisir un événement</option>
                                            @foreach($events as $event)
                                                <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->start_date->format('d/m/Y') }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div id="scanner-container" class="mb-3 d-none">
                                        <div class="ratio ratio-1x1 border rounded mb-3" style="max-width: 300px; margin: 0 auto;">
                                            <div id="reader"></div>
                                        </div>
                                        <button type="button" id="start-scanner" class="btn btn-primary">
                                            <i class="fas fa-camera me-2"></i> Démarrer le scanner
                                        </button>
                                        <button type="button" id="stop-scanner" class="btn btn-secondary d-none">
                                            <i class="fas fa-stop me-2"></i> Arrêter le scanner
                                        </button>
                                    </div>
                                    
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
                                                    Type de billet
                                                    <span id="ticket-type" class="badge bg-info rounded-pill"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Scanné à
                                                    <span id="ticket-scanned" class="badge bg-dark rounded-pill"></span>
                                                </li>
                                            </ul>
                                        </div>
                                        <button type="button" id="scan-again" class="btn btn-primary">
                                            <i class="fas fa-redo me-2"></i> Scanner un autre billet
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Générer un code d'accès pour l'application mobile</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3">Générez un code d'accès temporaire pour l'application mobile de scan.</p>
                                    
                                    <form action="{{ route('organizer.generate-access-code') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="event_id" class="form-label">Événement</label>
                                            <select class="form-select @error('event_id') is-invalid @enderror" id="event_id" name="event_id" required>
                                                <option value="">Sélectionnez un événement</option>
                                                @foreach($events as $event)
                                                    <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->start_date->format('d/m/Y') }})</option>
                                                @endforeach
                                            </select>
                                            @error('event_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="expiry_hours" class="form-label">Durée de validité (heures)</label>
                                            <input type="number" class="form-control @error('expiry_hours') is-invalid @enderror" id="expiry_hours" name="expiry_hours" min="1" max="72" value="24" required>
                                            @error('expiry_hours')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Le code sera valide pendant cette durée à partir de maintenant.</div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key me-2"></i> Générer un code d'accès
                                        </button>
                                    </form>
                                    
                                    @if(session('success') && str_contains(session('success'), 'Code d\'accès généré'))
                                        <div class="alert alert-success mt-3">
                                            <p class="mb-2"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</p>
                                            <p class="mb-0 small">Partagez ce code avec les personnes qui utiliseront l'application mobile pour scanner les billets.</p>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3">
                                        <a href="{{ route('organizer.access-codes') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-list me-1"></i> Voir tous les codes d'accès
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Statistiques de scan</h6>
                                </div>
                                <div class="card-body">
                                    <p>Sélectionnez un événement pour voir les statistiques détaillées des scans.</p>
                                    
                                    <div class="list-group">
                                        @foreach($events as $event)
                                            <a href="{{ route('organizer.scan-stats', $event->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                {{ $event->title }}
                                                <span class="badge bg-primary rounded-pill">
                                                    <i class="fas fa-chart-bar me-1"></i> Voir
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const eventSelect = document.getElementById('event_id_scanner');
    const scannerContainer = document.getElementById('scanner-container');
    const startScannerBtn = document.getElementById('start