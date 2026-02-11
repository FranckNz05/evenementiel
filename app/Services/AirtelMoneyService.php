<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Exception;

class AirtelMoneyService
{
    // Constantes pour les statuts de transaction
    const STATUS_INITIATED = 'initiated';
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_AMBIGUOUS = 'ambiguous';
    const STATUS_EXPIRED = 'expired';
    const STATUS_TIMEOUT = 'timeout';
    const STATUS_REFUSED = 'refused';
    const STATUS_ERROR = 'error';
    const STATUS_UNKNOWN = 'unknown';

    // Codes de réponse Airtel Money
    const CODE_SUCCESS = 'DP00800001001';
    const CODE_PENDING_USER_CONFIRMATION = 'DP00800001006';
    const CODE_AMBIGUOUS = 'DP00800001000';

    // Codes Disbursement (Série DP0090)
    const CODE_SUCCESS_DISBURSEMENT = 'DP00900001001';
    const CODE_PENDING_DISBURSEMENT = 'DP00900001006';
    const CODE_AMBIGUOUS_DISBURSEMENT = 'DP00900001000';

    // Statuts officiels Airtel (selon documentation)
    const AIRTEL_STATUS_TS = 'TS'; // Transaction Success
    const AIRTEL_STATUS_TF = 'TF'; // Transaction Failed
    const AIRTEL_STATUS_TA = 'TA'; // Transaction Ambiguous
    const AIRTEL_STATUS_TIP = 'TIP'; // Transaction in Progress
    const AIRTEL_STATUS_TE = 'TE'; // Transaction Expired

    // Configuration par défaut
    protected $httpTimeout = 30; // secondes
    protected $httpConnectTimeout = 10; // secondes
    protected $minAmount = 100; // Montant minimum en FCFA

    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $country;
    protected $currency;
    protected $isProduction;
    protected $rsaPublicKey;
    protected $rsaKeyId;
    protected $rsaKeyExpiry;

    /**
     * Codes d'erreur Airtel Money et leurs descriptions
     */
    protected $errorCodes = [
        // --- CODES DE PAIEMENT / COLLECTION (DP0080...) ---
        'DP00800001000' => [
            'status' => 'ambiguous',
            'message' => 'Transaction en statut ambigu. Une vérification automatique est en cours pour déterminer le statut final.',
            'retry' => true,
            'requires_polling' => true,
        ],
        'DP00800001001' => [
            'status' => 'success',
            'message' => 'Transaction is successful.',
            'retry' => false,
        ],
        'DP00800001002' => [
            'status' => 'failed',
            'message' => 'Incorrect pin has been entered.',
            'retry' => true,
        ],
        'DP00800001003' => [
            'status' => 'failed',
            'message' => 'Exceeds withdrawal amount limit(s) / Withdrawal amount limit exceeded. The User has exceeded their wallet allowed transaction limit.',
            'retry' => false,
        ],
        'DP00800001004' => [
            'status' => 'failed',
            'message' => 'Invalid Amount. The amount User is trying to transfer is less than the minimum amount allowed.',
            'retry' => false,
        ],
        'DP00800001005' => [
            'status' => 'failed',
            'message' => 'Transaction ID is invalid. User didn\'t enter the pin.',
            'retry' => true,
        ],
        'DP00800001006' => [
            'status' => 'pending',
            'message' => 'Veuillez confirmer le paiement sur votre téléphone. La transaction est en attente de votre confirmation.',
            'retry' => true,
            'requires_user_action' => true,
        ],
        'DP00800001007' => [
            'status' => 'failed',
            'message' => 'Not enough balance. User wallet does not have enough money to cover the payable amount.',
            'retry' => false,
        ],
        'DP00800001008' => [
            'status' => 'refused',
            'message' => 'Refused. The transaction was refused.',
            'retry' => false,
        ],
        'DP00800001010' => [
            'status' => 'failed',
            'message' => 'Transaction not permitted to Payee. Payee is already initiated for churn or barred or not registered on Airtel Money platform.',
            'retry' => false,
        ],
        'DP00800001024' => [
            'status' => 'timeout',
            'message' => 'Transaction Timed Out. The transaction was timed out.',
            'retry' => true,
        ],
        'DP00800001025' => [
            'status' => 'not_found',
            'message' => 'Transaction Not Found. The transaction was not found.',
            'retry' => false,
        ],

        // --- CODES DE DISBURSEMENT (DP0090...) ---
        'DP00900001000' => [ // Ambiguous
            'status' => 'ambiguous',
            'message' => 'The transaction is still processing and is in ambiguous state. Please do the transaction enquiry.',
            'retry' => true,
            'requires_polling' => true,
        ],
        'DP00900001001' => [ // Success
            'status' => 'success',
            'message' => 'Transaction is successful.',
            'retry' => false,
        ],
        'DP00900001003' => [ // Limit reached
            'status' => 'failed',
            'message' => 'Maximum transaction limit reached for the day.',
            'retry' => false,
        ],
        'DP00900001004' => [ // Invalid Amount
            'status' => 'failed',
            'message' => 'Amount entered is out of range with respect to defined limits.',
            'retry' => false,
        ],
        'DP00900001005' => [ // Failed
            'status' => 'failed',
            'message' => 'Transaction failed or refused.',
            'retry' => false,
        ],
        'DP00900001006' => [ // In progress
            'status' => 'pending',
            'message' => 'The transaction is still in progress. Please do the transaction enquiry.',
            'retry' => true,
            'requires_polling' => true,
        ],
        'DP00900001007' => [ // Insufficient Funds
            'status' => 'failed',
            'message' => 'Not enough funds in account to complete the transaction.',
            'retry' => false,
        ],
        'DP00900001009' => [ // Invalid Initiatee
            'status' => 'failed',
            'message' => 'Initiatee of the transaction is invalid.',
            'retry' => false,
        ],
        'DP00900001010' => [ // User Not Allowed
            'status' => 'failed',
            'message' => 'Payer is not an allowed user.',
            'retry' => false,
        ],
        'DP00900001012' => [ // Invalid Mobile Number
            'status' => 'failed',
            'message' => 'Mobile number entered is incorrect',
            'retry' => false,
        ],
        'DP00900001013' => [ // Transaction Refused
            'status' => 'refused',
            'message' => 'The transaction was refused.',
            'retry' => false,
        ],
        'DP00900001015' => [ // Transaction not Found
            'status' => 'not_found',
            'message' => 'Transaction is not found',
            'retry' => false,
        ],
        'DP00900001017' => [ // Duplicate Transaction Id
            'status' => 'failed',
            'message' => 'Duplicate Transaction Id.',
            'retry' => false,
        ],
        'DP00900001018' => [ // Forbidden
            'status' => 'failed',
            'message' => 'Forbidden. X-signature and payload did not match.',
            'retry' => true,
        ],
        'DP00900001019' => [ // Sender is Barred
            'status' => 'failed',
            'message' => 'Sender is Barred. Payer is Barred',
            'retry' => false,
        ],

        // Codes d'erreur pour l'API de chiffrement
        'DP02010001000' => [
            'status' => 'error',
            'message' => 'Erreur lors de la récupération de la clé de chiffrement.',
            'retry' => true,
        ],
        'DP02010001001' => [
            'status' => 'success',
            'message' => 'Clé de chiffrement récupérée avec succès.',
            'retry' => false,
        ],
    ];

    public function __construct()
    {
        $this->isProduction = config('services.airtel.production', false);
        // Utiliser l'URL de la documentation officielle avec les vraies clés
        $this->baseUrl = $this->isProduction
            ? 'https://openapi.airtel.cg'  // Production - à confirmer
            : 'https://openapiuat.airtel.cg'; // Test/UAT selon la doc

        $this->clientId = config('services.airtel.client_id');
        $this->clientSecret = config('services.airtel.client_secret');
        $this->country = config('services.airtel.country', 'CG'); // Congo par défaut
        $this->currency = config('services.airtel.currency', 'XAF'); // FCFA par défaut
    }

    /**
     * Obtient les informations d'erreur à partir d'un code d'erreur
     */
    protected function getErrorInfo($errorCode)
    {
        if (isset($this->errorCodes[$errorCode])) {
            return $this->errorCodes[$errorCode];
        }

        // Code d'erreur inconnu
        return [
            'status' => 'unknown',
            'message' => 'Erreur inconnue lors de la transaction.',
            'retry' => false,
        ];
    }

    /**
     * Détermine si un code de réponse indique un vrai succès (transaction complétée)
     * 
     * @param string|null $responseCode Code de réponse Airtel Money
     * @return bool
     */
    protected function isRealSuccess($responseCode)
    {
        return $responseCode === self::CODE_SUCCESS || $responseCode === self::CODE_SUCCESS_DISBURSEMENT;
    }

    /**
     * Détermine si un code de réponse indique un statut pending (nécessite confirmation utilisateur)
     * 
     * @param string|null $responseCode Code de réponse Airtel Money
     * @return bool
     */
    protected function isPendingStatus($responseCode)
    {
        return $responseCode === self::CODE_PENDING_USER_CONFIRMATION || $responseCode === self::CODE_PENDING_DISBURSEMENT;
    }

    /**
     * Détermine si un code de réponse indique un statut ambiguous (nécessite polling)
     * 
     * @param string|null $responseCode Code de réponse Airtel Money
     * @return bool
     */
    protected function isAmbiguousStatus($responseCode)
    {
        return $responseCode === self::CODE_AMBIGUOUS || $responseCode === self::CODE_AMBIGUOUS_DISBURSEMENT;
    }

    /**
     * Crée une instance Http avec timeouts configurés
     * 
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function httpClient()
    {
        return Http::timeout($this->httpTimeout)
            ->connectTimeout($this->httpConnectTimeout);
    }

    /**
     * Obtient un token OAuth2 pour l'authentification
     */
    protected function getAccessToken()
    {
        try {
            $response = $this->httpClient()->asForm()->post($this->baseUrl . '/auth/oauth2/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if (!$response->successful()) {
                Log::error('Erreur lors de l\'obtention du token Airtel', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new Exception('Impossible d\'obtenir le token d\'authentification Airtel');
            }

            $data = $response->json();
            return $data['access_token'] ?? null;

        } catch (Exception $e) {
            Log::error('Exception lors de l\'obtention du token Airtel', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Récupère les clés RSA de chiffrement depuis l'API Airtel
     * 
     * @return array Clés de chiffrement RSA
     */
    public function getEncryptionKeys()
    {
        try {
            // Vérifier le cache d'abord (les clés sont valides jusqu'à leur expiration)
            $cacheKey = 'airtel_encryption_keys_' . $this->country;
            $cachedKeys = Cache::get($cacheKey);
            
            if ($cachedKeys && isset($cachedKeys['valid_upto'])) {
                $expiryDate = \Carbon\Carbon::parse($cachedKeys['valid_upto']);
                if ($expiryDate->isFuture()) {
                    Log::info('Utilisation des clés RSA en cache', [
                        'key_id' => $cachedKeys['key_id'] ?? null,
                        'expires_at' => $cachedKeys['valid_upto']
                    ]);
                    return $cachedKeys;
                }
            }

            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new Exception('Impossible d\'obtenir le token d\'authentification');
            }

            $headers = [
                'Accept' => 'application/json',
                'X-Country' => $this->country,
                'X-Currency' => $this->currency,
                'Authorization' => 'Bearer ' . $accessToken,
            ];


            Log::info('Récupération des clés RSA de chiffrement Airtel', [
                'base_url' => $this->baseUrl,
                'endpoint' => '/v1/rsa/encryption-keys'
            ]);

            $response = $this->httpClient()->withHeaders($headers)
                ->get($this->baseUrl . '/v1/rsa/encryption-keys');

            $responseData = $response->json();

            if (!$response->successful()) {
                $errorCode = $responseData['status']['response_code'] ?? $responseData['status']['result_code'] ?? null;
                $httpStatus = $response->status();
                
                Log::error('Erreur lors de la récupération des clés RSA via API', [
                    'http_status' => $httpStatus,
                    'error_code' => $errorCode,
                    'response' => $responseData,
                    'headers' => $headers
                ]);


                // TENTATIVE DE FALLBACK SUR LA CLÉ CONFIGURÉE
                $fallbackKey = env('AIRTEL_RSA_PUBLIC_KEY');
                if ($fallbackKey) {
                    Log::warning('Utilisation de la clé RSA de fallback configurée dans .env suite à l\'échec de l\'API');
                    return [
                        'key_id' => 'fallback',
                        'key' => str_replace('\n', "\n", $fallbackKey), // Gérer les sauts de ligne si échappés
                        'valid_upto' => now()->addYears(1)->toDateTimeString(), // Validité arbitraire
                    ];
                }

                $errorMessage = $responseData['status']['message'] ?? 'Erreur lors de la récupération des clés de chiffrement';
                
                // Améliorer le message d'erreur selon le code HTTP
                if ($httpStatus >= 500) {
                    $errorMessage = 'Erreur serveur Airtel (HTTP ' . $httpStatus . '). API indisponible.';
                } elseif ($httpStatus === 401 || $httpStatus === 403) {
                    $errorMessage = 'Erreur d\'authentification Airtel (HTTP ' . $httpStatus . '). ' .
                                   'Vérifiez vos credentials AIRTEL_CLIENT_ID et AIRTEL_CLIENT_SECRET. ' .
                                   'Configurez AIRTEL_RSA_PUBLIC_KEY dans votre fichier .env comme solution de secours.';
                } elseif ($httpStatus === 404) {
                    $errorMessage = 'Endpoint Airtel introuvable (HTTP 404). ' .
                                   'L\'endpoint de récupération des clés RSA n\'existe peut-être pas pour votre environnement. ' .
                                   'Configurez AIRTEL_RSA_PUBLIC_KEY dans votre fichier .env.';
                }

                throw new Exception($errorMessage . ' Aucune clé de fallback trouvée (configurez AIRTEL_RSA_PUBLIC_KEY).');
            }

            $isSuccess = $responseData['status']['success'] ?? false;
            $errorCode = $responseData['status']['response_code'] ?? null;

            if ($isSuccess && $errorCode === 'DP02010001001' && isset($responseData['data'])) {
                $encryptionData = $responseData['data'];
                
                // Mettre en cache jusqu'à l'expiration
                $expiryDate = \Carbon\Carbon::parse($encryptionData['valid_upto']);
                $cacheMinutes = max(1, $expiryDate->diffInMinutes(now()));
                
                Cache::put($cacheKey, $encryptionData, now()->addMinutes($cacheMinutes));
                
                Log::info('Clés RSA récupérées avec succès', [
                    'key_id' => $encryptionData['key_id'] ?? null,
                    'expires_at' => $encryptionData['valid_upto']
                ]);

                return $encryptionData;
            }

            throw new Exception($responseData['status']['message'] ?? 'Impossible de récupérer les clés de chiffrement');

        } catch (Exception $e) {
            Log::error('Exception lors de la récupération des clés RSA', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Chiffre des données avec RSA (pour le PIN)
     * Utilise RSA avec OAEP padding et SHA-256 (requis par Airtel)
     * 
     * @param string $data Données à chiffrer
     * @param string|null $publicKey Clé publique RSA
     * @return string Données chiffrées en Base64
     * @throws Exception Si le chiffrement échoue
     */
    public function encryptWithRSA($data, $publicKey = null)
    {
        try {
            // Récupérer la clé publique si non fournie
            if (!$publicKey) {
                $encryptionKeys = $this->getEncryptionKeys();
                $publicKey = $encryptionKeys['key'] ?? null;
                
                if (!$publicKey) {
                    throw new Exception('Clé publique RSA introuvable');
                }
            }

            // Normaliser la clé (enlever les espaces inutiles)
            $publicKey = trim($publicKey);

            // Utiliser phpseclib pour un support complet de OAEP
            if (class_exists('\phpseclib3\Crypt\PublicKeyLoader')) {
                try {
                    // Airtel peut nécessiter SHA-256 (recommandé) ou SHA-1 (standard historique)
                    // La plupart des implémentations attendent MGF1 avec SHA-1 même pour un hash SHA-256
                    $hash = config('services.airtel.oaep_hash', 'sha256');
                    $mgfHash = config('services.airtel.oaep_mgf', 'sha1');

                    Log::debug("Chiffrement RSA avec phpseclib: Hash=$hash, MGF=$mgfHash");

                    $rsa = \phpseclib3\Crypt\PublicKeyLoader::load($publicKey)
                        ->withPadding(\phpseclib3\Crypt\RSA::ENCRYPTION_OAEP)
                        ->withHash($hash)
                        ->withMGFHash($mgfHash);
                    
                    $encrypted = $rsa->encrypt($data);
                    
                    Log::debug("Chiffrement RSA réussi avec phpseclib ($hash / MGF1-$mgfHash)");
                    return base64_encode($encrypted);
                } catch (Exception $e) {
                    Log::warning('Échec SHA-256 OAEP, tentative avec SHA-1 OAEP...', ['error' => $e->getMessage()]);
                    
                    try {
                        $rsa = \phpseclib3\Crypt\PublicKeyLoader::load($publicKey)
                            ->withPadding(\phpseclib3\Crypt\RSA::ENCRYPTION_OAEP)
                            ->withHash('sha1')
                            ->withMGFHash('sha1');
                        
                        $encrypted = $rsa->encrypt($data);
                        Log::debug('Chiffrement RSA réussi avec phpseclib (OAEP SHA-1)');
                        return base64_encode($encrypted);
                    } catch (Exception $e2) {
                        Log::error('Échec total phpseclib', ['error' => $e2->getMessage()]);
                    }
                }
            }

            // Fallback sur OpenSSL natif (SHA-1 OAEP par défaut en PHP)
            $publicKeyResource = openssl_pkey_get_public($publicKey);
            if (!$publicKeyResource) {
                throw new Exception('Clé publique RSA invalide: ' . openssl_error_string());
            }

            $encrypted = '';
            $success = openssl_public_encrypt($data, $encrypted, $publicKeyResource, OPENSSL_PKCS1_OAEP_PADDING);

            if (!$success) {
                throw new Exception('Échec du chiffrement RSA (OpenSSL): ' . openssl_error_string());
            }

            Log::debug('Chiffrement RSA réussi avec OpenSSL (OAEP Padding)');
            return base64_encode($encrypted);

        } catch (Exception $e) {
            Log::error('Erreur lors du chiffrement RSA', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }




    /**
     * Génère une signature de message pour les requêtes (si activée)
     * Utilise AES pour chiffrer le payload et RSA pour chiffrer la clé AES
     * 
     * @param array $payload Payload à signer
     * @param bool $useHybrid Utiliser le mode hybride (AES/GCM/NoPadding)
     * @return array Signature et clé chiffrée
     */
    protected function generateSignature($payload, $key = null, $iv = null, $useHybrid = false)
    {
        try {
            $signatureEnabled = config('services.airtel.signature_enabled', false);
            
            if (!$signatureEnabled) {
                return [
                    'signature' => '',
                    'key' => '',
                    'use_hybrid' => false
                ];
            }

            // Générer une clé AES aléatoire (256 bits) et un IV (128 bits)
            if (!$key || !$iv) {
                $key = random_bytes(32); // 256 bits
                $iv = random_bytes(16);  // 128 bits
            }

            // Encoder en Base64
            $keyBase64 = base64_encode($key);
            $ivBase64 = base64_encode($iv);

            // Convertir le payload en JSON
            $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);

            // Chiffrer le payload avec AES
            if ($useHybrid) {
                // Mode hybride: AES/GCM/NoPadding
                $cipher = 'aes-256-gcm';
                $tag = '';
                $encryptedPayload = openssl_encrypt($payloadJson, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
                if ($encryptedPayload === false) {
                    throw new Exception('Erreur lors du chiffrement AES-GCM: ' . openssl_error_string());
                }
                // Pour GCM, le tag est ajouté à la fin
                $encryptedPayload = $encryptedPayload . $tag;
            } else {
                // Mode standard: AES/CBC/PKCS5Padding
                $cipher = 'aes-256-cbc';
                $encryptedPayload = openssl_encrypt($payloadJson, $cipher, $key, OPENSSL_RAW_DATA, $iv);
                if ($encryptedPayload === false) {
                    throw new Exception('Erreur lors du chiffrement AES-CBC: ' . openssl_error_string());
                }
            }

            $signature = base64_encode($encryptedPayload);

            // Concaténer key:iv
            $keyIv = $keyBase64 . ':' . $ivBase64;

            // Récupérer la clé publique RSA
            $encryptionKeys = $this->getEncryptionKeys();
            $rsaPublicKey = $encryptionKeys['key'] ?? null;

            if (!$rsaPublicKey) {
                throw new Exception('Clé publique RSA introuvable pour la signature');
            }

            // Chiffrer key:iv avec RSA
            $encryptedKeyIv = $this->encryptWithRSA($keyIv, $rsaPublicKey);

            Log::info('Signature de message générée', [
                'use_hybrid' => $useHybrid,
                'payload_length' => strlen($payloadJson)
            ]);

            return [
                'signature' => $signature,
                'key' => $encryptedKeyIv,
                'use_hybrid' => $useHybrid
            ];

        } catch (Exception $e) {
            Log::error('Erreur lors de la génération de la signature', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // En cas d'erreur, retourner des valeurs vides (signature désactivée)
            return [
                'signature' => '',
                'key' => '',
                'use_hybrid' => false
            ];
        }
    }

    /**
     * Nettoie et valide le numéro de téléphone (enlève le code pays)
     * Note: Ne pas envoyer de code pays dans le msisdn selon la documentation
     * 
     * @param string $phone Numéro de téléphone à nettoyer
     * @return string Numéro nettoyé sans code pays
     * @throws Exception Si le numéro est invalide
     */
    protected function cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            throw new Exception('Le numéro de téléphone est requis');
        }

        // Enlever tout sauf les chiffres
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Pour le Congo (242)
        $countryCode = '242';
        $countryCodeLength = strlen($countryCode);
        
        // Si le numéro commence par le code pays
        if (strpos($phone, $countryCode) === 0) {
            $phone = substr($phone, $countryCodeLength);
        }
        
        // Vérifier la longueur (sans code pays)
        // Pour le Congo, les numéros font généralement 9 chiffres (ex: 123456789)
        $expectedLength = 9;
        if (strlen($phone) !== $expectedLength) {
            throw new Exception("Numéro de téléphone invalide. Attendu: {$expectedLength} chiffres sans le code pays. Reçu: " . strlen($phone) . " chiffres");
        }
        
        // Vérifier que le numéro commence par un chiffre valide (généralement 0, 5, 6, 7, 8, 9 pour le Congo)
        $firstDigit = substr($phone, 0, 1);
        if (!in_array($firstDigit, ['0', '5', '6', '7', '8', '9'])) {
            Log::warning('Numéro de téléphone avec préfixe inhabituel', [
                'phone' => $phone,
                'first_digit' => $firstDigit
            ]);
        }
        
        return $phone;
    }

    /**
     * Effectue un paiement USSD Push
     * 
     * Note: L'URL de callback doit être configurée dans les paramètres de l'application Airtel.
     * Format: https://partner_domain/callback_path
     * Route configurée: /webhooks/airtel/callback
     * 
     * @param array $data Données du paiement
     * @return array Réponse de l'API
     */
    public function initiatePayment(array $data)
    {
        try {
            // Obtenir le token d'authentification
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new Exception('Impossible d\'obtenir le token d\'authentification');
            }

            // Validation du montant
            if (!isset($data['amount'])) {
                throw new Exception('Le montant est requis');
            }

            $amount = $data['amount'];
            
            // Vérifier que le montant est numérique
            if (!is_numeric($amount)) {
                throw new Exception('Le montant doit être un nombre');
            }

            // Vérifier que le montant est positif
            if ($amount <= 0) {
                throw new Exception('Le montant doit être supérieur à zéro');
            }

            // Vérifier le montant minimum (généralement 100 FCFA pour Airtel)
            if ($amount < $this->minAmount) {
                throw new Exception("Le montant minimum est de {$this->minAmount} {$this->currency}");
            }

            // Nettoyer le numéro de téléphone
            $msisdn = $this->cleanPhoneNumber($data['phone']);

            // Préparer le payload selon la documentation
            // La référence doit être alphanumérique uniquement (4-64 caractères)
            $reference = $data['reference'] ?? 'TXN' . strtoupper(Str::random(12));
            // S'assurer que la référence est alphanumérique uniquement
            $reference = preg_replace('/[^A-Za-z0-9]/', '', $reference);
            // Tronquer à 64 caractères max si nécessaire
            $reference = substr($reference, 0, 64);

            $payload = [
                'reference' => $reference,
                'subscriber' => [
                    'country' => $this->country,
                    'currency' => $this->currency,
                    'msisdn' => $msisdn,
                ],
                'transaction' => [
                    'amount' => $data['amount'],
                    'country' => $this->country,
                    'currency' => $this->currency,
                    'id' => $data['transaction_id'] ?? Str::uuid()->toString(),
                ],
            ];

            // Générer la signature si nécessaire
            $signatureData = $this->generateSignature($payload);
            
            // Préparer les headers
            $headers = [
                'Accept' => '*/* ',
                'Content-Type' => 'application/json',
                'X-Country' => $this->country,
                'X-Currency' => $this->currency,
                'Authorization' => 'Bearer ' . $accessToken,
            ];

            // Ajouter les headers de signature si activés
            if (!empty($signatureData['signature']) && !empty($signatureData['key'])) {
                $headers['x-signature'] = $signatureData['signature'];
                $headers['x-key'] = $signatureData['key'];
                
                // Ajouter le header v2-padding si mode hybride
                if ($signatureData['use_hybrid'] ?? false) {
                    $headers['v2-padding'] = 'true';
                }
            }

            Log::info('Initiation paiement Airtel Money', [
                'payload' => $payload,
                'msisdn' => $msisdn
            ]);

            // Faire la requête POST selon la documentation
            $response = $this->httpClient()->withHeaders($headers)
                ->post($this->baseUrl . '/merchant/v1/payments/', $payload);

            $responseData = $response->json();

            // Extraire le code d'erreur de la réponse
            $errorCode = $responseData['status']['response_code'] ?? $responseData['status']['result_code'] ?? null;
            $errorInfo = $errorCode ? $this->getErrorInfo($errorCode) : null;

            if (!$response->successful()) {
                Log::error('Erreur lors du paiement Airtel Money', [
                    'http_status' => $response->status(),
                    'error_code' => $errorCode,
                    'response' => $responseData,
                    'payload' => $payload
                ]);

                $message = $errorInfo['message'] ?? $responseData['status']['message'] ?? 'Erreur lors du paiement';
                
                return [
                    'success' => false,
                    'status' => $errorInfo['status'] ?? 'failed',
                    'message' => $message,
                    'response_code' => $errorCode,
                    'error_code' => $errorCode,
                    'transaction_id' => $payload['transaction']['id'] ?? null,
                    'retry' => $errorInfo['retry'] ?? false,
                ];
            }

            // Analyser la réponse BASÉE UNIQUEMENT SUR response_code
            // Ne JAMAIS utiliser transaction.status pour déterminer le résultat final
            if ($errorCode) {
                $errorInfo = $this->getErrorInfo($errorCode);
                
                // Vérifier si c'est un vrai succès (transaction complétée)
                if ($this->isRealSuccess($errorCode)) {
                    Log::info('Paiement Airtel Money réussi (SUCCESS)', [
                        'transaction_id' => $responseData['data']['transaction']['id'] ?? $payload['transaction']['id'],
                        'response_code' => $errorCode,
                        'reference' => $responseData['data']['transaction']['id'] ?? $payload['reference'],
                    ]);

                    return [
                        'success' => true,
                        'status' => self::STATUS_SUCCESS,
                        'message' => $errorInfo['message'],
                        'transaction_id' => $responseData['data']['transaction']['id'] ?? $payload['transaction']['id'],
                        'reference' => $responseData['data']['transaction']['id'] ?? $payload['reference'],
                        'response_code' => $errorCode,
                        'raw_response' => $responseData,
                    ];
                }

                // Vérifier si c'est un statut pending (nécessite confirmation utilisateur)
                if ($this->isPendingStatus($errorCode)) {
                    $transactionId = $responseData['data']['transaction']['id'] ?? $payload['transaction']['id'];
                    $reference = $responseData['data']['transaction']['id'] ?? $payload['reference'];
                    
                    Log::info('Paiement Airtel Money en attente de confirmation utilisateur (PENDING)', [
                        'transaction_id' => $transactionId,
                        'response_code' => $errorCode,
                        'reference' => $reference,
                        'message' => 'Transaction initiée avec succès, en attente de confirmation sur le téléphone',
                    ]);

                    return [
                        'success' => true, // IMPORTANT: success = true pour permettre le workflow
                        'status' => self::STATUS_PENDING,
                        'message' => $errorInfo['message'],
                        'transaction_id' => $transactionId,
                        'reference' => $reference,
                        'response_code' => $errorCode,
                        'requires_user_action' => true,
                        'raw_response' => $responseData,
                    ];
                }

                // Vérifier si c'est un statut ambiguous (nécessite polling)
                if ($this->isAmbiguousStatus($errorCode)) {
                    $transactionId = $responseData['data']['transaction']['id'] ?? $payload['transaction']['id'];
                    $reference = $responseData['data']['transaction']['id'] ?? $payload['reference'];
                    
                    Log::warning('Paiement Airtel Money ambigu (AMBIGUOUS)', [
                        'transaction_id' => $transactionId,
                        'response_code' => $errorCode,
                        'reference' => $reference,
                        'message' => 'Transaction en statut ambigu, polling obligatoire pour déterminer le statut final',
                    ]);

                    return [
                        'success' => true, // IMPORTANT: success = true pour permettre le workflow
                        'status' => self::STATUS_AMBIGUOUS,
                        'message' => $errorInfo['message'],
                        'transaction_id' => $transactionId,
                        'reference' => $reference,
                        'response_code' => $errorCode,
                        'requires_polling' => true,
                        'raw_response' => $responseData,
                    ];
                }

                // Autres erreurs (vraies erreurs)
                Log::warning('Paiement Airtel Money échoué', [
                    'transaction_id' => $payload['transaction']['id'],
                    'response_code' => $errorCode,
                    'error_info' => $errorInfo,
                    'status' => $errorInfo['status'],
                ]);

                return [
                    'success' => false,
                    'status' => $errorInfo['status'],
                    'message' => $errorInfo['message'],
                    'transaction_id' => $responseData['data']['transaction']['id'] ?? $payload['transaction']['id'],
                    'reference' => $responseData['data']['transaction']['id'] ?? $payload['reference'],
                    'response_code' => $errorCode,
                    'error_code' => $errorCode,
                    'retry' => $errorInfo['retry'],
                    'raw_response' => $responseData,
                ];
            }

            // Si pas de code d'erreur, c'est une erreur de configuration API
            Log::error('Réponse paiement Airtel Money sans response_code', [
                'response' => $responseData,
                'transaction_id' => $payload['transaction']['id'],
            ]);

            return [
                'success' => false,
                'status' => self::STATUS_ERROR,
                'message' => 'Réponse API invalide: aucun code de réponse fourni',
                'transaction_id' => $responseData['data']['transaction']['id'] ?? $payload['transaction']['id'],
                'reference' => $responseData['data']['transaction']['id'] ?? $payload['reference'],
                'response_code' => null,
                'raw_response' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Exception lors du paiement Airtel Money', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur technique: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifie le statut d'une transaction de paiement (collection)
     * 
     * Statuts Airtel possibles :
     * - TS: Transaction Success
     * - TF: Transaction Failed  
     * - TA: Transaction Ambiguous (nécessite polling)
     * - TIP: Transaction in Progress (attente utilisateur)
     * - TE: Transaction Expired
     * 
     * @param string $transactionId ID de la transaction
     * @return array Statut de la transaction
     */
    public function checkTransactionStatus($transactionId)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new Exception('Impossible d\'obtenir le token d\'authentification');
            }

            $headers = [
                'Accept' => '*/*',
                'X-Country' => $this->country,
                'X-Currency' => $this->currency,
                'Authorization' => 'Bearer ' . $accessToken,
            ];

            Log::info('Vérification statut paiement Airtel Money', [
                'transaction_id' => $transactionId
            ]);

            $response = $this->httpClient()->withHeaders($headers)
                ->get($this->baseUrl . '/standard/v1/payments/' . $transactionId);

            // Gestion des erreurs HTTP
            if (!$response->successful()) {
                return $this->handleHttpError($response, $transactionId);
            }

            $responseData = $response->json();
            
            // Validation de la réponse
            if (!isset($responseData['data']['transaction'])) {
                Log::error('Réponse API Airtel invalide - transaction manquante', [
                    'transaction_id' => $transactionId,
                    'response' => $responseData
                ]);
                
                return [
                    'success' => false,
                    'status' => self::STATUS_ERROR,
                    'message' => 'Réponse API invalide: données transaction manquantes',
                    'transaction_id' => $transactionId,
                    'raw_response' => $responseData,
                ];
            }

            $transactionData = $responseData['data']['transaction'];
            $airtelStatus = $transactionData['status'] ?? null;
            $errorCode = $responseData['status']['response_code'] ?? $responseData['status']['result_code'] ?? null;
            
            // Si pas de statut Airtel, utiliser fallback sur response_code
            if (!$airtelStatus && $errorCode) {
                return $this->handleStatusByResponseCode($errorCode, $transactionData, $transactionId, $responseData);
            }
            
            // Traitement par statut Airtel
            return $this->handleAirtelStatus($airtelStatus, $transactionData, $transactionId, $errorCode, $responseData);

        } catch (Exception $e) {
            Log::error('Exception lors de la vérification du paiement Airtel Money', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur technique: ' . $e->getMessage(),
                'transaction_id' => $transactionId,
            ];
        }
    }

    /**
     * Gère les erreurs HTTP de l'API
     */
    private function handleHttpError($response, $transactionId)
    {
        $responseData = $response->json();
        $errorCode = $responseData['status']['response_code'] ?? $responseData['status']['result_code'] ?? null;
        $errorInfo = $errorCode ? $this->getErrorInfo($errorCode) : null;

        Log::error('Erreur HTTP lors de la vérification du paiement Airtel Money', [
            'http_status' => $response->status(),
            'transaction_id' => $transactionId,
            'error_code' => $errorCode,
            'response' => $responseData
        ]);

        return [
            'success' => false,
            'status' => $errorInfo['status'] ?? self::STATUS_ERROR,
            'transaction_status' => null,
            'message' => $errorInfo['message'] ?? ($responseData['status']['message'] ?? 'Erreur lors de la vérification'),
            'transaction_id' => $transactionId,
            'error_code' => $errorCode,
            'retry' => $errorInfo['retry'] ?? false,
            'http_status' => $response->status(),
        ];
    }

    /**
     * Gère le statut basé sur le code de réponse
     */
    private function handleStatusByResponseCode($errorCode, $transactionData, $transactionId, $responseData)
    {
        $errorInfo = $this->getErrorInfo($errorCode);
        
        // Déterminer le statut interne basé sur le code d'erreur
        if ($this->isRealSuccess($errorCode)) {
            $status = self::STATUS_SUCCESS;
            $success = true;
        } elseif ($this->isPendingStatus($errorCode)) {
            $status = self::STATUS_PENDING;
            $success = true; // IMPORTANT: success = true pour permettre le workflow
        } elseif ($this->isAmbiguousStatus($errorCode)) {
            $status = self::STATUS_AMBIGUOUS;
            $success = true; // IMPORTANT: success = true pour permettre le polling
        } else {
            $status = $errorInfo['status'] ?? self::STATUS_FAILED;
            $success = false;
        }

        Log::info('Statut paiement déterminé par response_code', [
            'transaction_id' => $transactionId,
            'response_code' => $errorCode,
            'status' => $status,
            'success' => $success
        ]);

        return [
            'success' => $success,
            'status' => $status,
            'transaction_status' => $this->mapResponseCodeToAirtelStatus($errorCode),
            'message' => $transactionData['message'] ?? $errorInfo['message'],
            'transaction_id' => $transactionData['id'] ?? $transactionId,
            'airtel_money_id' => $transactionData['airtel_money_id'] ?? null,
            'response_code' => $errorCode,
            'requires_polling' => $status === self::STATUS_AMBIGUOUS,
            'requires_user_action' => $status === self::STATUS_PENDING,
            'retry' => $errorInfo['retry'] ?? false,
            'raw_response' => $responseData,
        ];
    }

    /**
     * Gère le statut basé sur le code Airtel (TS, TF, TA, TIP, TE)
     */
    private function handleAirtelStatus($airtelStatus, $transactionData, $transactionId, $errorCode, $responseData)
    {
        $statusMap = [
            self::AIRTEL_STATUS_TS => [
                'success' => true,
                'status' => self::STATUS_SUCCESS,
                'log_level' => 'info',
                'log_message' => 'Paiement Airtel Money vérifié - SUCCESS (TS)',
            ],
            self::AIRTEL_STATUS_TF => [
                'success' => false,
                'status' => self::STATUS_FAILED,
                'log_level' => 'warning',
                'log_message' => 'Paiement Airtel Money vérifié - ÉCHEC (TF)',
            ],
            self::AIRTEL_STATUS_TA => [
                'success' => true, // IMPORTANT: pour permettre le polling
                'status' => self::STATUS_AMBIGUOUS,
                'log_level' => 'warning',
                'log_message' => 'Paiement Airtel Money vérifié - AMBIGUOUS (TA)',
                'requires_polling' => true,
            ],
            self::AIRTEL_STATUS_TIP => [
                'success' => true, // IMPORTANT: pour permettre l'attente utilisateur
                'status' => self::STATUS_PENDING,
                'log_level' => 'info',
                'log_message' => 'Paiement Airtel Money vérifié - PENDING (TIP)',
                'requires_user_action' => true,
            ],
            self::AIRTEL_STATUS_TE => [
                'success' => false,
                'status' => self::STATUS_EXPIRED,
                'log_level' => 'warning',
                'log_message' => 'Paiement Airtel Money vérifié - EXPIRED (TE)',
            ],
        ];

        if (!isset($statusMap[$airtelStatus])) {
            Log::warning('Statut Airtel inconnu', [
                'transaction_id' => $transactionId,
                'airtel_status' => $airtelStatus,
                'transaction_data' => $transactionData
            ]);
            
            return [
                'success' => false,
                'status' => self::STATUS_ERROR,
                'transaction_status' => $airtelStatus,
                'message' => 'Statut transaction inconnu: ' . $airtelStatus,
                'transaction_id' => $transactionData['id'] ?? $transactionId,
                'airtel_money_id' => $transactionData['airtel_money_id'] ?? null,
                'response_code' => $errorCode,
                'raw_response' => $responseData,
            ];
        }

        $config = $statusMap[$airtelStatus];
        
        // Récupérer le message d'Airtel
        $airtelMessage = $transactionData['message'] ?? $this->getDefaultMessage($airtelStatus);
        
        // Log avec le niveau approprié et le message
        Log::{$config['log_level']}($config['log_message'], [
            'transaction_id' => $transactionData['id'] ?? $transactionId,
            'airtel_money_id' => $transactionData['airtel_money_id'] ?? null,
            'transaction_status' => $airtelStatus,
            'message' => $airtelMessage, // Ajouter le message dans les logs
            'response_code' => $errorCode,
        ]);

        return array_merge([
            'transaction_status' => $airtelStatus,
            'message' => $airtelMessage,
            'transaction_id' => $transactionData['id'] ?? $transactionId,
            'airtel_money_id' => $transactionData['airtel_money_id'] ?? null,
            'response_code' => $errorCode,
            'raw_response' => $responseData,
        ], $config);
    }

    /**
     * Message par défaut selon le statut
     */
    private function getDefaultMessage($airtelStatus)
    {
        $messages = [
            self::AIRTEL_STATUS_TS => 'Transaction réussie',
            self::AIRTEL_STATUS_TF => 'Transaction échouée',
            self::AIRTEL_STATUS_TA => 'Transaction en statut ambigu',
            self::AIRTEL_STATUS_TIP => 'Transaction en cours de traitement',
            self::AIRTEL_STATUS_TE => 'Transaction expirée',
        ];
        
        return $messages[$airtelStatus] ?? 'Statut transaction inconnu';
    }

    /**
     * Mappe un code de réponse vers un statut Airtel
     */
    private function mapResponseCodeToAirtelStatus($responseCode)
    {
        // Implémentez la logique de mapping spécifique
        if ($this->isRealSuccess($responseCode)) {
            return self::AIRTEL_STATUS_TS;
        } elseif ($this->isPendingStatus($responseCode)) {
            return self::AIRTEL_STATUS_TIP;
        } elseif ($this->isAmbiguousStatus($responseCode)) {
            return self::AIRTEL_STATUS_TA;
        }
        
        return self::AIRTEL_STATUS_TF; // Par défaut, considérer comme échec
    }

    /**
     * Chiffre le PIN à 4 chiffres pour Airtel Money
     * Utilise RSA avec mode ECB, padding OAEPWithSHA-256AndMGF1Padding et KeyLength 2048
     * Selon la documentation Airtel
     * 
     * @param string $pin PIN à 4 chiffres
     * @return string PIN chiffré en Base64
     */
    public function encryptPin(string $pin): string
    {
        try {
            // Vérifier que le PIN est valide (4 chiffres)
            if (!preg_match('/^\d{4}$/', $pin)) {
                throw new Exception('Le PIN doit contenir exactement 4 chiffres');
            }

            $rsaPublicKey = null;
            $keyId = null;

            // Essayer de récupérer la clé depuis l'API Airtel
            try {
                $encryptionKeys = $this->getEncryptionKeys();
                $rsaPublicKey = $encryptionKeys['key'] ?? null;
                $keyId = $encryptionKeys['key_id'] ?? null;
            } catch (Exception $apiException) {
                Log::warning('Impossible de récupérer les clés RSA depuis l\'API Airtel, utilisation du fallback', [
                    'error' => $apiException->getMessage()
                ]);
            }

            // Fallback: utiliser la clé configurée si l'API a échoué
            if (!$rsaPublicKey) {
                Log::info('Utilisation de la clé publique RSA configurée (fallback)');
                $rsaPublicKey = config('services.airtel.rsa_public_key');
                
                if (!$rsaPublicKey) {
                    $errorMessage = 'Clé publique RSA introuvable pour le chiffrement du PIN Airtel Money. ' . 
                                   'Veuillez soit : ' .
                                   '1) Configurer AIRTEL_RSA_PUBLIC_KEY dans votre fichier .env avec la clé publique RSA fournie par Airtel, ' .
                                   '2) Vérifier que l\'API Airtel est accessible pour récupérer automatiquement la clé. ' .
                                   'Contactez le support Airtel pour obtenir votre clé publique RSA.';
                    
                    Log::error('Clé RSA manquante pour le chiffrement du PIN', [
                        'api_error' => 'L\'API Airtel n\'a pas pu fournir la clé',
                        'config_key' => 'AIRTEL_RSA_PUBLIC_KEY non configurée'
                    ]);
                    
                    throw new Exception($errorMessage);
                }
            }

            // Chiffrer le PIN avec RSA
            $encrypted = $this->encryptWithRSA($pin, $rsaPublicKey);

            Log::info('PIN chiffré avec RSA', [
                'key_id' => $keyId,
                'source' => $keyId ? 'api' : 'config'
            ]);

            return $encrypted;

        } catch (Exception $e) {
            Log::error('Erreur lors du chiffrement du PIN Airtel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Effectue un disbursement (retrait) vers un portefeuille Airtel Money
     * 
     * @param array $data Données du retrait
     * @return array Réponse de l'API
     */
    public function disburse(array $data)
    {
        try {
            // Obtenir le token d'authentification
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new Exception('Impossible d\'obtenir le token d\'authentification');
            }

            // Nettoyer le numéro de téléphone
            $msisdn = $this->cleanPhoneNumber($data['phone']);

            // Chiffrer le PIN
            $encryptedPin = $this->encryptPin($data['pin']);

            Log::debug('PIN chiffré généré', [
                'encrypted_length' => strlen($encryptedPin),
                'first_10_chars' => substr($encryptedPin, 0, 10) . '...'
            ]);


            // Nettoyer les références pour qu'elles soient strictement alphanumériques (erreur DP00900001013)
            $reference = preg_replace('/[^a-zA-Z0-9]/', '', $data['reference'] ?? Str::random(16));
            $transactionId = preg_replace('/[^a-zA-Z0-9]/', '', $data['transaction_id'] ?? Str::uuid()->toString());

            // Limiter à 64 caractères selon la doc
            $reference = substr($reference, 0, 64);
            $transactionId = substr($transactionId, 0, 64);

            // Préparer le payload selon la documentation Disbursement-APIs v3.0
            $payload = [
                'payee' => [
                    'msisdn' => $msisdn,
                    'wallet_type' => $data['wallet_type'] ?? 'MOBILE_MONEY',
                ],
                'reference' => $reference,
                'pin' => $encryptedPin,
                'transaction' => [
                    'amount' => $data['amount'],
                    'id' => $transactionId,
                    'type' => $data['transaction_type'] ?? 'B2B',
                ],
            ];

            // Générer la signature si nécessaire
            $signatureData = $this->generateSignature($payload);
            
            // Préparer les headers selon la documentation Disbursement-APIs v3.0
            $headers = [
                'Accept' => '*/*',
                'Content-Type' => 'application/json',
                'X-Country' => $this->country,
                'X-Currency' => $this->currency,
                'Authorization' => 'Bearer ' . $accessToken,
            ];

            // Ajouter les headers de signature si activés
            if (!empty($signatureData['signature']) && !empty($signatureData['key'])) {
                $headers['x-signature'] = $signatureData['signature'];
                $headers['x-key'] = $signatureData['key'];
                
                // Ajouter le header v2-padding si mode hybride
                if ($signatureData['use_hybrid'] ?? false) {
                    $headers['v2-padding'] = 'true';
                }
            }

            // Ne jamais logger le PIN, même chiffré
            $logPayload = $payload;
            unset($logPayload['pin']); // Retirer complètement le PIN des logs
            
            Log::info('Initiation disbursement Airtel Money', [
                'payload' => $logPayload,
                'msisdn' => $msisdn,
                'pin_encrypted' => true // Indiquer que le PIN est chiffré mais ne pas le logger
            ]);

            // Faire la requête POST
            $response = $this->httpClient()->withHeaders($headers)
                ->post($this->baseUrl . '/standard/v3/disbursements', $payload);

            $responseData = $response->json();

            // Extraire le code d'erreur de la réponse
            $errorCode = $responseData['status']['response_code'] ?? $responseData['status']['result_code'] ?? null;
            $errorInfo = $errorCode ? $this->getErrorInfo($errorCode) : null;

            if (!$response->successful()) {
                // Ne jamais logger le PIN, même en cas d'erreur
                $logPayload = $payload;
                unset($logPayload['pin']);
                
                Log::error('Erreur lors du disbursement Airtel Money', [
                    'http_status' => $response->status(),
                    'error_code' => $errorCode,
                    'response' => $responseData,
                    'payload' => $logPayload
                ]);

                $message = $errorInfo['message'] ?? $responseData['status']['message'] ?? 'Erreur lors du retrait';
                
                return [
                    'success' => false,
                    'status' => $errorInfo['status'] ?? 'failed',
                    'message' => $message,
                    'response_code' => $errorCode,
                    'error_code' => $errorCode,
                    'transaction_id' => $payload['transaction']['id'] ?? null,
                    'retry' => $errorInfo['retry'] ?? false,
                ];
            }

            // Analyser la réponse
            if ($errorCode) {
                $errorInfo = $this->getErrorInfo($errorCode);
                
                // Si c'est un succès (accepter les deux codes de succès selon la documentation)
                if (in_array($errorCode, ['DP00800001001', 'DP00900001001'])) {
                    $transactionData = $responseData['data']['transaction'] ?? [];
                    
                    Log::info('Disbursement Airtel Money réussi', [
                        'transaction_id' => $transactionData['id'] ?? $payload['transaction']['id'],
                        'airtel_money_id' => $transactionData['airtel_money_id'] ?? null,
                        'reference_id' => $transactionData['reference_id'] ?? null,
                        'error_code' => $errorCode,
                    ]);

                    return [
                        'success' => true,
                        'status' => 'success',
                        'message' => $errorInfo['message'],
                        'transaction_id' => $transactionData['id'] ?? $payload['transaction']['id'],
                        'airtel_money_id' => $transactionData['airtel_money_id'] ?? null,
                        'reference_id' => $transactionData['reference_id'] ?? null,
                        'response_code' => $errorCode,
                        'raw_response' => $responseData,
                    ];
                }

                // Autres statuts
                Log::warning('Disbursement Airtel Money avec statut intermédiaire', [
                    'transaction_id' => $payload['transaction']['id'],
                    'error_code' => $errorCode,
                    'error_info' => $errorInfo,
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'status' => $errorInfo['status'],
                    'message' => $errorInfo['message'],
                    'transaction_id' => $responseData['data']['transaction']['id'] ?? $payload['transaction']['id'],
                    'response_code' => $errorCode,
                    'error_code' => $errorCode,
                    'retry' => $errorInfo['retry'],
                    'raw_response' => $responseData,
                ];
            }

            // Si pas de code d'erreur, utiliser le statut de la transaction
            $transactionStatus = $responseData['data']['transaction']['status'] ?? 'pending';
            $isSuccess = $responseData['status']['success'] ?? false;

            Log::info('Réponse disbursement Airtel Money', [
                'status' => $transactionStatus,
                'success' => $isSuccess,
                'response_code' => $errorCode,
                'response' => $responseData
            ]);

            return [
                'success' => $isSuccess,
                'status' => $transactionStatus,
                'message' => $responseData['status']['message'] ?? 'Retrait initié',
                'transaction_id' => $responseData['data']['transaction']['id'] ?? $payload['transaction']['id'],
                'airtel_money_id' => $responseData['data']['transaction']['airtel_money_id'] ?? null,
                'reference_id' => $responseData['data']['transaction']['reference_id'] ?? null,
                'response_code' => $errorCode,
                'raw_response' => $responseData,
            ];

        } catch (Exception $e) {
            // Ne jamais logger le PIN, même en cas d'exception
            $logData = $data;
            unset($logData['pin']);
            
            Log::error('Exception lors du disbursement Airtel Money', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $logData
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur technique: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Teste le chiffrement RSA indépendamment
     * 
     * @param string $pin PIN à tester
     * @return array Résultats du test
     */
    public function testEncryption($pin = '1234')
    {
        try {
            // Récupérer la clé
            $keys = $this->getEncryptionKeys();
            $publicKey = $keys['key'];
            
            // Tester le chiffrement
            $encrypted = $this->encryptWithRSA($pin, $publicKey);
            
            return [
                'success' => true,
                'key_id' => $keys['key_id'] ?? null,
                'encrypted_pin' => $encrypted,
                'encrypted_length' => strlen($encrypted),
                'key_expiry' => $keys['valid_upto'] ?? null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Vérifie le statut d'une transaction de disbursement (retrait)
     * 
     * @param string $transactionId ID de la transaction partenaire
     * @return array Statut de la transaction
     */
    public function checkDisbursementStatus($transactionId)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new Exception('Impossible d\'obtenir le token d\'authentification');
            }

            $headers = [
                'Accept' => '*/* ',
                'X-Country' => $this->country,
                'X-Currency' => $this->currency,
                'Authorization' => 'Bearer ' . $accessToken,
            ];

            Log::info('Vérification statut disbursement Airtel Money', [
                'transaction_id' => $transactionId
            ]);

            $response = $this->httpClient()->withHeaders($headers)
                ->get($this->baseUrl . '/standard/v3/disbursements/' . $transactionId);

            $responseData = $response->json();

            if (!$response->successful()) {
                $errorCode = $responseData['status']['response_code'] ?? $responseData['status']['result_code'] ?? null;
                $errorInfo = $errorCode ? $this->getErrorInfo($errorCode) : null;

                Log::error('Erreur lors de la vérification du disbursement Airtel Money', [
                    'http_status' => $response->status(),
                    'transaction_id' => $transactionId,
                    'error_code' => $errorCode,
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'status' => $errorInfo['status'] ?? 'unknown',
                    'message' => $errorInfo['message'] ?? $responseData['status']['message'] ?? 'Erreur lors de la vérification',
                    'error_code' => $errorCode,
                    'retry' => $errorInfo['retry'] ?? false,
                ];
            }

            // Analyser le code d'erreur dans la réponse
            $errorCode = $responseData['status']['response_code'] ?? $responseData['status']['result_code'] ?? null;
            
            if ($errorCode) {
                $errorInfo = $this->getErrorInfo($errorCode);
                
                // Si c'est un succès (accepter les deux codes de succès selon la documentation)
                if (in_array($errorCode, ['DP00800001001', 'DP00900001001'])) {
                    $transactionData = $responseData['data']['transaction'] ?? [];
                    
                    Log::info('Disbursement Airtel Money vérifié - Succès', [
                        'transaction_id' => $transactionData['id'] ?? $transactionId,
                        'airtel_money_id' => $transactionData['airtel_money_id'] ?? null,
                        'status' => $transactionData['status'] ?? null,
                        'error_code' => $errorCode,
                    ]);

                    return [
                        'success' => true,
                        'status' => $transactionData['status'] ?? 'success',
                        'message' => $transactionData['message'] ?? $errorInfo['message'],
                        'transaction_id' => $transactionData['id'] ?? $transactionId,
                        'airtel_money_id' => $transactionData['airtel_money_id'] ?? null,
                        'reference_id' => $transactionData['reference_id'] ?? null,
                        'response_code' => $errorCode,
                        'raw_response' => $responseData,
                    ];
                }

                // Autres statuts
                $transactionData = $responseData['data']['transaction'] ?? [];
                
                Log::info('Disbursement Airtel Money vérifié - Statut intermédiaire', [
                    'transaction_id' => $transactionData['id'] ?? $transactionId,
                    'status' => $transactionData['status'] ?? null,
                    'error_code' => $errorCode,
                    'error_info' => $errorInfo,
                ]);

                return [
                    'success' => false,
                    'status' => $transactionData['status'] ?? $errorInfo['status'],
                    'message' => $transactionData['message'] ?? $errorInfo['message'],
                    'transaction_id' => $transactionData['id'] ?? $transactionId,
                    'airtel_money_id' => $transactionData['airtel_money_id'] ?? null,
                    'reference_id' => $transactionData['reference_id'] ?? null,
                    'response_code' => $errorCode,
                    'error_code' => $errorCode,
                    'retry' => $errorInfo['retry'],
                    'raw_response' => $responseData,
                ];
            }

            // Si pas de code d'erreur, utiliser le statut de la transaction
            $transactionData = $responseData['data']['transaction'] ?? [];
            $transactionStatus = $transactionData['status'] ?? 'unknown';
            $isSuccess = $responseData['status']['success'] ?? false;

            Log::info('Statut disbursement Airtel Money vérifié', [
                'transaction_id' => $transactionData['id'] ?? $transactionId,
                'status' => $transactionStatus,
                'success' => $isSuccess,
                'response_code' => $errorCode,
            ]);

            return [
                'success' => $isSuccess,
                'status' => $transactionStatus,
                'message' => $transactionData['message'] ?? $responseData['status']['message'] ?? 'Transaction vérifiée',
                'transaction_id' => $transactionData['id'] ?? $transactionId,
                'airtel_money_id' => $transactionData['airtel_money_id'] ?? null,
                'reference_id' => $transactionData['reference_id'] ?? null,
                'response_code' => $errorCode,
                'raw_response' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Exception lors de la vérification du disbursement Airtel Money', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur technique: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Effectue un remboursement (refund) d'une transaction Airtel Money
     *
     * @param array $data Données du remboursement
     * @return array Réponse de l'API
     */
    public function refund(array $data)
    {
        try {
            // Vérifier que l'airtel_money_id est fourni
            if (!isset($data['airtel_money_id']) || empty($data['airtel_money_id'])) {
                throw new Exception('airtel_money_id est requis pour le remboursement');
            }

            // Obtenir le token d'authentification
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                throw new Exception('Impossible d\'obtenir le token d\'authentification');
            }

            // Préparer les headers selon la documentation
            $headers = [
                'Accept' => '*/* ',
                'Content-Type' => 'application/json',
                'X-Country' => $this->country,
                'X-Currency' => $this->currency,
                'Authorization' => 'Bearer ' . $accessToken,
            ];

            // Préparer le payload selon la documentation
            $payload = [
                'transaction' => [
                    'airtel_money_id' => $data['airtel_money_id']
                ]
            ];

            Log::info('Initiation remboursement Airtel Money', [
                'airtel_money_id' => $data['airtel_money_id']
            ]);

            // Faire la requête POST
            $response = $this->httpClient()->withHeaders($headers)
                ->post($this->baseUrl . '/standard/v1/payments/refund', $payload);

            $responseData = $response->json();

            // Extraire le code d'erreur de la réponse
            $errorCode = $responseData['status']['response_code'] ?? $responseData['status']['result_code'] ?? null;
            $errorInfo = $errorCode ? $this->getErrorInfo($errorCode) : null;

            if (!$response->successful()) {
                Log::error('Erreur lors du remboursement Airtel Money', [
                    'http_status' => $response->status(),
                    'airtel_money_id' => $data['airtel_money_id'],
                    'error_code' => $errorCode,
                    'response' => $responseData
                ]);

                $message = $errorInfo['message'] ?? $responseData['status']['message'] ?? 'Erreur lors du remboursement';

                return [
                    'success' => false,
                    'status' => $errorInfo['status'] ?? 'failed',
                    'message' => $message,
                    'error_code' => $errorCode,
                    'retry' => $errorInfo['retry'] ?? false,
                ];
            }

            // Analyser la réponse
            if ($errorCode) {
                $errorInfo = $this->getErrorInfo($errorCode);

                // Si c'est un succès
                if (in_array($errorCode, ['DP00800001001', 'ESB000010'])) {
                    Log::info('Remboursement Airtel Money réussi', [
                        'airtel_money_id' => $data['airtel_money_id'],
                        'error_code' => $errorCode,
                        'response' => $responseData
                    ]);

                    return [
                        'success' => true,
                        'status' => 'success',
                        'message' => $errorInfo['message'] ?? 'Remboursement effectué avec succès',
                        'airtel_money_id' => $responseData['data']['transaction']['airtel_money_id'] ?? $data['airtel_money_id'],
                        'response_code' => $errorCode,
                        'raw_response' => $responseData,
                    ];
                }

                // Autres statuts
                Log::warning('Remboursement Airtel Money avec statut intermédiaire', [
                    'airtel_money_id' => $data['airtel_money_id'],
                    'error_code' => $errorCode,
                    'error_info' => $errorInfo,
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'status' => $errorInfo['status'],
                    'message' => $errorInfo['message'],
                    'airtel_money_id' => $data['airtel_money_id'],
                    'error_code' => $errorCode,
                    'retry' => $errorInfo['retry'],
                    'raw_response' => $responseData,
                ];
            }

            // Si pas de code d'erreur, utiliser le statut de la transaction
            $transactionStatus = $responseData['data']['transaction']['status'] ?? 'unknown';
            $isSuccess = $responseData['status']['success'] ?? false;

            Log::info('Réponse remboursement Airtel Money', [
                'airtel_money_id' => $data['airtel_money_id'],
                'status' => $transactionStatus,
                'success' => $isSuccess,
                'response' => $responseData
            ]);

            return [
                'success' => $isSuccess,
                'status' => $transactionStatus,
                'message' => $responseData['status']['message'] ?? 'Remboursement traité',
                'airtel_money_id' => $responseData['data']['transaction']['airtel_money_id'] ?? $data['airtel_money_id'],
                'response_code' => $errorCode,
                'raw_response' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Exception lors du remboursement Airtel Money', [
                'airtel_money_id' => $data['airtel_money_id'] ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur technique: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifie les informations d'un utilisateur Airtel Money
     *
     * @param string $phoneNumber Numéro de téléphone de l'utilisateur
     * @return array Informations de l'utilisateur
     */
    public function getUserInfo($phoneNumber)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new Exception('Impossible d\'obtenir le token d\'authentification');
            }

            // Nettoyer le numéro de téléphone (enlève le code pays)
            $msisdn = $this->cleanPhoneNumber($phoneNumber);

            $headers = [
                'Accept' => '*/* ',
                'X-Country' => $this->country,
                'X-Currency' => $this->currency,
                'Authorization' => 'Bearer ' . $accessToken,
            ];

            Log::info('Vérification informations utilisateur Airtel Money', [
                'msisdn' => $msisdn,
                'original_phone' => $phoneNumber
            ]);

            $response = $this->httpClient()->withHeaders($headers)
                ->get($this->baseUrl . '/standard/v2/users/' . $msisdn);

            $responseData = $response->json();

            if (!$response->successful()) {
                $errorCode = $responseData['status']['response_code'] ?? $responseData['status']['result_code'] ?? null;
                $errorInfo = $errorCode ? $this->getErrorInfo($errorCode) : null;

                Log::error('Erreur lors de la vérification des informations utilisateur Airtel Money', [
                    'http_status' => $response->status(),
                    'msisdn' => $msisdn,
                    'error_code' => $errorCode,
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'status' => $errorInfo['status'] ?? 'unknown',
                    'message' => $errorInfo['message'] ?? $responseData['status']['message'] ?? 'Erreur lors de la vérification',
                    'error_code' => $errorCode,
                ];
            }

            // Analyser le code d'erreur dans la réponse
            $errorCode = $responseData['status']['response_code'] ?? $responseData['status']['result_code'] ?? null;
            $isSuccess = $responseData['status']['success'] ?? false;

            if ($isSuccess && isset($responseData['data'])) {
                $userData = $responseData['data'];
                
                Log::info('Informations utilisateur Airtel Money récupérées', [
                    'msisdn' => $userData['msisdn'] ?? $msisdn,
                    'account_status' => $userData['account_status'] ?? null,
                    'is_pin_set' => $userData['is_pin_set'] ?? null,
                    'is_barred' => $userData['is_barred'] ?? null,
                ]);

                return [
                    'success' => true,
                    'user' => [
                        'msisdn' => $userData['msisdn'] ?? $msisdn,
                        'first_name' => $userData['first_name'] ?? null,
                        'last_name' => $userData['last_name'] ?? null,
                        'full_name' => trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? '')),
                        'grade' => $userData['grade'] ?? null,
                        'is_barred' => $userData['is_barred'] ?? false,
                        'is_pin_set' => $userData['is_pin_set'] ?? false,
                        'dob' => $userData['dob'] ?? null,
                        'account_status' => $userData['account_status'] ?? null,
                        'nationality' => $userData['nationatility'] ?? null, // Note: typo dans l'API Airtel
                        'id_number' => $userData['id_number'] ?? null,
                        'registration' => $userData['registration'] ?? null,
                    ],
                    'response_code' => $errorCode,
                    'raw_response' => $responseData,
                ];
            }

            // Si pas de succès
            Log::warning('Échec de la récupération des informations utilisateur Airtel Money', [
                'msisdn' => $msisdn,
                'error_code' => $errorCode,
                'response' => $responseData
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => $responseData['status']['message'] ?? 'Impossible de récupérer les informations utilisateur',
                'error_code' => $errorCode,
                'raw_response' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Exception lors de la vérification des informations utilisateur Airtel Money', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur technique: ' . $e->getMessage(),
            ];
        }
    }
}

