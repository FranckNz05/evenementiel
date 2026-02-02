<form method="GET" action="{{ route('admin.events.index') }}" class="mb-4">
    <div class="row g-3">
        <!-- Champ de recherche -->
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Rechercher..." 
                       value="{{ request('search') }}"
                       aria-label="Rechercher">
            </div>
        </div>

        <!-- Filtre par statut -->
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Tous les statuts</option>
                <option value="En cours" {{ request('status') == 'En cours' ? 'selected' : '' }}>En cours</option>
                <option value="En attente" {{ request('status') == 'En attente' ? 'selected' : '' }}>En attente</option>
                <option value="Archivé" {{ request('status') == 'Archivé' ? 'selected' : '' }}>Archivé</option>
                <option value="Annulé" {{ request('status') == 'Annulé' ? 'selected' : '' }}>Annulé</option>
            </select>
        </div>

        <!-- Filtre par catégorie -->
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                        {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Boutons -->
        <div class="col-md-2">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
                @if(request()->has('search') || request()->has('status') || request()->has('category'))
                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</form>