<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Entreprise;
use App\Models\Commande;
use App\Models\Livraison;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_employes' => User::where('role', 'employe')->count(),
            'employes_change' => $this->calculateChange(User::where('role', 'employe'), 'month'),
            
            'commandes_aujourd_hui' => Commande::whereDate('created_at', Carbon::today())->count(),
            'commandes_hier' => Commande::whereDate('created_at', Carbon::yesterday())->count(),
            
            'entreprises_actives' => Entreprise::where('statut', true)->count(),
            'entreprises_nouvelles' => Entreprise::where('created_at', '>=', Carbon::now()->subMonth())->count(),
            
            'chiffre_affaires' => Paiement::where('statut', 'valide')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->sum('montant'),
            'ca_change' => $this->calculateChange(Paiement::where('statut', 'valide'), 'month', 'montant'),
            
            // KPIs additionnels
            'taux_livraison' => $this->calculateTauxLivraison(),
            'panier_moyen' => $this->calculatePanierMoyen(),
            'livreurs_actifs' => User::where('role', 'livreur')->where('is_active', true)->count(),
        ];

        // Calculer le pourcentage de changement pour les commandes
        if ($stats['commandes_hier'] > 0) {
            $stats['commandes_change'] = round((($stats['commandes_aujourd_hui'] - $stats['commandes_hier']) / $stats['commandes_hier']) * 100);
        } else {
            $stats['commandes_change'] = $stats['commandes_aujourd_hui'] > 0 ? 100 : 0;
        }

        // Données pour les graphiques
        $charts = [
            'commandes_evolution' => $this->getCommandesEvolution(),
            'statuts_repartition' => $this->getStatutsRepartition(),
            'top_entreprises' => $this->getTopEntreprises(),
        ];

        return view('admin.dashboard', compact('stats', 'charts'));
    }

    /**
     * Évolution des commandes sur les 7 derniers jours
     */
    private function getCommandesEvolution()
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->locale('fr')->isoFormat('ddd D MMM');
            $data[] = Commande::whereDate('created_at', $date)->count();
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Répartition des commandes par statut
     */
    private function getStatutsRepartition()
    {
        $statuts = Commande::select('statut_commande', DB::raw('count(*) as total'))
            ->groupBy('statut_commande')
            ->get();

        $labels = [];
        $data = [];
        $labelMap = [
            'en_attente' => 'En attente',
            'confirmee' => 'Confirmée',
            'annulee' => 'Annulée',
            'terminee' => 'Terminée'
        ];

        foreach ($statuts as $statut) {
            $labels[] = $labelMap[$statut->statut_commande] ?? $statut->statut_commande;
            $data[] = $statut->total;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Top 10 entreprises par nombre de commandes
     */
    private function getTopEntreprises()
    {
        $topEntreprises = Entreprise::select('entreprises.nom', DB::raw('COUNT(commandes.id) as total_commandes'))
            ->leftJoin('users', 'users.id_entreprise', '=', 'entreprises.id')
            ->leftJoin('commandes', 'commandes.id_employe', '=', 'users.id')
            ->where('users.role', 'employe')
            ->groupBy('entreprises.id', 'entreprises.nom')
            ->orderBy('total_commandes', 'desc')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];

        foreach ($topEntreprises as $entreprise) {
            // Tronquer le nom si trop long
            $nom = strlen($entreprise->nom) > 20 ? substr($entreprise->nom, 0, 20) . '...' : $entreprise->nom;
            $labels[] = $nom;
            $data[] = $entreprise->total_commandes;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Calculer le taux de livraison (% de commandes livrées)
     */
    private function calculateTauxLivraison()
    {
        $totalCommandes = Commande::count();
        if ($totalCommandes == 0) return 0;

        $commandesLivrees = Commande::where('statut_livraison', 'livree')->count();
        
        return round(($commandesLivrees / $totalCommandes) * 100);
    }

    /**
     * Calculer le panier moyen
     */
    private function calculatePanierMoyen()
    {
        $totalCommandes = Commande::where('montant_total', '>', 0)->count();
        if ($totalCommandes == 0) return 0;

        $totalMontant = Commande::where('montant_total', '>', 0)->sum('montant_total');
        
        return round($totalMontant / $totalCommandes);
    }

    /**
     * Calculer le pourcentage de changement par rapport à la période précédente
     */
    private function calculateChange($query, $period = 'month', $column = null)
    {
        $isSum = $column !== null;
        
        if ($period === 'month') {
            $current = clone $query;
            $previous = clone $query;
            
            if ($isSum) {
                $currentValue = $current->where('created_at', '>=', Carbon::now()->startOfMonth())->sum($column);
                $previousValue = $previous->whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ])->sum($column);
            } else {
                $currentValue = $current->where('created_at', '>=', Carbon::now()->startOfMonth())->count();
                $previousValue = $previous->whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ])->count();
            }
            
            if ($previousValue > 0) {
                return round((($currentValue - $previousValue) / $previousValue) * 100);
            }
            
            return $currentValue > 0 ? 100 : 0;
        }
        
        return 0;
    }
}
