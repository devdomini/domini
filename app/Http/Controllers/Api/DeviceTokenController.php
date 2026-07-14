<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceTokenController extends Controller
{
    /**
     * Enregistre ou met à jour le jeton FCM de l’appareil (un seul jeton actif par utilisateur).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string|max:4096',
            'platform' => 'nullable|string|in:android,ios',
        ], [
            'fcm_token.required' => 'Le jeton FCM est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $user->update([
            'fcm_token' => $request->input('fcm_token'),
            'fcm_platform' => $request->input('platform'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jeton enregistré',
        ], 200);
    }

    /**
     * Supprime le jeton (déconnexion, désinstallation).
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->update([
            'fcm_token' => null,
            'fcm_platform' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jeton supprimé',
        ], 200);
    }
}
