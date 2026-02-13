@extends('layouts.dashboard')

@section('title', 'Tableau de bord')

@section('content')
<div class="container py-5">
    <!-- Profil -->
    <div class="row">
        <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="mb-4">Mon profil</h4>
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Prénom</label>
                                        <input type="text" name="prenom" class="form-control"
                                               value="{{ auth()->user()->prenom }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nom</label>
                                        <input type="text" name="nom" class="form-control"
                                               value="{{ auth()->user()->nom }}" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control"
                                           value="{{ auth()->user()->email }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" name="phone" class="form-control"
                                           value="{{ auth()->user()->phone }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Genre</label>
                                    <select name="genre" class="form-select">
                                        <option value="">Sélectionner</option>
                                        <option value="M" {{ auth()->user()->genre === 'M' ? 'selected' : '' }}>Homme</option>
                                        <option value="F" {{ auth()->user()->genre === 'F' ? 'selected' : '' }}>Féminin</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tranche d'âge</label>
                                    <select name="tranche_age" class="form-select">
                                        <option value="">Sélectionner</option>
                                        <option value="0-17" {{ auth()->user()->tranche_age === '0-17' ? 'selected' : '' }}>0-17 ans</option>
                                        <option value="18-25" {{ auth()->user()->tranche_age === '18-25' ? 'selected' : '' }}>18-25 ans</option>
                                        <option value="26-35" {{ auth()->user()->tranche_age === '26-35' ? 'selected' : '' }}>26-35 ans</option>
                                        <option value="36-45" {{ auth()->user()->tranche_age === '36-45' ? 'selected' : '' }}>36-45 ans</option>
                                        <option value="46+" {{ auth()->user()->tranche_age === '46+' ? 'selected' : '' }}>46+ ans</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                            </form>
                        </div>
                    </div>
        </div>
    </div>

    <!-- Réservations -->
    <div class="row mt-4">
        <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="mb-4">Mes réservations</h4>
                            @if($reservations->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Événement</th>
                                                <th>Ticket</th>
                                                <th>Quantité</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reservations as $reservation)
                                                <tr>
                                                    <td>{{ $reservation->ticket->event->title }}</td>
                                                    <td>{{ $reservation->ticket->nom }}</td>
                                                    <td>{{ $reservation->quantity }}</td>
                                                    <td>
                                                        @if($reservation->status === 'payé')
                                                            <span class="badge bg-success">Payé</span>
                                                        @elseif($reservation->status === 'réservé')
                                                            <span class="badge bg-warning">Réservé</span>
                                                        @else
                                                            <span class="badge bg-danger">Annulé</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('reservations.show', $reservation) }}"
                                                           class="btn btn-sm btn-primary">
                                                            Détails
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $reservations->links() }}
                            @else
                                <div class="alert alert-info">
                                    Vous n'avez pas encore de réservation.
                                </div>
                            @endif
                        </div>
                    </div>
        </div>
    </div>

    <!-- Paiements -->
    <div class="row mt-4">
        <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="mb-4">Mes paiements</h4>
                            @if($payments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Référence</th>
                                                <th>Événement</th>
                                                <th>Montant</th>
                                                <th>Statut</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->id }}</td>
                                                    <td>{{ $payment->event->title }}</td>
                                                    <td>{{ number_format($payment->montant, 2) }} FC</td>
                                                    <td>
                                                        @if($payment->statut === 'payé')
                                                            <span class="badge bg-success">Payé</span>
                                                        @elseif($payment->statut === 'en attente')
                                                            <span class="badge bg-warning">En attente</span>
                                                        @else
                                                            <span class="badge bg-danger">Échoué</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if($payment->statut === 'payé')
                                                            <a href="{{ route('payments.success', $payment) }}"
                                                               class="btn btn-sm btn-primary">
                                                                Voir le ticket
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $payments->links() }}
                            @else
                                <div class="alert alert-info">
                                    Vous n'avez pas encore effectué de paiement.
                                </div>
                            @endif
                        </div>
                    </div>
        </div>
    </div>

    <!-- Favoris -->
    <div class="row mt-4">
        <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="mb-4">Mes événements favoris</h4>
                            @if($favorites->count() > 0)
                                <div class="row g-4">
                                    @foreach($favorites as $favorite)
                                        <div class="col-md-6">
                                            <div class="card">
                                                <img src="{{ asset($favorite->event->image) }}"
                                                     class="card-img-top" alt="{{ $favorite->event->title }}">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $favorite->event->title }}</h5>
                                                    <p class="card-text">
                                                        <i class="far fa-calendar-alt text-primary me-2"></i>
                                                        {{ $favorite->event->start_date->format('d M Y') }}
                                                    </p>
                                                    <a href="{{ route('events.show', $favorite->event) }}"
                                                       class="btn btn-primary">
                                                        Voir l'événement
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                {{ $favorites->links() }}
                            @else
                                <div class="alert alert-info">
                                    Vous n'avez pas encore d'événements favoris.
                                </div>
                            @endif
                        </div>
                    </div>
        </div>
    </div>
</div>
@endsection
