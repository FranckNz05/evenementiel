<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use App\Models\Organizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Événements populaires (basés sur les vues)
        $popularEvents = Event::withCount('views')
            ->with(['category', 'organizer'])
            ->where('start_date', '>=', now())
            ->where('is_published', true)
            ->where('is_approved', true)
            ->orderByDesc('views_count')
            ->limit(8)
            ->get();

        // Organisateurs actifs
        $organizers = Organizer::withCount('events')
            ->whereHas('events', function($query) {
                $query->where('start_date', '>=', now())
                      ->where('is_published', true)
                      ->where('is_approved', true);
            })
            ->where('is_verified', true)
            ->orderByDesc('events_count')
            ->limit(10)
            ->get();

        // Catégories pour les filtres
        $categories = Category::orderBy('name')->get();

        return view('home', compact('popularEvents', 'organizers', 'categories'));
    }

    public function show(Event $event)
    {
        // ...
        $similarEvents = Event::where('id', '!=', $event->id)
            ->where('category_id', $event->category_id)
            ->where('start_date', '>=', now())
            ->where('is_published', true)
            ->where('is_approved', true)
            ->orderBy('start_date')
            ->limit(3)
            ->get();
        // ...
        return view('events.show', compact('event', 'similarEvents', 'isLiked', 'isFavorite'));
    }
}
