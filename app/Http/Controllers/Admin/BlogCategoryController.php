<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategories as BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    /**
     * Affiche la liste des catégories de blog
     */
    public function index()
    {
        $categories = BlogCategory::withCount('blogs')->paginate(15);
        return view('dashboard.admin.blog-categories.index', compact('categories'));
    }

    /**
     * Affiche le formulaire de création d'une catégorie
     */
    public function create()
    {
        return view('dashboard.admin.blog-categories.create');
    }

    /**
     * Enregistre une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        BlogCategory::create($validated);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Catégorie créée avec succès');
    }

    /**
     * Affiche une catégorie spécifique
     */
    public function show(BlogCategory $blogCategory)
    {
        $blogCategory->load('blogs');
        return view('dashboard.admin.blog-categories.show', compact('blogCategory'));
    }

    /**
     * Affiche le formulaire d'édition d'une catégorie
     */
    public function edit(BlogCategory $blogCategory)
    {
        return view('dashboard.admin.blog-categories.edit', compact('blogCategory'));
    }

    /**
     * Met à jour une catégorie
     */
    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $blogCategory->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $blogCategory->update($validated);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Catégorie mise à jour avec succès');
    }

    /**
     * Supprime une catégorie
     */
    public function destroy(BlogCategory $blogCategory)
    {
        // Vérifier s'il y a des blogs associés
        if ($blogCategory->blogs()->count() > 0) {
            return redirect()->route('admin.blog-categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des articles de blog');
        }

        $blogCategory->delete();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Catégorie supprimée avec succès');
    }
}
