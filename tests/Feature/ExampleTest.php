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
            ->assertSee('Blueprint, Phase 1 plan, checkpoints, progress log')
            ->assertSee('Focused checks after each safe slice')
            ->assertSee('Every safe slice leaves a visible Git checkpoint')
            ->assertSee('Cardholders and card inventory foundations')
            ->assertSee('Preserve Galaxy admin information architecture')
            ->assertSee('Land small safe foundation slices with visible Git history')
            ->assertSee('Keep checkpoints, analysis notes, and QA references close to the work')
            ->assertSee('Helpful project docs')
            ->assertSee('Doc coverage:')
            ->assertSee('18 public Galaxy migration references currently linked.')
            ->assertSee('Seam-source coverage:')
            ->assertSee('4 Phase 1 seam sources currently documented in')
            ->assertSee('README.md')
            ->assertSee('Seam-source baseline:')
            ->assertSee('config/phase-1-seam-sources.php')
            ->assertSee('Source of truth:')
            ->assertSee('config/landing-docs.php')
            ->assertSee('OpenClaw docs')
            ->assertSee('docs/blueprint.md')
            ->assertSee('docs/phase-1-domain-map.md')
            ->assertSee('docs/phase-1-foundation-seams.md')
            ->assertSee('docs/migration-plan.md')
            ->assertSee('docs/migration_plan.md')
            ->assertSee('docs/admin-information-architecture.md')
            ->assertSee('docs/admin-shell-layering.md')
            ->assertSee('docs/admin-shell-config-map.md')
            ->assertSee('docs/decisions.md')
            ->assertSee('docs/module_mapping.md')
            ->assertSee('docs/db_schema.md')
            ->assertSee('docs/api_endpoints.md')
            ->assertSee('docs/checkpoints/')
            ->assertSee('docs/analysis/')
            ->assertSee('docs/qa-test-environment.md')
            ->assertSee('docs/progress-log.md')
            ->assertSee('Open admin workspace')
            ->assertSee('Phase 1 snapshot');
    }
}
