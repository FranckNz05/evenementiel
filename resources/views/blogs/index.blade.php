@extends('layouts.dashboard')

@section('title', 'Mes articles')

@section('content')
<div class="container-fluid dashboard-container">
    {{-- En-tête de page --}}
    <x-page-header 
        title="Mes articles de blog" 
        icon="fas fa-blog"
        subtitle="Gérez tous vos articles et publications">
        <x-slot:actions>
            @auth
                @if(auth()->user()->hasRole([2, 3]))
                    <a href="{{ route('blogs.create') }}" class="modern-btn btn-primary-modern">
                        <i class="fas fa-plus"></i>
                        Créer un article
                    </a>
                @endif
            @endauth
        </x-slot:actions>
    </x-page-header>

    {{-- Messages Flash --}}
    @if(session('success'))
        <div class="modern-alert alert-success-modern fade-in">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="modern-alert alert-danger-modern fade-in">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Contenu principal --}}
    <x-content-section title="Liste des articles" icon="fas fa-list">
        @if($blogs->count() > 0)
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Image</th>
                            <th>Titre</th>
                            <th style="width: 120px;">Date</th>
                            <th style="width: 100px;">Vues</th>
                            <th style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blogs as $blog)
                            <tr>
                                <td>
                                    @if($blog->image_path)
                                        <img src="{{ Storage::url($blog->image_path) }}" alt="{{ $blog->title }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    @else
                                        <div style="width: 60px; height: 60px; background: var(--gray-100); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image" style="color: var(--gray-400);"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: var(--bleu-nuit);">{{ $blog->title }}</strong>
                                    <p class="mb-0 text-muted small">{{ Str::limit(strip_tags($blog->content), 100) }}</p>
                                </td>
                                <td>{{ $blog->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="modern-badge badge-info">
                                        <i class="fas fa-eye"></i>
                                        {{ $blog->views ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group-modern">
                                        <button type="button" class="modern-btn btn-sm-modern btn-info-modern" data-bs-toggle="modal" data-bs-target="#blogModal{{ $blog->id }}" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('blogs.edit', $blog) }}" class="modern-btn btn-sm-modern btn-warning-modern" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('blogs.destroy', $blog) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="modern-btn btn-sm-modern btn-danger-modern" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($blogs->hasPages())
                <div class="pagination-container">
                    {{ $blogs->links() }}
                </div>
            @endif
        @else
            <x-empty-state 
                icon="fas fa-blog"
                title="Aucun article pour le moment"
                message="Vous n'avez pas encore créé d'article. Commencez par créer votre premier article pour partager vos idées et actualités !">
                <x-slot:action>
                    @auth
                        @if(auth()->user()->hasRole([2, 3]))
                            <a href="{{ route('blogs.create') }}" class="modern-btn btn-primary-modern">
                                <i class="fas fa-plus"></i>
                                Créer mon premier article
                            </a>
                        @endif
                    @endauth
                </x-slot:action>
            </x-empty-state>
        @endif
    </x-content-section>
</div>

<!-- Modals pour afficher le contenu complet -->
@foreach($blogs as $blog)
<div class="modal fade modal-modern" id="blogModal{{ $blog->id }}" tabindex="-1" aria-labelledby="blogModalLabel{{ $blog->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blogModalLabel{{ $blog->id }}">
                    <i class="fas fa-blog"></i>
                    {{ $blog->title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($blog->image_path)
                    <img class="img-fluid w-100 rounded mb-4" src="{{ Storage::url($blog->image_path) }}" alt="{{ $blog->title }}" style="border-radius: var(--radius-lg);">
                @endif
                <div class="d-flex gap-3 mb-3 text-muted small">
                    <span><i class="fas fa-user me-1"></i>{{ optional(optional($blog->user)->organizer)->company_name ?? (optional($blog->user)->nom ?? 'Utilisateur') }}</span>
                    <span><i class="fas fa-calendar me-1"></i>{{ $blog->created_at->format('d M Y') }}</span>
                    <span><i class="fas fa-eye me-1"></i>{{ $blog->views ?? 0 }} vues</span>
                </div>
                <div class="blog-content">
                    {!! sanitize_html($blog->content) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="modern-btn btn-secondary-modern" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Fermer
                </button>
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
