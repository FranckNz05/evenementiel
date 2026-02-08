<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StockCasiersController extends Controller
{
    /**
     * Display a listing of products and the creation form.
     */
    public function index()
    {
        $products = Product::latest()->get();
        return view('stock.casiers.index', compact('products'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bottles_per_crate' => 'required|integer|min:1|max:100',
        ]);

        Product::create($validated);

        return redirect()->route('stock.casiers.index')
            ->with('success', 'Produit ajouté avec succès.');
    }

    /**
     * Display the printable grid for all products.
     */
    public function print()
    {
        $products = Product::orderBy('name')->get();
        return view('stock.casiers.print', compact('products'));
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('stock.casiers.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
}
