<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nom' => 'Plats Africains',
                'est_disponible' => true,
            ],
            [
                'nom' => 'Plats Européens',
                'est_disponible' => true,
            ],
            [
                'nom' => 'Plats Asiatiques',
                'est_disponible' => true,
            ],
            [
                'nom' => 'Grillades',
                'est_disponible' => true,
            ],
            [
                'nom' => 'Salades & Léger',
                'est_disponible' => true,
            ],
            [
                'nom' => 'Pâtes & Pizzas',
                'est_disponible' => true,
            ],
            [
                'nom' => 'Fast Food',
                'est_disponible' => false,
            ],
        ];

        foreach ($categories as $categorie) {
            Categorie::create($categorie);
        }
    }
}
