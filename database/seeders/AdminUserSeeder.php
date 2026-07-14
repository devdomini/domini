<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@domini.com'],
            [
                'name' => 'Admin Domini',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'telephone' => '+225 0123456789',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Email admin : admin@domini.com');
        if ($admin->wasRecentlyCreated) {
            $this->command->info('Compte admin créé — mot de passe : password123');
        } else {
            $this->command->info('Compte admin déjà présent — aucune modification (évite les doublons).');
        }
    }
}
