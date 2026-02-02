<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentValidationService
{
    /**
     * Valide une commande avant paiement
     */
    public function validateOrderForPayment(Order $order): array
    {
        $errors = [];

        // Vérifier que la commande existe et appartient à l'utilisateur
        if (!$order) {
            $errors[] = 'Commande introuvable';
            return ['valid' => false, 'errors' => $errors];
        }

        // Vérifier que la commande n'est pas déjà payée
        if ($order->statut === 'payé') {
            $errors[] = 'Cette commande a déjà été payée';
        }

        // Vérifier que la commande n'a pas expiré
        if ($order->expires_at && $order->expires_at->isPast()) {
            $errors[] = 'Cette commande a expiré. Veuillez recommencer';
        }

        // Vérifier que la commande a un montant valide
        if (!$order->montant_total || $order->montant_total <= 0) {
            $errors[] = 'Montant de la commande invalide';
        }

        // Vérifier la disponibilité des tickets
        foreach ($order->tickets as $ticket) {
            $requestedQuantity = $ticket->pivot->quantity;
            $availableQuantity = $ticket->quantite - $ticket->quantite_vendue;
            
            if ($requestedQuantity > $availableQuantity) {
                $errors[] = "Plus assez de billets disponibles pour '{$ticket->nom}' (demandé: {$requestedQuantity}, disponible: {$availableQuantity})";
            }

            // Vérifier que le ticket est toujours actif
            if ($ticket->statut !== 'disponible') {
                $errors[] = "Le ticket '{$ticket->nom}' n'est plus disponible";
            }

            // Vérifier les dates de validité du ticket
            if ($ticket->date_fin_vente && $ticket->date_fin_vente->isPast()) {
                $errors[] = "La vente du ticket '{$ticket->nom}' est fermée";
            }
        }

        // Vérifier l'événement associé
        if ($order->event) {
            if ($order->event->statut !== 'actif') {
                $errors[] = "L'événement '{$order->event->nom}' n'est plus actif";
            }

            if ($order->event->date_debut && $order->event->date_debut->isPast()) {
                $errors[] = "L'événement '{$order->event->nom}' a déjà commencé";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Valide les données de paiement
     */
    public function validatePaymentData(array $data): array
    {
        $validator = Validator::make($data, [
            'payment_type' => 'required|in:flexible,fixed_amount,fixed_phone,fixed_both',
            'phone_number' => 'required_if:payment_type,fixed_phone,fixed_both|string|min:9|max:15',
            'amount' => 'required_if:payment_type,fixed_amount,fixed_both|numeric|min:100|max:1000000',
            'country' => 'required_if:payment_type,fixed_amount|string|size:2|in:CG,CM,CI,SN,ML,BF,NE,TG,GW,GN,LR,SL,GM,GH,NG,KE,UG,TZ,RW,BI,MW,ZM,ZW,BW,NA,ZA,LS,SZ,MZ,MG,SC,MU,KM,DJ,SO,ET,ER,SD,SS,TD,CF,CD,AO,ZM,ZW,BW,NA,ZA,LS,SZ,MZ,MG,SC,MU,KM,DJ,SO,ET,ER,SD,SS,TD,CF,CD,AO',
        ], [
            'payment_type.required' => 'Le type de paiement est obligatoire',
            'payment_type.in' => 'Type de paiement invalide',
            'phone_number.required_if' => 'Le numéro de téléphone est obligatoire pour ce type de paiement',
            'phone_number.min' => 'Le numéro de téléphone doit contenir au moins 9 caractères',
            'phone_number.max' => 'Le numéro de téléphone ne peut pas dépasser 15 caractères',
            'amount.required_if' => 'Le montant est obligatoire pour ce type de paiement',
            'amount.min' => 'Le montant minimum est de 100 FCFA',
            'amount.max' => 'Le montant maximum est de 1,000,000 FCFA',
            'country.required_if' => 'Le pays est obligatoire pour ce type de paiement',
            'country.size' => 'Le code pays doit contenir exactement 2 caractères',
            'country.in' => 'Pays non supporté'
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->all()
            ];
        }

        // Validations supplémentaires
        $errors = [];

        // Valider le numéro de téléphone si fourni
        if (isset($data['phone_number'])) {
            $phoneValidation = $this->validatePhoneNumber($data['phone_number']);
            if (!$phoneValidation['valid']) {
                $errors = array_merge($errors, $phoneValidation['errors']);
            }
        }

        // Valider le montant si fourni
        if (isset($data['amount'])) {
            $amountValidation = $this->validateAmount($data['amount']);
            if (!$amountValidation['valid']) {
                $errors = array_merge($errors, $amountValidation['errors']);
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Valide un numéro de téléphone
     */
    public function validatePhoneNumber(string $phoneNumber): array
    {
        $errors = [];
        
        // Nettoyer le numéro
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Vérifier la longueur
        if (strlen($cleanPhone) < 9 || strlen($cleanPhone) > 15) {
            $errors[] = 'Le numéro de téléphone doit contenir entre 9 et 15 chiffres';
        }

        // Vérifier le format pour le Congo (242)
        if (str_starts_with($cleanPhone, '0')) {
            // Format local: 05 12 34 56 78
            $localNumber = substr($cleanPhone, 1);
            if (strlen($localNumber) !== 8) {
                $errors[] = 'Format local invalide. Utilisez: 05 12 34 56 78';
            }
        } elseif (str_starts_with($cleanPhone, '242')) {
            // Format international: 242 5 12 34 56 78
            $internationalNumber = substr($cleanPhone, 3);
            if (strlen($internationalNumber) !== 8) {
                $errors[] = 'Format international invalide. Utilisez: +242 5 12 34 56 78';
            }
        } elseif (str_starts_with($cleanPhone, '+242')) {
            // Format avec +
            $internationalNumber = substr($cleanPhone, 4);
            if (strlen($internationalNumber) !== 8) {
                $errors[] = 'Format international invalide. Utilisez: +242 5 12 34 56 78';
            }
        } else {
            $errors[] = 'Format de numéro non reconnu. Utilisez un format congolais valide';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'formatted' => $this->formatPhoneNumber($phoneNumber)
        ];
    }

    /**
     * Valide un montant
     */
    public function validateAmount(float $amount): array
    {
        $errors = [];

        // Vérifier les limites
        if ($amount < 100) {
            $errors[] = 'Le montant minimum est de 100 FCFA';
        }

        if ($amount > 1000000) {
            $errors[] = 'Le montant maximum est de 1,000,000 FCFA';
        }

        // Vérifier que c'est un nombre entier (pas de centimes pour les montants élevés)
        if ($amount >= 1000 && fmod($amount, 1) !== 0.0) {
            $errors[] = 'Les montants supérieurs à 1000 FCFA doivent être des nombres entiers';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Formate un numéro de téléphone
     */
    public function formatPhoneNumber(string $phoneNumber): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        if (str_starts_with($cleanPhone, '0')) {
            return '242' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '242')) {
            return '242' . $cleanPhone;
        }
        
        return $cleanPhone;
    }

    /**
     * Valide les limites de transaction selon le fournisseur
     */
    public function validateProviderLimits(string $provider, float $amount): array
    {
        $limits = [
            'mtn' => ['min' => 100, 'max' => 500000],
            'airtel' => ['min' => 100, 'max' => 500000],
            'orange' => ['min' => 100, 'max' => 300000],
            'moov' => ['min' => 100, 'max' => 200000],
        ];

        $providerLimits = $limits[$provider] ?? ['min' => 100, 'max' => 500000];

        if ($amount < $providerLimits['min']) {
            return [
                'valid' => false,
                'errors' => ["Le montant minimum pour {$provider} est de {$providerLimits['min']} FCFA"]
            ];
        }

        if ($amount > $providerLimits['max']) {
            return [
                'valid' => false,
                'errors' => ["Le montant maximum pour {$provider} est de {$providerLimits['max']} FCFA"]
            ];
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Valide la disponibilité d'un fournisseur
     */
    public function validateProviderAvailability(string $provider): array
    {
        // Simulation de vérification de disponibilité
        // En production, cela pourrait faire un appel API à pawaPay
        
        $unavailableProviders = []; // Liste des fournisseurs temporairement indisponibles
        
        if (in_array($provider, $unavailableProviders)) {
            return [
                'valid' => false,
                'errors' => ["Le fournisseur {$provider} est temporairement indisponible"]
            ];
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Valide la sécurité d'une transaction
     */
    public function validateTransactionSecurity(array $data, string $ip): array
    {
        $errors = [];

        // Vérifier les patterns suspects
        $suspiciousPatterns = [
            'amount' => [0, 1, 999999, 1000000], // Montants suspects
            'phone_number' => ['000000000', '111111111', '999999999'], // Numéros suspects
        ];

        foreach ($suspiciousPatterns as $field => $patterns) {
            if (isset($data[$field]) && in_array($data[$field], $patterns)) {
                $errors[] = "Valeur suspecte détectée pour le champ {$field}";
            }
        }

        // Log des tentatives suspectes
        if (!empty($errors)) {
            Log::warning('Tentative de transaction suspecte détectée', [
                'ip' => $ip,
                'data' => $data,
                'errors' => $errors
            ]);
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
