@extends('layouts.dashboard')

@section('title', 'Détail utilisateur')

@section('content')
<div class="container py-4">
    <div class="modern-card">
        <div class="card-header-modern">
            <h2 class="card-title">
                <i class="fas fa-user"></i>
                {{ $user->nom }} {{ $user->prenom }}
            </h2>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <img src="{{ $user->getProfilePhotoUrlAttribute() }}" alt="Avatar" class="img-fluid rounded" onerror="this.src='{{ asset('images/default-profile.png') }}'">
                </div>
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom</label>
                            <div class="form-control">{{ $user->nom }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom</label>
                            <div class="form-control">{{ $user->prenom }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <div class="form-control">{{ $user->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <div class="form-control">{{ $user->phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Genre</label>
                            <div class="form-control">{{ $user->genre ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rôle</label>
                            <div>
                                @if($user->hasRole(3))
                                    <span class="modern-badge badge-danger"><i class="fas fa-shield-alt"></i> Admin</span>
                                @elseif($user->hasRole(2))
                                    <span class="modern-badge badge-warning"><i class="fas fa-calendar-alt"></i> Organisateur</span>
                                @else
                                    <span class="modern-badge badge-info"><i class="fas fa-user"></i> Client</span>
                                @endif
                                @if($user->is_influencer)
                                    <span class="modern-badge badge-warning ms-2"><i class="fas fa-star"></i> Influenceur</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Statut email</label>
                            <div>
                                @if($user->email_verified_at)
                                    <span class="modern-badge badge-success"><i class="fas fa-check"></i> Vérifié</span>
                                @else
                                    <span class="modern-badge badge-warning"><i class="fas fa-clock"></i> Non vérifié</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Influenceur</label>
                            <div>
                                @if($user->is_influencer)
                                    <span class="modern-badge badge-success"><i class="fas fa-check"></i> Oui</span>
                                @elseif($user->influencer_requested)
                                    <span class="modern-badge badge-warning"><i class="fas fa-hourglass-half"></i> En attente</span>
                                @else
                                    <span class="modern-badge badge-secondary">Non</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Adresse</label>
                            <div class="form-control">{{ $user->address ?: '—' }}, {{ $user->city ?: '' }} {{ $user->country ?: '' }}</div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="modern-btn btn-warning-modern"><i class="fas fa-pen"></i> Modifier</a>
                        <a href="{{ route('admin.users.index') }}" class="modern-btn btn-secondary-modern"><i class="fas fa-arrow-left"></i> Retour</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


