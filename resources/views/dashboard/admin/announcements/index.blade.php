@extends('layouts.dashboard')

@section('title', 'Gestion des annonces')

@push('styles')
<style>
:root {
    --bleu-nuit: #0f1a3d;
    --bleu-nuit-clair: #1a237e;
    --blanc-or: #ffffff;
    --blanc-amber: #f59e0b;
    --shadow-gold: rgba(255, 215, 0, 0.2);
    --shadow-blue: rgba(185, 28, 28, 0.1);
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
    --gray-900: #111827;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #fca5a5;
    --border-radius: 0.75rem;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --transition: all 0.3s ease;
}

.modern-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    transition: var(--transition);
}

.modern-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.card-header {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    padding: 1.5rem;
    border-bottom: 2px solid var(--bleu-nuit);
}

.card-header h6 {
    color: var(--white);
    font-weight: 600;
    margin: 0;
}

.card-body-modern {
    padding: 2rem;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--white);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow);
}

.modern-table thead th {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
    padding: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.875rem;
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
    color: var(--gray-700);
}

.modern-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    transition: var(--transition);
}

.badge-success {
    background: var(--success);
    color: var(--white);
}

.badge-danger {
    background: var(--danger);
    color: var(--white);
}

.modern-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
    font-size: 0.875rem;
}

.btn-primary-modern {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    color: var(--white);
}

.btn-primary-modern:hover {
    background: linear-gradient(135deg, var(--bleu-nuit-clair) 0%, var(--bleu-nuit) 100%);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.btn-success {
    background: var(--success);
    color: var(--white);
}

.btn-success:hover {
    background: #059669;
    color: var(--white);
    transform: translateY(-2px);
}

.btn-warning {
    background: var(--warning);
    color: var(--white);
}

.btn-warning:hover {
    background: #d97706;
    color: var(--white);
    transform: translateY(-2px);
}

.btn-danger {
    background: var(--danger);
    color: var(--white);
}

.btn-danger:hover {
    background: #dc2626;
    color: var(--white);
    transform: translateY(-2px);
}

.btn-primary {
    background: var(--bleu-nuit);
    color: var(--white);
}

.btn-primary:hover {
    background: var(--bleu-nuit-clair);
    color: var(--white);
    transform: translateY(-2px);
}

.btn-group {
    display: flex;
    gap: 0.25rem;
}

.loading-state {
    opacity: 0.6;
    pointer-events: none;
}

.loading-state i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.status-badge {
    transition: var(--transition);
}

.status-badge.updating {
    animation: pulse 1s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.input-group {
    display: flex;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow);
}

.form-control {
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius) 0 0 var(--border-radius);
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--bleu-nuit);
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.btn-outline-secondary,
.btn-outline-success,
.btn-outline-danger {
    border: 1px solid var(--gray-300);
    color: var(--gray-600);
    background: var(--white);
    transition: var(--transition);
}

.btn-outline-secondary:hover,
.btn-outline-success:hover,
.btn-outline-danger:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-outline-success.active {
    background: var(--success);
    color: var(--white);
    border-color: var(--success);
}

.btn-outline-danger.active {
    background: var(--danger);
    color: var(--white);
    border-color: var(--danger);
}

@media (max-width: 768px) {
    .card-body-modern {
        padding: 1rem;
    }
    
    .modern-table {
        font-size: 0.875rem;
    }
    
    .modern-table td,
    .modern-table th {
        padding: 0.75rem 0.5rem;
    }
    
    .btn-group {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .modern-btn {
        justify-content: center;
    }
}
</style>
@endpush

@section('content')
<div class="modern-card">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Liste des annonces</h6>
        <div class="dropdown no-arrow">
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus fa-sm"></i> Nouvelle annonce
            </a>
        </div>
    </div>
    <div class="card-body-modern">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="announcementSearch" placeholder="Rechercher une annonce..." value="{{ request('search') }}">
                    <button class="modern-btn btn-primary-modern" type="button" id="searchBtn">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <div class="btn-group">
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">Toutes</a>
                    <a href="{{ route('admin.announcements.index', ['status' => 'active']) }}" class="btn btn-outline-success {{ request('status') == 'active' ? 'active' : '' }}">Actives</a>
                    <a href="{{ route('admin.announcements.index', ['status' => 'inactive']) }}" class="btn btn-outline-danger {{ request('status') == 'inactive' ? 'active' : '' }}">Inactives</a>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Contenu</th>
                        <th>Ordre</th>
                        <th>Période</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $announcement)
                        <tr data-announcement-id="{{ $announcement->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    {{ $announcement->title }}
                                    @if($announcement->is_urgent)
                                        <span class="badge bg-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Urgent
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ Str::limit($announcement->content, 50) }}</td>
                            <td>{{ $announcement->display_order }}</td>
                            <td>
                                @if($announcement->start_date && $announcement->end_date)
                                    Du {{ $announcement->start_date->format('d/m/Y') }} au {{ $announcement->end_date->format('d/m/Y') }}
                                @elseif($announcement->start_date)
                                    À partir du {{ $announcement->start_date->format('d/m/Y') }}
                                @elseif($announcement->end_date)
                                    Jusqu'au {{ $announcement->end_date->format('d/m/Y') }}
                                @else
                                    Permanente
                                @endif
                            </td>
                            <td>
                                <span class="modern-badge status-badge {{ $announcement->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm toggle-status-btn {{ $announcement->is_active ? 'btn-warning' : 'btn-success' }}"
                                            data-id="{{ $announcement->id }}"
                                            data-current-status="{{ $announcement->is_active ? 'active' : 'inactive' }}">
                                        <i class="fas {{ $announcement->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucune annonce trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $announcements->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
(function() {
    console.log('🚀 Script des annonces démarré !');
    
    // Configuration toastr
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "showDuration": "200",
        "hideDuration": "800",
        "timeOut": "4000",
        "extendedTimeOut": "800",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
    };
    
    // Fonction pour changer le statut d'une annonce
    function toggleAnnouncementStatus(announcementId, button) {
        console.log('🔄 Changement de statut pour l\'annonce:', announcementId);
        
        const row = button.closest('tr');
        const statusBadge = row.querySelector('.status-badge');
        const url = `/Administrateur/announcements/${announcementId}/toggle-status`;
        
        console.log('URL:', url);
        
        // Ajouter l'état de chargement
        button.classList.add('loading-state');
        statusBadge.classList.add('updating');
        
        // Désactiver le bouton
        button.disabled = true;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Réponse reçue:', response.status, response.statusText);
            if (!response.ok) {
                return response.json().then(errorData => {
                    console.log('Données d\'erreur:', errorData);
                    throw new Error(`Erreur HTTP: ${response.status} - ${errorData.message || 'Erreur inconnue'}`);
                }).catch(() => {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            if (data.success) {
                // Mettre à jour le badge de statut
                statusBadge.textContent = data.status_text;
                statusBadge.className = `modern-badge status-badge ${data.status_class}`;
                
                // Mettre à jour le bouton
                button.className = `btn btn-sm toggle-status-btn ${data.button_class}`;
                button.querySelector('i').className = `fas ${data.button_icon}`;
                button.setAttribute('data-current-status', data.is_active ? 'active' : 'inactive');
                
                // Afficher le message de succès
                toastr.success(data.message);
                
                console.log('✅ Statut mis à jour avec succès');
            } else {
                throw new Error(data.message || 'Erreur lors de la modification du statut');
            }
        })
        .catch(error => {
            console.error('❌ Erreur:', error);
            toastr.error(error.message || 'Erreur lors de la modification du statut');
        })
        .finally(() => {
            // Retirer l'état de chargement
            button.classList.remove('loading-state');
            statusBadge.classList.remove('updating');
            button.disabled = false;
        });
    }
    
    // Attendre que le DOM soit chargé
    setTimeout(() => {
        console.log('🔍 Recherche des boutons de changement de statut...');
        
        // Boutons de changement de statut
        const toggleButtons = document.querySelectorAll('.toggle-status-btn');
        console.log('Boutons trouvés:', toggleButtons.length);
        
        toggleButtons.forEach((button, index) => {
            console.log(`Bouton ${index + 1}:`, button);
            button.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('🔥 CLIC TOGGLE DÉTECTÉ !');
                const announcementId = this.getAttribute('data-id');
                console.log('Announcement ID:', announcementId);
                toggleAnnouncementStatus(announcementId, this);
            });
        });
        
        // Gestionnaire d'événement pour la recherche
        const searchBtn = document.getElementById('searchBtn');
        if (searchBtn) {
            searchBtn.addEventListener('click', function() {
                const search = document.getElementById('announcementSearch').value;
                window.location.href = `{{ route('admin.announcements.index') }}?search=${search}`;
            });
        }
        
        // Gestionnaire d'événement pour la touche Entrée dans le champ de recherche
        const searchInput = document.getElementById('announcementSearch');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const search = this.value;
                    window.location.href = `{{ route('admin.announcements.index') }}?search=${search}`;
                }
            });
        }
        
        // Confirmation de suppression
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?')) {
                    this.submit();
                }
            });
        });
        
        console.log('✅ Tous les gestionnaires d\'événements attachés !');
        
    }, 1000);
    
    console.log('✅ Script des annonces chargé complètement !');
})();
</script>
@endpush
