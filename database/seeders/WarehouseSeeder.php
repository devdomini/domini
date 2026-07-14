<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Garantit un entrepôt avec id = 1 (communes, trajets, seed CommuneEntreprise).
     */
    public function run(): void
    {
        if (Warehouse::query()->where('id', 1)->exists()) {
            $this->command?->info('Entrepôt id 1 existe déjà — ignoré.');

            return;
        }

        $now = now();

        DB::table('warehouses')->insert([
            'id' => 1,
            'name' => 'Entrepôt principal Abidjan',
            'address' => 'Zone portuaire, Vridi',
            'city' => 'Abidjan',
            'country' => 'Côte d\'Ivoire',
            'latitude' => 5.2556,
            'longitude' => -3.9292,
            'phone' => '+225 27 20 00 00 00',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->command?->info('Entrepôt id 1 créé (Entrepôt principal Abidjan).');
    }
}
