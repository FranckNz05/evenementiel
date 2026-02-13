@extends('layouts.app')

@section('content')
<div class="container-fluid py-5 mb-5">
    <div class="container">
        <h1 class="text-center mb-5">Résultats de recherche</h1>

        <!-- Search Form -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <form action="{{ route('events.search') }}" method="GET" class="search-form">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Rechercher un événement..." value="{{ request('q') }}">
                        <select name="category" class="form-select" style="max-width: 200px;">
                            <option value="">Toutes les catégories</option>
                            @foreach(\App\Models\EventCategory::all() as $category)
                                <option value="{{ $category->id }}" {{ request('categories') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="date" class="form-select" style="max-width: 200px;">
                            <option value="">Toutes les dates</option>
                            <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="week" {{ request('date') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="month" {{ request('date') == 'month' ? 'selected' : '' }}>Ce mois</option>
                        </select>
                        <button class="btn btn-primary" type="submit">Rechercher</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results -->
        <div class="row g-4">
            @forelse($events as $event)
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item rounded overflow-hidden">
                        <img class="img-fluid" src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}">
                        <div class="position-relative p-4 pt-0">
                            <div class="service-icon">
                                <i class="fa fa-calendar fa-3x"></i>
                            </div>
                            <h4 class="mb-3">{{ $event->title }}</h4>
                            <p>{{ Str::limit($event->description, 100) }}</p>
                            <small class="text-muted">
                                <i class="far fa-calendar-alt"></i> {{ $event->start_date->format('d/m/Y H:i') }}
                            </small>
                            <br>
                            <a class="small fw-medium" href="{{ route('events.show', $event) }}">En savoir plus<i class="fa fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p>Aucun événement trouvé.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                <x-pagination :paginator="$events" />
            </div>
        </div>
    </div>
</div>
@endsection
