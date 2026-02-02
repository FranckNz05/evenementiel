<!-- Filtres améliorés -->
<div class="col-lg-3">
    <div class="card border-0 bg-blue-nuit shadow-sm mb-4 filters-card">
        <div class="card-body p-4">
            <h5 class="card-title mb-4 d-flex align-items-center text-white">
                <i class="fas fa-sliders-h me-2 text-blanc-or"></i>
                <span>Filtrer par</span>
            </h5>

            <!-- Période -->
            <div class="filter-section mb-4">
                <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 d-flex align-items-center">
                        <i class="far fa-calendar-alt me-2 text-blanc-or"></i>
                        Période
                    </h6>
                    <i class="fas fa-chevron-down filter-toggle"></i>
                </div>
                <div class="filter-content">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ app('toggleFilter')('periode', 'today') }}"
                           class="filter-chip {{ request('periode') == 'today' ? 'active' : '' }}">
                            <i class="far fa-sun me-2"></i> Aujourd'hui
                        </a>
                        <a href="{{ app('toggleFilter')('periode', 'week') }}"
                           class="filter-chip {{ request('periode') == 'week' ? 'active' : '' }}">
                            <i class="far fa-calendar me-2"></i> Cette semaine
                        </a>
                        <a href="{{ app('toggleFilter')('periode', 'month') }}"
                           class="filter-chip {{ request('periode') == 'month' ? 'active' : '' }}">
                            <i class="fas fa-calendar-week me-2"></i> Ce mois-ci
                        </a>
                        <a href="{{ app('toggleFilter')('periode', 'upcoming') }}"
                           class="filter-chip {{ request('periode') == 'upcoming' ? 'active' : '' }}">
                            <i class="fas fa-clock me-2"></i> À venir
                        </a>
                    </div>
                </div>
            </div>

            <!-- Catégories -->
            <div class="filter-section mb-4">
                <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 d-flex align-items-center">
                        <i class="fas fa-tags me-2 text-blanc-or"></i>
                        Catégories
                    </h6>
                    <i class="fas fa-chevron-down filter-toggle"></i>
                </div>
                <div class="filter-content">
                    <div class="category-list d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto;">
                        @foreach($categories as $category)
                        <a href="{{ app('toggleFilter')('category', $category->slug) }}"
                           class="filter-chip {{ request('category') == $category->slug ? 'active' : '' }}">
                            <i class="fas fa-{{ $category->icon ?? 'circle' }} me-2"></i>
                            {{ $category->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Villes -->
            <div class="filter-section mb-4">
                <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 d-flex align-items-center">
                        <i class="fas fa-map-marker-alt me-2 text-blanc-or"></i>
                        Villes
                    </h6>
                    <i class="fas fa-chevron-down filter-toggle"></i>
                </div>
                <div class="filter-content">
                    <div class="d-flex flex-column gap-2">
                        @foreach($villes as $ville)
                        <a href="{{ app('toggleFilter')('ville', $ville) }}"
                           class="filter-chip {{ request('ville') == $ville ? 'active' : '' }}">
                            <i class="fas fa-city me-2"></i> {{ $ville }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Type d'événement -->
            <div class="filter-section mb-4">
                <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 d-flex align-items-center">
                        <i class="fas fa-calendar-alt me-2 text-blanc-or"></i>
                        Type
                    </h6>
                    <i class="fas fa-chevron-down filter-toggle"></i>
                </div>
                <div class="filter-content">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ app('toggleFilter')('event_type', 'Espace libre') }}"
                           class="filter-chip {{ request('event_type') == 'Espace libre' ? 'active' : '' }}">
                            <i class="fas fa-chair me-2"></i> Espace libre
                        </a>
                        <a href="{{ app('toggleFilter')('event_type', 'Plan de salle') }}"
                           class="filter-chip {{ request('event_type') == 'Plan de salle' ? 'active' : '' }}">
                            <i class="fas fa-th-large me-2"></i> Plan de salle
                        </a>
                        <a href="{{ app('toggleFilter')('event_type', 'Mixte') }}"
                           class="filter-chip {{ request('event_type') == 'Mixte' ? 'active' : '' }}">
                            <i class="fas fa-blender-phone me-2"></i> Mixte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statut -->
            <div class="filter-section mb-4">
                <div class="filter-header d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 d-flex align-items-center">
                        <i class="fas fa-ticket-alt me-2 text-blanc-or"></i>
                        Statut
                    </h6>
                    <i class="fas fa-chevron-down filter-toggle"></i>
                </div>
                <div class="filter-content">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ app('toggleFilter')('status', 'Gratuit') }}"
                           class="filter-chip {{ request('status') == 'Gratuit' ? 'active' : '' }}">
                            <span class="status-indicator bg-success me-2"></span> Gratuit
                        </a>
                        <a href="{{ app('toggleFilter')('status', 'Payant') }}"
                           class="filter-chip {{ request('status') == 'Payant' ? 'active' : '' }}">
                            <span class="status-indicator bg-warning me-2"></span> Payant
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bouton Reset -->
            @if(request()->except('page'))
            <div class="sticky-bottom pt-3">
                <a href="{{ url('/direct-events') }}" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                    <i class="fas fa-sync-alt me-2"></i> Réinitialiser
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.search-banner {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    box-shadow: var(--box-shadow);
}

/* Style des sections de filtre */
/* Ajoutez ceci à votre section de styles */
.filter-section .filter-content {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, opacity 0.3s ease;
}

.filter-section.active .filter-content {
    max-height: 1000px; /* Une valeur suffisamment grande */
    opacity: 1;
}

/* Style pour le sidebar quand tout est fermé */
.filters-collapsed {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%);
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.filters-collapsed .card-body {
    padding: 0 !important;
}

.filters-collapsed .filter-section {
    padding: 0.5rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.filters-collapsed .filter-header {
    color: white;
}

.filters-collapsed .filter-toggle {
    color: white;
}

.filters-collapsed .filter-chip {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    border-color: rgba(255, 255, 255, 0.2);
}

.filters-collapsed .filter-chip.active {
    background-color: var(--blanc-or);
    color: var(--bleu-nuit);
}

.filter-section {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding-bottom: 1rem;
    transition: all 0.3s ease;
}

.filter-section:last-child {
    border-bottom: none;
}

.filter-header {
    cursor: pointer;
    padding: 0.5rem 0;
    transition: all 0.3s ease;
}

.filter-header:hover {
    opacity: 0.8;
}

.filter-toggle {
    transition: transform 0.3s ease;
    color: var(--bleu-nuit);
}

.filter-section.active .filter-toggle {
    transform: rotate(180deg);
}

/* Style des chips de filtre */
.filter-chip {
    display: flex;
    align-items: center;
    padding: 0.6rem 1rem;
    border-radius: 50px;
    background-color: #f8f9fa;
    color: var(--text-color);
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    border: 1px solid #e9ecef;
}

.filter-chip:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: white;
}

.filter-chip.active {
    background-color: var(--blanc-or);
    color: var(--bleu-nuit);
    font-weight: 500;
    border-color: var(--blanc-or);
}

.filter-chip i {
    width: 20px;
    text-align: center;
}

/* Indicateur de statut */
.status-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

/* Animation des sections */
.filter-content {
    overflow: hidden;
    transition: max-height 0.3s ease, opacity 0.3s ease;
}

/* Recherche de catégories */
.category-search {
    border-radius: 50px !important;
    padding: 0.5rem 1rem;
    border: 1px solid #dee2e6;
}

.category-list {
    scrollbar-width: thin;
    scrollbar-color: var(--blanc-or) #f1f1f1;
}

.category-list::-webkit-scrollbar {
    width: 6px;
}

.category-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.category-list::-webkit-scrollbar-thumb {
    background-color: var(--blanc-or);
    border-radius: 10px;
}


/* Style principal du sidebar */
.filters-card {
    background: linear-gradient(135deg, var(--bleu-nuit) 0%, var(--bleu-nuit-clair) 100%) !important;
    border: none !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
    transition: all 0.4s ease;
    border-radius: 12px;
    overflow: hidden;
}
.filters-card .card-body { background: transparent !important; color: white !important; }

.filters-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25) !important;
}

.filters-card .card-body {
    color: white;
}

/* Titre principal */
.filters-card .card-title {
    color: white !important;
    font-size: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

/* Sections de filtre */
.filter-section {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.filter-section:last-child {
    border-bottom: none;
}

/* En-têtes de filtre */
.filter-header {
    cursor: pointer;
}

.filter-header h6 {
    color: white !important;
    font-weight: 500 !important;
    transition: all 0.3s ease;
    margin: 0;
}

.filter-header:hover h6 {
    color: var(--blanc-or) !important;
}

.filter-toggle {
    color: white !important;
    transition: all 0.3s ease;
}

.filter-section.active .filter-toggle {
    transform: rotate(180deg);
    color: var(--blanc-or) !important;
}

/* Contenu des filtres */
.filter-content {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.65, 0, 0.35, 1), opacity 0.3s ease;
    padding-left: 0.5rem;
}

.filter-section.active .filter-content {
    max-height: 1000px;
    opacity: 1;
    margin-top: 0.5rem;
}

/* Chips de filtre */
.filter-chip {
    display: flex;
    align-items: center;
    padding: 0.6rem 1rem;
    border-radius: 50px;
    background-color: rgba(255, 255, 255, 0.1);
    color: white !important;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    margin-bottom: 0.5rem;
}

.filter-chip:hover {
    transform: translateX(8px);
    background-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.filter-chip.active {
    background-color: var(--blanc-or) !important;
    color: var(--bleu-nuit) !important;
    font-weight: 500;
    border-color: var(--blanc-or);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.filter-chip i {
    width: 20px;
    text-align: center;
    transition: all 0.3s ease;
}

.filter-chip:hover i {
    transform: scale(1.1);
}

/* Icônes */
.text-blanc-or {
    color: var(--blanc-or) !important;
}

/* Bouton Reset */
.sticky-bottom {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(5px);
    border-radius: 8px;
    padding: 1rem !important;
    margin-top: 1rem;
}

.btn-outline-primary {
    color: white !important;
    border-color: white !important;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background-color: white !important;
    color: var(--bleu-nuit) !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Scrollbar */
.category-list::-webkit-scrollbar {
    width: 6px;
}

.category-list::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

.category-list::-webkit-scrollbar-thumb {
    background-color: var(--blanc-or);
    border-radius: 10px;
}

/* Indicateur de statut */
.status-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

/* Animation d'entrée */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card-body > * {
    animation: fadeIn 0.5s ease forwards;
    opacity: 0;
}

.card-body > *:nth-child(1) { animation-delay: 0.1s; }
.card-body > *:nth-child(2) { animation-delay: 0.2s; }
.card-body > *:nth-child(3) { animation-delay: 0.3s; }
.card-body > *:nth-child(4) { animation-delay: 0.4s; }
.card-body > *:nth-child(5) { animation-delay: 0.5s; }
.card-body > *:nth-child(6) { animation-delay: 0.6s; }

/* Responsive */
@media (max-width: 991.98px) {
    .card.border-0.shadow-sm.mb-4 {
        margin-top: 1rem;
    }
    
    .filter-chip {
        padding: 0.5rem 0.8rem;
    }
    
    .filter-content {
        padding-left: 0;
    }
}
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const filterContainer = document.querySelector('.card-body');
    const filterSections = document.querySelectorAll('.filter-section');


    // Animation des accordéons
    filterSections.forEach(header => {
        const section = header.closest('.filter-section');
        const content = header.querySelector('.filter-content');
        const filterHeaders = document.querySelectorAll('.filter-header');
        
        header.addEventListener('click', (e) => {
            // On ne déclenche pas si on clique sur un lien à l'intérieur
            if (e.target.tagName === 'A') return;
            
            if (section.classList.contains('active')) {
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                section.classList.remove('active');
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                section.classList.add('active');
            }
            
        });
    });

    filterHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const section = this.closest('.filter-section');
            const content = this.nextElementSibling;
            
            // Fermer toutes les autres sections
            if (!section.classList.contains('active')) {
                document.querySelectorAll('.filter-section.active').forEach(activeSection => {
                    if (activeSection !== section) {
                        activeSection.classList.remove('active');
                        activeSection.querySelector('.filter-content').style.maxHeight = '0';
                        activeSection.querySelector('.filter-content').style.opacity = '0';
                    }
                });
            }
            
            // Basculer l'état actuel
            section.classList.toggle('active');
            
            if (section.classList.contains('active')) {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
            } else {
                content.style.maxHeight = '0';
                content.style.opacity = '0';
            }
        });
    });

    // Animation au survol des chips
    const filterChips = document.querySelectorAll('.filter-chip');
    filterChips.forEach(chip => {
        chip.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px)';
        });
        chip.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateX(0)';
            }
        });
    });

    // Initialisation - Ouvrir la première section par défaut
    const firstSection = document.querySelector('.filter-section');
    if (firstSection) {
        firstSection.classList.add('active');
        const content = firstSection.querySelector('.filter-content');
        content.style.maxHeight = content.scrollHeight + 'px';
        content.style.opacity = '1';
    }

    // Initialisation - Fermer tous les filtres par défaut
    filterSections.forEach(section => {
        section.classList.remove('active');
        section.querySelector('.filter-content').style.maxHeight = '0';
        section.querySelector('.filter-content').style.opacity = '0';
    });
</script>