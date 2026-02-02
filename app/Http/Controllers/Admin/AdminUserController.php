<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\OrganizerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InfluencerRequestApproved;
use App\Mail\InfluencerRequestRejected;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Requête de base pour les utilisateurs
            $query = User::with('roles');

            // Recherche (nom, prénom, email, nom complet)
            if ($request->filled('search')) {
                $search = trim($request->string('search'));
                $query->where(function($q) use ($search) {
                    $q->where('prenom', 'like', "%{$search}%")
                      ->orWhere('nom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT(prenom,' ',nom) LIKE ?", ["%{$search}%"])
                      ->orWhereRaw("CONCAT(nom,' ',prenom) LIKE ?", ["%{$search}%"]);
                });
            }

            // Filtrage par rôle (id ou alias: admin|organizer|client)
            if ($request->filled('role')) {
                $role = $request->string('role');
                $roleId = null;
                if (is_numeric($role)) {
                    $roleId = (int) $role;
                } else {
                    $map = [
                        'admin' => 3,
                        'administrator' => 3,
                        'organizer' => 2,
                        'organisateur' => 2,
                        'client' => 1,
                        'user' => 1,
                        'utilisateur' => 1,
                    ];
                    $roleId = $map[strtolower($role)] ?? null;
                }
                if ($roleId) {
                    $query->whereHas('roles', function($q) use ($roleId) {
                        $q->where('id', $roleId);
                    });
                }
            }

            // Filtre par genre
            if ($request->filled('genre')) {
                $query->where('genre', $request->string('genre'));
            }

            // Filtre influenceur (yes/no)
            if ($request->filled('influencer')) {
                $val = strtolower($request->string('influencer'));
                if (in_array($val, ['yes', 'oui', '1'])) {
                    $query->where('is_influencer', true);
                } elseif (in_array($val, ['no', 'non', '0'])) {
                    $query->where('is_influencer', false);
                }
            }

            // Tri
            $sort = $request->input('sort', 'created_at');
            $direction = $request->input('direction', 'desc');
            $query->orderBy($sort, $direction);

            // Ajouter le nombre d'achats pour chaque utilisateur
            $users = $query->withCount(['orders as total_purchases' => function($query) {
                $query->where('statut', 'payé');
            }])->paginate(10);

            // Récupérer les demandes d'organisateur en attente avec pagination
            $organizerRequests = OrganizerRequest::with('user')
                ->where('status', 'en attente')
                ->orderBy('created_at', 'desc')
                ->paginate(5); // Paginer avec 5 éléments par page

            // Demandes Influenceur en attente (flag sur users)
            $influencerRequests = User::where('influencer_requested', true)
                ->where('is_influencer', false)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Récupérer tous les rôles disponibles
            $roles = Role::all();

            // Récupérer les codes de rejet
            $rejectionCodes = DB::table('rejection_codes')->get();

            return view('dashboard.admin.users.index', compact('users', 'roles', 'organizerRequests', 'rejectionCodes', 'influencerRequests'));
        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@index: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('dashboard.admin.users.index', [
                'users' => collect(),
                'roles' => Role::all(),
                'organizerRequests' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'influencerRequests' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'rejectionCodes' => collect(),
                'error' => 'Une erreur est survenue lors du chargement des utilisateurs.'
            ]);
        }
    }

    public function approveInfluencer(User $user)
    {
        try {
            $user->is_influencer = true;
            $user->influencer_requested = false;
            $user->save();
            Mail::to($user->email)->send(new InfluencerRequestApproved($user));
            return back()->with('success', 'Utilisateur approuvé comme influenceur.');
        } catch (\Exception $e) {
            Log::error('approveInfluencer error: '.$e->getMessage());
            return back()->with('error', 'Impossible d\'approuver la demande.');
        }
    }

    public function rejectInfluencer(Request $request, User $user)
    {
        try {
            $reason = $request->input('reason', '');
            $user->influencer_requested = false;
            $user->save();
            Mail::to($user->email)->send(new InfluencerRequestRejected($user, $reason));
            return back()->with('success', 'Demande influenceur rejetée.');
        } catch (\Exception $e) {
            Log::error('rejectInfluencer error: '.$e->getMessage());
            return back()->with('error', 'Impossible de rejeter la demande.');
        }
    }

    public function edit(User $user)
    {
        try {
            $roles = Role::all();
            return view('dashboard.admin.users.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@edit: ' . $e->getMessage());
            return redirect()->route('admin.users')->with('error', 'Une erreur est survenue.');
        }
    }

    public function show(User $user)
    {
        try {
            $user->load(['roles']);
            return view('dashboard.admin.users.show', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@show: ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'Impossible d\'afficher l\'utilisateur.');
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'prenom' => 'required|string|max:255',
                'nom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'role' => 'required|exists:roles,id',
                'is_active' => 'boolean'
            ]);

            $user->update([
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'is_active' => $request->has('is_active')
            ]);

            $user->syncRoles([$validated['role']]);

            return redirect()->route('admin.users')->with('success', 'Utilisateur mis à jour avec succès');
        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas supprimer votre propre compte'
                ]);
            }

            $user->delete();

            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.users')->with('success', 'Utilisateur supprimé avec succès');
        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@destroy: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la suppression'
                ]);
            }

            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
}




