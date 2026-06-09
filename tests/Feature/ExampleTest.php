<?php

namespace Tests\Feature;

use App\Http\Controllers\LandingPageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_route_uses_galaxy_landing_page_controller(): void
    {
        $route = Route::getRoutes()->match(Request::create('/', 'GET'));

        $this->assertSame(LandingPageController::class, $route->getActionName());
        $this->assertSame('landing', $route->getName());
        $this->assertSame(url('/'), route('landing'));
    }

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
            ->assertSee('Galaxi migration foundation')
            ->assertSee('Landing focus')
            ->assertSee('Keep the public landing page anchored to Galaxy-specific Phase 1 posture instead of drifting back toward starter copy.')
            ->assertSee('Target posture')
            ->assertSee('Primary route')
            ->assertSee('Current focus')
            ->assertSee('Reference trail')
            ->assertSee('QA rhythm')
            ->assertSee('Commit trail')
            ->assertSee('Migration mode')
            ->assertSee('Helpful project docs')
            ->assertSee('Doc focus:')
            ->assertSee('Keep the public Galaxy migration reference trail visible from the landing surface while repo guidance and Phase 1 seams are still moving.')
            ->assertSee('Doc coverage:')
            ->assertSee('29 public Galaxy migration references currently linked.')
            ->assertSee('Doc baseline:')
            ->assertSee('config/landing-docs.php')
            ->assertSee('Doc guide:')
            ->assertSee('README.md')
            ->assertSee('docs/blueprint.md')
            ->assertSee('docs/phase-1-plan.md')
            ->assertSee('Doc posture:')
            ->assertSee('public reference inventory stays explicit across the live Galaxy landing trail.')
            ->assertSee('Seam-source focus:')
            ->assertSee('Keep the README-level seam-source inventory visible across repo guidance plus the admin and public Phase 1 entry surfaces.')
            ->assertSee('Seam-source coverage:')
            ->assertSee('10 Phase 1 seam sources currently documented in')
            ->assertSee('README.md')
            ->assertSee('Seam-source baseline:')
            ->assertSee('config/phase-1-seam-sources.php')
            ->assertSee('Seam-source posture:')
            ->assertSee('README-backed seam-source baseline stays explicit across the live Galaxy reference trail.')
            ->assertSee('Seam-source source of truth:')
            ->assertSee('README.md')
            ->assertSee('config/phase-1-seam-sources.php')
            ->assertSee('Source of truth:')
            ->assertSee('README.md')
            ->assertSee('docs/phase-1-access-baseline.md')
            ->assertSee('config/phase-1-access-baseline.php')
            ->assertSee('docs/phase-1-shop-access-baseline.md')
            ->assertSee('config/phase-1-shop-access-baseline.php')
            ->assertSee('docs/phase-1-model-skeletons.md')
            ->assertSee('config/phase-1-model-skeletons.php')
            ->assertSee('docs/phase-1-migration-baseline.md')
            ->assertSee('config/phase-1-migration-baseline.php')
            ->assertSee('config/landing-docs.php')
            ->assertSee('Reference seam bridge:')
            ->assertSee('config/phase-1-seam-sources.php')
            ->assertSee('OpenClaw docs')
            ->assertSee('docs/blueprint.md')
            ->assertSee('docs/phase-1-domain-map.md')
            ->assertSee('docs/phase-1-foundation-seams.md')
            ->assertSee('docs/phase-1-access-baseline.md')
            ->assertSee('config/phase-1-access-baseline.php')
            ->assertSee('docs/phase-1-shop-access-baseline.md')
            ->assertSee('config/phase-1-shop-access-baseline.php')
            ->assertSee('docs/phase-1-model-skeletons.md')
            ->assertSee('config/phase-1-model-skeletons.php')
            ->assertSee('docs/phase-1-migration-baseline.md')
            ->assertSee('config/phase-1-migration-baseline.php')
            ->assertSee('config/landing-foundation.php')
            ->assertSee('config/phase-1-access-baseline.php')
            ->assertSee('config/phase-1-shop-access-baseline.php')
            ->assertSee('config/phase-1-model-skeletons.php')
            ->assertSee('config/phase-1-migration-baseline.php')
            ->assertSee('config/phase-1-seam-sources.php')
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
            ->assertSee('Admin login')
            ->assertSee('Live management surfaces')
            ->assertSee('Working rules')
            ->assertSee('Phase 1 snapshot')
            ->assertSee('Reference seam bridge:');
    }
}
