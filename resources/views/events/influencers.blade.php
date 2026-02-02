@extends('layouts.app')

@section('title', "Influenceurs présents - " . $event->title)

@section('content')
<div class="container py-4">
    <h2 class="mb-3">Influenceurs qui seront présents</h2>
    <p class="text-muted mb-4">{{ $event->title }}</p>

    <div class="row g-3">
        @forelse($attendees as $u)
            @php
                $img = $u->profil_image;
                $url = $img ? (str_starts_with($img,'http') ? $img : asset('storage/'.$img)) : 'https://ui-avatars.com/api/?name='.urlencode(($u->prenom.' '.$u->nom));
            @endphp
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <a href="{{ route('influencers.show', $u->id) }}" class="me-3">
                                <img src="{{ $url }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;" alt="{{ $u->prenom }}">
                            </a>
                            <div>
                                <a href="{{ route('influencers.show', $u->id) }}" class="fw-semibold text-decoration-none">{{ $u->prenom }} {{ $u->nom }}</a>
                                <div class="small text-muted">Influenceur</div>
                            </div>
                        </div>
                        <div class="mt-auto">
                            @auth
                                <form method="POST" action="{{ route('influencers.toggleFollow', $u->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        Suivre / Se désabonner
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Se connecter pour suivre</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Aucun influenceur n'a encore indiqué sa présence.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection


