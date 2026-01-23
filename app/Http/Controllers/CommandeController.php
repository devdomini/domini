<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Livraison;
use App\Models\User;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        $query = Commande::with(['employe', 'livraison.livreur', 'items.plat']);

        // Filtres
        if ($request->filled('statut_commande')) {
            $query->where('statut_commande', $request->statut_commande);
        }
        if ($request->filled('statut_preparation')) {
            $query->where('statut_preparation', $request->statut_preparation);
        }
        if ($request->filled('statut_livraison')) {
            $query->where('statut_livraison', $request->statut_livraison);
        }

        $commandes = $query->orderBy('created_at', 'desc')->paginate(10);
        $livreurs = User::where('role', 'livreur')->where('is_active', true)->get();

        return view('admin.commandes.index', compact('commandes', 'livreurs'));
    }

    public function show($id)
    {
        $commande = Commande::with(['employe', 'items.plat', 'livraison.livreur', 'paiements'])->findOrFail($id);
        $livreurs = User::where('role', 'livreur')->where('is_active', true)->get();
        
        return view('admin.commandes.show', compact('commande', 'livreurs'));
    }

    public function affecterLivreur(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);
        
        $validated = $request->validate([
            'livreur_id' => 'required|exists:users,id',
        ]);

        // Créer ou mettre à jour la livraison
        $livraison = Livraison::updateOrCreate(
            ['commande_id' => $commande->id],
            [
                'livreur_id' => $validated['livreur_id'],
                'client_id' => $commande->id_employe,
                'statut' => 'assignee',
                'heure_assignation' => now(),
            ]
        );

        $commande->update(['statut_livraison' => 'en_cours']);

        return redirect()->back()->with('success', 'Livreur affecté avec succès.');
    }

    public function changerStatutCommande(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);
        
        $validated = $request->validate([
            'statut_commande' => 'required|in:en_attente,confirmee,annulee,terminee',
        ]);

        $commande->update(['statut_commande' => $validated['statut_commande']]);

        return redirect()->back()->with('success', 'Statut de la commande mis à jour.');
    }

    public function changerStatutPreparation(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);
        
        $validated = $request->validate([
            'statut_preparation' => 'required|in:en_attente,en_cours,prete',
        ]);

        $commande->update(['statut_preparation' => $validated['statut_preparation']]);

        return redirect()->back()->with('success', 'Statut de préparation mis à jour.');
    }

    public function changerStatutLivraison(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);
        
        $validated = $request->validate([
            'statut_livraison' => 'required|in:en_attente,en_cours,livree,echec',
        ]);

        $commande->update(['statut_livraison' => $validated['statut_livraison']]);

        // Mettre à jour la livraison si elle existe
        if ($commande->livraison && $validated['statut_livraison'] === 'livree') {
            $commande->livraison->update([
                'statut' => 'livree',
                'heure_livraison' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Statut de livraison mis à jour.');
    }

    public function annuler($id)
    {
        $commande = Commande::findOrFail($id);
        
        $commande->update([
            'statut_commande' => 'annulee',
            'statut_livraison' => 'echec',
        ]);

        return redirect()->back()->with('success', 'Commande annulée.');
    }

    public function valider($id)
    {
        $commande = Commande::findOrFail($id);
        
        $commande->update([
            'statut_commande' => 'confirmee',
        ]);

        return redirect()->back()->with('success', 'Commande validée.');
    }
}
