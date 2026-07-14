<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Warehouse;
use App\Services\LunchDispatchService;
use Illuminate\Console\Command;

class DispatchLotsForLivreur extends Command
{
    protected $signature = 'dispatch:lots-livreur {user_id : ID du livreur} {--date= : YYYY-MM-DD (défaut: aujourd\'hui)} {--max=200 : Max colis par livreur}';

    protected $description = 'Dispatch des commandes lot (is_lunch) sur un livreur précis, selon trajet de son entrepôt.';

    public function handle(): int
    {
        $userId = (int) $this->argument('user_id');
        $date = (string) ($this->option('date') ?: now()->toDateString());
        $max = (int) ($this->option('max') ?: 200);

        /** @var User|null $livreur */
        $livreur = User::query()->where('role', 'livreur')->find($userId);
        if (! $livreur) {
            $this->error("Livreur introuvable: user_id={$userId}");
            return self::FAILURE;
        }
        if (! $livreur->warehouse_id) {
            $this->error("Le livreur {$userId} n'a pas d'entrepôt (warehouse_id NULL).");
            return self::FAILURE;
        }

        $warehouse = Warehouse::query()->find((int) $livreur->warehouse_id);
        if (! $warehouse) {
            $this->error("Entrepôt introuvable: warehouse_id={$livreur->warehouse_id}");
            return self::FAILURE;
        }

        $result = LunchDispatchService::dispatchForWarehouseToLivreurs($warehouse, $date, $max, [$livreur->id]);

        $this->info("Entrepôt: {$warehouse->id} - {$warehouse->name}");
        $this->info("Date: {$date} | Max: {$max} | Livreur: {$livreur->id} - {$livreur->name}");
        $this->info("assigned={$result['assigned_count']} unassigned={$result['unassigned_count']} skipped_not_in_trajet={$result['skipped_not_in_trajet']}");
        if (isset($result['debug']) && is_array($result['debug'])) {
            $d = $result['debug'];
            $this->line('debug: livres=' . ($d['livreurs_total'] ?? '?')
                . ' trajet_entreprises=' . ($d['trajet_entreprises_total'] ?? '?')
                . ' candidates=' . ($d['candidates_total'] ?? '?')
                . ' candidates_in_trajet=' . ($d['candidates_in_trajet_total'] ?? '?')
                . ' ordered=' . ($d['ordered_total'] ?? '?'));
        }

        return self::SUCCESS;
    }
}

