<?php

namespace Database\Seeders;

use App\Models\Commande;
use App\Models\ItemCommande;
use App\Models\Livraison;
use App\Models\Plat;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseTrajetItem;
use App\Services\DeliveryAssignmentService;
use App\Services\LunchDispatchService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Démo : lots entreprise (is_lunch) + commandes classiques.
 *
 * Règle métier lots : jusqu'à **200 commandes par livreur « entreprise »** (voir LunchDispatchService).
 * Ce seeder crée des commandes lot réparties sur **toutes** les entreprises du trajet
 * puis dispatch séquentiel (livreur 1 = 200 premiers colis du trajet, livreur 2 = 200 suivants, etc.).
 *
 * Par défaut : ceil(200 / nb_entreprises) colis / entreprise **× nb_livreurs lot**
 * (ex. 5 entreprises, 2 livreurs → 40×2 = 80 / entreprise, 400 au total → 200 par livreur).
 *
 * Variables d'environnement optionnelles :
 *   DEMO_ORDERS_PER_ENTREPRISE=80  — force le nb de colis / entreprise (sinon calcul auto)
 *   DEMO_LOTS_PER_LIVREUR=200      — plafond dispatch / livreur lot
 *   DEMO_TRAJET_ENTREPRISES=5      — si trajet vide : N entreprises les plus proches de l’entrepôt
 *
 *     php artisan db:seed --class=DemoLivraisonsSeeder
 */
class DemoLivraisonsSeeder extends Seeder
{
    /** Aligné sur LunchDispatchService::dispatchForWarehouse(..., $maxPerLivreur) */
    private const DEFAULT_MAX_LOTS_PER_LIVREUR = 200;

    private const DEFAULT_TRAJET_ENTREPRISES = 5;

    private const DEMO_CLASSIC_COUNT = 8;

    public function run(): void
    {
        $warehouse = Warehouse::query()->find(1);
        if (! $warehouse) {
            $this->command?->error('Entrepôt id 1 introuvable. Lance d’abord WarehouseSeeder.');

            return;
        }

        $maxPerLivreur = max(1, (int) env('DEMO_LOTS_PER_LIVREUR', self::DEFAULT_MAX_LOTS_PER_LIVREUR));
        $plat = Plat::query()->first();

        // ─── Livreurs « entreprise » (lots) : 2 max, le reste = classique ─────
        $allLivreurs = User::query()
            ->where('role', 'livreur')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        if ($allLivreurs->count() < 3) {
            $this->command?->error('Il faut au moins 3 livreurs actifs (LivreurSeeder).');

            return;
        }

        $lotLivreurs = User::query()
            ->where('role', 'livreur')
            ->where('is_active', true)
            ->where('type_livreur', 'entreprise')
            ->orderBy('id')
            ->limit(2)
            ->get();

        if ($lotLivreurs->count() < 2) {
            $lotLivreurs = $allLivreurs->take(2);
        }

        $lotLivreurIds = $lotLivreurs->pluck('id')->map(fn ($id) => (int) $id)->all();

        foreach ($allLivreurs as $l) {
            $l->update([
                'warehouse_id' => $warehouse->id,
                'is_dispo' => true,
                'type_livreur' => in_array((int) $l->id, $lotLivreurIds, true) ? 'entreprise' : 'classique',
            ]);
        }

        $classicLivreurs = $allLivreurs->filter(
            fn ($l) => ! in_array((int) $l->id, $lotLivreurIds, true)
        );

        $this->command?->info(sprintf(
            'Lots : max %d commandes / livreur entreprise (%d livreur(s) lot).',
            $maxPerLivreur,
            count($lotLivreurIds)
        ));
        foreach ($lotLivreurs as $l) {
            $this->command?->line("   • Lot : {$l->name} (id {$l->id})");
        }
        $this->command?->info('Classique : '.$classicLivreurs->pluck('id')->join(', '));

        $this->ensureTrajet($warehouse);

        $trajetEntrepriseIds = $warehouse->trajetItems()->orderBy('position')->pluck('entreprise_id')->values();
        if ($trajetEntrepriseIds->isEmpty()) {
            $this->command?->error('Aucune entreprise dans le trajet. Vérifiez CommuneEntrepriseSeeder.');

            return;
        }

        $this->ensureDemoEmployesOnTrajet($trajetEntrepriseIds);

        $employes = User::query()
            ->where('role', 'employe')
            ->where('is_active', true)
            ->whereNotNull('id_entreprise')
            ->whereIn('id_entreprise', $trajetEntrepriseIds->all())
            ->whereNotNull('num_box')
            ->orderBy('id')
            ->get();

        if ($employes->isEmpty()) {
            $this->command?->error('Aucun employé avec num_box sur les entreprises du trajet.');

            return;
        }

        $trajetCount = $trajetEntrepriseIds->count();
        $lotLivreurCount = count($lotLivreurIds);
        $explicitPerEnt = env('DEMO_ORDERS_PER_ENTREPRISE');

        if ($explicitPerEnt !== null && $explicitPerEnt !== '') {
            $ordersPerEntreprise = max(1, (int) $explicitPerEnt);
            $ordersPerEntreprisePerLivreur = (int) ceil($ordersPerEntreprise / max(1, $lotLivreurCount));
        } else {
            $ordersPerEntreprisePerLivreur = max(1, (int) ceil($maxPerLivreur / max(1, $trajetCount)));
            $ordersPerEntreprise = $ordersPerEntreprisePerLivreur * max(1, $lotLivreurCount);
        }

        $totalLotsTarget = $ordersPerEntreprise * $trajetCount;
        $dispatchPerLivreur = min($maxPerLivreur, (int) ceil($totalLotsTarget / max(1, $lotLivreurCount)));

        $lotsToCreate = $this->buildLotsToCreateEvenly(
            $employes,
            $trajetEntrepriseIds,
            $ordersPerEntreprise,
            $totalLotsTarget
        );

        $this->command?->info(sprintf(
            'Trajet : %d entreprise(s), %d colis/entreprise (%d×%d livreur(s) lot, ~%d colis/livreur), %d au total — ordre 1→%d.',
            $trajetCount,
            $ordersPerEntreprise,
            $ordersPerEntreprisePerLivreur,
            $lotLivreurCount,
            $dispatchPerLivreur,
            count($lotsToCreate),
            $trajetCount
        ));
        foreach ($trajetEntrepriseIds as $pos => $eid) {
            $nom = DB::table('entreprises')->where('id', $eid)->value('nom') ?? '?';
            $this->command?->line(sprintf('   %d. %s (id %s) — %d colis', $pos + 1, $nom, $eid, $ordersPerEntreprise));
        }

        // ─── 1) Créer les commandes lot (une entreprise du trajet après l’autre) ─
        $date = now()->toDateString();
        $createdLots = 0;

        $this->command?->info("Création de {$totalLotsTarget} commandes lot (date {$date})…");
        $bar = $this->command?->getOutput()->createProgressBar(count($lotsToCreate));
        $bar->start();

        DB::transaction(function () use (
            $lotsToCreate,
            $date,
            $plat,
            &$createdLots,
            $bar
        ) {
            foreach ($lotsToCreate as $pick) {
                /** @var User $u */
                $u = $pick['employe'];

                $commande = Commande::create([
                    'ref' => Commande::generateRef(),
                    'id_employe' => $u->id,
                    'montant_total' => 2500,
                    'is_lunch' => true,
                    'date_livraison' => $date,
                    'creneau' => '12h00-12h30',
                    'statut_commande' => 'confirmee',
                    'statut_preparation' => 'prete',
                    'statut_livraison' => 'en_attente',
                    'lieu' => 'Box '.($u->num_box ?? '—'),
                    'lat' => null,
                    'long' => null,
                    'consigne_livreur' => 'Démo lot #'.($createdLots + 1),
                    'statut_paiement' => 'paye',
                    'mode_paiement' => 'wallet',
                    'numero_telephone' => $u->telephone,
                ]);

                if ($plat) {
                    ItemCommande::create([
                        'commande_id' => $commande->id,
                        'plat_id' => $plat->id,
                        'accompagnements' => [],
                        'options' => [],
                        'prix' => 2500,
                        'is_subventionne' => true,
                        'quantite' => 1,
                    ]);
                }

                Livraison::firstOrCreate(
                    ['commande_id' => $commande->id],
                    [
                        'client_id' => $u->id,
                        'montant_livraison' => 500,
                        'statut' => 'en_attente',
                    ]
                );

                $createdLots++;
                $bar?->advance();
            }
        });

        $bar?->finish();
        $this->command?->newLine(2);

        $this->command?->info('Dispatch lot (sans notifications temps réel, peut prendre 1–2 min)…');
        $dispatch = LunchDispatchService::dispatchForWarehouse(
            $warehouse,
            $date,
            $maxPerLivreur,
            notifyRealtime: false,
            livreurIds: $lotLivreurIds
        );

        if (($dispatch['assigned_count'] ?? 0) === 0) {
            $this->command?->warn('Dispatch lot : '.($dispatch['message'] ?? '0 assignation'));
            if (! empty($dispatch['debug'])) {
                $this->command?->line(json_encode($dispatch['debug'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }

        // ─── 2) Classiques (après les lots) ────────────────────────────────────
        $createdClassic = $this->seedClassics($employes, $plat);

        $this->command?->info("✅ {$createdLots} lots créés, {$createdClassic} classiques créées.");
        $this->command?->info("📦 Dispatch lot (max {$maxPerLivreur}/livreur) : assigned={$dispatch['assigned_count']} unassigned={$dispatch['unassigned_count']}.");
        if (! empty($dispatch['by_livreur'])) {
            foreach ($dispatch['by_livreur'] as $row) {
                $this->command?->line("   → {$row['livreur_name']} (id {$row['livreur_id']}) : {$row['count']} lot(s)");
            }
        }

        $this->logDispatchByEntreprise($date, $trajetEntrepriseIds, $lotLivreurIds);
    }

    private function ensureTrajet(Warehouse $warehouse): void
    {
        if ($warehouse->trajetItems()->count() > 0) {
            return;
        }

        $maxStops = max(1, (int) env('DEMO_TRAJET_ENTREPRISES', self::DEFAULT_TRAJET_ENTREPRISES));
        $whLat = (float) $warehouse->latitude;
        $whLng = (float) $warehouse->longitude;
        $hasWhCoords = $whLat !== 0.0 || $whLng !== 0.0;

        $rows = DB::table('entreprises')
            ->join('communes', 'communes.id', '=', 'entreprises.commune_id')
            ->where('entreprises.statut', 1)
            ->where('communes.warehouse_id', $warehouse->id)
            ->select([
                'entreprises.id',
                'entreprises.nom',
                'entreprises.lat',
                'entreprises.long',
                'communes.latitude as commune_lat',
                'communes.longitude as commune_lng',
            ])
            ->get();

        $scored = [];
        foreach ($rows as $row) {
            $lat = $row->lat !== null && $row->lat !== '' ? (float) $row->lat : (float) ($row->commune_lat ?? 0);
            $lng = $row->long !== null && $row->long !== '' ? (float) $row->long : (float) ($row->commune_lng ?? 0);
            $km = $hasWhCoords && ($lat !== 0.0 || $lng !== 0.0)
                ? $this->haversineKm($whLat, $whLng, $lat, $lng)
                : 999999.0;
            $scored[] = ['id' => (int) $row->id, 'nom' => $row->nom, 'km' => $km];
        }

        usort($scored, fn ($a, $b) => $a['km'] <=> $b['km'] ?: strcmp((string) $a['nom'], (string) $b['nom']));
        $picked = array_slice($scored, 0, $maxStops);

        foreach ($picked as $i => $item) {
            WarehouseTrajetItem::create([
                'warehouse_id' => $warehouse->id,
                'entreprise_id' => $item['id'],
                'position' => $i + 1,
            ]);
        }

        $this->command?->info(sprintf(
            'Trajet auto créé : %d entreprise(s) (proximité entrepôt, max %d).',
            count($picked),
            $maxStops
        ));
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthKm = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthKm * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    /**
     * Récap dispatch par entreprise du trajet (vérifie que les 5 stops sont couverts).
     */
    private function logDispatchByEntreprise(string $date, $trajetEntrepriseIds, array $lotLivreurIds): void
    {
        $this->command?->info('Répartition dispatch par entreprise (ordre trajet) :');

        foreach ($trajetEntrepriseIds as $pos => $eid) {
            $eid = (int) $eid;
            $nom = DB::table('entreprises')->where('id', $eid)->value('nom') ?? '?';

            $byLivreur = DB::table('livraisons')
                ->join('commandes', 'commandes.id', '=', 'livraisons.commande_id')
                ->join('users as employes', 'employes.id', '=', 'commandes.id_employe')
                ->where('commandes.is_lunch', true)
                ->whereDate('commandes.date_livraison', $date)
                ->where('employes.id_entreprise', $eid)
                ->whereIn('livraisons.livreur_id', $lotLivreurIds)
                ->whereIn('livraisons.statut', ['assignee', 'en_cours', 'livree'])
                ->select('livraisons.livreur_id', DB::raw('COUNT(*) as c'))
                ->groupBy('livraisons.livreur_id')
                ->pluck('c', 'livreur_id');

            $total = (int) $byLivreur->sum();
            $detail = $byLivreur->isEmpty()
                ? 'aucune assignation'
                : $byLivreur->map(fn ($c, $lid) => "livreur {$lid}={$c}")->join(', ');

            $this->command?->line(sprintf('   %d. %s : %d colis (%s)', $pos + 1, $nom, $total, $detail));
        }
    }

    /**
     * Au moins un employé avec num_box par entreprise du trajet (sinon tout le lot se concentre sur 1–2 stops).
     */
    private function ensureDemoEmployesOnTrajet($trajetEntrepriseIds): void
    {
        $created = 0;
        foreach ($trajetEntrepriseIds as $index => $entrepriseId) {
            $eid = (int) $entrepriseId;
            $exists = User::query()
                ->where('role', 'employe')
                ->where('is_active', true)
                ->where('id_entreprise', $eid)
                ->whereNotNull('num_box')
                ->exists();

            if ($exists) {
                continue;
            }

            $slug = Str::slug('demo-ent-'.$eid);
            User::firstOrCreate(
                ['email' => "demo.box.{$slug}@domini.local"],
                [
                    'name' => 'Employé démo trajet #'.($index + 1),
                    'telephone' => '+225 07 00 00 '.str_pad((string) ($eid % 10000), 4, '0', STR_PAD_LEFT),
                    'id_entreprise' => $eid,
                    'num_box' => 'T-'.$eid.'-01',
                    'role' => 'employe',
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                ]
            );
            $created++;
        }

        if ($created > 0) {
            $this->command?->info("Employés démo créés pour {$created} entreprise(s) sans num_box.");
        }
    }

    /**
     * Répartition équitable : N commandes par entreprise du trajet, dans l’ordre des positions.
     *
     * @return list<array{employe: User, entreprise_id: int}>
     */
    private function buildLotsToCreateEvenly($employes, $trajetEntrepriseIds, int $ordersPerEntreprise, int $totalLotsTarget): array
    {
        $byEntreprise = $employes->groupBy(fn ($u) => (int) $u->id_entreprise);
        $lots = [];

        foreach ($trajetEntrepriseIds as $entrepriseId) {
            $eid = (int) $entrepriseId;
            $group = $byEntreprise->get($eid, collect());
            if ($group->isEmpty()) {
                $this->command?->warn("   ⚠ Entreprise id {$eid} : aucun employé num_box — colis ignorés pour ce stop.");

                continue;
            }
            $items = $group->values()->all();
            for ($n = 0; $n < $ordersPerEntreprise && count($lots) < $totalLotsTarget; $n++) {
                $u = $items[$n % count($items)];
                $lots[] = ['employe' => $u, 'entreprise_id' => $eid];
            }
        }

        return $lots;
    }

    private function seedClassics($employes, ?Plat $plat): int
    {
        $created = 0;
        $list = $employes->values();

        for ($i = 0; $i < self::DEMO_CLASSIC_COUNT; $i++) {
            $u = $list[$i % $list->count()];

            $commande = Commande::create([
                'ref' => Commande::generateRef(),
                'id_employe' => $u->id,
                'montant_total' => 3500,
                'is_lunch' => false,
                'statut_commande' => 'confirmee',
                'statut_preparation' => 'prete',
                'statut_livraison' => 'en_attente',
                'lieu' => 'Livraison domicile (démo classique)',
                'lat' => 5.3200 + (mt_rand(-30, 30) / 10000),
                'long' => -4.0200 + (mt_rand(-30, 30) / 10000),
                'consigne_livreur' => 'Démo classique',
                'statut_paiement' => 'paye',
                'mode_paiement' => 'mobile_money',
                'numero_telephone' => $u->telephone,
            ]);

            if ($plat) {
                ItemCommande::create([
                    'commande_id' => $commande->id,
                    'plat_id' => $plat->id,
                    'accompagnements' => [],
                    'options' => [],
                    'prix' => 3500,
                    'is_subventionne' => false,
                    'quantite' => 1,
                ]);
            }

            try {
                DeliveryAssignmentService::createAndAssignForCommande($commande);
            } catch (\Throwable $e) {
                DeliveryAssignmentService::createIfMissing($commande);
            }

            $created++;
        }

        return $created;
    }
}
