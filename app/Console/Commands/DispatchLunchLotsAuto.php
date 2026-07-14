<?php

namespace App\Console\Commands;

use App\Models\Warehouse;
use App\Services\LunchDispatchService;
use Illuminate\Console\Command;

/**
 * Ré-exécute le dispatch des commandes lot (is_lunch) pour tous les entrepôts actifs.
 * Complète le dispatch déjà lancé à chaque nouvelle commande (CommandeApiController::store).
 */
class DispatchLunchLotsAuto extends Command
{
    protected $signature = 'dispatch:lunch-lots-auto';

    protected $description = 'Dispatch automatique des lots entreprise (aujourd\'hui et demain) par entrepôt actif';

    public function handle(): int
    {
        $warehouses = Warehouse::query()->where('is_active', true)->get();
        if ($warehouses->isEmpty()) {
            return self::SUCCESS;
        }

        $dates = [
            now()->toDateString(),
            now()->addDay()->toDateString(),
        ];

        foreach ($warehouses as $warehouse) {
            foreach ($dates as $date) {
                try {
                    $result = LunchDispatchService::dispatchForWarehouse($warehouse, $date, 200);
                    if (($result['assigned_count'] ?? 0) > 0 && $this->output->isVerbose()) {
                        $this->line("Entrepôt {$warehouse->id} · {$date} : assignées {$result['assigned_count']}");
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('dispatch:lunch-lots-auto échec', [
                        'warehouse_id' => $warehouse->id,
                        'date' => $date,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return self::SUCCESS;
    }
}
