<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Event::with(['category', 'user'])
                ->where('status', 'upcoming')
                ->where('is_approved', true)
                ->orderBy('start_date', 'asc');

            if ($request->has('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            if ($request->has('category')) {
                $query->where('category_id', $request->category);
            }

            $events = $query->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => $event->description,
                        'start_date' => $event->start_date,
                        'end_date' => $event->end_date,
                        'location' => $event->location,
                        'image' => $event->image,
                        'price' => (float) $event->price,
                        'total_tickets' => (int) $event->total_tickets,
                        'available_tickets' => (int) $event->available_tickets,
                        'status' => $event->status,
                        'is_featured' => (bool) $event->is_featured,
                        'category' => $event->category ? [
                            'id' => $event->category->id,
                            'name' => $event->category->name,
                            'image' => $event->category->image,
                        ] : null,
                        'organizer' => $event->user ? [
                            'id' => $event->user->id,
                            'name' => $event->user->name,
                            'email' => $event->user->email,
                            'image' => $event->user->image,
                        ] : null,
                    ];
                });

            return response()->json([
                'status' => true,
                'events' => $events,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in EventController@index: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la récupération des événements',
            ], 500);
        }
    }

    public function show($eventId)
    {
        try {
            $event = Event::with(['category', 'user', 'tickets'])->findOrFail($eventId);

            return response()->json([
                'status' => true,
                'data' => $event
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Événement introuvable'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in EventController@show: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la récupération de l\'événement'
            ], 500);
        }
    }

    /**
     * Récupère les tickets d'un événement
     */
    public function tickets($eventId)
    {
        try {
            $event = Event::with(['tickets'])->findOrFail($eventId);

            $tickets = $event->tickets->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'nom' => $ticket->nom,
                    'prix' => (float) $ticket->prix,
                    'quantite_totale' => (int) $ticket->quantite_totale,
                    'quantite_vendue' => (int) $ticket->quantite_vendue,
                    'quantite_disponible' => (int) ($ticket->quantite_totale - $ticket->quantite_vendue),
                    'description' => $ticket->description,
                    'date_debut_vente' => $ticket->date_debut_vente,
                    'date_fin_vente' => $ticket->date_fin_vente,
                ];
            });

            return response()->json([
                'status' => true,
                'event_id' => $event->id,
                'event_title' => $event->title,
                'tickets' => $tickets
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Événement introuvable'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in EventController@tickets: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la récupération des tickets'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'ticket_types' => 'required|array',
            'ticket_types.*.name' => 'required|string',
            'ticket_types.*.price' => 'required|numeric',
            'ticket_types.*.quantity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Upload image
        $imagePath = $request->file('image')->store('events', 'public');

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
            'image' => $imagePath,
            'status' => 'active',
        ]);

        // Create ticket types
        foreach ($request->ticket_types as $ticketType) {
            $event->ticketTypes()->create([
                'name' => $ticketType['name'],
                'price' => $ticketType['price'],
                'quantity' => $ticketType['quantity'],
                'remaining' => $ticketType['quantity'],
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Event created successfully',
            'data' => $event->load(['category', 'ticketTypes'])
        ], 201);
    }

    public function update(Request $request, Event $event)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($event->user_id !== auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Upload new image if provided
        if ($request->hasFile('image')) {
            // Delete old image
            Storage::disk('public')->delete($event->image);
            // Upload new image
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'category_id' => $request->category_id,
            'image' => $request->hasFile('image') ? $imagePath : $event->image,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Event updated successfully',
            'data' => $event->load(['category', 'ticketTypes'])
        ]);
    }

    public function destroy(Event $event)
    {
        // Vérifier que l'utilisateur est le propriétaire
        if ($event->user_id !== auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete image
        Storage::disk('public')->delete($event->image);

        $event->delete();

        return response()->json([
            'status' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    public function byCategory($categoryId)
    {
        try {
            $category = Category::findOrFail($categoryId);
            
            $events = Event::with(['category', 'user'])
                ->where('category_id', $categoryId)
                ->where('status', 'upcoming')
                ->where('is_approved', true)
                ->orderBy('start_date', 'asc')
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => $event->description,
                        'start_date' => $event->start_date,
                        'end_date' => $event->end_date,
                        'location' => $event->location,
                        'image' => $event->image,
                        'price' => (float) $event->price,
                        'total_tickets' => (int) $event->total_tickets,
                        'available_tickets' => (int) $event->available_tickets,
                        'status' => $event->status,
                        'is_featured' => (bool) $event->is_featured,
                        'category' => $event->category ? [
                            'id' => $event->category->id,
                            'name' => $event->category->name,
                            'image' => $event->category->image,
                        ] : null,
                        'organizer' => $event->user ? [
                            'id' => $event->user->id,
                            'name' => $event->user->name,
                            'email' => $event->user->email,
                            'image' => $event->user->image,
                        ] : null,
                    ];
                });

            return response()->json([
                'status' => true,
                'events' => $events,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in EventController@byCategory: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la récupération des événements',
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            $events = Event::with(['category', 'user'])
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('location', 'like', "%{$query}%");
                })
                ->where('status', 'upcoming')
                ->where('is_approved', true)
                ->orderBy('start_date', 'asc')
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => $event->description,
                        'start_date' => $event->start_date,
                        'end_date' => $event->end_date,
                        'location' => $event->location,
                        'image' => $event->image,
                        'price' => (float) $event->price,
                        'total_tickets' => (int) $event->total_tickets,
                        'available_tickets' => (int) $event->available_tickets,
                        'status' => $event->status,
                        'is_featured' => (bool) $event->is_featured,
                        'category' => $event->category ? [
                            'id' => $event->category->id,
                            'name' => $event->category->name,
                            'image' => $event->category->image,
                        ] : null,
                        'organizer' => $event->user ? [
                            'id' => $event->user->id,
                            'name' => $event->user->name,
                            'email' => $event->user->email,
                            'image' => $event->user->image,
                        ] : null,
                    ];
                });

            return response()->json([
                'status' => true,
                'events' => $events,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in EventController@search: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue lors de la recherche des événements',
            ], 500);
        }
    }

    public function myEvents()
    {
        $events = Event::with(['category', 'ticketTypes'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $events
        ]);
    }
}
