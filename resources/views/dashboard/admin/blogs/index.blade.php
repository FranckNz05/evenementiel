@extends('layouts.dashboard')

@section('title', 'Gestion des blogs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-pagination.css') }}">
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
    
    --blanc-or: #ffffff;
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
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
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

/* Images de blog */
.blog-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: var(--border-radius);
    border: 2px solid var(--gray-200);
}

.blog-image-placeholder {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--gray-100), var(--gray-200));
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-500);
    border: 2px solid var(--gray-200);
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
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- En-tête de page -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-blog"></i>
            Gestion des blogs
        </h1>
        <div class="page-actions">
            <a href="{{ route('admin.blogs.create') }}" class="modern-btn btn-primary-modern">
                <i class="fas fa-plus"></i> Nouvel article
            </a>
        </div>
    </div>

    <!-- Liste des blogs -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-list"></i>
                Liste des blogs ({{ $blogs->total() }})
            </h5>
        </div>
        <div class="card-body-modern">
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Auteur</th>
                            <th>Date de création</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $blog)
                            <tr>
                                <td>
                                    @if($blog->image)
                                        <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="blog-image">
                                    @else
                                        <div class="blog-image-placeholder">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $blog->title }}</div>
                                    <small class="text-muted">{{ Str::limit($blog->content, 50) }}</small>
                                </td>
                                <td>
                                    <span class="modern-badge badge-info">{{ $blog->blogcategories->name ?? 'Sans catégorie' }}</span>
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $blog->user->nom ?? 'Auteur non défini' }}</div>
                                    <small class="text-muted">{{ $blog->user->email ?? 'Email non disponible' }}</small>
                                </td>
                                <td>{{ $blog->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($blog->deleted_at)
                                        <span class="modern-badge badge-danger">Supprimé</span>
                                    @else
                                        <span class="modern-badge badge-success">Actif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.blogs.show', $blog) }}" class="modern-btn btn-sm-modern" style="background: var(--info); color: white;" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="modern-btn btn-warning-modern btn-sm-modern" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="modern-btn btn-danger-modern btn-sm-modern delete-blog" data-id="{{ $blog->id }}" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucun blog trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($blogs->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $blogs->links('vendor.pagination.bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Liste des catégories -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Catégories</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus"></i> Nouvelle catégorie
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Nombre d'articles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blog_categories as $category)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $category->name }}</div>
                                    <small class="text-muted">{{ $category->slug }}</small>
                                </td>
                                <td>{{ Str::limit($category->description, 100) }}</td>
                                <td>{{ $category->blogs_count }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-warning btn-sm edit-category" 
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}"
                                                data-description="{{ $category->description }}"
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-category" 
                                                data-id="{{ $category->id }}"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Aucune catégorie trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($blogs->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $blogs->links('vendor.pagination.bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Ajout Catégorie -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.blog-categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Édition Catégorie -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression des blogs
    const deleteBlogButtons = document.querySelectorAll('.delete-blog');
    deleteBlogButtons.forEach(button => {
        button.addEventListener('click', function() {
            const blogId = this.dataset.id;
            if (confirm('Êtes-vous sûr de vouloir supprimer ce blog ?')) {
                fetch(`/Administrateur/blogs/${blogId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('tr').remove();
                        alert('Blog supprimé avec succès');
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la suppression');
                });
            }
        });
    });

    // Gestion de l'édition des catégories
    const editCategoryButtons = document.querySelectorAll('.edit-category');
    editCategoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('editCategoryForm').action = `/Administrateur/blog-categories/${categoryId}`;

            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        });
    });

    // Gestion de la suppression des catégories
    const deleteCategoryButtons = document.querySelectorAll('.delete-category');
    deleteCategoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.dataset.id;
            if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
                fetch(`/Administrateur/blog-categories/${categoryId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('tr').remove();
                        alert('Catégorie supprimée avec succès');
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la suppression');
                });
            }
        });
    });
});
</script>
@endpush
