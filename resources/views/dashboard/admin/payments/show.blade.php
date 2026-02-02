@extends('layouts.dashboard')

@section('title', 'Détails du paiement')

@section('content')
<div class="modern-card">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Détails du paiement #{{ $payment->id }}</h6>
        <div class="dropdown no-arrow">
            <a href="{{ route('admin.payments') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm"></i> Retour à la liste
            </a>
            <a href="{{ route('admin.payments.download', $payment) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-download fa-sm"></i> Télécharger la facture
            </a>
        </div>
    </div>
    <div class="card-body-modern">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Informations du paiement</h6>
                    </div>
                    <div class="card-body-modern">
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">ID:</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext">{{ $payment->id }}</p>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Référence:</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext">{{ $payment->matricule }}</p>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Montant:</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext">{{ number_format($payment->montant, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Mode de paiement:</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext">{{ $payment->methode_paiement }}</p>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Statut:</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext">
                                    @if($payment->statut == 'payé')
                                        <span class="modern-badge badge-success">Payé</span>
                                    @elseif($payment->statut == 'en attente')
                                        <span class="modern-badge badge-warning">En attente</span>
                                    @elseif($payment->statut == 'annulé')
                                        <span class="modern-badge badge-danger">Annulé</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $payment->statut }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Date:</label>
                            <div class="col-sm-8">
                                <p class="form-control-plaintext">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Informations de l'utilisateur</h6>
                    </div>
                    <div class="card-body-modern">
                        @if($payment->order && $payment->order->user)
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label fw-bold">Nom:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">{{ $payment->order->user->name }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label fw-bold">Email:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">{{ $payment->order->user->email }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label fw-bold">Téléphone:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">{{ $payment->order->user->phone ?? 'Non renseigné' }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label fw-bold">Adresse:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">{{ $payment->order->user->address ?? 'Non renseignée' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-center">Aucune information utilisateur disponible</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Détails de la reservation</h6>
                    </div>
                    <div class="card-body-modern">
                        @if($payment->order)
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label fw-bold">ID de reservation:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $payment->order->id }}</p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label fw-bold">Événement:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">
                                        @if($payment->order->event)
                                            <a href="{{ route('events.show', $payment->order->event) }}" target="_blank">
                                                {{ $payment->order->event->title }}
                                            </a>
                                        @else
                                            Non disponible
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label fw-bold">Date de l'événement:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">
                                        @if($payment->order->event)
                                            {{ $payment->order->event->start_date->format('d/m/Y H:i') }}
                                        @else
                                            Non disponible
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label fw-bold">Tickets:</label>
                                <div class="col-sm-10">
                                    <div class="table-responsive">
                                        <table class="modern-table">
                                            <thead>
                                                <tr>
                                                    <th>Type de ticket</th>
                                                    <th>Prix unitaire</th>
                                                    <th>Quantité</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($payment->order->tickets && $payment->order->tickets->count() > 0)
                                                    @foreach($payment->order->tickets as $ticket)
                                                        <tr>
                                                            <td>{{ $ticket->ticket_type->name ?? 'Type inconnu' }}</td>
                                                            <td>{{ number_format($ticket->price, 0, ',', ' ') }} FCFA</td>
                                                            <td>{{ $ticket->quantity }}</td>
                                                            <td>{{ number_format($ticket->price * $ticket->quantity, 0, ',', ' ') }} FCFA</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">Aucun ticket disponible</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-center">Aucune information de reservation disponible</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
