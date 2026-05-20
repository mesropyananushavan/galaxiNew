<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\DashboardController;
use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

enum AdminCardTypePreviewRoute: string
{
    case Gold = 'gold';
    case Silver = 'silver';
}

enum AdminCardTypePreviewMode
{
    case Gold;
    case Silver;
}

final class AdminCardTypePreviewStringable implements \Stringable
{
    public function __construct(
        private readonly string $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

final class AdminCardTypePreviewRoutable implements \Illuminate\Contracts\Routing\UrlRoutable
{
    public function __construct(
        private readonly string $value,
    ) {
    }

    public function getRouteKey(): string
    {
        return $this->value;
    }

    public function getRouteKeyName(): string
    {
        return 'cardType';
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return new self((string) $value);
    }

    public function resolveChildRouteBinding($childType, $value, $field): ?self
    {
        return new self((string) $value);
    }
}

final class AdminCardTypePreviewMixedRouteValue implements \Illuminate\Contracts\Routing\UrlRoutable, \Stringable
{
    public function __construct(
        private readonly string $routeKey,
        private readonly string $stringValue,
    ) {
    }

    public function __toString(): string
    {
        return $this->stringValue;
    }

    public function getRouteKey(): string
    {
        return $this->routeKey;
    }

    public function getRouteKeyName(): string
    {
        return 'cardType';
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return new self((string) $value, (string) $value);
    }

    public function resolveChildRouteBinding($childType, $value, $field): ?self
    {
        return new self((string) $value, (string) $value);
    }
}

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
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-dashboard',
            'is_active' => true,
        ]);

        $cardHolder = CardHolder::create([
            'full_name' => 'Mariam Dashboard',
            'phone' => '+37499111000',
            'email' => 'mariam.dashboard@example.com',
            'status' => 'active',
            'shop_id' => $shop->id,
        ]);

        $cardType = CardType::create([
            'name' => 'Dashboard Tier',
            'slug' => 'dashboard-tier',
            'points_rate' => 1.00,
            'is_active' => true,
        ]);

        Card::create([
            'number' => '550011223344',
            'status' => 'active',
            'card_holder_id' => $cardHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
            'issued_at' => now(),
        ]);

        $role = Role::create([
            'name' => 'Dashboard Lead',
            'slug' => 'dashboard-lead',
        ]);

        $permission = Permission::create([
            'name' => 'Manage dashboard',
            'slug' => 'manage-dashboard',
        ]);

        $role->permissions()->attach($permission);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Galaxi Admin')
            ->assertSee('Phase 1 admin information architecture baseline')
            ->assertSee('/admin')
            ->assertSee('Operations')
            ->assertSee('Cardholders')
            ->assertSee('Roles & Permissions')
            ->assertSee('Planned sections')
            ->assertSee('9')
            ->assertSee('Live domain coverage')
            ->assertSee('5/5 core Galaxy domains live')
            ->assertSee('Foundation readiness')
            ->assertSee('review-ready foundation')
            ->assertSee('Active foundation coverage')
            ->assertSee('Galaxy branches 1/1 active, Galaxy holders 1/1 active, Galaxy card shells 1/1 active')
            ->assertSee('Branch pause coverage')
            ->assertSee('0/1 branches paused')
            ->assertSee('Access baseline coverage')
            ->assertSee('1 Galaxy access shells, 1 access permissions visible')
            ->assertSee('Tier baseline coverage')
            ->assertSee('1/1 Galaxy tiers active')
            ->assertSee('Galaxy live foundation snapshot')
            ->assertSee('branch setup and review work can move through real operational entities instead of generic placeholders')
            ->assertSee('Foundation handoff signal')
            ->assertSee('The dashboard already shows enough live Galaxy entities to support a useful foundation handoff review.')
            ->assertSee('Foundation focus:')
            ->assertSee('all first-pass foundation surfaces are visible.')
            ->assertSee('Foundation posture:')
            ->assertSee('fully visible foundation baseline.')
            ->assertSee('Live Galaxy branches')
            ->assertSee('Active Galaxy branches')
            ->assertSee('Live Galaxy holders')
            ->assertSee('Active Galaxy holders')
            ->assertSee('Live Galaxy card shells')
            ->assertSee('Active Galaxy card shells')
            ->assertSee('Live Galaxy tiers')
            ->assertSee('Active Galaxy tiers')
            ->assertSee('Live Galaxy access shells')
            ->assertSee('Live Galaxy access permissions')
            ->assertSee('Live review entry points')
            ->assertSee('Use these Galaxy review surfaces to move from branch setup into live operational checks once records start landing')
            ->assertSee('Entry coverage:')
            ->assertSee('6 live review entry points staged.')
            ->assertSee('Entry focus:')
            ->assertSee('start with review live galaxy branches.')
            ->assertSee('Entry posture:')
            ->assertSee('review-ready staged entry surfaces.')
            ->assertSee('Entry handoff signal')
            ->assertSee('Shared entry points already have enough live branch, holder, and card-shell coverage to support a useful foundation handoff review.')
            ->assertSee('Review live Galaxy branches</a> (Route: /admin/shops)', false)
            ->assertSee('/admin/shops')
            ->assertSee('Review live Galaxy holders</a> (Route: /admin/cardholders)', false)
            ->assertSee('/admin/cardholders')
            ->assertSee('Review live Galaxy card shells</a> (Route: /admin/cards)', false)
            ->assertSee('/admin/cards')
            ->assertSee('Review live Galaxy tiers</a> (Route: /admin/card-types)', false)
            ->assertSee('/admin/card-types')
            ->assertSee('Review live Galaxy access shells</a> (Route: /admin/roles-permissions)', false)
            ->assertSee('/admin/roles-permissions')
            ->assertSee('Review Galaxy reporting sources</a> (Route: /admin/reports)', false)
            ->assertSee('/admin/reports')
            ->assertSee('Galaxy migration map')
            ->assertSee('These grouped sections mark the Galaxy admin surfaces that still need parity work, so each Phase 1 slice can land against a visible target map')
            ->assertSee('Mapped surfaces:')
            ->assertSee('10 planned admin surfaces are currently staged in the Phase 1 target map.')
            ->assertSee('Mapped groups:')
            ->assertSee('3 top-level admin groups are currently staged in the Phase 1 target map.')
            ->assertSee('Mapped routes:')
            ->assertSee('10 Galaxy foundation route targets are currently linked from the Phase 1 target map.')
            ->assertSee('Migration-map focus:')
            ->assertSee('start with dashboard.')
            ->assertSee('Migration-map posture:')
            ->assertSee('grounded parity planning.')
            ->assertSee('Migration-map handoff signal')
            ->assertSee('The migration map already spans 3 grouped sections with live coverage in 5 core Galaxy domains, so parity handoff planning can stay grounded in the current Galaxy foundation shell.')
            ->assertSee('Operations (4 surfaces):')
            ->assertSee('Catalog (3 surfaces):')
            ->assertSee('Administration (3 surfaces):')
            ->assertSeeText('Dashboard')
            ->assertSee('Operational overview and shortcuts.')
            ->assertSee('/admin')
            ->assertSeeText('Checks & Points')
            ->assertSee('Purchases, accrual, and fiscal search.')
            ->assertSee('/admin/checks-points')
            ->assertSee('Route:')
            ->assertSeeText('Services & Rules')
            ->assertSee('Service groups, conditions, and business rules.')
            ->assertSee('/admin/services-rules')
            ->assertSee('Route:')
            ->assertSeeText('Shops')
            ->assertSee('Galaxy branch catalog and scope boundaries.')
            ->assertSee('/admin/shops')
            ->assertSee('Route:')
            ->assertSeeText('Roles & Permissions')
            ->assertSee('Galaxy access shells, permissions, and access baseline.')
            ->assertSee('/admin/roles-permissions')
            ->assertSee('Route:')
            ->assertSeeText('Reports')
            ->assertSee('Galaxy reporting analytics and source histories.')
            ->assertSee('/admin/reports')
            ->assertSee('Route:')
            ->assertSee('Resume latest live work')
            ->assertSee('Latest-work coverage:')
            ->assertSee('5 latest-work shortcuts currently available.')
            ->assertSee('Latest-work focus:')
            ->assertSee('start with open latest branch review: galaxy central (active).')
            ->assertSee('Latest-work posture:')
            ->assertSee('review-ready jump-back coverage.')
            ->assertSee('Latest-work handoff signal')
            ->assertSee('Latest-work shortcuts already carry enough live Galaxy coverage for a useful handoff review jump-back.')
            ->assertSee('Jump back into the latest Galaxy workspace for the branch, holder, card shell, or access shell that most recently changed')
            ->assertSee('Open latest branch review: Galaxy Central (active)</a> (Route: /admin/shops)', false)
            ->assertSee('/admin/shops?shop=1')
            ->assertSee('Open latest Galaxy holder review: Mariam Dashboard (active)</a> (Route: /admin/cardholders)', false)
            ->assertSee('/admin/cardholders?cardholder=1')
            ->assertSee('Open latest Galaxy card shell review: 550011223344 (active)</a> (Route: /admin/cards)', false)
            ->assertSee('/admin/cards?card=1')
            ->assertSee('Open latest Galaxy tier shell review: Dashboard Tier (active)</a> (Route: /admin/card-types)', false)
            ->assertSee('/admin/card-types?cardType=1')
            ->assertSee('Open latest Galaxy access shell review: Dashboard Lead (1 permissions)</a> (Route: /admin/roles-permissions)', false)
            ->assertSee('/admin/roles-permissions?role=1')
            ->assertSee('1');
    }

    public function test_user_assigned_to_active_shop_can_access_admin_dashboard(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-admin-access',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $shop->id,
        ]);

        $role = Role::create([
            'name' => 'Shop Supervisor',
            'slug' => 'shop-supervisor-admin-access',
        ]);

        $permission = Permission::create([
            'name' => 'Access admin dashboard',
            'slug' => 'access-admin-dashboard',
        ]);

        $role->permissions()->attach($permission->id);

        $user->roles()->attach($role->id);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Galaxi Admin');

        $this->assertFalse($user->hasBootstrapAdminAccess());
        $this->assertTrue($user->belongsToActiveShop());
        $this->assertTrue($user->hasPermissionBearingRole());
        $this->assertTrue($user->hasShopScopedAdminAccess());
        $this->assertTrue($user->canAccessAdminPanel());
    }

    public function test_user_assigned_to_active_shop_without_role_cannot_access_admin_dashboard(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy North',
            'code' => 'galaxy-north-admin-access',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $shop->id,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();

        $this->assertFalse($user->hasBootstrapAdminAccess());
        $this->assertTrue($user->belongsToActiveShop());
        $this->assertFalse($user->hasPermissionBearingRole());
        $this->assertFalse($user->hasShopScopedAdminAccess());
        $this->assertFalse($user->canAccessAdminPanel());
    }

    public function test_user_assigned_to_active_shop_with_role_but_without_permissions_cannot_access_admin_dashboard(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy East',
            'code' => 'galaxy-east-admin-access',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $shop->id,
        ]);

        $role = Role::create([
            'name' => 'Shop Trainee',
            'slug' => 'shop-trainee-admin-access',
        ]);

        $user->roles()->attach($role->id);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();

        $this->assertFalse($user->hasBootstrapAdminAccess());
        $this->assertTrue($user->belongsToActiveShop());
        $this->assertFalse($user->hasPermissionBearingRole());
        $this->assertFalse($user->hasShopScopedAdminAccess());
        $this->assertFalse($user->canAccessAdminPanel());
    }

    public function test_user_assigned_to_paused_shop_cannot_access_admin_dashboard(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Airport',
            'code' => 'galaxy-airport-admin-access',
            'is_active' => false,
        ]);

        $user = User::factory()->create([
            'shop_id' => $shop->id,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();

        $this->assertFalse($user->hasBootstrapAdminAccess());
        $this->assertFalse($user->belongsToActiveShop());
        $this->assertFalse($user->hasShopScopedAdminAccess());
        $this->assertFalse($user->canAccessAdminPanel());
    }

    public function test_unscoped_user_keeps_bootstrap_admin_access_helpers(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Bootstrap Review Shop',
            'code' => 'bootstrap-review-shop',
            'is_active' => true,
        ]);

        $this->assertTrue($user->hasBootstrapAdminAccess());
        $this->assertFalse($user->belongsToActiveShop());
        $this->assertFalse($user->hasPermissionBearingRole());
        $this->assertFalse($user->hasShopScopedAdminAccess());
        $this->assertTrue($user->canAccessAdminPanel());
        $this->assertTrue($user->can('access-admin'));
        $this->assertTrue($user->canAccessShop($shop));
        $this->assertTrue($user->can('access-shop', $shop));
        $this->assertFalse($user->canAccessShop(null));
    }

    public function test_shop_scoped_admin_access_helper_allows_only_the_users_assigned_shop(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Assigned Shop',
            'code' => 'galaxy-assigned-shop-access',
            'is_active' => true,
        ]);

        $otherShop = Shop::create([
            'name' => 'Galaxy Other Shop',
            'code' => 'galaxy-other-shop-access',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Scoped Manager',
            'slug' => 'scoped-manager-shop-access',
        ]);

        $permission = Permission::create([
            'name' => 'Access scoped shop admin',
            'slug' => 'access-scoped-shop-admin',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $this->assertTrue($user->hasShopScopedAdminAccess());
        $this->assertTrue($user->can('access-admin'));
        $this->assertTrue($user->canAccessShop($assignedShop));
        $this->assertTrue($user->can('access-shop', $assignedShop));
        $this->assertFalse($user->canAccessShop($otherShop));
        $this->assertFalse($user->can('access-shop', $otherShop));
        $this->assertFalse($user->canAccessShop(null));
    }

    public function test_shop_scoped_admin_access_helper_denies_paused_shop_users_even_for_their_assigned_shop(): void
    {
        $pausedShop = Shop::create([
            'name' => 'Galaxy Paused Scoped Shop',
            'code' => 'galaxy-paused-scoped-shop-access',
            'is_active' => false,
        ]);

        $user = User::factory()->create([
            'shop_id' => $pausedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Paused Scoped Manager',
            'slug' => 'paused-scoped-manager-shop-access',
        ]);

        $permission = Permission::create([
            'name' => 'Access paused scoped shop admin',
            'slug' => 'access-paused-scoped-shop-admin',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $this->assertFalse($user->hasShopScopedAdminAccess());
        $this->assertFalse($user->can('access-admin'));
        $this->assertFalse($user->canAccessShop($pausedShop));
        $this->assertFalse($user->can('access-shop', $pausedShop));
    }

    public function test_dashboard_shows_live_workspace_fallback_when_no_records_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Live Galaxy tiers')
            ->assertSee('Active Galaxy tiers')
            ->assertSee('Live domain coverage')
            ->assertSee('0/5 core Galaxy domains live')
            ->assertSee('Foundation readiness')
            ->assertSee('foundation setup stage')
            ->assertSee('Active foundation coverage')
            ->assertSee('Galaxy branches 0/0 active, Galaxy holders 0/0 active, Galaxy card shells 0/0 active')
            ->assertSee('Branch pause coverage')
            ->assertSee('0/0 branches paused')
            ->assertSee('Access baseline coverage')
            ->assertSee('0 Galaxy access shells, 0 access permissions visible')
            ->assertSee('Tier baseline coverage')
            ->assertSee('0/0 Galaxy tiers active')
            ->assertSee('Foundation handoff signal')
            ->assertSee('Phase 1 is still in Galaxy foundation setup mode, so the dashboard should keep first live entities visible before any handoff review feels grounded.')
            ->assertSee('Foundation focus:')
            ->assertSee('stabilize live Galaxy branches next.')
            ->assertSee('Foundation posture:')
            ->assertSee('setup-first foundation baseline.')
            ->assertSee('Entry coverage:')
            ->assertSee('6 live review entry points staged.')
            ->assertSee('Entry focus:')
            ->assertSee('start with review live galaxy branches.')
            ->assertSee('Entry posture:')
            ->assertSee('setup-first staged entry surfaces.')
            ->assertSee('Entry handoff signal')
            ->assertSee('Entry points should stay setup-first until live branch, holder, and card-shell coverage is visible across the Galaxy foundation.')
            ->assertSee('Resume latest live work')
            ->assertSee('Latest-work coverage:')
            ->assertSee('0 latest-work shortcuts currently available.')
            ->assertSee('Latest-work focus:')
            ->assertSee('first live Galaxy workspace still needs to be created.')
            ->assertSee('Latest-work posture:')
            ->assertSee('setup-first jump-back pending.')
            ->assertSee('Latest-work handoff signal')
            ->assertSee('Latest-work shortcuts should stay setup-first until more live Galaxy workspaces are available to resume.')
            ->assertSee('Migration-map focus:')
            ->assertSee('start with dashboard.')
            ->assertSee('Migration-map posture:')
            ->assertSee('map-first parity planning.')
            ->assertSee('Migration-map handoff signal')
            ->assertSee('The migration map already spans 3 grouped sections and 10 planned surfaces, but handoff planning should stay map-first until live Galaxy domains start landing in the Galaxy foundation.')
            ->assertSee('No live records have been created yet. Start in the live review entry points above to open the first Galaxy-backed workspace.')
            ->assertSee('In Phase 1, this usually means the branch is still moving through first-pass setup for Galaxy branches, Galaxy holders, Galaxy card shells, or access structure.')
            ->assertDontSee('Open latest branch review:')
            ->assertDontSee('Open latest Galaxy holder review:')
            ->assertDontSee('Open latest Galaxy card shell review:')
            ->assertDontSee('Open latest Galaxy tier shell review:')
            ->assertDontSee('Open latest Galaxy access shell review:');
    }

    public function test_dashboard_shows_only_available_latest_workspace_links(): void
    {
        Shop::create([
            'name' => 'Partial Dashboard Shop',
            'code' => 'partial-dashboard-shop',
            'is_active' => false,
        ]);

        CardType::create([
            'name' => 'Partial Dashboard Tier',
            'slug' => 'partial-dashboard-tier',
            'points_rate' => 1.00,
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Live Galaxy tiers')
            ->assertSee('1')
            ->assertSee('Active Galaxy tiers')
            ->assertSee('0')
            ->assertSee('Live domain coverage')
            ->assertSee('1/5 core Galaxy domains live')
            ->assertSee('Foundation readiness')
            ->assertSee('foundation coverage in progress')
            ->assertSee('Active foundation coverage')
            ->assertSee('Galaxy branches 0/1 active, Galaxy holders 0/0 active, Galaxy card shells 0/0 active')
            ->assertSee('Branch pause coverage')
            ->assertSee('1/1 branches paused')
            ->assertSee('Access baseline coverage')
            ->assertSee('0 Galaxy access shells, 0 access permissions visible')
            ->assertSee('Tier baseline coverage')
            ->assertSee('0/1 Galaxy tiers active')
            ->assertSee('Foundation handoff signal')
            ->assertSee('Some live Galaxy entities are visible, but the dashboard still needs broader Galaxy foundation coverage before foundation handoff review feels complete.')
            ->assertSee('Foundation focus:')
            ->assertSee('stabilize live Galaxy holders next.')
            ->assertSee('Foundation posture:')
            ->assertSee('partial foundation baseline.')
            ->assertSee('Entry coverage:')
            ->assertSee('6 live review entry points staged.')
            ->assertSee('Entry focus:')
            ->assertSee('start with review live galaxy branches.')
            ->assertSee('Entry posture:')
            ->assertSee('partial staged entry coverage.')
            ->assertSee('Entry handoff signal')
            ->assertSee('Entry points should stay setup-first until live branch, holder, and card-shell coverage is visible across the Galaxy foundation.')
            ->assertSee('Resume latest live work')
            ->assertSee('Latest-work coverage:')
            ->assertSee('2 latest-work shortcuts currently available.')
            ->assertSee('Latest-work focus:')
            ->assertSee('start with open latest branch review: partial dashboard shop (inactive).')
            ->assertSee('Latest-work posture:')
            ->assertSee('partial jump-back coverage.')
            ->assertSee('Latest-work handoff signal')
            ->assertSee('Latest-work shortcuts should stay setup-first until more live Galaxy workspaces are available to resume.')
            ->assertSee('Migration-map focus:')
            ->assertSee('start with dashboard.')
            ->assertSee('Migration-map posture:')
            ->assertSee('parity staging in progress.')
            ->assertSee('Migration-map handoff signal')
            ->assertSee('The migration map already spans 3 grouped sections, but only 1 core Galaxy domains have live Galaxy foundation coverage so far.')
            ->assertSee('Open latest branch review: Partial Dashboard Shop (inactive)</a> (Route: /admin/shops)', false)
            ->assertSee('/admin/shops?shop=1')
            ->assertSee('Open latest Galaxy tier shell review: Partial Dashboard Tier (draft)</a> (Route: /admin/card-types)', false)
            ->assertSee('/admin/card-types?cardType=1')
            ->assertDontSee('No live records have been created yet. Start in the live review entry points above to open the first Galaxy-backed workspace.')
            ->assertDontSee('In Phase 1, this usually means the branch is still moving through first-pass setup for Galaxy branches, Galaxy holders, Galaxy card shells, or access structure.')
            ->assertDontSee('Open latest Galaxy holder review:')
            ->assertDontSee('Open latest Galaxy card shell review:')
            ->assertDontSee('Open latest Galaxy access shell review:');
    }

    public function test_dashboard_latest_live_work_shortcuts_respect_shop_scope(): void
    {
        Carbon::setTestNow('2026-04-21 15:00:00');

        $assignedShop = Shop::create([
            'name' => 'Dashboard Home Shop',
            'code' => 'dashboard-home-shop',
            'is_active' => true,
        ]);

        $otherShop = Shop::create([
            'name' => 'Dashboard Other Shop',
            'code' => 'dashboard-other-shop',
            'is_active' => true,
        ]);

        $assignedHolder = CardHolder::create([
            'shop_id' => $assignedShop->id,
            'full_name' => 'Scoped Dashboard Holder',
            'phone' => '+37495555555',
            'email' => 'scoped-dashboard-holder@example.com',
            'is_active' => true,
        ]);

        $assignedHolder->forceFill([
            'created_at' => '2026-04-20 09:15:00',
            'updated_at' => '2026-04-20 09:15:00',
        ])->saveQuietly();

        $otherHolder = CardHolder::create([
            'shop_id' => $otherShop->id,
            'full_name' => 'Other Dashboard Holder',
            'phone' => '+37496666666',
            'email' => 'other-dashboard-holder@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Dashboard Scoped Tier',
            'slug' => 'dashboard-scoped-tier',
            'points_rate' => 1.00,
            'is_active' => true,
        ]);

        $assignedCard = Card::create([
            'shop_id' => $assignedShop->id,
            'card_holder_id' => $assignedHolder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-DASH-001',
            'status' => 'active',
        ]);

        $assignedCard->forceFill([
            'created_at' => '2026-04-20 12:45:00',
            'updated_at' => '2026-04-20 12:45:00',
        ])->saveQuietly();

        Card::create([
            'shop_id' => $otherShop->id,
            'card_holder_id' => $otherHolder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-DASH-002',
            'status' => 'blocked',
        ]);

        $user = User::factory()->create([
            'name' => 'Scoped Dashboard Manager',
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Dashboard Scoped Reviewer',
            'slug' => 'dashboard-scoped-reviewer',
        ]);

        $permission = Permission::create([
            'name' => 'Review scoped dashboard shortcuts',
            'slug' => 'review-scoped-dashboard-shortcuts',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Current review scope')
            ->assertSee('Shop-scoped admin mode is active. Latest-work shortcuts and live review links should stay anchored to Dashboard Home Shop with branch-specific review wording while Phase 1 policies are still being mapped.')
            ->assertSee('Assigned branch snapshot')
            ->assertSee('Branch')
            ->assertSee('Dashboard Home Shop')
            ->assertSee('Scoped action coverage:')
            ->assertSee('3 scoped branch actions ready.')
            ->assertSee('Scoped action posture:')
            ->assertSee('review-ready scoped shortcuts.')
            ->assertSee('Scoped action focus:')
            ->assertSee('latest branch review ready.')
            ->assertSee('Branch code')
            ->assertSee('dashboard-home-shop')
            ->assertSee('Branch posture')
            ->assertSee('active branch, live activity visible')
            ->assertSee('Branch readiness')
            ->assertSee('review-ready')
            ->assertSee('Branch coverage')
            ->assertSee('Galaxy holders and card shells live')
            ->assertSee('Handoff signal')
            ->assertSee('Assigned branch already carries enough live coverage for a useful scoped handoff review.')
            ->assertSee('Primary manager')
            ->assertSee('Scoped Dashboard Manager')
            ->assertSee('Galaxy foundation status')
            ->assertSee('Visible Galaxy holders')
            ->assertSee('Visible Galaxy card shells')
            ->assertSee('Assigned staff')
            ->assertSee('Latest Galaxy holder')
            ->assertSee('Scoped Dashboard Holder')
            ->assertSee('Latest Galaxy holder status')
            ->assertSee('Latest Galaxy holder added')
            ->assertSee('2026-04-20')
            ->assertSee('Latest Galaxy card shell')
            ->assertSee('GX-DASH-001')
            ->assertSee('Latest Galaxy card shell status')
            ->assertSee('Latest Galaxy card shell issued')
            ->assertSee('Latest activity source')
            ->assertSee('Galaxy card shell issued')
            ->assertSee('Activity freshness')
            ->assertSee('fresh activity')
            ->assertSee('Suggested follow-up')
            ->assertSee('Resume the latest branch review flow from the scoped shortcuts.')
            ->assertSee('>1<', false)
            ->assertSee('Open assigned branch review</a> (Route: /admin/shops)', false)
            ->assertSee('/admin/shops?shop='.$assignedShop->id)
            ->assertSee('Open latest Galaxy holder in branch</a> (Route: /admin/cardholders)', false)
            ->assertSee('/admin/cardholders?cardholder='.$assignedHolder->id)
            ->assertSee('Open latest Galaxy card shell in branch</a> (Route: /admin/cards)', false)
            ->assertSee('/admin/cards?card='.$assignedCard->id)
            ->assertSee('Entry handoff signal')
            ->assertSee('Assigned-branch entry points already have enough live branch, holder, and card-shell coverage to support a useful scoped handoff review.')
            ->assertSee('Entry posture')
            ->assertSee('These entry points still open the shared Phase 1 workspaces, but branch-backed review inside Galaxy branches, Galaxy holders, and Galaxy card shells now narrows to the assigned branch with branch-specific review wording once the workspace loads.')
            ->assertSee('Review live Galaxy branches in assigned branch')
            ->assertSee('Review live Galaxy holders in assigned branch')
            ->assertSee('Review live Galaxy card shells in assigned branch')
            ->assertSee('Review shared Galaxy tiers')
            ->assertSee('Review shared Galaxy access shells')
            ->assertSee('Review shared Galaxy reporting sources')
            ->assertDontSee('Review live shops</a>', false)
            ->assertSee('Latest-work handoff signal')
            ->assertSee('Scoped latest-work shortcuts already carry enough branch-backed coverage for a useful handoff review jump-back.')
            ->assertSee('Phase 1 scope note')
            ->assertSee('Latest-work shortcuts for Galaxy branches, Galaxy holders, and Galaxy card shells now follow branch scope and branch-specific review wording. Galaxy tiers, Galaxy access shells, and reporting remain shared review surfaces until deeper shop-aware policies arrive.')
            ->assertSee('Open latest branch review: Dashboard Home Shop (active)</a> (Route: /admin/shops)', false)
            ->assertSee('/admin/shops?shop='.$assignedShop->id)
            ->assertDontSee('Open latest branch review: Dashboard Other Shop (active)')
            ->assertSee('Open latest branch holder review: Scoped Dashboard Holder (active)</a> (Route: /admin/cardholders)', false)
            ->assertSee('/admin/cardholders?cardholder='.$assignedHolder->id)
            ->assertDontSee('Open latest branch holder review: Other Dashboard Holder (active)')
            ->assertSee('Open latest branch card shell review: GX-DASH-001 (active)</a> (Route: /admin/cards)', false)
            ->assertSee('/admin/cards?card='.$assignedCard->id)
            ->assertDontSee('Open latest branch card shell review: GX-DASH-002 (blocked)');

        Carbon::setTestNow();
    }

    public function test_unscoped_dashboard_does_not_show_shop_scope_summary(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertDontSee('Current review scope')
            ->assertDontSee('Shop-scoped admin mode is active.')
            ->assertSee('Entry handoff signal')
            ->assertSee('Entry points should stay setup-first until live branch, holder, and card-shell coverage is visible across the Galaxy foundation.')
            ->assertDontSee('Assigned branch snapshot')
            ->assertDontSee('Branch code')
            ->assertDontSee('Branch posture')
            ->assertDontSee('Branch readiness')
            ->assertDontSee('Branch coverage')
            ->assertDontSee('Primary manager')
            ->assertDontSee('Visible Galaxy holders')
            ->assertDontSee('Visible Galaxy card shells')
            ->assertDontSee('Assigned staff')
            ->assertDontSee('Latest Galaxy holder')
            ->assertDontSee('Latest Galaxy holder status')
            ->assertDontSee('Latest Galaxy holder added')
            ->assertDontSee('Latest Galaxy card shell')
            ->assertDontSee('Latest Galaxy card shell status')
            ->assertDontSee('Latest Galaxy card shell issued')
            ->assertDontSee('Latest activity source')
            ->assertDontSee('Activity freshness')
            ->assertDontSee('Suggested follow-up')
            ->assertDontSee('Open assigned branch review')
            ->assertDontSee('Open latest Galaxy holder in branch')
            ->assertDontSee('Open latest Galaxy card shell in branch')
            ->assertDontSee('Scoped action posture:')
            ->assertDontSee('These entry points still open the shared Phase 1 workspaces')
            ->assertDontSee('Review live Galaxy branches in assigned branch')
            ->assertDontSee('Review live Galaxy holders in assigned branch')
            ->assertDontSee('Review live Galaxy card shells in assigned branch')
            ->assertSee('Review live Galaxy branches')
            ->assertSee('Review live Galaxy holders')
            ->assertSee('Review live Galaxy card shells')
            ->assertDontSee('Phase 1 scope note')
            ->assertDontSee('Latest-work shortcuts for Galaxy branches, Galaxy holders, and Galaxy card shells now follow branch scope.');
    }

    public function test_shop_scoped_dashboard_empty_branch_snapshot_surfaces_follow_up_posture(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Quiet Dashboard Shop',
            'code' => 'quiet-dashboard-shop',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'Quiet Branch Manager',
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Quiet Dashboard Reviewer',
            'slug' => 'quiet-dashboard-reviewer',
        ]);

        $permission = Permission::create([
            'name' => 'Review quiet dashboard shortcuts',
            'slug' => 'review-quiet-dashboard-shortcuts',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Assigned branch snapshot')
            ->assertSee('This branch snapshot keeps the assigned Galaxy location in view, so setup gaps and fresh activity are visible before you jump into review')
            ->assertSee('Quiet Dashboard Shop')
            ->assertSee('Scoped action coverage:')
            ->assertSee('1 scoped branch actions ready.')
            ->assertSee('Scoped action posture:')
            ->assertSee('setup-first shortcuts only.')
            ->assertSee('Scoped action focus:')
            ->assertSee('branch setup first.')
            ->assertSee('Branch posture')
            ->assertSee('active branch, setup pending')
            ->assertSee('Branch readiness')
            ->assertSee('setup pending')
            ->assertSee('Branch coverage')
            ->assertSee('core branch records pending')
            ->assertSee('Handoff signal')
            ->assertSee('Assigned branch still needs first live records before handoff review can feel grounded.')
            ->assertSee('Latest Galaxy holder')
            ->assertSee('No Galaxy holders in assigned branch yet')
            ->assertSee('Latest Galaxy holder status')
            ->assertSee('n/a')
            ->assertSee('Latest Galaxy holder added')
            ->assertSee('Latest Galaxy card shell')
            ->assertSee('No Galaxy card shells in assigned branch yet')
            ->assertSee('Latest Galaxy card shell status')
            ->assertSee('Latest Galaxy card shell issued')
            ->assertSee('Latest activity source')
            ->assertSee('Setup pending')
            ->assertSee('Activity freshness')
            ->assertSee('setup stage')
            ->assertSee('Suggested follow-up')
            ->assertSee('Open assigned branch setup and create the first live records.')
            ->assertSee('Open assigned branch setup</a> (Route: /admin/shops)', false)
            ->assertSee('/admin/shops?shop='.$assignedShop->id)
            ->assertDontSee('Open assigned branch review</a>', false)
            ->assertSee('Set up assigned branch')
            ->assertDontSee('Review live Galaxy branches in assigned branch</a>', false)
            ->assertSee('Set up first Galaxy holder in assigned branch')
            ->assertDontSee('Review live Galaxy holders in assigned branch</a>', false)
            ->assertSee('Set up first Galaxy card shell in assigned branch')
            ->assertDontSee('Review live Galaxy card shells in assigned branch</a>', false)
            ->assertSee('Open branch setup: Quiet Dashboard Shop (active)</a> (Route: /admin/shops)', false)
            ->assertDontSee('Open latest branch review: Quiet Dashboard Shop (active)')
            ->assertSee('Open first Galaxy holder setup in assigned branch')
            ->assertSee('/admin/cardholders')
            ->assertDontSee('Open latest Galaxy holder review:')
            ->assertSee('Open first Galaxy card shell setup in assigned branch')
            ->assertSee('/admin/cards')
            ->assertDontSee('Open latest Galaxy card shell review:')
            ->assertDontSee('Open latest Galaxy holder in branch')
            ->assertDontSee('Open latest Galaxy card shell in branch');
    }

    public function test_dashboard_branch_helper_logic_covers_paused_branch_posture(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Paused Dashboard Shop',
            'code' => 'paused-dashboard-shop',
            'is_active' => false,
        ]);

        $controller = new class extends DashboardController
        {
            public function branchPostureForTest(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
            {
                return $this->branchOperationalPosture($shop, $latestHolder, $latestCard);
            }

            public function branchFollowUpForTest(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
            {
                return $this->branchSuggestedFollowUp($shop, $latestHolder, $latestCard);
            }

            public function branchActionsForTest(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): array
            {
                return $this->assignedBranchSnapshotActions($shop, $latestHolder, $latestCard);
            }
        };

        $this->assertSame('paused branch', $controller->branchPostureForTest($assignedShop, null, null));
        $this->assertSame(
            'Confirm pause reason before reopening branch work.',
            $controller->branchFollowUpForTest($assignedShop, null, null),
        );
        $this->assertSame([], $controller->branchActionsForTest($assignedShop, null, null));
    }

    public function test_shop_scoped_dashboard_partial_branch_snapshot_surfaces_card_setup_follow_up(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Partial Dashboard Shop',
            'code' => 'partial-dashboard-shop',
            'is_active' => true,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $assignedShop->id,
            'full_name' => 'Partial Dashboard Holder',
            'phone' => '+37497777777',
            'email' => 'partial-dashboard-holder@example.com',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'Partial Branch Manager',
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Partial Dashboard Reviewer',
            'slug' => 'partial-dashboard-reviewer',
        ]);

        $permission = Permission::create([
            'name' => 'Review partial dashboard shortcuts',
            'slug' => 'review-partial-dashboard-shortcuts',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Assigned branch snapshot')
            ->assertSee('Partial Dashboard Shop')
            ->assertSee('Scoped action focus:')
            ->assertSee('Galaxy card issuance next.')
            ->assertSee('Branch readiness')
            ->assertSee('setup in progress')
            ->assertSee('Branch coverage')
            ->assertSee('Galaxy holders live, card shells pending')
            ->assertSee('Handoff signal')
            ->assertSee('Galaxy holder activity is visible, but Galaxy card-shell coverage still needs to catch up before full branch handoff review.')
            ->assertSee('Latest Galaxy holder')
            ->assertSee('Partial Dashboard Holder')
            ->assertSee('Latest Galaxy card shell')
            ->assertSee('No Galaxy card shells in assigned branch yet')
            ->assertSee('Suggested follow-up')
            ->assertSee('Open assigned branch Galaxy card shell setup and issue the first live card shell.')
            ->assertSee('Open assigned branch review')
            ->assertSee('/admin/shops?shop='.$assignedShop->id)
            ->assertSee('Open latest Galaxy holder in branch')
            ->assertSee('/admin/cardholders?cardholder='.$holder->id)
            ->assertDontSee('Open latest Galaxy card shell in branch');
    }

    public function test_shop_scoped_dashboard_partial_branch_snapshot_surfaces_cardholder_backfill_follow_up(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Card First Dashboard Shop',
            'code' => 'card-first-dashboard-shop',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Card First Dashboard Tier',
            'slug' => 'card-first-dashboard-tier',
            'points_rate' => 1.00,
            'is_active' => true,
        ]);

        $card = Card::create([
            'shop_id' => $assignedShop->id,
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'number' => 'GX-PARTIAL-001',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'name' => 'Card First Branch Manager',
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Card First Dashboard Reviewer',
            'slug' => 'card-first-dashboard-reviewer',
        ]);

        $permission = Permission::create([
            'name' => 'Review card first dashboard shortcuts',
            'slug' => 'review-card-first-dashboard-shortcuts',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Assigned branch snapshot')
            ->assertSee('Card First Dashboard Shop')
            ->assertSee('Scoped action focus:')
            ->assertSee('Galaxy holder backfill next.')
            ->assertSee('Branch readiness')
            ->assertSee('setup in progress')
            ->assertSee('Branch coverage')
            ->assertSee('Galaxy card shells live, holders pending')
            ->assertSee('Handoff signal')
            ->assertSee('Galaxy card shell activity is visible, but Galaxy holder coverage still needs to catch up before full branch handoff review.')
            ->assertSee('Latest Galaxy holder')
            ->assertSee('No Galaxy holders in assigned branch yet')
            ->assertSee('Latest Galaxy card shell')
            ->assertSee('GX-PARTIAL-001')
            ->assertSee('Suggested follow-up')
            ->assertSee('Review assigned branch Galaxy card shells and backfill the first visible Galaxy holder record.')
            ->assertSee('Open assigned branch review')
            ->assertSee('/admin/shops?shop='.$assignedShop->id)
            ->assertSee('Open latest Galaxy card shell in branch')
            ->assertSee('/admin/cards?card='.$card->id)
            ->assertDontSee('Open latest Galaxy holder in branch');
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
            ->assertSeeText('Administration / Roles & Permissions')
            ->assertSee('roles-permissions')
            ->assertSee('future shop-scoped access review.');
    }

    public function test_authenticated_user_can_access_roles_permissions_management_preview(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions');

        $response
            ->assertOk()
            ->assertSee('Create Galaxy role in Galaxy foundation')
            ->assertSee('Galaxy access-shell workspace for role identities, permission bundles, and future shop-scoped access review.')
            ->assertSee('Create access shell')
            ->assertSee('Back to access shell catalog')
            ->assertSee('Role name')
            ->assertSee('Slug')
            ->assertSee('Lowercase identifier for the minimal Galaxy foundation role record.')
            ->assertSee('Galaxy foundation status')
            ->assertSee('Draft')
            ->assertSee('Active')
            ->assertSee('Draft roles stay safer for parity review. Active status is now persistable, but publish-style access changes should still stay parity-first.')
            ->assertSee('Scope rollout')
            ->assertSee('Shop scope still pending')
            ->assertSee('Phase 1 keeps shop scope review-only until the next thin write slice is ready.')
            ->assertSee('Publish posture')
            ->assertSee('Draft-safe only')
            ->assertSee('Publishing remains blocked even though role identity and status can already be saved in the Galaxy foundation layer.')
            ->assertSee('Create or edit Galaxy role')
            ->assertSee('Publish access')
            ->assertSee('Blocked until role persistence and shop-scoped parity checks exist beyond the preview shell.')
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('New Galaxy role')
            ->assertSee('href="#live-form"', false)
            ->assertSee('Review matrix')
            ->assertSee('Blocked until the Galaxy foundation permission matrix can be verified against legacy staff access.')
            ->assertSee('Management snapshot')
            ->assertSee('Active Galaxy access shells')
            ->assertSee('Reviewed Galaxy access shells')
            ->assertSee('Scoped Galaxy branches')
            ->assertSee('No shop-scoped roles configured yet')
            ->assertSee('Create first Galaxy role')
            ->assertSee('This is the first minimal Galaxy foundation-backed role write path. Keep it limited to role identity while permission bundles and shop scope remain parity-first review surfaces.')
            ->assertSee('Keep the new minimal role identity flow narrow, then layer role assignment, permission matrix, and shop-aware policy flows on top of it.')
            ->assertSee('Role identity writes are live, publishing is still preview-only')
            ->assertSee('The first Galaxy foundation-backed role form now saves role identity, but permission persistence, publishing controls, and assignment flows still need implementation.')
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
            ->assertSee('First Galaxy foundation wiring step')
            ->assertSee('Build on the new minimal role create/update path before exposing full assignment screens.')
            ->assertSee('Keep role identity persistence stable before tackling permission matrix editing.')
            ->assertSee('Role and Permission models plus migration skeletons exist')
            ->assertSee('Assignment UI, policy wiring, and permission-matrix handlers are still pending beyond the first role identity save flow')
            ->assertSee('Legacy role boundaries mapped')
            ->assertSee('Assignment, publishing, and permission-matrix flows still need PHP-backed authorization work')
            ->assertSee('Shop Manager bundle reviewed')
            ->assertSee('Cashier draft held back')
            ->assertSee('Old Galaxy staff and access matrix')
            ->assertSee('cashier/manager split')
            ->assertSee('Shop Manager')
            ->assertSee('Scoped to assigned shop');
    }

    public function test_roles_permissions_catalog_actions_reflect_saved_role_readiness(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'roles-catalog-readiness',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Catalog Access Lead',
            'slug' => 'catalog-access-lead',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'Review reporting access',
            'slug' => 'review-reporting-access',
        ]);

        $role->permissions()->attach($permission);

        $assignedUser = User::factory()->create([
            'name' => 'Nare Access Catalog',
            'shop_id' => $shop->id,
        ]);

        $assignedUser->roles()->attach($role->id);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions');

        $response
            ->assertOk()
            ->assertSee('Review matrix')
            ->assertSee('Blocked until saved Galaxy foundation permission bundles are verified against legacy staff access.')
            ->assertSee('Publish access')
            ->assertSee('Blocked until saved live access bundles clear assignment and shop-scope parity.');
    }

    public function test_roles_permissions_page_replaces_preview_rows_with_model_backed_role_data(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-roles',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Shop Manager',
            'slug' => 'shop-manager-live',
            'is_active' => true,
            'review_note' => 'Keep manager workflow aligned with the first live parity pass.',
            'access_note' => 'Keep branch access handoff visible during parity review.',
            'assignment_note' => 'Keep branch staff rollout review-only until parity checks are complete.',
        ]);

        $permissionA = Permission::create([
            'name' => 'Manage cards',
            'slug' => 'manage-cards-live',
            'review_note' => 'Keep card access parity visible before widening matrix edits.',
        ]);

        $permissionB = Permission::create([
            'name' => 'Manage gifts',
            'slug' => 'manage-gifts-live',
        ]);

        $role->permissions()->attach([$permissionA->id, $permissionB->id]);

        $userWithRole = User::factory()->create([
            'shop_id' => $shop->id,
        ]);

        $userWithRole->roles()->attach($role->id);

        $draftRole = Role::create([
            'name' => 'Cashier Draft',
            'slug' => 'cashier-draft-live',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions');

        $response
            ->assertOk()
            ->assertSee('Shop Manager')
            ->assertSee('href="/admin/roles-permissions?role=', false)
            ->assertSee('Galaxy Central')
            ->assertSee('Manage cards, Manage gifts')
            ->assertSee('Keep card access parity visible before widening matrix edits.')
            ->assertSee('Keep branch staff rollout review-only until parity checks are complete.')
            ->assertSee('Cashier Draft')
            ->assertSee('Unscoped in the Galaxy foundation read slice')
            ->assertSee('No permissions linked yet')
            ->assertSee('No permission review note saved yet')
            ->assertSee('No assignment note saved yet')
            ->assertSee('Review latest saved access shell')
            ->assertSee('Active Galaxy access shells')
            ->assertSee('Draft Galaxy access shells')
            ->assertSee('Reviewed Galaxy access shells')
            ->assertSee('Galaxy access notes')
            ->assertSee('Galaxy assignment notes')
            ->assertSee('Galaxy permission review notes')
            ->assertSee('Scoped Galaxy branches')
            ->assertSee('active')
            ->assertSee('draft');
    }

    public function test_shop_scoped_admin_sees_role_mutation_actions_disabled_in_roles_workspace(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Role Workspace Branch',
            'code' => 'galaxy-scoped-role-workspace-branch',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $operatorRole = Role::create([
            'name' => 'Scoped Role Workspace Operator',
            'slug' => 'scoped-role-workspace-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Review scoped role workspace',
            'slug' => 'review-scoped-role-workspace',
            'review_note' => 'Phase 1 scoped role workspace action gating coverage.',
        ]);
        $operatorRole->permissions()->attach($permission->id);
        $user->roles()->attach($operatorRole->id);

        $role = Role::create([
            'name' => 'Shop Manager',
            'slug' => 'shop-manager-scoped-workspace',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('admin.roles-permissions.index', ['role' => $role]));

        $response
            ->assertOk()
            ->assertSee('Only bootstrap admins can reshape Galaxy access shells while Phase 1 keeps the access foundation under central control.')
            ->assertSee('Review the selected Galaxy role identity in the shared live form while Phase 1 keeps access-shell changes under bootstrap control.')
            ->assertSee('Back to access catalog')
            ->assertSee('>Save access changes<', false)
            ->assertSee('disabled title="Only bootstrap admins can reshape Galaxy access shells while Phase 1 keeps the access foundation under central control." aria-disabled="true"', false)
            ->assertSee('name="name"', false)
            ->assertSee('readonly', false)
            ->assertSee('aria-readonly="true"', false)
            ->assertSee('name="is_active"', false)
            ->assertSee('aria-disabled="true"', false);
    }

    public function test_roles_permissions_page_surfaces_selected_role_context_from_laravel_data(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-selected-role',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Shop Manager',
            'slug' => 'shop-manager-selected-role',
            'is_active' => true,
            'review_note' => 'Keep this role aligned with the legacy branch manager workflow during parity review.',
            'access_note' => 'Confirm branch access handoff before operators rely on this live role shell.',
            'assignment_note' => 'Keep assignment rollout review-only until branch staff mapping is verified.',
        ]);

        $permission = Permission::create([
            'name' => 'Manage cards',
            'slug' => 'manage-cards-selected-role',
            'review_note' => 'Keep card-access scope parity visible before any matrix rewrite is trusted.',
        ]);

        $role->permissions()->attach($permission->id);

        $assignedUser = User::factory()->create([
            'name' => 'Nare Gevorgyan',
            'shop_id' => $shop->id,
        ]);

        $assignedUser->roles()->attach($role->id);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions?role='.$role->id);

        $response
            ->assertOk()
            ->assertSee('Back to role catalog')
            ->assertSee('href="/admin/roles-permissions"', false)
            ->assertSee('Reviewing: Shop Manager')
            ->assertSee('Review matrix')
            ->assertSee('Blocked until this assignment-sensitive Galaxy foundation permission bundle is verified against legacy staff access.')
            ->assertSee('Publish access')
            ->assertSee('Blocked until assignment-sensitive live role parity is verified for this Galaxy foundation permission bundle.')
            ->assertSee('Selected role')
            ->assertSee('Role slug')
            ->assertSee('shop-manager-selected-role')
            ->assertSee('Edit Galaxy role in Galaxy foundation')
            ->assertSee('Save access changes')
            ->assertSee('Create new Galaxy access shell')
            ->assertSee('href="/admin/roles-permissions#live-form"', false)
            ->assertSee('action="/admin/roles-permissions/'.$role->id.'"', false)
            ->assertSee('Galaxy status')
            ->assertSee('selected>Active</option>', false)
            ->assertSee('Review note')
            ->assertSee('Keep this role aligned with the legacy branch manager workflow during parity review.')
            ->assertSee('Access note')
            ->assertSee('Confirm branch access handoff before operators rely on this live role shell.')
            ->assertSee('Assignment note')
            ->assertSee('Keep assignment rollout review-only until branch staff mapping is verified.')
            ->assertSee('Coverage signal')
            ->assertSee('scope, staff, and permission coverage visible')
            ->assertSee('Access focus')
            ->assertSee('Start with visible scope, assigned staff, and permission bundle overlap before discussing any later matrix editing flow.')
            ->assertSee('Access posture')
            ->assertSee('Keep access review in the live workspace first, then leave matrix edits and scope writes gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep shop scope, assigned staff, and visible permission bundle entries together before trusting any later matrix view.')
            ->assertSee('Handoff signal')
            ->assertSee('Live role already carries scope, staffing, and permission coverage for a useful access handoff review.')
            ->assertSee('Backend gap')
            ->assertSee('Role assignment, matrix editing, and shop-scoped authorization writes should stay preview-only until access parity is verified.')
            ->assertSee('Scope rollout')
            ->assertSee('Shop scope visible in review')
            ->assertSee('Publish posture')
            ->assertSee('Assignment-sensitive live bundle')
            ->assertSee('Review mode')
            ->assertSee('Live-impact review, linked staff or permissions already exist in the Galaxy foundation layer')
            ->assertSee('Operational readiness')
            ->assertSee('assignment-sensitive live role')
            ->assertSee('Lifecycle freshness')
            ->assertSee('newly created in Galaxy foundation review')
            ->assertSee('Last saved in Galaxy foundation')
            ->assertSee('Review note')
            ->assertSee('Keep this role aligned with the legacy branch manager workflow during parity review.')
            ->assertSee('Review freshness')
            ->assertSee('First review note is already saved on the initial Galaxy foundation access shell.')
            ->assertSee('Scope')
            ->assertSee('Scope coverage')
            ->assertSee('1 shop visible in Galaxy foundation review')
            ->assertSee('Scope rollout posture')
            ->assertSee('Shop scope is visible in Galaxy foundation review, but scope writes should stay parity-first until the next thin access slice is ready.')
            ->assertSee('Shop scope preview')
            ->assertSee('Galaxy Central')
            ->assertSee('Scope guidance')
            ->assertSee('This role already has visible shop scope in the Galaxy foundation layer, so any scope change should be treated as a parity-sensitive access change.')
            ->assertSee('Assigned users')
            ->assertSee('Assigned staff preview')
            ->assertSee('Nare Gevorgyan')
            ->assertSee('Assignment branch activity signal')
            ->assertSee('paused-branch assignment coverage is still pending for parity review')
            ->assertSee('Assignment guidance')
            ->assertSee('Assigned staff are already linked in the Galaxy foundation layer, so scope and permission changes should be reviewed against real operator impact.')
            ->assertSee('Permission count')
            ->assertSee('Permission coverage')
            ->assertSee('Live bundle present, review changes as parity-sensitive access coverage.')
            ->assertSee('Scoped permission signal')
            ->assertSee('1 scoped shops are already visible for this permission-linked role in parity review')
            ->assertSee('Permission branch activity signal')
            ->assertSee('paused-branch permission-linked staff coverage is still pending for parity review')
            ->assertSee('Permission bundle')
            ->assertSee('Manage cards')
            ->assertSee('Permission review note')
            ->assertSee('Keep card-access scope parity visible before any matrix rewrite is trusted.')
            ->assertSee('Galaxy status')
            ->assertSee('Access guidance')
            ->assertSee('This role already carries a Galaxy foundation permission bundle, so assignment and scope changes should stay parity-first until the matrix editor is verified.')
            ->assertSee('Shop Manager selected for Galaxy review')
            ->assertSee('Current request')
            ->assertSee('The shared roles-permissions workspace is now loading this saved role from the Galaxy foundation layer instead of only static preview rows.')
            ->assertSee('Shop Manager status reflected from model state')
            ->assertSee('This role is currently marked as active in the Galaxy foundation layer and the management context now treats it as a live access shell.')
            ->assertSee('Shop Manager lifecycle freshness reflected from model state')
            ->assertSee('This role was created in the Galaxy foundation layer on')
            ->assertSee('and has not been updated since, so operators are still reviewing the first saved access shell.')
            ->assertSee('Shop Manager last saved timestamp reflected from model state')
            ->assertSee('The latest saved Galaxy foundation timestamp for this role is')
            ->assertSee('giving operators a concrete checkpoint for the current access shell.')
            ->assertSee('Shop Manager review note reflected from model state')
            ->assertSee('The current Galaxy foundation review note says: Keep this role aligned with the legacy branch manager workflow during parity review.')
            ->assertSee('Shop Manager access note reflected from model state')
            ->assertSee('The current Galaxy foundation access note says: Confirm branch access handoff before operators rely on this live role shell.')
            ->assertSee('Shop Manager assignment note reflected from model state')
            ->assertSee('The current Galaxy foundation assignment note says: Keep assignment rollout review-only until branch staff mapping is verified.')
            ->assertSee('Shop Manager scope posture reflected from model state')
            ->assertSee('This role currently shows shop scope across Galaxy Central in Galaxy foundation review mode, so scope rollout stays visible while writes remain gated.')
            ->assertSee('Shop Manager scope coverage reflected from model state')
            ->assertSee('This role currently exposes shop scope across Galaxy Central in Galaxy foundation review, giving operators one visible branch to compare before any scope writes are enabled.')
            ->assertSee('Shop Manager permission bundle reflected from model state')
            ->assertSee('Shop Manager permission review note reflected from model state')
            ->assertSee('The current Galaxy foundation permission guidance says: Keep card-access scope parity visible before any matrix rewrite is trusted.')
            ->assertSee('Shop Manager assignment scope reflected from model state')
            ->assertSee('This role is currently linked to 1 assigned users across Galaxy Central in Galaxy foundation review mode.')
            ->assertSee('Access handoff stays visible in the workspace')
            ->assertSee('Operators should carry visible shop scope, assigned staff, and permission-bundle coverage in the live workspace before trusting any publish or matrix-edit follow-up.')
            ->assertSee('Manage cards')
            ->assertSee('Review matrix')
            ->assertSee('Blocked until this assignment-sensitive Galaxy foundation permission bundle is verified against legacy staff access.')
            ->assertSee('Publish access')
            ->assertSee('Blocked until assignment-sensitive live role parity is verified for this Galaxy foundation permission bundle.')
            ->assertSee('Review posture:')
            ->assertSee('Selected-role review is running in Galaxy foundation-backed read mode only')
            ->assertSee('Status posture:')
            ->assertSee('This role is active in the Galaxy foundation layer now, but live-facing access changes should still stay parity-first until assignment and matrix flows are verified.')
            ->assertSee('Lifecycle freshness:')
            ->assertSee('newly created in Galaxy foundation review')
            ->assertSee('Last saved in Galaxy foundation:')
            ->assertSee('Review note:')
            ->assertSee('Keep this role aligned with the legacy branch manager workflow during parity review.')
            ->assertSee('Review freshness:')
            ->assertSee('First review note is already saved on the initial Galaxy foundation access shell.')
            ->assertSee('Access note:')
            ->assertSee('Confirm branch access handoff before operators rely on this live role shell.')
            ->assertSee('Assignment note:')
            ->assertSee('Keep assignment rollout review-only until branch staff mapping is verified.')
            ->assertSee('Coverage signal:')
            ->assertSee('scope, staff, and permission coverage visible')
            ->assertSee('Role status signal')
            ->assertSee('Active role is already visible with scope, staffing, and permission coverage for live-access parity review.')
            ->assertSee('Handoff signal:')
            ->assertSee('Live role already carries scope, staffing, and permission coverage for a useful access handoff review.')
            ->assertSee('Scope rollout posture:')
            ->assertSee('This role already shows shop scope in Galaxy foundation review, but scope mutation should stay blocked until a dedicated access slice is verified.')
            ->assertSee('Scope coverage:')
            ->assertSee('1 shop visible in Galaxy foundation review')
            ->assertSee('Matrix posture:')
            ->assertSee('Keep matrix editing blocked until legacy staff-access parity is verified in the Galaxy foundation layer')
            ->assertSee('Assigned staff posture:')
            ->assertSee('Linked staff are already affected by this role in the Galaxy foundation layer, so assignment parity should be checked before any access changes move forward.')
            ->assertSee('Assignment branch activity signal:')
            ->assertSee('paused-branch assignment coverage is still pending for parity review')
            ->assertSee('Permission posture:')
            ->assertSee('The visible Galaxy foundation permission bundle is reviewable now, but bundle edits should stay blocked until legacy access mapping is verified.')
            ->assertSee('Permission review note:')
            ->assertSee('Keep card-access scope parity visible before any matrix rewrite is trusted.')
            ->assertSee('Scoped permission signal:')
            ->assertSee('1 scoped shops are already visible for this permission-linked role in parity review')
            ->assertSee('Permission branch activity signal:')
            ->assertSee('paused-branch permission-linked staff coverage is still pending for parity review')
            ->assertSee('Publish posture:')
            ->assertSee('This live permission bundle still needs assignment parity checks before publish-style role changes are safe.')
            ->assertSee('Scope posture:')
            ->assertSee('Assigned shops are visible for review, but scope writes should stay parity-first until staff assignment rules are confirmed.')
            ->assertSee('Handoff signal:')
            ->assertSee('Live role already carries scope, staffing, and permission coverage for a useful access handoff review.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Role assignment, matrix editing, and shop-scoped authorization writes should stay preview-only until access parity is verified.');
    }

    public function test_roles_permissions_page_supports_selected_mixed_branch_permission_review_context(): void
    {
        $activeShop = Shop::create([
            'name' => 'Galaxy Access Active Branch',
            'code' => 'galaxy-access-active-branch',
            'is_active' => true,
        ]);

        $pausedShop = Shop::create([
            'name' => 'Galaxy Access Paused Branch',
            'code' => 'galaxy-access-paused-branch',
            'is_active' => false,
        ]);

        $role = Role::create([
            'name' => 'Branch Access Supervisor',
            'slug' => 'branch-access-supervisor',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'Review branch access',
            'slug' => 'review-branch-access',
        ]);

        $role->permissions()->attach($permission->id);

        $activeAssignedUser = User::factory()->create([
            'name' => 'Anna Activebranch',
            'shop_id' => $activeShop->id,
        ]);

        $pausedAssignedUser = User::factory()->create([
            'name' => 'Paul Pausedbranch',
            'shop_id' => $pausedShop->id,
        ]);

        $activeAssignedUser->roles()->attach($role->id);
        $pausedAssignedUser->roles()->attach($role->id);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions?role='.$role->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: Branch Access Supervisor')
            ->assertSee('Assignment branch activity signal')
            ->assertSee('1 assigned staff are already visible in active branches beside 1 assigned staff in paused shops for parity review')
            ->assertSee('Scoped permission signal')
            ->assertSee('2 scoped shops are already visible for this permission-linked role in parity review')
            ->assertSee('Permission branch activity signal')
            ->assertSee('1 permission-linked staff are already visible in active branches beside 1 permission-linked staff in paused shops for parity review')
            ->assertSee('Access focus')
            ->assertSee('Start with visible scope, assigned staff, and permission bundle overlap before discussing any later matrix editing flow.')
            ->assertSee('Access posture')
            ->assertSee('Keep access review in the live workspace first, then leave matrix edits and scope writes gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep shop scope, assigned staff, and visible permission bundle entries together before trusting any later matrix view.')
            ->assertSee('Handoff signal')
            ->assertSee('Live role already carries scope, staffing, and permission coverage for a useful access handoff review.')
            ->assertSee('Access handoff stays visible in the workspace')
            ->assertSee('Operators should carry visible shop scope, assigned staff, and permission-bundle coverage in the live workspace before trusting any publish or matrix-edit follow-up.')
            ->assertSee('Backend gap')
            ->assertSee('Role assignment, matrix editing, and shop-scoped authorization writes should stay preview-only until access parity is verified.')
            ->assertSee('Implementation dependencies')
            ->assertSee('Assignment branch activity signal:')
            ->assertSee('1 assigned staff are already visible in active branches beside 1 assigned staff in paused shops for parity review')
            ->assertSee('Scoped permission signal:')
            ->assertSee('2 scoped shops are already visible for this permission-linked role in parity review')
            ->assertSee('Permission branch activity signal:')
            ->assertSee('1 permission-linked staff are already visible in active branches beside 1 permission-linked staff in paused shops for parity review')
            ->assertSee('Handoff signal:')
            ->assertSee('Live role already carries scope, staffing, and permission coverage for a useful access handoff review.');
    }

    public function test_selected_draft_role_shows_readiness_driven_action_gating_reasons(): void
    {
        $role = Role::create([
            'name' => 'Draft Branch Auditor',
            'slug' => 'draft-branch-auditor',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions?role='.$role->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: Draft Branch Auditor')
            ->assertSee('Review matrix')
            ->assertSee('Blocked until this draft role has a first verified Galaxy foundation permission bundle to compare against legacy staff access.')
            ->assertSee('Publish access')
            ->assertSee('Blocked until this draft role has a verified permission bundle and shop scope parity.')
            ->assertSee('Role status signal')
            ->assertSee('Draft role remains safer for access-rollout parity review before any live-access discussion.')
            ->assertSee('Access focus')
            ->assertSee('Start with draft status, visible scope gaps, and permission bundle gaps before discussing any later matrix editing flow.')
            ->assertSee('Access posture')
            ->assertSee('Keep draft role review in the workspace first, then leave matrix edits, scope writes, and activation flows gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep draft status, scope gaps, and permission bundle gaps together before trusting any later matrix or publish discussion.')
            ->assertSee('Access handoff stays visible in the workspace')
            ->assertSee('Operators should carry draft status, scope gaps, and permission-bundle gaps in the live workspace before trusting any publish or matrix-edit follow-up.')
            ->assertSee('Handoff signal')
            ->assertSee('Draft role should stay in handoff-only posture until review note, bundle, and scope parity are explicit.')
            ->assertSee('Backend gap')
            ->assertSee('Draft activation, first permission-bundle wiring, and shop-scoped authorization writes should stay preview-only until access parity is verified.')
            ->assertSee('Review freshness')
            ->assertSee('Draft role still needs a saved review note before parity handoff can feel grounded.')
            ->assertSee('Role status signal:')
            ->assertSee('Draft role remains safer for access-rollout parity review before any live-access discussion.')
            ->assertSee('Handoff signal:')
            ->assertSee('Draft role should stay in handoff-only posture until review note, bundle, and scope parity are explicit.')
            ->assertSee('Review freshness:')
            ->assertSee('Draft role still needs a saved review note before parity handoff can feel grounded.');
    }

    public function test_selected_active_role_without_permission_bundle_shows_readiness_driven_action_gating_reasons(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Scope Branch',
            'code' => 'galaxy-scope-branch-no-bundle',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Scoped Floor Lead',
            'slug' => 'scoped-floor-lead-no-bundle',
            'is_active' => true,
        ]);

        $assignedUser = User::factory()->create([
            'name' => 'Ani Scopeyan',
            'shop_id' => $shop->id,
        ]);

        $assignedUser->roles()->attach($role->id);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions?role='.$role->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: Scoped Floor Lead')
            ->assertSee('Review matrix')
            ->assertSee('Blocked until this active role has a first verified Galaxy foundation permission bundle to compare against legacy staff access.')
            ->assertSee('Publish access')
            ->assertSee('Blocked until this active role has a verified permission bundle to compare against legacy staff access.');
    }

    public function test_selected_live_permission_bundle_without_scope_shows_readiness_driven_action_gating_reasons(): void
    {
        $role = Role::create([
            'name' => 'Central Audit Lead',
            'slug' => 'central-audit-lead-live-bundle',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'Audit holders',
            'slug' => 'audit-holders-live-bundle',
        ]);

        $role->permissions()->attach($permission->id);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions?role='.$role->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: Central Audit Lead')
            ->assertSee('Review matrix')
            ->assertSee('Blocked until the Galaxy foundation permission matrix can be verified against legacy staff access for this live bundle.')
            ->assertSee('Publish access')
            ->assertSee('Blocked until this live permission bundle also has verified shop scope parity.');
    }

    public function test_selected_assignment_sensitive_live_role_without_scope_surfaces_access_review_context(): void
    {
        $role = Role::create([
            'name' => 'Shift Access Lead',
            'slug' => 'shift-access-lead-no-scope',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'Manage shifts',
            'slug' => 'manage-shifts-no-scope',
        ]);

        $role->permissions()->attach($permission->id);

        $assignedUser = User::factory()->create([
            'name' => 'Levon Shiftyan',
        ]);

        $assignedUser->roles()->attach($role->id);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions?role='.$role->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: Shift Access Lead')
            ->assertSee('Review matrix')
            ->assertSee('Blocked until this assignment-sensitive Galaxy foundation permission bundle is verified against legacy staff access.')
            ->assertSee('Publish access')
            ->assertSee('Blocked until this live permission bundle also has verified shop scope parity.')
            ->assertSee('Coverage signal')
            ->assertSee('staff and permission coverage visible, scope pending')
            ->assertSee('Access handoff stays visible in the workspace')
            ->assertSee('Operators should carry assigned staff, permission-bundle coverage, and scope gaps in the live workspace before trusting any publish or matrix-edit follow-up.')
            ->assertSee('Assigned staff preview')
            ->assertSee('Levon Shiftyan')
            ->assertSee('Scope guidance')
            ->assertSee('No shop scope is linked yet, which keeps this role safer for draft review before scope parity is confirmed.')
            ->assertSee('Assignment guidance')
            ->assertSee('Assigned staff are already linked in the Galaxy foundation layer, so scope and permission changes should be reviewed against real operator impact.');
    }

    public function test_roles_permissions_page_ignores_unknown_selected_role_query(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-unknown-role',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Shop Manager',
            'slug' => 'shop-manager-unknown-role',
        ]);

        $permission = Permission::create([
            'name' => 'Manage cards',
            'slug' => 'manage-cards-unknown-role',
        ]);

        $role->permissions()->attach($permission->id);

        $assignedUser = User::factory()->create([
            'shop_id' => $shop->id,
        ]);

        $assignedUser->roles()->attach($role->id);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions?role=999999');

        $response
            ->assertOk()
            ->assertSee('Shop Manager')
            ->assertSee('Review latest saved access shell')
            ->assertDontSee('Back to role catalog')
            ->assertDontSee('Selected role')
            ->assertDontSee('selected for Laravel review');
    }

    public function test_roles_permissions_page_ignores_malformed_selected_role_query(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-malformed-role',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Shop Manager',
            'slug' => 'shop-manager-malformed-role',
        ]);

        $permission = Permission::create([
            'name' => 'Manage cards',
            'slug' => 'manage-cards-malformed-role',
        ]);

        $role->permissions()->attach($permission->id);

        $assignedUser = User::factory()->create([
            'shop_id' => $shop->id,
        ]);

        $assignedUser->roles()->attach($role->id);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions?role=not-a-number');

        $response
            ->assertOk()
            ->assertSee('Shop Manager')
            ->assertSee('Review latest saved access shell')
            ->assertDontSee('Back to role catalog')
            ->assertDontSee('Selected role')
            ->assertDontSee('selected for Laravel review');
    }

    public function test_authenticated_user_can_create_role_from_minimal_live_admin_flow(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.roles-permissions.store'), [
            'name' => '  Branch Supervisor  ',
            'slug' => 'Branch Supervisor',
            'is_active' => '1',
            'review_note' => 'Start with the branch manager shell and keep assignment changes blocked.',
            'access_note' => 'Confirm staff access handoff before operators rely on this live role shell.',
            'assignment_note' => 'Keep assignment rollout blocked until branch staff parity is reviewed.',
        ]);

        $role = Role::query()->where('name', 'Branch Supervisor')->firstOrFail();

        $response
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Role "Branch Supervisor" was created.');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Branch Supervisor',
            'slug' => 'branch-supervisor',
            'is_active' => true,
            'review_note' => 'Start with the branch manager shell and keep assignment changes blocked.',
            'access_note' => 'Confirm staff access handoff before operators rely on this live role shell.',
            'assignment_note' => 'Keep assignment rollout blocked until branch staff parity is reviewed.',
        ]);
    }

    public function test_role_live_admin_form_returns_operator_friendly_validation_messages(): void
    {
        $user = User::factory()->create();

        Role::create([
            'name' => 'Shop Manager',
            'slug' => 'shop-manager',
        ]);

        $response = $this->from(route('admin.roles-permissions.index'))->actingAs($user)->post(route('admin.roles-permissions.store'), [
            'name' => 'Shop Manager Copy',
            'slug' => 'shop-manager',
        ]);

        $response
            ->assertRedirect(route('admin.roles-permissions.index').'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'This role slug is already in use.',
            ]);

        $longReviewNote = str_repeat('a', 1001);

        $reviewNoteResponse = $this->from(route('admin.roles-permissions.index'))->actingAs($user)->post(route('admin.roles-permissions.store'), [
            'name' => 'Shop Manager Review Copy',
            'slug' => 'shop-manager-review-copy',
            'is_active' => '0',
            'review_note' => $longReviewNote,
        ]);

        $reviewNoteResponse
            ->assertRedirect(route('admin.roles-permissions.index').'#live-form')
            ->assertSessionHasErrors([
                'review_note' => 'Keep the review note under 1000 characters so the role workspace stays operator-friendly.',
            ]);

        $longAccessNote = str_repeat('x', 1001);

        $accessNoteResponse = $this->from(route('admin.roles-permissions.index'))->actingAs($user)->post(route('admin.roles-permissions.store'), [
            'name' => 'Shop Manager Access Copy',
            'slug' => 'shop-manager-access-copy',
            'is_active' => '0',
            'access_note' => $longAccessNote,
        ]);

        $accessNoteResponse
            ->assertRedirect(route('admin.roles-permissions.index').'#live-form')
            ->assertSessionHasErrors([
                'access_note' => 'Keep the access note under 1000 characters so the role workspace stays operator-friendly.',
            ]);

        $longAssignmentNote = str_repeat('z', 1001);

        $assignmentNoteResponse = $this->from(route('admin.roles-permissions.index'))->actingAs($user)->post(route('admin.roles-permissions.store'), [
            'name' => 'Shop Manager Assignment Copy',
            'slug' => 'shop-manager-assignment-copy',
            'is_active' => '0',
            'assignment_note' => $longAssignmentNote,
        ]);

        $assignmentNoteResponse
            ->assertRedirect(route('admin.roles-permissions.index').'#live-form')
            ->assertSessionHasErrors([
                'assignment_note' => 'Keep the assignment note under 1000 characters so the role workspace stays operator-friendly.',
            ]);
    }

    public function test_role_create_validation_redirects_to_index_without_referrer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.roles-permissions.store'), [
            'name' => '',
            'slug' => 'invalid slug',
        ]);

        $response
            ->assertRedirect(route('admin.roles-permissions.index').'#live-form')
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseCount('roles', 0);
    }

    public function test_shop_scoped_admin_cannot_create_new_role(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Role Creator Branch',
            'code' => 'galaxy-scoped-role-creator-branch',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Role Creator Operator',
            'slug' => 'scoped-role-creator-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Create scoped roles',
            'slug' => 'create-scoped-roles',
            'review_note' => 'Phase 1 scoped role create coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->from(route('admin.roles-permissions.index'))->actingAs($user)->post(route('admin.roles-permissions.store'), [
            'name' => 'Scoped Unauthorized Role',
            'slug' => 'scoped-unauthorized-role',
            'is_active' => '1',
            'review_note' => 'Scoped admins should not create new roles during Phase 1.',
        ]);

        $response
            ->assertRedirect(route('admin.roles-permissions.index').'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'Only bootstrap admins can create new roles while the Galaxy access foundation is still centrally controlled.',
            ]);

        $this->assertDatabaseMissing('roles', [
            'slug' => 'scoped-unauthorized-role',
        ]);
    }

    public function test_shop_scoped_admin_cannot_update_role(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Role Update Branch',
            'code' => 'galaxy-scoped-role-update-branch',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $operatorRole = Role::create([
            'name' => 'Scoped Role Update Operator',
            'slug' => 'scoped-role-update-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Update scoped roles',
            'slug' => 'update-scoped-roles',
            'review_note' => 'Phase 1 scoped role update coverage.',
        ]);
        $operatorRole->permissions()->attach($permission->id);
        $user->roles()->attach($operatorRole->id);

        $roleToUpdate = Role::create([
            'name' => 'Galaxy Protected Role',
            'slug' => 'galaxy-protected-role',
            'is_active' => true,
        ]);

        $response = $this->from(route('admin.roles-permissions.index', ['role' => $roleToUpdate], absolute: false))->actingAs($user)->patch(route('admin.roles-permissions.update', $roleToUpdate), [
            'name' => 'Scoped Updated Role',
            'slug' => 'scoped-updated-role',
            'is_active' => '0',
        ]);

        $response
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $roleToUpdate], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'Only bootstrap admins can update roles while the Galaxy access foundation is still centrally controlled.',
            ]);

        $this->assertDatabaseHas('roles', [
            'id' => $roleToUpdate->id,
            'name' => 'Galaxy Protected Role',
            'slug' => 'galaxy-protected-role',
            'is_active' => true,
        ]);
    }

    public function test_authenticated_user_can_update_role_from_minimal_live_admin_flow(): void
    {
        $user = User::factory()->create();
        $role = Role::create([
            'name' => 'Branch Supervisor',
            'slug' => 'branch-supervisor',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.roles-permissions.update', $role), [
            'name' => '  Branch Operations Lead  ',
            'slug' => 'Branch Operations Lead',
            'is_active' => '1',
            'review_note' => 'Document the first live Galaxy foundation role adjustments before widening scope.',
            'access_note' => 'Keep access handoff visible while this role remains under parity review.',
            'assignment_note' => 'Keep assignment rollout review-only while this role remains under parity review.',
        ]);

        $response
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Role "Branch Operations Lead" was updated.');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Branch Operations Lead',
            'slug' => 'branch-operations-lead',
            'is_active' => true,
            'review_note' => 'Document the first live Galaxy foundation role adjustments before widening scope.',
            'access_note' => 'Keep access handoff visible while this role remains under parity review.',
            'assignment_note' => 'Keep assignment rollout review-only while this role remains under parity review.',
        ]);
    }

    public function test_role_live_flow_trims_role_identity_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.roles-permissions.store'), [
            'name' => '  Shift Access Reviewer  ',
            'slug' => 'Shift Access Reviewer',
            'is_active' => '0',
            'review_note' => 'Trim the first live role shell before widening scope or matrix writes.',
        ]);

        $role = Role::query()->where('slug', 'shift-access-reviewer')->firstOrFail();

        $response
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Role "Shift Access Reviewer" was created.');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Shift Access Reviewer',
            'slug' => 'shift-access-reviewer',
            'is_active' => false,
        ]);
    }

    public function test_role_live_flow_normalizes_blank_notes_to_null(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.roles-permissions.store'), [
            'name' => 'Parity Access Reviewer',
            'slug' => 'Parity Access Reviewer',
            'is_active' => '0',
            'review_note' => '   ',
            'access_note' => '   ',
            'assignment_note' => '   ',
        ]);

        $role = Role::query()->where('slug', 'parity-access-reviewer')->firstOrFail();

        $response
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#backend-flow-status');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'review_note' => null,
            'access_note' => null,
            'assignment_note' => null,
        ]);
    }

    public function test_role_update_live_flow_normalizes_blank_notes_to_null(): void
    {
        $user = User::factory()->create();
        $role = Role::create([
            'name' => 'Parity Access Lead',
            'slug' => 'parity-access-lead',
            'is_active' => true,
            'review_note' => 'Clear this review note while keeping the access shell live.',
            'access_note' => 'Clear this access note after parity review.',
            'assignment_note' => 'Clear this assignment note after staffing review.',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.roles-permissions.update', $role), [
            'name' => 'Parity Access Lead',
            'slug' => 'Parity Access Lead',
            'is_active' => '1',
            'review_note' => '   ',
            'access_note' => '   ',
            'assignment_note' => '   ',
        ]);

        $response
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Role "Parity Access Lead" was updated.');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'slug' => 'parity-access-lead',
            'review_note' => null,
            'access_note' => null,
            'assignment_note' => null,
        ]);
    }

    public function test_role_update_allows_reusing_current_slug_but_rejects_other_existing_slug(): void
    {
        $user = User::factory()->create();
        $role = Role::create([
            'name' => 'Branch Supervisor',
            'slug' => 'branch-supervisor',
        ]);
        $otherRole = Role::create([
            'name' => 'Cashier',
            'slug' => 'cashier',
        ]);

        $okResponse = $this->actingAs($user)->patch(route('admin.roles-permissions.update', $role), [
            'name' => 'Branch Supervisor Updated',
            'slug' => 'branch-supervisor',
        ]);

        $okResponse
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Role "Branch Supervisor Updated" was updated.');

        $errorResponse = $this->from(route('admin.roles-permissions.index'))->actingAs($user)->patch(route('admin.roles-permissions.update', $role), [
            'name' => 'Branch Supervisor Updated Again',
            'slug' => $otherRole->slug,
        ]);

        $errorResponse
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'This role slug is already in use.',
            ]);

        $longReviewNote = str_repeat('b', 1001);

        $reviewNoteErrorResponse = $this->from(route('admin.roles-permissions.index', ['role' => $role], absolute: false))->actingAs($user)->patch(route('admin.roles-permissions.update', $role), [
            'name' => 'Branch Supervisor Updated Again',
            'slug' => 'branch-supervisor',
            'is_active' => '1',
            'review_note' => $longReviewNote,
        ]);

        $reviewNoteErrorResponse
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'review_note' => 'Keep the review note under 1000 characters so the role workspace stays operator-friendly.',
            ]);

        $longAccessNote = str_repeat('y', 1001);

        $accessNoteErrorResponse = $this->from(route('admin.roles-permissions.index', ['role' => $role], absolute: false))->actingAs($user)->patch(route('admin.roles-permissions.update', $role), [
            'name' => 'Branch Supervisor Updated Again',
            'slug' => 'branch-supervisor',
            'is_active' => '1',
            'access_note' => $longAccessNote,
        ]);

        $accessNoteErrorResponse
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'access_note' => 'Keep the access note under 1000 characters so the role workspace stays operator-friendly.',
            ]);

        $longAssignmentNote = str_repeat('q', 1001);

        $assignmentNoteErrorResponse = $this->from(route('admin.roles-permissions.index', ['role' => $role], absolute: false))->actingAs($user)->patch(route('admin.roles-permissions.update', $role), [
            'name' => 'Branch Supervisor Updated Again',
            'slug' => 'branch-supervisor',
            'is_active' => '1',
            'assignment_note' => $longAssignmentNote,
        ]);

        $assignmentNoteErrorResponse
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'assignment_note' => 'Keep the assignment note under 1000 characters so the role workspace stays operator-friendly.',
            ]);
    }

    public function test_role_update_live_flow_rejects_duplicate_normalized_slug(): void
    {
        $user = User::factory()->create();

        $existingRole = Role::create([
            'name' => 'Existing Access Reviewer',
            'slug' => 'existing-access-reviewer',
            'is_active' => true,
        ]);

        $roleToUpdate = Role::create([
            'name' => 'Target Access Reviewer',
            'slug' => 'target-access-reviewer',
            'is_active' => false,
        ]);

        $response = $this->from(route('admin.roles-permissions.index', ['role' => $roleToUpdate], absolute: false))
            ->actingAs($user)
            ->patch(route('admin.roles-permissions.update', $roleToUpdate), [
                'name' => '  Target Access Reviewer  ',
                'slug' => ' Existing Access Reviewer ',
                'is_active' => '1',
                'review_note' => 'Duplicate normalized role slug should stay blocked during live edit work.',
            ]);

        $response
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $roleToUpdate], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'This role slug is already in use.',
            ]);

        $this->assertDatabaseHas('roles', [
            'id' => $existingRole->id,
            'slug' => 'existing-access-reviewer',
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $roleToUpdate->id,
            'slug' => 'target-access-reviewer',
        ]);
    }

    public function test_role_update_live_flow_keeps_role_slug_canonical(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'Galaxy Draft Access',
            'slug' => 'galaxy-draft-access',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.roles-permissions.update', $role), [
            'name' => '  Galaxy Draft Access Prime  ',
            'slug' => ' Galaxy Draft Access Prime ',
            'is_active' => '1',
            'review_note' => 'Keep role slug canonical while the live access shell stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Role "Galaxy Draft Access Prime" was updated.');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Galaxy Draft Access Prime',
            'slug' => 'galaxy-draft-access-prime',
            'is_active' => true,
        ]);
    }

    public function test_role_update_live_flow_keeps_status_boolean_canonical(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'Galaxy Boolean Access',
            'slug' => 'galaxy-boolean-access',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.roles-permissions.update', $role), [
            'name' => '  Galaxy Boolean Access Draft  ',
            'slug' => ' Galaxy Boolean Access Draft ',
            'is_active' => 'no',
            'review_note' => 'Keep role status canonical while the live access shell stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Role "Galaxy Boolean Access Draft" was updated.');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Galaxy Boolean Access Draft',
            'slug' => 'galaxy-boolean-access-draft',
            'is_active' => false,
        ]);
    }

    public function test_role_live_flow_keeps_status_boolean_canonical(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.roles-permissions.store'), [
            'name' => '  Galaxy Boolean Access Create  ',
            'slug' => ' Galaxy Boolean Access Create ',
            'is_active' => 'no',
            'review_note' => 'Keep role status canonical while the first live access shell stays narrow.',
        ]);

        $role = Role::query()->where('slug', 'galaxy-boolean-access-create')->firstOrFail();

        $response
            ->assertRedirect(route('admin.roles-permissions.index', ['role' => $role], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Role "Galaxy Boolean Access Create" was created.');

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Galaxy Boolean Access Create',
            'slug' => 'galaxy-boolean-access-create',
            'is_active' => false,
        ]);
    }

    public function test_roles_permissions_page_shows_update_success_flash_message(): void
    {
        $user = User::factory()->create();
        $role = Role::create([
            'name' => 'Branch Operations Lead',
            'slug' => 'branch-operations-lead-flash',
            'access_note' => 'Keep access handoff visible while this role remains under parity review.',
            'assignment_note' => 'Keep assignment rollout review-only while this role remains under parity review.',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['status' => 'Role "Branch Operations Lead" was updated.'])
            ->get(route('admin.roles-permissions.index', ['role' => $role]));

        $response
            ->assertOk()
            ->assertSee('id="backend-flow-status"', false)
            ->assertSee('Backend flow checkpoint')
            ->assertSee('Access-shell changes are now visible in the Galaxy foundation-backed workspace.')
            ->assertSee('Role "Branch Operations Lead" was updated.')
            ->assertSee('Reviewing: Branch Operations Lead')
            ->assertSee('Latest backend write result')
            ->assertSee('Latest flow result:')
            ->assertSee('Access note:')
            ->assertSee('Keep access handoff visible while this role remains under parity review.')
            ->assertSee('Assignment note:')
            ->assertSee('Keep assignment rollout review-only while this role remains under parity review.')
            ->assertSee('Role "Branch Operations Lead" was updated.');
    }

    public function test_authenticated_user_can_access_cards_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards');

        $response
            ->assertOk()
            ->assertSee('Cards placeholder')
            ->assertSee('Galaxy card-shell workspace for inventory, assignment review, status triage, and activation tracking.')
            ->assertSee('Operational index shape')
            ->assertSee('GX-100001')
            ->assertSee('Central Shop')
            ->assertSee('New Galaxy card')
            ->assertSee('Review blocked cards')
            ->assertSee('Blocked until the first Galaxy foundation-backed inventory slice exists for blocked-card parity review.')
            ->assertSee('Active Galaxy card shells')
            ->assertSee('Draft Galaxy card shells')
            ->assertSee('Blocked Galaxy card shells')
            ->assertSee('Issued Galaxy card shells')
            ->assertSee('Pre-activation cards')
            ->assertSee('Card operations are still preview-only')
            ->assertSee('Inventory actions, status metrics, and filters are laid out for Galaxy parity, but they are not connected to Laravel handlers yet.')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview inventory statuses and card-type filters are defined')
            ->assertSee('Inventory queries and card lifecycle handlers still need PHP-backed Laravel wiring')
            ->assertSee('Implementation dependencies')
            ->assertSee('Card and CardType models plus migration skeletons exist')
            ->assertSee('Inventory reads, assignment flows, and status mutations are still pending')
            ->assertSee('First Galaxy foundation wiring step')
            ->assertSee('When PHP is available, start with a read-only inventory table before exposing issue, block, or assignment flows.')
            ->assertSee('Load cards with holder, type, status, issue timing, and activation timing columns.')
            ->assertSee('The timestamp when an already issued physical or virtual card became usable in the loyalty flow.')
            ->assertSee('Recent activity preview')
            ->assertSee('Blocked card state kept visible')
            ->assertSee('Draft card review deferred')
            ->assertSee('Legacy parity mapping')
            ->assertSee('Old Galaxy card inventory screen')
            ->assertSee('Card states, holder linkage, issue and activation visibility')
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
            ->assertSee('2026-04-08')
            ->assertSee('2026-04-10')
            ->assertSee('2026-03-20')
            ->assertSee('2026-03-28')
            ->assertSee('Card type')
            ->assertSee('Issue/activation period');
    }

    public function test_cards_catalog_actions_reflect_saved_inventory_readiness(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central Cards Catalog',
            'code' => 'galaxy-central-cards-catalog',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Catalog Gold',
            'slug' => 'catalog-gold',
            'points_rate' => 1.50,
            'is_active' => true,
        ]);

        Card::create([
            'number' => 'GX-CATALOG-1001',
            'status' => 'draft',
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
        ]);

        Card::create([
            'number' => 'GX-CATALOG-1002',
            'status' => 'blocked',
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards');

        $response
            ->assertOk()
            ->assertSee('New Galaxy card')
            ->assertSee('Review blocked cards')
            ->assertSee('Blocked until saved blocked-card states are verified against legacy inventory semantics.');
    }

    public function test_cards_page_replaces_preview_rows_with_model_backed_inventory_data(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-cards',
            'is_active' => true,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Anna Petrova',
            'phone' => '+37491100001',
            'email' => 'anna-cards@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Gold',
            'slug' => 'galaxy-gold-cards',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-900001',
            'status' => 'active',
            'issued_at' => '2026-04-08 08:30:00',
            'review_note' => 'Keep active-card parity visible before widening replacement actions.',
            'activated_at' => '2026-04-10 10:00:00',
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'number' => 'GX-900002',
            'status' => 'draft',
            'issued_at' => '2026-04-09 09:00:00',
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-900003',
            'status' => 'blocked',
            'issued_at' => '2026-03-20 14:45:00',
            'activated_at' => null,
            'review_note' => 'Keep blocked-card dispute parity visible before any replacement follow-up is trusted.',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards');

        $response
            ->assertOk()
            ->assertSee('GX-900001')
            ->assertSee('href="/admin/cards?card=', false)
            ->assertSee('GX-900002')
            ->assertSee('GX-900003')
            ->assertSee('Anna Petrova')
            ->assertSee('Unassigned')
            ->assertSee('Galaxy Gold')
            ->assertSee('Galaxy Central')
            ->assertSee('Create Galaxy card in Galaxy foundation')
            ->assertSee('Create inventory shell')
            ->assertSee('Use the saved Galaxy foundation tier that matches the old Galaxy accrual and activation behavior.')
            ->assertSee('Use this safe Phase 1 note to record inventory review context without opening holder reassignment or replacement writes.')
            ->assertSee('action="/admin/cards"', false)
            ->assertSee('Review latest saved card shell')
            ->assertSee('Active Galaxy card shells')
            ->assertSee('Draft Galaxy card shells')
            ->assertSee('Blocked Galaxy card shells')
            ->assertSee('Issued Galaxy card shells')
            ->assertSee('Pre-activation cards')
            ->assertSee('Holder-linked cards')
            ->assertSee('Assignment-ready cards')
            ->assertSee('Assignment-pending cards')
            ->assertSee('Issued holder-linked Galaxy card shells')
            ->assertSee('Issued unassigned Galaxy card shells')
            ->assertSee('Pre-activation holder-linked Galaxy card shells')
            ->assertSee('Pre-activation unassigned Galaxy card shells')
            ->assertSee('Unassigned cards')
            ->assertSee('Active holder-linked Galaxy card shells')
            ->assertSee('Active unassigned Galaxy card shells')
            ->assertSee('Blocked pre-activation Galaxy card shells')
            ->assertSee('Blocked activated Galaxy card shells')
            ->assertSee('Blocked Galaxy card shells with holders')
            ->assertSee('Blocked unassigned Galaxy card shells')
            ->assertSee('Reviewed Galaxy card shells')
            ->assertSee('Keep active-card parity visible before widening replacement actions.')
            ->assertSee('No review note saved yet')
            ->assertSee('2026-04-08')
            ->assertSee('2026-04-09')
            ->assertSee('2026-04-10')
            ->assertSee('2026-03-20');
    }

    public function test_cards_page_surfaces_selected_card_context_from_laravel_data(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-selected-card',
            'is_active' => true,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Anna Petrova',
            'phone' => '+37491100001',
            'email' => 'anna-selected-card@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Gold',
            'slug' => 'galaxy-gold-selected-card',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $card = Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-910001',
            'status' => 'blocked',
            'issued_at' => '2026-03-20 14:45:00',
            'review_note' => 'Keep blocked-card dispute parity visible before any replacement follow-up is trusted.',
            'activated_at' => '2026-03-28 09:15:00',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Back to card catalog')
            ->assertSee('href="/admin/cards"', false)
            ->assertSee('Reviewing: GX-910001')
            ->assertSee('Edit Galaxy card in Galaxy foundation')
            ->assertSee('Save inventory changes')
            ->assertSee('Back to card catalog')
            ->assertDontSee('Create new Galaxy card shell')
            ->assertSee('action="/admin/cards/'.$card->id.'"', false)
            ->assertSee('name="issued_at"', false)
            ->assertSee('value="2026-03-20 14:45:00"', false)
            ->assertSee('name="activated_at"', false)
            ->assertSee('value="2026-03-28 09:15:00"', false)
            ->assertSee('Review blocked cards')
            ->assertSee('Blocked until this blocked holder-linked card clears dispute and replacement parity against the legacy Galaxy flow.')
            ->assertSee('Selected card')
            ->assertSee('Review mode')
            ->assertSee('Live inventory review, this saved Galaxy foundation card already carries operational state that should stay parity-first.')
            ->assertSee('Operational readiness')
            ->assertSee('blocked inventory, operator review only')
            ->assertSee('Lifecycle stage')
            ->assertSee('Issued and activated inventory already visible in the Galaxy foundation layer.')
            ->assertSee('Lifecycle freshness')
            ->assertSee('newly created in Galaxy foundation review')
            ->assertSee('Last saved in Galaxy foundation')
            ->assertSee('Issued')
            ->assertSee('2026-03-20')
            ->assertSee('Review note')
            ->assertSee('Keep blocked-card dispute parity visible before any replacement follow-up is trusted.')
            ->assertSee('Holder')
            ->assertSee('Card type')
            ->assertSee('Linkage signal')
            ->assertSee('holder and branch linkage visible')
            ->assertSee('Inventory focus')
            ->assertSee('Start with blocked status, holder linkage, and dispute context before discussing any later replacement or reassignment flow.')
            ->assertSee('Inventory posture')
            ->assertSee('Keep blocked inventory in dispute-first review, then leave replacement, reassignment, and cross-branch moves gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep blocked status, holder linkage, and dispute context visible together before trusting any later replacement or reassignment discussion.')
            ->assertSee('Inventory handoff signal')
            ->assertSee('Blocked holder-linked inventory already carries enough dispute context for a useful handoff review.')
            ->assertSee('Backend gap')
            ->assertSee('Blocked-card handling, dispute resolution, and replacement flows should stay preview-only until inventory parity is verified.')
            ->assertSee('Shop')
            ->assertSee('Shop guidance')
            ->assertSee('Keep this card tied to its current branch context during review, because cross-shop inventory handling was parity-sensitive in the old Galaxy flow.')
            ->assertSee('Galaxy status')
            ->assertSee('Activated')
            ->assertSee('Blocked pre-activation signal')
            ->assertSee('Blocked inventory already carries activation context in the Galaxy foundation layer for dispute-first review.')
            ->assertSee('Blocked activated signal')
            ->assertSee('Blocked inventory had already reached activation before dispute review in the Galaxy foundation layer.')
            ->assertSee('Blocked holder-linked signal')
            ->assertSee('Blocked inventory already carries holder linkage, so dispute and replacement review can stay anchored to the current member record.')
            ->assertSee('Blocked unassigned signal')
            ->assertSee('Blocked inventory already carries holder linkage in the Galaxy foundation layer for dispute-first review.')
            ->assertSee('Holder linkage summary')
            ->assertSee('Holder linkage is present, but this card stays in blocked dispute review until replacement parity is proven.')
            ->assertSee('Assignment readiness summary')
            ->assertSee('Holder linkage is present, but assignment changes stay dispute-gated while blocked parity is still under review.')
            ->assertSee('Assignment posture')
            ->assertSee('Blocked inventory already carries holder linkage, so dispute review can stay member-linked while replacement and reassignment remain parity-gated.')
            ->assertSee('Dispute posture')
            ->assertSee('Treat this as a dispute-first blocked card with prior activation context still visible in the Galaxy foundation layer.')
            ->assertSee('Activation readiness')
            ->assertSee('Activation is already recorded in the Galaxy foundation layer for this issued card.')
            ->assertSee('Inventory guidance')
            ->assertSee('This card is blocked in the Galaxy foundation layer, so replacement and dispute handling should remain review-only until legacy card-state parity is confirmed.')
            ->assertSee('GX-910001 selected for Galaxy review')
            ->assertSee('Current request')
            ->assertSee('The shared cards workspace is now loading this saved inventory record from the Galaxy foundation layer instead of only static preview rows.')
            ->assertSee('GX-910001 status reflected from model state')
            ->assertSee('GX-910001 lifecycle freshness reflected from model state')
            ->assertSee('This card was created in the Galaxy foundation layer on')
            ->assertSee('and has not been updated since, so operators are still reviewing the first saved inventory shell.')
            ->assertSee('GX-910001 last saved timestamp reflected from model state')
            ->assertSee('The latest saved Galaxy foundation timestamp for this card is')
            ->assertSee('giving operators a concrete checkpoint for the current inventory shell.')
            ->assertSee('GX-910001 review note reflected from model state')
            ->assertSee('The current Galaxy foundation card review note says: Keep blocked-card dispute parity visible before any replacement follow-up is trusted.')
            ->assertSee('Inventory handoff stays visible in the workspace')
            ->assertSee('Operators should carry blocked status, holder linkage, and dispute context in the live workspace before trusting any replacement or reassignment follow-up.')
            ->assertSee('Inventory posture:')
            ->assertSee('Selected-card review is running in Galaxy foundation-backed read mode only')
            ->assertSee('Lifecycle freshness:')
            ->assertSee('newly created in Galaxy foundation review')
            ->assertSee('Last saved in Galaxy foundation:')
            ->assertSee('Review note:')
            ->assertSee('Keep blocked-card dispute parity visible before any replacement follow-up is trusted.')
            ->assertSee('Linkage signal:')
            ->assertSee('holder and branch linkage visible')
            ->assertSee('Inventory handoff signal:')
            ->assertSee('Blocked holder-linked inventory already carries enough dispute context for a useful handoff review.')
            ->assertSee('Lifecycle posture:')
            ->assertSee('This blocked card should stay under review-only handling until dispute and replacement semantics match the old Galaxy flow.')
            ->assertSee('Assignment readiness summary:')
            ->assertSee('Holder linkage is present, but assignment changes stay dispute-gated while blocked parity is still under review.')
            ->assertSee('Assignment posture:')
            ->assertSee('Holder linkage is visible now, but reassignment and replacement actions should stay blocked until inventory parity is verified.')
            ->assertSee('Shop posture:')
            ->assertSee('Shop ownership is visible for review, but cross-branch movement should stay blocked until branch inventory rules are verified.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Blocked-card handling, dispute resolution, and replacement flows should stay preview-only until inventory parity is verified.');
    }

    public function test_cards_page_surfaces_blocked_pre_activation_signal_for_selected_card(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Blocked Pre-activation Branch',
            'code' => 'galaxy-blocked-pre-activation-branch',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Blocked Silver',
            'slug' => 'galaxy-blocked-silver-pre-activation-card',
            'points_rate' => '1.20',
            'is_active' => true,
        ]);

        $card = Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'number' => 'GX-910004',
            'status' => 'blocked',
            'issued_at' => '2026-04-11 09:00:00',
            'review_note' => 'Keep blocked pre-activation stock visible before dispute handling is widened.',
            'activated_at' => null,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: GX-910004')
            ->assertSee('Blocked pre-activation signal')
            ->assertSee('Blocked inventory was issued but never activated, so dispute review should stay separate from active-card recovery handling.')
            ->assertSee('Blocked activated signal')
            ->assertSee('Blocked activated review is not the active slice for this card right now.')
            ->assertSee('Pre-activation unassigned signal')
            ->assertSee('Pre-activation inventory is still unassigned, so holder recovery should stay explicit before activation parity widens into live usage.')
            ->assertSee('Holder linkage summary')
            ->assertSee('Holder linkage is still missing from this issued card, so assignment recovery remains visible.')
            ->assertSee('Assignment readiness summary')
            ->assertSee('Assignment state is still pending because this issued card has not been linked to a holder yet.')
            ->assertSee('Assignment posture')
            ->assertSee('Blocked inventory is still unassigned, so reassignment and replacement review should stay explicit before any holder recovery assumptions are trusted.')
            ->assertSee('Dispute posture')
            ->assertSee('Treat this as blocked inventory triage before any active-card recovery assumptions are made.')
            ->assertSee('Activation readiness')
            ->assertSee('Issued inventory is still waiting for activation review in the Galaxy foundation layer.')
            ->assertSee('Pre-activation unassigned signal:')
            ->assertSee('Pre-activation inventory is still unassigned, so holder recovery should stay explicit before activation parity widens into live usage.');
    }

    public function test_cards_page_surfaces_pre_activation_holder_linked_signal_for_selected_card(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Linked Pre-activation Branch',
            'code' => 'galaxy-linked-pre-activation-branch',
            'is_active' => true,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Arman Pre-activation',
            'phone' => '+37491100231',
            'email' => 'arman.pre.activation@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Linked Silver',
            'slug' => 'galaxy-linked-silver-pre-activation-card',
            'points_rate' => '1.25',
            'is_active' => true,
        ]);

        $card = Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-910006',
            'status' => 'draft',
            'issued_at' => '2026-04-12 10:30:00',
            'activated_at' => null,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: GX-910006')
            ->assertSee('Pre-activation holder-linked signal')
            ->assertSee('Pre-activation inventory already carries holder linkage, so activation parity can stay anchored to the current member record before live usage expands.')
            ->assertSee('Holder linkage summary')
            ->assertSee('Holder linkage is already present in the Galaxy foundation layer for this card review.')
            ->assertSee('Assignment readiness summary')
            ->assertSee('Holder linkage is present, so assignment state is ready for parity-first review in the Galaxy foundation layer.')
            ->assertSee('Pre-activation holder-linked signal:')
            ->assertSee('Pre-activation inventory already carries holder linkage, so activation parity can stay anchored to the current member record before live usage expands.');
    }

    public function test_cards_page_surfaces_pre_activation_readiness_for_issued_inventory(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy North',
            'code' => 'galaxy-north-pre-activation-card',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Silver',
            'slug' => 'galaxy-silver-pre-activation-card',
            'points_rate' => '1.20',
            'is_active' => true,
        ]);

        $card = Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'number' => 'GX-910002',
            'status' => 'draft',
            'issued_at' => '2026-04-09 09:00:00',
            'review_note' => 'Keep issued stock visible before activation parity is widened.',
            'activated_at' => null,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: GX-910002')
            ->assertSee('Operational readiness')
            ->assertSee('issued inventory, activation pending')
            ->assertSee('Holder linkage summary')
            ->assertSee('Holder linkage is still missing from this issued card, so assignment recovery remains visible.')
            ->assertSee('Assignment readiness summary')
            ->assertSee('Assignment state is still pending because this issued card has not been linked to a holder yet.')
            ->assertSee('Assignment posture')
            ->assertSee('Issued inventory is still unassigned, so assignment work should stay recovery-first until holder linkage catches up with activation review.')
            ->assertSee('Activation readiness')
            ->assertSee('Issued inventory is still waiting for activation review in the Galaxy foundation layer.')
            ->assertSee('Issued')
            ->assertSee('2026-04-09')
            ->assertSee('Activated')
            ->assertSee('—');
    }

    public function test_cards_page_surfaces_draft_lifecycle_stage_for_unissued_inventory(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy South',
            'code' => 'galaxy-south-draft-card',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Bronze',
            'slug' => 'galaxy-bronze-draft-card',
            'points_rate' => '1.00',
            'is_active' => true,
        ]);

        $card = Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'number' => 'GX-910003',
            'status' => 'draft',
            'review_note' => 'Keep unissued stock in safe review before any activation discussion starts.',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: GX-910003')
            ->assertSee('Lifecycle stage')
            ->assertSee('Draft inventory shell, not yet issued in the Galaxy foundation layer.')
            ->assertSee('Holder linkage summary')
            ->assertSee('Holder linkage is still intentionally absent while this card stays in draft inventory review.')
            ->assertSee('Assignment readiness summary')
            ->assertSee('Assignment state is still intentionally pending while this card remains a draft inventory shell.')
            ->assertSee('Assignment posture')
            ->assertSee('Draft inventory is still unassigned, which keeps assignment work safely in shell-review mode before issuance begins.')
            ->assertSee('Activation readiness')
            ->assertSee('This card has not been issued yet, so activation should remain out of scope during parity review.')
            ->assertSee('Issued')
            ->assertSee('Activated')
            ->assertSee('—');
    }

    public function test_authenticated_user_can_create_card_from_live_admin_flow(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Inventory Live Branch',
            'code' => 'galaxy-inventory-live-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Live Gold',
            'slug' => 'galaxy-live-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Live Inventory Holder',
            'phone' => '+37493000111',
            'email' => 'live.inventory.holder@example.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_holder_id' => (string) $holder->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-live-1001 ',
            'status' => 'active',
            'issued_at' => '2026-05-05 09:20:00',
            'activated_at' => '2026-05-05 12:40:00',
            'review_note' => 'Keep first-pass inventory parity visible before widening replacement handling.',
        ]);

        $card = Card::query()->where('number', 'GX-LIVE-1001')->firstOrFail();

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Card "GX-LIVE-1001" was created.');

        $this->assertDatabaseHas('cards', [
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-LIVE-1001',
            'status' => 'active',
            'issued_at' => '2026-05-05 09:20:00',
            'review_note' => 'Keep first-pass inventory parity visible before widening replacement handling.',
        ]);
    }

    public function test_authenticated_user_can_update_card_from_live_admin_flow(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Inventory Update Branch',
            'code' => 'galaxy-inventory-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Update Gold',
            'slug' => 'galaxy-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Update Inventory Holder',
            'phone' => '+37493000113',
            'email' => 'update.inventory.holder@example.com',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-LIVE-2001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_holder_id' => (string) $holder->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-live-2001a ',
            'status' => 'blocked',
            'issued_at' => '2026-05-05 08:10:00',
            'activated_at' => '2026-05-05 12:41:00',
            'review_note' => 'Keep blocked inventory under parity review before trusting dispute or replacement follow-up.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Card "GX-LIVE-2001A" was updated.');

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-LIVE-2001A',
            'status' => 'blocked',
            'issued_at' => '2026-05-05 08:10:00',
            'review_note' => 'Keep blocked inventory under parity review before trusting dispute or replacement follow-up.',
        ]);
    }

    public function test_card_live_flow_returns_operator_friendly_issue_timestamp_validation_message(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Invalid Issue Branch',
            'code' => 'galaxy-invalid-issue-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Invalid Issue Gold',
            'slug' => 'galaxy-invalid-issue-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-invalid-issue-1001 ',
            'status' => 'active',
            'issued_at' => 'not-a-real-timestamp',
            'activated_at' => '',
            'review_note' => 'Keep invalid issue timestamps obvious while the first live inventory shell stays narrow.',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'issued_at' => 'Use a real issue timestamp so the Galaxy inventory lifecycle stays operator-friendly.',
            ]);
    }

    public function test_card_update_live_flow_returns_operator_friendly_issue_timestamp_validation_message(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Invalid Issue Update Branch',
            'code' => 'galaxy-invalid-issue-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Invalid Issue Update Gold',
            'slug' => 'galaxy-invalid-issue-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-INVALID-ISS-UPD-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-invalid-iss-upd-1001 ',
            'status' => 'active',
            'issued_at' => 'not-a-real-timestamp',
            'activated_at' => '',
            'review_note' => 'Keep invalid issue timestamps obvious while selected inventory edits stay narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'issued_at' => 'Use a real issue timestamp so the Galaxy inventory lifecycle stays operator-friendly.',
            ]);
    }

    public function test_card_live_flow_returns_operator_friendly_activation_timestamp_validation_message(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Invalid Activation Branch',
            'code' => 'galaxy-invalid-activation-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Invalid Activation Gold',
            'slug' => 'galaxy-invalid-activation-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-invalid-activation-1001 ',
            'status' => 'active',
            'issued_at' => '',
            'activated_at' => 'not-a-real-timestamp',
            'review_note' => 'Keep invalid activation timestamps obvious while the first live inventory shell stays narrow.',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'activated_at' => 'Use a real activation timestamp so the Galaxy inventory lifecycle stays operator-friendly.',
            ]);
    }

    public function test_card_live_flow_rejects_activation_before_issue_timestamp(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Invalid Lifecycle Order Branch',
            'code' => 'galaxy-invalid-lifecycle-order-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Invalid Lifecycle Order Gold',
            'slug' => 'galaxy-invalid-lifecycle-order-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-invalid-order-1001 ',
            'status' => 'active',
            'issued_at' => '2026-05-05 12:40:00',
            'activated_at' => '2026-05-05 09:20:00',
            'review_note' => 'Keep invalid lifecycle ordering obvious while the first live inventory shell stays narrow.',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'activated_at' => 'Keep activation on or after issuance so the Galaxy lifecycle timeline stays operator-friendly.',
            ]);
    }

    public function test_card_live_flow_requires_issue_timestamp_when_activation_is_present(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Missing Issue Branch',
            'code' => 'galaxy-missing-issue-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Missing Issue Gold',
            'slug' => 'galaxy-missing-issue-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-missing-issue-1001 ',
            'status' => 'active',
            'issued_at' => '',
            'activated_at' => '2026-05-05 12:40:00',
            'review_note' => 'Keep missing issue timestamps obvious when activation is already present.',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'issued_at' => 'Add an issue timestamp before activation so the Galaxy lifecycle timeline stays operator-friendly.',
            ]);
    }

    public function test_card_live_flow_requires_activation_timestamp_for_active_status(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Missing Activation Branch',
            'code' => 'galaxy-missing-activation-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Missing Activation Gold',
            'slug' => 'galaxy-missing-activation-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-missing-activation-1001 ',
            'status' => 'active',
            'issued_at' => '2026-05-05 09:20:00',
            'activated_at' => '',
            'review_note' => 'Keep missing activation timestamps obvious before active inventory is trusted.',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'activated_at' => 'Add an activation timestamp before marking this card active so the Galaxy lifecycle timeline stays operator-friendly.',
            ]);
    }

    public function test_card_live_flow_rejects_activation_timestamp_for_draft_status(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Draft Activation Branch',
            'code' => 'galaxy-draft-activation-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Draft Activation Gold',
            'slug' => 'galaxy-draft-activation-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-draft-activation-1001 ',
            'status' => 'draft',
            'issued_at' => '2026-05-05 09:20:00',
            'activated_at' => '2026-05-05 12:40:00',
            'review_note' => 'Keep draft inventory from looking activated before parity is widened.',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'activated_at' => 'Leave activation blank while this card stays draft so the Galaxy lifecycle timeline stays operator-friendly.',
            ]);
    }

    public function test_card_live_flow_requires_issue_timestamp_for_blocked_status(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Missing Blocked Issue Branch',
            'code' => 'galaxy-missing-blocked-issue-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Missing Blocked Issue Gold',
            'slug' => 'galaxy-missing-blocked-issue-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-missing-blocked-issue-1001 ',
            'status' => 'blocked',
            'issued_at' => '',
            'activated_at' => '',
            'review_note' => 'Keep blocked inventory from looking unissued before parity is widened.',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'issued_at' => 'Add an issue timestamp before blocking this card so the Galaxy lifecycle timeline stays operator-friendly.',
            ]);
    }

    public function test_card_live_flow_requires_review_note_for_blocked_status(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Missing Blocked Note Branch',
            'code' => 'galaxy-missing-blocked-note-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Missing Blocked Note Gold',
            'slug' => 'galaxy-missing-blocked-note-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-missing-blocked-note-1001 ',
            'status' => 'blocked',
            'issued_at' => '2026-05-05 09:20:00',
            'activated_at' => '',
            'review_note' => '   ',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'review_note' => 'Add a review note before blocking this card so the Galaxy inventory workspace stays operator-friendly.',
            ]);
    }

    public function test_card_update_live_flow_requires_review_note_for_blocked_status(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Missing Blocked Note Update Branch',
            'code' => 'galaxy-missing-blocked-note-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Missing Blocked Note Update Gold',
            'slug' => 'galaxy-missing-blocked-note-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-MISSING-BLOCKED-NOTE-UPD-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-missing-blocked-note-upd-1001 ',
            'status' => 'blocked',
            'issued_at' => '2026-05-05 09:20:00',
            'activated_at' => '',
            'review_note' => '   ',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'review_note' => 'Add a review note before blocking this card so the Galaxy inventory workspace stays operator-friendly.',
            ]);
    }

    public function test_card_update_live_flow_requires_issue_timestamp_for_blocked_status(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Missing Blocked Issue Update Branch',
            'code' => 'galaxy-missing-blocked-issue-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Missing Blocked Issue Update Gold',
            'slug' => 'galaxy-missing-blocked-issue-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-MISSING-BLOCKED-ISS-UPD-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-missing-blocked-iss-upd-1001 ',
            'status' => 'blocked',
            'issued_at' => '',
            'activated_at' => '',
            'review_note' => 'Keep selected blocked inventory from looking unissued before parity is widened.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'issued_at' => 'Add an issue timestamp before blocking this card so the Galaxy lifecycle timeline stays operator-friendly.',
            ]);
    }

    public function test_card_update_live_flow_rejects_activation_timestamp_for_draft_status(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Draft Activation Update Branch',
            'code' => 'galaxy-draft-activation-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Draft Activation Update Gold',
            'slug' => 'galaxy-draft-activation-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-DRAFT-ACT-UPD-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-draft-act-upd-1001 ',
            'status' => 'draft',
            'issued_at' => '2026-05-05 09:20:00',
            'activated_at' => '2026-05-05 12:40:00',
            'review_note' => 'Keep selected draft inventory from looking activated before parity is widened.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'activated_at' => 'Leave activation blank while this card stays draft so the Galaxy lifecycle timeline stays operator-friendly.',
            ]);
    }

    public function test_card_update_live_flow_requires_activation_timestamp_for_active_status(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Missing Activation Update Branch',
            'code' => 'galaxy-missing-activation-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Missing Activation Update Gold',
            'slug' => 'galaxy-missing-activation-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-MISSING-ACT-UPD-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-missing-act-upd-1001 ',
            'status' => 'active',
            'issued_at' => '2026-05-05 09:20:00',
            'activated_at' => '',
            'review_note' => 'Keep missing activation timestamps obvious before selected inventory is marked active.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'activated_at' => 'Add an activation timestamp before marking this card active so the Galaxy lifecycle timeline stays operator-friendly.',
            ]);
    }

    public function test_card_update_live_flow_requires_issue_timestamp_when_activation_is_present(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Missing Issue Update Branch',
            'code' => 'galaxy-missing-issue-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Missing Issue Update Gold',
            'slug' => 'galaxy-missing-issue-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-MISSING-ISS-UPD-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-missing-iss-upd-1001 ',
            'status' => 'active',
            'issued_at' => '',
            'activated_at' => '2026-05-05 12:40:00',
            'review_note' => 'Keep missing issue timestamps obvious when selected inventory edits include activation.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'issued_at' => 'Add an issue timestamp before activation so the Galaxy lifecycle timeline stays operator-friendly.',
            ]);
    }

    public function test_card_update_live_flow_rejects_activation_before_issue_timestamp(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Invalid Lifecycle Order Update Branch',
            'code' => 'galaxy-invalid-lifecycle-order-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Invalid Lifecycle Order Update Gold',
            'slug' => 'galaxy-invalid-lifecycle-order-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-INVALID-ORDER-UPD-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-invalid-order-upd-1001 ',
            'status' => 'active',
            'issued_at' => '2026-05-05 12:40:00',
            'activated_at' => '2026-05-05 09:20:00',
            'review_note' => 'Keep invalid lifecycle ordering obvious while selected inventory edits stay narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'activated_at' => 'Keep activation on or after issuance so the Galaxy lifecycle timeline stays operator-friendly.',
            ]);
    }

    public function test_card_update_live_flow_returns_operator_friendly_activation_timestamp_validation_message(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Invalid Activation Update Branch',
            'code' => 'galaxy-invalid-activation-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Invalid Activation Update Gold',
            'slug' => 'galaxy-invalid-activation-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-INVALID-ACT-UPD-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-invalid-act-upd-1001 ',
            'status' => 'active',
            'issued_at' => '',
            'activated_at' => 'not-a-real-timestamp',
            'review_note' => 'Keep invalid activation timestamps obvious while selected inventory edits stay narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'activated_at' => 'Use a real activation timestamp so the Galaxy inventory lifecycle stays operator-friendly.',
            ]);
    }

    public function test_card_live_flow_normalizes_number_and_rejects_duplicate_inventory_identifier(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Duplicate Inventory Branch',
            'code' => 'galaxy-duplicate-inventory-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Duplicate Gold',
            'slug' => 'galaxy-duplicate-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-DUP-1001',
            'status' => 'draft',
        ]);

        $user = User::factory()->create();

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-dup-1001 ',
            'status' => 'active',
            'activated_at' => '2026-05-05 12:42:00',
            'review_note' => 'This duplicate should be blocked by the normalized inventory identifier.',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'number' => 'This card number is already in use in the Galaxy foundation inventory shell.',
            ]);
    }

    public function test_card_live_flow_rejects_holder_from_a_different_shop(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Scope Branch',
            'code' => 'galaxy-holder-scope-branch',
            'is_active' => true,
        ]);
        $otherShop = Shop::create([
            'name' => 'Galaxy Holder Foreign Branch',
            'code' => 'galaxy-holder-foreign-branch',
            'is_active' => true,
        ]);
        $holder = CardHolder::create([
            'shop_id' => $otherShop->id,
            'full_name' => 'Foreign Holder',
            'phone' => '+37493000112',
            'email' => 'foreign.holder@example.com',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Scope Gold',
            'slug' => 'galaxy-scope-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_holder_id' => (string) $holder->id,
            'card_type_id' => (string) $cardType->id,
            'number' => 'GX-SCOPE-1001',
            'status' => 'draft',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'card_holder_id' => 'Choose a cardholder from the same shop so the Galaxy inventory shell keeps holder linkage scoped correctly.',
            ]);

        $this->assertDatabaseMissing('cards', [
            'number' => 'GX-SCOPE-1001',
        ]);
    }

    public function test_card_update_live_flow_rejects_holder_from_a_different_shop(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Update Holder Scope Branch',
            'code' => 'galaxy-update-holder-scope-branch',
            'is_active' => true,
        ]);
        $otherShop = Shop::create([
            'name' => 'Galaxy Update Holder Foreign Branch',
            'code' => 'galaxy-update-holder-foreign-branch',
            'is_active' => true,
        ]);
        $holder = CardHolder::create([
            'shop_id' => $otherShop->id,
            'full_name' => 'Foreign Update Holder',
            'phone' => '+37493000114',
            'email' => 'foreign.update.holder@example.com',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Update Scope Gold',
            'slug' => 'galaxy-update-scope-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-UPD-SCOPE-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_holder_id' => (string) $holder->id,
            'card_type_id' => (string) $cardType->id,
            'number' => 'GX-UPD-SCOPE-1001',
            'status' => 'draft',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'card_holder_id' => 'Choose a cardholder from the same shop so the Galaxy inventory shell keeps holder linkage scoped correctly.',
            ]);

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'card_holder_id' => null,
        ]);
    }

    public function test_shop_scoped_admin_cannot_create_card_for_a_different_shop(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Assigned Card Shop',
            'code' => 'galaxy-scoped-assigned-card-shop',
            'is_active' => true,
        ]);
        $otherShop = Shop::create([
            'name' => 'Galaxy Scoped Foreign Card Shop',
            'code' => 'galaxy-scoped-foreign-card-shop',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Scoped Card Type',
            'slug' => 'galaxy-scoped-card-type',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Card Operator',
            'slug' => 'scoped-card-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Manage scoped cards',
            'slug' => 'manage-scoped-cards',
            'review_note' => 'Phase 1 scoped card create coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->from('/admin/cards')->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $otherShop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => 'GX-SCOPED-FOREIGN-1001',
            'status' => 'draft',
        ]);

        $response
            ->assertRedirect('/admin/cards#live-form')
            ->assertSessionHasErrors([
                'shop_id' => 'Choose a shop you can access so the Galaxy inventory shell stays scoped to your assigned branch.',
            ]);

        $this->assertDatabaseMissing('cards', [
            'number' => 'GX-SCOPED-FOREIGN-1001',
        ]);
    }

    public function test_cards_page_supports_selected_active_card_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Active Card Branch',
            'code' => 'galaxy-active-card-branch',
            'is_active' => true,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Mariam Active Card',
            'phone' => '+37491100222',
            'email' => 'mariam.active.card@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Active Tier',
            'slug' => 'galaxy-active-tier-card',
            'points_rate' => '1.80',
            'is_active' => true,
        ]);

        $card = Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-920001',
            'status' => 'active',
            'activated_at' => '2026-04-02 11:30:00',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Back to card catalog')
            ->assertSee('Reviewing: GX-920001')
            ->assertSee('Review blocked cards')
            ->assertSee('Blocked until blocked-card semantics are verified against this active Galaxy foundation inventory flow.')
            ->assertSee('Selected card')
            ->assertSee('GX-920001')
            ->assertSee('Card status signal')
            ->assertSee('Active card is already visible for live inventory parity review.')
            ->assertSee('Operational readiness')
            ->assertSee('issued inventory, parity-sensitive')
            ->assertSee('Active holder-linked signal')
            ->assertSee('Active inventory already carries holder linkage, so parity review can stay anchored to the current member record before later replacement flows are widened.')
            ->assertSee('Active unassigned signal')
            ->assertSee('Holder linkage summary')
            ->assertSee('Holder linkage is already present in the Galaxy foundation layer for this card review.')
            ->assertSee('Assignment readiness summary')
            ->assertSee('Holder linkage is present, so assignment state is ready for parity-first review in the Galaxy foundation layer.')
            ->assertSee('Assignment posture')
            ->assertSee('Active inventory is already anchored to a holder record, so parity review can stay member-linked before wider replacement work opens up.')
            ->assertSee('Active inventory already carries holder linkage in the Galaxy foundation layer for parity-first review.')
            ->assertSee('Linkage signal')
            ->assertSee('holder and branch linkage visible')
            ->assertSee('Inventory focus')
            ->assertSee('Start with card status, holder linkage, and branch ownership before discussing any later replacement or reassignment flow.')
            ->assertSee('Inventory posture')
            ->assertSee('Keep inventory review in the live workspace first, then leave replacement, reassignment, and cross-branch moves gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep card status, holder linkage, and branch ownership visible together before trusting any later replacement or reassignment discussion.')
            ->assertSee('Inventory handoff signal')
            ->assertSee('Active issued inventory already carries enough linkage context for a useful handoff review.')
            ->assertSee('Backend gap')
            ->assertSee('Card lifecycle writes, blocked-card handling, and replacement flows should stay preview-only until inventory parity is verified.')
            ->assertSee('Inventory guidance')
            ->assertSee('This card is already active in the Galaxy foundation layer, so inventory changes should stay parity-first until blocked and replacement semantics are verified.');
    }

    public function test_cards_page_supports_selected_active_unassigned_card_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Active Unassigned Branch',
            'code' => 'galaxy-active-unassigned-branch',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Active Unassigned Tier',
            'slug' => 'galaxy-active-unassigned-tier-card',
            'points_rate' => '1.40',
            'is_active' => true,
        ]);

        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-920055',
            'status' => 'active',
            'issued_at' => '2026-04-03 08:00:00',
            'activated_at' => '2026-04-03 10:15:00',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: GX-920055')
            ->assertSee('Active holder-linked signal')
            ->assertSee('Active holder-linked review is not the active slice for this card right now.')
            ->assertSee('Active unassigned signal')
            ->assertSee('Active inventory is still unassigned, so holder-linkage recovery should stay visible before operators assume a stable member attachment.')
            ->assertSee('Holder linkage summary')
            ->assertSee('Holder linkage is still missing from this issued card, so assignment recovery remains visible.')
            ->assertSee('Assignment readiness summary')
            ->assertSee('Assignment state is still pending because this issued card has not been linked to a holder yet.')
            ->assertSee('Assignment posture')
            ->assertSee('Active inventory still lacks holder linkage, so assignment recovery should stay visible before operators trust this as stable member stock.')
            ->assertSee('Linkage signal')
            ->assertSee('branch-linked inventory, holder pending');
    }

    public function test_cards_page_supports_selected_blocked_holder_linked_card_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Blocked Card Branch',
            'code' => 'galaxy-blocked-card-branch',
            'is_active' => true,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Lilit Blocked Card',
            'phone' => '+37491100223',
            'email' => 'lilit.blocked.card@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Blocked Tier',
            'slug' => 'galaxy-blocked-tier-card',
            'points_rate' => '1.65',
            'is_active' => true,
        ]);

        $card = Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-920099',
            'status' => 'blocked',
            'activated_at' => '2026-04-04 10:15:00',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Back to card catalog')
            ->assertSee('Reviewing: GX-920099')
            ->assertSee('Review blocked cards')
            ->assertSee('Blocked until this blocked holder-linked card clears dispute and replacement parity against the legacy Galaxy flow.')
            ->assertSee('Selected card')
            ->assertSee('GX-920099')
            ->assertSee('Card status signal')
            ->assertSee('Blocked card remains in operator review posture until dispute parity is verified.')
            ->assertSee('Operational readiness')
            ->assertSee('blocked inventory, operator review only')
            ->assertSee('Linkage signal')
            ->assertSee('holder and branch linkage visible')
            ->assertSee('Inventory focus')
            ->assertSee('Start with blocked status, holder linkage, and dispute context before discussing any later replacement or reassignment flow.')
            ->assertSee('Inventory posture')
            ->assertSee('Keep blocked inventory in dispute-first review, then leave replacement, reassignment, and cross-branch moves gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep blocked status, holder linkage, and dispute context visible together before trusting any later replacement or reassignment discussion.')
            ->assertSee('Inventory handoff signal')
            ->assertSee('Blocked holder-linked inventory already carries enough dispute context for a useful handoff review.')
            ->assertSee('Inventory handoff stays visible in the workspace')
            ->assertSee('Operators should carry blocked status, holder linkage, and dispute context in the live workspace before trusting any replacement or reassignment follow-up.')
            ->assertSee('Assignment readiness summary')
            ->assertSee('Holder linkage is present, but assignment changes stay dispute-gated while blocked parity is still under review.')
            ->assertSee('Assignment posture')
            ->assertSee('Blocked inventory already carries holder linkage, so dispute review can stay member-linked while replacement and reassignment remain parity-gated.')
            ->assertSee('Backend gap')
            ->assertSee('Blocked-card handling, dispute resolution, and replacement flows should stay preview-only until inventory parity is verified.')
            ->assertSee('Inventory guidance')
            ->assertSee('This card is blocked in the Galaxy foundation layer, so replacement and dispute handling should remain review-only until legacy card-state parity is confirmed.');
    }

    public function test_cards_page_supports_selected_blocked_unassigned_card_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Blocked Unassigned Branch',
            'code' => 'galaxy-blocked-unassigned-branch',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Blocked Unassigned Tier',
            'slug' => 'galaxy-blocked-unassigned-tier-card',
            'points_rate' => '1.45',
            'is_active' => true,
        ]);

        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-920199',
            'status' => 'blocked',
            'activated_at' => '2026-04-05 09:10:00',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Back to card catalog')
            ->assertSee('Reviewing: GX-920199')
            ->assertSee('Review blocked cards')
            ->assertSee('Blocked until this blocked card clears dispute and replacement parity against the legacy Galaxy flow.')
            ->assertSee('Selected card')
            ->assertSee('GX-920199')
            ->assertSee('Card status signal')
            ->assertSee('Blocked card remains in operator review posture until dispute parity is verified.')
            ->assertSee('Operational readiness')
            ->assertSee('blocked inventory, operator review only')
            ->assertSee('Linkage signal')
            ->assertSee('branch-linked inventory, holder pending')
            ->assertSee('Inventory focus')
            ->assertSee('Start with blocked status, holder linkage, and dispute context before discussing any later replacement or reassignment flow.')
            ->assertSee('Inventory posture')
            ->assertSee('Keep blocked inventory in dispute-first review, then leave replacement, reassignment, and cross-branch moves gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep blocked status, holder linkage, and dispute context visible together before trusting any later replacement or reassignment discussion.')
            ->assertSee('Inventory handoff signal')
            ->assertSee('Blocked inventory should stay in handoff-only posture until dispute and replacement parity are explicit.')
            ->assertSee('Inventory handoff stays visible in the workspace')
            ->assertSee('Operators should carry blocked status, branch ownership, and holder-linkage gaps in the live workspace before trusting any replacement or reassignment follow-up.')
            ->assertSee('Backend gap')
            ->assertSee('Blocked-card handling, dispute resolution, and replacement flows should stay preview-only until inventory parity is verified.')
            ->assertSee('Blocked holder-linked signal')
            ->assertSee('Blocked holder-linked review is not the active slice for this card right now.')
            ->assertSee('Blocked unassigned signal')
            ->assertSee('Blocked inventory is still unassigned, so replacement and reassignment review should stay explicit before any holder recovery assumptions are made.')
            ->assertSee('Holder linkage summary')
            ->assertSee('Holder linkage is still intentionally absent while this card stays in draft inventory review.')
            ->assertSee('Assignment readiness summary')
            ->assertSee('Assignment state is still intentionally pending while this card remains a draft inventory shell.')
            ->assertSee('Assignment posture')
            ->assertSee('Blocked inventory is still unassigned, so reassignment and replacement review should stay explicit before any holder recovery assumptions are trusted.')
            ->assertSee('Inventory guidance')
            ->assertSee('This card is blocked in the Galaxy foundation layer, so replacement and dispute handling should remain review-only until legacy card-state parity is confirmed.')
            ->assertSee('Assignment posture:')
            ->assertSee('No holder is linked yet, which keeps this inventory record safer for assignment-flow-parity review before assignment flows are enabled.');
    }

    public function test_cards_page_supports_selected_draft_card_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Draft Card Branch',
            'code' => 'galaxy-draft-card-branch',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Draft Tier',
            'slug' => 'galaxy-draft-tier-card',
            'points_rate' => '1.10',
            'is_active' => false,
        ]);

        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-930001',
            'status' => 'draft',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Back to card catalog')
            ->assertSee('Reviewing: GX-930001')
            ->assertSee('Review blocked cards')
            ->assertSee('Blocked until blocked-card semantics are verified against this draft Galaxy foundation inventory flow.')
            ->assertSee('Selected card')
            ->assertSee('GX-930001')
            ->assertSee('Card status signal')
            ->assertSee('Draft card remains safer for issuance-parity review before any issuance-flow discussion.')
            ->assertSee('Operational readiness')
            ->assertSee('draft inventory shell')
            ->assertSee('Holder linkage summary')
            ->assertSee('Holder linkage is still intentionally absent while this card stays in draft inventory review.')
            ->assertSee('Assignment readiness summary')
            ->assertSee('Assignment state is still intentionally pending while this card remains a draft inventory shell.')
            ->assertSee('Assignment posture')
            ->assertSee('Draft inventory is still unassigned, which keeps assignment work safely in shell-review mode before issuance begins.')
            ->assertSee('Linkage signal')
            ->assertSee('branch-linked inventory, holder pending')
            ->assertSee('Inventory focus')
            ->assertSee('Start with draft status, holder linkage gaps, and branch ownership before discussing any later issuance or reassignment flow.')
            ->assertSee('Inventory posture')
            ->assertSee('Keep draft inventory in issuance-readiness review first, then leave activation, reassignment, and cross-branch moves gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep draft status, holder linkage gaps, and branch ownership visible together before trusting any later issuance or reassignment discussion.')
            ->assertSee('Inventory handoff signal')
            ->assertSee('Draft inventory should stay in handoff-only posture until issuance parity is explicit.')
            ->assertSee('Backend gap')
            ->assertSee('Card issuance, activation, and lifecycle writes should stay preview-only until inventory parity is verified.')
            ->assertSee('Assignment readiness summary:')
            ->assertSee('Assignment state is still intentionally pending while this card remains a draft inventory shell.')
            ->assertSee('Inventory guidance')
            ->assertSee('This card is still draft inventory in the Galaxy foundation layer, which keeps it safe for parity checks before operators treat it as issued stock.');
    }

    public function test_cards_page_ignores_unknown_selected_card_query(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-unknown-card',
            'is_active' => true,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Anna Petrova',
            'phone' => '+37491100001',
            'email' => 'anna-unknown-card@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Gold',
            'slug' => 'galaxy-gold-unknown-card',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-920001',
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card=999999');

        $response
            ->assertOk()
            ->assertSee('GX-920001')
            ->assertSee('Review latest saved card shell')
            ->assertDontSee('Back to card catalog')
            ->assertDontSee('Selected card')
            ->assertDontSee('selected for Laravel review');
    }

    public function test_cards_page_ignores_malformed_selected_card_query(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-malformed-card',
            'is_active' => true,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Anna Petrova',
            'phone' => '+37491100001',
            'email' => 'anna-malformed-card@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Gold',
            'slug' => 'galaxy-gold-malformed-card',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-930001',
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card=not-a-number');

        $response
            ->assertOk()
            ->assertSee('GX-930001')
            ->assertSee('Review latest saved card shell')
            ->assertDontSee('Back to card catalog')
            ->assertDontSee('Selected card')
            ->assertDontSee('selected for Laravel review');
    }

    public function test_cards_page_hides_other_shop_card_review_links_for_shop_scoped_admins(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Card Home',
            'code' => 'galaxy-card-home',
            'is_active' => true,
        ]);

        $otherShop = Shop::create([
            'name' => 'Galaxy Card Other',
            'code' => 'galaxy-card-other',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Scoped Card Type',
            'slug' => 'galaxy-scoped-card-type',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $assignedCard = Card::create([
            'shop_id' => $assignedShop->id,
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'number' => 'GX-940001',
            'status' => 'active',
        ]);

        $otherCard = Card::create([
            'shop_id' => $otherShop->id,
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'number' => 'GX-940002',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Scoped Card Reviewer',
            'slug' => 'scoped-card-reviewer-index',
        ]);

        $permission = Permission::create([
            'name' => 'Review scoped card workspace',
            'slug' => 'review-scoped-card-workspace',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/admin/cards');

        $response
            ->assertOk()
            ->assertSee('href="/admin/cards?card='.$assignedCard->id.'"', false)
            ->assertDontSee('href="/admin/cards?card='.$otherCard->id.'"', false)
            ->assertSee('Review latest saved card shell')
            ->assertSee('href="/admin/cards?card='.$assignedCard->id.'"', false);
    }

    public function test_cards_page_ignores_inaccessible_selected_card_query_for_shop_scoped_admins(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Card Review Home',
            'code' => 'galaxy-card-review-home',
            'is_active' => true,
        ]);

        $otherShop = Shop::create([
            'name' => 'Galaxy Card Review Other',
            'code' => 'galaxy-card-review-other',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Scoped Review Card Type',
            'slug' => 'galaxy-scoped-review-card-type',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $assignedShop->id,
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'number' => 'GX-950001',
            'status' => 'active',
        ]);

        $otherCard = Card::create([
            'shop_id' => $otherShop->id,
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'number' => 'GX-950002',
            'status' => 'blocked',
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Scoped Card Selector',
            'slug' => 'scoped-card-selector-index',
        ]);

        $permission = Permission::create([
            'name' => 'Select scoped card workspace',
            'slug' => 'select-scoped-card-workspace',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/admin/cards?card='.$otherCard->id);

        $response
            ->assertOk()
            ->assertSee('GX-950001')
            ->assertSee('GX-950002')
            ->assertDontSee('Back to card catalog')
            ->assertDontSee('Reviewing: GX-950002')
            ->assertDontSee('Selected card')
            ->assertSee('Review latest saved card shell')
            ->assertSee('href="/admin/cards?card=1"', false);
    }

    public function test_authenticated_user_can_access_cardholders_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders');

        $response
            ->assertOk()
            ->assertSee('Cardholders placeholder')
            ->assertSee('Galaxy holder workspace for worker and client lookup, holder history, and lifecycle review.')
            ->assertSee('Operational index shape')
            ->assertSee('Anna Petrova')
            ->assertSee('Has cards')
            ->assertSee('New Galaxy holder')
            ->assertSee('Review recent activity')
            ->assertSee('Blocked until the first Galaxy foundation-backed cardholder slice exists for activity-history parity review.')
            ->assertSee('Active Galaxy holders')
            ->assertSee('Inactive Galaxy holders')
            ->assertSee('Linked Galaxy card shells')
            ->assertSee('Cardholder operations are still preview-only')
            ->assertSee('Search actions, metrics, and lifecycle cues are shaping the target Galaxy flow, but they are not backed by Laravel reads or writes yet.')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview holder search surface and activity cues are defined')
            ->assertSee('Search, profile reads, and activity history still need PHP-backed Laravel wiring')
            ->assertSee('Implementation dependencies')
            ->assertSee('CardHolder model and shop linkage baseline exist')
            ->assertSee('Searchable index, profile read path, and activity sourcing are still pending')
            ->assertSee('First Galaxy foundation wiring step')
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
            ->assertSee('Galaxy branch workspace for scope boundaries, activation review, and future access checks.')
            ->assertSee('Operational index shape')
            ->assertSee('Central Shop')
            ->assertSee('Volume tier')
            ->assertSee('New Galaxy branch')
            ->assertSee('Review branch scope')
            ->assertSee('Blocked until branch ownership rules are confirmed against the legacy Galaxy multi-shop access model.')
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('Active Galaxy branches')
            ->assertSee('Paused Galaxy branches')
            ->assertSee('Assigned branch managers')
            ->assertSee('Shop operations are still preview-only')
            ->assertSee('Branch actions, metrics, and filters are shaping the final Galaxy workspace, but they are not wired to Laravel queries or handlers yet.')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview shop rows and branch actions defined')
            ->assertSee('Real shop queries and branch mutations still need PHP-backed Laravel wiring')
            ->assertSee('Implementation dependencies')
            ->assertSee('Shop model and user-to-shop linkage baseline exist')
            ->assertSee('Query-backed shop index and branch actions are still pending')
            ->assertSee('First Galaxy foundation wiring step')
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

    public function test_shops_catalog_actions_reflect_saved_branch_readiness(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central Scope',
            'code' => 'galaxy-central-scope',
            'is_active' => false,
        ]);

        $manager = User::factory()->create([
            'name' => 'Nare Scope Lead',
            'shop_id' => $shop->id,
        ]);

        $shop->users()->save($manager);

        $holder = CardHolder::create([
            'full_name' => 'Scope Holder',
            'phone' => '+37499110101',
            'status' => 'active',
            'is_active' => true,
            'shop_id' => $shop->id,
        ]);

        $cardType = CardType::create([
            'name' => 'Scope Gold',
            'slug' => 'scope-gold',
            'points_rate' => 1.50,
            'is_active' => true,
        ]);

        Card::create([
            'number' => 'GX-SHOP-SCOPE-1',
            'status' => 'active',
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
            'issued_at' => now(),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('New Galaxy branch')
            ->assertSee('Review branch scope')
            ->assertSee('Blocked until saved branch ownership and scope coverage are verified against the legacy Galaxy multi-shop model.');
    }

    public function test_shops_page_replaces_preview_rows_with_model_backed_index_data(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central',
            'is_active' => true,
            'review_note' => 'Keep branch ownership parity visible before scope writes are trusted.',
        ]);

        $pausedShop = Shop::create([
            'name' => 'Galaxy Airport',
            'code' => 'galaxy-airport',
            'is_active' => false,
        ]);

        User::factory()->create([
            'name' => 'Nare Gevorgyan',
            'shop_id' => $shop->id,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Aram Petrosyan',
            'phone' => '+37410000000',
            'email' => 'aram@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-200001',
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Galaxy Central')
            ->assertSee('href="/admin/shops?shop='.$shop->id.'"', false)
            ->assertSee('galaxy-central')
            ->assertSee('Nare Gevorgyan')
            ->assertSee('Galaxy Airport')
            ->assertSee('href="/admin/shops?shop='.$pausedShop->id.'"', false)
            ->assertSee('galaxy-airport')
            ->assertSee('Keep branch ownership parity visible before scope writes are trusted.')
            ->assertSee('No review note saved yet')
            ->assertSee('Unassigned')
            ->assertSee('Create Galaxy branch in Galaxy foundation')
            ->assertSee('Create branch shell')
            ->assertSee('Galaxy foundation status')
            ->assertSee('Lowercase identifier for the minimal Galaxy foundation branch record.')
            ->assertSee('Use this safe Phase 1 note to record branch review context without opening manager or scope writes.')
            ->assertSee('action="/admin/shops"', false)
            ->assertSee('Review latest saved branch shell')
            ->assertSee('href="/admin/shops?shop='.$pausedShop->id.'"', false)
            ->assertSee('Reviewed Galaxy branches')
            ->assertSee('This is the first minimal Galaxy foundation-backed shop write path. Keep it limited to branch identity and review notes while manager reassignment and scope changes remain parity-first review surfaces.')
            ->assertSee('Assigned branch managers')
            ->assertSee('>1<', false)
            ->assertSee('active')
            ->assertSee('paused');
    }

    public function test_shops_page_surfaces_selected_shop_context_from_laravel_data(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central',
            'is_active' => true,
            'review_note' => 'Keep branch ownership parity visible before any live scope mutation is trusted.',
        ]);

        User::factory()->create([
            'name' => 'Nare Gevorgyan',
            'shop_id' => $shop->id,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Aram Petrosyan',
            'phone' => '+37410000000',
            'email' => 'aram@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-200001',
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops?shop='.$shop->id);

        $response
            ->assertOk()
            ->assertSee('Back to branch catalog')
            ->assertSee('href="/admin/shops"', false)
            ->assertSee('Reviewing: Galaxy Central')
            ->assertSee('Edit Galaxy branch in Galaxy foundation')
            ->assertSee('Save branch changes')
            ->assertSee('Create new Galaxy branch shell')
            ->assertSee('action="/admin/shops/'.$shop->id.'"', false)
            ->assertSee('Review branch scope')
            ->assertSee('Blocked until manager-linked branch scope is verified against live holder/card coverage and the legacy Galaxy multi-shop model.')
            ->assertSee('Selected shop')
            ->assertSee('Review mode')
            ->assertSee('Live branch review, this Galaxy foundation shop already carries operational visibility and should stay parity-first.')
            ->assertSee('Operational readiness')
            ->assertSee('active branch, operator-visible coverage live')
            ->assertSee('Lifecycle freshness')
            ->assertSee('newly created in Galaxy foundation review')
            ->assertSee('Last saved in Galaxy foundation')
            ->assertSee('Review note')
            ->assertSee('Keep branch ownership parity visible before any live scope mutation is trusted.')
            ->assertSee('Code')
            ->assertSee('Coverage signal')
            ->assertSee('manager, holder, and card coverage visible')
            ->assertSee('Shop status signal')
            ->assertSee('Active branch is already visible with manager and customer coverage for branch coverage parity review.')
            ->assertSee('Branch focus')
            ->assertSee('Start with manager ownership, holder coverage, and card coverage before discussing any later reassignment or scope-mutation flow.')
            ->assertSee('Branch posture')
            ->assertSee('Keep branch review in the live workspace first, then leave reassignment and scope-mutation flows gated until full branch parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep manager ownership, holder coverage, and card coverage together before trusting any later reassignment or branch-scope mutation discussion.')
            ->assertSee('Scope handoff signal')
            ->assertSee('Branch already shows enough ownership and customer coverage for a useful scope handoff review.')
            ->assertSee('Backend gap')
            ->assertSee('Branch writes, manager reassignment, and shop-scope mutation flows should stay preview-only until branch parity is verified.')
            ->assertSee('Assigned manager')
            ->assertSee('Manager guidance')
            ->assertSee('Keep current branch manager ownership visible during review, because legacy Galaxy branch administration depended on clear branch responsibility.')
            ->assertSee('Cardholders')
            ->assertSee('Cards')
            ->assertSee('Galaxy status')
            ->assertSee('Branch guidance')
            ->assertSee('This branch is already active in the Galaxy foundation layer, so scope and manager changes should stay parity-first until branch ownership rules are verified.')
            ->assertSee('Galaxy Central selected for Galaxy review')
            ->assertSee('Current request')
            ->assertSee('The shared shops workspace is now loading this saved branch from the Galaxy foundation layer instead of only static preview rows.')
            ->assertSee('Galaxy Central status reflected from model state')
            ->assertSee('Galaxy Central lifecycle freshness reflected from model state')
            ->assertSee('This branch was created in the Galaxy foundation layer on')
            ->assertSee('and has not been updated since, so operators are still reviewing the first saved branch shell.')
            ->assertSee('Galaxy Central last saved timestamp reflected from model state')
            ->assertSee('The latest saved Galaxy foundation timestamp for this branch is')
            ->assertSee('giving operators a concrete checkpoint for the current branch shell.')
            ->assertSee('Galaxy Central review note reflected from model state')
            ->assertSee('The current Galaxy foundation branch review note says: Keep branch ownership parity visible before any live scope mutation is trusted.')
            ->assertSee('Branch scope handoff stays visible in the workspace')
            ->assertSee('Operators should carry manager ownership, holder coverage, and card coverage in the live workspace before trusting any scope-mutation or reassignment follow-up.')
            ->assertSee('Branch posture:')
            ->assertSee('Selected-shop review is running in Galaxy foundation-backed read mode only')
            ->assertSee('Lifecycle freshness:')
            ->assertSee('newly created in Galaxy foundation review')
            ->assertSee('Last saved in Galaxy foundation:')
            ->assertSee('Review note:')
            ->assertSee('Keep branch ownership parity visible before any live scope mutation is trusted.')
            ->assertSee('Coverage signal:')
            ->assertSee('manager, holder, and card coverage visible')
            ->assertSee('Shop status signal:')
            ->assertSee('Active branch is already visible with manager and customer coverage for branch coverage parity review.')
            ->assertSee('Scope handoff signal:')
            ->assertSee('Branch already shows enough ownership and customer coverage for a useful scope handoff review.')
            ->assertSee('Status posture:')
            ->assertSee('This active branch is visible for review now, but manager and scope changes should stay blocked until legacy ownership rules are verified.')
            ->assertSee('Manager posture:')
            ->assertSee('Assigned branch managers are visible in the Galaxy foundation layer, but reassignment should stay blocked until Galaxy branch ownership parity is confirmed.')
            ->assertSee('Coverage posture:')
            ->assertSee('This branch currently exposes 1 cardholders and 1 cards for read-only Galaxy foundation review.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Branch writes, manager reassignment, and shop-scope mutation flows should stay preview-only until branch parity is verified.');
    }

    public function test_authenticated_user_can_create_shop_from_live_admin_flow(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.shops.store'), [
            'name' => '  Galaxy South  ',
            'code' => 'Galaxy South',
            'is_active' => 'true',
            'review_note' => 'Document branch parity before widening scope changes.',
        ]);

        $shop = Shop::query()->where('code', 'galaxy-south')->firstOrFail();

        $response
            ->assertRedirect(route('admin.shops.index', ['shop' => $shop], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Shop "Galaxy South" was created.');

        $this->assertDatabaseHas('shops', [
            'name' => 'Galaxy South',
            'code' => 'galaxy-south',
            'is_active' => true,
            'review_note' => 'Document branch parity before widening scope changes.',
        ]);
    }

    public function test_shop_scoped_admin_cannot_create_new_shop(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Creator Branch',
            'code' => 'galaxy-scoped-creator-branch',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Shop Creator Operator',
            'slug' => 'scoped-shop-creator-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Create scoped shops',
            'slug' => 'create-scoped-shops',
            'review_note' => 'Phase 1 scoped shop create coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->from('/admin/shops')->actingAs($user)->post(route('admin.shops.store'), [
            'name' => 'Galaxy Scoped Unauthorized Branch',
            'code' => 'galaxy-scoped-unauthorized-branch',
            'is_active' => 'true',
            'review_note' => 'Scoped admins should not create new branches during Phase 1.',
        ]);

        $response
            ->assertRedirect('/admin/shops#live-form')
            ->assertSessionHasErrors([
                'shop_id' => 'Only bootstrap admins can create new shops while the Galaxy branch foundation is still centrally controlled.',
            ]);

        $this->assertDatabaseMissing('shops', [
            'code' => 'galaxy-scoped-unauthorized-branch',
        ]);
    }

    public function test_authenticated_user_can_update_shop_from_live_admin_flow(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy South',
            'code' => 'galaxy-south',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.shops.update', $shop), [
            'name' => '  Galaxy South Prime  ',
            'code' => 'Galaxy South Prime',
            'is_active' => 'true',
            'review_note' => 'Keep manager ownership parity visible while this branch becomes active.',
        ]);

        $response
            ->assertRedirect(route('admin.shops.index', ['shop' => $shop], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Shop "Galaxy South Prime" was updated.');

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Galaxy South Prime',
            'code' => 'galaxy-south-prime',
            'is_active' => true,
            'review_note' => 'Keep manager ownership parity visible while this branch becomes active.',
        ]);
    }

    public function test_shop_live_flow_trims_name_and_rejects_duplicate_normalized_code(): void
    {
        $user = User::factory()->create();

        Shop::create([
            'name' => 'Galaxy Existing Branch',
            'code' => 'galaxy-existing-branch',
            'is_active' => true,
        ]);

        $response = $this->from('/admin/shops')->actingAs($user)->post(route('admin.shops.store'), [
            'name' => '  Galaxy Existing Branch Copy  ',
            'code' => ' Galaxy Existing Branch ',
            'is_active' => 'true',
            'review_note' => 'Duplicate normalized code should stay blocked in the Laravel branch shell.',
        ]);

        $response
            ->assertRedirect('/admin/shops#live-form')
            ->assertSessionHasErrors([
                'code' => 'This shop code is already in use.',
            ]);
    }

    public function test_shop_live_flow_normalizes_blank_review_note_to_null(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.shops.store'), [
            'name' => 'Galaxy Empty Note Branch',
            'code' => 'Galaxy Empty Note Branch',
            'is_active' => 'true',
            'review_note' => '   ',
        ]);

        $shop = Shop::query()->where('code', 'galaxy-empty-note-branch')->firstOrFail();

        $response
            ->assertRedirect(route('admin.shops.index', ['shop' => $shop], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Shop "Galaxy Empty Note Branch" was created.');

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'review_note' => null,
        ]);
    }

    public function test_shop_update_live_flow_normalizes_blank_review_note_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Branch Note Cleanup',
            'code' => 'galaxy-branch-note-cleanup',
            'is_active' => false,
            'review_note' => 'Keep this note visible until the next branch review pass.',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.shops.update', $shop), [
            'name' => 'Galaxy Branch Note Cleanup',
            'code' => 'Galaxy Branch Note Cleanup',
            'is_active' => 'false',
            'review_note' => '   ',
        ]);

        $response
            ->assertRedirect(route('admin.shops.index', ['shop' => $shop], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Shop "Galaxy Branch Note Cleanup" was updated.');

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'code' => 'galaxy-branch-note-cleanup',
            'review_note' => null,
        ]);
    }

    public function test_shop_update_live_flow_rejects_duplicate_normalized_code(): void
    {
        $user = User::factory()->create();

        $existingShop = Shop::create([
            'name' => 'Galaxy Existing Update Branch',
            'code' => 'galaxy-existing-update-branch',
            'is_active' => true,
        ]);

        $shopToUpdate = Shop::create([
            'name' => 'Galaxy Target Update Branch',
            'code' => 'galaxy-target-update-branch',
            'is_active' => false,
        ]);

        $response = $this->from(route('admin.shops.index', ['shop' => $shopToUpdate], absolute: false))
            ->actingAs($user)
            ->patch(route('admin.shops.update', $shopToUpdate), [
                'name' => '  Galaxy Target Update Branch  ',
                'code' => ' Galaxy Existing Update Branch ',
                'is_active' => 'true',
                'review_note' => 'Duplicate normalized branch code should stay blocked during live edit work.',
            ]);

        $response
            ->assertRedirect(route('admin.shops.index', ['shop' => $shopToUpdate], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'code' => 'This shop code is already in use.',
            ]);

        $this->assertDatabaseHas('shops', [
            'id' => $existingShop->id,
            'code' => 'galaxy-existing-update-branch',
        ]);

        $this->assertDatabaseHas('shops', [
            'id' => $shopToUpdate->id,
            'code' => 'galaxy-target-update-branch',
        ]);
    }

    public function test_shop_scoped_admin_cannot_update_a_different_shop(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Assigned Branch',
            'code' => 'galaxy-scoped-assigned-branch',
            'is_active' => true,
        ]);
        $otherShop = Shop::create([
            'name' => 'Galaxy Scoped Foreign Branch',
            'code' => 'galaxy-scoped-foreign-branch',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Shop Update Operator',
            'slug' => 'scoped-shop-update-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Update scoped shops',
            'slug' => 'update-scoped-shops',
            'review_note' => 'Phase 1 scoped shop update coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->patch(route('admin.shops.update', $otherShop), [
            'name' => 'Galaxy Scoped Foreign Branch Prime',
            'code' => 'galaxy-scoped-foreign-branch-prime',
            'is_active' => 'true',
            'review_note' => 'Scoped admins should not mutate foreign branch settings.',
        ]);

        $response
            ->assertRedirect(route('admin.shops.index', ['shop' => $otherShop], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'shop_id' => 'Choose a shop you can access before changing branch settings in the Galaxy workspace.',
            ]);

        $this->assertDatabaseHas('shops', [
            'id' => $otherShop->id,
            'name' => 'Galaxy Scoped Foreign Branch',
            'code' => 'galaxy-scoped-foreign-branch',
        ]);
    }

    public function test_shop_update_live_flow_keeps_branch_code_canonical(): void
    {
        $user = User::factory()->create();

        $shop = Shop::create([
            'name' => 'Galaxy Draft Branch',
            'code' => 'galaxy-draft-branch',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.shops.update', $shop), [
            'name' => '  Galaxy Draft Branch Prime  ',
            'code' => ' Galaxy Draft Branch Prime ',
            'is_active' => 'true',
            'review_note' => 'Keep branch code canonical while the live branch shell stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.shops.index', ['shop' => $shop], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Shop "Galaxy Draft Branch Prime" was updated.');

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Galaxy Draft Branch Prime',
            'code' => 'galaxy-draft-branch-prime',
            'is_active' => true,
        ]);
    }

    public function test_shop_update_live_flow_keeps_status_boolean_canonical(): void
    {
        $user = User::factory()->create();

        $shop = Shop::create([
            'name' => 'Galaxy Boolean Branch',
            'code' => 'galaxy-boolean-branch',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.shops.update', $shop), [
            'name' => '  Galaxy Boolean Branch Draft  ',
            'code' => ' Galaxy Boolean Branch Draft ',
            'is_active' => 'no',
            'review_note' => 'Keep branch status canonical while the live branch shell stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.shops.index', ['shop' => $shop], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Shop "Galaxy Boolean Branch Draft" was updated.');

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Galaxy Boolean Branch Draft',
            'code' => 'galaxy-boolean-branch-draft',
            'is_active' => false,
        ]);
    }

    public function test_shop_live_flow_keeps_status_boolean_canonical(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.shops.store'), [
            'name' => '  Galaxy Boolean Branch Create  ',
            'code' => ' Galaxy Boolean Branch Create ',
            'is_active' => 'no',
            'review_note' => 'Keep branch status canonical while the first live branch shell stays narrow.',
        ]);

        $shop = Shop::query()->where('code', 'galaxy-boolean-branch-create')->firstOrFail();

        $response
            ->assertRedirect(route('admin.shops.index', ['shop' => $shop], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Shop "Galaxy Boolean Branch Create" was created.');

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Galaxy Boolean Branch Create',
            'code' => 'galaxy-boolean-branch-create',
            'is_active' => false,
        ]);
    }

    public function test_shops_page_supports_selected_branch_coverage_without_manager_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Coverage Only Branch',
            'code' => 'galaxy-coverage-only-branch',
            'is_active' => true,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Lusine Coverage',
            'phone' => '+37410000221',
            'email' => 'lusine.coverage@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Coverage Tier',
            'slug' => 'galaxy-coverage-tier-shop',
            'points_rate' => '1.35',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-SHOP-0001',
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops?shop='.$shop->id);

        $response
            ->assertOk()
            ->assertSee('Back to branch catalog')
            ->assertSee('Reviewing: Galaxy Coverage Only Branch')
            ->assertSee('Review branch scope')
            ->assertSee('Blocked until visible branch coverage is verified against the legacy Galaxy multi-shop model.')
            ->assertSee('Selected shop')
            ->assertSee('Galaxy Coverage Only Branch')
            ->assertSee('Operational readiness')
            ->assertSee('active branch shell, ownership still forming')
            ->assertSee('Coverage signal')
            ->assertSee('branch records visible, manager coverage pending')
            ->assertSee('Shop status signal')
            ->assertSee('Active branch is already visible with customer coverage while manager ownership is still pending.')
            ->assertSee('Branch focus')
            ->assertSee('Start with manager ownership, holder coverage, and card coverage before discussing any later reassignment or scope-mutation flow.')
            ->assertSee('Branch posture')
            ->assertSee('Keep branch coverage review in the live workspace first, then leave ownership assignment, reassignment, and scope-mutation flows gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep holder coverage, card coverage, and ownership gaps together before trusting any later branch-scope mutation discussion.')
            ->assertSee('Scope handoff signal')
            ->assertSee('Customer coverage is visible, but ownership handoff is still incomplete for branch-scope review.')
            ->assertSee('Branch scope handoff stays visible in the workspace')
            ->assertSee('Operators should carry customer coverage, ownership gaps, and branch readiness in the live workspace before trusting any scope-mutation or reassignment follow-up.')
            ->assertSee('Backend gap')
            ->assertSee('Ownership assignment, branch writes, and shop-scope mutation flows should stay preview-only until branch coverage parity is verified.')
            ->assertSee('Assigned manager')
            ->assertSee('Unassigned')
            ->assertSee('Manager guidance')
            ->assertSee('No branch manager is assigned yet, so ownership expectations should stay parity-first until Galaxy branch ownership-assignment parity is verified.')
            ->assertSee('Manager posture:')
            ->assertSee('No branch manager is assigned yet, which keeps this Galaxy branch safer for ownership-flow parity review before ownership flows are enabled.');
    }

    public function test_shops_page_supports_selected_manager_linked_coverage_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Full Coverage Branch',
            'code' => 'galaxy-full-coverage-branch',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Narek Coverage Lead',
            'shop_id' => $shop->id,
        ]);

        $holder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Tatev Coverage Holder',
            'phone' => '+37410000231',
            'email' => 'tatev.coverage@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Full Coverage Tier',
            'slug' => 'galaxy-full-coverage-tier-shop',
            'points_rate' => '1.55',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-SHOP-0001',
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops?shop='.$shop->id);

        $response
            ->assertOk()
            ->assertSee('Back to branch catalog')
            ->assertSee('Reviewing: Galaxy Full Coverage Branch')
            ->assertSee('Review branch scope')
            ->assertSee('Blocked until manager-linked branch scope is verified against live holder/card coverage and the legacy Galaxy multi-shop model.')
            ->assertSee('Selected shop')
            ->assertSee('Galaxy Full Coverage Branch')
            ->assertSee('Operational readiness')
            ->assertSee('active branch, operator-visible coverage live')
            ->assertSee('Coverage signal')
            ->assertSee('manager, holder, and card coverage visible')
            ->assertSee('Shop status signal')
            ->assertSee('Active branch is already visible with manager and customer coverage for branch coverage parity review.')
            ->assertSee('Scope handoff signal')
            ->assertSee('Branch already shows enough ownership and customer coverage for a useful scope handoff review.')
            ->assertSee('Branch scope handoff stays visible in the workspace')
            ->assertSee('Operators should carry manager ownership, holder coverage, and card coverage in the live workspace before trusting any scope-mutation or reassignment follow-up.')
            ->assertSee('Assigned manager')
            ->assertSee('Narek Coverage Lead')
            ->assertSee('Manager guidance')
            ->assertSee('Keep current branch manager ownership visible during review, because legacy Galaxy branch administration depended on clear branch responsibility.');
    }

    public function test_shops_page_supports_selected_manager_only_branch_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Manager Only Branch',
            'code' => 'galaxy-manager-only-branch',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Tigran Managerov',
            'shop_id' => $shop->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops?shop='.$shop->id);

        $response
            ->assertOk()
            ->assertSee('Back to branch catalog')
            ->assertSee('Reviewing: Galaxy Manager Only Branch')
            ->assertSee('Review branch scope')
            ->assertSee('Blocked until manager-linked branch scope is verified against the legacy Galaxy multi-shop model.')
            ->assertSee('Selected shop')
            ->assertSee('Galaxy Manager Only Branch')
            ->assertSee('Operational readiness')
            ->assertSee('active branch, manager assigned and build-out pending')
            ->assertSee('Coverage signal')
            ->assertSee('manager coverage visible, branch records pending')
            ->assertSee('Shop status signal')
            ->assertSee('Active branch is already visible with manager ownership for rollout review.')
            ->assertSee('Branch posture')
            ->assertSee('Keep manager-owned branch review in the live workspace first, then leave coverage build-out, reassignment, and scope-mutation flows gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep manager ownership, branch readiness gaps, and missing holder or card coverage together before trusting any rollout-flow discussion.')
            ->assertSee('Scope handoff signal')
            ->assertSee('Manager ownership is visible, but customer coverage still needs to catch up before full scope handoff review.')
            ->assertSee('Branch scope handoff stays visible in the workspace')
            ->assertSee('Operators should carry manager ownership, branch readiness gaps, and missing customer coverage in the live workspace before trusting any scope-mutation or reassignment follow-up.')
            ->assertSee('Backend gap')
            ->assertSee('Coverage backfill writes, manager reassignment, and shop-scope mutation flows should stay preview-only until manager-led branch parity is verified.')
            ->assertSee('Assigned manager')
            ->assertSee('Tigran Managerov')
            ->assertSee('Manager guidance')
            ->assertSee('Keep current manager ownership visible during review, because legacy Galaxy branch administration depended on clear branch responsibility.');
    }

    public function test_shops_page_supports_selected_paused_branch_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Paused Branch',
            'code' => 'galaxy-paused-branch',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops?shop='.$shop->id);

        $response
            ->assertOk()
            ->assertSee('Back to branch catalog')
            ->assertSee('Reviewing: Galaxy Paused Branch')
            ->assertSee('Selected shop')
            ->assertSee('Review mode')
            ->assertSee('Paused-branch review, this shop remains safer for parity checks before operators treat it as fully reopened.')
            ->assertSee('Operational readiness')
            ->assertSee('paused branch, recovery review only')
            ->assertSee('Coverage signal')
            ->assertSee('manager and branch coverage pending')
            ->assertSee('Shop status signal')
            ->assertSee('Paused branch remains safer for reopening-parity review before any reopening-flow discussion.')
            ->assertSee('Branch focus')
            ->assertSee('Start with paused status, recovery ownership gaps, and branch coverage before discussing any later reopening, reassignment, or scope-recovery flow.')
            ->assertSee('Branch posture')
            ->assertSee('Keep paused-branch review in the live workspace first, then leave reopening, reassignment, and scope-mutation flows gated until recovery parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep paused status, recovery ownership gaps, and any visible holder or card coverage together before trusting any reopening, reassignment, or scope-recovery discussion.')
            ->assertSee('Scope handoff signal')
            ->assertSee('Paused branch should stay in recovery handoff-only posture until ownership and scope approval are explicit.')
            ->assertSee('Branch scope handoff stays visible in the workspace')
            ->assertSee('Operators should carry paused status, recovery ownership gaps, branch coverage, and scope approval context in the live workspace before trusting any recovery or reassignment follow-up.')
            ->assertSee('Backend gap')
            ->assertSee('Branch recovery writes, manager reassignment, ownership repair, and shop-scope mutation flows should stay preview-only until paused-branch parity is verified.')
            ->assertSee('Manager guidance')
            ->assertSee('No branch manager is assigned yet, so recovery ownership expectations should stay parity-first until paused Galaxy branch ownership-assignment parity is verified.')
            ->assertSee('Galaxy status')
            ->assertSee('paused')
            ->assertSee('Branch guidance')
            ->assertSee('This branch is still paused, so recovery, ownership, and scope review should stay parity-first before operators treat it as fully live.')
            ->assertSee('Shop status signal:')
            ->assertSee('Paused branch remains safer for reopening-parity review before any reopening-flow discussion.')
            ->assertSee('Scope handoff signal:')
            ->assertSee('Paused branch should stay in recovery handoff-only posture until ownership and scope approval are explicit.')
            ->assertSee('Status posture:')
            ->assertSee('This paused branch should stay review-only until recovery, ownership, and scope parity are verified.')
            ->assertSee('Manager posture:')
            ->assertSee('No branch manager is assigned yet, which keeps this paused Galaxy branch safer for recovery and ownership-flow parity review before ownership flows are enabled.')
            ->assertSee('Coverage posture:')
            ->assertSee('This paused branch currently exposes 0 cardholders and 0 cards for read-only Galaxy foundation recovery review.');
    }

    public function test_shops_page_ignores_unknown_selected_shop_query(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-unknown-shop',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Nare Gevorgyan',
            'shop_id' => $shop->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops?shop=999999');

        $response
            ->assertOk()
            ->assertSee('Galaxy Central')
            ->assertSee('Review latest saved branch shell')
            ->assertDontSee('Back to branch catalog')
            ->assertDontSee('Selected shop')
            ->assertDontSee('selected for Laravel review');
    }

    public function test_shops_page_ignores_malformed_selected_shop_query(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-malformed-shop',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Nare Gevorgyan',
            'shop_id' => $shop->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/shops?shop=not-a-number');

        $response
            ->assertOk()
            ->assertSee('Galaxy Central')
            ->assertSee('Review latest saved branch shell')
            ->assertDontSee('Back to branch catalog')
            ->assertDontSee('Selected shop')
            ->assertDontSee('selected for Laravel review');
    }

    public function test_shop_scoped_admin_sees_branch_creation_actions_disabled_in_shops_workspace(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Branch Workspace',
            'code' => 'galaxy-scoped-branch-workspace',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Scoped Branch Workspace Operator',
            'slug' => 'scoped-branch-workspace-operator',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'Review scoped branch workspace',
            'slug' => 'review-scoped-branch-workspace',
            'review_note' => 'Phase 1 scoped branch workspace action gating coverage.',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $catalogResponse = $this->actingAs($user)->get(route('admin.shops.index'));

        $catalogResponse
            ->assertOk()
            ->assertSee('Only bootstrap admins can create new Galaxy branch shells while Phase 1 keeps the branch foundation under central control.')
            ->assertSee('New Galaxy branch');

        $selectedResponse = $this->actingAs($user)->get(route('admin.shops.index', ['shop' => $assignedShop]));

        $selectedResponse
            ->assertOk()
            ->assertSee('Review the selected Galaxy branch in the shared live form while Phase 1 keeps branch-creation changes under bootstrap control.')
            ->assertSee('Back to branch catalog')
            ->assertDontSee('Create new Galaxy branch shell')
            ->assertSee('>Save branch changes<', false)
            ->assertSee('disabled title="Only bootstrap admins can create new Galaxy branch shells while Phase 1 keeps the branch foundation under central control." aria-disabled="true"', false)
            ->assertSee('name="name"', false)
            ->assertSee('readonly', false)
            ->assertSee('aria-readonly="true"', false)
            ->assertSee('name="is_active"', false)
            ->assertSee('aria-disabled="true"', false);
    }

    public function test_shops_page_hides_other_shop_review_links_for_shop_scoped_admins(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Home',
            'code' => 'galaxy-scoped-home',
            'is_active' => true,
        ]);

        $otherShop = Shop::create([
            'name' => 'Galaxy Scoped Other',
            'code' => 'galaxy-scoped-other',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Scoped Shop Reviewer',
            'slug' => 'scoped-shop-reviewer-index',
        ]);

        $permission = Permission::create([
            'name' => 'Review scoped shop workspace',
            'slug' => 'review-scoped-shop-workspace',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('href="/admin/shops?shop='.$assignedShop->id.'"', false)
            ->assertDontSee('href="/admin/shops?shop='.$otherShop->id.'"', false)
            ->assertSee('Review latest saved branch shell')
            ->assertSee('href="/admin/shops?shop='.$assignedShop->id.'"', false);
    }

    public function test_shops_page_ignores_inaccessible_selected_shop_query_for_shop_scoped_admins(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Review Home',
            'code' => 'galaxy-scoped-review-home',
            'is_active' => true,
        ]);

        $otherShop = Shop::create([
            'name' => 'Galaxy Scoped Review Other',
            'code' => 'galaxy-scoped-review-other',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Scoped Shop Selector',
            'slug' => 'scoped-shop-selector-index',
        ]);

        $permission = Permission::create([
            'name' => 'Select scoped shop workspace',
            'slug' => 'select-scoped-shop-workspace',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/admin/shops?shop='.$otherShop->id);

        $response
            ->assertOk()
            ->assertSee('Galaxy Scoped Review Home')
            ->assertSee('Galaxy Scoped Review Other')
            ->assertDontSee('Back to branch catalog')
            ->assertDontSee('Reviewing: Galaxy Scoped Review Other')
            ->assertDontSee('Selected shop')
            ->assertSee('Review latest saved branch shell')
            ->assertSee('href="/admin/shops?shop='.$assignedShop->id.'"', false);
    }

    public function test_cardholders_page_replaces_preview_rows_with_model_backed_index_data(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central',
            'is_active' => true,
        ]);

        $inactiveShop = Shop::create([
            'name' => 'Galaxy North',
            'code' => 'galaxy-north',
            'is_active' => false,
        ]);

        $activeHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Anna Petrova',
            'phone' => '+37491100001',
            'email' => 'anna@example.com',
            'review_note' => 'Keep duplicate-holder parity visible before profile merges are trusted.',
            'is_active' => true,
        ]);

        $inactiveHolder = CardHolder::create([
            'shop_id' => $inactiveShop->id,
            'full_name' => 'Arman Hakobyan',
            'phone' => '+37491100003',
            'email' => 'arman@example.com',
            'is_active' => false,
        ]);

        $activeUnlinkedHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Meri Unlinked Holder',
            'phone' => '+37491100004',
            'email' => 'meri@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime-cardholders',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $activeHolder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-300001',
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders');

        $response
            ->assertOk()
            ->assertSee('Anna Petrova')
            ->assertSee('href="/admin/cardholders?cardholder=', false)
            ->assertSee('+37491100001')
            ->assertSee('Galaxy Central')
            ->assertSee('Arman Hakobyan')
            ->assertSee('+37491100003')
            ->assertSee('Galaxy North')
            ->assertSee('Meri Unlinked Holder')
            ->assertSee('Keep duplicate-holder parity visible before profile merges are trusted.')
            ->assertSee('No review note saved yet')
            ->assertSee('Create Galaxy holder in Galaxy foundation')
            ->assertSee('Create holder shell')
            ->assertSee('Galaxy foundation status')
            ->assertSee('action="/admin/cardholders"', false)
            ->assertSee('Review latest saved holder shell')
            ->assertSee('Blocked until paused-branch linked-holder activity is verified against legacy lookup recovery history.')
            ->assertSee('Active-branch holders')
            ->assertSee('Paused-branch holders')
            ->assertSee('Active linked holders')
            ->assertSee('Inactive linked holders')
            ->assertSee('Active unlinked holders')
            ->assertSee('Inactive unlinked holders')
            ->assertSee('Active-branch linked holders')
            ->assertSee('Paused-branch unlinked holders')
            ->assertSee('Reviewed Galaxy holders')
            ->assertSee('This is the first minimal Galaxy foundation-backed cardholder write path. Keep it limited to profile identity, status, and review notes while card linkage and activity history remain parity-first review surfaces.')
            ->assertSee('Linked Galaxy card shells')
            ->assertSee('>1<', false)
            ->assertSee('active')
            ->assertSee('inactive');
    }

    public function test_authenticated_user_can_create_cardholder_from_live_admin_flow(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Live Branch',
            'code' => 'galaxy-holder-live-branch',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.cardholders.store'), [
            'shop_id' => (string) $shop->id,
            'full_name' => '  Mariam Sargsyan  ',
            'phone' => '  +37491100022  ',
            'email' => '  MARIAM.LIVE.CARDHOLDER@EXAMPLE.COM  ',
            'is_active' => 'true',
            'review_note' => 'Document duplicate-profile parity before widening lifecycle changes.',
        ]);

        $cardHolder = CardHolder::query()->where('email', 'mariam.live.cardholder@example.com')->firstOrFail();

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Cardholder "Mariam Sargsyan" was created.');

        $this->assertDatabaseHas('card_holders', [
            'shop_id' => $shop->id,
            'full_name' => 'Mariam Sargsyan',
            'phone' => '+37491100022',
            'email' => 'mariam.live.cardholder@example.com',
            'is_active' => true,
            'review_note' => 'Document duplicate-profile parity before widening lifecycle changes.',
        ]);
    }

    public function test_authenticated_user_can_update_cardholder_from_live_admin_flow(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Update Branch',
            'code' => 'galaxy-holder-update-branch',
            'is_active' => true,
        ]);
        $cardHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Mariam Sargsyan',
            'phone' => '+37491100022',
            'email' => 'mariam.live.cardholder@example.com',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cardholders.update', $cardHolder), [
            'shop_id' => (string) $shop->id,
            'full_name' => '  Mariam Sargsyan Prime  ',
            'phone' => '  +37491100033  ',
            'email' => '  MARIAM.PRIME.CARDHOLDER@EXAMPLE.COM  ',
            'is_active' => 'true',
            'review_note' => 'Keep lifecycle parity visible while this holder returns to active review.',
        ]);

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Cardholder "Mariam Sargsyan Prime" was updated.');

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'shop_id' => $shop->id,
            'full_name' => 'Mariam Sargsyan Prime',
            'phone' => '+37491100033',
            'email' => 'mariam.prime.cardholder@example.com',
            'is_active' => true,
            'review_note' => 'Keep lifecycle parity visible while this holder returns to active review.',
        ]);
    }

    public function test_shop_scoped_admin_cannot_create_cardholder_for_a_different_shop(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Assigned Holder Shop',
            'code' => 'galaxy-scoped-assigned-holder-shop',
            'is_active' => true,
        ]);
        $otherShop = Shop::create([
            'name' => 'Galaxy Scoped Foreign Holder Shop',
            'code' => 'galaxy-scoped-foreign-holder-shop',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Holder Operator',
            'slug' => 'scoped-holder-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Manage scoped holders',
            'slug' => 'manage-scoped-holders',
            'review_note' => 'Phase 1 scoped holder create coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->from('/admin/cardholders')->actingAs($user)->post(route('admin.cardholders.store'), [
            'shop_id' => (string) $otherShop->id,
            'full_name' => 'Scoped Foreign Holder',
            'phone' => '+37499114455',
            'email' => 'scoped.foreign.holder@example.com',
            'is_active' => 'true',
        ]);

        $response
            ->assertRedirect('/admin/cardholders#live-form')
            ->assertSessionHasErrors([
                'shop_id' => 'Choose a shop you can access so the Galaxy holder shell stays scoped to your assigned branch.',
            ]);

        $this->assertDatabaseMissing('card_holders', [
            'email' => 'scoped.foreign.holder@example.com',
        ]);
    }

    public function test_cardholder_live_flow_normalizes_contact_identity_fields(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Normalize Branch',
            'code' => 'galaxy-holder-normalize-branch',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.cardholders.store'), [
            'shop_id' => (string) $shop->id,
            'full_name' => '  Anna Petrosyan  ',
            'phone' => '  +37499111222  ',
            'email' => '  ANNA.PETROSYAN@EXAMPLE.COM  ',
            'is_active' => 'true',
            'review_note' => 'Normalize holder identity before widening duplicate-profile handling.',
        ]);

        $cardHolder = CardHolder::query()->where('email', 'anna.petrosyan@example.com')->firstOrFail();

        $response->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status');

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'full_name' => 'Anna Petrosyan',
            'phone' => '+37499111222',
            'email' => 'anna.petrosyan@example.com',
        ]);
    }

    public function test_cardholder_live_flow_normalizes_blank_contact_fields_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Null Contact Branch',
            'code' => 'galaxy-holder-null-contact-branch',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.cardholders.store'), [
            'shop_id' => (string) $shop->id,
            'full_name' => '  Arpi Sargsyan  ',
            'phone' => '   ',
            'email' => '   ',
            'is_active' => 'true',
            'review_note' => 'Normalize empty contact values before widening holder lifecycle work.',
        ]);

        $cardHolder = CardHolder::query()->where('full_name', 'Arpi Sargsyan')->firstOrFail();

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status');

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'phone' => null,
            'email' => null,
        ]);
    }

    public function test_cardholder_live_flow_normalizes_blank_review_note_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Empty Note Branch',
            'code' => 'galaxy-holder-empty-note-branch',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.cardholders.store'), [
            'shop_id' => (string) $shop->id,
            'full_name' => 'Marine Hovhannisyan',
            'phone' => '+37499117711',
            'email' => 'marine.hovhannisyan@example.com',
            'is_active' => 'true',
            'review_note' => '   ',
        ]);

        $cardHolder = CardHolder::query()->where('email', 'marine.hovhannisyan@example.com')->firstOrFail();

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Cardholder "Marine Hovhannisyan" was created.');

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'review_note' => null,
        ]);
    }

    public function test_shop_scoped_admin_cannot_update_cardholder_into_a_different_shop(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Update Assigned Holder Shop',
            'code' => 'galaxy-scoped-update-assigned-holder-shop',
            'is_active' => true,
        ]);
        $otherShop = Shop::create([
            'name' => 'Galaxy Scoped Update Foreign Holder Shop',
            'code' => 'galaxy-scoped-update-foreign-holder-shop',
            'is_active' => true,
        ]);
        $cardHolder = CardHolder::create([
            'shop_id' => $assignedShop->id,
            'full_name' => 'Scoped Update Holder',
            'phone' => '+37499119944',
            'email' => 'scoped.update.holder@example.com',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Holder Update Operator',
            'slug' => 'scoped-holder-update-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Update scoped holders',
            'slug' => 'update-scoped-holders',
            'review_note' => 'Phase 1 scoped holder update coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->patch(route('admin.cardholders.update', $cardHolder), [
            'shop_id' => (string) $otherShop->id,
            'full_name' => 'Scoped Update Holder',
            'phone' => '+37499119944',
            'email' => 'scoped.update.holder@example.com',
            'is_active' => 'true',
        ]);

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'shop_id' => 'Choose a shop you can access so the Galaxy holder shell stays scoped to your assigned branch.',
            ]);

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'shop_id' => $assignedShop->id,
        ]);
    }

    public function test_shop_scoped_admin_cannot_update_foreign_cardholder_even_if_target_shop_is_accessible(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Assigned Holder Guard Shop',
            'code' => 'galaxy-scoped-assigned-holder-guard-shop',
            'is_active' => true,
        ]);
        $otherShop = Shop::create([
            'name' => 'Galaxy Scoped Foreign Holder Guard Shop',
            'code' => 'galaxy-scoped-foreign-holder-guard-shop',
            'is_active' => true,
        ]);
        $cardHolder = CardHolder::create([
            'shop_id' => $otherShop->id,
            'full_name' => 'Scoped Foreign Guard Holder',
            'phone' => '+37499119988',
            'email' => 'scoped.foreign.guard.holder@example.com',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Holder Guard Operator',
            'slug' => 'scoped-holder-guard-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Guard scoped holders',
            'slug' => 'guard-scoped-holders',
            'review_note' => 'Phase 1 foreign holder update guard coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->patch(route('admin.cardholders.update', $cardHolder), [
            'shop_id' => (string) $assignedShop->id,
            'full_name' => 'Scoped Foreign Guard Holder',
            'phone' => '+37499119988',
            'email' => 'scoped.foreign.guard.holder@example.com',
            'is_active' => 'true',
        ]);

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'shop_id' => 'Choose a cardholder from a shop you can access before changing holder details in the Galaxy workspace.',
            ]);

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'shop_id' => $otherShop->id,
        ]);
    }

    public function test_cardholder_update_live_flow_normalizes_blank_review_note_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Note Cleanup Branch',
            'code' => 'galaxy-holder-note-cleanup-branch',
            'is_active' => true,
        ]);
        $cardHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Marine Cleanup',
            'phone' => '+37499118822',
            'email' => 'marine.cleanup@example.com',
            'is_active' => true,
            'review_note' => 'Clear this note while keeping the holder shell live.',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cardholders.update', $cardHolder), [
            'shop_id' => (string) $shop->id,
            'full_name' => 'Marine Cleanup',
            'phone' => '+37499118822',
            'email' => 'marine.cleanup@example.com',
            'is_active' => 'true',
            'review_note' => '   ',
        ]);

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Cardholder "Marine Cleanup" was updated.');

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'review_note' => null,
        ]);
    }

    public function test_cardholder_update_live_flow_normalizes_blank_contact_fields_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Contact Cleanup Branch',
            'code' => 'galaxy-holder-contact-cleanup-branch',
            'is_active' => true,
        ]);
        $cardHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Contact Cleanup Holder',
            'phone' => '+37499119933',
            'email' => 'contact.cleanup@example.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cardholders.update', $cardHolder), [
            'shop_id' => (string) $shop->id,
            'full_name' => 'Contact Cleanup Holder',
            'phone' => '   ',
            'email' => '   ',
            'is_active' => 'true',
            'review_note' => 'Keep the holder shell live while contact cleanup stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Cardholder "Contact Cleanup Holder" was updated.');

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'phone' => null,
            'email' => null,
        ]);
    }

    public function test_cardholder_update_live_flow_keeps_contact_identity_canonical(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Canonical Contact Branch',
            'code' => 'galaxy-holder-canonical-contact-branch',
            'is_active' => true,
        ]);
        $cardHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Canonical Contact Holder',
            'phone' => '+37499116655',
            'email' => 'canonical.contact@example.com',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cardholders.update', $cardHolder), [
            'shop_id' => (string) $shop->id,
            'full_name' => '  Canonical Contact Holder Prime  ',
            'phone' => '  +37499116677  ',
            'email' => '  CANONICAL.CONTACT.PRIME@EXAMPLE.COM  ',
            'is_active' => 'true',
            'review_note' => 'Keep holder contact identity canonical while the live shell stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Cardholder "Canonical Contact Holder Prime" was updated.');

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'full_name' => 'Canonical Contact Holder Prime',
            'phone' => '+37499116677',
            'email' => 'canonical.contact.prime@example.com',
            'is_active' => true,
        ]);
    }

    public function test_cardholder_update_live_flow_keeps_status_boolean_canonical(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Boolean Branch',
            'code' => 'galaxy-holder-boolean-branch',
            'is_active' => true,
        ]);
        $cardHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Boolean Holder',
            'phone' => '+37499115544',
            'email' => 'boolean.holder@example.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cardholders.update', $cardHolder), [
            'shop_id' => (string) $shop->id,
            'full_name' => '  Boolean Holder Draft  ',
            'phone' => '  +37499115599  ',
            'email' => '  BOOLEAN.HOLDER.DRAFT@EXAMPLE.COM  ',
            'is_active' => 'no',
            'review_note' => 'Keep holder status canonical while the live shell stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Cardholder "Boolean Holder Draft" was updated.');

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'full_name' => 'Boolean Holder Draft',
            'phone' => '+37499115599',
            'email' => 'boolean.holder.draft@example.com',
            'is_active' => false,
        ]);
    }

    public function test_cardholder_live_flow_keeps_status_boolean_canonical(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Holder Boolean Create Branch',
            'code' => 'galaxy-holder-boolean-create-branch',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.cardholders.store'), [
            'shop_id' => (string) $shop->id,
            'full_name' => '  Boolean Holder Create  ',
            'phone' => '  +37499115511  ',
            'email' => '  BOOLEAN.HOLDER.CREATE@EXAMPLE.COM  ',
            'is_active' => 'no',
            'review_note' => 'Keep holder status canonical while the first live shell stays narrow.',
        ]);

        $cardHolder = CardHolder::query()->where('email', 'boolean.holder.create@example.com')->firstOrFail();

        $response
            ->assertRedirect(route('admin.cardholders.index', ['cardholder' => $cardHolder], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Cardholder "Boolean Holder Create" was created.');

        $this->assertDatabaseHas('card_holders', [
            'id' => $cardHolder->id,
            'full_name' => 'Boolean Holder Create',
            'phone' => '+37499115511',
            'email' => 'boolean.holder.create@example.com',
            'is_active' => false,
        ]);
    }

    public function test_cardholders_catalog_actions_reflect_saved_holder_readiness(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Holder Catalog',
            'code' => 'galaxy-holder-catalog',
            'is_active' => true,
        ]);

        CardHolder::create([
            'full_name' => 'Inactive Holder Catalog',
            'phone' => '+37499110001',
            'status' => 'inactive',
            'is_active' => false,
            'shop_id' => $shop->id,
        ]);

        $activeHolder = CardHolder::create([
            'full_name' => 'Linked Holder Catalog',
            'phone' => '+37499110002',
            'status' => 'active',
            'is_active' => true,
            'shop_id' => $shop->id,
        ]);

        $cardType = CardType::create([
            'name' => 'Holder Catalog Gold',
            'slug' => 'holder-catalog-gold',
            'points_rate' => 1.50,
            'is_active' => true,
        ]);

        Card::create([
            'number' => 'GX-HOLDER-CATALOG-1',
            'status' => 'active',
            'card_holder_id' => $activeHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
            'issued_at' => now(),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders');

        $response
            ->assertOk()
            ->assertSee('New Galaxy holder')
            ->assertSee('Review recent activity')
            ->assertSee('Blocked until linked-holder activity coverage is verified against legacy lookup history.');
    }

    public function test_cardholders_page_surfaces_selected_holder_context_from_laravel_data(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-selected-holder',
            'is_active' => true,
        ]);

        $cardHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Anna Petrova',
            'phone' => '+37491100001',
            'email' => 'anna-selected-holder@example.com',
            'is_active' => false,
            'review_note' => 'Keep dormant-holder lookup parity anchored to the branch before any reactivation follow-up.',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders?cardholder='.$cardHolder->id);

        $response
            ->assertOk()
            ->assertSee('Back to holder catalog')
            ->assertSee('href="/admin/cardholders"', false)
            ->assertSee('Reviewing: Anna Petrova')
            ->assertSee('Edit Galaxy holder in Galaxy foundation')
            ->assertSee('Save holder changes')
            ->assertSee('Back to holder catalog')
            ->assertDontSee('Create new Galaxy holder shell')
            ->assertSee('action="/admin/cardholders/'.$cardHolder->id.'"', false)
            ->assertSee('Review recent activity')
            ->assertSee('Blocked until inactive-holder activity history is backed by a stable Galaxy foundation event source for lifecycle parity.')
            ->assertSee('Selected holder')
            ->assertSee('Holder status signal')
            ->assertSee('Inactive holder remains safer for reactivation-flow-parity review before any reactivation-flow discussion.')
            ->assertSee('Review mode')
            ->assertSee('Dormant-profile review, this inactive holder stays safer for parity checks before any reactivation path is trusted.')
            ->assertSee('Operational readiness')
            ->assertSee('inactive profile, review only')
            ->assertSee('Lifecycle freshness')
            ->assertSee('newly created in Galaxy foundation review')
            ->assertSee('Last saved in Galaxy foundation')
            ->assertSee('Review note')
            ->assertSee('Keep dormant-holder lookup parity anchored to the branch before any reactivation follow-up.')
            ->assertSee('Phone')
            ->assertSee('Linkage signal')
            ->assertSee('branch-linked profile, card linkage pending')
            ->assertSee('Shop activity signal')
            ->assertSee('Holder is anchored to an active branch for live lookup review.')
            ->assertSee('Holder focus')
            ->assertSee('Start with inactive status, branch linkage, and linked-card visibility before discussing any later reactivation or profile merge flow.')
            ->assertSee('Holder posture')
            ->assertSee('Keep inactive holder review in the workspace first, then leave reactivation, merge, and profile-write flows gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep inactive status, branch linkage, and linked-card visibility together before trusting any later reactivation or merge discussion.')
            ->assertSee('Activity handoff signal')
            ->assertSee('Dormant holder should stay in handoff-only posture until reactivation parity is explicit.')
            ->assertSee('Backend gap')
            ->assertSee('Reactivation handling, profile writes, and recent-activity sourcing should stay preview-only until holder parity is verified.')
            ->assertSee('Shop')
            ->assertSee('Shop guidance')
            ->assertSee('Keep this holder anchored to the current branch during review, because old Galaxy lookup flows depended on branch-aware identity context.')
            ->assertSee('Linked cards')
            ->assertSee('Galaxy status')
            ->assertSee('Lookup guidance')
            ->assertSee('This holder is inactive in the Galaxy foundation layer, which keeps the record safe for parity checks before operators treat it as fully reactivated.')
            ->assertSee('Anna Petrova selected for Galaxy review')
            ->assertSee('Current request')
            ->assertSee('The shared cardholders workspace is now loading this saved holder from the Galaxy foundation layer instead of only static preview rows.')
            ->assertSee('Anna Petrova status reflected from model state')
            ->assertSee('Anna Petrova lifecycle freshness reflected from model state')
            ->assertSee('This holder was created in the Galaxy foundation layer on')
            ->assertSee('and has not been updated since, so operators are still reviewing the first saved profile shell.')
            ->assertSee('Anna Petrova last saved timestamp reflected from model state')
            ->assertSee('The latest saved Galaxy foundation timestamp for this holder is')
            ->assertSee('giving operators a concrete checkpoint for the current profile shell.')
            ->assertSee('Anna Petrova review note reflected from model state')
            ->assertSee('The current Galaxy foundation holder review note says: Keep dormant-holder lookup parity anchored to the branch before any reactivation follow-up.')
            ->assertSee('Holder activity handoff stays visible in the workspace')
            ->assertSee('Operators should carry inactive status, branch context, and card-linkage gaps in the live workspace before trusting any reactivation or merge follow-up.')
            ->assertSee('Lookup posture:')
            ->assertSee('Selected-holder review is running in Galaxy foundation-backed read mode only')
            ->assertSee('Lifecycle freshness:')
            ->assertSee('newly created in Galaxy foundation review')
            ->assertSee('Last saved in Galaxy foundation:')
            ->assertSee('Review note:')
            ->assertSee('Keep dormant-holder lookup parity anchored to the branch before any reactivation follow-up.')
            ->assertSee('Linkage signal:')
            ->assertSee('branch-linked profile, card linkage pending')
            ->assertSee('Shop activity signal:')
            ->assertSee('Holder is anchored to an active branch for live lookup review.')
            ->assertSee('Activity handoff signal:')
            ->assertSee('Dormant holder should stay in handoff-only posture until reactivation parity is explicit.')
            ->assertSee('Status posture:')
            ->assertSee('This inactive holder should stay review-only until reactivation and duplicate-profile rules are verified.')
            ->assertSee('Card linkage posture:')
            ->assertSee('No linked cards exist yet, which keeps this holder safer for card-link-parity review before card-link flows are enabled.')
            ->assertSee('Activity posture:')
            ->assertSee('Recent activity remains blocked until a stable Galaxy foundation event source exists for holder lookup parity.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Reactivation handling, profile writes, and recent-activity sourcing should stay preview-only until holder parity is verified.');
    }

    public function test_cardholders_page_supports_selected_active_linked_holder_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Linked Holder Branch',
            'code' => 'galaxy-linked-holder-branch',
            'is_active' => true,
        ]);

        $cardHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Mariam Linked Holder',
            'phone' => '+37491100077',
            'email' => 'mariam.linked.holder@example.com',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Linked Tier',
            'slug' => 'galaxy-linked-tier-holder',
            'points_rate' => '1.75',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $cardHolder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-LINK-0001',
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders?cardholder='.$cardHolder->id);

        $response
            ->assertOk()
            ->assertSee('Back to holder catalog')
            ->assertSee('Reviewing: Mariam Linked Holder')
            ->assertSee('Review recent activity')
            ->assertSee('Blocked until linked-card activity is backed by a stable Galaxy foundation event source for active-holder lookup parity.')
            ->assertSee('Selected holder')
            ->assertSee('Mariam Linked Holder')
            ->assertSee('Holder status signal')
            ->assertSee('Active holder is already visible for live profile parity review.')
            ->assertSee('Operational readiness')
            ->assertSee('linked profile, operator-visible')
            ->assertSee('Linkage signal')
            ->assertSee('branch-linked profile with visible cards')
            ->assertSee('Holder focus')
            ->assertSee('Start with active status, branch linkage, and linked-card visibility before discussing any later profile merge or reactivation edge case.')
            ->assertSee('Holder posture')
            ->assertSee('Keep live holder review in the workspace first, then leave profile-write, merge, and lifecycle-change flows gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep active status, branch linkage, and linked-card visibility together before trusting any later profile merge or lifecycle-change discussion.')
            ->assertSee('Activity handoff signal')
            ->assertSee('Active holder already carries linked-card context for a useful activity handoff review.')
            ->assertSee('Holder activity handoff stays visible in the workspace')
            ->assertSee('Operators should carry active status, linked-card evidence, and branch context in the live workspace before trusting any lifecycle-change or merge follow-up.')
            ->assertSee('Backend gap')
            ->assertSee('Profile writes, merge handling, and recent-activity sourcing should stay preview-only until holder parity is verified.')
            ->assertSee('Linked cards')
            ->assertSee('1')
            ->assertSee('Lookup guidance')
            ->assertSee('This holder is active in the Galaxy foundation layer, so identity and linkage review should stay parity-first until recent-activity sourcing is verified.');
    }

    public function test_cardholders_page_supports_selected_active_unlinked_holder_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Active Unlinked Branch',
            'code' => 'galaxy-active-unlinked-branch',
            'is_active' => true,
        ]);

        $cardHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Aram Unlinked Holder',
            'phone' => '+37491100087',
            'email' => 'aram.unlinked.holder@example.com',
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders?cardholder='.$cardHolder->id);

        $response
            ->assertOk()
            ->assertSee('Back to holder catalog')
            ->assertSee('Reviewing: Aram Unlinked Holder')
            ->assertSee('Review recent activity')
            ->assertSee('Blocked until a stable Galaxy foundation activity source exists for holder lookup parity.')
            ->assertSee('Selected holder')
            ->assertSee('Aram Unlinked Holder')
            ->assertSee('Holder status signal')
            ->assertSee('Active holder is already visible for live profile parity review.')
            ->assertSee('Operational readiness')
            ->assertSee('active profile, linkage build-out pending')
            ->assertSee('Linkage signal')
            ->assertSee('branch-linked profile, card linkage pending')
            ->assertSee('Holder focus')
            ->assertSee('Start with active status, branch linkage, and linked-card visibility before discussing any later profile merge or reactivation edge case.')
            ->assertSee('Holder posture')
            ->assertSee('Keep live holder review in the workspace first, then leave profile-write, merge, and lifecycle-change flows gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep active status, branch linkage, and linked-card visibility together before trusting any later profile merge or lifecycle-change discussion.')
            ->assertSee('Activity handoff signal')
            ->assertSee('Active holder exists, but linked-card activity context is still thin for handoff review.')
            ->assertSee('Holder activity handoff stays visible in the workspace')
            ->assertSee('Operators should carry active status, branch context, and card-linkage gaps in the live workspace before trusting any lifecycle-change or merge follow-up.')
            ->assertSee('Backend gap')
            ->assertSee('Profile writes, merge handling, and recent-activity sourcing should stay preview-only until holder parity is verified.')
            ->assertSee('Lookup guidance')
            ->assertSee('This holder is active in the Galaxy foundation layer, so identity and linkage review should stay parity-first until recent-activity sourcing is verified.')
            ->assertSee('Card linkage posture:')
            ->assertSee('No linked cards exist yet, which keeps this holder safer for card-link-parity review before card-link flows are enabled.');
    }

    public function test_cardholders_page_supports_selected_inactive_linked_holder_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Dormant Linked Branch',
            'code' => 'galaxy-dormant-linked-branch',
            'is_active' => true,
        ]);

        $cardHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Sona Dormant Holder',
            'phone' => '+37491100088',
            'email' => 'sona.dormant.holder@example.com',
            'is_active' => false,
        ]);

        $cardType = CardType::create([
            'name' => 'Galaxy Dormant Tier',
            'slug' => 'galaxy-dormant-tier-holder',
            'points_rate' => '1.20',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $cardHolder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-DORM-0001',
            'status' => 'blocked',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders?cardholder='.$cardHolder->id);

        $response
            ->assertOk()
            ->assertSee('Back to holder catalog')
            ->assertSee('Reviewing: Sona Dormant Holder')
            ->assertSee('Review recent activity')
            ->assertSee('Blocked until linked-card activity is backed by a stable Galaxy foundation event source for holder lookup parity.')
            ->assertSee('Selected holder')
            ->assertSee('Sona Dormant Holder')
            ->assertSee('Holder status signal')
            ->assertSee('Inactive holder remains safer for reactivation-flow-parity review before any reactivation-flow discussion.')
            ->assertSee('Operational readiness')
            ->assertSee('inactive profile, review only')
            ->assertSee('Linkage signal')
            ->assertSee('branch-linked profile with visible cards')
            ->assertSee('Holder focus')
            ->assertSee('Start with inactive status, branch linkage, and linked-card visibility before discussing any later reactivation or profile merge flow.')
            ->assertSee('Holder posture')
            ->assertSee('Keep inactive holder review in the workspace first, then leave reactivation, merge, and profile-write flows gated until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep inactive status, branch linkage, and linked-card visibility together before trusting any later reactivation or merge discussion.')
            ->assertSee('Activity handoff signal')
            ->assertSee('Dormant holder already carries linked-card evidence for a useful lifecycle handoff review.')
            ->assertSee('Holder activity handoff stays visible in the workspace')
            ->assertSee('Operators should carry inactive status, linked-card evidence, and branch context in the live workspace before trusting any reactivation or merge follow-up.')
            ->assertSee('Backend gap')
            ->assertSee('Reactivation handling, profile writes, and recent-activity sourcing should stay preview-only until holder parity is verified.')
            ->assertSee('Lookup guidance')
            ->assertSee('This holder is inactive in the Galaxy foundation layer, which keeps the record safe for parity checks before operators treat it as fully reactivated.')
            ->assertSee('Card linkage posture:')
            ->assertSee('Linked cards are visible in the Galaxy foundation layer, but card-to-holder lifecycle changes should stay parity-first until activity sourcing is verified.');
    }

    public function test_cardholders_page_surfaces_paused_branch_signal_for_selected_holder(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Paused Holder Branch',
            'code' => 'galaxy-paused-holder-branch',
            'is_active' => false,
        ]);

        $cardHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Lilit Paused Branch Holder',
            'phone' => '+37491100089',
            'email' => 'lilit.paused.branch.holder@example.com',
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders?cardholder='.$cardHolder->id);

        $response
            ->assertOk()
            ->assertSee('Reviewing: Lilit Paused Branch Holder')
            ->assertSee('Blocked until paused-branch activity history is backed by a stable Galaxy foundation event source for recovery-parity review.')
            ->assertSee('Shop activity signal')
            ->assertSee('Holder is anchored to a paused branch, so branch-recovery context should stay visible during lookup review.')
            ->assertSee('Activity handoff signal')
            ->assertSee('Paused-branch holder should carry branch-recovery context forward until lookup and reactivation parity are explicit.')
            ->assertSee('Holder activity handoff stays visible in the workspace')
            ->assertSee('Operators should carry paused-branch context, holder status, and card-linkage gaps in the live workspace before trusting any reactivation or merge follow-up.')
            ->assertSee('Holder focus')
            ->assertSee('Start with paused-branch status, branch linkage, and linked-card visibility before discussing any later recovery, profile merge, or lifecycle-change edge case.')
            ->assertSee('Shop activity signal:')
            ->assertSee('Holder is anchored to a paused branch, so branch-recovery context should stay visible during lookup review.')
            ->assertSee('Activity handoff signal:')
            ->assertSee('Paused-branch holder should carry branch-recovery context forward until lookup and reactivation parity are explicit.')
            ->assertSee('Status posture:')
            ->assertSee('This holder is visible in a paused branch, so lifecycle changes should stay blocked until branch-recovery and lookup parity are verified.')
            ->assertSee('Activity posture:')
            ->assertSee('Recent activity remains blocked until a stable Galaxy foundation event source preserves paused-branch lookup and recovery parity.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Recovery handling, profile writes, merge handling, and recent-activity sourcing should stay preview-only until paused-branch holder parity is verified.')
            ->assertSee('Lookup guidance:')
            ->assertSee('This holder is active in the Galaxy foundation layer but anchored to a paused branch, so identity, linkage, and recovery review should stay parity-first until recent-activity sourcing is verified.');
    }

    public function test_cardholders_page_ignores_unknown_selected_holder_query(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-unknown-holder',
            'is_active' => true,
        ]);

        CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Anna Petrova',
            'phone' => '+37491100001',
            'email' => 'anna-unknown-holder@example.com',
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders?cardholder=999999');

        $response
            ->assertOk()
            ->assertSee('Anna Petrova')
            ->assertSee('Review latest saved holder shell')
            ->assertDontSee('Back to holder catalog')
            ->assertDontSee('Selected holder')
            ->assertDontSee('selected for Laravel review');
    }

    public function test_cardholders_page_ignores_malformed_selected_holder_query(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-malformed-holder',
            'is_active' => true,
        ]);

        CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Anna Petrova',
            'phone' => '+37491100001',
            'email' => 'anna-malformed-holder@example.com',
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders?cardholder=not-a-number');

        $response
            ->assertOk()
            ->assertSee('Anna Petrova')
            ->assertSee('Review latest saved holder shell')
            ->assertDontSee('Back to holder catalog')
            ->assertDontSee('Selected holder')
            ->assertDontSee('selected for Laravel review');
    }

    public function test_cardholders_page_hides_other_shop_holder_review_links_for_shop_scoped_admins(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Holder Home',
            'code' => 'galaxy-holder-home',
            'is_active' => true,
        ]);

        $otherShop = Shop::create([
            'name' => 'Galaxy Holder Other',
            'code' => 'galaxy-holder-other',
            'is_active' => true,
        ]);

        $assignedHolder = CardHolder::create([
            'shop_id' => $assignedShop->id,
            'full_name' => 'Assigned Holder',
            'phone' => '+37491111111',
            'email' => 'assigned-holder@example.com',
            'is_active' => true,
        ]);

        $otherHolder = CardHolder::create([
            'shop_id' => $otherShop->id,
            'full_name' => 'Other Holder',
            'phone' => '+37492222222',
            'email' => 'other-holder@example.com',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Scoped Holder Reviewer',
            'slug' => 'scoped-holder-reviewer-index',
        ]);

        $permission = Permission::create([
            'name' => 'Review scoped holder workspace',
            'slug' => 'review-scoped-holder-workspace',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/admin/cardholders');

        $response
            ->assertOk()
            ->assertSee('href="/admin/cardholders?cardholder='.$assignedHolder->id.'"', false)
            ->assertDontSee('href="/admin/cardholders?cardholder='.$otherHolder->id.'"', false)
            ->assertSee('Review latest saved holder shell')
            ->assertSee('href="/admin/cardholders?cardholder='.$assignedHolder->id.'"', false);
    }

    public function test_cardholders_page_ignores_inaccessible_selected_holder_query_for_shop_scoped_admins(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Holder Review Home',
            'code' => 'galaxy-holder-review-home',
            'is_active' => true,
        ]);

        $otherShop = Shop::create([
            'name' => 'Galaxy Holder Review Other',
            'code' => 'galaxy-holder-review-other',
            'is_active' => true,
        ]);

        CardHolder::create([
            'shop_id' => $assignedShop->id,
            'full_name' => 'Assigned Holder Review',
            'phone' => '+37493333333',
            'email' => 'assigned-holder-review@example.com',
            'is_active' => true,
        ]);

        $otherHolder = CardHolder::create([
            'shop_id' => $otherShop->id,
            'full_name' => 'Other Holder Review',
            'phone' => '+37494444444',
            'email' => 'other-holder-review@example.com',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);

        $role = Role::create([
            'name' => 'Scoped Holder Selector',
            'slug' => 'scoped-holder-selector-index',
        ]);

        $permission = Permission::create([
            'name' => 'Select scoped holder workspace',
            'slug' => 'select-scoped-holder-workspace',
        ]);

        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/admin/cardholders?cardholder='.$otherHolder->id);

        $response
            ->assertOk()
            ->assertSee('Assigned Holder Review')
            ->assertSee('Other Holder Review')
            ->assertDontSee('Back to holder catalog')
            ->assertDontSee('Reviewing: Other Holder Review')
            ->assertDontSee('Selected holder')
            ->assertSee('Review latest saved holder shell');
    }

    public function test_authenticated_user_can_access_checks_points_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/checks-points');

        $response
            ->assertOk()
            ->assertSeeText('Operations / Checks & Points')
            ->assertSee('CHK-90421')
            ->assertSee('/admin/checks-points?receipt=chk-90421')
            ->assertSee('Fiscal receipt')
            ->assertSee('Find receipt')
            ->assertSee('Blocked until fiscal receipt lookup is verified against branch-aware transaction history and legacy search habits.')
            ->assertSee('Review accrual gaps')
            ->assertSee('Blocked until zero-accrual and branch-aware troubleshooting are backed by Laravel transaction and rule data.')
            ->assertSee('Review chk-90421 receipt')
            ->assertSee('Receipts listed')
            ->assertSee('Positive accruals')
            ->assertSee('Zero accruals')
            ->assertSee('Galaxy receipt and accrual workspace for purchases, fiscal search, and point adjustments.')
            ->assertSee('Checks and points operations are still preview-only')
            ->assertSee('Receipt lookup actions, accrual metrics, and troubleshooting cues are shaping the final Galaxy flow, but real Laravel transaction reads do not exist yet.')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview receipt lookup surface and accrual metrics are defined')
            ->assertSee('Transaction tables, receipt reads, and adjustment flows still need PHP-backed Laravel wiring')
            ->assertSee('Implementation dependencies')
            ->assertSee('Transaction domain tables do not exist yet')
            ->assertSee('Receipt history queries and adjustment handlers are still pending')
            ->assertSee('First Galaxy foundation wiring step')
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

    public function test_checks_points_page_supports_selected_receipt_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/checks-points?receipt=chk-90407');

        $response
            ->assertOk()
            ->assertSee('Back to receipt catalog')
            ->assertSee('/admin/checks-points')
            ->assertSee('Reviewing: CHK-90407')
            ->assertSee('Find receipt')
            ->assertSee('Blocked until receipt lookup is backed by Galaxy foundation transaction reads and fiscal-search parity checks.')
            ->assertSee('Review accrual gaps')
            ->assertSee('Blocked until zero-accrual review is backed by Galaxy foundation transaction and rule data.')
            ->assertSee('Selected receipt preview')
            ->assertSee('CHK-90407')
            ->assertSee('Receipt status signal')
            ->assertSee('Zero-accrual receipt remains highly visible for parity troubleshooting review.')
            ->assertSee('Receipt focus')
            ->assertSee('Start with the zero-point outcome before expanding into broader rule-gap discussion.')
            ->assertSee('Receipt handoff signal')
            ->assertSee('Carry receipt evidence and zero-point context forward before escalating any rule-gap discussion.')
            ->assertSee('Receipt posture')
            ->assertSee('Receipt lookup should stay read-only until Galaxy foundation transaction history is verified against legacy fiscal search behavior.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep receipt, amount, and zero-point outcome visible together before expanding into broader rule troubleshooting.')
            ->assertSee('Accrual posture')
            ->assertSee('Zero-accrual receipts should stay highly visible, because they drive the most parity-sensitive troubleshooting in the old Galaxy flow.')
            ->assertSee('Backend gap')
            ->assertSee('Receipt reads, zero-accrual rule traces, and adjustment handlers should stay preview-only until fiscal-search parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep zero-accrual receipts in compact on-screen review first, because operators need amount, points, and rule context together before escalating.')
            ->assertSee('Troubleshooting guidance')
            ->assertSee('Treat this receipt as read-only review until Galaxy foundation transaction history and rule-backed explanations exist.')
            ->assertSee('CHK-90407 selected for zero-accrual review')
            ->assertSee('Zero-accrual handoff stays cautious')
            ->assertSee('Zero-accrual handoff stays evidence-first')
            ->assertSee('Receipt, amount, and zero-point outcome should stay visible in the workspace before any rule-gap discussion moves forward.')
            ->assertSee('Zero-point outcomes still need rule and receipt parity verification before any adjustment path is safe.')
            ->assertSee('Remaining backend gap')
            ->assertSee('Receipt reads, zero-accrual rule traces, and adjustment handlers should stay preview-only until fiscal-search parity is verified.');
    }

    public function test_checks_points_page_supports_selected_branch_receipt_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/checks-points?receipt=chk-90388');

        $response
            ->assertOk()
            ->assertSee('Back to receipt catalog')
            ->assertSee('/admin/checks-points')
            ->assertSee('Reviewing: CHK-90388')
            ->assertSee('Find receipt')
            ->assertSee('Blocked until branch-aware receipt lookup is backed by Galaxy foundation shop filters and transaction reads.')
            ->assertSee('Review accrual gaps')
            ->assertSee('Blocked until branch-aware accrual review is backed by Galaxy foundation transaction and rule data.')
            ->assertSee('Selected receipt preview')
            ->assertSee('CHK-90388')
            ->assertSee('Shop context')
            ->assertSee('North Shop')
            ->assertSee('Receipt status signal')
            ->assertSee('Branch receipt is already visible for shop-aware ledger parity review.')
            ->assertSee('Receipt focus')
            ->assertSee('Start with local shop evidence before comparing this receipt against cross-branch behavior.')
            ->assertSee('Receipt handoff signal')
            ->assertSee('Carry branch receipt and shop context forward before any cross-branch troubleshooting expands.')
            ->assertSee('Receipt posture')
            ->assertSee('Branch receipt lookup should stay read-only until Galaxy foundation shop filters and transaction history are verified against the old flow.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep shop, amount, and points visible together before comparing this branch receipt against other locations.')
            ->assertSee('Accrual posture')
            ->assertSee('North Shop accrual receipts should stay branch-aware, because cross-shop troubleshooting must preserve local receipt context.')
            ->assertSee('Backend gap')
            ->assertSee('Branch-aware receipt reads, shop-filter parity, and adjustment handlers should stay preview-only until cross-branch ledger parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep branch receipts in table-first review mode, because operators need the shop, amount, and points visible together before cross-shop comparisons begin.')
            ->assertSee('Troubleshooting guidance')
            ->assertSee('Treat this receipt as read-only review until Galaxy foundation transaction history and shop-aware filters exist.')
            ->assertSee('CHK-90388 selected for branch receipt review')
            ->assertSee('Branch-specific handoff stays receipt-first')
            ->assertSee('Branch receipt handoff keeps local evidence visible')
            ->assertSee('Shop, amount, and points should stay visible in the workspace before any cross-branch troubleshooting discussion begins.')
            ->assertSee('Branch receipt lookup should stay read-only until Galaxy foundation shop filters and transaction history are verified against the old flow.')
            ->assertSee('Positive branch accrual outcomes still need live transaction-domain parity before any adjustment path is safe.')
            ->assertSee('Remaining backend gap')
            ->assertSee('Branch-aware receipt reads, shop-filter parity, and adjustment handlers should stay preview-only until cross-branch ledger parity is verified.');
    }

    public function test_checks_points_page_supports_selected_positive_accrual_receipt_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/checks-points?receipt=chk-90421');

        $response
            ->assertOk()
            ->assertSee('Back to receipt catalog')
            ->assertSee('/admin/checks-points')
            ->assertSee('Reviewing: CHK-90421')
            ->assertSee('Find receipt')
            ->assertSee('Blocked until receipt lookup is backed by Galaxy foundation transaction reads and fiscal-search parity checks.')
            ->assertSee('Review accrual gaps')
            ->assertSee('Blocked until accrual-gap review is backed by Galaxy foundation transaction and rule data.')
            ->assertSee('Selected receipt preview')
            ->assertSee('CHK-90421')
            ->assertSee('Card')
            ->assertSee('GX-100001')
            ->assertSee('Shop context')
            ->assertSee('Central Shop')
            ->assertSee('Receipt status signal')
            ->assertSee('Positive accrual receipt is already visible for live ledger parity review.')
            ->assertSee('Receipt focus')
            ->assertSee('Start with amount-to-points parity before discussing any later correction path.')
            ->assertSee('Receipt handoff signal')
            ->assertSee('Carry receipt, card, and amount context forward before any later correction discussion begins.')
            ->assertSee('Receipt posture')
            ->assertSee('Fiscal receipt review should remain read-only until Galaxy foundation transaction history is verified against the legacy ledger.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep amount, points, and timestamp visible together before comparing this receipt against any later correction narrative.')
            ->assertSee('Accrual posture')
            ->assertSee('Positive accrual receipts should stay parity-first, because receipt math must match the old Galaxy ledger before any correction flow appears.')
            ->assertSee('Backend gap')
            ->assertSee('Receipt reads, transaction tables, and adjustment handlers should stay preview-only until accrual parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep this receipt in table-first review mode, because operators usually compare amount, points, and timestamp together before opening deeper investigation.')
            ->assertSee('Troubleshooting guidance')
            ->assertSee('Treat this receipt as read-only review until Galaxy foundation transaction history and adjustment flows exist.')
            ->assertSee('CHK-90421 selected for receipt review')
            ->assertSee('Receipt-first handoff stays visible')
            ->assertSee('Positive-accrual handoff stays evidence-first')
            ->assertSee('Amount, points, and timestamp should stay visible in the workspace before any future export or correction discussion begins.')
            ->assertSee('Positive point outcomes still need live transaction-domain parity before any adjustment path is safe.')
            ->assertSee('Remaining backend gap')
            ->assertSee('Receipt reads, transaction tables, and adjustment handlers should stay preview-only until accrual parity is verified.');
    }

    public function test_checks_points_page_ignores_unknown_selected_receipt_and_falls_back_to_catalog(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/checks-points?receipt=unknown-receipt');

        $response
            ->assertOk()
            ->assertSee('Review chk-90421 receipt')
            ->assertDontSee('Back to receipt catalog')
            ->assertDontSee('Selected receipt preview');
    }

    public function test_checks_points_page_accepts_case_insensitive_selected_receipt_query(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/checks-points?receipt=CHK-90407');

        $response
            ->assertOk()
            ->assertSee('Back to receipt catalog')
            ->assertSee('Reviewing: CHK-90407')
            ->assertSee('Selected receipt preview');
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
            ->assertSee('Open Galaxy reporting catalog')
            ->assertSee('Review export presets')
            ->assertSee('Blocked until preset handling is backed by Galaxy foundation reporting flow validation.')
            ->assertSee('Export source snapshot')
            ->assertSee('Blocked until the first live Galaxy foundation report source exists for export parity review.')
            ->assertSee('Planned reports')
            ->assertSee('Export formats')
            ->assertSee('Preset periods')
            ->assertSee('Galaxy reporting workspace for analytics, histories, and export-oriented admin review.')
            ->assertSee('Reporting operations are still preview-only')
            ->assertSee('Catalog actions, summary metrics, and export cues are outlining the Galaxy reporting workspace, but no Laravel reporting pipeline is wired yet.')
            ->assertSee('Migration readiness checklist')
            ->assertSee('Preview report catalog actions and preset metrics are defined')
            ->assertSee('Real report sources, presets, and exports still need PHP-backed Laravel wiring')
            ->assertSee('Implementation dependencies')
            ->assertSee('Report catalog is still config-backed with no reporting domain service yet')
            ->assertSee('Preset handling, query sources, and export pipeline are still pending')
            ->assertSee('First Galaxy foundation wiring step')
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

    public function test_reports_page_replaces_preview_catalog_with_model_backed_reporting_sources(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-reports',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Reporting Tier',
            'slug' => 'reporting-tier-reports',
            'points_rate' => 1.00,
            'is_active' => true,
        ]);

        $cardHolder = CardHolder::create([
            'full_name' => 'Mariam Sargsyan',
            'phone' => '+37499111223',
            'email' => 'mariam.reports@example.com',
            'status' => 'active',
            'shop_id' => $shop->id,
        ]);

        Card::create([
            'number' => '990011223344',
            'status' => 'active',
            'card_holder_id' => $cardHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
            'issued_at' => now(),
        ]);

        Role::create([
            'name' => 'Reporting Lead',
            'slug' => 'reporting-lead-reports',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports');

        $response
            ->assertOk()
            ->assertSee('Live Galaxy sources')
            ->assertSee('Tracked Galaxy branches')
            ->assertSee('Tracked Galaxy card shells')
            ->assertSee('Tracked Galaxy holders')
            ->assertSee('Tracked Galaxy access shells')
            ->assertSee('1')
            ->assertSee('Galaxy branch card-shell coverage')
            ->assertSee('/admin/reports?source=cards-by-shop')
            ->assertSee('1 Galaxy branches')
            ->assertSee('Galaxy holder status overview')
            ->assertSee('/admin/reports?source=cardholder-status')
            ->assertSee('1 Galaxy holders')
            ->assertSee('Galaxy access coverage')
            ->assertSee('/admin/reports?source=role-access')
            ->assertSee('1 Galaxy access shells')
            ->assertSee('Review Galaxy branch card-shell coverage reporting source')
            ->assertSee('Open Galaxy reporting catalog')
            ->assertSee('Review export presets')
            ->assertSee('Blocked until preset handling is verified against multiple live Galaxy foundation reporting sources.')
            ->assertSee('Export source snapshot')
            ->assertSee('Blocked until multi-source export snapshots are verified against legacy file delivery and grouped totals.')
            ->assertSee('Reporting workspace is now partially Galaxy foundation-backed')
            ->assertSee('Catalog metrics and report entry rows now reflect live Galaxy source counts from Galaxy foundation models, while presets and exports remain preview-only.')
            ->assertSee('Live reporting sources reflected from Galaxy foundation models')
            ->assertSee('The reporting workspace now sees 1 Galaxy branches, 1 Galaxy card shells, 1 Galaxy holders, and 1 Galaxy access shells through the current Galaxy foundation layer.')
            ->assertSee('Export catalog remains parity-first')
            ->assertSee('Metrics and entry rows are live-backed now, but preset handling and export generation still stay blocked until the reporting pipeline is verified.')
            ->assertSee('Report catalog is still lightweight, but source counts now come from live Galaxy foundation models')
            ->assertSee('Reporting posture')
            ->assertSee('This workspace is now live-backed for read-only source review, but preset and export flows should stay parity-first until the reporting pipeline is verified.')
            ->assertSee('Readiness signal')
            ->assertSee('Partially ready: live source review works now, while preset handling and exports stay blocked behind later reporting-pipeline verification.')
            ->assertSee('Preset posture')
            ->assertSee('Preset periods are still preview-only, so operators should treat the live source layer as reviewable while preset-driven report flows remain gated.')
            ->assertSee('Export posture')
            ->assertSee('Export generation is still blocked, so the live reporting layer should stay review-only until file delivery and parity checks are verified.')
            ->assertSee('Source coverage')
            ->assertSee('Galaxy foundation reporting inputs currently cover 1 shops, 1 cards, 1 cardholders, and 1 roles for read-only review.')
            ->assertSee('Preset handling, query shaping, and export pipeline are still pending');
    }

    public function test_reports_page_supports_selected_live_source_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-reports-selected',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Reporting Tier Selected',
            'slug' => 'reporting-tier-selected',
            'points_rate' => 1.00,
            'is_active' => true,
        ]);

        $cardHolder = CardHolder::create([
            'full_name' => 'Mariam Reporting Selected',
            'phone' => '+37499111224',
            'email' => 'mariam.reports.selected@example.com',
            'status' => 'active',
            'shop_id' => $shop->id,
        ]);

        Card::create([
            'number' => '990011223345',
            'status' => 'active',
            'card_holder_id' => $cardHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
            'activated_at' => now(),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=cards-by-shop');

        $response
            ->assertOk()
            ->assertSee('Back to report catalog')
            ->assertSee('/admin/reports')
            ->assertSee('Reviewing: Galaxy branch card-shell coverage')
            ->assertSee('Review export presets')
            ->assertSee('Blocked until branch-total preset periods are verified against live shop grouping and legacy reporting habits.')
            ->assertSee('Export source snapshot')
            ->assertSee('Blocked until branch-total export snapshots are verified against legacy grouped totals and file delivery.')
            ->assertSee('Selected report source')
            ->assertSee('Galaxy branch card-shell coverage')
            ->assertSee('Review mode')
            ->assertSee('Live-source review, card inventory already exists in the Galaxy foundation layer for shop-level reporting checks.')
            ->assertSee('Source coverage')
            ->assertSee('1 Galaxy card shells across 1 tracked Galaxy branches are currently available for read-only Galaxy branch card-shell reporting review.')
            ->assertSee('Source status signal')
            ->assertSee('Galaxy branch card-shell source is already visible with live branch inventory for parity review.')
            ->assertSee('Source focus')
            ->assertSee('Start with branch totals and assignment mix before discussing any later export snapshot.')
            ->assertSee('Source posture')
            ->assertSee('Keep branch inventory review on-screen first, then leave grouped export expectations in preview mode until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep branch totals, paused-shop counts, and assigned-versus-unassigned inventory visible together before trusting any export view.')
            ->assertSee('Source signal')
            ->assertSee('live cards and branch coverage visible')
            ->assertSee('Galaxy input signal')
            ->assertSee('Galaxy card-shell and branch inputs are ready for on-screen review')
            ->assertSee('Comparison signal')
            ->assertSee('full branch, inventory, and assignment comparison coverage is still pending')
            ->assertSee('Branch review readiness')
            ->assertSee('ready for branch-total review across 1 live Galaxy branches')
            ->assertSee('Branch activity signal')
            ->assertSee('paused Galaxy branch coverage is still pending for comparison review')
            ->assertSee('Inventory state signal')
            ->assertSee('blocked inventory coverage is still pending for parity review')
            ->assertSee('Assignment linkage signal')
            ->assertSee('unassigned inventory coverage is still pending for parity review')
            ->assertSee('Activated assignment signal')
            ->assertSee('1 activated holder-linked Galaxy card shells are already visible for live Galaxy customer inventory review')
            ->assertSee('Blocked assignment signal')
            ->assertSee('blocked holder-linked inventory is still pending for parity review')
            ->assertSee('Draft assignment signal')
            ->assertSee('draft holder-linked inventory is still pending for parity review')
            ->assertSee('Active branch assignment signal')
            ->assertSee('1 holder-linked Galaxy card shells are already visible in active Galaxy branches for live branch review')
            ->assertSee('Paused branch assignment signal')
            ->assertSee('paused-branch holder-linked inventory is still pending for parity review')
            ->assertSee('Unassigned branch activity signal')
            ->assertSee('mixed unassigned branch coverage is still pending for parity review')
            ->assertSee('Activated unassigned signal')
            ->assertSee('activated unassigned inventory is still pending for parity review')
            ->assertSee('Blocked unassigned signal')
            ->assertSee('blocked unassigned inventory is still pending for parity review')
            ->assertSee('Draft unassigned signal')
            ->assertSee('draft unassigned inventory is still pending for parity review')
            ->assertSee('Draft inventory signal')
            ->assertSee('draft inventory coverage is still pending for parity review')
            ->assertSee('Activation signal')
            ->assertSee('activation coverage is still pending for parity review')
            ->assertSee('Scope guidance')
            ->assertSee('Keep this source centered on branch-by-branch totals, because old Galaxy operators usually compared card inventory by shop before opening broader exports.')
            ->assertSee('Default period posture')
            ->assertSee('Use current snapshot review first, then keep preset periods staged until branch-total parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Prefer table-first review here, because branch inventory checks should stay visible on screen before anyone expects export files.')
            ->assertSee('Handoff signal')
            ->assertSee('Keep branch-total and linked-holder inventory findings in the live workspace before asking for export-driven handoff.')
            ->assertSee('Backend gap')
            ->assertSee('Preset handling, unassigned-inventory shaping, and export generation should stay preview-only until branch-total assignment parity is verified.')
            ->assertSee('Galaxy branch card-shell coverage selected for Galaxy review')
            ->assertSee('This reporting view now reflects 1 tracked Galaxy card shells across 1 Galaxy branches from the current Galaxy foundation layer.')
            ->assertSee('Branch inventory handoff stays on-screen first')
            ->assertSee('Operators should hand off branch-total and linked-holder inventory findings in the live workspace before relying on exported files for this source.')
            ->assertSee('Source status signal:')
            ->assertSee('Galaxy branch card-shell source is already visible with live branch inventory for parity review.')
            ->assertSee('Source signal')
            ->assertSee('live cards and branch coverage visible')
            ->assertSee('Galaxy input signal')
            ->assertSee('Galaxy card-shell and branch inputs are ready for on-screen review')
            ->assertSee('Comparison signal:')
            ->assertSee('full branch, inventory, and assignment comparison coverage is still pending')
            ->assertSee('Branch review readiness:')
            ->assertSee('ready for branch-total review across 1 live Galaxy branches')
            ->assertSee('Branch activity signal:')
            ->assertSee('paused Galaxy branch coverage is still pending for comparison review')
            ->assertSee('Inventory state signal:')
            ->assertSee('blocked inventory coverage is still pending for parity review')
            ->assertSee('Activated assignment signal:')
            ->assertSee('1 activated holder-linked Galaxy card shells are already visible for live Galaxy customer inventory review')
            ->assertSee('Blocked assignment signal:')
            ->assertSee('blocked holder-linked inventory is still pending for parity review')
            ->assertSee('Draft assignment signal:')
            ->assertSee('draft holder-linked inventory is still pending for parity review')
            ->assertSee('Active branch assignment signal:')
            ->assertSee('1 holder-linked Galaxy card shells are already visible in active Galaxy branches for live branch review')
            ->assertSee('Paused branch assignment signal:')
            ->assertSee('paused-branch holder-linked inventory is still pending for parity review')
            ->assertSee('Unassigned branch activity signal:')
            ->assertSee('mixed unassigned branch coverage is still pending for parity review')
            ->assertSee('Activated unassigned signal:')
            ->assertSee('activated unassigned inventory is still pending for parity review')
            ->assertSee('Blocked unassigned signal:')
            ->assertSee('blocked unassigned inventory is still pending for parity review')
            ->assertSee('Draft unassigned signal:')
            ->assertSee('draft unassigned inventory is still pending for parity review')
            ->assertSee('Scope posture')
            ->assertSee('Branch-level comparison is the first parity target, so cross-shop shaping should stay conservative until legacy report totals are matched.')
            ->assertSee('Grouping posture')
            ->assertSee('Shop grouping should stay read-only until query shaping is verified against legacy report totals.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Preset handling, unassigned-inventory shaping, and export generation should stay preview-only until branch-total assignment parity is verified.')
            ->assertSee('Handoff signal:')
            ->assertSee('Keep branch-total and linked-holder inventory findings in the live workspace before asking for export-driven handoff.');
    }

    public function test_reports_page_supports_selected_mixed_branch_activity_review_context(): void
    {
        $liveShop = Shop::create([
            'name' => 'Galaxy Mixed Live Branch',
            'code' => 'galaxy-mixed-live-branch',
            'is_active' => true,
        ]);

        $pausedShop = Shop::create([
            'name' => 'Galaxy Mixed Paused Branch',
            'code' => 'galaxy-mixed-paused-branch',
            'is_active' => false,
        ]);

        $cardType = CardType::create([
            'name' => 'Reporting Mixed Branch Tier',
            'slug' => 'reporting-mixed-branch-tier',
            'points_rate' => 1.00,
            'is_active' => true,
        ]);

        $cardHolder = CardHolder::create([
            'full_name' => 'Mariam Mixed Branch Review',
            'phone' => '+37499111229',
            'email' => 'mariam.reports.mixed.branch@example.com',
            'is_active' => true,
            'shop_id' => $liveShop->id,
        ]);

        Card::create([
            'number' => '990011223349',
            'status' => 'active',
            'card_holder_id' => $cardHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $liveShop->id,
            'activated_at' => now(),
        ]);

        Card::create([
            'number' => '990011223350',
            'status' => 'blocked',
            'card_holder_id' => $cardHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $pausedShop->id,
            'issued_at' => now(),
        ]);

        Card::create([
            'number' => '990011223351',
            'status' => 'draft',
            'card_holder_id' => $cardHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $pausedShop->id,
        ]);

        Card::create([
            'number' => '990011223352',
            'status' => 'blocked',
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'shop_id' => $pausedShop->id,
        ]);

        Card::create([
            'number' => '990011223353',
            'status' => 'active',
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'shop_id' => $liveShop->id,
            'activated_at' => now(),
        ]);

        Card::create([
            'number' => '990011223354',
            'status' => 'draft',
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'shop_id' => $pausedShop->id,
        ]);

        $branchActivitySignal = '1 live Galaxy branches are already visible beside 1 paused branches for comparison review';

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=cards-by-shop');

        $response
            ->assertOk()
            ->assertSee('Reviewing: Galaxy branch card-shell coverage')
            ->assertSee('Source coverage')
            ->assertSee('6 Galaxy card shells across 2 tracked Galaxy branches are currently available for read-only Galaxy branch card-shell reporting review.')
            ->assertSee('Comparison signal')
            ->assertSee('branch, inventory, and assignment comparison cues are all visible for parity walkthrough')
            ->assertSee('Branch activity signal')
            ->assertSee($branchActivitySignal)
            ->assertSee('Inventory state signal')
            ->assertSee('2 active cards are already visible beside 2 blocked inventory records for parity review')
            ->assertSee('Assignment linkage signal')
            ->assertSee('3 holder-linked cards are already visible beside 3 unassigned inventory records for parity review')
            ->assertSee('Activated assignment signal')
            ->assertSee('1 activated holder-linked Galaxy card shells are already visible for live Galaxy customer inventory review')
            ->assertSee('Blocked assignment signal')
            ->assertSee('1 blocked holder-linked cards are already visible for dispute and replacement review')
            ->assertSee('Draft assignment signal')
            ->assertSee('1 draft holder-linked cards are already visible for pre-issuance customer review')
            ->assertSee('Active branch assignment signal')
            ->assertSee('1 holder-linked Galaxy card shells are already visible in active Galaxy branches for live branch review')
            ->assertSee('Paused branch assignment signal')
            ->assertSee('2 holder-linked Galaxy card shells are already visible in paused Galaxy branches for branch-recovery review')
            ->assertSee('Unassigned branch activity signal')
            ->assertSee('1 unassigned Galaxy card shells are already visible in active Galaxy branches beside 2 unassigned Galaxy card shells in paused Galaxy branches for parity review')
            ->assertSee('Activated unassigned signal')
            ->assertSee('1 activated unassigned cards are already visible for inventory recovery review')
            ->assertSee('Blocked unassigned signal')
            ->assertSee('1 blocked unassigned cards are already visible for replacement inventory review')
            ->assertSee('Draft unassigned signal')
            ->assertSee('1 draft unassigned cards are already visible for pre-issuance inventory review')
            ->assertSee('Draft inventory signal')
            ->assertSee('2 draft cards are already visible beside 4 issued inventory records for parity review')
            ->assertSee('Activation signal')
            ->assertSee('2 activated cards are already visible beside 4 not-yet-activated inventory records for parity review')
            ->assertSee('Handoff signal')
            ->assertSee('Keep branch-total and assignment-split findings in the live workspace before asking for export-driven handoff.')
            ->assertSee('Branch inventory handoff stays on-screen first')
            ->assertSee('Operators should hand off branch-total and assignment-split findings in the live workspace before relying on exported files for this source.')
            ->assertSee('Implementation dependencies')
            ->assertSee('Comparison signal:')
            ->assertSee('branch, inventory, and assignment comparison cues are all visible for parity walkthrough')
            ->assertSee('Branch activity signal:')
            ->assertSee($branchActivitySignal)
            ->assertSee('Inventory state signal:')
            ->assertSee('2 active cards are already visible beside 2 blocked inventory records for parity review')
            ->assertSee('Assignment linkage signal:')
            ->assertSee('3 holder-linked cards are already visible beside 3 unassigned inventory records for parity review')
            ->assertSee('Activated assignment signal:')
            ->assertSee('1 activated holder-linked Galaxy card shells are already visible for live Galaxy customer inventory review')
            ->assertSee('Blocked assignment signal:')
            ->assertSee('1 blocked holder-linked cards are already visible for dispute and replacement review')
            ->assertSee('Draft assignment signal:')
            ->assertSee('1 draft holder-linked cards are already visible for pre-issuance customer review')
            ->assertSee('Active branch assignment signal:')
            ->assertSee('1 holder-linked Galaxy card shells are already visible in active Galaxy branches for live branch review')
            ->assertSee('Paused branch assignment signal:')
            ->assertSee('2 holder-linked Galaxy card shells are already visible in paused Galaxy branches for branch-recovery review')
            ->assertSee('Unassigned branch activity signal:')
            ->assertSee('1 unassigned Galaxy card shells are already visible in active Galaxy branches beside 2 unassigned Galaxy card shells in paused Galaxy branches for parity review')
            ->assertSee('Activated unassigned signal:')
            ->assertSee('1 activated unassigned cards are already visible for inventory recovery review')
            ->assertSee('Blocked unassigned signal:')
            ->assertSee('1 blocked unassigned cards are already visible for replacement inventory review')
            ->assertSee('Draft unassigned signal:')
            ->assertSee('1 draft unassigned cards are already visible for pre-issuance inventory review')
            ->assertSee('Draft inventory signal:')
            ->assertSee('2 draft cards are already visible beside 4 issued inventory records for parity review')
            ->assertSee('Activation signal:')
            ->assertSee('2 activated cards are already visible beside 4 not-yet-activated inventory records for parity review')
            ->assertSee('Handoff signal:')
            ->assertSee('Keep branch-total and assignment-split findings in the live workspace before asking for export-driven handoff.');
    }

    public function test_reports_page_supports_selected_role_access_pending_readiness_context(): void
    {
        $role = Role::create([
            'name' => 'Draft Access Observer',
            'slug' => 'draft-access-observer',
            'is_active' => false,
        ]);

        $assignedUser = User::factory()->create([
            'name' => 'Ani Access Pending',
        ]);

        $permission = Permission::create([
            'name' => 'Review pending access parity',
            'slug' => 'review-pending-access-parity',
        ]);

        $role->permissions()->attach($permission);

        $assignedUser->roles()->attach($role->id);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=role-access');

        $response
            ->assertOk()
            ->assertSee('Back to report catalog')
            ->assertSee('Reviewing: Galaxy access coverage')
            ->assertSee('Review export presets')
            ->assertSee('Blocked until role-access preset periods are verified against scope and assignment reporting parity.')
            ->assertSee('Export source snapshot')
            ->assertSee('Blocked until role-access export snapshots are verified against scope summaries and file delivery.')
            ->assertSee('Selected report source')
            ->assertSee('Galaxy access coverage')
            ->assertSee('Source coverage')
            ->assertSee('1 Galaxy access shells are currently available for read-only Galaxy access reporting review.')
            ->assertSee('Source signal')
            ->assertSee('live role coverage visible')
            ->assertSee('Access mix signal')
            ->assertSee('combined role, bundle, and staffing coverage is still pending')
            ->assertSee('Access readiness')
            ->assertSee('permission-linked active access posture is still pending')
            ->assertSee('Assignment signal')
            ->assertSee('1 staff assignments are already visible for access review')
            ->assertSee('Draft staffing signal')
            ->assertSee('1 draft Galaxy access roles already carry visible staff assignments that still need activation review')
            ->assertSee('Draft bundle signal')
            ->assertSee('1 draft Galaxy access roles already carry visible permission bundles that still need activation review')
            ->assertSee('Assigned bundle signal')
            ->assertSee('assigned permission-bundle coverage is still pending')
            ->assertSee('Scoped bundle signal')
            ->assertSee('shop-linked permission-bundle coverage is still pending')
            ->assertSee('Bundle branch activity signal')
            ->assertSee('paused-branch permission-bundle coverage is still pending')
            ->assertSee('Role state signal')
            ->assertSee('draft access-role coverage is still pending')
            ->assertSee('Permission bundle signal')
            ->assertSee('unbundled active-role coverage is still pending')
            ->assertSee('Handoff signal')
            ->assertSee('Keep role-coverage and staff-assignment findings in the live workspace before asking for export-driven handoff.')
            ->assertSee('Default period posture')
            ->assertSee('Use current access coverage review first, then stage preset periods only after scope and assignment parity are verified.')
            ->assertSee('Access reporting parity stays review-only')
            ->assertSee('Operators should hand off role-coverage and staff-assignment findings in the live review context before trusting export files for access decisions.')
            ->assertSee('Access mix signal:')
            ->assertSee('combined role, bundle, and staffing coverage is still pending')
            ->assertSee('Access readiness:')
            ->assertSee('permission-linked active access posture is still pending')
            ->assertSee('Draft staffing signal:')
            ->assertSee('1 draft Galaxy access roles already carry visible staff assignments that still need activation review')
            ->assertSee('Draft bundle signal:')
            ->assertSee('1 draft Galaxy access roles already carry visible permission bundles that still need activation review')
            ->assertSee('Assigned bundle signal:')
            ->assertSee('assigned permission-bundle coverage is still pending')
            ->assertSee('Scoped bundle signal:')
            ->assertSee('shop-linked permission-bundle coverage is still pending')
            ->assertSee('Bundle branch activity signal:')
            ->assertSee('paused-branch permission-bundle coverage is still pending')
            ->assertSee('Handoff signal:')
            ->assertSee('Keep role-coverage and staff-assignment findings in the live workspace before asking for export-driven handoff.');
    }

    public function test_reports_page_supports_selected_mixed_role_state_review_context(): void
    {
        $activeRole = Role::create([
            'name' => 'Reporting Active Access Lead',
            'slug' => 'reporting-active-access-lead',
            'is_active' => true,
        ]);

        Role::create([
            'name' => 'Reporting Draft Access Observer',
            'slug' => 'reporting-draft-access-observer',
            'is_active' => false,
        ]);

        $permission = Permission::create([
            'name' => 'Review access audits',
            'slug' => 'review-access-audits',
        ]);

        $activeRole->permissions()->attach($permission);

        $assignedUser = User::factory()->create([
            'name' => 'Mariam Mixed Access Review',
        ]);

        $assignedUser->roles()->attach($activeRole->id);

        $roleStateSignal = '1 active Galaxy access roles are already visible beside 1 draft Galaxy access roles for parity review';

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=role-access');

        $response
            ->assertOk()
            ->assertSee('Reviewing: Galaxy access coverage')
            ->assertSee('Source coverage')
            ->assertSee('2 Galaxy access shells are currently available for read-only Galaxy access reporting review.')
            ->assertSee('Role state signal')
            ->assertSee($roleStateSignal)
            ->assertSee('Staff coverage signal')
            ->assertSee('unassigned active-role staff coverage is still pending')
            ->assertSee('Permission bundle signal')
            ->assertSee('unbundled active-role coverage is still pending')
            ->assertSee('Handoff signal')
            ->assertSee('Keep role-coverage and staffing findings in the live workspace before asking for export-driven handoff.')
            ->assertSee('Access-review handoff should stay visible in the workspace')
            ->assertSee('Operators should hand off role-coverage and staffing findings in the live review context before trusting export files for access decisions.')
            ->assertSee('Implementation dependencies')
            ->assertSee('Role state signal:')
            ->assertSee($roleStateSignal)
            ->assertSee('Staff coverage signal:')
            ->assertSee('unassigned active-role staff coverage is still pending')
            ->assertSee('Permission bundle signal:')
            ->assertSee('unbundled active-role coverage is still pending')
            ->assertSee('Handoff signal:')
            ->assertSee('Keep role-coverage and staffing findings in the live workspace before asking for export-driven handoff.');
    }

    public function test_reports_page_supports_selected_mixed_permission_bundle_review_context(): void
    {
        $bundledRole = Role::create([
            'name' => 'Reporting Bundled Access Lead',
            'slug' => 'reporting-bundled-access-lead',
            'is_active' => true,
        ]);

        Role::create([
            'name' => 'Reporting Unbundled Access Lead',
            'slug' => 'reporting-unbundled-access-lead',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'Review branch access audits',
            'slug' => 'review-branch-access-audits',
        ]);

        $bundledRole->permissions()->attach($permission);

        $assignedUser = User::factory()->create([
            'name' => 'Levon Mixed Bundle Review',
        ]);

        $assignedUser->roles()->attach($bundledRole->id);

        $permissionBundleSignal = '1 permission-linked Galaxy access roles are already visible beside 1 unbundled active Galaxy access roles for parity review';

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=role-access');

        $response
            ->assertOk()
            ->assertSee('Reviewing: Galaxy access coverage')
            ->assertSee('Source coverage')
            ->assertSee('2 Galaxy access shells are currently available for read-only Galaxy access reporting review.')
            ->assertSee('Permission bundle signal')
            ->assertSee($permissionBundleSignal)
            ->assertSee('Assigned bundle signal')
            ->assertSee('1 permission-linked roles already carry visible staff assignments for parity review')
            ->assertSee('Handoff signal')
            ->assertSee('Keep role-coverage and staffing findings in the live workspace before asking for export-driven handoff.')
            ->assertSee('Access-review handoff should stay visible in the workspace')
            ->assertSee('Operators should hand off role-coverage and staffing findings in the live review context before trusting export files for access decisions.')
            ->assertSee('Implementation dependencies')
            ->assertSee('Permission bundle signal:')
            ->assertSee($permissionBundleSignal)
            ->assertSee('Assigned bundle signal:')
            ->assertSee('1 permission-linked roles already carry visible staff assignments for parity review')
            ->assertSee('Handoff signal:')
            ->assertSee('Keep role-coverage and staffing findings in the live workspace before asking for export-driven handoff.');
    }

    public function test_reports_page_ignores_unknown_selected_source_and_falls_back_to_catalog(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-reports-fallback',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Reporting Tier Fallback',
            'slug' => 'reporting-tier-fallback',
            'points_rate' => 1.00,
            'is_active' => true,
        ]);

        $cardHolder = CardHolder::create([
            'full_name' => 'Mariam Reporting Fallback',
            'phone' => '+37499111225',
            'email' => 'mariam.reports.fallback@example.com',
            'status' => 'active',
            'shop_id' => $shop->id,
        ]);

        Card::create([
            'number' => '990011223346',
            'status' => 'active',
            'card_holder_id' => $cardHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
            'issued_at' => now(),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=unknown-source');

        $response
            ->assertOk()
            ->assertSee('Review Galaxy branch card-shell coverage reporting source')
            ->assertDontSee('Back to report catalog')
            ->assertDontSee('Selected report source');
    }

    public function test_reports_page_supports_selected_cardholder_status_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Holder Reporting',
            'code' => 'galaxy-holder-reporting',
            'is_active' => true,
        ]);

        CardHolder::create([
            'full_name' => 'Mariam Holder Review',
            'phone' => '+37499111227',
            'email' => 'mariam.holder.review@example.com',
            'status' => 'active',
            'shop_id' => $shop->id,
        ]);

        $cardType = CardType::create([
            'name' => 'Reporting Holder Tier',
            'slug' => 'reporting-holder-tier',
            'points_rate' => 1.00,
            'is_active' => true,
        ]);

        Card::create([
            'number' => '990011223359',
            'status' => 'draft',
            'card_holder_id' => CardHolder::query()->first()->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
        ]);

        Card::create([
            'number' => '990011223358',
            'status' => 'active',
            'card_holder_id' => CardHolder::query()->first()->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
            'activated_at' => now(),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=cardholder-status');

        $response
            ->assertOk()
            ->assertSee('Back to report catalog')
            ->assertSee('Reviewing: Galaxy holder status overview')
            ->assertSee('Review export presets')
            ->assertSee('Blocked until holder-status preset periods are verified against lifecycle and recency reporting parity.')
            ->assertSee('Export source snapshot')
            ->assertSee('Blocked until holder-status export snapshots are verified against lifecycle summaries and file delivery.')
            ->assertSee('Selected report source')
            ->assertSee('Galaxy holder status overview')
            ->assertSee('Source coverage')
            ->assertSee('1 Galaxy holders are currently available for read-only Galaxy holder status reporting review.')
            ->assertSee('Source status signal')
            ->assertSee('Galaxy holder-status source is already visible with live lifecycle coverage for parity review.')
            ->assertSee('Source focus')
            ->assertSee('Start with active-versus-inactive holder posture before expanding into deeper linkage comparisons.')
            ->assertSee('Source posture')
            ->assertSee('Keep support-style status triage visible first, then leave export-style lifecycle summaries in preview mode until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep holder status counts, linked-versus-unlinked profiles, and blocked-card posture visible together before trusting any export view.')
            ->assertSee('Source signal')
            ->assertSee('live holder status coverage visible')
            ->assertSee('Galaxy input signal')
            ->assertSee('holder status inputs are ready for on-screen review')
            ->assertSee('Comparison signal')
            ->assertSee('full lifecycle, linkage, and linked-card comparison coverage is still pending')
            ->assertSee('Review readiness')
            ->assertSee('ready for holder-status triage review')
            ->assertSee('Lifecycle signal')
            ->assertSee('inactive holder coverage is still pending for lifecycle review')
            ->assertSee('Card linkage signal')
            ->assertSee('unlinked holder coverage is still pending for parity review')
            ->assertSee('Linked card state signal')
            ->assertSee('blocked linked-card coverage is still pending for parity review')
            ->assertSee('Linked card draft signal')
            ->assertSee('1 draft holder-linked Galaxy card shells are already visible for pre-issuance parity review')
            ->assertSee('Linked card activation signal')
            ->assertSee('1 activated holder-linked Galaxy card shells are already visible for holder-lifecycle parity review')
            ->assertSee('Blocked holder signal')
            ->assertSee('blocked-holder coverage is still pending for support review')
            ->assertSee('Draft holder signal')
            ->assertSee('1 holder profiles already carry draft linked-card posture for pre-issuance review')
            ->assertSee('Active holder signal')
            ->assertSee('1 holder profiles already carry active linked-card posture for lifecycle review')
            ->assertSee('Activated holder signal')
            ->assertSee('1 holder profiles already carry activated linked-card posture for lifecycle review')
            ->assertSee('Handoff signal')
            ->assertSee('Keep holder lifecycle and linked-profile findings in the live workspace before asking for export-driven handoff.')
            ->assertSee('Backend gap')
            ->assertSee('Preset handling, inactive-holder shaping, and export generation should stay preview-only until linked-profile lifecycle parity is verified.')
            ->assertSee('Holder branch activity signal')
            ->assertSee('paused-branch holder coverage is still pending for parity review')
            ->assertSee('Galaxy holder status source selected for Galaxy review')
            ->assertSee('This reporting view now reflects 1 tracked Galaxy holders from the current Galaxy foundation layer.')
            ->assertSee('Support handoff should keep holder posture visible')
            ->assertSee('Operators should pass along holder lifecycle and linked-profile findings in the live review flow before expecting export-driven follow-up.')
            ->assertSee('Implementation dependencies')
            ->assertSee('Source status signal:')
            ->assertSee('Galaxy holder-status source is already visible with live lifecycle coverage for parity review.')
            ->assertSee('Review readiness:')
            ->assertSee('ready for holder-status triage review')
            ->assertSee('Lifecycle posture:')
            ->assertSee('Status aggregation should stay read-only until holder lifecycle and activity parity are verified.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Preset handling, inactive-holder shaping, and export generation should stay preview-only until linked-profile lifecycle parity is verified.')
            ->assertSee('Handoff signal:')
            ->assertSee('Keep holder lifecycle and linked-profile findings in the live workspace before asking for export-driven handoff.');
    }

    public function test_reports_page_supports_selected_mixed_cardholder_status_review_context(): void
    {
        $activeShop = Shop::create([
            'name' => 'Galaxy Mixed Holder Reporting',
            'code' => 'galaxy-mixed-holder-reporting',
            'is_active' => true,
        ]);

        $pausedShop = Shop::create([
            'name' => 'Galaxy Mixed Holder Paused Branch',
            'code' => 'galaxy-mixed-holder-paused-branch',
            'is_active' => false,
        ]);

        $activeHolder = CardHolder::create([
            'full_name' => 'Mariam Mixed Holder Active',
            'phone' => '+37499111230',
            'email' => 'mariam.holder.mixed.active@example.com',
            'is_active' => true,
            'shop_id' => $activeShop->id,
        ]);

        $inactiveHolder = CardHolder::create([
            'full_name' => 'Mariam Mixed Holder Inactive',
            'phone' => '+37499111231',
            'email' => 'mariam.holder.mixed.inactive@example.com',
            'is_active' => false,
            'shop_id' => $pausedShop->id,
        ]);

        $cardType = CardType::create([
            'name' => 'Reporting Mixed Holder Tier',
            'slug' => 'reporting-mixed-holder-tier',
            'points_rate' => 1.00,
            'is_active' => true,
        ]);

        Card::create([
            'number' => '990011223360',
            'status' => 'active',
            'card_holder_id' => $activeHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $activeShop->id,
            'activated_at' => now(),
        ]);

        Card::create([
            'number' => '990011223361',
            'status' => 'blocked',
            'card_holder_id' => $inactiveHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $pausedShop->id,
            'issued_at' => now(),
        ]);

        Card::create([
            'number' => '990011223362',
            'status' => 'draft',
            'card_holder_id' => $activeHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $activeShop->id,
        ]);

        CardHolder::create([
            'full_name' => 'Mariam Mixed Holder Unlinked',
            'phone' => '+37499111232',
            'email' => 'mariam.holder.mixed.unlinked@example.com',
            'is_active' => true,
            'shop_id' => $activeShop->id,
        ]);

        $inactiveHolderCount = CardHolder::query()->where('is_active', false)->count();
        $activeHolderCount = CardHolder::query()->where('is_active', true)->count();
        $lifecycleSignal = sprintf('%d inactive holders are already visible beside %d active profiles for lifecycle review', $inactiveHolderCount, $activeHolderCount);
        $holderBranchActivitySignal = '2 Galaxy holder profiles are already visible in active Galaxy branches beside 1 Galaxy holder profiles in paused Galaxy branches for parity review';

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=cardholder-status');

        $response
            ->assertOk()
            ->assertSee('Reviewing: Galaxy holder status overview')
            ->assertSee('Source coverage')
            ->assertSee('3 Galaxy holders are currently available for read-only Galaxy holder status reporting review.')
            ->assertSee('Comparison signal')
            ->assertSee('lifecycle, linkage, and linked-card comparison cues are all visible for parity walkthrough')
            ->assertSee('Lifecycle signal')
            ->assertSee($lifecycleSignal)
            ->assertSee('Card linkage signal')
            ->assertSee('2 linked holders are already visible beside 1 unlinked profiles for parity review')
            ->assertSee('Linked card state signal')
            ->assertSee('1 active holder-linked Galaxy card shells are already visible beside 1 blocked holder-linked Galaxy card shells for parity review')
            ->assertSee('Linked card draft signal')
            ->assertSee('1 draft holder-linked Galaxy card shells are already visible for pre-issuance parity review')
            ->assertSee('Linked card activation signal')
            ->assertSee('1 activated holder-linked Galaxy card shells are already visible for holder-lifecycle parity review')
            ->assertSee('Blocked holder signal')
            ->assertSee('1 holder profiles already carry blocked linked-card posture for support review')
            ->assertSee('Draft holder signal')
            ->assertSee('1 holder profiles already carry draft linked-card posture for pre-issuance review')
            ->assertSee('Active holder signal')
            ->assertSee('1 holder profiles already carry active linked-card posture for lifecycle review')
            ->assertSee('Activated holder signal')
            ->assertSee('1 holder profiles already carry activated linked-card posture for lifecycle review')
            ->assertSee('Holder branch activity signal')
            ->assertSee($holderBranchActivitySignal)
            ->assertSee('Handoff signal')
            ->assertSee('Keep holder lifecycle and linkage findings in the live workspace before asking for export-driven handoff.')
            ->assertSee('Support handoff should keep holder posture visible')
            ->assertSee('Operators should pass along holder lifecycle and linkage findings in the live review flow before expecting export-driven follow-up.')
            ->assertSee('Implementation dependencies')
            ->assertSee('Comparison signal:')
            ->assertSee('lifecycle, linkage, and linked-card comparison cues are all visible for parity walkthrough')
            ->assertSee('Lifecycle signal:')
            ->assertSee($lifecycleSignal)
            ->assertSee('Card linkage signal:')
            ->assertSee('2 linked holders are already visible beside 1 unlinked profiles for parity review')
            ->assertSee('Linked card state signal:')
            ->assertSee('1 active holder-linked Galaxy card shells are already visible beside 1 blocked holder-linked Galaxy card shells for parity review')
            ->assertSee('Linked card draft signal:')
            ->assertSee('1 draft holder-linked Galaxy card shells are already visible for pre-issuance parity review')
            ->assertSee('Linked card activation signal:')
            ->assertSee('1 activated holder-linked Galaxy card shells are already visible for holder-lifecycle parity review')
            ->assertSee('Blocked holder signal:')
            ->assertSee('1 holder profiles already carry blocked linked-card posture for support review')
            ->assertSee('Draft holder signal:')
            ->assertSee('1 holder profiles already carry draft linked-card posture for pre-issuance review')
            ->assertSee('Active holder signal:')
            ->assertSee('1 holder profiles already carry active linked-card posture for lifecycle review')
            ->assertSee('Activated holder signal:')
            ->assertSee('1 holder profiles already carry activated linked-card posture for lifecycle review')
            ->assertSee('Holder branch activity signal:')
            ->assertSee($holderBranchActivitySignal)
            ->assertSee('Handoff signal:')
            ->assertSee('Keep holder lifecycle and linkage findings in the live workspace before asking for export-driven handoff.');
    }

    public function test_reports_page_supports_selected_role_access_review_context(): void
    {
        $role = Role::create([
            'name' => 'Reporting Access Lead',
            'slug' => 'reporting-access-lead',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'Review reports',
            'slug' => 'review-reports',
        ]);

        $role->permissions()->attach($permission);

        $assignedUser = User::factory()->create([
            'name' => 'Levon Access Review',
        ]);

        $assignedUser->roles()->attach($role->id);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=role-access');

        $response
            ->assertOk()
            ->assertSee('Back to report catalog')
            ->assertSee('Reviewing: Galaxy access coverage')
            ->assertSee('Review export presets')
            ->assertSee('Blocked until role-access preset periods are verified against scope and assignment reporting parity.')
            ->assertSee('Export source snapshot')
            ->assertSee('Blocked until role-access export snapshots are verified against scope summaries and file delivery.')
            ->assertSee('Selected report source')
            ->assertSee('Galaxy access coverage')
            ->assertSee('Source coverage')
            ->assertSee('1 Galaxy access shells are currently available for read-only Galaxy access reporting review.')
            ->assertSee('Source status signal')
            ->assertSee('Galaxy access source is already visible with live access coverage for parity review.')
            ->assertSee('Source focus')
            ->assertSee('Start with role coverage and branch scope visibility before comparing any later export expectations.')
            ->assertSee('Source posture')
            ->assertSee('Keep access scope review in the live workspace first, then leave export-style access summaries in preview mode until parity is proven.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep active roles, permission-linked coverage, and assigned staff scope visible together before trusting any export view.')
            ->assertSee('Source signal')
            ->assertSee('live role coverage visible')
            ->assertSee('Galaxy input signal')
            ->assertSee('role inputs are ready for on-screen review')
            ->assertSee('Access mix signal')
            ->assertSee('role, bundle, and staffing inputs are jointly visible for access parity walkthrough')
            ->assertSee('Access readiness')
            ->assertSee('1 active roles already carry permission-linked access posture for on-screen review')
            ->assertSee('Assignment signal')
            ->assertSee('1 staff assignments are already visible for access review')
            ->assertSee('Assignment scope signal')
            ->assertSee('unscoped access-assignment coverage is still pending')
            ->assertSee('Assignment branch activity signal')
            ->assertSee('paused-branch access-assignment coverage is still pending')
            ->assertSee('Staff coverage signal')
            ->assertSee('unassigned active-role staff coverage is still pending')
            ->assertSee('Role state signal')
            ->assertSee('draft access-role coverage is still pending')
            ->assertSee('Handoff signal')
            ->assertSee('Keep role-coverage and staffing findings in the live workspace before asking for export-driven handoff.')
            ->assertSee('Backend gap')
            ->assertSee('Preset handling, grouped access shaping, and export generation should stay preview-only until scope and staffing parity are verified.')
            ->assertSee('Permission bundle signal')
            ->assertSee('unbundled active-role coverage is still pending')
            ->assertSee('Galaxy access source selected for Galaxy review')
            ->assertSee('This reporting view now reflects 1 tracked Galaxy access shells from the current Galaxy foundation layer.')
            ->assertSee('Implementation dependencies')
            ->assertSee('Source status signal:')
            ->assertSee('Galaxy access source is already visible with live access coverage for parity review.')
            ->assertSee('Access mix signal:')
            ->assertSee('role, bundle, and staffing inputs are jointly visible for access parity walkthrough')
            ->assertSee('Access readiness:')
            ->assertSee('1 active roles already carry permission-linked access posture for on-screen review')
            ->assertSee('Assignment signal:')
            ->assertSee('1 staff assignments are already visible for access review')
            ->assertSee('Assignment scope signal:')
            ->assertSee('unscoped access-assignment coverage is still pending')
            ->assertSee('Assignment branch activity signal:')
            ->assertSee('paused-branch access-assignment coverage is still pending')
            ->assertSee('Staff coverage signal:')
            ->assertSee('unassigned active-role staff coverage is still pending')
            ->assertSee('Role state signal:')
            ->assertSee('draft access-role coverage is still pending')
            ->assertSee('Permission bundle signal:')
            ->assertSee('unbundled active-role coverage is still pending')
            ->assertSee('Access posture:')
            ->assertSee('Role coverage should stay read-only until access-report parity and scope shaping are verified.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Preset handling, grouped access shaping, and export generation should stay preview-only until scope and staffing parity are verified.')
            ->assertSee('Handoff signal:')
            ->assertSee('Keep role-coverage and staffing findings in the live workspace before asking for export-driven handoff.');
    }

    public function test_reports_page_supports_selected_mixed_assignment_scope_review_context(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Scoped Access Branch',
            'code' => 'galaxy-scoped-access-branch',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Reporting Scoped Access Lead',
            'slug' => 'reporting-scoped-access-lead',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'Review scoped access reports',
            'slug' => 'review-scoped-access-reports',
        ]);

        $role->permissions()->attach($permission);

        $scopedUser = User::factory()->create([
            'name' => 'Scoped Access Operator',
            'shop_id' => $shop->id,
        ]);

        $unscopedUser = User::factory()->create([
            'name' => 'Bootstrap Access Operator',
            'shop_id' => null,
        ]);

        $scopedUser->roles()->attach($role->id);
        $unscopedUser->roles()->attach($role->id);

        $assignmentScopeSignal = '1 shop-linked staff assignments are already visible beside 1 unscoped access assignments for parity review';

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=role-access');

        $response
            ->assertOk()
            ->assertSee('Reviewing: Galaxy access coverage')
            ->assertSee('Source coverage')
            ->assertSee('1 Galaxy access shells are currently available for read-only Galaxy access reporting review.')
            ->assertSee('Assignment signal')
            ->assertSee('2 staff assignments are already visible for access review')
            ->assertSee('Assignment scope signal')
            ->assertSee($assignmentScopeSignal)
            ->assertSee('Scoped bundle signal')
            ->assertSee('1 permission-linked roles already carry shop-linked access scope for parity review')
            ->assertSee('Bundle branch activity signal')
            ->assertSee('paused-branch permission-bundle coverage is still pending')
            ->assertSee('Handoff signal')
            ->assertSee('Keep role-coverage and staffing findings in the live workspace before asking for export-driven handoff.')
            ->assertSee('Access-review handoff should stay visible in the workspace')
            ->assertSee('Operators should hand off role-coverage and staffing findings in the live review context before trusting export files for access decisions.')
            ->assertSee('Implementation dependencies')
            ->assertSee('Assignment scope signal:')
            ->assertSee($assignmentScopeSignal)
            ->assertSee('Scoped bundle signal:')
            ->assertSee('1 permission-linked roles already carry shop-linked access scope for parity review')
            ->assertSee('Bundle branch activity signal:')
            ->assertSee('paused-branch permission-bundle coverage is still pending')
            ->assertSee('Handoff signal:')
            ->assertSee('Keep role-coverage and staffing findings in the live workspace before asking for export-driven handoff.');
    }

    public function test_reports_page_supports_selected_mixed_assignment_branch_activity_review_context(): void
    {
        $activeShop = Shop::create([
            'name' => 'Galaxy Active Access Branch',
            'code' => 'galaxy-active-access-branch',
            'is_active' => true,
        ]);

        $pausedShop = Shop::create([
            'name' => 'Galaxy Paused Access Branch',
            'code' => 'galaxy-paused-access-branch',
            'is_active' => false,
        ]);

        $role = Role::create([
            'name' => 'Reporting Branch Activity Access Lead',
            'slug' => 'reporting-branch-activity-access-lead',
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'Review branch activity access reports',
            'slug' => 'review-branch-activity-access-reports',
        ]);

        $role->permissions()->attach($permission);

        $activeScopedUser = User::factory()->create([
            'name' => 'Active Branch Access Operator',
            'shop_id' => $activeShop->id,
        ]);

        $pausedScopedUser = User::factory()->create([
            'name' => 'Paused Branch Access Operator',
            'shop_id' => $pausedShop->id,
        ]);

        $activeScopedUser->roles()->attach($role->id);
        $pausedScopedUser->roles()->attach($role->id);

        $assignmentBranchActivitySignal = '1 branch-linked staff assignments are already visible in active Galaxy branches beside 1 branch-linked staff assignments in paused Galaxy branches for parity review';

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=role-access');

        $response
            ->assertOk()
            ->assertSee('Reviewing: Galaxy access coverage')
            ->assertSee('Assignment signal')
            ->assertSee('2 staff assignments are already visible for access review')
            ->assertSee('Assignment branch activity signal')
            ->assertSee($assignmentBranchActivitySignal)
            ->assertSee('Bundle branch activity signal')
            ->assertSee('1 permission-linked Galaxy access roles are already visible in active Galaxy branches beside 1 Galaxy access roles in paused Galaxy branches for parity review')
            ->assertSee('Access-review handoff should stay visible in the workspace')
            ->assertSee('Operators should hand off role-coverage and staffing findings in the live review context before trusting export files for access decisions.')
            ->assertSee('Implementation dependencies')
            ->assertSee('Assignment branch activity signal:')
            ->assertSee($assignmentBranchActivitySignal)
            ->assertSee('Bundle branch activity signal:')
            ->assertSee('1 permission-linked Galaxy access roles are already visible in active Galaxy branches beside 1 Galaxy access roles in paused Galaxy branches for parity review')
            ->assertSee('Handoff signal:')
            ->assertSee('Keep role-coverage and staffing findings in the live workspace before asking for export-driven handoff.');
    }

    public function test_reports_page_accepts_case_insensitive_selected_source_query(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central-reports-case-insensitive',
            'is_active' => true,
        ]);

        $cardType = CardType::create([
            'name' => 'Reporting Tier Case Insensitive',
            'slug' => 'reporting-tier-case-insensitive',
            'points_rate' => 1.00,
            'is_active' => true,
        ]);

        $cardHolder = CardHolder::create([
            'full_name' => 'Mariam Reporting Case Insensitive',
            'phone' => '+37499111226',
            'email' => 'mariam.reports.case@example.com',
            'status' => 'active',
            'shop_id' => $shop->id,
        ]);

        Card::create([
            'number' => '990011223347',
            'status' => 'active',
            'card_holder_id' => $cardHolder->id,
            'card_type_id' => $cardType->id,
            'shop_id' => $shop->id,
            'issued_at' => now(),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=CARDS-BY-SHOP');

        $response
            ->assertOk()
            ->assertSee('Back to report catalog')
            ->assertSee('Reviewing: Galaxy branch card-shell coverage')
            ->assertSee('Selected report source');
    }

    public function test_authenticated_user_can_access_services_rules_operational_index_shape(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules');

        $response
            ->assertOk()
            ->assertSeeText('Catalog / Services & Rules')
            ->assertSee('Birthday bonus')
            ->assertSee('/admin/services-rules?rule=birthday-bonus')
            ->assertSee('Rule type')
            ->assertSee('Partner card uplift')
            ->assertSee('/admin/services-rules?rule=partner-card-uplift')
            ->assertSee('Review birthday bonus rule');
    }

    public function test_services_rules_page_supports_selected_rule_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules?rule=night-service-block');

        $response
            ->assertOk()
            ->assertSee('Back to rule catalog')
            ->assertSee('/admin/services-rules')
            ->assertSee('Reviewing: Night service block')
            ->assertSee('Review priorities')
            ->assertSee('Blocked until draft rule priority order is verified against legacy exclusion precedence in the Galaxy foundation layer.')
            ->assertSee('Publish Galaxy rule')
            ->assertSee('Blocked until this draft rule clears CRUD, exclusion-parity, and publish-safety checks beyond the preview shell.')
            ->assertSee('Selected rule preview')
            ->assertSee('Night service block')
            ->assertSee('Rule status signal')
            ->assertSee('Draft exclusion rule remains safer for bar-service-exclusion parity review before any live-publish-flow discussion.')
            ->assertSee('Rule focus')
            ->assertSee('Start with the blocking condition and local scope before discussing any later publish decision.')
            ->assertSee('Rule handoff signal')
            ->assertSee('Carry blocking condition and local scope context forward before any later publish decision expands.')
            ->assertSee('Scope posture')
            ->assertSee('North Shop exclusions should stay draft-only until scoped exception behavior is verified against the legacy system.')
            ->assertSee('Condition posture')
            ->assertSee('Bar-service exclusions should remain draft-only until legacy exception behavior is rechecked in the Galaxy foundation layer.')
            ->assertSee('Priority posture')
            ->assertSee('Keep this blocking rule below confirmed accrual logic until exclusion order is verified.')
            ->assertSee('Backend gap')
            ->assertSee('Rule persistence, exclusion validation, and publish flow should stay preview-only until bar-service-exclusion parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep draft exclusion rules in compact on-screen review first, because operators need scope, condition, and effect visible together before discussing publication.')
            ->assertSee('Night service block selected for exception review')
            ->assertSee('Draft exclusion handoff stays cautious')
            ->assertSee('Draft exclusion handoff keeps parity evidence visible')
            ->assertSee('Scope, blocking condition, and no-accrual effect should stay visible in the workspace before any publish discussion begins.')
            ->assertSee('Remaining backend gap')
            ->assertSee('Rule persistence, exclusion validation, and publish flow should stay preview-only until bar-service-exclusion parity is verified.')
            ->assertSee('North Shop exclusions should stay draft-only until scoped exception behavior is verified against the legacy system.')
            ->assertSee('Blocking-rule order is still preview-only until exclusion precedence is validated in the Galaxy foundation layer.')
            ->assertSee('Treat the no-accrual effect as a review-only exception until the Galaxy foundation layer can safely reproduce the old block semantics.');
    }

    public function test_services_rules_page_supports_selected_scoped_rule_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules?rule=partner-card-uplift');

        $response
            ->assertOk()
            ->assertSee('Back to rule catalog')
            ->assertSee('/admin/services-rules')
            ->assertSee('Reviewing: Partner card uplift')
            ->assertSee('Review priorities')
            ->assertSee('Blocked until scoped rule priority order is verified against broader loyalty overlaps in the Galaxy foundation layer.')
            ->assertSee('Publish Galaxy rule')
            ->assertSee('Blocked until this scoped rule clears CRUD, scope-parity, and publish-safety checks beyond the preview shell.')
            ->assertSee('Selected rule preview')
            ->assertSee('Partner card uplift')
            ->assertSee('Scope')
            ->assertSee('Central Shop')
            ->assertSee('Rule status signal')
            ->assertSee('Active scoped uplift is already visible for partner-card uplift parity review.')
            ->assertSee('Rule focus')
            ->assertSee('Start with scoped card-type conditions before comparing this uplift against broader loyalty overlaps.')
            ->assertSee('Rule handoff signal')
            ->assertSee('Carry scoped card-type conditions and branch context forward before any broader publish discussion begins.')
            ->assertSee('Scope posture')
            ->assertSee('Shop-scoped behavior should stay preview-only until Galaxy foundation scope checks are verified against legacy branch rules.')
            ->assertSee('Condition posture')
            ->assertSee('Partner-card checks should stay tied to visible card-type parity before any Galaxy foundation rule editor opens them up.')
            ->assertSee('Priority posture')
            ->assertSee('This scoped uplift should remain below birthday-wide behavior until legacy overlap order is rechecked.')
            ->assertSee('Backend gap')
            ->assertSee('Rule persistence, partner-card condition editing, and publish flow should stay preview-only until partner-card uplift parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep scoped uplift rules in compact on-screen review first, because operators need scope, condition, and priority visible together before escalating.')
            ->assertSee('Effect guidance')
            ->assertSee('Treat the partner uplift as review-only until scoped accrual behavior is backed by Galaxy foundation rule writes.')
            ->assertSee('Partner card uplift selected for scope review')
            ->assertSee('Scoped uplift handoff stays branch-aware')
            ->assertSee('Scoped uplift handoff keeps branch evidence visible')
            ->assertSee('Scope, condition, and priority should stay visible in the workspace before any publish discussion begins.')
            ->assertSee('Remaining backend gap')
            ->assertSee('Rule persistence, partner-card condition editing, and publish flow should stay preview-only until partner-card uplift parity is verified.')
            ->assertSee('Shop-scoped behavior should stay preview-only until Galaxy foundation scope checks are verified against legacy branch rules.')
            ->assertSee('Overlap with broader loyalty rules still needs parity verification before any publish path is safe.');
    }

    public function test_services_rules_page_supports_selected_all_shop_rule_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules?rule=birthday-bonus');

        $response
            ->assertOk()
            ->assertSee('Back to rule catalog')
            ->assertSee('/admin/services-rules')
            ->assertSee('Reviewing: Birthday bonus')
            ->assertSee('Review priorities')
            ->assertSee('Blocked until all-shop rule priority order is verified in the Galaxy foundation layer.')
            ->assertSee('Publish Galaxy rule')
            ->assertSee('Blocked until this all-shop rule clears CRUD and publish-safety parity beyond the preview shell.')
            ->assertSee('Selected rule preview')
            ->assertSee('Birthday bonus')
            ->assertSee('Scope')
            ->assertSee('All shops')
            ->assertSee('Rule status signal')
            ->assertSee('Active loyalty uplift is already visible for birthday uplift parity review.')
            ->assertSee('Rule focus')
            ->assertSee('Start with birthday eligibility and priority order before discussing any later publish path.')
            ->assertSee('Rule handoff signal')
            ->assertSee('Carry birthday eligibility, scope, and uplift context forward before any later publish discussion begins.')
            ->assertSee('Scope posture')
            ->assertSee('All-shop scope should remain stable until Galaxy foundation scope handling is verified against legacy loyalty behavior.')
            ->assertSee('Condition posture')
            ->assertSee('Birthday window logic should stay parity-first, because date-sensitive loyalty rules are easy to drift during migration.')
            ->assertSee('Priority posture')
            ->assertSee('Keep this rule near the top of the preview stack until Galaxy foundation priority resolution is verified against the old Galaxy order.')
            ->assertSee('Backend gap')
            ->assertSee('Rule persistence, birthday-window editing, and publish flow should stay preview-only until all-shop birthday accrual parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep this rule in table-first review mode, because operators usually compare scope, effect, and priority together before discussing publication.')
            ->assertSee('Effect guidance')
            ->assertSee('Treat the uplift as review-only until accrual calculations and birthday eligibility are backed by Galaxy foundation writes.')
            ->assertSee('Birthday bonus selected for rule review')
            ->assertSee('Birthday rule handoff stays parity-first')
            ->assertSee('Birthday rule handoff keeps parity evidence visible')
            ->assertSee('Scope, priority, and uplift effect should stay visible in the workspace before any publish discussion begins.')
            ->assertSee('Remaining backend gap')
            ->assertSee('Rule persistence, birthday-window editing, and publish flow should stay preview-only until all-shop birthday accrual parity is verified.')
            ->assertSee('Priority resolution remains preview-only until overlapping rule order is validated in the Galaxy foundation layer.');
    }

    public function test_services_rules_page_ignores_unknown_selected_rule_and_falls_back_to_catalog(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules?rule=unknown-rule');

        $response
            ->assertOk()
            ->assertSee('Review birthday bonus rule')
            ->assertDontSee('Back to rule catalog')
            ->assertDontSee('Selected rule preview');
    }

    public function test_services_rules_page_accepts_case_insensitive_selected_rule_query(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules?rule=NIGHT-SERVICE-BLOCK');

        $response
            ->assertOk()
            ->assertSee('Back to rule catalog')
            ->assertSee('Reviewing: Night service block')
            ->assertSee('Selected rule preview');
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
                && strpos($content, 'Open issues to carry') < strpos($content, 'First Galaxy foundation wiring step'),
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
                && strpos($content, 'Legacy parity mapping') < strpos($content, 'First Galaxy foundation wiring step'),
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
                < strpos($content, 'First Galaxy foundation wiring step'),
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
                && strpos($content, 'No custom card types configured yet') < strpos($content, 'First Galaxy foundation wiring step'),
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
            strpos($content, 'Central Shop') < strpos($content, 'Active Galaxy branches')
                && strpos($content, 'Active Galaxy branches') < strpos($content, 'First Galaxy foundation wiring step'),
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
            strpos($content, 'Active Galaxy branches')
                < strpos($content, 'Recent activity preview')
                && strpos($content, 'Recent activity preview') < strpos($content, 'Shop operations are still preview-only')
                && strpos($content, 'Shop operations are still preview-only') < strpos($content, 'Open issues to carry')
                && strpos($content, 'Open issues to carry') < strpos($content, 'First Galaxy foundation wiring step'),
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
            strpos($content, 'Active Galaxy branches')
                < strpos($content, 'Recent activity preview')
                && strpos($content, 'Recent activity preview') < strpos($content, 'Shop operations are still preview-only')
                && strpos($content, 'Shop operations are still preview-only') < strpos($content, 'Open issues to carry')
                && strpos($content, 'Open issues to carry') < strpos($content, 'First Galaxy foundation wiring step'),
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
            strpos($content, 'Active Galaxy branches')
                < strpos($content, 'Recent activity preview')
                && strpos($content, 'Recent activity preview') < strpos($content, 'Shop operations are still preview-only')
                && strpos($content, 'Shop operations are still preview-only') < strpos($content, 'Open issues to carry')
                && strpos($content, 'Open issues to carry') < strpos($content, 'First Galaxy foundation wiring step'),
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
            ['label' => 'New Galaxy branch', 'tone' => 'primary'],
            'invalid-action-entry',
            ['tone' => 'secondary'],
            ['label' => 'Review branch scope', 'tone' => ['invalid-tone']],
            ['label' => 'Review branch scope', 'tone' => 'secondary'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('New Galaxy branch')
            ->assertSee('Review branch scope')
            ->assertDontSee('invalid-action-entry')
            ->assertDontSee('Array');
    }

    public function test_resource_summary_metrics_ignore_malformed_metric_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.shops.metrics', [
            ['label' => 'Active Galaxy branches', 'value' => '2'],
            'invalid-metric-entry',
            ['label' => 'Paused Galaxy branches'],
            ['label' => 'Assigned branch managers', 'value' => 2],
            ['label' => 'Paused Galaxy branches', 'value' => '1'],
        ]);

        $response = $this->actingAs($user)->get('/admin/shops');

        $response
            ->assertOk()
            ->assertSee('Active Galaxy branches')
            ->assertSee('Paused Galaxy branches')
            ->assertDontSee('invalid-metric-entry')
            ->assertDontSee('Assigned branch managers')
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
            ->assertSee('First Galaxy foundation wiring step')
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
                ['label' => 'Create first Galaxy role', 'tone' => 'primary'],
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
            ->assertSee('Create first Galaxy role')
            ->assertDontSee('invalid-action-entry')
            ->assertDontSee('Array');
    }

    public function test_form_preview_ignores_malformed_entries(): void
    {
        $user = User::factory()->create();

        Config::set('admin-pages.roles-permissions.form', [
            'title' => 'Create or edit Galaxy role',
            'actions' => [
                ['label' => 'Publish access', 'tone' => 'primary'],
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
            ->assertSee('Create or edit Galaxy role')
            ->assertSee('Publish access')
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
            ->assertSee('First Galaxy foundation wiring step');
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
            ->assertSee('Create or edit Galaxy role')
            ->assertSee('Publish access')
            ->assertSee('Blocked until role persistence and shop-scoped parity checks exist beyond the preview shell.')
            ->assertSee('No shop-scoped roles configured yet')
            ->assertSee('Create first Galaxy role');
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
            ->assertSee('First Galaxy foundation wiring step');
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
            ->assertSee('Create or edit Galaxy role')
            ->assertSee('Publish access')
            ->assertSee('Blocked until role persistence and shop-scoped parity checks exist beyond the preview shell.')
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
            ->assertSee('First Galaxy foundation wiring step');
    }

    public function test_authenticated_user_can_access_services_rules_management_preview(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules');

        $response
            ->assertOk()
            ->assertSee('Galaxy rule workspace for service groups, eligibility review, and loyalty behavior conditions.')
            ->assertSee('Create or edit Galaxy rule')
            ->assertSee('Publish Galaxy rule')
            ->assertSee('New Galaxy rule')
            ->assertSee('Blocked until the first Galaxy foundation-backed service-rule write flow exists for group, scope, effect, and priority.')
            ->assertSee('Review priorities')
            ->assertSee('Blocked until rule priority resolution is verified in the Galaxy foundation.')
            ->assertSee('Blocked until rule CRUD and parity checks exist beyond the preview shell.')
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('Management snapshot')
            ->assertSee('Active Galaxy rules')
            ->assertSee('Galaxy branch scopes')
            ->assertSee('No service rules configured yet')
            ->assertSee('Create first Galaxy rule')
            ->assertSee('Rule editing is still preview-only')
            ->assertSee('This screen outlines the target Galaxy rule workflow, but save and publish actions are not wired to Galaxy foundation handlers yet.')
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
            ->assertSee('First Galaxy foundation wiring step')
            ->assertSee('When backend work starts, introduce the smallest rule persistence path before attempting full condition-builder parity.')
            ->assertSee('Keep advanced condition syntax out of the first implementation slice.')
            ->assertSee('Service/rule domain is still preview-config only')
            ->assertSee('Rule CRUD endpoints and validation are still pending')
            ->assertSee('Legacy rule groups identified')
            ->assertSee('Rule persistence still blocked until Galaxy foundation handlers can run')
            ->assertSee('Define a first Galaxy foundation model and migration for service rules.')
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
            ->assertSee('Galaxy reward workspace for catalog scope, redemption settings, and stock-aware reward review.')
            ->assertSee('Coffee voucher')
            ->assertSee('/admin/gifts?gift=coffee-voucher')
            ->assertSee('Points range')
            ->assertSee('Premium dessert set')
            ->assertSee('/admin/gifts?gift=premium-dessert-set')
            ->assertSee('Review coffee voucher gift');
    }

    public function test_gifts_page_supports_selected_gift_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts?gift=premium-dessert-set');

        $response
            ->assertOk()
            ->assertSee('Back to gift catalog')
            ->assertSee('/admin/gifts')
            ->assertSee('Reviewing: Premium dessert set')
            ->assertSee('Stock audit')
            ->assertSee('Blocked until zero-stock recovery checks are backed by Galaxy foundation inventory data and reopening parity.')
            ->assertSee('Publish reward')
            ->assertSee('Blocked until this paused reward clears CRUD, stock-recovery, and redemption parity beyond the preview shell.')
            ->assertSee('Selected gift preview')
            ->assertSee('Premium dessert set')
            ->assertSee('Gift status signal')
            ->assertSee('Paused zero-stock reward remains safer for zero-stock-recovery-parity review before any reopening-flow discussion.')
            ->assertSee('Gift focus')
            ->assertSee('Start with zero-stock state and reopening risk before discussing any later publish decision.')
            ->assertSee('Gift handoff signal')
            ->assertSee('Carry zero-stock and reopening context forward before any later publish-review discussion expands.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep zero-stock state, shop scope, and points cost visible together before any reopening-flow discussion expands.')
            ->assertSee('Stock posture')
            ->assertSee('Zero-stock rewards should remain paused in review mode until Galaxy foundation inventory and reopening flows can reproduce the old behavior safely.')
            ->assertSee('Backend gap')
            ->assertSee('Gift CRUD, zero-stock recovery, and redemption persistence should stay preview-only until paused-zero-stock-recovery parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep paused zero-stock rewards in compact on-screen review first, because operators need scope, stock, and cost visible together before discussing reopening.')
            ->assertSee('Redemption guidance')
            ->assertSee('Treat this paused reward as review-only until stock recovery and redemption parity are backed by Galaxy foundation flows.')
            ->assertSee('Premium dessert set selected for paused reward review')
            ->assertSee('Paused reward handoff stays cautious')
            ->assertSee('Paused reward handoff keeps stock evidence visible')
            ->assertSee('Scope, zero-stock state, and points cost should stay visible in the workspace before any reopening-flow discussion begins.')
            ->assertSee('Remaining backend gap')
            ->assertSee('Gift CRUD, zero-stock recovery, and redemption persistence should stay preview-only until paused-zero-stock-recovery parity is verified.')
            ->assertSee('Zero-stock handling is still preview-only until inventory sync and recovery behavior are validated in the Galaxy foundation layer.');
    }

    public function test_gifts_page_supports_selected_scoped_gift_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts?gift=airport-transfer');

        $response
            ->assertOk()
            ->assertSee('Back to gift catalog')
            ->assertSee('/admin/gifts')
            ->assertSee('Reviewing: Airport transfer')
            ->assertSee('Stock audit')
            ->assertSee('Blocked until finite-stock checks are backed by Galaxy foundation inventory data and scoped stock parity.')
            ->assertSee('Publish reward')
            ->assertSee('Blocked until this scoped reward clears CRUD, scope-parity, and redemption checks beyond the preview shell.')
            ->assertSee('Selected gift preview')
            ->assertSee('Airport transfer')
            ->assertSee('Points cost')
            ->assertSee('900')
            ->assertSee('Gift status signal')
            ->assertSee('Active scoped reward is already visible for kiosk reward parity review.')
            ->assertSee('Gift focus')
            ->assertSee('Start with local stock and scope before comparing this reward against broader catalog behavior.')
            ->assertSee('Gift handoff signal')
            ->assertSee('Carry local stock and scope context forward before any broader publish discussion begins.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep stock, scope, and points cost visible together before comparing this reward against broader catalog behavior.')
            ->assertSee('Scope posture')
            ->assertSee('Kiosk-scoped rewards should stay branch-aware, because legacy redemption expectations depended on local availability.')
            ->assertSee('Stock posture')
            ->assertSee('Finite stock should remain review-only until Galaxy foundation inventory updates can preserve remaining-quantity parity.')
            ->assertSee('Backend gap')
            ->assertSee('Gift CRUD, kiosk-scoped stock updates, and redemption persistence should stay preview-only until kiosk-reward parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep kiosk-scoped rewards in compact on-screen review first, because operators need cost, stock, and local scope visible together before escalating.')
            ->assertSee('Redemption guidance')
            ->assertSee('Treat this scoped reward as review-only until stock-aware redemption behavior is backed by Galaxy foundation flows.')
            ->assertSee('Airport transfer selected for scoped reward review')
            ->assertSee('Finite-stock handoff stays branch-specific')
            ->assertSee('Finite-stock handoff keeps kiosk evidence visible')
            ->assertSee('Scope, remaining stock, and points cost should stay visible in the workspace before any publish discussion begins.')
            ->assertSee('Remaining backend gap')
            ->assertSee('Gift CRUD, kiosk-scoped stock updates, and redemption persistence should stay preview-only until kiosk-reward parity is verified.')
            ->assertSee('Shop-scoped reward behavior should stay preview-only until Galaxy foundation scope checks are verified against legacy kiosk rules.')
            ->assertSee('Finite-stock handling still needs backend inventory wiring before a publish path is safe.');
    }

    public function test_gifts_page_supports_selected_all_shop_gift_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts?gift=coffee-voucher');

        $response
            ->assertOk()
            ->assertSee('Back to gift catalog')
            ->assertSee('/admin/gifts')
            ->assertSee('Reviewing: Coffee voucher')
            ->assertSee('Stock audit')
            ->assertSee('Blocked until all-shop stock checks are backed by Galaxy foundation inventory data.')
            ->assertSee('Publish reward')
            ->assertSee('Blocked until this all-shop reward clears CRUD and redemption parity beyond the preview shell.')
            ->assertSee('Selected gift preview')
            ->assertSee('Coffee voucher')
            ->assertSee('Points cost')
            ->assertSee('150')
            ->assertSee('Gift status signal')
            ->assertSee('Active all-shop reward is already visible for live all-shop reward parity review.')
            ->assertSee('Gift focus')
            ->assertSee('Start with points cost and stock policy before discussing any later publish path.')
            ->assertSee('Gift handoff signal')
            ->assertSee('Carry points cost, stock policy, and scope context forward before any later publish discussion begins.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep points cost, stock policy, and shop scope visible together before comparing this reward against any later publish narrative.')
            ->assertSee('Scope posture')
            ->assertSee('All-shop rewards should stay parity-first, because wide-scope catalog changes affect the most operators and redemptions.')
            ->assertSee('Stock posture')
            ->assertSee('Unlimited stock can stay reviewable, but warehouse sync assumptions should remain explicit until Galaxy foundation inventory writes exist.')
            ->assertSee('Backend gap')
            ->assertSee('Gift CRUD, all-shop stock assumptions, and redemption persistence should stay preview-only until all-shop-reward parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep this reward in table-first review mode, because operators usually compare scope, stock policy, and points cost together before discussing publication.')
            ->assertSee('Redemption guidance')
            ->assertSee('Treat this reward as review-only until gift CRUD and redemption parity are backed by Galaxy foundation flows.')
            ->assertSee('Coffee voucher selected for reward review')
            ->assertSee('All-shop reward handoff stays stock-aware')
            ->assertSee('All-shop reward handoff keeps catalog evidence visible')
            ->assertSee('Points cost, stock policy, and shop scope should stay visible in the workspace before any publish discussion begins.')
            ->assertSee('Remaining backend gap')
            ->assertSee('Gift CRUD, all-shop stock assumptions, and redemption persistence should stay preview-only until all-shop-reward parity is verified.')
            ->assertSee('All-shop reward coverage should remain stable until Galaxy foundation scope handling is verified against the legacy catalog.')
            ->assertSee('Unlimited-stock assumptions still need backend inventory wiring before operators can trust live publish behavior.');
    }

    public function test_gifts_page_supports_selected_paused_finite_stock_gift_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts?gift=weekend-brunch-pass');

        $response
            ->assertOk()
            ->assertSee('Back to gift catalog')
            ->assertSee('/admin/gifts')
            ->assertSee('Reviewing: Weekend brunch pass')
            ->assertSee('Stock audit')
            ->assertSee('Blocked until finite-stock checks are backed by Galaxy foundation inventory data and scoped stock parity.')
            ->assertSee('Publish reward')
            ->assertSee('Blocked until this paused reward clears CRUD, stock-recovery, and redemption parity beyond the preview shell.')
            ->assertSee('Selected gift preview')
            ->assertSee('Weekend brunch pass')
            ->assertSee('Points cost')
            ->assertSee('320')
            ->assertSee('Gift status signal')
            ->assertSee('Paused finite-stock reward remains safer for paused-branch-reopening-parity review before any reopening-flow discussion.')
            ->assertSee('Gift focus')
            ->assertSee('Start with remaining stock and local reopening assumptions before any wider catalog-review discussion begins.')
            ->assertSee('Gift handoff signal')
            ->assertSee('Carry remaining stock and local reopening context forward before any wider catalog-review discussion begins.')
            ->assertSee('Evidence priority')
            ->assertSee('Keep remaining stock, local scope, and points cost visible together before any wider catalog-review discussion begins.')
            ->assertSee('Scope posture')
            ->assertSee('Paused branch rewards should stay locally reviewable, because reopening decisions still depend on shop-specific redemption habits.')
            ->assertSee('Stock posture')
            ->assertSee('Finite paused stock should remain review-only until Galaxy foundation inventory updates and reopening flows can preserve remaining-quantity parity.')
            ->assertSee('Backend gap')
            ->assertSee('Gift CRUD, paused-stock recovery, and redemption persistence should stay preview-only until paused-branch-reopening parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep paused finite-stock rewards in compact on-screen review first, because operators need scope, stock, and reopening posture visible together before escalating.')
            ->assertSee('Redemption guidance')
            ->assertSee('Treat this paused branch reward as review-only until stock-aware reopening and redemption parity are backed by Galaxy foundation flows.')
            ->assertSee('Weekend brunch pass selected for paused branch reward review')
            ->assertSee('Paused branch reward handoff stays stock-aware')
            ->assertSee('Paused branch reward keeps finite-stock evidence visible')
            ->assertSee('Scope, remaining stock, and points cost should stay visible in the workspace before any reopening-flow discussion begins.')
            ->assertSee('Remaining backend gap')
            ->assertSee('Gift CRUD, paused-stock recovery, and redemption persistence should stay preview-only until paused-branch-reopening parity is verified.')
            ->assertSee('Paused branch reward behavior should stay preview-only until Galaxy foundation scope and reopening checks are verified.')
            ->assertSee('Finite paused stock still needs backend inventory wiring before operators can trust reopening decisions.');
    }

    public function test_gifts_page_ignores_unknown_selected_gift_and_falls_back_to_catalog(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts?gift=unknown-gift');

        $response
            ->assertOk()
            ->assertSee('Review coffee voucher gift')
            ->assertDontSee('Back to gift catalog')
            ->assertDontSee('Selected gift preview');
    }

    public function test_gifts_page_accepts_case_insensitive_selected_gift_query(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts?gift=PREMIUM-DESSERT-SET');

        $response
            ->assertOk()
            ->assertSee('Back to gift catalog')
            ->assertSee('Reviewing: Premium dessert set')
            ->assertSee('Selected gift preview');
    }

    public function test_authenticated_user_can_access_gifts_management_preview(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts');

        $response
            ->assertOk()
            ->assertSee('Create or edit Galaxy reward')
            ->assertSee('Publish reward')
            ->assertSee('New Galaxy reward')
            ->assertSee('Blocked until the first Galaxy foundation-backed gift write flow exists for catalog, scope, cost, and stock state.')
            ->assertSee('Stock audit')
            ->assertSee('Blocked until stock checks are backed by Galaxy foundation inventory data.')
            ->assertSee('Blocked until gift CRUD and redemption parity exist beyond the preview shell.')
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('Management snapshot')
            ->assertSee('Active Galaxy rewards')
            ->assertSee('Low stock items')
            ->assertSee('No gift campaigns configured yet')
            ->assertSee('Create first Galaxy reward')
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
            ->assertSee('First Galaxy foundation wiring step')
            ->assertSee('When PHP is available, begin with basic gift CRUD and defer stock synchronization until after the first write path works.')
            ->assertSee('Treat warehouse sync and redemption logs as a later follow-up slice.')
            ->assertSee('Gift domain is still represented through config-backed preview data')
            ->assertSee('CRUD handlers, stock updates, and redemption persistence are still pending')
            ->assertSee('Legacy reward catalog mapped')
            ->assertSee('Real redemption and stock sync need PHP-backed flows')
            ->assertSee('Coffee voucher stock policy checked')
            ->assertSee('Unlimited stock remains the baseline until real warehouse sync is wired in the Galaxy foundation.')
            ->assertSee('Premium dessert set paused')
            ->assertSee('Old Galaxy gift and reward list')
            ->assertSee('stock-aware redemption')
            ->assertSee('Unlimited')
            ->assertSee('Coffee voucher');
    }

    public function test_shop_scoped_admin_sees_card_type_mutation_actions_disabled_in_card_types_workspace(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Tier Workspace Branch',
            'code' => 'galaxy-scoped-tier-workspace-branch',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Tier Workspace Operator',
            'slug' => 'scoped-tier-workspace-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Review scoped tier workspace',
            'slug' => 'review-scoped-tier-workspace',
            'review_note' => 'Phase 1 scoped tier workspace action gating coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime-scoped-workspace',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('admin.card-types.index', ['cardType' => $cardType]));

        $response
            ->assertOk()
            ->assertSee('Only bootstrap admins can reshape Galaxy tier shells while Phase 1 keeps the tier foundation under central control.')
            ->assertSee('Review the selected Galaxy tier in the shared live form while Phase 1 keeps tier-shell changes under bootstrap control.')
            ->assertSee('Back to tier catalog')
            ->assertSee('Move to draft')
            ->assertSee('>Save tier changes<', false)
            ->assertSee('disabled title="Only bootstrap admins can reshape Galaxy tier shells while Phase 1 keeps the tier foundation under central control." aria-disabled="true"', false)
            ->assertSee('name="name"', false)
            ->assertSee('readonly', false)
            ->assertSee('aria-readonly="true"', false)
            ->assertSee('name="is_active"', false)
            ->assertSee('aria-disabled="true"', false);
    }

    public function test_authenticated_user_can_access_card_types_management_preview(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/card-types');

        $response
            ->assertOk()
            ->assertSee('Card Types placeholder')
            ->assertSee('Galaxy tier workspace for card-tier identities, points rules, and activation review.')
            ->assertSee('Create Galaxy tier in Galaxy foundation')
            ->assertSee('Create tier shell')
            ->assertSee('Status')
            ->assertSee('Active')
            ->assertSee('Draft')
            ->assertSee('Create or edit Galaxy tier')
            ->assertSee('Publish tier')
            ->assertSee('Blocked until the first Galaxy foundation-backed tier exists before any publish-style rollout.')
            ->assertSee('New Galaxy tier')
            ->assertSee('href="#live-form"', false)
            ->assertSee('Import rules')
            ->assertSee('Blocked until the first Galaxy foundation-backed tier exists for rule parity review.')
            ->assertSee('Management snapshot')
            ->assertSee('Active Galaxy tiers')
            ->assertSee('Imported rules')
            ->assertSee('No custom card types configured yet')
            ->assertSee('Create first Galaxy tier')
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
            ->assertSee('A minimal Galaxy foundation create path now exists for card types, while the richer tier rules and publish workflow remain preview-only.')
            ->assertSee('Minimal Galaxy foundation create path is now wired for card types')
            ->assertSee('Operator checklist')
            ->assertSee('Review activation mode before publishing a new or changed tier.')
            ->assertSee('Escalation guide')
            ->assertSee('Escalate activation-rule disagreements before publishing a tier change.')
            ->assertSee('Shift handoff notes')
            ->assertSee('Hand off draft tiers with the exact legacy rate and activation mode they are meant to mirror.')
            ->assertSee('Open issues to carry')
            ->assertSee('Partner tier approval flow parity is still unresolved against the legacy workflow.')
            ->assertSee('First Galaxy foundation wiring step')
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

    public function test_card_types_catalog_actions_reflect_saved_tier_readiness(): void
    {
        CardType::create([
            'name' => 'Gold',
            'slug' => 'gold-catalog-readiness',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('Import rules')
            ->assertSee('Blocked until saved tier accrual parity is verified before importing legacy rules.')
            ->assertSee('Publish tier')
            ->assertSee('Blocked until saved live tiers clear Galaxy tier rollout parity before any broader catalog move.');
    }

    public function test_card_types_page_exposes_edit_link_for_latest_saved_type(): void
    {
        CardType::create([
            'name' => 'Gold',
            'slug' => 'gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $latestCardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '2.00',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('Edit latest saved tier shell')
            ->assertSee('href="/admin/card-types?cardType='.$latestCardType->id.'#live-form"', false);
    }

    public function test_card_types_page_switches_live_form_into_real_edit_mode_for_selected_card_type(): void
    {
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.75',
            'is_active' => false,
            'review_note' => 'Keep this tier in draft until legacy accrual parity is confirmed.',
            'activation_note' => 'Only activate this tier after the legacy branch handoff is verified.',
            'rollout_note' => 'Keep rollout review-only until legacy tier behavior is verified branch by branch.',
        ]);

        $shop = Shop::create([
            'name' => 'Galaxy Coverage Branch',
            'code' => 'galaxy-coverage-branch-card-type',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'number' => 'GX-CT-200001',
            'status' => 'draft',
            'card_type_id' => $cardType->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index', ['cardType' => $cardType->id]));

        $response
            ->assertOk()
            ->assertSee('Create new Galaxy tier shell')
            ->assertSee('href="/admin/card-types#live-form"', false)
            ->assertSee('Activate tier')
            ->assertSee('<form method="POST" action="/admin/card-types/'.$cardType->id.'/toggle-status"', false)
            ->assertSee('name="_method"', false)
            ->assertSee('value="PATCH"', false)
            ->assertSee('Editing: Galaxy Prime')
            ->assertSee('Import rules')
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('Blocked until draft tier accrual parity is verified against visible card coverage.')
            ->assertSee('Publish tier')
            ->assertSee('Blocked until this draft tier clears rule and rollout parity review against visible card coverage.')
            ->assertSee('Selected record summary')
            ->assertSee('Selected tier:')
            ->assertSee('Galaxy Prime')
            ->assertSee('Slug:')
            ->assertSee('galaxy-prime')
            ->assertSee('Points rate:')
            ->assertSee('1.75x')
            ->assertSee('Galaxy status:')
            ->assertSee('draft')
            ->assertSee('Lifecycle freshness:')
            ->assertSee('newly created in Galaxy foundation review')
            ->assertSee('Last saved in Galaxy foundation:')
            ->assertSee('Review note:')
            ->assertSee('Keep this tier in draft until legacy accrual parity is confirmed.')
            ->assertSee('Activation note:')
            ->assertSee('Only activate this tier after the legacy branch handoff is verified.')
            ->assertSee('Activation freshness:')
            ->assertSee('Activation note is already staged on this draft Galaxy foundation tier shell.')
            ->assertSee('Rollout note:')
            ->assertSee('Keep rollout review-only until legacy tier behavior is verified branch by branch.')
            ->assertSee('Rollout freshness:')
            ->assertSee('Rollout note is already staged on this draft Galaxy foundation tier shell.')
            ->assertSee('Coverage signal:')
            ->assertSee('draft tier with visible card coverage')
            ->assertSee('Coverage freshness:')
            ->assertSee('Draft tier already has saved card coverage anchored in Galaxy foundation data for parity review.')
            ->assertSee('Tier status signal:')
            ->assertSee('Draft tier remains safer for parity review while saved card coverage is already visible.')
            ->assertSee('Tier focus:')
            ->assertSee('Start with saved card coverage, draft status, activation readiness, and rollout note clarity before discussing any later rule import step.')
            ->assertSee('Tier posture:')
            ->assertSee('Keep draft tier review in the workspace first, then leave rule import and publish-style moves gated until parity is proven.')
            ->assertSee('Evidence priority:')
            ->assertSee('Keep visible card coverage, activation readiness, and rollout note together before trusting any later rule import discussion.')
            ->assertSee('Handoff signal:')
            ->assertSee('Draft tier already carries visible card coverage for a useful parity handoff review.')
            ->assertSee('Backend gap:')
            ->assertSee('Draft activation, publish logic, and rule-import parity should stay preview-only until draft tier parity is verified.')
            ->assertSee('Status guidance:')
            ->assertSee('This tier is still in draft, which keeps it safe for parity checks before operators treat it as live loyalty behavior.')
            ->assertSee('Rule-import blocker:')
            ->assertSee('Rule import is still blocked, but draft state keeps this tier safe for parity-first catalog and accrual checks.')
            ->assertSee('Publish guidance:')
            ->assertSee('Keep this tier in draft until rule import expectations and old Galaxy behavior are mapped clearly enough to publish safely.')
            ->assertSee('Readiness signal:')
            ->assertSee('Not ready to publish: draft mode is still the holding state for parity validation and rule-import review.')
            ->assertSee('Recent activity preview')
            ->assertSee('Galaxy Prime selected for Galaxy edit flow')
            ->assertSee('Current request')
            ->assertSee('The shared card-type form is now loading this saved tier directly from Galaxy foundation data instead of preview-only defaults.')
            ->assertSee('Galaxy Prime status reflected from model state')
            ->assertSee('This tier is currently marked as draft in the Galaxy foundation layer and the management context card now mirrors that state.')
            ->assertSee('Galaxy Prime lifecycle freshness reflected from model state')
            ->assertSee('This tier was created in the Galaxy foundation layer on')
            ->assertSee('and has not been updated since, so operators are still reviewing the first saved catalog shell.')
            ->assertSee('Galaxy Prime last saved timestamp reflected from model state')
            ->assertSee('The latest saved Galaxy foundation timestamp for this tier is')
            ->assertSee('giving operators a concrete checkpoint for the current catalog shell.')
            ->assertSee('Galaxy Prime review note reflected from model state')
            ->assertSee('The current Galaxy foundation tier review note says: Keep this tier in draft until legacy accrual parity is confirmed.')
            ->assertSee('Galaxy Prime activation note reflected from model state')
            ->assertSee('The current Galaxy foundation activation note says: Only activate this tier after the legacy branch handoff is verified.')
            ->assertSee('Galaxy Prime rollout note reflected from model state')
            ->assertSee('The current Galaxy foundation rollout note says: Keep rollout review-only until legacy tier behavior is verified branch by branch.')
            ->assertSee('Galaxy Prime card coverage signal reflected from model state')
            ->assertSee('The current Galaxy foundation tier is showing draft tier with visible card coverage in the workspace review shell.')
            ->assertSee('Galaxy Prime card coverage freshness reflected from model state')
            ->assertSee('Draft tier already has saved card coverage anchored in Galaxy foundation data for parity review.')
            ->assertSee('Galaxy Prime tier status signal reflected from model state')
            ->assertSee('Draft tier remains safer for parity review while saved card coverage is already visible.')
            ->assertSee('Galaxy Prime handoff signal reflected from model state')
            ->assertSee('Draft tier already carries visible card coverage for a useful parity handoff review.')
            ->assertSee('Galaxy Prime backend gap reflected from model state')
            ->assertSee('Draft activation, publish logic, and rule-import parity should stay preview-only until draft tier parity is verified.')
            ->assertSee('Galaxy Prime rule-import posture reflected from model state')
            ->assertSee('Imports can be reviewed in draft mode, but they are still not safe to enable yet')
            ->assertSee('Galaxy Prime publish posture reflected from model state')
            ->assertSee('Draft tiers should stay unpublished until legacy behavior is mapped more explicitly')
            ->assertSee('Galaxy Prime action gating reflected from model state')
            ->assertSee('Allow draft-safe edits and validation only, keep live-facing actions gated')
            ->assertSee('Implementation dependencies')
            ->assertSee('Selected record:')
            ->assertSee('Edit flow state:')
            ->assertSee('Shared live form is running in request-driven PATCH mode')
            ->assertSee('Lifecycle freshness:')
            ->assertSee('newly created in Galaxy foundation review')
            ->assertSee('Last saved in Galaxy foundation:')
            ->assertSee('Review note:')
            ->assertSee('Keep this tier in draft until legacy accrual parity is confirmed.')
            ->assertSee('Activation note:')
            ->assertSee('Only activate this tier after the legacy branch handoff is verified.')
            ->assertSee('Activation freshness:')
            ->assertSee('Activation note is already staged on this draft Galaxy foundation tier shell.')
            ->assertSee('Rollout note:')
            ->assertSee('Keep rollout review-only until legacy tier behavior is verified branch by branch.')
            ->assertSee('Rollout freshness:')
            ->assertSee('Rollout note is already staged on this draft Galaxy foundation tier shell.')
            ->assertSee('Coverage signal:')
            ->assertSee('draft tier with visible card coverage')
            ->assertSee('Coverage freshness:')
            ->assertSee('Draft tier already has saved card coverage anchored in Galaxy foundation data for parity review.')
            ->assertSee('Tier status signal:')
            ->assertSee('Draft tier remains safer for parity review while saved card coverage is already visible.')
            ->assertSee('Handoff signal:')
            ->assertSee('Draft tier already carries visible card coverage for a useful parity handoff review.')
            ->assertSee('Current status posture:')
            ->assertSee('Draft tiers are the safe place for parity-first validation and copy changes')
            ->assertSee('Rule-import posture:')
            ->assertSee('Imports can be reviewed in draft mode, but they are still not safe to enable yet')
            ->assertSee('Publish posture:')
            ->assertSee('Draft tiers should stay unpublished until legacy behavior is mapped more explicitly')
            ->assertSee('Action gating:')
            ->assertSee('Allow draft-safe edits and validation only, keep live-facing actions gated')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Draft activation, publish logic, and rule-import parity should stay preview-only until draft tier parity is verified.')
            ->assertSee('Edit Galaxy tier in Galaxy foundation')
            ->assertSee('Update the selected Galaxy tier through the shared live form without leaving the card-types workspace.')
            ->assertSee('>Save tier changes<', false)
            ->assertSee('action="/admin/card-types/'.$cardType->id.'"', false)
            ->assertSee('name="_method"', false)
            ->assertSee('value="PATCH"', false)
            ->assertSee('value="Galaxy Prime"', false)
            ->assertSee('value="galaxy-prime"', false)
            ->assertSee('value="1.75"', false)
            ->assertSee('<option value="0" selected>Draft</option>', false)
            ->assertSee('href="/admin/card-types"', false);
    }

    public function test_selected_live_card_type_without_card_coverage_shows_readiness_driven_action_gating_reasons(): void
    {
        $cardType = CardType::create([
            'name' => 'Galaxy Platinum',
            'slug' => 'galaxy-platinum',
            'points_rate' => '2.25',
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index', ['cardType' => $cardType->id]));

        $response
            ->assertOk()
            ->assertSee('Editing: Galaxy Platinum')
            ->assertSee('Import rules')
            ->assertSee('Blocked until this live tier has visible card coverage for accrual parity review.')
            ->assertSee('Publish tier')
            ->assertSee('Blocked until this live tier has visible card coverage and Galaxy tier rollout parity review.');
    }

    public function test_selected_live_card_type_with_visible_card_coverage_surfaces_live_tier_status_signal(): void
    {
        $cardType = CardType::create([
            'name' => 'Galaxy Onyx',
            'slug' => 'galaxy-onyx',
            'points_rate' => '2.60',
            'is_active' => true,
        ]);

        $shop = Shop::create([
            'name' => 'Galaxy Tier Coverage Branch',
            'code' => 'galaxy-tier-coverage-branch',
            'is_active' => true,
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'number' => 'GX-CT-300001',
            'status' => 'active',
            'card_type_id' => $cardType->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index', ['cardType' => $cardType->id]));

        $response
            ->assertOk()
            ->assertSee('Editing: Galaxy Onyx')
            ->assertSee('Coverage signal:')
            ->assertSee('live tier with visible card coverage')
            ->assertSee('Coverage freshness:')
            ->assertSee('Live tier already has saved card coverage anchored in Galaxy foundation data for rollout review.')
            ->assertSee('Galaxy Onyx card coverage freshness reflected from model state')
            ->assertSee('Tier status signal:')
            ->assertSee('Active tier is already visible with saved card coverage for live tier parity review.')
            ->assertSee('Handoff signal:')
            ->assertSee('Live tier already carries visible card coverage for a useful rollout-parity handoff review.');
    }

    public function test_selected_live_card_type_without_visible_card_coverage_shows_readiness_driven_action_gating_reasons(): void
    {
        $cardType = CardType::create([
            'name' => 'Galaxy Amber',
            'slug' => 'galaxy-amber-live-no-cards',
            'points_rate' => '1.40',
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/card-types?cardType='.$cardType->id);

        $response
            ->assertOk()
            ->assertSee('Editing: Galaxy Amber')
            ->assertSee('Tier status signal:')
            ->assertSee('Active tier is already visible, but card coverage still needs rollout-parity review before any rollout discussion.')
            ->assertSee('Tier focus:')
            ->assertSee('Start with saved card coverage, live status, and rollout note clarity before discussing any later publish reversal or rule import step.')
            ->assertSee('Tier posture:')
            ->assertSee('Keep live tier review in the workspace first, then leave publish reversal, rule import, and rollout-sensitive moves gated until parity is proven.')
            ->assertSee('Evidence priority:')
            ->assertSee('Keep visible card coverage, live status, activation note, and rollout note together before trusting any later publish reversal or rule import discussion.')
            ->assertSee('Activation freshness:')
            ->assertSee('Live tier still needs a saved activation note before rollout handoff can feel grounded.')
            ->assertSee('Rollout freshness:')
            ->assertSee('Live tier still needs a saved rollout note before rollout handoff can feel grounded.')
            ->assertSee('Coverage freshness:')
            ->assertSee('Live tier still needs its first saved card coverage before rollout review can feel grounded.')
            ->assertSee('Galaxy Amber card coverage freshness reflected from model state')
            ->assertSee('Galaxy Amber tier status signal reflected from model state')
            ->assertSee('Galaxy Amber tier focus reflected from model state')
            ->assertSee('Start with saved card coverage, live status, and rollout note clarity before discussing any later publish reversal or rule import step.')
            ->assertSee('Galaxy Amber tier posture reflected from model state')
            ->assertSee('Keep live tier review in the workspace first, then leave publish reversal, rule import, and rollout-sensitive moves gated until parity is proven.')
            ->assertSee('Galaxy Amber evidence priority reflected from model state')
            ->assertSee('Keep visible card coverage, live status, activation note, and rollout note together before trusting any later publish reversal or rule import discussion.')
            ->assertSee('Galaxy Amber current status posture reflected from model state')
            ->assertSee('Active tiers should stay stable unless parity checks are complete')
            ->assertSee('Galaxy Amber handoff signal reflected from model state')
            ->assertSee('Live tier should stay in handoff-only posture until visible card coverage and rollout parity are explicit.')
            ->assertSee('Galaxy Amber backend gap reflected from model state')
            ->assertSee('Rollout confirmation, publish logic, and rule-import parity should stay preview-only until live tier coverage is verified.')
            ->assertSee('Galaxy Amber rule-import posture reflected from model state')
            ->assertSee('Keep imports blocked until active-tier accrual parity is verified')
            ->assertSee('Galaxy Amber publish posture reflected from model state')
            ->assertSee('Live tiers need parity confirmation before further publish-style changes')
            ->assertSee('Galaxy Amber action gating reflected from model state')
            ->assertSee('Allow small state corrections only, keep publish-like and import actions gated')
            ->assertSee('Handoff signal:')
            ->assertSee('Live tier should stay in handoff-only posture until visible card coverage and rollout parity are explicit.')
            ->assertSee('Backend gap:')
            ->assertSee('Rollout confirmation, publish logic, and rule-import parity should stay preview-only until live tier coverage is verified.')
            ->assertSee('Import rules')
            ->assertSee('Blocked until this live tier has visible card coverage for accrual parity review.')
            ->assertSee('Publish tier')
            ->assertSee('Blocked until this live tier has visible card coverage and Galaxy tier rollout parity review.');
    }

    public function test_selected_draft_card_type_without_visible_card_coverage_shows_readiness_driven_action_gating_reasons(): void
    {
        $cardType = CardType::create([
            'name' => 'Galaxy Seed Tier',
            'slug' => 'galaxy-seed-tier-no-cards',
            'points_rate' => '1.05',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/card-types?cardType='.$cardType->id);

        $response
            ->assertOk()
            ->assertSee('Editing: Galaxy Seed Tier')
            ->assertSee('Tier status signal:')
            ->assertSee('Draft tier remains safer for visible-card-coverage parity-review before any rollout discussion lands.')
            ->assertSee('Tier focus:')
            ->assertSee('Start with saved card coverage, draft status, activation readiness, and rollout note clarity before discussing any later rule import step.')
            ->assertSee('Tier posture:')
            ->assertSee('Keep draft tier review in the workspace first, then leave rule import and publish-style moves gated until parity is proven.')
            ->assertSee('Evidence priority:')
            ->assertSee('Keep visible card coverage, activation readiness, and rollout note together before trusting any later rule import discussion.')
            ->assertSee('Coverage freshness:')
            ->assertSee('Draft tier is still waiting on its first saved card coverage before parity review can feel grounded.')
            ->assertSee('Galaxy Seed Tier card coverage signal reflected from model state')
            ->assertSee('The current Galaxy foundation tier is showing draft tier, card coverage pending in the workspace review shell.')
            ->assertSee('Galaxy Seed Tier card coverage freshness reflected from model state')
            ->assertSee('Galaxy Seed Tier tier focus reflected from model state')
            ->assertSee('Start with saved card coverage, draft status, activation readiness, and rollout note clarity before discussing any later rule import step.')
            ->assertSee('Galaxy Seed Tier tier posture reflected from model state')
            ->assertSee('Keep draft tier review in the workspace first, then leave rule import and publish-style moves gated until parity is proven.')
            ->assertSee('Galaxy Seed Tier evidence priority reflected from model state')
            ->assertSee('Keep visible card coverage, activation readiness, and rollout note together before trusting any later rule import discussion.')
            ->assertSee('Galaxy Seed Tier current status posture reflected from model state')
            ->assertSee('Draft tiers are the safe place for parity-first validation and copy changes')
            ->assertSee('Handoff signal:')
            ->assertSee('Draft tier should stay in handoff-only posture until visible card coverage grounds rollout review.')
            ->assertSee('Backend gap:')
            ->assertSee('Draft activation, publish logic, and rule-import parity should stay preview-only until visible tier coverage is verified.')
            ->assertSee('Import rules')
            ->assertSee('Blocked until draft parity review has visible card coverage to compare against.')
            ->assertSee('Publish tier')
            ->assertSee('Blocked until this draft tier clears rule-and-rollout parity review before any publish-like move.');
    }

    public function test_card_types_page_ignores_unknown_selected_card_type_query(): void
    {
        CardType::create([
            'name' => 'Galaxy Silver',
            'slug' => 'galaxy-silver-unknown-selected',
            'points_rate' => '1.25',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/card-types?cardType=999999');

        $response
            ->assertOk()
            ->assertSee('Galaxy Silver')
            ->assertSee('Edit latest saved tier shell')
            ->assertDontSee('Edit card type in Laravel')
            ->assertDontSee('Selected tier')
            ->assertDontSee('selected for Laravel edit flow');
    }

    public function test_card_types_page_ignores_malformed_selected_card_type_query(): void
    {
        CardType::create([
            'name' => 'Galaxy Silver',
            'slug' => 'galaxy-silver-malformed-selected',
            'points_rate' => '1.25',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/card-types?cardType=not-a-number');

        $response
            ->assertOk()
            ->assertSee('Galaxy Silver')
            ->assertSee('Edit latest saved tier shell')
            ->assertDontSee('Edit card type in Laravel')
            ->assertDontSee('Selected tier')
            ->assertDontSee('selected for Laravel edit flow');
    }

    public function test_shop_scoped_admin_cannot_toggle_card_type_status(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Tier Toggle Branch',
            'code' => 'galaxy-scoped-tier-toggle-branch',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Tier Toggle Operator',
            'slug' => 'scoped-tier-toggle-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Toggle scoped card types',
            'slug' => 'toggle-scoped-card-types',
            'review_note' => 'Phase 1 scoped card type toggle coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $cardType = CardType::create([
            'name' => 'Galaxy Protected Tier',
            'slug' => 'galaxy-protected-tier',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.card-types.toggle-status', $cardType));

        $response->assertForbidden();

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'is_active' => true,
        ]);
    }

    public function test_authenticated_user_can_toggle_card_type_status_from_header_action(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.card-types.toggle-status', $cardType));

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Prime" is now draft.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'is_active' => false,
        ]);
    }

    public function test_card_types_page_replaces_preview_rows_with_model_backed_edit_links(): void
    {
        $gold = CardType::create([
            'name' => 'Gold',
            'slug' => 'gold',
            'points_rate' => '1.50',
            'is_active' => true,
            'review_note' => 'Keep Gold aligned with the legacy auto-activation workflow.',
            'activation_note' => 'Confirm the old branch activation handoff before rollout.',
            'rollout_note' => 'Keep rollout review-only during Gold parity checks.',
        ]);

        $partner = CardType::create([
            'name' => 'Partner',
            'slug' => 'partner',
            'points_rate' => '1.20',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('href="/admin/card-types?cardType='.$gold->id.'#live-form"', false)
            ->assertSee('href="/admin/card-types?cardType='.$partner->id.'#live-form"', false)
            ->assertSee('<form method="POST" action="/admin/card-types/'.$gold->id.'/toggle-status"', false)
            ->assertSee('<form method="POST" action="/admin/card-types/'.$partner->id.'/toggle-status"', false)
            ->assertSee('Keep rollout review-only during Gold parity checks.')
            ->assertSee('No rollout note saved yet')
            ->assertSee('Active in Galaxy foundation flow')
            ->assertSee('Draft in Galaxy foundation flow')
            ->assertDontSee('>active<', false)
            ->assertDontSee('>draft<', false);
    }

    public function test_authenticated_user_can_toggle_card_type_status_from_row_level_action(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Partner',
            'slug' => 'galaxy-partner',
            'points_rate' => '1.20',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.card-types.toggle-status', $cardType));

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Partner" is now active.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'is_active' => true,
        ]);
    }

    public function test_card_type_toggle_status_surfaces_selected_record_success_cue_after_redirect(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->followingRedirects()->actingAs($user)
            ->patch(route('admin.card-types.toggle-status', $cardType));

        $response
            ->assertOk()
            ->assertSee('Backend flow checkpoint')
            ->assertSee('Card type "Galaxy Prime" is now draft.')
            ->assertSee('Selected record summary')
            ->assertSee('Latest flow result:')
            ->assertSee('Card type "Galaxy Prime" is now draft.')
            ->assertSee('Recent activity preview')
            ->assertSee('Latest backend write result')
            ->assertSee('Card type "Galaxy Prime" is now draft.')
            ->assertSee('Import rules')
            ->assertSee('Blocked until draft parity review has visible card coverage to compare against.')
            ->assertSee('Status guidance:')
            ->assertSee('This tier is still in draft, which keeps it safe for parity checks before operators treat it as live loyalty behavior.')
            ->assertSee('Rule-import blocker:')
            ->assertSee('Rule import is still blocked, but draft state keeps this tier safe for parity-first catalog and accrual checks.')
            ->assertSee('Publish guidance:')
            ->assertSee('Keep this tier in draft until rule import expectations and old Galaxy behavior are mapped clearly enough to publish safely.')
            ->assertSee('Readiness signal:')
            ->assertSee('Not ready to publish: draft mode is still the holding state for parity validation and rule-import review.')
            ->assertSee('Current status posture:')
            ->assertSee('Draft tiers are the safe place for parity-first validation and copy changes')
            ->assertSee('Rule-import posture:')
            ->assertSee('Imports can be reviewed in draft mode, but they are still not safe to enable yet')
            ->assertSee('Publish posture:')
            ->assertSee('Draft tiers should stay unpublished until legacy behavior is mapped more explicitly')
            ->assertSee('Action gating:')
            ->assertSee('Allow draft-safe edits and validation only, keep live-facing actions gated')
            ->assertSee('Latest flow result:')
            ->assertSee('Card type "Galaxy Prime" is now draft.');
    }

    public function test_card_types_page_replaces_preview_metrics_with_model_backed_counts(): void
    {
        CardType::create([
            'name' => 'Gold',
            'slug' => 'gold',
            'points_rate' => '1.50',
            'is_active' => true,
            'review_note' => 'Keep Gold aligned with the legacy auto-activation workflow.',
            'activation_note' => 'Confirm the old branch activation handoff before rollout.',
            'rollout_note' => 'Keep rollout review-only during Gold parity checks.',
        ]);

        CardType::create([
            'name' => 'Silver',
            'slug' => 'silver',
            'points_rate' => '1.00',
            'is_active' => true,
        ]);

        CardType::create([
            'name' => 'Partner',
            'slug' => 'partner',
            'points_rate' => '1.20',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('Active Galaxy tiers')
            ->assertSee('Draft Galaxy tiers')
            ->assertSee('Reviewed Galaxy tiers')
            ->assertSee('Activation notes')
            ->assertSee('Rollout notes')
            ->assertSee('Saved types')
            ->assertDontSee('Imported rules')
            ->assertSee('>2<', false)
            ->assertSee('>1<', false)
            ->assertSee('>1<', false)
            ->assertSee('>3<', false);
    }

    public function test_shop_scoped_admin_cannot_create_new_card_type(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Tier Creator Branch',
            'code' => 'galaxy-scoped-tier-creator-branch',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Tier Creator Operator',
            'slug' => 'scoped-tier-creator-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Create scoped card types',
            'slug' => 'create-scoped-card-types',
            'review_note' => 'Phase 1 scoped card type create coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->from(route('admin.card-types.index'))->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => 'Scoped Unauthorized Tier',
            'slug' => 'scoped-unauthorized-tier',
            'points_rate' => '1.25',
            'is_active' => '1',
            'rollout_note' => 'Scoped admins should not create new card types during Phase 1.',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'Only bootstrap admins can create new card types while the Galaxy tier foundation is still centrally controlled.',
            ]);

        $this->assertDatabaseMissing('card_types', [
            'slug' => 'scoped-unauthorized-tier',
        ]);
    }

    public function test_authenticated_user_can_store_card_type_from_live_admin_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => '  Galaxy Prime  ',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.75',
            'is_active' => '1',
            'rollout_note' => 'Keep rollout review-only until legacy tier behavior is verified branch by branch.',
        ]);

        $cardType = CardType::query()->where('slug', 'galaxy-prime')->firstOrFail();

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Prime" was created.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.75',
            'is_active' => true,
            'rollout_note' => 'Keep rollout review-only until legacy tier behavior is verified branch by branch.',
        ]);
    }

    public function test_card_types_page_shows_live_flow_success_flash_message(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime-created-flash',
            'points_rate' => '1.75',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['status' => 'Card type "Galaxy Prime" was created.'])
            ->get(route('admin.card-types.index', ['cardType' => $cardType]));

        $response
            ->assertOk()
            ->assertSee('id="backend-flow-status"', false)
            ->assertSee('tabindex="-1"', false)
            ->assertSee('role="status"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('Backend flow checkpoint')
            ->assertSee('Tier changes are now visible in the Galaxy foundation-backed workspace.')
            ->assertSee('Card type "Galaxy Prime" was created.')
            ->assertSee('Selected tier:')
            ->assertSee('Galaxy Prime')
            ->assertSee('Edit Galaxy tier in Galaxy foundation')
            ->assertSee('action="/admin/card-types/'.$cardType->id.'"', false);
    }

    public function test_cards_page_shows_resource_specific_live_flow_success_flash_message(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Flash Branch',
            'code' => 'galaxy-flash-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Flash Gold',
            'slug' => 'galaxy-flash-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-FLASH-1001',
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['status' => 'Card "GX-FLASH-1001" was updated.'])
            ->get(route('admin.cards.index', ['card' => $card]));

        $response
            ->assertOk()
            ->assertSee('id="backend-flow-status"', false)
            ->assertSee('Backend flow checkpoint')
            ->assertSee('Inventory changes are now visible in the Galaxy foundation-backed workspace.')
            ->assertSee('Card "GX-FLASH-1001" was updated.')
            ->assertSee('Reviewing: GX-FLASH-1001');
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
            ->assertSee('Back to tier catalog');
    }

    public function test_card_types_page_resolves_live_form_action_route_parameters(): void
    {
        $this->registerAdminPreviewRoute(
            '/card-types/{cardType}/draft-preview',
            fn (string $cardType) => $cardType,
            'card-types.draft-preview',
        );

        Config::set('admin-pages.card-types.liveForm.actionRoute', 'admin.card-types.draft-preview');
        Config::set('admin-pages.card-types.liveForm.actionRouteParameters', [
            'cardType' => new AdminCardTypePreviewRoutable('gold'),
            'ignored' => ['bad'],
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelRoute', 'admin.card-types.draft-preview');
        Config::set('admin-pages.card-types.liveForm.cancelRouteParameters', [
            'cardType' => new AdminCardTypePreviewStringable('silver'),
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

    public function test_card_types_page_prefers_routable_route_keys_over_stringable_values(): void
    {
        $this->registerAdminPreviewRoute(
            '/card-types/{cardType}/draft-preview',
            fn (string $cardType) => $cardType,
            'card-types.draft-preview',
        );

        Config::set('admin-pages.card-types.liveForm.actionRoute', 'admin.card-types.draft-preview');
        Config::set('admin-pages.card-types.liveForm.actionRouteParameters', [
            'cardType' => new AdminCardTypePreviewMixedRouteValue('gold', 'string-only-gold'),
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelRoute', 'admin.card-types.draft-preview');
        Config::set('admin-pages.card-types.liveForm.cancelRouteParameters', [
            'cardType' => new AdminCardTypePreviewMixedRouteValue('silver', 'string-only-silver'),
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelLabel', 'Return to draft preview');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('action="/admin/card-types/gold/draft-preview"', false)
            ->assertSee('href="/admin/card-types/silver/draft-preview"', false)
            ->assertDontSee('/admin/card-types/string-only-gold/draft-preview', false)
            ->assertDontSee('/admin/card-types/string-only-silver/draft-preview', false)
            ->assertSee('Return to draft preview');
    }

    public function test_card_types_page_resolves_unit_enum_route_parameters_by_name(): void
    {
        $this->registerAdminPreviewRoute(
            '/card-types/{cardType}/draft-preview',
            fn (string $cardType) => $cardType,
            'card-types.draft-preview',
        );

        Config::set('admin-pages.card-types.liveForm.actionRoute', 'admin.card-types.draft-preview');
        Config::set('admin-pages.card-types.liveForm.actionRouteParameters', [
            'cardType' => AdminCardTypePreviewMode::Gold,
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelRoute', 'admin.card-types.draft-preview');
        Config::set('admin-pages.card-types.liveForm.cancelRouteParameters', [
            'cardType' => AdminCardTypePreviewMode::Silver,
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelLabel', 'Return to draft preview');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('action="/admin/card-types/Gold/draft-preview"', false)
            ->assertSee('href="/admin/card-types/Silver/draft-preview"', false)
            ->assertSee('Return to draft preview');
    }

    public function test_card_types_page_resolves_boolean_route_parameters(): void
    {
        $this->registerAdminPreviewRoute(
            '/card-types/{cardType}/toggle/{enabled}',
            fn (string $cardType, string $enabled) => $cardType.'-'.$enabled,
            'card-types.toggle-preview',
        );

        Config::set('admin-pages.card-types.liveForm.actionRoute', 'admin.card-types.toggle-preview');
        Config::set('admin-pages.card-types.liveForm.actionRouteParameters', [
            'cardType' => 'gold',
            'enabled' => true,
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelRoute', 'admin.card-types.toggle-preview');
        Config::set('admin-pages.card-types.liveForm.cancelRouteParameters', [
            'cardType' => 'silver',
            'enabled' => false,
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelLabel', 'Return to toggle preview');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('action="/admin/card-types/gold/toggle/1"', false)
            ->assertSee('href="/admin/card-types/silver/toggle/0"', false)
            ->assertSee('Return to toggle preview');
    }

    public function test_card_types_page_resolves_live_form_values_from_config_callback(): void
    {
        Config::set('admin-pages.card-types.liveForm.valuesResolver', function (string $resource, array $page, array $liveForm): array {
            $this->assertSame('card-types', $resource);
            $this->assertSame('Create Galaxy tier in Galaxy foundation', $liveForm['title']);
            $this->assertSame('Card Types', $page['pageTitle']);

            return [
                'name' => 'Galaxy Prime',
                'slug' => 'galaxy-prime',
                'points_rate' => 2.25,
                'is_active' => false,
            ];
        });

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('value="Galaxy Prime"', false)
            ->assertSee('value="galaxy-prime"', false)
            ->assertSee('value="2.25"', false)
            ->assertSee('<option value="0" selected>Draft</option>', false);
    }

    public function test_card_types_page_resolves_route_parameters_from_config_callback(): void
    {
        $this->registerAdminPreviewRoute(
            '/card-types/{cardType}/preview/{mode}',
            fn (string $cardType, string $mode) => $cardType.'-'.$mode,
            'card-types.context-preview',
        );

        Config::set('admin-pages.card-types.liveForm.actionRoute', 'admin.card-types.context-preview');
        Config::set('admin-pages.card-types.liveForm.actionRouteParameters', function (string $resource, array $page, array $liveForm): array {
            $this->assertSame('card-types', $resource);
            $this->assertSame('Card Types', $page['pageTitle']);
            $this->assertSame('Create Galaxy tier in Galaxy foundation', $liveForm['title']);

            return [
                'cardType' => new AdminCardTypePreviewRoutable('gold'),
                'mode' => AdminCardTypePreviewMode::Silver,
            ];
        });
        Config::set('admin-pages.card-types.liveForm.cancelRoute', 'admin.card-types.context-preview');
        Config::set('admin-pages.card-types.liveForm.cancelRouteParameters', function (): array {
            return [
                'cardType' => new AdminCardTypePreviewStringable('silver'),
                'mode' => true,
            ];
        });
        Config::set('admin-pages.card-types.liveForm.cancelLabel', 'Return to context preview');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('action="/admin/card-types/gold/preview/Silver"', false)
            ->assertSee('href="/admin/card-types/silver/preview/1"', false)
            ->assertSee('Return to context preview');
    }

    public function test_card_types_page_resolves_live_form_mode_copy_from_config_callbacks(): void
    {
        Config::set('admin-pages.card-types.liveForm.title', fn () => 'Edit card type');
        Config::set('admin-pages.card-types.liveForm.description', fn () => 'Update the selected Galaxy tier without leaving the shared live form.');
        Config::set('admin-pages.card-types.liveForm.submitLabel', fn () => 'Save card type changes');
        Config::set('admin-pages.card-types.liveForm.method', fn () => 'PATCH');
        Config::set('admin-pages.card-types.liveForm.actionRoute', 'admin.card-types.update');
        Config::set('admin-pages.card-types.liveForm.actionRouteParameters', fn () => [
            'cardType' => 7,
        ]);
        Config::set('admin-pages.card-types.liveForm.cancelRoute', 'admin.card-types.index');
        Config::set('admin-pages.card-types.liveForm.cancelLabel', 'Back to tier catalog');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('Edit card type')
            ->assertSee('Update the selected Galaxy tier without leaving the shared live form.')
            ->assertSee('>Save card type changes<', false)
            ->assertSee('action="/admin/card-types/7"', false)
            ->assertSee('name="_method"', false)
            ->assertSee('value="PATCH"', false);
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
        Config::set('admin-pages.card-types.liveForm.cancelLabel', 'Back to tier catalog');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('method="POST"', false)
            ->assertSee('action="/admin/card-types/'.$cardType->id.'"', false)
            ->assertSee('name="_method"', false)
            ->assertSee('value="PATCH"', false)
            ->assertSee('href="/admin/card-types"', false)
            ->assertSee('Back to tier catalog');
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

    public function test_card_types_page_renders_submit_attributes(): void
    {
        Config::set('admin-pages.card-types.liveForm.submitAttributes', [
            'data-mode' => 'update',
            'disabled' => true,
            'aria-controls' => 'backend-flow-status',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('button type="submit" class="button button-primary"', false)
            ->assertSee('data-mode="update"', false)
            ->assertSee('disabled', false)
            ->assertSee('aria-controls="backend-flow-status"', false)
            ->assertDontSee('disabled="1"', false);
    }

    public function test_card_types_page_renders_form_attributes(): void
    {
        Config::set('admin-pages.card-types.liveForm.formAttributes', [
            'data-form-mode' => 'update',
            'novalidate' => true,
            'aria-controls' => 'backend-flow-status',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('<form method="POST" action="/admin/card-types"', false)
            ->assertSee('data-form-mode="update"', false)
            ->assertSee('novalidate', false)
            ->assertSee('aria-controls="backend-flow-status"', false)
            ->assertDontSee('novalidate="1"', false);
    }

    public function test_card_types_edit_page_keeps_normalized_optional_live_form_attributes_when_config_omits_them(): void
    {
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        Config::set('admin-pages.card-types.liveForm.formAttributes', null);
        Config::set('admin-pages.card-types.liveForm.submitAttributes', null);
        Config::set('admin-pages.card-types.liveForm.cancelAttributes', null);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index', ['cardType' => $cardType->id]));

        $response
            ->assertOk()
            ->assertSee('<form method="POST" action="'.route('admin.card-types.update', $cardType, false).'"', false)
            ->assertSee('Save card type changes')
            ->assertSee('href="'.route('admin.card-types.index', absolute: false).'" class="button button-secondary"', false);
    }

    public function test_card_types_page_renders_cancel_attributes(): void
    {
        Config::set('admin-pages.card-types.liveForm.cancelAttributes', [
            'data-cancel-mode' => 'update',
            'download' => true,
            'aria-controls' => 'live-form',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('href="/admin/card-types" class="button button-secondary"', false)
            ->assertSee('data-cancel-mode="update"', false)
            ->assertSee('download', false)
            ->assertSee('aria-controls="live-form"', false)
            ->assertDontSee('download="1"', false);
    }

    public function test_card_types_page_renders_field_wrapper_attributes(): void
    {
        Config::set('admin-pages.card-types.liveForm.fields', [
            ...array_map(function (array $field): array {
                if ($field['name'] !== 'name') {
                    return $field;
                }

                $field['wrapperAttributes'] = [
                    'data-field-mode' => 'edit',
                    'hidden' => true,
                    'aria-hidden' => 'true',
                ];

                return $field;
            }, Config::get('admin-pages.card-types.liveForm.fields', [])),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index'));

        $response
            ->assertOk()
            ->assertSee('<label for="live-form-name" data-field-mode="edit" hidden aria-hidden="true"', false)
            ->assertDontSee('hidden="1"', false);
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

        $response = $this->followingRedirects()->from(route('admin.card-types.index'))->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => '',
            'slug' => 'invalid slug',
            'points_rate' => '-1',
            'is_active' => 'not-a-boolean',
        ]);

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
            'review_note' => 'Keep this tier aligned with the legacy accrual workflow before widening imports.',
            'activation_note' => 'Confirm branch activation handoff before operators rely on this live tier.',
        ]);

        $cardType = CardType::query()->where('slug', 'galaxy-prime-plus')->firstOrFail();

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Prime Plus" was created.');

        $this->assertDatabaseHas('card_types', [
            'name' => 'Galaxy Prime Plus',
            'slug' => 'galaxy-prime-plus',
            'points_rate' => '2.50',
            'is_active' => true,
            'review_note' => 'Keep this tier aligned with the legacy accrual workflow before widening imports.',
            'activation_note' => 'Confirm branch activation handoff before operators rely on this live tier.',
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

        $longReviewNote = str_repeat('c', 1001);

        $reviewNoteResponse = $this->from(route('admin.card-types.index'))->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => 'Galaxy Prime Review Copy',
            'slug' => 'galaxy-prime-review-copy',
            'points_rate' => '1.50',
            'is_active' => '1',
            'review_note' => $longReviewNote,
        ]);

        $reviewNoteResponse
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors([
                'review_note' => 'Keep the review note under 1000 characters so the tier workspace stays operator-friendly.',
            ]);

        $longActivationNote = str_repeat('a', 1001);

        $activationNoteResponse = $this->from(route('admin.card-types.index'))->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => 'Galaxy Prime Activation Copy',
            'slug' => 'galaxy-prime-activation-copy',
            'points_rate' => '1.50',
            'is_active' => '1',
            'activation_note' => $longActivationNote,
        ]);

        $activationNoteResponse
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors([
                'activation_note' => 'Keep the activation note under 1000 characters so the tier workspace stays operator-friendly.',
            ]);

        $longRolloutNote = str_repeat('r', 1001);

        $rolloutNoteResponse = $this->from(route('admin.card-types.index'))->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => 'Galaxy Prime Rollout Copy',
            'slug' => 'galaxy-prime-rollout-copy',
            'points_rate' => '1.50',
            'is_active' => '1',
            'rollout_note' => $longRolloutNote,
        ]);

        $rolloutNoteResponse
            ->assertRedirect(route('admin.card-types.index').'#live-form')
            ->assertSessionHasErrors([
                'rollout_note' => 'Keep the rollout note under 1000 characters so the tier workspace stays operator-friendly.',
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

    public function test_shop_scoped_admin_cannot_update_card_type(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Tier Update Branch',
            'code' => 'galaxy-scoped-tier-update-branch',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Tier Update Operator',
            'slug' => 'scoped-tier-update-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Update scoped card types',
            'slug' => 'update-scoped-card-types',
            'review_note' => 'Phase 1 scoped card type update coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $cardType = CardType::create([
            'name' => 'Galaxy Protected Tier',
            'slug' => 'galaxy-protected-tier-update',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from(route('admin.card-types.index', ['cardType' => $cardType], absolute: false))->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => 'Scoped Updated Tier',
            'slug' => 'scoped-updated-tier',
            'points_rate' => '1.25',
            'is_active' => '0',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'Only bootstrap admins can update card types while the Galaxy tier foundation is still centrally controlled.',
            ]);

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'name' => 'Galaxy Protected Tier',
            'slug' => 'galaxy-protected-tier-update',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
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
            'review_note' => 'Document the first Galaxy foundation tier adjustments before widening rule imports.',
            'activation_note' => 'Keep activation handoff visible while this tier stays in draft review.',
            'rollout_note' => 'Keep rollout review-only while this tier stays in draft review.',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Prime Plus" was updated.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'name' => 'Galaxy Prime Plus',
            'slug' => 'galaxy-prime-plus',
            'points_rate' => '2.25',
            'is_active' => false,
            'review_note' => 'Document the first Galaxy foundation tier adjustments before widening rule imports.',
            'activation_note' => 'Keep activation handoff visible while this tier stays in draft review.',
            'rollout_note' => 'Keep rollout review-only while this tier stays in draft review.',
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
            'name' => '  Galaxy Prime Updated  ',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.75',
            'is_active' => '1',
        ]);

        $okResponse
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Prime Updated" was updated.');

        $errorResponse = $this->from(route('admin.card-types.index'))->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => 'Galaxy Prime Updated Again',
            'slug' => $otherCardType->slug,
            'points_rate' => '1.90',
            'is_active' => '1',
        ]);

        $this->assertSame(302, $errorResponse->getStatusCode());
        $this->assertSame(route('admin.card-types.index', ['cardType' => $cardType]).'#live-form', $errorResponse->headers->get('Location'));
    }

    public function test_card_type_live_flow_trims_tier_identity_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => '  Galaxy Silver  ',
            'slug' => 'Galaxy Silver',
            'points_rate' => '1.25',
            'is_active' => '0',
            'review_note' => 'Trim the first live tier shell before widening rule import work.',
        ]);

        $cardType = CardType::query()->where('slug', 'galaxy-silver')->firstOrFail();

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Silver" was created.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'name' => 'Galaxy Silver',
            'slug' => 'galaxy-silver',
            'points_rate' => '1.25',
            'is_active' => false,
        ]);
    }

    public function test_card_type_live_flow_normalizes_blank_notes_to_null(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => 'Galaxy Bronze',
            'slug' => 'Galaxy Bronze',
            'points_rate' => '0.75',
            'is_active' => '0',
            'review_note' => '   ',
            'activation_note' => '   ',
            'rollout_note' => '   ',
        ]);

        $cardType = CardType::query()->where('slug', 'galaxy-bronze')->firstOrFail();

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Bronze" was created.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'review_note' => null,
            'activation_note' => null,
            'rollout_note' => null,
        ]);
    }

    public function test_card_type_update_live_flow_normalizes_blank_notes_to_null(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Bronze Plus',
            'slug' => 'galaxy-bronze-plus',
            'points_rate' => '0.90',
            'is_active' => true,
            'review_note' => 'Clear this tier note while keeping the shell live.',
            'activation_note' => 'Clear this activation note after parity review.',
            'rollout_note' => 'Clear this rollout note after draft verification.',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => 'Galaxy Bronze Plus',
            'slug' => 'Galaxy Bronze Plus',
            'points_rate' => '0.90',
            'is_active' => '1',
            'review_note' => '   ',
            'activation_note' => '   ',
            'rollout_note' => '   ',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Bronze Plus" was updated.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'slug' => 'galaxy-bronze-plus',
            'review_note' => null,
            'activation_note' => null,
            'rollout_note' => null,
        ]);
    }

    public function test_card_type_update_live_flow_rejects_duplicate_normalized_slug(): void
    {
        $user = User::factory()->create();

        $existingCardType = CardType::create([
            'name' => 'Existing Tier Reviewer',
            'slug' => 'existing-tier-reviewer',
            'points_rate' => '1.25',
            'is_active' => true,
        ]);

        $cardTypeToUpdate = CardType::create([
            'name' => 'Target Tier Reviewer',
            'slug' => 'target-tier-reviewer',
            'points_rate' => '0.95',
            'is_active' => false,
        ]);

        $response = $this->from(route('admin.card-types.index', ['cardType' => $cardTypeToUpdate], absolute: false))
            ->actingAs($user)
            ->patch(route('admin.card-types.update', $cardTypeToUpdate), [
                'name' => '  Target Tier Reviewer  ',
                'slug' => ' Existing Tier Reviewer ',
                'points_rate' => '1.05',
                'is_active' => '1',
                'review_note' => 'Duplicate normalized tier slug should stay blocked during live edit work.',
            ]);

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardTypeToUpdate], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'This card type slug is already in use.',
            ]);

        $this->assertDatabaseHas('card_types', [
            'id' => $existingCardType->id,
            'slug' => 'existing-tier-reviewer',
        ]);

        $this->assertDatabaseHas('card_types', [
            'id' => $cardTypeToUpdate->id,
            'slug' => 'target-tier-reviewer',
        ]);
    }

    public function test_card_type_update_live_flow_keeps_tier_slug_canonical(): void
    {
        $user = User::factory()->create();

        $cardType = CardType::create([
            'name' => 'Galaxy Draft Tier',
            'slug' => 'galaxy-draft-tier',
            'points_rate' => '0.85',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => '  Galaxy Draft Tier Prime  ',
            'slug' => ' Galaxy Draft Tier Prime ',
            'points_rate' => '1.05',
            'is_active' => '1',
            'review_note' => 'Keep tier slug canonical while the live catalog shell stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Draft Tier Prime" was updated.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'name' => 'Galaxy Draft Tier Prime',
            'slug' => 'galaxy-draft-tier-prime',
            'points_rate' => '1.05',
            'is_active' => true,
        ]);
    }

    public function test_card_type_update_live_flow_keeps_status_boolean_canonical(): void
    {
        $user = User::factory()->create();

        $cardType = CardType::create([
            'name' => 'Galaxy Boolean Tier',
            'slug' => 'galaxy-boolean-tier',
            'points_rate' => '0.95',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => '  Galaxy Boolean Tier Draft  ',
            'slug' => ' Galaxy Boolean Tier Draft ',
            'points_rate' => '0.85',
            'is_active' => 'no',
            'review_note' => 'Keep tier status canonical while the live catalog shell stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Boolean Tier Draft" was updated.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'name' => 'Galaxy Boolean Tier Draft',
            'slug' => 'galaxy-boolean-tier-draft',
            'points_rate' => '0.85',
            'is_active' => false,
        ]);
    }

    public function test_card_type_live_flow_keeps_status_boolean_canonical(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.card-types.store'), [
            'name' => '  Galaxy Boolean Tier Create  ',
            'slug' => ' Galaxy Boolean Tier Create ',
            'points_rate' => '0.85',
            'is_active' => 'no',
            'review_note' => 'Keep tier status canonical while the first live catalog shell stays narrow.',
        ]);

        $cardType = CardType::query()->where('slug', 'galaxy-boolean-tier-create')->firstOrFail();

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
            ->assertSessionHas('status', 'Card type "Galaxy Boolean Tier Create" was created.');

        $this->assertDatabaseHas('card_types', [
            'id' => $cardType->id,
            'name' => 'Galaxy Boolean Tier Create',
            'slug' => 'galaxy-boolean-tier-create',
            'points_rate' => '0.85',
            'is_active' => false,
        ]);
    }

    public function test_shop_scoped_admin_cannot_update_card_into_a_different_shop(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Update Assigned Shop',
            'code' => 'galaxy-scoped-update-assigned-shop',
            'is_active' => true,
        ]);
        $otherShop = Shop::create([
            'name' => 'Galaxy Scoped Update Foreign Shop',
            'code' => 'galaxy-scoped-update-foreign-shop',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Scoped Update Card Type',
            'slug' => 'galaxy-scoped-update-card-type',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $assignedShop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-SCOPED-UPD-1001',
            'status' => 'draft',
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Card Update Operator',
            'slug' => 'scoped-card-update-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Update scoped cards',
            'slug' => 'update-scoped-cards',
            'review_note' => 'Phase 1 scoped card update coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $otherShop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => 'GX-SCOPED-UPD-1001',
            'status' => 'draft',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'shop_id' => 'Choose a shop you can access so the Galaxy inventory shell stays scoped to your assigned branch.',
            ]);

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'shop_id' => $assignedShop->id,
        ]);
    }

    public function test_shop_scoped_admin_cannot_update_foreign_card_even_if_target_shop_is_accessible(): void
    {
        $assignedShop = Shop::create([
            'name' => 'Galaxy Scoped Assigned Card Guard Shop',
            'code' => 'galaxy-scoped-assigned-card-guard-shop',
            'is_active' => true,
        ]);
        $otherShop = Shop::create([
            'name' => 'Galaxy Scoped Foreign Card Guard Shop',
            'code' => 'galaxy-scoped-foreign-card-guard-shop',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Scoped Card Guard Type',
            'slug' => 'galaxy-scoped-card-guard-type',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $otherShop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-SCOPED-GUARD-1001',
            'status' => 'draft',
        ]);

        $user = User::factory()->create([
            'shop_id' => $assignedShop->id,
        ]);
        $role = Role::create([
            'name' => 'Scoped Card Guard Operator',
            'slug' => 'scoped-card-guard-operator',
            'is_active' => true,
        ]);
        $permission = Permission::create([
            'name' => 'Guard scoped cards',
            'slug' => 'guard-scoped-cards',
            'review_note' => 'Phase 1 foreign card update guard coverage.',
        ]);
        $role->permissions()->attach($permission->id);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $assignedShop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => 'GX-SCOPED-GUARD-1001',
            'status' => 'draft',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'shop_id' => 'Choose a card from a shop you can access before changing inventory details in the Galaxy workspace.',
            ]);

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'shop_id' => $otherShop->id,
        ]);
    }

    public function test_card_live_flow_normalizes_blank_review_note_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Null Note Branch',
            'code' => 'galaxy-null-note-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Null Note Gold',
            'slug' => 'galaxy-null-note-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => 'GX-NULL-1001',
            'status' => 'draft',
            'review_note' => '   ',
        ]);

        $card = Card::query()->where('number', 'GX-NULL-1001')->firstOrFail();

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status');

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'review_note' => null,
        ]);
    }

    public function test_card_update_live_flow_normalizes_blank_review_note_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Null Note Update Branch',
            'code' => 'galaxy-null-note-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Null Note Update Gold',
            'slug' => 'galaxy-null-note-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-NULL-UPD-1001',
            'status' => 'active',
            'review_note' => 'Clear this note while keeping the inventory shell live.',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-null-upd-1001 ',
            'status' => 'active',
            'activated_at' => '',
            'review_note' => '   ',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Card "GX-NULL-UPD-1001" was updated.');

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'number' => 'GX-NULL-UPD-1001',
            'review_note' => null,
        ]);
    }

    public function test_card_update_live_flow_normalizes_blank_activation_timestamp_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Activation Cleanup Branch',
            'code' => 'galaxy-activation-cleanup-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Activation Cleanup Gold',
            'slug' => 'galaxy-activation-cleanup-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-ACT-UPD-1001',
            'status' => 'active',
            'activated_at' => '2026-05-05 11:30:00',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-act-upd-1001 ',
            'status' => 'active',
            'activated_at' => '   ',
            'review_note' => 'Keep inventory cleanup narrow while activation timing is reset.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Card "GX-ACT-UPD-1001" was updated.');

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'number' => 'GX-ACT-UPD-1001',
            'activated_at' => null,
        ]);
    }

    public function test_card_update_live_flow_normalizes_blank_issue_timestamp_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Issue Cleanup Branch',
            'code' => 'galaxy-issue-cleanup-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Issue Cleanup Gold',
            'slug' => 'galaxy-issue-cleanup-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-ISS-UPD-1001',
            'status' => 'active',
            'issued_at' => '2026-05-05 09:30:00',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-iss-upd-1001 ',
            'status' => 'active',
            'issued_at' => '   ',
            'activated_at' => '',
            'review_note' => 'Keep inventory cleanup narrow while issue timing is reset.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Card "GX-ISS-UPD-1001" was updated.');

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'number' => 'GX-ISS-UPD-1001',
            'issued_at' => null,
        ]);
    }

    public function test_card_live_flow_normalizes_blank_activation_timestamp_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Activation Null Branch',
            'code' => 'galaxy-activation-null-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Activation Null Gold',
            'slug' => 'galaxy-activation-null-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-act-null-1001 ',
            'status' => 'active',
            'activated_at' => '   ',
            'review_note' => 'Keep inventory activation cleanup narrow while the first shell stays live.',
        ]);

        $card = Card::query()->where('number', 'GX-ACT-NULL-1001')->firstOrFail();

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Card "GX-ACT-NULL-1001" was created.');

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'number' => 'GX-ACT-NULL-1001',
            'activated_at' => null,
        ]);
    }

    public function test_card_live_flow_normalizes_blank_issue_timestamp_to_null(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Issue Null Branch',
            'code' => 'galaxy-issue-null-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Issue Null Gold',
            'slug' => 'galaxy-issue-null-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-iss-null-1001 ',
            'status' => 'active',
            'issued_at' => '   ',
            'activated_at' => '',
            'review_note' => 'Keep inventory issue cleanup narrow while the first shell stays live.',
        ]);

        $card = Card::query()->where('number', 'GX-ISS-NULL-1001')->firstOrFail();

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Card "GX-ISS-NULL-1001" was created.');

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'number' => 'GX-ISS-NULL-1001',
            'issued_at' => null,
        ]);
    }

    public function test_card_update_live_flow_keeps_inventory_identifier_canonical(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Canonical Inventory Branch',
            'code' => 'galaxy-canonical-inventory-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Canonical Gold',
            'slug' => 'galaxy-canonical-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-CANON-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-canon-1001a ',
            'status' => 'blocked',
            'activated_at' => '',
            'review_note' => 'Keep the inventory identifier canonical while blocked review stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Card "GX-CANON-1001A" was updated.');

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'number' => 'GX-CANON-1001A',
            'status' => 'blocked',
        ]);
    }

    public function test_card_update_live_flow_keeps_status_canonical(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Canonical Status Branch',
            'code' => 'galaxy-canonical-status-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Canonical Status Gold',
            'slug' => 'galaxy-canonical-status-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        $card = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-STATUS-1001',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.cards.update', $card), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-status-1001 ',
            'status' => ' BLOCKED ',
            'activated_at' => '',
            'review_note' => 'Keep the inventory status canonical while the live shell stays narrow.',
        ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Card "GX-STATUS-1001" was updated.');

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'number' => 'GX-STATUS-1001',
            'status' => 'blocked',
        ]);
    }

    public function test_card_live_flow_keeps_status_canonical(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Create Status Branch',
            'code' => 'galaxy-create-status-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Create Status Gold',
            'slug' => 'galaxy-create-status-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.cards.store'), [
            'shop_id' => (string) $shop->id,
            'card_type_id' => (string) $cardType->id,
            'number' => ' gx-create-status-1001 ',
            'status' => ' BLOCKED ',
            'activated_at' => '',
            'review_note' => 'Keep the inventory status canonical while the first live shell stays narrow.',
        ]);

        $card = Card::query()->where('number', 'GX-CREATE-STATUS-1001')->firstOrFail();

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $card], absolute: false).'#backend-flow-status')
            ->assertSessionHas('status', 'Card "GX-CREATE-STATUS-1001" was created.');

        $this->assertDatabaseHas('cards', [
            'id' => $card->id,
            'number' => 'GX-CREATE-STATUS-1001',
            'status' => 'blocked',
        ]);
    }

    public function test_card_update_live_flow_rejects_duplicate_inventory_identifier_after_normalization(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'name' => 'Galaxy Duplicate Update Branch',
            'code' => 'galaxy-duplicate-update-branch',
            'is_active' => true,
        ]);
        $cardType = CardType::create([
            'name' => 'Galaxy Duplicate Update Gold',
            'slug' => 'galaxy-duplicate-update-gold',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $existingCard = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-DUP-UPD-1001',
            'status' => 'active',
        ]);

        $cardToUpdate = Card::create([
            'shop_id' => $shop->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-DUP-UPD-2002',
            'status' => 'draft',
        ]);

        $response = $this->from(route('admin.cards.index', ['card' => $cardToUpdate], absolute: false))
            ->actingAs($user)
            ->patch(route('admin.cards.update', $cardToUpdate), [
                'shop_id' => (string) $shop->id,
                'card_type_id' => (string) $cardType->id,
                'number' => ' gx-dup-upd-1001 ',
                'status' => 'blocked',
                'activated_at' => '',
                'review_note' => 'This duplicate should stay blocked after identifier normalization.',
            ]);

        $response
            ->assertRedirect(route('admin.cards.index', ['card' => $cardToUpdate], absolute: false).'#live-form')
            ->assertSessionHasErrors([
                'number' => 'This card number is already in use in the Galaxy foundation inventory shell.',
            ]);

        $this->assertDatabaseHas('cards', [
            'id' => $existingCard->id,
            'number' => 'GX-DUP-UPD-1001',
        ]);

        $this->assertDatabaseHas('cards', [
            'id' => $cardToUpdate->id,
            'number' => 'GX-DUP-UPD-2002',
        ]);
    }

    public function test_card_types_page_shows_update_success_flash_message(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime Plus',
            'slug' => 'galaxy-prime-plus-flash',
            'points_rate' => '2.25',
            'is_active' => false,
            'review_note' => 'Document the first Galaxy foundation tier adjustments before widening rule imports.',
            'activation_note' => 'Keep activation handoff visible while this tier stays in draft review.',
            'rollout_note' => 'Keep rollout review-only while this tier stays in draft review.',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['status' => 'Card type "Galaxy Prime Plus" was updated.'])
            ->get(route('admin.card-types.index', ['cardType' => $cardType]));

        $response
            ->assertOk()
            ->assertSee('id="backend-flow-status"', false)
            ->assertSee('tabindex="-1"', false)
            ->assertSee('role="status"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('Backend flow checkpoint')
            ->assertSee('Card type "Galaxy Prime Plus" was updated.')
            ->assertSee('Selected tier:')
            ->assertSee('Galaxy Prime Plus')
            ->assertSee('Review note:')
            ->assertSee('Document the first Galaxy foundation tier adjustments before widening rule imports.')
            ->assertSee('Activation note:')
            ->assertSee('Keep activation handoff visible while this tier stays in draft review.')
            ->assertSee('Rollout note:')
            ->assertSee('Keep rollout review-only while this tier stays in draft review.')
            ->assertSee('Edit Galaxy tier in Galaxy foundation')
            ->assertSee('action="/admin/card-types/'.$cardType->id.'"', false);
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

        $response = $this->from(route('admin.card-types.index', ['cardType' => $cardType]))->actingAs($user)->patch(route('admin.card-types.update', $cardType), [
            'name' => '',
            'slug' => 'invalid slug',
            'points_rate' => '-1',
            'is_active' => 'not-a-boolean',
        ]);

        $response
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#live-form')
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
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#live-form')
            ->assertSessionHasErrors([
                'slug' => 'This card type slug is already in use.',
                'is_active' => 'The status field must be Active or Draft.',
            ]);
    }

    public function test_card_type_update_validation_redirects_to_selected_index_without_referrer(): void
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
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#live-form')
            ->assertSessionHasErrors(['name', 'points_rate', 'is_active']);
    }

    public function test_card_type_update_validation_keeps_selected_edit_context_after_redirect(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime-context',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from(route('admin.card-types.index', ['cardType' => $cardType], absolute: false))
            ->actingAs($user)
            ->followingRedirects()
            ->patch(route('admin.card-types.update', $cardType), [
                'name' => '',
                'slug' => 'invalid slug',
                'points_rate' => '-1',
                'is_active' => 'not-a-boolean',
            ]);

        $response
            ->assertOk()
            ->assertSee('Edit Galaxy tier in Galaxy foundation')
            ->assertSee('Save card type changes')
            ->assertSee('Selected tier:')
            ->assertSee('Galaxy Prime')
            ->assertSee('action="/admin/card-types/'.$cardType->id.'"', false)
            ->assertSee('The card type name field is required.')
            ->assertSee('The points rate field must be at least 0.')
            ->assertSee('The status field must be Active or Draft.');
    }

    public function test_card_type_update_validation_keeps_operator_input_in_selected_edit_mode(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime-old-values',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);
        CardType::create([
            'name' => 'Galaxy Silver',
            'slug' => 'galaxy-silver-duplicate-target',
            'points_rate' => '1.00',
            'is_active' => true,
        ]);

        $response = $this->from(route('admin.card-types.index', ['cardType' => $cardType], absolute: false))
            ->actingAs($user)
            ->followingRedirects()
            ->patch(route('admin.card-types.update', $cardType), [
                'name' => 'Galaxy Prime Attempt',
                'slug' => 'galaxy-silver-duplicate-target',
                'points_rate' => '2.75',
                'is_active' => '0',
            ]);

        $response
            ->assertOk()
            ->assertSee('Edit Galaxy tier in Galaxy foundation')
            ->assertSee('value="Galaxy Prime Attempt"', false)
            ->assertSee('value="galaxy-silver-duplicate-target"', false)
            ->assertSee('value="2.75"', false)
            ->assertSee('<option value="0" selected>Draft</option>', false)
            ->assertDontSee('value="Galaxy Prime"', false)
            ->assertDontSee('value="1.50"', false)
            ->assertSee('This card type slug is already in use.');
    }

    public function test_card_type_update_validation_preserves_error_summary_links_in_selected_edit_mode(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime-error-links',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from(route('admin.card-types.index', ['cardType' => $cardType], absolute: false))
            ->actingAs($user)
            ->followingRedirects()
            ->patch(route('admin.card-types.update', $cardType), [
                'name' => '',
                'slug' => 'invalid slug',
                'points_rate' => '-1',
                'is_active' => 'not-a-boolean',
            ]);

        $response
            ->assertOk()
            ->assertSee('Edit Galaxy tier in Galaxy foundation')
            ->assertSee('Selected tier:')
            ->assertSee('Galaxy Prime')
            ->assertSee('id="live-form-validation-title"', false)
            ->assertSee('aria-labelledby="live-form-validation-title"', false)
            ->assertSee('href="#live-form-name"', false)
            ->assertSee('href="#live-form-points_rate"', false)
            ->assertSee('href="#live-form-is_active"', false)
            ->assertSee('aria-errormessage="live-form-name-error"', false)
            ->assertSee('aria-errormessage="live-form-points_rate-error"', false)
            ->assertSee('aria-errormessage="live-form-is_active-error"', false);
    }

    public function test_card_type_update_validation_keeps_safe_cancel_action_in_selected_edit_mode(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime-cancel-action',
            'points_rate' => '1.50',
            'is_active' => true,
        ]);

        $response = $this->from(route('admin.card-types.index', ['cardType' => $cardType], absolute: false))
            ->actingAs($user)
            ->followingRedirects()
            ->patch(route('admin.card-types.update', $cardType), [
                'name' => '',
                'slug' => 'invalid slug',
                'points_rate' => '-1',
                'is_active' => 'not-a-boolean',
            ]);

        $response
            ->assertOk()
            ->assertSee('Edit Galaxy tier in Galaxy foundation')
            ->assertSee('Create new Galaxy tier shell')
            ->assertSee('href="/admin/card-types"', false)
            ->assertDontSee('href="/admin/card-types?cardType='.$cardType->id.'"', false);
    }

    private function registerAdminPreviewRoute(string $uri, callable $action, string $name): void
    {
        Route::middleware(['web', 'auth', 'can:access-admin'])
            ->get('/admin'.(str_starts_with($uri, '/') ? $uri : '/'.$uri), $action)
            ->name('admin.'.$name);

        app('router')->getRoutes()->refreshNameLookups();
        app('router')->getRoutes()->refreshActionLookups();
    }
}
