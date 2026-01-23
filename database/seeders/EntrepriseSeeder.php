<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use Illuminate\Database\Seeder;

class EntrepriseSeeder extends Seeder
{
    public function run(): void
    {
        $entreprises = [
            [
                'nom' => 'Orange Côte d\'Ivoire',
                'adresse' => 'Boulevard Lagunaire, Zone 4C',
                'lat' => 5.3364,
                'long' => -4.0267,
                'numero' => '+225 07 08 09 10 11',
                'pays' => 'Côte d\'Ivoire',
                'ville' => 'Abidjan',
                'statut' => true,
            ],
            [
                'nom' => 'MTN Côte d\'Ivoire',
                'adresse' => 'Immeuble Alpha 2000, Plateau',
                'lat' => 5.3254,
                'long' => -4.0168,
                'numero' => '+225 05 06 07 08 09',
                'pays' => 'Côte d\'Ivoire',
                'ville' => 'Abidjan',
                'statut' => true,
            ],
            [
                'nom' => 'Société Générale CI',
                'adresse' => 'Avenue Nogués, Plateau',
                'lat' => 5.3234,
                'long' => -4.0145,
                'numero' => '+225 27 20 12 34 56',
                'pays' => 'Côte d\'Ivoire',
                'ville' => 'Abidjan',
                'statut' => true,
            ],
            [
                'nom' => 'Nestlé Côte d\'Ivoire',
                'adresse' => 'Zone Industrielle de Yopougon',
                'lat' => 5.3453,
                'long' => -4.0923,
                'numero' => '+225 27 21 23 45 67',
                'pays' => 'Côte d\'Ivoire',
                'ville' => 'Abidjan',
                'statut' => true,
            ],
            [
                'nom' => 'Bloomfield Investment',
                'adresse' => 'Rue Gourgas, Plateau',
                'lat' => 5.3198,
                'long' => -4.0234,
                'numero' => '+225 27 20 34 56 78',
                'pays' => 'Côte d\'Ivoire',
                'ville' => 'Abidjan',
                'statut' => true,
            ],
            [
                'nom' => 'NSIA Banque',
                'adresse' => 'Boulevard Carde, Plateau',
                'lat' => 5.3176,
                'long' => -4.0287,
                'numero' => '+225 27 20 45 67 89',
                'pays' => 'Côte d\'Ivoire',
                'ville' => 'Abidjan',
                'statut' => true,
            ],
            [
                'nom' => 'CFAO Technologies',
                'adresse' => 'Boulevard VGE, Marcory',
                'lat' => 5.3089,
                'long' => -3.9867,
                'numero' => '+225 27 21 56 78 90',
                'pays' => 'Côte d\'Ivoire',
                'ville' => 'Abidjan',
                'statut' => false,
            ],
        ];

        foreach ($entreprises as $entreprise) {
            Entreprise::create($entreprise);
        }
    }
}
