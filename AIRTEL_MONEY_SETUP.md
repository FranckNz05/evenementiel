# Configuration Airtel Money

## Variables d'environnement à ajouter dans votre fichier `.env`

Ajoutez ces variables à votre fichier `.env` :

```bash
# Configuration Airtel Money
AIRTEL_CLIENT_ID=b280b215-8b00-4be4-bfbb-02f9b2a155c5
AIRTEL_CLIENT_SECRET=c8ecb836-657e-429f-ae34-d4e646cde2f1

# Configuration environnement
AIRTEL_PRODUCTION=false
AIRTEL_COUNTRY=CG
AIRTEL_CURRENCY=XAF

# Sécurité des callbacks (optionnel)
AIRTEL_CALLBACK_AUTH_ENABLED=true
AIRTEL_CALLBACK_PRIVATE_KEY=

# Signature des requêtes (optionnel - désactivé par défaut)
AIRTEL_SIGNATURE_ENABLED=false
AIRTEL_SIGNATURE_KEY=
AIRTEL_SIGNATURE_IV=

# Clé RSA pour le chiffrement (sera récupérée automatiquement via l'API)
AIRTEL_RSA_PUBLIC_KEY=
```

## Comment utiliser l'intégration Airtel Money

### 1. Service AirtelMoneyService

Le service `AirtelMoneyService` fournit toutes les méthodes nécessaires :

```php
use App\Services\AirtelMoneyService;

$airtelService = new AirtelMoneyService();

// Initier un paiement
$result = $airtelService->initiatePayment([
    'phone' => '242061234567',
    'amount' => 1000,
    'reference' => 'TXN_12345'
]);

// Vérifier le statut d'une transaction
$status = $airtelService->checkTransactionStatus('transaction_id');

// Effectuer un retrait (disbursement)
$disburse = $airtelService->disburse([
    'phone' => '242061234567',
    'amount' => 500,
    'pin' => '1234',
    'reference' => 'DISBURSE_123'
]);

// Vérifier les informations utilisateur
$userInfo = $airtelService->getUserInfo('242061234567');
```

### 2. Gateway AirtelMoneyGateway

Pour une intégration standardisée avec l'interface `PaymentGatewayInterface` :

```php
use App\Services\AirtelMoneyGateway;

$gateway = new AirtelMoneyGateway();

// Créer une session de paiement
$session = $gateway->createPaymentSession([
    'phone' => '242061234567',
    'amount' => 1000,
    'reference' => 'TXN_12345'
]);

// Vérifier un paiement
$verification = $gateway->verifyPayment('reference');

// Traiter un webhook
$webhookResult = $gateway->handleWebhook($payload);
```

### 3. Webhook

Le webhook est déjà configuré à l'URL : `/webhooks/airtel/callback`

Le contrôleur `AirtelCallbackController` gère automatiquement :
- La vérification de signature HMAC
- La mise à jour des paiements
- La confirmation des commandes
- L'envoi des billets

### 4. Configuration du webhook dans Airtel

Dans votre tableau de bord Airtel Money :
1. Allez dans les paramètres de l'application
2. Configurez l'URL de callback : `https://mokilievent.com/webhooks/airtel/callback`
3. Si l'authentification HMAC est activée, configurez la clé privée

## Codes d'erreur Airtel Money

Le service gère automatiquement les codes d'erreur suivants :

- `DP00800001001` : Transaction réussie
- `DP00800001002` : PIN incorrect
- `DP00800001003` : Limite de transaction dépassée
- `DP00800001007` : Solde insuffisant
- `DP00800001024` : Transaction expirée (timeout)

## Sécurité

- Le chiffrement RSA est utilisé pour sécuriser les PINs
- Les clés de chiffrement sont automatiquement récupérées et mises en cache
- L'authentification HMAC peut être activée pour les webhooks
- Toutes les données sensibles sont chiffrées et non loggées

## Test de l'intégration

Pour tester l'intégration :

```php
// Tester la récupération du token
$service = new AirtelMoneyService();
$token = $service->getAccessToken();

// Tester les clés de chiffrement
$keys = $service->getEncryptionKeys();

// Tester le chiffrement PIN
$encryptedPin = $service->encryptPin('1234');
```
