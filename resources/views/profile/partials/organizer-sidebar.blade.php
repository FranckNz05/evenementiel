<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('organizer.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
            </a>
            <a href="{{ route('organizer.profile.edit') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.profile.*') ? 'active' : '' }}">
                <i class="fas fa-user-edit me-2"></i> Mon profil
            </a>
            <a href="{{ route('organizer.events.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.events.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt me-2"></i> Mes événements
            </a>
            <a href="{{ route('organizer.events.create') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.events.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle me-2"></i> Créer un événement
            </a>
            <a href="{{ route('organizer.tickets.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.tickets.*') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt me-2"></i> Billets
            </a>
            <a href="{{ route('organizer.scans.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.scans.*') ? 'active' : '' }}">
                <i class="fas fa-qrcode me-2"></i> Scanner des billets
            </a>
            <a href="{{ route('organizer.payments.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.payments.*') ? 'active' : '' }}">
                <i class="fas fa-credit-card me-2"></i> Paiements
            </a>
            <a href="{{ route('organizer.statistics.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.statistics.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line me-2"></i> Statistiques
            </a>
        </div>
    </div>
</div>

