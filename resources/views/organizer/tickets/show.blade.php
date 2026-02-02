@extends('layouts.dashboard')

@section('title', 'Détails du billet')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails du billet</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('organizer.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('organizer.tickets.index') }}">Billets</a></li>
        <li class="breadcrumb-item active">Détails</li>
    </ol>

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

    <div class="row">
        <!-- Informations sur le billet -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-ticket-alt me-1"></i>
                    Informations sur le billet
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:30%">Nom</th>
                            <td>{{ $ticket->nom ?? $ticket->name ?? 'Sans nom' }}</td>
                        </tr>
                        <tr>
                            <th>Événement</th>
                            <td>
                                <a href="{{ route('organizer.events.show', $ticket->event_id) }}">
                                    {{ $ticket->event->title ?? $ticket->event->titre ?? 'Événement inconnu' }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Prix</th>
                            <td>{{ number_format($ticket->prix ?? $ticket->price ?? 0, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr>
                            <th>Quantité totale</th>
                            <td>{{ $ticket->quantite ?? $ticket->quantity ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>Quantité vendue</th>
                            <td>{{ $ticket->sold_count ?? $ticket->quantite_vendue ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>Quantité disponible</th>
                            <td>{{ ($ticket->quantite ?? $ticket->quantity ?? 0) - ($ticket->sold_count ?? $ticket->quantite_vendue ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $ticket->description ?? 'Aucune description' }}</td>
                        </tr>
                        <tr>
                            <th>Statut</th>
                            <td>
                                @if($ticket->statut == 'actif' || $ticket->status == 'active')
                                    <span class="badge bg-success">Actif</span>
                                @elseif($ticket->statut == 'inactif' || $ticket->status == 'inactive')
                                    <span class="badge bg-danger">Inactif</span>
                                @else
                                    <span class="badge bg-secondary">{{ $ticket->statut ?? $ticket->status ?? 'Non défini' }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="d-flex mt-3 gap-2">
                        <a href="{{ route('organizer.tickets.edit', $ticket) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                        <form action="{{ route('organizer.tickets.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce billet?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques du billet -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Statistiques du billet
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <h2 class="display-4 text-primary">{{ $ticket->sold_count ?? $ticket->quantite_vendue ?? 0 }}</h2>
                                    <p class="text-muted">Billets vendus</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <h2 class="display-4 text-success">{{ number_format($ticket->revenue ?? 0, 0, ',', ' ') }}</h2>
                                    <p class="text-muted">Revenus totaux (FCFA)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($ticket->promotion_start)
                    <div class="alert alert-info">
                        <h5 class="mb-2">Promotion</h5>
                        <p class="mb-1">
                            Prix promotionnel: <strong>{{ number_format($ticket->montant_promotionnel ?? 0, 0, ',', ' ') }} FCFA</strong><br>
                            Période: du {{ $ticket->promotion_start->format('d/m/Y') }} 
                                   au {{ $ticket->promotion_end ? $ticket->promotion_end->format('d/m/Y') : 'Non défini' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Ventes récentes -->
    @if(isset($sales) && $sales->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Ventes récentes
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="salesTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Quantité</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $order)
                        <tr>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $order->reference }}</td>
                            <td>{{ $order->user->prenom ?? '' }} {{ $order->user->nom ?? '' }}</td>
                            <td>{{ $order->pivot->quantity ?? 1 }}</td>
                            <td>{{ number_format($order->pivot->total_amount ?? $order->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        Aucune vente enregistrée pour ce billet.
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#salesTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json',
            }
        });
    });
</script>
@endsection
