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
        User::create([
            'name' => 'Admin Domini',
            'email' => 'admin@domini.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'telephone' => '+225 0123456789',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@domini.com');
        $this->command->info('Password: password123');
    }
}
