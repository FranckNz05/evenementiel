<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WirepickService
{
    protected string $apiUrl;
    protected ?string $clientId;
    protected ?string $password;
    protected ?string $from;

    public function __construct()
    {
        $this->apiUrl = env('WIREPICK_API_URL', 'https://apisms.wirepick.com/httpsms/send');
        $this->clientId = env('WIREPICK_CLIENT_ID');
        $this->password = env('WIREPICK_PASSWORD');
        $this->from = env('WIREPICK_FROM', 'TST');
    }

    /**
     * Envoi d'un SMS groupé via l'API Wirepick.
     *
     * @param  array<int, string>|string  $phones
     * @param  string  $message
     * @param  string|null  $clientId
     * @param  string|null  $clientPassword
     * @param  string|null  $from
     * @return array<int, array<string, string|null>>
     *
     * @throws Exception
     */
    public function sendBulkSms($phones, string $message, ?string $clientId = null, ?string $clientPassword = null, ?string $from = null): array
    {
        $clientId ??= $this->clientId;
        $clientPassword ??= $this->password;
        $from ??= $this->from;

        if (empty($clientId) || empty($clientPassword)) {
            throw new Exception("Identifiants Wirepick manquants. Renseignez 'WIREPICK_CLIENT_ID' et 'WIREPICK_PASSWORD'.");
        }

        $sanitizedMessage = $this->sanitizeSmsText($message);
        $phoneString = is_array($phones) ? implode(';', $phones) : $phones;

        $url = $this->apiUrl . '?' . http_build_query([
            'client' => $clientId,
            'password' => $clientPassword,
            'phone' => $phoneString,
            'text' => $sanitizedMessage,
            'from' => $from,
        ]);

        Log::info('Wirepick: tentative d'envoi', [
            'client_id' => $clientId,
            'phones' => count(explode(';', $phoneString)),
            'message_length' => strlen($sanitizedMessage),
            'url' => str_replace($clientPassword, '***', $url),
        ]);

        try {
            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                $xmlResponse = $response->body();
                $results = $this->parseWirepickResponse($xmlResponse);

                Log::info('Wirepick: envoi réussi', [
                    'client_id' => $clientId,
                    'results_count' => count($results),
                ]);

                return $results;
            }

            Log::error('Wirepick: erreur API', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new Exception("Erreur Wirepick {$response->status()}: {$response->body()}");
        } catch (Exception $exception) {
            Log::error('Wirepick: échec envoi', [
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Nettoyage du message pour respecter les contraintes Wirepick.
     */
    public function sanitizeSmsText(string $text): string
    {
        $converted = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        if ($converted !== false) {
            $text = $converted;
        }
        $text = preg_replace('/[^A-Za-z0-9 .,!?@#%&()\-]/', '', $text);

        return trim(substr($text, 0, 150));
    }

    /**
     * Validation/formattage des numéros (format République du Congo).
     *
     * @param  array<int, string>  $phones
     * @return array<int, string>
     */
    public function validateAndFormatPhones(array $phones): array
    {
        $validated = [];

        foreach ($phones as $phone) {
            $phone = preg_replace('/\D/', '', $phone ?? '');

            if ($phone === '') {
                continue;
            }

            if (str_starts_with($phone, '242') && preg_match('/^2420\d{8}$/', $phone)) {
                $validated[] = $phone;
                continue;
            }

            if (str_starts_with($phone, '0') && preg_match('/^0\d{8}$/', $phone)) {
                $validated[] = '242' . $phone;
                continue;
            }

            if (str_starts_with($phone, '+242') && preg_match('/^\+2420\d{8}$/', $phone)) {
                $validated[] = substr($phone, 1);
            }
        }

        return array_values(array_unique($validated));
    }

    /**
     * Analyse la réponse XML de Wirepick.
     *
     * @return array<int, array<string, string|null>>
     */
    protected function parseWirepickResponse(string $xmlResponse): array
    {
        try {
            $xml = simplexml_load_string($xmlResponse);

            if ($xml === false) {
                Log::error('Wirepick: réponse XML invalide', ['response' => $xmlResponse]);
                return [];
            }

            $results = [];

            if (isset($xml->sms)) {
                $results[] = $this->formatSmsNode($xml->sms);
            }

            if (isset($xml->messages)) {
                foreach ($xml->messages->sms as $sms) {
                    $results[] = $this->formatSmsNode($sms);
                }
            }

            if (isset($xml->message)) {
                foreach ($xml->message as $sms) {
                    $results[] = $this->formatSmsNode($sms);
                }
            }

            return $results;
        } catch (Exception $exception) {
            Log::error('Wirepick: parsing échoué', [
                'error' => $exception->getMessage(),
                'response' => $xmlResponse,
            ]);

            return [];
        }
    }

    /**
     * @param  \SimpleXMLElement  $node
     * @return array<string, string|null>
     */
    protected function formatSmsNode(\SimpleXMLElement $node): array
    {
        $status = (string) ($node->status ?? '');

        return [
            'msgid' => (string) ($node->msgid ?? ''),
            'phone' => (string) ($node->phone ?? ''),
            'status' => $status,
            'status_message' => $this->getStatusMessage($status),
            'country' => isset($node->country) ? (string) $node->country : null,
            'unit_price' => isset($node->unit_price) ? (string) $node->unit_price : null,
            'total_cost' => isset($node->total_cost) ? (string) $node->total_cost : null,
            'currency' => isset($node->currency) ? (string) $node->currency : null,
        ];
    }

    protected function getStatusMessage(string $status): string
    {
        return [
            'ACT' => 'Message accepté par Wirepick',
            'DLV' => 'Message livré',
            'DLG' => 'Message reçu par la passerelle',
            'BUF' => 'Message en file d'attente',
            'NCR' => 'Route non configurée',
            'RTN' => 'Route non configurée',
            'NSF' => 'Crédits insuffisants',
            'REJ' => 'Message rejeté',
            'EXP' => 'Message expiré',
            'ABS' => 'Abonné absent',
            'BLO' => 'Numéro bloqué',
            'DUP' => 'Message dupliqué',
            'ERD' => 'Erreur de livraison',
            'LEN' => 'Message trop long',
            'PHN' => 'Numéro invalide',
            'PWD' => 'Mot de passe invalide',
            'UNK' => 'Réseau inconnu',
            'USB' => 'Abonné inconnu',
        ][$status] ?? 'Statut inconnu: ' . $status;
    }
}

