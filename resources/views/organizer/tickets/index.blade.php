@extends('layouts.dashboard')

@section('title', 'Billets')

@section('content')
<div class="container-fluid px-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="fas fa-ticket-alt me-2"></i>
            Liste des billets
        </h5>
        <a href="{{ route('organizer.events.index') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Créer un billet
        </a>
    </div>
    @if($tickets->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="ticketsTable">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Événement</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Vendus</th>
                    <th>Disponibles</th>
                    <th>Période de vente</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->nom ?? $ticket->name ?? 'Sans nom' }}</td>
                    <td>{{ $ticket->event->title ?? $ticket->event->titre ?? 'Événement inconnu' }}</td>
                    <td>{{ number_format($ticket->prix ?? $ticket->price ?? 0, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $ticket->quantite ?? $ticket->quantity ?? 0 }}</td>
                    <td>{{ $ticket->sold_count ?? $ticket->quantite_vendue ?? 0 }}</td>
                    <td>{{ ($ticket->quantite ?? $ticket->quantity ?? 0) - ($ticket->sold_count ?? $ticket->quantite_vendue ?? 0) }}</td>
                    <td>
                        <small>
                            @if($ticket->promotion_start)
                                Promo du {{ $ticket->promotion_start->format('d/m/Y') }}<br>
                                au {{ $ticket->promotion_end ? $ticket->promotion_end->format('d/m/Y') : 'Non défini' }}
                            @else
                                Pas de période de vente spécifique
                            @endif
                        </small>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('organizer.tickets.show', $ticket) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('organizer.tickets.edit', $ticket) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('organizer.tickets.destroy', $ticket) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce billet?')">
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
    
    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
    @else
    <div class="alert alert-info">
        Vous n'avez pas encore créé de billets. 
        <a href="{{ route('organizer.events.index') }}" class="alert-link">Créez d'abord un événement</a>, 
        puis ajoutez des billets à cet événement.
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#ticketsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json',
            }
        });
    });
</script>
@endsection
