<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_admin_dashboard_page_is_available(): void
    {
        $response = $this->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Galaxi Admin')
            ->assertSee('Placeholder dashboard shell')
            ->assertSee('/admin');
    }
}
