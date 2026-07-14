<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favori;
use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriApiController extends Controller
{
    /**
     * Liste des plats favoris de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $favoris = Favori::where('user_id', $user->id)
            ->with(['plat.categorie', 'plat.accompagnements', 'plat.options'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'favoris' => $favoris,
                'total' => $favoris->count(),
            ]
        ], 200);
    }

    /**
     * Ajouter un plat aux favoris
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plat_id' => 'required|exists:plats,id',
        ], [
            'plat_id.required' => 'L\'ID du plat est obligatoire.',
            'plat_id.exists' => 'Le plat sélectionné n\'existe pas.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Vérifier si le plat est déjà en favori
        $existingFavori = Favori::where('user_id', $user->id)
            ->where('plat_id', $request->plat_id)
            ->first();

        if ($existingFavori) {
            return response()->json([
                'success' => false,
                'message' => 'Ce plat est déjà dans vos favoris'
            ], 400);
        }

        $favori = Favori::create([
            'user_id' => $user->id,
            'plat_id' => $request->plat_id,
        ]);

        $favori->load(['plat.categorie', 'plat.accompagnements', 'plat.options']);

        return response()->json([
            'success' => true,
            'message' => 'Plat ajouté aux favoris',
            'data' => [
                'favori' => $favori,
            ]
        ], 201);
    }

    /**
     * Retirer un plat des favoris
     */
    public function destroy($platId, Request $request)
    {
        $user = $request->user();

        $favori = Favori::where('user_id', $user->id)
            ->where('plat_id', $platId)
            ->firstOrFail();

        $favori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plat retiré des favoris'
        ], 200);
    }

    /**
     * Vérifier si un plat est en favori
     */
    public function check($platId, Request $request)
    {
        $user = $request->user();

        $isFavori = Favori::where('user_id', $user->id)
            ->where('plat_id', $platId)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'is_favori' => $isFavori,
            ]
        ], 200);
    }
}
