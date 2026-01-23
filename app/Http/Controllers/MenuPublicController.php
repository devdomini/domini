<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Plat;
use Illuminate\Http\Request;

class MenuPublicController extends Controller
{
    public function index()
    {
        // Récupérer toutes les catégories disponibles
        $categories = Categorie::where('est_disponible', true)
            ->orderBy('nom')
            ->get();

        // Récupérer tous les plats disponibles avec leur catégorie
        $plats = Plat::with('categorie')
            ->where('est_disponible', true)
            ->orderBy('nom')
            ->get();

        return view('menu', compact('categories', 'plats'));
    }
}
