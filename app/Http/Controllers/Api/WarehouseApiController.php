<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WarehouseApiController extends Controller
{
    /**
     * Liste des entrepôts actifs
     */
    public function index()
    {
        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $warehouses
        ]);
    }

    /**
     * Obtenir l'entrepôt principal (le premier actif)
     */
    public function main()
    {
        $warehouse = \App\Models\Warehouse::where('is_active', true)->first();

        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun entrepôt actif configuré.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $warehouse
        ]);
    }
}
