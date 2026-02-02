<!-- Popular Events Section -->
<div class="container py-5">
    <h2 class="text-center mb-5">Nos Événements Populaires</h2>
    <div class="row g-4">
        @foreach($events as $event)
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                @if($event->image)
                    <img src="{{ Storage::url($event->image) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $event->title }}</h5>
                    <p class="card-text text-muted">
                        <i class="far fa-calendar-alt me-2"></i>
                        {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}
                    </p>
                    <p class="card-text text-muted">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ $event->ville }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary">{{ $event->category->name }}</span>
                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-outline-primary">Voir détails</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div> 