<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commande;
use App\Models\ItemCommande;
use App\Models\Livraison;
use App\Models\User;
use App\Models\Plat;
use App\Models\Entreprise;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CommandeRepartitionSeeder extends Seeder
{
    // ─── Point d'origine : cuisine / entrepôt Domini ─────────────────────
    const ORIGIN_LAT = 5.3599517;
    const ORIGIN_LNG = -4.0082563;

    /**
     * Distance Haversine en kilomètres entre deux points GPS.
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R   = 6371; // rayon Terre en km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a   = sin($dLat / 2) ** 2
             + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Durée estimée en minutes selon la distance (trafic Abidjan ~20 km/h).
     * Retourne aussi le montant livraison (500 FCFA/km, min 500).
     */
    private function estimerLivraison(float $distKm): array
    {
        $vitesse   = 20; // km/h moyen en ville
        $dureeMin  = (int) ceil(($distKm / $vitesse) * 60) + rand(2, 8); // +aléa trafic
        $montant   = max(500, (int) ceil($distKm * 500 / 1) * 1); // 500 FCFA/km
        return ['duree_min' => $dureeMin, 'montant' => $montant];
    }

    public function run(): void
    {
        // ─── Données réelles ───────────────────────────────────────────────
        $livreurIds = [34, 37]; // Abdoulaye Dolo, idrissa kan
        $platIds    = range(1, 22);

        // Charger les entreprises avec leurs vraies coordonnées
        $entreprises = Entreprise::whereNotNull('lat')
            ->whereNotNull('long')
            ->get(['id', 'nom', 'adresse', 'lat', 'long'])
            ->keyBy('id');

        // Employés avec entreprise ayant des coordonnées
        $employes = User::where('role', 'employe')
            ->whereIn('id_entreprise', $entreprises->keys())
            ->get(['id', 'id_entreprise', 'telephone']);

        if ($employes->isEmpty()) {
            $this->command->error('Aucun employé avec entreprise géolocalisée !');
            return;
        }

        $total     = 400;
        $statuts   = ['en_attente']; // toutes les commandes en attente
        $modesPay  = ['mobile_money', 'mobile_money', 'wallet', 'especes'];
        $creneaux  = ['11h30-12h00', '12h00-12h30', '12h30-13h00', '13h00-13h30'];

        $nbLivreurs = count($livreurIds);
        $this->command->info("Création de {$total} commandes (coordonnées réelles des entreprises)...");
        $this->command->info("Entreprises chargées : " . $entreprises->count());

        $bar = $this->command->getOutput()->createProgressBar($total);
        $bar->start();

        for ($i = 0; $i < $total; $i++) {
            // ─── Employé + entreprise ────────────────────────────────────
            $employe    = $employes->random();
            $entreprise = $entreprises->get($employe->id_entreprise);
            $livreurId  = $livreurIds[$i % count($livreurIds)];
            $statut     = $statuts[array_rand($statuts)];
            $modePay    = $modesPay[array_rand($modesPay)];
            $creneau    = $creneaux[array_rand($creneaux)];

            // ─── Coordonnées réelles de l'entreprise ─────────────────────
            $lat  = (float) $entreprise->lat;
            $lng  = (float) $entreprise->long;
            $lieu = $entreprise->adresse ?? $entreprise->nom;

            // ─── Distance & durée de livraison ───────────────────────────
            $distKm   = $this->haversine(self::ORIGIN_LAT, self::ORIGIN_LNG, $lat, $lng);
            $livraison = $this->estimerLivraison($distKm);
            $dureeMin  = $livraison['duree_min'];
            $montantLivraison = $livraison['montant'];

            // ─── Horaires (commande matin, livraison midi) ────────────────
            $dateCmd  = Carbon::today()->setHour(rand(8, 10))->setMinute(rand(0, 59));
            $dateLivr = Carbon::today()->setHour(rand(12, 13))->setMinute(rand(0, 30));

            // ─── Référence unique ─────────────────────────────────────────
            $ref = 'CMD-' . $dateCmd->format('Ymd') . '-' . strtoupper(Str::random(6));

            // ─── Statuts dérivés ──────────────────────────────────────────
            [$statutPrep, $statutLivr, $statutPay, $statutLivraisonModel] = ['en_attente', 'en_attente', 'en_attente', 'en_attente'];

            // ─── Créer la commande ────────────────────────────────────────
            $commande = Commande::create([
                'ref'                => $ref,
                'id_employe'         => $employe->id,
                'montant_total'      => 0,
                'is_lunch'           => true,
                'date_livraison'     => $dateLivr->toDateString(),
                'creneau'            => $creneau,
                'statut_commande'    => $statut,
                'statut_preparation' => $statutPrep,
                'statut_livraison'   => $statutLivr,
                'lieu'               => $lieu,
                'lat'                => $lat,   // ← vraie coordonnée entreprise
                'long'               => $lng,   // ← vraie coordonnée entreprise
                'statut_paiement'    => $statutPay,
                'mode_paiement'      => $modePay,
                'numero_telephone'   => $employe->telephone ?? '+2250700000000',
                'created_at'         => $dateCmd,
                'updated_at'         => $dateCmd,
            ]);

            // ─── Items ────────────────────────────────────────────────────
            $nbPlats     = rand(1, 2);
            $platsChoix  = collect($platIds)->shuffle()->take($nbPlats);
            $montantTotal = 0;

            foreach ($platsChoix as $platId) {
                $plat = Plat::find($platId);
                if (!$plat) continue;
                $prix = (float) $plat->prix;
                $montantTotal += $prix;

                ItemCommande::create([
                    'commande_id'     => $commande->id,
                    'plat_id'         => $platId,
                    'quantite'        => 1,
                    'prix'            => $prix,
                    'is_subventionne' => ($modePay === 'wallet'),
                    'accompagnements' => null,
                    'options'         => null,
                    'created_at'      => $dateCmd,
                    'updated_at'      => $dateCmd,
                ]);
            }

            $commande->update(['montant_total' => $montantTotal]);

            // ─── Livraison avec durée calculée ───────────────────────────
            $heureAssign = (clone $dateLivr)->subMinutes($dureeMin + rand(5, 15));
            $heurePrise  = (clone $heureAssign)->addMinutes(rand(3, 8));
            $heureRecup  = (clone $heurePrise)->addMinutes(rand(5, 12));
            $heureLivr   = (clone $heureRecup)->addMinutes($dureeMin); // durée réelle trajet

            Livraison::create([
                'commande_id'           => $commande->id,
                'livreur_id'            => $livreurId,
                'client_id'             => $employe->id,
                'montant_livraison'     => $montantLivraison, // ← calculé selon distance
                'statut'                => $statutLivraisonModel,
                'heure_assignation'     => $heureAssign,
                'heure_prise_en_charge' => $heurePrise,
                'heure_recuperation'    => ($statut !== 'annulee') ? $heureRecup : null,
                'heure_livraison'       => ($statut === 'terminee') ? $heureLivr : null,
                'commentaire'           => null,
                'refused_livreur_ids'   => null,
                'created_at'            => $dateCmd,
                'updated_at'            => $dateCmd,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine(2);

        // ─── Résumé ───────────────────────────────────────────────────────
        $this->command->info('=== RÉSUMÉ ===');
        $this->command->info('Total commandes : ' . Commande::count());

        foreach ($livreurIds as $lid) {
            $livreur = User::find($lid);
            $nb = Livraison::where('livreur_id', $lid)->count();
            $this->command->info("  Livreur {$livreur->name} : {$nb} livraisons");
        }

        foreach (['en_attente', 'confirmee', 'terminee', 'annulee'] as $s) {
            $nb = Commande::where('statut_commande', $s)->count();
            $this->command->info("  Statut [{$s}] : {$nb}");
        }

        // Distances réelles par entreprise
        $this->command->info('--- Distances depuis cuisine ---');
        foreach ($entreprises as $ent) {
            $dist = round($this->haversine(self::ORIGIN_LAT, self::ORIGIN_LNG, (float)$ent->lat, (float)$ent->long), 2);
            $duree = $this->estimerLivraison($dist)['duree_min'];
            $this->command->info("  {$ent->nom} : {$dist} km | ~{$duree} min");
        }

        $ca = Commande::where('statut_paiement', 'paye')->sum('montant_total');
        $this->command->info('CA total (payées) : ' . number_format($ca, 0, ',', ' ') . ' FCFA');
    }
}
