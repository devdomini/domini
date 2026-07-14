<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entreprise;
use App\Models\Warehouse;
use App\Models\WarehouseTrajetItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrajetController extends Controller
{
    /**
     * Liste des entrepôts avec accès au trajet.
     */
    public function index()
    {
        $warehouses = Warehouse::query()
            ->withCount('trajetItems')
            ->orderBy('name')
            ->get();

        return view('admin.trajets.index', compact('warehouses'));
    }

    /**
     * Définir l'ordre de livraison pour un entrepôt (entreprises dont la commune est liée à l'entrepôt).
     */
    public function edit(Warehouse $warehouse)
    {
        $eligible = $this->eligibleEntreprisesQuery($warehouse)->get();
        $eligibleIds = $eligible->pluck('id');

        // Retirer du trajet les entreprises dont la commune ne correspond plus à cet entrepôt
        if ($eligibleIds->isEmpty()) {
            $warehouse->trajetItems()->delete();
        } else {
            $warehouse->trajetItems()->whereNotIn('entreprise_id', $eligibleIds)->delete();
        }

        $orderedItems = $warehouse->trajetItems()
            ->with(['entreprise.commune'])
            ->orderBy('position')
            ->get();

        $inTrajetIds = $orderedItems->pluck('entreprise_id');
        $disponibles = $eligible->filter(fn ($e) => ! $inTrajetIds->contains($e->id))->values();

        return view('admin.trajets.edit', compact('warehouse', 'eligible', 'orderedItems', 'disponibles'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $eligibleIds = $this->eligibleEntreprisesQuery($warehouse)->pluck('id');

        $validated = $request->validate([
            'entreprise_ids' => 'nullable|array',
            'entreprise_ids.*' => 'integer|exists:entreprises,id',
        ]);

        $ids = collect($validated['entreprise_ids'] ?? [])->unique()->values();

        $ids = $ids->filter(fn ($id) => $eligibleIds->contains($id))->values();

        DB::transaction(function () use ($warehouse, $ids) {
            $warehouse->trajetItems()->delete();
            foreach ($ids as $index => $entrepriseId) {
                WarehouseTrajetItem::create([
                    'warehouse_id' => $warehouse->id,
                    'entreprise_id' => $entrepriseId,
                    'position' => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('admin.trajets.edit', $warehouse)
            ->with('success', 'Trajet de livraison enregistré.');
    }

    /**
     * Génère automatiquement l’ordre du trajet : du plus proche au plus loin de l’entrepôt (formule de Haversine).
     * Inclut toutes les entreprises éligibles ; celles sans coordonnées GPS sont placées à la fin.
     */
    public function autoProximity(Warehouse $warehouse): RedirectResponse
    {
        if (! $this->warehouseHasCoords($warehouse)) {
            return redirect()
                ->route('admin.trajets.edit', $warehouse)
                ->with('error', 'Renseignez les coordonnées GPS de l’entrepôt (latitude / longitude) pour calculer la proximité.');
        }

        $eligible = $this->eligibleEntreprisesQuery($warehouse)->get();

        if ($eligible->isEmpty()) {
            return redirect()
                ->route('admin.trajets.edit', $warehouse)
                ->with('error', 'Aucune entreprise éligible pour ce trajet.');
        }

        $whLat = (float) $warehouse->latitude;
        $whLng = (float) $warehouse->longitude;

        $withDistance = [];
        $sansCoords = [];

        foreach ($eligible as $e) {
            $coords = $this->resolveEntrepriseCoords($e);
            if ($coords === null) {
                $sansCoords[] = $e;

                continue;
            }
            $km = $this->haversineKm($whLat, $whLng, $coords['lat'], $coords['lng']);
            $withDistance[] = ['id' => $e->id, 'km' => $km];
        }

        usort($withDistance, fn ($a, $b) => $a['km'] <=> $b['km']);

        usort($sansCoords, fn ($a, $b) => strcmp($a->nom, $b->nom));

        $orderedIds = collect($withDistance)->pluck('id')->values();
        foreach ($sansCoords as $e) {
            $orderedIds->push($e->id);
        }

        DB::transaction(function () use ($warehouse, $orderedIds) {
            $warehouse->trajetItems()->delete();
            foreach ($orderedIds as $index => $entrepriseId) {
                WarehouseTrajetItem::create([
                    'warehouse_id' => $warehouse->id,
                    'entreprise_id' => $entrepriseId,
                    'position' => $index + 1,
                ]);
            }
        });

        $msg = 'Trajet généré automatiquement : ordre du plus proche au plus loin de l’entrepôt.';
        if (count($sansCoords) > 0) {
            $msg .= ' '.count($sansCoords).' entreprise(s) sans coordonnées GPS (entreprise ou commune) placée(s) à la fin.';
        }

        return redirect()
            ->route('admin.trajets.edit', $warehouse)
            ->with('success', $msg);
    }

    /**
     * Distance à vol d’oiseau entre deux points (km).
     */
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
     * Carte Google Maps : visualisation du trajet (entrepôt puis entreprises dans l’ordre).
     */
    public function map(Warehouse $warehouse): View
    {
        $orderedItems = $warehouse->trajetItems()
            ->with(['entreprise.commune'])
            ->orderBy('position')
            ->get();

        $waypoints = [];
        $sansCoordonnees = [];

        if ($this->warehouseHasCoords($warehouse)) {
            $waypoints[] = [
                'kind' => 'warehouse',
                'title' => $warehouse->name,
                'subtitle' => 'Départ — entrepôt',
                'lat' => (float) $warehouse->latitude,
                'lng' => (float) $warehouse->longitude,
                'order' => null,
                'label' => 'D',
            ];
        }

        foreach ($orderedItems as $item) {
            $e = $item->entreprise;
            if (! $e) {
                continue;
            }
            $coords = $this->resolveEntrepriseCoords($e);
            if ($coords === null) {
                $sansCoordonnees[] = [
                    'nom' => $e->nom,
                    'position' => $item->position,
                    'raison' => 'Ajoutez des coordonnées GPS à l’entreprise ou à sa commune.',
                ];

                continue;
            }
            $waypoints[] = [
                'kind' => 'stop',
                'title' => $e->nom,
                'subtitle' => $e->commune?->nom ?? '',
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'order' => $item->position,
                'label' => (string) $item->position,
            ];
        }

        $apiKey = config('services.google_maps.api_key');

        return view('admin.trajets.map', [
            'warehouse' => $warehouse,
            'waypoints' => $waypoints,
            'sansCoordonnees' => $sansCoordonnees,
            'apiKey' => $apiKey,
        ]);
    }

    private function warehouseHasCoords(Warehouse $warehouse): bool
    {
        return $warehouse->latitude !== null
            && $warehouse->longitude !== null
            && $warehouse->latitude !== ''
            && $warehouse->longitude !== '';
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    private function resolveEntrepriseCoords(Entreprise $e): ?array
    {
        if ($e->lat !== null && $e->long !== null && $e->lat !== '' && $e->long !== '') {
            return ['lat' => (float) $e->lat, 'lng' => (float) $e->long];
        }
        $c = $e->commune;
        if ($c && $c->latitude !== null && $c->longitude !== null && $c->latitude !== '' && $c->longitude !== '') {
            return ['lat' => (float) $c->latitude, 'lng' => (float) $c->longitude];
        }

        return null;
    }

    private function eligibleEntreprisesQuery(Warehouse $warehouse)
    {
        return Entreprise::query()
            ->where('statut', true)
            ->whereHas('commune', fn ($q) => $q->where('warehouse_id', $warehouse->id))
            ->with('commune')
            ->orderBy('nom');
    }
}
