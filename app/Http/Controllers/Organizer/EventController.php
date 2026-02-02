<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $organizer = $user->organizer;

        $events = $organizer
            ? $organizer->events()
                ->with(['category', 'tickets'])
                ->latest()
                ->paginate(10)
            : collect();

        // Récupérer les événements personnalisés de l'utilisateur
        $customEvents = \App\Models\CustomPersonalEvent::where('organizer_id', $user->id)
            ->with('guests')
            ->latest()
            ->get();

        return view('organizer.events.index', compact('events', 'customEvents'));
    }

    /**
     * Affiche les détails d'un événement spécifique
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\View\View
     */
    public function show(Event $event)
    {
        // Vérifier que l'utilisateur est bien l'organisateur de l'événement
        $this->authorize('view', $event);

        // Charger les relations nécessaires
        $event->load(['category', 'tickets']);

        return view('dashboard.organizer.events.show', compact('event'));
    }

    /**
     * Affiche la page de sélection du type d'événement
     * Permet aux clients de créer des événements personnalisés
     */
    public function selectType()
    {
        // Tous les utilisateurs authentifiés peuvent accéder à cette page
        // La vue gérera l'affichage selon le rôle
        return view('organizer.events.select-type');
    }
    
    /**
     * Affiche le formulaire de création d'un événement standard pour les organisateurs
     * Redirige selon le rôle :
     * - Clients : vers la sélection des offres (événements personnalisés uniquement)
     * - Organisateurs/Admin : vers la sélection du type d'événement
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Récupérer les rôles réels de l'utilisateur
        $userRoles = $user->getRoleNames()->toArray();
        $roleIds = $user->roles->pluck('id')->toArray();
        
        // Vérifier si l'utilisateur est organisateur ou admin (rôle 2 ou 3)
        // Par défaut, si l'utilisateur n'a pas de rôle 2 ou 3, c'est un client
        $isOrganizer = in_array(3, $roleIds) || in_array(2, $roleIds);
        
        // Si pas de rôle par ID, vérifier par nom
        if (!$isOrganizer) {
            $isOrganizer = in_array('Administrateur', $userRoles) || 
                          in_array('administrateur', array_map('strtolower', $userRoles)) ||
                          in_array('Organizer', $userRoles) || 
                          in_array('organizer', array_map('strtolower', $userRoles)) ||
                          $user->isOrganizer() || 
                          $user->isAdmin();
        }
        
        \Log::info('EventController@create - User role check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'roles' => $userRoles,
            'role_ids' => $roleIds,
            'isOrganizer' => $isOrganizer,
            'request_url' => $request->fullUrl(),
        ]);
        
        if ($isOrganizer) {
            // Organisateurs et admins : choix entre événement simple et personnalisé
            \Log::info('Redirecting organizer to select-type');
            return redirect()->route('events.select-type');
        } else {
            // Clients : directement vers la sélection des offres pour événement personnalisé
            $redirectUrl = url('/evenements-personnalises');
            \Log::info('Redirecting client to custom-offers.index', [
                'route_exists' => \Route::has('custom-offers.index'),
                'route_url' => $redirectUrl,
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);
            // Redirection immédiate vers la sélection des offres
            return redirect($redirectUrl);
        }
    }

    /**
     * Enregistre un nouvel événement créé par un organisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'event_type' => 'required|string',
        ]);

        // Traitement de l'image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        // Ajout des informations supplémentaires
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();
        $validated['organizer_id'] = auth()->user()->organizer->id ?? null;
        $validated['status'] = 'draft'; // Par défaut en brouillon
        
        // Création de l'événement
        $event = \App\Models\Event::create($validated);

        // Traitement des billets si nécessaire
        if ($request->has('tickets')) {
            foreach ($request->tickets as $ticketData) {
                $event->tickets()->create([
                    'nom' => $ticketData['name'],
                    'description' => $ticketData['description'] ?? null,
                    'prix' => $ticketData['price'],
                    'quantite' => $ticketData['quantity'],
                    'date_debut_vente' => $ticketData['sale_start_date'] ?? $event->start_date,
                    'date_fin_vente' => $ticketData['sale_end_date'] ?? $event->end_date,
                ]);
            }
        }

        return redirect()->route('organizer.events.index')
            ->with('success', 'Événement créé avec succès. Il sera publié après validation par un admin.');
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $categories = Category::all();
        $event->load('tickets');

        return view('organizer.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tickets' => 'required|array|min:1',
            'tickets.*.name' => 'required|string|max:255',
            'tickets.*.price' => 'required|numeric|min:0',
            'tickets.*.quantity' => 'required|integer|min:1',
            'tickets.*.description' => 'nullable|string',
            'tickets.*.start_sale_date' => 'required|date|before:end_sale_date',
            'tickets.*.end_sale_date' => 'required|date|after:start_sale_date|before:start_date'
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'category_id' => $request->category_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location
        ];

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($event->image);
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        // Mise à jour des billets
        $event->tickets()->delete();
        foreach ($request->tickets as $ticketData) {
            $event->tickets()->create($ticketData);
        }

        return redirect()->route('organizer.events.index')
            ->with('success', 'Événement mis à jour avec succès.');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        Storage::disk('public')->delete($event->image);
        $event->delete();

        return redirect()->route('organizer.events.index')
            ->with('success', 'Événement supprimé avec succès.');
    }

    public function publish(Event $event)
    {
        $this->authorize('update', $event);

        $event->update(['status' => 'published']);

        return back()->with('success', 'Événement publié avec succès.');
    }

    public function unpublish(Event $event)
    {
        $this->authorize('update', $event);

        $event->update(['status' => 'draft']);

        return back()->with('success', 'Événement retiré de la publication.');
    }
}

