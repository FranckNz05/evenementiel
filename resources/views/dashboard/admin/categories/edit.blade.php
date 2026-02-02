@extends('layouts.dashboard')

@section('title', 'Modifier la catégorie')

@push('styles')
<style>
:root {
    --primary: #0f1a3d;
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-500: #6b7280;
    --gray-700: #374151;
    --success: #10b981;
    --danger: #ef4444;
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
    --radius-md: 8px;
    --radius-lg: 12px;
}

.container-fluid {
    padding: 1.5rem;
    max-width: 800px;
    margin: 0 auto;
    background: var(--gray-50);
    min-height: 100vh;
}

.modern-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.card-header-modern {
    background: linear-gradient(135deg, var(--primary) 0%, #1a237e 100%);
    padding: 1.25rem 1.5rem;
}

.card-header-modern * {
    color: var(--white) !important;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-body-modern {
    padding: 1.5rem;
}

.modern-form-control {
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    width: 100%;
    transition: all 0.2s;
}

.modern-form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(15, 26, 61, 0.1);
}

.modern-btn {
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-md);
    font-weight: 500;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-primary-modern {
    background: var(--primary);
    color: var(--white);
}

.btn-primary-modern:hover {
    background: #1a237e;
}

.btn-secondary-modern {
    background: var(--gray-300);
    color: var(--gray-700);
}

.btn-secondary-modern:hover {
    background: var(--gray-400);
}

.image-preview {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: var(--radius-md);
    border: 2px solid var(--gray-200);
    margin-top: 0.5rem;
}

.current-image {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: var(--radius-md);
    border: 2px solid var(--gray-200);
    margin-top: 0.5rem;
}

.modern-alert {
    padding: 0.875rem 1rem;
    border-radius: var(--radius-md);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-danger-modern {
    background: #fee2e2;
    color: #991b1b;
    border-left: 3px solid var(--danger);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    @if(session('error'))
        <div class="modern-alert alert-danger-modern">
            <i class="fas fa-exclamation-triangle"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="modern-card">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-edit"></i>
                Modifier la catégorie
            </h5>
        </div>
        <div class="card-body-modern">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 1.5rem;">
                    <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--gray-700);">
                        Nom de la catégorie <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" 
                           class="modern-form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $category->name) }}" 
                           required 
                           maxlength="255"
                           placeholder="Ex: Musique, Sport, Culture...">
                    @error('name')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="image" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--gray-700);">
                        Image de la catégorie
                    </label>
                    @if($category->image)
                        <div style="margin-bottom: 0.5rem;">
                            <p style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 0.5rem;">Image actuelle:</p>
                            <img src="{{ Storage::disk('public')->url($category->image) }}" alt="{{ $category->name }}" class="current-image" onerror="this.src='{{ asset('images/default-category.png') }}'">
                        </div>
                    @endif
                    <input type="file" 
                           class="modern-form-control @error('image') is-invalid @enderror" 
                           id="image" 
                           name="image" 
                           accept="image/jpeg,image/png,image/jpg,image/gif"
                           onchange="previewImage(this)">
                    @error('image')
                        <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                    <img id="imagePreview" class="image-preview" alt="Nouvel aperçu" style="display: none;">
                    <small style="color: var(--gray-500); font-size: 0.8125rem; display: block; margin-top: 0.25rem;">
                        Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB). Laisser vide pour conserver l'image actuelle.
                    </small>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <a href="{{ route('admin.categories.index') }}" class="modern-btn btn-secondary-modern">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                    <button type="submit" class="modern-btn btn-primary-modern">
                        <i class="fas fa-save"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection

