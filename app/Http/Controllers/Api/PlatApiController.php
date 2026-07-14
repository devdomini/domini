<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plat;
use App\Models\Categorie;
use Illuminate\Http\Request;

class PlatApiController extends Controller
{
    /**
     * Liste de tous les plats disponibles
     */
    public function index(Request $request)
    {
        $query = Plat::with(['categorie', 'accompagnements', 'options']);

        // Filtrer par disponibilité
        if ($request->has('disponible')) {
            $query->where('est_disponible', $request->boolean('disponible'));
        } else {
            // Par défaut, afficher seulement les plats disponibles
            $query->where('est_disponible', true);
        }

        // Filtrer par catégorie
        if ($request->has('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        // Filtrer par qualité
        if ($request->has('qualite')) {
            $query->where('qualite', $request->qualite);
        }

        // Recherche par nom
        if ($request->has('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        // Tri
        $sortBy = $request->get('sort_by', 'nom'); // nom, prix, created_at
        $sortOrder = $request->get('sort_order', 'asc'); // asc, desc

        $query->orderBy($sortBy, $sortOrder);

        $plats = $query->get();

        return response()->json([
            'success' => true,
            'data' => [
                'plats' => $plats,
                'total' => $plats->count(),
            ]
        ], 200);
    }

    /**
     * Liste des plats par catégorie
     */
    public function byCategory($categorieId, Request $request)
    {
        $categorie = Categorie::findOrFail($categorieId);

        $query = Plat::where('categorie_id', $categorieId)
            ->with(['categorie', 'accompagnements', 'options']);

        // Filtrer par disponibilité
        if ($request->has('disponible')) {
            $query->where('est_disponible', $request->boolean('disponible'));
        } else {
            $query->where('est_disponible', true);
        }

        // Filtrer par qualité
        if ($request->has('qualite')) {
            $query->where('qualite', $request->qualite);
        }

        $plats = $query->orderBy('nom')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'categorie' => $categorie,
                'plats' => $plats,
                'total' => $plats->count(),
            ]
        ], 200);
    }

    /**
     * Détails d'un plat
     */
    public function show($id)
    {
        $plat = Plat::with(['categorie', 'accompagnements', 'options'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'plat' => $plat,
            ]
        ], 200);
    }
}
