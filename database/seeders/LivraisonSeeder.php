<?php

namespace Database\Seeders;

use App\Models\Livraison;
use App\Models\Commande;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LivraisonSeeder extends Seeder
{
    public function run(): void
    {
        $livreurs = User::where('role', 'livreur')->get();
        
        // Livraison 1 - En cours (commande 4)
        Livraison::create([
            'commande_id' => 4,
            'livreur_id' => $livreurs[0]->id,
            'client_id' => Commande::find(4)->id_employe,
            'montant_livraison' => 500,
            'statut' => 'en_cours',
            'heure_assignation' => Carbon::now()->subMinutes(30),
            'heure_prise_en_charge' => Carbon::now()->subMinutes(15),
        ]);

        // Livraison 2 - Livrée (commande 5)
        Livraison::create([
            'commande_id' => 5,
            'livreur_id' => $livreurs[1]->id,
            'client_id' => Commande::find(5)->id_employe,
            'montant_livraison' => 500,
            'statut' => 'livree',
            'heure_assignation' => Carbon::now()->subHours(2),
            'heure_prise_en_charge' => Carbon::now()->subHours(1)->subMinutes(45),
            'heure_livraison' => Carbon::now()->subHour(),
            'commentaire' => 'Livraison effectuée avec succès. Client satisfait.',
        ]);

        // Livraison 3 - Livrée (commande 6)
        Livraison::create([
            'commande_id' => 6,
            'livreur_id' => $livreurs[1]->id,
            'client_id' => Commande::find(6)->id_employe,
            'montant_livraison' => 500,
            'statut' => 'livree',
            'heure_assignation' => Carbon::now()->subHours(2)->subMinutes(30),
            'heure_prise_en_charge' => Carbon::now()->subHours(2)->subMinutes(15),
            'heure_livraison' => Carbon::now()->subHours(1)->subMinutes(30),
            'commentaire' => 'Client était au même endroit que la commande précédente.',
        ]);

        // Livraison 4 - Échec (commande 7 annulée)
        Livraison::create([
            'commande_id' => 7,
            'livreur_id' => $livreurs[2]->id,
            'client_id' => Commande::find(7)->id_employe,
            'montant_livraison' => 500,
            'statut' => 'echec',
            'heure_assignation' => Carbon::now()->subHours(3),
            'heure_prise_en_charge' => Carbon::now()->subHours(2)->subMinutes(45),
            'commentaire' => 'Client injoignable. Commande annulée après plusieurs tentatives.',
        ]);

        // Livraison 5 - Assignée (commande 3)
        Livraison::create([
            'commande_id' => 3,
            'livreur_id' => $livreurs[3]->id,
            'client_id' => Commande::find(3)->id_employe,
            'montant_livraison' => 500,
            'statut' => 'assignee',
            'heure_assignation' => Carbon::now()->subMinutes(5),
        ]);

        // Livraison 6 - En cours (commande 8)
        Livraison::create([
            'commande_id' => 8,
            'livreur_id' => $livreurs[4]->id,
            'client_id' => Commande::find(8)->id_employe,
            'montant_livraison' => 500,
            'statut' => 'en_cours',
            'heure_assignation' => Carbon::now()->subMinutes(25),
            'heure_prise_en_charge' => Carbon::now()->subMinutes(10),
        ]);
    }
}
