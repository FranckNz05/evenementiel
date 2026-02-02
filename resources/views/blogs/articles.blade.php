@extends('layouts.app')

@section('title', 'Articles')

@section('content')
<div class="container py-4" style="max-width: 680px;">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h1 class="mb-1" style="color: var(--bleu-nuit); font-size: 2rem; font-weight: 700;">Articles</h1>
            <p class="text-muted mb-0">Découvrez nos derniers articles</p>
        </div>
        
        @auth
            @if(auth()->user()->hasRole([2, 3]))
                <a href="{{ route('blogs.create') }}" class="btn-threads-primary">
                    <i class="fas fa-plus"></i>
                </a>
            @endif
        @endauth
    </div>
    
    @if($articles->count() > 0)
        <!-- Liste des articles style Threads -->
        <div class="threads-feed">
            @foreach($articles as $article)
                @include('partials.blog-card-threads', ['blog' => $article])
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $articles->links() }}
        </div>
    @else
        <div class="threads-empty-state">
            <i class="fas fa-newspaper mb-3" style="font-size: 3rem; color: var(--blanc-or);"></i>
            <h3 style="color: var(--bleu-nuit);">Aucun article disponible</h3>
            <p class="text-muted">Revenez plus tard pour découvrir de nouveaux articles</p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffffff;
    --blanc-or-fonce: #e6b800;
}

.btn-threads-primary {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-fonce));
    color: var(--bleu-nuit);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
}

.btn-threads-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
    color: var(--bleu-nuit);
}

.threads-empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.threads-feed {
    display: flex;
    flex-direction: column;
}
</style>
@endpush
