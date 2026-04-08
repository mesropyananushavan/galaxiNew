<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_user_show_to_login(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/admin/users/{$user->id}");

        $response->assertRedirect('/login');
    }

    public function test_non_admin_user_cannot_access_admin_user_show(): void
    {
        $actor = User::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($actor)->get("/admin/users/{$user->id}");

        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_user_show(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'name' => 'Viewed User',
            'email' => 'viewed.user@example.com',
            'is_admin' => false,
            'created_at' => now()->setDate(2026, 4, 7)->setTime(15, 30),
        ]);

        $response = $this->actingAs($admin)->get("/admin/users/{$user->id}");

        $response
            ->assertOk()
            ->assertSee('User details')
            ->assertSee('Viewed User')
            ->assertSee('viewed.user@example.com')
            ->assertSee('No')
            ->assertSee('2026-04-07 15:30');
    }
}
