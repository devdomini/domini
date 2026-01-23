<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\ItemCommande;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommandeSeeder extends Seeder
{
    public function run(): void
    {
        $employes = User::where('role', 'employe')->get();

        // Commande 1 - En attente
        $commande1 = Commande::create([
            'ref' => Commande::generateRef(),
            'id_employe' => $employes[0]->id,
            'montant_total' => 2500,
            'statut_commande' => 'en_attente',
            'statut_preparation' => 'en_attente',
            'statut_livraison' => 'en_attente',
            'lieu' => 'Orange CI, Boulevard Lagunaire, Zone 4C',
            'lat' => 5.3364,
            'long' => -4.0267,
            'consigne_cuisinier' => 'Pas trop épicé svp',
            'consigne_livreur' => 'Appeler en arrivant, bureau au 2ème étage',
            'statut_paiement' => 'en_attente',
            'mode_paiement' => 'mobile_money',
            'numero_telephone' => $employes[0]->telephone,
        ]);

        ItemCommande::create([
            'commande_id' => $commande1->id,
            'plat_id' => 1,
            'accompagnements' => [['nom' => 'Alloco', 'quantite' => 1]],
            'options' => [['nom' => 'Sauce pimentée', 'quantite' => 1], ['nom' => 'Citron', 'quantite' => 2]],
            'prix' => 2500,
            'is_subventionne' => true,
            'quantite' => 1,
        ]);

        // Commande 2 - Confirmée
        $commande2 = Commande::create([
            'ref' => Commande::generateRef(),
            'id_employe' => $employes[1]->id,
            'montant_total' => 3000,
            'statut_commande' => 'confirmee',
            'statut_preparation' => 'en_cours',
            'statut_livraison' => 'en_attente',
            'lieu' => 'Orange CI, Boulevard Lagunaire, Zone 4C',
            'lat' => 5.3364,
            'long' => -4.0267,
            'consigne_livreur' => 'Bureau A-102',
            'statut_paiement' => 'paye',
            'mode_paiement' => 'wallet',
            'numero_telephone' => $employes[1]->telephone,
        ]);

        ItemCommande::create([
            'commande_id' => $commande2->id,
            'plat_id' => 4,
            'accompagnements' => [['nom' => 'Légumes grillés', 'quantite' => 1]],
            'options' => [['nom' => 'Sauce poivre', 'quantite' => 1]],
            'prix' => 3000,
            'is_subventionne' => true,
            'quantite' => 1,
        ]);

        // Commande 3 - Prête à livrer
        $commande3 = Commande::create([
            'ref' => Commande::generateRef(),
            'id_employe' => $employes[3]->id,
            'montant_total' => 2300,
            'statut_commande' => 'confirmee',
            'statut_preparation' => 'prete',
            'statut_livraison' => 'en_attente',
            'lieu' => 'MTN CI, Immeuble Alpha 2000, Plateau',
            'lat' => 5.3254,
            'long' => -4.0168,
            'consigne_cuisinier' => 'Bien cuit',
            'consigne_livreur' => 'Entrée principale, demander Fatou',
            'statut_paiement' => 'paye',
            'mode_paiement' => 'mobile_money',
            'numero_telephone' => $employes[3]->telephone,
        ]);

        ItemCommande::create([
            'commande_id' => $commande3->id,
            'plat_id' => 6,
            'accompagnements' => [['nom' => 'Nems', 'quantite' => 2]],
            'options' => [['nom' => 'Sauce soja', 'quantite' => 2], ['nom' => 'Piment', 'quantite' => 1]],
            'prix' => 2300,
            'is_subventionne' => false,
            'quantite' => 1,
        ]);

        // Commande 4 - En livraison
        $commande4 = Commande::create([
            'ref' => Commande::generateRef(),
            'id_employe' => $employes[6]->id,
            'montant_total' => 2000,
            'statut_commande' => 'confirmee',
            'statut_preparation' => 'prete',
            'statut_livraison' => 'en_cours',
            'lieu' => 'Société Générale CI, Avenue Nogués, Plateau',
            'lat' => 5.3234,
            'long' => -4.0145,
            'consigne_livreur' => 'Accueil au RDC',
            'statut_paiement' => 'paye',
            'mode_paiement' => 'wallet',
            'numero_telephone' => $employes[6]->telephone,
        ]);

        ItemCommande::create([
            'commande_id' => $commande4->id,
            'plat_id' => 2,
            'accompagnements' => [['nom' => 'Alloco', 'quantite' => 1]],
            'options' => [],
            'prix' => 2000,
            'is_subventionne' => true,
            'quantite' => 1,
        ]);

        // Commande 5 - Livrée
        $commande5 = Commande::create([
            'ref' => Commande::generateRef(),
            'id_employe' => $employes[8]->id,
            'montant_total' => 2700,
            'statut_commande' => 'terminee',
            'statut_preparation' => 'prete',
            'statut_livraison' => 'livree',
            'lieu' => 'Nestlé CI, Zone Industrielle de Yopougon',
            'lat' => 5.3453,
            'long' => -4.0923,
            'consigne_livreur' => 'Entrée principale, badge obligatoire',
            'statut_paiement' => 'paye',
            'mode_paiement' => 'mobile_money',
            'numero_telephone' => $employes[8]->telephone,
        ]);

        ItemCommande::create([
            'commande_id' => $commande5->id,
            'plat_id' => 8,
            'accompagnements' => [['nom' => 'Frites', 'quantite' => 1]],
            'options' => [['nom' => 'Sauce barbecue', 'quantite' => 2]],
            'prix' => 2700,
            'is_subventionne' => true,
            'quantite' => 1,
        ]);

        // Commande 6 - Livrée
        $commande6 = Commande::create([
            'ref' => Commande::generateRef(),
            'id_employe' => $employes[9]->id,
            'montant_total' => 2200,
            'statut_commande' => 'terminee',
            'statut_preparation' => 'prete',
            'statut_livraison' => 'livree',
            'lieu' => 'Nestlé CI, Zone Industrielle de Yopougon',
            'lat' => 5.3453,
            'long' => -4.0923,
            'statut_paiement' => 'paye',
            'mode_paiement' => 'wallet',
            'numero_telephone' => $employes[9]->telephone,
        ]);

        ItemCommande::create([
            'commande_id' => $commande6->id,
            'plat_id' => 9,
            'accompagnements' => [],
            'options' => [['nom' => 'Pain grillé', 'quantite' => 2]],
            'prix' => 2200,
            'is_subventionne' => false,
            'quantite' => 1,
        ]);

        // Commande 7 - Annulée
        $commande7 = Commande::create([
            'ref' => Commande::generateRef(),
            'id_employe' => $employes[11]->id,
            'montant_total' => 2400,
            'statut_commande' => 'annulee',
            'statut_preparation' => 'en_attente',
            'statut_livraison' => 'echec',
            'lieu' => 'Bloomfield Investment, Rue Gourgas, Plateau',
            'lat' => 5.3198,
            'long' => -4.0234,
            'statut_paiement' => 'rembourse',
            'mode_paiement' => 'especes',
            'numero_telephone' => $employes[11]->telephone,
        ]);

        ItemCommande::create([
            'commande_id' => $commande7->id,
            'plat_id' => 10,
            'accompagnements' => [['nom' => 'Pain à l\'ail', 'quantite' => 2]],
            'options' => [['nom' => 'Parmesan', 'quantite' => 1]],
            'prix' => 2400,
            'is_subventionne' => true,
            'quantite' => 1,
        ]);

        // Commande 8 - Multiple items
        $commande8 = Commande::create([
            'ref' => Commande::generateRef(),
            'id_employe' => $employes[13]->id,
            'montant_total' => 5000,
            'statut_commande' => 'confirmee',
            'statut_preparation' => 'en_cours',
            'statut_livraison' => 'en_attente',
            'lieu' => 'NSIA Banque, Boulevard Carde, Plateau',
            'lat' => 5.3176,
            'long' => -4.0287,
            'consigne_cuisinier' => '2 plats séparés SVP',
            'consigne_livreur' => 'Bureau F-601',
            'statut_paiement' => 'paye',
            'mode_paiement' => 'wallet',
            'numero_telephone' => $employes[13]->telephone,
        ]);

        ItemCommande::create([
            'commande_id' => $commande8->id,
            'plat_id' => 1,
            'accompagnements' => [['nom' => 'Alloco', 'quantite' => 1]],
            'options' => [],
            'prix' => 2500,
            'is_subventionne' => true,
            'quantite' => 1,
        ]);

        ItemCommande::create([
            'commande_id' => $commande8->id,
            'plat_id' => 4,
            'accompagnements' => [['nom' => 'Légumes grillés', 'quantite' => 1]],
            'options' => [['nom' => 'Sauce champignon', 'quantite' => 1]],
            'prix' => 3000,
            'is_subventionne' => false,
            'quantite' => 1,
        ]);
    }
}
