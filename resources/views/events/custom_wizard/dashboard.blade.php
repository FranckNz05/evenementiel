@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-calendar-alt me-2"></i>Mes événements personnalisés</h1>
        <div class="page-actions">
            @if($unusedPurchases->count() > 0)
                <a href="{{ route('custom-events.wizard.step1', ['purchase' => $unusedPurchases->first()->id]) }}" class="modern-btn btn-success-modern">
                    <i class="fas fa-plus"></i> Créer un événement ({{ ucfirst($unusedPurchases->first()->plan) }})
                </a>
            @else
                <a href="{{ route('custom-offers.index') }}" class="modern-btn btn-warning-modern" title="Aucune offre disponible">
                    <i class="fas fa-star"></i> Choisir une formule
                </a>
            @endif
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="modern-card mb-4">
                    <div class="card-header-modern">
                        <div class="card-title"><i class="fas fa-clock"></i> Historique des événements</div>
                    </div>
                    <div class="card-body-modern">
                        @if($events->count() > 0)
                            <div class="table-responsive">
                                <table class="modern-table">
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Type</th>
                                            <th>Dates</th>
                                            <th>Invités</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($events as $event)
                                            <tr>
                                                <td>{{ $event->title }}</td>
                                                <td>{{ ucfirst($event->type) }}</td>
                                                <td>{{ $event->start_date?->format('d/m/Y H:i') }} @if($event->end_date) - {{ $event->end_date->format('d/m/Y H:i') }} @endif</td>
                                                <td>{{ $event->guests->count() }} / {{ $event->guest_limit ?? '∞' }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('custom-events.show', $event) }}" class="modern-btn btn-primary-modern btn-sm-modern">
                                                        <i class="fas fa-eye"></i> Ouvrir
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">Aucun événement personnalisé pour le moment.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="modern-card mb-4">
                    <div class="card-header-modern">
                        <div class="card-title"><i class="fas fa-receipt"></i> Mes offres</div>
                    </div>
                    <div class="card-body-modern">
                        @if($unusedPurchases->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($unusedPurchases as $purchase)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ ucfirst($purchase->plan) }} • {{ number_format($purchase->price, 0, ',', ' ') }} FCFA</span>
                                        <a href="{{ route('custom-events.wizard.step1', ['purchase' => $purchase->id]) }}" class="modern-btn btn-success-modern btn-sm-modern">
                                            Utiliser
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted mb-2">Aucune offre disponible.</p>
                            <a href="{{ route('custom-offers.index') }}" class="modern-btn btn-warning-modern">
                                <i class="fas fa-star"></i> Acheter une formule
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


