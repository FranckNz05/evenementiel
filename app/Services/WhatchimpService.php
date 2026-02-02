<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatchimpService
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.whatchimp.base_url', 'https://whatchimp.support');
        $this->token = config('services.whatchimp.token');
    }

    /**
     * Envoi d'un message WhatsApp via l'API Whatchimp en utilisant un template.
     *
     * @param  string  $phone
     * @param  string  $title
     * @param  string  $message
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function sendMessage(string $phone, string $title, string $message): array
    {
        if (empty($this->token)) {
            throw new Exception("Token Whatchimp manquant. Renseignez 'WHATCHIMP_TOKEN' dans le fichier .env");
        }

        $url = rtrim($this->baseUrl, '/') . '/api/send/template';
        
        $phone = $this->formatPhoneForWhatsApp($phone);

        Log::info('Whatchimp: tentative d'envoi via template', [
            'phone' => $phone,
            'title' => $title,
            'message_length' => strlen($message),
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($url, [
                'phone' => $phone,
                'template' => [
                    'name' => 'informations',
                    'language' => [
                        'code' => 'fr',
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $title,
                                ],
                                [
                                    'type' => 'text',
                                    'text' => $message,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('Whatchimp: envoi réussi', [
                    'phone' => $phone,
                    'response' => $responseData,
                ]);

                return [
                    'success' => true,
                    'phone' => $phone,
                    'response' => $responseData,
                ];
            }

            Log::error('Whatchimp: erreur API', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new Exception("Erreur Whatchimp {$response->status()}: {$response->body()}");
        } catch (Exception $exception) {
            Log::error('Whatchimp: échec envoi', [
                'error' => $exception->getMessage(),
                'phone' => $phone,
            ]);

            throw $exception;
        }
    }

    /**
     * Envoi groupé de messages WhatsApp.
     *
     * @param  array<int, string>  $phones
     * @param  string  $title
     * @param  string  $message
     * @return array<int, array<string, mixed>>
     */
    public function sendBulkMessages(array $phones, string $title, string $message): array
    {
        $results = [];
        $errors = [];

        foreach ($phones as $phone) {
            try {
                $result = $this->sendMessage($phone, $title, $message);
                $results[] = $result;
            } catch (Exception $exception) {
                $errors[] = [
                    'phone' => $phone,
                    'error' => $exception->getMessage(),
                ];
                $results[] = [
                    'success' => false,
                    'phone' => $phone,
                    'error' => $exception->getMessage(),
                ];
            }
        }

        if (!empty($errors)) {
            Log::warning('Whatchimp: erreurs lors de l'envoi groupé', [
                'errors_count' => count($errors),
                'errors' => $errors,
            ]);
        }

        return $results;
    }

    /**
     * Formate le numéro de téléphone pour WhatsApp (format international avec +).
     *
     * @param  string  $phone
     * @return string
     */
    public function formatPhoneForWhatsApp(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (empty($phone)) {
            return $phone;
        }

        if (str_starts_with($phone, '242')) {
            return '+' . $phone;
        }

        if (str_starts_with($phone, '0') && strlen($phone) === 9) {
            return '+242' . $phone;
        }

        if (!str_starts_with($phone, '+')) {
            return '+' . $phone;
        }

        return $phone;
    }
}

