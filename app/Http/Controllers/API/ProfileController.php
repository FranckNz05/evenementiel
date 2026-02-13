<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\OrderTicket;
use App\Models\Event;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Afficher le profil utilisateur
     */
    public function show()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'profile_completion' => $this->calculateProfileCompletion($user),
            ]
        ]);
    }

    /**
     * Mettre à jour le profil
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'prenom' => ['sometimes', 'string', 'max:255'],
            'nom' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['sometimes', 'string', 'max:20'],
            'genre' => ['nullable', 'string', 'in:Homme,Femme,Autre'],
            'tranche_age' => ['nullable', 'string', 'in:0-17,18-25,26-35,36-45,46+'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $user->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'data' => [
                    'user' => $user,
                    'profile_completion' => $this->calculateProfileCompletion($user),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour profil', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil'
            ], 500);
        }
    }

    /**
     * Supprimer le compte utilisateur
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        try {
            Auth::logout();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Compte supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur suppression compte', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du compte'
            ], 500);
        }
    }

    /**
     * Liste des billets de l'utilisateur
     */
    public function tickets()
    {
        $user = auth()->user();

        $tickets = OrderTicket::with(['ticket.event', 'order'])
            ->whereHas('order', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('statut', 'payé');
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }

    /**
     * Liste des événements de l'organisateur
     */
    public function events()
    {
        if (!auth()->user()->isOrganizer()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé. Cette section est réservée aux organisateurs.'
            ], 403);
        }

        $events = Event::where('user_id', auth()->user()->id)
            ->with(['category', 'location'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Upload photo de profil
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        try {
            // Supprimer l'ancienne image si elle existe
            if ($user->profil_image && Storage::exists('public/'.$user->profil_image)) {
                Storage::delete('public/'.$user->profil_image);
            }

            // Enregistrer la nouvelle image
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profil_image = $path;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès',
                'data' => [
                    'photo_url' => Storage::url($path),
                    'profile_completion' => $this->calculateProfileCompletion($user),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur upload photo', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la photo'
            ], 500);
        }
    }

    /**
     * Calcul du pourcentage de complétion du profil
     */
    private function calculateProfileCompletion(User $user)
    {
        $requiredFields = [
            'prenom' => $user->prenom,
            'nom' => $user->nom,
            'email' => $user->email,
            'phone' => $user->phone,
            'profil_image' => $user->profil_image,
        ];

        $filledFields = count(array_filter($requiredFields));
        $totalFields = count($requiredFields);

        return [
            'percentage' => round(($filledFields / $totalFields) * 100),
            'filled_fields' => $filledFields,
            'total_fields' => $totalFields,
        ];
    }
}
