<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display application settings
     */
    public function index()
    {
        $categories = Setting::getArticleCategories();
        $types = Setting::getArticleTypes();
        
        return view('settings.index', compact('categories', 'types'));
    }

    /**
     * Update article categories
     */
    public function updateCategories(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|string|max:255',
        ]);

        Setting::setValue(
            'article_categories',
            $validated['categories'],
            'Catégories d\'articles',
            'Liste des catégories disponibles pour les articles'
        );

        return redirect()->route('settings.index')
            ->with('success', 'Catégories mises à jour avec succès.');
    }

    /**
     * Update article types
     */
    public function updateTypes(Request $request)
    {
        $validated = $request->validate([
            'types' => 'required|array|min:1',
            'types.*' => 'required|string|max:255',
        ]);

        Setting::setValue(
            'article_types',
            $validated['types'],
            'Types d\'articles',
            'Liste des types disponibles pour les articles'
        );

        return redirect()->route('settings.index')
            ->with('success', 'Types mis à jour avec succès.');
    }
}
