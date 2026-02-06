@extends('layouts.dashboard')

@section('title', 'Mon compte')

@section('content')
<div class="row">
    <!-- User Profile & Stats -->
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="modern-card mb-4 text-center p-4">
            <div class="mb-4 position-relative d-inline-block">
                @if(Auth::user()->profile_photo_path)
                    <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                         alt="{{ Auth::user()->name }}"
                         class="rounded-circle shadow-lg border border-4 border-white"
                         style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-gradient-blue d-flex align-items-center justify-content-center shadow-lg border border-4 border-white"
                         style="width: 120px; height: 120px; margin: 0 auto;">
                        <span class="text-white display-4 fw-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                @endif
                <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white p-2"></div>
            </div>
            
            <h4 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h4>
            <p class="text-muted mb-4">{{ Auth::user()->email }}</p>
            
            <div class="d-grid gap-2">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary-modern">
                    <i class="fas fa-edit"></i> Modifier le profil
                </a>
                @if(!Auth::user()->hasRole('organizer'))
                    <a href="{{ route('organizer-request.create') }}" class="btn btn-outline-modern">
                        <i class="fas fa-star"></i> Devenir organisateur
                    </a>
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="stat-card mb-4">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $stats['tickets_count'] }}</div>
                    <div class="stat-label">Billets achetés</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>

        <div class="stat-card mb-4">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ $stats['events_attended'] }}</div>
                    <div class="stat-label">Événements</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-flag-checkered"></i>
                </div>
            </div>
        </div>

        <div class="stat-card mb-4">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-number">{{ number_format($stats['total_spent'], 0, ',', ' ') }} FCFA</div>
                    <div class="stat-label">Total dépensé</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Upcoming Events -->
        <div class="modern-card mb-4">
            <div class="card-header-modern">
                <div class="card-title">
                    <i class="fas fa-calendar-alt"></i>
                    Mes prochains événements
                </div>
                <a href="{{ route('tickets.my-tickets') }}" class="btn btn-sm btn-outline-light">
                    Voir tous mes billets
                </a>
            </div>
            <div class="card-body-modern p-0">
                <div class="list-group list-group-flush">
                    @forelse($upcomingEvents as $event)
                        <div class="list-group-item p-4 border-bottom">
                            <div class="d-flex align-items-center">
                                @if($event->image)
                                    <img src="{{ asset('storage/' . $event->image) }}"
                                         alt="{{ $event->title }}"
                                         class="rounded-3 shadow-sm"
                                         style="width: 64px; height: 64px; object-fit: cover;">
                                @else
                                    <div class="rounded-3 bg-gradient-blue d-flex align-items-center justify-content-center shadow-sm"
                                         style="width: 64px; height: 64px;">
                                        <i class="fas fa-calendar-event text-white fa-lg"></i>
                                    </div>
                                @endif

                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-bold text-dark">{{ $event->title }}</h6>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $event->start_date->format('d/m/Y H:i') }}
                                    </p>
                                </div>

                                <a href="{{ route('events.show', $event) }}"
                                   class="btn btn-sm btn-primary-modern">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-ticket-alt fa-3x text-muted opacity-50"></i>
                            </div>
                            <p class="text-muted mb-3">Vous n'avez pas encore de billets pour des événements à venir.</p>
                            <a href="{{ route('events.index') }}" class="btn btn-primary-modern">Découvrir les événements</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="modern-card">
            <div class="card-header-modern">
                <div class="card-title">
                    <i class="fas fa-shopping-bag"></i>
                    Mes dernières commandes
                </div>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-light">
                    Voir toutes mes commandes
                </a>
            </div>
            <div class="card-body-modern p-0">
                <div class="list-group list-group-flush">
                    @forelse($orders as $order)
                        <div class="list-group-item p-4 border-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Commande #{{ $order->order_number }}</h6>
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-clock me-1"></i> {{ $order->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <h6 class="fw-bold text-success mb-1">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</h6>
                                    <span class="modern-badge badge-{{ $order->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ $order->status === 'completed' ? 'Payée' : 'En attente' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-shopping-cart fa-3x text-muted opacity-50"></i>
                            </div>
                            <p class="text-muted mb-3">Vous n'avez pas encore effectué de commande.</p>
                            <a href="{{ route('events.index') }}" class="btn btn-primary-modern">Découvrir les événements</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
