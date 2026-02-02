@extends('layouts.dashboard')

@section('title', 'Gestion des abonnés à la newsletter')

@section('content')
<div class="modern-card">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Liste des abonnés à la newsletter</h6>
        <div class="dropdown no-arrow">
            <a href="{{ route('admin.newsletter.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-paper-plane fa-sm"></i> Envoyer une newsletter
            </a>
            <a href="{{ route('admin.newsletter.export') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-download fa-sm"></i> Exporter
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

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="subscriberSearch" placeholder="Rechercher un abonné...">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="subscriberStatus">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="subscriberSort">
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date d'inscription</option>
                    <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscribers as $subscriber)
                    <tr>
                        <td>{{ $subscriber->id }}</td>
                        <td>{{ $subscriber->name ?: 'Non renseigné' }}</td>
                        <td>{{ $subscriber->email }}</td>
                        <td>
                            @if($subscriber->is_active)
                                <span class="modern-badge badge-success">Actif</span>
                            @else
                                <span class="modern-badge badge-warning">Inactif</span>
                            @endif
                        </td>
                        <td>{{ $subscriber->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                @if($subscriber->is_active)
                                    <form action="{{ route('admin.newsletter.deactivate', $subscriber) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet abonné ?')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.newsletter.activate', $subscriber) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir activer cet abonné ?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.newsletter.destroy', $subscriber) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet abonné ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $subscribers->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestionnaire d'événement pour la recherche
    document.getElementById('searchBtn').addEventListener('click', function() {
        const search = document.getElementById('subscriberSearch').value;
        window.location.href = `{{ route('admin.newsletter.index') }}?search=${search}`;
    });

    // Gestionnaire d'événement pour la touche Entrée dans le champ de recherche
    document.getElementById('subscriberSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const search = this.value;
            window.location.href = `{{ route('admin.newsletter.index') }}?search=${search}`;
        }
    });

    // Gestionnaire d'événement pour le filtre par statut
    document.getElementById('subscriberStatus').addEventListener('change', function() {
        const status = this.value;
        window.location.href = `{{ route('admin.newsletter.index') }}?status=${status}`;
    });

    // Gestionnaire d'événement pour le tri
    document.getElementById('subscriberSort').addEventListener('change', function() {
        const sort = this.value;
        window.location.href = `{{ route('admin.newsletter.index') }}?sort=${sort}`;
    });

    // Initialisation des graphiques
    fetch('{{ url('admin/api/newsletter/stats') }}')
        .then(response => response.json())
        .then(data => {
            // Graphique de l'évolution des abonnements
            const subscribersChart = new Chart(document.getElementById('subscribersChart'), {
                type: 'line',
                data: {
                    labels: data.by_month.labels,
                    datasets: [{
                        label: 'Nouveaux abonnés',
                        data: data.by_month.data,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Graphique de la répartition par statut
            const statusChart = new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Actifs', 'Inactifs'],
                    datasets: [{
                        data: [data.active_count, data.inactive_count],
                        backgroundColor: ['#1cc88a', '#f6c23e'],
                        hoverBackgroundColor: ['#17a673', '#dda20a'],
                        hoverBorderColor: 'rgba(234, 236, 244, 1)',
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des statistiques:', error);
        });
});
</script>
@endpush
