@extends('layouts.dashboard')

@section('title', 'Gestion des organisateurs')

@section('content')
<div class="modern-card">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Liste des organisateurs</h6>
        <div class="dropdown no-arrow">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm"></i> Retour au tableau de bord
            </a>
            <a href="{{ route('admin.organizers.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus fa-sm"></i> Ajouter un organisateur
            </a>
        </div>
    </div>
    <div class="card-body-modern">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="organizerSearch" placeholder="Rechercher un organisateur...">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="organizerVerified">
                    <option value="">Tous les statuts</option>
                    <option value="yes" {{ request('verified') == 'yes' ? 'selected' : '' }}>Vérifiés</option>
                    <option value="no" {{ request('verified') == 'no' ? 'selected' : '' }}>Non vérifiés</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="organizerSort">
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date d'inscription</option>
                    <option value="company_name" {{ request('sort') == 'company_name' ? 'selected' : '' }}>Nom de l'entreprise</option>
                    <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="modern-table" width="100%" cellspacing="0">
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
                    @foreach($organizers as $organizer)
                    <tr>
                        <td>
                            <img src="{{ $organizer->logoUrl }}" alt="{{ $organizer->company_name }}" class="img-thumbnail mr-2" style="width: 40px; height: 40px; object-fit: cover;">
                            {{ $organizer->company_name }}
                        </td>
                        <td>{{ $organizer->user->name ?? 'N/A' }}</td>
                        <td>{{ $organizer->email }}</td>
                        <td>{{ $organizer->phone_primary }}</td>
                        <td>
                            @if($organizer->is_verified)
                                <span class="modern-badge badge-success">Vérifié</span>
                            @else
                                <span class="modern-badge badge-warning">Non vérifié</span>
                            @endif
                        </td>
                        <td>{{ $organizer->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.organizers.show', $organizer) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.organizers.edit', $organizer) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-sm delete-organizer" data-id="{{ $organizer->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $organizers->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestionnaire d'événement pour la recherche
    document.getElementById('searchBtn').addEventListener('click', function() {
        const search = document.getElementById('organizerSearch').value;
        window.location.href = `{{ route('admin.organizers') }}?search=${search}`;
    });
    
    // Gestionnaire d'événement pour la touche Entrée dans le champ de recherche
    document.getElementById('organizerSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const search = this.value;
            window.location.href = `{{ route('admin.organizers') }}?search=${search}`;
        }
    });
    
    // Gestionnaire d'événement pour le filtre par statut de vérification
    document.getElementById('organizerVerified').addEventListener('change', function() {
        const verified = this.value;
        window.location.href = `{{ route('admin.organizers') }}?verified=${verified}`;
    });
    
    // Gestionnaire d'événement pour le tri
    document.getElementById('organizerSort').addEventListener('change', function() {
        const sort = this.value;
        window.location.href = `{{ route('admin.organizers') }}?sort=${sort}`;
    });
    
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
            // Recharger la page
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

