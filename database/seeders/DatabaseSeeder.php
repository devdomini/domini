<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordre important pour respecter les dépendances
        $this->call([
            AdminUserSeeder::class,        // 1. Admin d'abord
            EntrepriseSeeder::class,       // 2. Entreprises
            EmployeSeeder::class,          // 3. Employés (dépendent des entreprises)
            LivreurSeeder::class,          // 4. Livreurs
            CategorieSeeder::class,        // 5. Catégories de plats
            PlatSeeder::class,             // 6. Plats (dépendent des catégories)
            CommandeSeeder::class,         // 7. Commandes (dépendent des employés et plats)
            LivraisonSeeder::class,        // 8. Livraisons (dépendent des commandes et livreurs)
            PaiementSeeder::class,         // 9. Paiements (dépendent des commandes)
        ]);

        $this->command->info('✅ Base de données peuplée avec succès !');
        $this->command->info('📊 Données créées :');
        $this->command->info('   - 1 Admin');
        $this->command->info('   - 7 Entreprises');
        $this->command->info('   - 15 Employés');
        $this->command->info('   - 8 Livreurs');
        $this->command->info('   - 7 Catégories');
        $this->command->info('   - 11 Plats avec accompagnements et options');
        $this->command->info('   - 8 Commandes');
        $this->command->info('   - 6 Livraisons');
        $this->command->info('   - 14 Paiements');
    }
}
