<article class="threads-post">
    <!-- En-tête avec avatar et infos auteur -->
    <div class="threads-header">
        <div class="d-flex align-items-start gap-3">
            <!-- Avatar -->
            <div class="threads-avatar">
                @if($blog->user && $blog->user->organizer && $blog->user->organizer->logo)
                    <img src="{{ asset('storage/' . $blog->user->organizer->logo) }}"
                         alt="{{ $blog->user->organizer->company_name }}">
                @else
                    <div class="threads-avatar-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
            </div>

            <!-- Infos auteur et contenu -->
            <div class="flex-grow-1">
                <!-- Nom et date -->
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h6 class="threads-author-name mb-0">
                        @if($blog->user && $blog->user->organizer)
                            {{ $blog->user->organizer->company_name }}
                        @else
                            {{ $blog->user ? $blog->user->name : 'Utilisateur inconnu' }}
                        @endif
                    </h6>
                    @if($blog->user && $blog->user->organizer && $blog->user->organizer->is_verified)
                        <i class="fas fa-check-circle" style="color: var(--blanc-or); font-size: 0.875rem;"></i>
                    @endif
                    <span class="threads-separator">·</span>
                    <span class="threads-date">{{ $blog->created_at->diffForHumans() }}</span>
                </div>

                <!-- Titre -->
                <h5 class="threads-title">{{ $blog->title }}</h5>

                <!-- Contenu avec voir plus -->
                <div class="threads-content">
                    @php
                        $content = strip_tags($blog->content);
                        $isLong = strlen($content) > 280;
                        $shortContent = Str::limit($content, 280, '');
                    @endphp
                    
                    <p class="threads-text mb-0" id="content-{{ $blog->id }}">
                        <span class="short-content">{{ $shortContent }}</span>
                        @if($isLong)
                            <span class="full-content" style="display: none;">{{ $content }}</span>
                            <button class="threads-read-more" onclick="toggleContent({{ $blog->id }})">
                                ... <span class="see-more-text">voir plus</span><span class="see-less-text" style="display: none;">voir moins</span>
                            </button>
                        @endif
                    </p>
                </div>

                <!-- Image si existe -->
                @if($blog->image)
                    <div class="threads-image mt-3">
                        <img src="{{ asset('storage/' . $blog->image) }}" 
                             alt="{{ $blog->title }}"
                             onclick="window.location.href='{{ route('blogs.show', $blog->id) }}'">
                    </div>
                @endif

                <!-- Actions -->
                <div class="threads-actions">
                    <!-- Like -->
                    <button class="threads-action-btn like-btn" data-blog-id="{{ $blog->id }}">
                        <i class="{{ auth()->check() && $blog->isLikedBy(auth()->user()) ? 'fas' : 'far' }} fa-heart"></i>
                        <span class="likes-count">{{ $blog->likes_count ?? 0 }}</span>
                    </button>

                    <!-- Commentaires -->
                    <a href="{{ route('blogs.show', $blog->id) }}" class="threads-action-btn">
                        <i class="far fa-comment"></i>
                        <span>{{ $blog->comments_count ?? 0 }}</span>
                    </a>

                    <!-- Partager -->
                    <button class="threads-action-btn share-btn" data-blog-id="{{ $blog->id }}">
                        <i class="fas fa-share"></i>
                    </button>

                    <!-- Lien vers l'article complet -->
                    <a href="{{ route('blogs.show', $blog->id) }}" class="threads-action-btn ms-auto">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</article>

@once
@push('styles')
<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffffff;
    --blanc-or-fonce: #e6b800;
}

.threads-post {
    padding: 1.5rem 0;
    border-bottom: 1px solid #e8e8e8;
    transition: background-color 0.2s ease;
}

.threads-post:hover {
    background-color: #f9f9f9;
    margin: 0 -1rem;
    padding-left: 1rem;
    padding-right: 1rem;
}

.threads-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid var(--blanc-or);
}

.threads-avatar img {
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
    font-size: 1.25rem;
}

.threads-author-name {
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--bleu-nuit);
}

.threads-separator {
    color: #999;
    font-size: 0.875rem;
}

.threads-date {
    font-size: 0.875rem;
    color: #999;
}

.threads-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--bleu-nuit);
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.threads-content {
    margin-bottom: 0.75rem;
}

.threads-text {
    font-size: 0.9375rem;
    line-height: 1.6;
    color: #1a1a1a;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.threads-read-more {
    background: none;
    border: none;
    color: var(--bleu-nuit);
    font-weight: 600;
    padding: 0;
    margin-left: 0.25rem;
    cursor: pointer;
    transition: color 0.2s ease;
    font-size: 0.9375rem;
}

.threads-read-more:hover {
    color: var(--blanc-or);
}

.threads-image {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e8e8e8;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.threads-image:hover {
    transform: scale(0.99);
}

.threads-image img {
    width: 100%;
    height: auto;
    max-height: 500px;
    object-fit: cover;
    display: block;
}

.threads-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
}

.threads-action-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: none;
    border: none;
    color: #999;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.threads-action-btn:hover {
    background-color: #f0f0f0;
    color: var(--bleu-nuit);
}

.threads-action-btn i {
    font-size: 1.125rem;
    transition: transform 0.2s ease, color 0.2s ease;
}

.threads-action-btn.like-btn:hover i {
    color: #e0245e;
}

.threads-action-btn.like-btn i.fas {
    color: #e0245e;
}

.threads-action-btn span {
    font-weight: 600;
}

/* États vides */
.threads-empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

/* Responsive */
@media (max-width: 576px) {
    .threads-post {
        padding: 1rem 0;
    }

    .threads-avatar {
        width: 40px;
        height: 40px;
    }

    .threads-title {
        font-size: 1rem;
    }

    .threads-text {
        font-size: 0.875rem;
    }

    .threads-actions {
        gap: 0.5rem;
    }

    .threads-action-btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.8125rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleContent(blogId) {
    const container = document.getElementById('content-' + blogId);
    const shortContent = container.querySelector('.short-content');
    const fullContent = container.querySelector('.full-content');
    const button = container.querySelector('.threads-read-more');
    const seeMoreText = button.querySelector('.see-more-text');
    const seeLessText = button.querySelector('.see-less-text');
    
    if (fullContent.style.display === 'none') {
        shortContent.style.display = 'none';
        fullContent.style.display = 'inline';
        seeMoreText.style.display = 'none';
        seeLessText.style.display = 'inline';
    } else {
        shortContent.style.display = 'inline';
        fullContent.style.display = 'none';
        seeMoreText.style.display = 'inline';
        seeLessText.style.display = 'none';
    }
}

// Gestion des likes - instantané comme Facebook
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const blogId = this.dataset.blogId;
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.likes-count');
            
            // Animation immédiate (optimistic UI)
            const wasLiked = icon.classList.contains('fas');
            const currentCount = parseInt(countSpan.textContent) || 0;
            
            // Mise à jour immédiate de l'interface
            if (wasLiked) {
                icon.classList.remove('fas');
                icon.classList.add('far');
                countSpan.textContent = Math.max(0, currentCount - 1);
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
                countSpan.textContent = currentCount + 1;
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
                    countSpan.textContent = data.likes_count;
                }
            })
            .catch(error => {
                // En cas d'erreur, annuler les changements
                console.error('Error:', error);
                if (wasLiked) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    countSpan.textContent = currentCount;
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    countSpan.textContent = currentCount;
                }
            });
        });
    });

    // Gestion du partage
    document.querySelectorAll('.share-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const blogId = this.dataset.blogId;
            const url = `${window.location.origin}/blogs/${blogId}`;

            if (navigator.share) {
                navigator.share({
                    title: 'Article MokiliEvent',
                    text: 'Découvrez cet article intéressant',
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    // Animation de confirmation
                    const originalIcon = this.querySelector('i').className;
                    this.querySelector('i').className = 'fas fa-check';
                    setTimeout(() => {
                        this.querySelector('i').className = originalIcon;
                    }, 2000);
                });
            }
        });
    });
});
</script>
@endpush
@endonce

