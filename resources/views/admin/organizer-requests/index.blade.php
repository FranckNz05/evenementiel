@extends('layouts.dashboard')

@section('title', 'Demandes d\'Organisateur')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Demandes d'Organisateur</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Administration</a></li>
                    <li class="breadcrumb-item active">Demandes d'organisateur</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card modern-card shadow-sm mb-4">
        <div class="card-header-modern">
            <h5 class="card-title my-1 text-white">
                <i class="fas fa-id-card me-2"></i>Dossiers de candidature en attente
            </h5>
        </div>
        <div class="card-body p-0">
            @if($requests->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-folder-open fa-4x text-light"></i>
                    </div>
                    <h5 class="text-muted">Aucune demande en attente</h5>
                    <p class="text-muted small">Toutes les candidatures d'organisateurs ont été traitées.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table modern-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Date de dépôt</th>
                                <th>Utilisateur</th>
                                <th>Entreprise / Structure</th>
                                <th>Contact Coordonnées</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td class="ps-4">
                                        <div class="small fw-bold text-dark">{{ $request->created_at->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $request->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $request->user->name }}</div>
                                        <small class="text-muted">ID: #{{ $request->user->id }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $request->company_name }}</div>
                                        <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($request->company_address, 30) }}</div>
                                    </td>
                                    <td>
                                        <div class="small text-dark"><i class="fas fa-envelope me-1 text-muted"></i>{{ $request->company_email }}</div>
                                        <div class="small text-dark"><i class="fas fa-phone me-1 text-muted"></i>{{ $request->company_phone }}</div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group shadow-sm">
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $request->id }}" title="Consulter le dossier">
                                                <i class="fas fa-eye"></i> Dossier
                                            </button>
                                            <form action="{{ route('admin.organizer-requests.approve', $request->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Approuver cet utilisateur en tant qu\'organisateur ?')" title="Approuver">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}" title="Rejeter la demande">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@foreach($requests as $request)
    <!-- Modal Détails -->
    <div class="modal fade" id="detailsModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-file-alt me-2 text-primary"></i>Dossier de candidature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold mb-3 d-block border-bottom pb-1">Informations Entreprise</label>
                            <div class="mb-3">
                                <small class="text-muted d-block">Nom Commercial / Structure</small>
                                <p class="fw-bold text-dark mb-0">{{ $request->company_name }}</p>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Coordonnées Email</small>
                                <p class="text-dark mb-0">{{ $request->company_email }}</p>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Téléphone Professionnel</small>
                                <p class="text-dark mb-0">{{ $request->company_phone }}</p>
                            </div>
                            <div class="mb-0">
                                <small class="text-muted d-block">Siège Social</small>
                                <p class="text-dark mb-0">{{ $request->company_address }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 border-start-md">
                            <label class="small text-muted text-uppercase fw-bold mb-3 d-block border-bottom pb-1">Motivations / Profil</label>
                            <div class="p-3 bg-light rounded border mb-4">
                                <p class="small text-dark mb-0 italic">"{{ $request->description }}"</p>
                            </div>

                            @if(!empty($request->documents))
                                <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Pièces Jointes</label>
                                <div class="list-group list-group-flush border rounded overflow-hidden">
                                    @foreach($request->documents as $document)
                                        <a href="{{ Storage::url($document) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center small p-2">
                                            <i class="fas fa-file-pdf text-danger me-2 fa-lg"></i> 
                                            <span class="text-truncate">Consulter le document {{ $loop->iteration }}</span>
                                            <i class="fas fa-external-link-alt ms-auto text-muted" style="font-size: 0.7rem;"></i>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Fermer</button>
                    <form action="{{ route('admin.organizer-requests.approve', $request->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 shadow-sm" onclick="return confirm('Confirmer l\'approbation ?')">
                            <i class="fas fa-check me-1"></i> Approuver
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rejet -->
    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-times me-2"></i>Rejeter la demande</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.organizer-requests.reject', $request->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <p class="text-dark mb-0">Êtes-vous sûr de vouloir rejeter la demande d'organisateur de <strong>{{ $request->user->name }}</strong> ?</p>
                        <p class="small text-muted mt-2">Cette action est irréversible. L'utilisateur devra soumettre une nouvelle demande.</p>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger px-4 shadow-sm">Confirmer le rejet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
