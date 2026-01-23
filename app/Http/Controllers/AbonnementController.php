<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbonnementController extends Controller
{
    /**
     * Afficher la liste des abonnements
     */
    public function index(Request $request)
    {
        $query = Abonnement::with('entreprise');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('entreprise')) {
            $query->where('id_entreprise', $request->entreprise);
        }

        $abonnements = $query->orderBy('created_at', 'desc')->paginate(10);
        $entreprises = Entreprise::where('statut', true)->orderBy('nom')->get();

        // Stats
        $stats = [
            'total' => Abonnement::count(),
            'actifs' => Abonnement::where('status', 'actif')->count(),
            'suspendus' => Abonnement::where('status', 'suspendu')->count(),
            'resilies' => Abonnement::where('status', 'resilie')->count(),
            'expires' => Abonnement::where('status', 'expire')->count(),
        ];

        return view('admin.abonnements.index', compact('abonnements', 'entreprises', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $entreprises = Entreprise::where('statut', true)->orderBy('nom')->get();
        
        return view('admin.abonnements.create', compact('entreprises'));
    }

    /**
     * Enregistrer un nouvel abonnement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_entreprise' => 'required|exists:entreprises,id',
            'representant' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'fonction' => 'required|string|max:255',
            'statut_subvention_commande' => 'required|in:totale,partielle,aucune',
            'pourcentage' => 'required|numeric|min:0|max:100',
            'nbre_employe' => 'required|integer|min:1',
            'duree_mois' => 'required|integer|min:1|max:36',
        ]);

        $validated['date_debut'] = Carbon::today();
        $validated['date_fin'] = Carbon::today()->addMonths((int) $validated['duree_mois']);
        $validated['status'] = 'actif';

        unset($validated['duree_mois']);

        Abonnement::create($validated);

        return redirect()->route('admin.abonnements.index')
            ->with('success', 'Abonnement créé avec succès !');
    }

    /**
     * Afficher les détails d'un abonnement
     */
    public function show(Abonnement $abonnement)
    {
        $abonnement->load('entreprise');
        
        return view('admin.abonnements.show', compact('abonnement'));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Abonnement $abonnement)
    {
        $entreprises = Entreprise::where('statut', true)->orderBy('nom')->get();
        
        return view('admin.abonnements.edit', compact('abonnement', 'entreprises'));
    }

    /**
     * Mettre à jour un abonnement
     */
    public function update(Request $request, Abonnement $abonnement)
    {
        $validated = $request->validate([
            'representant' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'fonction' => 'required|string|max:255',
            'statut_subvention_commande' => 'required|in:totale,partielle,aucune',
            'pourcentage' => 'required|numeric|min:0|max:100',
            'nbre_employe' => 'required|integer|min:1',
        ]);

        $abonnement->update($validated);

        return redirect()->route('admin.abonnements.index')
            ->with('success', 'Abonnement mis à jour avec succès !');
    }

    /**
     * Résilier un abonnement
     */
    public function resilier(Request $request, Abonnement $abonnement)
    {
        $validated = $request->validate([
            'raison' => 'required|string|max:500',
        ]);

        $abonnement->resilier($validated['raison']);

        return redirect()->back()
            ->with('success', 'Abonnement résilié avec succès !');
    }

    /**
     * Suspendre un abonnement
     */
    public function suspendre(Abonnement $abonnement)
    {
        $abonnement->suspendre();

        return redirect()->back()
            ->with('success', 'Abonnement suspendu avec succès !');
    }

    /**
     * Réactiver un abonnement
     */
    public function reactiver(Abonnement $abonnement)
    {
        if ($abonnement->estExpire()) {
            return redirect()->back()
                ->with('error', 'Impossible de réactiver un abonnement expiré. Veuillez le renouveler.');
        }

        $abonnement->reactiver();

        return redirect()->back()
            ->with('success', 'Abonnement réactivé avec succès !');
    }

    /**
     * Renouveler un abonnement
     */
    public function renouveler(Request $request, Abonnement $abonnement)
    {
        $validated = $request->validate([
            'duree_mois' => 'required|integer|min:1|max:36',
        ]);

        $abonnement->renouveler($validated['duree_mois']);

        return redirect()->back()
            ->with('success', "Abonnement renouvelé pour {$validated['duree_mois']} mois !");
    }
}
