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
            'liveDomainCoverage' => $this->liveDomainCoverage(),
            'foundationFocus' => $this->foundationFocus(),
            'foundationPosture' => $this->foundationPosture(),
            'foundationReadinessSignal' => $this->foundationReadinessSignal(),
            'activeFoundationCoverage' => $this->activeFoundationCoverage(),
            'branchPauseCoverage' => $this->branchPauseCoverage(),
            'accessBaselineCoverage' => $this->accessBaselineCoverage(),
            'tierBaselineCoverage' => $this->tierBaselineCoverage(),
            'shopCount' => Shop::query()->count(),
            'activeShopCount' => Shop::query()->where('is_active', true)->count(),
            'cardHolderCount' => CardHolder::query()->count(),
            'activeCardHolderCount' => CardHolder::query()->where('is_active', true)->count(),
            'cardCount' => Card::query()->count(),
            'activeCardCount' => Card::query()->where('status', 'active')->count(),
            'cardTypeCount' => CardType::query()->count(),
            'activeCardTypeCount' => CardType::query()->where('is_active', true)->count(),
            'roleCount' => Role::query()->count(),
            'permissionCount' => Permission::query()->count(),
            'foundationHandoffSummary' => $this->foundationHandoffSummary(),
            'dashboardScopeSummary' => $this->dashboardScopeSummary(),
            'assignedBranchSnapshot' => $this->assignedBranchSnapshot(),
            'liveEntryHandoffSummary' => $this->liveEntryHandoffSummary(),
            'liveEntryScopeNote' => $this->liveEntryScopeNote(),
            'latestWorkspaceHandoffSummary' => $this->latestWorkspaceHandoffSummary(),
            'latestWorkspaceScopeNote' => $this->latestWorkspaceScopeNote(),
            'migrationMapHandoffSummary' => $this->migrationMapHandoffSummary($navigation),
            'migrationMapFocus' => $this->migrationMapFocus($navigation),
            'migrationMapPosture' => $this->migrationMapPosture(),
            'liveReviewEntryPoints' => $this->liveReviewEntryPoints(),
            'liveEntryPointCoverage' => $this->liveEntryPointCoverage(),
            'liveEntryPointFocus' => $this->liveEntryPointFocus(),
            'liveEntryPointPosture' => $this->liveEntryPointPosture(),
            'latestWorkspaceCoverage' => $this->latestWorkspaceCoverage(),
            'latestWorkspaceFocus' => $this->latestWorkspaceFocus(),
            'latestWorkspacePosture' => $this->latestWorkspacePosture(),
            'latestWorkspaces' => array_values(array_filter([
                $this->latestShopWorkspace(),
                $this->latestCardHolderWorkspace(),
                $this->latestCardWorkspace(),
                $this->latestCardTypeWorkspace(),
                $this->latestRoleWorkspace(),
            ])),
        ]);
    }

    protected function liveEntryPointCoverage(): string
    {
        return sprintf('%d live review entry points staged', count($this->liveReviewEntryPoints()));
    }

    protected function liveEntryPointFocus(): string
    {
        $entryPoint = $this->liveReviewEntryPoints()[0] ?? null;

        if (! is_array($entryPoint) || ! isset($entryPoint['label'])) {
            return 'first live review surface still needs to be staged';
        }

        return sprintf('start with %s', mb_strtolower((string) $entryPoint['label']));
    }

    protected function liveEntryPointPosture(): string
    {
        $liveEntryDomainCount = collect([
            Shop::query()->count(),
            CardHolder::query()->count(),
            Card::query()->count(),
        ])->filter(fn (int $count): bool => $count > 0)->count();

        return match (true) {
            $liveEntryDomainCount === 0 => 'setup-first staged entry surfaces',
            $liveEntryDomainCount < 3 => 'partial staged entry coverage',
            default => 'review-ready staged entry surfaces',
        };
    }

    protected function latestWorkspaceCoverage(): string
    {
        $latestWorkspaceCount = count(array_values(array_filter([
            $this->latestShopWorkspace(),
            $this->latestCardHolderWorkspace(),
            $this->latestCardWorkspace(),
            $this->latestCardTypeWorkspace(),
            $this->latestRoleWorkspace(),
        ])));

        return sprintf('%d latest-work shortcuts currently available', $latestWorkspaceCount);
    }

    protected function latestWorkspaceFocus(): string
    {
        $latestWorkspace = array_values(array_filter([
            $this->latestShopWorkspace(),
            $this->latestCardHolderWorkspace(),
            $this->latestCardWorkspace(),
            $this->latestCardTypeWorkspace(),
            $this->latestRoleWorkspace(),
        ]))[0] ?? null;

        if (! is_array($latestWorkspace) || ! isset($latestWorkspace['label'])) {
            return 'first live workspace still needs to be created';
        }

        return sprintf('start with %s', mb_strtolower((string) $latestWorkspace['label']));
    }

    protected function latestWorkspacePosture(): string
    {
        $latestWorkspaceCount = count(array_values(array_filter([
            $this->latestShopWorkspace(),
            $this->latestCardHolderWorkspace(),
            $this->latestCardWorkspace(),
            $this->latestCardTypeWorkspace(),
            $this->latestRoleWorkspace(),
        ])));

        return match (true) {
            $latestWorkspaceCount === 0 => 'setup-first jump-back pending',
            $latestWorkspaceCount < 3 => 'partial jump-back coverage',
            default => 'review-ready jump-back coverage',
        };
    }

    protected function migrationMapFocus(array $navigation): string
    {
        $firstItem = collect($navigation)
            ->pluck('items')
            ->flatten(1)
            ->first();

        if (! is_array($firstItem) || ! isset($firstItem['label'])) {
            return 'first parity target still needs to be mapped';
        }

        return sprintf('start with %s', mb_strtolower((string) $firstItem['label']));
    }

    protected function migrationMapPosture(): string
    {
        $liveDomainCount = collect([
            Shop::query()->count(),
            CardHolder::query()->count(),
            Card::query()->count(),
            Role::query()->count(),
            Permission::query()->count(),
        ])->filter(fn (int $count): bool => $count > 0)->count();

        return match (true) {
            $liveDomainCount === 0 => 'map-first parity planning',
            $liveDomainCount < 5 => 'parity staging in progress',
            default => 'grounded parity planning',
        };
    }

    protected function foundationFocus(): string
    {
        $foundationTargets = [
            ['count' => Shop::query()->count(), 'label' => 'live shops'],
            ['count' => CardHolder::query()->count(), 'label' => 'live cardholders'],
            ['count' => Card::query()->count(), 'label' => 'live cards'],
            ['count' => CardType::query()->count(), 'label' => 'live card types'],
            ['count' => Role::query()->count(), 'label' => 'live roles'],
            ['count' => Permission::query()->count(), 'label' => 'live permissions'],
        ];

        $firstMissingTarget = collect($foundationTargets)->first(fn (array $target): bool => $target['count'] === 0);

        if (is_array($firstMissingTarget) && isset($firstMissingTarget['label'])) {
            return sprintf('stabilize %s next', $firstMissingTarget['label']);
        }

        return 'all first-pass foundation surfaces are visible';
    }

    protected function foundationPosture(): string
    {
        $visibleFoundationCount = collect([
            Shop::query()->count(),
            CardHolder::query()->count(),
            Card::query()->count(),
            CardType::query()->count(),
            Role::query()->count(),
            Permission::query()->count(),
        ])->filter(fn (int $count): bool => $count > 0)->count();

        return match (true) {
            $visibleFoundationCount === 0 => 'setup-first foundation baseline',
            $visibleFoundationCount < 6 => 'partial foundation baseline',
            default => 'fully visible foundation baseline',
        };
    }

    protected function liveDomainCoverage(): string
    {
        $liveDomainCount = collect([
            Shop::query()->count(),
            CardHolder::query()->count(),
            Card::query()->count(),
            Role::query()->count(),
            Permission::query()->count(),
        ])->filter(fn (int $count): bool => $count > 0)->count();

        return sprintf('%d/5 core Galaxy domains live', $liveDomainCount);
    }

    protected function foundationReadinessSignal(): string
    {
        $liveDomainCount = collect([
            Shop::query()->count(),
            CardHolder::query()->count(),
            Card::query()->count(),
            Role::query()->count(),
            Permission::query()->count(),
        ])->filter(fn (int $count): bool => $count > 0)->count();

        return match (true) {
            $liveDomainCount === 5 => 'review-ready foundation',
            $liveDomainCount > 0 => 'foundation coverage in progress',
            default => 'foundation setup stage',
        };
    }

    protected function activeFoundationCoverage(): string
    {
        $shopCount = Shop::query()->count();
        $activeShopCount = Shop::query()->where('is_active', true)->count();
        $cardHolderCount = CardHolder::query()->count();
        $activeCardHolderCount = CardHolder::query()->where('is_active', true)->count();
        $cardCount = Card::query()->count();
        $activeCardCount = Card::query()->where('status', 'active')->count();

        $coverageParts = [
            sprintf('shops %d/%d active', $activeShopCount, $shopCount),
            sprintf('cardholders %d/%d active', $activeCardHolderCount, $cardHolderCount),
            sprintf('cards %d/%d active', $activeCardCount, $cardCount),
        ];

        return implode(', ', $coverageParts);
    }

    protected function branchPauseCoverage(): string
    {
        $shopCount = Shop::query()->count();
        $pausedShopCount = Shop::query()->where('is_active', false)->count();

        return sprintf('%d/%d branches paused', $pausedShopCount, $shopCount);
    }

    protected function accessBaselineCoverage(): string
    {
        $roleCount = Role::query()->count();
        $permissionCount = Permission::query()->count();

        return sprintf('%d roles, %d permissions visible', $roleCount, $permissionCount);
    }

    protected function tierBaselineCoverage(): string
    {
        $cardTypeCount = CardType::query()->count();
        $activeCardTypeCount = CardType::query()->where('is_active', true)->count();

        return sprintf('%d/%d card types active', $activeCardTypeCount, $cardTypeCount);
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
        return $this->scopedLatestWorkspaceLabel(
            shop: $shop,
            reviewLabel: $this->scopedLatestReviewWorkspaceLabel(
                scopedLabel: 'Open latest branch review',
                defaultLabel: 'Open latest shop review',
            ),
            setupLabel: 'Open branch setup',
            emptyRelations: ['cardHolders', 'cards'],
        );
    }

    protected function latestCardHolderWorkspace(): ?array
    {
        $cardHolder = $this->latestAccessibleRecord(
            CardHolder::query()->with('shop')->latest('id')->get(),
            fn (CardHolder $cardHolder): ?Shop => $cardHolder->shop,
        );

        if (! $cardHolder) {
            return $this->scopedLatestSetupWorkspaceLink(
                relation: 'cardHolders',
                label: 'Open first cardholder setup in assigned branch',
                routeName: 'admin.cardholders.index',
            );
        }

        $status = $cardHolder->status ?? ($cardHolder->is_active ? 'active' : 'inactive');

        return $this->workspaceLink(
            label: sprintf('%s: %s (%s)', $this->latestCardHolderWorkspaceLabel(), $cardHolder->full_name, $status),
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
            label: sprintf('%s: %s (%s)', $this->latestCardWorkspaceLabel(), $card->number, $card->status),
            routeName: 'admin.cards.index',
            parameters: ['card' => $card->id],
        ) : $this->scopedLatestSetupWorkspaceLink(
            relation: 'cards',
            label: 'Open first card setup in assigned branch',
            routeName: 'admin.cards.index',
        );
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

    protected function latestCardHolderWorkspaceLabel(): string
    {
        return $this->scopedLatestReviewWorkspaceLabel(
            scopedLabel: 'Open latest branch cardholder review',
            defaultLabel: 'Open latest cardholder review',
        );
    }

    protected function latestCardWorkspaceLabel(): string
    {
        return $this->scopedLatestReviewWorkspaceLabel(
            scopedLabel: 'Open latest branch card review',
            defaultLabel: 'Open latest card review',
        );
    }

    protected function scopedLatestReviewWorkspaceLabel(string $scopedLabel, string $defaultLabel): string
    {
        return $this->isShopScopedAdmin()
            ? $scopedLabel
            : $defaultLabel;
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

    protected function scopedLatestSetupWorkspaceLink(string $relation, string $label, string $routeName): ?array
    {
        if (! $this->shouldShowScopedLatestSetupWorkspace($relation)) {
            return null;
        }

        return $this->workspaceLink($label, $routeName);
    }

    protected function shouldShowScopedLatestSetupWorkspace(string $relation): bool
    {
        $shop = $this->activeScopedShop();

        if (! $shop instanceof Shop) {
            return false;
        }

        return $this->shopHasNoRecords($shop, [$relation]);
    }

    protected function scopedLatestWorkspaceLabel(Shop $shop, string $reviewLabel, string $setupLabel, array $emptyRelations): string
    {
        if (! $this->isShopScopedAdmin() || ! $shop->is_active) {
            return $reviewLabel;
        }

        return $this->shopHasNoRecords($shop, $emptyRelations)
            ? $setupLabel
            : $reviewLabel;
    }

    protected function activeScopedShop(): ?Shop
    {
        if (! $this->isShopScopedAdmin()) {
            return null;
        }

        $shop = $this->adminUser()?->shop;

        return $shop instanceof Shop && $shop->is_active
            ? $shop
            : null;
    }

    protected function shopHasNoRecords(Shop $shop, array $relations): bool
    {
        $shop->loadCount($relations);

        foreach ($relations as $relation) {
            $countAttribute = $this->shopRelationCountAttribute($relation);

            if (! is_string($countAttribute) || ($shop->{$countAttribute} ?? 0) !== 0) {
                return false;
            }
        }

        return true;
    }

    protected function shopRelationCountAttribute(string $relation): ?string
    {
        return match ($relation) {
            'cardHolders' => 'card_holders_count',
            'cards' => 'cards_count',
            'users' => 'users_count',
            default => null,
        };
    }

    protected function liveReviewEntryPoints(): array
    {
        $shop = $this->activeScopedShop();

        if (! $shop instanceof Shop) {
            return [
                $this->workspaceLink('Review live Galaxy branches', 'admin.shops.index'),
                $this->workspaceLink('Review live cardholders', 'admin.cardholders.index'),
                $this->workspaceLink('Review live cards', 'admin.cards.index'),
                $this->workspaceLink('Review live Galaxy tiers', 'admin.card-types.index'),
                $this->workspaceLink('Review live Galaxy access shells', 'admin.roles-permissions.index'),
                $this->workspaceLink('Review Galaxy reporting sources', 'admin.reports.index'),
            ];
        }

        $primaryScopedShopEntryLabel = $this->scopedShopEntryLabel($shop);
        $primaryScopedCardholderEntryLabel = $this->scopedCardholderEntryLabel($shop);
        $primaryScopedCardEntryLabel = $this->scopedCardEntryLabel($shop);

        return [
            $this->workspaceLink($primaryScopedShopEntryLabel, 'admin.shops.index'),
            $this->workspaceLink($primaryScopedCardholderEntryLabel, 'admin.cardholders.index'),
            $this->workspaceLink($primaryScopedCardEntryLabel, 'admin.cards.index'),
            $this->scopedSharedLiveEntryPoint('Review shared Galaxy tiers', 'admin.card-types.index'),
            $this->scopedSharedLiveEntryPoint('Review shared Galaxy access shells', 'admin.roles-permissions.index'),
            $this->scopedSharedLiveEntryPoint('Review shared Galaxy reporting sources', 'admin.reports.index'),
        ];
    }

    protected function scopedSharedLiveEntryPoint(string $label, string $routeName): array
    {
        return $this->workspaceLink($label, $routeName);
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
        $shop = $this->activeScopedShop();

        if (! $shop instanceof Shop) {
            return null;
        }

        return [
            'label' => 'Current review scope',
            'value' => sprintf('Shop-scoped admin mode is active. Latest-work shortcuts and live review links should stay anchored to %s with branch-specific review wording while Phase 1 policies are still being mapped.', $shop->name),
        ];
    }

    protected function foundationHandoffSummary(): array
    {
        $shopCount = Shop::query()->count();
        $cardHolderCount = CardHolder::query()->count();
        $cardCount = Card::query()->count();
        $roleCount = Role::query()->count();
        $permissionCount = Permission::query()->count();

        return [
            'label' => 'Foundation handoff signal',
            'value' => match (true) {
                $shopCount === 0 && $cardHolderCount === 0 && $cardCount === 0 && $roleCount === 0 && $permissionCount === 0
                    => 'Phase 1 is still in Galaxy foundation setup mode, so the dashboard should keep first live entities visible before any handoff review feels grounded.',
                $shopCount > 0 && $cardHolderCount > 0 && $cardCount > 0 && $roleCount > 0 && $permissionCount > 0
                    => 'The dashboard already shows enough live Galaxy entities to support a useful foundation handoff review.',
                default
                    => 'Some live Galaxy entities are visible, but the dashboard still needs broader Laravel coverage before foundation handoff review feels complete.',
            },
        ];
    }

    protected function assignedBranchSnapshot(): ?array
    {
        $shop = $this->activeScopedShop();

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
        $branchReadiness = $this->branchReadinessStatus($shop, $latestHolder, $latestCard);
        $branchCoverage = $this->branchCoverageStatus($shop);
        $branchHandoffSignal = $this->branchHandoffSignal($shop, $latestHolder, $latestCard);
        $suggestedFollowUp = $this->branchSuggestedFollowUp($shop, $latestHolder, $latestCard);
        $actions = $this->assignedBranchSnapshotActions($shop, $latestHolder, $latestCard);

        return [
            'label' => 'Assigned branch snapshot',
            'actionCoverage' => $this->assignedBranchActionCoverage($actions),
            'actionPosture' => $this->assignedBranchActionPosture($shop, $latestHolder, $latestCard),
            'actionFocus' => $this->assignedBranchActionFocus($shop, $latestHolder, $latestCard),
            'items' => [
                ['label' => 'Branch', 'value' => $shop->name],
                ['label' => 'Branch code', 'value' => $shop->code],
                ['label' => 'Branch posture', 'value' => $branchPosture],
                ['label' => 'Branch readiness', 'value' => $branchReadiness],
                ['label' => 'Branch coverage', 'value' => $branchCoverage],
                ['label' => 'Handoff signal', 'value' => $branchHandoffSignal],
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

    protected function assignedBranchActionCoverage(array $actions): string
    {
        return sprintf('%d scoped branch actions ready', count($actions));
    }

    protected function assignedBranchActionPosture(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $shop->is_active) {
            return 'paused branch actions only';
        }

        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'setup-first shortcuts only';
        }

        if (! $latestHolder instanceof CardHolder || ! $latestCard instanceof Card) {
            return 'mixed setup and review shortcuts';
        }

        return 'review-ready scoped shortcuts';
    }

    protected function assignedBranchActionFocus(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $shop->is_active) {
            return 'recovery review only';
        }

        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'branch setup first';
        }

        if (! $latestHolder instanceof CardHolder) {
            return 'cardholder backfill next';
        }

        if (! $latestCard instanceof Card) {
            return 'card issuance next';
        }

        return 'latest branch review ready';
    }

    protected function latestBranchActivitySummary(?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'Setup pending';
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
        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'setup stage';
        }

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

        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'active branch, setup pending';
        }

        return 'active branch, live activity visible';
    }

    protected function branchReadinessStatus(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $shop->is_active) {
            return 'paused';
        }

        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'setup pending';
        }

        if (! $latestHolder instanceof CardHolder || ! $latestCard instanceof Card) {
            return 'setup in progress';
        }

        return 'review-ready';
    }

    protected function branchCoverageStatus(Shop $shop): string
    {
        $hasCardholders = $shop->card_holders_count > 0;
        $hasCards = $shop->cards_count > 0;

        return match (true) {
            $hasCardholders && $hasCards => 'cardholders and cards live',
            $hasCardholders => 'cardholders live, cards pending',
            $hasCards => 'cards live, cardholders pending',
            default => 'core branch records pending',
        };
    }

    protected function branchHandoffSignal(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $shop->is_active) {
            return 'Paused branch should stay in handoff-only posture until reopen intent is explicit.';
        }

        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'Assigned branch still needs first live records before handoff review can feel grounded.';
        }

        if (! $latestHolder instanceof CardHolder) {
            return 'Card activity is visible, but holder coverage still needs to catch up before full branch handoff review.';
        }

        if (! $latestCard instanceof Card) {
            return 'Holder activity is visible, but card coverage still needs to catch up before full branch handoff review.';
        }

        return 'Assigned branch already carries enough live coverage for a useful scoped handoff review.';
    }

    protected function branchSuggestedFollowUp(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $shop->is_active) {
            return 'Confirm pause reason before reopening branch work.';
        }

        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'Open assigned branch setup and create the first live records.';
        }

        if (! $latestHolder instanceof CardHolder) {
            return 'Review assigned branch cards and backfill the first visible cardholder record.';
        }

        if (! $latestCard instanceof Card) {
            return 'Open assigned branch card setup and issue the first live card.';
        }

        return 'Resume the latest branch review flow from the scoped shortcuts.';
    }

    protected function branchSetupPending(?CardHolder $latestHolder, ?Card $latestCard): bool
    {
        return ! $latestHolder instanceof CardHolder && ! $latestCard instanceof Card;
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
        return $this->branchSetupPending($latestHolder, $latestCard)
            ? 'Open assigned branch setup'
            : 'Open assigned branch review';
    }

    protected function scopedShopEntryLabel(?Shop $shop): string
    {
        return $this->scopedEntryLabel(
            shop: $shop,
            liveLabel: 'Review live shops in assigned branch',
            setupLabel: 'Set up assigned branch',
            countRelations: ['cardHolders', 'cards'],
            isSetupConditionMet: fn (Shop $shop): bool => $this->shopHasNoRecords($shop, ['cardHolders', 'cards']),
        );
    }

    protected function scopedCardholderEntryLabel(?Shop $shop): string
    {
        return $this->scopedEntryLabel(
            shop: $shop,
            liveLabel: 'Review live cardholders in assigned branch',
            setupLabel: 'Set up first cardholder in assigned branch',
            countRelations: ['cardHolders'],
            isSetupConditionMet: fn (Shop $shop): bool => $this->shopHasNoRecords($shop, ['cardHolders']),
        );
    }

    protected function scopedCardEntryLabel(?Shop $shop): string
    {
        return $this->scopedEntryLabel(
            shop: $shop,
            liveLabel: 'Review live cards in assigned branch',
            setupLabel: 'Set up first card in assigned branch',
            countRelations: ['cards'],
            isSetupConditionMet: fn (Shop $shop): bool => $this->shopHasNoRecords($shop, ['cards']),
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
            'value' => 'These entry points still open the shared Phase 1 workspaces, but shop-backed review inside shops, cardholders, and cards now narrows to the assigned branch with branch-specific review wording once the workspace loads.',
        ];
    }

    protected function liveEntryHandoffSummary(): array
    {
        $shop = $this->activeScopedShop();
        $shopCount = Shop::query()->count();
        $cardHolderCount = CardHolder::query()->count();
        $cardCount = Card::query()->count();

        return [
            'label' => 'Entry handoff signal',
            'value' => match (true) {
                $shop instanceof Shop && $shopCount > 0 && $cardHolderCount > 0 && $cardCount > 0
                    => 'Assigned-branch entry points already have enough live shop, holder, and card coverage to support a useful scoped handoff review.',
                $shop instanceof Shop
                    => 'Assigned-branch entry points should stay setup-aware until the branch shows live shop, holder, and card coverage together.',
                $shopCount > 0 && $cardHolderCount > 0 && $cardCount > 0
                    => 'Shared entry points already have enough live branch, holder, and card coverage to support a useful foundation handoff review.',
                default
                    => 'Entry points should stay setup-first until live branch, holder, and card coverage is visible across the Laravel foundation.',
            },
        ];
    }

    protected function latestWorkspaceScopeNote(): ?array
    {
        if (! $this->isShopScopedAdmin()) {
            return null;
        }

        return [
            'label' => 'Phase 1 scope note',
            'value' => 'Latest-work shortcuts for shops, cardholders, and cards now follow branch scope and branch-specific review wording. Card types, roles, and reporting remain shared review surfaces until deeper shop-aware policies arrive.',
        ];
    }

    protected function latestWorkspaceHandoffSummary(): array
    {
        $shop = $this->activeScopedShop();
        $latestWorkspaceCount = count(array_values(array_filter([
            $this->latestShopWorkspace(),
            $this->latestCardHolderWorkspace(),
            $this->latestCardWorkspace(),
            $this->latestCardTypeWorkspace(),
            $this->latestRoleWorkspace(),
        ])));

        return [
            'label' => 'Latest-work handoff signal',
            'value' => match (true) {
                $shop instanceof Shop && $latestWorkspaceCount >= 3
                    => 'Scoped latest-work shortcuts already carry enough branch-backed coverage for a useful handoff review jump-back.',
                $shop instanceof Shop
                    => 'Scoped latest-work shortcuts should stay setup-aware until more branch-backed workspaces are populated.',
                $latestWorkspaceCount >= 3
                    => 'Latest-work shortcuts already carry enough live Galaxy coverage for a useful handoff review jump-back.',
                default
                    => 'Latest-work shortcuts should stay setup-first until more live Galaxy workspaces are available to resume.',
            },
        ];
    }

    protected function migrationMapHandoffSummary(array $navigation): array
    {
        $plannedSectionCount = collect($navigation)->sum(fn (array $group): int => count($group['items']));
        $mappedGroupCount = count($navigation);
        $liveDomainCount = collect([
            Shop::query()->count(),
            CardHolder::query()->count(),
            Card::query()->count(),
            Role::query()->count(),
            Permission::query()->count(),
        ])->filter(fn (int $count): bool => $count > 0)->count();

        return [
            'label' => 'Migration-map handoff signal',
            'value' => match (true) {
                $liveDomainCount >= 5
                    => sprintf('The migration map already spans %d grouped sections with live coverage in %d core Galaxy domains, so parity handoff planning can stay grounded in the current Laravel shell.', $mappedGroupCount, $liveDomainCount),
                $liveDomainCount > 0
                    => sprintf('The migration map already spans %d grouped sections, but only %d core Galaxy domains have live Laravel coverage so far.', $mappedGroupCount, $liveDomainCount),
                default
                    => sprintf('The migration map already spans %d grouped sections and %d planned surfaces, but handoff planning should stay map-first until live Galaxy domains start landing in Laravel.', $mappedGroupCount, $plannedSectionCount),
            },
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
