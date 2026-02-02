@extends('layouts.dashboard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-navy text-gold py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Création d'événement - Prévisualisation
                    </h4>
                </div>
                
                <div class="card-body">
                    <!-- Barre de progression -->
                    <div class="progress mb-4" style="height: 10px;">
                        <div class="progress-bar bg-gold" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <div class="alert alert-info bg-light-blue text-navy border-navy mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Vérifiez les détails de votre événement avant de confirmer la création
                    </div>
                    
                    <!-- Prévisualisation de l'événement -->
                    <div class="event-preview mb-4">
                        <!-- Image de couverture -->
                        @if(isset($event['image']) && !empty($event['image']))
                            <div class="event-image mb-4" style="background-image: url('{{ $event['image'] }}');">
                                <div class="event-header-content">
                                    <h2>{{ $event['title'] }}</h2>
                                    <div class="event-meta">
                                        <span><i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($event['start_date'])->format('d/m/Y H:i') }}</span>
                                        <span><i class="fas fa-map-marker-alt"></i> {{ $event['lieu'] }}</span>
                                        <span><i class="fas fa-tag"></i> {{ $event['status'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-light-blue p-4 mb-4 text-center rounded">
                                <h2 class="text-navy">{{ $event['title'] }}</h2>
                                <div class="d-flex justify-content-center gap-4 mt-3">
                                    <span><i class="far fa-calendar-alt text-navy"></i> {{ \Carbon\Carbon::parse($event['start_date'])->format('d/m/Y H:i') }}</span>
                                    <span><i class="fas fa-map-marker-alt text-navy"></i> {{ $event['lieu'] }}</span>
                                    <span><i class="fas fa-tag text-navy"></i> {{ $event['status'] }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Détails de l'événement -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card border-navy mb-4">
                                    <div class="card-header bg-light-blue text-navy">
                                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Détails</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-tag text-navy me-2"></i> <strong>Type:</strong> {{ $event['status'] }}</li>
                                            <li class="mb-2"><i class="fas fa-users text-navy me-2"></i> <strong>Capacité:</strong> {{ $event['capacity'] ?? 'Illimitée' }} {{ isset($event['capacity']) ? 'places' : '' }}</li>
                                            <li class="mb-2"><i class="fas fa-calendar-check text-navy me-2"></i> <strong>Date de début:</strong> {{ \Carbon\Carbon::parse($event['start_date'])->format('d/m/Y H:i') }}</li>
                                            <li class="mb-2"><i class="fas fa-calendar-times text-navy me-2"></i> <strong>Date de fin:</strong> {{ \Carbon\Carbon::parse($event['end_date'])->format('d/m/Y H:i') }}</li>
                                            <li class="mb-2"><i class="fas fa-map-marker-alt text-navy me-2"></i> <strong>Lieu:</strong> {{ $event['lieu'] }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-navy mb-4">
                                    <div class="card-header bg-light-blue text-navy">
                                        <h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Description</h5>
                                    </div>
                                    <div class="card-body">
                                        {!! $event['description'] !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                       <!-- Sponsors -->
                        @if(count($sponsors) > 0)
                        <div class="card border-navy mb-4">
                            <div class="card-header bg-light-blue text-navy py-2">
                                <h5 class="mb-0">Nos Sponsors</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-center gap-4">
                                    @foreach($sponsors as $sponsor)
                                    <div class="sponsor-logo-container" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $sponsor['name'] }}">
                                        @if(!empty($sponsor['logo_path']))
                                            <img src="{{ $sponsor['logo_path'] }}" 
                                                alt="{{ $sponsor['name'] }}"
                                                class="sponsor-logo">
                                        @else
                                            <div class="sponsor-logo text-center d-flex align-items-center justify-content-center">
                                                <span class="sponsor-initials">{{ substr($sponsor['name'], 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <form action="{{ route('events.wizard.complete') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="d-flex justify-content-between pt-3 border-top border-gold">
                            <a href="{{ route('events.wizard.step3') }}" class="btn btn-outline-navy">
                                <i class="fas fa-arrow-left me-2"></i> Précédent
                            </a>
                            <button type="submit" class="btn bg-gold text-navy">
                                <i class="fas fa-check-circle me-2"></i> Confirmer et créer l'événement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-navy { background-color: #001f3f; }
    .text-navy { color: #001f3f; }
    .bg-gold { background-color: #ffffff; }
    .text-gold { color: #ffffff; }
    .bg-light-blue { background-color: #E6F2FF; }
    .border-gold { border-color: #ffffff !important; }
    .border-navy { border-color: #001f3f !important; }
    
    .btn-outline-navy {
        border: 1px solid #001f3f;
        color: #001f3f;
    }
    .btn-outline-navy:hover {
        background-color: #001f3f;
        color: white;
    }
    .btn.bg-gold:hover {
        background-color: #e6c200;
    }
    
    /* Style pour la carte */
    .card-body iframe {
        width: 100%;
        height: 300px;
        border: none;
    }
    
    .sponsor-logo-container {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        overflow: hidden;
        background: white;
        border: 2px solid #001f3f;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    .sponsor-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 5px;
        transition: transform 0.3s ease;
    }
    
    .sponsor-logo-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .sponsor-logo-container:hover .sponsor-logo {
        transform: scale(1.1);
    }
    
    .sponsor-initials {
        font-weight: bold;
        color: #001f3f;
        font-size: 1.2rem;
    }
    
    /* Ajustement pour les écrans mobiles */
    @media (max-width: 768px) {
        .sponsor-logo-container {
            width: 60px;
            height: 60px;
        }
    }
    
    /* Style pour l'image de couverture */
    .event-image {
        position: relative;
        height: 300px;
        background-size: cover;
        background-position: center;
        border-radius: 8px;
    }
    
    .event-header-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 2rem;
        background: linear-gradient(0deg, rgba(0,31,63,0.8) 0%, rgba(0,31,63,0) 100%);
        color: white;
    }
</style>
@endpush

@push('scripts')
<script>
    // Activer les tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection