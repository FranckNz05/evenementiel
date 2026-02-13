@extends('layouts.app')

@section('title', $blog->title)

@section('content')
<div class="container py-4" style="max-width: 680px;">
    <!-- Bouton retour -->
    <div class="mb-4">
        <a href="javascript:history.back()" class="threads-back-btn">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <!-- Article complet style Threads -->
    <article class="threads-post-full">
        <!-- En-tête avec avatar et infos auteur -->
        <div class="threads-header-full">
            <div class="d-flex align-items-start gap-3 mb-4">
                <!-- Avatar -->
                <div class="threads-avatar-large">
                    @if($blog->user && $blog->user->organizer && $blog->user->organizer->logo)
                        <img src="{{ asset('storage/' . $blog->user->organizer->logo) }}"
                             alt="{{ $blog->user->organizer->company_name }}">
                    @else
                        <div class="threads-avatar-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>

                <!-- Infos auteur -->
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h6 class="threads-author-name-large mb-0">
                            @if($blog->user && $blog->user->organizer)
                                {{ $blog->user->organizer->company_name }}
                            @else
                                {{ $blog->user ? $blog->user->name : 'Utilisateur inconnu' }}
                            @endif
                        </h6>
                        @if($blog->user && $blog->user->organizer && $blog->user->organizer->is_verified)
                            <i class="fas fa-check-circle" style="color: var(--blanc-or);"></i>
                        @endif
                    </div>
                    <div class="threads-date-full">{{ $blog->created_at->format('d M Y') }} · {{ $blog->created_at->diffForHumans() }}</div>
                </div>
            </div>

            <!-- Titre -->
            <h1 class="threads-title-full">{{ $blog->title }}</h1>

            <!-- Catégorie -->
            @if($blog->category)
                <div class="threads-category mb-3">
                    <span class="badge" style="background: var(--blanc-or); color: var(--bleu-nuit);">
                        {{ $blog->category->name }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Image principale -->
        @if($blog->image)
            <div class="threads-image-full mb-4">
                <img src="{{ asset('storage/' . $blog->image) }}" 
                     alt="{{ $blog->title }}">
            </div>
        @endif

        <!-- Contenu complet -->
        <div class="threads-content-full">
            {!! sanitize_html($blog->content) !!}
        </div>

        <!-- Stats et actions -->
        <div class="threads-stats-actions">
            <div class="threads-stats">
                <span><i class="fas fa-heart" style="color: #e0245e;"></i> {{ $blog->likes_count ?? 0 }} j'aime</span>
                <span><i class="far fa-comment"></i> {{ $blog->comments->count() }} commentaires</span>
            </div>

            <div class="threads-actions-full">
                <!-- Like -->
                <button class="threads-action-btn-large like-btn" data-blog-id="{{ $blog->id }}">
                    <i class="{{ auth()->check() && $blog->isLikedBy(auth()->user()) ? 'fas' : 'far' }} fa-heart"></i>
                </button>

                <!-- Partager -->
                <button class="threads-action-btn-large share-btn" data-blog-id="{{ $blog->id }}">
                    <i class="fas fa-share"></i>
                </button>
            </div>
        </div>

        <!-- Section commentaires -->
        <div class="threads-comments-section">
            <h4 class="threads-comments-title">
                <i class="far fa-comment me-2"></i>Commentaires ({{ $blog->comments()->whereNull('parent_id')->count() }})
            </h4>

            @auth
                <!-- Formulaire de commentaire -->
                <form action="{{ route('blogs.comments.store', $blog) }}" method="POST" class="threads-comment-form mb-4">
                    @csrf
                    <div class="d-flex gap-3">
                        <div class="threads-comment-avatar">
                            @if(auth()->user()->organizer && auth()->user()->organizer->logo)
                                <img src="{{ asset('storage/' . auth()->user()->organizer->logo) }}" alt="{{ auth()->user()->name }}">
                            @else
                                <div class="threads-avatar-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <textarea class="threads-comment-input" 
                                      name="content" 
                                      rows="3" 
                                      placeholder="Écrire un commentaire..."
                                      required></textarea>
                            <div class="text-end mt-2">
                                <button type="submit" class="threads-comment-submit">
                                    <i class="fas fa-paper-plane me-2"></i>Publier
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="threads-login-prompt">
                    <i class="fas fa-lock me-2"></i>
                    <a href="{{ route('login') }}">Connectez-vous</a> pour laisser un commentaire
                </div>
            @endauth

            <!-- Liste des commentaires -->
            <div class="threads-comments-list">
                @forelse($blog->comments as $comment)
                    <div class="threads-comment-item {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}" data-comment-id="{{ $comment->id }}">
                        <div class="d-flex gap-3">
                            <div class="threads-comment-avatar">
                                @if($comment->user->organizer && $comment->user->organizer->logo)
                                    <img src="{{ asset('storage/' . $comment->user->organizer->logo) }}" alt="{{ $comment->user->name }}">
                                @else
                                    <div class="threads-avatar-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <span class="threads-comment-author">{{ $comment->user->name }}</span>
                                        <span class="threads-comment-date ms-2">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    @auth
                                    @if($comment->user_id === auth()->id())
                                    <button class="btn btn-sm btn-link text-danger p-0 delete-comment-btn" 
                                            data-comment-id="{{ $comment->id }}">
                                        <i class="fas fa-trash fa-xs"></i>
                                    </button>
                                    @endif
                                    @endauth
                                </div>
                                <p class="threads-comment-text mb-2">{{ $comment->content }}</p>
                                
                                <!-- Action buttons -->
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <!-- Reply Toggle Button -->
                                    @if($comment->replies && $comment->replies->count() > 0)
                                    <button class="btn btn-sm btn-link toggle-replies-btn text-primary" 
                                            data-comment-id="{{ $comment->id }}"
                                            style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                        <i class="fas fa-comments me-1"></i>
                                        <span class="replies-count">{{ $comment->replies->count() }}</span> réponse{{ $comment->replies->count() > 1 ? 's' : '' }}
                                    </button>
                                    @endif
                                    
                                    <!-- Reply Button (for users to reply) -->
                                    @auth
                                    <button class="btn btn-sm btn-link toggle-reply-form-btn text-primary" 
                                            data-comment-id="{{ $comment->id }}"
                                            style="padding: 0.25rem 0.5rem; font-size: 0.875rem; font-weight: 500;">
                                        <i class="fas fa-reply me-1"></i>
                                        Répondre
                                    </button>
                                    @endauth
                                </div>
                            </div>
                        </div>

                        <!-- Reply Form (Hidden by default) -->
                        <div class="reply-form-container" id="reply-form-{{ $comment->id }}" style="display: none;">
                            <div class="ms-5 mt-2">
                                @auth
                                <form class="reply-form mb-2" data-parent-id="{{ $comment->id }}" 
                                      action="{{ route('blogs.comment', $blog) }}">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="d-flex gap-2">
                                        <div class="threads-comment-avatar" style="width: 28px; height: 28px;">
                                            @if(auth()->user()->organizer && auth()->user()->organizer->logo)
                                                <img src="{{ asset('storage/' . auth()->user()->organizer->logo) }}" alt="{{ auth()->user()->name }}">
                                            @else
                                                <div class="threads-avatar-placeholder">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="text" 
                                               name="content" 
                                               class="form-control form-control-sm" 
                                               placeholder="Répondre..." 
                                               required>
                                        <button type="submit" class="btn btn-sm btn-primary" style="background-color: var(--bleu-nuit); border: none;">
                                            <i class="fas fa-paper-plane" style="color: white;"></i>
                                        </button>
                                    </div>
                                </form>
                                @endauth
                            </div>
                        </div>

                        <!-- Replies (Hidden by default) -->
                        <div class="replies-container" id="replies-{{ $comment->id }}" style="display: none;">
                            <div class="ms-5 mt-2">
                                <!-- Replies List -->
                                <div class="replies-list">
                                    @foreach($comment->replies as $reply)
                                    <div class="reply-item mb-2" data-comment-id="{{ $reply->id }}">
                                        <div class="d-flex gap-2">
                                            <div class="threads-comment-avatar" style="width: 28px; height: 28px;">
                                                @if($reply->user->organizer && $reply->user->organizer->logo)
                                                    <img src="{{ asset('storage/' . $reply->user->organizer->logo) }}" alt="{{ $reply->user->name }}">
                                                @else
                                                    <div class="threads-avatar-placeholder">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div>
                                                        <span class="threads-comment-author" style="font-size: 0.85rem;">{{ $reply->user->name }}</span>
                                                        <span class="threads-comment-date ms-2" style="font-size: 0.75rem;">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    @auth
                                                    @if($reply->user_id === auth()->id())
                                                    <button class="btn btn-sm btn-link text-danger p-0 delete-reply-btn" 
                                                            data-comment-id="{{ $reply->id }}"
                                                            data-parent-id="{{ $comment->id }}">
                                                        <i class="fas fa-trash fa-xs"></i>
                                                    </button>
                                                    @endif
                                                    @endauth
                                                </div>
                                                <p class="threads-comment-text mb-0" style="font-size: 0.9rem;">{{ $reply->content }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="threads-no-comments">
                        <i class="far fa-comments mb-2" style="font-size: 2rem; color: #ccc;"></i>
                        <p class="text-muted mb-0">Aucun commentaire pour le moment. Soyez le premier à réagir !</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Articles similaires -->
        @if(isset($similarBlogs) && $similarBlogs->count() > 0)
            <div class="threads-similar-section">
                <h4 class="threads-similar-title">Articles similaires</h4>
                <div class="threads-similar-grid">
                    @foreach($similarBlogs as $relatedBlog)
                        <a href="{{ route('blogs.show', $relatedBlog) }}" class="threads-similar-card">
                            @if($relatedBlog->image)
                                <img src="{{ asset('storage/' . $relatedBlog->image) }}" alt="{{ $relatedBlog->title }}">
                            @else
                                <div class="threads-similar-placeholder">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                            @endif
                            <div class="threads-similar-content">
                                <h6>{{ Str::limit($relatedBlog->title, 60) }}</h6>
                                <small>{{ $relatedBlog->created_at->format('d M Y') }}</small>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>
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

.threads-back-btn {
    display: inline-flex;
    align-items: center;
    color: var(--bleu-nuit);
    text-decoration: none;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    transition: all 0.2s ease;
}

.threads-back-btn:hover {
    background-color: #f0f0f0;
    color: var(--bleu-nuit);
}

.threads-post-full {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.threads-avatar-large {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid var(--blanc-or);
}

.threads-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.threads-avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--bleu-nuit), var(--bleu-nuit-clair));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.threads-author-name-large {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--bleu-nuit);
}

.threads-date-full {
    font-size: 0.875rem;
    color: #999;
}

.threads-title-full {
    font-size: 2rem;
    font-weight: 800;
    color: var(--bleu-nuit);
    line-height: 1.3;
    margin-bottom: 1rem;
}

.threads-image-full {
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e8e8e8;
}

.threads-image-full img {
    width: 100%;
    height: auto;
    display: block;
}

.threads-content-full {
    font-size: 1.0625rem;
    line-height: 1.8;
    color: #1a1a1a;
    margin-bottom: 2rem;
}

.threads-content-full h1,
.threads-content-full h2,
.threads-content-full h3 {
    color: var(--bleu-nuit);
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.threads-content-full img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 1.5rem 0;
}

.threads-content-full p {
    margin-bottom: 1rem;
}

.threads-stats-actions {
    padding: 1.5rem 0;
    border-top: 1px solid #e8e8e8;
    border-bottom: 1px solid #e8e8e8;
    margin-bottom: 2rem;
}

.threads-stats {
    display: flex;
    gap: 2rem;
    margin-bottom: 1rem;
    color: #666;
    font-size: 0.9375rem;
}

.threads-stats span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.threads-actions-full {
    display: flex;
    gap: 1rem;
}

.threads-action-btn-large {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: none;
    border: 1px solid #e8e8e8;
    border-radius: 50%;
    color: #666;
    font-size: 1.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.threads-action-btn-large i {
    transition: transform 0.2s ease, color 0.2s ease;
}

.threads-action-btn-large:hover {
    background-color: #f9f9f9;
    border-color: var(--blanc-or);
    color: var(--bleu-nuit);
}

.threads-action-btn-large.like-btn i.fas {
    color: #e0245e;
}

.threads-comments-section {
    margin-top: 2rem;
}

.threads-comments-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 1.5rem;
}

.threads-comment-form {
    padding: 1.5rem;
    background: #f9f9f9;
    border-radius: 12px;
}

.threads-comment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid var(--blanc-or);
}

.threads-comment-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.threads-comment-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    font-size: 0.9375rem;
    resize: vertical;
    transition: all 0.2s ease;
}

.threads-comment-input:focus {
    outline: none;
    border-color: var(--blanc-or);
    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.threads-comment-submit {
    padding: 0.625rem 1.5rem;
    background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-fonce));
    color: var(--bleu-nuit);
    border: none;
    border-radius: 20px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.threads-comment-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

.threads-login-prompt {
    padding: 1.5rem;
    background: #f9f9f9;
    border-radius: 12px;
    text-align: center;
    color: #666;
    margin-bottom: 2rem;
}

.threads-login-prompt a {
    color: var(--bleu-nuit);
    font-weight: 600;
    text-decoration: none;
}

.threads-login-prompt a:hover {
    color: var(--blanc-or);
}

.threads-comments-list {
    margin-top: 1.5rem;
}

.threads-comment-item {
    padding: 1.5rem 0;
    border-bottom: 1px solid #f0f0f0;
    transition: opacity 0.3s ease;
}

.threads-comment-item:last-child {
    border-bottom: none;
}

.threads-comment-item.temp-comment {
    opacity: 0.6;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 0.6;
        transform: translateY(0);
    }
}

.threads-comment-author {
    font-weight: 700;
    color: var(--bleu-nuit);
    font-size: 0.9375rem;
}

.threads-comment-date {
    font-size: 0.8125rem;
    color: #999;
}

.threads-comment-text {
    margin: 0.5rem 0 0 0;
    color: #1a1a1a;
    line-height: 1.6;
}

/* Styles pour les réponses - identique à la page d'événement */
.replies-container {
    border-left: 3px solid var(--bleu-nuit-clair);
    padding-left: 1rem;
    margin-top: 1rem;
}

.reply-item {
    background-color: #f8f9fa;
    padding: 0.75rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.reply-item:hover {
    background-color: #e9ecef;
}

.toggle-replies-btn,
.toggle-reply-form-btn {
    font-size: 0.875rem !important;
    color: var(--bleu-nuit) !important;
    text-decoration: none !important;
    padding: 0.25rem 0.5rem !important;
    margin-right: 0.5rem;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.toggle-replies-btn:hover,
.toggle-reply-form-btn:hover {
    color: var(--bleu-nuit-clair) !important;
    background-color: rgba(153, 27, 27, 0.1);
    text-decoration: none !important;
}

.reply-form input[type="text"] {
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 0.5rem 1rem;
}

.reply-form input[type="text"]:focus {
    border-color: var(--bleu-nuit);
    box-shadow: 0 0 0 0.2rem rgba(153, 27, 27, 0.25);
}

.replies-count {
    font-weight: 600;
}

.threads-no-comments {
    text-align: center;
    padding: 3rem 1rem;
}

.threads-similar-section {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 2px solid #f0f0f0;
}

.threads-similar-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 1.5rem;
}

.threads-similar-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.threads-similar-card {
    display: block;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e8e8e8;
    text-decoration: none;
    transition: all 0.2s ease;
}

.threads-similar-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.threads-similar-card img {
    width: 100%;
    height: 140px;
    object-fit: cover;
}

.threads-similar-placeholder {
    width: 100%;
    height: 140px;
    background: linear-gradient(135deg, var(--bleu-nuit), var(--bleu-nuit-clair));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.threads-similar-content {
    padding: 1rem;
}

.threads-similar-content h6 {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--bleu-nuit);
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.threads-similar-content small {
    color: #999;
    font-size: 0.8125rem;
}

/* Responsive */
@media (max-width: 768px) {
    .threads-post-full {
        padding: 1.5rem;
    }

    .threads-title-full {
        font-size: 1.5rem;
    }

    .threads-content-full {
        font-size: 1rem;
    }

    .threads-similar-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du like - instantané comme Facebook
    const likeBtn = document.querySelector('.like-btn');
    if (likeBtn) {
        likeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const blogId = this.dataset.blogId;
            const icon = this.querySelector('i');
            const statsLikes = document.querySelector('.threads-stats span:first-child');
            
            // Animation immédiate (optimistic UI)
            const wasLiked = icon.classList.contains('fas');
            const currentCount = parseInt(statsLikes.textContent.match(/\d+/)[0]) || 0;
            
            // Mise à jour immédiate de l'interface
            if (wasLiked) {
                icon.classList.remove('fas');
                icon.classList.add('far');
                statsLikes.innerHTML = `<i class="far fa-heart"></i> ${Math.max(0, currentCount - 1)} j'aime`;
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
                statsLikes.innerHTML = `<i class="fas fa-heart" style="color: #e0245e;"></i> ${currentCount + 1} j'aime`;
                // Animation de pulsation
                icon.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    icon.style.transform = 'scale(1)';
                }, 200);
            }

            // Envoyer la requête en arrière-plan
            fetch(`/blogs/${blogId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Synchroniser avec le serveur
                    const likedClass = data.action === 'liked' ? 'fas' : 'far';
                    const heartColor = data.action === 'liked' ? 'style="color: #e0245e;"' : '';
                    statsLikes.innerHTML = `<i class="${likedClass} fa-heart" ${heartColor}></i> ${data.likes_count} j'aime`;
                }
            })
            .catch(error => {
                // En cas d'erreur, annuler les changements
                console.error('Error:', error);
                if (wasLiked) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    statsLikes.innerHTML = `<i class="fas fa-heart" style="color: #e0245e;"></i> ${currentCount} j'aime`;
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    statsLikes.innerHTML = `<i class="far fa-heart"></i> ${currentCount} j'aime`;
                }
            });
        });
    }

    // Gestion des commentaires - instantané
    const commentForm = document.querySelector('.threads-comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const blogId = '{{ $blog->id }}';
            const textarea = this.querySelector('textarea[name="content"]');
            const content = textarea.value.trim();
            
            if (!content) return;
            
            // Créer l'élément commentaire temporaire immédiatement
            const commentsList = document.querySelector('.threads-comments-list');
            const noComments = document.querySelector('.threads-no-comments');
            
            if (noComments) {
                noComments.remove();
            }
            
            const tempComment = createCommentElement({
                user: {
                    name: '{{ auth()->check() ? auth()->user()->name : "Utilisateur" }}',
                    logo: '{{ auth()->check() && auth()->user()->organizer && auth()->user()->organizer->logo ? asset("storage/" . auth()->user()->organizer->logo) : "" }}'
                },
                content: content,
                created_at: 'À l\'instant',
                isTemp: true
            });
            
            commentsList.insertBefore(tempComment, commentsList.firstChild);
            textarea.value = '';
            
            // Mettre à jour le compteur
            const commentsTitle = document.querySelector('.threads-comments-title');
            const currentCount = parseInt(commentsTitle.textContent.match(/\d+/)[0]) || 0;
            commentsTitle.innerHTML = `<i class="far fa-comment me-2"></i>Commentaires (${currentCount + 1})`;
            
            // Mettre à jour les stats
            const statsComments = document.querySelector('.threads-stats span:nth-child(2)');
            if (statsComments) {
                statsComments.innerHTML = `<i class="far fa-comment"></i> ${currentCount + 1} commentaires`;
            }
            
            // Envoyer la requête en arrière-plan
            fetch(`/blogs/${blogId}/comment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content: content })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remplacer le commentaire temporaire par le vrai
                    tempComment.classList.remove('temp-comment');
                    // Mettre à jour avec les vraies données
                    const dateSpan = tempComment.querySelector('.threads-comment-date');
                    if (dateSpan) {
                        dateSpan.textContent = 'À l\'instant';
                    }
                    
                    // Synchroniser le compteur avec le serveur
                    commentsTitle.innerHTML = `<i class="far fa-comment me-2"></i>Commentaires (${data.commentsCount})`;
                    if (statsComments) {
                        statsComments.innerHTML = `<i class="far fa-comment"></i> ${data.commentsCount} commentaires`;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // En cas d'erreur, retirer le commentaire temporaire
                tempComment.remove();
                commentsTitle.innerHTML = `<i class="far fa-comment me-2"></i>Commentaires (${currentCount})`;
                if (statsComments) {
                    statsComments.innerHTML = `<i class="far fa-comment"></i> ${currentCount} commentaires`;
                }
                textarea.value = content;
            });
        });
    }

    // Gestion des réponses aux commentaires - identique à la page d'événement
    // Toggle reply form
    document.querySelectorAll('.toggle-reply-form-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const commentId = this.dataset.commentId;
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            
            if (replyForm) {
                const isVisible = replyForm.style.display !== 'none';
                replyForm.style.display = isVisible ? 'none' : 'block';
                
                // Focus sur l'input si on ouvre le formulaire
                if (!isVisible) {
                    setTimeout(() => {
                        const input = replyForm.querySelector('input[name="content"]');
                        if (input) input.focus();
                    }, 100);
                }
            }
        });
    });

    // Toggle replies display
    document.querySelectorAll('.toggle-replies-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const commentId = this.dataset.commentId;
            const repliesContainer = document.getElementById(`replies-${commentId}`);
            
            if (repliesContainer) {
                const isVisible = repliesContainer.style.display !== 'none';
                repliesContainer.style.display = isVisible ? 'none' : 'block';
            }
        });
    });

    // Soumission des réponses
    document.querySelectorAll('.reply-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const blogId = '{{ $blog->id }}';
            const parentId = this.dataset.parentId;
            const input = this.querySelector('input[name="content"]');
            const content = input.value.trim();
            
            if (!content) return;
            
            const repliesList = this.closest('.reply-form-container').nextElementSibling?.querySelector('.replies-list');
            const parentComment = this.closest('.threads-comment-item');
            
            // Créer la réponse temporaire
            const tempReply = createReplyElement({
                user: {
                    name: '{{ auth()->check() ? auth()->user()->name : "Utilisateur" }}',
                    logo: '{{ auth()->check() && auth()->user()->organizer && auth()->user()->organizer->logo ? asset("storage/" . auth()->user()->organizer->logo) : "" }}'
                },
                content: content,
                created_at: 'À l\'instant'
            });
            
            if (repliesList) {
                repliesList.appendChild(tempReply);
                const repliesContainer = document.getElementById(`replies-${parentId}`);
                if (repliesContainer) {
                    repliesContainer.style.display = 'block';
                }
                
                // Mettre à jour le bouton toggle replies
                const toggleBtn = parentComment.querySelector('.toggle-replies-btn');
                if (toggleBtn) {
                    const countSpan = toggleBtn.querySelector('.replies-count');
                    const currentCount = parseInt(countSpan.textContent) || 0;
                    countSpan.textContent = currentCount + 1;
                    const text = countSpan.textContent == 1 ? 'réponse' : 'réponses';
                    toggleBtn.innerHTML = `<i class="fas fa-comments me-1"></i><span class="replies-count">${currentCount + 1}</span> ${text}`;
                }
            }
            
            input.value = '';
            this.closest('.reply-form-container').style.display = 'none';
            
            // Envoyer la requête
            fetch(`/blogs/${blogId}/comment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    content: content,
                    parent_id: parentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour la réponse avec les vraies données
                    const dateSpan = tempReply.querySelector('.threads-comment-date');
                    if (dateSpan) {
                        dateSpan.textContent = 'À l\'instant';
                    }
                    tempReply.setAttribute('data-comment-id', data.comment.id);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tempReply.remove();
                input.value = content;
            });
        });
    });
    
    // Fonction helper pour créer un élément commentaire
    function createCommentElement(comment) {
        const div = document.createElement('div');
        div.className = 'threads-comment-item' + (comment.isTemp ? ' temp-comment' : '');
        div.innerHTML = `
            <div class="d-flex gap-3">
                <div class="threads-comment-avatar">
                    ${comment.user.logo ? 
                        `<img src="${comment.user.logo}" alt="${comment.user.name}">` :
                        `<div class="threads-avatar-placeholder"><i class="fas fa-user"></i></div>`
                    }
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="threads-comment-author">${comment.user.name}</span>
                        <span class="threads-comment-date">${comment.created_at}</span>
                    </div>
                    <p class="threads-comment-text">${comment.content}</p>
                </div>
            </div>
        `;
        return div;
    }

    // Fonction helper pour créer un élément réponse
    function createReplyElement(reply) {
        const div = document.createElement('div');
        div.className = 'reply-item mb-2';
        div.innerHTML = `
            <div class="d-flex gap-2">
                <div class="threads-comment-avatar" style="width: 28px; height: 28px;">
                    ${reply.user.logo ? 
                        `<img src="${reply.user.logo}" alt="${reply.user.name}">` :
                        `<div class="threads-avatar-placeholder"><i class="fas fa-user"></i></div>`
                    }
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <span class="threads-comment-author" style="font-size: 0.85rem;">${reply.user.name}</span>
                            <span class="threads-comment-date ms-2" style="font-size: 0.75rem;">${reply.created_at}</span>
                        </div>
                    </div>
                    <p class="threads-comment-text mb-0" style="font-size: 0.9rem;">${reply.content}</p>
                </div>
            </div>
        `;
        return div;
    }

    // Gestion du partage
    const shareBtn = document.querySelector('.share-btn');
    if (shareBtn) {
        shareBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const blogId = this.dataset.blogId;
            const url = window.location.href;

            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    text: 'Découvrez cet article',
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    const originalIcon = this.querySelector('i').className;
                    this.querySelector('i').className = 'fas fa-check';
                    setTimeout(() => {
                        this.querySelector('i').className = originalIcon;
                    }, 2000);
                });
            }
        });
    }
});
</script>
@endpush
