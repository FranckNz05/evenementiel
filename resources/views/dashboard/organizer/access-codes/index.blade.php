@extends('layouts.dashboard')

@section('title', 'Gestion des codes d\'accès')

@section('content')
<div class="container-fluid">
    <div class="modern-card">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Mes codes d'accès</h6>
            <a href="{{ route('access-codes.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Nouveau code
            </a>
        </div>
        <div class="card-body-modern">
            <div class="table-responsive">
                <table class="modern-table" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Id</th>
                            <th>Événement</th>
                            <th>Valide du</th>
                            <th>Valide jusqu'au</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($codes as $code)
                        <tr>
                            <td><code>{{ $code->access_code }}</code></td>
                            <td>{{ $code->event->id ?? 'N0' }}</td>
                            <td>{{ $code->event->title ?? 'Tous les événements' }}</td>
                            <td>{{ $code->valid_from->format('d/m/Y H:i') }}</td>
                            <td>{{ $code->valid_until->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($code->is_active && $code->valid_until >= now())
                                    <span class="modern-badge badge-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                            <form action="{{ route('access-codes.destroy', $code->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $codes->links() }}
        </div>
    </div>
</div>
@endsection