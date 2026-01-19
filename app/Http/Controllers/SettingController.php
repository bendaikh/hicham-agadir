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

    /**
     * Update business information (name and logo)
     */
    public function updateBusiness(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_logo' => 'nullable|boolean',
        ]);

        // Update business name
        if (isset($validated['business_name'])) {
            Setting::setValue(
                'business_name',
                $validated['business_name'],
                'Nom de l\'entreprise',
                'Nom de l\'entreprise affiché sur les factures'
            );
        }

        // Handle logo
        if ($request->hasFile('business_logo')) {
            $logo = $request->file('business_logo');
            $logoPath = $logo->store('business', 'public');
            
            // Delete old logo if exists
            $oldLogo = Setting::getValue('business_logo', '');
            if ($oldLogo && \Storage::disk('public')->exists($oldLogo)) {
                \Storage::disk('public')->delete($oldLogo);
            }
            
            Setting::setValue(
                'business_logo',
                $logoPath,
                'Logo de l\'entreprise',
                'Logo de l\'entreprise affiché sur les factures'
            );
        } elseif ($request->has('remove_logo') && $request->remove_logo) {
            // Remove logo
            $oldLogo = Setting::getValue('business_logo', '');
            if ($oldLogo && \Storage::disk('public')->exists($oldLogo)) {
                \Storage::disk('public')->delete($oldLogo);
            }
            Setting::where('key', 'business_logo')->delete();
        }

        return redirect()->route('settings.index')
            ->with('success', 'Informations de l\'entreprise mises à jour avec succès.');
    }
}
