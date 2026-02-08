# Guide Utilisateur UAT - MokiliEvent

## Bienvenue Testeur UAT ! 👋

Merci de participer aux tests de l'application MokiliEvent. Ce guide vous explique comment accéder à l'environnement de test et signaler les problèmes.

## 1. Accès à l'Environnement UAT

### URL de Test
**🌐 https://uat.mokilievent.com**

> ⚠️ **Important**: Ne PAS utiliser de vraies informations personnelles ou de paiement. Cet environnement est uniquement pour les tests.

### Comptes de Test Disponibles

Utilisez ces comptes selon votre rôle de test:

#### Utilisateur Standard
- **Email**: user@uat.test
- **Mot de passe**: User123!
- **Rôle**: Acheter des billets, gérer son profil

#### Organisateur
- **Email**: orga@uat.test
- **Mot de passe**: Orga123!
- **Rôle**: Créer et gérer des événements

#### Administrateur
- **Email**: admin@uat.test
- **Mot de passe**: Admin123!
- **Rôle**: Gérer l'application, approuver les organisateurs

## 2. Que Tester ?

### Parcours Utilisateur Standard

1. **Inscription/Connexion**
   - Créez un nouveau compte
   - Connectez-vous avec Google/Facebook (optionnel)
   - Testez la réinitialisation de mot de passe

2. **Recherche d'Événements**
   - Cherchez un événement par nom
   - Filtrez par catégorie
   - Visualisez les détails d'un événement

3. **Réservation de Billets**
   - Sélectionnez un type de billet
   - Ajoutez au panier
   - Complétez le paiement (mode test)
   - Vérifiez l'email de confirmation

4. **Gestion du Profil**
   - Modifiez vos informations
   - Consultez vos réservations
   - Téléchargez vos billets (QR codes)

### Parcours Organisateur

1. **Création d'Événement**
   - Créez un nouvel événement
   - Ajoutez une image principale
   - Configurez les types de billets
   - Publiez l'événement

2. **Gestion d'Événement**
   - Visualisez les statistiques de vente
   - Consultez la liste des participants
   - Exportez les données
   - Testez le scan de QR codes

### Parcours Administrateur

1. **Approbation d'Organisateurs**
   - Consultez les demandes
   - Approuvez/Refusez des organisateurs

2. **Modération de Contenu**
   - Visualisez les événements en attente
   - Approuvez ou supprimez des contenus

3. **Statistiques Globales**
   - Consultez le tableau de bord admin
   - Vérifiez les métriques

## 3. Comment Signaler un Bug ?

### Via Email
Envoyez un email à: **bugs-uat@mokilievent.com**

### Template à Utiliser

```
Sujet: [UAT] Titre court du problème

Sévérité: [Critique / Haute / Moyenne / Basse]

Étapes pour reproduire:
1. Aller sur la page...
2. Cliquer sur...
3. Saisir...

Résultat attendu:
[Ce qui devrait se passer]

Résultat obtenu:
[Ce qui s'est réellement passé]

Informations techniques:
- Navigateur: [Chrome/Firefox/Safari/Edge]
- Version: [Ex: Chrome 120]
- Appareil: [Desktop/Tablet/Mobile]
- Système: [Windows/Mac/iOS/Android]

Capture d'écran:
[Joindre une capture si possible]
```

### Exemple de Bug Signalé

```
Sujet: [UAT] Impossible de finaliser le paiement

Sévérité: Critique

Étapes pour reproduire:
1. Se connecter comme user@uat.test
2. Sélectionner l'événement "Concert Jazz"
3. Ajouter 2 billets VIP
4. Cliquer sur "Payer"
5. Remplir les informations de paiement test
6. Cliquer sur "Confirmer le paiement"

Résultat attendu:
- Le paiement est validé
- Page de confirmation affichée
- Email de confirmation reçu

Résultat obtenu:
- Erreur 500 affichée
- Pas de redirection
- Aucun email reçu

Informations techniques:
- Navigateur: Chrome
- Version: 120.0
- Appareil: Desktop
- Système: Windows 11

Capture d'écran: [pièce jointe]
```

## 4. Sévérité des Bugs

### 🔴 Critique (P0)
**L'application ne fonctionne pas**
- Impossible de se connecter
- Paiement bloqué
- Page blanche/erreur systématique

### 🟠 Haute (P1)
**Fonctionnalité majeure cassée**
- Email non envoyé
- QR code ne génère pas
- Dashboard ne charge pas

### 🟡 Moyenne (P2)
**Problème mineur mais gênant**
- Filtre de recherche ne fonctionne pas
- Image ne s'affiche pas correctement
- Texte mal traduit

### 🟢 Basse (P3)
**Cosmétique ou amélioration**
- Alignement de texte
- Couleur étrange
- Suggestion d'amélioration

## 5. Bonnes Pratiques de Test

### ✅ À Faire
- Tester sur plusieurs navigateurs
- Essayer différents scénarios
- Vérifier les emails (consultez votre boîte mail)
- Documenter précisément les problèmes
- Re-tester après correction

### ❌ À Éviter
- Utiliser de vraies informations bancaires
- Créer trop de données inutiles
- Signaler le même bug plusieurs fois
- Reporter des bugs vagues sans détails

## 6. Carte de Test (Paiement)

Pour tester les paiements en mode sandbox:

**Carte de Test Fonctionnelle**
```
Numéro: 4242 4242 4242 4242
Expiration: 12/25
CVV: 123
Nom: TEST USER
```

**Carte de Test - Paiement Refusé**
```
Numéro: 4000 0000 0000 0002
Expiration: 12/25
CVV: 123
```

## 7. Support

Si vous avez des questions ou besoin d'aide:
- **Email**: support-uat@mokilievent.com
- **WhatsApp**: +242 XX XXX XXXX
- **Disponibilité**: Lun-Ven, 9h-18h

## 8. Feedback Général

En plus des bugs, n'hésitez pas à partager:
- 💡 Suggestions d'amélioration
- 🎨 Retours sur le design
- 📱 Expérience utilisateur
- ⚡ Performance ressentie

---

**Merci pour votre contribution aux tests ! 🙏**

Votre feedback est essentiel pour garantir la qualité de MokiliEvent avant le lancement public.
