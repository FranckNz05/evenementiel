<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPVerification;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'prenom' => ['required', 'string', 'max:191'],
            'nom' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users', new \App\Rules\ValidEmailDomain()],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
            'genre' => ['nullable', 'in:Homme,Femme,Autre'],
            'tranche_age' => ['nullable', 'in:0-17,18-25,26-35,36-45,46+'],
            'profil_image' => ['nullable', 'image', 'max:2048'],
        ]);

        try {
            // Gestion de l'image de profil
            $profileImagePath = null;
            if ($request->hasFile('profil_image')) {
                $profileImagePath = $this->imageService->handleProfilePhoto($request->file('profil_image'));
            }

            // Génération OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $otp_expires_at = now()->addMinutes(4);

            $user = User::create([
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'email' => $request->email,
                'phone' => $request->phone,
                'genre' => $request->genre,
                'tranche_age' => $request->tranche_age,
                'password' => Hash::make($request->password),
                'profil_image' => $profileImagePath,
                'otp' => $otp,
                'otp_expires_at' => $otp_expires_at,
                'is_active' => false, // Désactivé jusqu'à vérification OTP
            ]);

            // Envoi de l'OTP par email
            $emailSent = false;
            try {
                Mail::to($user->email)->send(new OTPVerification($user));
                $emailSent = true;
            } catch (\Exception $mailException) {
                \Log::error('Erreur envoi email OTP', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $mailException->getMessage()
                ]);
                
                // Si l'email est invalide, supprimer l'utilisateur
                if (str_contains($mailException->getMessage(), 'domain') || str_contains($mailException->getMessage(), 'invalid')) {
                    $user->delete();
                    return response()->json([
                        'success' => false,
                        'message' => 'L\'adresse email fournie n\'est pas valide. Veuillez vérifier votre adresse email.',
                        'error' => 'invalid_email'
                    ], 422);
                }
            }

            return response()->json([
                'success' => true,
                'message' => $emailSent 
                    ? 'Inscription réussie. Un code OTP a été envoyé à votre email.'
                    : 'Inscription réussie. Cependant, l\'envoi de l\'email a échoué. Vous pouvez demander un nouveau code.',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'email_sent' => $emailSent,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'inscription API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer plus tard.',
                'error' => config('app.debug') ? $e->getMessage() : 'internal_error'
            ], 500);
        }
    }

    public function messages()
{
    return [
        'password.regex' => 'Le mot de passe doit contenir au moins 8 caractères dont une majuscule, une minuscule, un chiffre et un caractère spécial.',
    ];
}
}