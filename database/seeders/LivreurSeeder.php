<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LivreurSeeder extends Seeder
{
    public function run(): void
    {
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

        foreach ($livreurs as $livreur) {
            User::create(array_merge($livreur, [
                'role' => 'livreur',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]));
        }
    }
}
