@extends('layouts.dashboard')

@section('title', 'Détails de l\'article')

@push('styles')
<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffffff;
    --blanc-amber: #f59e0b;
    --shadow-gold: rgba(255, 215, 0, 0.2);
    --shadow-blue: rgba(153, 27, 27, 0.1);
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --border-radius: 0.75rem;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --transition: all 0.3s ease;
}

.container-fluid {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    min-height: 100vh;
    padding: 2rem;
    border-top: 4px solid var(--bleu-nuit);
}

.page-header {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    padding: 2rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.page-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    position: relative;
    z-index: 1;
}

.modern-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    transition: var(--transition);
}

.modern-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.card-header-modern {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    border-bottom: 2px solid var(--bleu-nuit);
    padding: 1.5rem;
}

.card-title {
    color: var(--bleu-nuit);
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.modern-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
    font-size: 0.875rem;
}

.btn-primary-modern {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
}

.btn-primary-modern:hover {
    background: linear-gradient(135deg, var(--bleu-nuit-clair) 0%, var(--bleu-nuit) 100%);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-warning-modern {
    background: linear-gradient(135deg, var(--blanc-or) 0%, var(--blanc-amber) 100%);
    color: var(--white);
}

.btn-warning-modern:hover {
    background: linear-gradient(135deg, var(--blanc-amber) 0%, var(--blanc-or) 100%);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-danger-modern {
    background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
    color: var(--white);
}

.btn-danger-modern:hover {
    background: linear-gradient(135deg, #dc2626 0%, var(--danger) 100%);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-sm-modern {
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
}

.blog-image {
    width: 100%;
    max-width: 400px;
    height: 250px;
    object-fit: cover;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.blog-content {
    line-height: 1.8;
    color: var(--gray-700);
    font-size: 1rem;
}

.blog-meta {
    background: var(--gray-50);
    padding: 1rem;
    border-radius: var(--border-radius);
    border-left: 4px solid var(--bleu-nuit);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    color: var(--gray-600);
}

.meta-item:last-child {
    margin-bottom: 0;
}

.meta-label {
    font-weight: 600;
    color: var(--bleu-nuit);
    min-width: 100px;
}

.modern-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.badge-info {
    background: var(--info);
    color: var(--white);
}

.badge-success {
    background: var(--success);
    color: var(--white);
}

.badge-danger {
    background: var(--danger);
    color: var(--white);
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .page-header {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .page-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .modern-btn {
        justify-content: center;
    }
    
    .blog-image {
        height: 200px;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-eye"></i>
            Détails de l'article
        </h1>
        <div class="page-actions">
            <a href="{{ route('admin.blogs.index') }}" class="modern-btn btn-primary-modern">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
            <a href="{{ route('admin.blogs.edit', $blog) }}" class="modern-btn btn-warning-modern">
                <i class="fas fa-edit"></i>
                Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5 class="card-title">
                        <i class="fas fa-file-alt"></i>
                        {{ $blog->title }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($blog->image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="blog-image">
                        </div>
                    @endif
                    
                    <div class="blog-content">
                        {!! nl2br(e($blog->content)) !!}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Informations
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="blog-meta">
                        <div class="meta-item">
                            <span class="meta-label">Auteur:</span>
                            <span>{{ $blog->user->nom ?? 'Non défini' }}</span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Email:</span>
                            <span>{{ $blog->user->email ?? 'Non disponible' }}</span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Catégorie:</span>
                            <span class="modern-badge badge-info">
                                {{ $blog->blogcategories->name ?? 'Sans catégorie' }}
                            </span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Slug:</span>
                            <span class="font-monospace">{{ $blog->slug }}</span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Créé le:</span>
                            <span>{{ $blog->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Modifié le:</span>
                            <span>{{ $blog->updated_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Statut:</span>
                            @if($blog->deleted_at)
                                <span class="modern-badge badge-danger">Supprimé</span>
                            @else
                                <span class="modern-badge badge-success">Actif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
