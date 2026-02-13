@extends('layouts.dashboard')

@section('title', 'Gestion des organisateurs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-table-pages.css') }}">
<style>
.organizers-page {
    min-height: 100vh;
    background: var(--gray-50);
    padding: 2rem;
}

.organizers-table {
    width: 100%;
    border-collapse: collapse;
}

.organizers-table thead {
    background: var(--gray-50);
}

.organizers-table thead th {
    padding: 0.875rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-600);
    border-bottom: 2px solid var(--gray-200);
    white-space: nowrap;
}

.organizers-table tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background 0.15s;
}

.organizers-table tbody tr:hover {
    background: var(--gray-50);
}

.organizers-table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-900);
    vertical-align: middle;
}

.organizer-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.organizer-logo {
    width: 40px;
    height: 40px;
    border-radius: 0.5rem;
    object-fit: cover;
    border: 1px solid var(--gray-200);
}

.badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-success {
    background: var(--success);
    color: white;
}

.badge-warning {
    background: var(--warning);
    color: white;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

.btn-primary {
    background: var(--primary);
    color: white;
    border: none;
}

.btn-primary:hover {
    background: var(--primary-light);
    color: white;
}

.btn-warning {
    background: var(--warning);
    color: white;
    border: none;
}

.btn-danger {
    background: var(--danger);
    color: white;
    border: none;
}
</style>
@endpush

@section('content')
<div class="organizers-page">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title-section">
            <h1>Gestion des organisateurs</h1>
            <p>Gérez tous les organisateurs de la plateforme</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tableau de bord
            </a>
            @if(route('admin.organizers.create'))
                <a href="{{ route('admin.organizers.create') }}" class="btn btn-secondary">
                    <i class="fas fa-plus"></i>
                    Ajouter un organisateur
                </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.organizers.index') }}" id="filtersForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Recherche</label>
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            id="organizerSearch"
                            placeholder="Rechercher par entreprise, utilisateur, email..."
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Statut de vérification</label>
                    <select name="verified" class="form-select" id="organizerVerified" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="yes" {{ request('verified') == 'yes' ? 'selected' : '' }}>Vérifiés</option>
                        <option value="no" {{ request('verified') == 'no' ? 'selected' : '' }}>Non vérifiés</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Trier par</label>
                    <select name="sort" class="form-select" id="organizerSort" onchange="this.form.submit()">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date d'inscription</option>
                        <option value="company_name" {{ request('sort') == 'company_name' ? 'selected' : '' }}>Nom de l'entreprise</option>
                        <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-search"></i>
                        Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Organizers Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Liste des organisateurs</h3>
            <span style="font-size: 0.875rem; color: var(--gray-600);">{{ $organizers->total() }} organisateur(s)</span>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="organizers-table">
                    <thead>
                        <tr>
                            <th>Entreprise</th>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Vérifié</th>
                            <th>Date d'inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organizers as $organizer)
                            <tr>
                                <td>
                                    <div class="organizer-info">
                                        <img src="{{ $organizer->logoUrl }}" alt="{{ $organizer->company_name }}" class="organizer-logo" onerror="this.src='{{ asset('images/default-logo.png') }}'">
                                        <span style="font-weight: 600; color: var(--gray-900);">{{ $organizer->company_name }}</span>
                                    </div>
                                </td>
                                <td>{{ $organizer->user->name ?? 'N/A' }}</td>
                                <td>{{ $organizer->email }}</td>
                                <td>{{ $organizer->phone_primary ?? 'N/A' }}</td>
                                <td>
                                    @if($organizer->is_verified)
                                        <span class="badge badge-success">Vérifié</span>
                                    @else
                                        <span class="badge badge-warning">Non vérifié</span>
                                    @endif
                                </td>
                                <td>{{ $organizer->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="{{ route('admin.organizers.show', $organizer) }}" class="btn btn-sm btn-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.organizers.edit', $organizer) }}" class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger delete-organizer" data-id="{{ $organizer->id }}" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem;">
                                    <i class="fas fa-users" style="font-size: 3rem; color: var(--gray-300); margin-bottom: 1rem;"></i>
                                    <p style="color: var(--gray-500); margin: 0;">Aucun organisateur trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($organizers->hasPages())
            <div class="card-footer">
                <div style="font-size: 0.875rem; color: var(--gray-600);">
                    Affichage de {{ $organizers->firstItem() }} à {{ $organizers->lastItem() }} sur {{ $organizers->total() }} organisateurs
                </div>
                <div>
                    {{ $organizers->appends(request()->except('page'))->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Délégation d'événements pour les boutons de suppression
    document.querySelectorAll('.delete-organizer').forEach(button => {
        button.addEventListener('click', function() {
            const organizerId = this.dataset.id;
            if (confirm('Êtes-vous sûr de vouloir supprimer cet organisateur ?')) {
                deleteOrganizer(organizerId);
            }
        });
    });
});

function deleteOrganizer(organizerId) {
    fetch(`{{ url('admin/organisateurs') }}/${organizerId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Erreur lors de la suppression de l\'organisateur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la suppression de l\'organisateur');
    });
}
</script>
@endpush
@endsection
