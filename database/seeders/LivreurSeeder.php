<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LivreurSeeder extends Seeder
{
    public function run(): void
    {
        // Coordonnées par défaut (Abidjan) pour afficher les livreurs sur la carte
        // et éviter qu'ils n'apparaissent "invisibles" tant qu'ils n'ont pas envoyé de GPS.
        $baseLat = 5.3599517;
        $baseLng = -4.0082563;

        $livreurs = [
            [
                'name' => 'Souleymane Bakayoko',
                'email' => 's.bakayoko@domini.ci',
                'telephone' => '+225 07 11 22 33 44',
            ],
            [
                'name' => 'Seydou Doumbia',
                'email' => 's.doumbia@domini.ci',
                'telephone' => '+225 05 22 33 44 55',
            ],
            [
                'name' => 'Mamadou Diabaté',
                'email' => 'm.diabate@domini.ci',
                'telephone' => '+225 07 33 44 55 66',
            ],
            [
                'name' => 'Ali Coulibaly',
                'email' => 'a.coulibaly@domini.ci',
                'telephone' => '+225 05 44 55 66 77',
            ],
            [
                'name' => 'Adama Konaté',
                'email' => 'a.konate@domini.ci',
                'telephone' => '+225 07 55 66 77 88',
            ],
            [
                'name' => 'Lassina Fofana',
                'email' => 'l.fofana@domini.ci',
                'telephone' => '+225 05 66 77 88 99',
            ],
            [
                'name' => 'Ismaël Touré',
                'email' => 'i.toure@domini.ci',
                'telephone' => '+225 07 77 88 99 00',
            ],
            [
                'name' => 'Daouda Sylla',
                'email' => 'd.sylla@domini.ci',
                'telephone' => '+225 05 88 99 00 11',
            ],
        ];

        $cree = 0;
        $existants = 0;

        foreach ($livreurs as $index => $livreur) {
            // Petite variation de position par livreur
            $lat = $baseLat + (mt_rand(-40, 40) / 10000);
            $lng = $baseLng + (mt_rand(-40, 40) / 10000);
            // 2 premiers = lots entreprise, les autres = classique (voir DemoLivraisonsSeeder)
            $typeLivreur = $index < 2 ? 'entreprise' : 'classique';

            $user = User::firstOrCreate(
                ['email' => $livreur['email']],
                array_merge($livreur, [
                    'role' => 'livreur',
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                    'is_dispo' => true,
                    'warehouse_id' => 1,
                    'type_livreur' => $typeLivreur,
                    'current_lat' => $lat,
                    'current_long' => $lng,
                    'last_location_at' => now(),
                ])
            );

            if ($user->wasRecentlyCreated) {
                $cree++;
            } else {
                $existants++;
                // Met à jour les champs utiles à l'affichage carte (si déjà existant)
                $user->update([
                    'is_active' => true,
                    'is_dispo' => $user->is_dispo ?? true,
                    'warehouse_id' => $user->warehouse_id ?? 1,
                    'type_livreur' => $user->type_livreur ?? $typeLivreur,
                    'current_lat' => $user->current_lat ?? $lat,
                    'current_long' => $user->current_long ?? $lng,
                    'last_location_at' => $user->last_location_at ?? now(),
                ]);
            }
        }

        $this->command?->info("Livreurs : {$cree} créé(s), {$existants} déjà présent(s) (emails uniques).");
    }
}
