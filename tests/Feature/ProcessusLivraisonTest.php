<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Commande;
use App\Models\Entreprise;
use App\Models\Livraison;
use App\Models\Plat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProcessusLivraisonTest extends TestCase
{
    use RefreshDatabase;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $connection = getenv('DB_CONNECTION') ?: null;
        if ($connection !== 'mysql') return;

        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $db = getenv('DB_DATABASE') ?: null;
        $user = getenv('DB_USERNAME') ?: 'root';
        $pass = getenv('DB_PASSWORD') ?: '';

        if (!$db) return;

        $dsn = "mysql:host={$host};port={$port}";
        $pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);

        $dbSafe = str_replace('`', '``', $db);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbSafe}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    private function seedMinimalMenu(): Plat
    {
        $categorie = Categorie::create([
            'nom' => 'Catégorie test',
            'logo' => null,
            'est_disponible' => true,
            'qualite' => 'classic',
        ]);

        return Plat::create([
            'nom' => 'Plat test',
            'prix' => 2000,
            'image' => null,
            'est_disponible' => true,
            'detail' => null,
            'categorie_id' => $categorie->id,
            'qualite' => 'classic',
        ]);
    }

    private function createLivreur(string $email, bool $active = true): User
    {
        return User::factory()->create([
            'email' => $email,
            'role' => 'livreur',
            'telephone' => '0100000000',
            'is_active' => $active,
        ]);
    }

    private function createEmploye(string $email, ?int $entrepriseId = null, ?string $numBox = null): User
    {
        return User::factory()->create([
            'email' => $email,
            'role' => 'employe',
            'telephone' => '0200000000',
            'id_entreprise' => $entrepriseId,
            'num_box' => $numBox,
            'is_active' => true,
        ]);
    }

    private function updateLivreurLocation(User $livreur, float $lat, float $lng): void
    {
        Sanctum::actingAs($livreur);
        $this->postJson('/api/livreur/location', [
            'lat' => $lat,
            'long' => $lng,
        ])->assertStatus(200);
    }

    public function test_commande_classique_est_assignee_automatiquement_au_livreur_proche_et_connecte(): void
    {
        $plat = $this->seedMinimalMenu();

        $client = $this->createEmploye('client@classique.test');

        $livreurProche = $this->createLivreur('livreur.proche@test.tld');
        $livreurLoin = $this->createLivreur('livreur.loin@test.tld');

        $this->updateLivreurLocation($livreurProche, 5.3300, -4.0200);
        $this->updateLivreurLocation($livreurLoin, 5.5000, -4.2000);

        Sanctum::actingAs($client);

        $resp = $this->postJson('/api/commandes', [
            'items' => [
                [
                    'plat_id' => $plat->id,
                    'quantite' => 1,
                    'accompagnements' => [],
                    'options' => [],
                ],
            ],
            'lieu' => 'Adresse test',
            'lat' => 5.3310,
            'long' => -4.0210,
            'is_lunch' => false,
        ]);

        $resp->assertStatus(201)->assertJsonPath('success', true);

        $commandeId = $resp->json('data.commande.id');
        $this->assertNotNull($commandeId);

        $commande = Commande::with('livraison')->findOrFail($commandeId);
        $this->assertNotNull($commande->livraison);
        $this->assertSame('assignee', $commande->livraison->statut);
        $this->assertSame($livreurProche->id, (int) $commande->livraison->livreur_id);
    }

    public function test_commande_box_priorise_livreurs_entreprise_sinon_fallback_proche_connecte(): void
    {
        $plat = $this->seedMinimalMenu();

        $entreprise = Entreprise::create([
            'nom' => 'Entreprise test',
            'adresse' => 'Adresse entreprise',
            'lat' => 5.4000,
            'long' => -4.1000,
            'numero' => null,
            'pays' => "Côte d'Ivoire",
            'ville' => 'Abidjan',
            'logo' => null,
            'statut' => true,
        ]);

        $client = $this->createEmploye('client@box.test', $entreprise->id, '12');

        $livreurEntreprise = $this->createLivreur('livreur.entreprise@test.tld');
        $livreurEntreprise->entreprises()->attach($entreprise->id);
        $livreurEntreprise->update([
            'current_lat' => 5.4010,
            'current_long' => -4.1010,
            'last_location_at' => now()->subHours(2),
        ]);

        $livreurFallback = $this->createLivreur('livreur.fallback@test.tld');
        $this->updateLivreurLocation($livreurFallback, 5.4015, -4.1015);

        Sanctum::actingAs($client);

        $date = now()->toDateString();
        $resp = $this->postJson('/api/commandes', [
            'items' => [
                [
                    'plat_id' => $plat->id,
                    'quantite' => 1,
                    'accompagnements' => [],
                    'options' => [],
                ],
            ],
            'is_lunch' => true,
            'date_livraison' => $date,
        ]);

        $resp->assertStatus(201)->assertJsonPath('success', true);
        $commandeId = $resp->json('data.commande.id');

        $commande = Commande::with('livraison')->findOrFail($commandeId);
        $this->assertNotNull($commande->livraison);
        $this->assertSame('assignee', $commande->livraison->statut);
        $this->assertSame($livreurFallback->id, (int) $commande->livraison->livreur_id);

        $resp2 = $this->postJson('/api/commandes', [
            'items' => [
                [
                    'plat_id' => $plat->id,
                    'quantite' => 1,
                    'accompagnements' => [],
                    'options' => [],
                ],
            ],
            'is_lunch' => true,
            'date_livraison' => $date,
        ]);
        $resp2->assertStatus(403);
    }

    public function test_refus_livreur_reassigne_automatiquement_a_un_autre_livreur_disponible(): void
    {
        $plat = $this->seedMinimalMenu();

        $client = $this->createEmploye('client@refus.test');

        $livreurA = $this->createLivreur('livreur.a@test.tld');
        $livreurB = $this->createLivreur('livreur.b@test.tld');

        $this->updateLivreurLocation($livreurA, 5.3000, -4.0000);
        $this->updateLivreurLocation($livreurB, 5.3010, -4.0010);

        Sanctum::actingAs($client);
        $resp = $this->postJson('/api/commandes', [
            'items' => [
                [
                    'plat_id' => $plat->id,
                    'quantite' => 1,
                    'accompagnements' => [],
                    'options' => [],
                ],
            ],
            'lieu' => 'Adresse test',
            'lat' => 5.3002,
            'long' => -4.0002,
            'is_lunch' => false,
        ]);
        $resp->assertStatus(201)->assertJsonPath('success', true);

        $commandeId = $resp->json('data.commande.id');
        $commande = Commande::with('livraison')->findOrFail($commandeId);
        $livraison = Livraison::findOrFail($commande->livraison->id);
        $this->assertSame($livreurA->id, (int) $livraison->livreur_id);

        Sanctum::actingAs($livreurA);
        $this->postJson('/api/livreur/livraisons/' . $livraison->id . '/refuser', [
            'commentaire' => 'Indisponible',
        ])->assertStatus(200);

        $livraison->refresh();
        $this->assertSame('assignee', $livraison->statut);
        $this->assertSame($livreurB->id, (int) $livraison->livreur_id);
        $this->assertIsArray($livraison->refused_livreur_ids);
        $this->assertContains($livreurA->id, $livraison->refused_livreur_ids);
    }
}
