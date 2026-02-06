@extends('layouts.dashboard')

@section('title', 'Modifier l\'Utilisateur')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Modifier l'Utilisateur</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
                    <li class="breadcrumb-item active">Édition</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light shadow-sm border">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card modern-card shadow-sm mb-4">
                <div class="card-header-modern">
                    <h5 class="card-title my-1 text-white">
                        <i class="fas fa-user-edit me-2"></i>Édition de : {{ $user->name }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <ul class="mb-0 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="name" class="form-label fw-bold text-dark">Nom complet</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold text-dark">Adresse Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label fw-bold text-dark">Numéro de téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+242 ...">
                            </div>
                        </div>

                        <div class="mb-4 p-3 bg-light rounded border">
                            <label class="form-label fw-bold text-primary border-bottom pb-2 w-100 mb-3 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05rem;">
                                <i class="fas fa-user-tag me-2"></i>Attribution des Rôles
                            </label>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="admin" id="roleAdmin" 
                                            {{ $user->hasRole('admin') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium text-dark" for="roleAdmin">Administrateur</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="organizer" id="roleOrganizer"
                                            {{ $user->hasRole('organizer') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium text-dark" for="roleOrganizer">Organisateur</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="client" id="roleClient"
                                            {{ $user->hasRole('client') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium text-dark" for="roleClient">Client</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch bg-white p-3 rounded border shadow-sm border-start border-4 {{ !$user->trashed() ? 'border-success' : 'border-danger' }}">
                                <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" name="is_active" id="isActive" value="1" 
                                    {{ !$user->trashed() ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold {{ !$user->trashed() ? 'text-success' : 'text-danger' }}" for="isActive">
                                    {{ !$user->trashed() ? 'Compte Actif' : 'Compte Suspendu / Inactif' }}
                                </label>
                                <p class="small text-muted mb-0 mt-1 ms-4 ps-2">Désactivez cette option pour suspendre l'accès de l'utilisateur à la plateforme.</p>
                            </div>
                        </div>

                        <div class="text-end pt-3">
                            <hr class="mb-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light border px-4 me-2">Annuler</a>
                            <button type="submit" class="btn btn-primary-modern shadow-sm px-5">
                                <i class="fas fa-save me-2"></i> Appliquer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
