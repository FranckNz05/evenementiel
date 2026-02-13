@extends('layouts.dashboard')

@section('title', 'Modifier utilisateur')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Modifier l'utilisateur</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            Édition de {{ $user->name }}
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Rôles</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="Administrateur" id="roleAdmin" 
                            {{ $user->hasRole(3) ? 'checked' : '' }}>
                        <label class="form-check-label" for="roleAdmin">
                            Administrateur
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="organizer" id="roleOrganizer"
                            {{ $user->hasRole(2) ? 'checked' : '' }}>
                        <label class="form-check-label" for="roleOrganizer">
                            Organisateur
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="client" id="roleClient"
                            {{ $user->hasRole(1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="roleClient">
                            Client
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" 
                            {{ $user->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">
                            Compte actif
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">Retour</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
