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
            WarehouseSeeder::class,        // 2. Entrepôt id 1 (communes / trajets)
            EntrepriseSeeder::class,       // 3. Entreprises (ids 1–7 pour EmployeSeeder)
            CommuneEntrepriseSeeder::class, // 4. Communes entrepôt 1 + liaison + entreprises démo
            CommercialUserSeeder::class,   // 5. Commercial démo + rattachement entreprises
            EmployeSeeder::class,          // 6. Employés (dépendent des entreprises)
            LivreurSeeder::class,          // 7. Livreurs
            CategorieSeeder::class,        // 8. Catégories de plats
            PlatSeeder::class,             // 9. Plats (dépendent des catégories)
            CommandeSeeder::class,         // 10. Commandes (dépendent des employés et plats)
            LivraisonSeeder::class,        // 11. Livraisons (dépendent des commandes et livreurs)
            PaiementSeeder::class,         // 12. Paiements (dépendent des commandes)
            SupportTicketSeeder::class,    // 13. Tickets support (employés / livreurs)
        ]);

        $this->command->info('✅ Base de données peuplée avec succès !');
        $this->command->info('📊 Données créées :');
        $this->command->info('   - 1 Admin');
        $this->command->info('   - 1 Commercial (commercial@domini.com / password123)');
        $this->command->info('   - 1 Entrepôt (id 1) + communes + entreprises liées');
        $this->command->info('   - 7 Entreprises (seed de base) + 3 entreprises démo avec commune');
        $this->command->info('   - 15 Employés');
        $this->command->info('   - 8 Livreurs');
        $this->command->info('   - 7 Catégories');
        $this->command->info('   - 11 Plats avec accompagnements et options');
        $this->command->info('   - 8 Commandes');
        $this->command->info('   - 6 Livraisons');
        $this->command->info('   - 14 Paiements');
        $this->command->info('   - 5 Conversations support (tickets démo)');
    }
}
