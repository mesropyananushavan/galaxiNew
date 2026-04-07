<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_users_index_to_login(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('/login');
    }

    public function test_non_admin_user_cannot_access_admin_users_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_users_index(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Person',
            'email' => 'admin.person@example.com',
        ]);

        $member = User::factory()->create([
            'name' => 'Regular Member',
            'email' => 'member@example.com',
        ]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response
            ->assertOk()
            ->assertSee('Users')
            ->assertSee('Admin Person')
            ->assertSee('admin.person@example.com')
            ->assertSee('Regular Member')
            ->assertSee('member@example.com')
            ->assertSee('Yes')
            ->assertSee('No');
    }

    public function test_admin_users_index_shows_empty_state_when_no_users_exist(): void
    {
        $admin = User::factory()->admin()->make([
            'name' => 'Transient Admin',
            'email' => 'transient-admin@example.com',
        ]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response
            ->assertOk()
            ->assertSee('No users found yet.');
    }
}
