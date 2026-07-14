<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Commune;
use App\Services\CommercialPortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntrepriseController extends Controller
{
    public function index(CommercialPortfolioService $portfolio)
    {
        $entreprises = $portfolio->entreprisesQuery(Auth::user())->paginate(15);

        return view('commercial.entreprises.index', compact('entreprises'));
    }

    public function create()
    {
        $communes = Commune::with('warehouse')->orderBy('nom')->get();
        $googleMapsApiKey = config('services.google_maps.api_key');

        return view('commercial.entreprises.create', compact('communes', 'googleMapsApiKey'));
    }

    public function store(Request $request, CommercialPortfolioService $portfolio)
    {
        $validated = $portfolio->validateEntreprisePayload($request);
        $portfolio->createEntreprise(Auth::user(), $validated, $request);

        return redirect()->route('commercial.entreprises.index')
            ->with('success', 'Entreprise créée avec succès.');
    }

    public function edit(int $id, CommercialPortfolioService $portfolio)
    {
        $entreprise = $portfolio->findEntrepriseForCommercial($id, Auth::user());
        $communes = Commune::with('warehouse')->orderBy('nom')->get();
        $googleMapsApiKey = config('services.google_maps.api_key');

        return view('commercial.entreprises.edit', compact('entreprise', 'communes', 'googleMapsApiKey'));
    }

    public function update(Request $request, int $id, CommercialPortfolioService $portfolio)
    {
        $entreprise = $portfolio->findEntrepriseForCommercial($id, Auth::user());
        $validated = $portfolio->validateEntreprisePayload($request, true);
        $portfolio->updateEntreprise($entreprise, $validated, $request, Auth::user());

        return redirect()->route('commercial.entreprises.index')
            ->with('success', 'Entreprise mise à jour.');
    }

    public function show(int $id, CommercialPortfolioService $portfolio)
    {
        $entreprise = $portfolio->findEntrepriseForCommercial($id, Auth::user());
        $entreprise->load(['boxes.casiers', 'employes']);

        return view('commercial.entreprises.show', compact('entreprise'));
    }
}
