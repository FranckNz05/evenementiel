@extends('layouts.dashboard')

@section('title', 'Modifier l\'article')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="fas fa-edit me-2 text-primary"></i>
            Modifier l'article
        </h5>
        <a href="{{ route('organizer.blogs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('organizer.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="mb-4">
                    <label for="title" class="form-label fw-semibold">Titre de l'article <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title', $blog->title) }}" required placeholder="Entrez un titre accrocheur">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="content" class="form-label fw-semibold">Contenu <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="15" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $blog->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-4">
                <div class="mb-4">
                    <label for="image" class="form-label fw-semibold">Image de couverture</label>
                    @if($blog->image_path)
                        <div class="mb-2">
                            <img src="{{ Storage::url($blog->image_path) }}" alt="Image actuelle" class="img-thumbnail w-100" style="max-height: 200px; object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    <div class="form-text">Laissez vide pour conserver l'image actuelle</div>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="event_id" class="form-label fw-semibold">Événement associé</label>
                    <select name="event_id" id="event_id" class="form-select @error('event_id') is-invalid @enderror">
                        <option value="">Aucun événement</option>
                        @foreach($events ?? [] as $event)
                            <option value="{{ $event->id }}" {{ old('event_id', $blog->event_id) == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('event_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label fw-semibold">Statut</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="draft" {{ old('status', $blog->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="published" {{ old('status', $blog->status ?? 'draft') == 'published' ? 'selected' : '' }}>Publié</option>
                    </select>
                    @error('status')
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
                <i class="fas fa-save me-1"></i>Mettre à jour l'article
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 500,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | \
                alignleft aligncenter alignright alignjustify | \
                bullist numlist outdent indent | removeformat | help',
        menubar: false
    });
</script>
@endpush

@endsection
