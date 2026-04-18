<?php

namespace Tests\Feature;

use App\Models\CardType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_login_page_is_available(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSee('Admin login')
            ->assertSee('Minimal session-based sign in');
    }

    public function test_authenticated_user_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Galaxi Admin')
            ->assertSee('Phase 1 admin information architecture baseline')
            ->assertSee('/admin')
            ->assertSee('Operations')
            ->assertSee('Cardholders')
            ->assertSee('Roles &amp; Permissions')
            ->assertSee('Planned sections')
            ->assertSee('9');
    }

    public function test_authenticated_user_can_access_cardholders_placeholder_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders');

        $response
            ->assertOk()
            ->assertSee('Cardholders placeholder')
            ->assertSee('cardholders')
            ->assertSee('Phase');
    }

    public function test_authenticated_user_can_access_roles_permissions_placeholder_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions');

        $response
            ->assertOk()
            ->assertSee('Roles &amp; Permissions placeholder')
            ->assertSee('roles-permissions')
            ->assertSee('shop-scoped access rules');
    }

    public function test_authenticated_user_can_access_roles_permissions_management_preview(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions');

        $response
            ->assertOk()
            ->assertSee('Create or edit role')
            ->assertSee('Publish role')
            ->assertSee('New role')
            ->assertSee('Review matrix')
            ->assertSee('Management snapshot')
            ->assertSee('Active roles')
            ->assertSee('Scoped shops')
            ->assertSee('No shop-scoped roles configured yet')
            ->assertSee('Create first role')
            ->assertSee('Role publishing is still preview-only')
            ->assertSee('Role identity')
            ->assertSee('Access policy')
            ->assertSee('Compare staff roles')
            ->assertSee('Preview matrix impact')
            ->assertSee('old Galaxy staff model')
            ->assertSee('authorization matrix and assignment flow')
            ->assertSee('Legacy parity mapping')
            ->assertSee('Recent activity preview')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Implementation dependencies')
            ->assertSee('Operator checklist')
            ->assertSee('Review shop scope before publishing a manager or cashier role change.')
            ->assertSee('Escalation guide')
            ->assertSee('Escalate shop-scope disagreements before changing a role bundle.')
            ->assertSee('Shift handoff notes')
            ->assertSee('Hand off draft roles with the exact legacy bundle they are meant to mirror.')
            ->assertSee('Open issues to carry')
            ->assertSee('Cashier assignment rules remain unverified against legacy shop-scoped behavior.')
            ->assertSee('First Laravel wiring step')
            ->assertSee('Once PHP-backed flows are possible, start with a minimal role create/update path before exposing full assignment screens.')
            ->assertSee('Persist a minimal role record before tackling permission matrix editing.')
            ->assertSee('Role and Permission models plus migration skeletons exist')
            ->assertSee('Assignment UI, policy wiring, and persistence handlers are still pending')
            ->assertSee('Legacy role boundaries mapped')
            ->assertSee('Assignment and persistence flows still need PHP-backed authorization work')
            ->assertSee('Shop Manager bundle reviewed')
            ->assertSee('Cashier draft held back')
            ->assertSee('Old Galaxy staff and access matrix')
            ->assertSee('cashier/manager split')
            ->assertSee('Shop Manager')
            ->assertSee('Scoped to assigned shop');
    }

    public function test_authenticated_user_can_access_cards_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards');

        $response
            ->assertOk()
            ->assertSee('Cards placeholder')
            ->assertSee('Operational index shape')
            ->assertSee('GX-100001')
            ->assertSee('Central Shop')
            ->assertSee('Issue card')
            ->assertSee('Review blocked cards')
            ->assertSee('Active cards')
            ->assertSee('Draft cards')
            ->assertSee('Blocked cards')
            ->assertSee('Card operations are still preview-only')
            ->assertSee('Inventory actions, status metrics, and filters are laid out for Galaxy parity, but they are not connected to Laravel handlers yet.')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview inventory statuses and card-type filters are defined')
            ->assertSee('Inventory queries and card lifecycle handlers still need PHP-backed Laravel wiring')
            ->assertSee('Implementation dependencies')
            ->assertSee('Card and CardType models plus migration skeletons exist')
            ->assertSee('Inventory reads, assignment flows, and status mutations are still pending')
            ->assertSee('First Laravel wiring step')
            ->assertSee('When PHP is available, start with a read-only inventory table before exposing issue, block, or assignment flows.')
            ->assertSee('Load cards with holder, type, status, and activation timestamp columns.')
            ->assertSee('Recent activity preview')
            ->assertSee('Blocked card state kept visible')
            ->assertSee('Draft card review deferred')
            ->assertSee('Legacy parity mapping')
            ->assertSee('Old Galaxy card inventory screen')
            ->assertSee('Card states, holder linkage, activation visibility')
            ->assertSee('Operator checklist')
            ->assertSee('Review blocked cards before issuing new replacements.')
            ->assertSee('Escalation guide')
            ->assertSee('Escalate blocked-card disputes before issuing a fresh card number.')
            ->assertSee('Shift handoff notes')
            ->assertSee('Carry blocked-card disputes into the next shift until replacement is approved.')
            ->assertSee('Open issues to carry')
            ->assertSee('Blocked-card disputes remain open until replacement approval is explicit.')
            ->assertSee('Operational glossary')
            ->assertSee('Legacy parity notes')
            ->assertSee('Operational next slice')
            ->assertSee('Operational data source status')
            ->assertSee('Operational migration blockers')
            ->assertSee('legacy blocked and draft card behavior')
            ->assertSee('Card records joined with holders, card types, and shops')
            ->assertSee('query-backed inventory table')
            ->assertSee('Retain clear visibility for unassigned, active, and blocked card states.')
            ->assertSee('Card type')
            ->assertSee('Activation period');
    }

    public function test_authenticated_user_can_access_cardholders_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders');

        $response
            ->assertOk()
            ->assertSee('Cardholders placeholder')
            ->assertSee('Operational index shape')
            ->assertSee('Anna Petrova')
            ->assertSee('Has cards')
            ->assertSee('New cardholder')
            ->assertSee('Review recent activity')
            ->assertSee('Active holders')
            ->assertSee('Inactive holders')
            ->assertSee('Linked cards')
            ->assertSee('Cardholder operations are still preview-only')
            ->assertSee('Search actions, metrics, and lifecycle cues are shaping the target Galaxy flow, but they are not backed by Laravel reads or writes yet.')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview holder search surface and activity cues are defined')
            ->assertSee('Search, profile reads, and activity history still need PHP-backed Laravel wiring')
            ->assertSee('Implementation dependencies')
            ->assertSee('CardHolder model and shop linkage baseline exist')
            ->assertSee('Searchable index, profile read path, and activity sourcing are still pending')
            ->assertSee('First Laravel wiring step')
            ->assertSee('When PHP is available, start with a searchable cardholder index before attempting profile edits or lifecycle actions.')
            ->assertSee('Load cardholders with shop, status, and linked-card counts.')
            ->assertSee('Recent activity preview')
            ->assertSee('Anna Petrova activity pattern reviewed')
            ->assertSee('North Shop inactive holder retained')
            ->assertSee('Legacy parity mapping')
            ->assertSee('Old Galaxy holder and client lookup screen')
            ->assertSee('Fast search, linked cards, recent activity visibility')
            ->assertSee('Operator checklist')
            ->assertSee('Search inactive holders before creating duplicate profiles.')
            ->assertSee('Escalation guide')
            ->assertSee('Escalate duplicate-profile suspicions before creating a replacement holder record.')
            ->assertSee('Shift handoff notes')
            ->assertSee('Carry duplicate-profile investigations into the next shift until identity is confirmed.')
            ->assertSee('Open issues to carry')
            ->assertSee('Potential duplicate-holder cases stay open until identity is confirmed.')
            ->assertSee('Operational glossary')
            ->assertSee('Legacy parity notes')
            ->assertSee('Operational next slice')
            ->assertSee('Operational data source status')
            ->assertSee('Operational migration blockers')
            ->assertSee('Recent activity still needs a stable event source')
            ->assertSee('CardHolder records with shop linkage and recent activity data')
            ->assertSee('searchable cardholder index')
            ->assertSee('Preserve the operational emphasis on recent activity and card linkage.')
            ->assertSee('Last activity')
            ->assertSee('Central Shop');
    }

    public function test_authenticated_user_can_access_shops_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Shops placeholder')
            ->assertSee('Operational index shape')
            ->assertSee('Central Shop')
            ->assertSee('Volume tier')
            ->assertSee('New shop')
            ->assertSee('Review branch scope')
            ->assertSee('Active shops')
            ->assertSee('Paused shops')
            ->assertSee('Assigned managers')
            ->assertSee('Shop operations are still preview-only')
            ->assertSee('Branch actions, metrics, and filters are shaping the final Galaxy workspace, but they are not wired to Laravel queries or handlers yet.')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview shop rows and branch actions defined')
            ->assertSee('Real shop queries and branch mutations still need PHP-backed Laravel wiring')
            ->assertSee('Implementation dependencies')
            ->assertSee('Shop model and user-to-shop linkage baseline exist')
            ->assertSee('Query-backed shop index and branch actions are still pending')
            ->assertSee('First Laravel wiring step')
            ->assertSee('When PHP is available, start with a minimal read-only shop index before adding any branch mutation flows.')
            ->assertSee('Wire an Eloquent-backed shops index with manager and status columns.')
            ->assertSee('Recent activity preview')
            ->assertSee('Central Shop scope reviewed')
            ->assertSee('Airport Kiosk kept paused')
            ->assertSee('Legacy parity mapping')
            ->assertSee('Old Galaxy branch administration screen')
            ->assertSee('Branch scope, manager assignment, active versus paused visibility')
            ->assertSee('Operator checklist')
            ->assertSee('Review paused branches before shift handoff.')
            ->assertSee('Escalation guide')
            ->assertSee('Route manager-assignment gaps to operations supervision first.')
            ->assertSee('Shift handoff notes')
            ->assertSee('Carry paused-branch context into the next shift until recovery is approved.')
            ->assertSee('Open issues to carry')
            ->assertSee('Airport Kiosk remains paused pending recovery approval.')
            ->assertSee('Operational glossary')
            ->assertSee('Legacy parity notes')
            ->assertSee('Operational next slice')
            ->assertSee('Operational data source status')
            ->assertSee('Operational migration blockers')
            ->assertSee('old Galaxy branch model')
            ->assertSee('Shop records and manager relations from Eloquent')
            ->assertSee('minimal query-backed index')
            ->assertSee('branch ownership model')
            ->assertSee('Manager assigned')
            ->assertSee('Airport Kiosk');
    }

    public function test_authenticated_user_can_access_checks_points_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/checks-points');

        $response
            ->assertOk()
            ->assertSee('Checks &amp; Points placeholder')
            ->assertSee('CHK-90421')
            ->assertSee('Fiscal receipt')
            ->assertSee('Find receipt')
            ->assertSee('Review accrual gaps')
            ->assertSee('Receipts listed')
            ->assertSee('Positive accruals')
            ->assertSee('Zero accruals')
            ->assertSee('Checks and points operations are still preview-only')
            ->assertSee('Receipt lookup actions, accrual metrics, and troubleshooting cues are shaping the final Galaxy flow, but real Laravel transaction reads do not exist yet.')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview receipt lookup surface and accrual metrics are defined')
            ->assertSee('Transaction tables, receipt reads, and adjustment flows still need PHP-backed Laravel wiring')
            ->assertSee('Implementation dependencies')
            ->assertSee('Transaction domain tables do not exist yet')
            ->assertSee('Receipt history queries and adjustment handlers are still pending')
            ->assertSee('First Laravel wiring step')
            ->assertSee('When PHP is available, start with a read-only receipt history before attempting manual accrual adjustments.')
            ->assertSee('Introduce transaction tables or a read model for receipt and points history.')
            ->assertSee('Recent activity preview')
            ->assertSee('Receipt-first lookup preserved')
            ->assertSee('Zero-accrual case retained')
            ->assertSee('Legacy parity mapping')
            ->assertSee('Old Galaxy checks and points history screen')
            ->assertSee('Receipt-first lookup, points delta visibility, troubleshooting flow')
            ->assertSee('Operator checklist')
            ->assertSee('Search by fiscal receipt before checking card history.')
            ->assertSee('Escalation guide')
            ->assertSee('Escalate receipt-not-found cases before discussing manual point recovery.')
            ->assertSee('Shift handoff notes')
            ->assertSee('Carry unresolved receipt-not-found cases with the last verified search inputs.')
            ->assertSee('Open issues to carry')
            ->assertSee('Receipt-not-found cases remain open until fiscal search inputs are exhausted.')
            ->assertSee('Operational glossary')
            ->assertSee('Legacy parity notes')
            ->assertSee('Operational next slice')
            ->assertSee('Operational data source status')
            ->assertSee('Operational migration blockers')
            ->assertSee('Transaction domain tables do not exist yet')
            ->assertSee('Receipt and accrual event records from the future transaction domain')
            ->assertSee('read-only receipt history')
            ->assertSee('receipt-first lookup')
            ->assertSee('The loyalty delta applied after receipt validation')
            ->assertSee('GX-100001');
    }

    public function test_authenticated_user_can_access_reports_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports');

        $response
            ->assertOk()
            ->assertSee('Reports placeholder')
            ->assertSee('Points accrual summary')
            ->assertSee('Report type')
            ->assertSee('Open report catalog')
            ->assertSee('Review export presets')
            ->assertSee('Planned reports')
            ->assertSee('Export formats')
            ->assertSee('Preset periods')
            ->assertSee('Reporting operations are still preview-only')
            ->assertSee('Catalog actions, summary metrics, and export cues are outlining the Galaxy reporting workspace, but no Laravel reporting pipeline is wired yet.')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview report catalog actions and preset metrics are defined')
            ->assertSee('Real report sources, presets, and exports still need PHP-backed Laravel wiring')
            ->assertSee('Implementation dependencies')
            ->assertSee('Report catalog is still config-backed with no reporting domain service yet')
            ->assertSee('Preset handling, query sources, and export pipeline are still pending')
            ->assertSee('First Laravel wiring step')
            ->assertSee('When PHP is available, start with a simple report catalog and fixed presets before building exports or analytics pipelines.')
            ->assertSee('Move report definitions into a lightweight service or queryable catalog source.')
            ->assertSee('Recent activity preview')
            ->assertSee('Points accrual summary kept first')
            ->assertSee('Gift redemption report retained')
            ->assertSee('Legacy parity mapping')
            ->assertSee('Old Galaxy reporting catalog')
            ->assertSee('Preset periods, export-first habits, report ordering')
            ->assertSee('Operator checklist')
            ->assertSee('Start with preset periods before opening custom date-range work.')
            ->assertSee('Escalation guide')
            ->assertSee('Escalate missing preset coverage before promising custom analytics.')
            ->assertSee('Shift handoff notes')
            ->assertSee('Carry unmet preset requests into the next shift before inventing custom exports.')
            ->assertSee('Open issues to carry')
            ->assertSee('Missing preset coverage remains open until the reporting owner confirms a supported path.')
            ->assertSee('Operational glossary')
            ->assertSee('Legacy parity notes')
            ->assertSee('Operational next slice')
            ->assertSee('Operational data source status')
            ->assertSee('Operational migration blockers')
            ->assertSee('reports remain catalog-only previews')
            ->assertSee('Report catalog definitions plus later query-backed metrics sources')
            ->assertSee('simple catalog of report entry points')
            ->assertSee('export-first reporting habits')
            ->assertSee('Default period')
            ->assertSee('Gift redemption report');
    }

    public function test_authenticated_user_can_access_services_rules_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules');

        $response
            ->assertOk()
            ->assertSee('Services &amp; Rules placeholder')
            ->assertSee('Birthday bonus')
            ->assertSee('Rule type')
            ->assertSee('Partner card uplift');
    }

    public function test_resource_page_shell_keeps_shared_header_and_rationale_visible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Shops placeholder')
            ->assertSee('Section key')
            ->assertSee('shops')
            ->assertSee('Phase')
            ->assertSee('1')
            ->assertSee('Next step')
            ->assertSee('Replace sample rows with real shop records, manager info, and scoped access actions.')
            ->assertSee('Why this page exists now')
            ->assertSee('connect the admin navigation to real Galaxy sections instead of dead placeholders;')
            ->assertSee('reserve stable route names for future CRUD and reporting flows;')
            ->assertSee('make the Phase 1 shell visibly closer to the old operational product shape.');
    }

    public function test_resource_page_shell_uses_config_driven_defaults(): void
    {
        $user = User::factory()->create();

        Config::set('admin-resource-page-defaults.phase', 2);
        Config::set('admin-resource-page-defaults.pageRationale', [
            'config-driven shell defaults stay overrideable in tests and future rollout steps.',
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Phase')
            ->assertSee('2')
            ->assertSee('Why this page exists now')
            ->assertSee('config-driven shell defaults stay overrideable in tests and future rollout steps.');
    }

    public function test_resource_page_shell_uses_config_driven_block_order(): void
    {
        $user = User::factory()->create();

        Config::set('admin-resource-page-defaults.resourceBlocks', [
            ['key' => 'legacyParityNotes', 'partial' => 'admin.partials.resource-legacy-parity-notes', 'prop' => 'legacyParityNotes'],
            ['key' => 'operationalGlossary', 'partial' => 'admin.partials.resource-operational-glossary', 'prop' => 'operationalGlossary'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');
        $content = $response->getContent();

        $this->assertTrue(
            strpos($content, 'Legacy parity notes') < strpos($content, 'Operational glossary'),
            'Expected config-driven resource block order to be reflected in rendered output.'
        );
    }

    public function test_resource_page_shell_can_reorder_operational_workflow_blocks(): void
    {
        $user = User::factory()->create();

        Config::set('admin-resource-page-defaults.resourceBlocks', [
            ['key' => 'openIssues', 'partial' => 'admin.partials.resource-open-issues', 'prop' => 'openIssues'],
            ['key' => 'shiftHandoff', 'partial' => 'admin.partials.resource-shift-handoff', 'prop' => 'shiftHandoff'],
            ['key' => 'escalationGuide', 'partial' => 'admin.partials.resource-escalation-guide', 'prop' => 'escalationGuide'],
            ['key' => 'operatorChecklist', 'partial' => 'admin.partials.resource-operator-checklist', 'prop' => 'operatorChecklist'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');
        $content = $response->getContent();

        $this->assertTrue(
            strpos($content, 'Open issues to carry')
                < strpos($content, 'Shift handoff notes')
                && strpos($content, 'Shift handoff notes') < strpos($content, 'Escalation guide')
                && strpos($content, 'Escalation guide') < strpos($content, 'Operator checklist'),
            'Expected operational workflow blocks to stay reorderable through config-driven composition.'
        );
    }

    public function test_resource_page_shell_can_use_dedicated_operational_workflow_stack_config(): void
    {
        $user = User::factory()->create();

        Config::set('admin-operational-workflow-blocks', [
            ['key' => 'shiftHandoff', 'partial' => 'admin.partials.resource-shift-handoff', 'prop' => 'shiftHandoff'],
            ['key' => 'openIssues', 'partial' => 'admin.partials.resource-open-issues', 'prop' => 'openIssues'],
        ]);
        Config::set('admin-resource-page-defaults.resourceBlocks', array_merge([
            ['key' => 'legacyMapping', 'partial' => 'admin.partials.resource-legacy-mapping', 'prop' => 'legacyMapping'],
        ], config('admin-operational-workflow-blocks'), [
            ['key' => 'implementationHandoff', 'partial' => 'admin.partials.resource-implementation-handoff', 'prop' => 'implementationHandoff'],
        ]));

        $response = $this->actingAs($user)->get('/admin/shops');
        $content = $response->getContent();

        $this->assertTrue(
            strpos($content, 'Legacy parity mapping')
                < strpos($content, 'Shift handoff notes')
                && strpos($content, 'Shift handoff notes') < strpos($content, 'Open issues to carry')
                && strpos($content, 'Open issues to carry') < strpos($content, 'First Laravel wiring step'),
            'Expected the dedicated operational workflow config stack to remain composable inside page defaults.'
        );
    }

    public function test_resource_page_shell_can_use_dedicated_operational_context_stack_config(): void
    {
        $user = User::factory()->create();

        Config::set('admin-operational-context-blocks', [
            ['key' => 'activityTimeline', 'partial' => 'admin.partials.resource-activity-timeline', 'prop' => 'activityTimeline'],
            ['key' => 'legacyMapping', 'partial' => 'admin.partials.resource-legacy-mapping', 'prop' => 'legacyMapping'],
        ]);
        Config::set('admin-resource-page-defaults.resourceBlocks', array_merge([
            ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
        ], config('admin-operational-context-blocks'), [
            ['key' => 'implementationHandoff', 'partial' => 'admin.partials.resource-implementation-handoff', 'prop' => 'implementationHandoff'],
        ]));

        $response = $this->actingAs($user)->get('/admin/shops');
        $content = $response->getContent();

        $this->assertTrue(
            strpos($content, 'Recent activity preview')
                < strpos($content, 'Legacy parity mapping')
                && strpos($content, 'Legacy parity mapping') < strpos($content, 'First Laravel wiring step'),
            'Expected the dedicated operational context config stack to remain composable inside page defaults.'
        );
    }

    public function test_resource_page_shell_can_use_dedicated_operational_closing_stack_config(): void
    {
        $user = User::factory()->create();

        Config::set('admin-operational-closing-blocks', [
            ['key' => 'dependencyStatus', 'partial' => 'admin.partials.resource-dependency-status', 'prop' => 'dependencyStatus'],
            ['key' => 'implementationHandoff', 'partial' => 'admin.partials.resource-implementation-handoff', 'prop' => 'implementationHandoff'],
        ]);
        Config::set('admin-resource-page-defaults.resourceBlocks', array_merge([
            ['key' => 'legacyMapping', 'partial' => 'admin.partials.resource-legacy-mapping', 'prop' => 'legacyMapping'],
        ], config('admin-operational-closing-blocks')));

        $response = $this->actingAs($user)->get('/admin/shops');
        $content = $response->getContent();

        $this->assertTrue(
            strpos($content, 'Implementation dependencies')
                < strpos($content, 'First Laravel wiring step'),
            'Expected the dedicated operational closing config stack to remain composable inside page defaults.'
        );
    }

    public function test_resource_page_shell_can_use_dedicated_preview_shell_stack_config(): void
    {
        $user = User::factory()->create();

        Config::set('admin-preview-shell-blocks', [
            ['key' => 'notice', 'partial' => 'admin.partials.resource-preview-notice', 'prop' => 'notice'],
            ['key' => 'emptyState', 'partial' => 'admin.partials.resource-empty-state', 'prop' => 'emptyState'],
        ]);
        Config::set('admin-resource-page-defaults.resourceBlocks', array_merge([
            ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
        ], config('admin-preview-shell-blocks'), [
            ['key' => 'implementationHandoff', 'partial' => 'admin.partials.resource-implementation-handoff', 'prop' => 'implementationHandoff'],
        ]));

        $response = $this->actingAs($user)->get('/admin/card-types');
        $content = $response->getContent();

        $this->assertTrue(
            strpos($content, 'Card type rules are still preview-only')
                < strpos($content, 'No custom card types configured yet')
                && strpos($content, 'No custom card types configured yet') < strpos($content, 'First Laravel wiring step'),
            'Expected the dedicated preview shell config stack to remain composable inside page defaults.'
        );
    }

    public function test_resource_page_shell_can_use_dedicated_base_shell_stack_config(): void
    {
        $user = User::factory()->create();

        Config::set('admin-base-shell-blocks', [
            ['key' => 'table', 'partial' => 'admin.partials.operational-index-table', 'prop' => 'table'],
            ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
        ]);
        Config::set('admin-resource-page-defaults.resourceBlocks', array_merge(
            config('admin-base-shell-blocks'),
            [
                ['key' => 'implementationHandoff', 'partial' => 'admin.partials.resource-implementation-handoff', 'prop' => 'implementationHandoff'],
            ]
        ));

        $response = $this->actingAs($user)->get('/admin/shops');
        $content = $response->getContent();

        $this->assertTrue(
            strpos($content, 'Central Shop') < strpos($content, 'Active shops')
                && strpos($content, 'Active shops') < strpos($content, 'First Laravel wiring step'),
            'Expected the dedicated base shell config stack to remain composable inside page defaults.'
        );
    }

    public function test_resource_page_shell_can_compose_all_five_shell_layers_together(): void
    {
        $user = User::factory()->create();

        Config::set('admin-base-shell-blocks', [
            ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
        ]);
        Config::set('admin-operational-context-blocks', [
            ['key' => 'activityTimeline', 'partial' => 'admin.partials.resource-activity-timeline', 'prop' => 'activityTimeline'],
        ]);
        Config::set('admin-preview-shell-blocks', [
            ['key' => 'notice', 'partial' => 'admin.partials.resource-preview-notice', 'prop' => 'notice'],
        ]);
        Config::set('admin-operational-workflow-blocks', [
            ['key' => 'openIssues', 'partial' => 'admin.partials.resource-open-issues', 'prop' => 'openIssues'],
        ]);
        Config::set('admin-operational-closing-blocks', [
            ['key' => 'implementationHandoff', 'partial' => 'admin.partials.resource-implementation-handoff', 'prop' => 'implementationHandoff'],
        ]);
        Config::set('admin-resource-page-defaults.resourceBlocks', array_merge(
            config('admin-base-shell-blocks'),
            config('admin-operational-context-blocks'),
            config('admin-preview-shell-blocks'),
            config('admin-operational-workflow-blocks'),
            config('admin-operational-closing-blocks'),
        ));

        $response = $this->actingAs($user)->get('/admin/shops');
        $content = $response->getContent();

        $this->assertTrue(
            strpos($content, 'Active shops')
                < strpos($content, 'Recent activity preview')
                && strpos($content, 'Recent activity preview') < strpos($content, 'Shop operations are still preview-only')
                && strpos($content, 'Shop operations are still preview-only') < strpos($content, 'Open issues to carry')
                && strpos($content, 'Open issues to carry') < strpos($content, 'First Laravel wiring step'),
            'Expected base, context, preview, workflow, and closing shell layers to compose in sequence.'
        );
    }

    public function test_resource_page_defaults_can_bridge_all_five_shell_layers_via_admin_resource_blocks(): void
    {
        $user = User::factory()->create();

        Config::set('admin-base-shell-blocks', [
            ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
        ]);
        Config::set('admin-operational-context-blocks', [
            ['key' => 'activityTimeline', 'partial' => 'admin.partials.resource-activity-timeline', 'prop' => 'activityTimeline'],
        ]);
        Config::set('admin-preview-shell-blocks', [
            ['key' => 'notice', 'partial' => 'admin.partials.resource-preview-notice', 'prop' => 'notice'],
        ]);
        Config::set('admin-operational-workflow-blocks', [
            ['key' => 'openIssues', 'partial' => 'admin.partials.resource-open-issues', 'prop' => 'openIssues'],
        ]);
        Config::set('admin-operational-closing-blocks', [
            ['key' => 'implementationHandoff', 'partial' => 'admin.partials.resource-implementation-handoff', 'prop' => 'implementationHandoff'],
        ]);
        Config::set('admin-resource-blocks', array_merge(
            config('admin-base-shell-blocks'),
            config('admin-operational-context-blocks'),
            config('admin-preview-shell-blocks'),
            config('admin-operational-workflow-blocks'),
            config('admin-operational-closing-blocks'),
        ));
        Config::set('admin-resource-page-defaults.resourceBlocks', config('admin-resource-blocks'));

        $response = $this->actingAs($user)->get('/admin/shops');
        $content = $response->getContent();

        $this->assertTrue(
            strpos($content, 'Active shops')
                < strpos($content, 'Recent activity preview')
                && strpos($content, 'Recent activity preview') < strpos($content, 'Shop operations are still preview-only')
                && strpos($content, 'Shop operations are still preview-only') < strpos($content, 'Open issues to carry')
                && strpos($content, 'Open issues to carry') < strpos($content, 'First Laravel wiring step'),
            'Expected admin-resource-blocks to bridge all five shell layers into resource page defaults.'
        );
    }

    public function test_resource_page_defaults_array_can_bridge_all_five_shell_layers_via_admin_resource_blocks(): void
    {
        $user = User::factory()->create();

        Config::set('admin-base-shell-blocks', [
            ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
        ]);
        Config::set('admin-operational-context-blocks', [
            ['key' => 'activityTimeline', 'partial' => 'admin.partials.resource-activity-timeline', 'prop' => 'activityTimeline'],
        ]);
        Config::set('admin-preview-shell-blocks', [
            ['key' => 'notice', 'partial' => 'admin.partials.resource-preview-notice', 'prop' => 'notice'],
        ]);
        Config::set('admin-operational-workflow-blocks', [
            ['key' => 'openIssues', 'partial' => 'admin.partials.resource-open-issues', 'prop' => 'openIssues'],
        ]);
        Config::set('admin-operational-closing-blocks', [
            ['key' => 'implementationHandoff', 'partial' => 'admin.partials.resource-implementation-handoff', 'prop' => 'implementationHandoff'],
        ]);
        Config::set('admin-resource-blocks', array_merge(
            config('admin-base-shell-blocks'),
            config('admin-operational-context-blocks'),
            config('admin-preview-shell-blocks'),
            config('admin-operational-workflow-blocks'),
            config('admin-operational-closing-blocks'),
        ));
        Config::set('admin-resource-page-defaults', [
            'phase' => 1,
            'resourceBlocks' => config('admin-resource-blocks'),
            'pageRationale' => config('admin-page-rationale', []),
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');
        $content = $response->getContent();

        $this->assertTrue(
            strpos($content, 'Active shops')
                < strpos($content, 'Recent activity preview')
                && strpos($content, 'Recent activity preview') < strpos($content, 'Shop operations are still preview-only')
                && strpos($content, 'Shop operations are still preview-only') < strpos($content, 'Open issues to carry')
                && strpos($content, 'Open issues to carry') < strpos($content, 'First Laravel wiring step'),
            'Expected the full admin-resource-page-defaults array to bridge layered shell composition through admin-resource-blocks.'
        );
    }

    public function test_resource_page_defaults_helpers_fall_back_when_shell_defaults_are_not_arrays(): void
    {
        $user = User::factory()->create();

        Config::set('admin-resource-page-defaults', [
            'phase' => 1,
            'resourceBlocks' => 'invalid-block-list',
            'pageRationale' => 'invalid-rationale',
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Shop operations')
            ->assertDontSee('Recent activity preview')
            ->assertDontSee('Phase 1 keeps these Galaxy resource pages config-driven')
            ->assertDontSee('Roll out shop status updates');
    }

    public function test_resource_page_defaults_root_falls_back_when_shell_defaults_config_is_not_an_array(): void
    {
        $user = User::factory()->create();

        Config::set('admin-resource-page-defaults', 'invalid-defaults-root');

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Shop operations')
            ->assertSee('Phase 1')
            ->assertDontSee('Recent activity preview')
            ->assertDontSee('Phase 1 keeps these Galaxy resource pages config-driven');
    }

    public function test_resource_page_defaults_helper_falls_back_to_phase_one_when_phase_is_not_an_int(): void
    {
        $user = User::factory()->create();

        Config::set('admin-resource-page-defaults', [
            'phase' => 'invalid-phase',
            'resourceBlocks' => [],
            'pageRationale' => [],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Phase 1')
            ->assertDontSee('Phase invalid-phase');
    }

    public function test_resource_page_defaults_helpers_ignore_malformed_block_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-resource-page-defaults', [
            'phase' => 1,
            'resourceBlocks' => [
                ['key' => 'legacyParityNotes', 'partial' => 'admin.partials.resource-legacy-parity-notes', 'prop' => 'legacyParityNotes'],
                'invalid-block-entry',
                ['key' => 'previewNotice', 'partial' => 'admin.partials.resource-preview-notice'],
                ['key' => 'operationalGlossary', 'partial' => 'admin.partials.resource-operational-glossary', 'prop' => 'operationalGlossary'],
            ],
            'pageRationale' => [],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');
        $content = $response->getContent();

        $response
            ->assertOk()
            ->assertSee('Legacy parity notes')
            ->assertSee('Operational glossary')
            ->assertDontSee('Preview-only');

        $this->assertTrue(
            strpos($content, 'Legacy parity notes') < strpos($content, 'Operational glossary'),
            'Expected malformed resource block entries to be ignored while valid block order still renders.'
        );
    }

    public function test_resource_page_defaults_helpers_ignore_malformed_page_rationale_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-resource-page-defaults', [
            'phase' => 1,
            'resourceBlocks' => [],
            'pageRationale' => [
                'Keep parity-first rollout visible to operators.',
                ['invalid-rationale-entry'],
                42,
                'Leave real Laravel forms for the first safe backend slice.',
            ],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Why this page exists now')
            ->assertSee('Keep parity-first rollout visible to operators.')
            ->assertSee('Leave real Laravel forms for the first safe backend slice.')
            ->assertDontSee('invalid-rationale-entry')
            ->assertDontSee('42');
    }

    public function test_resource_page_header_actions_ignore_malformed_action_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.actions', [
            ['label' => 'New shop', 'tone' => 'primary'],
            'invalid-action-entry',
            ['tone' => 'secondary'],
            ['label' => 'Review branch scope', 'tone' => ['invalid-tone']],
            ['label' => 'Review branch scope', 'tone' => 'secondary'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('New shop')
            ->assertSee('Review branch scope')
            ->assertDontSee('invalid-action-entry')
            ->assertDontSee('Array');
    }

    public function test_resource_summary_metrics_ignore_malformed_metric_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.metrics', [
            ['label' => 'Active shops', 'value' => '2'],
            'invalid-metric-entry',
            ['label' => 'Paused shops'],
            ['label' => 'Assigned managers', 'value' => 2],
            ['label' => 'Paused shops', 'value' => '1'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Active shops')
            ->assertSee('Paused shops')
            ->assertDontSee('invalid-metric-entry')
            ->assertDontSee('Assigned managers')
            ->assertDontSee('Array');
    }

    public function test_operational_table_ignores_malformed_table_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.table', [
            'columns' => ['Shop', ['invalid-column'], 'Status'],
            'rows' => [
                ['Central Shop', 'active'],
                'invalid-row-entry',
                ['North Shop', 12],
                ['Airport Kiosk', 'paused'],
            ],
            'filters' => ['Status', ['invalid-filter'], 'Volume tier'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Shop')
            ->assertSee('Status')
            ->assertSee('Central Shop')
            ->assertSee('Airport Kiosk')
            ->assertSee('Volume tier')
            ->assertDontSee('invalid-column')
            ->assertDontSee('invalid-row-entry')
            ->assertDontSee('North Shop')
            ->assertDontSee('12')
            ->assertDontSee('invalid-filter')
            ->assertDontSee('Array');
    }

    public function test_preview_notice_ignores_malformed_notice_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.notice', [
            'title' => ['invalid-title'],
            'description' => 'Branch actions, metrics, and filters are shaping the final Galaxy workspace, but they are not wired to Laravel queries or handlers yet.',
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertDontSee('Preview notice')
            ->assertDontSee('Branch actions, metrics, and filters are shaping the final Galaxy workspace, but they are not wired to Laravel queries or handlers yet.')
            ->assertDontSee('Array');
    }

    public function test_readiness_checklist_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.readinessChecklist', [
            ['status' => 'ready', 'label' => 'Preview shop rows and branch actions defined'],
            'invalid-readiness-entry',
            ['label' => 'Missing status'],
            ['status' => 'pending', 'label' => 42],
            ['status' => 'pending', 'label' => 'Real shop queries and branch mutations still need PHP-backed Laravel wiring'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview shop rows and branch actions defined')
            ->assertSee('Real shop queries and branch mutations still need PHP-backed Laravel wiring')
            ->assertDontSee('invalid-readiness-entry')
            ->assertDontSee('Missing status')
            ->assertDontSee('42')
            ->assertDontSee('Array');
    }

    public function test_activity_timeline_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.activityTimeline', [
            ['title' => 'Central Shop scope reviewed', 'time' => 'Today, 17:40', 'description' => 'Branch ownership and manager visibility were checked against the old Galaxy operating model.'],
            'invalid-timeline-entry',
            ['title' => 'Airport Kiosk kept paused', 'time' => ['invalid-time'], 'description' => 'The preview state preserves a paused branch case for parity before real status flows exist.'],
            ['title' => 'North Shop reopened', 'time' => 'Yesterday, 09:15'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Recent activity preview')
            ->assertSee('Central Shop scope reviewed')
            ->assertSee('Today, 17:40')
            ->assertDontSee('invalid-timeline-entry')
            ->assertDontSee('Airport Kiosk kept paused')
            ->assertDontSee('North Shop reopened')
            ->assertDontSee('Array');
    }

    public function test_dependency_status_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.dependencyStatus', [
            ['label' => 'Domain model', 'value' => 'Shop model and user-to-shop linkage baseline exist'],
            'invalid-dependency-entry',
            ['label' => 'Backend dependency'],
            ['label' => 'Operational dependency', 'value' => 42],
            ['label' => 'Backend dependency', 'value' => 'Query-backed shop index and branch actions are still pending'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Implementation dependencies')
            ->assertSee('Shop model and user-to-shop linkage baseline exist')
            ->assertSee('Query-backed shop index and branch actions are still pending')
            ->assertDontSee('invalid-dependency-entry')
            ->assertDontSee('Operational dependency')
            ->assertDontSee('42')
            ->assertDontSee('Array');
    }

    public function test_legacy_mapping_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.legacyMapping', [
            ['label' => 'Legacy source', 'value' => 'Old Galaxy branch administration screen'],
            'invalid-legacy-entry',
            ['label' => 'Parity focus'],
            ['label' => 'Migration note', 'value' => 42],
            ['label' => 'Parity focus', 'value' => 'Branch scope, manager assignment, active versus paused visibility'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Legacy parity mapping')
            ->assertSee('Old Galaxy branch administration screen')
            ->assertSee('Branch scope, manager assignment, active versus paused visibility')
            ->assertDontSee('invalid-legacy-entry')
            ->assertDontSee('Migration note')
            ->assertDontSee('42')
            ->assertDontSee('Array');
    }

    public function test_implementation_handoff_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.implementationHandoff', [
            'summary' => 'When PHP is available, start with a minimal read-only shop index before adding any branch mutation flows.',
            'steps' => [
                'Wire an Eloquent-backed shops index with manager and status columns.',
                ['invalid-step'],
                42,
                'Delay create and edit actions until the read path is stable against live data.',
            ],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('First Laravel wiring step')
            ->assertSee('When PHP is available, start with a minimal read-only shop index before adding any branch mutation flows.')
            ->assertSee('Wire an Eloquent-backed shops index with manager and status columns.')
            ->assertSee('Delay create and edit actions until the read path is stable against live data.')
            ->assertDontSee('invalid-step')
            ->assertDontSee('42')
            ->assertDontSee('Array');
    }

    public function test_operational_next_slice_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.operationalNextSlice', [
            'summary' => 'When backend execution is available, start by replacing static shop rows with a minimal query-backed index.',
            'steps' => [
                'Load shops with manager and status columns from Eloquent.',
                ['invalid-step'],
                42,
                'Add row actions only after the read path is stable.',
            ],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Operational next slice')
            ->assertSee('When backend execution is available, start by replacing static shop rows with a minimal query-backed index.')
            ->assertSee('Load shops with manager and status columns from Eloquent.')
            ->assertSee('Add row actions only after the read path is stable.')
            ->assertDontSee('invalid-step')
            ->assertDontSee('42')
            ->assertDontSee('Array');
    }

    public function test_operator_checklist_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.operatorChecklist', [
            'summary' => 'Daily branch oversight in the old Galaxy workspace was built around quick visual checks before anyone opened a detail screen.',
            'items' => [
                'Review paused branches before shift handoff.',
                ['invalid-item'],
                42,
                'Confirm each active shop still has an assigned manager.',
            ],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Operator checklist')
            ->assertSee('Daily branch oversight in the old Galaxy workspace was built around quick visual checks before anyone opened a detail screen.')
            ->assertSee('Review paused branches before shift handoff.')
            ->assertSee('Confirm each active shop still has an assigned manager.')
            ->assertDontSee('invalid-item')
            ->assertDontSee('42')
            ->assertDontSee('Array');
    }

    public function test_escalation_guide_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.escalationGuide', [
            'summary' => 'Branch issues in the legacy admin usually moved through a short escalation path instead of ad hoc edits.',
            'items' => [
                'Route manager-assignment gaps to operations supervision first.',
                ['invalid-item'],
                42,
                'Treat paused-branch recovery as an approval step, not a same-screen quick fix.',
            ],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Escalation guide')
            ->assertSee('Branch issues in the legacy admin usually moved through a short escalation path instead of ad hoc edits.')
            ->assertSee('Route manager-assignment gaps to operations supervision first.')
            ->assertSee('Treat paused-branch recovery as an approval step, not a same-screen quick fix.')
            ->assertDontSee('invalid-item')
            ->assertDontSee('42')
            ->assertDontSee('Array');
    }

    public function test_shift_handoff_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.shiftHandoff', [
            'summary' => 'Shop oversight in the old Galaxy console depended on explicit handoff notes so the next operator could continue branch monitoring without rechecking everything.',
            'items' => [
                'Carry paused-branch context into the next shift until recovery is approved.',
                ['invalid-item'],
                42,
                'Flag any shop that still lacks a manager assignment at handoff time.',
            ],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Shift handoff notes')
            ->assertSee('Shop oversight in the old Galaxy console depended on explicit handoff notes so the next operator could continue branch monitoring without rechecking everything.')
            ->assertSee('Carry paused-branch context into the next shift until recovery is approved.')
            ->assertSee('Flag any shop that still lacks a manager assignment at handoff time.')
            ->assertDontSee('invalid-item')
            ->assertDontSee('42')
            ->assertDontSee('Array');
    }

    public function test_open_issues_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.openIssues', [
            'summary' => 'The old Galaxy branch screen usually kept a short list of unresolved branch items mentally attached to the shift.',
            'items' => [
                'Airport Kiosk remains paused pending recovery approval.',
                ['invalid-item'],
                42,
                'Cross-shop visibility disagreements must remain open until scope is verified.',
            ],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Open issues to carry')
            ->assertSee('The old Galaxy branch screen usually kept a short list of unresolved branch items mentally attached to the shift.')
            ->assertSee('Airport Kiosk remains paused pending recovery approval.')
            ->assertSee('Cross-shop visibility disagreements must remain open until scope is verified.')
            ->assertDontSee('invalid-item')
            ->assertDontSee('42')
            ->assertDontSee('Array');
    }

    public function test_empty_state_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.roles-permissions.emptyState', [
            'title' => 'No shop-scoped roles configured yet',
            'description' => 'Seed the first role profile from the old Galaxy cashier and manager matrix before wiring persistence.',
            'actions' => [
                ['label' => 'Create first role', 'tone' => 'primary'],
                ['tone' => 'secondary'],
                'invalid-action-entry',
                ['label' => 'Review matrix', 'tone' => ['invalid-tone']],
            ],
        ]);

        $response = $this->actingAs($user)->get('/admin/roles-permissions');

        $response
            ->assertOk()
            ->assertSee('No shop-scoped roles configured yet')
            ->assertSee('Seed the first role profile from the old Galaxy cashier and manager matrix before wiring persistence.')
            ->assertSee('Create first role')
            ->assertDontSee('invalid-action-entry')
            ->assertDontSee('Array');
    }

    public function test_form_preview_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.roles-permissions.form', [
            'title' => 'Create or edit role',
            'actions' => [
                ['label' => 'Publish role', 'tone' => 'primary'],
                ['tone' => 'secondary'],
                'invalid-action-entry',
            ],
            'sections' => [
                [
                    'title' => 'Role identity',
                    'help' => 'Keep legacy naming visible while the matrix is still preview-only.',
                    'actions' => [
                        ['label' => 'Compare staff roles', 'tone' => 'secondary'],
                        ['label' => ['invalid-label']],
                    ],
                    'fields' => [
                        ['label' => 'Role name', 'value' => 'Shop Manager'],
                        ['label' => 'Scope'],
                        'invalid-field-entry',
                    ],
                ],
                [
                    'title' => 'Access policy',
                    'fields' => [
                        ['label' => 'Visibility', 'value' => 'Scoped to assigned shop'],
                    ],
                ],
                'invalid-section-entry',
            ],
        ]);

        $response = $this->actingAs($user)->get('/admin/roles-permissions');

        $response
            ->assertOk()
            ->assertSee('Create or edit role')
            ->assertSee('Publish role')
            ->assertSee('Role identity')
            ->assertSee('Keep legacy naming visible while the matrix is still preview-only.')
            ->assertSee('Compare staff roles')
            ->assertSee('Role name')
            ->assertSee('Shop Manager')
            ->assertSee('Access policy')
            ->assertSee('Visibility')
            ->assertSee('Scoped to assigned shop')
            ->assertDontSee('invalid-action-entry')
            ->assertDontSee('invalid-field-entry')
            ->assertDontSee('invalid-section-entry')
            ->assertDontSee('Array');
    }

    public function test_summary_list_blocks_still_render_after_grouped_controller_lookup(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Operational next slice')
            ->assertSee('Operator checklist')
            ->assertSee('Escalation guide')
            ->assertSee('Shift handoff notes')
            ->assertSee('Open issues to carry')
            ->assertSee('First Laravel wiring step');
    }

    public function test_key_value_blocks_still_render_after_grouped_controller_lookup(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Implementation dependencies')
            ->assertSee('Legacy parity mapping')
            ->assertSee('Shop model and user-to-shop linkage baseline exist')
            ->assertSee('Old Galaxy branch administration screen');
    }

    public function test_preview_context_blocks_still_render_after_grouped_controller_lookup(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Preview notice')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Recent activity preview')
            ->assertSee('Shop operations are still preview-only')
            ->assertSee('Preview shop rows and branch actions defined')
            ->assertSee('Central Shop scope reviewed');
    }

    public function test_primary_page_blocks_still_render_after_grouped_controller_lookup(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions');

        $response
            ->assertOk()
            ->assertSee('Create or edit role')
            ->assertSee('Publish role')
            ->assertSee('No shop-scoped roles configured yet')
            ->assertSee('Create first role');
    }

    public function test_normalized_page_assembly_still_renders_mixed_block_types(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Operational index shape')
            ->assertSee('Preview notice')
            ->assertSee('Implementation dependencies')
            ->assertSee('Open issues to carry')
            ->assertSee('First Laravel wiring step');
    }

    public function test_resource_page_returns_not_found_when_page_definition_is_not_an_array(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops', 'invalid-page-definition');

        $response = $this->actingAs($user)->get('/admin/shops');

        $response->assertNotFound();
    }

    public function test_resource_page_still_renders_after_normalizer_is_extracted(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions');

        $response
            ->assertOk()
            ->assertSee('Create or edit role')
            ->assertSee('Publish role')
            ->assertSee('No shop-scoped roles configured yet')
            ->assertSee('Preview notice')
            ->assertSee('Migration readiness checklist');
    }

    public function test_resource_page_still_renders_with_injected_normalizer_dependency(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Shops placeholder')
            ->assertSee('Operational index shape')
            ->assertSee('Preview notice')
            ->assertSee('First Laravel wiring step');
    }

    public function test_authenticated_user_can_access_services_rules_management_preview(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules');

        $response
            ->assertOk()
            ->assertSee('Create or edit service rule')
            ->assertSee('Publish rule')
            ->assertSee('New rule')
            ->assertSee('Review priorities')
            ->assertSee('Management snapshot')
            ->assertSee('Active rules')
            ->assertSee('Shop scopes')
            ->assertSee('No service rules configured yet')
            ->assertSee('Create first rule')
            ->assertSee('Rule editing is still preview-only')
            ->assertSee('Rule identity')
            ->assertSee('Effect and priority')
            ->assertSee('Compare legacy rules')
            ->assertSee('Preview priority')
            ->assertSee('Old Galaxy services and business rules')
            ->assertSee('Recent activity preview')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Implementation dependencies')
            ->assertSee('Operator checklist')
            ->assertSee('Review priority collisions before drafting a replacement rule.')
            ->assertSee('Escalation guide')
            ->assertSee('Escalate overlapping priority conflicts before introducing a new accrual rule.')
            ->assertSee('Shift handoff notes')
            ->assertSee('Hand off unresolved priority conflicts with the compared legacy rule names.')
            ->assertSee('Open issues to carry')
            ->assertSee('Night service block parity is still under review against legacy exclusions.')
            ->assertSee('First Laravel wiring step')
            ->assertSee('When backend work starts, introduce the smallest rule persistence path before attempting full condition-builder parity.')
            ->assertSee('Keep advanced condition syntax out of the first implementation slice.')
            ->assertSee('Service/rule domain is still preview-config only')
            ->assertSee('Rule CRUD endpoints and validation are still pending')
            ->assertSee('Legacy rule groups identified')
            ->assertSee('Rule persistence still blocked until Laravel handlers can run')
            ->assertSee('Birthday bonus rule validated')
            ->assertSee('Night service block left in draft')
            ->assertSee('loyalty effect logic');
    }

    public function test_authenticated_user_can_access_gifts_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts');

        $response
            ->assertOk()
            ->assertSee('Gifts placeholder')
            ->assertSee('Coffee voucher')
            ->assertSee('Points range')
            ->assertSee('Premium dessert set');
    }

    public function test_authenticated_user_can_access_gifts_management_preview(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts');

        $response
            ->assertOk()
            ->assertSee('Create or edit gift')
            ->assertSee('Publish gift')
            ->assertSee('New gift')
            ->assertSee('Stock audit')
            ->assertSee('Management snapshot')
            ->assertSee('Active gifts')
            ->assertSee('Low stock items')
            ->assertSee('No gift campaigns configured yet')
            ->assertSee('Create first gift')
            ->assertSee('Gift redemption controls are still preview-only')
            ->assertSee('Catalog identity')
            ->assertSee('Availability')
            ->assertSee('Compare legacy catalog')
            ->assertSee('Preview stock impact')
            ->assertSee('old Galaxy gift list')
            ->assertSee('existing redemption process')
            ->assertSee('Legacy parity mapping')
            ->assertSee('Recent activity preview')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Implementation dependencies')
            ->assertSee('Operator checklist')
            ->assertSee('Review zero-stock rewards before reopening a paused gift.')
            ->assertSee('Escalation guide')
            ->assertSee('Escalate stock discrepancies before reactivating a paused reward.')
            ->assertSee('Shift handoff notes')
            ->assertSee('Hand off paused rewards with the latest stock assumption and shop scope.')
            ->assertSee('Open issues to carry')
            ->assertSee('Premium dessert set remains paused until zero-stock parity is confirmed.')
            ->assertSee('First Laravel wiring step')
            ->assertSee('When PHP is available, begin with basic gift CRUD and defer stock synchronization until after the first write path works.')
            ->assertSee('Treat warehouse sync and redemption logs as a later follow-up slice.')
            ->assertSee('Gift domain is still represented through config-backed preview data')
            ->assertSee('CRUD handlers, stock updates, and redemption persistence are still pending')
            ->assertSee('Legacy reward catalog mapped')
            ->assertSee('Real redemption and stock sync need PHP-backed flows')
            ->assertSee('Coffee voucher stock policy checked')
            ->assertSee('Premium dessert set paused')
            ->assertSee('Old Galaxy gift and reward list')
            ->assertSee('stock-aware redemption')
            ->assertSee('Unlimited')
            ->assertSee('Coffee voucher');
    }

    public function test_authenticated_user_can_access_card_types_management_preview(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/card-types');

        $response
            ->assertOk()
            ->assertSee('Card Types placeholder')
            ->assertSee('Create card type in Laravel')
            ->assertSee('Create card type')
            ->assertSee('Status')
            ->assertSee('Active')
            ->assertSee('Draft')
            ->assertSee('Create or edit card type')
            ->assertSee('Publish type')
            ->assertSee('New type')
            ->assertSee('Import rules')
            ->assertSee('Management snapshot')
            ->assertSee('Active tiers')
            ->assertSee('Imported rules')
            ->assertSee('No custom card types configured yet')
            ->assertSee('Create first type')
            ->assertSee('Card type workflow is partially live')
            ->assertSee('Identity')
            ->assertSee('Accrual settings')
            ->assertSee('Check duplicates')
            ->assertSee('Preview accrual')
            ->assertSee('old Galaxy card catalog')
            ->assertSee('old operational rules')
            ->assertSee('Legacy parity mapping')
            ->assertSee('Recent activity preview')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Implementation dependencies')
            ->assertSee('Minimal Laravel create path is now wired for card types')
            ->assertSee('Operator checklist')
            ->assertSee('Review activation mode before publishing a new or changed tier.')
            ->assertSee('Escalation guide')
            ->assertSee('Escalate activation-rule disagreements before publishing a tier change.')
            ->assertSee('Shift handoff notes')
            ->assertSee('Hand off draft tiers with the exact legacy rate and activation mode they are meant to mirror.')
            ->assertSee('Open issues to carry')
            ->assertSee('Partner tier approval flow parity is still unresolved against the legacy workflow.')
            ->assertSee('First Laravel wiring step')
            ->assertSee('When PHP becomes available, start by turning the card type preview into a real create/update path with the smallest possible write flow.')
            ->assertSee('Persist a minimal name, slug, rate, and activation mode payload before expanding rule imports.')
            ->assertSee('CardType model and migration skeleton exist')
            ->assertSee('Minimal create wiring now exists, but update flow, publish logic, and rule imports are still pending')
            ->assertSee('Legacy tier names mapped')
            ->assertSee('Tier rule publishing and richer workflow handlers still need PHP-backed follow-through')
            ->assertSee('Gold tier rules reviewed')
            ->assertSee('Partner tier held as draft')
            ->assertSee('Old Galaxy card tier catalog')
            ->assertSee('activation behavior')
            ->assertSee('Auto after issue')
            ->assertSee('1.50x');
    }

    public function test_authenticated_user_can_store_card_type_from_live_admin_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.75',
            'is_active' => '1',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index').'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Prime" was created.');

        $this->assertDatabaseHas('card_types', [
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.75',
            'is_active' => true,
        ]);
    }

    public function test_card_types_page_shows_live_flow_success_flash_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['status' => 'Card type "Galaxy Prime" was created.'])
            ->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('id="backend-flow-status"', false)
            ->assertSee('tabindex="-1"', false)
            ->assertSee('role="status"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('Backend flow checkpoint')
            ->assertSee('Card type "Galaxy Prime" was created.')
            ->assertSee('id="live-form"', false);
    }

    public function test_card_types_page_resolves_live_form_action_from_route_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('method="POST"', false)
            ->assertSee('action="/admin/card-types"', false)
            ->assertSee('href="/admin/card-types"', false)
            ->assertSee('Back to catalog');
    }

    public function test_card_types_page_resolves_live_form_action_route_parameters(): void
    {
        Route::middleware(['web', 'auth', 'can:access-admin'])
            ->prefix('admin')
            ->as('admin.')
            ->get('/card-types/{cardType}/draft-preview', fn (string $cardType) => $cardType)
            ->name('card-types.draft-preview');

        Config::set('admin-pages.card-types.liveForm.actionRoute', 'admin.card-types.draft-preview');
        Config::set('admin-pages.card-types.liveForm.actionRouteParameters', [
            'cardType' => 'gold',
            'ignored' => ['bad'],
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelRoute', 'admin.card-types.draft-preview');
        Config::set('admin-pages.card-types.liveForm.cancelRouteParameters', [
            'cardType' => 'silver',
            'ignored' => ['bad'],
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelLabel', 'Return to draft preview');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('action="/admin/card-types/gold/draft-preview"', false)
            ->assertSee('href="/admin/card-types/silver/draft-preview"', false)
            ->assertSee('Return to draft preview');
    }

    public function test_card_types_page_renders_live_form_patch_method_spoofing(): void
    {
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        Config::set('admin-pages.card-types.liveForm.method', 'PATCH');
        Config::set('admin-pages.card-types.liveForm.actionRoute', 'admin.card-types.update');
        Config::set('admin-pages.card-types.liveForm.actionRouteParameters', [
            'cardType' => $cardType->id,
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelRoute', 'admin.card-types.index');
        Config::set('admin-pages.card-types.liveForm.cancelLabel', 'Back to catalog');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('method="POST"', false)
            ->assertSee('action="/admin/card-types/'.$cardType->id.'"', false)
            ->assertSee('name="_method"', false)
            ->assertSee('value="PATCH"', false)
            ->assertSee('href="/admin/card-types"', false)
            ->assertSee('Back to catalog');
    }

    public function test_card_types_page_renders_hidden_live_form_fields_without_label_markup(): void
    {
        Config::set('admin-pages.card-types.liveForm.fields', [
            [
                'name' => 'mode',
                'type' => 'hidden',
                'value' => 'edit',
            ],
            ...Config::get('admin-pages.card-types.liveForm.fields', []),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('type="hidden"', false)
            ->assertSee('name="mode"', false)
            ->assertSee('value="edit"', false)
            ->assertDontSee('<label for="live-form-mode"', false);
    }

    public function test_card_types_page_renders_hidden_live_form_zero_values(): void
    {
        Config::set('admin-pages.card-types.liveForm.fields', [
            [
                'name' => 'mode',
                'type' => 'hidden',
                'value' => 0,
            ],
            ...Config::get('admin-pages.card-types.liveForm.fields', []),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('type="hidden"', false)
            ->assertSee('name="mode"', false)
            ->assertSee('value="0"', false);
    }

    public function test_card_types_page_renders_boolean_live_form_attributes(): void
    {
        Config::set('admin-pages.card-types.liveForm.fields', [
            ...array_map(function (array $field): array {
                if ($field['name'] !== 'slug') {
                    return $field;
                }

                $field['attributes']['readonly'] = true;

                return $field;
            }, Config::get('admin-pages.card-types.liveForm.fields', [])),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('name="slug"', false)
            ->assertSee('readonly', false)
            ->assertDontSee('readonly="1"', false);
    }

    public function test_card_types_page_renders_scalar_live_form_values(): void
    {
        Config::set('admin-pages.card-types.liveForm.fields', [
            ...array_map(function (array $field): array {
                return match ($field['name']) {
                    'points_rate' => [...$field, 'value' => 2.75],
                    'is_active' => [...$field, 'value' => false],
                    default => $field,
                };
            }, Config::get('admin-pages.card-types.liveForm.fields', [])),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('value="2.75"', false)
            ->assertSee('<option value="0" selected>', false);
    }

    public function test_card_types_page_renders_scalar_live_form_option_values(): void
    {
        Config::set('admin-pages.card-types.liveForm.fields', [
            ...array_map(function (array $field): array {
                if ($field['name'] !== 'is_active') {
                    return $field;
                }

                return [
                    ...$field,
                    'value' => true,
                    'options' => [
                        ['label' => 'Active', 'value' => true],
                        ['label' => 'Draft', 'value' => false],
                        ['label' => 'Archived', 'value' => 2],
                    ],
                ];
            }, Config::get('admin-pages.card-types.liveForm.fields', [])),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('<option value="1" selected>', false)
            ->assertSee('<option value="0"', false)
            ->assertSee('<option value="2"', false);
    }

    public function test_card_types_page_renders_live_form_field_attributes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('autocomplete="organization-title"', false)
            ->assertSee('spellcheck="false"', false)
            ->assertSee('step="0.01"', false)
            ->assertSee('min="0"', false)
            ->assertSee('inputmode="decimal"', false)
            ->assertSee('placeholder="Galaxy Prime"', false)
            ->assertSee('placeholder="galaxy-prime"', false)
            ->assertSee('placeholder="1.50"', false)
            ->assertSee('id="live-form-name"', false)
            ->assertSee('for="live-form-name"', false)
            ->assertSee('id="live-form-name-help"', false)
            ->assertSee('aria-describedby="live-form-name-help"', false)
            ->assertSee('autofocus', false)
            ->assertDontSee('aria-errormessage=', false)
            ->assertSee('aria-invalid="false"', false)
            ->assertSee('required', false)
            ->assertSee('Use the operator-facing tier name from the Galaxy catalog.')
            ->assertSee('Lowercase identifier used in imports and rule mapping.')
            ->assertSee('Decimal multiplier applied to spend accrual for this tier.');
    }

    public function test_card_types_page_links_live_form_errors_to_field_descriptions(): void
    {
        $user = User::factory()->create();

        $response = $this->from(route('admin.card-types.index'))->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => '',
            'slug' => 'invalid slug',
            'points_rate' => '-1',
            'is_active' => 'not-a-boolean',
        ])->followRedirects();

        $response
            ->assertOk()
            ->assertSee('href="#live-form-name"', false)
            ->assertSee('href="#live-form-points_rate"', false)
            ->assertSee('id="live-form-validation-title"', false)
            ->assertSee('aria-labelledby="live-form-validation-title"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('id="live-form-name-error"', false)
            ->assertSee('role="alert"', false)
            ->assertSee('aria-describedby="live-form-name-help live-form-name-error"', false)
            ->assertSee('aria-errormessage="live-form-name-error"', false)
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('id="live-form-points_rate-error"', false)
            ->assertSee('aria-errormessage="live-form-points_rate-error"', false);
    }

    public function test_card_type_live_admin_form_returns_validation_errors_for_invalid_payload(): void
    {
        $user = User::factory()->create();

        $response = $this->from(route('admin.card-types.index'))->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => '',
            'slug' => 'invalid slug',
            'points_rate' => '-1',
            'is_active' => 'not-a-boolean',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors(['name', 'points_rate', 'is_active']);

        $this->assertDatabaseCount('card_types', 0);
    }

    public function test_card_type_live_admin_form_normalizes_slug_and_boolean_input_before_store(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => 'Galaxy Prime Plus',
            'slug' => 'Galaxy Prime Plus',
            'points_rate' => '2.50',
            'is_active' => 'true',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index').'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Prime Plus" was created.');

        $this->assertDatabaseHas('card_types', [
            'name' => 'Galaxy Prime Plus',
            'slug' => 'galaxy-prime-plus',
            'points_rate' => '2.50',
            'is_active' => true,
        ]);
    }

    public function test_card_type_live_admin_form_returns_operator_friendly_validation_messages(): void
    {
        $user = User::factory()->create();

        CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from(route('admin.card-types.index'))->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => 'Galaxy Prime Copy',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => 'not-a-boolean',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'This card type slug is already in use.',
                'is_active' => 'The status field must be Active or Draft.',
            ]);
    }

    public function test_card_type_create_validation_redirects_to_index_without_referrer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => '',
            'slug' => 'invalid slug',
            'points_rate' => '-1',
            'is_active' => 'not-a-boolean',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors(['name', 'points_rate', 'is_active']);
    }

    public function test_authenticated_user_can_update_card_type_from_live_admin_flow(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => 'Galaxy Prime Plus',
            'slug' => 'Galaxy Prime Plus',
            'points_rate' => '2.25',
            'is_active' => 'false',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index').'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Prime Plus" was updated.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'name' => 'Galaxy Prime Plus',
            'slug' => 'galaxy-prime-plus',
            'points_rate' => '2.25',
            'is_active' => false,
        ]);
    }

    public function test_card_type_update_allows_reusing_current_slug_but_rejects_other_existing_slug(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $otherCardType = CardType::create([
            'name' => 'Galaxy Silver',
            'slug' => 'galaxy-silver',
            'points_rate' => '1.00',
            'is_active' => true,
        ]);

        $okResponse = $this->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => 'Galaxy Prime Updated',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.75',
            'is_active' => '1',
        ]);

        $okResponse
            ->assertRedirect(route('admin.card-types.index').'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Prime Updated" was updated.');

        $errorResponse = $this->from(route('admin.card-types.index'))->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => 'Galaxy Prime Updated Again',
            'slug' => $otherCardType->slug,
            'points_rate' => '1.90',
            'is_active' => '1',
        ]);

        $errorResponse
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'This card type slug is already in use.',
            ]);
    }

    public function test_card_types_page_shows_update_success_flash_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['status' => 'Card type "Galaxy Prime Plus" was updated.'])
            ->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('id="backend-flow-status"', false)
            ->assertSee('tabindex="-1"', false)
            ->assertSee('role="status"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('Backend flow checkpoint')
            ->assertSee('Card type "Galaxy Prime Plus" was updated.');
    }

    public function test_card_type_update_returns_validation_errors_for_invalid_payload(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from(route('admin.card-types.index'))->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => '',
            'slug' => 'invalid slug',
            'points_rate' => '-1',
            'is_active' => 'not-a-boolean',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors(['name', 'points_rate', 'is_active']);

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
    }

    public function test_card_type_update_returns_operator_friendly_validation_messages(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $otherCardType = CardType::create([
            'name' => 'Galaxy Silver',
            'slug' => 'galaxy-silver',
            'points_rate' => '1.00',
            'is_active' => true,
        ]);

        $response = $this->from(route('admin.card-types.index'))->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => 'Galaxy Prime Copy',
            'slug' => $otherCardType->slug,
            'points_rate' => '1.50',
            'is_active' => 'not-a-boolean',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'This card type slug is already in use.',
                'is_active' => 'The status field must be Active or Draft.',
            ]);
    }

    public function test_card_type_update_validation_redirects_to_index_without_referrer(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => '',
            'slug' => 'invalid slug',
            'points_rate' => '-1',
            'is_active' => 'not-a-boolean',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors(['name', 'points_rate', 'is_active']);
    }
}
