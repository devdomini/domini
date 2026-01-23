<?php

namespace Database\Seeders;

use App\Models\Paiement;
use App\Models\Commande;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PaiementSeeder extends Seeder
{
    public function run(): void
    {
        // Paiements pour les commandes validées
        
        // Paiement commande 2
        Paiement::create([
            'type' => 'commande',
            'montant' => 3000,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'wallet',
            'statut' => 'valide',
            'commande_id' => 2,
            'user_id' => Commande::find(2)->id_employe,
            'description' => 'Paiement pour commande ' . Commande::find(2)->ref,
            'created_at' => Carbon::now()->subHours(2),
        ]);

        // Paiement commande 3
        Paiement::create([
            'type' => 'commande',
            'montant' => 2300,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'mobile_money',
            'statut' => 'valide',
            'commande_id' => 3,
            'user_id' => Commande::find(3)->id_employe,
            'description' => 'Paiement pour commande ' . Commande::find(3)->ref,
            'created_at' => Carbon::now()->subHour(),
        ]);

        // Paiement commande 4
        Paiement::create([
            'type' => 'commande',
            'montant' => 2000,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'wallet',
            'statut' => 'valide',
            'commande_id' => 4,
            'user_id' => Commande::find(4)->id_employe,
            'description' => 'Paiement pour commande ' . Commande::find(4)->ref,
            'created_at' => Carbon::now()->subMinutes(45),
        ]);

        // Paiement commande 5 + livraison
        Paiement::create([
            'type' => 'commande',
            'montant' => 2700,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'mobile_money',
            'statut' => 'valide',
            'commande_id' => 5,
            'user_id' => Commande::find(5)->id_employe,
            'description' => 'Paiement pour commande ' . Commande::find(5)->ref,
            'created_at' => Carbon::now()->subHours(2)->subMinutes(15),
        ]);

        Paiement::create([
            'type' => 'paiement_livraison',
            'montant' => 500,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'mobile_money',
            'statut' => 'valide',
            'commande_id' => 5,
            'user_id' => Commande::find(5)->id_employe,
            'description' => 'Frais de livraison pour commande ' . Commande::find(5)->ref,
            'created_at' => Carbon::now()->subHours(2)->subMinutes(15),
        ]);

        // Paiement commande 6
        Paiement::create([
            'type' => 'commande',
            'montant' => 2200,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'wallet',
            'statut' => 'valide',
            'commande_id' => 6,
            'user_id' => Commande::find(6)->id_employe,
            'description' => 'Paiement pour commande ' . Commande::find(6)->ref,
            'created_at' => Carbon::now()->subHours(2)->subMinutes(40),
        ]);

        Paiement::create([
            'type' => 'paiement_livraison',
            'montant' => 500,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'wallet',
            'statut' => 'valide',
            'commande_id' => 6,
            'user_id' => Commande::find(6)->id_employe,
            'description' => 'Frais de livraison pour commande ' . Commande::find(6)->ref,
            'created_at' => Carbon::now()->subHours(2)->subMinutes(40),
        ]);

        // Paiement commande 7 (remboursé car annulée)
        Paiement::create([
            'type' => 'commande',
            'montant' => 2400,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'especes',
            'statut' => 'rembourse',
            'commande_id' => 7,
            'user_id' => Commande::find(7)->id_employe,
            'description' => 'Paiement pour commande ' . Commande::find(7)->ref . ' (annulée)',
            'created_at' => Carbon::now()->subHours(3)->subMinutes(20),
        ]);

        Paiement::create([
            'type' => 'remboursement',
            'montant' => 2400,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'especes',
            'statut' => 'valide',
            'commande_id' => 7,
            'user_id' => Commande::find(7)->id_employe,
            'description' => 'Remboursement pour commande annulée ' . Commande::find(7)->ref,
            'created_at' => Carbon::now()->subHours(2)->subMinutes(50),
        ]);

        // Paiement commande 8
        Paiement::create([
            'type' => 'commande',
            'montant' => 5000,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'wallet',
            'statut' => 'valide',
            'commande_id' => 8,
            'user_id' => Commande::find(8)->id_employe,
            'description' => 'Paiement pour commande ' . Commande::find(8)->ref,
            'created_at' => Carbon::now()->subMinutes(30),
        ]);

        // Paiement en attente (commande 1)
        Paiement::create([
            'type' => 'commande',
            'montant' => 2500,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'mobile_money',
            'statut' => 'en_attente',
            'commande_id' => 1,
            'user_id' => Commande::find(1)->id_employe,
            'description' => 'Paiement pour commande ' . Commande::find(1)->ref,
            'created_at' => Carbon::now()->subMinutes(10),
        ]);

        // Paiements d'abonnement (exemples)
        Paiement::create([
            'type' => 'abonnement',
            'montant' => 50000,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'virement',
            'statut' => 'valide',
            'user_id' => 1, // Entreprise 1
            'description' => 'Abonnement mensuel Orange CI',
            'metadata' => json_encode([
                'entreprise_id' => 1,
                'periode' => 'Janvier 2026',
                'nombre_employes' => 50,
            ]),
            'created_at' => Carbon::now()->subDays(15),
        ]);

        Paiement::create([
            'type' => 'abonnement',
            'montant' => 45000,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'virement',
            'statut' => 'valide',
            'user_id' => 4, // Entreprise 2
            'description' => 'Abonnement mensuel MTN CI',
            'metadata' => json_encode([
                'entreprise_id' => 2,
                'periode' => 'Janvier 2026',
                'nombre_employes' => 45,
            ]),
            'created_at' => Carbon::now()->subDays(12),
        ]);

        Paiement::create([
            'type' => 'abonnement',
            'montant' => 35000,
            'ref' => Paiement::generateRef(),
            'mode_paiement' => 'carte',
            'statut' => 'en_attente',
            'user_id' => 7, // Entreprise 3
            'description' => 'Abonnement mensuel Société Générale CI',
            'metadata' => json_encode([
                'entreprise_id' => 3,
                'periode' => 'Janvier 2026',
                'nombre_employes' => 35,
            ]),
            'created_at' => Carbon::now()->subDays(2),
        ]);
    }
}
