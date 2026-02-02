<?php

namespace App\Http\Controllers\Auth\API;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('auth:sanctum');
    }

    public function show(): JsonResponse
    {
        $user = auth()->user()->load(['roles', 'permissions']);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'prenom' => $user->prenom,
                    'nom' => $user->nom,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'genre' => $user->genre,
                    'tranche_age' => $user->tranche_age,
                    'profil_image' => $user->profil_image,
                    'address' => $user->address,
                    'city' => $user->city,
                    'country' => $user->country,
                    'is_profile_complete' => $user->is_profile_complete,
                ],
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ]
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'prenom' => ['sometimes', 'string', 'max:191'],
            'nom' => ['sometimes', 'string', 'max:191'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'genre' => ['sometimes', 'in:Homme,Femme,Autre'],
            'tranche_age' => ['sometimes', 'in:0-17,18-25,26-35,36-45,46+'],
            'profil_image' => ['sometimes', 'image', 'max:2048'],
            'address' => ['sometimes', 'string', 'max:191'],
            'city' => ['sometimes', 'string', 'max:191'],
            'country' => ['sometimes', 'string', 'max:191'],
        ]);

        $data = $request->except('profil_image');

        if ($request->hasFile('profil_image')) {
            $data['profil_image'] = $this->imageService->handleProfilePhoto($request->file('profil_image'));
        }

        // Vérifier si le profil est complet
        $requiredFields = ['prenom', 'nom', 'email', 'phone'];
        $data['is_profile_complete'] = true;
        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                $data['is_profile_complete'] = false;
                break;
            }
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'data' => $user->only(['id', 'prenom', 'nom', 'email', 'phone', 'profil_image', 'is_profile_complete'])
        ]);
    }
}