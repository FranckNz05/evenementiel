<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()->bookings()
            ->with(['event', 'user'])
            ->latest()
            ->paginate(10);
            
        return view('organizer.bookings.index', compact('bookings'));
    }
    
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        
        $booking->load(['event', 'user']);
        
        return view('organizer.bookings.show', compact('booking'));
    }
    
    public function updateStatus(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);
        
        $booking->update([
            'status' => $request->status
        ]);
        
        return back()->with('success', 'Statut de la réservation mis à jour avec succès.');
    }
    
    public function checkIn(Booking $booking)
    {
        $this->authorize('update', $booking);
        
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Seules les réservations confirmées peuvent être validées.');
        }
        
        $booking->update([
            'status' => 'completed',
            'checked_in_at' => now()
        ]);
        
        return back()->with('success', 'Réservation validée avec succès.');
    }
    
    public function export(Event $event)
    {
        $this->authorize('view', $event);
        
        $bookings = $event->bookings()
            ->with('user')
            ->get();
            
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings_' . $event->slug . '.csv"',
        ];
        
        $callback = function() use($bookings) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'ID',
                'Nom',
                'Email',
                'Statut',
                'Prix total',
                'Date de réservation',
                'Date de validation'
            ]);
            
            // Données
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->id,
                    $booking->user->name,
                    $booking->user->email,
                    $booking->status,
                    $booking->total_price,
                    $booking->created_at->format('d/m/Y H:i'),
                    $booking->checked_in_at ? $booking->checked_in_at->format('d/m/Y H:i') : ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
} 