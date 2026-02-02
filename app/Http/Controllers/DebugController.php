<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Payment;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function checkEventImages()
    {
        $events = Event::whereNotNull('image')->take(5)->get(['id', 'title', 'image']);
        
        $result = [];
        foreach ($events as $event) {
            $imagePath = $event->image;
            $paths = [
                'original' => $imagePath,
                'storage/app/public' => storage_path('app/public/' . $imagePath),
                'public/storage' => public_path('storage/' . $imagePath),
                'public' => public_path($imagePath),
            ];
            
            $found = [];
            foreach ($paths as $label => $path) {
                if ($label !== 'original' && file_exists($path)) {
                    $found[] = $label . ': ' . $path;
                }
            }
            
            $result[] = [
                'id' => $event->id,
                'title' => $event->title,
                'image_path' => $imagePath,
                'is_url' => filter_var($imagePath, FILTER_VALIDATE_URL) ? 'Oui' : 'Non',
                'found_in' => !empty($found) ? implode(', ', $found) : 'Introuvable',
            ];
        }
        
        return response()->json([
            'events' => $result,
            'storage_path' => storage_path('app/public/'),
            'public_path' => public_path('storage/'),
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    public function checkPaymentImages($paymentId)
    {
        $payment = Payment::with(['order.event'])->find($paymentId);
        
        if (!$payment || !$payment->order || !$payment->order->event) {
            return response()->json(['error' => 'Paiement ou événement introuvable'], 404);
        }
        
        $event = $payment->order->event;
        $imagePath = $event->image;
        
        $paths = [
            'storage/app/public/' . $imagePath => file_exists(storage_path('app/public/' . $imagePath)),
            'public/storage/' . $imagePath => file_exists(public_path('storage/' . $imagePath)),
            'public/' . $imagePath => file_exists(public_path($imagePath)),
        ];
        
        return response()->json([
            'payment_id' => $paymentId,
            'event_id' => $event->id,
            'event_title' => $event->title,
            'image_path' => $imagePath,
            'is_url' => filter_var($imagePath, FILTER_VALIDATE_URL),
            'paths_checked' => $paths,
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}

