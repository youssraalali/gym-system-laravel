<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_protected_route_requires_token(): void
    {
        $response = $this->getJson('/api/members');
        $response->assertStatus(401)
            ->assertJsonStructure([
                'message'
            ]);
    }


    public function test_member_cannot_access_admin_routes(): void
    {
        Sanctum::actingAs(
        $user = User::factory()->create([
            'role' => 'member',
            'email' => 'test@example.com'
        ])
        );

        $response = $this->getJson('/api/members');

        $response->assertStatus(403)
            ->assertJsonStructure([
                'message'
            ]);
    }

    public function test_guest_get_redirected_to_login_when_accessing_admin_routes(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

}
