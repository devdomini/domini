<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Casier;
use App\Services\CommercialPortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BoxController extends Controller
{
    public function create(int $entrepriseId, CommercialPortfolioService $portfolio)
    {
        $entreprise = $portfolio->findEntrepriseForCommercial($entrepriseId, Auth::user());

        return view('commercial.boxes.create', compact('entreprise'));
    }

    public function store(Request $request, CommercialPortfolioService $portfolio)
    {
        $validated = $request->validate([
            'id_entreprise' => 'required|exists:entreprises,id',
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'capacite' => 'required|integer|min:1|max:200',
        ]);

        $box = $portfolio->createBox(Auth::user(), $validated);

        return redirect()->route('commercial.boxes.show', $box->id)
            ->with('success', 'Box et casiers créés.');
    }

    public function show(int $id, CommercialPortfolioService $portfolio)
    {
        $box = $portfolio->findBoxForCommercial($id, Auth::user());

        return view('commercial.boxes.show', compact('box'));
    }

    public function assignEmploye(Request $request, int $boxId, int $casierId, CommercialPortfolioService $portfolio)
    {
        $validated = $request->validate(['employe_id' => 'required|exists:users,id']);
        $box = $portfolio->findBoxForCommercial($boxId, Auth::user());
        $casier = Casier::where('id_box', $box->id)->findOrFail($casierId);

        try {
            $portfolio->assignCasier(Auth::user(), $box, $casier, (int) $validated['employe_id']);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()->with('success', 'Casier attribué.');
    }

    public function unassignEmploye(int $boxId, int $casierId, CommercialPortfolioService $portfolio)
    {
        $portfolio->findBoxForCommercial($boxId, Auth::user());
        $casier = Casier::where('id_box', $boxId)->findOrFail($casierId);
        $portfolio->unassignCasier($casier);

        return back()->with('success', 'Casier libéré.');
    }
}
