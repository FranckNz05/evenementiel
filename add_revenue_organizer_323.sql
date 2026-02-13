-- Ajouter 100 000 000 FCFA de revenu net à l'organisateur ID 323 (user_id 345)
-- Revenu net = 90% du total (commission 10%)
-- Donc total_revenue nécessaire = 100 000 000 / 0.9 = 111 111 111.11 FCFA

-- Étape 1: Vérifier/créer un événement pour l'organisateur si nécessaire
-- (On suppose qu'un événement existe déjà, sinon il faudra le créer)

-- Étape 2: Créer un paiement de 111 111 111.11 FCFA pour cet organisateur
-- Récupérer un événement de l'organisateur (ou créer un événement de test si aucun n'existe)

-- Option 1: Si un événement existe déjà pour l'organisateur 323
INSERT INTO paiements (
    user_id,
    evenement_id,
    montant,
    methode_paiement,
    statut,
    date_paiement,
    reference_transaction,
    reference_paiement,
    created_at,
    updated_at
)
SELECT 
    345 as user_id,  -- user_id de l'organisateur
    e.id as evenement_id,  -- ID d'un événement de l'organisateur
    111111111.11 as montant,  -- Montant pour avoir 100M de revenu net
    'Airtel Money' as methode_paiement,
    'payé' as statut,
    NOW() as date_paiement,
    CONCAT('TEST-', UNIX_TIMESTAMP(), '-', FLOOR(RAND() * 10000)) as reference_transaction,
    CONCAT('PAY-', UNIX_TIMESTAMP(), '-', FLOOR(RAND() * 10000)) as reference_paiement,
    NOW() as created_at,
    NOW() as updated_at
FROM events e
WHERE e.organizer_id = 323
AND e.deleted_at IS NULL
LIMIT 1;

-- Si aucun événement n'existe, créer d'abord un événement de test puis le paiement
-- (Décommentez cette partie si nécessaire)

/*
-- Créer un événement de test pour l'organisateur
INSERT INTO events (
    organizer_id,
    title,
    slug,
    description,
    start_date,
    end_date,
    adresse,
    ville,
    pays,
    image,
    status,
    category_id,
    is_approved,
    is_published,
    created_at,
    updated_at
)
VALUES (
    323,
    'Événement de test - Revenus',
    CONCAT('test-revenus-', UNIX_TIMESTAMP()),
    'Événement créé pour les tests de revenus',
    DATE_ADD(NOW(), INTERVAL 30 DAY),
    DATE_ADD(NOW(), INTERVAL 31 DAY),
    'Lieu de test',
    'Brazzaville',
    'Congo',
    'default-event.jpg',
    'Payant',
    1,  -- category_id (à ajuster selon votre base)
    1,  -- is_approved
    1,  -- is_published
    NOW(),
    NOW()
);

-- Puis créer le paiement avec le nouvel événement
SET @event_id = LAST_INSERT_ID();

INSERT INTO paiements (
    user_id,
    evenement_id,
    montant,
    methode_paiement,
    statut,
    date_paiement,
    reference_transaction,
    reference_paiement,
    created_at,
    updated_at
)
VALUES (
    345,
    @event_id,
    111111111.11,
    'Airtel Money',
    'payé',
    NOW(),
    CONCAT('TEST-', UNIX_TIMESTAMP(), '-', FLOOR(RAND() * 10000)),
    CONCAT('PAY-', UNIX_TIMESTAMP(), '-', FLOOR(RAND() * 10000)),
    NOW(),
    NOW()
);
*/

-- Vérification: Calculer le revenu net après insertion
-- Revenu net = total_revenue * 0.9 (car commission = 10%)
SELECT 
    SUM(p.montant) as total_revenue,
    SUM(p.montant) * 0.9 as net_revenue,
    SUM(p.montant) * 0.1 as commission
FROM paiements p
INNER JOIN events e ON p.evenement_id = e.id
WHERE e.organizer_id = 323
AND p.statut = 'payé'
AND e.deleted_at IS NULL;

