@extends('layouts.dashboard')

@section('title', 'Mes événements favoris')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-0">Mes événements favoris</h1>
            <p class="text-muted">Retrouvez tous les événements que vous avez ajoutés à vos favoris</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($favorites->count() > 0)
        <div class="row g-4">
            @foreach($favorites as $favorite)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                        <div class="position-relative">
                            <img src="{{ asset($favorite->event->image) }}" 
                                 class="card-img-top" alt="{{ $favorite->event->title }}"
                                 style="height: 200px; object-fit: cover;">
                            
                            <!-- Badge catégorie -->
                            @if($favorite->event->category)
                                <span class="position-absolute top-0 start-0 m-3 badge bg-primary">
                                    {{ $favorite->event->category->name }}
                                </span>
                            @endif
                            
                            <!-- Bouton favoris -->
                            <form action="{{ route('events.favorite', $favorite->event) }}" 
                                  method="POST" class="position-absolute top-0 end-0 m-3">
                                @csrf
                                <button type="submit" class="btn btn-light rounded-circle p-2 shadow-sm">
                                    <i class="fas fa-heart text-danger"></i>
                                </button>
                            </form>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ $favorite->event->title }}</h5>
                            
                            <!-- Date et Heure -->
                            @if($favorite->event->start_date)
                                <div class="mb-2 text-muted small">
                                    <i class="far fa-calendar-alt me-2"></i>
                                    {{ \Carbon\Carbon::parse($favorite->event->start_date)->isoFormat('D MMMM YYYY [à] HH[h]mm') }}
                                </div>
                            @endif
                            
                            <!-- Lieu -->
                            @if($favorite->event->ville)
                                <div class="mb-3 text-muted small">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    {{ $favorite->event->ville }}
                                </div>
                            @endif
                            
                            <!-- Prix -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    @if($favorite->event->tickets->min('prix') == 0)
                                        <span class="badge bg-success">Gratuit</span>
                                    @else
                                        <span class="fw-bold">À partir de {{ number_format($favorite->event->tickets->min('prix'), 0, ',', ' ') }} FCFA</span>
                                    @endif
                                </div>
                                <a href="{{ route('events.show', $favorite->event) }}" class="btn btn-primary btn-sm">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Vous n'avez pas encore d'événements favoris.
        </div>
        
        <div class="text-center mt-4">
            <a href="/direct-events" class="btn btn-primary">
                <i class="fas fa-search me-2"></i> Découvrir des événements
            </a>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush
