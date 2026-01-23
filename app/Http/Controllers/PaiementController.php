<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $query = Paiement::with(['commande', 'user']);

        // Filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('mode_paiement')) {
            $query->where('mode_paiement', $request->mode_paiement);
        }

        $paiements = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistiques
        $stats = [
            'total' => Paiement::where('statut', 'valide')->sum('montant'),
            'en_attente' => Paiement::where('statut', 'en_attente')->sum('montant'),
            'rembourse' => Paiement::where('statut', 'rembourse')->sum('montant'),
        ];

        return view('admin.paiements.index', compact('paiements', 'stats'));
    }
}
