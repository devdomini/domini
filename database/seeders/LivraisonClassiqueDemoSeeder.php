<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\ItemCommande;
use App\Models\Livraison;
use App\Models\Plat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Crée une livraison **classique** (non lunch) en statut **assignee** pour tests / démo.
 *
 * Prérequis : employés, livreurs et plats déjà seedés (EmployeSeeder, LivreurSeeder, PlatSeeder…).
 *
 *     php artisan db:seed --class=LivraisonClassiqueDemoSeeder
 */
class LivraisonClassiqueDemoSeeder extends Seeder
{
    public function run(): void
    {
        $employe = User::where('role', 'employe')->first();
        $livreur = User::where('id', '26')->where('is_active', true)->first()
            ?? User::where('id', '26')->first();
        $plat = Plat::query()->first();

        if (! $employe || ! $livreur || ! $plat) {
            $this->command->warn('LivraisonClassiqueDemoSeeder : employé, livreur ou plat introuvable. Exécutez d’abord les seeders de base (EmployeSeeder, LivreurSeeder, PlatSeeder).');

            return;
        }

        $commande = Commande::create([
            'ref' => Commande::generateRef(),
            'id_employe' => $employe->id,
            'montant_total' => 3500,
            'is_lunch' => false,
            'statut_commande' => 'confirmee',
            'statut_preparation' => 'prete',
            'statut_livraison' => 'en_attente',
            'lieu' => 'Cocody Riviera, résidence Les Rosiers (seeder classique)',
            'lat' => 5.3520,
            'long' => -3.9890,
            'consigne_livreur' => 'Test seeder : livraison classique, interphone 12B',
            'statut_paiement' => 'paye',
            'mode_paiement' => 'mobile_money',
            'numero_telephone' => $employe->telephone ?? '+225 07 00 00 00 01',
        ]);

        ItemCommande::create([
            'commande_id' => $commande->id,
            'plat_id' => $plat->id,
            'accompagnements' => [],
            'options' => [],
            'prix' => 3500,
            'is_subventionne' => false,
            'quantite' => 1,
        ]);

        Livraison::create([
            'commande_id' => $commande->id,
            'livreur_id' => $livreur->id,
            'client_id' => $employe->id,
            'montant_livraison' => 750,
            'statut' => 'assignee',
            'heure_assignation' => Carbon::now(),
        ]);

        $this->command->info(sprintf(
            'Livraison classique créée : commande #%d (ref %s) → livreur %s (ID %d), statut assignee.',
            $commande->id,
            $commande->ref,
            $livreur->name,
            $livreur->id
        ));
    }
}
