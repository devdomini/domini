<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategorieController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Categorie::orderBy('nom')->get();
        return view('admin.menu.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'est_disponible' => 'nullable|boolean',
        ], [
            'nom.required' => 'Le nom de la catégorie est obligatoire.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.mimes' => 'Le logo doit être au format: jpeg, png, jpg, gif ou webp.',
            'logo.max' => 'La taille du logo ne peut pas dépasser 5 Mo.',
        ]);

        $data = [
            'nom' => $validated['nom'],
            'est_disponible' => $request->has('est_disponible') ? true : false,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                $logoPath = $request->file('logo')->store('categories', 'public');
                $data['logo'] = $logoPath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Erreur lors de l\'upload du logo: ' . $e->getMessage())
                    ->withInput();
            }
        }

        Categorie::create($data);

        return redirect()->route('admin.menu.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Categorie $categorie)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'est_disponible' => 'nullable|boolean',
        ], [
            'nom.required' => 'Le nom de la catégorie est obligatoire.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.mimes' => 'Le logo doit être au format: jpeg, png, jpg, gif ou webp.',
            'logo.max' => 'La taille du logo ne peut pas dépasser 5 Mo.',
        ]);

        $data = [
            'nom' => $validated['nom'],
            'est_disponible' => $request->has('est_disponible') ? true : false,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                // Delete old logo if exists
                if ($categorie->logo) {
                    Storage::disk('public')->delete($categorie->logo);
                }
                
                $logoPath = $request->file('logo')->store('categories', 'public');
                $data['logo'] = $logoPath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Erreur lors de l\'upload du logo: ' . $e->getMessage())
                    ->withInput();
            }
        }

        $categorie->update($data);

        return redirect()->route('admin.menu.categories.index')
            ->with('success', 'Catégorie modifiée avec succès.');
    }

    /**
     * Toggle the availability of the category.
     */
    public function toggle(Categorie $categorie)
    {
        $categorie->update([
            'est_disponible' => !$categorie->est_disponible
        ]);

        return redirect()->back()
            ->with('success', 'Statut de la catégorie mis à jour.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Categorie $categorie)
    {
        // Check if category has plats
        if ($categorie->plats()->exists()) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des plats.');
        }

        // Delete logo if exists
        if ($categorie->logo) {
            Storage::disk('public')->delete($categorie->logo);
        }

        $categorie->delete();

        return redirect()->route('admin.menu.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}
