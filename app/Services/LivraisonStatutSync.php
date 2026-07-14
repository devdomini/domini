<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Livraison;

/**
 * Aligne livraisons.statut (5 valeurs) et commandes.statut_livraison (4 valeurs, sans « assignee »).
 *
 * Source de vérité app livreur + historique API : livraisons.statut
 * (livree, echec, assignee, en_cours, en_attente).
 */
class LivraisonStatutSync
{
    public const LIVRAISON_STATUTS = ['en_attente', 'assignee', 'en_cours', 'livree', 'echec'];

    public const COMMANDE_STATUT_LIVRAISON = ['en_attente', 'en_cours', 'livree', 'echec'];

    public static function commandeStatutLivraisonFromLivraison(string $livraisonStatut): string
    {
        return match ($livraisonStatut) {
            'en_cours' => 'en_cours',
            'livree' => 'livree',
            'echec' => 'echec',
            'assignee', 'en_attente' => 'en_attente',
            default => 'en_attente',
        };
    }

    public static function livraisonStatutFromCommandeStatutLivraison(
        string $commandeStatutLivraison,
        ?Livraison $livraison = null
    ): string {
        return match ($commandeStatutLivraison) {
            'en_cours' => 'en_cours',
            'livree' => 'livree',
            'echec' => 'echec',
            'en_attente' => ($livraison && $livraison->livreur_id) ? 'assignee' : 'en_attente',
            default => $livraison?->statut ?? 'en_attente',
        };
    }

    public static function syncCommandeFromLivraison(Livraison $livraison, ?string $livraisonStatut = null): void
    {
        $commande = $livraison->commande;
        if (! $commande) {
            return;
        }

        $statut = $livraisonStatut ?? (string) $livraison->statut;
        $updates = [
            'statut_livraison' => self::commandeStatutLivraisonFromLivraison($statut),
        ];

        if ($statut === 'livree') {
            $updates['statut_commande'] = 'terminee';
        } elseif ($statut === 'echec') {
            $updates['statut_commande'] = 'annulee';
            $updates['statut_livraison'] = 'echec';
        }

        $commande->update($updates);
    }

    /**
     * Mise à jour admin depuis commandes.statut_livraison → ligne livraisons.
     */
    public static function applyCommandeStatutLivraison(Commande $commande, string $statutLivraison): void
    {
        if (! in_array($statutLivraison, self::COMMANDE_STATUT_LIVRAISON, true)) {
            return;
        }

        $updates = ['statut_livraison' => $statutLivraison];
        if ($statutLivraison === 'livree') {
            $updates['statut_commande'] = 'terminee';
        } elseif ($statutLivraison === 'echec') {
            $updates['statut_commande'] = 'annulee';
        }

        $commande->update($updates);

        $livraison = $commande->livraison;
        if (! $livraison) {
            return;
        }

        $livraisonStatut = self::livraisonStatutFromCommandeStatutLivraison($statutLivraison, $livraison);
        $livraisonUpdates = ['statut' => $livraisonStatut];

        if ($livraisonStatut === 'livree') {
            $livraisonUpdates['heure_livraison'] = $livraison->heure_livraison ?? now();
        }
        if ($livraisonStatut === 'en_cours' && empty($livraison->heure_prise_en_charge)) {
            $livraisonUpdates['heure_prise_en_charge'] = now();
        }

        $livraison->update($livraisonUpdates);
    }
}
