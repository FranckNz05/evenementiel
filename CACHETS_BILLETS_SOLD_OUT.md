# ✅ Cachets Indisponible et SOLD OUT

## 🎯 Fonctionnalités Ajoutées

### 1. Cachet "Indisponible" sur les billets individuels
Pour chaque billet épuisé (quantité = 0) :
- 📋 Badge blanc "INDISPONIBLE" en haut à droite du billet
- 🎨 Design avec bordure noire et ombre
- ⚠️ Les contrôles de quantité sont désactivés

### 2. Cachet "SOLD OUT" sur la section complète
Quand TOUS les billets sont épuisés :
- 📋 Cachet blanc "SOLD OUT" en haut à droite de la section
- 🎨 Style de cachet roté à 15 degrés avec bordure épaisse
- 🎯 L'en-tête de la section devient semi-transparent (opacity: 0.7)

## 🎨 Styles Appliqués

### Cachet Indisponible
```css
.unavailable-badge {
    background: var(--blanc-or);  /* blanc doré */
    color: #000;                  /* Texte noir */
    border: 2px solid #000;       /* Bordure noire épaisse */
    border-radius: 50px;          /* Arrondi */
    text-transform: uppercase;    /* Majuscules */
    font-weight: 800;             /* Gras */
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.4);
}
```

### Cachet SOLD OUT
```css
.sold-out-stamp {
    position: absolute;
    right: -30px;
    transform: rotate(15deg);     /* Rotation pour effet cachet */
    background: var(--blanc-or);
    color: #000;
    padding: 1rem 2rem;
    border: 3px solid #000;       /* Bordure très épaisse */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    font-weight: 900;
    font-size: 1.5rem;
}
```

## 📋 Logique

### Vérification des billets épuisés
```php
$remainingTickets = $ticket->quantite - $ticket->quantite_vendue;
if ($remainingTickets <= 0) {
    // Afficher badge "Indisponible"
}
```

### Vérification si tous les billets sont vendus
```php
$allTicketsSoldOut = $event->tickets->every(function($ticket) {
    return ($ticket->quantite - $ticket->quantite_vendue) <= 0;
});
```

## 🎯 Résultat

✅ Billets individuels : Badge "Indisponible" blanc avec bordure noire  
✅ Section complète : Cachet "SOLD OUT" roté et stylisé  
✅ Design cohérent avec le thème blanc doré  
✅ Indicateurs visuels clairs pour l'utilisateur

---

**🎊 Les cachets ajoutent un cachet professionnel à votre plateforme !**

