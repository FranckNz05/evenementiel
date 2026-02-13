@extends('layouts.dashboard')

@section('title', 'Mes articles')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="fas fa-blog me-2 text-primary"></i>
            Mes articles de blog
        </h5>
        @auth
            @if(auth()->user()->hasRole([2, 3]))
                <a href="{{ route('organizer.blogs.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Créer un article
                </a>
            @endif
        @endauth
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

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">Image</th>
                    <th>Titre</th>
                    <th style="width: 120px;">Date</th>
                    <th style="width: 100px;">Vues</th>
                    <th style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blogs as $blog)
                    <tr>
                        <td>
                            @if($blog->image_path)
                                <img src="{{ Storage::url($blog->image_path) }}" alt="{{ $blog->title }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 4px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <h6 class="mb-1">{{ $blog->title }}</h6>
                            <p class="mb-0 text-muted small">{{ Str::limit(strip_tags($blog->content), 100) }}</p>
                        </td>
                        <td>{{ $blog->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-info">
                                <i class="fas fa-eye me-1"></i>{{ $blog->views ?? 0 }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#blogModal{{ $blog->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('organizer.blogs.edit', $blog) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('organizer.blogs.destroy', $blog) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-blog fa-2x mb-2 d-block"></i>
                            Aucun article pour le moment. Créez votre premier article !
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($blogs->hasPages())
    <div class="mt-4">
        {{ $blogs->links() }}
    </div>
    @endif
</div>

<!-- Modals pour afficher le contenu complet -->
@foreach($blogs as $blog)
<div class="modal fade" id="blogModal{{ $blog->id }}" tabindex="-1" aria-labelledby="blogModalLabel{{ $blog->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blogModalLabel{{ $blog->id }}">{{ $blog->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($blog->image_path)
                    <img class="img-fluid w-100 rounded mb-4" src="{{ Storage::url($blog->image_path) }}" alt="{{ $blog->title }}">
                @endif
                <div class="d-flex gap-3 mb-3 text-muted small">
                    <span><i class="fas fa-user me-1"></i>{{ $blog->user->organizer->company_name ?? $blog->user->nom }}</span>
                    <span><i class="fas fa-calendar me-1"></i>{{ $blog->created_at->format('d M Y') }}</span>
                    <span><i class="fas fa-eye me-1"></i>{{ $blog->views ?? 0 }} vues</span>
                </div>
                <div class="blog-content">
                    {!! sanitize_html($blog->content) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endpush

@endsection
