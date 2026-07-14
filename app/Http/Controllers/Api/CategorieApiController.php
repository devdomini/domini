<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieApiController extends Controller
{
    /**
     * Liste de toutes les catégories disponibles
     */
    public function index(Request $request)
    {
        $query = Categorie::query();

        // Filtrer par disponibilité
        if ($request->has('disponible')) {
            $query->where('est_disponible', $request->boolean('disponible'));
        } else {
            // Par défaut, afficher seulement les catégories disponibles
            $query->where('est_disponible', true);
        }

        // Filtrer par qualité
        if ($request->has('qualite')) {
            $query->where('qualite', $request->qualite);
        }

        $categories = $query->withCount('plats')
            ->orderBy('nom')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'total' => $categories->count(),
            ]
        ], 200);
    }

    /**
     * Détails d'une catégorie
     */
    public function show($id)
    {
        $categorie = Categorie::with(['plats' => function($query) {
            $query->where('est_disponible', true);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'categorie' => $categorie,
            ]
        ], 200);
    }
}
