<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EntrepriseController extends Controller
{
    /**
     * Afficher la liste des entreprises
     */
    public function index()
    {
        $entreprises = Entreprise::with(['employes', 'commune.warehouse'])->orderBy('created_at', 'desc')->paginate(10);
        $totalEmployes = User::where('role', 'employe')->count();
        
        return view('admin.entreprises.index', compact('entreprises', 'totalEmployes'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $communes = Commune::with('warehouse')->orderBy('nom')->get();
        $commerciaux = User::query()
            ->where('role', 'commercial')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $googleMapsApiKey = config('services.google_maps.api_key');

        return view('admin.entreprises.create', compact('communes', 'commerciaux', 'googleMapsApiKey'));
    }

    /**
     * Enregistrer une nouvelle entreprise
     */
    public function store(Request $request)
    {
        $this->normalizeGpsCoordinatesOnRequest($request);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:255',
            'pays' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'lat' => 'nullable|numeric|between:-90,90',
            'long' => 'nullable|numeric|between:-180,180',
            'commune_id' => 'nullable|exists:communes,id',
            'commercial_id' => 'nullable|exists:users,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nom.required' => 'Le nom de l\'entreprise est requis.',
            'adresse.required' => 'L\'adresse est requise.',
            'ville.required' => 'La ville est requise.',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.max' => 'Le logo ne doit pas dépasser 2MB.',
        ]);

        // Upload du logo
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $validated['statut'] = $request->has('statut');

        if (empty($validated['commercial_id'])) {
            $validated['commercial_id'] = null;
        }

        Entreprise::create($validated);

        return redirect()
            ->route('admin.entreprises.index')
            ->with('success', 'Entreprise créée avec succès !');
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit($id)
    {
        $entreprise = Entreprise::findOrFail($id);
        $communes = Commune::with('warehouse')->orderBy('nom')->get();
        $commerciaux = User::query()
            ->where('role', 'commercial')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $googleMapsApiKey = config('services.google_maps.api_key');

        return view('admin.entreprises.edit', compact('entreprise', 'communes', 'commerciaux', 'googleMapsApiKey'));
    }

    /**
     * Mettre à jour une entreprise
     */
    public function update(Request $request, $id)
    {
        $entreprise = Entreprise::findOrFail($id);

        $this->normalizeGpsCoordinatesOnRequest($request);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:255',
            'pays' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'lat' => 'nullable|numeric|between:-90,90',
            'long' => 'nullable|numeric|between:-180,180',
            'commune_id' => 'nullable|exists:communes,id',
            'commercial_id' => 'nullable|exists:users,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nom.required' => 'Le nom de l\'entreprise est requis.',
            'adresse.required' => 'L\'adresse est requise.',
            'ville.required' => 'La ville est requise.',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.max' => 'Le logo ne doit pas dépasser 2MB.',
        ]);

        // Upload du nouveau logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo
            if ($entreprise->logo) {
                Storage::disk('public')->delete($entreprise->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $validated['statut'] = $request->has('statut');

        if (empty($validated['commercial_id'])) {
            $validated['commercial_id'] = null;
        }

        $entreprise->update($validated);

        return redirect()
            ->route('admin.entreprises.index')
            ->with('success', 'Entreprise mise à jour avec succès !');
    }

    /**
     * Supprimer une entreprise
     */
    public function destroy($id)
    {
        $entreprise = Entreprise::findOrFail($id);

        // Vérifier s'il y a des employés liés
        $nombreEmployes = $entreprise->employes()->count();
        
        if ($nombreEmployes > 0) {
            return redirect()
                ->route('admin.entreprises.index')
                ->with('error', "Impossible de supprimer cette entreprise. Elle a {$nombreEmployes} employé(s) associé(s).");
        }

        // Supprimer le logo
        if ($entreprise->logo) {
            Storage::disk('public')->delete($entreprise->logo);
        }

        $entreprise->delete();

        return redirect()
            ->route('admin.entreprises.index')
            ->with('success', 'Entreprise supprimée avec succès !');
    }

    /** Virgule → point, sans zéros finaux superflus (avant validation). */
    private function normalizeGpsCoordinatesOnRequest(Request $request): void
    {
        foreach (['lat', 'long'] as $key) {
            if (! $request->has($key)) {
                continue;
            }
            $raw = $request->input($key);
            if ($raw === null || $raw === '') {
                continue;
            }
            $formatted = Entreprise::formatCoordinateForInput($raw);
            if ($formatted !== null) {
                $request->merge([$key => $formatted]);
            }
        }
    }
}
