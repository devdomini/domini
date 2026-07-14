<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = \App\Models\Warehouse::query()->withCount('trajetItems')->orderBy('name')->get();
        return view('admin.warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.warehouses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
        ]);

        \App\Models\Warehouse::create($validated);

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Entrepôt créé avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Warehouse $warehouse)
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, \App\Models\Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $warehouse->update($validated);

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Entrepôt mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Entrepôt supprimé avec succès.');
    }
}
