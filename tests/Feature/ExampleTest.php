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
            ->assertSee('Galaxy-specific Laravel foundation, not a generic starter.')
            ->assertSee('Open admin workspace')
            ->assertSee('Phase 1 snapshot');
    }
}
