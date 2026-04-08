<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_user_create_page_to_login(): void
    {
        $response = $this->get('/admin/users/create');

        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_from_admin_user_store_to_login(): void
    {
        $response = $this->post('/admin/users', [
            'name' => 'Guest Attempt',
            'email' => 'guest.attempt@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_non_admin_user_cannot_access_admin_user_create_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users/create');

        $response->assertForbidden();
    }

    public function test_non_admin_user_cannot_store_admin_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/users', [
            'name' => 'Blocked Attempt',
            'email' => 'blocked.attempt@example.com',
            'password' => 'secret123',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_user_create_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/users/create');

        $response
            ->assertOk()
            ->assertSee('Create user')
            ->assertSee('Name')
            ->assertSee('Email')
            ->assertSee('Password')
            ->assertSee('Grant admin access')
            ->assertSee(route('admin.users.store'), false);
    }

    public function test_admin_user_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Created User',
            'email' => 'created.user@example.com',
            'password' => 'secret123',
            'is_admin' => '1',
        ]);

        $user = User::query()->where('email', 'created.user@example.com')->first();

        $this->assertNotNull($user);

        $response->assertRedirect("/admin/users/{$user->id}");

        $this->assertSame('Created User', $user->name);
        $this->assertTrue($user->is_admin);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_store_validates_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->from('/admin/users/create')->actingAs($admin)->post('/admin/users', [
            'name' => '',
            'email' => '',
            'password' => '',
        ]);

        $response
            ->assertRedirect('/admin/users/create')
            ->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_store_validates_duplicate_email(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $response = $this->from('/admin/users/create')->actingAs($admin)->post('/admin/users', [
            'name' => 'Duplicate Email',
            'email' => 'duplicate@example.com',
            'password' => 'secret123',
        ]);

        $response
            ->assertRedirect('/admin/users/create')
            ->assertSessionHasErrors(['email']);
    }

    public function test_store_validates_password_minimum_length(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->from('/admin/users/create')->actingAs($admin)->post('/admin/users', [
            'name' => 'Short Password',
            'email' => 'short.password@example.com',
            'password' => 'short',
        ]);

        $response
            ->assertRedirect('/admin/users/create')
            ->assertSessionHasErrors(['password']);
    }
}
