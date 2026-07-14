<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLivreurVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_livreur_cree_par_admin_est_considerer_comme_telephone_verifie(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin);
        $this->withoutMiddleware();

        $email = 'livreur.admin.'.now()->timestamp.'@example.test';

        $this->post(route('admin.livreurs.store'), [
            'name' => 'Livreur Admin',
            'email' => $email,
            'telephone' => '0700000000',
            'password' => 'password123',
            'is_active' => '1',
        ])->assertStatus(302);

        $livreur = User::where('email', $email)->firstOrFail();

        $this->assertSame('livreur', $livreur->role);
        $this->assertNotNull($livreur->telephone_verified_at);
        $this->assertNull($livreur->code_verification);
        $this->assertNull($livreur->code_expires_at);
    }
}
