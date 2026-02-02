<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Organizer;

class OrganizerProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $organizer = $user->organizer;
        
        // Si l'utilisateur n'a pas encore de profil organisateur, on en crée un vide
        if (!$organizer) {
            $organizer = new Organizer([
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            $organizer->save();
            // Recharger la relation
            $user->load('organizer');
            $organizer = $user->organizer;
        }
        
        return view('profile.organizer-edit', [
            'user' => $user,
            'organizer' => $organizer,
            'socialMedia' => $organizer->social_media ? json_decode($organizer->social_media, true) : []
        ]);
    }

    /**
     * Met à jour le profil de l'organisateur
     */
    public function update(Request $request, Organizer $organizer)
    {
        
        $user = Auth::user();
    $organizer = $user->organizer;

    if (!$organizer) {
        return redirect()->back()->with('error', 'Profil organisateur non trouvé');
    }
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:191',
            'slogan' => 'nullable|string|max:191',
            'description' => 'required|string|min:100|max:65535',
            'email' => 'required|email|max:191',
            'phone_primary' => 'required|string|max:191',
            'phone_secondary' => 'nullable|string|max:191',
            'website' => 'nullable|url|max:191',
            'address' => 'required|string|max:191',
            'city' => 'required|string|max:191',
            'country' => 'required|string|max:191',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facebook' => 'nullable|url|max:191',
            'twitter' => 'nullable|url|max:191',
            'instagram' => 'nullable|url|max:191',
            'linkedin' => 'nullable|url|max:191',
        ]);

        try {
            DB::beginTransaction();

            // Gestion des fichiers
            if ($request->hasFile('logo')) {
                if ($organizer->logo) {
                    Storage::delete('public/'.$organizer->logo);
                }
                $validated['logo'] = $request->file('logo')->store('organizers/logos', 'public');
            }

            if ($request->hasFile('banner_image')) {
                if ($organizer->banner_image) {
                    Storage::delete('public/'.$organizer->banner_image);
                }
                $validated['banner_image'] = $request->file('banner_image')->store('organizers/banners', 'public');
            }

            // Gestion des réseaux sociaux
            $socialMedia = [
                'facebook' => $validated['facebook'] ?? null,
                'twitter' => $validated['twitter'] ?? null,
                'instagram' => $validated['instagram'] ?? null,
                'linkedin' => $validated['linkedin'] ?? null,
            ];

            // Filtrer les champs pour ne garder que ceux qui sont dans la table
            $organizerData = collect($validated)
                ->except(['facebook', 'twitter', 'instagram', 'linkedin'])
                ->toArray();

            // Ajouter les réseaux sociaux en JSON
            $organizerData['social_media'] = json_encode($socialMedia);

            // Mettre à jour l'organisateur
            $organizer->update($organizerData);

            DB::commit();

            return redirect()->back()->with('success', 'Profil mis à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du profil: ' . $e->getMessage());
        }
    }
    
    protected function updateLogo($organizer, $file)
            {
                if ($organizer->logo) {
                    Storage::disk('public')->delete($organizer->logo);
                }
                return $file->store('/logos', 'public');
            }
            
            protected function updateBanner($organizer, $file)
            {
                if ($organizer->banner_image) {
                    Storage::disk('public')->delete($organizer->banner_image);
                }
                return $file->store('/banners', 'public');
}
}