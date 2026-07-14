<?php

namespace App\Http\Controllers;

use App\Models\Plat;
use App\Models\Categorie;
use App\Models\Accompagnement;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlatController extends Controller
{
    /**
     * Display a listing of plats.
     */
    public function index()
    {
        $plats = Plat::with(['categorie', 'accompagnements', 'options'])->orderBy('nom')->get();
        $categories = Categorie::where('est_disponible', true)->orderBy('nom')->get();
        
        return view('admin.menu.plats.index', compact('plats', 'categories'));
    }

    /**
     * Store a newly created plat.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'detail' => 'nullable|string',
            'categorie_id' => 'required|exists:categories,id',
            'est_disponible' => 'nullable|boolean',
            'accompagnements' => 'nullable|array',
            'accompagnements.*.nom' => 'required|string|max:255',
            'accompagnements.*.qte_gratuit' => 'required|integer|min:0',
            'accompagnements.*.prix_unitaire' => 'required|numeric|min:0',
            'accompagnements.*.disponible' => 'nullable|boolean',
            'options' => 'nullable|array',
            'options.*.nom' => 'required|string|max:255',
            'options.*.qte_gratuit' => 'required|integer|min:0',
            'options.*.prix_unitaire' => 'required|numeric|min:0',
            'options.*.disponible' => 'nullable|boolean',
        ], [
            'nom.required' => 'Le nom du plat est obligatoire.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format: jpeg, png, jpg, gif ou webp.',
            'image.max' => 'La taille de l\'image ne peut pas dépasser 5 Mo.',
            'categorie_id.required' => 'La catégorie est obligatoire.',
            'categorie_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'qualite.in' => 'La qualité doit être: classic, pro ou premium.',
        ]);

        $data = [
            'nom' => $validated['nom'],
            'prix' => $validated['prix'],
            'detail' => $validated['detail'] ?? null,
            'categorie_id' => $validated['categorie_id'],
            'est_disponible' => $request->has('est_disponible') ? true : false,
            'qualite' => $validated['qualite'] ?? 'classic',
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                $imagePath = $request->file('image')->store('plats', 'public');
                $data['image'] = $imagePath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage())
                    ->withInput();
            }
        }

        $plat = Plat::create($data);

        // Create accompagnements
        if ($request->has('accompagnements')) {
            foreach ($request->accompagnements as $accompagnement) {
                Accompagnement::create([
                    'nom' => $accompagnement['nom'],
                    'qte_gratuit' => $accompagnement['qte_gratuit'],
                    'prix_unitaire' => $accompagnement['prix_unitaire'],
                    'disponible' => isset($accompagnement['disponible']) ? true : false,
                    'plat_id' => $plat->id,
                ]);
            }
        }

        // Create options
        if ($request->has('options')) {
            foreach ($request->options as $option) {
                Option::create([
                    'nom' => $option['nom'],
                    'qte_gratuit' => $option['qte_gratuit'],
                    'prix_unitaire' => $option['prix_unitaire'],
                    'disponible' => isset($option['disponible']) ? true : false,
                    'plat_id' => $plat->id,
                ]);
            }
        }

        return redirect()->route('admin.menu.plats.index')
            ->with('success', 'Plat créé avec succès avec ' . 
                ($request->has('accompagnements') ? count($request->accompagnements) : 0) . ' accompagnement(s) et ' . 
                ($request->has('options') ? count($request->options) : 0) . ' option(s).');
    }

    /**
     * Update the specified plat.
     */
    public function update(Request $request, Plat $plat)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'detail' => 'nullable|string',
            'categorie_id' => 'required|exists:categories,id',
            'est_disponible' => 'nullable|boolean',
            'qualite' => 'nullable|in:classic,pro,premium',
            'accompagnements' => 'nullable|array',
            'accompagnements.*.nom' => 'required|string|max:255',
            'accompagnements.*.qte_gratuit' => 'required|integer|min:0',
            'accompagnements.*.prix_unitaire' => 'required|numeric|min:0',
            'accompagnements.*.disponible' => 'nullable|boolean',
            'options' => 'nullable|array',
            'options.*.nom' => 'required|string|max:255',
            'options.*.qte_gratuit' => 'required|integer|min:0',
            'options.*.prix_unitaire' => 'required|numeric|min:0',
            'options.*.disponible' => 'nullable|boolean',
        ], [
            'nom.required' => 'Le nom du plat est obligatoire.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format: jpeg, png, jpg, gif ou webp.',
            'image.max' => 'La taille de l\'image ne peut pas dépasser 5 Mo.',
            'categorie_id.required' => 'La catégorie est obligatoire.',
            'categorie_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'qualite.in' => 'La qualité doit être: classic, pro ou premium.',
        ]);

        $data = [
            'nom' => $validated['nom'],
            'prix' => $validated['prix'],
            'detail' => $validated['detail'] ?? null,
            'categorie_id' => $validated['categorie_id'],
            'est_disponible' => $request->has('est_disponible') ? true : false,
            'qualite' => $validated['qualite'] ?? $plat->qualite ?? 'classic',
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                // Delete old image if exists
                if ($plat->image) {
                    Storage::disk('public')->delete($plat->image);
                }
                
                $imagePath = $request->file('image')->store('plats', 'public');
                $data['image'] = $imagePath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage())
                    ->withInput();
            }
        }

        $plat->update($data);

        // Add new accompagnements (existing ones remain via the extras panel)
        if ($request->has('accompagnements')) {
            foreach ($request->accompagnements as $accompagnement) {
                Accompagnement::create([
                    'nom' => $accompagnement['nom'],
                    'qte_gratuit' => $accompagnement['qte_gratuit'],
                    'prix_unitaire' => $accompagnement['prix_unitaire'],
                    'disponible' => isset($accompagnement['disponible']) ? true : false,
                    'plat_id' => $plat->id,
                ]);
            }
        }

        // Add new options (existing ones remain via the extras panel)
        if ($request->has('options')) {
            foreach ($request->options as $option) {
                Option::create([
                    'nom' => $option['nom'],
                    'qte_gratuit' => $option['qte_gratuit'],
                    'prix_unitaire' => $option['prix_unitaire'],
                    'disponible' => isset($option['disponible']) ? true : false,
                    'plat_id' => $plat->id,
                ]);
            }
        }

        $message = 'Plat modifié avec succès';
        if ($request->has('accompagnements') || $request->has('options')) {
            $message .= ' avec ' . 
                ($request->has('accompagnements') ? count($request->accompagnements) : 0) . ' nouvel(le) accompagnement(s) et ' . 
                ($request->has('options') ? count($request->options) : 0) . ' nouvelle(s) option(s)';
        }

        return redirect()->route('admin.menu.plats.index')
            ->with('success', $message . '.');
    }

    /**
     * Toggle availability of the plat.
     */
    public function toggle(Plat $plat)
    {
        $plat->update([
            'est_disponible' => !$plat->est_disponible
        ]);

        return redirect()->back()
            ->with('success', 'Statut du plat mis à jour.');
    }

    /**
     * Remove the specified plat.
     */
    public function destroy(Plat $plat)
    {
        // Delete image if exists
        if ($plat->image) {
            Storage::disk('public')->delete($plat->image);
        }

        // Delete associated accompagnements and options images
        foreach ($plat->accompagnements as $accompagnement) {
            if ($accompagnement->image) {
                Storage::disk('public')->delete($accompagnement->image);
            }
        }

        foreach ($plat->options as $option) {
            if ($option->image) {
                Storage::disk('public')->delete($option->image);
            }
        }

        $plat->delete();

        return redirect()->route('admin.menu.plats.index')
            ->with('success', 'Plat supprimé avec succès.');
    }

    /**
     * Store accompagnement for a plat.
     */
    public function storeAccompagnement(Request $request, Plat $plat)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'qte_gratuit' => 'required|integer|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'disponible' => 'nullable|boolean',
        ]);

        $data = [
            'nom' => $validated['nom'],
            'qte_gratuit' => $validated['qte_gratuit'],
            'prix_unitaire' => $validated['prix_unitaire'],
            'disponible' => $request->has('disponible') ? true : false,
            'plat_id' => $plat->id,
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('accompagnements', 'public');
            $data['image'] = $imagePath;
        }

        Accompagnement::create($data);

        return redirect()->back()->with('success', 'Accompagnement ajouté avec succès.');
    }

    /**
     * Store option for a plat.
     */
    public function storeOption(Request $request, Plat $plat)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'qte_gratuit' => 'required|integer|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'disponible' => 'nullable|boolean',
        ]);

        $data = [
            'nom' => $validated['nom'],
            'qte_gratuit' => $validated['qte_gratuit'],
            'prix_unitaire' => $validated['prix_unitaire'],
            'disponible' => $request->has('disponible') ? true : false,
            'plat_id' => $plat->id,
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('options', 'public');
            $data['image'] = $imagePath;
        }

        Option::create($data);

        return redirect()->back()->with('success', 'Option ajoutée avec succès.');
    }

    /**
     * Delete accompagnement.
     */
    public function destroyAccompagnement(Accompagnement $accompagnement)
    {
        if ($accompagnement->image) {
            Storage::disk('public')->delete($accompagnement->image);
        }

        $accompagnement->delete();

        return redirect()->back()->with('success', 'Accompagnement supprimé avec succès.');
    }

    /**
     * Delete option.
     */
    public function destroyOption(Option $option)
    {
        if ($option->image) {
            Storage::disk('public')->delete($option->image);
        }

        $option->delete();

        return redirect()->back()->with('success', 'Option supprimée avec succès.');
    }
}
