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
            'assignedBranchSnapshot' => $this->assignedBranchSnapshot(),
            'liveEntryScopeNote' => $this->liveEntryScopeNote(),
            'latestWorkspaceScopeNote' => $this->latestWorkspaceScopeNote(),
            'liveReviewEntryPoints' => $this->liveReviewEntryPoints(),
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
            label: sprintf('%s: %s (%s)', $this->latestShopWorkspaceLabel($shop), $shop->name, $shop->is_active ? 'active' : 'inactive'),
            routeName: 'admin.shops.index',
            parameters: ['shop' => $shop->id],
        ) : null;
    }

    protected function latestShopWorkspaceLabel(Shop $shop): string
    {
        if (! $this->isShopScopedAdmin()) {
            return 'Open latest shop review';
        }

        if (! $shop->is_active) {
            return 'Open latest shop review';
        }

        $shop->loadCount(['cardHolders', 'cards']);

        if ($shop->card_holders_count === 0 && $shop->cards_count === 0) {
            return 'Open branch setup';
        }

        return 'Open latest shop review';
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

    protected function liveReviewEntryPoints(): array
    {
        if (! $this->isShopScopedAdmin()) {
            return [
                $this->workspaceLink('Review live shops', 'admin.shops.index'),
                $this->workspaceLink('Review live cardholders', 'admin.cardholders.index'),
                $this->workspaceLink('Review live cards', 'admin.cards.index'),
                $this->workspaceLink('Review live card types', 'admin.card-types.index'),
                $this->workspaceLink('Review live access roles', 'admin.roles-permissions.index'),
                $this->workspaceLink('Review live reporting sources', 'admin.reports.index'),
            ];
        }

        $shop = $this->adminUser()?->shop;
        $primaryScopedShopEntryLabel = $this->scopedShopEntryLabel($shop);
        $primaryScopedCardholderEntryLabel = $this->scopedCardholderEntryLabel($shop);
        $primaryScopedCardEntryLabel = $this->scopedCardEntryLabel($shop);

        return [
            $this->workspaceLink($primaryScopedShopEntryLabel, 'admin.shops.index'),
            $this->workspaceLink($primaryScopedCardholderEntryLabel, 'admin.cardholders.index'),
            $this->workspaceLink($primaryScopedCardEntryLabel, 'admin.cards.index'),
            $this->workspaceLink('Review live card types', 'admin.card-types.index'),
            $this->workspaceLink('Review live access roles', 'admin.roles-permissions.index'),
            $this->workspaceLink('Review live reporting sources', 'admin.reports.index'),
        ];
    }

    protected function adminUser(): ?User
    {
        $user = request()->user();

        return $user instanceof User ? $user : null;
    }

    protected function isShopScopedAdmin(): bool
    {
        $user = $this->adminUser();

        return $user instanceof User && $user->hasShopScopedAdminAccess();
    }

    protected function dashboardScopeSummary(): ?array
    {
        $user = $this->adminUser();

        if (! $this->isShopScopedAdmin()) {
            return null;
        }

        $shopName = $user?->shop?->name ?? 'assigned shop';

        return [
            'label' => 'Current review scope',
            'value' => sprintf('Shop-scoped admin mode is active. Latest-work shortcuts and live review links should stay anchored to %s while Phase 1 policies are still being mapped.', $shopName),
        ];
    }

    protected function assignedBranchSnapshot(): ?array
    {
        $user = $this->adminUser();

        if (! $this->isShopScopedAdmin() || ! $user?->relationLoaded('shop')) {
            $user?->loadMissing('shop');
        }

        $shop = $user?->shop;

        if (! $shop instanceof Shop) {
            return null;
        }

        $shop->loadCount(['cardHolders', 'cards', 'users']);
        $shop->loadMissing(['users' => fn ($query) => $query->orderBy('name')]);

        $latestHolder = CardHolder::query()
            ->where('shop_id', $shop->id)
            ->latest('id')
            ->first();

        $latestCard = Card::query()
            ->where('shop_id', $shop->id)
            ->latest('id')
            ->first();

        $latestActivity = $this->latestBranchActivitySummary($latestHolder, $latestCard);
        $activityFreshness = $this->latestBranchActivityFreshness($latestHolder, $latestCard);
        $branchPosture = $this->branchOperationalPosture($shop, $latestHolder, $latestCard);
        $suggestedFollowUp = $this->branchSuggestedFollowUp($shop, $latestHolder, $latestCard);
        $actions = $this->assignedBranchSnapshotActions($shop, $latestHolder, $latestCard);

        return [
            'label' => 'Assigned branch snapshot',
            'items' => [
                ['label' => 'Branch', 'value' => $shop->name],
                ['label' => 'Branch code', 'value' => $shop->code],
                ['label' => 'Branch posture', 'value' => $branchPosture],
                ['label' => 'Primary manager', 'value' => $shop->users->first()?->name ?? 'Unassigned'],
                ['label' => 'Laravel status', 'value' => $shop->is_active ? 'active' : 'paused'],
                ['label' => 'Visible cardholders', 'value' => (string) $shop->card_holders_count],
                ['label' => 'Visible cards', 'value' => (string) $shop->cards_count],
                ['label' => 'Assigned staff', 'value' => (string) $shop->users_count],
                ['label' => 'Latest holder', 'value' => $latestHolder instanceof CardHolder ? $latestHolder->full_name : 'No holders in assigned branch yet'],
                ['label' => 'Latest holder status', 'value' => $latestHolder instanceof CardHolder ? ($latestHolder->is_active ? 'active' : 'inactive') : 'n/a'],
                ['label' => 'Latest holder added', 'value' => $latestHolder instanceof CardHolder ? $latestHolder->created_at?->toDateString() ?? 'unknown' : 'n/a'],
                ['label' => 'Latest card', 'value' => $latestCard instanceof Card ? $latestCard->number : 'No cards in assigned branch yet'],
                ['label' => 'Latest card status', 'value' => $latestCard instanceof Card ? $latestCard->status : 'n/a'],
                ['label' => 'Latest card issued', 'value' => $latestCard instanceof Card ? $latestCard->created_at?->toDateString() ?? 'unknown' : 'n/a'],
                ['label' => 'Latest activity source', 'value' => $latestActivity],
                ['label' => 'Activity freshness', 'value' => $activityFreshness],
                ['label' => 'Suggested follow-up', 'value' => $suggestedFollowUp],
            ],
            'actions' => $actions,
        ];
    }

    protected function latestBranchActivitySummary(?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $latestHolder instanceof CardHolder && ! $latestCard instanceof Card) {
            return 'No branch activity yet';
        }

        if (! $latestCard instanceof Card) {
            return 'Cardholder added';
        }

        if (! $latestHolder instanceof CardHolder) {
            return 'Card issued';
        }

        $holderCreatedAt = $latestHolder->created_at;
        $cardCreatedAt = $latestCard->created_at;

        if ($holderCreatedAt === null && $cardCreatedAt === null) {
            return 'Latest branch record updated';
        }

        if ($holderCreatedAt === null) {
            return 'Card issued';
        }

        if ($cardCreatedAt === null) {
            return 'Cardholder added';
        }

        return $cardCreatedAt->greaterThanOrEqualTo($holderCreatedAt)
            ? 'Card issued'
            : 'Cardholder added';
    }

    protected function latestBranchActivityFreshness(?CardHolder $latestHolder, ?Card $latestCard): string
    {
        $latestTimestamp = collect([
            $latestHolder?->created_at,
            $latestCard?->created_at,
        ])
            ->filter()
            ->sortDesc()
            ->first();

        if ($latestTimestamp === null) {
            return 'unknown';
        }

        return now()->diffInDays($latestTimestamp) <= 1
            ? 'fresh activity'
            : 'stale activity';
    }

    protected function branchOperationalPosture(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $shop->is_active) {
            return 'paused branch';
        }

        if (! $latestHolder instanceof CardHolder && ! $latestCard instanceof Card) {
            return 'active branch, no live activity yet';
        }

        return 'active branch, live activity visible';
    }

    protected function branchSuggestedFollowUp(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $shop->is_active) {
            return 'Confirm pause reason before reopening branch work.';
        }

        if (! $latestHolder instanceof CardHolder && ! $latestCard instanceof Card) {
            return 'Open assigned branch review and verify the first live records.';
        }

        return 'Resume the latest branch review flow from the scoped shortcuts.';
    }

    protected function assignedBranchSnapshotActions(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): array
    {
        if (! $shop->is_active) {
            return [];
        }

        $actions = [[
            'label' => $this->assignedBranchPrimaryActionLabel($latestHolder, $latestCard),
            'route' => route('admin.shops.index', ['shop' => $shop->id]),
        ]];

        if ($latestHolder instanceof CardHolder) {
            $actions[] = [
                'label' => 'Open latest holder in branch',
                'route' => route('admin.cardholders.index', ['cardholder' => $latestHolder->id]),
            ];
        }

        if ($latestCard instanceof Card) {
            $actions[] = [
                'label' => 'Open latest card in branch',
                'route' => route('admin.cards.index', ['card' => $latestCard->id]),
            ];
        }

        return $actions;
    }

    protected function assignedBranchPrimaryActionLabel(?CardHolder $latestHolder, ?Card $latestCard): string
    {
        return $latestHolder instanceof CardHolder || $latestCard instanceof Card
            ? 'Open assigned branch review'
            : 'Open assigned branch setup';
    }

    protected function scopedShopEntryLabel(?Shop $shop): string
    {
        return $this->scopedEntryLabel(
            shop: $shop,
            liveLabel: 'Review live shops in assigned branch',
            setupLabel: 'Set up assigned branch',
            countRelations: ['cardHolders', 'cards'],
            isSetupConditionMet: fn (Shop $shop): bool => $shop->card_holders_count === 0 && $shop->cards_count === 0,
        );
    }

    protected function scopedCardholderEntryLabel(?Shop $shop): string
    {
        return $this->scopedEntryLabel(
            shop: $shop,
            liveLabel: 'Review live cardholders in assigned branch',
            setupLabel: 'Set up first cardholder in assigned branch',
            countRelations: ['cardHolders'],
            isSetupConditionMet: fn (Shop $shop): bool => $shop->card_holders_count === 0,
        );
    }

    protected function scopedCardEntryLabel(?Shop $shop): string
    {
        return $this->scopedEntryLabel(
            shop: $shop,
            liveLabel: 'Review live cards in assigned branch',
            setupLabel: 'Set up first card in assigned branch',
            countRelations: ['cards'],
            isSetupConditionMet: fn (Shop $shop): bool => $shop->cards_count === 0,
        );
    }

    protected function scopedEntryLabel(?Shop $shop, string $liveLabel, string $setupLabel, array $countRelations, callable $isSetupConditionMet): string
    {
        if (! $shop instanceof Shop || ! $shop->is_active) {
            return $liveLabel;
        }

        $shop->loadCount($countRelations);

        return $isSetupConditionMet($shop)
            ? $setupLabel
            : $liveLabel;
    }

    protected function liveEntryScopeNote(): ?array
    {
        if (! $this->isShopScopedAdmin()) {
            return null;
        }

        return [
            'label' => 'Entry posture',
            'value' => 'These entry points still open the shared Phase 1 workspaces, but shop-backed review inside shops, cardholders, and cards now narrows to the assigned branch once the workspace loads.',
        ];
    }

    protected function latestWorkspaceScopeNote(): ?array
    {
        if (! $this->isShopScopedAdmin()) {
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
