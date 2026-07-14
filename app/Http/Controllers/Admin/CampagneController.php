<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FcmNotificationService;
use Illuminate\Http\Request;

class CampagneController extends Controller
{
    private function ensureAdmin(): void
    {
        if (! auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }
    }

    public function index()
    {
        $this->ensureAdmin();

        return view('admin.campagnes.index', [
            'fcmConfigured' => FcmNotificationService::isConfigured(),
            'stats' => FcmNotificationService::campaignRecipientStats(),
        ]);
    }

    public function send(Request $request)
    {
        $this->ensureAdmin();

        if (! FcmNotificationService::isConfigured()) {
            return redirect()
                ->route('admin.campagnes.index')
                ->with('error', 'Firebase n’est pas configuré (variable FIREBASE_CREDENTIALS et fichier JSON compte de service).');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'body' => 'required|string|max:2000',
            'audience' => 'required|string|in:all,employe,livreur',
            'kind' => 'required|string|in:promo,fidelisation,message',
        ], [
            'title.required' => 'Le titre est obligatoire.',
            'body.required' => 'Le message est obligatoire.',
        ]);

        $roles = match ($validated['audience']) {
            'employe' => ['employe'],
            'livreur' => ['livreur'],
            default => ['employe', 'livreur'],
        };

        $result = FcmNotificationService::broadcastCampaign(
            $roles,
            $validated['title'],
            $validated['body'],
            $validated['kind']
        );

        if ($result['targets'] === 0) {
            return redirect()
                ->route('admin.campagnes.index')
                ->with('error', 'Aucun destinataire : aucun compte employé ou livreur n’a de jeton FCM dans cette base. '
                    .'Ouvrez l’app mobile connectée à ce même serveur (même URL dans api_config) et connectez-vous pour enregistrer les jetons.');
        }

        $msg = sprintf(
            'Campagne envoyée : %d destinataire(s) avec jeton FCM, %d envoi(s) réussi(s), %d échec(s).',
            $result['targets'],
            $result['sent'],
            $result['failed']
        );

        return redirect()
            ->route('admin.campagnes.index')
            ->with('success', $msg);
    }
}
