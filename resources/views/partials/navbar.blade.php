<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-premium shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <span class="text-white fw-bold">Mokili</span><span class="text-danger fw-bold">Event</span>
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
                    <a class="nav-link px-3 {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-home me-1"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">
                        <i class="fas fa-calendar-alt me-1"></i> Événements
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('blogs.*') ? 'active' : '' }}" href="{{ route('blogs.index') }}">
                        <i class="fas fa-blog me-1"></i> Blog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about.index') }}">
                        <i class="fas fa-info-circle me-1"></i> À propos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact.index') }}">
                        <i class="fas fa-envelope me-1"></i> Contact
                    </a>
                </li>
            </ul>

            <!-- Boutons d'action -->
            <div class="d-flex gap-2">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-premium-light dropdown-toggle text-white" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1 text-white"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i> Mon profil</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.tickets') }}"><i class="fas fa-ticket-alt me-2"></i> Mes billets</a></li>
                            @if(Auth::user()->hasRole('organizer'))
                                <li><a class="dropdown-item" href="{{ route('organizer.dashboard') }}"><i class="fas fa-chart-line me-2"></i> Tableau de bord</a></li>
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
                    <a href="{{ route('auth.login') }}" class="btn btn-outline-light rounded-pill px-4">
                        <i class="fas fa-sign-in-alt me-1"></i> Connexion
                    </a>
                    <a href="{{ route('auth.register') }}" class="btn btn-danger rounded-pill px-4">
                        <i class="fas fa-user-plus me-1"></i> Inscription
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
}

.bg-premium {
    background: linear-gradient(135deg, #0f1a3d 0%, #1a2a5a 100%);
    backdrop-filter: blur(10px);
}

.navbar.scrolled {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    background: rgba(15, 26, 61, 0.95);
}

.navbar-brand {
    font-size: 1.5rem;
    letter-spacing: -0.5px;
}

.nav-link {
    font-weight: 500;
    color: rgba(255, 255, 255, 0.8) !important;
    transition: all 0.3s ease;
}

.nav-link i {
    color: white !important;
}

.nav-link:hover {
    color: white !important;
    transform: translateY(-1px);
}

.nav-link.active {
    color: white !important;
    position: relative;
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 1rem;
    right: 1rem;
    height: 2px;
    background-color: var(--bs-danger);
    border-radius: 2px;
}

.btn-premium-light {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.btn-premium-light:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
}

.dropdown-menu {
    border-radius: 0.5rem;
    margin-top: 0.5rem !important;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    font-weight: 500;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item i {
    width: 20px;
}

@media (max-width: 991.98px) {
    .navbar-collapse {
        padding: 1rem 0;
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
