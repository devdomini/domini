<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Livraison;
use App\Models\User;
use Illuminate\Http\Request;

class TrafficLivreurController extends Controller
{
    public function index()
    {
        $apiKey = config('services.google_maps.api_key');
        return view('admin.traffic-livreur.index', [
            'apiKey' => $apiKey,
        ]);
    }

    public function data(Request $request)
    {
        // Filtres période (sur livraisons)
        // - date: YYYY-MM-DD (jour précis)
        // - from/to: YYYY-MM-DD (plage inclusive)
        $date = $request->input('date');
        $from = $request->input('from');
        $to = $request->input('to');

        $onlineCutoff = now()->subMinutes(10);

        // Livreurs (positions + statut)
        $livreurs = User::query()
            ->where('role', 'livreur')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'telephone',
                'is_active',
                'is_dispo',
                'current_lat',
                'current_long',
                'last_location_at',
                'type_livreur',
            ])
            ->map(function ($u) use ($onlineCutoff) {
                $isOnline = $u->last_location_at ? $u->last_location_at->gte($onlineCutoff) : false;
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'telephone' => $u->telephone,
                    'is_active' => (bool) $u->is_active,
                    'is_dispo' => (bool) ($u->is_dispo ?? true),
                    'is_online' => (bool) $isOnline,
                    'type_livreur' => $u->type_livreur,
                    'lat' => $u->current_lat !== null ? (float) $u->current_lat : null,
                    'lng' => $u->current_long !== null ? (float) $u->current_long : null,
                    'last_location_at' => $u->last_location_at?->toISOString(),
                ];
            })
            ->values();

        // Livraisons (flux) : par défaut actives, ou filtrées par période si fournie.
        $livraisonsQuery = Livraison::query()
            ->with([
                'livreur:id,name,telephone,current_lat,current_long,is_active,is_dispo,last_location_at',
                'commande:id,ref,lat,long,lieu,is_lunch,statut_commande,statut_livraison,id_employe',
                'commande.employe:id,name,id_entreprise,num_box',
                'commande.employe.entreprise:id,nom,lat,long',
                'commande.adresse:id,latitude,longitude,lieu',
            ])
            ->orderBy('updated_at', 'desc');

        // Si une période est définie, on filtre sur created_at (couvre un jour précis ou une plage)
        // Sinon, on affiche uniquement les livraisons actives.
        if ($date) {
            $start = $date . ' 00:00:00';
            $end = $date . ' 23:59:59';
            $livraisonsQuery->whereBetween('created_at', [$start, $end]);
        } elseif ($from || $to) {
            $start = ($from ?: $to) . ' 00:00:00';
            $end = ($to ?: $from) . ' 23:59:59';
            $livraisonsQuery->whereBetween('created_at', [$start, $end]);
        } else {
            $livraisonsQuery->whereIn('statut', ['assignee', 'en_cours']);
        }

        $livraisons = $livraisonsQuery
            ->limit(250)
            ->get()
            ->map(function ($l) {
                $cmd = $l->commande;
                $liv = $l->livreur;

                $destLat = null;
                $destLng = null;
                $destLabel = null;

                if ($cmd) {
                    $destLabel = $cmd->lieu;

                    // Si commande lunch: destination = entreprise
                    if ($cmd->is_lunch && $cmd->employe && $cmd->employe->entreprise) {
                        $destLat = $cmd->employe->entreprise->lat !== null ? (float) $cmd->employe->entreprise->lat : null;
                        $destLng = $cmd->employe->entreprise->long !== null ? (float) $cmd->employe->entreprise->long : null;
                        $destLabel = $cmd->employe->entreprise->nom;
                    } elseif ($cmd->adresse) {
                        $destLat = $cmd->adresse->latitude !== null ? (float) $cmd->adresse->latitude : null;
                        $destLng = $cmd->adresse->longitude !== null ? (float) $cmd->adresse->longitude : null;
                        $destLabel = $cmd->adresse->lieu ?? $cmd->lieu;
                    } else {
                        $destLat = $cmd->lat !== null ? (float) $cmd->lat : null;
                        $destLng = $cmd->long !== null ? (float) $cmd->long : null;
                    }
                }

                return [
                    'id' => $l->id,
                    'statut' => $l->statut,
                    'commande' => $cmd ? [
                        'id' => $cmd->id,
                        'ref' => $cmd->ref,
                        'is_lunch' => (bool) $cmd->is_lunch,
                        'statut_commande' => $cmd->statut_commande,
                        'statut_livraison' => $cmd->statut_livraison,
                        'lieu' => $cmd->lieu,
                    ] : null,
                    'livreur' => $liv ? [
                        'id' => $liv->id,
                        'name' => $liv->name,
                        'telephone' => $liv->telephone,
                        'lat' => $liv->current_lat !== null ? (float) $liv->current_lat : null,
                        'lng' => $liv->current_long !== null ? (float) $liv->current_long : null,
                        'is_active' => (bool) $liv->is_active,
                        'is_dispo' => (bool) ($liv->is_dispo ?? true),
                        'last_location_at' => $liv->last_location_at?->toISOString(),
                    ] : null,
                    'destination' => [
                        'lat' => $destLat,
                        'lng' => $destLng,
                        'label' => $destLabel,
                    ],
                ];
            })
            ->values();

        $stats = [
            'livreurs_total' => $livreurs->count(),
            'livreurs_actifs' => $livreurs->where('is_active', true)->count(),
            'livreurs_dispo' => $livreurs->where('is_active', true)->where('is_dispo', true)->count(),
            'livraisons_actives' => $livraisons->count(),
            'livraisons_assignees' => $livraisons->where('statut', 'assignee')->count(),
            'livraisons_en_cours' => $livraisons->where('statut', 'en_cours')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'livreurs' => $livreurs,
                'livraisons' => $livraisons,
                'filters' => [
                    'date' => $date,
                    'from' => $from,
                    'to' => $to,
                ],
                'server_time' => now()->toISOString(),
            ],
        ]);
    }
}

