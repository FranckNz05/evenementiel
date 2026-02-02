<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MokiliEvent') }} - Tableau de bord</title>

    <link rel="icon" href="{{ setting('favicon') ? asset(setting('favicon')) : asset('images/logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-pagination.css') }}">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard-design-system.css') }}" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    @stack('styles')
</head>
<body>
    <div class="page-loader" id="pageLoader">
        <div class="text-center text-white">
            <img src="{{ setting('logo') ? asset(setting('logo')) : asset('images/logo.png') }}" alt="MokiliEvent" style="width:80px;height:80px;border-radius:12px;margin-bottom:1rem">
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">
                <a href="{{ route('home') }}">
                    <img src="{{ setting('logo') ? asset(setting('logo')) : asset('images/logo.png') }}" alt="MokiliEvent">
                    <span>MokiliEvent</span>
                </a>
                <button class="btn btn-link text-white p-0 d-lg-none" id="closeSidebar"><i class="fas fa-times"></i></button>
            </div>

            <div class="list-group list-group-flush d-flex flex-column h-100">
                @role('3')
                    <!-- === MENU ADMINISTRATEUR === -->
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-gauge"></i> Tableau de bord
                    </a>
                    <div class="sidebar-section-title">Gestion des utilisateurs</div>
                    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Utilisateurs
                    </a>
                    <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i> Mon profil Admin
                    </a>
                    <div class="sidebar-section-title">Gestion des événements</div>
                    <a href="{{ route('admin.events.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i> Tous les événements
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> Catégories
                    </a>
                    <a href="{{ route('admin.tickets.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                        <i class="fas fa-ticket-alt"></i> Tous les billets
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <i class="fas fa-money-bill"></i> Tous les paiements
                    </a>
                    <div class="sidebar-section-title">Contenu & Communication</div>
                    <a href="{{ route('admin.announcements.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                        <i class="fas fa-bullhorn"></i> Annonces
                    </a>
                    <a href="{{ route('admin.blogs.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                        <i class="fas fa-blog"></i> Articles de blog
                    </a>
                    @if(Route::has('admin.stats'))
                    <div class="sidebar-section-title">Analytique</div>
                    <a href="{{ route('admin.stats') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.stats*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> Statistiques globales
                    </a>
                    @endif
                    @if(Route::has('admin.reports.index'))
                    <a href="{{ route('admin.reports.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-file-chart-column"></i> Rapports détaillés
                    </a>
                    @endif
                    <div class="sidebar-section-title">Configuration</div>
                    <a href="{{ route('admin.settings') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="fas fa-gear"></i> Paramètres système
                    </a>
                    <a href="{{ route('admin.commission-settings.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.commission-settings*') ? 'active' : '' }}">
                        <i class="fas fa-percentage"></i> Commissions & Paiements
                    </a>
                @elserole('2')
                    <!-- === MENU ORGANISATEUR === -->
                    <a href="{{ route('organizer.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                    <div class="sidebar-section-title">Mon compte</div>
                    <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.show') ? 'active' : '' }}"><i class="fas fa-user-tie"></i> Profil organisateur</a>
                    <div class="sidebar-section-title">Mes événements</div>
                    @if(Route::has('organizer.events.index'))
                    <a href="{{ route('organizer.events.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.events.*') ? 'active' : '' }}"><i class="fas fa-calendar"></i> Gérer mes événements</a>
                    @endif
                    @if(Route::has('organizer.tickets.index'))
                    <a href="{{ route('organizer.tickets.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.tickets.*') ? 'active' : '' }}"><i class="fas fa-ticket-alt"></i> Mes billets</a>
                    @endif
                    @if(Route::has('organizer.payments.index'))
                    <a href="{{ route('organizer.payments.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.payments.*') ? 'active' : '' }}"><i class="fas fa-credit-card"></i> Paiements</a>
                    @endif
                    <div class="sidebar-section-title">Outils</div>
                    @if(Route::has('organizer.scans.index'))
                    <a href="{{ route('organizer.scans.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.scans.*') ? 'active' : '' }}"><i class="fas fa-qrcode"></i> Scanner billets</a>
                    @endif
                    @if(Route::has('organizer.access-codes.index'))
                    <a href="{{ route('organizer.access-codes.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.access-codes.*') ? 'active' : '' }}"><i class="fas fa-key"></i> Codes d'accès</a>
                    @endif
                    <div class="sidebar-section-title">Suivi & Analyse</div>
                    @if(Route::has('organizer.analytics.index'))
                    <a href="{{ route('organizer.analytics.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.analytics.*') || request()->routeIs('organizer.statistics.*') ? 'active' : '' }}"><i class="fas fa-chart-bar"></i> Analytics</a>
                    @endif
                    @if(Route::has('organizer.reports.index'))
                    <a href="{{ route('organizer.reports.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.reports.*') ? 'active' : '' }}"><i class="fas fa-file-chart-column"></i> Mes rapports</a>
                    @endif
                    @if(Route::has('organizer.blogs.index'))
                    <div class="sidebar-section-title">Mon contenu</div>
                    <a href="{{ route('organizer.blogs.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('organizer.blogs.*') ? 'active' : '' }}"><i class="fas fa-blog"></i> Mes articles</a>
                    @endif
                @else
                    <!-- === MENU CLIENT === -->
                    <div class="sidebar-section-title">Mon compte</div>
                    <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.show') ? 'active' : '' }}"><i class="fas fa-user"></i> Mon profil</a>
                    <a href="{{ route('favorites.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('favorites.index') ? 'active' : '' }}"><i class="fas fa-heart"></i> Mes favoris</a>
                    <div class="sidebar-section-title">Mes achats</div>
                    <a href="{{ route('profile.tickets') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.tickets') ? 'active' : '' }}"><i class="fas fa-ticket-alt"></i> Mes billets</a>
                    @if (Route::has('reservations.index'))
                    <a href="{{ route('reservations.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('reservations.*') ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Mes réservations</a>
                    @endif
                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('orders.*') ? 'active' : '' }}"><i class="fas fa-receipt"></i> Mes commandes</a>
                    <a href="{{ route('payments.history') }}" class="list-group-item list-group-item-action {{ request()->routeIs('payments.history') ? 'active' : '' }}"><i class="fas fa-money-bill-wave"></i> Historique de paiement</a>
                @endrole

                <!-- Footer de déconnexion (commun à tous) -->
                <div class="sidebar-footer mt-auto">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn w-100 text-start">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="topbar">
                <button class="toggle-sidebar" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <h4 class="page-title">@yield('title', 'Tableau de bord')</h4>
                <div class="topbar-divider"></div>
                <div class="user-info">
                    <img src="{{ Auth::user()->getProfilePhotoUrlAttribute() }}" alt="Photo de profil" class="user-avatar" onerror="this.src='{{ asset('images/default-profile.png') }}'">
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->name ?? (Auth::user()->prenom . ' ' . Auth::user()->nom) }}</div>
                        <div class="user-role">
                            @role('3')
                                <i class="fas fa-shield-alt me-1"></i>Administrateur
                            @elserole('2')
                                <i class="fas fa-calendar-alt me-1"></i>Organisateur
                            @else 
                                <i class="fas fa-user me-1"></i>Client 
                            @endrole
                        </div>
                    </div>
                    <div class="user-dropdown">
                        <a href="{{ route('profile.show') }}" class="user-dropdown-item">
                            <i class="fas fa-user me-2"></i> Mon profil
                        </a>
                        
                        @role('3')
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('admin.settings') }}" class="user-dropdown-item">
                            <i class="fas fa-cog me-2"></i> Paramètres système
                        </a>
                        @endrole
                        
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="user-dropdown-item w-100 text-start border-0 bg-transparent">
                                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <div class="main-content">
                <!-- Notifications Toast Container -->
                <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                    @if (session('success'))
                        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
                            <div class="toast-header" style="background-color: #0f1a3d; color: white;">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong class="me-auto">Succès</strong>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                            </div>
                            <div class="toast-body">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="7000">
                            <div class="toast-header" style="background-color: #0f1a3d; color: white;">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong class="me-auto">Erreur</strong>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                            </div>
                            <div class="toast-body">
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4000">
                            <div class="toast-header bg-info text-white">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong class="me-auto">Information</strong>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                            </div>
                            <div class="toast-body">
                                {{ session('info') }}
                            </div>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="6000">
                            <div class="toast-header bg-warning text-dark">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong class="me-auto">Attention</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                            </div>
                            <div class="toast-body">
                                {{ session('warning') }}
                            </div>
                        </div>
                    @endif
                </div>

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar-wrapper');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const closeSidebar = document.getElementById('closeSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const pageContent = document.getElementById('page-content-wrapper');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
                if (sidebar.classList.contains('show')) { 
                    document.body.style.overflow = 'hidden'; 
                } else { 
                    document.body.style.overflow = ''; 
                }
            }
            
            if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
            if (closeSidebar) closeSidebar.addEventListener('click', toggleSidebar);
            if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

            // Auto-close on link click (mobile)
            if (window.innerWidth < 1200) {
                document.querySelectorAll('#sidebar-wrapper a').forEach(link => {
                    link.addEventListener('click', function() { 
                        if (!this.classList.contains('dropdown-toggle')) {
                            setTimeout(() => toggleSidebar(), 150); // Petit délai pour l'UX
                        }
                    });
                });
            }

            // Responsive behavior
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1200) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });

            // Fonction pour masquer le loader
            function hidePageLoader() {
                const pageLoader = document.getElementById('pageLoader');
                if (pageLoader) {
                    pageLoader.classList.add('fade-out');
                    setTimeout(() => { 
                        pageLoader.style.display = 'none'; 
                    }, 800);
                }
            }

            // Masquer le loader quand la page est chargée
            if (document.readyState === 'complete') {
                // La page est déjà chargée
                hidePageLoader();
            } else {
                // Attendre que la page soit chargée
                window.addEventListener('load', hidePageLoader);
                
                // Timeout de secours au cas où l'événement load ne se déclenche pas
                setTimeout(hidePageLoader, 5000); // 5 secondes max
            }

            // Gestion des erreurs JavaScript non capturées
            window.addEventListener('error', function() {
                hidePageLoader();
                return true; // Empêche le message d'erreur par défaut
            });

            // Animation pour les éléments du menu
            const menuItems = document.querySelectorAll('.list-group-item');
            menuItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.05}s`;
                item.classList.add('animate-slide-in');
            });

            // Amélioration des notifications toast
            if (typeof Toastify !== 'undefined') {
                // Configuration globale pour les toasts
                const toastConfig = {
                    duration: 4000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%)",
                        borderRadius: "12px",
                        fontFamily: "Inter, sans-serif",
                        fontWeight: "500"
                    }
                };
                
                // Fonction helper pour afficher des notifications
                window.showToast = function(message, type = 'success') {
                    const config = {...toastConfig};
                    if (type === 'error') {
                        config.style.background = "linear-gradient(135deg, #dc3545 0%, #c82333 100%)";
                    } else if (type === 'warning') {
                        config.style.background = "linear-gradient(135deg, #ffc107 0%, #e0a800 100%)";
                        config.style.color = "#212529";
                    }
                    config.text = message;
                    Toastify(config).showToast();
                };
            }
        });

        // Fonction pour mettre à jour le titre de la page dynamiquement
        function updatePageTitle(newTitle) {
            document.querySelector('.page-title').textContent = newTitle;
            document.title = `${newTitle} - MokiliEvent`;
        }

        // Gestion de l'état de chargement pour les liens
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && !link.href.includes('#') && !link.target) {
                // Ajouter un indicateur de chargement
                const originalText = link.innerHTML;
                if (!link.classList.contains('no-loading')) {
                    link.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>' + link.textContent;
                    setTimeout(() => {
                        if (link) link.innerHTML = originalText;
                    }, 3000);
                }
            }
        });
    </script>
    
    <!-- Styles d'animation -->
    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out forwards;
        }
        
        .dropdown-divider {
            height: 1px;
            background: rgba(0,0,0,0.1);
            margin: 0.5rem 0;
        }
        
        /* Amélioration du responsive */
        @media (max-width: 576px) {
            .topbar {
                padding: 0 1rem;
            }
            
            .page-title {
                font-size: 1.25rem;
            }
            
            .user-details {
                display: none;
            }
            
            .user-avatar {
                margin-right: 0;
            }
            
            .sidebar-section-title {
                padding: 0.5rem 1.5rem 0.25rem;
                font-size: 0.7rem;
            }
            
            .list-group-item {
                padding: 0.9rem 1.5rem;
                margin: 0.25rem 0.8rem;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 0.75rem;
            }
            
            .topbar {
                height: 60px;
                padding: 0 0.75rem;
            }
            
            #page-content-wrapper {
                --topbar-height: 60px;
            }
        }
        
        /* Amélioration des focus states pour l'accessibilité */
        .list-group-item:focus,
        .logout-btn:focus,
        .toggle-sidebar:focus {
            outline: 2px solid var(--blanc-or);
            outline-offset: 2px;
        }
        
        /* Animation pour les cartes statistiques dans le dashboard */
        .stat-card {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Amélioration du loader */
        .page-loader {
            background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
            background-attachment: fixed;
        }
        
        .page-loader::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 2s ease-in-out infinite alternate;
        }
        
        @keyframes pulse {
            from { opacity: 0.3; }
            to { opacity: 0.7; }
        }
        
        /* Style pour les badges de rôle dans la topbar */
        .user-role i {
            font-size: 0.7rem;
            opacity: 0.8;
        }
        
        /* Amélioration des alertes */
        .alert {
            border-left: 4px solid;
            padding-left: 1rem;
            animation: slideInAlert 0.5s ease-out forwards;
        }
        
        @keyframes slideInAlert {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        :root {
            --bleu-nuit: #0f1a3d;
            --bleu-nuit-clair: #1a237e;
            --jaune-or: #ffffff;
            --jaune-or-fonce: #f8fafc;
            --text-color: #2d3748;
        }
        
        /* Uniformisation globale des couleurs - Remplacer vert/rouge par bleu-nuit */
        .bg-success, .bg-success-soft {
            background-color: var(--bleu-nuit) !important;
        }
        .bg-danger, .bg-danger-soft {
            background-color: var(--bleu-nuit) !important;
        }
        .text-success {
            color: var(--bleu-nuit) !important;
        }
        .text-danger {
            color: var(--bleu-nuit) !important;
        }
        .badge.bg-success, .badge.bg-success-soft {
            background-color: var(--bleu-nuit) !important;
            color: white !important;
        }
        .badge.bg-danger, .badge.bg-danger-soft {
            background-color: var(--bleu-nuit) !important;
            color: white !important;
        }
        
        /* Textes en noir par défaut */
        body, p, span, div, h1, h2, h3, h4, h5, h6, label, input, textarea, select {
            color: var(--text-color) !important;
        }
        
        /* Textes en blanc sur fond bleu */
        #sidebar-wrapper, .page-header, [style*="background-color: var(--bleu-nuit)"], 
        [style*="background: var(--bleu-nuit)"], 
        .btn-primary {
            color: white !important;
        }
        #sidebar-wrapper *, .page-header *,
        [style*="background-color: var(--bleu-nuit)"] *,
        [style*="background: var(--bleu-nuit)"] * {
            color: white !important;
        }
        
        :root {
            --sidebar-width: 280px;
            --topbar-height: 72px;
            --sidebar-bg: var(--bleu-nuit, #0f1a3d);
            --sidebar-text: #ffffff;
            --sidebar-active: rgba(255,255,255,0.15);
            --sidebar-hover: rgba(255,255,255,0.1);
        }

        html, body { height: 100%; }
        #wrapper { display: flex; min-height: 100vh; background: #f6f8fb; }

        /* Sidebar */
        #sidebar-wrapper {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1030;
            transform: translateX(0);
            transition: transform .25s ease;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        #sidebar-wrapper .sidebar-heading {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px; background: rgba(255,255,255,0.04);
            position: sticky; top: 0; z-index: 2;
            backdrop-filter: blur(6px);
        }
        #sidebar-wrapper .sidebar-heading img { width: 36px; height: 36px; border-radius: 8px; margin-right: 10px; }
        #sidebar-wrapper .sidebar-heading span { font-weight: 700; letter-spacing: .2px; color: #ffffff !important; text-decoration: none; }
        #sidebar-wrapper .sidebar-heading a { text-decoration: none; color: #ffffff; }
        #sidebar-wrapper .sidebar-heading button i,
        #sidebar-wrapper .sidebar-heading .btn-link {
            color: #ffffff !important;
        }
        #sidebar-wrapper .list-group-item {
            background: transparent; color: #ffffff;
            border: 0; border-radius: 10px; margin: 4px 12px; padding: 12px 14px;
            display: flex; align-items: center; gap: 10px;
        }
        #sidebar-wrapper .list-group-item i { 
            width: 18px; 
            opacity: .9; 
            color: #ffffff !important;
        }
        #sidebar-wrapper .list-group-item:hover { 
            background: rgba(255, 255, 255, 0.1); 
            color: #fff; 
        }
        #sidebar-wrapper .list-group-item:hover i {
            color: #ffffff !important;
        }
        #sidebar-wrapper .list-group-item.active { 
            background: rgba(255, 255, 255, 0.15); 
            color: #fff; 
        }
        #sidebar-wrapper .list-group-item.active i {
            color: #ffffff !important;
        }
        #sidebar-wrapper .sidebar-section-title {
            text-transform: uppercase; 
            opacity: .7; 
            font-size: .72rem;
            letter-spacing: .08em; 
            padding: 8px 16px; 
            margin-top: 10px;
            color: rgba(255, 255, 255, 0.7) !important;
        }
        #sidebar-wrapper .sidebar-footer { padding: 8px 12px 16px; }
        #sidebar-wrapper .logout-btn {
            background: rgba(255,255,255,0.06); color: #fff; border: 0; border-radius: 10px; padding: 10px 14px;
        }
        #sidebar-wrapper .logout-btn:hover { background: rgba(255,255,255,0.12); }

        /* Overlay for mobile */
        .sidebar-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.35); z-index: 1025; display: none; }
        .sidebar-overlay.show { display: block; }

        /* Page content */
        #page-content-wrapper { flex: 1; margin-left: var(--sidebar-width); min-width: 0; }
        .topbar {
            height: var(--topbar-height); display: flex; align-items: center; gap: 12px;
            padding: 0 20px; background: #fff; border-bottom: 1px solid rgba(0,0,0,0.06);
            position: sticky; top: 0; z-index: 1010;
        }
        .topbar .toggle-sidebar { 
            background: transparent; 
            border: 0; 
            font-size: 20px; 
            color: var(--bleu-nuit, #0f1a3d) !important; 
        }
        .topbar .toggle-sidebar i {
            color: var(--bleu-nuit, #0f1a3d) !important;
        }
        .page-title { 
            font-weight: 800; 
            letter-spacing: .2px; 
            margin: 0; 
            color: var(--bleu-nuit, #0f1a3d) !important;
        }
        .user-info { margin-left: auto; display: flex; align-items: center; gap: 10px; position: relative; }
        .user-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; box-shadow: 0 0 0 2px #fff; }
        .user-details { line-height: 1.1; }
        .user-name { font-weight: 700; font-size: .95rem; }
        .user-role { font-size: .72rem; opacity: .8; }
        .user-dropdown { display: none; position: absolute; right: 0; top: calc(100% + 6px); background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px; min-width: 220px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
        .user-info:hover .user-dropdown { display: block; }
        .user-dropdown-item { display: block; padding: 10px 12px; color: #2b3a55; text-decoration: none; }
        .user-dropdown-item:hover { background: #f2f5fb; }
        .main-content { padding: 20px; }
        
        /* Icônes bleues sur fond blanc dans le contenu principal */
        .main-content i.fas,
        .main-content i.far,
        .main-content i.fab,
        .main-content i.fal,
        .topbar i.fas,
        .topbar i.far,
        .topbar i.fab,
        .topbar i.fal,
        .card i.fas,
        .card i.far,
        .card i.fab,
        .card i.fal {
            color: var(--bleu-nuit, #0f1a3d) !important;
        }

        /* Headings uniformes dans le contenu */
        .main-content h1, .main-content h2, .main-content h3, .main-content h4 { font-family: Inter, sans-serif; font-weight: 800; letter-spacing: .2px; }
        .main-content h1 { font-size: 1.75rem; }
        .main-content h2 { font-size: 1.4rem; }
        .main-content h3 { font-size: 1.15rem; }
        .main-content h4 { font-size: 1rem; }

        /* Cards et tables */
        .card { border-radius: 16px; border: 1px solid rgba(0,0,0,0.06); }
        .card-header { border-bottom: 1px solid rgba(0,0,0,0.06); border-top-left-radius: 16px; border-top-right-radius: 16px; }
        .table > :not(caption) > * > * { padding: 12px 14px; }

        /* Correction boutons bleus avec texte blanc - TOUS LES DASHBOARDS */
        .btn-primary,
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary:not(:disabled):not(.disabled),
        .btn.btn-primary,
        a.btn-primary,
        button.btn-primary {
            background-color: var(--bleu-nuit, #0f1a3d) !important;
            border-color: var(--bleu-nuit, #0f1a3d) !important;
            color: #ffffff !important;
            text-decoration: none !important;
        }

        .btn-primary i,
        .btn-primary svg,
        .btn-primary .fas,
        .btn-primary .far,
        .btn-primary .fab,
        .btn-primary [class*="fa-"] {
            color: #ffffff !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: var(--bleu-nuit-clair, #1a237e) !important;
            border-color: var(--bleu-nuit-clair, #1a237e) !important;
            color: #ffffff !important;
            text-decoration: none !important;
        }

        .btn-primary:hover i,
        .btn-primary:hover svg,
        .btn-primary:hover .fas,
        .btn-primary:hover .far,
        .btn-primary:hover .fab {
            color: #ffffff !important;
        }
        
        a.btn-primary,
        a.btn-primary:hover,
        a.btn-primary:focus,
        a.btn-primary:active {
            text-decoration: none !important;
        }

        /* Correction titre de la page - toujours en bleu */
        .page-title,
        h4.page-title,
        .topbar .page-title,
        .topbar h4.page-title,
        nav.topbar .page-title {
            color: var(--bleu-nuit, #0f1a3d) !important;
        }

        /* Responsive */
        @media (max-width: 1199.98px) {
            #sidebar-wrapper { transform: translateX(-100%); }
            #sidebar-wrapper.show { transform: translateX(0); }
            #page-content-wrapper { margin-left: 0; }
        }

        /* Correction des breadcrumbs dans page-header */
        .page-header .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }
        
        .page-header .breadcrumb-item a,
        .page-header .breadcrumb-item {
            color: rgba(255, 255, 255, 0.9) !important;
            text-decoration: none;
        }
        
        .page-header .breadcrumb-item a:hover {
            color: var(--blanc-or, #ffffff) !important;
        }
        
        .page-header .breadcrumb-item.active {
            color: var(--blanc-or, #ffffff) !important;
        }

        .page-header .breadcrumb-item + .breadcrumb-item::before {
            content: var(--bs-breadcrumb-divider, ">");
            color: rgba(255, 255, 255, 0.7) !important;
            padding: 0 0.5rem;
        }

        /* Correction du titre et sous-titre dans page-header - texte en blanc */
        .page-header .page-title,
        .page-header h1.page-title,
        .page-header .page-subtitle,
        .page-header p.page-subtitle {
            color: white !important;
        }

        .page-header .page-title i,
        .page-header .page-title .fas,
        .page-header .page-title .far,
        .page-header .page-title .fab {
            color: white !important;
        }

        /* Amélioration de la disposition des cartes dans le dashboard */
        .main-content .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .main-content .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .main-content h5 {
            color: var(--bleu-nuit, #0f1a3d) !important;
            font-weight: 600;
        }

        .main-content p, .main-content .text-muted {
            color: #6b7280 !important;
        }
    </style>
    
    @include('components.auth-modals')
    @stack('scripts')
</body>
</html>