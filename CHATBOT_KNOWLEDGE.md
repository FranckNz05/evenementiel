# MokiliEvent – Fichier de connaissances pour chatbot

## 1) Aperçu
- Plateforme de billetterie et gestion d’événements au Congo.
- Deux parcours majeurs:
  - Événements publics (billetterie classique)
  - Événements personnalisés (formules Start/Standard/Premium/Ultimate)

## 2) Liens principaux (frontend)
- Accueil: /
- À propos: /about
- Contact: /contact
- Blog: /blogs
- FAQ: /faq
- Conditions: /terms
- Confidentialité: /privacy
- Explorer tous les événements: /direct-events
- Organisateurs: /organizers (ou route('organizers.index'))
- Documentation synthèse: /docs

## 3) Authentification et profil
- Connexion: /login
- Inscription: /register
- Mon profil: route('profile.edit')
- Réservations (client): /reservations
- Historique paiements: route('payments.history')

## 4) Événements publics
- Liste: /direct-events
- Détail: route('events.show', {event})
- Réservations:
  - Liste: /reservations
  - Détail: /reservations/{reservation}

## 5) Événements personnalisés – Offres et flux
- Offres: /evenements-personnalises (route('custom-offers.index'))
- Paiement (simulation): /evenements-personnalises/paiement?plan={start|standard|premium|ultimate}
  - Opérateurs: Airtel Money, MTN Mobile Money
  - Numéro: 05xxxxxxx, 06xxxxxxx, 04xxxxxxx
- Confirmation: /evenements-personnalises/confirmation?plan=...&operator=...&purchase={id}
- Tableau de bord: route('custom-events.index')
  - Historique des événements personnalisés
  - Offres non utilisées (achats non consommés)
  - Bouton “Utiliser” → Wizard avec purchase={id}

### Wizard (création personnalisée)
- Étape 1: /custom-events/wizard/step1[?purchase={id}]
- Étape 2: /custom-events/wizard/step2
- Étape 3: /custom-events/wizard/step3
- Fin: /custom-events/wizard/complete (crée l’événement + marque l’offre utilisée)

### Règles par formule
- Start: max 100 invités, pas d’ajout après création, pas d’exports ni URL temps réel, support email
- Standard: max 300 invités, ajout après création, envois/programmation, URL temps réel, stats, support email+WhatsApp
- Premium: max 800 invités, envois/programmation, stats/temps réel, exports CSV/PDF, rappel J-1, support email+WhatsApp+téléphone
- Ultimate: max 1500 invités, toutes options Premium + outils avancés contrôle d’accès

### Contrôles serveur
- Invitations (simulation): route('custom-events.invitations.send-all') → 403 si offre < Standard
- Export CSV invités: route('custom-events.guests.export', {event}) → 403 si offre < Premium
- Ajout d’invités: interdit après création si offre = Start

## 6) Rôles & sécurité
- Rôles: Organisateur, Client
- Middleware rôles pour les pages protégées
- CSRF sur formulaires

## 7) Paiements
- Opérateurs: Airtel Money, MTN Mobile Money
- Regex numéro: ^0(4|5|6)\d{7}$
- Achats persistés: table custom_offer_purchases (plan, price, operator, phone, used_at)
- Création personnalisée consomme une offre (used_at=now)

## 8) Réservations (client)
- POST /reservations (avec tickets)
- GET /reservations/{reservation}
- Annulation: POST /reservations/{reservation}/cancel (ou PATCH)
- Affichage:
  - Événement: titre, date, lieu
  - Tickets: nom, quantité, prix unitaire (pivot->price), total par ligne
  - Total = somme des (quantité*prix)

## 9) Pages utiles
- À propos: /about
- Aide/Docs: /docs
- Offres personnalisées: /evenements-personnalises
- Dashboard personnalisé: route('custom-events.index')
- Wizard: /custom-events/wizard/step1
- Tous les événements: /direct-events
- Organisateurs: route('organizers.index')
- Réservations: /reservations

## 10) Domaine
- https://www.mokilievent.com

## 11) Exemples de prompts
- “Créer un événement personnalisé Premium” → /evenements-personnalises → payer → /custom-events/wizard/step1
- “Pourquoi l’export n’est pas disponible ?” → réservé à Premium+
- “Programmer des invitations WhatsApp ?” → disponible à partir de Standard

## 12) Support
- Email: setting `contact_email` (fallback: contact@mokilievent.com)
- Téléphone: setting `phone_number` (fallback: +242064088868 / +242057668371)
