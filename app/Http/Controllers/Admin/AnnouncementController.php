<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query();

        if ($request->has('status')) {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->orderBy('display_order')->paginate(10);

        return view('dashboard.admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('dashboard.admin.announcements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'display_order' => 'required|integer|min:0',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'is_active' => 'sometimes|boolean',
        'is_urgent' => 'sometimes|boolean'
    ]);

    // Convertir les dates vides en null
    $validated['start_date'] = $validated['start_date'] ?: null;
    $validated['end_date'] = $validated['end_date'] ?: null;

    // Gérer les cases à cocher
    $validated['is_active'] = $request->has('is_active');
    $validated['is_urgent'] = $request->has('is_urgent');

    try {
        Announcement::create($validated);
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Annonce créée avec succès.');
    } catch (\Exception $e) {
        logger()->error('Erreur création annonce: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Une erreur est survenue lors de la création.')
            ->withInput();
    }
}

    public function edit(Announcement $announcement)
    {
        return view('dashboard.admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'display_order' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
            'is_urgent' => 'sometimes|boolean'
        ]);

        // Gérer les cases à cocher
        $validated['is_active'] = $request->has('is_active');
        $validated['is_urgent'] = $request->has('is_urgent');

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Annonce mise à jour avec succès.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Annonce supprimée avec succès.');
    }

    public function toggleStatus(Request $request, Announcement $announcement)
    {
        try {
            $announcement->update([
                'is_active' => !$announcement->is_active
            ]);

            // Si c'est une requête AJAX, retourner une réponse JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Statut de l\'annonce modifié avec succès.',
                    'is_active' => $announcement->is_active,
                    'status_text' => $announcement->is_active ? 'Active' : 'Inactive',
                    'status_class' => $announcement->is_active ? 'badge-success' : 'badge-danger',
                    'button_class' => $announcement->is_active ? 'btn-warning' : 'btn-success',
                    'button_icon' => $announcement->is_active ? 'fa-ban' : 'fa-check'
                ]);
            }

            return redirect()->route('admin.announcements.index')
                ->with('success', 'Statut de l\'annonce modifié avec succès.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la modification du statut.'
                ], 500);
            }

            return redirect()->route('admin.announcements.index')
                ->with('error', 'Erreur lors de la modification du statut.');
        }
    }
}


