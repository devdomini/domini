<?php

namespace Database\Seeders;

use App\Models\Commune;
use App\Models\Entreprise;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class CommuneEntrepriseSeeder extends Seeder
{
    /**
     * Communes pour l’entrepôt id 1 + liaison des entreprises seedées + quelques entreprises supplémentaires.
     */
    public function run(): void
    {
        $warehouse = Warehouse::query()->find(1);
        if (! $warehouse) {
            $this->command?->error('Aucun entrepôt avec id 1. Exécutez d’abord : php artisan db:seed --class=WarehouseSeeder');

            return;
        }

        $communesData = [
            ['nom' => 'Plateau', 'latitude' => 5.3192, 'longitude' => -4.0197],
            ['nom' => 'Cocody', 'latitude' => 5.3520, 'longitude' => -3.9880],
            ['nom' => 'Marcory', 'latitude' => 5.3080, 'longitude' => -3.9885],
            ['nom' => 'Yopougon', 'latitude' => 5.3450, 'longitude' => -4.0800],
            ['nom' => 'Zone 4', 'latitude' => 5.3360, 'longitude' => -4.0260],
        ];

        $communesByName = [];
        foreach ($communesData as $row) {
            $c = Commune::query()->firstOrCreate(
                ['warehouse_id' => $warehouse->id, 'nom' => $row['nom']],
                [
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                ]
            );
            $communesByName[$row['nom']] = $c;
        }

        // Rattacher les 7 entreprises du EntrepriseSeeder (ids 1–7) à des communes de l’entrepôt 1
        $mapEntrepriseIdToCommuneNom = [
            1 => 'Cocody',       // Orange
            2 => 'Plateau',      // MTN
            3 => 'Plateau',      // SG
            4 => 'Yopougon',     // Nestlé
            5 => 'Plateau',      // Bloomfield
            6 => 'Plateau',      // NSIA
            7 => 'Marcory',      // CFAO
        ];

        foreach ($mapEntrepriseIdToCommuneNom as $entrepriseId => $communeNom) {
            $e = Entreprise::query()->find($entrepriseId);
            if ($e && isset($communesByName[$communeNom])) {
                $e->update(['commune_id' => $communesByName[$communeNom]->id]);
            }
        }

        // Entreprises supplémentaires (nouvelles)
        $nouvelles = [
            [
                'nom' => 'Bureau Demo Plateau',
                'adresse' => 'Rue du Commerce, Plateau',
                'lat' => 5.3180,
                'long' => -4.0210,
                'numero' => '+225 27 22 11 00 00',
                'pays' => 'Côte d\'Ivoire',
                'ville' => 'Abidjan',
                'commune_nom' => 'Plateau',
                'statut' => true,
            ],
            [
                'nom' => 'Siège associatif Cocody',
                'adresse' => 'Riviera Palmeraie',
                'lat' => 5.3580,
                'long' => -3.9920,
                'numero' => '+225 27 22 11 00 01',
                'pays' => 'Côte d\'Ivoire',
                'ville' => 'Abidjan',
                'commune_nom' => 'Cocody',
                'statut' => true,
            ],
            [
                'nom' => 'Entrepôt client Marcory',
                'adresse' => 'Boulevard VGE',
                'lat' => 5.3050,
                'long' => -3.9850,
                'numero' => '+225 27 22 11 00 02',
                'pays' => 'Côte d\'Ivoire',
                'ville' => 'Abidjan',
                'commune_nom' => 'Marcory',
                'statut' => true,
            ],
        ];

        foreach ($nouvelles as $row) {
            $nomCommune = $row['commune_nom'];
            unset($row['commune_nom']);
            if (! isset($communesByName[$nomCommune])) {
                continue;
            }
            $nom = $row['nom'];
            $row['commune_id'] = $communesByName[$nomCommune]->id;
            Entreprise::query()->firstOrCreate(
                ['nom' => $nom],
                $row
            );
        }

        $this->command?->info('Communes (entrepôt 1) : '.count($communesByName).' — entreprises existantes mises à jour + '.count($nouvelles).' entreprises démo (firstOrCreate).');
    }
}
