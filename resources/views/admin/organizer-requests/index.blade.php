@extends('layouts.Administrateur')

@section('title', 'Demandes d\'organisateur')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Demandes d'organisateurs</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Demandes d'organisateurs</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Liste des demandes
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Utilisateur</th>
                                <th>Entreprise</th>
                                <th>Contact</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        {{ $request->user->prenom }} {{ $request->user->nom }}<br>
                                        <small class="text-muted">{{ $request->user->email }}</small>
                                    </td>
                                    <td>{{ $request->company_name }}</td>
                                    <td>
                                        <strong>Email:</strong> {{ $request->email }}<br>
                                        <strong>Tél:</strong> {{ $request->phone_primary }}
                                    </td>
                                    <td>
                                        @if($request->status == 'en attente')
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @elseif($request->status == 'approuvé')
                                            <span class="badge bg-success">Approuvée</span>
                                        @elseif($request->status == 'rejeté')
                                            <span class="badge bg-danger">Rejetée</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->status == 'en attente')
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('admin.organizer-requests.approve', $request->id) }}" method="POST" class="me-2">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> Approuver
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $request->id }}">
                                                    <i class="fas fa-times"></i> Rejeter
                                                </button>
                                            </div>

                                            <!-- Modal de rejet -->
                                            <div class="modal fade" id="rejectModal-{{ $request->id }}" tabindex="-1" aria-labelledby="rejectModalLabel-{{ $request->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectModalLabel-{{ $request->id }}">Rejeter la demande</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.organizer-requests.reject', $request->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="rejection_reason" class="form-label">Motif du rejet</label>
                                                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                                    <div class="form-text">Ce message sera visible par l'utilisateur.</div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                <button type="submit" class="btn btn-danger">Confirmer le rejet</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Aucune action disponible</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i> Aucune demande d'organisateur pour le moment.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
