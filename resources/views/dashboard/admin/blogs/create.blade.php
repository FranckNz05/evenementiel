@extends('layouts.Administrateur')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Créer un nouvel article</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-blog me-1"></i>
            Nouvel article
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

            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Titre</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Contenu</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image de couverture</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">Format recommandé : 1200x630px, max 2MB</div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publié</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.blogs') }}" class="btn btn-secondary">Retour</a>
                    <button type="submit" class="btn btn-primary">Créer l'article</button>
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
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | \
                alignleft aligncenter alignright alignjustify | \
                bullist numlist outdent indent | removeformat | help'
    });
</script>
@endpush
@endsection
