@extends('layouts.dashboard')

@section('title', 'Demandes d\'organisateur en attente')

@section('content')
<div class="modern-card">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Demandes d'organisateur en attente</h6>
        <div class="dropdown no-arrow">
            <a href="{{ route('admin.organizers') }}" class="btn btn-sm btn-secondary">
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

        @if($organizers->isEmpty())
            <div class="alert alert-info">
                Aucune demande d'organisateur en attente.
            </div>
        @else
            <div class="table-responsive">
                <table class="modern-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Entreprise</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Date de demande</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($organizers as $organizer)
                        <tr>
                            <td>{{ $organizer->user->name ?? 'N/A' }}</td>
                            <td>
                                @if($organizer->logo)
                                    <img src="{{ $organizer->logo }}" alt="{{ $organizer->company_name }}" class="img-thumbnail mr-2" style="width: 40px; height: 40px; object-fit: cover;">
                                @endif
                                {{ $organizer->company_name }}
                            </td>
                            <td>{{ $organizer->email }}</td>
                            <td>{{ $organizer->phone_primary }}</td>
                            <td>{{ $organizer->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.organizers.show', $organizer) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.organizers.approve', $organizer) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir approuver cette demande d\'organisateur ?')">
                                            <i class="fas fa-check"></i> Approuver
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.organizers.reject', $organizer) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette demande d\'organisateur ?')">
                                            <i class="fas fa-times"></i> Rejeter
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
                {{ $organizers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
