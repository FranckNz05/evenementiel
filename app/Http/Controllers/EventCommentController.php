<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Store a newly created comment
     */
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = $event->comments()->create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Commentaire ajouté avec succès',
                'comment' => $comment->load('user')
            ], 201);
        }

        return back()->with('success', 'Commentaire ajouté avec succès');
    }

    /**
     * Remove the specified comment
     */
    public function destroy(Event $event, Comment $comment)
    {
        // Vérifier que l'utilisateur peut supprimer ce commentaire
        if ($comment->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire'
                ], 403);
            }
            abort(403);
        }

        $comment->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Commentaire supprimé avec succès'
            ]);
        }

        return back()->with('success', 'Commentaire supprimé avec succès');
    }
}
