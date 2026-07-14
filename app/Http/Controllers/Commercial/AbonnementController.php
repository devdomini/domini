<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Entreprise;
use App\Services\CommercialPortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbonnementController extends Controller
{
    public function index(Request $request, CommercialPortfolioService $portfolio)
    {
        $entrepriseId = $request->integer('entreprise_id') ?: null;
        $abonnements = $portfolio->abonnementsQuery(Auth::user(), $entrepriseId)->paginate(20);
        $entreprises = $portfolio->entreprisesQuery(Auth::user())->get(['id', 'nom']);

        return view('commercial.abonnements.index', compact('abonnements', 'entreprises', 'entrepriseId'));
    }
}
