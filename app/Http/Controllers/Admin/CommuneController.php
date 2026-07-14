<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commune;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class CommuneController extends Controller
{
    public function index()
    {
        $communes = Commune::with('warehouse')->orderBy('nom')->paginate(20);

        return view('admin.communes.index', compact('communes'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        return view('admin.communes.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'nom' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ], [
            'warehouse_id.required' => 'Choisissez un entrepôt.',
            'nom.required' => 'Le nom de la commune est requis.',
        ]);

        Commune::create($validated);

        return redirect()->route('admin.communes.index')
            ->with('success', 'Commune créée avec succès.');
    }

    public function edit(Commune $commune)
    {
        $warehouses = Warehouse::orderBy('name')->get();

        return view('admin.communes.edit', compact('commune', 'warehouses'));
    }

    public function update(Request $request, Commune $commune)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'nom' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $commune->update($validated);

        return redirect()->route('admin.communes.index')
            ->with('success', 'Commune mise à jour avec succès.');
    }

    public function destroy(Commune $commune)
    {
        if ($commune->entreprises()->exists()) {
            return redirect()->route('admin.communes.index')
                ->with('error', 'Impossible de supprimer : des entreprises sont liées à cette commune.');
        }

        $commune->delete();

        return redirect()->route('admin.communes.index')
            ->with('success', 'Commune supprimée.');
    }
}
