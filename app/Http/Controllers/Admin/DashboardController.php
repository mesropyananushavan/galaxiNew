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
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $navigation = config('admin-navigation');
        $preparedNavigationGroups = $this->preparedNavigationGroups($navigation);

        return view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'navigationGroups' => $preparedNavigationGroups,
            'phaseOneDomainMap' => $this->preparedDomainMap(config('phase-1-domain-map.entities', [])),
            'phaseOneDomainFocus' => (string) config('phase-1-domain-map.focus', 'Keep the first Galaxy foundation entities explicit while Phase 1 work is still landing.'),
            'phaseOneDomainGuide' => config('phase-1-domain-map.guide', ['docs/phase-1-domain-map.md', 'config/phase-1-domain-map.php']),
            'phaseOneDomainGuideText' => $this->inlineCodeList(config('phase-1-domain-map.guide', ['docs/phase-1-domain-map.md', 'config/phase-1-domain-map.php'])),
            'phaseOneDomainSourceOfTruth' => config('phase-1-domain-map.source_of_truth', ['docs/phase-1-domain-map.md', 'config/phase-1-domain-map.php']),
            'phaseOneDomainSourceOfTruthText' => $this->inlineCodeList(config('phase-1-domain-map.source_of_truth', ['docs/phase-1-domain-map.md', 'config/phase-1-domain-map.php'])),
            'phaseOneDomainPosture' => (string) config('phase-1-domain-map.posture', 'documented entity baseline for live foundation work'),
            'phaseOneDomainInventory' => $this->phaseOneDomainInventory(),
            'phaseOneModelSkeletons' => $this->preparedModelSkeletons(config('phase-1-model-skeletons.items', [])),
            'phaseOneModelSkeletonsFocus' => (string) config('phase-1-model-skeletons.focus', 'Keep the first Galaxy Eloquent models and migration anchors explicit while Phase 1 model work is still landing.'),
            'phaseOneModelSkeletonsGuideText' => $this->inlineCodeList(config('phase-1-model-skeletons.guide', ['docs/phase-1-model-skeletons.md', 'config/phase-1-model-skeletons.php'])),
            'phaseOneModelSkeletonsSourceOfTruthText' => $this->inlineCodeList(config('phase-1-model-skeletons.source_of_truth', ['docs/phase-1-model-skeletons.md', 'config/phase-1-model-skeletons.php', 'app/Models', 'database/migrations'])),
            'phaseOneModelSkeletonsPosture' => (string) config('phase-1-model-skeletons.posture', 'documented model-and-migration baseline for the live Galaxy foundation layer'),
            'phaseOneModelSkeletonMetrics' => $this->phaseOneModelSkeletonMetrics(),
            'phaseOneMigrationBaseline' => $this->preparedMigrationBaseline(config('phase-1-migration-baseline.items', [])),
            'phaseOneMigrationBaselineFocus' => (string) config('phase-1-migration-baseline.focus', 'Keep the first Galaxy schema anchors explicit while Phase 1 migration work is still landing.'),
            'phaseOneMigrationBaselineGuideText' => $this->inlineCodeList(config('phase-1-migration-baseline.guide', ['docs/phase-1-migration-baseline.md', 'config/phase-1-migration-baseline.php'])),
            'phaseOneMigrationBaselineSourceOfTruthText' => $this->inlineCodeList(config('phase-1-migration-baseline.source_of_truth', ['docs/phase-1-migration-baseline.md', 'config/phase-1-migration-baseline.php', 'database/migrations'])),
            'phaseOneMigrationBaselinePosture' => (string) config('phase-1-migration-baseline.posture', 'documented migration baseline for the live Galaxy schema layer'),
            'phaseOneMigrationBaselineMetrics' => $this->phaseOneMigrationBaselineMetrics(),
            'phaseOneAccessBaseline' => $this->preparedAccessBaseline(config('phase-1-access-baseline.gates', []), config('phase-1-access-baseline.route_guardrails', []), config('phase-1-access-baseline.policies', [])),
            'phaseOneAccessBaselineFocus' => (string) config('phase-1-access-baseline.focus', 'Keep the first Galaxy authorization gates and policy mappings explicit while Phase 1 access work is still landing.'),
            'phaseOneAccessBaselineGuideText' => $this->inlineCodeList(config('phase-1-access-baseline.guide', ['docs/phase-1-access-baseline.md', 'config/phase-1-access-baseline.php'])),
            'phaseOneAccessBaselineSourceOfTruthText' => $this->inlineCodeList(config('phase-1-access-baseline.source_of_truth', ['docs/phase-1-access-baseline.md', 'config/phase-1-access-baseline.php', 'app/Providers/Concerns/RegistersAdminAccessGates.php', 'app/Providers/Concerns/RegistersAdminPolicies.php', 'routes/admin.php'])),
            'phaseOneAccessBaselinePosture' => (string) config('phase-1-access-baseline.posture', 'documented authorization baseline for live Galaxy admin access'),
            'phaseOneAccessBaselineMetrics' => $this->phaseOneAccessBaselineMetrics(),
            'phaseOneShopAccessBaseline' => $this->preparedShopAccessBaseline(config('phase-1-shop-access-baseline.rules', [])),
            'phaseOneShopAccessBaselineFocus' => (string) config('phase-1-shop-access-baseline.focus', 'Keep the first Galaxy branch-scoped access rules explicit while Phase 1 shop visibility is still landing.'),
            'phaseOneShopAccessBaselineGuideText' => $this->inlineCodeList(config('phase-1-shop-access-baseline.guide', ['docs/phase-1-shop-access-baseline.md', 'config/phase-1-shop-access-baseline.php'])),
            'phaseOneShopAccessBaselineSourceOfTruthText' => $this->inlineCodeList(config('phase-1-shop-access-baseline.source_of_truth', ['docs/phase-1-shop-access-baseline.md', 'config/phase-1-shop-access-baseline.php', 'app/Models/User.php', 'app/Policies/ShopPolicy.php', 'app/Providers/Concerns/RegistersAdminAccessGates.php'])),
            'phaseOneShopAccessBaselinePosture' => (string) config('phase-1-shop-access-baseline.posture', 'documented shop-scoped access baseline for branch-aware Galaxy review'),
            'phaseOneShopAccessBaselineMetrics' => $this->phaseOneShopAccessBaselineMetrics(),
            'phaseOneReferenceDocs' => $this->preparedReferenceDocs(config('phase-1-reference-docs.items', [])),
            'phaseOneReferenceDocsFocus' => (string) config('phase-1-reference-docs.focus', 'Keep the current Galaxy admin map, shell layering, checkpoint trail, and seam-source baseline close while Phase 1 slices are still moving.'),
            'phaseOneReferenceDocsGuide' => config('phase-1-reference-docs.guide', ['README.md', 'docs/blueprint.md', 'docs/phase-1-plan.md']),
            'phaseOneReferenceDocsGuideText' => $this->inlineCodeList(config('phase-1-reference-docs.guide', ['README.md', 'docs/blueprint.md', 'docs/phase-1-plan.md'])),
            'phaseOneReferenceDocsSourceOfTruth' => config('phase-1-reference-docs.source_of_truth', ['README.md', 'docs/blueprint.md', 'docs/phase-1-plan.md', 'config/phase-1-reference-docs.php']),
            'phaseOneReferenceDocsSourceOfTruthText' => $this->inlineCodeList(config('phase-1-reference-docs.source_of_truth', ['README.md', 'docs/blueprint.md', 'docs/phase-1-plan.md', 'config/phase-1-reference-docs.php'])),
            'phaseOneReferenceDocMetrics' => $this->phaseOneReferenceDocMetrics(),
            'phaseOneSeamSources' => $this->preparedSeamSources(config('phase-1-seam-sources.items', [])),
            'phaseOneSeamSourcesFocus' => (string) config('phase-1-seam-sources.focus', 'Keep the README-level seam-source inventory visible inside the admin workspace, so contributors can trace which small config seams are currently carrying the Galaxy-specific Phase 1 foundation.'),
            'phaseOneSeamSourcesGuide' => config('phase-1-seam-sources.guide', ['README.md', 'config/phase-1-seam-sources.php']),
            'phaseOneSeamSourcesGuideText' => $this->inlineCodeList(config('phase-1-seam-sources.guide', ['README.md', 'config/phase-1-seam-sources.php'])),
            'phaseOneSeamSourcesSourceOfTruth' => config('phase-1-seam-sources.source_of_truth', ['README.md', 'config/phase-1-seam-sources.php']),
            'phaseOneSeamSourcesSourceOfTruthText' => $this->inlineCodeList(config('phase-1-seam-sources.source_of_truth', ['README.md', 'config/phase-1-seam-sources.php'])),
            'phaseOneSeamSourceMetrics' => $this->phaseOneSeamSourceMetrics(),
            'phaseOneFoundationSeams' => $this->preparedFoundationSeams(config('phase-1-foundation-seams.items', [])),
            'phaseOneFoundationSeamsFocus' => (string) config('phase-1-foundation-seams.focus', 'Keep the new Galaxy-specific Phase 1 seams visible where contributors review the live foundation shell.'),
            'phaseOneFoundationSeamsGuide' => config('phase-1-foundation-seams.guide', ['docs/phase-1-foundation-seams.md', 'config/phase-1-foundation-seams.php']),
            'phaseOneFoundationSeamsGuideText' => $this->inlineCodeList(config('phase-1-foundation-seams.guide', ['docs/phase-1-foundation-seams.md', 'config/phase-1-foundation-seams.php'])),
            'phaseOneFoundationSeamsSourceOfTruth' => config('phase-1-foundation-seams.source_of_truth', ['docs/phase-1-foundation-seams.md', 'config/phase-1-foundation-seams.php']),
            'phaseOneFoundationSeamsSourceOfTruthText' => $this->inlineCodeList(config('phase-1-foundation-seams.source_of_truth', ['docs/phase-1-foundation-seams.md', 'config/phase-1-foundation-seams.php'])),
            'phaseOneFoundationSeamMetrics' => $this->phaseOneFoundationSeamMetrics(),
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
            'foundationSnapshotMetrics' => $this->foundationSnapshotMetrics($navigation),
            'dashboardScopeSummary' => $this->dashboardScopeSummary(),
            'assignedBranchSnapshot' => $this->assignedBranchSnapshot(),
            'liveEntryMetrics' => $this->liveEntryMetrics(),
            'liveEntryHandoffSummary' => $this->liveEntryHandoffSummary(),
            'liveEntryScopeNote' => $this->liveEntryScopeNote(),
            'latestWorkspaceMetrics' => $this->latestWorkspaceMetrics(),
            'latestWorkspaceHandoffSummary' => $this->latestWorkspaceHandoffSummary(),
            'latestWorkspaceScopeNote' => $this->latestWorkspaceScopeNote(),
            'migrationMapMetrics' => $this->migrationMapMetrics($preparedNavigationGroups),
            'migrationMapHandoffSummary' => $this->migrationMapHandoffSummary($navigation),
            'migrationMapFocus' => $this->migrationMapFocus($navigation),
            'migrationMapPosture' => $this->migrationMapPosture(),
            'phaseOneDomainMetrics' => $this->phaseOneDomainMetrics(),
            'liveReviewEntryPoints' => $this->liveReviewEntryPoints(),
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

    protected function phaseOneReferenceDocMetrics(): array
    {
        return [
            ['label' => 'Reference coverage', 'value' => $this->phaseOneReferenceDocsCoverage().'.'],
            ['label' => 'Reference baseline', 'value' => '<code>config/phase-1-reference-docs.php</code> keeps this admin-side Phase 1 reference inventory aligned.', 'html' => true],
            ['label' => 'Reference posture', 'value' => e((string) config('phase-1-reference-docs.posture', 'admin reference inventory stays explicit across the live Galaxy dashboard trail')).'.', 'html' => true],
        ];
    }

    protected function phaseOneAccessBaselineMetrics(): array
    {
        $routeGuardrails = config('phase-1-access-baseline.route_guardrails', []);

        return [
            ['label' => 'Gate coverage', 'value' => sprintf('%d Phase 1 admin access gates currently tracked.', $this->accessGateCount())],
            ['label' => 'Route guardrails', 'value' => sprintf('%d Phase 1 admin route guardrails currently tracked.', $this->accessRouteGuardrailCount())],
            ['label' => 'Policy-backed guardrails', 'value' => sprintf('%d live resource guardrails already run through model policy checks.', $this->policyBackedAccessRouteGuardrailCount($routeGuardrails))],
            ['label' => 'Shared-shell guardrails', 'value' => sprintf('%d live operational routes still rely on the shared admin shell guard.', $this->sharedShellAccessRouteGuardrailCount($routeGuardrails))],
            ['label' => 'Policy coverage', 'value' => sprintf('%d model policies currently mapped for Phase 1 admin resources.', $this->accessPolicyCount())],
            ['label' => 'Admin guardrail', 'value' => '<code>routes/admin.php</code> keeps the Galaxy admin shell behind <code>auth</code> and <code>can:access-admin</code>.', 'html' => true],
        ];
    }

    protected function phaseOneModelSkeletonMetrics(): array
    {
        return [
            ['label' => 'Skeleton coverage', 'value' => sprintf('%d model skeleton checkpoints currently tracked.', $this->modelSkeletonCount())],
            ['label' => 'Model anchor', 'value' => '<code>app/Models</code> keeps the first Galaxy Eloquent layer visible.', 'html' => true],
            ['label' => 'Migration anchor', 'value' => '<code>database/migrations</code> keeps the first Galaxy schema layer visible.', 'html' => true],
        ];
    }

    protected function phaseOneMigrationBaselineMetrics(): array
    {
        return [
            ['label' => 'Migration coverage', 'value' => sprintf('%d migration checkpoints currently tracked.', $this->migrationBaselineCount())],
            ['label' => 'Schema anchor', 'value' => '<code>database/migrations</code> keeps the first Galaxy schema checkpoints visible.', 'html' => true],
            ['label' => 'Layer split', 'value' => 'Access schema, card-domain schema, and later review follow-ups remain visible as separate Phase 1 migration checkpoints.'],
        ];
    }

    protected function phaseOneSeamSourceMetrics(): array
    {
        return [
            ['label' => 'Seam-source coverage', 'value' => $this->phaseOneSeamSourcesCoverage().'.'],
            ['label' => 'Seam-source baseline', 'value' => '<code>config/phase-1-seam-sources.php</code> keeps this README-level seam-source inventory aligned.', 'html' => true],
            ['label' => 'Seam-source posture', 'value' => e((string) config('phase-1-seam-sources.posture', 'README-backed seam-source baseline stays explicit across the live Galaxy reference trail')).'.', 'html' => true],
        ];
    }

    protected function phaseOneShopAccessBaselineMetrics(): array
    {
        return [
            ['label' => 'Scope rule coverage', 'value' => sprintf('%d branch-scope rules currently tracked.', $this->shopAccessRuleCount())],
            ['label' => 'Scope gate anchor', 'value' => '<code>access-shop</code> keeps branch-aware visibility explicit at the Laravel gate layer.', 'html' => true],
            ['label' => 'Policy reuse', 'value' => '<code>ShopPolicy</code> currently reuses the same branch-access seam for both <code>view</code> and <code>update</code>.', 'html' => true],
        ];
    }

    protected function phaseOneFoundationSeamMetrics(): array
    {
        return [
            ['label' => 'Seam coverage', 'value' => $this->phaseOneFoundationSeamsCoverage().'.'],
            ['label' => 'Seam baseline', 'value' => '<code>config/phase-1-foundation-seams.php</code> keeps this mapped seam inventory aligned.', 'html' => true],
            ['label' => 'Seam posture', 'value' => e((string) config('phase-1-foundation-seams.posture', 'small config-backed and doc-backed foundation seams stay explicit')).'.', 'html' => true],
        ];
    }

    protected function preparedNavigationGroups(array $groups): array
    {
        return collect($groups)
            ->filter(fn ($group): bool => is_array($group) && filled($group['group'] ?? null))
            ->map(function (array $group): array {
                return [
                    'group' => (string) $group['group'],
                    'items' => collect($group['items'] ?? [])
                        ->filter(fn ($item): bool => is_array($item) && filled($item['label'] ?? null) && filled($item['route'] ?? null))
                        ->map(function (array $item): array {
                            $route = route((string) $item['route']);
                            $label = (string) $item['label'];
                            $description = (string) ($item['description'] ?? '');
                            $path = parse_url($route, PHP_URL_PATH) ?: $route;

                            return [
                                'label' => $label,
                                'description' => $description,
                                'route' => (string) $item['route'],
                                'href' => $route,
                                'path' => $path,
                                'displaySummary' => sprintf('<a href="%s">%s</a> (%s Route: %s)', e($route), e($label), e($description), e((string) $path)),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    protected function preparedDomainMap(array $entities): array
    {
        return collect($entities)
            ->filter(fn ($entity): bool => is_array($entity) && filled($entity['label'] ?? null))
            ->map(function (array $entity): array {
                $label = (string) ($entity['label'] ?? '');
                $table = (string) ($entity['table'] ?? '');
                $coverage = (string) ($entity['coverage'] ?? '');

                return [
                    'label' => $label,
                    'table' => $table,
                    'coverage' => $coverage,
                    'displaySummary' => sprintf('<strong>%s</strong> (<code>%s</code>), %s', e($label), e($table), e($coverage)),
                ];
            })
            ->values()
            ->all();
    }

    protected function preparedModelSkeletons(array $items): array
    {
        return collect($items)
            ->filter(fn ($item): bool => is_array($item) && filled($item['label'] ?? null) && filled($item['model'] ?? null) && filled($item['migration'] ?? null))
            ->map(function (array $item): array {
                $label = (string) ($item['label'] ?? '');
                $model = (string) ($item['model'] ?? '');
                $migration = (string) ($item['migration'] ?? '');
                $coverage = (string) ($item['coverage'] ?? '');

                return [
                    'label' => $label,
                    'model' => $model,
                    'migration' => $migration,
                    'coverage' => $coverage,
                    'displaySummary' => sprintf('<strong>%s</strong> (<code>%s</code>; <code>%s</code>), %s', e($label), e($model), e($migration), e($coverage)),
                ];
            })
            ->values()
            ->all();
    }

    protected function preparedMigrationBaseline(array $items): array
    {
        return collect($items)
            ->filter(fn ($item): bool => is_array($item) && filled($item['label'] ?? null) && filled($item['migration'] ?? null))
            ->map(function (array $item): array {
                $label = (string) ($item['label'] ?? '');
                $migration = (string) ($item['migration'] ?? '');
                $coverage = (string) ($item['coverage'] ?? '');

                return [
                    'label' => $label,
                    'migration' => $migration,
                    'coverage' => $coverage,
                    'displaySummary' => sprintf('<strong>%s</strong> (<code>%s</code>), %s', e($label), e($migration), e($coverage)),
                ];
            })
            ->values()
            ->all();
    }

    protected function preparedAccessBaseline(array $gates, array $routeGuardrails, array $policies): array
    {
        return [
            'gates' => collect($gates)
                ->filter(fn ($gate): bool => is_array($gate) && filled($gate['label'] ?? null) && filled($gate['ability'] ?? null))
                ->map(function (array $gate): array {
                    $label = (string) ($gate['label'] ?? '');
                    $ability = (string) ($gate['ability'] ?? '');
                    $coverage = (string) ($gate['coverage'] ?? '');

                    return [
                        'label' => $label,
                        'ability' => $ability,
                        'coverage' => $coverage,
                        'displaySummary' => sprintf('<strong>%s</strong> (<code>%s</code>), %s', e($label), e($ability), e($coverage)),
                    ];
                })
                ->values()
                ->all(),
            'gateIntro' => $this->accessGateIntro($gates),
            'routeGuardrails' => $this->preparedAccessRouteGuardrails($routeGuardrails),
            'routeGuardrailGroups' => $this->preparedAccessRouteGuardrailGroups($routeGuardrails),
            'routeGuardrailIntro' => $this->accessRouteGuardrailIntro($routeGuardrails),
            'policies' => collect($policies)
                ->filter(fn ($policy): bool => is_array($policy) && filled($policy['label'] ?? null) && filled($policy['policy'] ?? null))
                ->map(function (array $policy): array {
                    $label = (string) ($policy['label'] ?? '');
                    $model = (string) ($policy['model'] ?? '');
                    $policyClass = (string) ($policy['policy'] ?? '');
                    $coverage = (string) ($policy['coverage'] ?? '');

                    return [
                        'label' => $label,
                        'model' => $model,
                        'policy' => $policyClass,
                        'coverage' => $coverage,
                        'displaySummary' => sprintf('<strong>%s</strong> (<code>%s</code> → <code>%s</code>), %s', e($label), e($model), e($policyClass), e($coverage)),
                    ];
                })
                ->values()
                ->all(),
            'policyIntro' => $this->accessPolicyIntro($policies),
        ];
    }

    protected function accessGateIntro(array $gates): string
    {
        return sprintf(
            'These %d controller-tracked admin gates keep the Galaxy workspace shell and selected branch scope explicit before deeper Phase 1 role matrices land.',
            count($gates),
        );
    }

    protected function accessPolicyIntro(array $policies): string
    {
        return sprintf(
            'These %d mapped model policies keep the first live Galaxy admin resources tied to explicit review and write rules while richer Phase 1 access behavior is still landing.',
            count($policies),
        );
    }

    protected function preparedAccessRouteGuardrails(array $routeGuardrails): array
    {
        return collect($routeGuardrails)
            ->filter(fn ($guardrail): bool => is_array($guardrail) && filled($guardrail['label'] ?? null) && filled($guardrail['route'] ?? null) && filled($guardrail['guard'] ?? null) && filled($guardrail['maturity'] ?? null))
            ->map(function (array $guardrail): array {
                $label = (string) ($guardrail['label'] ?? '');
                $route = (string) ($guardrail['route'] ?? '');
                $guard = (string) ($guardrail['guard'] ?? '');
                $coverage = (string) ($guardrail['coverage'] ?? '');
                $routeDefinition = Route::getRoutes()->getByName($route);
                $methods = collect($routeDefinition?->methods() ?? [])
                    ->reject(fn (string $method): bool => $method === 'HEAD')
                    ->values()
                    ->implode(', ');
                $path = $routeDefinition !== null ? '/'.$routeDefinition->uri() : null;
                $routeContract = $methods !== '' && filled($path)
                    ? sprintf('%s %s', $methods, $path)
                    : null;

                $maturityLabel = (string) ($guardrail['maturity'] ?? 'policy-backed');

                return [
                    'label' => $label,
                    'route' => $route,
                    'guard' => $guard,
                    'coverage' => $coverage,
                    'path' => $path,
                    'methods' => $methods,
                    'routeContract' => $routeContract,
                    'family' => $this->accessRouteGuardrailFamily($route),
                    'familyLabel' => $this->accessRouteGuardrailFamilyLabel($route),
                    'maturityLabel' => $maturityLabel,
                    'displaySummary' => filled($routeContract)
                        ? sprintf('<strong>%s</strong> (<code>%s</code>; <code>%s</code>; <code>%s</code>; <code>%s</code>), %s', e($label), e($route), e($routeContract), e($guard), e($maturityLabel), e($coverage))
                        : sprintf('<strong>%s</strong> (<code>%s</code>; <code>%s</code>; <code>%s</code>), %s', e($label), e($route), e($guard), e($maturityLabel), e($coverage)),
                ];
            })
            ->values()
            ->all();
    }

    protected function preparedAccessRouteGuardrailGroups(array $routeGuardrails): array
    {
        return collect($this->preparedAccessRouteGuardrails($routeGuardrails))
            ->groupBy('family')
            ->map(function ($items, $family): array {
                $first = $items->first();

                return [
                    'family' => (string) $family,
                    'label' => (string) ($first['familyLabel'] ?? 'Route guardrail group'),
                    'count' => $items->count(),
                    'displayLabel' => sprintf('%s (%d)', (string) ($first['familyLabel'] ?? 'Route guardrail group'), $items->count()),
                    'summary' => $this->accessRouteGuardrailFamilySummary((string) $family, $items->count()),
                    'maturityNote' => $this->accessRouteGuardrailFamilyMaturityNote($items->all()),
                    'displaySummary' => $this->accessRouteGuardrailFamilyDisplaySummary((string) ($first['familyLabel'] ?? 'Route guardrail group'), $items->count(), (string) $family, $items->all()),
                    'items' => $items->values()->all(),
                ];
            })
            ->sortBy(fn (array $group): int => $this->accessRouteGuardrailFamilyOrder($group['family'] ?? 'other'))
            ->values()
            ->all();
    }

    protected function accessRouteGuardrailFamily(string $route): string
    {
        return match (true) {
            str_starts_with($route, 'admin.shops.') => 'shops',
            str_starts_with($route, 'admin.cardholders.') => 'cardholders',
            str_starts_with($route, 'admin.cards.') => 'cards',
            str_starts_with($route, 'admin.card-types.') => 'card-types',
            str_starts_with($route, 'admin.roles-permissions.') => 'roles-permissions',
            str_starts_with($route, 'admin.checks-points.') => 'checks-points',
            str_starts_with($route, 'admin.services-rules.') => 'services-rules',
            str_starts_with($route, 'admin.gifts.') => 'gifts',
            str_starts_with($route, 'admin.reports.') => 'reports',
            default => 'other',
        };
    }

    protected function accessRouteGuardrailFamilyLabel(string $route): string
    {
        return match ($this->accessRouteGuardrailFamily($route)) {
            'shops' => 'Galaxy branches',
            'cardholders' => 'Galaxy holders',
            'cards' => 'Galaxy card shells',
            'card-types' => 'Galaxy tiers',
            'roles-permissions' => 'Galaxy access shells',
            'checks-points' => 'Galaxy receipt operations',
            'services-rules' => 'Galaxy rule operations',
            'gifts' => 'Galaxy reward operations',
            'reports' => 'Galaxy reporting operations',
            default => 'Other access guardrails',
        };
    }

    protected function accessRouteGuardrailFamilyOrder(string $family): int
    {
        return match ($family) {
            'shops' => 10,
            'cardholders' => 20,
            'cards' => 30,
            'card-types' => 40,
            'roles-permissions' => 50,
            'checks-points' => 60,
            'services-rules' => 70,
            'gifts' => 80,
            'reports' => 90,
            default => 999,
        };
    }

    protected function accessRouteGuardrailFamilySummary(string $family, int $count): string
    {
        return match ($family) {
            'shops' => sprintf('Branch review and write entry points stay visible through %d guarded Galaxy branch routes.', $count),
            'cardholders' => sprintf('Holder review and write entry points stay visible through %d guarded Galaxy holder routes.', $count),
            'cards' => sprintf('Card-shell review and write entry points stay visible through %d guarded Galaxy card routes.', $count),
            'card-types' => sprintf('Tier review, writes, and status activation stay visible through %d guarded Galaxy tier routes.', $count),
            'roles-permissions' => sprintf('Access-shell review and write entry points stay visible through %d guarded Galaxy access routes.', $count),
            'checks-points' => sprintf('Receipt and accrual review stays visible through %d shared-shell Galaxy operations route.', $count),
            'services-rules' => sprintf('Rule review stays visible through %d shared-shell Galaxy rules route.', $count),
            'gifts' => sprintf('Reward review stays visible through %d shared-shell Galaxy rewards route.', $count),
            'reports' => sprintf('Reporting review stays visible through %d shared-shell Galaxy reporting route.', $count),
            default => sprintf('This guardrail family currently tracks %d Phase 1 admin routes.', $count),
        };
    }

    protected function accessRouteGuardrailFamilyDisplaySummary(string $label, int $count, string $family, array $items): string
    {
        return sprintf('%s (%d), %s %s', $label, $count, $this->accessRouteGuardrailFamilySummary($family, $count), $this->accessRouteGuardrailFamilyMaturityNote($items));
    }

    protected function accessRouteGuardrailFamilyMaturityNote(array $items): string
    {
        $maturities = collect($items)
            ->pluck('maturityLabel')
            ->filter(fn ($maturity): bool => filled($maturity))
            ->unique()
            ->values();

        if ($maturities->count() === 1 && $maturities->first() === 'shared-shell') {
            return 'This lane is still guarded only by the shared Galaxy admin shell.';
        }

        if ($maturities->count() === 1 && $maturities->first() === 'policy-backed') {
            return 'This lane already runs through explicit Phase 1 policy checks.';
        }

        return 'This lane currently mixes policy-backed and shared-shell guardrails.';
    }

    protected function accessRouteGuardrailIntro(array $routeGuardrails): string
    {
        $groups = $this->preparedAccessRouteGuardrailGroups($routeGuardrails);
        $policyBackedCount = $this->policyBackedAccessRouteGuardrailCount($routeGuardrails);
        $sharedShellCount = $this->sharedShellAccessRouteGuardrailCount($routeGuardrails);

        return sprintf(
            'These %d live Phase 1 route guardrails are grouped into %d controller-shaped Galaxy access lanes, with %d policy-backed resource guardrails and %d shared-shell operational guardrails so access maturity stays readable on the admin dashboard.',
            $this->accessRouteGuardrailCount(),
            count($groups),
            $policyBackedCount,
            $sharedShellCount,
        );
    }

    protected function policyBackedAccessRouteGuardrailCount(array $routeGuardrails): int
    {
        return collect($routeGuardrails)
            ->filter(fn (array $guardrail): bool => ! str_contains((string) ($guardrail['guard'] ?? ''), 'auth + can:access-admin'))
            ->count();
    }

    protected function sharedShellAccessRouteGuardrailCount(array $routeGuardrails): int
    {
        return collect($routeGuardrails)
            ->filter(fn (array $guardrail): bool => str_contains((string) ($guardrail['guard'] ?? ''), 'auth + can:access-admin'))
            ->count();
    }

    protected function preparedShopAccessBaseline(array $rules): array
    {
        return collect($rules)
            ->filter(fn ($rule): bool => is_array($rule) && filled($rule['label'] ?? null) && filled($rule['rule'] ?? null))
            ->map(function (array $rule): array {
                $label = (string) ($rule['label'] ?? '');
                $statement = (string) ($rule['rule'] ?? '');
                $coverage = (string) ($rule['coverage'] ?? '');

                return [
                    'label' => $label,
                    'rule' => $statement,
                    'coverage' => $coverage,
                    'displaySummary' => sprintf('<strong>%s</strong>, %s %s', e($label), e($statement), e($coverage)),
                ];
            })
            ->values()
            ->all();
    }

    protected function domainEntities()
    {
        return collect(config('phase-1-domain-map.entities', []));
    }

    protected function modelSkeletons()
    {
        return collect(config('phase-1-model-skeletons.items', []));
    }

    protected function modelSkeletonCount(): int
    {
        return $this->countConfigItems($this->modelSkeletons());
    }

    protected function migrationBaselines()
    {
        return collect(config('phase-1-migration-baseline.items', []));
    }

    protected function migrationBaselineCount(): int
    {
        return $this->countConfigItems($this->migrationBaselines());
    }

    protected function shopAccessRules()
    {
        return collect(config('phase-1-shop-access-baseline.rules', []));
    }

    protected function shopAccessRuleCount(): int
    {
        return $this->countConfigItems($this->shopAccessRules());
    }

    protected function accessGates()
    {
        return collect(config('phase-1-access-baseline.gates', []));
    }

    protected function accessGateCount(): int
    {
        return $this->countConfigItems($this->accessGates());
    }

    protected function accessRouteGuardrails()
    {
        return collect(config('phase-1-access-baseline.route_guardrails', []));
    }

    protected function accessRouteGuardrailCount(): int
    {
        return $this->countConfigItems($this->accessRouteGuardrails());
    }

    protected function accessPolicies()
    {
        return collect(config('phase-1-access-baseline.policies', []));
    }

    protected function accessPolicyCount(): int
    {
        return $this->countConfigItems($this->accessPolicies());
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
        return $this->queryCount($modelClass::query());
    }

    protected function scopedModelCount(string $modelClass, string $scope): int
    {
        return $this->queryCount($modelClass::query()->{$scope}());
    }

    protected function foundationSeams()
    {
        return collect(config('phase-1-foundation-seams.items', []));
    }

    protected function preparedFoundationSeams(array $seams): array
    {
        return collect($seams)
            ->filter(fn ($seam): bool => is_array($seam) && filled($seam['label'] ?? null))
            ->map(function (array $seam): array {
                $sourcesText = $this->plainList(array_map('strval', array_filter($seam['sources'] ?? [], fn ($source) => filled($source))));

                $label = (string) ($seam['label'] ?? '');
                $summary = (string) ($seam['summary'] ?? '');

                return [
                    'label' => $label,
                    'summary' => $summary,
                    'displaySummary' => sprintf('<strong>%s</strong>, %s', e($label), e($summary)),
                    'sourcesText' => $sourcesText,
                    'sourcesNote' => filled($sourcesText) ? sprintf('Sources: %s', $sourcesText) : null,
                ];
            })
            ->values()
            ->all();
    }

    protected function foundationSeamCount(): int
    {
        return $this->countConfigItems($this->foundationSeams());
    }

    protected function phaseOneReferenceDocsCoverage(): string
    {
        return sprintf('%d Phase 1 reference docs currently linked', $this->referenceDocCount());
    }

    protected function preparedReferenceDocs(array $items): array
    {
        return collect($items)
            ->filter(fn ($item): bool => filled($item))
            ->map(fn ($item): array => [
                'label' => (string) $item,
                'displayLabel' => sprintf('<code>%s</code>', e((string) $item)),
            ])
            ->values()
            ->all();
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

    protected function preparedSeamSources(array $items): array
    {
        return collect($items)
            ->filter(fn ($item): bool => filled($item))
            ->map(fn ($item): array => [
                'label' => (string) $item,
                'displayLabel' => sprintf('<code>%s</code>', e((string) $item)),
            ])
            ->values()
            ->all();
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

    protected function inlineCodeList(array $items): string
    {
        return collect($items)
            ->filter(fn ($item): bool => filled($item))
            ->map(fn ($item): string => sprintf('<code>%s</code>', e((string) $item)))
            ->implode(', ');
    }

    protected function plainList(array $items): string
    {
        return collect($items)
            ->filter(fn ($item): bool => filled($item))
            ->map(fn ($item): string => e((string) $item))
            ->implode(', ');
    }

    protected function countItems($items): int
    {
        return is_array($items) ? count($items) : $items->count();
    }

    protected function firstItem($items): mixed
    {
        return is_array($items) ? ($items[0] ?? null) : $this->firstIterableItem($items);
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
        $firstItem = $this->firstMappedNavigationItem($navigation);

        if (! is_array($firstItem) || ! isset($firstItem['label'])) {
            return 'first parity target still needs to be mapped';
        }

        return sprintf('start with %s', mb_strtolower((string) $firstItem['label']));
    }

    protected function firstMappedNavigationItem(array $navigation): mixed
    {
        return $this->firstIterableItem($this->mappedNavigationItems($navigation));
    }

    protected function mappedNavigationItems(array $navigation): iterable
    {
        return collect($navigation)
            ->pluck('items')
            ->flatten(1);
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
        $cardType = $this->latestModelRecord(CardType::class);

        return $cardType instanceof CardType ? $cardType : null;
    }

    protected function latestSavedRole(): ?Role
    {
        $role = $this->latestModelRecord(Role::class);

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

    protected function preparedWorkspaceLinks(array $links): array
    {
        return collect($links)
            ->filter(fn ($link): bool => is_array($link) && filled($link['label'] ?? null) && filled($link['route'] ?? null))
            ->map(fn (array $link): array => [
                'label' => (string) $link['label'],
                'route' => (string) $link['route'],
                'path' => (string) ($link['path'] ?? parse_url((string) $link['route'], PHP_URL_PATH) ?: (string) $link['route']),
            ])
            ->values()
            ->all();
    }

    protected function workspaceLink(string $label, string $routeName, array $parameters = []): array
    {
        $route = route($routeName, $parameters);

        return [
            'label' => $label,
            'route' => $route,
            'path' => parse_url($route, PHP_URL_PATH) ?: $route,
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

        return (int) ($model->{$countAttribute} ?? $this->loadedRelationCount($model->{$relation}));
    }

    protected function loadedRelationCount(iterable $items): int
    {
        return $this->countItems($items);
    }

    protected function liveReviewEntryPoints(): array
    {
        $shop = $this->activeScopedShop();

        if (! $shop instanceof Shop) {
            return $this->preparedWorkspaceLinks([
                $this->workspaceLink('Review live Galaxy branches', 'admin.shops.index'),
                $this->workspaceLink('Review live Galaxy holders', 'admin.cardholders.index'),
                $this->workspaceLink('Review live Galaxy card shells', 'admin.cards.index'),
                $this->workspaceLink('Review live Galaxy tiers', 'admin.card-types.index'),
                $this->workspaceLink('Review live Galaxy access shells', 'admin.roles-permissions.index'),
                $this->workspaceLink('Review Galaxy reporting sources', 'admin.reports.index'),
            ]);
        }

        $primaryScopedShopEntryLabel = $this->scopedShopEntryLabel($shop);
        $primaryScopedCardholderEntryLabel = $this->scopedCardholderEntryLabel($shop);
        $primaryScopedCardEntryLabel = $this->scopedCardEntryLabel($shop);

        return $this->preparedWorkspaceLinks([
            $this->workspaceLink($primaryScopedShopEntryLabel, 'admin.shops.index'),
            $this->workspaceLink($primaryScopedCardholderEntryLabel, 'admin.cardholders.index'),
            $this->workspaceLink($primaryScopedCardEntryLabel, 'admin.cards.index'),
            $this->scopedSharedLiveEntryPoint('Review shared Galaxy tiers', 'admin.card-types.index'),
            $this->scopedSharedLiveEntryPoint('Review shared Galaxy access shells', 'admin.roles-permissions.index'),
            $this->scopedSharedLiveEntryPoint('Review shared Galaxy reporting sources', 'admin.reports.index'),
        ]);
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

    protected function foundationSnapshotMetrics(array $navigation): array
    {
        return [
            ['label' => 'Route namespace', 'value' => '/admin'],
            ['label' => 'Planned sections', 'value' => (string) collect($navigation)->sum(fn (array $group): int => count($group['items']))],
            ['label' => 'Live domain coverage', 'value' => $this->liveDomainCoverage()],
            ['label' => 'Foundation readiness', 'value' => $this->foundationReadinessSignal()],
            ['label' => 'Active foundation coverage', 'value' => $this->activeFoundationCoverage()],
            ['label' => 'Branch pause coverage', 'value' => $this->branchPauseCoverage()],
            ['label' => 'Access baseline coverage', 'value' => $this->accessBaselineCoverage()],
            ['label' => 'Tier baseline coverage', 'value' => $this->tierBaselineCoverage()],
            ['label' => 'Live Galaxy branches', 'value' => (string) $this->savedShopCount()],
            ['label' => 'Active-state Galaxy branches', 'value' => (string) $this->activeShopCount()],
            ['label' => 'Live Galaxy holders', 'value' => (string) $this->savedCardHolderCount()],
            ['label' => 'Active-state Galaxy holders', 'value' => (string) $this->activeCardHolderCount()],
            ['label' => 'Live Galaxy card shells', 'value' => (string) $this->savedCardCount()],
            ['label' => 'Active-state Galaxy card shells', 'value' => (string) $this->activeCardCount()],
            ['label' => 'Live Galaxy tiers', 'value' => (string) $this->savedCardTypeCount()],
            ['label' => 'Active-state Galaxy tiers', 'value' => (string) $this->activeCardTypeCount()],
            ['label' => 'Live Galaxy access shells', 'value' => (string) $this->savedRoleCount()],
            ['label' => 'Live Galaxy access permissions', 'value' => (string) $this->savedPermissionCount()],
        ];
    }

    protected function migrationMapMetrics(array $navigationGroups): array
    {
        $mappedSurfaceCount = collect($navigationGroups)->sum(fn (array $group): int => count($group['items'] ?? []));
        $mappedGroupCount = count($navigationGroups);

        return [
            ['label' => 'Mapped surfaces', 'value' => sprintf('%d planned admin surfaces are currently staged in the Phase 1 target map.', $mappedSurfaceCount)],
            ['label' => 'Mapped groups', 'value' => sprintf('%d top-level admin groups are currently staged in the Phase 1 target map.', $mappedGroupCount)],
            ['label' => 'Mapped routes', 'value' => sprintf('%d Galaxy foundation route targets are currently linked from the Phase 1 target map.', $mappedSurfaceCount)],
        ];
    }

    protected function liveEntryMetrics(): array
    {
        return [
            ['label' => 'Entry coverage', 'value' => $this->liveEntryPointCoverage().'.'],
            ['label' => 'Entry focus', 'value' => $this->liveEntryPointFocus().'.'],
            ['label' => 'Entry posture', 'value' => $this->liveEntryPointPosture().'.'],
        ];
    }

    protected function latestWorkspaceMetrics(): array
    {
        return [
            ['label' => 'Latest-work coverage', 'value' => $this->latestWorkspaceCoverage().'.'],
            ['label' => 'Latest-work focus', 'value' => $this->latestWorkspaceFocus().'.'],
            ['label' => 'Latest-work posture', 'value' => $this->latestWorkspacePosture().'.'],
        ];
    }

    protected function phaseOneDomainMetrics(): array
    {
        return [
            ['label' => 'Entity coverage', 'value' => $this->phaseOneDomainCoverage().'.'],
            ['label' => 'Entity inventory', 'value' => $this->phaseOneDomainInventory().'.'],
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
            'actionMetrics' => $this->assignedBranchActionMetrics($shop, $latestHolder, $latestCard, $actions),
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

    protected function assignedBranchActionMetrics(Shop $shop, ?CardHolder $latestHolder, ?Card $latestCard, array $actions): array
    {
        return [
            ['label' => 'Scoped action coverage', 'value' => $this->assignedBranchActionCoverage($actions).'.'],
            ['label' => 'Scoped action posture', 'value' => $this->assignedBranchActionPosture($shop, $latestHolder, $latestCard).'.'],
            ['label' => 'Scoped action focus', 'value' => $this->assignedBranchActionFocus($shop, $latestHolder, $latestCard).'.'],
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

        $latestTimestamp = $this->latestKnownTimestamp([
            $latestHolder?->created_at,
            $latestCard?->created_at,
        ]);

        if ($latestTimestamp === null) {
            return 'unknown';
        }

        return now()->diffInDays($latestTimestamp) <= 1
            ? 'fresh activity'
            : 'stale activity';
    }

    protected function latestKnownTimestamp(array $timestamps): mixed
    {
        return $this->firstIterableItem($this->sortedKnownTimestamps($timestamps));
    }

    protected function sortedKnownTimestamps(array $timestamps): iterable
    {
        return collect($timestamps)
            ->filter()
            ->sortDesc();
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
        $manager = $this->firstItem($shop->users);

        return $manager instanceof User ? $manager->name : 'Unassigned';
    }

    protected function latestShopCardHolder(Shop $shop): ?CardHolder
    {
        $cardHolder = $this->latestModelRecord(CardHolder::class, ['shop_id' => $shop->id]);

        return $cardHolder instanceof CardHolder ? $cardHolder : null;
    }

    protected function latestShopCard(Shop $shop): ?Card
    {
        $card = $this->latestModelRecord(Card::class, ['shop_id' => $shop->id]);

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

        return $this->preparedWorkspaceLinks($actions);
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
        return $this->preparedWorkspaceLinks([
            $this->latestShopWorkspace(),
            $this->latestCardHolderWorkspace(),
            $this->latestCardWorkspace(),
            $this->latestCardTypeWorkspace(),
            $this->latestRoleWorkspace(),
        ]);
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
        $records = $this->iterableItems($records);

        if (! $adminUser instanceof User) {
            return $this->firstIterableItem($records);
        }

        return $records->first(fn (mixed $record): bool => $adminUser->can('view', $shopResolver($record)));
    }

    protected function firstIterableItem(iterable $items): mixed
    {
        return $this->iterableItems($items)->first();
    }

    protected function iterableItems(iterable $items)
    {
        return collect($items);
    }

    protected function latestModelRecord(string $modelClass, array $where = []): mixed
    {
        return $this->firstIterableItem(
            $this->queryItems(
                $modelClass::query()
                    ->where($where)
                    ->latest('id')
            )
        );
    }

    protected function queryCount(mixed $query): int
    {
        return $this->countItems($this->queryItems($query));
    }

    protected function queryItems(mixed $query): iterable
    {
        return $query->get();
    }
}
