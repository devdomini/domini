<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Casier;
use App\Models\Commande;
use App\Models\Entreprise;
use App\Models\Livraison;
use App\Models\User;
use App\Services\DeliveryAssignmentService;
use App\Services\FcmNotificationService;
use App\Services\LivraisonStatutSync;
use App\Services\PusherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LivreurApiController extends Controller
{
    private function syncCommandeFromLivraisonRow(Livraison $livraison, ?string $livraisonStatut = null): void
    {
        $fresh = $livraison->fresh(['commande']);
        if (! $fresh) {
            return;
        }
        LivraisonStatutSync::syncCommandeFromLivraison($fresh, $livraisonStatut);
        $statut = $livraisonStatut ?? (string) $fresh->statut;
        if ($statut === 'livree') {
            $cmd = $fresh->commande;
            if ($cmd && ! $cmd->is_lunch && ($cmd->statut_paiement ?? '') !== 'paye') {
                $cmd->update(['statut_paiement' => 'paye']);
            }
        }
    }

    private function ensureLivreur(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        }

        if ($user->role !== 'livreur') {
            return response()->json(['success' => false, 'message' => 'Accès réservé aux livreurs'], 403);
        }

        if (!$user->is_active) {
            return response()->json(['success' => false, 'message' => 'Compte livreur désactivé'], 403);
        }

        return null;
    }

    private function destinationForCommande(Commande $commande): array
    {
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

        $mapsUrl = null;
        if ($lat !== null && $lng !== null) {
            $mapsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . $lat . ',' . $lng;
        }

        return [
            'label' => $label,
            'latitude' => $lat,
            'longitude' => $lng,
            'maps_url' => $mapsUrl,
        ];
    }

    private function notifyClientDeliveryFcm(Livraison $livraison, string $event, string $title, string $body): void
    {
        if (! FcmNotificationService::isConfigured()) {
            return;
        }
        $livraison->loadMissing('commande.employe');
        $commande = $livraison->commande;
        if (! $commande) {
            return;
        }
        FcmNotificationService::notifyClientLivraisonEvent($commande, $event, $title, $body);
    }

    private function livraisonResource(Livraison $livraison): array
    {
        $commande = $livraison->commande;

        return [
            'id' => $livraison->id,
            'statut' => $livraison->statut,
            'montant_livraison' => $livraison->montant_livraison,
            'heure_assignation' => optional($livraison->heure_assignation)->toISOString(),
            'heure_prise_en_charge' => optional($livraison->heure_prise_en_charge)->toISOString(),
            'heure_recuperation' => optional($livraison->heure_recuperation)->toISOString(),
            'heure_livraison' => optional($livraison->heure_livraison)->toISOString(),
            'commentaire' => $livraison->commentaire,
            'commande' => $commande ? [
                'id' => $commande->id,
                'ref' => $commande->ref,
                'montant_total' => $commande->montant_total,
                'is_lunch' => (bool) $commande->is_lunch,
                'date_livraison' => optional($commande->date_livraison)->toDateString(),
                'creneau' => $commande->creneau,
                'lieu' => $commande->lieu,
                'lat' => $commande->lat,
                'long' => $commande->long,
                'statut_commande' => $commande->statut_commande,
                'statut_preparation' => $commande->statut_preparation,
                'statut_livraison' => $commande->statut_livraison,
                'statut_paiement' => $commande->statut_paiement,
                'mode_paiement' => $commande->mode_paiement,
                'consigne_livreur' => $commande->consigne_livreur,
                'numero_telephone' => $commande->numero_telephone,
                'client' => $commande->employe ? [
                    'id' => $commande->employe->id,
                    'name' => $commande->employe->name,
                    'telephone' => $commande->employe->telephone,
                    'id_entreprise' => $commande->employe->id_entreprise,
                ] : null,
                'entreprise' => $commande->employe && $commande->employe->entreprise ? [
                    'id' => $commande->employe->entreprise->id,
                    'nom' => $commande->employe->entreprise->nom,
                    'adresse' => $commande->employe->entreprise->adresse,
                    'lat' => $commande->employe->entreprise->lat,
                    'long' => $commande->employe->entreprise->long,
                ] : null,
                'items' => $commande->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantite' => $item->quantite,
                        'prix' => $item->prix,
                        'accompagnements' => $item->accompagnements,
                        'options' => $item->options,
                        'plat' => $item->plat ? [
                            'id' => $item->plat->id,
                            'nom' => $item->plat->nom,
                            'prix' => $item->plat->prix,
                            'image' => $item->plat->image,
                        ] : null,
                    ];
                })->values(),
            ] : null,
            'destination' => $commande ? $this->destinationForCommande($commande) : null,
        ];
    }

    /**
     * Requête des livraisons actives ordonnée pour l’app (carte + listes) :
     * 1. en_cours avant assignee ;
     * 2. lots entreprise (is_lunch) avant commandes classiques (même statut) — aligné avec la politique
     *    d’assignation (les livreurs entreprise ne reçoivent du classique qu’après leurs lots actifs) ;
     * 3. FIFO sur heure_assignation (sans date en dernier), puis id.
     */
    private function activeLivraisonsOrderedQuery(int $livreurId, bool $orderLotsByTrajet = false): \Illuminate\Database\Eloquent\Builder
    {
        $query = Livraison::query()
            ->with([
                'commande.employe.entreprise',
                'commande.adresse',
                'commande.items.plat',
            ])
            ->where('livraisons.livreur_id', $livreurId)
            ->whereIn('livraisons.statut', ['assignee', 'en_cours'])
            ->join('commandes', 'commandes.id', '=', 'livraisons.commande_id')
            ->where(function ($q) {
                $q->whereNull('commandes.statut_livraison')
                    ->orWhereNotIn('commandes.statut_livraison', ['livree', 'echec']);
            })
            ->select('livraisons.*')
            ->orderByRaw("CASE WHEN livraisons.statut = 'en_cours' THEN 0 ELSE 1 END")
            ->orderByRaw('CASE WHEN commandes.is_lunch = 1 THEN 0 ELSE 1 END');

        if ($orderLotsByTrajet) {
            $warehouseId = (int) (User::query()->whereKey($livreurId)->value('warehouse_id') ?? 0);
            if ($warehouseId > 0) {
                $query
                    ->leftJoin('users as employes_cmd', 'employes_cmd.id', '=', 'commandes.id_employe')
                    ->leftJoin('entreprises', 'entreprises.id', '=', 'employes_cmd.id_entreprise')
                    ->leftJoin('warehouse_trajet_items as wti', function ($join) use ($warehouseId) {
                        $join->on('wti.entreprise_id', '=', 'entreprises.id')
                            ->where('wti.warehouse_id', '=', $warehouseId);
                    })
                    ->orderByRaw(
                        'CASE WHEN commandes.is_lunch = 1 THEN COALESCE(wti.position, 999999) ELSE 999999 END ASC'
                    );
            }
        }

        return $query
            ->orderByRaw('CASE WHEN livraisons.heure_assignation IS NULL THEN 1 ELSE 0 END')
            ->orderBy('livraisons.heure_assignation', 'asc')
            ->orderBy('livraisons.id', 'asc');
    }

    public function activeLivraisons(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $livraisons = $this->activeLivraisonsOrderedQuery((int) $user->id, orderLotsByTrajet: true)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'livraisons' => $livraisons->map(fn ($l) => $this->livraisonResource($l))->values(),
            ],
        ], 200);
    }

    public function activeLivraisonsClassiques(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $livraisons = $this->activeLivraisonsOrderedQuery((int) $user->id)
            ->where('commandes.is_lunch', false)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'type' => 'classique',
                'livraisons' => $livraisons->map(fn ($l) => $this->livraisonResource($l))->values(),
            ],
        ], 200);
    }

    public function activeLivraisonsLots(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $livraisons = $this->activeLivraisonsOrderedQuery((int) $user->id, orderLotsByTrajet: true)
            ->where('commandes.is_lunch', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'type' => 'lot_entreprise',
                'livraisons' => $livraisons->map(fn ($l) => $this->livraisonResource($l))->values(),
            ],
        ], 200);
    }

    public function updateLocation(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-180,180',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update([
            'current_lat' => $request->lat,
            'current_long' => $request->long,
            'last_location_at' => now(),
        ]);

        // Temps réel admin + suivi livreur
        PusherService::trigger('traffic-livreur', 'livreur.location.updated', [
            'livreur' => [
                'id' => $user->id,
                'name' => $user->name,
                'telephone' => $user->telephone,
                'is_active' => (bool) $user->is_active,
                'is_dispo' => (bool) ($user->is_dispo ?? true),
                'lat' => (float) $user->current_lat,
                'lng' => (float) $user->current_long,
                'last_location_at' => optional($user->last_location_at)->toISOString(),
            ],
        ]);

        PusherService::trigger('livreur.' . $user->id, 'livreur.location.updated', [
            'livreur' => [
                'id' => $user->id,
                'lat' => (float) $user->current_lat,
                'lng' => (float) $user->current_long,
                'last_location_at' => optional($user->last_location_at)->toISOString(),
            ],
        ]);

        // Temps réel : suivi client (toutes les commandes actives de ce livreur)
        // On pousse sur commande.{id} pour que l’écran client bouge en direct.
        $activeLivraisons = \App\Models\Livraison::query()
            ->where('livreur_id', $user->id)
            ->whereIn('statut', ['assignee', 'en_cours'])
            ->get(['id', 'commande_id', 'statut', 'updated_at']);

        foreach ($activeLivraisons as $l) {
            if (!$l->commande_id) continue;
            PusherService::trigger('commande.' . $l->commande_id, 'livreur.location.updated', [
                'livreur' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'telephone' => $user->telephone,
                    'lat' => (float) $user->current_lat,
                    'lng' => (float) $user->current_long,
                    'last_location_at' => optional($user->last_location_at)->toISOString(),
                ],
                'livraison' => [
                    'id' => (int) $l->id,
                    'statut' => (string) $l->statut,
                    'commande_id' => (int) $l->commande_id,
                    'livreur_id' => (int) $user->id,
                    'updated_at' => optional($l->updated_at)->toISOString(),
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Localisation mise à jour',
        ], 200);
    }

    /**
     * Heartbeat "en ligne" : met à jour last_location_at même si le livreur ne bouge pas.
     */
    public function ping(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $user->update([
            'last_location_at' => now(),
        ]);

        // Temps réel admin : statut "en ligne" (sans modifier les coords)
        PusherService::trigger('traffic-livreur', 'livreur.status.updated', [
            'livreur' => [
                'id' => $user->id,
                'is_active' => (bool) $user->is_active,
                'is_dispo' => (bool) ($user->is_dispo ?? true),
                'indispo_reason' => $user->indispo_reason,
                'indispo_at' => optional($user->indispo_at)->toISOString(),
                'last_location_at' => optional($user->last_location_at)->toISOString(),
            ],
        ]);

        // Optionnel : canal livreur (utile si on affiche l'état en live côté app livreur)
        PusherService::trigger('livreur.' . $user->id, 'livreur.status.updated', [
            'livreur' => [
                'id' => $user->id,
                'last_location_at' => optional($user->last_location_at)->toISOString(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ping OK',
            'data' => [
                'last_location_at' => optional($user->last_location_at)->toISOString(),
            ],
        ], 200);
    }

    public function setDispo(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'is_dispo' => 'required|boolean',
            'raison' => 'nullable|string|max:500',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $isDispo = (bool) $request->input('is_dispo');
        $raison = trim((string) $request->input('raison', ''));

        if (! $isDispo && $raison === '') {
            return response()->json([
                'success' => false,
                'message' => 'La raison est obligatoire quand vous passez indisponible.',
            ], 422);
        }

        $user->update([
            'is_dispo' => $isDispo,
            'indispo_reason' => $isDispo ? null : $raison,
            'indispo_at' => $isDispo ? null : now(),
        ]);

        PusherService::trigger('traffic-livreur', 'livreur.status.updated', [
            'livreur' => [
                'id' => $user->id,
                'is_active' => (bool) $user->is_active,
                'is_dispo' => (bool) ($user->is_dispo ?? true),
                'indispo_reason' => $user->indispo_reason,
                'indispo_at' => optional($user->indispo_at)->toISOString(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => $isDispo ? 'Statut: disponible' : 'Statut: indisponible',
            'data' => [
                'is_dispo' => (bool) $user->is_dispo,
                'indispo_reason' => $user->indispo_reason,
                'indispo_at' => optional($user->indispo_at)->toISOString(),
            ],
        ], 200);
    }

    /**
     * Regroupe les livraisons « entreprise » (is_lunch) par entreprise employeur.
     * Les commandes classiques et les lunch sans fiche entreprise sont renvoyées à part.
     *
     * @param  array<int, array<string, mixed>>  $resources
     * @return array{groupes_entreprise: array<int, array{entreprise: array<string, mixed>, livraisons: array<int, array<string, mixed>>}>, livraisons_classiques: array<int, array<string, mixed>>, livraisons_entreprise_sans_entreprise: array<int, array<string, mixed>>}
     */
    private function buildHistoriqueGroupesEntreprise(array $resources): array
    {
        $classiques = [];
        $byEnt = [];
        $orphans = [];

        foreach ($resources as $r) {
            $cmd = $r['commande'] ?? null;
            if (! is_array($cmd) || empty($cmd['is_lunch'])) {
                $classiques[] = $r;
                continue;
            }
            $ent = $cmd['entreprise'] ?? null;
            if (! is_array($ent) || ! isset($ent['id'])) {
                $orphans[] = $r;
                continue;
            }
            $id = (int) $ent['id'];
            if (! isset($byEnt[$id])) {
                $byEnt[$id] = [
                    'entreprise' => $ent,
                    'livraisons' => [],
                ];
            }
            $byEnt[$id]['livraisons'][] = $r;
        }

        $groupes = array_values($byEnt);

        foreach ($groupes as &$g) {
            usort($g['livraisons'], function ($a, $b) {
                return $this->historiqueLivraisonTimestamp($b) <=> $this->historiqueLivraisonTimestamp($a);
            });
        }
        unset($g);

        usort($groupes, function ($a, $b) {
            $maxB = $this->maxHistoriqueLivraisonTimestamp($b['livraisons']);
            $maxA = $this->maxHistoriqueLivraisonTimestamp($a['livraisons']);

            return $maxB <=> $maxA;
        });

        return [
            'groupes_entreprise' => $groupes,
            'livraisons_classiques' => $classiques,
            'livraisons_entreprise_sans_entreprise' => $orphans,
        ];
    }

    /**
     * @param  array<string, mixed>  $livraison
     */
    private function historiqueLivraisonTimestamp(array $livraison): int
    {
        foreach (['heure_livraison', 'heure_recuperation', 'heure_assignation'] as $key) {
            if (empty($livraison[$key])) {
                continue;
            }
            $ts = strtotime((string) $livraison[$key]);
            if ($ts !== false) {
                return $ts;
            }
        }

        return 0;
    }

    /**
     * @param  array<int, array<string, mixed>>  $livraisons
     */
    private function maxHistoriqueLivraisonTimestamp(array $livraisons): int
    {
        $max = 0;
        foreach ($livraisons as $l) {
            $max = max($max, $this->historiqueLivraisonTimestamp($l));
        }

        return $max;
    }

    /**
     * @return array{0: \Illuminate\Support\Carbon, 1: \Illuminate\Support\Carbon}|null
     */
    private function historiquePeriodBounds(?string $period): ?array
    {
        $period = $period ? strtolower(trim($period)) : null;
        if ($period === null || $period === '' || $period === 'all') {
            return null;
        }

        $now = now();
        $from = match ($period) {
            'day' => $now->copy()->startOfDay(),
            'week' => $now->copy()->startOfWeek(),
            'month' => $now->copy()->startOfMonth(),
            default => null,
        };

        return $from ? [$from, $now] : null;
    }

    private function historyLivraisonsQuery(int $livreurId, Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Livraison::query()
            ->with([
                'commande.employe.entreprise',
                'commande.adresse',
                'commande.items.plat',
            ])
            ->where('livreur_id', $livreurId)
            ->where(function ($q) {
                $q->whereIn('statut', ['livree', 'echec'])
                    ->orWhereHas('commande', function ($c) {
                        $c->whereIn('statut_livraison', ['livree', 'echec']);
                    });
            });

        $period = $request->get('period');
        if ($bounds = $this->historiquePeriodBounds(is_string($period) ? $period : null)) {
            [$from, $to] = $bounds;
            $query->where(function ($q) use ($from, $to) {
                $q->whereBetween('heure_livraison', [$from, $to])
                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->whereNull('heure_livraison')
                            ->whereBetween('updated_at', [$from, $to]);
                    });
            });
        }

        $statut = $request->get('statut');
        if (is_string($statut) && in_array($statut, ['livree', 'echec'], true)) {
            $query->where(function ($q) use ($statut) {
                $q->where('statut', $statut)
                    ->orWhereHas('commande', fn ($c) => $c->where('statut_livraison', $statut));
            });
        }

        $type = $request->get('type');
        if ($type === 'entreprise') {
            $query->whereHas('commande', fn ($c) => $c->where('is_lunch', true));
        } elseif ($type === 'classique') {
            $query->whereHas('commande', fn ($c) => $c->where('is_lunch', false));
        }

        return $query->orderByDesc('heure_livraison')->orderByDesc('updated_at');
    }

    public function historyLivraisons(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $perPage = min(max((int) $request->get('per_page', 50), 1), 200);

        $livraisons = $this->historyLivraisonsQuery((int) $user->id, $request)->paginate($perPage);

        $flat = collect($livraisons->items())->map(function ($l) {
            if (! in_array($l->statut, ['livree', 'echec'], true)
                && $l->commande
                && in_array($l->commande->statut_livraison, ['livree', 'echec'], true)) {
                $target = $l->commande->statut_livraison === 'echec' ? 'echec' : 'livree';
                $l->update([
                    'statut' => $target,
                    'heure_livraison' => $l->heure_livraison ?? now(),
                ]);
                $l->refresh(['commande.employe.entreprise', 'commande.adresse', 'commande.items.plat']);
            }

            return $this->livraisonResource($l);
        })->values()->all();

        $data = [
            'livraisons' => $flat,
            'pagination' => [
                'current_page' => $livraisons->currentPage(),
                'last_page' => $livraisons->lastPage(),
                'per_page' => $livraisons->perPage(),
                'total' => $livraisons->total(),
            ],
        ];

        if ($request->boolean('group_entreprise')) {
            $data = array_merge($data, $this->buildHistoriqueGroupesEntreprise($flat));
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    public function showLivraison(Request $request, $id)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $livraison = Livraison::with([
            'commande.employe.entreprise',
            'commande.adresse',
            'commande.items.plat',
        ])->find($id);

        if (!$livraison) {
            return response()->json(['success' => false, 'message' => 'Livraison introuvable'], 404);
        }

        if ((int) $livraison->livreur_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès interdit'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'livraison' => $this->livraisonResource($livraison),
            ],
        ], 200);
    }

    public function accepter(Request $request, $id)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $livraison = Livraison::with('commande')->find($id);
        if (!$livraison) return response()->json(['success' => false, 'message' => 'Livraison introuvable'], 404);

        if ((int) $livraison->livreur_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès interdit'], 403);
        }

        if ($livraison->statut !== 'assignee') {
            return response()->json(['success' => false, 'message' => 'Cette livraison ne peut pas être acceptée'], 409);
        }

        $livraison->update([
            'statut' => 'en_cours',
            'heure_prise_en_charge' => now(),
        ]);

        $this->syncCommandeFromLivraisonRow($livraison, 'en_cours');

        // Temps réel : livraison passée en cours
        $fresh = $livraison->fresh(['commande', 'livreur']);
        PusherService::trigger('traffic-livreur', 'livraison.status.updated', [
            'livraison' => [
                'id' => $fresh->id,
                'statut' => $fresh->statut,
                'commande_id' => $fresh->commande_id,
                'livreur_id' => $fresh->livreur_id,
                'updated_at' => optional($fresh->updated_at)->toISOString(),
            ],
        ]);
        if ($fresh->commande_id) {
            PusherService::trigger('commande.' . $fresh->commande_id, 'livraison.status.updated', [
                'livraison' => [
                    'id' => $fresh->id,
                    'statut' => $fresh->statut,
                    'commande_id' => $fresh->commande_id,
                    'livreur_id' => $fresh->livreur_id,
                    'updated_at' => optional($fresh->updated_at)->toISOString(),
                ],
            ]);
        }

        if ($fresh->livreur_id) {
            PusherService::trigger('livreur.' . $fresh->livreur_id, 'livraison.status.updated', [
                'livraison' => [
                    'id' => $fresh->id,
                    'statut' => $fresh->statut,
                    'commande_id' => $fresh->commande_id,
                    'livreur_id' => $fresh->livreur_id,
                    'updated_at' => optional($fresh->updated_at)->toISOString(),
                ],
            ]);
        }

        $ref = $fresh->commande ? ($fresh->commande->ref ?? $fresh->commande->id) : '';
        $this->notifyClientDeliveryFcm(
            $fresh,
            'livraison_acceptee',
            'Livraison démarrée',
            'Le livreur a pris en charge votre commande #'.$ref.'.'
        );

        return response()->json([
            'success' => true,
            'message' => 'Livraison acceptée',
            'data' => [
                'livraison' => $this->livraisonResource($livraison->fresh([
                    'commande.employe.entreprise',
                    'commande.adresse',
                    'commande.items.plat',
                ])),
            ],
        ], 200);
    }

    /**
     * Met en cours un lot de livraisons assignées (une requête, un événement Pusher).
     */
    public function accepterLot(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) {
            return $resp;
        }
        $user = $request->user();

        $data = $request->validate([
            'livraison_ids' => 'required|array|min:1|max:500',
            'livraison_ids.*' => 'integer|min:1',
        ]);

        $ids = array_values(array_unique(array_map('intval', $data['livraison_ids'])));

        $livraisons = Livraison::query()
            ->with('commande')
            ->where('livreur_id', $user->id)
            ->where('statut', 'assignee')
            ->whereIn('id', $ids)
            ->get();

        $acceptedIds = [];
        $now = now();

        DB::transaction(function () use ($livraisons, $now, &$acceptedIds) {
            foreach ($livraisons as $livraison) {
                $livraison->update([
                    'statut' => 'en_cours',
                    'heure_prise_en_charge' => $now,
                ]);
                $this->syncCommandeFromLivraisonRow($livraison, 'en_cours');
                $acceptedIds[] = $livraison->id;
            }
        });

        $acceptedCount = count($acceptedIds);
        $skippedCount = max(0, count($ids) - $acceptedCount);

        if ($acceptedCount > 0) {
            PusherService::trigger('livreur.'.$user->id, 'livraison.lot.batch.updated', [
                'action' => 'accepted',
                'count' => $acceptedCount,
                'livraison_ids' => $acceptedIds,
            ]);
            PusherService::trigger('traffic-livreur', 'livraison.lot.batch.updated', [
                'action' => 'accepted',
                'livreur_id' => $user->id,
                'count' => $acceptedCount,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $acceptedCount > 0
                ? "Lot mis en cours ({$acceptedCount} livraison(s))."
                : 'Aucune livraison assignée à mettre en cours.',
            'data' => [
                'accepted_count' => $acceptedCount,
                'skipped_count' => $skippedCount,
                'failed_count' => 0,
            ],
        ], 200);
    }

    /**
     * Récupération au dépôt pour tout un lot (une requête, un événement Pusher).
     */
    public function confirmerRecuperationLot(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) {
            return $resp;
        }
        $user = $request->user();

        $data = $request->validate([
            'livraison_ids' => 'required|array|min:1|max:500',
            'livraison_ids.*' => 'integer|min:1',
        ]);

        $ids = array_values(array_unique(array_map('intval', $data['livraison_ids'])));

        $livraisons = Livraison::query()
            ->with('commande')
            ->where('livreur_id', $user->id)
            ->whereIn('statut', ['assignee', 'en_cours'])
            ->whereIn('id', $ids)
            ->get();

        $confirmedIds = [];
        $now = now();

        DB::transaction(function () use ($livraisons, $now, &$confirmedIds) {
            foreach ($livraisons as $livraison) {
                if ($livraison->heure_recuperation !== null) {
                    continue;
                }

                $updates = ['heure_recuperation' => $now];
                if ($livraison->statut === 'assignee') {
                    $updates['statut'] = 'en_cours';
                    $updates['heure_prise_en_charge'] = $now;
                }
                $livraison->update($updates);
                $this->syncCommandeFromLivraisonRow($livraison);
                $confirmedIds[] = $livraison->id;
            }
        });

        $confirmedCount = count($confirmedIds);
        if ($confirmedCount > 0) {
            $this->triggerLotBatchPusher($user->id, 'recuperation', $confirmedCount, $confirmedIds);
        }

        return response()->json([
            'success' => true,
            'message' => $confirmedCount > 0
                ? "Récupération confirmée pour {$confirmedCount} colis."
                : 'Aucun colis à récupérer.',
            'data' => [
                'confirmed_count' => $confirmedCount,
                'skipped_count' => max(0, count($ids) - $confirmedCount),
            ],
        ], 200);
    }

    public function confirmerRecuperation(Request $request, $id)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $livraison = Livraison::with('commande')->find($id);
        if (!$livraison) return response()->json(['success' => false, 'message' => 'Livraison introuvable'], 404);

        if ((int) $livraison->livreur_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès interdit'], 403);
        }

        if (!in_array($livraison->statut, ['assignee', 'en_cours'])) {
            return response()->json(['success' => false, 'message' => 'Statut incorrect'], 409);
        }

        if ($livraison->heure_recuperation !== null) {
            return response()->json([
                'success' => true,
                'message' => 'Récupération déjà confirmée',
                'data' => [
                    'livraison' => $this->livraisonResource($livraison->fresh([
                        'commande.employe.entreprise',
                        'commande.adresse',
                        'commande.items.plat',
                    ])),
                ],
            ], 200);
        }

        $now = now();
        $recuperationUpdates = ['heure_recuperation' => $now];
        if ($livraison->statut === 'assignee') {
            $recuperationUpdates['statut'] = 'en_cours';
            $recuperationUpdates['heure_prise_en_charge'] = $now;
        }
        $livraison->update($recuperationUpdates);
        $this->syncCommandeFromLivraisonRow($livraison);

        $freshRecup = $livraison->fresh(['commande', 'livreur']);
        if ($freshRecup) {
            $this->triggerLotBatchPusher($user->id, 'recuperation', 1, [$freshRecup->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Récupération confirmée',
            'data' => [
                'livraison' => $this->livraisonResource($livraison->fresh([
                    'commande.employe.entreprise',
                    'commande.adresse',
                    'commande.items.plat',
                ])),
            ],
        ], 200);
    }

    /**
     * Livraison lot entreprise par QR : token entreprise = tout le lot ; casier/box = colis correspondants.
     */
    public function confirmerLivraisonLotParQr(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) {
            return $resp;
        }
        $user = $request->user();

        $data = $request->validate([
            'livraison_ids' => 'required|array|min:1|max:500',
            'livraison_ids.*' => 'integer|min:1',
            'qr_token' => 'required|string',
        ]);

        $ids = array_values(array_unique(array_map('intval', $data['livraison_ids'])));
        $token = trim((string) $data['qr_token']);

        $livraisons = Livraison::query()
            ->with('commande.employe.entreprise')
            ->where('livreur_id', $user->id)
            ->where('statut', 'en_cours')
            ->whereIn('id', $ids)
            ->get();

        if ($livraisons->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune livraison en cours à valider.',
            ], 409);
        }

        $first = $livraisons->first();
        $entreprise = $first->commande?->employe?->entreprise;
        if (! $first->commande?->is_lunch || ! $entreprise) {
            return response()->json([
                'success' => false,
                'message' => 'Ce n\'est pas un lot entreprise.',
            ], 400);
        }

        $entrepriseTokenValid = filled($entreprise->qr_code_token) && $entreprise->qr_code_token === $token;

        $deliveredIds = [];
        $now = now();

        DB::transaction(function () use ($livraisons, $entreprise, $token, $entrepriseTokenValid, $now, &$deliveredIds) {
            foreach ($livraisons as $livraison) {
                $commande = $livraison->commande;
                if (! $commande || ! $commande->is_lunch) {
                    continue;
                }

                $ent = $commande->employe?->entreprise;
                if (! $ent || (int) $ent->id !== (int) $entreprise->id) {
                    continue;
                }

                if (! $entrepriseTokenValid && ! $this->lunchQrMatchesEmploye($commande, $token, $ent)) {
                    continue;
                }

                $livraison->update([
                    'statut' => 'livree',
                    'heure_livraison' => $now,
                    'commentaire' => trim(($livraison->commentaire ? $livraison->commentaire."\n" : '').'Validé par QR Code (lot)'),
                ]);

                $this->syncCommandeFromLivraisonRow($livraison, 'livree');
                if ($commande && ($commande->statut_paiement ?? '') !== 'paye') {
                    $commande->update(['statut_paiement' => 'paye']);
                }

                $deliveredIds[] = $livraison->id;
            }
        });

        $deliveredCount = count($deliveredIds);
        if ($deliveredCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code invalide ou aucun colis correspondant dans ce lot.',
            ], 400);
        }

        $this->triggerLotBatchPusher($user->id, 'delivered_qr', $deliveredCount, $deliveredIds);

        return response()->json([
            'success' => true,
            'message' => "Lot validé ({$deliveredCount} colis).",
            'data' => [
                'delivered_count' => $deliveredCount,
                'skipped_count' => max(0, count($ids) - $deliveredCount),
            ],
        ], 200);
    }

    public function confirmerLivraisonParQr(Request $request, $id)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $livraison = Livraison::with('commande.employe.entreprise')->find($id);
        if (!$livraison) return response()->json(['success' => false, 'message' => 'Livraison introuvable'], 404);

        if ((int) $livraison->livreur_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès interdit'], 403);
        }

        if (!in_array($livraison->statut, ['assignee', 'en_cours'])) {
            return response()->json(['success' => false, 'message' => 'Statut incorrect'], 409);
        }

        // Vérification que c'est une commande entreprise
        $entreprise = $livraison->commande->employe?->entreprise;
        if (!$livraison->commande->is_lunch || !$entreprise) {
            return response()->json(['success' => false, 'message' => 'Ce n\'est pas une livraison entreprise'], 400);
        }

        $token = trim((string) $request->qr_token);
        if (! $this->lunchQrMatchesEmploye($livraison->commande, $token, $entreprise)) {
            return response()->json(['success' => false, 'message' => 'QR Code invalide'], 400);
        }

        $livraison->update([
            'statut' => 'livree',
            'heure_livraison' => now(),
            'commentaire' => 'Validé par QR Code',
        ]);

        $this->syncCommandeFromLivraisonRow($livraison, 'livree');
        if ($livraison->commande && ($livraison->commande->statut_paiement ?? '') !== 'paye') {
            $livraison->commande->update(['statut_paiement' => 'paye']);
        }

        // Temps réel : livraison livrée
        $fresh = $livraison->fresh(['commande', 'livreur']);
        $livraisonPayload = [
            'id'             => $fresh->id,
            'statut'         => $fresh->statut,
            'commande_id'    => $fresh->commande_id,
            'livreur_id'     => $fresh->livreur_id,
            'updated_at'     => optional($fresh->updated_at)->toISOString(),
            'statut_commande'=> optional($fresh->commande)->statut_commande,
        ];
        PusherService::trigger('traffic-livreur', 'livraison.status.updated', ['livraison' => $livraisonPayload]);
        if ($fresh->commande_id) {
            PusherService::trigger('commande.' . $fresh->commande_id, 'livraison.status.updated', ['livraison' => $livraisonPayload]);
        }
        if ($fresh->livreur_id) {
            PusherService::trigger('livreur.' . $fresh->livreur_id, 'livraison.status.updated', ['livraison' => $livraisonPayload]);
        }

        $refQr = $fresh->commande ? ($fresh->commande->ref ?? $fresh->commande->id) : '';
        $this->notifyClientDeliveryFcm(
            $fresh,
            'livraison_livree',
            'Commande livrée',
            'Votre commande #'.$refQr.' a été livrée.'
        );

        return response()->json([
            'success' => true,
            'message' => 'Livraison confirmée par QR Code',
            'data' => [
                'livraison' => $this->livraisonResource($livraison->fresh([
                    'commande.employe.entreprise',
                    'commande.adresse',
                    'commande.items.plat',
                ])),
            ],
        ], 200);
    }

    public function refuser(Request $request, $id)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'raison' => 'nullable|string|max:500',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $livraison = Livraison::with(['commande.employe.entreprise', 'commande.adresse', 'commande.items.plat'])->find($id);
        if (!$livraison) return response()->json(['success' => false, 'message' => 'Livraison introuvable'], 404);

        if ((int) $livraison->livreur_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès interdit'], 403);
        }

        if ($livraison->statut !== 'assignee') {
            return response()->json(['success' => false, 'message' => 'Cette livraison ne peut pas être refusée'], 409);
        }

        $note = 'Refusé par livreur_id=' . $user->id . ' le ' . now()->toDateTimeString();
        if (!empty($request->raison)) {
            $note .= ' | ' . $request->raison;
        }
        $commentaire = trim(($livraison->commentaire ? $livraison->commentaire . "\n" : '') . $note);

        $refused = $livraison->refused_livreur_ids ?? [];
        if (!is_array($refused)) $refused = [];
        $refused[] = (int) $user->id;
        $refused = array_values(array_unique(array_map('intval', $refused)));

        $livraison->update([
            'heure_prise_en_charge' => null,
            'commentaire' => $commentaire,
            'refused_livreur_ids' => $refused,
        ]);

        $alternative = null;
        if ($livraison->commande) {
            $alternative = DeliveryAssignmentService::assignNearestLivreur($livraison, $livraison->commande, $refused);
        }

        if ($alternative) {
            return response()->json([
                'success' => true,
                'message' => 'Livraison refusée. Elle a été réaffectée à un autre livreur.',
            ], 200);
        }

        $livraison->update([
            'livreur_id' => null,
            'statut' => 'en_attente',
            'heure_assignation' => null,
            'heure_prise_en_charge' => null,
            'commentaire' => $commentaire,
        ]);

        $this->syncCommandeFromLivraisonRow($livraison->fresh(), 'en_attente');

        return response()->json([
            'success' => true,
            'message' => 'Livraison refusée. Aucun autre livreur disponible pour réaffectation.',
        ], 200);
    }

    public function changerStatut(Request $request, $id)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'statut' => 'required|string|in:livree,echec',
            'commentaire' => 'nullable|string|max:500',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $livraison = Livraison::with('commande')->find($id);
        if (!$livraison) return response()->json(['success' => false, 'message' => 'Livraison introuvable'], 404);

        if ((int) $livraison->livreur_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès interdit'], 403);
        }

        if (!in_array($livraison->statut, ['assignee', 'en_cours'])) {
            return response()->json(['success' => false, 'message' => 'Changement de statut non autorisé'], 409);
        }

        $statut = $request->statut;
        $updates = [
            'statut' => $statut,
        ];

        // Si le livreur a sauté l'étape "accepter", on comble les horodatages manquants
        if ($livraison->statut === 'assignee') {
            if (empty($livraison->heure_prise_en_charge)) {
                $updates['heure_prise_en_charge'] = now();
            }
            if (empty($livraison->heure_recuperation)) {
                $updates['heure_recuperation'] = now();
            }
        }

        if ($statut === 'livree') {
            $updates['heure_livraison'] = now();
        }

        if (!empty($request->commentaire)) {
            $updates['commentaire'] = trim(($livraison->commentaire ? $livraison->commentaire . "\n" : '') . $request->commentaire);
        }

        $livraison->update($updates);

        $this->syncCommandeFromLivraisonRow($livraison, $statut);

        $freshSt = $livraison->fresh(['commande', 'livreur']);
        if ($freshSt) {
            $payloadSt = [
                'id'             => $freshSt->id,
                'statut'         => $freshSt->statut,
                'commande_id'    => $freshSt->commande_id,
                'livreur_id'     => $freshSt->livreur_id,
                'updated_at'     => optional($freshSt->updated_at)->toISOString(),
                'statut_commande'=> optional($freshSt->commande)->statut_commande,
            ];
            PusherService::trigger('traffic-livreur', 'livraison.status.updated', ['livraison' => $payloadSt]);
            if ($freshSt->commande_id) {
                PusherService::trigger('commande.' . $freshSt->commande_id, 'livraison.status.updated', ['livraison' => $payloadSt]);
            }
            if ($freshSt->livreur_id) {
                PusherService::trigger('livreur.' . $freshSt->livreur_id, 'livraison.status.updated', ['livraison' => $payloadSt]);
            }

            $refSt = $freshSt->commande ? ($freshSt->commande->ref ?? $freshSt->commande->id) : '';
            if ($statut === 'livree') {
                $this->notifyClientDeliveryFcm(
                    $freshSt,
                    'livraison_livree',
                    'Commande livrée',
                    'Votre commande #'.$refSt.' a été livrée.'
                );
            } else {
                $this->notifyClientDeliveryFcm(
                    $freshSt,
                    'livraison_echec',
                    'Livraison',
                    'Un souci est survenu pour la livraison de la commande #'.$refSt.'.'
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut de livraison mis à jour',
            'data' => [
                'livraison' => $this->livraisonResource($livraison->fresh([
                    'commande.employe.entreprise',
                    'commande.adresse',
                    'commande.items.plat',
                ])),
            ],
        ], 200);
    }

    public function marquerPaye(Request $request, $id)
    {
        if ($resp = $this->ensureLivreur($request)) return $resp;
        $user = $request->user();

        $livraison = Livraison::with('commande')->find($id);
        if (!$livraison) return response()->json(['success' => false, 'message' => 'Livraison introuvable'], 404);

        if ((int) $livraison->livreur_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès interdit'], 403);
        }

        $commande = $livraison->commande;
        if (!$commande) {
            return response()->json(['success' => false, 'message' => 'Commande introuvable'], 404);
        }

        // Créer un paiement pour cette commande
        // Si un paiement existe déjà, on ne le recrée pas
        $existingPaiement = \App\Models\Paiement::where('commande_id', $commande->id)
            ->where('statut', 'valide')
            ->exists();

        if ($existingPaiement) {
            return response()->json(['success' => true, 'message' => 'Paiement déjà enregistré'], 200);
        }

        \App\Models\Paiement::create([
            'ref' => \App\Models\Paiement::generateRef(),
            'commande_id' => $commande->id,
            'user_id' => $commande->id_employe,
            'montant' => $commande->montant_total + $livraison->montant_livraison,
            'mode_paiement' => 'especes', // Par défaut pour le livreur
            'statut' => 'valide',
            'type' => 'commande',
            'description' => 'Paiement encaissé par le livreur ' . $user->name,
        ]);

        $commande->update(['statut_paiement' => 'paye']);

        return response()->json([
            'success' => true,
            'message' => 'Paiement marqué avec succès',
        ], 200);
    }

    /**
     * Marque tout un lot entreprise comme livré sans encaissement (facturation fin de mois, wallet, etc.).
     */
    public function marquerLotLivree(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) {
            return $resp;
        }
        $user = $request->user();

        $data = $request->validate([
            'livraison_ids' => 'required|array|min:1|max:500',
            'livraison_ids.*' => 'integer|min:1',
            'commentaire' => 'nullable|string|max:500',
        ]);

        $ids = array_values(array_unique(array_map('intval', $data['livraison_ids'])));
        $commentaire = isset($data['commentaire']) ? trim((string) $data['commentaire']) : '';

        $livraisons = Livraison::query()
            ->with('commande')
            ->where('livreur_id', $user->id)
            ->where('statut', 'en_cours')
            ->whereIn('id', $ids)
            ->get();

        $deliveredIds = [];
        $now = now();

        DB::transaction(function () use ($livraisons, $commentaire, $now, &$deliveredIds) {
            foreach ($livraisons as $livraison) {
                $commande = $livraison->commande;
                if (! $commande || ! $commande->is_lunch) {
                    continue;
                }

                $note = trim(($livraison->commentaire ? $livraison->commentaire."\n" : '').'Livré (lot entreprise)');
                if ($commentaire !== '') {
                    $note .= ' | '.$commentaire;
                }

                $livraison->update([
                    'statut' => 'livree',
                    'heure_livraison' => $now,
                    'commentaire' => $note,
                ]);

                $this->syncCommandeFromLivraisonRow($livraison, 'livree');

                $deliveredIds[] = $livraison->id;
            }
        });

        $count = count($deliveredIds);
        if ($count > 0) {
            $this->triggerLotBatchPusher($user->id, 'delivered', $count, $deliveredIds);
        }

        return response()->json([
            'success' => true,
            'message' => $count > 0
                ? "Lot marqué livré ({$count} colis)."
                : 'Aucun colis en cours à clôturer.',
            'data' => [
                'delivered_count' => $count,
                'skipped_count' => max(0, count($ids) - $count),
            ],
        ], 200);
    }

    /**
     * Encaissement espèces pour les colis du lot encore non payés.
     */
    public function marquerLotPaye(Request $request)
    {
        if ($resp = $this->ensureLivreur($request)) {
            return $resp;
        }
        $user = $request->user();

        $data = $request->validate([
            'livraison_ids' => 'required|array|min:1|max:500',
            'livraison_ids.*' => 'integer|min:1',
        ]);

        $ids = array_values(array_unique(array_map('intval', $data['livraison_ids'])));

        $livraisons = Livraison::query()
            ->with('commande')
            ->where('livreur_id', $user->id)
            ->whereIn('statut', ['assignee', 'en_cours'])
            ->whereIn('id', $ids)
            ->get();

        $paidIds = [];

        DB::transaction(function () use ($livraisons, $user, &$paidIds) {
            foreach ($livraisons as $livraison) {
                $commande = $livraison->commande;
                if (! $commande) {
                    continue;
                }

                if ($commande->statut_paiement === 'paye') {
                    $paidIds[] = $livraison->id;

                    continue;
                }

                $exists = \App\Models\Paiement::query()
                    ->where('commande_id', $commande->id)
                    ->where('statut', 'valide')
                    ->exists();

                if ($exists) {
                    $commande->update(['statut_paiement' => 'paye']);
                    $paidIds[] = $livraison->id;

                    continue;
                }

                \App\Models\Paiement::create([
                    'ref' => \App\Models\Paiement::generateRef(),
                    'commande_id' => $commande->id,
                    'user_id' => $commande->id_employe,
                    'montant' => $commande->montant_total + $livraison->montant_livraison,
                    'mode_paiement' => 'especes',
                    'statut' => 'valide',
                    'type' => 'commande',
                    'description' => 'Encaissement lot par le livreur '.$user->name,
                ]);

                $commande->update(['statut_paiement' => 'paye']);
                $paidIds[] = $livraison->id;
            }
        });

        $paidCount = count($paidIds);

        return response()->json([
            'success' => true,
            'message' => $paidCount > 0
                ? "Paiement enregistré pour {$paidCount} colis."
                : 'Aucun colis à encaisser.',
            'data' => [
                'paid_count' => $paidCount,
                'skipped_count' => max(0, count($ids) - $paidCount),
            ],
        ], 200);
    }

    private function lunchQrMatchesEmploye(Commande $commande, string $token, ?Entreprise $entreprise): bool
    {
        if ($entreprise && filled($entreprise->qr_code_token) && $entreprise->qr_code_token === $token) {
            return true;
        }

        $employe = $commande->employe;
        if (! $employe) {
            return false;
        }

        if (filled($employe->num_box) && trim((string) $employe->num_box) === $token) {
            return true;
        }

        $casier = Casier::query()->where('id_employe', $employe->id)->first();
        if ($casier && $casier->qr_code === $token) {
            return true;
        }

        $lieu = trim((string) ($commande->lieu ?? ''));
        if ($lieu !== '' && (str_contains($lieu, $token) || $lieu === 'Box '.$token)) {
            return true;
        }

        return false;
    }

    /**
     * @param array<int,int> $livraisonIds
     */
    private function triggerLotBatchPusher(int $livreurId, string $action, int $count, array $livraisonIds): void
    {
        $payload = [
            'action' => $action,
            'count' => $count,
            'livraison_ids' => array_values($livraisonIds),
        ];
        PusherService::trigger('livreur.'.$livreurId, 'livraison.lot.batch.updated', $payload);
        PusherService::trigger('traffic-livreur', 'livraison.lot.batch.updated', array_merge($payload, [
            'livreur_id' => $livreurId,
        ]));
    }
}
