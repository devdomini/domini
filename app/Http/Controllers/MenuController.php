<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Plat;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display the menu management dashboard.
     */
    public function index()
    {
        $totalCategories = Categorie::count();
        $totalPlats = Plat::count();
        $platsActifs = Plat::where('est_disponible', true)->count();

        return view('admin.menu.index', compact('totalCategories', 'totalPlats', 'platsActifs'));
    }
}
