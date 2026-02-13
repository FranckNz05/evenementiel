@extends('layouts.dashboard')

@section('title', 'Créer un article')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="fas fa-pen me-2 text-primary"></i>
            Créer un nouvel article
        </h5>
        <a href="{{ route('organizer.blogs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    <form action="{{ route('organizer.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="title" class="form-label fw-semibold">Titre de l'article <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="Entrez un titre accrocheur">
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="content" class="form-label fw-semibold">Contenu <span class="text-danger">*</span></label>
            <textarea name="content" id="content" rows="10" class="form-control @error('content') is-invalid @enderror" required placeholder="Rédigez votre article...">{{ old('content') }}</textarea>
            @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="image" class="form-label fw-semibold">Image de couverture <span class="text-danger">*</span></label>
            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
            <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 2MB</div>
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between gap-2">
            <a href="{{ route('organizer.blogs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i>Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Créer l'article
            </button>
        </div>
    </form>
</div>
@endsection
