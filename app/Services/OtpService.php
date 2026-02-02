<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPVerification;

class OtpService
{
    protected $twilio;
    protected $twilioNumber;

    public function __construct()
    {
        $this->twilioNumber = config('services.twilio.phone_number');
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
    }

    /**
     * Génère un code OTP et le stocke dans le cache
     */
   public function generateOtp($identifier, $length = 6, $expiresInMinutes = 3)
{
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $cacheKey = "otp_{$identifier}";
    
    Cache::put($cacheKey, $otp, now()->addMinutes($expiresInMinutes));
    Cache::put("{$cacheKey}_expires_at", now()->addMinutes($expiresInMinutes)->timestamp, now()->addMinutes($expiresInMinutes));
    
    return $otp;
}

    /**
     * Envoie un OTP par SMS via Twilio
     */
    public function sendOtpViaSms($phoneNumber, $otp)
{
    try {
        $formattedNumber = $this->formatPhoneNumber($phoneNumber);
        $message = "Votre code de vérification MokiliEvent est : {$otp}. Valable 3 minutes.";
        
        \Log::info('Tentative d\'envoi SMS', [
            'numero' => $formattedNumber,
            'message' => $message
        ]);

        $message = $this->twilio->messages->create(
            $formattedNumber,
            [
                'from' => $this->twilioNumber,
                'body' => $message
            ]
        );

        \Log::info('SMS envoyé avec succès', [
            'sid' => $message->sid,
            'to' => $formattedNumber
        ]);

        return true;
    } catch (\Exception $e) {
        \Log::error('Erreur d\'envoi SMS: ' . $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        return false;
    }
}

    /**
     * Envoie un OTP par email
     */
    public function sendOtpViaEmail($email, $otp, $name = null)
    {
        try {
            Mail::to($email)->send(new OTPVerification($otp, $name));
            return true;
        } catch (\Exception $e) {
            \Log::error("Erreur d'envoi email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si l'OTP est valide
     */
    public function verifyOtp($identifier, $otp)
    {
        $cacheKey = "otp_{$identifier}";
        $storedOtp = Cache::get($cacheKey);
        
        if (!$storedOtp) {
            return false;
        }
        
        if ($storedOtp === $otp) {
            Cache::forget($cacheKey);
            return true;
        }
        
        return false;
    }

    /**
     * Formate le numéro de téléphone pour Twilio
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Supprime tous les caractères non numériques
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Si le numéro commence par 0, on le remplace par +242 (pour le Congo)
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '+242' . substr($phoneNumber, 1);
        }
        // Si le numéro commence par 242, on ajoute le +
        elseif (substr($phoneNumber, 0, 3) === '242') {
            $phoneNumber = '+' . $phoneNumber;
        }
        // Si le numéro commence par +, on le laisse tel quel
        elseif (substr($phoneNumber, 0, 1) !== '+') {
            $phoneNumber = '+242' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * Récupère le temps restant avant expiration de l'OTP
     */
    public function getRemainingTime($identifier)
    {
        $cacheKey = "otp_{$identifier}";
        return Cache::get($cacheKey) ? Cache::get("otp_{$identifier}_expires_at") - time() : 0;
    }
}