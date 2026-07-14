<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Livraison;
use App\Models\User;
use App\Services\OrderStatusNotifier;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        $livreurs = User::where('role', 'livreur')->where('is_active', true)->orderBy('name')->get();

        $type = (string) $request->get('type_commande', '');

        $applyFilters = function ($query) use ($request) {
            if ($request->filled('statut_commande')) {
                $query->where('statut_commande', $request->statut_commande);
            }
            if ($request->filled('statut_preparation')) {
                $query->where('statut_preparation', $request->statut_preparation);
            }
            if ($request->filled('statut_livraison')) {
                $query->where('statut_livraison', $request->statut_livraison);
            }
            if ($request->filled('client')) {
                $s = trim((string) $request->client);
                if ($s !== '') {
                    $like = '%'.addcslashes($s, '%_\\').'%';
                    $query->where(function ($w) use ($like) {
                        $w->whereHas('employe', function ($e) use ($like) {
                            $e->where('name', 'like', $like)
                                ->orWhere('email', 'like', $like);
                        })->orWhere('numero_telephone', 'like', $like)
                            ->orWhere('ref', 'like', $like);
                    });
                }
            }
            if ($request->filled('date_debut')) {
                $query->whereDate('created_at', '>=', $request->date_debut);
            }
            if ($request->filled('date_fin')) {
                $query->whereDate('created_at', '<=', $request->date_fin);
            }
        };

        $commandesClassiques = null;
        $commandesLots = null;
        $commandesLotsGrouped = null;
        $lotsFiltreesCount = null;

        if ($type === '' || $type === 'classique') {
            $q = Commande::with(['employe', 'livraison.livreur', 'items.plat']);
            $applyFilters($q);
            $q->where('is_lunch', false)->orderBy('created_at', 'desc');
            $pageName = $type === '' ? 'page_classique' : 'page';
            $commandesClassiques = $q->paginate(10, ['*'], $pageName)->withQueryString();
        }

        if ($type === '' || $type === 'lot') {
            $q = Commande::with(['employe.entreprise', 'livraison.livreur', 'items.plat']);
            $applyFilters($q);
            $q->where('is_lunch', true);

            if ($type === 'lot') {
                $all = (clone $q)->orderBy('created_at', 'desc')->get();
                $lotsFiltreesCount = $all->count();
                $grouped = $all->groupBy(function ($c) {
                    return (int) ($c->employe?->id_entreprise ?? 0);
                });
                $orderedKeys = $grouped->keys()->sort(function ($a, $b) use ($grouped) {
                    $na = optional($grouped[$a]->first()->employe?->entreprise)->nom ?? '';
                    $nb = optional($grouped[$b]->first()->employe?->entreprise)->nom ?? '';
                    $cmp = strcasecmp($na, $nb);
                    if ($cmp !== 0) {
                        return $cmp;
                    }

                    return $a <=> $b;
                })->values();

                $perPageEntreprises = 8;
                $currentPage = max(1, (int) $request->get('page', 1));
                $totalGroups = $orderedKeys->count();
                $items = $orderedKeys
                    ->slice(($currentPage - 1) * $perPageEntreprises, $perPageEntreprises)
                    ->map(function ($eid) use ($grouped) {
                        $cmds = $grouped[$eid]->sortByDesc('created_at')->values();
                        $ent = $cmds->first()->employe?->entreprise;

                        return [
                            'entreprise_id' => $eid ?: null,
                            'entreprise' => $ent,
                            'commandes' => $cmds,
                        ];
                    })
                    ->values();

                $commandesLotsGrouped = new LengthAwarePaginator(
                    $items,
                    $totalGroups,
                    $perPageEntreprises,
                    $currentPage,
                    [
                        'path' => $request->url(),
                        'pageName' => 'page',
                    ]
                );
                $commandesLotsGrouped->withQueryString();
            } else {
                $q->orderBy('created_at', 'desc');
                $pageName = 'page_lot';
                $commandesLots = $q->paginate(10, ['*'], $pageName)->withQueryString();
            }
        }

        return view('admin.commandes.index', [
            'commandesClassiques' => $commandesClassiques,
            'commandesLots' => $commandesLots,
            'commandesLotsGrouped' => $commandesLotsGrouped,
            'lotsFiltreesCount' => $lotsFiltreesCount,
            'livreurs' => $livreurs,
            'typeVue' => $type,
        ]);
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

        $commande->update(['statut_livraison' => 'en_attente']);

        \App\Services\LivraisonAssignmentNotifier::afterSingleAssignment(
            $livraison->fresh(),
            $commande->fresh()
        );

        return redirect()->back()->with('success', 'Livreur affecté avec succès.');
    }

    public function changerStatutCommande(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);

        $validated = $request->validate([
            'statut_commande' => 'required|in:en_attente,confirmee,annulee,terminee',
        ]);

        $commande->update(['statut_commande' => $validated['statut_commande']]);

        OrderStatusNotifier::notify($commande->fresh(['employe', 'livraison.livreur']), 'commande', $validated['statut_commande']);

        return redirect()->back()->with('success', 'Statut de la commande mis à jour.');
    }

    public function changerStatutPreparation(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);

        $validated = $request->validate([
            'statut_preparation' => 'required|in:en_attente,en_cours,prete',
        ]);

        $commande->update(['statut_preparation' => $validated['statut_preparation']]);

        OrderStatusNotifier::notify($commande->fresh(['employe', 'livraison.livreur']), 'preparation', $validated['statut_preparation']);

        return redirect()->back()->with('success', 'Statut de préparation mis à jour.');
    }

    public function changerStatutLivraison(Request $request, $id)
    {
        $commande = Commande::findOrFail($id);

        $validated = $request->validate([
            'statut_livraison' => 'required|in:en_attente,en_cours,livree,echec',
        ]);

        \App\Services\LivraisonStatutSync::applyCommandeStatutLivraison(
            $commande,
            $validated['statut_livraison']
        );
        $commande->refresh();

        OrderStatusNotifier::notify($commande->fresh(['employe', 'livraison.livreur']), 'livraison', $validated['statut_livraison']);

        return redirect()->back()->with('success', 'Statut de livraison mis à jour.');
    }

    public function annuler($id)
    {
        $commande = Commande::findOrFail($id);

        $commande->update([
            'statut_commande' => 'annulee',
            'statut_livraison' => 'echec',
        ]);

        if ($commande->livraison) {
            $commande->livraison->update(['statut' => 'echec']);
        }

        OrderStatusNotifier::notify($commande->fresh(['employe', 'livraison.livreur']), 'commande', 'annulee');

        return redirect()->back()->with('success', 'Commande annulée.');
    }

    public function valider($id)
    {
        $commande = Commande::findOrFail($id);

        $commande->update([
            'statut_commande' => 'confirmee',
        ]);

        OrderStatusNotifier::notify($commande->fresh(['employe', 'livraison.livreur']), 'commande', 'confirmee');

        return redirect()->back()->with('success', 'Commande validée.');
    }
}
