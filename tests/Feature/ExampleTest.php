<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_home_page_surfaces_the_galaxy_foundation_landing_page(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Galaxy-specific foundation, not generic scaffolding.')
            ->assertSee('Galaxy foundation home for admin flows')
            ->assertSee('current foundation layer is focused on replacing scaffold defaults with Galaxy operational context')
            ->assertSee('Cardholders and card inventory foundations')
            ->assertSee('Land small safe foundation slices with visible Git history')
            ->assertSee('Open admin workspace')
            ->assertSee('Phase 1 snapshot');
    }
}
