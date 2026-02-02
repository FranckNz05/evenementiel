<div class="card h-100 shadow-sm">
    <img src="{{ asset('storage/' . $blog->image) }}" class="card-img-top" alt="{{ $blog->title }}" style="height: 200px; object-fit: cover;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted">{{ $blog->created_at->format('d M Y') }}</small>
        </div>
        <h5 class="card-title">{{ $blog->title }}</h5>
        <p class="card-text text-muted">{{ Str::limit(strip_tags($blog->content), 150) }}</p>
    </div>
    <div class="card-footer bg-white border-top-0">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                @if($blog->user && $blog->user->organizer)
                    <img src="{{ asset('storage/' . $blog->user->organizer->logo) }}" 
                         class="rounded-circle me-2" 
                         style="width: 30px; height: 30px; object-fit: cover;" 
                         alt="{{ $blog->user->organizer->company_name }}">
                    <span class="small">{{ $blog->user->organizer->company_name }}</span>
                @else
                    <span class="small text-muted">Utilisateur inconnu</span>
                @endif
            </div>
            <div class="d-flex align-items-center">
                <span class="small text-muted me-2">
                    <i class="far fa-comment"></i> {{ $blog->comments->count() }}
                </span>
            </div>
        </div>
    </div>
</div> 