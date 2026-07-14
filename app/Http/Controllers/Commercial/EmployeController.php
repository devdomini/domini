<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Services\CommercialPortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeController extends Controller
{
    public function store(Request $request, int $entrepriseId, CommercialPortfolioService $portfolio)
    {
        $entreprise = $portfolio->findEntrepriseForCommercial($entrepriseId, Auth::user());
        $validated = $portfolio->validateEmployePayload($request);
        $portfolio->createEmploye(Auth::user(), $entreprise, $validated, $request);

        return redirect()
            ->route('commercial.entreprises.show', $entreprise->id)
            ->with('success', 'Employé ajouté avec succès.');
    }

    public function update(Request $request, int $employeId, CommercialPortfolioService $portfolio)
    {
        $employe = $portfolio->findEmployeForCommercial($employeId, Auth::user());
        $validated = $portfolio->validateEmployePayload($request, $employe);
        $portfolio->updateEmploye($employe, $validated, $request);

        return redirect()
            ->route('commercial.entreprises.show', $employe->id_entreprise)
            ->with('success', 'Employé mis à jour.');
    }
}
