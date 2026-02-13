<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\Billet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les statistiques de l'utilisateur
        $stats = [
            'tickets' => $user->tickets()->count(),
            'events_attended' => $user->tickets()->distinct('event_id')->count(),
            'comments' => $user->comments()->count(),
            'likes' => $user->likes()->count(),
        ];

        // Récupérer les reservations de l'utilisateur
        $orders = $user->orders;

        return view('user.dashboard', compact('stats', 'orders'));
    }
}
