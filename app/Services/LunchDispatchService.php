<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Livraison;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\LivraisonAssignmentNotifier;

class LunchDispatchService
{
    /**
     * Dispatch séquentiel des commandes is_lunch (max N par livreur) selon l'ordre du trajet.
     *
     * @return array<string,mixed>
     */
    public static function dispatchForWarehouse(
        Warehouse $warehouse,
        string $deliveryDate,
        int $maxPerLivreur = 200,
        bool $notifyRealtime = true,
        ?array $livreurIds = null
    ): array {
        return self::dispatchForWarehouseToLivreurs($warehouse, $deliveryDate, $maxPerLivreur, $livreurIds, $notifyRealtime);
    }

    /**
     * Dispatch séquentiel vers une liste de livreurs (optionnel).
     * - Si $livreurIds est null: tous les livreurs actifs de l'entrepôt.
     * - Si $livreurIds est fourni: dispatch uniquement vers ces livreurs (ex: un seul user_id).
     *
     * @param array<int,int>|null $livreurIds
     * @return array<string,mixed>
     */
    public static function dispatchForWarehouseToLivreurs(
        Warehouse $warehouse,
        string $deliveryDate,
        int $maxPerLivreur = 200,
        ?array $livreurIds = null,
        bool $notifyRealtime = true
    ): array {
        $maxPerLivreur = max(1, $maxPerLivreur);

        $livreursQuery = User::query()
            ->where('role', 'livreur')
            ->where('is_active', true)
            ->where('warehouse_id', $warehouse->id);

        if (is_array($livreurIds) && count($livreurIds) > 0) {
            $livreursQuery->whereIn('id', array_values(array_unique(array_map('intval', $livreurIds))));
        } else {
            // Dispatch lots → livreurs "entreprise" uniquement
            $livreursQuery->where('type_livreur', 'entreprise');
            // Dispo uniquement (pas de livraisons actives en cours)
            $livreursQuery->whereDoesntHave('livraisons', function ($q) {
                $q->whereIn('statut', ['assignee', 'en_cours']);
            });
        }

        $livreurs = $livreursQuery->orderBy('id')->get(['id', 'name']);

        if ($livreurs->isEmpty()) {
            return [
                'assigned_count' => 0,
                'unassigned_count' => 0,
                'skipped_not_in_trajet' => 0,
                'debug' => [
                    'delivery_date' => $deliveryDate,
                    'livreurs_total' => 0,
                    'trajet_entreprises_total' => 0,
                    'candidates_total' => 0,
                    'candidates_in_trajet_total' => 0,
                    'ordered_total' => 0,
                ],
                'message' => 'Aucun livreur actif lié à cet entrepôt.',
                'by_livreur' => [],
            ];
        }

        $trajetEntrepriseIds = $warehouse->trajetItems()
            ->orderBy('position')
            ->pluck('entreprise_id')
            ->values();

        if ($trajetEntrepriseIds->isEmpty()) {
            return [
                'assigned_count' => 0,
                'unassigned_count' => 0,
                'skipped_not_in_trajet' => 0,
                'debug' => [
                    'delivery_date' => $deliveryDate,
                    'livreurs_total' => $livreurs->count(),
                    'trajet_entreprises_total' => 0,
                    'candidates_total' => 0,
                    'candidates_in_trajet_total' => 0,
                    'ordered_total' => 0,
                ],
                'message' => 'Aucun trajet défini pour cet entrepôt.',
                'by_livreur' => [],
            ];
        }

        // Candidats is_lunch du jour (hors annulées, hors terminées/livrées)
        $baseQuery = Commande::query()
            ->where('is_lunch', true)
            ->whereDate('date_livraison', $deliveryDate)
            ->where('statut_commande', '!=', 'annulee')
            ->where(function ($q) {
                $q->whereNull('statut_livraison')
                    ->orWhereNotIn('statut_livraison', ['livree']);
            })
            ->whereHas('employe.entreprise')
            ->with(['employe.entreprise', 'livraison']);

        $allCandidates = $baseQuery->get();

        $byEntreprise = $allCandidates
            ->filter(fn (Commande $c) => $c->employe && $c->employe->entreprise)
            ->groupBy(fn (Commande $c) => (int) $c->employe->entreprise->id);

        $orderedCommandes = collect();
        foreach ($trajetEntrepriseIds as $entrepriseId) {
            /** @var \Illuminate\Support\Collection<int,\App\Models\Commande> $group */
            $group = $byEntreprise->get((int) $entrepriseId, collect());
            if ($group->isEmpty()) {
                continue;
            }
            $sorted = $group->sortBy('created_at')->values();
            $orderedCommandes = $orderedCommandes->concat($sorted);
        }

        $inTrajetIds = $trajetEntrepriseIds->map(fn ($id) => (int) $id)->all();

        $candidatesInTrajetTotal = $allCandidates->filter(function (Commande $c) use ($inTrajetIds) {
            $eid = (int) optional(optional($c->employe)->entreprise)->id;
            return $eid > 0 && in_array($eid, $inTrajetIds, true);
        })->count();

        $skippedNotInTrajet = $allCandidates->filter(function (Commande $c) use ($inTrajetIds) {
            $eid = (int) optional(optional($c->employe)->entreprise)->id;
            return $eid > 0 && !in_array($eid, $inTrajetIds, true);
        })->count();

        $assignedByLivreur = [];
        foreach ($livreurs as $livreur) {
            $assignedByLivreur[$livreur->id] = [
                'livreur_id' => $livreur->id,
                'livreur_name' => $livreur->name,
                'count' => 0,
            ];
        }

        $assignedCount = 0;
        $unassignedCount = 0;
        $orderedList = $orderedCommandes->values()->all();
        $cursor = 0;
        $totalOrdered = count($orderedList);

        // Blocs séquentiels : livreur 1 = positions 0..199 du trajet, livreur 2 = 200..399, etc.
        // (pas de round-robin 1/2 qui casse l’ordre du trajet pour le 2e livreur)
        DB::transaction(function () use (
            $orderedList,
            $livreurs,
            $maxPerLivreur,
            $notifyRealtime,
            &$cursor,
            &$assignedCount,
            &$unassignedCount,
            &$assignedByLivreur
        ) {
            foreach ($livreurs as $livreur) {
                $chunk = array_slice($orderedList, $cursor, $maxPerLivreur);
                if ($chunk === []) {
                    break;
                }

                $chunkCount = 0;
                $sampleCommande = null;
                foreach ($chunk as $commande) {
                    self::assignLunchCommandeToLivreur($commande, $livreur, $notifyRealtime);
                    $assignedCount++;
                    $assignedByLivreur[$livreur->id]['count']++;
                    $chunkCount++;
                    $sampleCommande ??= $commande;
                }

                if ($notifyRealtime && $chunkCount > 0) {
                    LivraisonAssignmentNotifier::afterLunchBatchAssigned($livreur, $chunkCount, $sampleCommande);
                }

                $cursor += count($chunk);
            }
        });

        $unassignedCount = max(0, $totalOrdered - $cursor);

        return [
            'assigned_count' => $assignedCount,
            'unassigned_count' => $unassignedCount,
            'skipped_not_in_trajet' => $skippedNotInTrajet,
            'debug' => [
                'delivery_date' => $deliveryDate,
                'livreurs_total' => $livreurs->count(),
                'trajet_entreprises_total' => $trajetEntrepriseIds->count(),
                'candidates_total' => $allCandidates->count(),
                'candidates_in_trajet_total' => $candidatesInTrajetTotal,
                'ordered_total' => $orderedCommandes->count(),
            ],
            'message' => 'Dispatch lot terminé.',
            'by_livreur' => array_values($assignedByLivreur),
        ];
    }

    private static function assignLunchCommandeToLivreur(Commande $commande, User $livreur, bool $notifyRealtime): void
    {
        Livraison::updateOrCreate(
            ['commande_id' => $commande->id],
            [
                'livreur_id' => $livreur->id,
                'client_id' => $commande->id_employe,
                'statut' => 'assignee',
                'heure_assignation' => now(),
                'heure_prise_en_charge' => null,
            ]
        );

        $commande->update([
            'statut_livraison' => 'en_attente',
        ]);

        if (! $notifyRealtime) {
            return;
        }

        $livraisonFresh = Livraison::where('commande_id', $commande->id)->first();
        if (! $livraisonFresh) {
            return;
        }

        PusherService::trigger('traffic-livreur', 'livraison.assigned', [
            'livraison' => [
                'id' => $livraisonFresh->id,
                'statut' => $livraisonFresh->statut,
                'commande_id' => $livraisonFresh->commande_id,
                'livreur_id' => $livraisonFresh->livreur_id,
                'updated_at' => optional($livraisonFresh->updated_at)->toISOString(),
            ],
        ]);
        PusherService::trigger('livreur.'.$livreur->id, 'livraison.assigned', [
            'livraison' => [
                'id' => $livraisonFresh->id,
                'statut' => $livraisonFresh->statut,
                'commande_id' => $livraisonFresh->commande_id,
            ],
        ]);
        PusherService::trigger('commande.'.$commande->id, 'livraison.assigned', [
            'livraison' => [
                'id' => $livraisonFresh->id,
                'statut' => $livraisonFresh->statut,
                'commande_id' => $livraisonFresh->commande_id,
                'livreur_id' => $livraisonFresh->livreur_id,
            ],
        ]);
    }
}

