<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Services\CommercialPortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaiementController extends Controller
{
    public function index(Request $request, CommercialPortfolioService $portfolio)
    {
        $entrepriseId = $request->integer('entreprise_id') ?: null;
        $paiements = $portfolio->paiementsQuery(Auth::user(), $entrepriseId)->paginate(20);
        $entreprises = $portfolio->entreprisesQuery(Auth::user())->get(['id', 'nom']);

        return view('commercial.paiements.index', compact('paiements', 'entreprises', 'entrepriseId'));
    }

    public function impayes(Request $request, CommercialPortfolioService $portfolio)
    {
        $entrepriseId = $request->integer('entreprise_id') ?: null;
        $commandes = $portfolio->impayesQuery(Auth::user(), $entrepriseId)->paginate(20);
        $entreprises = $portfolio->entreprisesQuery(Auth::user())->get(['id', 'nom']);

        return view('commercial.impayes.index', compact('commandes', 'entreprises', 'entrepriseId'));
    }
}
