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

        <div class="row">
            <div class="col-lg-8">
                <div class="mb-4">
                    <label for="title" class="form-label fw-semibold">Titre de l'article <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title') }}" required placeholder="Entrez un titre accrocheur">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="excerpt" class="form-label fw-semibold">Résumé <span class="text-danger">*</span></label>
                    <textarea name="excerpt" id="excerpt" rows="3" 
                              class="form-control @error('excerpt') is-invalid @enderror" required
                              placeholder="Un bref résumé de l'article">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="content" class="form-label fw-semibold">Contenu <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="15" 
                              class="form-control @error('content') is-invalid @enderror" required 
                              placeholder="Rédigez votre article...">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-4">
                <div class="mb-4">
                    <label for="image" class="form-label fw-semibold">Image de couverture <span class="text-danger">*</span></label>
                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" 
                           accept="image/*" required>
                    <div class="form-text">Format recommandé : 1200x630px, max 2MB</div>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="event_id" class="form-label fw-semibold">Événement associé</label>
                    <select name="event_id" id="event_id" class="form-select @error('event_id') is-invalid @enderror">
                        <option value="">Aucun événement</option>
                        @foreach($events ?? [] as $event)
                            <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('event_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="tags" class="form-label fw-semibold">Tags</label>
                    <input type="text" name="tags" id="tags" class="form-control @error('tags') is-invalid @enderror" 
                           value="{{ old('tags') }}" placeholder="événement, musique, festival">
                    <div class="form-text">Séparez les tags par des virgules</div>
                    @error('tags')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between gap-2 mt-4">
            <a href="{{ route('organizer.blogs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i>Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Publier l'article
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser TinyMCE
    tinymce.init({
        selector: '#content',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        height: 500
    });
});
</script>
@endpush
@endsection
