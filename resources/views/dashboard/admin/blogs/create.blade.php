@extends('layouts.dashboard')

@section('title', 'Créer un Article')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Créer un Article</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blog</a></li>
                    <li class="breadcrumb-item active">Nouvel article</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-light shadow-sm border">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="card modern-card shadow-sm mb-4">
        <div class="card-header-modern">
            <h5 class="card-title my-1 text-white">
                <i class="fas fa-blog me-2"></i>Contenu de l'article
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

            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold text-dark">Titre de l'article</label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ old('title') }}" placeholder="Entrez un titre accrocheur" required>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label fw-bold text-dark">Contenu de l'article</label>
                            <textarea class="form-control" id="content" name="content" rows="15">{{ old('content') }}</textarea>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="p-4 bg-light rounded border shadow-sm mb-4">
                            <label class="form-label fw-bold text-primary border-bottom pb-2 w-100 mb-3 text-uppercase" style="font-size: 0.75rem;">
                                <i class="fas fa-image me-2"></i>Image à la une
                            </label>
                            <div class="mb-3">
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text mt-2 small">Format recommandé : 1200x630px (aspect 16:9), max 2Mo.</div>
                            </div>
                            <!-- Preview placeholder can be added here via JS -->
                        </div>

                        <div class="p-4 bg-light rounded border shadow-sm mb-4">
                            <label class="form-label fw-bold text-primary border-bottom pb-2 w-100 mb-3 text-uppercase" style="font-size: 0.75rem;">
                                <i class="fas fa-paper-plane me-2"></i>Publication
                            </label>
                            <div class="mb-4">
                                <label for="status" class="form-label small fw-bold text-dark">État de l'article</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon (Non visible)</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publié (En ligne)</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary-modern shadow-sm">
                                    <i class="fas fa-save me-2"></i> Créer l'article
                                </button>
                                <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary border-0 small text-decoration-underline mt-2">
                                    Annuler et quitter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 500,
        menubar: true,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | \
                alignleft aligncenter alignright alignjustify | \
                bullist numlist outdent indent | removeformat | help',
        skin: 'oxide',
        content_css: 'default'
    });
</script>
@endpush
@endsection
