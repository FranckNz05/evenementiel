# Scénarios de Test Détaillés - MokiliEvent UAT

## Scénario 1: Inscription Utilisateur

### S1.1 - Inscription par Email
**Prérequis**: Aucun  
**Compte de test**: Nouveau compte

**Étapes**:
1. Aller sur https://uat.mokilievent.com
2. Cliquer sur "Inscription"
3. Remplir le formulaire:
   - Prénom: "Jean"
   - Nom: "Dupont"
   - Email: "jean.dupont.test@example.com"
   - Mot de passe: "Test123!"
   - Confirmation: "Test123!"
4. Cocher "J'accepte les CGU"
5. Cliquer sur "S'inscrire"

**Résultat attendu**:
- ✅ Redirection vers tableau de bord
- ✅ Message de bienvenue affiché
- ✅ Email de vérification reçu dans la boîte mail
- ✅ Profil créé dans "Mon compte"

**Données à vérifier**:
- User créé en base
- Email_verified_at = null
- Token de vérification généré

---

### S1.2 - Inscription via Google
**Prérequis**: Compte Google de test configuré

**Étapes**:
1. Sur la page d'inscription, cliquer "Continuer avec Google"
2. Sélectionner le compte Google de test
3. Autoriser l'accès aux informations

**Résultat attendu**:
- ✅ Compte créé automatiquement
- ✅ Redirection vers tableau de bord
- ✅ Photo de profil Google importée
- ✅ Email_verified_at rempli automatiquement

---

## Scénario 2: Réservation de Billets

### S2.1 - Réservation Simple (1 billet)
**Prérequis**: Être connecté comme user@uat.test  
**Événement test**: "Concert Jazz 2026"

**Étapes**:
1. Aller sur la page d'accueil
2. Chercher "Concert Jazz" dans la barre de recherche
3. Cliquer sur l'événement
4. Sélectionner "Billet Standard" (quantité: 1)
5. Cliquer sur "Réserver"
6. Vérifier le récapitulatif:
   - Événement: Concert Jazz 2026
   - Type: Standard
   - Prix: 7 596 FCFA
   - Expiration: [Date +30min]
7. Cliquer sur "Procéder au paiement"

**Résultat attendu**:
- ✅ Réservation créée avec status "Réservé"
- ✅ Timer d'expiration affiché (30 minutes)
- ✅ Réservation visible dans "Mes réservations"
- ✅ Bouton "Payer" disponible

---

### S2.2 - Paiement de Réservation
**Prérequis**: Réservation créée (S2.1)

**Étapes**:
1. Dans "Mes réservations", cliquer "Payer"
2. Sélectionner le mode de paiement "Carte bancaire"
3. Remplir les informations de carte test:
   - Numéro: 4242 4242 4242 4242
   - Exp: 12/25
   - CVV: 123
   - Nom: TEST USER
4. Cliquer sur "Payer 7 596 FCFA"

**Résultat attendu**:
- ✅ Paiement validé (mode sandbox)
- ✅ Redirection vers page de confirmation
- ✅ Email de confirmation avec billet PDF
- ✅ Status réservation: "Confirmé"
- ✅ QR Code généré et visible
- ✅ Billet téléchargeable en PDF

**Données à vérifier**:
- Payment créé avec status "payé"
- Reservation status = "Confirmé"
- QR code unique généré
- Email envoyé

---

### S2.3 - Réservation Multiple (plusieurs billets)
**Prérequis**: Être connecté

**Étapes**:
1. Sélectionner un événement
2. Ajouter 2 billets "VIP" + 3 billets "Standard"
3. Vérifier le total: (2 × 15000) + (3 × 7596) = 52788 FCFA
4. Réserver et payer

**Résultat attendu**:
- ✅ 5 billets distincts générés
- ✅ 5 QR codes uniques
- ✅ Montant total correct

---

## Scénario 3: Création d'Événement (Organisateur)

### S3.1 - Création Événement avec 1 Type de Billet
**Prérequis**: Être connecté comme orga@uat.test

**Étapes**:
1. Aller dans "Tableau de bord organisateur"
2. Cliquer sur "Créer un événement"
3. Remplir le formulaire:
   - Titre: "Soirée Gala Test UAT"
   - Description: "Événement de test pour validation UAT"
   - Catégorie: "Gala"
   - Date début: [Date future +15 jours]
   - Heure: "19:00"
   - Lieu: "Hôtel Radisson, Brazzaville"
   - Image: [Upload test-event.jpg]
4. Section Billets:
   - Nom: "Billet Entrée"
   - Prix: 10000 FCFA
   - Quantité: 100
5. Cliquer sur "Publier l'événement"

**Résultat attendu**:
- ✅ Événement créé avec status "En attente"
- ✅ Notification envoyée aux admins
- ✅ Événement visible dans "Mes événements"
- ✅ Image uploadée correctement

---

### S3.2 - Modification d'Événement
**Prérequis**: Événement créé (S3.1)

**Étapes**:
1. Dans "Mes événements", cliquer "Modifier"
2. Changer le titre en "Soirée Gala Test UAT - MODIFIÉ"
3. Ajouter un nouveau type de billet:
   - Nom: "VIP"
   - Prix: 25000 FCFA
   - Quantité: 20
4. Sauvegarder

**Résultat attendu**:
- ✅ Modifications enregistrées
- ✅ Nouveau type de billet visible
- ✅ Ancien billet toujours disponible

---

## Scénario 4: Scan de QR Codes

### S4.1 - Scan Billet Valide
**Prérequis**: 
- Connecté comme orga@uat.test
- Billet confirmé existant

**Étapes**:
1. Aller dans "Mes événements"
2. Sélectionner l'événement avec billets vendus
3. Cliquer sur "Scanner les billets"
4. Autoriser l'accès à la caméra
5. Scanner le QR code d'un billet valide

**Résultat attendu**:
- ✅ Billet reconnu
- ✅ Informations affichées: Nom, Type de billet
- ✅ Message "✅ Billet validé"
- ✅ Son de confirmation

**Données à vérifier**:
- Billet marqué comme "scanné" en base
- Timestamp de scan enregistré

---

### S4.2 - Scan Billet Déjà Utilisé
**Prérequis**: Scanner le même billet que S4.1

**Étapes**:
1. Scanner à nouveau le même QR code

**Résultat attendu**:
- ✅ Message "❌ Billet déjà scanné"
- ✅ Détails: Date/heure du premier scan
- ✅ Son d'erreur

---

## Scénario 5: Administration

### S5.1 - Approbation Organisateur
**Prérequis**: Connecté comme admin@uat.test

**Étapes**:
1. Aller dans "Admin" > "Organisateurs en attente"
2. Visualiser la demande d'un nouvel organisateur
3. Vérifier les informations
4. Cliquer sur "Approuver"

**Résultat attendu**:
- ✅ Organisateur approuvé
- ✅ Email de confirmation envoyé
- ✅ Utilisateur peut maintenant créer des événements

---

### S5.2 - Modération Événement
**Prérequis**: Événement "En attente" existant

**Étapes**:
1. Aller dans "Admin" > "Événements en attente"
2. Cliquer sur un événement
3. Vérifier le contenu
4. Options:
   - **Approuver**: Événement devient "Actif"
   - **Rejeter**: Retour à l'organisateur avec motif

**Résultat attendu**:
- ✅ Action enregistrée
- ✅ Status événement mis à jour
- ✅ Notification envoyée à l'organisateur

---

## Scénario 6: Tests Négatifs

### S6.1 - Paiement avec Carte Invalide
**Étapes**:
1. Créer une réservation
2. Utiliser carte de test refusée: 4000 0000 0000 0002
3. Tenter le paiement

**Résultat attendu**:
- ✅ Message d'erreur clair
- ✅ Réservation reste "Réservé"
- ✅ Possibilité de réessayer
- ✅ Pas de charge créée

---

### S6.2 - Réservation Expirée
**Étapes**:
1. Créer une réservation
2. Attendre 31 minutes (ou modifier manuellement en base)
3. Tenter de payer

**Résultat attendu**:
- ✅ Message "Réservation expirée"
- ✅ Tickets libérés
- ✅ Redirection vers l'événement
- ✅ Possibilité de réserver à nouveau

---

### S6.3 - Accès Non Autorisé
**Étapes**:
1. Se déconnecter
2. Tenter d'accéder à /organizer/dashboard

**Résultat attendu**:
- ✅ Redirection vers login
- ✅ Message "Veuillez vous connecter"
- ✅ Aucune donnée sensible exposée

---

## Notes pour les Testeurs

### Bonnes Pratiques
- 📸 Prendre des captures d'écran à chaque étape
- ⏱️ Noter le temps de chargement
- 🔄 Tester sur mobile + desktop
- 📧 Vérifier TOUS les emails

### Checklist Rapide
- [ ] Tous les boutons fonctionnent
- [ ] Tous les formulaires valident correctement
- [ ] Les images s'affichent
- [ ] Les emails arrivent
- [ ] Le responsive fonctionne
- [ ] Pas d'erreur console

### En Cas de Bug
1. Noter le scénario exact
2. Copier l'erreur console (F12)
3. Prendre une capture
4. Signaler immédiatement si P0/P1
