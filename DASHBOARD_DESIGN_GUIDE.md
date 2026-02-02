# 📘 Guide du Système de Design - Dashboard MokiliEvent

## 🎨 Vue d'ensemble

Ce guide présente le système de design unifié pour toutes les pages du tableau de bord MokiliEvent. Le système garantit une cohérence visuelle, une expérience utilisateur optimale et une maintenance simplifiée.

---

## 🎯 Principes de Design

- **Cohérence** : Utiliser les mêmes composants sur toutes les pages
- **Responsive** : Adaptatif sur tous les appareils (mobile, tablette, desktop)
- **Accessibilité** : Conforme aux standards WCAG
- **Performance** : Optimisé pour un chargement rapide
- **Élégance** : Design premium avec les couleurs MokiliEvent (Bleu nuit & Or)

---

## 📦 Composants Disponibles

### 1. En-tête de Page (`<x-page-header>`)

Utilisé pour afficher le titre de la page avec des actions optionnelles.

```blade
<x-page-header 
    title="Gestion des événements" 
    icon="fas fa-calendar"
    subtitle="Gérez tous vos événements en un seul endroit">
    <x-slot:actions>
        <a href="{{ route('events.create') }}" class="modern-btn btn-primary-modern">
            <i class="fas fa-plus"></i>
            Nouvel événement
        </a>
    </x-slot:actions>
</x-page-header>
```

**Attributs** :
- `title` (requis) : Titre de la page
- `icon` (optionnel) : Icône Font Awesome
- `subtitle` (optionnel) : Sous-titre descriptif
- `actions` (slot optionnel) : Boutons d'action

---

### 2. Section de Contenu (`<x-content-section>`)

Pour organiser le contenu en sections distinctes.

```blade
<x-content-section 
    title="Détails de l'événement" 
    icon="fas fa-info-circle"
    class="slide-in-left">
    
    <p>Contenu de la section...</p>
    
</x-content-section>
```

**Attributs** :
- `title` (optionnel) : Titre de la section
- `icon` (optionnel) : Icône Font Awesome
- `class` (optionnel) : Classes CSS additionnelles

---

### 3. Carte de Statistique (`<x-stat-card>`)

Pour afficher des KPIs et statistiques.

```blade
<div class="stats-grid">
    <x-stat-card 
        number="1,234" 
        label="Total Événements" 
        icon="fas fa-calendar-check" 
    />
    <x-stat-card 
        number="€45,678" 
        label="Revenus" 
        icon="fas fa-euro-sign" 
    />
    <x-stat-card 
        number="8,901" 
        label="Participants" 
        icon="fas fa-users" 
    />
</div>
```

**Attributs** :
- `number` (requis) : Valeur numérique
- `label` (requis) : Libellé de la stat
- `icon` (requis) : Icône Font Awesome

---

### 4. État Vide (`<x-empty-state>`)

Affichage quand il n'y a pas de données.

```blade
<x-empty-state 
    icon="fas fa-inbox"
    title="Aucun événement trouvé"
    message="Vous n'avez pas encore créé d'événement. Commencez par créer votre premier événement !">
    <x-slot:action>
        <a href="{{ route('events.create') }}" class="modern-btn btn-primary-modern">
            <i class="fas fa-plus"></i>
            Créer un événement
        </a>
    </x-slot:action>
</x-empty-state>
```

**Attributs** :
- `icon` (optionnel, défaut: `fa-inbox`) : Icône
- `title` (requis) : Titre du message
- `message` (requis) : Description
- `action` (slot optionnel) : Bouton d'action

---

## 🎨 Classes CSS Disponibles

### Cartes et Conteneurs

```css
.modern-card          /* Carte standard */
.card-header-modern   /* En-tête de carte avec dégradé bleu */
.card-body-modern     /* Corps de carte */
.content-section      /* Section de contenu avec ombre */
.stats-grid           /* Grille responsive pour stats */
```

### Boutons

```css
.modern-btn                /* Bouton de base */
.btn-primary-modern        /* Bouton principal (Or) */
.btn-secondary-modern      /* Bouton secondaire (Blanc) */
.btn-success-modern        /* Bouton succès (Vert) */
.btn-warning-modern        /* Bouton avertissement (Orange) */
.btn-danger-modern         /* Bouton danger (Rouge) */
.btn-info-modern           /* Bouton info (Bleu) */
.btn-outline-modern        /* Bouton contour */
.btn-sm-modern             /* Bouton petit */
.btn-group-modern          /* Groupe de boutons */
```

**Exemple** :
```html
<button class="modern-btn btn-primary-modern">
    <i class="fas fa-save"></i>
    Enregistrer
</button>
```

### Tableaux

```css
.table-container      /* Container avec scroll */
.modern-table         /* Tableau stylisé */
```

**Exemple** :
```html
<div class="table-container">
    <table class="modern-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>John Doe</td>
                <td>john@example.com</td>
                <td>
                    <div class="btn-group-modern">
                        <a href="#" class="modern-btn btn-sm-modern btn-info-modern">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="#" class="modern-btn btn-sm-modern btn-warning-modern">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### Badges

```css
.modern-badge         /* Badge de base */
.badge-success        /* Vert */
.badge-warning        /* Orange */
.badge-danger         /* Rouge */
.badge-info           /* Bleu */
.badge-primary        /* Or */
```

**Exemple** :
```html
<span class="modern-badge badge-success">
    <i class="fas fa-check"></i>
    Actif
</span>
```

### Alertes

```css
.modern-alert           /* Alerte de base */
.alert-success-modern   /* Succès (Vert) */
.alert-warning-modern   /* Avertissement (Orange) */
.alert-danger-modern    /* Erreur (Rouge) */
.alert-info-modern      /* Info (Bleu) */
```

**Exemple** :
```html
<div class="modern-alert alert-success-modern">
    <i class="fas fa-check-circle"></i>
    <span>Opération réussie !</span>
</div>
```

### Filtres

```css
.filters-container    /* Container de filtres */
.filters-row          /* Grille de filtres */
.filter-group         /* Groupe de filtre */
.filter-label         /* Label de filtre */
.filter-input         /* Input de filtre */
.filter-select        /* Select de filtre */
.filters-actions      /* Actions de filtres */
```

**Exemple** :
```html
<div class="filters-container">
    <div class="filters-row">
        <div class="filter-group">
            <label class="filter-label">
                <i class="fas fa-search"></i>
                Rechercher
            </label>
            <input type="text" class="filter-input" placeholder="Nom, email...">
        </div>
        
        <div class="filter-group">
            <label class="filter-label">
                <i class="fas fa-filter"></i>
                Statut
            </label>
            <select class="filter-select">
                <option>Tous</option>
                <option>Actif</option>
                <option>Inactif</option>
            </select>
        </div>
    </div>
    
    <div class="filters-actions">
        <button class="modern-btn btn-primary-modern">
            <i class="fas fa-search"></i>
            Filtrer
        </button>
        <button class="modern-btn btn-secondary-modern">
            <i class="fas fa-redo"></i>
            Réinitialiser
        </button>
    </div>
</div>
```

### Formulaires

```css
.form-group-modern        /* Groupe de champ */
.form-label-modern        /* Label de champ */
.form-input-modern        /* Input */
.form-textarea-modern     /* Textarea */
.form-select-modern       /* Select */
.form-text-modern         /* Texte d'aide */
.invalid-feedback-modern  /* Message d'erreur */
```

**Exemple** :
```html
<div class="form-group-modern">
    <label class="form-label-modern">
        <i class="fas fa-user"></i>
        Nom complet
    </label>
    <input type="text" class="form-input-modern" placeholder="Entrez votre nom">
    <small class="form-text-modern">Veuillez entrer votre nom complet</small>
</div>
```

### Animations

```css
.fade-in          /* Apparition progressive */
.slide-in-left    /* Glissement depuis la gauche */
.slide-in-right   /* Glissement depuis la droite */
.skeleton         /* Animation de chargement */
```

### Utilitaires

```css
.text-gradient         /* Texte avec dégradé */
.bg-gradient-gold      /* Fond dégradé or */
.bg-gradient-blue      /* Fond dégradé bleu */
.shadow-gold           /* Ombre dorée */
.shadow-blue           /* Ombre bleue */
.d-mobile-none         /* Masqué sur mobile */
.d-mobile-only         /* Visible uniquement sur mobile */
```

---

## 🎨 Palette de Couleurs

### Couleurs Principales

```css
--bleu-nuit: #0f1a3d       /* Bleu nuit principal */
--bleu-nuit-clair: #1a237e /* Bleu nuit clair */
--blanc-or: #ffd700         /* Or principal */
--blanc-or-light: #ffe44d   /* Or clair */
--blanc-or-dark: #e6c200    /* Or foncé */
```

### Couleurs d'État

```css
--success: #10b981   /* Vert succès */
--warning: #f59e0b   /* Orange avertissement */
--danger: #ef4444    /* Rouge erreur */
--info: #3b82f6      /* Bleu information */
```

### Nuances Neutres

```css
--white: #ffffff
--light-gray: #f8fafc
--gray-100: #f1f5f9
--gray-200: #e2e8f0
--gray-300: #cbd5e1
--gray-500: #64748b
--gray-700: #334155
```

---

## 📱 Responsive Design

Le système est entièrement responsive avec des breakpoints :

- **Mobile** : < 576px
- **Tablette** : 576px - 768px
- **Desktop** : > 768px

**Exemple de grille responsive** :
```html
<div class="stats-grid">
    <!-- 1 colonne sur mobile, 2-3 sur tablette, 3-4 sur desktop -->
</div>
```

---

## ✨ Exemple de Page Complète

```blade
@extends('layouts.dashboard')

@section('content')
<div class="container-fluid dashboard-container">
    {{-- En-tête --}}
    <x-page-header 
        title="Gestion des Utilisateurs" 
        icon="fas fa-users"
        subtitle="Gérez et suivez tous les utilisateurs de la plateforme">
        <x-slot:actions>
            <a href="{{ route('users.export') }}" class="modern-btn btn-secondary-modern btn-sm-modern">
                <i class="fas fa-download"></i>
                Exporter
            </a>
            <a href="{{ route('users.create') }}" class="modern-btn btn-primary-modern">
                <i class="fas fa-plus"></i>
                Nouvel utilisateur
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Statistiques --}}
    <div class="stats-grid">
        <x-stat-card number="{{ $totalUsers }}" label="Total Utilisateurs" icon="fas fa-users" />
        <x-stat-card number="{{ $activeUsers }}" label="Utilisateurs Actifs" icon="fas fa-user-check" />
        <x-stat-card number="{{ $newUsers }}" label="Nouveaux (ce mois)" icon="fas fa-user-plus" />
    </div>

    {{-- Filtres --}}
    <div class="filters-container">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="filters-row">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-search"></i>
                        Rechercher
                    </label>
                    <input type="text" name="search" class="filter-input" placeholder="Nom, email...">
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fas fa-filter"></i>
                        Statut
                    </label>
                    <select name="status" class="filter-select">
                        <option value="">Tous</option>
                        <option value="active">Actif</option>
                        <option value="inactive">Inactif</option>
                    </select>
                </div>
            </div>
            
            <div class="filters-actions">
                <button type="submit" class="modern-btn btn-primary-modern">
                    <i class="fas fa-search"></i>
                    Filtrer
                </button>
                <a href="{{ route('users.index') }}" class="modern-btn btn-secondary-modern">
                    <i class="fas fa-redo"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    {{-- Tableau --}}
    <x-content-section title="Liste des utilisateurs" icon="fas fa-list">
        @if($users->count() > 0)
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="modern-badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group-modern">
                                    <a href="{{ route('users.show', $user) }}" class="modern-btn btn-sm-modern btn-info-modern">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="modern-btn btn-sm-modern btn-warning-modern">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="modern-btn btn-sm-modern btn-danger-modern" onclick="return confirm('Êtes-vous sûr ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="pagination-container">
                {{ $users->links() }}
            </div>
        @else
            <x-empty-state 
                icon="fas fa-users-slash"
                title="Aucun utilisateur trouvé"
                message="Aucun utilisateur ne correspond à vos critères de recherche.">
                <x-slot:action>
                    <a href="{{ route('users.index') }}" class="modern-btn btn-secondary-modern">
                        <i class="fas fa-redo"></i>
                        Réinitialiser les filtres
                    </a>
                </x-slot:action>
            </x-empty-state>
        @endif
    </x-content-section>
</div>
@endsection
```

---

## 🚀 Migration des Pages Existantes

### Étape 1 : Remplacer l'en-tête

**Avant** :
```html
<h1>Gestion des événements</h1>
<a href="#" class="btn btn-primary">Créer</a>
```

**Après** :
```blade
<x-page-header title="Gestion des événements" icon="fas fa-calendar">
    <x-slot:actions>
        <a href="#" class="modern-btn btn-primary-modern">
            <i class="fas fa-plus"></i>
            Créer
        </a>
    </x-slot:actions>
</x-page-header>
```

### Étape 2 : Remplacer les cartes

**Avant** :
```html
<div class="card">
    <div class="card-header">Titre</div>
    <div class="card-body">Contenu</div>
</div>
```

**Après** :
```blade
<x-content-section title="Titre" icon="fas fa-icon">
    Contenu
</x-content-section>
```

### Étape 3 : Remplacer les boutons

**Avant** :
```html
<button class="btn btn-primary">Enregistrer</button>
```

**Après** :
```html
<button class="modern-btn btn-primary-modern">
    <i class="fas fa-save"></i>
    Enregistrer
</button>
```

---

## 📚 Ressources

- **Icônes** : [Font Awesome 6](https://fontawesome.com/icons)
- **Polices** : Inter (système), Playfair Display (titres)
- **Framework CSS** : Bootstrap 5.3 + Système custom

---

## 💡 Bonnes Pratiques

1. **Toujours utiliser les composants** plutôt que du HTML brut
2. **Rester cohérent** avec les couleurs et espacements
3. **Tester sur mobile** avant de déployer
4. **Utiliser les animations** avec parcimonie
5. **Respecter l'accessibilité** (alt, aria-label, etc.)

---

## ✅ Checklist de Migration

- [ ] En-tête de page avec `<x-page-header>`
- [ ] Statistiques avec `<x-stat-card>`
- [ ] Sections avec `<x-content-section>`
- [ ] Boutons avec classes `.modern-btn`
- [ ] Tableaux avec `.modern-table`
- [ ] Filtres avec `.filters-container`
- [ ] États vides avec `<x-empty-state>`
- [ ] Alertes avec `.modern-alert`
- [ ] Badges avec `.modern-badge`
- [ ] Formulaires avec `.form-*-modern`

---

**Version** : 1.0.0  
**Dernière mise à jour** : Octobre 2025  
**Contact** : Équipe Développement MokiliEvent

