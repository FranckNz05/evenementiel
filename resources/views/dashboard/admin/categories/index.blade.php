@extends('layouts.dashboard')

@section('title', 'Gestion des catégories')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-pagination.css') }}">
<style>
/* Styles similaires à events/index.blade.php */
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
    --transition: 200ms cubic-bezier(0.4, 0, 0.2, 1);
}

.container-fluid {
    padding: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
    background: var(--gray-50);
    min-height: 100vh;
}

.modern-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
    overflow: hidden;
}

.card-header-modern {
    background: linear-gradient(135deg, var(--primary) 0%, #1a237e 100%);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
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

.modern-btn {
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    font-weight: 500;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-primary-modern {
    background: var(--white);
    color: var(--primary);
}

.btn-primary-modern:hover {
    background: #f3f4f6;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead th {
    background: var(--gray-50);
    color: var(--gray-700);
    font-weight: 600;
    padding: 1rem 1.25rem;
    text-align: left;
    border-bottom: 2px solid var(--gray-200);
}

.modern-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background var(--transition);
}

.modern-table tbody tr:hover {
    background: var(--gray-50);
}

.modern-table td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
}

.category-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.btn-icon-modern {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-md);
    border: none;
    cursor: pointer;
    transition: all var(--transition);
    color: var(--white) !important;
}

.btn-info-modern {
    background: var(--primary);
}

.btn-warning-modern {
    background: #1a237e;
}

.btn-danger-modern {
    background: var(--danger);
}

.modern-alert {
    padding: 0.875rem 1rem;
    border-radius: var(--radius-md);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success-modern {
    background: #d1fae5;
    color: #065f46;
    border-left: 3px solid var(--success);
}

.alert-danger-modern {
    background: #fee2e2;
    color: #991b1b;
    border-left: 3px solid var(--danger);
}

.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    color: var(--gray-500);
}

.fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Alertes -->
    @if(session('success'))
        <div class="modern-alert alert-success-modern fade-in">
            <i class="fas fa-check-circle"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="modern-alert alert-danger-modern fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Liste des catégories -->
    <div class="modern-card fade-in">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-tags"></i>
                Catégories d'événements ({{ $categories->total() }})
            </h5>
            <a href="{{ route('admin.categories.create') }}" class="modern-btn btn-primary-modern">
                <i class="fas fa-plus"></i>
                Nouvelle catégorie
            </a>
        </div>
        <div class="card-body-modern" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Slug</th>
                            <th>Événements</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    @if($category->image)
                                        <img src="{{ Storage::disk('public')->url($category->image) }}" alt="{{ $category->name }}" class="category-image" onerror="this.src='{{ asset('images/default-category.png') }}'">
                                    @else
                                        <div class="category-image" style="background: var(--gray-200); display: flex; align-items: center; justify-content: center; color: var(--gray-500);">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                </td>
                                <td>
                                    <code style="background: var(--gray-200); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem;">{{ $category->slug }}</code>
                                </td>
                                <td>
                                    <span style="background: var(--gray-200); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 500;">
                                        {{ $category->events_count + $category->custom_events_count }}
                                    </span>
                                    <small style="display: block; color: var(--gray-500); font-size: 0.75rem; margin-top: 0.25rem;">
                                        {{ $category->events_count }} normal(s), {{ $category->custom_events_count }} personnalisé(s)
                                    </small>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <a href="{{ route('admin.categories.show', $category) }}" class="btn-icon-modern btn-info-modern" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn-icon-modern btn-warning-modern" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon-modern btn-danger-modern" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">
                                    <div class="empty-state">
                                        <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--gray-300);">
                                            <i class="fas fa-tags"></i>
                                        </div>
                                        <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--gray-700);">
                                            Aucune catégorie
                                        </div>
                                        <div style="font-size: 0.875rem; color: var(--gray-500);">
                                            Commencez par créer votre première catégorie
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div style="padding: 1.5rem; border-top: 1px solid var(--gray-200);">
                    {{ $categories->links('vendor.pagination.bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

