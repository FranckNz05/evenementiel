@extends('layouts.dashboard')

@section('title', 'Détails de la catégorie')

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
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
    --radius-md: 8px;
    --radius-lg: 12px;
}

.container-fluid {
    padding: 1.5rem;
    max-width: 1000px;
    margin: 0 auto;
    background: var(--gray-50);
    min-height: 100vh;
}

.modern-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    margin-bottom: 1.5rem;
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

.info-row {
    display: flex;
    padding: 1rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: var(--gray-700);
    width: 200px;
    flex-shrink: 0;
}

.info-value {
    color: var(--gray-500);
    flex: 1;
}

.category-image-large {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: var(--radius-lg);
    border: 2px solid var(--gray-200);
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
}

.modern-table tbody tr:hover {
    background: var(--gray-50);
}

.modern-table td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="modern-card">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-tag"></i>
                Détails de la catégorie
            </h5>
        </div>
        <div class="card-body-modern">
            <div style="display: flex; gap: 2rem; margin-bottom: 2rem;">
                @if($category->image)
                    <img src="{{ Storage::disk('public')->url($category->image) }}" alt="{{ $category->name }}" class="category-image-large" onerror="this.src='{{ asset('images/default-category.png') }}'">
                @else
                    <div class="category-image-large" style="background: var(--gray-200); display: flex; align-items: center; justify-content: center; color: var(--gray-500);">
                        <i class="fas fa-image" style="font-size: 3rem;"></i>
                    </div>
                @endif
                <div style="flex: 1;">
                    <div class="info-row">
                        <div class="info-label">Nom:</div>
                        <div class="info-value">
                            <strong style="color: var(--gray-700); font-size: 1.25rem;">{{ $category->name }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Slug:</div>
                        <div class="info-value">
                            <code style="background: var(--gray-200); padding: 0.25rem 0.5rem; border-radius: 4px;">{{ $category->slug }}</code>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nombre d'événements:</div>
                        <div class="info-value">
                            <span style="background: var(--gray-200); padding: 0.25rem 0.75rem; border-radius: 12px; font-weight: 500;">
                                {{ $category->events->count() + $customEvents->count() }}
                            </span>
                            <small style="display: block; color: var(--gray-500); font-size: 0.875rem; margin-top: 0.25rem;">
                                {{ $category->events->count() }} normal(s), {{ $customEvents->count() }} personnalisé(s)
                            </small>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Créée le:</div>
                        <div class="info-value">{{ $category->created_at ? $category->created_at->format('d/m/Y à H:i') : 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Modifiée le:</div>
                        <div class="info-value">{{ $category->updated_at ? $category->updated_at->format('d/m/Y à H:i') : 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('admin.categories.index') }}" class="modern-btn btn-secondary-modern">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
                <a href="{{ route('admin.categories.edit', $category) }}" class="modern-btn btn-primary-modern">
                    <i class="fas fa-edit"></i>
                    Modifier
                </a>
            </div>
        </div>
    </div>

    @if($category->events->count() > 0 || $customEvents->count() > 0)
    <!-- Événements normaux -->
    @if($category->events->count() > 0)
    <div class="modern-card">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-calendar-alt"></i>
                Événements à billet ({{ $category->events->count() }})
            </h5>
        </div>
        <div class="card-body-modern" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Organisateur</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($category->events as $event)
                            <tr>
                                <td>
                                    <strong>{{ $event->title }}</strong>
                                </td>
                                <td>
                                    {{ $event->organizer->company_name ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ $event->start_date ? $event->start_date->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if($event->etat === 'En cours')
                                        <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Actif</span>
                                    @elseif($event->etat === 'En attente')
                                        <span style="background: #e8eaf6; color: #1a237e; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Attente</span>
                                    @else
                                        <span style="background: var(--gray-200); color: var(--gray-700); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">{{ $event->etat }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Événements personnalisés -->
    @if($customEvents->count() > 0)
    <div class="modern-card">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-user-lock"></i>
                Événements personnalisés ({{ $customEvents->count() }})
            </h5>
        </div>
        <div class="card-body-modern" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Organisateur</th>
                            <th>Date</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customEvents as $event)
                            <tr>
                                <td>
                                    <strong>{{ $event->title }}</strong>
                                </td>
                                <td>
                                    {{ $event->organizer->prenom ?? '' }} {{ $event->organizer->nom ?? '' }}
                                    @if($event->organizer->organizer)
                                        ({{ $event->organizer->organizer->company_name ?? '' }})
                                    @endif
                                </td>
                                <td>
                                    {{ $event->start_date ? $event->start_date->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    <span style="background: #e8eaf6; color: #1a237e; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">
                                        {{ $event->type ?? 'Personnalisé' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>
@endsection

