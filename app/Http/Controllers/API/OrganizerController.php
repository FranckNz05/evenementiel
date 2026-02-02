<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use Illuminate\Support\Facades\Storage;

class OrganizerController extends Controller
{
    public function index()
    {
        $organizers = Organizer::withCount(['events'])
            ->orderByDesc('events_count')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $organizers->map(function($organizer) {
                return [
                    'id' => $organizer->id,
                    'company_name' => $organizer->company_name,
                    'logo' => $organizer->logo ? Storage::url($organizer->logo) : null,
                    'logo_url' => $organizer->logo ? Storage::url($organizer->logo) : null,
                    'email' => $organizer->email,
                    'phone' => $organizer->phone,
                    'description' => $organizer->description,
                    'events_count' => $organizer->events_count,
                ];
            })->values(),
        ]);
    }

    public function topOrganizers()
    {
        $organizers = Organizer::withCount(['events', 'followers'])
            ->whereHas('events', function($query) {
                $query->where('is_published', true)
                      ->where('is_approved', true);
            })
            ->where('is_verified', true)
            ->orderByDesc('events_count')
            ->limit(6)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $organizers
        ]);
    }
}

