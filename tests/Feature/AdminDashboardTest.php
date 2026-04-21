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
            ->assertSee('Galaxy live foundation snapshot')
            ->assertSee('branch setup and review work can move through real operational entities instead of starter placeholders')
            ->assertSee('Live shops')
            ->assertSee('Active shops')
            ->assertSee('Live cardholders')
            ->assertSee('Active cardholders')
            ->assertSee('Live cards')
            ->assertSee('Active cards')
            ->assertSee('Live roles')
            ->assertSee('Live permissions')
            ->assertSee('Live review entry points')
            ->assertSee('Use these Galaxy review surfaces to move from branch setup into live operational checks once records start landing')
            ->assertSee('Review live shops')
            ->assertSee('/admin/shops')
            ->assertSee('Review live cardholders')
            ->assertSee('/admin/cardholders')
            ->assertSee('Review live cards')
            ->assertSee('/admin/cards')
            ->assertSee('Review live card types')
            ->assertSee('/admin/card-types')
            ->assertSee('Review live access roles')
            ->assertSee('/admin/roles-permissions')
            ->assertSee('Review live reporting sources')
            ->assertSee('/admin/reports')
            ->assertSee('Galaxy migration map')
            ->assertSee('These grouped sections mark the Galaxy admin surfaces that still need parity work, so each Phase 1 slice can land against a visible target map')
            ->assertSee('Resume latest live work')
            ->assertSee('Jump back into the latest Galaxy workspace for the branch, cardholder, card, or access item that most recently changed')
            ->assertSee('Open latest shop review: Galaxy Central (active)')
            ->assertSee('/admin/shops?shop=1')
            ->assertSee('Open latest cardholder review: Mariam Dashboard (active)')
            ->assertSee('/admin/cardholders?cardholder=1')
            ->assertSee('Open latest card review: 550011223344 (active)')
            ->assertSee('/admin/cards?card=1')
            ->assertSee('Open latest card type workspace: Dashboard Tier (active)')
            ->assertSee('/admin/card-types?cardType=1')
            ->assertSee('Open latest role review: Dashboard Lead (1 permissions)')
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
        $this->assertTrue($user->canAccessShop($shop));
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
        $this->assertTrue($user->canAccessShop($assignedShop));
        $this->assertFalse($user->canAccessShop($otherShop));
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
        $this->assertFalse($user->canAccessShop($pausedShop));
    }

    public function test_dashboard_shows_live_workspace_fallback_when_no_records_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Resume latest live work')
            ->assertSee('No live records have been created yet. Start in the live review entry points above to open the first Galaxy-backed workspace.')
            ->assertSee('In Phase 1, this usually means the branch is still moving through first-pass setup for shops, cardholders, cards, or access structure.')
            ->assertDontSee('Open latest shop review:')
            ->assertDontSee('Open latest cardholder review:')
            ->assertDontSee('Open latest card review:')
            ->assertDontSee('Open latest card type workspace:')
            ->assertDontSee('Open latest role review:');
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
            ->assertSee('Resume latest live work')
            ->assertSee('Open latest shop review: Partial Dashboard Shop (inactive)')
            ->assertSee('/admin/shops?shop=1')
            ->assertSee('Open latest card type workspace: Partial Dashboard Tier (draft)')
            ->assertSee('/admin/card-types?cardType=1')
            ->assertDontSee('No live records have been created yet. Start in the live review entry points above to open the first Galaxy-backed workspace.')
            ->assertDontSee('In Phase 1, this usually means the branch is still moving through first-pass setup for shops, cardholders, cards, or access structure.')
            ->assertDontSee('Open latest cardholder review:')
            ->assertDontSee('Open latest card review:')
            ->assertDontSee('Open latest role review:');
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
            ->assertSee('Branch code')
            ->assertSee('dashboard-home-shop')
            ->assertSee('Branch posture')
            ->assertSee('active branch, live activity visible')
            ->assertSee('Primary manager')
            ->assertSee('Scoped Dashboard Manager')
            ->assertSee('Laravel status')
            ->assertSee('Visible cardholders')
            ->assertSee('Visible cards')
            ->assertSee('Assigned staff')
            ->assertSee('Latest holder')
            ->assertSee('Scoped Dashboard Holder')
            ->assertSee('Latest holder status')
            ->assertSee('Latest holder added')
            ->assertSee('2026-04-20')
            ->assertSee('Latest card')
            ->assertSee('GX-DASH-001')
            ->assertSee('Latest card status')
            ->assertSee('Latest card issued')
            ->assertSee('Latest activity source')
            ->assertSee('Card issued')
            ->assertSee('Activity freshness')
            ->assertSee('fresh activity')
            ->assertSee('Suggested follow-up')
            ->assertSee('Resume the latest branch review flow from the scoped shortcuts.')
            ->assertSee('>1<', false)
            ->assertSee('Open assigned branch review')
            ->assertSee('/admin/shops?shop='.$assignedShop->id)
            ->assertSee('Open latest holder in branch')
            ->assertSee('/admin/cardholders?cardholder='.$assignedHolder->id)
            ->assertSee('Open latest card in branch')
            ->assertSee('/admin/cards?card='.$assignedCard->id)
            ->assertSee('Entry posture')
            ->assertSee('These entry points still open the shared Phase 1 workspaces, but shop-backed review inside shops, cardholders, and cards now narrows to the assigned branch with branch-specific review wording once the workspace loads.')
            ->assertSee('Review live shops in assigned branch')
            ->assertSee('Review live cardholders in assigned branch')
            ->assertSee('Review live cards in assigned branch')
            ->assertSee('Review shared card types')
            ->assertSee('Review shared access roles')
            ->assertSee('Review shared reporting sources')
            ->assertDontSee('Review live shops</a>', false)
            ->assertSee('Phase 1 scope note')
            ->assertSee('Latest-work shortcuts for shops, cardholders, and cards now follow branch scope and branch-specific review wording. Card types, roles, and reporting remain shared review surfaces until deeper shop-aware policies arrive.')
            ->assertSee('Open latest branch review: Dashboard Home Shop (active)')
            ->assertSee('/admin/shops?shop='.$assignedShop->id)
            ->assertDontSee('Open latest branch review: Dashboard Other Shop (active)')
            ->assertSee('Open latest branch cardholder review: Scoped Dashboard Holder (active)')
            ->assertSee('/admin/cardholders?cardholder='.$assignedHolder->id)
            ->assertDontSee('Open latest branch cardholder review: Other Dashboard Holder (active)')
            ->assertSee('Open latest branch card review: GX-DASH-001 (active)')
            ->assertSee('/admin/cards?card='.$assignedCard->id)
            ->assertDontSee('Open latest branch card review: GX-DASH-002 (blocked)');

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
            ->assertDontSee('Assigned branch snapshot')
            ->assertDontSee('Branch code')
            ->assertDontSee('Branch posture')
            ->assertDontSee('Primary manager')
            ->assertDontSee('Visible cardholders')
            ->assertDontSee('Visible cards')
            ->assertDontSee('Assigned staff')
            ->assertDontSee('Latest holder')
            ->assertDontSee('Latest holder status')
            ->assertDontSee('Latest holder added')
            ->assertDontSee('Latest card')
            ->assertDontSee('Latest card status')
            ->assertDontSee('Latest card issued')
            ->assertDontSee('Latest activity source')
            ->assertDontSee('Activity freshness')
            ->assertDontSee('Suggested follow-up')
            ->assertDontSee('Open assigned branch review')
            ->assertDontSee('Open latest holder in branch')
            ->assertDontSee('Open latest card in branch')
            ->assertDontSee('Entry posture')
            ->assertDontSee('These entry points still open the shared Phase 1 workspaces')
            ->assertDontSee('Review live shops in assigned branch')
            ->assertDontSee('Review live cardholders in assigned branch')
            ->assertDontSee('Review live cards in assigned branch')
            ->assertSee('Review live shops')
            ->assertSee('Review live cardholders')
            ->assertSee('Review live cards')
            ->assertDontSee('Phase 1 scope note')
            ->assertDontSee('Latest-work shortcuts for shops, cardholders, and cards now follow branch scope.');
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
            ->assertSee('Branch posture')
            ->assertSee('active branch, no live activity yet')
            ->assertSee('Latest holder')
            ->assertSee('No holders in assigned branch yet')
            ->assertSee('Latest holder status')
            ->assertSee('n/a')
            ->assertSee('Latest holder added')
            ->assertSee('Latest card')
            ->assertSee('No cards in assigned branch yet')
            ->assertSee('Latest card status')
            ->assertSee('Latest card issued')
            ->assertSee('Latest activity source')
            ->assertSee('Setup pending')
            ->assertSee('Activity freshness')
            ->assertSee('unknown')
            ->assertSee('Suggested follow-up')
            ->assertSee('Open assigned branch setup and create the first live records.')
            ->assertSee('Open assigned branch setup')
            ->assertSee('/admin/shops?shop='.$assignedShop->id)
            ->assertDontSee('Open assigned branch review</a>', false)
            ->assertSee('Set up assigned branch')
            ->assertDontSee('Review live shops in assigned branch</a>', false)
            ->assertSee('Set up first cardholder in assigned branch')
            ->assertDontSee('Review live cardholders in assigned branch</a>', false)
            ->assertSee('Set up first card in assigned branch')
            ->assertDontSee('Review live cards in assigned branch</a>', false)
            ->assertSee('Open branch setup: Quiet Dashboard Shop (active)')
            ->assertDontSee('Open latest shop review: Quiet Dashboard Shop (active)')
            ->assertSee('Open first cardholder setup in assigned branch')
            ->assertSee('/admin/cardholders')
            ->assertDontSee('Open latest cardholder review:')
            ->assertSee('Open first card setup in assigned branch')
            ->assertSee('/admin/cards')
            ->assertDontSee('Open latest card review:')
            ->assertDontSee('Open latest holder in branch')
            ->assertDontSee('Open latest card in branch');
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
            ->assertSee('Blocked until role persistence and shop-scoped parity checks exist beyond the preview shell.')
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('New role')
            ->assertSee('Blocked until the first Laravel-backed role write flow exists for role identity, scope, and permission bundle parity.')
            ->assertSee('Review matrix')
            ->assertSee('Blocked until the Laravel permission matrix can be verified against legacy staff access.')
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
        ]);

        $permissionA = Permission::create([
            'name' => 'Manage cards',
            'slug' => 'manage-cards-live',
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
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles-permissions');

        $response
            ->assertOk()
            ->assertSee('Shop Manager')
            ->assertSee('href="/admin/roles-permissions?role=', false)
            ->assertSee('Galaxy Central')
            ->assertSee('Manage cards, Manage gifts')
            ->assertSee('Cashier Draft')
            ->assertSee('No permissions linked yet')
            ->assertSee('Review latest saved role')
            ->assertSee('Active roles')
            ->assertSee('Draft roles')
            ->assertSee('Scoped shops')
            ->assertSee('active')
            ->assertSee('draft');
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
        ]);

        $permission = Permission::create([
            'name' => 'Manage cards',
            'slug' => 'manage-cards-selected-role',
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
            ->assertSee('Back to all roles')
            ->assertSee('href="/admin/roles-permissions"', false)
            ->assertSee('Reviewing: Shop Manager')
            ->assertSee('Selected role')
            ->assertSee('Review mode')
            ->assertSee('Live-impact review, linked staff or permissions already exist in Laravel')
            ->assertSee('Scope')
            ->assertSee('Shop scope preview')
            ->assertSee('Galaxy Central')
            ->assertSee('Scope guidance')
            ->assertSee('This role already has visible shop scope in Laravel, so any scope change should be treated as a parity-sensitive access change.')
            ->assertSee('Assigned users')
            ->assertSee('Assigned staff preview')
            ->assertSee('Nare Gevorgyan')
            ->assertSee('Assignment guidance')
            ->assertSee('Assigned staff are already linked in Laravel, so scope and permission changes should be reviewed against real operator impact.')
            ->assertSee('Permission count')
            ->assertSee('Permission coverage')
            ->assertSee('Live bundle present, review changes as parity-sensitive access coverage.')
            ->assertSee('Permission bundle')
            ->assertSee('Manage cards')
            ->assertSee('Laravel status')
            ->assertSee('Access guidance')
            ->assertSee('This role already carries a Laravel permission bundle, so assignment and scope changes should stay parity-first until the matrix editor is verified.')
            ->assertSee('Shop Manager selected for Laravel review')
            ->assertSee('Current request')
            ->assertSee('The shared roles-permissions workspace is now loading this saved role from Laravel data instead of only static preview rows.')
            ->assertSee('Shop Manager permission bundle reflected from model state')
            ->assertSee('Shop Manager assignment scope reflected from model state')
            ->assertSee('This role is currently linked to 1 assigned users across Galaxy Central in Laravel review mode.')
            ->assertSee('Manage cards')
            ->assertSee('Publish role')
            ->assertSee('Blocked until live role assignment parity is verified for this Laravel permission bundle.')
            ->assertSee('Review posture:')
            ->assertSee('Selected-role review is running in Laravel-backed read mode only')
            ->assertSee('Matrix posture:')
            ->assertSee('Keep matrix editing blocked until legacy staff-access parity is verified in Laravel')
            ->assertSee('Assigned staff posture:')
            ->assertSee('Linked staff are already affected by this role in Laravel, so assignment parity should be checked before any access changes move forward.')
            ->assertSee('Permission posture:')
            ->assertSee('The visible Laravel permission bundle is reviewable now, but bundle edits should stay blocked until legacy access mapping is verified.')
            ->assertSee('Publish posture:')
            ->assertSee('This live permission bundle still needs assignment parity checks before publish-style role changes are safe.')
            ->assertSee('Scope posture:')
            ->assertSee('Assigned shops are visible for review, but scope writes should stay parity-first until staff assignment rules are confirmed.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Role assignment, matrix editing, and shop-scoped authorization writes still remain preview-only for this workspace');
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
            ->assertSee('Review latest saved role')
            ->assertDontSee('Back to all roles')
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
            ->assertSee('Review latest saved role')
            ->assertDontSee('Back to all roles')
            ->assertDontSee('Selected role')
            ->assertDontSee('selected for Laravel review');
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
            'activated_at' => '2026-04-10 10:00:00',
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => null,
            'card_type_id' => $cardType->id,
            'number' => 'GX-900002',
            'status' => 'draft',
        ]);

        Card::create([
            'shop_id' => $shop->id,
            'card_holder_id' => $holder->id,
            'card_type_id' => $cardType->id,
            'number' => 'GX-900003',
            'status' => 'blocked',
            'activated_at' => '2026-03-28 09:15:00',
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
            ->assertSee('Review latest saved card')
            ->assertSee('Active cards')
            ->assertSee('Draft cards')
            ->assertSee('Blocked cards')
            ->assertSee('2026-04-10')
            ->assertSee('2026-03-28');
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
            'activated_at' => '2026-03-28 09:15:00',
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cards?card='.$card->id);

        $response
            ->assertOk()
            ->assertSee('Back to all cards')
            ->assertSee('href="/admin/cards"', false)
            ->assertSee('Reviewing: GX-910001')
            ->assertSee('Selected card')
            ->assertSee('Review mode')
            ->assertSee('Live inventory review, this saved Laravel card already carries operational state that should stay parity-first.')
            ->assertSee('Holder')
            ->assertSee('Card type')
            ->assertSee('Shop')
            ->assertSee('Shop guidance')
            ->assertSee('Keep this card tied to its current branch context during review, because cross-shop inventory handling was parity-sensitive in the old Galaxy flow.')
            ->assertSee('Laravel status')
            ->assertSee('Activated')
            ->assertSee('Inventory guidance')
            ->assertSee('This card is blocked in Laravel, so replacement and dispute handling should remain review-only until legacy card-state parity is confirmed.')
            ->assertSee('GX-910001 selected for Laravel review')
            ->assertSee('Current request')
            ->assertSee('The shared cards workspace is now loading this saved inventory record from Laravel data instead of only static preview rows.')
            ->assertSee('GX-910001 status reflected from model state')
            ->assertSee('Inventory posture:')
            ->assertSee('Selected-card review is running in Laravel-backed read mode only')
            ->assertSee('Lifecycle posture:')
            ->assertSee('This blocked card should stay under review-only handling until dispute and replacement semantics match the old Galaxy flow.')
            ->assertSee('Assignment posture:')
            ->assertSee('Holder linkage is visible now, but reassignment and replacement actions should stay blocked until inventory parity is verified.')
            ->assertSee('Shop posture:')
            ->assertSee('Shop ownership is visible for review, but cross-branch movement should stay blocked until branch inventory rules are verified.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Card lifecycle writes, blocked-card handling, and replacement flows still remain preview-only for this workspace');
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
            ->assertSee('Review latest saved card')
            ->assertDontSee('Back to all cards')
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
            ->assertSee('Review latest saved card')
            ->assertDontSee('Back to all cards')
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
            ->assertSee('Review latest saved card')
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
            ->assertDontSee('Back to all cards')
            ->assertDontSee('Reviewing: GX-950002')
            ->assertDontSee('Selected card')
            ->assertSee('Review latest saved card')
            ->assertSee('href="/admin/cards?card=1"', false);
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
            ->assertSee('Blocked until the first Laravel-backed shops index and manager assignment parity checks are verified.')
            ->assertSee('Review branch scope')
            ->assertSee('Blocked until branch ownership rules are confirmed against the legacy Galaxy multi-shop access model.')
            ->assertSee('aria-disabled="true"', false)
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

    public function test_shops_page_replaces_preview_rows_with_model_backed_index_data(): void
    {
        $shop = Shop::create([
            'name' => 'Galaxy Central',
            'code' => 'galaxy-central',
            'is_active' => true,
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
            ->assertSee('Unassigned')
            ->assertSee('Review latest saved shop')
            ->assertSee('href="/admin/shops?shop='.$pausedShop->id.'"', false)
            ->assertSee('Assigned managers')
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
            ->assertSee('Back to all shops')
            ->assertSee('href="/admin/shops"', false)
            ->assertSee('Reviewing: Galaxy Central')
            ->assertSee('Selected shop')
            ->assertSee('Review mode')
            ->assertSee('Live branch review, this Laravel shop already carries operational visibility and should stay parity-first.')
            ->assertSee('Code')
            ->assertSee('Assigned manager')
            ->assertSee('Manager guidance')
            ->assertSee('Keep current manager ownership visible during review, because legacy Galaxy branch administration depended on clear branch responsibility.')
            ->assertSee('Cardholders')
            ->assertSee('Cards')
            ->assertSee('Laravel status')
            ->assertSee('Branch guidance')
            ->assertSee('This branch is already active in Laravel, so scope and manager changes should stay parity-first until branch ownership rules are verified.')
            ->assertSee('Galaxy Central selected for Laravel review')
            ->assertSee('Current request')
            ->assertSee('The shared shops workspace is now loading this saved branch from Laravel data instead of only static preview rows.')
            ->assertSee('Galaxy Central status reflected from model state')
            ->assertSee('Branch posture:')
            ->assertSee('Selected-shop review is running in Laravel-backed read mode only')
            ->assertSee('Status posture:')
            ->assertSee('This active branch is visible for review now, but manager and scope changes should stay blocked until legacy ownership rules are verified.')
            ->assertSee('Manager posture:')
            ->assertSee('Assigned managers are visible in Laravel, but reassignment should stay blocked until branch ownership parity is confirmed.')
            ->assertSee('Coverage posture:')
            ->assertSee('This branch currently exposes 1 cardholders and 1 cards for read-only Laravel review.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Branch writes, manager reassignment, and shop-scope mutation flows still remain preview-only for this workspace');
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
            ->assertSee('Review latest saved shop')
            ->assertDontSee('Back to all shops')
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
            ->assertSee('Review latest saved shop')
            ->assertDontSee('Back to all shops')
            ->assertDontSee('Selected shop')
            ->assertDontSee('selected for Laravel review');
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
            ->assertSee('Review latest saved shop')
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
            ->assertDontSee('Back to all shops')
            ->assertDontSee('Reviewing: Galaxy Scoped Review Other')
            ->assertDontSee('Selected shop')
            ->assertSee('Review latest saved shop')
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
            'is_active' => true,
        ]);

        $activeHolder = CardHolder::create([
            'shop_id' => $shop->id,
            'full_name' => 'Anna Petrova',
            'phone' => '+37491100001',
            'email' => 'anna@example.com',
            'is_active' => true,
        ]);

        $inactiveHolder = CardHolder::create([
            'shop_id' => $inactiveShop->id,
            'full_name' => 'Arman Hakobyan',
            'phone' => '+37491100003',
            'email' => 'arman@example.com',
            'is_active' => false,
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
            ->assertSee('Review latest saved holder')
            ->assertSee('Linked cards')
            ->assertSee('>1<', false)
            ->assertSee('active')
            ->assertSee('inactive');
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
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cardholders?cardholder='.$cardHolder->id);

        $response
            ->assertOk()
            ->assertSee('Back to all holders')
            ->assertSee('href="/admin/cardholders"', false)
            ->assertSee('Reviewing: Anna Petrova')
            ->assertSee('Selected holder')
            ->assertSee('Review mode')
            ->assertSee('Dormant-profile review, this inactive holder stays safer for parity checks before any reactivation path is trusted.')
            ->assertSee('Phone')
            ->assertSee('Shop')
            ->assertSee('Shop guidance')
            ->assertSee('Keep this holder anchored to the current branch during review, because old Galaxy lookup flows depended on branch-aware identity context.')
            ->assertSee('Linked cards')
            ->assertSee('Laravel status')
            ->assertSee('Lookup guidance')
            ->assertSee('This holder is inactive in Laravel, which keeps the record safe for parity checks before operators treat it as fully reactivated.')
            ->assertSee('Anna Petrova selected for Laravel review')
            ->assertSee('Current request')
            ->assertSee('The shared cardholders workspace is now loading this saved holder from Laravel data instead of only static preview rows.')
            ->assertSee('Anna Petrova status reflected from model state')
            ->assertSee('Lookup posture:')
            ->assertSee('Selected-holder review is running in Laravel-backed read mode only')
            ->assertSee('Status posture:')
            ->assertSee('This inactive holder should stay review-only until reactivation and duplicate-profile rules are verified.')
            ->assertSee('Card linkage posture:')
            ->assertSee('No linked cards exist yet, which keeps this holder safer for identity review before card-link flows are enabled.')
            ->assertSee('Activity posture:')
            ->assertSee('Recent activity remains blocked until a stable Laravel event source exists for holder lookup parity.')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Holder search, profile writes, and recent-activity sourcing still remain preview-only for this workspace');
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
            ->assertSee('Review latest saved holder')
            ->assertDontSee('Back to all holders')
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
            ->assertSee('Review latest saved holder')
            ->assertDontSee('Back to all holders')
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
            ->assertSee('Review latest saved holder')
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
            ->assertDontSee('Back to all holders')
            ->assertDontSee('Reviewing: Other Holder Review')
            ->assertDontSee('Selected holder')
            ->assertSee('Review latest saved holder');
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
            ->assertSee('Review accrual gaps')
            ->assertSee('Review chk-90421 receipt')
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

    public function test_checks_points_page_supports_selected_receipt_review_context(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/checks-points?receipt=chk-90407');

        $response
            ->assertOk()
            ->assertSee('Back to all receipts')
            ->assertSee('/admin/checks-points')
            ->assertSee('Reviewing: CHK-90407')
            ->assertSee('Blocked until receipt lookup is backed by Laravel transaction reads.')
            ->assertSee('Blocked until accrual-gap review is backed by Laravel transaction and rule data.')
            ->assertSee('Selected receipt preview')
            ->assertSee('CHK-90407')
            ->assertSee('Accrual posture')
            ->assertSee('Zero-accrual receipts should stay highly visible, because they drive the most parity-sensitive troubleshooting in the old Galaxy flow.')
            ->assertSee('Format guidance')
            ->assertSee('Keep zero-accrual receipts in compact on-screen review first, because operators need amount, points, and rule context together before escalating.')
            ->assertSee('Troubleshooting guidance')
            ->assertSee('Treat this receipt as read-only review until Laravel transaction history and rule-backed explanations exist.')
            ->assertSee('CHK-90407 selected for zero-accrual review')
            ->assertSee('Zero-accrual handoff stays cautious')
            ->assertSee('Zero-accrual handoff stays evidence-first')
            ->assertSee('Receipt, amount, and zero-point outcome should stay visible in the workspace before any rule-gap discussion moves forward.')
            ->assertSee('Zero-point outcomes still need rule and receipt parity verification before any adjustment path is safe.');
    }

    public function test_checks_points_page_ignores_unknown_selected_receipt_and_falls_back_to_catalog(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/checks-points?receipt=unknown-receipt');

        $response
            ->assertOk()
            ->assertSee('Review chk-90421 receipt')
            ->assertDontSee('Back to all receipts')
            ->assertDontSee('Selected receipt preview');
    }

    public function test_checks_points_page_accepts_case_insensitive_selected_receipt_query(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/checks-points?receipt=CHK-90407');

        $response
            ->assertOk()
            ->assertSee('Back to all receipts')
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
            ->assertSee('Open live report catalog')
            ->assertSee('Review export presets')
            ->assertSee('Blocked until preset handling is backed by Laravel reporting flow validation.')
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
            ->assertSee('Live sources')
            ->assertSee('Tracked shops')
            ->assertSee('Tracked cards')
            ->assertSee('Tracked cardholders')
            ->assertSee('Tracked roles')
            ->assertSee('1')
            ->assertSee('Cards by shop')
            ->assertSee('/admin/reports?source=cards-by-shop')
            ->assertSee('1 shops')
            ->assertSee('Cardholder status overview')
            ->assertSee('/admin/reports?source=cardholder-status')
            ->assertSee('1 holders')
            ->assertSee('Role access coverage')
            ->assertSee('/admin/reports?source=role-access')
            ->assertSee('1 roles')
            ->assertSee('Review cards by shop source')
            ->assertSee('Open live report catalog')
            ->assertSee('Review export presets')
            ->assertSee('Blocked until preset handling is backed by Laravel reporting flow validation.')
            ->assertSee('Reporting workspace is now partially Laravel-backed')
            ->assertSee('Catalog metrics and report entry rows now reflect live Galaxy source counts from Laravel models, while presets and exports remain preview-only.')
            ->assertSee('Live reporting sources reflected from Laravel models')
            ->assertSee('The reporting workspace now sees 1 shops, 1 cards, 1 cardholders, and 1 roles through the current Laravel foundation.')
            ->assertSee('Export catalog remains parity-first')
            ->assertSee('Metrics and entry rows are live-backed now, but preset handling and export generation still stay blocked until the reporting pipeline is verified.')
            ->assertSee('Report catalog is still lightweight, but source counts now come from live Laravel models')
            ->assertSee('Reporting posture')
            ->assertSee('This workspace is now live-backed for read-only source review, but preset and export flows should stay parity-first until the reporting pipeline is verified.')
            ->assertSee('Readiness signal')
            ->assertSee('Partially ready: live source review works now, while preset handling and exports stay blocked behind later reporting-pipeline verification.')
            ->assertSee('Preset posture')
            ->assertSee('Preset periods are still preview-only, so operators should treat the live source layer as reviewable while preset-driven report flows remain gated.')
            ->assertSee('Export posture')
            ->assertSee('Export generation is still blocked, so the live reporting layer should stay review-only until file delivery and parity checks are verified.')
            ->assertSee('Source coverage')
            ->assertSee('Laravel reporting inputs currently cover 1 shops, 1 cards, 1 cardholders, and 1 roles for read-only review.')
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
            'issued_at' => now(),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/reports?source=cards-by-shop');

        $response
            ->assertOk()
            ->assertSee('Back to report catalog')
            ->assertSee('/admin/reports')
            ->assertSee('Reviewing: Cards by shop')
            ->assertSee('Export source snapshot')
            ->assertSee('Blocked until reporting exports and file delivery are verified against legacy Galaxy output expectations.')
            ->assertSee('Selected report source')
            ->assertSee('Cards by shop')
            ->assertSee('Review mode')
            ->assertSee('Live-source review, card inventory already exists in Laravel for shop-level reporting checks.')
            ->assertSee('Source coverage')
            ->assertSee('1 cards across 1 tracked shops are currently available for read-only reporting review.')
            ->assertSee('Scope guidance')
            ->assertSee('Keep this source centered on branch-by-branch totals, because old Galaxy operators usually compared card inventory by shop before opening broader exports.')
            ->assertSee('Default period posture')
            ->assertSee('Use current snapshot review first, then keep preset periods staged until branch-total parity is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Prefer table-first review here, because branch inventory checks should stay visible on screen before anyone expects export files.')
            ->assertSee('Cards by shop source selected for Laravel review')
            ->assertSee('This reporting view now reflects 1 tracked cards across 1 shops from the current Laravel foundation.')
            ->assertSee('Branch inventory handoff stays on-screen first')
            ->assertSee('Operators should hand off branch comparison findings in the live workspace before relying on exported files for this source.')
            ->assertSee('Scope posture')
            ->assertSee('Branch-level comparison is the first parity target, so cross-shop shaping should stay conservative until legacy report totals are matched.')
            ->assertSee('Grouping posture')
            ->assertSee('Shop grouping should stay read-only until query shaping is verified against legacy report totals.');
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
            ->assertSee('Review cards by shop source')
            ->assertDontSee('Back to report catalog')
            ->assertDontSee('Selected report source');
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
            ->assertSee('Reviewing: Cards by shop')
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
            ->assertSee('Back to all rules')
            ->assertSee('/admin/services-rules')
            ->assertSee('Reviewing: Night service block')
            ->assertSee('Publish rule')
            ->assertSee('Blocked until rule CRUD and parity checks exist beyond the preview shell.')
            ->assertSee('Selected rule preview')
            ->assertSee('Night service block')
            ->assertSee('Condition posture')
            ->assertSee('Bar-service exclusions should remain draft-only until legacy exception behavior is rechecked in Laravel.')
            ->assertSee('Priority posture')
            ->assertSee('Keep this blocking rule below confirmed accrual logic until exclusion order is verified.')
            ->assertSee('Format guidance')
            ->assertSee('Keep draft exclusion rules in compact on-screen review first, because operators need scope, condition, and effect visible together before discussing publication.')
            ->assertSee('Night service block selected for exception review')
            ->assertSee('Draft exclusion handoff stays cautious')
            ->assertSee('Draft exclusion handoff keeps parity evidence visible')
            ->assertSee('Scope, blocking condition, and no-accrual effect should stay visible in the workspace before any publish discussion begins.')
            ->assertSee('North Shop exclusions should stay draft-only until scoped exception behavior is verified against the legacy system.');
    }

    public function test_services_rules_page_ignores_unknown_selected_rule_and_falls_back_to_catalog(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules?rule=unknown-rule');

        $response
            ->assertOk()
            ->assertSee('Review birthday bonus rule')
            ->assertDontSee('Back to all rules')
            ->assertDontSee('Selected rule preview');
    }

    public function test_services_rules_page_accepts_case_insensitive_selected_rule_query(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/services-rules?rule=NIGHT-SERVICE-BLOCK');

        $response
            ->assertOk()
            ->assertSee('Back to all rules')
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
            ->assertSee('Blocked until role persistence and shop-scoped parity checks exist beyond the preview shell.')
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
            ->assertSee('Blocked until role persistence and shop-scoped parity checks exist beyond the preview shell.')
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
            ->assertSee('Blocked until the first Laravel-backed service-rule write flow exists for group, scope, effect, and priority.')
            ->assertSee('Review priorities')
            ->assertSee('Blocked until rule priority resolution is verified in Laravel.')
            ->assertSee('Blocked until rule CRUD and parity checks exist beyond the preview shell.')
            ->assertSee('aria-disabled="true"', false)
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
            ->assertSee('Back to all gifts')
            ->assertSee('/admin/gifts')
            ->assertSee('Reviewing: Premium dessert set')
            ->assertSee('Publish gift')
            ->assertSee('Blocked until gift CRUD and redemption parity exist beyond the preview shell.')
            ->assertSee('Selected gift preview')
            ->assertSee('Premium dessert set')
            ->assertSee('Stock posture')
            ->assertSee('Zero-stock rewards should remain paused in review mode until Laravel inventory and reopening flows can reproduce the old behavior safely.')
            ->assertSee('Format guidance')
            ->assertSee('Keep paused zero-stock rewards in compact on-screen review first, because operators need scope, stock, and cost visible together before discussing reopening.')
            ->assertSee('Redemption guidance')
            ->assertSee('Treat this paused reward as review-only until stock recovery and redemption parity are backed by Laravel flows.')
            ->assertSee('Premium dessert set selected for paused reward review')
            ->assertSee('Paused reward handoff stays cautious')
            ->assertSee('Paused reward handoff keeps stock evidence visible')
            ->assertSee('Scope, zero-stock state, and points cost should stay visible in the workspace before any reopening discussion begins.')
            ->assertSee('Zero-stock handling is still preview-only until inventory sync and recovery behavior are validated in Laravel.');
    }

    public function test_gifts_page_ignores_unknown_selected_gift_and_falls_back_to_catalog(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts?gift=unknown-gift');

        $response
            ->assertOk()
            ->assertSee('Review coffee voucher gift')
            ->assertDontSee('Back to all gifts')
            ->assertDontSee('Selected gift preview');
    }

    public function test_gifts_page_accepts_case_insensitive_selected_gift_query(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/gifts?gift=PREMIUM-DESSERT-SET');

        $response
            ->assertOk()
            ->assertSee('Back to all gifts')
            ->assertSee('Reviewing: Premium dessert set')
            ->assertSee('Selected gift preview');
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
            ->assertSee('Blocked until the first Laravel-backed gift write flow exists for catalog, scope, cost, and stock state.')
            ->assertSee('Stock audit')
            ->assertSee('Blocked until stock checks are backed by Laravel inventory data.')
            ->assertSee('Blocked until gift CRUD and redemption parity exist beyond the preview shell.')
            ->assertSee('aria-disabled="true"', false)
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
            ->assertSee('href="#live-form"', false)
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
            ->assertSee('Edit latest saved type')
            ->assertSee('href="/admin/card-types?cardType='.$latestCardType->id.'#live-form"', false);
    }

    public function test_card_types_page_switches_live_form_into_real_edit_mode_for_selected_card_type(): void
    {
        $cardType = CardType::create([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.75',
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.card-types.index', ['cardType' => $cardType->id]));

        $response
            ->assertOk()
            ->assertSee('Create new type')
            ->assertSee('href="/admin/card-types#live-form"', false)
            ->assertSee('Activate type')
            ->assertSee('<form method="POST" action="/admin/card-types/'.$cardType->id.'/toggle-status"', false)
            ->assertSee('name="_method"', false)
            ->assertSee('value="PATCH"', false)
            ->assertSee('Editing: Galaxy Prime')
            ->assertSee('Import rules')
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('Blocked until draft parity review is complete.')
            ->assertSee('Selected record summary')
            ->assertSee('Selected tier:')
            ->assertSee('Galaxy Prime')
            ->assertSee('Slug:')
            ->assertSee('galaxy-prime')
            ->assertSee('Points rate:')
            ->assertSee('1.75x')
            ->assertSee('Laravel status:')
            ->assertSee('draft')
            ->assertSee('Status guidance:')
            ->assertSee('This tier is still in draft, which keeps it safe for parity checks before operators treat it as live loyalty behavior.')
            ->assertSee('Rule-import blocker:')
            ->assertSee('Rule import is still blocked, but draft state keeps this tier safe for parity-first catalog and accrual checks.')
            ->assertSee('Publish guidance:')
            ->assertSee('Keep this tier in draft until rule import expectations and old Galaxy behavior are mapped clearly enough to publish safely.')
            ->assertSee('Readiness signal:')
            ->assertSee('Not ready to publish: draft mode is still the holding state for parity validation and rule-import review.')
            ->assertSee('Recent activity preview')
            ->assertSee('Galaxy Prime selected for Laravel edit flow')
            ->assertSee('Current request')
            ->assertSee('The shared card-type form is now loading this saved tier directly from Laravel data instead of preview-only defaults.')
            ->assertSee('Galaxy Prime status reflected from model state')
            ->assertSee('This tier is currently marked as draft in Laravel and the management context card now mirrors that state.')
            ->assertSee('Implementation dependencies')
            ->assertSee('Selected record:')
            ->assertSee('Edit flow state:')
            ->assertSee('Shared live form is running in request-driven PATCH mode')
            ->assertSee('Current status posture:')
            ->assertSee('Draft tiers are the safe place for parity-first validation and copy changes')
            ->assertSee('Rule-import posture:')
            ->assertSee('Imports can be reviewed in draft mode, but they are still not safe to enable yet')
            ->assertSee('Publish posture:')
            ->assertSee('Draft tiers should stay unpublished until legacy behavior is mapped more explicitly')
            ->assertSee('Action gating:')
            ->assertSee('Allow draft-safe edits and validation only, keep live-facing actions gated')
            ->assertSee('Remaining backend gap:')
            ->assertSee('Publish logic and rule-import parity still remain preview-only for this tier')
            ->assertSee('Edit card type in Laravel')
            ->assertSee('Update the selected Galaxy tier through the shared live form without leaving the card-types workspace.')
            ->assertSee('>Save card type changes<', false)
            ->assertSee('action="/admin/card-types/'.$cardType->id.'"', false)
            ->assertSee('name="_method"', false)
            ->assertSee('value="PATCH"', false)
            ->assertSee('value="Galaxy Prime"', false)
            ->assertSee('value="galaxy-prime"', false)
            ->assertSee('value="1.75"', false)
            ->assertSee('<option value="0" selected>Draft</option>', false)
            ->assertSee('href="/admin/card-types"', false);
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
            ->assertSee('Edit latest saved type')
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
            ->assertSee('Edit latest saved type')
            ->assertDontSee('Edit card type in Laravel')
            ->assertDontSee('Selected tier')
            ->assertDontSee('selected for Laravel edit flow');
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
            ->assertSee('Active in Laravel flow')
            ->assertSee('Draft in Laravel flow')
            ->assertSee('Auto after issue')
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
            ->assertSee('Blocked until draft parity review is complete.')
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
            ->assertSee('Active tiers')
            ->assertSee('Draft tiers')
            ->assertSee('Saved types')
            ->assertDontSee('Imported rules')
            ->assertSee('>2<', false)
            ->assertSee('>1<', false)
            ->assertSee('>3<', false);
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
            ->assertSee('Card type "Galaxy Prime" was created.')
            ->assertSee('Selected tier:')
            ->assertSee('Galaxy Prime')
            ->assertSee('Edit card type in Laravel')
            ->assertSee('action="/admin/card-types/'.$cardType->id.'"', false);
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
            $this->assertSame('Create card type in Laravel', $liveForm['title']);
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
            $this->assertSame('Create card type in Laravel', $liveForm['title']);

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
        Config::set('admin-pages.card-types.liveForm.cancelLabel', 'Back to catalog');

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
            ->assertRedirect(route('admin.card-types.index', ['cardType' => $cardType]).'#backend-flow-status')
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

    public function test_card_types_page_shows_update_success_flash_message(): void
    {
        $user = User::factory()->create();
        $cardType = CardType::create([
            'name' => 'Galaxy Prime Plus',
            'slug' => 'galaxy-prime-plus-flash',
            'points_rate' => '2.25',
            'is_active' => false,
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
            ->assertSee('Edit card type in Laravel')
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
            ->assertRedirect(route('admin.card-types.index').'#live-form')
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
            ->assertSee('Edit card type in Laravel')
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
            ->assertSee('Edit card type in Laravel')
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
            ->assertSee('Edit card type in Laravel')
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
            ->assertSee('Edit card type in Laravel')
            ->assertSee('Create new type')
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
