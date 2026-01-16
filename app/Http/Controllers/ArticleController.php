<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::latest()->paginate(15);
        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255|unique:articles',
            'designation' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'thickness' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'unit' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            'surface_area' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['stock_quantity'] = 0;
        $validated['is_active'] = true;

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
            $validated['image'] = $imagePath;
        }

        Article::create($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Article créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255|unique:articles,reference,' . $article->id,
            'designation' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'thickness' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'unit' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            'surface_area' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($article->image && Storage::disk('public')->exists($article->image)) {
                Storage::disk('public')->delete($article->image);
            }
            
            $imagePath = $request->file('image')->store('articles', 'public');
            $validated['image'] = $imagePath;
        }

        $article->update($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Delete image if exists
        if ($article->image && Storage::disk('public')->exists($article->image)) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Article supprimé avec succès.');
    }
}
