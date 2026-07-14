<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entreprise;
use Illuminate\Http\Request;

class EntrepriseApiController extends Controller
{
    /**
     * Liste de toutes les entreprises actives
     */
    public function index(Request $request)
    {
        $query = Entreprise::query();

        // Filtrer par statut actif
        if ($request->has('actif')) {
            $query->where('statut', $request->boolean('actif'));
        } else {
            // Par défaut, afficher seulement les entreprises actives
            $query->where('statut', true);
        }

        $entreprises = $query->orderBy('nom')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'entreprises' => $entreprises,
                'total' => $entreprises->count(),
            ]
        ], 200);
    }

    /**
     * Détails d'une entreprise
     */
    public function show($id)
    {
        $entreprise = Entreprise::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'entreprise' => $entreprise,
            ]
        ], 200);
    }
}
