@extends('layouts.dashboard')

@section('title', 'Détails de l\'organisateur')

@section('content')
<div class="modern-card">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Détails de l'organisateur</h6>
        <div class="dropdown no-arrow">
            <a href="{{ route('admin.organizers') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm"></i> Retour à la liste
            </a>
            <a href="{{ route('admin.organizers.edit', $organizer) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit fa-sm"></i> Modifier
            </a>
        </div>
    </div>
    <div class="card-body-modern">
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <img src="{{ $organizer->logoUrl }}" alt="{{ $organizer->company_name }}" class="img-fluid rounded mb-3" style="max-height: 200px;">
                <h4 class="font-weight-bold">{{ $organizer->company_name }}</h4>
                @if($organizer->is_verified)
                    <span class="modern-badge badge-success">Vérifié</span>
                @else
                    <span class="modern-badge badge-warning">Non vérifié</span>
                @endif
                <p class="text-muted mt-2">{{ $organizer->slogan }}</p>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Informations de contact</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Utilisateur associé:</label>
                            <p>{{ $organizer->user->name ?? 'Non associé' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p>{{ $organizer->email }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Téléphone principal:</label>
                            <p>{{ $organizer->phone_primary }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Téléphone secondaire:</label>
                            <p>{{ $organizer->phone_secondary ?: 'Non renseigné' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Site web:</label>
                            <p>
                                @if($organizer->website)
                                    <a href="{{ $organizer->website }}" target="_blank">{{ $organizer->website }}</a>
                                @else
                                    Non renseigné
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Localisation</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adresse:</label>
                            <p>{{ $organizer->address ?: 'Non renseignée' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ville:</label>
                            <p>{{ $organizer->city ?: 'Non renseignée' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pays:</label>
                            <p>{{ $organizer->country ?: 'Non renseigné' }}</p>
                        </div>
                        <h5 class="font-weight-bold mt-4">Réseaux sociaux</h5>
                        <div class="d-flex gap-2">
                            @if(isset($organizer->social_media['facebook']))
                                <a href="{{ $organizer->social_media['facebook'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fab fa-facebook"></i>
                                </a>
                            @endif
                            @if(isset($organizer->social_media['twitter']))
                                <a href="{{ $organizer->social_media['twitter'] }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            @if(isset($organizer->social_media['instagram']))
                                <a href="{{ $organizer->social_media['instagram'] }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if(isset($organizer->social_media['linkedin']))
                                <a href="{{ $organizer->social_media['linkedin'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <h5 class="font-weight-bold">Description</h5>
                <div class="card">
                    <div class="card-body-modern">
                        {!! sanitize_html($organizer->description) ?: 'Aucune description disponible' !!}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <h5 class="font-weight-bold">Statistiques</h5>
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body-modern">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Événements</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['events_count'] }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body-modern">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Tickets vendus</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['tickets_sold'] }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body-modern">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Revenus</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['revenue'], 0, ',', ' ') }} FCFA</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body-modern">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Note moyenne</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['average_rating'], 1) }}/5</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-star fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <h5 class="font-weight-bold">Événements</h5>
                @if($organizer->events->count() > 0)
                    <div class="table-responsive">
                        <table class="modern-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Date</th>
                                    <th>Lieu</th>
                                    <th>Tickets vendus</th>
                                    <th>Revenus</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($organizer->events as $event)
                                <tr>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->start_date->format('d/m/Y H:i') }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>{{ $event->tickets->count() }}</td>
                                    <td>{{ number_format($event->payments->where('statut', 'payé')->sum('montant'), 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Cet organisateur n'a pas encore créé d'événements.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

