<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories
     */
    public function index()
    {
        $categories = Category::withCount('events')->paginate(15);
        
        // Ajouter le compteur d'événements personnalisés manuellement
        foreach ($categories as $category) {
            $category->custom_events_count = \App\Models\CustomEvent::where('category', $category->name)->count();
        }
        
        return view('dashboard.admin.categories.index', compact('categories'));
    }

    /**
     * Affiche le formulaire de création d'une catégorie
     */
    public function create()
    {
        return view('dashboard.admin.categories.create');
    }

    /**
     * Enregistre une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $filename = 'category_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('categories', $filename, 'public');
                $validated['image'] = $path;
            } catch (\Exception $e) {
                Log::error('Erreur upload image catégorie', [
                    'error' => $e->getMessage()
                ]);
                return back()->with('error', 'Erreur lors de l\'enregistrement de l\'image: ' . $e->getMessage())
                    ->withInput();
            }
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès');
    }

    /**
     * Affiche une catégorie spécifique
     */
    public function show(Category $category)
    {
        $category->load('events');
        $customEvents = \App\Models\CustomEvent::where('category', $category->name)->get();
        return view('dashboard.admin.categories.show', compact('category', 'customEvents'));
    }

    /**
     * Affiche le formulaire d'édition d'une catégorie
     */
    public function edit(Category $category)
    {
        return view('dashboard.admin.categories.edit', compact('category'));
    }

    /**
     * Met à jour une catégorie
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            try {
                // Supprimer l'ancienne image si elle existe
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                $image = $request->file('image');
                $filename = 'category_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('categories', $filename, 'public');
                $validated['image'] = $path;
            } catch (\Exception $e) {
                Log::error('Erreur upload image catégorie', [
                    'error' => $e->getMessage()
                ]);
                return back()->with('error', 'Erreur lors de l\'enregistrement de l\'image: ' . $e->getMessage())
                    ->withInput();
            }
        } else {
            // Conserver l'image existante si aucune nouvelle image n'est fournie
            $validated['image'] = $category->image;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès');
    }

    /**
     * Supprime une catégorie
     */
    public function destroy(Category $category)
    {
        // Vérifier s'il y a des événements associés (normaux ou personnalisés)
        $eventsCount = $category->events()->count();
        $customEventsCount = \App\Models\CustomEvent::where('category', $category->name)->count();
        
        if ($eventsCount > 0 || $customEventsCount > 0) {
            $total = $eventsCount + $customEventsCount;
            return redirect()->route('admin.categories.index')
                ->with('error', "Impossible de supprimer cette catégorie car elle contient {$total} événement(s) ({$eventsCount} normal(s) et {$customEventsCount} personnalisé(s))");
        }

        // Supprimer l'image si elle existe
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès');
    }
}

