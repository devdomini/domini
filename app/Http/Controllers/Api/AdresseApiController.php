<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Adresse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdresseApiController extends Controller
{
    /**
     * Lister les adresses de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $adresses = $request->user()->adresses()->orderBy('is_default', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $adresses
        ]);
    }

    /**
     * Ajouter une nouvelle adresse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'nullable|string|max:50',
            'adresse' => 'required|string|max:255',
            'ville' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Si c'est la première adresse ou si définie comme par défaut, désactiver les autres par défaut
        if ($request->is_default || $user->adresses()->count() === 0) {
            $user->adresses()->update(['is_default' => false]);
            $request->merge(['is_default' => true]);
        }

        $adresse = $user->adresses()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Adresse ajoutée avec succès',
            'data' => $adresse
        ], 201);
    }

    /**
     * Mettre à jour une adresse
     */
    public function update(Request $request, $id)
    {
        $adresse = $request->user()->adresses()->find($id);

        if (!$adresse) {
            return response()->json([
                'success' => false,
                'message' => 'Adresse introuvable'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'titre' => 'nullable|string|max:50',
            'adresse' => 'sometimes|required|string|max:255',
            'ville' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Gestion de l'adresse par défaut
        if ($request->has('is_default') && $request->is_default) {
            $request->user()->adresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $adresse->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Adresse mise à jour avec succès',
            'data' => $adresse
        ]);
    }

    /**
     * Supprimer une adresse
     */
    public function destroy(Request $request, $id)
    {
        $adresse = $request->user()->adresses()->find($id);

        if (!$adresse) {
            return response()->json([
                'success' => false,
                'message' => 'Adresse introuvable'
            ], 404);
        }

        $wasDefault = $adresse->is_default;
        $adresse->delete();

        // Si on a supprimé l'adresse par défaut, en définir une autre (la plus récente) comme par défaut
        if ($wasDefault) {
            $newDefault = $request->user()->adresses()->latest()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Adresse supprimée avec succès'
        ]);
    }
}
