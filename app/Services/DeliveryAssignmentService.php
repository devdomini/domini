<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Livraison;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class DeliveryAssignmentService
{
    private const ACTIVE_STATUSES = ['assignee', 'en_cours'];

    /** Un livreur classique ne peut pas avoir plus de N livraisons classiques actives simultanément. */
    private const MAX_CLASSIC_ACTIVE = 2;

    /** Rayon (km) autour de l'entrepôt : un livreur est considéré « proche » s'il est à cette distance. */
    private const NEAR_WAREHOUSE_KM = 3;

    private static function applyClassicDispatchPolicy($query)
    {
        return (clone $query)
            ->where('is_dispo', true)
            ->where(function ($q) {
                $q->where('type_livreur', 'classique')
                    ->orWhere(function ($q2) {
                        $q2->where('type_livreur', 'entreprise')
                            ->whereDoesntHave('livraisons', function ($lq) {
                                $lq->whereIn('statut', self::ACTIVE_STATUSES)
                                    ->whereHas('commande', function ($cq) {
                                        $cq->where('is_lunch', true);
                                    });
                            });
                    })
                    ->orWhereNull('type_livreur');
            });
    }

    /**
     * Exclure les livreurs qui ont déjà >= MAX_CLASSIC_ACTIVE livraisons classiques
     * (non-lunch) actives, SAUF s'ils sont à proximité de l'entrepôt.
     *
     * Règle métier : un livreur classique prend max 2 courses à la fois ;
     * on peut lui en attribuer de nouvelles uniquement s'il a terminé les précédentes
     * OU s'il est revenu à proximité de l'entrepôt.
     */
    private static function applyClassicSlotLimit($query)
    {
        $warehouse = \App\Models\Warehouse::where('is_active', true)->first();

        $activeStatuses = implode(',', array_map(fn ($s) => "'{$s}'", self::ACTIVE_STATUSES));
        $maxActive = self::MAX_CLASSIC_ACTIVE;

        $classicActiveSql = "(
            SELECT COUNT(*)
            FROM livraisons
            INNER JOIN commandes ON commandes.id = livraisons.commande_id
            WHERE livraisons.livreur_id = users.id
              AND livraisons.statut IN ({$activeStatuses})
              AND (commandes.is_lunch = 0 OR commandes.is_lunch IS NULL)
        )";

        return (clone $query)->where(function ($w) use ($classicActiveSql, $maxActive, $warehouse) {
            // Cas 1 : moins de MAX livraisons classiques actives
            $w->whereRaw("{$classicActiveSql} < ?", [$maxActive]);

            // Cas 2 : à proximité de l'entrepôt (le livreur est revenu, on peut réassigner)
            if ($warehouse && $warehouse->latitude && $warehouse->longitude) {
                $wLat = (float) $warehouse->latitude;
                $wLng = (float) $warehouse->longitude;
                $nearKm = self::NEAR_WAREHOUSE_KM;

                $w->orWhereRaw(
                    "(users.current_lat IS NOT NULL AND users.current_long IS NOT NULL AND
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(users.current_lat))
                        * cos(radians(users.current_long) - radians(?))
                        + sin(radians(?)) * sin(radians(users.current_lat))
                    )) <= ?)",
                    [$wLat, $wLng, $wLat, $nearKm]
                );
            }
        });
    }

    private static function pickLeastBusy($query): ?User
    {
        return (clone $query)
            ->withCount([
                'livraisons as active_livraisons_count' => function ($q) {
                    $q->whereIn('statut', self::ACTIVE_STATUSES);
                }
            ])
            ->orderBy('active_livraisons_count', 'asc')
            ->orderBy('updated_at', 'asc')
            ->first();
    }

    private static function pickByDistanceOrLeastBusy($query, float $lat, float $lng): ?User
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $candidates = (clone $query)
                ->whereNotNull('current_lat')
                ->whereNotNull('current_long')
                ->withCount([
                    'livraisons as active_livraisons_count' => function ($q) {
                        $q->whereIn('statut', self::ACTIVE_STATUSES);
                    }
                ])
                ->orderBy('last_location_at', 'desc')
                ->limit(200)
                ->get();

            $best = null;
            $bestDistance = null;

            foreach ($candidates as $candidate) {
                $d = self::haversineKm(
                    $lat,
                    $lng,
                    (float) $candidate->current_lat,
                    (float) $candidate->current_long
                );

                if ($best === null) {
                    $best = $candidate;
                    $bestDistance = $d;
                    continue;
                }

                if ($d < $bestDistance) {
                    $best = $candidate;
                    $bestDistance = $d;
                    continue;
                }

                if ($d === $bestDistance) {
                    if ((int) $candidate->active_livraisons_count < (int) $best->active_livraisons_count) {
                        $best = $candidate;
                        $bestDistance = $d;
                        continue;
                    }

                    if ((int) $candidate->active_livraisons_count === (int) $best->active_livraisons_count) {
                        if ($candidate->last_location_at && $best->last_location_at && $candidate->last_location_at->gt($best->last_location_at)) {
                            $best = $candidate;
                            $bestDistance = $d;
                        }
                    }
                }
            }

            return $best ?: self::pickLeastBusy($query);
        }

        $withLocation = (clone $query)
            ->whereNotNull('current_lat')
            ->whereNotNull('current_long')
            ->select('users.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(current_lat)) * cos(radians(current_long) - radians(?)) + sin(radians(?)) * sin(radians(current_lat)))) as distance_km',
                [$lat, $lng, $lat]
            )
            ->withCount([
                'livraisons as active_livraisons_count' => function ($q) {
                    $q->whereIn('statut', self::ACTIVE_STATUSES);
                }
            ])
            ->orderBy('distance_km', 'asc')
            ->orderBy('active_livraisons_count', 'asc')
            ->orderBy('last_location_at', 'desc')
            ->first();

        return $withLocation ?: self::pickLeastBusy($query);
    }

    private static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadiusKm * $c;
    }

    public static function resolveDestination(Commande $commande): array
    {
        $commande->loadMissing(['employe.entreprise', 'adresse']);

        $lat = null;
        $lng = null;
        $label = $commande->lieu;

        if ($commande->is_lunch) {
            $entreprise = optional($commande->employe)->entreprise;
            $lat = $entreprise?->lat;
            $lng = $entreprise?->long;
            $label = $entreprise?->adresse ?? $commande->lieu;
        } else {
            if ($commande->adresse && $commande->adresse->latitude !== null && $commande->adresse->longitude !== null) {
                $lat = $commande->adresse->latitude;
                $lng = $commande->adresse->longitude;
                $label = $commande->adresse->adresse;
            } else {
                $lat = $commande->lat;
                $lng = $commande->long;
                $label = $commande->lieu;
            }
        }

        $entrepriseId = optional($commande->employe)->id_entreprise;

        return [
            'latitude' => $lat !== null ? (float) $lat : null,
            'longitude' => $lng !== null ? (float) $lng : null,
            'label' => $label,
            'entreprise_id' => $entrepriseId ? (int) $entrepriseId : null,
        ];
    }

    public static function createIfMissing(Commande $commande): Livraison
    {
        $existing = $commande->livraison;
        if ($existing) return $existing;

        $destination = self::resolveDestination($commande);
        $distanceKm = 0.0;

        // Calcul de la distance si les coordonnées sont disponibles
        // On suppose que le restaurant/entrepôt est le point de départ
        // TODO: Utiliser l'entrepôt réel configuré en DB
        $entrepot = \App\Models\Warehouse::where('is_active', true)->first();
        if ($entrepot && $destination['latitude'] && $destination['longitude']) {
            $distanceKm = self::haversineKm(
                $entrepot->latitude,
                $entrepot->longitude,
                $destination['latitude'],
                $destination['longitude']
            );
        }

        // Calcul du montant : 300 FCFA par Km
        $montantLivraison = max(500, round($distanceKm * 300)); // Minimum 500 FCFA

        return Livraison::create([
            'commande_id' => $commande->id,
            'client_id' => $commande->id_employe,
            'montant_livraison' => $montantLivraison,
            'statut' => 'en_attente',
        ]);
    }

    public static function assignNearestLivreur(Livraison $livraison, Commande $commande, array $excludeLivreurIds = []): ?User
    {
        $destination = self::resolveDestination($commande);
        $lat = $destination['latitude'];
        $lng = $destination['longitude'];
        $entrepriseId = $destination['entreprise_id'];

        $exclude = array_values(array_unique(array_filter(array_map('intval', $excludeLivreurIds))));

        if ($livraison->refused_livreur_ids && is_array($livraison->refused_livreur_ids)) {
            $exclude = array_values(array_unique(array_merge($exclude, array_map('intval', $livraison->refused_livreur_ids))));
        }

        $baseQuery = User::query()
            ->where('role', 'livreur')
            ->where('is_active', true);

        // Politique dispo + type livreur + limite de slots classiques
        if (! $commande->is_lunch) {
            $baseQuery = self::applyClassicDispatchPolicy($baseQuery);
            $baseQuery = self::applyClassicSlotLimit($baseQuery);
        } else {
            // Pour lot: uniquement les dispo (le filtrage "type_livreur=entreprise" est géré par LunchDispatchService)
            $baseQuery->where('is_dispo', true);
        }

        if (!empty($exclude)) {
            $baseQuery->whereNotIn('id', $exclude);
        }

        $connectedCutoff = now()->subMinutes(10);
        $entrepriseOnly = $commande->is_lunch && $entrepriseId;

        $makeQuery = function (bool $connectedOnly, bool $restrictEntreprise) use ($baseQuery, $connectedCutoff, $entrepriseId) {
            $q = clone $baseQuery;
            if ($connectedOnly) {
                $q->whereNotNull('last_location_at')
                    ->where('last_location_at', '>=', $connectedCutoff);
            }
            if ($restrictEntreprise && $entrepriseId) {
                $q->whereHas('entreprises', function ($sub) use ($entrepriseId) {
                    $sub->where('entreprises.id', $entrepriseId);
                });
            }
            return $q;
        };

        // Classique : uniquement des livreurs « en ligne » (last_location_at récent).
        // Sinon la livraison reste en_attente jusqu’à ce qu’un livreur soit disponible (retry manuel / job).
        // Lots (is_lunch) : on garde les fallbacks (proximité entreprise, puis hors ligne si besoin).
        if (! $commande->is_lunch) {
            $candidates = [
                [$makeQuery(true, false), $lat, $lng],
            ];
        } else {
            $candidates = [
                [$makeQuery(true, $entrepriseOnly), $lat, $lng],
                [$entrepriseOnly ? $makeQuery(true, false) : null, $lat, $lng],
                [$makeQuery(false, $entrepriseOnly), $lat, $lng],
                [$entrepriseOnly ? $makeQuery(false, false) : null, $lat, $lng],
            ];
        }

        $candidate = null;
        foreach ($candidates as [$q, $dLat, $dLng]) {
            if ($q === null) continue;
            if ($dLat === null || $dLng === null) {
                $candidate = self::pickLeastBusy($q);
            } else {
                $candidate = self::pickByDistanceOrLeastBusy($q, (float) $dLat, (float) $dLng);
            }
            if ($candidate) break;
        }

        if (!$candidate) return null;

        $livraison->update([
            'livreur_id' => $candidate->id,
            'statut' => 'assignee',
            'heure_assignation' => now(),
        ]);
        $commande->update(['statut_livraison' => 'en_attente']);

        LivraisonAssignmentNotifier::afterSingleAssignment($livraison->fresh(), $commande);

        return $candidate;
    }

    public static function createAndAssignForCommande(Commande $commande): Livraison
    {
        return DB::transaction(function () use ($commande) {
            $commande->loadMissing(['livraison']);
            $livraison = self::createIfMissing($commande);

            if ($livraison->statut === 'livree' || $livraison->statut === 'en_cours') {
                return $livraison;
            }

            if ($livraison->livreur_id) {
                return $livraison;
            }

            self::assignNearestLivreur($livraison, $commande);
            return $livraison->fresh();
        });
    }
}
