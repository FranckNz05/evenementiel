@extends('layouts.dashboard')

@section('title', 'Envoyer une newsletter')

@push('styles')
<style>

/* Variables CSS pour design cohérent */
/* ===============================================
   SYSTÈME DE DESIGN - BLEU NUIT & blanc OR
   Design Premium avec Excellence UX/UI
   =============================================== */

:root {
    /* Palette Principale - Bleu MokiliEvent & blanc Or */
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --bleu-nuit-lighter: #2c3e8f;
    --bleu-royal: #3b4f9a;
    --bleu-soft: #5a6ba8;
    --bleu-light: #7c8db8;
    
    --blanc-or: #ffd700;
    --blanc-or-light: #ffe44d;
    --blanc-or-dark: #e6c200;
    --blanc-amber: #ffb700;
    --blanc-warm: #ffdb58;
    
    /* Couleurs d'état */
    --success: #10b981;
    --success-bg: #d1fae5;
    --warning: #ffd700;
    --warning-bg: #fff8dc;
    --danger: #ef4444;
    --danger-bg: #fee2e2;
    --info: #3b82f6;
    --info-bg: #dbeafe;
    
    /* Nuances neutres */
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    
    /* Ombres et effets */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --shadow-gold: 0 10px 25px -5px rgba(255, 215, 0, 0.3);
    --shadow-blue: 0 10px 25px -5px rgba(15, 26, 61, 0.3);
    
    --border-radius: 0.75rem;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Container principal */
.container-fluid {
    padding: 1rem;
    max-width: 1600px;
    margin: 0 auto;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    min-height: 100vh;
    position: relative;
}

.container-fluid::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--bleu-nuit), var(--blanc-or), var(--bleu-nuit-clair));
    z-index: 1;
}

/* En-tête de page */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    border-radius: var(--border-radius);
    color: white;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: var(--blanc-or);
    opacity: 0.1;
    border-radius: 50%;
    transform: rotate(45deg);
}

.page-title {
    font-size: 1.875rem;
    font-weight: 700;
    margin: 0;
    position: relative;
    z-index: 2;
}

.page-actions {
    display: flex;
    gap: 1rem;
    position: relative;
    z-index: 2;
}

/* Cartes modernes */
.modern-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    border: none;
    overflow: hidden;
    transition: var(--transition);
}

.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

.card-header-modern {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    border-bottom: 3px solid var(--blanc-or);
    padding: 1.5rem;
    color: white;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-body-modern {
    padding: 1.5rem;
}

/* Tableaux modernes */
.modern-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.modern-table thead th {
    background: linear-gradient(135deg, var(--bleu-nuit-light), var(--bleu-nuit));
    color: var(--blanc-or);
    padding: 1rem;
    text-align: left;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}

.modern-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: var(--transition);
}

.modern-table tbody tr:hover {
    background: var(--gray-50);
    transform: scale(1.01);
}

.modern-table td {
    padding: 1rem;
    vertical-align: middle;
}

/* Boutons modernes */
.modern-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-primary-modern {
    background: linear-gradient(135deg, var(--bleu-nuit), var(--bleu-nuit-clair));
    color: var(--blanc-or);
}

.btn-primary-modern:hover {
    background: linear-gradient(135deg, var(--bleu-nuit-clair), var(--bleu-nuit-lighter));
    transform: translateY(-2px);
    box-shadow: var(--shadow-blue);
}

.btn-success-modern {
    background: linear-gradient(135deg, var(--success), #059669);
    color: white;
}

.btn-success-modern:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-warning-modern {
    background: linear-gradient(135deg, var(--blanc-or), var(--blanc-or-dark));
    color: var(--bleu-nuit);
}

.btn-warning-modern:hover {
    background: linear-gradient(135deg, var(--blanc-or-dark), var(--blanc-amber));
    transform: translateY(-2px);
    box-shadow: var(--shadow-gold);
}

.btn-danger-modern {
    background: linear-gradient(135deg, var(--danger), #dc2626);
    color: white;
}

.btn-danger-modern:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-secondary-modern {
    background: linear-gradient(135deg, var(--gray-500), var(--gray-600));
    color: white;
}

.btn-secondary-modern:hover {
    background: linear-gradient(135deg, var(--gray-600), var(--gray-700));
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-sm-modern {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

/* Badges modernes */
.modern-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background: var(--success-bg);
    color: var(--success);
}

.badge-warning {
    background: var(--warning-bg);
    color: var(--blanc-or);
}

.badge-danger {
    background: var(--danger-bg);
    color: var(--danger);
}

.badge-info {
    background: var(--info-bg);
    color: var(--info);
}

/* Responsive Design */
@media (max-width: 768px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    .page-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
        padding: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .page-actions {
        width: 100%;
        justify-content: stretch;
        flex-wrap: wrap;
    }
    
    .modern-table {
        font-size: 0.75rem;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 0.5rem;
    }
}

@media (max-width: 480px) {
    .modern-table {
        font-size: 0.7rem;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 0.375rem;
    }
    
    .modern-btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
@endpush

@section('content')
<div class="modern-card">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Envoyer une newsletter</h6>
        <div class="dropdown no-arrow">
            <a href="{{ route('admin.newsletter.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm"></i> Retour à la liste
            </a>
        </div>
    </div>
    <div class="card-body-modern">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.newsletter.send') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="subject" class="form-label">Sujet</label>
                <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="recipients" class="form-label">Destinataires</label>
                <select class="form-select @error('recipients') is-invalid @enderror" id="recipients" name="recipients" required>
                    <option value="active" selected>Abonnés actifs uniquement ({{ $activeSubscribersCount }})</option>
                    <option value="all">Tous les abonnés</option>
                </select>
                @error('recipients')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Contenu</label>
                <div class="editor-container">
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                </div>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    Utilisez l'éditeur pour mettre en forme votre newsletter.
                </small>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="modern-btn btn-primary-modern">
                    <i class="fas fa-paper-plane me-1"></i> Envoyer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Utiliser CKEditor à la place de TinyMCE -->
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        ClassicEditor
            .create(document.querySelector('#content'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
            })
            .then(editor => {
                console.log('CKEditor initialized');

                // S'assurer que le contenu est mis à jour dans le textarea lors de la soumission du formulaire
                const form = document.querySelector('form');
                form.addEventListener('submit', function() {
                    const contentInput = document.querySelector('#content');
                    contentInput.value = editor.getData();
                });
            })
            .catch(error => {
                console.error('CKEditor initialization failed:', error);
            });
    });
</script>
@endpush
