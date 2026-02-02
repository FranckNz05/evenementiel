<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Affiche la liste des articles de blog
     */
    public function index(Request $request)
    {
        $query = Blog::with(['user', 'blogcategories']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Tri
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);

        $blogs = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json($blogs);
        }

        $blog_categories = BlogCategories::withCount('blogs')->get();
        return view('dashboard.admin.blogs.index', compact('blogs', 'blog_categories'));
    }

    /**
     * Affiche le formulaire de création d'un article
     */
    public function create()
    {
        $blog_categories = BlogCategories::all();
        return view('dashboard.admin.blogs.create', compact('blog_categories'));
    }

    /**
     * Enregistre un nouvel article
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blogs', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();

        $blog = Blog::create($validated);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Article créé avec succès');
    }

    /**
     * Affiche un article spécifique
     */
    public function show(Blog $blog)
    {
        $blog->load(['user', 'blogcategories']);
        return view('dashboard.admin.blogs.show', compact('blog'));
    }

    /**
     * Affiche le formulaire d'édition d'un article
     */
    public function edit(Blog $blog)
    {
        $blog_categories = BlogCategories::all();
        return view('dashboard.admin.blogs.edit', compact('blog', 'blog_categories'));
    }

    /**
     * Met à jour un article
     */
    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }

            $imagePath = $request->file('image')->store('blogs', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['slug'] = Str::slug($validated['title']);

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Article mis à jour avec succès');
    }

    /**
     * Supprime un article
     */
    public function destroy(Blog $blog)
    {
        // Supprimer l'image si elle existe
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Article supprimé avec succès');
    }
    /**
     * Récupère les blogs pour l'API du tableau de bord
     */
    public function getBlogs(Request $request)
    {
        $query = Blog::with(['user', 'blogcategories'])
                    ->withCount(['likes', 'comments'])
                    ->select([
                        'id',
                        'title',
                        'slug',
                        'content',
                        'image',
                        'user_id',
                        'category_id',
                        'created_at',
                        'updated_at'
                    ]);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filtre par catégorie
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Tri
        $sort = $request->input('sort', 'created_at');
        switch ($sort) {
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', 'desc');
        }

        $blogs = $query->paginate(10);

        return response()->json([
            'data' => $blogs->items(),
            'current_page' => $blogs->currentPage(),
            'last_page' => $blogs->lastPage(),
            'total' => $blogs->total()
        ]);
    }
}





