# Plan de Tests UAT - MokiliEvent

## 1. Objectifs des Tests UAT

Valider que l'application MokiliEvent répond aux besoins métier et fonctionne correctement avant la mise en production.

### Périmètre
- ✅ Inscription et authentification utilisateur
- ✅ Réservation et paiement de billets
- ✅ Gestion d'événements (organisateurs)
- ✅ Tableau de bord administrateur
- ✅ Notifications et emails
- ✅ Responsive design (mobile/desktop)

## 2. Environnement UAT

- **URL**: https://uat.mokilievent.com
- **Base de données**: `mokilievent_uat` (données de test uniquement)
- **Paiements**: Mode sandbox (aucun paiement réel)

## 3. Comptes de Test

| Rôle | Email | Mot de passe | Description |
|------|-------|--------------|-------------|
| Admin | admin@uat.test | Admin123! | Accès complet admin |
| Organisateur | orga@uat.test | Orga123! | Créer et gérer des événements |
| Utilisateur | user@uat.test | User123! | Acheter des billets |

## 4. Scénarios de Test Critiques

### 4.1 Inscription et Connexion (P0)
**Objectif**: Vérifier que les utilisateurs peuvent créer un compte et se connecter

✅ **Critères d'acceptation**:
- [ ] Inscription par email fonctionne
- [ ] Inscription via Google fonctionne
- [ ] Inscription via Facebook fonctionne
- [ ] Email de vérification est envoyé
- [ ] Connexion avec email/mot de passe fonctionne
- [ ] Réinitialisation de mot de passe fonctionne
- [ ] Redirection après connexion correcte

### 4.2 Réservation de Billets (P0)
**Objectif**: Vérifier le flux complet de réservation

✅ **Critères d'acceptation**:
- [ ] Recherche d'événements fonctionne
- [ ] Sélection de tickets fonctionne
- [ ] Réservation créée avec expiration (30min)
- [ ] Paiement en sandbox fonctionne
- [ ] Email de confirmation envoyé
- [ ] Billet visible dans "Mes billets"
- [ ] QR Code généré et fonctionnel

### 4.3 Création d'Événement (P0)
**Objectif**: Vérifier que les organisateurs peuvent créer des événements

✅ **Critères d'acceptation**:
- [ ] Upload d'image d'événement fonctionne
- [ ] Création de tickets multiples fonctionne
- [ ] Configuration des prix fonctionne
- [ ] Définition de la date/heure fonctionne
- [ ] Validation avant publication fonctionne
- [ ] Événement visible sur la page d'accueil

### 4.4 Gestion Organisateur (P1)
**Objectif**: Vérifier les fonctionnalités du tableau de bord organisateur

✅ **Critères d'acceptation**:
- [ ] Statistiques de vente affichées
- [ ] Liste des réservations visible
- [ ] Export des participants fonctionne
- [ ] Scan de QR codes fonctionne
- [ ] Modification d'événement fonctionne

### 4.5 Administration (P1)
**Objectif**: Vérifier les fonctionnalités admin

✅ **Critères d'acceptation**:
- [ ] Liste des utilisateurs visible
- [ ] Approbation d'organisateurs fonctionne
- [ ] Modération d'événements fonctionne
- [ ] Statistiques globales affichées

## 5. Tests de Performance

- [ ] Page d'accueil charge en < 2 secondes
- [ ] Recherche retourne résultats en < 1 seconde
- [ ] Paiement complété en < 5 secondes
- [ ] Application supporte 50 utilisateurs simultanés

## 6. Tests de Compatibilité

### Navigateurs
- [ ] Chrome (dernière version)
- [ ] Firefox (dernière version)
- [ ] Safari (dernière version)
- [ ] Edge (dernière version)

### Appareils
- [ ] Desktop (1920x1080)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

## 7. Tests de Sécurité

- [ ] Tentative d'accès non autorisé bloquée
- [ ] Injection SQL impossible
- [ ] XSS protection active
- [ ] CSRF protection active
- [ ] Données sensibles chiffrées

## 8. Reporting des Bugs

### Sévérité
- **Critique (P0)**: Bloque l'utilisation - correction immédiate
- **Haute (P1)**: Fonctionnalité majeure cassée - correction prioritaire
- **Moyenne (P2)**: Problème mineur - correction avant production
- **Basse (P3)**: Cosmétique - correction optionnelle

### Template de Bug
```
Titre: [Description courte]
Sévérité: [P0/P1/P2/P3]
Étapes de reproduction:
1. 
2. 
3. 

Résultat attendu: 
Résultat obtenu: 
Navigateur/Appareil: 
Capture d'écran: [si applicable]
```

## 9. Critères de Validation

L'application peut passer en production si:
- ✅ Tous les tests P0 passent à 100%
- ✅ Tests P1 passent à minimum 90%
- ✅ Aucun bug critique ouvert
- ✅ Performance acceptable
- ✅ Compatibilité validée sur navigateurs principaux

## 10. Planning

| Phase | Durée | Responsable |
|-------|-------|-------------|
| Setup environnement UAT | 1 jour | DevOps |
| Tests fonctionnels | 3 jours | Testeurs |
| Corrections bugs | 2 jours | Développeurs |
| Re-tests | 1 jour | Testeurs |
| Validation finale | 1 jour | Product Owner |

**Durée totale estimée**: 8 jours ouvrés
