<?php

namespace Database\Seeders;

use App\Models\Plat;
use App\Models\Accompagnement;
use App\Models\Option;
use Illuminate\Database\Seeder;

class PlatSeeder extends Seeder
{
    public function run(): void
    {
        // Plats Africains
        $attiekePoisson = Plat::create([
            'categorie_id' => 1,
            'nom' => 'Attiéké Poisson Braisé',
            'prix' => 2500,
            'detail' => 'Attiéké accompagné de poisson braisé frais et sauce tomate oignon',
            'est_disponible' => true,
        ]);

        Accompagnement::create(['plat_id' => $attiekePoisson->id, 'nom' => 'Alloco', 'qte_gratuit' => 1, 'prix_unitaire' => 500, 'disponible' => true]);
        Accompagnement::create(['plat_id' => $attiekePoisson->id, 'nom' => 'Salade', 'qte_gratuit' => 1, 'prix_unitaire' => 300, 'disponible' => true]);
        Option::create(['plat_id' => $attiekePoisson->id, 'nom' => 'Sauce pimentée', 'qte_gratuit' => 1, 'disponible' => true]);
        Option::create(['plat_id' => $attiekePoisson->id, 'nom' => 'Citron', 'qte_gratuit' => 2, 'disponible' => true]);

        $riz = Plat::create([
            'categorie_id' => 1,
            'nom' => 'Riz Sauce Graine',
            'prix' => 2000,
            'detail' => 'Riz blanc avec sauce graine de palme, viande de bœuf',
            'est_disponible' => true,
        ]);

        Accompagnement::create(['plat_id' => $riz->id, 'nom' => 'Poulet', 'qte_gratuit' => 0, 'prix_unitaire' => 1000, 'disponible' => true]);
        Accompagnement::create(['plat_id' => $riz->id, 'nom' => 'Alloco', 'qte_gratuit' => 1, 'prix_unitaire' => 500, 'disponible' => true]);

        $foutou = Plat::create([
            'categorie_id' => 1,
            'nom' => 'Foutou Sauce Arachide',
            'prix' => 2200,
            'detail' => 'Foutou d\'igname avec sauce arachide et viande de bœuf',
            'est_disponible' => true,
        ]);

        Accompagnement::create(['plat_id' => $foutou->id, 'nom' => 'Poisson fumé', 'qte_gratuit' => 0, 'prix_unitaire' => 800, 'disponible' => true]);

        // Plats Européens
        $steak = Plat::create([
            'categorie_id' => 2,
            'nom' => 'Steak Frites',
            'prix' => 3000,
            'detail' => 'Steak de bœuf grillé avec frites maison',
            'est_disponible' => true,
        ]);

        Accompagnement::create(['plat_id' => $steak->id, 'nom' => 'Légumes grillés', 'qte_gratuit' => 1, 'prix_unitaire' => 500, 'disponible' => true]);
        Option::create(['plat_id' => $steak->id, 'nom' => 'Sauce poivre', 'qte_gratuit' => 1, 'disponible' => true]);
        Option::create(['plat_id' => $steak->id, 'nom' => 'Sauce champignon', 'qte_gratuit' => 1, 'disponible' => true]);

        $poulet = Plat::create([
            'categorie_id' => 2,
            'nom' => 'Poulet Rôti & Purée',
            'prix' => 2800,
            'detail' => 'Cuisse de poulet rôti avec purée de pommes de terre',
            'est_disponible' => true,
        ]);

        Accompagnement::create(['plat_id' => $poulet->id, 'nom' => 'Haricots verts', 'qte_gratuit' => 1, 'prix_unitaire' => 400, 'disponible' => true]);

        // Plats Asiatiques
        $noodles = Plat::create([
            'categorie_id' => 3,
            'nom' => 'Nouilles Sautées Poulet',
            'prix' => 2300,
            'detail' => 'Nouilles chinoises sautées avec poulet et légumes',
            'est_disponible' => true,
        ]);

        Accompagnement::create(['plat_id' => $noodles->id, 'nom' => 'Nems', 'qte_gratuit' => 2, 'prix_unitaire' => 300, 'disponible' => true]);
        Option::create(['plat_id' => $noodles->id, 'nom' => 'Sauce soja', 'qte_gratuit' => 2, 'disponible' => true]);
        Option::create(['plat_id' => $noodles->id, 'nom' => 'Piment', 'qte_gratuit' => 1, 'disponible' => true]);

        $rizCantonais = Plat::create([
            'categorie_id' => 3,
            'nom' => 'Riz Cantonais',
            'prix' => 2500,
            'detail' => 'Riz frit à la cantonaise avec œufs, légumes et crevettes',
            'est_disponible' => true,
        ]);

        Accompagnement::create(['plat_id' => $rizCantonais->id, 'nom' => 'Beignet de crevettes', 'qte_gratuit' => 3, 'prix_unitaire' => 400, 'disponible' => true]);

        // Grillades
        $brochettes = Plat::create([
            'categorie_id' => 4,
            'nom' => 'Brochettes de Bœuf',
            'prix' => 2700,
            'detail' => '6 brochettes de bœuf marinées et grillées',
            'est_disponible' => true,
        ]);

        Accompagnement::create(['plat_id' => $brochettes->id, 'nom' => 'Frites', 'qte_gratuit' => 1, 'prix_unitaire' => 500, 'disponible' => true]);
        Accompagnement::create(['plat_id' => $brochettes->id, 'nom' => 'Alloco', 'qte_gratuit' => 1, 'prix_unitaire' => 500, 'disponible' => true]);
        Option::create(['plat_id' => $brochettes->id, 'nom' => 'Sauce barbecue', 'qte_gratuit' => 2, 'disponible' => true]);

        // Salades
        $salade = Plat::create([
            'categorie_id' => 5,
            'nom' => 'Salade César Poulet',
            'prix' => 2200,
            'detail' => 'Salade verte, poulet grillé, croûtons, parmesan, sauce césar',
            'est_disponible' => true,
        ]);

        Option::create(['plat_id' => $salade->id, 'nom' => 'Pain grillé', 'qte_gratuit' => 2, 'disponible' => true]);

        // Pâtes
        $spaghetti = Plat::create([
            'categorie_id' => 6,
            'nom' => 'Spaghetti Bolognaise',
            'prix' => 2400,
            'detail' => 'Spaghetti avec sauce bolognaise maison et viande hachée',
            'est_disponible' => true,
        ]);

        Accompagnement::create(['plat_id' => $spaghetti->id, 'nom' => 'Pain à l\'ail', 'qte_gratuit' => 2, 'prix_unitaire' => 300, 'disponible' => true]);
        Option::create(['plat_id' => $spaghetti->id, 'nom' => 'Parmesan', 'qte_gratuit' => 1, 'disponible' => true]);

        $pizza = Plat::create([
            'categorie_id' => 6,
            'nom' => 'Pizza Margherita',
            'prix' => 2600,
            'detail' => 'Pizza tomate, mozzarella, basilic frais',
            'est_disponible' => true,
        ]);

        Option::create(['plat_id' => $pizza->id, 'nom' => 'Piment', 'qte_gratuit' => 1, 'disponible' => true]);
    }
}
