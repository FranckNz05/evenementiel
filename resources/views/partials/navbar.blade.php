<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg fixed-top bg-dark shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <span class="text-primary fw-bold">Mokili</span><span class="text-warning fw-bold">Event</span>
        </a>

        <!-- Bouton pour mobile -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars text-white"></i>
        </button>

        <!-- Navigation -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Menu principal -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link px-3 text-white {{ request()->routeIs('events.public') || request()->routeIs('events.index') || request()->routeIs('direct-events.*') ? 'active' : '' }}" href="{{ route('events.public') }}">
                        Trouver un évènement
                    </a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link px-3 text-white {{ request()->routeIs('events.select-type') || request()->routeIs('events.create') || request()->routeIs('organizer.events.*') || request()->routeIs('events.wizard.*') || request()->routeIs('custom-offers.*') || request()->routeIs('custom-events.*') ? 'active' : '' }}" href="{{ route('events.create') }}">
                            Créer un évènement
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 text-white {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                            Mon compte
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 text-white {{ request()->routeIs('reservations.*') ? 'active' : '' }}" href="{{ route('reservations.index') }}">
                            Mes réservations
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link px-3 text-white"
                           href="#"
                           data-auth-modal-target="authLoginModal"
                           data-auth-redirect="{{ route('events.select-type') }}">
                        Créer un évènement
                        </a>
                    </li>
                @endauth
            </ul>

            <!-- Boutons d'action -->
            <div class="d-flex gap-2">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-warning dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userMenu">
                            @if(Auth::user()->hasRole(3))
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Tableau de bord Administrateur</a></li>
                            @elseif(Auth::user()->hasRole(2))
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-chart-line me-2"></i> Tableau de bord Organisateur</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Tableau de bord</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.tickets') }}"><i class="fas fa-ticket-alt me-2"></i> Mes billets</a></li>
                            @endif

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="#"
                       class="btn btn-outline-warning"
                       data-auth-modal-target="authLoginModal">
                        Connexion
                    </a>
                    <a href="#"
                       class="btn btn-warning text-dark"
                       data-auth-modal-target="authRegisterModal">
                        Inscription
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<style>
.navbar {
    padding-top: 1rem;
    padding-bottom: 1rem;
    transition: all 0.3s ease;
    background-color: #1a237e !important; /* Bleu de nuit */
}

.navbar.scrolled {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.navbar-brand {
    font-size: 1.5rem;
    letter-spacing: -0.5px;
}

.nav-link {
    font-weight: 500;
    transition: color 0.3s ease;
}

.nav-link:hover {
    color: #ffffff !important; /* blanc or au survol */
}

.nav-link.active {
    color: #ffffff !important; /* blanc or pour l'élément actif */
    position: relative;
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 24px;
    height: 2px;
    background-color: #ffffff; /* blanc or */
    border-radius: 2px;
}

.dropdown-menu {
    border-radius: 0.5rem;
    background-color: #1a237e; /* Bleu de nuit */
    border: 1px solid rgba(255, 215, 0, 0.2); /* Bordure blanc or très transparente */
}

.dropdown-item {
    padding: 0.5rem 1rem;
    font-weight: 500;
    color: white !important;
}

.dropdown-item:hover {
    background-color: rgba(255, 215, 0, 0.1) !important; /* blanc or très transparent */
    color: #ffffff !important;
}

.dropdown-item i {
    width: 20px;
}

.btn-warning {
    background-color: #ffffff !important; /* blanc or */
    border-color: #ffffff !important;
    color: #1a237e !important; /* Texte en bleu de nuit */
}

.btn-outline-warning {
    color: #ffffff !important; /* Texte en blanc or */
    border-color: #ffffff !important;
}

.btn-outline-warning:hover {
    background-color: rgba(255, 215, 0, 0.1) !important;
}

@media (max-width: 991.98px) {
    .navbar-collapse {
        padding: 1rem 0;
        background-color: #1a237e; /* Bleu de nuit pour le menu mobile */
    }

    .navbar-nav {
        margin: 1rem 0;
    }

    .nav-link {
        padding: 0.5rem 0;
    }

    .nav-link.active::after {
        display: none;
    }

    .d-flex.gap-2 {
        margin-top: 1rem;
        flex-direction: column;
    }

    .d-flex.gap-2 .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
});
</script>
<!-- Navbar End -->

