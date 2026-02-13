@extends('layouts.dashboard')

@section('title', 'Tableau de bord')

@section('content')
@php
$breadcrumbs = [
    ['text' => 'Tableau de bord']
];
@endphp

@include('layouts.partials.page-header', ['pageTitle' => 'Tableau de bord', 'breadcrumbs' => $breadcrumbs])

<div class="container py-5">
            @php
                $user = auth()->user();
                $missingFields = [];

                if(empty($user->genre)) $missingFields[] = 'genre';
                if(empty($user->tranche_age)) $missingFields[] = 'tranche d\'âge';
                if(empty($user->address)) $missingFields[] = 'adresse';
                if(empty($user->city)) $missingFields[] = 'ville';
                if(empty($user->country)) $missingFields[] = 'pays';

                $completionPercentage = $user->getProfileCompletionPercentageAttribute();
            @endphp

            @if(count($missingFields) > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-edit fa-2x text-primary me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5>Complétez votre profil</h5>
                                <p class="mb-2">Pour une meilleure expérience et des recommandations personnalisées, veuillez compléter votre profil.</p>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="progress flex-grow-1 me-3" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercentage }}%" aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="badge bg-primary">{{ $completionPercentage }}%</span>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Compléter mon profil</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Cartes de statistiques -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-ticket-alt fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Mes billets</h5>
                            <h2 class="mb-0">{{ $user->tickets()->count() }}</h2>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="{{ route('profile.tickets') }}" class="btn btn-outline-primary btn-sm">Voir tous mes billets</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                            <h5 class="card-title">Favoris</h5>
                            <h2 class="mb-0">{{ $user->favorites()->count() }}</h2>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="#" class="btn btn-outline-primary btn-sm">Voir mes favoris</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-comment fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Commentaires</h5>
                            <h2 class="mb-0">{{ $user->comments()->count() }}</h2>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="#" class="btn btn-outline-primary btn-sm">Voir mes commentaires</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Événements à venir -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Mes événements à venir</h5>
                </div>
                <div class="card-body">
                    @php
                        $upcomingEvents = $user->tickets()
                            ->with('event')
                            ->whereHas('event', function($q) {
                                $q->where('start_date', '>=', now());
                            })
                            ->get()
                            ->map(function($ticket) {
                                return $ticket->event;
                            })
                            ->unique('id')
                            ->take(3);
                    @endphp

                    @if($upcomingEvents->count() > 0)
                        <div class="row">
                            @foreach($upcomingEvents as $event)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <img src="{{ $event->featured_image ? asset('storage/' . $event->featured_image) : asset('images/event-placeholder.jpg') }}"
                                            class="card-img-top" alt="{{ $event->title }}" style="height: 150px; object-fit: cover;">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $event->title }}</h6>
                                            <p class="text-muted small mb-1">
                                                <i class="far fa-calendar me-1"></i> {{ $event->start_date->format('d/m/Y') }}
                                            </p>
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-map-marker-alt me-1"></i> {{ $event->location }}
                                            </p>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <a href="{{ route('events.show', $event->id) }}" class="btn btn-sm btn-outline-primary w-100">Détails</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/no-events.svg') }}" alt="Aucun événement" class="img-fluid mb-3" style="max-width: 150px;">
                            <h5>Vous n'avez pas d'événements à venir</h5>
                            <p class="text-muted">Découvrez les événements disponibles et achetez des billets</p>
                            <a href="{{ url('/direct-events') }}" class="btn btn-primary">Découvrir des événements</a>
                        </div>
                    @endif
                </div>
            </div>
</div>
@endsection

@push('scripts')
<script>
    // Garder l'onglet actif après le rechargement de la page
    $(document).ready(function() {
        var hash = window.location.hash;
        if (hash) {
            $('.list-group-item[href="' + hash + '"]').tab('show');
        }

        // Mettre à jour l'URL lors du changement d'onglet
        $('.list-group-item').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
    });
</script>
@endpush


