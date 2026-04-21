<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $navigation = config('admin-navigation');

        return view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'navigationGroups' => $navigation,
            'plannedSectionCount' => collect($navigation)->sum(fn (array $group): int => count($group['items'])),
            'shopCount' => Shop::query()->count(),
            'activeShopCount' => Shop::query()->where('is_active', true)->count(),
            'cardHolderCount' => CardHolder::query()->count(),
            'activeCardHolderCount' => CardHolder::query()->where('status', 'active')->count(),
            'cardCount' => Card::query()->count(),
            'activeCardCount' => Card::query()->where('status', 'active')->count(),
            'roleCount' => Role::query()->count(),
            'permissionCount' => Permission::query()->count(),
            'dashboardScopeSummary' => $this->dashboardScopeSummary(),
            'latestWorkspaceScopeNote' => $this->latestWorkspaceScopeNote(),
            'liveReviewEntryPoints' => [
                $this->workspaceLink('Review live shops', 'admin.shops.index'),
                $this->workspaceLink('Review live cardholders', 'admin.cardholders.index'),
                $this->workspaceLink('Review live cards', 'admin.cards.index'),
                $this->workspaceLink('Review live card types', 'admin.card-types.index'),
                $this->workspaceLink('Review live access roles', 'admin.roles-permissions.index'),
                $this->workspaceLink('Review live reporting sources', 'admin.reports.index'),
            ],
            'latestWorkspaces' => array_values(array_filter([
                $this->latestShopWorkspace(),
                $this->latestCardHolderWorkspace(),
                $this->latestCardWorkspace(),
                $this->latestCardTypeWorkspace(),
                $this->latestRoleWorkspace(),
            ])),
        ]);
    }

    protected function latestShopWorkspace(): ?array
    {
        $shop = $this->latestAccessibleRecord(
            Shop::query()->latest('id')->get(),
            fn (Shop $shop): ?Shop => $shop,
        );

        return $shop ? $this->workspaceLink(
            label: sprintf('Open latest shop review: %s (%s)', $shop->name, $shop->is_active ? 'active' : 'inactive'),
            routeName: 'admin.shops.index',
            parameters: ['shop' => $shop->id],
        ) : null;
    }

    protected function latestCardHolderWorkspace(): ?array
    {
        $cardHolder = $this->latestAccessibleRecord(
            CardHolder::query()->with('shop')->latest('id')->get(),
            fn (CardHolder $cardHolder): ?Shop => $cardHolder->shop,
        );

        if (! $cardHolder) {
            return null;
        }

        $status = $cardHolder->status ?? ($cardHolder->is_active ? 'active' : 'inactive');

        return $this->workspaceLink(
            label: sprintf('Open latest cardholder review: %s (%s)', $cardHolder->full_name, $status),
            routeName: 'admin.cardholders.index',
            parameters: ['cardholder' => $cardHolder->id],
        );
    }

    protected function latestCardWorkspace(): ?array
    {
        $card = $this->latestAccessibleRecord(
            Card::query()->with('shop')->latest('id')->get(),
            fn (Card $card): ?Shop => $card->shop,
        );

        return $card ? $this->workspaceLink(
            label: sprintf('Open latest card review: %s (%s)', $card->number, $card->status),
            routeName: 'admin.cards.index',
            parameters: ['card' => $card->id],
        ) : null;
    }

    protected function latestCardTypeWorkspace(): ?array
    {
        $cardType = CardType::query()->latest('id')->first();

        return $cardType ? $this->workspaceLink(
            label: sprintf('Open latest card type workspace: %s (%s)', $cardType->name, $cardType->is_active ? 'active' : 'draft'),
            routeName: 'admin.card-types.index',
            parameters: ['cardType' => $cardType->id],
        ) : null;
    }

    protected function latestRoleWorkspace(): ?array
    {
        $role = Role::query()->latest('id')->first();

        return $role ? $this->workspaceLink(
            label: sprintf('Open latest role review: %s (%d permissions)', $role->name, $role->permissions()->count()),
            routeName: 'admin.roles-permissions.index',
            parameters: ['role' => $role->id],
        ) : null;
    }

    protected function workspaceLink(string $label, string $routeName, array $parameters = []): array
    {
        return [
            'label' => $label,
            'route' => route($routeName, $parameters),
        ];
    }

    protected function adminUser(): ?User
    {
        $user = request()->user();

        return $user instanceof User ? $user : null;
    }

    protected function dashboardScopeSummary(): ?array
    {
        $user = $this->adminUser();

        if (! $user instanceof User || ! $user->hasShopScopedAdminAccess()) {
            return null;
        }

        $shopName = $user->shop?->name ?? 'assigned shop';

        return [
            'label' => 'Current review scope',
            'value' => sprintf('Shop-scoped admin mode is active. Latest-work shortcuts and live review links should stay anchored to %s while Phase 1 policies are still being mapped.', $shopName),
        ];
    }

    protected function latestWorkspaceScopeNote(): ?array
    {
        $user = $this->adminUser();

        if (! $user instanceof User || ! $user->hasShopScopedAdminAccess()) {
            return null;
        }

        return [
            'label' => 'Phase 1 scope note',
            'value' => 'Latest-work shortcuts for shops, cardholders, and cards now follow branch scope. Card types, roles, and reporting remain shared review surfaces until deeper shop-aware policies arrive.',
        ];
    }

    protected function latestAccessibleRecord(iterable $records, callable $shopResolver): mixed
    {
        $adminUser = $this->adminUser();

        if (! $adminUser instanceof User) {
            return collect($records)->first();
        }

        return collect($records)
            ->first(fn (mixed $record): bool => $adminUser->canAccessShop($shopResolver($record)));
    }
}
