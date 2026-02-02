@extends('layouts.app')

@section('title', $influencer->prenom . ' ' . $influencer->nom)

@section('content')
@php
    $img = $influencer->profil_image;
    $avatar = $img ? (str_starts_with($img,'http') ? $img : asset('storage/'.$img)) : 'https://ui-avatars.com/api/?name='.urlencode(($influencer->prenom.' '.$influencer->nom));
@endphp
<div class="container py-4">
    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img src="{{ $avatar }}" class="rounded-circle mb-3" style="width:128px;height:128px;object-fit:cover;" alt="Avatar">
                    <h3 class="mb-1">{{ $influencer->prenom }} {{ $influencer->nom }}</h3>
                    <div class="mb-3">
                        @if($influencer->hasRole(2))
                            <span class="badge rounded-circle" style="background:#ffffff;width:14px;height:14px;display:inline-block;" title="Organisateur"></span>
                        @endif
                        @if($influencer->is_influencer)
                            <span class="ms-2" style="color:#ffffff;" title="Influenceur"><i class="fas fa-star"></i></span>
                        @endif
                    </div>
                    @auth
                        @if(auth()->id() !== $influencer->id)
                        <form method="POST" action="{{ route('influencers.toggleFollow', $influencer->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">Suivre / Se désabonner</button>
                        </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Se connecter pour suivre</a>
                    @endauth
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Informations</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><strong>Email:</strong> {{ $influencer->email }}</div>
                        <div class="col-md-6"><strong>Téléphone:</strong> {{ $influencer->phone ?? '—' }}</div>
                        <div class="col-md-6"><strong>Ville:</strong> {{ $influencer->city ?? '—' }}</div>
                        <div class="col-md-6"><strong>Pays:</strong> {{ $influencer->country ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


