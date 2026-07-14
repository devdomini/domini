<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\ItemCommande;
use App\Services\DeliveryAssignmentService;
use App\Services\FcmNotificationService;
use App\Services\LunchDispatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommandeApiController extends Controller
{
    /**
     * Créer une nouvelle commande
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.plat_id' => 'required|exists:plats,id',
            'items.*.quantite' => 'required|integer|min:1',
            'items.*.accompagnement_id' => 'nullable|exists:accompagnements,id',
            'items.*.option_id' => 'nullable|exists:options,id',
            'lieu' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'is_lunch' => 'boolean',
            'date_livraison' => 'nullable|date|after_or_equal:today',
            'adresse_id' => 'nullable|exists:adresses,id',
            'mode_paiement' => 'nullable|in:especes,carte,mobile_money,wallet',
            'frais_livraison' => 'nullable|numeric|min:0',
            'montant_reduction' => 'nullable|numeric|min:0',
            'montant_total' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Validation conditionnelle manuelle pour plus de flexibilité
        if ($request->is_lunch) {
            if (! $request->date_livraison) {
                return response()->json([
                    'success' => false,
                    'message' => 'La date de livraison est requise pour un déjeuner au bureau.',
                ], 422);
            }
            // Vérifier si l'utilisateur est un employé avec un num_box
            if (! $user->num_box) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez avoir un numéro de box pour commander un plat de midi au travail.',
                ], 403);
            }

            // Vérifier la limite d'un plat par jour
            $existingOrder = Commande::where('id_employe', $user->id)
                ->where('is_lunch', true)
                ->whereDate('date_livraison', $request->date_livraison)
                ->where('statut_commande', '!=', 'annulee')
                ->exists();

            if ($existingOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà commandé un plat de midi pour cette date.',
                ], 403);
            }

            // TODO: Vérifier si la date est un jour ouvrable (Lundi-Vendredi)
            // $dayOfWeek = date('N', strtotime($request->date_livraison));
            // if ($dayOfWeek > 5) { return error... }
        } else {
            // Livraison classique : adresse_id ou lieu requis ?
            // Ici on peut décider si adresse_id est obligatoire ou si lieu suffit
            // Pour l'instant on laisse flexible
        }

        // Création de la commande
        $commande = Commande::create([
            'ref' => Commande::generateRef(),
            'id_employe' => $user->id,
            'montant_total' => 0, // Recalculé après les lignes (sous-total + frais − réduction)
            'statut_commande' => 'en_attente',
            'lieu' => $request->is_lunch ? 'Box '.$user->num_box : $request->lieu,
            'lat' => $request->lat,
            'long' => $request->long,
            'consigne_cuisinier' => $request->consigne_cuisinier,
            'consigne_livreur' => $request->consigne_livreur,
            'is_lunch' => $request->is_lunch ?? false,
            'date_livraison' => $request->date_livraison,
            'adresse_id' => $request->is_lunch ? null : $request->adresse_id,
            'numero_telephone' => $user->telephone,
            'mode_paiement' => $request->filled('mode_paiement') ? $request->mode_paiement : null,
            'statut_paiement' => 'en_attente',
        ]);

        $subtotal = 0.0;

        foreach ($request->items as $item) {
            $plat = \App\Models\Plat::find($item['plat_id']);
            $prix = (float) $plat->prix;

            // Ajouter le prix de l'accompagnement sélectionné (même logique que l'app mobile)
            if (!empty($item['accompagnement_id'])) {
                $accompagnement = \App\Models\Accompagnement::find($item['accompagnement_id']);
                if ($accompagnement) {
                    $prix += (float) $accompagnement->prix_unitaire;
                }
            }

            // Ajouter le prix de l'option sélectionnée (même logique que l'app mobile)
            if (!empty($item['option_id'])) {
                $option = \App\Models\Option::find($item['option_id']);
                if ($option) {
                    $prix += (float) $option->prix_unitaire;
                }
            }

            $itemCommande = ItemCommande::create([
                'commande_id' => $commande->id,
                'plat_id' => $plat->id,
                'quantite' => $item['quantite'],
                'prix' => $prix,
                'accompagnements' => $item['accompagnements'] ?? null,
                'options' => $item['options'] ?? null,
                'is_subventionne' => false,
            ]);

            $subtotal += $prix * (int) $item['quantite'];
        }

        // Aligné sur le panier mobile : frais fixes si non fourni (voir CartLoaded.deliveryFee)
        $fraisLivraison = $request->filled('frais_livraison')
            ? (float) $request->frais_livraison
            : (count($request->items) > 0 ? 1000.0 : 0.0);
        $fraisLivraison = max(0.0, $fraisLivraison);

        $reduction = $request->filled('montant_reduction')
            ? (float) $request->montant_reduction
            : 0.0;
        $reduction = max(0.0, min($reduction, $subtotal));

        $montantCalcule = round($subtotal + $fraisLivraison - $reduction, 2);

        if ($request->filled('montant_total')) {
            $clientTotal = round((float) $request->montant_total, 2);
            $tolerance = max(50.0, $montantCalcule * 0.005);
            if (abs($clientTotal - $montantCalcule) > $tolerance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le montant total ne correspond pas au calcul serveur.',
                    'errors' => [
                        'montant_total' => [
                            'Calcul serveur : '.$montantCalcule.' FCFA (sous-total '.$subtotal.', frais '.$fraisLivraison.', réduction '.$reduction.').',
                        ],
                    ],
                ], 422);
            }
        }

        $commande->update(['montant_total' => $montantCalcule]);

        $commande->load(['items.plat', 'employe.entreprise', 'adresse', 'livraison.livreur']);

        try {
            // Classique: assignation immédiate (proximité)
            // Entreprise (is_lunch): dispatch séquentiel automatique (trajet + 200 max/livreur)
            if ($commande->is_lunch) {
                $commande->loadMissing(['employe.entreprise.commune.warehouse']);
                $warehouse = optional(optional(optional($commande->employe)->entreprise)->commune)->warehouse;

                if ($warehouse) {
                    LunchDispatchService::dispatchForWarehouse(
                        $warehouse,
                        $commande->date_livraison ? $commande->date_livraison->toDateString() : now()->toDateString(),
                        200
                    );
                } else {
                    // Fallback: créer la livraison non assignée si pas de rattachement entrepôt
                    DeliveryAssignmentService::createIfMissing($commande);
                }
            } else {
                DeliveryAssignmentService::createAndAssignForCommande($commande);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur auto-assignation/dispatch livraison: '.$e->getMessage());
        }

        $commande->load(['livraison.livreur']);

        try {
            $commande->loadMissing('employe');
            if (FcmNotificationService::isConfigured()) {
                FcmNotificationService::notifyClientOrderCreated($commande);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur notification FCM création commande: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Commande créée avec succès',
            'data' => [
                'commande' => $commande,
            ],
        ], 201);
    }

    /**
     * Suivi en temps réel de la commande (Position livreur)
     */
    public function tracking($id, Request $request)
    {
        $user = $request->user();

        $commande = Commande::where('id_employe', $user->id)
            ->with(['livraison.livreur'])
            ->findOrFail($id);

        $livraison = $commande->livraison;

        if (!$livraison || !$livraison->livreur) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun livreur assigné pour le moment.',
                'data' => null
            ]);
        }

        $livreur = $livraison->livreur;

        $commande->loadMissing(['employe.entreprise', 'adresse']);
        $dest = DeliveryAssignmentService::resolveDestination($commande);

        return response()->json([
            'success' => true,
            'data' => [
                'statut_commande' => $commande->statut_commande,
                // livraison_statut = modèle Livraison (assignee, en_cours, livree…)
                'livraison_statut' => $livraison->statut,
                // commande_statut_livraison = champ commandes.statut_livraison
                'commande_statut_livraison' => $commande->statut_livraison,
                // Alias historique : même valeur que livraison_statut
                'statut_livraison' => $livraison->statut,
                'livreur' => [
                    'nom' => $livreur->name,
                    'telephone' => $livreur->telephone,
                    'lat' => $livreur->current_lat,
                    'long' => $livreur->current_long,
                    'updated_at' => $livreur->last_location_at,
                ],
                'destination' => [
                    'lat' => $dest['latitude'],
                    'long' => $dest['longitude'],
                    'label' => $dest['label'],
                ],
            ],
        ], 200);
    }

    /**
     * Historique des commandes de l'utilisateur connecté
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $query = Commande::where('id_employe', $user->id)
            ->with(['items.plat', 'livraison.livreur']);

        // Filtrer par statut
        if ($request->has('statut_commande')) {
            $query->where('statut_commande', $request->statut_commande);
        }

        if ($request->has('statut_preparation')) {
            $query->where('statut_preparation', $request->statut_preparation);
        }

        if ($request->has('statut_livraison')) {
            $query->where('statut_livraison', $request->statut_livraison);
        }

        // Pagination (plafonnée pour éviter surcharge)
        $perPage = min((int) $request->get('per_page', 15), 100);
        $perPage = max($perPage, 1);
        $commandes = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'commandes' => $commandes->items(),
                'pagination' => [
                    'current_page' => $commandes->currentPage(),
                    'last_page' => $commandes->lastPage(),
                    'per_page' => $commandes->perPage(),
                    'total' => $commandes->total(),
                ],
            ],
        ], 200);
    }

    /**
     * Détails d'une commande spécifique
     */
    public function show($id, Request $request)
    {
        $user = $request->user();

        $commande = Commande::where('id_employe', $user->id)
            ->with(['items.plat', 'livraison.livreur', 'paiements', 'adresse', 'employe.entreprise'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'commande' => $commande,
            ],
        ], 200);
    }

    /**
     * Annuler une commande (côté client)
     */
    public function annuler($id, Request $request)
    {
        $user = $request->user();

        $commande = Commande::where('id_employe', $user->id)
            ->with(['livraison'])
            ->findOrFail($id);

        // Idempotent
        if ($commande->statut_commande === 'annulee') {
            return response()->json([
                'success' => true,
                'message' => 'Commande déjà annulée.',
                'data' => [
                    'commande' => $commande->fresh(['items.plat', 'livraison.livreur', 'paiements', 'adresse', 'employe.entreprise']),
                ],
            ], 200);
        }

        // Blocage si déjà terminée / livrée
        $livraison = $commande->livraison;
        $isDelivered = ($commande->statut_livraison === 'livree')
            || ($livraison && $livraison->statut === 'livree');

        if ($commande->statut_commande === 'terminee' || $isDelivered) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d’annuler : la commande est déjà livrée/terminée.',
            ], 409);
        }

        $commande->update([
            'statut_commande' => 'annulee',
            'statut_livraison' => 'echec',
        ]);

        if ($livraison && $livraison->statut !== 'echec') {
            $livraison->update([
                'statut' => 'echec',
                'commentaire' => trim(($livraison->commentaire ? $livraison->commentaire . "\n" : '') . 'Annulée par le client.'),
            ]);
        }

        // Notification
        try {
            $user->notify(new \App\Notifications\OrderStatusChanged($commande, 'commande', 'annulee'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur notification annulation commande: ' . $e->getMessage());
        }

        $commande = $commande->fresh(['items.plat', 'livraison.livreur', 'paiements', 'adresse', 'employe.entreprise']);

        return response()->json([
            'success' => true,
            'message' => 'Commande annulée.',
            'data' => [
                'commande' => $commande,
            ],
        ], 200);
    }
}
