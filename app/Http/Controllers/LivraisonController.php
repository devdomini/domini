<?php

namespace App\Http\Controllers;

use App\Models\Livraison;
use App\Services\LivraisonStatutSync;
use Illuminate\Http\Request;

class LivraisonController extends Controller
{
    public function index(Request $request)
    {
        $query = Livraison::with(['commande', 'livreur', 'client']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('livreur_id')) {
            $query->where('livreur_id', $request->livreur_id);
        }

        $livraisons = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.livraisons.index', compact('livraisons'));
    }

    public function show($id)
    {
        $livraison = Livraison::with(['commande.items.plat', 'livreur', 'client', 'commande.employe'])->findOrFail($id);
        
        return view('admin.livraisons.show', compact('livraison'));
    }

    public function changerStatut(Request $request, $id)
    {
        $livraison = Livraison::findOrFail($id);
        
        $validated = $request->validate([
            'statut' => 'required|in:en_attente,assignee,en_cours,livree,echec',
        ]);

        $data = ['statut' => $validated['statut']];

        // Mettre à jour les horodatages selon le statut
        if ($validated['statut'] === 'en_cours') {
            $data['heure_prise_en_charge'] = now();
        } elseif ($validated['statut'] === 'livree') {
            $data['heure_livraison'] = now();
        }

        $livraison->update($data);
        LivraisonStatutSync::syncCommandeFromLivraison($livraison->fresh());

        return redirect()->back()->with('success', 'Statut de livraison mis à jour.');
    }
}
