<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizerRequest;
use App\Models\User;
use App\Models\Organizer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Mail\OrganizerRequestApproved;
use App\Mail\OrganizerRequestRejected;

class OrganizerRequestController extends Controller
{
    public function approve($id)
{
    try {
        Log::info('Approbation demande organisateur - début', ['request_id' => $id]);
        
        $organizerRequest = OrganizerRequest::find($id);
        
        if (!$organizerRequest) {
            Log::error('Demande non trouvée', ['request_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Demande non trouvée'
            ], 404);
        }

        Log::info('Approbation demande organisateur', [
            'request_id' => $organizerRequest->id,
            'status' => $organizerRequest->status,
            'user_id' => $organizerRequest->user_id
        ]);

        DB::beginTransaction();

        if ($organizerRequest->status !== 'en attente') {
            Log::warning('Demande déjà traitée', [
                'request_id' => $organizerRequest->id,
                'current_status' => $organizerRequest->status
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Cette demande a déjà été traitée'
            ], 400);
        }

        // Récupération de l'utilisateur
        $user = $organizerRequest->user;
        
        if (!$user) {
            throw new \Exception("Aucun utilisateur associé à cette demande");
        }

        // Mise à jour du statut avec l'admin qui a approuvé
        $organizerRequest->update([
            'status' => 'approuvé',
            'approved_by' => auth()->id() // Ajout de l'admin qui a approuvé
        ]);

        // Gestion des rôles - Approche plus robuste
        $organizerRole = Role::where('name', 'Organizer')->first();
        
        if (!$organizerRole) {
            throw new \Exception("Le rôle Organizer n'existe pas dans la base de données");
        }

        // Suppression de tous les rôles existants
        DB::table('model_has_roles')->where('model_id', $user->id)->delete();
        
        // Attribution du rôle Organizer
        DB::table('model_has_roles')->insert([
            'role_id' => $organizerRole->id,
            'model_type' => get_class($user),
            'model_id' => $user->id
        ]);

        // Génération de slug unique
        $slug = Str::slug($organizerRequest->company_name);
        $originalSlug = $slug;
        $counter = 1;
        
        while (Organizer::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Création ou mise à jour du profil Organizer
        Organizer::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $organizerRequest->company_name,
                'slug' => $slug,
                'email' => $organizerRequest->email ?: $user->email,
                'phone_primary' => $organizerRequest->phone_primary,
                'address' => $organizerRequest->address,
                'is_verified' => true,
                'description' => $organizerRequest->motivation,
                'experience' => $organizerRequest->experience, // Ajout de l'expérience
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Envoi de l'email
        Mail::to($user->email)
            ->queue(new OrganizerRequestApproved($user));

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Demande approuvée avec succès'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur approbation organisateur: '.$e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request_id' => $organizerRequest->id ?? null,
            'user_id' => $organizerRequest->user->id ?? null
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'approbation: '.$e->getMessage()
        ], 500);
    }
}

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        try {
            Log::info('Rejet demande organisateur - début', ['request_id' => $id]);
            
            $organizerRequest = OrganizerRequest::find($id);
            
            if (!$organizerRequest) {
                Log::error('Demande non trouvée pour rejet', ['request_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Demande non trouvée'
                ], 404);
            }

            DB::beginTransaction();

            if ($organizerRequest->status !== 'en attente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande a déjà été traitée'
                ], 400);
            }

            $organizerRequest->update([
                'status' => 'rejeté',
                'rejection_reason' => $validated['rejection_reason']
            ]);

            if ($organizerRequest->user && $organizerRequest->user->email) {
                Mail::to($organizerRequest->user->email)
                    ->queue(new OrganizerRequestRejected(
                        $organizerRequest->user,
                        $validated['rejection_reason']
                    ));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Demande rejetée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur rejet demande', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}