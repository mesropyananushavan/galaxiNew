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
use Illuminate\Database\Eloquent\Model;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $navigation = config('admin-navigation');

        return view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'navigationGroups' => $navigation,
            'phaseOneDomainMap' => config('phase-1-domain-map.entities', []),
            'phaseOneDomainFocus' => (string) config('phase-1-domain-map.focus', 'Keep the first Galaxy foundation entities explicit while Phase 1 work is still landing.'),
            'phaseOneDomainGuide' => config('phase-1-domain-map.guide', ['docs/phase-1-domain-map.md', 'config/phase-1-domain-map.php']),
            'phaseOneDomainSourceOfTruth' => config('phase-1-domain-map.source_of_truth', ['docs/phase-1-domain-map.md', 'config/phase-1-domain-map.php']),
            'phaseOneDomainPosture' => (string) config('phase-1-domain-map.posture', 'documented entity baseline for live foundation work'),
            'phaseOneDomainInventory' => $this->phaseOneDomainInventory(),
            'phaseOneReferenceDocs' => config('phase-1-reference-docs.items', []),
            'phaseOneReferenceDocsFocus' => (string) config('phase-1-reference-docs.focus', 'Keep the current Galaxy admin map, shell layering, checkpoint trail, and seam-source baseline close while Phase 1 slices are still moving.'),
            'phaseOneReferenceDocsGuide' => config('phase-1-reference-docs.guide', ['README.md', 'docs/blueprint.md', 'docs/phase-1-plan.md']),
            'phaseOneReferenceDocsSourceOfTruth' => config('phase-1-reference-docs.source_of_truth', ['README.md', 'docs/blueprint.md', 'docs/phase-1-plan.md', 'config/phase-1-reference-docs.php']),
            'phaseOneReferenceDocsPosture' => (string) config('phase-1-reference-docs.posture', 'admin reference inventory stays explicit across the live Galaxy dashboard trail'),
            'phaseOneReferenceDocsCoverage' => $this->phaseOneReferenceDocsCoverage(),
            'phaseOneSeamSources' => config('phase-1-seam-sources.items', []),
            'phaseOneSeamSourcesFocus' => (string) config('phase-1-seam-sources.focus', 'Keep the README-level seam-source inventory visible inside the admin workspace, so contributors can trace which small config seams are currently carrying the Galaxy-specific Phase 1 foundation.'),
            'phaseOneSeamSourcesGuide' => config('phase-1-seam-sources.guide', ['README.md', 'config/phase-1-seam-sources.php']),
            'phaseOneSeamSourcesSourceOfTruth' => config('phase-1-seam-sources.source_of_truth', ['README.md', 'config/phase-1-seam-sources.php']),
            'phaseOneSeamSourcesPosture' => (string) config('phase-1-seam-sources.posture', 'README-backed seam-source baseline stays explicit across the live Galaxy reference trail'),
            'phaseOneSeamSourcesCoverage' => $this->phaseOneSeamSourcesCoverage(),
            'phaseOneFoundationSeams' => config('phase-1-foundation-seams.items', []),
            'phaseOneFoundationSeamsFocus' => (string) config('phase-1-foundation-seams.focus', 'Keep the new Galaxy-specific Phase 1 seams visible where contributors review the live foundation shell.'),
            'phaseOneFoundationSeamsGuide' => config('phase-1-foundation-seams.guide', ['docs/phase-1-foundation-seams.md', 'config/phase-1-foundation-seams.php']),
            'phaseOneFoundationSeamsSourceOfTruth' => config('phase-1-foundation-seams.source_of_truth', ['docs/phase-1-foundation-seams.md', 'config/phase-1-foundation-seams.php']),
            'phaseOneFoundationSeamsPosture' => (string) config('phase-1-foundation-seams.posture', 'small config-backed and doc-backed foundation seams stay explicit'),
            'phaseOneFoundationSeamsCoverage' => $this->phaseOneFoundationSeamsCoverage(),
            'plannedSectionCount' => collect($navigation)->sum(fn (array $group): int => count($group['items'])),
            'liveDomainCoverage' => $this->liveDomainCoverage(),
            'foundationFocus' => $this->foundationFocus(),
            'foundationPosture' => $this->foundationPosture(),
            'foundationReadinessSignal' => $this->foundationReadinessSignal(),
            'activeFoundationCoverage' => $this->activeFoundationCoverage(),
            'branchPauseCoverage' => $this->branchPauseCoverage(),
            'accessBaselineCoverage' => $this->accessBaselineCoverage(),
            'tierBaselineCoverage' => $this->tierBaselineCoverage(),
            'shopCount' => $this->savedShopCount(),
            'activeShopCount' => $this->activeShopCount(),
            'cardHolderCount' => $this->savedCardHolderCount(),
            'activeCardHolderCount' => $this->activeCardHolderCount(),
            'cardCount' => $this->savedCardCount(),
            'activeCardCount' => $this->activeCardCount(),
            'cardTypeCount' => $this->savedCardTypeCount(),
            'activeCardTypeCount' => $this->activeCardTypeCount(),
            'roleCount' => $this->savedRoleCount(),
            'permissionCount' => $this->savedPermissionCount(),
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
            'phaseOneDomainCoverage' => $this->phaseOneDomainCoverage(),
            'liveReviewEntryPoints' => $this->liveReviewEntryPoints(),
            'liveEntryPointCoverage' => $this->liveEntryPointCoverage(),
            'liveEntryPointFocus' => $this->liveEntryPointFocus(),
            'liveEntryPointPosture' => $this->liveEntryPointPosture(),
            'latestWorkspaceCoverage' => $this->latestWorkspaceCoverage(),
            'latestWorkspaceFocus' => $this->latestWorkspaceFocus(),
            'latestWorkspacePosture' => $this->latestWorkspacePosture(),
            'latestWorkspaces' => $this->latestWorkspaces(),
        ]);
    }

    protected function liveEntryPointCoverage(): string
    {
        return sprintf('%d live review entry points staged', $this->liveReviewEntryPointCount());
    }

    protected function liveReviewEntryPointCount(): int
    {
        return $this->countItems($this->liveReviewEntryPoints());
    }

    protected function phaseOneDomainCoverage(): string
    {
        return sprintf('%d/%d Phase 1 entities already have live Galaxy records', $this->livePhaseOneEntityCount(), $this->mappedPhaseOneEntityCount());
    }

    protected function phaseOneDomainInventory(): string
    {
        return sprintf('%d Phase 1 entities currently mapped', $this->mappedPhaseOneEntityCount());
    }

    protected function phaseOneFoundationSeamsCoverage(): string
    {
        return sprintf('%d Phase 1 foundation seams currently tracked', $this->foundationSeamCount());
    }

    protected function domainEntities()
    {
        return collect(config('phase-1-domain-map.entities', []));
    }

    protected function mappedPhaseOneEntityCount(): int
    {
        return $this->countItems($this->domainEntities());
    }

    protected function livePhaseOneEntityCount(): int
    {
        return $this->countItems(
            $this->domainEntities()
                ->filter(fn (array $entity): bool => $this->phaseOneEntityHasLiveRecords($entity))
        );
    }

    protected function phaseOneEntityHasLiveRecords(array $entity): bool
    {
        $model = $entity['model'] ?? null;

        if (! filled($model) || ! class_exists($model)) {
            return false;
        }

        return $this->modelClassCount($model) > 0;
    }

    protected function modelClassCount(string $modelClass): int
    {
        return (int) $modelClass::query()->count();
    }

    protected function scopedModelCount(string $modelClass, string $scope): int
    {
        return (int) $modelClass::query()->{$scope}()->count();
    }

    protected function foundationSeams()
    {
        return collect(config('phase-1-foundation-seams.items', []));
    }

    protected function foundationSeamCount(): int
    {
        return $this->countConfigItems($this->foundationSeams());
    }

    protected function phaseOneReferenceDocsCoverage(): string
    {
        return sprintf('%d Phase 1 reference docs currently linked', $this->referenceDocCount());
    }

    protected function referenceDocs()
    {
        return collect(config('phase-1-reference-docs.items', []));
    }

    protected function referenceDocCount(): int
    {
        return $this->countConfigItems($this->referenceDocs());
    }

    protected function phaseOneSeamSourcesCoverage(): string
    {
        return sprintf('%d README-level seam sources currently tracked', $this->seamSourceCount());
    }

    protected function seamSources()
    {
        return collect(config('phase-1-seam-sources.items', []));
    }

    protected function seamSourceCount(): int
    {
        return $this->countConfigItems($this->seamSources());
    }

    protected function countConfigItems($items): int
    {
        return $this->countItems($items);
    }

    protected function countItems($items): int
    {
        return is_array($items) ? count($items) : $items->count();
    }

    protected function firstItem($items): mixed
    {
        return is_array($items) ? ($items[0] ?? null) : $items->first();
    }

    protected function liveEntryPointFocus(): string
    {
        $entryPoint = $this->firstLiveReviewEntryPoint();

        if (! is_array($entryPoint) || ! isset($entryPoint['label'])) {
            return 'first live Galaxy review surface still needs to be staged';
        }

        return sprintf('start with %s', mb_strtolower((string) $entryPoint['label']));
    }

    protected function firstLiveReviewEntryPoint(): ?array
    {
        $entryPoint = $this->firstItem($this->liveReviewEntryPoints());

        return is_array($entryPoint) ? $entryPoint : null;
    }

    protected function liveEntryPointPosture(): string
    {
        $liveEntryDomainCount = $this->liveEntryDomainCount();

        return match (true) {
            $liveEntryDomainCount === 0 => 'setup-first staged entry surfaces',
            $liveEntryDomainCount < 3 => 'partial staged entry coverage',
            default => 'review-ready staged entry surfaces',
        };
    }

    protected function latestWorkspaceCoverage(): string
    {
        $latestWorkspaceCount = $this->latestWorkspaceCount();

        return sprintf('%d latest-work shortcuts currently available', $latestWorkspaceCount);
    }

    protected function latestWorkspaceFocus(): string
    {
        $latestWorkspace = $this->firstLatestWorkspace();

        if (! is_array($latestWorkspace) || ! isset($latestWorkspace['label'])) {
            return 'first live Galaxy workspace still needs to be created';
        }

        return sprintf('start with %s', mb_strtolower((string) $latestWorkspace['label']));
    }

    protected function latestWorkspacePosture(): string
    {
        $latestWorkspaceCount = $this->latestWorkspaceCount();

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
        $liveDomainCount = $this->liveCoreDomainCount();

        return match (true) {
            $liveDomainCount === 0 => 'map-first parity planning',
            $liveDomainCount < 5 => 'parity staging in progress',
            default => 'grounded parity planning',
        };
    }

    protected function foundationFocus(): string
    {
        $firstMissingTarget = collect($this->foundationTargets())->first(fn (array $target): bool => $target['count'] === 0);

        if (is_array($firstMissingTarget) && isset($firstMissingTarget['label'])) {
            return sprintf('stabilize %s next', $firstMissingTarget['label']);
        }

        return 'all first-pass foundation surfaces are visible';
    }

    protected function foundationPosture(): string
    {
        $visibleFoundationCount = $this->liveFoundationSurfaceCount();

        return match (true) {
            $visibleFoundationCount === 0 => 'setup-first foundation baseline',
            $visibleFoundationCount < 6 => 'partial foundation baseline',
            default => 'fully visible foundation baseline',
        };
    }

    protected function liveDomainCoverage(): string
    {
        $liveDomainCount = $this->liveCoreDomainCount();

        return sprintf('%d/5 core Galaxy domains live', $liveDomainCount);
    }

    protected function foundationReadinessSignal(): string
    {
        $liveDomainCount = $this->liveCoreDomainCount();

        return match (true) {
            $liveDomainCount === 5 => 'review-ready foundation',
            $liveDomainCount > 0 => 'foundation coverage in progress',
            default => 'foundation setup stage',
        };
    }

    protected function activeFoundationCoverage(): string
    {
        $shopCount = $this->savedShopCount();
        $activeShopCount = $this->activeShopCount();
        $cardHolderCount = $this->savedCardHolderCount();
        $activeCardHolderCount = $this->activeCardHolderCount();
        $cardCount = $this->savedCardCount();
        $activeCardCount = $this->activeCardCount();

        $coverageParts = [
            sprintf('Galaxy branches %d/%d active', $activeShopCount, $shopCount),
            sprintf('Galaxy holders %d/%d active', $activeCardHolderCount, $cardHolderCount),
            sprintf('Galaxy card shells %d/%d active', $activeCardCount, $cardCount),
        ];

        return implode(', ', $coverageParts);
    }

    protected function branchPauseCoverage(): string
    {
        $shopCount = $this->savedShopCount();
        $pausedShopCount = $this->pausedShopCount();

        return sprintf('%d/%d branches paused', $pausedShopCount, $shopCount);
    }

    protected function accessBaselineCoverage(): string
    {
        [$roleTarget, $permissionTarget] = $this->visibleAccessTargets();

        return sprintf('%d %s, %d %s visible', $roleTarget['count'], $roleTarget['label'], $permissionTarget['count'], $permissionTarget['label']);
    }

    protected function permissionBearingRoleCount(): int
    {
        return $this->scopedModelCount(Role::class, 'permissionBearing');
    }

    protected function savedShopCount(): int
    {
        return $this->modelClassCount(Shop::class);
    }

    protected function activeShopCount(): int
    {
        return $this->scopedModelCount(Shop::class, 'active');
    }

    protected function pausedShopCount(): int
    {
        return $this->scopedModelCount(Shop::class, 'paused');
    }

    protected function savedRoleCount(): int
    {
        return $this->modelClassCount(Role::class);
    }

    protected function savedPermissionCount(): int
    {
        return $this->modelClassCount(Permission::class);
    }

    protected function savedCardHolderCount(): int
    {
        return $this->modelClassCount(CardHolder::class);
    }

    protected function activeCardHolderCount(): int
    {
        return $this->scopedModelCount(CardHolder::class, 'active');
    }

    protected function savedCardCount(): int
    {
        return $this->modelClassCount(Card::class);
    }

    protected function activeCardCount(): int
    {
        return $this->scopedModelCount(Card::class, 'active');
    }

    protected function savedCardTypeCount(): int
    {
        return $this->modelClassCount(CardType::class);
    }

    protected function activeCardTypeCount(): int
    {
        return $this->scopedModelCount(CardType::class, 'active');
    }

    protected function liveCoreDomainCount(): int
    {
        return $this->countVisibleTargets($this->liveCoreTargets());
    }

    protected function liveEntryDomainCount(): int
    {
        return $this->countVisibleTargets($this->liveEntryTargets());
    }

    protected function liveFoundationSurfaceCount(): int
    {
        return $this->countVisibleTargets($this->foundationTargets());
    }

    protected function foundationTargets(): array
    {
        return [
            ...$this->liveEntryTargets(),
            ['count' => $this->savedCardTypeCount(), 'label' => 'live Galaxy tiers'],
            ...$this->liveAccessTargets(),
        ];
    }

    protected function liveCoreTargets(): array
    {
        return [
            ...$this->liveEntryTargets(),
            ...$this->liveAccessTargets(),
        ];
    }

    protected function liveEntryTargets(): array
    {
        return [
            ['count' => $this->savedShopCount(), 'label' => 'live Galaxy branches'],
            ['count' => $this->savedCardHolderCount(), 'label' => 'live Galaxy holders'],
            ['count' => $this->savedCardCount(), 'label' => 'live Galaxy card shells'],
        ];
    }

    protected function liveAccessTargets(): array
    {
        return [
            ['count' => $this->savedRoleCount(), 'label' => 'live Galaxy access shells'],
            ['count' => $this->savedPermissionCount(), 'label' => 'live access permissions'],
        ];
    }

    protected function countVisibleTargets(array $targets): int
    {
        return $this->countItems(
            collect($this->targetCounts($targets))
                ->filter(fn (int $count): bool => $count > 0)
        );
    }

    protected function targetCounts(array $targets): array
    {
        return collect($targets)
            ->pluck('count')
            ->all();
    }

    protected function visibleAccessTargets(): array
    {
        return [
            ['count' => $this->permissionBearingRoleCount(), 'label' => 'Galaxy access shells'],
            ['count' => $this->assignedPermissionCount(), 'label' => 'access permissions'],
        ];
    }

    protected function assignedPermissionCount(): int
    {
        return $this->scopedModelCount(Permission::class, 'assignedToRoles');
    }

    protected function tierBaselineCoverage(): string
    {
        $cardTypeCount = $this->savedCardTypeCount();
        $activeCardTypeCount = $this->activeCardTypeCount();

        return sprintf('%d/%d Galaxy tiers active', $activeCardTypeCount, $cardTypeCount);
    }

    protected function latestShopWorkspace(): ?array
    {
        $shop = $this->latestAccessibleRecord(
            Shop::query()->latest('id')->get(),
            fn (Shop $shop): ?Shop => $shop,
        );

        return $shop ? $this->workspaceLink(
            label: sprintf('%s: %s (%s)', $this->latestShopWorkspaceLabel($shop), $shop->name, $this->shopWorkspaceStatusValue($shop)),
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
                defaultLabel: 'Open latest branch review',
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
                label: 'Open first Galaxy holder setup in assigned branch',
                routeName: 'admin.cardholders.index',
            );
        }

        $status = $cardHolder->status ?? $this->cardHolderStatusValue($cardHolder);

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
            label: 'Open first Galaxy card shell setup in assigned branch',
            routeName: 'admin.cards.index',
        );
    }

    protected function latestCardTypeWorkspace(): ?array
    {
        $cardType = $this->latestSavedCardType();

        return $cardType ? $this->workspaceLink(
            label: sprintf('Open latest Galaxy tier shell review: %s (%s)', $cardType->name, $this->cardTypeStatusValue($cardType)),
            routeName: 'admin.card-types.index',
            parameters: ['cardType' => $cardType->id],
        ) : null;
    }

    protected function latestCardHolderWorkspaceLabel(): string
    {
        return $this->scopedLatestReviewWorkspaceLabel(
            scopedLabel: 'Open latest branch holder review',
            defaultLabel: 'Open latest Galaxy holder review',
        );
    }

    protected function latestCardWorkspaceLabel(): string
    {
        return $this->scopedLatestReviewWorkspaceLabel(
            scopedLabel: 'Open latest branch card shell review',
            defaultLabel: 'Open latest Galaxy card shell review',
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
        $role = $this->latestSavedRole();

        return $role ? $this->workspaceLink(
            label: sprintf('Open latest Galaxy access shell review: %s (%d permissions)', $role->name, $this->rolePermissionCount($role)),
            routeName: 'admin.roles-permissions.index',
            parameters: ['role' => $role->id],
        ) : null;
    }

    protected function latestSavedCardType(): ?CardType
    {
        $cardType = CardType::query()->latest('id')->first();

        return $cardType instanceof CardType ? $cardType : null;
    }

    protected function latestSavedRole(): ?Role
    {
        $role = Role::query()->latest('id')->first();

        return $role instanceof Role ? $role : null;
    }

    protected function rolePermissionCount(Role $role): int
    {
        return $this->roleRelationCount($role, 'permissions');
    }

    protected function roleRelationCount(Role $role, string $relation): int
    {
        return $this->modelRelationCount($role, $relation, $this->roleRelationCountAttribute($relation));
    }

    protected function roleRelationCountAttribute(string $relation): ?string
    {
        return match ($relation) {
            'permissions' => 'permissions_count',
            default => null,
        };
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
        if (! $this->isShopScopedAdmin() || ! $this->shopIsActive($shop)) {
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

        return $shop instanceof Shop && $this->shopIsActive($shop)
            ? $shop
            : null;
    }

    protected function shopHasNoRecords(Shop $shop, array $relations): bool
    {
        $shop->loadCount($relations);

        foreach ($relations as $relation) {
            if ($this->shopRelationCount($shop, $relation) !== 0) {
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

    protected function shopRelationCount(Shop $shop, string $relation): int
    {
        return $this->modelRelationCount($shop, $relation, $this->shopRelationCountAttribute($relation));
    }

    protected function modelRelationCount(Model $model, string $relation, ?string $countAttribute): int
    {
        if (! is_string($countAttribute)) {
            return 0;
        }

        return (int) ($model->{$countAttribute} ?? $model->{$relation}->count());
    }

    protected function liveReviewEntryPoints(): array
    {
        $shop = $this->activeScopedShop();

        if (! $shop instanceof Shop) {
            return [
                $this->workspaceLink('Review live Galaxy branches', 'admin.shops.index'),
                $this->workspaceLink('Review live Galaxy holders', 'admin.cardholders.index'),
                $this->workspaceLink('Review live Galaxy card shells', 'admin.cards.index'),
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
        $liveDomainCount = $this->liveCoreDomainCount();

        return [
            'label' => 'Foundation handoff signal',
            'value' => match (true) {
                $liveDomainCount === 0
                    => 'Phase 1 is still in Galaxy foundation setup mode, so the dashboard should keep first live entities visible before any handoff review feels grounded.',
                $liveDomainCount === 5
                    => 'The dashboard already shows enough live Galaxy entities to support a useful foundation handoff review.',
                default
                    => 'Some live Galaxy entities are visible, but the dashboard still needs broader Galaxy foundation coverage before foundation handoff review feels complete.',
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

        $latestHolder = $this->latestShopCardHolder($shop);

        $latestCard = $this->latestShopCard($shop);

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
                ['label' => 'Primary manager', 'value' => $this->shopPrimaryManagerName($shop)],
                ['label' => 'Galaxy foundation status', 'value' => $this->shopStatusValue($shop)],
                ['label' => 'Visible Galaxy holders', 'value' => (string) $this->shopVisibleHolderCount($shop)],
                ['label' => 'Visible Galaxy card shells', 'value' => (string) $this->shopVisibleCardCount($shop)],
                ['label' => 'Assigned staff', 'value' => (string) $this->shopAssignedStaffCount($shop)],
                ['label' => 'Latest Galaxy holder', 'value' => $latestHolder instanceof CardHolder ? $latestHolder->full_name : 'No Galaxy holders in assigned branch yet'],
                ['label' => 'Latest Galaxy holder status', 'value' => $latestHolder instanceof CardHolder ? $this->cardHolderStatusValue($latestHolder) : 'n/a'],
                ['label' => 'Latest Galaxy holder added', 'value' => $latestHolder instanceof CardHolder ? $latestHolder->created_at?->toDateString() ?? 'unknown' : 'n/a'],
                ['label' => 'Latest Galaxy card shell', 'value' => $latestCard instanceof Card ? $latestCard->number : 'No Galaxy card shells in assigned branch yet'],
                ['label' => 'Latest Galaxy card shell status', 'value' => $latestCard instanceof Card ? $latestCard->status : 'n/a'],
                ['label' => 'Latest Galaxy card shell issued', 'value' => $latestCard instanceof Card ? $latestCard->created_at?->toDateString() ?? 'unknown' : 'n/a'],
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
        if (! $this->shopIsActive($shop)) {
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
        if (! $this->shopIsActive($shop)) {
            return 'recovery review only';
        }

        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'branch setup first';
        }

        if (! $latestHolder instanceof CardHolder) {
            return 'Galaxy holder backfill next';
        }

        if (! $latestCard instanceof Card) {
            return 'Galaxy card issuance next';
        }

        return 'latest branch review ready';
    }

    protected function latestBranchActivitySummary(?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'Setup pending';
        }

        if (! $latestCard instanceof Card) {
            return 'Galaxy holder added';
        }

        if (! $latestHolder instanceof CardHolder) {
            return 'Galaxy card shell issued';
        }

        $holderCreatedAt = $latestHolder->created_at;
        $cardCreatedAt = $latestCard->created_at;

        if ($holderCreatedAt === null && $cardCreatedAt === null) {
            return 'Latest branch record updated';
        }

        if ($holderCreatedAt === null) {
            return 'Galaxy card shell issued';
        }

        if ($cardCreatedAt === null) {
            return 'Galaxy holder added';
        }

        return $cardCreatedAt->greaterThanOrEqualTo($holderCreatedAt)
            ? 'Galaxy card shell issued'
            : 'Galaxy holder added';
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

    protected function cardTypeIsActive(CardType $cardType): bool
    {
        return (bool) $cardType->is_active;
    }

    protected function cardTypeStatusValue(CardType $cardType): string
    {
        return $this->cardTypeIsActive($cardType) ? 'active' : 'draft';
    }

    protected function cardHolderIsActive(CardHolder $cardHolder): bool
    {
        return (bool) $cardHolder->is_active;
    }

    protected function cardHolderStatusValue(CardHolder $cardHolder): string
    {
        return $this->cardHolderIsActive($cardHolder) ? 'active' : 'inactive';
    }

    protected function shopVisibleHolderCount(Shop $shop): int
    {
        return $this->shopRelationCount($shop, 'cardHolders');
    }

    protected function shopVisibleCardCount(Shop $shop): int
    {
        return $this->shopRelationCount($shop, 'cards');
    }

    protected function shopAssignedStaffCount(Shop $shop): int
    {
        return $this->shopRelationCount($shop, 'users');
    }

    protected function shopPrimaryManagerName(Shop $shop): string
    {
        return $shop->users->first()?->name ?? 'Unassigned';
    }

    protected function latestShopCardHolder(Shop $shop): ?CardHolder
    {
        $cardHolder = CardHolder::query()
            ->where('shop_id', $shop->id)
            ->latest('id')
            ->first();

        return $cardHolder instanceof CardHolder ? $cardHolder : null;
    }

    protected function latestShopCard(Shop $shop): ?Card
    {
        $card = Card::query()
            ->where('shop_id', $shop->id)
            ->latest('id')
            ->first();

        return $card instanceof Card ? $card : null;
    }

    protected function shopIsActive(Shop $shop): bool
    {
        return (bool) $shop->is_active;
    }

    protected function shopStatusValue(Shop $shop): string
    {
        return $this->shopIsActive($shop) ? 'active' : 'paused';
    }

    protected function shopWorkspaceStatusValue(Shop $shop): string
    {
        return $this->shopIsActive($shop) ? 'active' : 'inactive';
    }

    protected function branchOperationalPosture(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $this->shopIsActive($shop)) {
            return 'paused branch';
        }

        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'active branch, setup pending';
        }

        return 'active branch, live activity visible';
    }

    protected function branchReadinessStatus(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $this->shopIsActive($shop)) {
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
        $hasCardholders = $this->shopVisibleHolderCount($shop) > 0;
        $hasCards = $this->shopVisibleCardCount($shop) > 0;

        return match (true) {
            $hasCardholders && $hasCards => 'Galaxy holders and card shells live',
            $hasCardholders => 'Galaxy holders live, card shells pending',
            $hasCards => 'Galaxy card shells live, holders pending',
            default => 'core branch records pending',
        };
    }

    protected function branchHandoffSignal(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $this->shopIsActive($shop)) {
            return 'Paused branch should stay in handoff-only posture until reopen intent is explicit.';
        }

        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'Assigned branch still needs first live records before handoff review can feel grounded.';
        }

        if (! $latestHolder instanceof CardHolder) {
            return 'Galaxy card shell activity is visible, but Galaxy holder coverage still needs to catch up before full branch handoff review.';
        }

        if (! $latestCard instanceof Card) {
            return 'Galaxy holder activity is visible, but Galaxy card-shell coverage still needs to catch up before full branch handoff review.';
        }

        return 'Assigned branch already carries enough live coverage for a useful scoped handoff review.';
    }

    protected function branchSuggestedFollowUp(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): string
    {
        if (! $this->shopIsActive($shop)) {
            return 'Confirm pause reason before reopening branch work.';
        }

        if ($this->branchSetupPending($latestHolder, $latestCard)) {
            return 'Open assigned branch setup and create the first live records.';
        }

        if (! $latestHolder instanceof CardHolder) {
            return 'Review assigned branch Galaxy card shells and backfill the first visible Galaxy holder record.';
        }

        if (! $latestCard instanceof Card) {
            return 'Open assigned branch Galaxy card shell setup and issue the first live card shell.';
        }

        return 'Resume the latest branch review flow from the scoped shortcuts.';
    }

    protected function branchSetupPending(?CardHolder $latestHolder, ?Card $latestCard): bool
    {
        return ! $latestHolder instanceof CardHolder && ! $latestCard instanceof Card;
    }

    protected function assignedBranchSnapshotActions(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard): array
    {
        if (! $this->shopIsActive($shop)) {
            return [];
        }

        $actions = [[
            'label' => $this->assignedBranchPrimaryActionLabel($latestHolder, $latestCard),
            'route' => route('admin.shops.index', ['shop' => $shop->id]),
        ]];

        if ($latestHolder instanceof CardHolder) {
            $actions[] = [
                'label' => 'Open latest Galaxy holder in branch',
                'route' => route('admin.cardholders.index', ['cardholder' => $latestHolder->id]),
            ];
        }

        if ($latestCard instanceof Card) {
            $actions[] = [
                'label' => 'Open latest Galaxy card shell in branch',
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
            liveLabel: 'Review live Galaxy branches in assigned branch',
            setupLabel: 'Set up assigned branch',
            countRelations: ['cardHolders', 'cards'],
            isSetupConditionMet: fn (Shop $shop): bool => $this->shopHasNoRecords($shop, ['cardHolders', 'cards']),
        );
    }

    protected function scopedCardholderEntryLabel(?Shop $shop): string
    {
        return $this->scopedEntryLabel(
            shop: $shop,
            liveLabel: 'Review live Galaxy holders in assigned branch',
            setupLabel: 'Set up first Galaxy holder in assigned branch',
            countRelations: ['cardHolders'],
            isSetupConditionMet: fn (Shop $shop): bool => $this->shopHasNoRecords($shop, ['cardHolders']),
        );
    }

    protected function scopedCardEntryLabel(?Shop $shop): string
    {
        return $this->scopedEntryLabel(
            shop: $shop,
            liveLabel: 'Review live Galaxy card shells in assigned branch',
            setupLabel: 'Set up first Galaxy card shell in assigned branch',
            countRelations: ['cards'],
            isSetupConditionMet: fn (Shop $shop): bool => $this->shopHasNoRecords($shop, ['cards']),
        );
    }

    protected function scopedEntryLabel(?Shop $shop, string $liveLabel, string $setupLabel, array $countRelations, callable $isSetupConditionMet): string
    {
        if (! $shop instanceof Shop || ! $this->shopIsActive($shop)) {
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
            'value' => 'These entry points still open the shared Phase 1 workspaces, but branch-backed review inside Galaxy branches, Galaxy holders, and Galaxy card shells now narrows to the assigned branch with branch-specific review wording once the workspace loads.',
        ];
    }

    protected function liveEntryHandoffSummary(): array
    {
        $shop = $this->activeScopedShop();
        $liveEntryDomainCount = $this->liveEntryDomainCount();

        return [
            'label' => 'Entry handoff signal',
            'value' => match (true) {
                $shop instanceof Shop && $liveEntryDomainCount === 3
                    => 'Assigned-branch entry points already have enough live branch, holder, and card-shell coverage to support a useful scoped handoff review.',
                $shop instanceof Shop
                    => 'Assigned-branch entry points should stay setup-aware until the branch shows live branch, holder, and card-shell coverage together.',
                $liveEntryDomainCount === 3
                    => 'Shared entry points already have enough live branch, holder, and card-shell coverage to support a useful foundation handoff review.',
                default
                    => 'Entry points should stay setup-first until live branch, holder, and card-shell coverage is visible across the Galaxy foundation.',
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
            'value' => 'Latest-work shortcuts for Galaxy branches, Galaxy holders, and Galaxy card shells now follow branch scope and branch-specific review wording. Galaxy tiers, Galaxy access shells, and reporting remain shared review surfaces until deeper shop-aware policies arrive.',
        ];
    }

    protected function latestWorkspaces(): array
    {
        return array_values(array_filter([
            $this->latestShopWorkspace(),
            $this->latestCardHolderWorkspace(),
            $this->latestCardWorkspace(),
            $this->latestCardTypeWorkspace(),
            $this->latestRoleWorkspace(),
        ]));
    }

    protected function latestWorkspaceCount(): int
    {
        return $this->countItems($this->latestWorkspaces());
    }

    protected function firstLatestWorkspace(): ?array
    {
        $workspace = $this->firstItem($this->latestWorkspaces());

        return is_array($workspace) ? $workspace : null;
    }

    protected function latestWorkspaceHandoffSummary(): array
    {
        $shop = $this->activeScopedShop();
        $latestWorkspaceCount = $this->latestWorkspaceCount();

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
        $liveDomainCount = $this->liveCoreDomainCount();

        return [
            'label' => 'Migration-map handoff signal',
            'value' => match (true) {
                $liveDomainCount >= 5
                    => sprintf('The migration map already spans %d grouped sections with live coverage in %d core Galaxy domains, so parity handoff planning can stay grounded in the current Galaxy foundation shell.', $mappedGroupCount, $liveDomainCount),
                $liveDomainCount > 0
                    => sprintf('The migration map already spans %d grouped sections, but only %d core Galaxy domains have live Galaxy foundation coverage so far.', $mappedGroupCount, $liveDomainCount),
                default
                    => sprintf('The migration map already spans %d grouped sections and %d planned surfaces, but handoff planning should stay map-first until live Galaxy domains start landing in the Galaxy foundation.', $mappedGroupCount, $plannedSectionCount),
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
            ->first(fn (mixed $record): bool => $adminUser->can('view', $shopResolver($record)));
    }
}
