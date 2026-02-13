@extends('layouts.dashboard')

@section('content')
<div class="container py-5">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('import_errors') && is_array(session('import_errors')) && count(session('import_errors')) > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs d'importation</h6>
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Event Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('custom-events.index') }}">Mes événements</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $event->title }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">{{ $event->title ?? 'Événement sans titre' }}</h1>
                    <div class="d-flex align-items-center mt-2">
                        @if($event->type)
                        <span class="badge bg-{{ ['mariage' => 'danger', 'anniversaire' => 'warning', 'conference' => 'info', 'autre' => 'secondary'][$event->type] ?? 'primary' }} me-2">
                            {{ ucfirst($event->type) }}
                        </span>
                        @endif
                        <span class="text-muted small">
                            <i class="far fa-calendar-alt me-1"></i>
                            @if($event->start_date)
                                {{ $event->start_date->format('d/m/Y H:i') }}
                                @if($event->end_date)
                                    - {{ $event->end_date->format('d/m/Y H:i') }}
                                @endif
                            @else
                                Date non définie
                            @endif
                        </span>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="eventActions" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="eventActions">
                        <li><a class="dropdown-item" href="{{ route('custom-events.edit', $event) }}"><i class="fas fa-edit me-2"></i>Modifier</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#shareEventModal"><i class="fas fa-share-alt me-2"></i>Partager</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('custom-events.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash-alt me-2"></i>Supprimer
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Event Image -->
            @if($event->image)
                <div class="card mb-4 border-0 shadow-sm">
                    <img src="{{ Storage::url($event->image) }}" class="card-img-top" alt="{{ $event->title }}" style="max-height: 400px; object-fit: cover;">
                </div>
            @endif

            <!-- Event Details -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Détails de l'événement</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <strong>Lieu :</strong> {{ $event->location ?? 'Non défini' }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-users text-primary me-2"></i>
                                <strong>Invités :</strong> {{ $event->guests->count() }} / {{ $event->guest_limit ?? '∞' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-calendar-day text-primary me-2"></i>
                                <strong>Date de création :</strong> {{ $event->created_at ? $event->created_at->format('d/m/Y') : 'N/A' }}
                            </p>
                            @if($event->invitation_link)
                            <p class="mb-2">
                                <i class="fas fa-link text-primary me-2"></i>
                                <strong>Lien d'invitation :</strong> 
                                <a href="{{ route('custom-events.invitation', $event->invitation_link) }}" target="_blank">
                                    Voir la page d'invitation
                                </a>
                            </p>
                            @else
                            <p class="mb-2">
                                <i class="fas fa-link text-primary me-2"></i>
                                <strong>Lien d'invitation :</strong> 
                                <span class="text-muted">Non disponible</span>
                            </p>
                            @endif
                        </div>
                    </div>
                    
                    @if($event->description)
                        <div class="mt-4">
                            <h6>Description :</h6>
                            <p class="card-text">{{ $event->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Guest List -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des invités</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                            <i class="fas fa-user-plus me-1"></i>Ajouter un invité
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importGuestsModal">
                            <i class="fas fa-file-import me-1"></i>Importer
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($event->guests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($event->guests as $guest)
                                        <tr>
                                            <td>{{ $guest->full_name }}</td>
                                            <td>{{ $guest->email ?? '-' }}</td>
                                            <td>{{ $guest->phone ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ ['pending' => 'secondary', 'confirmed' => 'success', 'cancelled' => 'danger', 'arrived' => 'primary'][$guest->status] ?? 'secondary' }}">
                                                    {{ ucfirst(__($guest->status)) }}
                                                </span>
                                                @if($guest->checked_in_at)
                                                    <span class="badge bg-success ms-1" title="Entré le {{ $guest->checked_in_at->format('d/m/Y à H:i') }}">
                                                        <i class="fas fa-check me-1"></i>Entré
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editGuestModal{{ $guest->id }}"><i class="fas fa-edit me-2"></i>Modifier</a></li>
                                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#sendInvitationModal{{ $guest->id }}"><i class="fas fa-envelope me-2"></i>Envoyer l'invitation</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('custom-events.guests.destroy', [$event, $guest]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet invité ?')">
                                                                    <i class="fas fa-trash-alt me-2"></i>Supprimer
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun invité pour le moment</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                                <i class="fas fa-user-plus me-2"></i>Ajouter un invité
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-primary">Actions rapides</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action text-primary {{ $event->canScheduleSms() ? '' : 'disabled' }}" data-bs-toggle="modal" data-bs-target="#sendInvitationsModal" {{ $event->canScheduleSms() ? '' : 'aria-disabled=true title=\'Disponible à partir de Standard\'' }}>
                        <i class="fas fa-paper-plane text-primary me-2"></i>Envoyer les invitations
                    </a>
                    <a href="{{ $event->hasExports() ? route('custom-events.guests.export', $event) : '#' }}" class="list-group-item list-group-item-action text-primary {{ $event->hasExports() ? '' : 'disabled' }}" {{ $event->hasExports() ? '' : 'aria-disabled=true title=\'Exports disponibles à partir de Premium\'' }}>
                        <i class="fas fa-file-export text-primary me-2"></i>Exporter la liste des invités
                    </a>
                    @if($event->canUseRealtimeCheckin() && $event->checkin_url)
                    <a href="{{ route('checkin.realtime', $event->checkin_url) }}" target="_blank" class="list-group-item list-group-item-action text-primary">
                        <i class="fas fa-qrcode text-primary me-2"></i>Check-in temps réel
                    </a>
                    @else
                    <a href="#" class="list-group-item list-group-item-action text-primary disabled" aria-disabled="true" title="Disponible à partir de Standard">
                        <i class="fas fa-qrcode text-primary me-2"></i>Check-in temps réel
                    </a>
                    @endif
                </div>
            </div>

            <!-- Event Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Inscriptions</span>
                            <span>{{ $event->guests->count() }} / {{ $event->guest_limit ?? '∞' }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $event->guest_limit ? min(100, ($event->guests->count() / $event->guest_limit) * 100) : 0 }}%" 
                                 aria-valuenow="{{ $event->guests->count() }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="{{ $event->guest_limit ?? $event->guests->count() }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-user-clock text-secondary me-2"></i> En attente</span>
                            <span class="badge bg-secondary rounded-pill">{{ $event->guests->where('status', 'pending')->whereNull('checked_in_at')->count() }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-user-check text-primary me-2"></i> Présents</span>
                            <span class="badge bg-primary rounded-pill">{{ $event->guests->where('status', 'arrived')->count() }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-times-circle text-danger me-2"></i> Annulés</span>
                            <span class="badge bg-danger rounded-pill">{{ $event->guests->where('status', 'cancelled')->count() }}</span>
                        </div>
                        @if($event->canUseRealtimeCheckin())
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-qrcode text-info me-2"></i> Entrés (check-in)</span>
                            <span class="badge bg-info rounded-pill">{{ $event->guests->whereNotNull('checked_in_at')->count() }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Event Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Plan :</strong> {{ ucfirst($event->offer_plan ?? 'start') }}
                    </div>
                    <div class="mb-3">
                        <strong>Date de création :</strong> {{ $event->created_at ? $event->created_at->format('d/m/Y à H:i') : 'N/A' }}
                    </div>
                    @if($event->invitation_link)
                    <div class="mb-3">
                        <strong>Lien d'invitation :</strong><br>
                        <a href="{{ route('custom-events.invitation', $event->invitation_link) }}" target="_blank" class="small">
                            {{ route('custom-events.invitation', $event->invitation_link) }}
                        </a>
                    </div>
                    @endif
                    @if($event->canUseRealtimeCheckin() && $event->checkin_url)
                    <div class="mb-3">
                        <strong>URL de check-in temps réel :</strong><br>
                        <div class="input-group input-group-sm mt-2">
                            <input type="text" class="form-control" value="{{ route('checkin.realtime', $event->checkin_url) }}" id="checkinUrlInput" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyCheckinUrl()">
                                <i class="fas fa-copy"></i>
                            </button>
                            <a href="{{ route('checkin.realtime', $event->checkin_url) }}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        <small class="text-muted">Ouvrez cette URL pour gérer les check-ins en temps réel</small>
                    </div>
                    @elseif($event->canUseRealtimeCheckin() && !$event->checkin_url)
                    <div class="mb-3">
                        <form action="{{ route('custom-events.generate-checkin-url', $event) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-link me-1"></i>Générer l'URL de check-in
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('custom-events.modals.add-guest')
@include('custom-events.modals.edit-guest')
@include('custom-events.modals.send-invitation')
@include('custom-events.modals.send-invitations')
@include('custom-events.modals.import-guests')
@include('custom-events.modals.check-in')
@include('custom-events.modals.share-event')

@endsection

