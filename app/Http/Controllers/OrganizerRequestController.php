<?php

namespace App\Http\Controllers;

use App\Models\OrganizerRequest;
use App\Models\Organizer;
use App\Models\User;
use App\Models\RejectionCode;
use App\Mail\OrganizerRequestApproved;
use App\Mail\OrganizerRequestRejected;
use App\Mail\NewOrganizerRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Str;

class OrganizerRequestController extends Controller
{
    // Méthodes pour les clients

    public function create()
    {
        $user = Auth::user();

        if ($user->isOrganizer()) {
            return redirect()->route('dashboard')
                ->with('warning', 'Vous êtes déjà un organisateur.');
        }

        if ($user->hasPendingOrganizerRequest()) {
            return redirect()->route('organizer.request.status')
                ->with('info', 'Vous avez déjà une demande en cours de traitement.');
        }

        return view('organizer-requests.create', [
            'hasRejectedRequest' => $user->hasRejectedOrganizerRequest()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|min:3|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'phone_primary' => [
                'required',
                'string',
                'max:20',
                'regex:/^(\+\d{1,3}[- ]?)?\d{10}$/'
            ],
            'address' => 'required|string|min:10|max:500',
            'motivation' => 'required|string|min:100|max:2000',
            'experience' => 'required|string|min:100|max:2000',
        ], [
            // Messages personnalisés
            'company_name.required' => 'Le nom de l\'entreprise est requis.',
            'company_name.min' => 'Le nom de l\'entreprise doit comporter au moins :min caractères.',
            'company_name.max' => 'Le nom de l\'entreprise ne peut pas dépasser :max caractères.',
            
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.max' => 'L\'adresse email ne peut pas dépasser :max caractères.',
            
            'phone_primary.required' => 'Le numéro de téléphone est requis.',
            'phone_primary.regex' => 'Le format du numéro de téléphone est invalide. Utilisez le format : +XXX XX XX XX XX ou 0X XX XX XX XX',
            'phone_primary.max' => 'Le numéro de téléphone ne peut pas dépasser :max caractères.',
            
            'address.required' => 'L\'adresse est requise.',
            'address.min' => 'L\'adresse doit comporter au moins :min caractères.',
            'address.max' => 'L\'adresse ne peut pas dépasser :max caractères.',
            
            'motivation.required' => 'Le champ motivation est requis.',
            'motivation.min' => 'Le champ motivation doit comporter au moins :min caractères.',
            'motivation.max' => 'Le champ motivation ne peut pas dépasser :max caractères.',
            
            'experience.required' => 'Le champ expérience est requis.',
            'experience.min' => 'Le champ expérience doit comporter au moins :min caractères.',
            'experience.max' => 'Le champ expérience ne peut pas dépasser :max caractères.',
        ]);

        try {
            DB::beginTransaction();

            $organizerRequest = OrganizerRequest::updateOrCreate(
                ['user_id' => Auth::id()],
                array_merge($validated, ['status' => 'en attente'])
            );

            $this->notifyAdmins($organizerRequest);

            DB::commit();

            return redirect()->route('organizer.request.status')
                ->with('success', 'Votre demande a été soumise avec succès. Elle sera examinée par notre équipe sous 48h.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur soumission demande organisateur: '.$e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la soumission de votre demande. Veuillez réessayer plus tard.');
        }
    }

    public function status()
    {
        $user = Auth::user();
        $request = $user->organizerRequest;

        if (!$request) {
            return redirect()->route('organizer.request.create')
                ->with('info', 'Vous n\'avez pas encore soumis de demande.');
        }

        return view('organizer-requests.status', [
            'request' => $request,
            'canResubmit' => $request->status === 'rejeté' && !$user->isOrganizer()
        ]);
    }

    // Méthodes pour les administrateurs

    public function adminIndex()
    {
        return view('admin.organizer-requests.index');
    }

    public function getData(Request $request)
    {
        try {
            $requests = OrganizerRequest::with(['user'])
                ->when($request->status, fn($q) => $q->where('status', $request->status))
                ->when($request->search, fn($q) => $q->where('company_name', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn($q) => $q->where('prenom', 'like', "%{$request->search}%")
                        ->orWhere('nom', 'like', "%{$request->search}%")))
                ->orderBy('created_at', 'desc')
                ->paginate($request->perPage ?? 10);

            return response()->json([
                'success' => true,
                'data' => $requests,
                'rejection_codes' => RejectionCode::all()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching organizer requests', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors du chargement'], 500);
        }
    }

   public function approve(OrganizerRequest $organizerRequest)
{
    try {
        DB::beginTransaction();

        if ($organizerRequest->status !== 'en attente') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cette demande a déjà été traitée'
            ], 400);
        }
        
        // Forcer le rechargement des permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Mise à jour du statut avec l'ID de l'admin qui a approuvé
        $organizerRequest->update([
            'status' => 'approuvé',
            'approved_by' => Auth::id()
        ]);

        // Récupérer l'utilisateur
        $user = $organizerRequest->user;
        
        // Supprimer tous les rôles existants (Client et autres)
        DB::table('model_has_roles')->where('model_id', $user->id)->delete();
        
        // Assigner le nouveau rôle Organizer (ID 2)
        DB::table('model_has_roles')->insert([
            'role_id' => 2, // ID du rôle Organizer
            'model_type' => 'App\Models\User',
            'model_id' => $user->id
        ]);

        // Générer un slug unique
        $slug = Str::slug($organizerRequest->company_name);
        $uniqueSlug = $slug;
        $counter = 1;
        
        while (Organizer::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter;
            $counter++;
        }

        // Création du profil Organizer
        Organizer::create([
            'user_id' => $user->id,
            'company_name' => $organizerRequest->company_name,
            'slug' => $uniqueSlug,
            'email' => $organizerRequest->email ?: $user->email,
            'phone_primary' => $organizerRequest->phone_primary,
            'address' => $organizerRequest->address,
            'is_verified' => true,
            'description' => $organizerRequest->motivation,
            'experience' => $organizerRequest->experience,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Envoi de l'email de confirmation
        Mail::to($user->email)
            ->queue(new OrganizerRequestApproved($user));

        // Notification aux administrateurs
        $this->notifyAdminsAboutNewOrganizer($organizerRequest, Auth::user());

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Demande approuvée avec succès'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Approval error: '.$e->getMessage()."\n".$e->getTraceAsString());
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur serveur: '.$e->getMessage()
        ], 500);
    }
}

    public function reject(Request $request, OrganizerRequest $organizerRequest)
    {
        try {
            // Valider la requête
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500',
                'rejection_code_id' => 'nullable|exists:rejection_codes,id'
            ]);

            DB::beginTransaction();

            if ($organizerRequest->status !== 'en attente') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cette demande a déjà été traitée'
                ], 400);
            }

            // Mise à jour de la demande
            $updateData = [
                'status' => 'rejeté',
                'rejection_reason' => $validated['rejection_reason']
            ];

            if (isset($validated['rejection_code_id'])) {
                $updateData['rejection_code_id'] = $validated['rejection_code_id'];
            }

            $organizerRequest->update($updateData);

            // Envoi de l'email de rejet si l'utilisateur existe
            if ($organizerRequest->user && $organizerRequest->user->email) {
                try {
                    Mail::to($organizerRequest->user->email)
                        ->queue(new OrganizerRequestRejected(
                            $organizerRequest->user,
                            $organizerRequest->rejection_reason
                        ));
                } catch (\Exception $mailException) {
                    Log::error('Email rejection failed', [
                        'error' => $mailException->getMessage(),
                        'request_id' => $organizerRequest->id
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Demande rejetée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reject error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'organizerRequest' => $organizerRequest->id,
                'request_data' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur serveur: '.$e->getMessage()
            ], 500);
        }
    }

    protected function notifyAdmins(OrganizerRequest $request)
    {
        User::role('Administrateur')->each(function($Administrateur) use ($request) {
            try {
                Mail::to($Administrateur->email)
                    ->queue(new NewOrganizerRequest($request));
            } catch (\Exception $e) {
                Log::error("Erreur notification Administrateur: ".$e->getMessage());
            }
        });
    }

    protected function notifyAdminsAboutNewOrganizer(OrganizerRequest $request, User $approver)
{
    try {
        User::role('Administrateur')->each(function($admin) use ($request, $approver) {
            try {
                if (class_exists('App\Mail\NewOrganizerApproved')) {
                    Mail::to($admin->email)
                        ->queue(new \App\Mail\NewOrganizerApproved($request, $approver));
                }
            } catch (\Exception $e) {
                Log::error("Erreur notification Administrateur: ".$e->getMessage());
            }
        });
    } catch (\Exception $e) {
        Log::error("Erreur lors de la notification des administrateurs: ".$e->getMessage());
    }
}
}
