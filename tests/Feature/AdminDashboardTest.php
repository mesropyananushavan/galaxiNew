<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
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
            ->assertSee('Create or edit card type')
            ->assertSee('Publish type')
            ->assertSee('New type')
            ->assertSee('Import rules')
            ->assertSee('Management snapshot')
            ->assertSee('Active tiers')
            ->assertSee('Imported rules')
            ->assertSee('No custom card types configured yet')
            ->assertSee('Create first type')
            ->assertSee('Card type rules are still preview-only')
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
            ->assertSee('First Laravel wiring step')
            ->assertSee('When PHP becomes available, start by turning the card type preview into a real create/update path with the smallest possible write flow.')
            ->assertSee('Persist a minimal name, slug, rate, and activation mode payload before expanding rule imports.')
            ->assertSee('CardType model and migration skeleton exist')
            ->assertSee('Form request, controller action, and persistence wiring still pending')
            ->assertSee('Legacy tier names mapped')
            ->assertSee('Laravel save handler still unavailable without PHP runtime')
            ->assertSee('Gold tier rules reviewed')
            ->assertSee('Partner tier held as draft')
            ->assertSee('Old Galaxy card tier catalog')
            ->assertSee('activation behavior')
            ->assertSee('Auto after issue')
            ->assertSee('1.50x');
    }
}
