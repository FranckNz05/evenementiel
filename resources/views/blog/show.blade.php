@extends('layouts.app')

@section('content')
<!-- Page Header Start -->
<div class="container-fluid page-header mb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <h1 class="display-3 mb-3 animated slideInDown">{{ $blog->title }}</h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($blog->title, 30) }}</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<!-- Blog Detail Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                <!-- Article Content -->
                <div class="mb-5">
                    @if($blog->image_path)
                        <img class="img-fluid w-100 rounded mb-5" src="{{ Storage::url($blog->image_path) }}" alt="{{ $blog->title }}">
                    @endif
                    
                    <div class="d-flex mb-3">
                        <small class="me-3"><i class="far fa-user text-primary me-2"></i>{{ $blog->user->organizer->company_name ?? 'Auteur inconnu' }}</small>
                        <small class="me-3"><i class="far fa-calendar-alt text-primary me-2"></i>{{ $blog->created_at->format('d M Y') }}</small>
                        <small><i class="far fa-eye text-primary me-2"></i>{{ $blog->views }} Vues</small>
                    </div>

                    @if($blog->event)
                        <div class="mb-4">
                            <h6>Événement associé :</h6>
                            <a href="{{ route('events.show', $blog->event) }}" class="text-primary">
                                {{ $blog->event->title }}
                            </a>
                        </div>
                    @endif

                    <div class="blog-content">
                        {!! sanitize_html($blog->content) !!}
                    </div>

                    @auth
                        @if(auth()->user()->id === $blog->user_id || auth()->user()->hasRole(3))
                            <div class="mt-4">
                                <a href="{{ route('blog.edit', $blog) }}" class="btn btn-primary me-2">Modifier</a>
                                <form action="{{ route('blog.destroy', $blog) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>

                <!-- Comments Section -->
                <div class="mb-5">
                    <h4 class="mb-4">Commentaires</h4>
                    @auth
                        <form action="{{ route('comments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="blog_id" value="{{ $blog->id }}">
                            <div class="form-group mb-3">
                                <textarea class="form-control" name="content" rows="3" placeholder="Votre commentaire..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Commenter</button>
                        </form>
                    @else
                        <p>Veuillez vous <a href="{{ route('login') }}">connecter</a> pour laisser un commentaire.</p>
                    @endauth

                    <div class="comments mt-4">
                        @forelse($blog->comments as $comment)
                            <div class="comment border-bottom pb-4 mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="me-2 mb-0">{{ $comment->user->name }}</h6>
                                    @php($badge = $comment->user->badge_type ?? ($comment->user->badgeType ?? null))
                                    @if(($comment->user->isOrganizer() ?? false))
                                        <span class="ms-1" title="Organisateur" style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;background:#ffffff;"></span>
                                    @elseif(($comment->user->isInfluencer() ?? false))
                                        <span class="ms-1" title="Influenceur" style="color:#ffffff;">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    @endif
                                    <small><i class="far fa-calendar-alt text-primary me-2"></i>{{ $comment->created_at->format('d M Y') }}</small>
                                </div>
                                <p class="mb-0">{{ $comment->content }}</p>
                            </div>
                        @empty
                            <p>Aucun commentaire pour le moment.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Author Info -->
                <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                    <div class="section-title section-title-sm position-relative pb-3 mb-4">
                        <h3 class="mb-0">À propos de l'auteur</h3>
                    </div>
                    <div class="d-flex mb-3">
                        <img class="img-fluid rounded" src="{{ asset('images/default-avatar.jpg') }}" style="width: 60px; height: 60px; object-fit: cover;" alt="Author">
                        <div class="ps-3">
                            <h6 class="text-primary mb-1">{{ $blog->user->name }}</h6>
                            <small>{{ $blog->user->hasRole(2) ? 'Organisateur' : 'Membre' }}</small>
                        </div>
                    </div>
                </div>

                <!-- Recent Posts -->
                @if(isset($recentPosts) && $recentPosts->count() > 0)
                    <div class="mb-5 wow slideInUp" data-wow-delay="0.1s">
                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                            <h3 class="mb-0">Articles récents</h3>
                        </div>
                        @foreach($recentPosts as $post)
                            <div class="d-flex rounded overflow-hidden mb-3">
                                <img class="img-fluid" src="{{ $post->image_path ? Storage::url($post->image_path) : asset('images/default-blog.jpg') }}" 
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="">
                                <a href="{{ route('blog.show', $post) }}" class="h5 fw-semi-bold d-flex align-items-center bg-light px-3 mb-0">
                                    {{ Str::limit($post->title, 50) }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Blog Detail End -->
@endsection
