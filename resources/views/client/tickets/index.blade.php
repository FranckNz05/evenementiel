@extends('layouts.app')

@section('title', 'Mes Billets')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes Billets</h5>
                    <a href="{{ route('tickets.export') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel me-1"></i> Exporter
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Événement</th>
                                    <th>Type de billet</th>
                                    <th>Date de l'événement</th>
                                    <th>Prix</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->ticket_code ?? 'N/A' }}</td>
                                        <td>
                                            @if($ticket->event)
                                                {{ $ticket->event->title }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->nom }}</td>
                                        <td>
                                            @if($ticket->event && $ticket->event->start_date)
                                                {{ \Carbon\Carbon::parse($ticket->event->start_date)->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($ticket->prix, 0, ',', ' ') }} XAF</td>
                                        <td>
                                            <span class="badge bg-{{ $ticket->statut === 'disponible' ? 'success' : 'warning' }}">
                                                {{ ucfirst($ticket->statut) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('tickets.show', $ticket) }}"
                                                   class="btn btn-sm btn-info"
                                                   title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($ticket->statut === 'disponible')
                                                    <a href="{{ route('tickets.download', $ticket) }}"
                                                       class="btn btn-sm btn-success"
                                                       title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-ticket-alt fa-2x mb-3"></i>
                                                <p>Vous n'avez pas encore de billets</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


