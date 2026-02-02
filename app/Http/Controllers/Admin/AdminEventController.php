<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\EventPublicationRequest;
use App\Notifications\EventApprovedNotification;
use App\Notifications\EventRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminEventController extends Controller
{
    const PAGINATION_PER_PAGE = 6;
    const PENDING_EVENTS_PER_PAGE = 4;

    public function index(Request $request)
{
    $query = Event::query()->with(['category', 'user.organizerProfile', 'publicationRequest']);

    // Recherche
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('ville', 'like', "%{$search}%");
        });
    }

    // Filtres
    if ($request->filled('status')) {
        $query->where('etat', $request->status);
    }

    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // Événements approuvés
    $approvedEvents = $query->where('is_approved', true)
        ->latest()
        ->paginate(self::PAGINATION_PER_PAGE);

    // Événements en attente (séparément)
    $pendingEvents = Event::with(['category', 'user.organizerProfile', 'publicationRequest'])
        ->whereHas('publicationRequest', function($q) {
            $q->where('status', 'pending');
        })
        ->latest()
        ->paginate(self::PENDING_EVENTS_PER_PAGE, ['*'], 'pending_page');

    // Ajout de la récupération des événements personnalisés
    $customEvents = \App\Models\CustomPersonalEvent::with(['organizer', 'guests'])
        ->latest()
        ->paginate(6, ['*'], 'custom_page');

    return view('dashboard.admin.events.index', [
        'events' => $approvedEvents,
        'pendingEvents' => $pendingEvents,
        'categories' => Category::all(),
        'customEvents' => $customEvents,
    ]);
}

    public function create()
    {
        return view('dashboard.admin.events.create', [
            'categories' => Category::all(),
            'organizers' => User::role('Organizer')->with('organizerProfile')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateEventRequest($request);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement créé avec succès.');
    }

    public function show(Event $event)
    {
        return view('dashboard.admin.events.show', [
            'event' => $event->load(['category', 'user.organizerProfile'])
        ]);
    }

    public function edit(Event $event)
    {
        return view('dashboard.admin.events.edit', [
            'event' => $event,
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $this->validateEventRequest($request, true);

        // Gestion des champs booléens
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_approved'] = $request->has('is_approved');
        $validated['is_published'] = $request->has('is_published');

        // Conversion des keywords
        $validated['keywords'] = $request->filled('keywords') 
            ? json_encode(array_map('trim', explode(',', $request->keywords)))
            : null;

        // Gestion de l'image
        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement mis à jour avec succès.');
    }

    public function destroy(Event $event)
    {
        DB::transaction(function() use ($event) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $event->delete();
        });

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement supprimé avec succès.');
    }

        // 1. PROBLÈME PRINCIPAL : Vérification de l'existence des relations
public function approve(Request $request, $eventId)
{
    try {
        $event = Event::with(['user', 'category', 'publicationRequest'])->findOrFail($eventId);
        
        // CORRECTION : Vérifier si une demande de publication existe
        if (!$event->publicationRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune demande de publication trouvée pour cet événement'
            ], 400);
        }
        
        // Vérification que l'événement n'est pas déjà approuvé
        if ($event->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Cet événement est déjà approuvé'
            ], 400);
        }

        // CORRECTION : Vérification de date plus robuste
        if ($event->end_date && now()->isAfter($event->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'approuver un événement dont la date de fin est passée'
            ], 400);
        }

        DB::transaction(function() use ($event) {
            // Mise à jour de l'événement
            $event->update([
                'status' => 'published',
                'is_approved' => true,
                'is_published' => true,
                'etat' => 'En cours',
                'published_at' => now(),
                'publish_at' => now()
            ]);

            // CORRECTION : Vérifier l'existence avant la mise à jour
            if ($event->publicationRequest) {
                $event->publicationRequest->update([
                    'status' => 'approved',
                    'rejection_reason' => null,
                    'processed_at' => now()
                ]);
            } else {
                // Créer une nouvelle demande si elle n'existe pas
                EventPublicationRequest::create([
                    'event_id' => $event->id,
                    'user_id' => $event->user_id,
                    'status' => 'approved',
                    'processed_at' => now()
                ]);
            }
        });

        // Envoyer la notification en arrière-plan (après la transaction)
        if ($event->user && class_exists(EventApprovedNotification::class)) {
            try {
                // Utiliser dispatch pour une exécution asynchrone
                dispatch(function() use ($event) {
                    $event->user->notify(new EventApprovedNotification($event));
                })->afterResponse();
            } catch (\Exception $e) {
                Log::warning("Failed to queue approval notification: {$e->getMessage()}");
            }
        }

        // Redirection pour les requêtes normales, JSON pour AJAX
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Événement approuvé avec succès',
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'is_approved' => true,
                    'status' => 'published'
                ]
            ]);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement approuvé avec succès');

    } catch (\Exception $e) {
        Log::error("Error approving event {$eventId}: {$e->getMessage()}", [
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'approbation',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur serveur'
            ], 500);
        }

        return redirect()->route('admin.events.index')
            ->with('error', 'Erreur lors de l\'approbation de l\'événement');
    }
}

public function reject(Request $request, $eventId)
{
    // CORRECTION : Validation plus robuste
    $validated = $request->validate([
        'rejection_reason' => 'required|string|min:5|max:500'
    ], [
        'rejection_reason.required' => 'La raison du rejet est obligatoire',
        'rejection_reason.min' => 'La raison doit contenir au moins 5 caractères',
        'rejection_reason.max' => 'La raison ne peut pas dépasser 500 caractères'
    ]);

    try {
        $event = Event::with(['user', 'category', 'publicationRequest'])->findOrFail($eventId);
        
        // CORRECTION : Vérifier si une demande de publication existe
        if (!$event->publicationRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune demande de publication trouvée pour cet événement'
            ], 400);
        }
        
        // Vérification que l'événement n'est pas déjà rejeté
        if (!$event->is_approved && $event->publicationRequest->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Cet événement est déjà rejeté'
            ], 400);
        }

        DB::transaction(function() use ($event, $validated) {
            // Mise à jour de l'événement
            $event->update([
                'status' => 'draft',
                'is_approved' => false,
                'is_published' => false,
                'etat' => 'En attente'
            ]);

            // CORRECTION : Mise à jour sécurisée
            if ($event->publicationRequest) {
                $event->publicationRequest->update([
                    'status' => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                    'processed_at' => now()
                ]);
            } else {
                // Créer une nouvelle demande si elle n'existe pas
                EventPublicationRequest::create([
                    'event_id' => $event->id,
                    'user_id' => $event->user_id,
                    'status' => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                    'processed_at' => now()
                ]);
            }
        });

        // Envoyer la notification en arrière-plan (après la transaction)
        if ($event->user && class_exists(EventRejectedNotification::class)) {
            try {
                // Utiliser dispatch pour une exécution asynchrone
                $rejectionReason = $validated['rejection_reason'];
                dispatch(function() use ($event, $rejectionReason) {
                    $event->user->notify(new EventRejectedNotification($event, $rejectionReason));
                })->afterResponse();
            } catch (\Exception $e) {
                Log::warning("Failed to queue rejection notification: {$e->getMessage()}");
            }
        }

        // Redirection pour les requêtes normales, JSON pour AJAX
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Événement rejeté avec succès',
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'is_approved' => false,
                    'status' => 'draft'
                ]
            ]);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement rejeté avec succès');

    } catch (\Illuminate\Validation\ValidationException $e) {
        // CORRECTION : Gestion spécifique des erreurs de validation
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
        }
        
        return redirect()->back()->withErrors($e->errors())->withInput();
        
    } catch (\Exception $e) {
        Log::error("Error rejecting event {$eventId}: {$e->getMessage()}", [
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rejet',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur serveur'
            ], 500);
        }

        return redirect()->route('admin.events.index')
            ->with('error', 'Erreur lors du rejet de l\'événement');
    }
}

// CORRECTION : Méthode pour créer automatiquement une demande de publication si manquante
private function ensurePublicationRequest(Event $event)
{
    if (!$event->publicationRequest) {
        return EventPublicationRequest::create([
            'event_id' => $event->id,
            'user_id' => $event->user_id,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    return $event->publicationRequest;
}

    public function pendingApi(Request $request)
    {
        try {
            $publicationRequests = EventPublicationRequest::with([
                'event.category',
                'user.organizerProfile'
            ])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(self::PENDING_EVENTS_PER_PAGE);

            return response()->json([
                'data' => $publicationRequests->map(function($request) {
                    return [
                        'id' => $request->event->id,
                        'title' => $request->event->title,
                        'description' => $request->event->description,
                        'category' => $request->event->category,
                        'user' => $request->user,
                        'start_date' => $request->event->start_date,
                        'end_date' => $request->event->end_date,
                        'location' => $request->event->location,
                        'ville' => $request->event->ville ?? '',
                        'pays' => $request->event->pays ?? '',
                        'status' => $request->event->status,
                        'image' => $request->event->image ? asset('storage/'.$request->event->image) : null,
                        'created_at' => $request->event->created_at,
                        'publication_request' => [
                            'id' => $request->id,
                            'status' => $request->status,
                            'created_at' => $request->created_at,
                            'requested_at' => $request->created_at->diffForHumans()
                        ]
                    ];
                }),
                'meta' => $this->buildPaginationMeta($publicationRequests)
            ]);

        } catch (\Exception $e) {
            Log::error("Error fetching pending events: {$e->getMessage()}");
            return $this->jsonResponse(false, 'Erreur serveur', 500);
        }
    }
    
    public function pending()
    {
        return view('dashboard.admin.events.pending');
    }

    private function validateEventRequest(Request $request, $update = false)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'organizer_id' => 'nullable|exists:users,id',
            'lieu' => 'required|string|max:100',
            'adresse' => 'required|string|max:191',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
            'adresse_map' => 'nullable|string|max:700',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:Payant,Gratuit',
            'event_type' => 'nullable|in:Espace libre,Plan de salle,Mixte',
            'etat' => 'nullable|in:En cours,Archivé,Annulé,En attente',
            'publish_at' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'keywords' => 'nullable|string',
        ];

        if (!$update) {
            $rules['location'] = 'required|string|max:255';
            unset($rules['lieu'], $rules['adresse'], $rules['ville'], $rules['pays'], $rules['adresse_map']);
        }

        return $request->validate($rules);
    }

    private function jsonResponse($success, $message, $status = 200, $additional = [])
    {
        return response()->json(array_merge([
            'success' => $success,
            'message' => $message
        ], $additional), $status);
    }

    private function buildPaginationMeta($paginator)
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'links' => $paginator->links()->elements ?? [],
        ];
    }

    /**
     * Publier un événement
     */
    public function publish(Event $event)
    {
        try {
            $event->update([
                'is_approved' => true,
                'is_published' => true,
                'status' => 'published'
            ]);

            return redirect()->back()->with('success', 'Événement publié avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la publication : ' . $e->getMessage());
        }
    }

    /**
     * Dépublier un événement
     */
    public function unpublish(Event $event)
    {
        try {
            $event->update([
                'is_published' => false,
                'status' => 'Payant' // Retour au statut par défaut
            ]);

            return redirect()->back()->with('success', 'Événement dépublié avec succès');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la dépublication : ' . $e->getMessage());
        }
    }
}