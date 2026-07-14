<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CommercialUserSeeder extends Seeder
{
    /**
     * Compte commercial de démo (app mobile + portail /admin/commercial).
     */
    public function run(): void
    {
        $commercial = User::firstOrCreate(
            ['email' => 'commercial@domini.com'],
            [
                'name' => 'Commercial Domini',
                'password' => Hash::make('password123'),
                'role' => 'commercial',
                'telephone' => '+225 07 00 11 22 33',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Rattacher les entreprises sans commercial au compte démo (utile après EntrepriseSeeder).
        $linked = Entreprise::query()
            ->whereNull('commercial_id')
            ->update(['commercial_id' => $commercial->id]);

        $this->command->info('Email commercial : commercial@domini.com');
        if ($commercial->wasRecentlyCreated) {
            $this->command->info('Compte commercial créé — mot de passe : password123');
        } else {
            $this->command->info('Compte commercial déjà présent — aucune modification du mot de passe.');
        }
        if ($linked > 0) {
            $this->command->info("{$linked} entreprise(s) rattachée(s) à ce commercial.");
        }
    }
}
