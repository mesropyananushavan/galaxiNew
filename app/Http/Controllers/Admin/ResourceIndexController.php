<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use App\Support\AdminResourcePageNormalizer;
use BackedEnum;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Stringable;
use UnitEnum;

class ResourceIndexController extends Controller
{
    public function __construct(
        private readonly AdminResourcePageNormalizer $normalizer,
    ) {
    }

    public function __invoke(string $resource): View
    {
        $pages = config('admin-pages');

        // Shared shell defaults stay config-driven so the layered resource-page
        // composition can evolve without growing controller conditionals.
        $defaults = $this->defaults(config('admin-resource-page-defaults', []));
        $page = $this->page($pages, $resource);

        abort_unless($page !== null, 404);

        $normalizedPage = $this->normalizer->normalize($page);

        return view('admin.resource-index', $normalizedPage + $page + [
            'resourceKey' => $resource,
            'resourceBlocks' => $this->resourceBlocks($defaults),
            'phase' => $this->phase($defaults),
            'pageRationale' => $this->pageRationale($defaults),
        ]);
    }

    private function defaults(mixed $defaults): array
    {
        return is_array($defaults) ? $defaults : [];
    }

    private function page(mixed $pages, string $resource): ?array
    {
        if (! is_array($pages) || ! array_key_exists($resource, $pages) || ! is_array($pages[$resource])) {
            return null;
        }

        $page = $pages[$resource];

        $page = $this->enrichPage($resource, $page);

        if (is_array($page['liveForm'] ?? null)) {
            $page['liveForm']['title'] = $this->resolveLiveFormString(
                $page['liveForm']['title'] ?? null,
                $resource,
                $page,
            );
            $page['liveForm']['description'] = $this->resolveLiveFormNullableString(
                $page['liveForm']['description'] ?? null,
                $resource,
                $page,
            );
            $page['liveForm']['submitLabel'] = $this->resolveLiveFormString(
                $page['liveForm']['submitLabel'] ?? null,
                $resource,
                $page,
            );
            $page['liveForm']['method'] = $this->resolveLiveFormString(
                $page['liveForm']['method'] ?? null,
                $resource,
                $page,
            );
            $page['liveForm']['fields'] = $this->applyLiveFormValues(
                $page['liveForm']['fields'] ?? [],
                $this->resolveLiveFormValues($page['liveForm']['valuesResolver'] ?? null, $resource, $page),
            );

            if (is_string($page['liveForm']['actionRoute'] ?? null)) {
                $page['liveForm']['action'] = route(
                    $page['liveForm']['actionRoute'],
                    $this->liveFormActionParameters(
                        $this->resolveLiveFormRouteParameters(
                            $page['liveForm']['actionRouteParameters'] ?? [],
                            $resource,
                            $page,
                        ),
                    ),
                    absolute: false,
                );
            }

            if (is_string($page['liveForm']['cancelRoute'] ?? null)) {
                $page['liveForm']['cancelAction'] = [
                    'label' => is_string($page['liveForm']['cancelLabel'] ?? null) ? $page['liveForm']['cancelLabel'] : 'Back',
                    'href' => route(
                        $page['liveForm']['cancelRoute'],
                        $this->liveFormActionParameters(
                            $this->resolveLiveFormRouteParameters(
                                $page['liveForm']['cancelRouteParameters'] ?? [],
                                $resource,
                                $page,
                            ),
                        ),
                        absolute: false,
                    ),
                ];
            }
        }

        return $page;
    }

    private function enrichPage(string $resource, array $page): array
    {
        return match ($resource) {
            'card-types' => $this->enrichCardTypesPage($page),
            'shops' => $this->enrichShopsPage($page),
            'cardholders' => $this->enrichCardHoldersPage($page),
            'cards' => $this->enrichCardsPage($page),
            'checks-points' => $this->enrichChecksPointsPage($page),
            'roles-permissions' => $this->enrichRolesPermissionsPage($page),
            'reports' => $this->enrichReportsPage($page),
            'services-rules' => $this->enrichServicesRulesPage($page),
            'gifts' => $this->enrichGiftsPage($page),
            default => $page,
        };
    }

    private function enrichChecksPointsPage(array $page): array
    {
        $receiptPreviews = [
            [
                'key' => 'chk-90421',
                'label' => 'CHK-90421',
                'card' => 'GX-100001',
                'shop' => 'Central Shop',
                'amount' => '24,500',
                'points' => '+245',
                'created' => '2026-04-13 18:42',
                'summary' => [
                    ['label' => 'Selected receipt preview', 'value' => 'CHK-90421'],
                    ['label' => 'Card', 'value' => 'GX-100001'],
                    ['label' => 'Shop context', 'value' => 'Central Shop'],
                    ['label' => 'Receipt status signal', 'value' => 'Positive accrual receipt is already visible for live ledger parity review.'],
                    ['label' => 'Receipt focus', 'value' => 'Start with amount-to-points parity before discussing any later correction path.'],
                    ['label' => 'Receipt handoff signal', 'value' => 'Carry receipt, card, and amount context forward before any later correction discussion begins.'],
                    ['label' => 'Receipt posture', 'value' => 'Fiscal receipt review should remain read-only until Laravel transaction history is verified against the legacy ledger.'],
                    ['label' => 'Evidence priority', 'value' => 'Keep amount, points, and timestamp visible together before comparing this receipt against any later correction narrative.'],
                    ['label' => 'Accrual posture', 'value' => 'Positive accrual receipts should stay parity-first, because receipt math must match the old Galaxy ledger before any correction flow appears.'],
                    ['label' => 'Backend gap', 'value' => $this->checksPointsBackendGap('chk-90421')],
                    ['label' => 'Format guidance', 'value' => 'Keep this receipt in table-first review mode, because operators usually compare amount, points, and timestamp together before opening deeper investigation.'],
                    ['label' => 'Troubleshooting guidance', 'value' => 'Treat this receipt as read-only review until Laravel transaction history and adjustment flows exist.'],
                ],
                'timeline' => [
                    ['title' => 'CHK-90421 selected for receipt review', 'time' => 'Current request', 'description' => 'This preview now keeps the positive-accrual receipt in a dedicated Galaxy review context instead of a flat transaction row.'],
                    ['title' => 'Receipt-first handoff stays visible', 'time' => 'Current request', 'description' => 'Operators should pass along verified receipt, card, and shop context here before discussing any later correction flow.'],
                    ['title' => 'Positive-accrual handoff stays evidence-first', 'time' => 'Current request', 'description' => 'Amount, points, and timestamp should stay visible in the workspace before any future export or correction discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected receipt', 'value' => 'CHK-90421'],
                    ['label' => 'Receipt status signal', 'value' => 'Positive accrual receipt is already visible for live ledger parity review.'],
                    ['label' => 'Receipt posture', 'value' => 'Fiscal receipt review should remain read-only until Laravel transaction history is verified against the legacy ledger.'],
                    ['label' => 'Accrual posture', 'value' => 'Positive point outcomes still need live transaction-domain parity before any adjustment path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => $this->checksPointsBackendGap('chk-90421')],
                ],
            ],
            [
                'key' => 'chk-90407',
                'label' => 'CHK-90407',
                'card' => 'GX-100003',
                'shop' => 'Central Shop',
                'amount' => '11,000',
                'points' => '0',
                'created' => '2026-04-13 14:05',
                'summary' => [
                    ['label' => 'Selected receipt preview', 'value' => 'CHK-90407'],
                    ['label' => 'Card', 'value' => 'GX-100003'],
                    ['label' => 'Shop context', 'value' => 'Central Shop'],
                    ['label' => 'Receipt status signal', 'value' => 'Zero-accrual receipt remains highly visible for parity troubleshooting review.'],
                    ['label' => 'Receipt focus', 'value' => 'Start with the zero-point outcome before expanding into broader rule-gap discussion.'],
                    ['label' => 'Receipt handoff signal', 'value' => 'Carry receipt evidence and zero-point context forward before escalating any rule-gap discussion.'],
                    ['label' => 'Receipt posture', 'value' => 'Receipt lookup should stay read-only until Laravel transaction history is verified against legacy fiscal search behavior.'],
                    ['label' => 'Evidence priority', 'value' => 'Keep receipt, amount, and zero-point outcome visible together before expanding into broader rule troubleshooting.'],
                    ['label' => 'Accrual posture', 'value' => 'Zero-accrual receipts should stay highly visible, because they drive the most parity-sensitive troubleshooting in the old Galaxy flow.'],
                    ['label' => 'Backend gap', 'value' => $this->checksPointsBackendGap('chk-90407')],
                    ['label' => 'Format guidance', 'value' => 'Keep zero-accrual receipts in compact on-screen review first, because operators need amount, points, and rule context together before escalating.'],
                    ['label' => 'Troubleshooting guidance', 'value' => 'Treat this receipt as read-only review until Laravel transaction history and rule-backed explanations exist.'],
                ],
                'timeline' => [
                    ['title' => 'CHK-90407 selected for zero-accrual review', 'time' => 'Current request', 'description' => 'This preview now keeps the zero-accrual receipt in a dedicated Galaxy review context instead of a flat transaction row.'],
                    ['title' => 'Zero-accrual handoff stays cautious', 'time' => 'Current request', 'description' => 'Operators should hand off receipt evidence and shop context here before escalating to future correction workflows.'],
                    ['title' => 'Zero-accrual handoff stays evidence-first', 'time' => 'Current request', 'description' => 'Receipt, amount, and zero-point outcome should stay visible in the workspace before any rule-gap discussion moves forward.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected receipt', 'value' => 'CHK-90407'],
                    ['label' => 'Receipt status signal', 'value' => 'Zero-accrual receipt remains highly visible for parity troubleshooting review.'],
                    ['label' => 'Receipt posture', 'value' => 'Receipt lookup should stay read-only until Laravel transaction history is verified against legacy fiscal search behavior.'],
                    ['label' => 'Accrual posture', 'value' => 'Zero-point outcomes still need rule and receipt parity verification before any adjustment path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => $this->checksPointsBackendGap('chk-90407')],
                ],
            ],
            [
                'key' => 'chk-90388',
                'label' => 'CHK-90388',
                'card' => 'GX-100002',
                'shop' => 'North Shop',
                'amount' => '7,300',
                'points' => '+73',
                'created' => '2026-04-13 10:11',
                'summary' => [
                    ['label' => 'Selected receipt preview', 'value' => 'CHK-90388'],
                    ['label' => 'Card', 'value' => 'GX-100002'],
                    ['label' => 'Shop context', 'value' => 'North Shop'],
                    ['label' => 'Receipt status signal', 'value' => 'Branch receipt is already visible for shop-aware ledger parity review.'],
                    ['label' => 'Receipt focus', 'value' => 'Start with local shop evidence before comparing this receipt against cross-branch behavior.'],
                    ['label' => 'Receipt handoff signal', 'value' => 'Carry branch receipt and shop context forward before any cross-branch troubleshooting expands.'],
                    ['label' => 'Receipt posture', 'value' => 'Branch receipt lookup should stay read-only until Laravel shop filters and transaction history are verified against the old flow.'],
                    ['label' => 'Evidence priority', 'value' => 'Keep shop, amount, and points visible together before comparing this branch receipt against other locations.'],
                    ['label' => 'Accrual posture', 'value' => 'North Shop accrual receipts should stay branch-aware, because cross-shop troubleshooting must preserve local receipt context.'],
                    ['label' => 'Backend gap', 'value' => $this->checksPointsBackendGap('chk-90388')],
                    ['label' => 'Format guidance', 'value' => 'Keep branch receipts in table-first review mode, because operators need the shop, amount, and points visible together before cross-shop comparisons begin.'],
                    ['label' => 'Troubleshooting guidance', 'value' => 'Treat this receipt as read-only review until Laravel transaction history and shop-aware filters exist.'],
                ],
                'timeline' => [
                    ['title' => 'CHK-90388 selected for branch receipt review', 'time' => 'Current request', 'description' => 'This preview now keeps the North Shop receipt in a dedicated Galaxy review context instead of a flat transaction row.'],
                    ['title' => 'Branch-specific handoff stays receipt-first', 'time' => 'Current request', 'description' => 'Operators should hand off receipt and shop context here before any future transaction-edit or correction flow is considered.'],
                    ['title' => 'Branch receipt handoff keeps local evidence visible', 'time' => 'Current request', 'description' => 'Shop, amount, and points should stay visible in the workspace before any cross-branch troubleshooting discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected receipt', 'value' => 'CHK-90388'],
                    ['label' => 'Receipt status signal', 'value' => 'Branch receipt is already visible for shop-aware ledger parity review.'],
                    ['label' => 'Receipt posture', 'value' => 'Branch receipt lookup should stay read-only until Laravel shop filters and transaction history are verified against the old flow.'],
                    ['label' => 'Accrual posture', 'value' => 'Positive branch accrual outcomes still need live transaction-domain parity before any adjustment path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => $this->checksPointsBackendGap('chk-90388')],
                ],
            ],
        ];

        $page['actions'] = [
            [
                'label' => 'Find receipt',
                'tone' => 'primary',
                'disabled' => true,
                'disabledReason' => $this->checksPointsCatalogFindReceiptDisabledReason($receiptPreviews),
            ],
            [
                'label' => 'Review accrual gaps',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->checksPointsCatalogReviewGapsDisabledReason($receiptPreviews),
            ],
        ];

        $page['table']['rows'] = collect($receiptPreviews)->map(fn (array $receipt): array => [
            $this->linkedTableCell($receipt['label'], 'admin.checks-points.index', ['receipt' => $receipt['key']]),
            $receipt['card'],
            $receipt['shop'],
            $receipt['amount'],
            $receipt['points'],
            $receipt['created'],
        ])->all();

        $latestReceiptPreview = collect($receiptPreviews)->first();

        if (is_array($latestReceiptPreview)) {
            $page = $this->appendPageAction($page, [
                'label' => sprintf('Review %s receipt', strtolower($latestReceiptPreview['label'])),
                'tone' => 'secondary',
                'href' => route('admin.checks-points.index', ['receipt' => $latestReceiptPreview['key']], absolute: false),
            ]);
        }

        $selectedReceiptPreview = $this->selectedPreviewByKey($receiptPreviews, 'receipt');

        if (! is_array($selectedReceiptPreview)) {
            return $page;
        }

        $page = $this->applySelectedPreviewContext(
            $page,
            $selectedReceiptPreview,
            'admin.checks-points.index',
            'Back to all receipts',
            [
            [
                'label' => 'Find receipt',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->checksPointsSelectedFindReceiptDisabledReason($selectedReceiptPreview),
            ],
            [
                'label' => 'Review accrual gaps',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->checksPointsSelectedReviewGapsDisabledReason($selectedReceiptPreview),
            ],
            ],
        );

        return $page;
    }

    private function checksPointsBackendGap(string $receiptKey): string
    {
        return match ($receiptKey) {
            'chk-90407' => 'Receipt reads, zero-accrual rule traces, and adjustment handlers should stay preview-only until fiscal-search parity is verified.',
            'chk-90388' => 'Branch-aware receipt reads, shop-filter parity, and adjustment handlers should stay preview-only until cross-branch ledger parity is verified.',
            default => 'Receipt reads, transaction tables, and adjustment handlers should stay preview-only until accrual parity is verified.',
        };
    }

    private function enrichServicesRulesPage(array $page): array
    {
        $rulePreviews = [
            [
                'key' => 'birthday-bonus',
                'label' => 'Birthday bonus',
                'scope' => 'All shops',
                'condition' => 'Holder birthday window',
                'effect' => '+10% points',
                'priority' => '10',
                'status' => 'active',
                'summary' => [
                    ['label' => 'Selected rule preview', 'value' => 'Birthday bonus'],
                    ['label' => 'Scope', 'value' => 'All shops'],
                    ['label' => 'Rule status signal', 'value' => 'Active loyalty uplift is already visible for birthday uplift parity review.'],
                    ['label' => 'Rule focus', 'value' => 'Start with birthday eligibility and priority order before discussing any later publish path.'],
                    ['label' => 'Rule handoff signal', 'value' => 'Carry birthday eligibility, scope, and uplift context forward before any later publish discussion begins.'],
                    ['label' => 'Scope posture', 'value' => 'All-shop scope should remain stable until Laravel scope handling is verified against legacy loyalty behavior.'],
                    ['label' => 'Condition posture', 'value' => 'Birthday window logic should stay parity-first, because date-sensitive loyalty rules are easy to drift during migration.'],
                    ['label' => 'Priority posture', 'value' => 'Keep this rule near the top of the preview stack until Laravel priority resolution is verified against the old Galaxy order.'],
                    ['label' => 'Backend gap', 'value' => $this->servicesRulesBackendGap('birthday-bonus')],
                    ['label' => 'Format guidance', 'value' => 'Keep this rule in table-first review mode, because operators usually compare scope, effect, and priority together before discussing publication.' ],
                    ['label' => 'Effect guidance', 'value' => 'Treat the uplift as review-only until accrual calculations and birthday eligibility are backed by Laravel writes.'],
                ],
                'timeline' => [
                    ['title' => 'Birthday bonus selected for rule review', 'time' => 'Current request', 'description' => 'This preview now keeps the highest-visibility loyalty uplift in a dedicated Galaxy review context instead of a flat catalog row.'],
                    ['title' => 'Birthday rule handoff stays parity-first', 'time' => 'Current request', 'description' => 'Operators should carry birthday eligibility and accrual notes in the workspace before trusting any future write flow.'],
                    ['title' => 'Birthday rule handoff keeps parity evidence visible', 'time' => 'Current request', 'description' => 'Scope, priority, and uplift effect should stay visible in the workspace before any publish discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected rule', 'value' => 'Birthday bonus'],
                    ['label' => 'Rule status signal', 'value' => 'Active loyalty uplift is already visible for birthday uplift parity review.'],
                    ['label' => 'Scope posture', 'value' => 'All-shop scope should remain stable until Laravel scope handling is verified against legacy loyalty behavior.'],
                    ['label' => 'Priority posture', 'value' => 'Priority resolution remains preview-only until overlapping rule order is validated in Laravel.'],
                    ['label' => 'Remaining backend gap', 'value' => $this->servicesRulesBackendGap('birthday-bonus')],
                ],
            ],
            [
                'key' => 'partner-card-uplift',
                'label' => 'Partner card uplift',
                'scope' => 'Central Shop',
                'condition' => 'Card type = Partner',
                'effect' => '+5% points',
                'priority' => '20',
                'status' => 'active',
                'summary' => [
                    ['label' => 'Selected rule preview', 'value' => 'Partner card uplift'],
                    ['label' => 'Scope', 'value' => 'Central Shop'],
                    ['label' => 'Rule status signal', 'value' => 'Active scoped uplift is already visible for partner-card uplift parity review.'],
                    ['label' => 'Rule focus', 'value' => 'Start with scoped card-type conditions before comparing this uplift against broader loyalty overlaps.'],
                    ['label' => 'Rule handoff signal', 'value' => 'Carry scoped card-type conditions and branch context forward before any broader publish discussion begins.'],
                    ['label' => 'Scope posture', 'value' => 'Shop-scoped behavior should stay preview-only until Laravel scope checks are verified against legacy branch rules.'],
                    ['label' => 'Condition posture', 'value' => 'Partner-card checks should stay tied to visible card-type parity before any Laravel rule editor opens them up.'],
                    ['label' => 'Priority posture', 'value' => 'This scoped uplift should remain below birthday-wide behavior until legacy overlap order is rechecked.'],
                    ['label' => 'Backend gap', 'value' => $this->servicesRulesBackendGap('partner-card-uplift')],
                    ['label' => 'Format guidance', 'value' => 'Keep scoped uplift rules in compact on-screen review first, because operators need scope, condition, and priority visible together before escalating.' ],
                    ['label' => 'Effect guidance', 'value' => 'Treat the partner uplift as review-only until scoped accrual behavior is backed by Laravel rule writes.'],
                ],
                'timeline' => [
                    ['title' => 'Partner card uplift selected for scope review', 'time' => 'Current request', 'description' => 'This preview now keeps the scoped partner-card rule visible in its own Galaxy review context.'],
                    ['title' => 'Scoped uplift handoff stays branch-aware', 'time' => 'Current request', 'description' => 'Operators should pass along Central Shop scope assumptions here before relying on future rule-editing flows.'],
                    ['title' => 'Scoped uplift handoff keeps branch evidence visible', 'time' => 'Current request', 'description' => 'Scope, condition, and priority should stay visible in the workspace before any publish discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected rule', 'value' => 'Partner card uplift'],
                    ['label' => 'Rule status signal', 'value' => 'Active scoped uplift is already visible for partner-card uplift parity review.'],
                    ['label' => 'Scope posture', 'value' => 'Shop-scoped behavior should stay preview-only until Laravel scope checks are verified against legacy branch rules.'],
                    ['label' => 'Priority posture', 'value' => 'Overlap with broader loyalty rules still needs parity verification before any publish path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => $this->servicesRulesBackendGap('partner-card-uplift')],
                ],
            ],
            [
                'key' => 'night-service-block',
                'label' => 'Night service block',
                'scope' => 'North Shop',
                'condition' => 'Service group = Bar',
                'effect' => 'No accrual',
                'priority' => '30',
                'status' => 'draft',
                'summary' => [
                    ['label' => 'Selected rule preview', 'value' => 'Night service block'],
                    ['label' => 'Scope', 'value' => 'North Shop'],
                    ['label' => 'Rule status signal', 'value' => 'Draft exclusion rule remains safer for bar-service-exclusion parity review before any live-publish-flow discussion.' ],
                    ['label' => 'Rule focus', 'value' => 'Start with the blocking condition and local scope before discussing any later publish decision.' ],
                    ['label' => 'Rule handoff signal', 'value' => 'Carry blocking condition and local scope context forward before any later publish decision expands.' ],
                    ['label' => 'Scope posture', 'value' => 'North Shop exclusions should stay draft-only until scoped exception behavior is verified against the legacy system.' ],
                    ['label' => 'Condition posture', 'value' => 'Bar-service exclusions should remain draft-only until legacy exception behavior is rechecked in Laravel.'],
                    ['label' => 'Priority posture', 'value' => 'Keep this blocking rule below confirmed accrual logic until exclusion order is verified.'],
                    ['label' => 'Backend gap', 'value' => $this->servicesRulesBackendGap('night-service-block')],
                    ['label' => 'Format guidance', 'value' => 'Keep draft exclusion rules in compact on-screen review first, because operators need scope, condition, and effect visible together before discussing publication.' ],
                    ['label' => 'Effect guidance', 'value' => 'Treat the no-accrual effect as a review-only exception until Laravel can safely reproduce the old block semantics.'],
                ],
                'timeline' => [
                    ['title' => 'Night service block selected for exception review', 'time' => 'Current request', 'description' => 'This preview now keeps the draft exclusion rule in a dedicated Galaxy review context instead of leaving it as a flat table row.'],
                    ['title' => 'Draft exclusion handoff stays cautious', 'time' => 'Current request', 'description' => 'Operators should hand off bar-service parity concerns here before any future publish flow is allowed.'],
                    ['title' => 'Draft exclusion handoff keeps parity evidence visible', 'time' => 'Current request', 'description' => 'Scope, blocking condition, and no-accrual effect should stay visible in the workspace before any publish discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected rule', 'value' => 'Night service block'],
                    ['label' => 'Rule status signal', 'value' => 'Draft exclusion rule remains safer for bar-service-exclusion parity review before any live-publish-flow discussion.'],
                    ['label' => 'Scope posture', 'value' => 'North Shop exclusions should stay draft-only until scoped exception behavior is verified against the legacy system.'],
                    ['label' => 'Priority posture', 'value' => 'Blocking-rule order is still preview-only until exclusion precedence is validated in Laravel.'],
                    ['label' => 'Remaining backend gap', 'value' => $this->servicesRulesBackendGap('night-service-block')],
                ],
            ],
        ];

        $page['table']['rows'] = collect($rulePreviews)->map(fn (array $rule): array => [
            $this->linkedTableCell($rule['label'], 'admin.services-rules.index', ['rule' => $rule['key']]),
            $rule['scope'],
            $rule['condition'],
            $rule['effect'],
            $rule['priority'],
            $rule['status'],
        ])->all();

        $latestRulePreview = collect($rulePreviews)->first();

        if (is_array($latestRulePreview)) {
            $page = $this->appendPageAction($page, [
                'label' => sprintf('Review %s rule', strtolower($latestRulePreview['label'])),
                'tone' => 'secondary',
                'href' => route('admin.services-rules.index', ['rule' => $latestRulePreview['key']], absolute: false),
            ]);
        }

        $selectedRulePreview = $this->selectedPreviewByKey($rulePreviews, 'rule');

        if (! is_array($selectedRulePreview)) {
            return $page;
        }

        $page = $this->applySelectedPreviewContext(
            $page,
            $selectedRulePreview,
            'admin.services-rules.index',
            'Back to all rules',
            [
            [
                'label' => 'Review priorities',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->servicesRulesSelectedReviewPrioritiesDisabledReason($selectedRulePreview),
            ],
            [
                'label' => 'Publish Galaxy rule',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->servicesRulesSelectedPublishRuleDisabledReason($selectedRulePreview),
            ],
            ],
        );

        return $page;
    }

    private function enrichGiftsPage(array $page): array
    {
        $giftPreviews = [
            [
                'key' => 'coffee-voucher',
                'label' => 'Coffee voucher',
                'pointsCost' => '150',
                'scope' => 'All shops',
                'stock' => 'Unlimited',
                'status' => 'active',
                'summary' => [
                    ['label' => 'Selected gift preview', 'value' => 'Coffee voucher'],
                    ['label' => 'Points cost', 'value' => '150'],
                    ['label' => 'Gift status signal', 'value' => 'Active all-shop reward is already visible for live all-shop reward parity review.'],
                    ['label' => 'Gift focus', 'value' => 'Start with points cost and stock policy before discussing any later publish path.'],
                    ['label' => 'Gift handoff signal', 'value' => 'Carry points cost, stock policy, and scope context forward before any later publish discussion begins.'],
                    ['label' => 'Evidence priority', 'value' => 'Keep points cost, stock policy, and shop scope visible together before comparing this reward against any later publish narrative.'],
                    ['label' => 'Scope posture', 'value' => 'All-shop rewards should stay parity-first, because wide-scope catalog changes affect the most operators and redemptions.'],
                    ['label' => 'Stock posture', 'value' => 'Unlimited stock can stay reviewable, but warehouse sync assumptions should remain explicit until Laravel inventory writes exist.'],
                    ['label' => 'Backend gap', 'value' => $this->giftsBackendGap('coffee-voucher')],
                    ['label' => 'Format guidance', 'value' => 'Keep this reward in table-first review mode, because operators usually compare scope, stock policy, and points cost together before discussing publication.'],
                    ['label' => 'Redemption guidance', 'value' => 'Treat this reward as review-only until gift CRUD and redemption parity are backed by Laravel flows.'],
                ],
                'timeline' => [
                    ['title' => 'Coffee voucher selected for reward review', 'time' => 'Current request', 'description' => 'This preview now keeps the baseline all-shop reward in a dedicated Galaxy review context instead of a flat catalog row.'],
                    ['title' => 'All-shop reward handoff stays stock-aware', 'time' => 'Current request', 'description' => 'Operators should carry stock assumptions and reward-availability notes here before trusting future publish flows.'],
                    ['title' => 'All-shop reward handoff keeps catalog evidence visible', 'time' => 'Current request', 'description' => 'Points cost, stock policy, and shop scope should stay visible in the workspace before any publish discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected gift', 'value' => 'Coffee voucher'],
                    ['label' => 'Gift status signal', 'value' => 'Active all-shop reward is already visible for live all-shop reward parity review.'],
                    ['label' => 'Scope posture', 'value' => 'All-shop reward coverage should remain stable until Laravel scope handling is verified against the legacy catalog.'],
                    ['label' => 'Stock posture', 'value' => 'Unlimited-stock assumptions still need backend inventory wiring before operators can trust live publish behavior.'],
                    ['label' => 'Remaining backend gap', 'value' => $this->giftsBackendGap('coffee-voucher')],
                ],
            ],
            [
                'key' => 'airport-transfer',
                'label' => 'Airport transfer',
                'pointsCost' => '900',
                'scope' => 'Airport Kiosk',
                'stock' => '12',
                'status' => 'active',
                'summary' => [
                    ['label' => 'Selected gift preview', 'value' => 'Airport transfer'],
                    ['label' => 'Points cost', 'value' => '900'],
                    ['label' => 'Gift status signal', 'value' => 'Active scoped reward is already visible for kiosk reward parity review.'],
                    ['label' => 'Gift focus', 'value' => 'Start with local stock and scope before comparing this reward against broader catalog behavior.'],
                    ['label' => 'Gift handoff signal', 'value' => 'Carry local stock and scope context forward before any broader publish discussion begins.'],
                    ['label' => 'Evidence priority', 'value' => 'Keep stock, scope, and points cost visible together before comparing this reward against broader catalog behavior.'],
                    ['label' => 'Scope posture', 'value' => 'Kiosk-scoped rewards should stay branch-aware, because legacy redemption expectations depended on local availability.'],
                    ['label' => 'Stock posture', 'value' => 'Finite stock should remain review-only until Laravel inventory updates can preserve remaining-quantity parity.'],
                    ['label' => 'Backend gap', 'value' => $this->giftsBackendGap('airport-transfer')],
                    ['label' => 'Format guidance', 'value' => 'Keep kiosk-scoped rewards in compact on-screen review first, because operators need cost, stock, and local scope visible together before escalating.'],
                    ['label' => 'Redemption guidance', 'value' => 'Treat this scoped reward as review-only until stock-aware redemption behavior is backed by Laravel flows.'],
                ],
                'timeline' => [
                    ['title' => 'Airport transfer selected for scoped reward review', 'time' => 'Current request', 'description' => 'This preview now keeps the kiosk-scoped reward in its own Galaxy review context instead of a flat catalog row.'],
                    ['title' => 'Finite-stock handoff stays branch-specific', 'time' => 'Current request', 'description' => 'Operators should hand off Airport Kiosk stock assumptions here before relying on future gift-write flows.'],
                    ['title' => 'Finite-stock handoff keeps kiosk evidence visible', 'time' => 'Current request', 'description' => 'Scope, remaining stock, and points cost should stay visible in the workspace before any publish discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected gift', 'value' => 'Airport transfer'],
                    ['label' => 'Gift status signal', 'value' => 'Active scoped reward is already visible for kiosk reward parity review.'],
                    ['label' => 'Scope posture', 'value' => 'Shop-scoped reward behavior should stay preview-only until Laravel scope checks are verified against legacy kiosk rules.'],
                    ['label' => 'Stock posture', 'value' => 'Finite-stock handling still needs backend inventory wiring before a publish path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => $this->giftsBackendGap('airport-transfer')],
                ],
            ],
            [
                'key' => 'premium-dessert-set',
                'label' => 'Premium dessert set',
                'pointsCost' => '450',
                'scope' => 'Central Shop',
                'stock' => '0',
                'status' => 'paused',
                'summary' => [
                    ['label' => 'Selected gift preview', 'value' => 'Premium dessert set'],
                    ['label' => 'Points cost', 'value' => '450'],
                    ['label' => 'Gift status signal', 'value' => 'Paused zero-stock reward remains safer for zero-stock-recovery-parity review before any reopening-flow discussion.'],
                    ['label' => 'Gift focus', 'value' => 'Start with zero-stock state and reopening risk before discussing any later publish decision.'],
                    ['label' => 'Gift handoff signal', 'value' => 'Carry zero-stock and reopening context forward before any later publish-review discussion expands.'],
                    ['label' => 'Evidence priority', 'value' => 'Keep zero-stock state, shop scope, and points cost visible together before any reopening-flow discussion expands.'],
                    ['label' => 'Scope posture', 'value' => 'Central Shop reward availability should stay parity-first until paused reward behavior matches the legacy catalog.'],
                    ['label' => 'Stock posture', 'value' => 'Zero-stock rewards should remain paused in review mode until Laravel inventory and reopening flows can reproduce the old behavior safely.'],
                    ['label' => 'Backend gap', 'value' => $this->giftsBackendGap('premium-dessert-set')],
                    ['label' => 'Format guidance', 'value' => 'Keep paused zero-stock rewards in compact on-screen review first, because operators need scope, stock, and cost visible together before discussing reopening.'],
                    ['label' => 'Redemption guidance', 'value' => 'Treat this paused reward as review-only until stock recovery and redemption parity are backed by Laravel flows.'],
                ],
                'timeline' => [
                    ['title' => 'Premium dessert set selected for paused reward review', 'time' => 'Current request', 'description' => 'This preview now keeps the zero-stock reward in a dedicated Galaxy review context instead of leaving it as a flat table row.'],
                    ['title' => 'Paused reward handoff stays cautious', 'time' => 'Current request', 'description' => 'Operators should hand off zero-stock and reopening assumptions here before any future publish or stock-update flow is allowed.'],
                    ['title' => 'Paused reward handoff keeps stock evidence visible', 'time' => 'Current request', 'description' => 'Scope, zero-stock state, and points cost should stay visible in the workspace before any reopening-flow discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected gift', 'value' => 'Premium dessert set'],
                    ['label' => 'Gift status signal', 'value' => 'Paused zero-stock reward remains safer for zero-stock-recovery-parity review before any reopening-flow discussion.'],
                    ['label' => 'Scope posture', 'value' => 'Paused shop-scoped reward behavior should stay preview-only until Laravel scope and reopening checks are verified.'],
                    ['label' => 'Stock posture', 'value' => 'Zero-stock handling is still preview-only until inventory sync and recovery behavior are validated in Laravel.'],
                    ['label' => 'Remaining backend gap', 'value' => $this->giftsBackendGap('premium-dessert-set')],
                ],
            ],
            [
                'key' => 'weekend-brunch-pass',
                'label' => 'Weekend brunch pass',
                'pointsCost' => '320',
                'scope' => 'Riverside Shop',
                'stock' => '6',
                'status' => 'paused',
                'summary' => [
                    ['label' => 'Selected gift preview', 'value' => 'Weekend brunch pass'],
                    ['label' => 'Points cost', 'value' => '320'],
                    ['label' => 'Gift status signal', 'value' => 'Paused finite-stock reward remains safer for paused-branch-reopening-parity review before any reopening-flow discussion.'],
                    ['label' => 'Gift focus', 'value' => 'Start with remaining stock and local reopening assumptions before any wider catalog-review discussion begins.'],
                    ['label' => 'Gift handoff signal', 'value' => 'Carry remaining stock and local reopening context forward before any wider catalog-review discussion begins.'],
                    ['label' => 'Evidence priority', 'value' => 'Keep remaining stock, local scope, and points cost visible together before any wider catalog-review discussion begins.'],
                    ['label' => 'Scope posture', 'value' => 'Paused branch rewards should stay locally reviewable, because reopening decisions still depend on shop-specific redemption habits.'],
                    ['label' => 'Stock posture', 'value' => 'Finite paused stock should remain review-only until Laravel inventory updates and reopening flows can preserve remaining-quantity parity.'],
                    ['label' => 'Backend gap', 'value' => $this->giftsBackendGap('weekend-brunch-pass')],
                    ['label' => 'Format guidance', 'value' => 'Keep paused finite-stock rewards in compact on-screen review first, because operators need scope, stock, and reopening posture visible together before escalating.'],
                    ['label' => 'Redemption guidance', 'value' => 'Treat this paused branch reward as review-only until stock-aware reopening and redemption parity are backed by Laravel flows.'],
                ],
                'timeline' => [
                    ['title' => 'Weekend brunch pass selected for paused branch reward review', 'time' => 'Current request', 'description' => 'This preview now keeps the paused finite-stock reward in a dedicated Galaxy review context instead of leaving it as a flat table row.'],
                    ['title' => 'Paused branch reward handoff stays stock-aware', 'time' => 'Current request', 'description' => 'Operators should hand off Riverside Shop reopening assumptions here before any future publish or stock-update flow is allowed.'],
                    ['title' => 'Paused branch reward keeps finite-stock evidence visible', 'time' => 'Current request', 'description' => 'Scope, remaining stock, and points cost should stay visible in the workspace before any reopening-flow discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected gift', 'value' => 'Weekend brunch pass'],
                    ['label' => 'Gift status signal', 'value' => 'Paused finite-stock reward remains safer for paused-branch-reopening-parity review before any reopening-flow discussion.'],
                    ['label' => 'Scope posture', 'value' => 'Paused branch reward behavior should stay preview-only until Laravel scope and reopening checks are verified.'],
                    ['label' => 'Stock posture', 'value' => 'Finite paused stock still needs backend inventory wiring before operators can trust reopening decisions.'],
                    ['label' => 'Remaining backend gap', 'value' => $this->giftsBackendGap('weekend-brunch-pass')],
                ],
            ],
        ];

        $page['table']['rows'] = collect($giftPreviews)->map(fn (array $gift): array => [
            $this->linkedTableCell($gift['label'], 'admin.gifts.index', ['gift' => $gift['key']]),
            $gift['pointsCost'],
            $gift['scope'],
            $gift['stock'],
            $gift['status'],
        ])->all();

        $latestGiftPreview = collect($giftPreviews)->first();

        if (is_array($latestGiftPreview)) {
            $page = $this->appendPageAction($page, [
                'label' => sprintf('Review %s gift', strtolower($latestGiftPreview['label'])),
                'tone' => 'secondary',
                'href' => route('admin.gifts.index', ['gift' => $latestGiftPreview['key']], absolute: false),
            ]);
        }

        $selectedGiftPreview = $this->selectedPreviewByKey($giftPreviews, 'gift');

        if (! is_array($selectedGiftPreview)) {
            return $page;
        }

        $page = $this->applySelectedPreviewContext(
            $page,
            $selectedGiftPreview,
            'admin.gifts.index',
            'Back to all gifts',
            [
            [
                'label' => 'Stock audit',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->giftsSelectedStockAuditDisabledReason($selectedGiftPreview),
            ],
            [
                'label' => 'Publish reward',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->giftsSelectedPublishGiftDisabledReason($selectedGiftPreview),
            ],
            ],
        );

        return $page;
    }

    private function enrichRolesPermissionsPage(array $page): array
    {
        $roles = Role::query()
            ->with(['permissions' => fn ($query) => $query->orderBy('name'), 'users.shop'])
            ->withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();

        if ($roles->isEmpty()) {
            return $page;
        }

        $page['metrics'] = [
            ['label' => 'Active roles', 'value' => (string) $roles->where('is_active', true)->count()],
            ['label' => 'Draft roles', 'value' => (string) $roles->where('is_active', false)->count()],
            ['label' => 'Reviewed roles', 'value' => (string) $roles->filter(fn (Role $role): bool => filled($role->review_note))->count()],
            ['label' => 'Access notes', 'value' => (string) $roles->filter(fn (Role $role): bool => filled($role->access_note))->count()],
            ['label' => 'Assignment notes', 'value' => (string) $roles->filter(fn (Role $role): bool => filled($role->assignment_note))->count()],
            ['label' => 'Permission review notes', 'value' => (string) $roles->flatMap(fn (Role $role) => $role->permissions->pluck('review_note'))->filter(fn (mixed $note): bool => filled($note))->count()],
            ['label' => 'Scoped shops', 'value' => (string) $roles->flatMap(fn (Role $role) => $role->users->pluck('shop_id'))->filter()->unique()->count()],
        ];

        $page['actions'] = [
            [
                'label' => 'New Galaxy role',
                'tone' => 'primary',
                'href' => '#live-form',
            ],
            [
                'label' => 'Review matrix',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->rolesPermissionsCatalogReviewMatrixDisabledReason($roles),
            ],
            [
                'label' => 'Publish access',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->rolesPermissionsCatalogPublishRoleDisabledReason($roles),
            ],
        ];

        $page['table']['rows'] = $roles->map(function (Role $role): array {
            $scope = $role->users->pluck('shop.name')->filter()->unique();
            $permissionPreview = $role->permissions->pluck('name')->take(3)->implode(', ');
            $permissionReviewNote = $role->permissions
                ->pluck('review_note')
                ->first(fn (?string $note): bool => is_string($note) && trim($note) !== '');

            return [
                $this->linkedTableCell($role->name, 'admin.roles-permissions.index', ['role' => $role->id]),
                $scope->isNotEmpty() ? $scope->join(', ') : 'Unscoped in Laravel read slice',
                $permissionPreview !== '' ? $permissionPreview : 'No permissions linked yet',
                $permissionReviewNote !== null ? str($permissionReviewNote)->limit(72)->toString() : 'No permission review note saved yet',
                filled($role->assignment_note) ? str($role->assignment_note)->limit(72)->toString() : 'No assignment note saved yet',
                (string) $role->users_count,
                $role->is_active ? 'active' : 'draft',
            ];
        })->all();

        $latestRole = $roles->sortByDesc('id')->first();

        if ($latestRole !== null) {
            $page = $this->appendPageAction($page, [
                'label' => 'Review latest saved role',
                'tone' => 'secondary',
                'href' => route('admin.roles-permissions.index', ['role' => $latestRole->id], absolute: false),
            ]);
        }

        $selectedRoleId = $this->selectedRecordId('role');

        if ($selectedRoleId < 1) {
            return $page;
        }

        $selectedRole = $roles->firstWhere('id', $selectedRoleId);

        if (! $selectedRole instanceof Role) {
            return $page;
        }

        $scope = $selectedRole->users->pluck('shop.name')->filter()->unique();
        $permissionPreview = $selectedRole->permissions->pluck('name');
        $assignedUserPreview = $selectedRole->users->pluck('name')->filter()->take(3);

        $page['selectedRecordSummary'] = $this->rolesPermissionsSelectedRoleSummary(
            $selectedRole,
            $scope,
            $permissionPreview,
            $assignedUserPreview,
        );

        if (is_array($page['liveForm'] ?? null)) {
            $page['liveForm']['title'] = 'Edit Galaxy role in Laravel';
            $page['liveForm']['description'] = 'Update the selected Galaxy role identity through the shared live form while permission bundles and shop scope remain in review-only mode.';
            $page['liveForm']['method'] = 'PATCH';
            $page['liveForm']['actionRoute'] = 'admin.roles-permissions.update';
            $page['liveForm']['actionRouteParameters'] = [
                'role' => $selectedRole,
            ];
            $page['liveForm']['cancelRoute'] = 'admin.roles-permissions.index';
            $page['liveForm']['cancelLabel'] = 'Create new Galaxy access shell';
            $page['liveForm']['cancelRouteParameters'] = [];
            $page['liveForm']['submitLabel'] = 'Save access changes';
            $page['liveForm']['valuesResolver'] = [
                'name' => $selectedRole->name,
                'slug' => $selectedRole->slug,
                'is_active' => $selectedRole->is_active ? '1' : '0',
                'review_note' => $selectedRole->review_note ?? '',
                'access_note' => $selectedRole->access_note ?? '',
                'assignment_note' => $selectedRole->assignment_note ?? '',
                'scope_rollout' => $this->rolesPermissionsScopeRolloutValue($scope),
                'publish_posture' => $this->rolesPermissionsPublishPostureValue($selectedRole),
            ];
        }

        $page['actions'] = $this->selectedReadContextActions(
            'admin.roles-permissions.index',
            'Back to all roles',
            $selectedRole->name,
            [
            [
                'label' => 'Create new Galaxy access shell',
                'tone' => 'secondary',
                'href' => route('admin.roles-permissions.index', absolute: false).'#live-form',
            ],
            [
                'label' => 'Review matrix',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->rolesPermissionsReviewMatrixDisabledReason($selectedRole),
            ],
            [
                'label' => 'Publish access',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->rolesPermissionsPublishRoleDisabledReason($selectedRole, $scope),
            ],
            ],
        );

        $page['activityTimeline'] = $this->rolesPermissionsSelectedRoleTimeline(
            $selectedRole,
            $scope,
            $permissionPreview,
        );

        $page = $this->prependLatestBackendWriteTimelineItem($page);

        $page['dependencyStatus'] = $this->rolesPermissionsSelectedRoleDependencyStatus(
            $selectedRole,
            $scope,
            $permissionPreview,
        );

        $page = $this->appendLatestBackendWriteDependencyStatus($page);

        return $page;
    }

    private function enrichCardsPage(array $page): array
    {
        $cards = Card::query()
            ->with(['shop', 'holder', 'type'])
            ->orderBy('number')
            ->get();
        $shopOptions = Shop::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Shop $shop): array => ['value' => (string) $shop->id, 'label' => $shop->name])
            ->values()
            ->all();
        $cardTypeOptions = CardType::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (CardType $cardType): array => ['value' => (string) $cardType->id, 'label' => $cardType->name])
            ->values()
            ->all();

        if (is_array($page['liveForm'] ?? null)) {
            $page['liveForm']['fields'] = collect($page['liveForm']['fields'] ?? [])
                ->map(function (array $field) use ($shopOptions, $cardTypeOptions): array {
                    if (($field['name'] ?? null) === 'shop_id') {
                        $field['options'] = $shopOptions;
                        $field['value'] = $field['value'] !== '' ? $field['value'] : ($shopOptions[0]['value'] ?? '');
                    }

                    if (($field['name'] ?? null) === 'card_type_id') {
                        $field['options'] = $cardTypeOptions;
                        $field['value'] = $field['value'] !== '' ? $field['value'] : ($cardTypeOptions[0]['value'] ?? '');
                    }

                    return $field;
                })
                ->all();
        }

        $page['actions'] = [
            [
                'label' => 'New Galaxy card',
                'tone' => 'primary',
                'href' => '#live-form',
            ],
            [
                'label' => 'Review blocked cards',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardsCatalogReviewBlockedDisabledReason($cards),
            ],
        ];

        if ($cards->isEmpty()) {
            return $page;
        }

        $adminUser = $this->adminUser();
        $accessibleCards = $this->filterShopScopedRecords(
            $cards,
            fn (Card $card): ?Shop => $card->shop,
        );

        $page['metrics'] = [
            ['label' => 'Active cards', 'value' => (string) $cards->where('status', 'active')->count()],
            ['label' => 'Draft cards', 'value' => (string) $cards->where('status', 'draft')->count()],
            ['label' => 'Blocked cards', 'value' => (string) $cards->where('status', 'blocked')->count()],
            ['label' => 'Issued cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->issued_at !== null)->count()],
            ['label' => 'Pre-activation cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->issued_at !== null && $card->activated_at === null)->count()],
            ['label' => 'Holder-linked cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->card_holder_id !== null)->count()],
            ['label' => 'Assignment-ready cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->card_holder_id !== null)->count()],
            ['label' => 'Assignment-pending cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->card_holder_id === null)->count()],
            ['label' => 'Issued holder-linked cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->issued_at !== null && $card->card_holder_id !== null)->count()],
            ['label' => 'Issued unassigned cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->issued_at !== null && $card->card_holder_id === null)->count()],
            ['label' => 'Pre-activation holder-linked cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->issued_at !== null && $card->activated_at === null && $card->card_holder_id !== null)->count()],
            ['label' => 'Pre-activation unassigned cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->issued_at !== null && $card->activated_at === null && $card->card_holder_id === null)->count()],
            ['label' => 'Unassigned cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->card_holder_id === null)->count()],
            ['label' => 'Active holder-linked cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->status === 'active' && $card->card_holder_id !== null)->count()],
            ['label' => 'Active unassigned cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->status === 'active' && $card->card_holder_id === null)->count()],
            ['label' => 'Blocked pre-activation cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->status === 'blocked' && $card->issued_at !== null && $card->activated_at === null)->count()],
            ['label' => 'Blocked activated cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->status === 'blocked' && $card->activated_at !== null)->count()],
            ['label' => 'Blocked cards with holders', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->status === 'blocked' && $card->card_holder_id !== null)->count()],
            ['label' => 'Blocked unassigned cards', 'value' => (string) $cards->filter(fn (Card $card): bool => $card->status === 'blocked' && $card->card_holder_id === null)->count()],
            ['label' => 'Reviewed cards', 'value' => (string) $cards->filter(fn (Card $card): bool => filled($card->review_note))->count()],
        ];

        $page['table']['rows'] = $cards->map(fn (Card $card): array => [
            $this->cannotAccessRecordShop($adminUser, $card->shop)
                ? $card->number
                : $this->linkedTableCell($card->number, 'admin.cards.index', ['card' => $card->id]),
            $card->holder?->full_name ?? 'Unassigned',
            $card->type?->name ?? 'Unknown',
            filled($card->review_note) ? str($card->review_note)->limit(72)->toString() : 'No review note saved yet',
            $card->shop?->name ?? 'Unassigned',
            $card->status,
            $card->issued_at?->format('Y-m-d') ?? '—',
            $card->activated_at?->format('Y-m-d') ?? '—',
        ])->all();

        $latestCard = $accessibleCards->sortByDesc('id')->first();

        if ($latestCard !== null) {
            $page = $this->appendPageAction($page, [
                'label' => 'Review latest saved card',
                'tone' => 'secondary',
                'href' => route('admin.cards.index', ['card' => $latestCard->id], absolute: false),
            ]);
        }

        $selectedCardId = $this->selectedRecordId('card');

        if ($selectedCardId < 1) {
            return $page;
        }

        $selectedCard = $cards->firstWhere('id', $selectedCardId);

        if (! $selectedCard instanceof Card) {
            return $page;
        }

        if ($this->cannotAccessRecordShop($adminUser, $selectedCard->shop)) {
            return $page;
        }

        $page['selectedRecordSummary'] = $this->cardsSelectedCardSummary($selectedCard);

        if (is_array($page['liveForm'] ?? null)) {
            $page['liveForm']['title'] = 'Edit Galaxy card in Laravel';
            $page['liveForm']['description'] = 'Update the selected Galaxy card through the shared live form while holder assignment, dispute handling, and replacement flows remain review-only.';
            $page['liveForm']['method'] = 'PATCH';
            $page['liveForm']['actionRoute'] = 'admin.cards.update';
            $page['liveForm']['actionRouteParameters'] = [
                'card' => $selectedCard,
            ];
            $page['liveForm']['cancelRoute'] = 'admin.cards.index';
            $page['liveForm']['cancelLabel'] = 'Create new Galaxy card shell';
            $page['liveForm']['cancelRouteParameters'] = [];
            $page['liveForm']['submitLabel'] = 'Save inventory changes';
            $page['liveForm']['valuesResolver'] = [
                'shop_id' => $selectedCard->shop_id !== null ? (string) $selectedCard->shop_id : '',
                'card_type_id' => $selectedCard->card_type_id !== null ? (string) $selectedCard->card_type_id : '',
                'number' => $selectedCard->number,
                'status' => $selectedCard->status,
                'issued_at' => $selectedCard->issued_at?->format('Y-m-d H:i:s') ?? '',
                'activated_at' => $selectedCard->activated_at?->format('Y-m-d H:i:s') ?? '',
                'review_note' => $selectedCard->review_note ?? '',
            ];
        }

        $page['actions'] = $this->selectedReadContextActions(
            'admin.cards.index',
            'Back to all cards',
            $selectedCard->number,
            [
            [
                'label' => 'Review blocked cards',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardsSelectedReviewBlockedDisabledReason($selectedCard),
            ],
            ],
        );

        $page['activityTimeline'] = [
            [
                'title' => sprintf('%s selected for Laravel review', $selectedCard->number),
                'time' => 'Current request',
                'description' => 'The shared cards workspace is now loading this saved inventory record from Laravel data instead of only static preview rows.',
            ],
            [
                'title' => sprintf('%s status reflected from model state', $selectedCard->number),
                'time' => 'Current request',
                'description' => sprintf('This card is currently marked as %s in Laravel and the management context now mirrors that state.', $selectedCard->status),
            ],
            [
                'title' => sprintf('%s lifecycle freshness reflected from model state', $selectedCard->number),
                'time' => 'Current request',
                'description' => $this->cardsLifecycleFreshnessDescription($selectedCard),
            ],
            [
                'title' => sprintf('%s last saved timestamp reflected from model state', $selectedCard->number),
                'time' => 'Current request',
                'description' => sprintf('The latest saved Laravel timestamp for this card is %s, giving operators a concrete checkpoint for the current inventory shell.', $this->cardsLastSavedLabel($selectedCard)),
            ],
            [
                'title' => sprintf('%s review note reflected from model state', $selectedCard->number),
                'time' => 'Current request',
                'description' => $selectedCard->review_note !== null && trim($selectedCard->review_note) !== ''
                    ? sprintf('The current Laravel card review note says: %s', $selectedCard->review_note)
                    : 'No Laravel card review note is saved yet, so inventory handoff context still depends on the surrounding workspace cues.',
            ],
            [
                'title' => 'Inventory handoff stays visible in the workspace',
                'time' => 'Current request',
                'description' => $this->cardsInventoryTimelineHandoffDescription($selectedCard),
            ],
        ];

        $page['dependencyStatus'] = $this->cardsSelectedCardDependencyStatus($selectedCard);

        return $page;
    }

    private function enrichCardHoldersPage(array $page): array
    {
        $cardHolders = CardHolder::query()
            ->with(['shop'])
            ->withCount('cards')
            ->orderBy('full_name')
            ->get();
        $shopOptions = Shop::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Shop $shop): array => ['value' => (string) $shop->id, 'label' => $shop->name])
            ->values()
            ->all();

        if (is_array($page['liveForm'] ?? null)) {
            $page['liveForm']['fields'] = collect($page['liveForm']['fields'] ?? [])
                ->map(function (array $field) use ($shopOptions): array {
                    if (($field['name'] ?? null) === 'shop_id') {
                        $field['options'] = $shopOptions;
                        $field['value'] = $field['value'] !== '' ? $field['value'] : ($shopOptions[0]['value'] ?? '');
                    }

                    return $field;
                })
                ->all();
        }

        $page['actions'] = [
            [
                'label' => 'New Galaxy holder',
                'tone' => 'primary',
                'href' => '#live-form',
            ],
            [
                'label' => 'Review recent activity',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardholdersCatalogReviewActivityDisabledReason($cardHolders),
            ],
        ];

        if ($cardHolders->isEmpty()) {
            return $page;
        }

        $adminUser = $this->adminUser();
        $accessibleCardHolders = $this->filterShopScopedRecords(
            $cardHolders,
            fn (CardHolder $cardHolder): ?Shop => $cardHolder->shop,
        );

        $page['metrics'] = [
            ['label' => 'Active holders', 'value' => (string) $cardHolders->where('is_active', true)->count()],
            ['label' => 'Inactive holders', 'value' => (string) $cardHolders->where('is_active', false)->count()],
            ['label' => 'Active-branch holders', 'value' => (string) $cardHolders->filter(fn (CardHolder $cardHolder): bool => (bool) $cardHolder->shop?->is_active)->count()],
            ['label' => 'Paused-branch holders', 'value' => (string) $cardHolders->filter(fn (CardHolder $cardHolder): bool => $cardHolder->shop !== null && ! (bool) $cardHolder->shop->is_active)->count()],
            ['label' => 'Active linked holders', 'value' => (string) $cardHolders->filter(fn (CardHolder $cardHolder): bool => $cardHolder->is_active && $cardHolder->cards_count > 0)->count()],
            ['label' => 'Inactive linked holders', 'value' => (string) $cardHolders->filter(fn (CardHolder $cardHolder): bool => ! $cardHolder->is_active && $cardHolder->cards_count > 0)->count()],
            ['label' => 'Active unlinked holders', 'value' => (string) $cardHolders->filter(fn (CardHolder $cardHolder): bool => $cardHolder->is_active && $cardHolder->cards_count === 0)->count()],
            ['label' => 'Inactive unlinked holders', 'value' => (string) $cardHolders->filter(fn (CardHolder $cardHolder): bool => ! $cardHolder->is_active && $cardHolder->cards_count === 0)->count()],
            ['label' => 'Active-branch linked holders', 'value' => (string) $cardHolders->filter(fn (CardHolder $cardHolder): bool => (bool) $cardHolder->shop?->is_active && $cardHolder->cards_count > 0)->count()],
            ['label' => 'Paused-branch unlinked holders', 'value' => (string) $cardHolders->filter(fn (CardHolder $cardHolder): bool => $cardHolder->shop !== null && ! (bool) $cardHolder->shop->is_active && $cardHolder->cards_count === 0)->count()],
            ['label' => 'Reviewed holders', 'value' => (string) $cardHolders->filter(fn (CardHolder $cardHolder): bool => filled($cardHolder->review_note))->count()],
            ['label' => 'Linked cards', 'value' => (string) $cardHolders->sum('cards_count')],
        ];

        $page['table']['rows'] = $cardHolders->map(fn (CardHolder $cardHolder): array => [
            $this->cannotAccessRecordShop($adminUser, $cardHolder->shop)
                ? $cardHolder->full_name
                : $this->linkedTableCell($cardHolder->full_name, 'admin.cardholders.index', ['cardholder' => $cardHolder->id]),
            $cardHolder->phone ?? '—',
            filled($cardHolder->review_note) ? str($cardHolder->review_note)->limit(72)->toString() : 'No review note saved yet',
            $cardHolder->shop?->name ?? 'Unassigned',
            (string) $cardHolder->cards_count,
            $cardHolder->is_active ? 'active' : 'inactive',
            $cardHolder->updated_at?->format('Y-m-d') ?? '—',
        ])->all();

        $latestCardHolder = $accessibleCardHolders->sortByDesc('id')->first();

        if ($latestCardHolder !== null) {
            $page = $this->appendPageAction($page, [
                'label' => 'Review latest saved holder',
                'tone' => 'secondary',
                'href' => route('admin.cardholders.index', ['cardholder' => $latestCardHolder->id], absolute: false),
            ]);
        }

        $selectedCardHolderId = $this->selectedRecordId('cardholder');

        if ($selectedCardHolderId < 1) {
            return $page;
        }

        $selectedCardHolder = $cardHolders->firstWhere('id', $selectedCardHolderId);

        if (! $selectedCardHolder instanceof CardHolder) {
            return $page;
        }

        if ($this->cannotAccessRecordShop($adminUser, $selectedCardHolder->shop)) {
            return $page;
        }

        $page['selectedRecordSummary'] = $this->cardholdersSelectedHolderSummary($selectedCardHolder);

        if (is_array($page['liveForm'] ?? null)) {
            $page['liveForm']['title'] = 'Edit Galaxy holder in Laravel';
            $page['liveForm']['description'] = 'Update the selected Galaxy cardholder through the shared live form while card linkage and activity history remain review-only.';
            $page['liveForm']['method'] = 'PATCH';
            $page['liveForm']['actionRoute'] = 'admin.cardholders.update';
            $page['liveForm']['actionRouteParameters'] = [
                'cardholder' => $selectedCardHolder,
            ];
            $page['liveForm']['cancelRoute'] = 'admin.cardholders.index';
            $page['liveForm']['cancelLabel'] = 'Create new Galaxy holder shell';
            $page['liveForm']['cancelRouteParameters'] = [];
            $page['liveForm']['submitLabel'] = 'Save holder changes';
            $page['liveForm']['valuesResolver'] = [
                'shop_id' => $selectedCardHolder->shop_id !== null ? (string) $selectedCardHolder->shop_id : '',
                'full_name' => $selectedCardHolder->full_name,
                'phone' => $selectedCardHolder->phone ?? '',
                'email' => $selectedCardHolder->email ?? '',
                'is_active' => $selectedCardHolder->is_active ? '1' : '0',
                'review_note' => $selectedCardHolder->review_note ?? '',
            ];
        }

        $page['actions'] = $this->selectedReadContextActions(
            'admin.cardholders.index',
            'Back to all holders',
            $selectedCardHolder->full_name,
            [
            [
                'label' => 'Review recent activity',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardholdersSelectedReviewActivityDisabledReason($selectedCardHolder),
            ],
            ],
        );

        $page['activityTimeline'] = [
            [
                'title' => sprintf('%s selected for Laravel review', $selectedCardHolder->full_name),
                'time' => 'Current request',
                'description' => 'The shared cardholders workspace is now loading this saved holder from Laravel data instead of only static preview rows.',
            ],
            [
                'title' => sprintf('%s status reflected from model state', $selectedCardHolder->full_name),
                'time' => 'Current request',
                'description' => sprintf('This holder is currently marked as %s in Laravel and the management context now mirrors that state.', $selectedCardHolder->is_active ? 'active' : 'inactive'),
            ],
            [
                'title' => sprintf('%s lifecycle freshness reflected from model state', $selectedCardHolder->full_name),
                'time' => 'Current request',
                'description' => $this->cardholdersLifecycleFreshnessDescription($selectedCardHolder),
            ],
            [
                'title' => sprintf('%s last saved timestamp reflected from model state', $selectedCardHolder->full_name),
                'time' => 'Current request',
                'description' => sprintf('The latest saved Laravel timestamp for this holder is %s, giving operators a concrete checkpoint for the current profile shell.', $this->cardholdersLastSavedLabel($selectedCardHolder)),
            ],
            [
                'title' => sprintf('%s review note reflected from model state', $selectedCardHolder->full_name),
                'time' => 'Current request',
                'description' => $selectedCardHolder->review_note !== null && trim($selectedCardHolder->review_note) !== ''
                    ? sprintf('The current Laravel holder review note says: %s', $selectedCardHolder->review_note)
                    : 'No Laravel holder review note is saved yet, so lifecycle handoff context still depends on the surrounding workspace cues.',
            ],
            [
                'title' => 'Holder activity handoff stays visible in the workspace',
                'time' => 'Current request',
                'description' => $this->cardholdersActivityTimelineHandoffDescription($selectedCardHolder),
            ],
        ];

        $page['dependencyStatus'] = $this->cardholdersSelectedHolderDependencyStatus($selectedCardHolder);

        return $page;
    }

    private function enrichShopsPage(array $page): array
    {
        $shops = Shop::query()
            ->withCount(['users', 'cardHolders', 'cards'])
            ->with(['users' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        $page['actions'] = [
            [
                'label' => 'New Galaxy branch',
                'tone' => 'primary',
                'href' => '#live-form',
            ],
            [
                'label' => 'Review branch scope',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->shopsCatalogReviewScopeDisabledReason($shops),
            ],
        ];

        if ($shops->isEmpty()) {
            return $page;
        }

        $adminUser = $this->adminUser();
        $accessibleShops = $this->filterShopScopedRecords(
            $shops,
            fn (Shop $shop): ?Shop => $shop,
        );

        $page['metrics'] = [
            ['label' => 'Active shops', 'value' => (string) $shops->where('is_active', true)->count()],
            ['label' => 'Paused shops', 'value' => (string) $shops->where('is_active', false)->count()],
            ['label' => 'Reviewed shops', 'value' => (string) $shops->filter(fn (Shop $shop): bool => filled($shop->review_note))->count()],
            ['label' => 'Assigned managers', 'value' => (string) $shops->filter(fn (Shop $shop): bool => $shop->users_count > 0)->count()],
        ];

        $page['table']['rows'] = $shops->map(fn (Shop $shop): array => [
            $this->cannotAccessRecordShop($adminUser, $shop)
                ? $shop->name
                : $this->linkedTableCell($shop->name, 'admin.shops.index', ['shop' => $shop->id]),
            $shop->code,
            filled($shop->review_note) ? str($shop->review_note)->limit(72)->toString() : 'No review note saved yet',
            $shop->users->first()?->name ?? 'Unassigned',
            (string) $shop->card_holders_count,
            (string) $shop->cards_count,
            $shop->is_active ? 'active' : 'paused',
        ])->all();

        $latestShop = $accessibleShops->sortByDesc('id')->first();

        if ($latestShop !== null) {
            $page = $this->appendPageAction($page, [
                'label' => 'Review latest saved shop',
                'tone' => 'secondary',
                'href' => route('admin.shops.index', ['shop' => $latestShop->id], absolute: false),
            ]);
        }

        $selectedShopId = $this->selectedRecordId('shop');

        if ($selectedShopId < 1) {
            return $page;
        }

        $selectedShop = $shops->firstWhere('id', $selectedShopId);

        if (! $selectedShop instanceof Shop) {
            return $page;
        }

        if ($this->cannotAccessRecordShop($adminUser, $selectedShop)) {
            return $page;
        }

        $page['selectedRecordSummary'] = $this->shopsSelectedShopSummary($selectedShop);

        if (is_array($page['liveForm'] ?? null)) {
            $page['liveForm']['title'] = 'Edit Galaxy branch in Laravel';
            $page['liveForm']['description'] = 'Update the selected Galaxy branch through the shared live form while manager reassignment and scope changes remain review-only.';
            $page['liveForm']['method'] = 'PATCH';
            $page['liveForm']['actionRoute'] = 'admin.shops.update';
            $page['liveForm']['actionRouteParameters'] = [
                'shop' => $selectedShop,
            ];
            $page['liveForm']['cancelRoute'] = 'admin.shops.index';
            $page['liveForm']['cancelLabel'] = 'Create new Galaxy branch shell';
            $page['liveForm']['cancelRouteParameters'] = [];
            $page['liveForm']['submitLabel'] = 'Save branch changes';
            $page['liveForm']['valuesResolver'] = [
                'name' => $selectedShop->name,
                'code' => $selectedShop->code,
                'is_active' => $selectedShop->is_active ? '1' : '0',
                'review_note' => $selectedShop->review_note ?? '',
            ];
        }

        $page['actions'] = $this->selectedReadContextActions(
            'admin.shops.index',
            'Back to all shops',
            $selectedShop->name,
            [
            [
                'label' => 'Review branch scope',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->shopsSelectedReviewScopeDisabledReason($selectedShop),
            ],
            ],
        );

        $page['activityTimeline'] = [
            [
                'title' => sprintf('%s selected for Laravel review', $selectedShop->name),
                'time' => 'Current request',
                'description' => 'The shared shops workspace is now loading this saved branch from Laravel data instead of only static preview rows.',
            ],
            [
                'title' => sprintf('%s status reflected from model state', $selectedShop->name),
                'time' => 'Current request',
                'description' => sprintf('This branch is currently marked as %s in Laravel and the management context now mirrors that state.', $selectedShop->is_active ? 'active' : 'paused'),
            ],
            [
                'title' => sprintf('%s lifecycle freshness reflected from model state', $selectedShop->name),
                'time' => 'Current request',
                'description' => $this->shopsLifecycleFreshnessDescription($selectedShop),
            ],
            [
                'title' => sprintf('%s last saved timestamp reflected from model state', $selectedShop->name),
                'time' => 'Current request',
                'description' => sprintf('The latest saved Laravel timestamp for this branch is %s, giving operators a concrete checkpoint for the current branch shell.', $this->shopsLastSavedLabel($selectedShop)),
            ],
            [
                'title' => sprintf('%s review note reflected from model state', $selectedShop->name),
                'time' => 'Current request',
                'description' => $selectedShop->review_note !== null && trim($selectedShop->review_note) !== ''
                    ? sprintf('The current Laravel branch review note says: %s', $selectedShop->review_note)
                    : 'No Laravel branch review note is saved yet, so scope handoff context still depends on the surrounding workspace cues.',
            ],
            [
                'title' => 'Branch scope handoff stays visible in the workspace',
                'time' => 'Current request',
                'description' => $this->shopsScopeTimelineHandoffDescription($selectedShop),
            ],
        ];

        $page['dependencyStatus'] = $this->shopsSelectedShopDependencyStatus($selectedShop);

        return $page;
    }

    private function enrichReportsPage(array $page): array
    {
        $shopCount = Shop::query()->count();
        $activeShopCount = Shop::query()->where('is_active', true)->count();
        $cardCount = Card::query()->count();
        $activeCardCount = Card::query()->where('status', 'active')->count();
        $blockedCardCount = Card::query()->where('status', 'blocked')->count();
        $draftCardCount = Card::query()->where('status', 'draft')->count();
        $activatedCardCount = Card::query()->whereNotNull('activated_at')->count();
        $holderLinkedCardCount = Card::query()->whereNotNull('card_holder_id')->count();
        $unassignedCardCount = $cardCount - $holderLinkedCardCount;
        $activatedHolderLinkedCardCount = Card::query()->whereNotNull('activated_at')->whereNotNull('card_holder_id')->count();
        $blockedHolderLinkedCardCount = Card::query()->where('status', 'blocked')->whereNotNull('card_holder_id')->count();
        $draftHolderLinkedCardCount = Card::query()->where('status', 'draft')->whereNotNull('card_holder_id')->count();
        $activeShopHolderLinkedCardCount = Card::query()->whereNotNull('card_holder_id')->whereHas('shop', fn ($query) => $query->where('is_active', true))->count();
        $pausedShopHolderLinkedCardCount = Card::query()->whereNotNull('card_holder_id')->whereHas('shop', fn ($query) => $query->where('is_active', false))->count();
        $activeShopUnassignedCardCount = Card::query()->whereNull('card_holder_id')->whereHas('shop', fn ($query) => $query->where('is_active', true))->count();
        $pausedShopUnassignedCardCount = Card::query()->whereNull('card_holder_id')->whereHas('shop', fn ($query) => $query->where('is_active', false))->count();
        $activatedUnassignedCardCount = Card::query()->whereNotNull('activated_at')->whereNull('card_holder_id')->count();
        $blockedUnassignedCardCount = Card::query()->where('status', 'blocked')->whereNull('card_holder_id')->count();
        $draftUnassignedCardCount = Card::query()->where('status', 'draft')->whereNull('card_holder_id')->count();
        $cardHolders = CardHolder::query()->withCount('cards')->with(['shop:id,is_active', 'cards:id,card_holder_id,status,activated_at'])->get();
        $cardHolderCount = $cardHolders->count();
        $activeCardHolderCount = $cardHolders->where('is_active', true)->count();
        $inactiveCardHolderCount = $cardHolders->where('is_active', false)->count();
        $linkedCardHolderCount = $cardHolders->filter(fn (CardHolder $cardHolder): bool => $cardHolder->cards_count > 0)->count();
        $unlinkedCardHolderCount = $cardHolderCount - $linkedCardHolderCount;
        $activeShopCardHolderCount = $cardHolders->filter(fn (CardHolder $cardHolder): bool => (bool) $cardHolder->shop?->is_active)->count();
        $pausedShopCardHolderCount = $cardHolderCount - $activeShopCardHolderCount;
        $activeLinkedCardCount = $cardHolders->sum(fn (CardHolder $cardHolder): int => $cardHolder->cards->where('status', 'active')->count());
        $blockedLinkedCardCount = $cardHolders->sum(fn (CardHolder $cardHolder): int => $cardHolder->cards->where('status', 'blocked')->count());
        $draftLinkedCardCount = $cardHolders->sum(fn (CardHolder $cardHolder): int => $cardHolder->cards->where('status', 'draft')->count());
        $activatedLinkedCardCount = $cardHolders->sum(fn (CardHolder $cardHolder): int => $cardHolder->cards->filter(fn ($card): bool => $card->activated_at !== null)->count());
        $blockedLinkedHolderCount = $cardHolders->filter(fn (CardHolder $cardHolder): bool => $cardHolder->cards->contains(fn ($card): bool => $card->status === 'blocked'))->count();
        $draftLinkedHolderCount = $cardHolders->filter(fn (CardHolder $cardHolder): bool => $cardHolder->cards->contains(fn ($card): bool => $card->status === 'draft'))->count();
        $activeLinkedHolderCount = $cardHolders->filter(fn (CardHolder $cardHolder): bool => $cardHolder->cards->contains(fn ($card): bool => $card->status === 'active'))->count();
        $activatedLinkedHolderCount = $cardHolders->filter(fn (CardHolder $cardHolder): bool => $cardHolder->cards->contains(fn ($card): bool => $card->activated_at !== null))->count();
        $roles = Role::query()->withCount(['permissions', 'users'])->with('users.shop:id,is_active')->get();
        $roleCount = $roles->count();
        $activeRoleCount = $roles->where('is_active', true)->count();
        $permissionLinkedRoleCount = $roles->filter(fn (Role $role): bool => $role->is_active && $role->permissions_count > 0)->count();
        $permissionlessActiveRoleCount = $activeRoleCount - $permissionLinkedRoleCount;
        $assignedPermissionLinkedRoleCount = $roles->filter(fn (Role $role): bool => $role->is_active && $role->permissions_count > 0 && $role->users_count > 0)->count();
        $scopedPermissionLinkedRoleCount = $roles->filter(fn (Role $role): bool => $role->is_active
            && $role->permissions_count > 0
            && $role->users->contains(fn ($user): bool => $user->shop_id !== null))->count();
        $activeBranchPermissionLinkedRoleCount = $roles->filter(fn (Role $role): bool => $role->is_active
            && $role->permissions_count > 0
            && $role->users->contains(fn ($user): bool => $user->shop_id !== null && (bool) $user->shop?->is_active))->count();
        $pausedBranchPermissionLinkedRoleCount = $roles->filter(fn (Role $role): bool => $role->is_active
            && $role->permissions_count > 0
            && $role->users->contains(fn ($user): bool => $user->shop_id !== null && ! (bool) $user->shop?->is_active))->count();
        $draftPermissionLinkedRoleCount = $roles->filter(fn (Role $role): bool => ! $role->is_active && $role->permissions_count > 0)->count();
        $assignedActiveRoleCount = $roles->filter(fn (Role $role): bool => $role->is_active && $role->users_count > 0)->count();
        $unassignedActiveRoleCount = $activeRoleCount - $assignedActiveRoleCount;
        $draftAssignedRoleCount = $roles->filter(fn (Role $role): bool => ! $role->is_active && $role->users_count > 0)->count();
        $assignedStaffCount = (int) $roles->sum('users_count');
        $shopScopedAssignedStaffCount = $roles->sum(fn (Role $role): int => $role->users->filter(fn ($user): bool => $user->shop_id !== null)->count());
        $unscopedAssignedStaffCount = $assignedStaffCount - $shopScopedAssignedStaffCount;
        $activeShopAssignedStaffCount = $roles->sum(fn (Role $role): int => $role->users->filter(fn ($user): bool => $user->shop_id !== null && (bool) $user->shop?->is_active)->count());
        $pausedShopAssignedStaffCount = $shopScopedAssignedStaffCount - $activeShopAssignedStaffCount;

        $page['actions'] = [
            [
                'label' => 'Open live report catalog',
                'tone' => 'primary',
            ],
            [
                'label' => 'Review export presets',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->reportsCatalogPresetDisabledReason($shopCount, $cardCount, $cardHolderCount, $roleCount),
            ],
            [
                'label' => 'Export source snapshot',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->reportsCatalogExportDisabledReason($shopCount, $cardCount, $cardHolderCount, $roleCount),
            ],
        ];

        if ($shopCount === 0 && $cardCount === 0 && $cardHolderCount === 0 && $roleCount === 0) {
            return $page;
        }

        $page['metrics'] = [
            ['label' => 'Live sources', 'value' => (string) collect([$shopCount, $cardCount, $cardHolderCount, $roleCount])->filter(fn (int $count): bool => $count > 0)->count()],
            ['label' => 'Tracked shops', 'value' => (string) $shopCount],
            ['label' => 'Tracked cards', 'value' => (string) $cardCount],
            ['label' => 'Tracked cardholders', 'value' => (string) $cardHolderCount],
            ['label' => 'Tracked roles', 'value' => (string) $roleCount],
        ];

        $reportSources = [
            [
                'key' => 'cards-by-shop',
                'label' => 'Cards by shop',
                'scope' => $shopCount > 0 ? sprintf('%d shops', $shopCount) : 'No shops tracked yet',
                'status' => $cardCount > 0 ? 'live' : 'draft',
                'selectedSummary' => [
                    ['label' => 'Selected report source', 'value' => 'Cards by shop'],
                    ['label' => 'Review mode', 'value' => $cardCount > 0
                        ? 'Live-source review, card inventory already exists in Laravel for shop-level reporting checks.'
                        : 'Draft-safe review, no cards are tracked yet so this source remains a catalog-only planning stub.'],
                    ['label' => 'Source coverage', 'value' => sprintf('%d cards across %d tracked shops are currently available for read-only reporting review.', $cardCount, $shopCount)],
                    ['label' => 'Source status signal', 'value' => $cardCount > 0 && $shopCount > 0
                        ? 'Cards-by-shop source is already visible with live branch inventory for parity review.'
                        : 'Cards-by-shop source remains safer as planning-only review until live branch inventory appears.'],
                    ['label' => 'Source focus', 'value' => 'Start with branch totals and assignment mix before discussing any later export snapshot.'],
                    ['label' => 'Source posture', 'value' => 'Keep branch inventory review on-screen first, then leave grouped export expectations in preview mode until parity is proven.'],
                    ['label' => 'Evidence priority', 'value' => 'Keep branch totals, paused-shop counts, and assigned-versus-unassigned inventory visible together before trusting any export view.'],
                    ['label' => 'Source signal', 'value' => $cardCount > 0 && $shopCount > 0 ? 'live cards and branch coverage visible' : 'cards or branch coverage still pending'],
                    ['label' => 'Laravel input signal', 'value' => $cardCount > 0 && $shopCount > 0 ? 'card and branch inputs are ready for on-screen review' : 'card or branch inputs still need live Laravel coverage'],
                    ['label' => 'Comparison signal', 'value' => $activeShopCount > 0 && $shopCount > $activeShopCount && $activeCardCount > 0 && $blockedCardCount > 0 && $holderLinkedCardCount > 0 && $unassignedCardCount > 0
                        ? 'branch, inventory, and assignment comparison cues are all visible for parity walkthrough'
                        : 'full branch, inventory, and assignment comparison coverage is still pending'],
                    ['label' => 'Branch review readiness', 'value' => $cardCount > 0 && $shopCount > 0
                        ? sprintf('ready for branch-total review across %d live shops', $shopCount)
                        : 'wait for both live branch and card coverage before branch-total review'],
                    ['label' => 'Branch activity signal', 'value' => $activeShopCount > 0 && $shopCount > $activeShopCount
                        ? sprintf('%d live shops are already visible beside %d paused branches for comparison review', $activeShopCount, $shopCount - $activeShopCount)
                        : 'paused branch coverage is still pending for comparison review'],
                    ['label' => 'Inventory state signal', 'value' => $activeCardCount > 0 && $blockedCardCount > 0
                        ? sprintf('%d active cards are already visible beside %d blocked inventory records for parity review', $activeCardCount, $blockedCardCount)
                        : 'blocked inventory coverage is still pending for parity review'],
                    ['label' => 'Assignment linkage signal', 'value' => $holderLinkedCardCount > 0 && $unassignedCardCount > 0
                        ? sprintf('%d holder-linked cards are already visible beside %d unassigned inventory records for parity review', $holderLinkedCardCount, $unassignedCardCount)
                        : 'unassigned inventory coverage is still pending for parity review'],
                    ['label' => 'Activated assignment signal', 'value' => $activatedHolderLinkedCardCount > 0
                        ? sprintf('%d activated holder-linked cards are already visible for live customer inventory review', $activatedHolderLinkedCardCount)
                        : 'activated holder-linked inventory is still pending for parity review'],
                    ['label' => 'Blocked assignment signal', 'value' => $blockedHolderLinkedCardCount > 0
                        ? sprintf('%d blocked holder-linked cards are already visible for dispute and replacement review', $blockedHolderLinkedCardCount)
                        : 'blocked holder-linked inventory is still pending for parity review'],
                    ['label' => 'Draft assignment signal', 'value' => $draftHolderLinkedCardCount > 0
                        ? sprintf('%d draft holder-linked cards are already visible for pre-issuance customer review', $draftHolderLinkedCardCount)
                        : 'draft holder-linked inventory is still pending for parity review'],
                    ['label' => 'Active branch assignment signal', 'value' => $activeShopHolderLinkedCardCount > 0
                        ? sprintf('%d holder-linked cards are already visible in active branches for live branch review', $activeShopHolderLinkedCardCount)
                        : 'active-branch holder-linked inventory is still pending for parity review'],
                    ['label' => 'Paused branch assignment signal', 'value' => $pausedShopHolderLinkedCardCount > 0
                        ? sprintf('%d holder-linked cards are already visible in paused branches for branch-recovery review', $pausedShopHolderLinkedCardCount)
                        : 'paused-branch holder-linked inventory is still pending for parity review'],
                    ['label' => 'Unassigned branch activity signal', 'value' => $activeShopUnassignedCardCount > 0 && $pausedShopUnassignedCardCount > 0
                        ? sprintf('%d unassigned cards are already visible in active branches beside %d unassigned cards in paused shops for parity review', $activeShopUnassignedCardCount, $pausedShopUnassignedCardCount)
                        : 'mixed unassigned branch coverage is still pending for parity review'],
                    ['label' => 'Activated unassigned signal', 'value' => $activatedUnassignedCardCount > 0
                        ? sprintf('%d activated unassigned cards are already visible for inventory recovery review', $activatedUnassignedCardCount)
                        : 'activated unassigned inventory is still pending for parity review'],
                    ['label' => 'Blocked unassigned signal', 'value' => $blockedUnassignedCardCount > 0
                        ? sprintf('%d blocked unassigned cards are already visible for replacement inventory review', $blockedUnassignedCardCount)
                        : 'blocked unassigned inventory is still pending for parity review'],
                    ['label' => 'Draft unassigned signal', 'value' => $draftUnassignedCardCount > 0
                        ? sprintf('%d draft unassigned cards are already visible for pre-issuance inventory review', $draftUnassignedCardCount)
                        : 'draft unassigned inventory is still pending for parity review'],
                    ['label' => 'Draft inventory signal', 'value' => $draftCardCount > 0 && $cardCount > $draftCardCount
                        ? sprintf('%d draft cards are already visible beside %d issued inventory records for parity review', $draftCardCount, $cardCount - $draftCardCount)
                        : 'draft inventory coverage is still pending for parity review'],
                    ['label' => 'Activation signal', 'value' => $activatedCardCount > 0 && $cardCount > $activatedCardCount
                        ? sprintf('%d activated cards are already visible beside %d not-yet-activated inventory records for parity review', $activatedCardCount, $cardCount - $activatedCardCount)
                        : 'activation coverage is still pending for parity review'],
                    ['label' => 'Scope guidance', 'value' => $shopCount > 0
                        ? 'Keep this source centered on branch-by-branch totals, because old Galaxy operators usually compared card inventory by shop before opening broader exports.'
                        : 'No tracked shops exist yet, so branch-level scope review should stay in planning mode only.'],
                    ['label' => 'Default period posture', 'value' => 'Use current snapshot review first, then keep preset periods staged until branch-total parity is verified.'],
                    ['label' => 'Format guidance', 'value' => 'Prefer table-first review here, because branch inventory checks should stay visible on screen before anyone expects export files.'],
                    ['label' => 'Handoff signal', 'value' => $this->reportsCardsByShopHandoffSignal($holderLinkedCardCount, $unassignedCardCount)],
                    ['label' => 'Backend gap', 'value' => $this->reportsCardsByShopBackendGap($cardCount, $shopCount, $holderLinkedCardCount, $unassignedCardCount)],
                    ['label' => 'Preset posture', 'value' => 'Keep period presets preview-only until shop-level totals and export parity are verified.'],
                    ['label' => 'Export posture', 'value' => 'Treat this source as review-only until file export formatting and delivery are validated.'],
                ],
                'timeline' => [
                    ['title' => 'Cards by shop source selected for Laravel review', 'time' => 'Current request', 'description' => sprintf('This reporting view now reflects %d tracked cards across %d shops from the current Laravel foundation.', $cardCount, $shopCount)],
                    ['title' => 'Shop-level inventory parity stays review-only', 'time' => 'Current request', 'description' => 'Counts are live-backed now, but grouped report shaping and export output should stay parity-first until reporting pipeline checks exist.'],
                    ['title' => 'Branch inventory handoff stays on-screen first', 'time' => 'Current request', 'description' => $this->reportsCardsByShopTimelineHandoffDescription($holderLinkedCardCount, $unassignedCardCount)],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected source', 'value' => 'Cards by shop'],
                    ['label' => 'Laravel inputs', 'value' => sprintf('%d cards and %d shops are currently visible to the reporting workspace.', $cardCount, $shopCount)],
                    ['label' => 'Source status signal', 'value' => $cardCount > 0 && $shopCount > 0
                        ? 'Cards-by-shop source is already visible with live branch inventory for parity review.'
                        : 'Cards-by-shop source remains safer as planning-only review until live branch inventory appears.'],
                    ['label' => 'Source signal', 'value' => $cardCount > 0 && $shopCount > 0 ? 'live cards and branch coverage visible' : 'cards or branch coverage still pending'],
                    ['label' => 'Laravel input signal', 'value' => $cardCount > 0 && $shopCount > 0 ? 'card and branch inputs are ready for on-screen review' : 'card or branch inputs still need live Laravel coverage'],
                    ['label' => 'Comparison signal', 'value' => $activeShopCount > 0 && $shopCount > $activeShopCount && $activeCardCount > 0 && $blockedCardCount > 0 && $holderLinkedCardCount > 0 && $unassignedCardCount > 0
                        ? 'branch, inventory, and assignment comparison cues are all visible for parity walkthrough'
                        : 'full branch, inventory, and assignment comparison coverage is still pending'],
                    ['label' => 'Branch review readiness', 'value' => $cardCount > 0 && $shopCount > 0
                        ? sprintf('ready for branch-total review across %d live shops', $shopCount)
                        : 'wait for both live branch and card coverage before branch-total review'],
                    ['label' => 'Branch activity signal', 'value' => $activeShopCount > 0 && $shopCount > $activeShopCount
                        ? sprintf('%d live shops are already visible beside %d paused branches for comparison review', $activeShopCount, $shopCount - $activeShopCount)
                        : 'paused branch coverage is still pending for comparison review'],
                    ['label' => 'Inventory state signal', 'value' => $activeCardCount > 0 && $blockedCardCount > 0
                        ? sprintf('%d active cards are already visible beside %d blocked inventory records for parity review', $activeCardCount, $blockedCardCount)
                        : 'blocked inventory coverage is still pending for parity review'],
                    ['label' => 'Assignment linkage signal', 'value' => $holderLinkedCardCount > 0 && $unassignedCardCount > 0
                        ? sprintf('%d holder-linked cards are already visible beside %d unassigned inventory records for parity review', $holderLinkedCardCount, $unassignedCardCount)
                        : 'unassigned inventory coverage is still pending for parity review'],
                    ['label' => 'Activated assignment signal', 'value' => $activatedHolderLinkedCardCount > 0
                        ? sprintf('%d activated holder-linked cards are already visible for live customer inventory review', $activatedHolderLinkedCardCount)
                        : 'activated holder-linked inventory is still pending for parity review'],
                    ['label' => 'Blocked assignment signal', 'value' => $blockedHolderLinkedCardCount > 0
                        ? sprintf('%d blocked holder-linked cards are already visible for dispute and replacement review', $blockedHolderLinkedCardCount)
                        : 'blocked holder-linked inventory is still pending for parity review'],
                    ['label' => 'Draft assignment signal', 'value' => $draftHolderLinkedCardCount > 0
                        ? sprintf('%d draft holder-linked cards are already visible for pre-issuance customer review', $draftHolderLinkedCardCount)
                        : 'draft holder-linked inventory is still pending for parity review'],
                    ['label' => 'Active branch assignment signal', 'value' => $activeShopHolderLinkedCardCount > 0
                        ? sprintf('%d holder-linked cards are already visible in active branches for live branch review', $activeShopHolderLinkedCardCount)
                        : 'active-branch holder-linked inventory is still pending for parity review'],
                    ['label' => 'Paused branch assignment signal', 'value' => $pausedShopHolderLinkedCardCount > 0
                        ? sprintf('%d holder-linked cards are already visible in paused branches for branch-recovery review', $pausedShopHolderLinkedCardCount)
                        : 'paused-branch holder-linked inventory is still pending for parity review'],
                    ['label' => 'Unassigned branch activity signal', 'value' => $activeShopUnassignedCardCount > 0 && $pausedShopUnassignedCardCount > 0
                        ? sprintf('%d unassigned cards are already visible in active branches beside %d unassigned cards in paused shops for parity review', $activeShopUnassignedCardCount, $pausedShopUnassignedCardCount)
                        : 'mixed unassigned branch coverage is still pending for parity review'],
                    ['label' => 'Activated unassigned signal', 'value' => $activatedUnassignedCardCount > 0
                        ? sprintf('%d activated unassigned cards are already visible for inventory recovery review', $activatedUnassignedCardCount)
                        : 'activated unassigned inventory is still pending for parity review'],
                    ['label' => 'Blocked unassigned signal', 'value' => $blockedUnassignedCardCount > 0
                        ? sprintf('%d blocked unassigned cards are already visible for replacement inventory review', $blockedUnassignedCardCount)
                        : 'blocked unassigned inventory is still pending for parity review'],
                    ['label' => 'Draft unassigned signal', 'value' => $draftUnassignedCardCount > 0
                        ? sprintf('%d draft unassigned cards are already visible for pre-issuance inventory review', $draftUnassignedCardCount)
                        : 'draft unassigned inventory is still pending for parity review'],
                    ['label' => 'Draft inventory signal', 'value' => $draftCardCount > 0 && $cardCount > $draftCardCount
                        ? sprintf('%d draft cards are already visible beside %d issued inventory records for parity review', $draftCardCount, $cardCount - $draftCardCount)
                        : 'draft inventory coverage is still pending for parity review'],
                    ['label' => 'Activation signal', 'value' => $activatedCardCount > 0 && $cardCount > $activatedCardCount
                        ? sprintf('%d activated cards are already visible beside %d not-yet-activated inventory records for parity review', $activatedCardCount, $cardCount - $activatedCardCount)
                        : 'activation coverage is still pending for parity review'],
                    ['label' => 'Scope posture', 'value' => 'Branch-level comparison is the first parity target, so cross-shop shaping should stay conservative until legacy report totals are matched.'],
                    ['label' => 'Grouping posture', 'value' => 'Shop grouping should stay read-only until query shaping is verified against legacy report totals.'],
                    ['label' => 'Handoff signal', 'value' => $this->reportsCardsByShopHandoffSignal($holderLinkedCardCount, $unassignedCardCount)],
                    ['label' => 'Remaining backend gap', 'value' => $this->reportsCardsByShopBackendGap($cardCount, $shopCount, $holderLinkedCardCount, $unassignedCardCount)],
                ],
            ],
            [
                'key' => 'cardholder-status',
                'label' => 'Cardholder status overview',
                'scope' => $cardHolderCount > 0 ? sprintf('%d holders', $cardHolderCount) : 'No holders tracked yet',
                'status' => $cardHolderCount > 0 ? 'live' : 'draft',
                'selectedSummary' => [
                    ['label' => 'Selected report source', 'value' => 'Cardholder status overview'],
                    ['label' => 'Review mode', 'value' => $cardHolderCount > 0
                        ? 'Live-source review, holder status records already exist in Laravel for read-only reporting checks.'
                        : 'Draft-safe review, no cardholders are tracked yet so this source remains a planning-only catalog entry.'],
                    ['label' => 'Source coverage', 'value' => sprintf('%d cardholders are currently available for read-only status reporting review.', $cardHolderCount)],
                    ['label' => 'Source status signal', 'value' => $cardHolderCount > 0
                        ? 'Cardholder-status source is already visible with live lifecycle coverage for parity review.'
                        : 'Cardholder-status source remains safer as planning-only review until live lifecycle coverage appears.'],
                    ['label' => 'Source focus', 'value' => 'Start with active-versus-inactive holder posture before expanding into deeper linkage comparisons.'],
                    ['label' => 'Source posture', 'value' => 'Keep support-style status triage visible first, then leave export-style lifecycle summaries in preview mode until parity is proven.'],
                    ['label' => 'Evidence priority', 'value' => 'Keep holder status counts, linked-versus-unlinked profiles, and blocked-card posture visible together before trusting any export view.'],
                    ['label' => 'Source signal', 'value' => $cardHolderCount > 0 ? 'live holder status coverage visible' : 'holder status coverage pending'],
                    ['label' => 'Laravel input signal', 'value' => $cardHolderCount > 0 ? 'holder status inputs are ready for on-screen review' : 'holder status inputs still need live Laravel coverage'],
                    ['label' => 'Comparison signal', 'value' => $inactiveCardHolderCount > 0 && $activeCardHolderCount > 0 && $linkedCardHolderCount > 0 && $unlinkedCardHolderCount > 0 && $activeLinkedCardCount > 0 && $blockedLinkedCardCount > 0
                        ? 'lifecycle, linkage, and linked-card comparison cues are all visible for parity walkthrough'
                        : 'full lifecycle, linkage, and linked-card comparison coverage is still pending'],
                    ['label' => 'Review readiness', 'value' => $cardHolderCount > 0 ? 'ready for holder-status triage review' : 'wait for live holder coverage before triage review'],
                    ['label' => 'Lifecycle signal', 'value' => $inactiveCardHolderCount > 0 && $activeCardHolderCount > 0
                        ? sprintf('%d inactive holders are already visible beside %d active profiles for lifecycle review', $inactiveCardHolderCount, $activeCardHolderCount)
                        : 'inactive holder coverage is still pending for lifecycle review'],
                    ['label' => 'Card linkage signal', 'value' => $linkedCardHolderCount > 0 && $unlinkedCardHolderCount > 0
                        ? sprintf('%d linked holders are already visible beside %d unlinked profiles for parity review', $linkedCardHolderCount, $unlinkedCardHolderCount)
                        : 'unlinked holder coverage is still pending for parity review'],
                    ['label' => 'Linked card state signal', 'value' => $activeLinkedCardCount > 0 && $blockedLinkedCardCount > 0
                        ? sprintf('%d active linked cards are already visible beside %d blocked linked cards for parity review', $activeLinkedCardCount, $blockedLinkedCardCount)
                        : 'blocked linked-card coverage is still pending for parity review'],
                    ['label' => 'Linked card draft signal', 'value' => $draftLinkedCardCount > 0
                        ? sprintf('%d draft linked cards are already visible for pre-issuance parity review', $draftLinkedCardCount)
                        : 'draft linked-card coverage is still pending for parity review'],
                    ['label' => 'Linked card activation signal', 'value' => $activatedLinkedCardCount > 0
                        ? sprintf('%d activated linked cards are already visible for holder-lifecycle parity review', $activatedLinkedCardCount)
                        : 'activated linked-card coverage is still pending for parity review'],
                    ['label' => 'Blocked holder signal', 'value' => $blockedLinkedHolderCount > 0
                        ? sprintf('%d holder profiles already carry blocked linked-card posture for support review', $blockedLinkedHolderCount)
                        : 'blocked-holder coverage is still pending for support review'],
                    ['label' => 'Draft holder signal', 'value' => $draftLinkedHolderCount > 0
                        ? sprintf('%d holder profiles already carry draft linked-card posture for pre-issuance review', $draftLinkedHolderCount)
                        : 'draft-holder coverage is still pending for pre-issuance review'],
                    ['label' => 'Active holder signal', 'value' => $activeLinkedHolderCount > 0
                        ? sprintf('%d holder profiles already carry active linked-card posture for lifecycle review', $activeLinkedHolderCount)
                        : 'active-holder coverage is still pending for lifecycle review'],
                    ['label' => 'Activated holder signal', 'value' => $activatedLinkedHolderCount > 0
                        ? sprintf('%d holder profiles already carry activated linked-card posture for lifecycle review', $activatedLinkedHolderCount)
                        : 'activated-holder coverage is still pending for lifecycle review'],
                    ['label' => 'Holder branch activity signal', 'value' => $activeShopCardHolderCount > 0 && $pausedShopCardHolderCount > 0
                        ? sprintf('%d holder profiles are already visible in active branches beside %d profiles in paused shops for parity review', $activeShopCardHolderCount, $pausedShopCardHolderCount)
                        : 'paused-branch holder coverage is still pending for parity review'],
                    ['label' => 'Scope guidance', 'value' => 'Keep this source focused on active versus inactive holder posture first, because old Galaxy support flows used status review before deeper profile history.' ],
                    ['label' => 'Default period posture', 'value' => 'Use a current-status review first, then stage preset periods until lifecycle and recency parity are verified.'],
                    ['label' => 'Format guidance', 'value' => 'Prefer a compact on-screen table first, because holder-status review usually started as a fast support triage surface, not an export job.' ],
                    ['label' => 'Handoff signal', 'value' => $this->reportsCardholderStatusHandoffSignal($linkedCardHolderCount, $inactiveCardHolderCount, $blockedLinkedCardCount)],
                    ['label' => 'Backend gap', 'value' => $this->reportsCardholderStatusBackendGap($cardHolderCount, $linkedCardHolderCount, $inactiveCardHolderCount, $blockedLinkedCardCount)],
                    ['label' => 'Preset posture', 'value' => 'Keep status-period presets preview-only until holder lifecycle parity is verified.'],
                    ['label' => 'Export posture', 'value' => 'Treat this source as review-only until summary exports and lifecycle report expectations are validated.'],
                ],
                'timeline' => [
                    ['title' => 'Cardholder status source selected for Laravel review', 'time' => 'Current request', 'description' => sprintf('This reporting view now reflects %d tracked cardholders from the current Laravel foundation.', $cardHolderCount)],
                    ['title' => 'Lifecycle reporting parity stays review-only', 'time' => 'Current request', 'description' => 'Source counts are live-backed now, but period presets and export behavior should stay blocked until reporting parity is verified.'],
                    ['title' => 'Support handoff should keep holder posture visible', 'time' => 'Current request', 'description' => $this->reportsCardholderStatusTimelineHandoffDescription($linkedCardHolderCount, $inactiveCardHolderCount, $blockedLinkedCardCount)],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected source', 'value' => 'Cardholder status overview'],
                    ['label' => 'Laravel inputs', 'value' => sprintf('%d cardholders are currently visible to the reporting workspace.', $cardHolderCount)],
                    ['label' => 'Source status signal', 'value' => $cardHolderCount > 0
                        ? 'Cardholder-status source is already visible with live lifecycle coverage for parity review.'
                        : 'Cardholder-status source remains safer as planning-only review until live lifecycle coverage appears.'],
                    ['label' => 'Source signal', 'value' => $cardHolderCount > 0 ? 'live holder status coverage visible' : 'holder status coverage pending'],
                    ['label' => 'Laravel input signal', 'value' => $cardHolderCount > 0 ? 'holder status inputs are ready for on-screen review' : 'holder status inputs still need live Laravel coverage'],
                    ['label' => 'Comparison signal', 'value' => $inactiveCardHolderCount > 0 && $activeCardHolderCount > 0 && $linkedCardHolderCount > 0 && $unlinkedCardHolderCount > 0 && $activeLinkedCardCount > 0 && $blockedLinkedCardCount > 0
                        ? 'lifecycle, linkage, and linked-card comparison cues are all visible for parity walkthrough'
                        : 'full lifecycle, linkage, and linked-card comparison coverage is still pending'],
                    ['label' => 'Review readiness', 'value' => $cardHolderCount > 0 ? 'ready for holder-status triage review' : 'wait for live holder coverage before triage review'],
                    ['label' => 'Lifecycle signal', 'value' => $inactiveCardHolderCount > 0 && $activeCardHolderCount > 0
                        ? sprintf('%d inactive holders are already visible beside %d active profiles for lifecycle review', $inactiveCardHolderCount, $activeCardHolderCount)
                        : 'inactive holder coverage is still pending for lifecycle review'],
                    ['label' => 'Card linkage signal', 'value' => $linkedCardHolderCount > 0 && $unlinkedCardHolderCount > 0
                        ? sprintf('%d linked holders are already visible beside %d unlinked profiles for parity review', $linkedCardHolderCount, $unlinkedCardHolderCount)
                        : 'unlinked holder coverage is still pending for parity review'],
                    ['label' => 'Linked card state signal', 'value' => $activeLinkedCardCount > 0 && $blockedLinkedCardCount > 0
                        ? sprintf('%d active linked cards are already visible beside %d blocked linked cards for parity review', $activeLinkedCardCount, $blockedLinkedCardCount)
                        : 'blocked linked-card coverage is still pending for parity review'],
                    ['label' => 'Linked card draft signal', 'value' => $draftLinkedCardCount > 0
                        ? sprintf('%d draft linked cards are already visible for pre-issuance parity review', $draftLinkedCardCount)
                        : 'draft linked-card coverage is still pending for parity review'],
                    ['label' => 'Linked card activation signal', 'value' => $activatedLinkedCardCount > 0
                        ? sprintf('%d activated linked cards are already visible for holder-lifecycle parity review', $activatedLinkedCardCount)
                        : 'activated linked-card coverage is still pending for parity review'],
                    ['label' => 'Blocked holder signal', 'value' => $blockedLinkedHolderCount > 0
                        ? sprintf('%d holder profiles already carry blocked linked-card posture for support review', $blockedLinkedHolderCount)
                        : 'blocked-holder coverage is still pending for support review'],
                    ['label' => 'Draft holder signal', 'value' => $draftLinkedHolderCount > 0
                        ? sprintf('%d holder profiles already carry draft linked-card posture for pre-issuance review', $draftLinkedHolderCount)
                        : 'draft-holder coverage is still pending for pre-issuance review'],
                    ['label' => 'Active holder signal', 'value' => $activeLinkedHolderCount > 0
                        ? sprintf('%d holder profiles already carry active linked-card posture for lifecycle review', $activeLinkedHolderCount)
                        : 'active-holder coverage is still pending for lifecycle review'],
                    ['label' => 'Activated holder signal', 'value' => $activatedLinkedHolderCount > 0
                        ? sprintf('%d holder profiles already carry activated linked-card posture for lifecycle review', $activatedLinkedHolderCount)
                        : 'activated-holder coverage is still pending for lifecycle review'],
                    ['label' => 'Holder branch activity signal', 'value' => $activeShopCardHolderCount > 0 && $pausedShopCardHolderCount > 0
                        ? sprintf('%d holder profiles are already visible in active branches beside %d profiles in paused shops for parity review', $activeShopCardHolderCount, $pausedShopCardHolderCount)
                        : 'paused-branch holder coverage is still pending for parity review'],
                    ['label' => 'Scope posture', 'value' => 'Status-first review should stay ahead of deeper segmentation until lifecycle parity and operator lookup habits are matched.'],
                    ['label' => 'Lifecycle posture', 'value' => 'Status aggregation should stay read-only until holder lifecycle and activity parity are verified.'],
                    ['label' => 'Handoff signal', 'value' => $this->reportsCardholderStatusHandoffSignal($linkedCardHolderCount, $inactiveCardHolderCount, $blockedLinkedCardCount)],
                    ['label' => 'Remaining backend gap', 'value' => $this->reportsCardholderStatusBackendGap($cardHolderCount, $linkedCardHolderCount, $inactiveCardHolderCount, $blockedLinkedCardCount)],
                ],
            ],
            [
                'key' => 'role-access',
                'label' => 'Role access coverage',
                'scope' => $roleCount > 0 ? sprintf('%d roles', $roleCount) : 'No roles tracked yet',
                'status' => $roleCount > 0 ? 'live' : 'draft',
                'selectedSummary' => [
                    ['label' => 'Selected report source', 'value' => 'Role access coverage'],
                    ['label' => 'Review mode', 'value' => $roleCount > 0
                        ? 'Live-source review, access roles already exist in Laravel for read-only reporting checks.'
                        : 'Draft-safe review, no roles are tracked yet so this source remains a catalog-only planning stub.'],
                    ['label' => 'Source coverage', 'value' => sprintf('%d roles are currently available for read-only access reporting review.', $roleCount)],
                    ['label' => 'Source status signal', 'value' => $roleCount > 0
                        ? 'Role-access source is already visible with live access coverage for parity review.'
                        : 'Role-access source remains safer as planning-only review until live access coverage appears.'],
                    ['label' => 'Source focus', 'value' => 'Start with role coverage and branch scope visibility before comparing any later export expectations.'],
                    ['label' => 'Source posture', 'value' => 'Keep access scope review in the live workspace first, then leave export-style access summaries in preview mode until parity is proven.'],
                    ['label' => 'Evidence priority', 'value' => 'Keep active roles, permission-linked coverage, and assigned staff scope visible together before trusting any export view.'],
                    ['label' => 'Source signal', 'value' => $roleCount > 0 ? 'live role coverage visible' : 'role coverage pending'],
                    ['label' => 'Laravel input signal', 'value' => $roleCount > 0 ? 'role inputs are ready for on-screen review' : 'role inputs still need live Laravel coverage'],
                    ['label' => 'Access mix signal', 'value' => $permissionLinkedRoleCount > 0 && $assignedStaffCount > 0 && $activeRoleCount > 0
                        ? 'role, bundle, and staffing inputs are jointly visible for access parity walkthrough'
                        : 'combined role, bundle, and staffing coverage is still pending'],
                    ['label' => 'Access readiness', 'value' => $permissionLinkedRoleCount > 0 && $activeRoleCount > 0
                        ? sprintf('%d active roles already carry permission-linked access posture for on-screen review', $permissionLinkedRoleCount)
                        : 'permission-linked active access posture is still pending'],
                    ['label' => 'Assignment signal', 'value' => $assignedStaffCount > 0
                        ? sprintf('%d staff assignments are already visible for access review', $assignedStaffCount)
                        : 'staff assignment coverage is still pending'],
                    ['label' => 'Assignment scope signal', 'value' => $shopScopedAssignedStaffCount > 0 && $unscopedAssignedStaffCount > 0
                        ? sprintf('%d shop-linked staff assignments are already visible beside %d unscoped access assignments for parity review', $shopScopedAssignedStaffCount, $unscopedAssignedStaffCount)
                        : 'unscoped access-assignment coverage is still pending'],
                    ['label' => 'Assignment branch activity signal', 'value' => $activeShopAssignedStaffCount > 0 && $pausedShopAssignedStaffCount > 0
                        ? sprintf('%d shop-linked staff assignments are already visible in active branches beside %d assignments in paused shops for parity review', $activeShopAssignedStaffCount, $pausedShopAssignedStaffCount)
                        : 'paused-branch access-assignment coverage is still pending'],
                    ['label' => 'Staff coverage signal', 'value' => $assignedActiveRoleCount > 0 && $unassignedActiveRoleCount > 0
                        ? sprintf('%d active roles already carry visible staff coverage beside %d unassigned access roles for parity review', $assignedActiveRoleCount, $unassignedActiveRoleCount)
                        : 'unassigned active-role staff coverage is still pending'],
                    ['label' => 'Draft staffing signal', 'value' => $draftAssignedRoleCount > 0
                        ? sprintf('%d draft access roles already carry visible staff assignments that still need activation review', $draftAssignedRoleCount)
                        : 'draft-role staff coverage is still pending'],
                    ['label' => 'Draft bundle signal', 'value' => $draftPermissionLinkedRoleCount > 0
                        ? sprintf('%d draft access roles already carry visible permission bundles that still need activation review', $draftPermissionLinkedRoleCount)
                        : 'draft-role permission-bundle coverage is still pending'],
                    ['label' => 'Assigned bundle signal', 'value' => $assignedPermissionLinkedRoleCount > 0
                        ? sprintf('%d permission-linked roles already carry visible staff assignments for parity review', $assignedPermissionLinkedRoleCount)
                        : 'assigned permission-bundle coverage is still pending'],
                    ['label' => 'Scoped bundle signal', 'value' => $scopedPermissionLinkedRoleCount > 0
                        ? sprintf('%d permission-linked roles already carry shop-linked access scope for parity review', $scopedPermissionLinkedRoleCount)
                        : 'shop-linked permission-bundle coverage is still pending'],
                    ['label' => 'Bundle branch activity signal', 'value' => $activeBranchPermissionLinkedRoleCount > 0 && $pausedBranchPermissionLinkedRoleCount > 0
                        ? sprintf('%d permission-linked roles are already visible in active branches beside %d roles in paused shops for parity review', $activeBranchPermissionLinkedRoleCount, $pausedBranchPermissionLinkedRoleCount)
                        : 'paused-branch permission-bundle coverage is still pending'],
                    ['label' => 'Role state signal', 'value' => $activeRoleCount > 0 && $roleCount > $activeRoleCount
                        ? sprintf('%d active roles are already visible beside %d draft access roles for parity review', $activeRoleCount, $roleCount - $activeRoleCount)
                        : 'draft access-role coverage is still pending'],
                    ['label' => 'Permission bundle signal', 'value' => $permissionLinkedRoleCount > 0 && $permissionlessActiveRoleCount > 0
                        ? sprintf('%d permission-linked roles are already visible beside %d unbundled active roles for parity review', $permissionLinkedRoleCount, $permissionlessActiveRoleCount)
                        : 'unbundled active-role coverage is still pending'],
                    ['label' => 'Scope guidance', 'value' => 'Keep this source centered on role coverage and scope visibility first, because old Galaxy access checks were driven by who could see which branch context.' ],
                    ['label' => 'Default period posture', 'value' => 'Use current access coverage review first, then stage preset periods only after scope and assignment parity are verified.'],
                    ['label' => 'Format guidance', 'value' => 'Prefer table-first review here, because access coverage checks need visible role and scope context before any export workflow is trusted.' ],
                    ['label' => 'Handoff signal', 'value' => $this->reportsRoleAccessHandoffSignal($permissionLinkedRoleCount, $assignedStaffCount)],
                    ['label' => 'Backend gap', 'value' => $this->reportsRoleAccessBackendGap($roleCount, $permissionLinkedRoleCount, $assignedStaffCount)],
                    ['label' => 'Preset posture', 'value' => 'Keep access-report presets preview-only until role and scope parity are verified.'],
                    ['label' => 'Export posture', 'value' => 'Treat this source as review-only until access export expectations and file delivery are validated.'],
                ],
                'timeline' => [
                    ['title' => 'Role access source selected for Laravel review', 'time' => 'Current request', 'description' => sprintf('This reporting view now reflects %d tracked roles from the current Laravel foundation.', $roleCount)],
                    ['title' => 'Access reporting parity stays review-only', 'time' => 'Current request', 'description' => 'Source counts are live-backed now, but grouped role exports and access analytics should stay blocked until reporting parity is verified.'],
                    ['title' => 'Access-review handoff should stay visible in the workspace', 'time' => 'Current request', 'description' => $this->reportsRoleAccessTimelineHandoffDescription($permissionLinkedRoleCount, $assignedStaffCount)],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected source', 'value' => 'Role access coverage'],
                    ['label' => 'Laravel inputs', 'value' => sprintf('%d roles are currently visible to the reporting workspace.', $roleCount)],
                    ['label' => 'Source status signal', 'value' => $roleCount > 0
                        ? 'Role-access source is already visible with live access coverage for parity review.'
                        : 'Role-access source remains safer as planning-only review until live access coverage appears.'],
                    ['label' => 'Source signal', 'value' => $roleCount > 0 ? 'live role coverage visible' : 'role coverage pending'],
                    ['label' => 'Laravel input signal', 'value' => $roleCount > 0 ? 'role inputs are ready for on-screen review' : 'role inputs still need live Laravel coverage'],
                    ['label' => 'Access mix signal', 'value' => $permissionLinkedRoleCount > 0 && $assignedStaffCount > 0 && $activeRoleCount > 0
                        ? 'role, bundle, and staffing inputs are jointly visible for access parity walkthrough'
                        : 'combined role, bundle, and staffing coverage is still pending'],
                    ['label' => 'Access readiness', 'value' => $permissionLinkedRoleCount > 0 && $activeRoleCount > 0
                        ? sprintf('%d active roles already carry permission-linked access posture for on-screen review', $permissionLinkedRoleCount)
                        : 'permission-linked active access posture is still pending'],
                    ['label' => 'Assignment signal', 'value' => $assignedStaffCount > 0
                        ? sprintf('%d staff assignments are already visible for access review', $assignedStaffCount)
                        : 'staff assignment coverage is still pending'],
                    ['label' => 'Assignment scope signal', 'value' => $shopScopedAssignedStaffCount > 0 && $unscopedAssignedStaffCount > 0
                        ? sprintf('%d shop-linked staff assignments are already visible beside %d unscoped access assignments for parity review', $shopScopedAssignedStaffCount, $unscopedAssignedStaffCount)
                        : 'unscoped access-assignment coverage is still pending'],
                    ['label' => 'Assignment branch activity signal', 'value' => $activeShopAssignedStaffCount > 0 && $pausedShopAssignedStaffCount > 0
                        ? sprintf('%d shop-linked staff assignments are already visible in active branches beside %d assignments in paused shops for parity review', $activeShopAssignedStaffCount, $pausedShopAssignedStaffCount)
                        : 'paused-branch access-assignment coverage is still pending'],
                    ['label' => 'Staff coverage signal', 'value' => $assignedActiveRoleCount > 0 && $unassignedActiveRoleCount > 0
                        ? sprintf('%d active roles already carry visible staff coverage beside %d unassigned access roles for parity review', $assignedActiveRoleCount, $unassignedActiveRoleCount)
                        : 'unassigned active-role staff coverage is still pending'],
                    ['label' => 'Draft staffing signal', 'value' => $draftAssignedRoleCount > 0
                        ? sprintf('%d draft access roles already carry visible staff assignments that still need activation review', $draftAssignedRoleCount)
                        : 'draft-role staff coverage is still pending'],
                    ['label' => 'Draft bundle signal', 'value' => $draftPermissionLinkedRoleCount > 0
                        ? sprintf('%d draft access roles already carry visible permission bundles that still need activation review', $draftPermissionLinkedRoleCount)
                        : 'draft-role permission-bundle coverage is still pending'],
                    ['label' => 'Assigned bundle signal', 'value' => $assignedPermissionLinkedRoleCount > 0
                        ? sprintf('%d permission-linked roles already carry visible staff assignments for parity review', $assignedPermissionLinkedRoleCount)
                        : 'assigned permission-bundle coverage is still pending'],
                    ['label' => 'Scoped bundle signal', 'value' => $scopedPermissionLinkedRoleCount > 0
                        ? sprintf('%d permission-linked roles already carry shop-linked access scope for parity review', $scopedPermissionLinkedRoleCount)
                        : 'shop-linked permission-bundle coverage is still pending'],
                    ['label' => 'Bundle branch activity signal', 'value' => $activeBranchPermissionLinkedRoleCount > 0 && $pausedBranchPermissionLinkedRoleCount > 0
                        ? sprintf('%d permission-linked roles are already visible in active branches beside %d roles in paused shops for parity review', $activeBranchPermissionLinkedRoleCount, $pausedBranchPermissionLinkedRoleCount)
                        : 'paused-branch permission-bundle coverage is still pending'],
                    ['label' => 'Role state signal', 'value' => $activeRoleCount > 0 && $roleCount > $activeRoleCount
                        ? sprintf('%d active roles are already visible beside %d draft access roles for parity review', $activeRoleCount, $roleCount - $activeRoleCount)
                        : 'draft access-role coverage is still pending'],
                    ['label' => 'Permission bundle signal', 'value' => $permissionLinkedRoleCount > 0 && $permissionlessActiveRoleCount > 0
                        ? sprintf('%d permission-linked roles are already visible beside %d unbundled active roles for parity review', $permissionLinkedRoleCount, $permissionlessActiveRoleCount)
                        : 'unbundled active-role coverage is still pending'],
                    ['label' => 'Scope posture', 'value' => 'Scope visibility should stay read-only until access-report parity and branch-assignment shaping are verified.'],
                    ['label' => 'Access posture', 'value' => 'Role coverage should stay read-only until access-report parity and scope shaping are verified.'],
                    ['label' => 'Handoff signal', 'value' => $this->reportsRoleAccessHandoffSignal($permissionLinkedRoleCount, $assignedStaffCount)],
                    ['label' => 'Remaining backend gap', 'value' => $this->reportsRoleAccessBackendGap($roleCount, $permissionLinkedRoleCount, $assignedStaffCount)],
                ],
            ],
        ];

        $page['table']['rows'] = collect($reportSources)->map(fn (array $source): array => [
            $this->linkedTableCell($source['label'], 'admin.reports.index', ['source' => $source['key']]),
            $source['scope'],
            'Current snapshot',
            'Table',
            $source['status'],
        ])->all();

        $latestLiveSource = collect($reportSources)->first(fn (array $source): bool => $source['status'] === 'live');

        if (is_array($latestLiveSource)) {
            $page = $this->appendPageAction($page, [
                'label' => sprintf('Review %s source', strtolower($latestLiveSource['label'])),
                'tone' => 'secondary',
                'href' => route('admin.reports.index', ['source' => $latestLiveSource['key']], absolute: false),
            ]);
        }

        $page['notice'] = [
            'title' => 'Reporting workspace is now partially Laravel-backed',
            'description' => 'Catalog metrics and report entry rows now reflect live Galaxy source counts from Laravel models, while presets and exports remain preview-only.',
        ];

        $page['activityTimeline'] = $this->reportsActivityTimeline($shopCount, $cardCount, $cardHolderCount, $roleCount);

        $page['dependencyStatus'] = $this->reportsDependencyStatus($shopCount, $cardCount, $cardHolderCount, $roleCount);

        $selectedReportSource = $this->selectedPreviewByKey($reportSources, 'source');

        if (! is_array($selectedReportSource)) {
            return $page;
        }

        $page['selectedRecordSummary'] = $selectedReportSource['selectedSummary'];
        $page['actions'] = [
            [
                'label' => 'Back to report catalog',
                'tone' => 'primary',
                'href' => route('admin.reports.index', absolute: false),
            ],
            [
                'label' => sprintf('Reviewing: %s', $selectedReportSource['label']),
                'tone' => 'secondary',
            ],
            [
                'label' => 'Review export presets',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->reportsSelectedSourcePresetDisabledReason($selectedReportSource),
            ],
            [
                'label' => 'Export source snapshot',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->reportsSelectedSourceExportDisabledReason($selectedReportSource),
            ],
        ];
        $page['activityTimeline'] = $selectedReportSource['timeline'];
        $page['dependencyStatus'] = $selectedReportSource['dependencyStatus'];

        return $page;
    }

    private function enrichCardTypesPage(array $page): array
    {
        $latestCardType = CardType::query()->latest('id')->first();
        $cardTypes = CardType::query()->orderBy('name')->get();

        if ($cardTypes->isNotEmpty()) {
            $page['metrics'] = [
                ['label' => 'Active tiers', 'value' => (string) $cardTypes->where('is_active', true)->count()],
                ['label' => 'Draft tiers', 'value' => (string) $cardTypes->where('is_active', false)->count()],
                ['label' => 'Reviewed tiers', 'value' => (string) $cardTypes->filter(fn (CardType $cardType): bool => filled($cardType->review_note))->count()],
                ['label' => 'Activation notes', 'value' => (string) $cardTypes->filter(fn (CardType $cardType): bool => filled($cardType->activation_note))->count()],
                ['label' => 'Rollout notes', 'value' => (string) $cardTypes->filter(fn (CardType $cardType): bool => filled($cardType->rollout_note))->count()],
                ['label' => 'Saved types', 'value' => (string) $cardTypes->count()],
            ];
        }

        if ($cardTypes->isNotEmpty()) {
            $page['table']['rows'] = $cardTypes->map(fn (CardType $cardType): array => [
                [
                    'label' => $cardType->name,
                    'href' => route('admin.card-types.index', ['cardType' => $cardType->id], absolute: false).'#live-form',
                ],
                $cardType->slug,
                number_format((float) $cardType->points_rate, 2).'x',
                filled($cardType->rollout_note) ? str($cardType->rollout_note)->limit(72)->toString() : 'No rollout note saved yet',
                $cardType->is_active ? 'Active in Laravel flow' : 'Draft in Laravel flow',
                [
                    'label' => $cardType->is_active ? 'Move to draft' : 'Activate tier',
                    'href' => route('admin.card-types.toggle-status', $cardType, absolute: false),
                    'method' => 'PATCH',
                ],
            ])->all();
        }

        $page['actions'] = [
            [
                'label' => 'New Galaxy tier',
                'tone' => 'primary',
                'href' => '#live-form',
            ],
            [
                'label' => 'Import rules',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardTypesCatalogImportRulesDisabledReason($cardTypes),
            ],
            [
                'label' => 'Publish tier',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardTypesCatalogPublishTypeDisabledReason($cardTypes),
            ],
        ];

        if ($latestCardType !== null) {
            $page = $this->appendPageAction($page, [
                'label' => 'Edit latest saved tier',
                'tone' => 'secondary',
                'href' => route('admin.card-types.index', ['cardType' => $latestCardType->id], absolute: false).'#live-form',
            ]);
        }

        $selectedCardTypeId = $this->selectedRecordId('cardType');

        if ($selectedCardTypeId < 1) {
            return $page;
        }

        $selectedCardType = CardType::query()->withCount('cards')->find($selectedCardTypeId);

        if ($selectedCardType === null || ! is_array($page['liveForm'] ?? null)) {
            return $page;
        }

        $page['selectedRecordSummary'] = $this->cardTypesSelectedTypeSummary($selectedCardType);

        $page = $this->appendCardTypeLatestFlowFeedback($page);

        $page['actions'] = [
            [
                'label' => $this->cardTypesCreateShellActionLabel(),
                'tone' => 'primary',
                'href' => route('admin.card-types.index', absolute: false).'#live-form',
            ],
            [
                'label' => $this->cardTypesToggleStatusActionLabel($selectedCardType),
                'tone' => 'secondary',
                'href' => route('admin.card-types.toggle-status', $selectedCardType, absolute: false),
                'method' => 'PATCH',
            ],
            [
                'label' => $this->cardTypesEditingActionLabel($selectedCardType),
                'tone' => 'secondary',
            ],
            [
                'label' => $this->cardTypesImportRulesActionLabel(),
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardTypesSelectedImportRulesDisabledReason($selectedCardType),
            ],
            [
                'label' => $this->cardTypesPublishActionLabel(),
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardTypesSelectedPublishTypeDisabledReason($selectedCardType),
            ],
        ];

        $page['activityTimeline'] = [
            [
                'title' => $this->cardTypesSelectedForEditFlowTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesSelectedForEditFlowDescription(),
            ],
            [
                'title' => $this->cardTypesStatusTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesStatusTimelineDescription($selectedCardType),
            ],
            [
                'title' => $this->cardTypesLifecycleFreshnessTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesLifecycleFreshnessDescription($selectedCardType),
            ],
            [
                'title' => $this->cardTypesLastSavedTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesLastSavedTimelineDescription($selectedCardType),
            ],
            [
                'title' => $this->cardTypesReviewNoteTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesReviewNoteReflection($selectedCardType),
            ],
            [
                'title' => $this->cardTypesActivationNoteTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesActivationNoteReflection($selectedCardType),
            ],
            [
                'title' => $this->cardTypesRolloutNoteTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesRolloutNoteReflection($selectedCardType),
            ],
            [
                'title' => $this->cardTypesCoverageSignalTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesCoverageSignalTimelineDescription($selectedCardType),
            ],
            [
                'title' => $this->cardTypesCoverageFreshnessTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesCoverageFreshness($selectedCardType),
            ],
            [
                'title' => $this->cardTypesStatusSignalTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesStatusSignal($selectedCardType),
            ],
            [
                'title' => $this->cardTypesFocusTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesFocus($selectedCardType),
            ],
            [
                'title' => $this->cardTypesPostureTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesPosture($selectedCardType),
            ],
            [
                'title' => $this->cardTypesEvidencePriorityTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesEvidencePriority($selectedCardType),
            ],
            [
                'title' => $this->cardTypesCurrentStatusPostureTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesCurrentStatusPosture($selectedCardType),
            ],
            [
                'title' => $this->cardTypesHandoffSignalTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesHandoffSignal($selectedCardType),
            ],
            [
                'title' => $this->cardTypesBackendGapTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesBackendGap($selectedCardType),
            ],
            [
                'title' => $this->cardTypesRuleImportPostureTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesRuleImportPosture($selectedCardType),
            ],
            [
                'title' => $this->cardTypesPublishPostureTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesPublishPosture($selectedCardType),
            ],
            [
                'title' => $this->cardTypesActionGatingTimelineTitle($selectedCardType),
                'time' => 'Current request',
                'description' => $this->cardTypesActionGating($selectedCardType),
            ],
        ];

        $page = $this->prependLatestBackendWriteTimelineItem($page);

        $page['dependencyStatus'] = [
            ['label' => 'Selected record', 'value' => $selectedCardType->name],
            ['label' => 'Edit flow state', 'value' => 'Shared live form is running in request-driven PATCH mode'],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardTypesLifecycleFreshnessLabel($selectedCardType)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardTypesLastSavedLabel($selectedCardType)],
            ['label' => 'Review note', 'value' => $selectedCardType->review_note ?: 'No review note saved yet'],
            ['label' => 'Activation note', 'value' => $selectedCardType->activation_note ?: 'No activation note saved yet'],
            ['label' => 'Activation freshness', 'value' => $this->cardTypesActivationFreshness($selectedCardType)],
            ['label' => 'Rollout note', 'value' => $selectedCardType->rollout_note ?: 'No rollout note saved yet'],
            ['label' => 'Rollout freshness', 'value' => $this->cardTypesRolloutFreshness($selectedCardType)],
            ['label' => 'Coverage signal', 'value' => $this->cardTypesCoverageSignal($selectedCardType)],
            ['label' => 'Coverage freshness', 'value' => $this->cardTypesCoverageFreshness($selectedCardType)],
            ['label' => 'Tier status signal', 'value' => $this->cardTypesStatusSignal($selectedCardType)],
            ['label' => 'Handoff signal', 'value' => $this->cardTypesHandoffSignal($selectedCardType)],
            ['label' => 'Current status posture', 'value' => $this->cardTypesCurrentStatusPosture($selectedCardType)],
            ['label' => 'Rule-import posture', 'value' => $this->cardTypesRuleImportPosture($selectedCardType)],
            ['label' => 'Publish posture', 'value' => $this->cardTypesPublishPosture($selectedCardType)],
            ['label' => 'Action gating', 'value' => $this->cardTypesActionGating($selectedCardType)],
            ['label' => 'Remaining backend gap', 'value' => $this->cardTypesBackendGap($selectedCardType)],
        ];

        $page = $this->appendLatestBackendWriteDependencyStatus($page);

        $page['liveForm']['title'] = $this->cardTypesLiveFormTitle();
        $page['liveForm']['description'] = $this->cardTypesLiveFormDescription();
        $page['liveForm']['method'] = 'PATCH';
        $page['liveForm']['actionRoute'] = 'admin.card-types.update';
        $page['liveForm']['actionRouteParameters'] = [
            'cardType' => $selectedCardType,
        ];
        $page['liveForm']['cancelRoute'] = 'admin.card-types.index';
        $page['liveForm']['cancelLabel'] = $this->cardTypesLiveFormCancelLabel();
        $page['liveForm']['cancelRouteParameters'] = [];
        $page['liveForm']['submitLabel'] = $this->cardTypesLiveFormSubmitLabel();
        $page['liveForm']['valuesResolver'] = [
            'name' => $selectedCardType->name,
            'slug' => $selectedCardType->slug,
            'points_rate' => (string) $selectedCardType->points_rate,
            'is_active' => $selectedCardType->is_active ? '1' : '0',
            'review_note' => $selectedCardType->review_note ?? '',
            'activation_note' => $selectedCardType->activation_note ?? '',
            'rollout_note' => $selectedCardType->rollout_note ?? '',
        ];

        return $page;
    }

    private function cardTypesSelectedTypeSummary(CardType $selectedCardType): array
    {
        return [
            ['label' => 'Selected tier', 'value' => $selectedCardType->name],
            ['label' => 'Slug', 'value' => $selectedCardType->slug],
            ['label' => 'Points rate', 'value' => number_format((float) $selectedCardType->points_rate, 2).'x'],
            ['label' => 'Laravel status', 'value' => $selectedCardType->is_active ? 'active' : 'draft'],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardTypesLifecycleFreshnessLabel($selectedCardType)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardTypesLastSavedLabel($selectedCardType)],
            ['label' => 'Review note', 'value' => $selectedCardType->review_note ?: 'No review note saved yet'],
            ['label' => 'Activation note', 'value' => $selectedCardType->activation_note ?: 'No activation note saved yet'],
            ['label' => 'Activation freshness', 'value' => $this->cardTypesActivationFreshness($selectedCardType)],
            ['label' => 'Rollout note', 'value' => $selectedCardType->rollout_note ?: 'No rollout note saved yet'],
            ['label' => 'Rollout freshness', 'value' => $this->cardTypesRolloutFreshness($selectedCardType)],
            ['label' => 'Coverage signal', 'value' => $this->cardTypesCoverageSignal($selectedCardType)],
            ['label' => 'Coverage freshness', 'value' => $this->cardTypesCoverageFreshness($selectedCardType)],
            ['label' => 'Tier status signal', 'value' => $this->cardTypesStatusSignal($selectedCardType)],
            ['label' => 'Tier focus', 'value' => $this->cardTypesFocus($selectedCardType)],
            ['label' => 'Tier posture', 'value' => $this->cardTypesPosture($selectedCardType)],
            ['label' => 'Evidence priority', 'value' => $this->cardTypesEvidencePriority($selectedCardType)],
            ['label' => 'Handoff signal', 'value' => $this->cardTypesHandoffSignal($selectedCardType)],
            ['label' => 'Backend gap', 'value' => $this->cardTypesBackendGap($selectedCardType)],
            ['label' => 'Status guidance', 'value' => $this->cardTypesStatusGuidance($selectedCardType)],
            ['label' => 'Rule-import blocker', 'value' => $this->cardTypesRuleImportBlocker($selectedCardType)],
            ['label' => 'Publish guidance', 'value' => $this->cardTypesPublishGuidance($selectedCardType)],
            ['label' => 'Readiness signal', 'value' => $this->cardTypesReadinessSignal($selectedCardType)],
        ];
    }

    private function cardTypesCreateShellActionLabel(): string
    {
        return 'Create new Galaxy tier shell';
    }

    private function cardTypesToggleStatusActionLabel(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active ? 'Move to draft' : 'Activate tier';
    }

    private function cardTypesEditingActionLabel(CardType $selectedCardType): string
    {
        return sprintf('Editing: %s', $selectedCardType->name);
    }

    private function cardTypesImportRulesActionLabel(): string
    {
        return 'Import rules';
    }

    private function cardTypesPublishActionLabel(): string
    {
        return 'Publish tier';
    }

    private function cardTypesSelectedForEditFlowTitle(CardType $selectedCardType): string
    {
        return sprintf('%s selected for Laravel edit flow', $selectedCardType->name);
    }

    private function cardTypesSelectedForEditFlowDescription(): string
    {
        return 'The shared card-type form is now loading this saved tier directly from Laravel data instead of preview-only defaults.';
    }

    private function cardTypesLifecycleFreshnessTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s lifecycle freshness reflected from model state', $selectedCardType->name);
    }

    private function cardTypesStatusTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s status reflected from model state', $selectedCardType->name);
    }

    private function cardTypesStatusTimelineDescription(CardType $selectedCardType): string
    {
        return sprintf('This tier is currently marked as %s in Laravel and the management context card now mirrors that state.', $selectedCardType->is_active ? 'active' : 'draft');
    }

    private function cardTypesLastSavedTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s last saved timestamp reflected from model state', $selectedCardType->name);
    }

    private function cardTypesLastSavedTimelineDescription(CardType $selectedCardType): string
    {
        return sprintf('The latest saved Laravel timestamp for this tier is %s, giving operators a concrete checkpoint for the current catalog shell.', $this->cardTypesLastSavedLabel($selectedCardType));
    }

    private function cardTypesReviewNoteTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s review note reflected from model state', $selectedCardType->name);
    }

    private function cardTypesActivationNoteTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s activation note reflected from model state', $selectedCardType->name);
    }

    private function cardTypesRolloutNoteTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s rollout note reflected from model state', $selectedCardType->name);
    }

    private function cardTypesCoverageSignalTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s card coverage signal reflected from model state', $selectedCardType->name);
    }

    private function cardTypesCoverageFreshnessTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s card coverage freshness reflected from model state', $selectedCardType->name);
    }

    private function cardTypesStatusSignalTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s tier status signal reflected from model state', $selectedCardType->name);
    }

    private function cardTypesFocusTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s tier focus reflected from model state', $selectedCardType->name);
    }

    private function cardTypesPostureTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s tier posture reflected from model state', $selectedCardType->name);
    }

    private function cardTypesEvidencePriorityTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s evidence priority reflected from model state', $selectedCardType->name);
    }

    private function cardTypesCurrentStatusPostureTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s current status posture reflected from model state', $selectedCardType->name);
    }

    private function cardTypesHandoffSignalTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s handoff signal reflected from model state', $selectedCardType->name);
    }

    private function cardTypesBackendGapTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s backend gap reflected from model state', $selectedCardType->name);
    }

    private function cardTypesRuleImportPostureTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s rule-import posture reflected from model state', $selectedCardType->name);
    }

    private function cardTypesPublishPostureTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s publish posture reflected from model state', $selectedCardType->name);
    }

    private function cardTypesActionGatingTimelineTitle(CardType $selectedCardType): string
    {
        return sprintf('%s action gating reflected from model state', $selectedCardType->name);
    }

    private function cardTypesCoverageSignalTimelineDescription(CardType $selectedCardType): string
    {
        return sprintf('The current Laravel tier is showing %s in the workspace review shell.', $this->cardTypesCoverageSignal($selectedCardType));
    }

    private function cardTypesLiveFormTitle(): string
    {
        return 'Edit Galaxy tier in Laravel';
    }

    private function cardTypesLiveFormDescription(): string
    {
        return 'Update the selected Galaxy tier through the shared live form without leaving the card-types workspace.';
    }

    private function cardTypesLiveFormCancelLabel(): string
    {
        return $this->cardTypesCreateShellActionLabel();
    }

    private function cardTypesLiveFormSubmitLabel(): string
    {
        return 'Save tier changes';
    }

    private function cardTypesLifecycleFreshnessLabel(CardType $selectedCardType): string
    {
        return $this->lifecycleFreshnessLabel($selectedCardType);
    }

    private function cardTypesCoverageSignal(CardType $selectedCardType): string
    {
        $cardsCount = $selectedCardType->cards_count ?? 0;

        return match (true) {
            $selectedCardType->is_active && $cardsCount > 0 => 'live tier with visible card coverage',
            $selectedCardType->is_active => 'live tier, card coverage still building out',
            $cardsCount > 0 => 'draft tier with visible card coverage',
            default => 'draft tier, card coverage pending',
        };
    }

    private function cardTypesStatusSignal(CardType $selectedCardType): string
    {
        $cardsCount = $selectedCardType->cards_count ?? 0;

        return match (true) {
            $selectedCardType->is_active && $cardsCount > 0 => 'Active tier is already visible with saved card coverage for live tier parity review.',
            $selectedCardType->is_active => 'Active tier is already visible, but card coverage still needs rollout-parity review before any rollout discussion.',
            $cardsCount > 0 => 'Draft tier remains safer for parity review while saved card coverage is already visible.',
            default => 'Draft tier remains safer for visible-card-coverage parity-review before any rollout discussion lands.',
        };
    }

    private function cardTypesFocus(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'Start with saved card coverage, live status, and rollout note clarity before discussing any later publish reversal or rule import step.'
            : 'Start with saved card coverage, draft status, activation readiness, and rollout note clarity before discussing any later rule import step.';
    }

    private function cardTypesPosture(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'Keep live tier review in the workspace first, then leave publish reversal, rule import, and rollout-sensitive moves gated until parity is proven.'
            : 'Keep draft tier review in the workspace first, then leave rule import and publish-style moves gated until parity is proven.';
    }

    private function cardTypesEvidencePriority(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'Keep visible card coverage, live status, activation note, and rollout note together before trusting any later publish reversal or rule import discussion.'
            : 'Keep visible card coverage, activation readiness, and rollout note together before trusting any later rule import discussion.';
    }

    private function cardTypesCurrentStatusPosture(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'Active tiers should stay stable unless parity checks are complete'
            : 'Draft tiers are the safe place for parity-first validation and copy changes';
    }

    private function cardTypesRuleImportPosture(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'Keep imports blocked until active-tier accrual parity is verified'
            : 'Imports can be reviewed in draft mode, but they are still not safe to enable yet';
    }

    private function cardTypesPublishPosture(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'Live tiers need parity confirmation before further publish-style changes'
            : 'Draft tiers should stay unpublished until legacy behavior is mapped more explicitly';
    }

    private function cardTypesActionGating(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'Allow small state corrections only, keep publish-like and import actions gated'
            : 'Allow draft-safe edits and validation only, keep live-facing actions gated';
    }

    private function cardTypesStatusGuidance(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'This tier is live in the current Laravel foundation, so operators should move it back to draft before parity-sensitive rule changes.'
            : 'This tier is still in draft, which keeps it safe for parity checks before operators treat it as live loyalty behavior.';
    }

    private function cardTypesRuleImportBlocker(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'Rule import should stay blocked for this live tier until legacy accrual parity is verified against the active behavior.'
            : 'Rule import is still blocked, but draft state keeps this tier safe for parity-first catalog and accrual checks.';
    }

    private function cardTypesPublishGuidance(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'Treat this tier as already live in Laravel, so publish-like changes should wait for rule parity and operator confirmation.'
            : 'Keep this tier in draft until rule import expectations and old Galaxy behavior are mapped clearly enough to publish safely.';
    }

    private function cardTypesReadinessSignal(CardType $selectedCardType): string
    {
        return $selectedCardType->is_active
            ? 'Partially ready: the tier is live in Laravel, but parity-sensitive follow-up actions should stay gated.'
            : 'Not ready to publish: draft mode is still the holding state for parity validation and rule-import review.';
    }

    private function cardTypesCoverageFreshness(CardType $selectedCardType): string
    {
        $cardsCount = $selectedCardType->cards_count ?? 0;

        return match (true) {
            $selectedCardType->is_active && $cardsCount > 0 => 'Live tier already has saved card coverage anchored in Laravel for rollout review.',
            $selectedCardType->is_active => 'Live tier still needs its first saved card coverage before rollout review can feel grounded.',
            $cardsCount > 0 => 'Draft tier already has saved card coverage anchored in Laravel for parity review.',
            default => 'Draft tier is still waiting on its first saved card coverage before parity review can feel grounded.',
        };
    }

    private function cardTypesLifecycleFreshnessDescription(CardType $selectedCardType): string
    {
        return $this->lifecycleFreshnessDescription(
            $selectedCardType,
            'This tier does not expose complete Laravel timestamps yet, so lifecycle freshness should stay in review-only posture.',
            'This tier was created in Laravel on %s and has not been updated since, so operators are still reviewing the first saved catalog shell.',
            'This tier was first created in Laravel on %s and last updated on %s, so operators are reviewing a catalog shell that has already changed after initial setup.',
        );
    }

    private function cardTypesCatalogImportRulesDisabledReason(mixed $cardTypes): string
    {
        $savedCount = $cardTypes->count();
        $activeCount = $cardTypes->where('is_active', true)->count();

        return match (true) {
            $savedCount === 0 => 'Blocked until the first Laravel-backed tier exists for rule parity review.',
            $activeCount > 0 => 'Blocked until saved tier accrual parity is verified before importing legacy rules.',
            default => 'Blocked until a saved draft tier is ready for parity-first rule review.',
        };
    }

    private function cardTypesCatalogPublishTypeDisabledReason(mixed $cardTypes): string
    {
        $savedCount = $cardTypes->count();
        $activeCount = $cardTypes->where('is_active', true)->count();

        return match (true) {
            $savedCount === 0 => 'Blocked until the first Laravel-backed tier exists before any publish-style rollout.',
            $activeCount > 0 => 'Blocked until saved live tiers clear Galaxy tier rollout parity before any broader catalog move.',
            default => 'Blocked until a saved draft tier clears rollout parity before any publish-like move.',
        };
    }

    private function cardTypesSelectedImportRulesDisabledReason(CardType $selectedCardType): string
    {
        $cardsCount = $selectedCardType->cards_count ?? 0;

        return match (true) {
            $selectedCardType->is_active && $cardsCount > 0 => 'Blocked until live-tier accrual parity is verified against visible card coverage.',
            $selectedCardType->is_active => 'Blocked until this live tier has visible card coverage for accrual parity review.',
            $cardsCount > 0 => 'Blocked until draft tier accrual parity is verified against visible card coverage.',
            default => 'Blocked until draft parity review has visible card coverage to compare against.',
        };
    }

    private function cardTypesSelectedPublishTypeDisabledReason(CardType $selectedCardType): string
    {
        $cardsCount = $selectedCardType->cards_count ?? 0;

        return match (true) {
            $selectedCardType->is_active && $cardsCount > 0 => 'Blocked until live-tier rollout parity is verified across visible card coverage.',
            $selectedCardType->is_active => 'Blocked until this live tier has visible card coverage and Galaxy tier rollout parity review.',
            $cardsCount > 0 => 'Blocked until this draft tier clears rule and rollout parity review against visible card coverage.',
            default => 'Blocked until this draft tier clears rule-and-rollout parity review before any publish-like move.',
        };
    }

    private function cardTypesReviewNoteReflection(CardType $selectedCardType): string
    {
        return $selectedCardType->review_note !== null && trim($selectedCardType->review_note) !== ''
            ? sprintf('The current Laravel tier review note says: %s', $selectedCardType->review_note)
            : 'No Laravel tier review note is saved yet, so parity-sensitive tier context still depends on the surrounding workspace cues.';
    }

    private function cardTypesActivationNoteReflection(CardType $selectedCardType): string
    {
        return $selectedCardType->activation_note !== null && trim($selectedCardType->activation_note) !== ''
            ? sprintf('The current Laravel activation note says: %s', $selectedCardType->activation_note)
            : 'No Laravel activation note is saved yet, so activation handoff guidance still depends on the surrounding workspace cues.';
    }

    private function cardTypesRolloutNoteReflection(CardType $selectedCardType): string
    {
        return $selectedCardType->rollout_note !== null && trim($selectedCardType->rollout_note) !== ''
            ? sprintf('The current Laravel rollout note says: %s', $selectedCardType->rollout_note)
            : 'No Laravel rollout note is saved yet, so rollout guidance still depends on the surrounding workspace cues.';
    }

    private function cardTypesHasVisibleCoverage(CardType $selectedCardType): bool
    {
        return ($selectedCardType->cards_count ?? 0) > 0;
    }

    private function cardTypesLastSavedLabel(CardType $selectedCardType): string
    {
        return $this->lastSavedLabel($selectedCardType);
    }

    private function cardTypesHandoffSignal(CardType $selectedCardType): string
    {
        $hasVisibleCoverage = $this->cardTypesHasVisibleCoverage($selectedCardType);

        return match (true) {
            $selectedCardType->is_active && $hasVisibleCoverage => 'Live tier already carries visible card coverage for a useful rollout-parity handoff review.',
            $selectedCardType->is_active => 'Live tier should stay in handoff-only posture until visible card coverage and rollout parity are explicit.',
            $hasVisibleCoverage => 'Draft tier already carries visible card coverage for a useful parity handoff review.',
            default => 'Draft tier should stay in handoff-only posture until visible card coverage grounds rollout review.',
        };
    }

    private function cardTypesBackendGap(CardType $selectedCardType): string
    {
        $hasVisibleCoverage = $this->cardTypesHasVisibleCoverage($selectedCardType);

        return match (true) {
            $selectedCardType->is_active && $hasVisibleCoverage => 'Publish logic and rule-import parity should stay preview-only until live tier parity is verified.',
            $selectedCardType->is_active => 'Rollout confirmation, publish logic, and rule-import parity should stay preview-only until live tier coverage is verified.',
            $hasVisibleCoverage => 'Draft activation, publish logic, and rule-import parity should stay preview-only until draft tier parity is verified.',
            default => 'Draft activation, publish logic, and rule-import parity should stay preview-only until visible tier coverage is verified.',
        };
    }

    private function cardTypesActivationFreshness(CardType $selectedCardType): string
    {
        return match (true) {
            filled($selectedCardType->activation_note) && $selectedCardType->is_active => 'Activation note is already saved on this live Laravel tier shell.',
            filled($selectedCardType->activation_note) => 'Activation note is already staged on this draft Laravel tier shell.',
            $selectedCardType->is_active => 'Live tier still needs a saved activation note before rollout handoff can feel grounded.',
            default => 'Draft tier can stay safe while activation guidance is still being written.',
        };
    }

    private function cardTypesRolloutFreshness(CardType $selectedCardType): string
    {
        return match (true) {
            filled($selectedCardType->rollout_note) && $selectedCardType->is_active => 'Rollout note is already saved on this live Laravel tier shell.',
            filled($selectedCardType->rollout_note) => 'Rollout note is already staged on this draft Laravel tier shell.',
            $selectedCardType->is_active => 'Live tier still needs a saved rollout note before rollout handoff can feel grounded.',
            default => 'Draft tier can stay safe while rollout guidance is still being written.',
        };
    }

    private function liveFormActionParameters(mixed $parameters): array
    {
        if (! is_array($parameters)) {
            return [];
        }

        return array_filter(
            array_map($this->resolveLiveFormRouteParameterValue(...), $parameters),
            fn (mixed $value, mixed $key): bool => (is_string($key) || is_int($key))
                && (is_string($value) || is_int($value)),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    private function cardsCatalogIssueCardDisabledReason(mixed $cards): string
    {
        $draftCount = $cards->where('status', 'draft')->count();
        $activeCount = $cards->where('status', 'active')->count();

        return match (true) {
            $draftCount > 0 => 'Blocked until saved draft inventory is verified against legacy issue-flow parity.',
            $activeCount > 0 => 'Blocked until active inventory and issuance parity are verified against the old Galaxy card flow.',
            default => 'Blocked until the first Laravel-backed card inventory slice exists for issue-flow parity review.',
        };
    }

    private function cardsCatalogReviewBlockedDisabledReason(mixed $cards): string
    {
        $blockedCount = $cards->where('status', 'blocked')->count();
        $activeCount = $cards->where('status', 'active')->count();

        return match (true) {
            $blockedCount > 0 => 'Blocked until saved blocked-card states are verified against legacy inventory semantics.',
            $activeCount > 0 => 'Blocked until live inventory has verified blocked-card parity to compare against legacy behavior.',
            default => 'Blocked until the first Laravel-backed inventory slice exists for blocked-card parity review.',
        };
    }

    private function cardholdersCatalogNewHolderDisabledReason(mixed $cardHolders): string
    {
        $inactiveCount = $cardHolders->where('is_active', false)->count();
        $linkedCards = $cardHolders->sum('cards_count');

        return match (true) {
            $inactiveCount > 0 => 'Blocked until saved inactive-holder states are verified against legacy profile and lifecycle parity.',
            $linkedCards > 0 => 'Blocked until linked-holder coverage is verified against the old Galaxy profile flow.',
            default => 'Blocked until the first Laravel-backed cardholder slice exists for profile parity review.',
        };
    }

    private function cardholdersCatalogReviewActivityDisabledReason(mixed $cardHolders): string
    {
        $linkedCards = $cardHolders->sum('cards_count');
        $activeCount = $cardHolders->where('is_active', true)->count();
        $pausedBranchCount = $cardHolders->filter(fn (CardHolder $cardHolder): bool => $cardHolder->shop !== null && ! (bool) $cardHolder->shop->is_active)->count();

        return match (true) {
            $pausedBranchCount > 0 && $linkedCards > 0 => 'Blocked until paused-branch linked-holder activity is verified against legacy lookup recovery history.',
            $pausedBranchCount > 0 => 'Blocked until paused-branch holder activity is verified against legacy lookup recovery history.',
            $linkedCards > 0 => 'Blocked until linked-holder activity coverage is verified against legacy lookup history.',
            $activeCount > 0 => 'Blocked until active-holder coverage has a stable Laravel activity source for parity review.',
            default => 'Blocked until the first Laravel-backed cardholder slice exists for activity-history parity review.',
        };
    }

    private function shopsCatalogNewShopDisabledReason(mixed $shops): string
    {
        $managerCount = $shops->filter(fn (Shop $shop): bool => $shop->users_count > 0)->count();
        $pausedCount = $shops->where('is_active', false)->count();

        return match (true) {
            $pausedCount > 0 => 'Blocked until paused-branch recovery and manager assignment parity are verified.',
            $managerCount > 0 => 'Blocked until saved branch ownership and manager assignment parity are verified.',
            default => 'Blocked until the first Laravel-backed shops index and manager assignment parity checks are verified.',
        };
    }

    private function shopsCatalogReviewScopeDisabledReason(mixed $shops): string
    {
        $managerCount = $shops->filter(fn (Shop $shop): bool => $shop->users_count > 0)->count();
        $coverageCount = $shops->filter(fn (Shop $shop): bool => $shop->card_holders_count > 0 || $shop->cards_count > 0)->count();

        return match (true) {
            $managerCount > 0 && $coverageCount > 0 => 'Blocked until saved branch ownership and scope coverage are verified against the legacy Galaxy multi-shop model.',
            $managerCount > 0 => 'Blocked until manager-linked branch scope is verified against the legacy Galaxy multi-shop model.',
            $coverageCount > 0 => 'Blocked until visible branch coverage is verified against the legacy Galaxy multi-shop model.',
            default => 'Blocked until branch ownership rules are confirmed against the legacy Galaxy multi-shop access model.',
        };
    }

    private function reportsCatalogPresetDisabledReason(int $shopCount, int $cardCount, int $cardHolderCount, int $roleCount): string
    {
        $liveSourceCount = collect([$shopCount, $cardCount, $cardHolderCount, $roleCount])->filter(fn (int $count): bool => $count > 0)->count();

        return match (true) {
            $liveSourceCount >= 3 => 'Blocked until preset handling is verified against multiple live Laravel reporting sources.',
            $liveSourceCount > 0 => 'Blocked until preset handling is backed by Laravel reporting flow validation across the first live sources.',
            default => 'Blocked until preset handling is backed by Laravel reporting flow validation.',
        };
    }

    private function reportsCatalogExportDisabledReason(int $shopCount, int $cardCount, int $cardHolderCount, int $roleCount): string
    {
        $liveSourceCount = collect([$shopCount, $cardCount, $cardHolderCount, $roleCount])->filter(fn (int $count): bool => $count > 0)->count();
        $inventoryCoverageReady = $shopCount > 0 && $cardCount > 0;

        return match (true) {
            $liveSourceCount >= 3 && $inventoryCoverageReady => 'Blocked until multi-source export snapshots are verified against legacy file delivery and grouped totals.',
            $liveSourceCount > 0 => 'Blocked until live Laravel source snapshots are verified against legacy export totals and file delivery.',
            default => 'Blocked until the first live Laravel report source exists for export parity review.',
        };
    }

    private function checksPointsCatalogFindReceiptDisabledReason(array $receiptPreviews): string
    {
        $shopCount = collect($receiptPreviews)->pluck('shop')->unique()->count();
        $receiptCount = count($receiptPreviews);

        return match (true) {
            $receiptCount > 0 && $shopCount > 1 => 'Blocked until fiscal receipt lookup is verified against branch-aware transaction history and legacy search habits.',
            $receiptCount > 0 => 'Blocked until fiscal receipt lookup is backed by Laravel transaction reads and receipt-history parity checks.',
            default => 'Blocked until the first receipt-history slice exists for fiscal lookup parity review.',
        };
    }

    private function checksPointsCatalogReviewGapsDisabledReason(array $receiptPreviews): string
    {
        $zeroAccrualCount = collect($receiptPreviews)->filter(fn (array $receipt): bool => $receipt['points'] === '0')->count();
        $shopCount = collect($receiptPreviews)->pluck('shop')->unique()->count();

        return match (true) {
            $zeroAccrualCount > 0 && $shopCount > 1 => 'Blocked until zero-accrual and branch-aware troubleshooting are backed by Laravel transaction and rule data.',
            $zeroAccrualCount > 0 => 'Blocked until zero-accrual troubleshooting is backed by Laravel transaction and rule data.',
            default => 'Blocked until accrual-gap review is backed by Laravel transaction and rule data.',
        };
    }

    private function checksPointsSelectedFindReceiptDisabledReason(array $selectedReceiptPreview): string
    {
        return $this->checksPointsSelectedFindReceiptDisabledReasonSummary($selectedReceiptPreview);
    }

    private function checksPointsSelectedFindReceiptDisabledReasonSummary(array $selectedReceiptPreview): string
    {
        return match ($selectedReceiptPreview['shop'] ?? null) {
            'North Shop' => 'Blocked until branch-aware receipt lookup is backed by Laravel shop filters and transaction reads.',
            default => 'Blocked until receipt lookup is backed by Laravel transaction reads and fiscal-search parity checks.',
        };
    }

    private function checksPointsSelectedReviewGapsDisabledReason(array $selectedReceiptPreview): string
    {
        return $this->checksPointsSelectedReviewGapsDisabledReasonSummary($selectedReceiptPreview);
    }

    private function checksPointsSelectedReviewGapsDisabledReasonSummary(array $selectedReceiptPreview): string
    {
        return match (true) {
            ($selectedReceiptPreview['points'] ?? null) === '0' => 'Blocked until zero-accrual review is backed by Laravel transaction and rule data.',
            ($selectedReceiptPreview['shop'] ?? null) === 'North Shop' => 'Blocked until branch-aware accrual review is backed by Laravel transaction and rule data.',
            default => 'Blocked until accrual-gap review is backed by Laravel transaction and rule data.',
        };
    }

    private function reportsSelectedSourcePresetDisabledReason(array $selectedReportSource): string
    {
        return $this->reportsSelectedSourcePresetDisabledReasonSummary($selectedReportSource);
    }

    private function reportsSelectedSourcePresetDisabledReasonSummary(array $selectedReportSource): string
    {
        return match ($selectedReportSource['key'] ?? null) {
            'cards-by-shop' => 'Blocked until branch-total preset periods are verified against live shop grouping and legacy reporting habits.',
            'cardholder-status' => 'Blocked until holder-status preset periods are verified against lifecycle and recency reporting parity.',
            'role-access' => 'Blocked until role-access preset periods are verified against scope and assignment reporting parity.',
            default => 'Blocked until preset handling is backed by Laravel reporting flow validation.',
        };
    }

    private function reportsSelectedSourceExportDisabledReason(array $selectedReportSource): string
    {
        return $this->reportsSelectedSourceExportDisabledReasonSummary($selectedReportSource);
    }

    private function reportsSelectedSourceExportDisabledReasonSummary(array $selectedReportSource): string
    {
        return match ($selectedReportSource['key'] ?? null) {
            'cards-by-shop' => 'Blocked until branch-total export snapshots are verified against legacy grouped totals and file delivery.',
            'cardholder-status' => 'Blocked until holder-status export snapshots are verified against lifecycle summaries and file delivery.',
            'role-access' => 'Blocked until role-access export snapshots are verified against scope summaries and file delivery.',
            default => 'Blocked until reporting exports and file delivery are verified against legacy Galaxy output expectations.',
        };
    }

    private function giftsSelectedStockAuditDisabledReason(array $selectedGiftPreview): string
    {
        return $this->giftsSelectedStockAuditDisabledReasonSummary($selectedGiftPreview);
    }

    private function giftsSelectedStockAuditDisabledReasonSummary(array $selectedGiftPreview): string
    {
        return match (true) {
            ($selectedGiftPreview['stock'] ?? null) === '0' => 'Blocked until zero-stock recovery checks are backed by Laravel inventory data and reopening parity.',
            ($selectedGiftPreview['stock'] ?? null) !== 'Unlimited' => 'Blocked until finite-stock checks are backed by Laravel inventory data and scoped stock parity.',
            default => 'Blocked until all-shop stock checks are backed by Laravel inventory data.',
        };
    }

    private function giftsSelectedPublishGiftDisabledReason(array $selectedGiftPreview): string
    {
        return $this->giftsSelectedPublishGiftDisabledReasonSummary($selectedGiftPreview);
    }

    private function giftsSelectedPublishGiftDisabledReasonSummary(array $selectedGiftPreview): string
    {
        return match (true) {
            ($selectedGiftPreview['status'] ?? null) === 'paused' => 'Blocked until this paused reward clears CRUD, stock-recovery, and redemption parity beyond the preview shell.',
            ($selectedGiftPreview['scope'] ?? null) !== 'All shops' => 'Blocked until this scoped reward clears CRUD, scope-parity, and redemption checks beyond the preview shell.',
            default => 'Blocked until this all-shop reward clears CRUD and redemption parity beyond the preview shell.',
        };
    }

    private function servicesRulesSelectedReviewPrioritiesDisabledReason(array $selectedRulePreview): string
    {
        return $this->servicesRulesSelectedReviewPrioritiesDisabledReasonSummary($selectedRulePreview);
    }

    private function servicesRulesSelectedReviewPrioritiesDisabledReasonSummary(array $selectedRulePreview): string
    {
        return match (true) {
            ($selectedRulePreview['status'] ?? null) === 'draft' => 'Blocked until draft rule priority order is verified against legacy exclusion precedence in Laravel.',
            ($selectedRulePreview['scope'] ?? null) !== 'All shops' => 'Blocked until scoped rule priority order is verified against broader loyalty overlaps in Laravel.',
            default => 'Blocked until all-shop rule priority order is verified in Laravel.',
        };
    }

    private function servicesRulesSelectedPublishRuleDisabledReason(array $selectedRulePreview): string
    {
        return $this->servicesRulesSelectedPublishRuleDisabledReasonSummary($selectedRulePreview);
    }

    private function servicesRulesSelectedPublishRuleDisabledReasonSummary(array $selectedRulePreview): string
    {
        return match (true) {
            ($selectedRulePreview['status'] ?? null) === 'draft' => 'Blocked until this draft rule clears CRUD, exclusion-parity, and publish-safety checks beyond the preview shell.',
            ($selectedRulePreview['scope'] ?? null) !== 'All shops' => 'Blocked until this scoped rule clears CRUD, scope-parity, and publish-safety checks beyond the preview shell.',
            default => 'Blocked until this all-shop rule clears CRUD and publish-safety parity beyond the preview shell.',
        };
    }

    private function cardsSelectedReviewBlockedDisabledReason(Card $selectedCard): string
    {
        return $this->cardsSelectedReviewBlockedDisabledReasonSummary($selectedCard);
    }

    private function cardsSelectedReviewBlockedDisabledReasonSummary(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'blocked' && $selectedCard->holder !== null => 'Blocked until this blocked holder-linked card clears dispute and replacement parity against the legacy Galaxy flow.',
            $selectedCard->status === 'blocked' => 'Blocked until this blocked card clears dispute and replacement parity against the legacy Galaxy flow.',
            $selectedCard->status === 'active' => 'Blocked until blocked-card semantics are verified against this active Laravel inventory flow.',
            default => 'Blocked until blocked-card semantics are verified against this draft Laravel inventory flow.',
        };
    }

    private function cardholdersSelectedReviewActivityDisabledReason(CardHolder $selectedCardHolder): string
    {
        return $this->cardholdersSelectedReviewActivityDisabledReasonSummary($selectedCardHolder);
    }

    private function cardholdersSelectedReviewActivityDisabledReasonSummary(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            (bool) $selectedCardHolder->shop?->is_active === false && $selectedCardHolder->cards_count > 0 => 'Blocked until paused-branch linked-card activity is backed by a stable Laravel event source for recovery-parity review.',
            (bool) $selectedCardHolder->shop?->is_active === false => 'Blocked until paused-branch activity history is backed by a stable Laravel event source for recovery-parity review.',
            $selectedCardHolder->cards_count > 0 && $selectedCardHolder->is_active => 'Blocked until linked-card activity is backed by a stable Laravel event source for active-holder lookup parity.',
            $selectedCardHolder->cards_count > 0 => 'Blocked until linked-card activity is backed by a stable Laravel event source for holder lookup parity.',
            ! $selectedCardHolder->is_active => 'Blocked until inactive-holder activity history is backed by a stable Laravel event source for lifecycle parity.',
            default => 'Blocked until a stable Laravel activity source exists for holder lookup parity.',
        };
    }

    private function shopsSelectedReviewScopeDisabledReason(Shop $selectedShop): string
    {
        return $this->shopsSelectedReviewScopeDisabledReasonSummary($selectedShop);
    }

    private function shopsSelectedReviewScopeDisabledReasonSummary(Shop $selectedShop): string
    {
        return match (true) {
            $selectedShop->users_count > 0 && $selectedShop->card_holders_count > 0 && $selectedShop->cards_count > 0 => 'Blocked until manager-linked branch scope is verified against live holder/card coverage and the legacy Galaxy multi-shop model.',
            $selectedShop->users_count > 0 => 'Blocked until manager-linked branch scope is verified against the legacy Galaxy multi-shop model.',
            $selectedShop->card_holders_count > 0 || $selectedShop->cards_count > 0 => 'Blocked until visible branch coverage is verified against the legacy Galaxy multi-shop model.',
            default => 'Blocked until branch ownership rules are confirmed against the legacy Galaxy multi-shop access model.',
        };
    }

    private function resolveLiveFormRouteParameterValue(mixed $value): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        if ($value instanceof UrlRoutable) {
            return $value->getRouteKey();
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return $value;
    }

    private function resolveLiveFormValues(mixed $resolver, string $resource, array $page): array
    {
        $values = $this->resolveLiveFormConfigValue($resolver, $resource, $page);

        return is_array($values) ? $values : [];
    }

    private function resolveLiveFormRouteParameters(mixed $parameters, string $resource, array $page): array
    {
        $parameters = $this->resolveLiveFormConfigValue($parameters, $resource, $page);

        return is_array($parameters) ? $parameters : [];
    }

    private function appendPageAction(array $page, array $action): array
    {
        $actions = is_array($page['actions'] ?? null) ? $page['actions'] : [];
        $page['actions'] = [...$actions, $action];

        return $page;
    }

    private function applySelectedPreviewContext(
        array $page,
        array $selectedPreview,
        string $indexRouteName,
        string $backLabel,
        array $additionalActions,
    ): array {
        $page['selectedRecordSummary'] = is_array($selectedPreview['summary'] ?? null) ? $selectedPreview['summary'] : [];
        $page['actions'] = [
            [
                'label' => $backLabel,
                'tone' => 'primary',
                'href' => route($indexRouteName, absolute: false),
            ],
            [
                'label' => sprintf('Reviewing: %s', (string) ($selectedPreview['label'] ?? 'Preview item')),
                'tone' => 'secondary',
            ],
            ...$additionalActions,
        ];
        $page['activityTimeline'] = is_array($selectedPreview['timeline'] ?? null) ? $selectedPreview['timeline'] : [];
        $page['dependencyStatus'] = is_array($selectedPreview['dependencyStatus'] ?? null) ? $selectedPreview['dependencyStatus'] : [];

        return $page;
    }

    private function selectedReadContextActions(
        string $indexRouteName,
        string $backLabel,
        string $selectedLabel,
        array $additionalActions,
    ): array {
        return [
            [
                'label' => $backLabel,
                'tone' => 'primary',
                'href' => route($indexRouteName, absolute: false),
            ],
            [
                'label' => sprintf('Reviewing: %s', $selectedLabel),
                'tone' => 'secondary',
            ],
            ...$additionalActions,
        ];
    }

    private function selectedPreviewByKey(array $previews, string $queryKey): ?array
    {
        $selectedKey = request()->query($queryKey);

        if (! is_string($selectedKey)) {
            return null;
        }

        $normalizedKey = strtolower(trim($selectedKey));

        if ($normalizedKey === '') {
            return null;
        }

        $selectedPreview = collect($previews)->first(
            fn (array $preview): bool => strtolower((string) ($preview['key'] ?? '')) === $normalizedKey
        );

        return is_array($selectedPreview) ? $selectedPreview : null;
    }

    private function appendCardTypeLatestFlowFeedback(array $page): array
    {
        $status = session('status');

        if (! is_string($status)) {
            return $page;
        }

        $summary = is_array($page['selectedRecordSummary'] ?? null) ? $page['selectedRecordSummary'] : [];
        $summary[] = [
            'label' => 'Latest flow result',
            'value' => $status,
        ];
        $page['selectedRecordSummary'] = $summary;

        return $page;
    }

    private function reportsActivityTimeline(int $shopCount, int $cardCount, int $cardHolderCount, int $roleCount): array
    {
        return [
            [
                'title' => 'Live reporting sources reflected from Laravel models',
                'time' => 'Current request',
                'description' => sprintf('The reporting workspace now sees %d shops, %d cards, %d cardholders, and %d roles through the current Laravel foundation.', $shopCount, $cardCount, $cardHolderCount, $roleCount),
            ],
            [
                'title' => 'Export catalog remains parity-first',
                'time' => 'Current request',
                'description' => 'Metrics and entry rows are live-backed now, but preset handling and export generation still stay blocked until the reporting pipeline is verified.',
            ],
        ];
    }

    private function reportsDependencyStatus(int $shopCount, int $cardCount, int $cardHolderCount, int $roleCount): array
    {
        return [
            ['label' => 'Domain model', 'value' => 'Report catalog is still lightweight, but source counts now come from live Laravel models'],
            ['label' => 'Reporting posture', 'value' => 'This workspace is now live-backed for read-only source review, but preset and export flows should stay parity-first until the reporting pipeline is verified.'],
            ['label' => 'Readiness signal', 'value' => 'Partially ready: live source review works now, while preset handling and exports stay blocked behind later reporting-pipeline verification.'],
            ['label' => 'Preset posture', 'value' => 'Preset periods are still preview-only, so operators should treat the live source layer as reviewable while preset-driven report flows remain gated.'],
            ['label' => 'Export posture', 'value' => 'Export generation is still blocked, so the live reporting layer should stay review-only until file delivery and parity checks are verified.'],
            ['label' => 'Source coverage', 'value' => sprintf('Laravel reporting inputs currently cover %d shops, %d cards, %d cardholders, and %d roles for read-only review.', $shopCount, $cardCount, $cardHolderCount, $roleCount)],
            ['label' => 'Backend dependency', 'value' => 'Preset handling, query shaping, and export pipeline are still pending'],
            ['label' => 'Operational dependency', 'value' => 'Legacy report presets and export expectations still need live verification'],
        ];
    }

    private function rolesPermissionsSelectedRoleSummary(Role $selectedRole, mixed $scope, mixed $permissionPreview, mixed $assignedUserPreview): array
    {
        $activeShopAssignedUserCount = $selectedRole->users->filter(fn ($user): bool => (bool) $user->shop?->is_active)->count();
        $pausedShopAssignedUserCount = $selectedRole->users->filter(fn ($user): bool => $user->shop !== null && ! $user->shop->is_active)->count();
        $permissionBranchActivitySignal = $selectedRole->permissions_count > 0 && $activeShopAssignedUserCount > 0 && $pausedShopAssignedUserCount > 0
            ? sprintf('%d permission-linked staff are already visible in active branches beside %d permission-linked staff in paused shops for parity review', $activeShopAssignedUserCount, $pausedShopAssignedUserCount)
            : 'paused-branch permission-linked staff coverage is still pending for parity review';
        $scopedPermissionSignal = $selectedRole->permissions_count > 0 && $scope->isNotEmpty()
            ? sprintf('%d scoped shops are already visible for this permission-linked role in parity review', $scope->count())
            : 'scoped permission coverage is still pending for parity review';
        $permissionReviewNote = $selectedRole->permissions
            ->pluck('review_note')
            ->first(fn (?string $note): bool => is_string($note) && trim($note) !== '');

        return [
            ['label' => 'Selected role', 'value' => $selectedRole->name],
            ['label' => 'Role slug', 'value' => $selectedRole->slug],
            ['label' => 'Review mode', 'value' => $selectedRole->is_active || $selectedRole->users_count > 0 || $selectedRole->permissions_count > 0
                ? 'Live-impact review, linked staff or permissions already exist in Laravel'
                : 'Draft-safe review, no linked staff or permissions yet in Laravel'],
            ['label' => 'Operational readiness', 'value' => $this->rolesPermissionsOperationalReadiness($selectedRole)],
            ['label' => 'Lifecycle freshness', 'value' => $this->rolesPermissionsLifecycleFreshnessLabel($selectedRole)],
            ['label' => 'Last saved in Laravel', 'value' => $this->rolesPermissionsLastSavedLabel($selectedRole)],
            ['label' => 'Review note', 'value' => $selectedRole->review_note ?: 'No review note saved yet'],
            ['label' => 'Review freshness', 'value' => $this->rolesPermissionsReviewFreshness($selectedRole)],
            ['label' => 'Access note', 'value' => $selectedRole->access_note ?: 'No access note saved yet'],
            ['label' => 'Assignment note', 'value' => $selectedRole->assignment_note ?: 'No assignment note saved yet'],
            ['label' => 'Coverage signal', 'value' => $this->rolesPermissionsCoverageSignal($selectedRole, $scope)],
            ['label' => 'Role status signal', 'value' => $this->rolesPermissionsStatusSignal($selectedRole, $scope)],
            ['label' => 'Access focus', 'value' => $this->rolesPermissionsAccessFocus($selectedRole)],
            ['label' => 'Access posture', 'value' => $this->rolesPermissionsAccessPosture($selectedRole)],
            ['label' => 'Evidence priority', 'value' => $this->rolesPermissionsEvidencePriority($selectedRole)],
            ['label' => 'Handoff signal', 'value' => $this->rolesPermissionsHandoffSignal($selectedRole, $scope)],
            ['label' => 'Backend gap', 'value' => $this->rolesPermissionsBackendGap($selectedRole)],
            ['label' => 'Scope', 'value' => $scope->isNotEmpty() ? $scope->join(', ') : 'Unscoped in Laravel read slice'],
            ['label' => 'Scope coverage', 'value' => $this->rolesPermissionsScopeCoverageLabel($scope)],
            ['label' => 'Scope rollout posture', 'value' => $this->rolesPermissionsScopeRolloutSummaryPosture($scope)],
            ['label' => 'Shop scope preview', 'value' => $scope->isNotEmpty() ? $scope->take(3)->join(', ') : 'No shops linked yet'],
            ['label' => 'Scope guidance', 'value' => $scope->isNotEmpty()
                ? 'This role already has visible shop scope in Laravel, so any scope change should be treated as a parity-sensitive access change.'
                : 'No shop scope is linked yet, which keeps this role safer for draft review before scope parity is confirmed.'],
            ['label' => 'Assigned users', 'value' => (string) $selectedRole->users_count],
            ['label' => 'Assigned staff preview', 'value' => $assignedUserPreview->isNotEmpty() ? $assignedUserPreview->join(', ') : 'No staff linked yet'],
            ['label' => 'Assignment branch activity signal', 'value' => $activeShopAssignedUserCount > 0 && $pausedShopAssignedUserCount > 0
                ? sprintf('%d assigned staff are already visible in active branches beside %d assigned staff in paused shops for parity review', $activeShopAssignedUserCount, $pausedShopAssignedUserCount)
                : 'paused-branch assignment coverage is still pending for parity review'],
            ['label' => 'Assignment guidance', 'value' => $selectedRole->users_count > 0
                ? 'Assigned staff are already linked in Laravel, so scope and permission changes should be reviewed against real operator impact.'
                : 'No staff are linked yet, which keeps this role safer for draft access review before assignment parity is confirmed.'],
            ['label' => 'Permission count', 'value' => (string) $selectedRole->permissions_count],
            ['label' => 'Permission coverage', 'value' => $selectedRole->permissions_count > 0
                ? 'Live bundle present, review changes as parity-sensitive access coverage.'
                : 'No bundle linked yet, this role remains safer for draft parity review.'],
            ['label' => 'Scoped permission signal', 'value' => $scopedPermissionSignal],
            ['label' => 'Permission branch activity signal', 'value' => $permissionBranchActivitySignal],
            ['label' => 'Permission bundle', 'value' => $permissionPreview->isNotEmpty() ? $permissionPreview->take(3)->implode(', ') : 'No permissions linked yet'],
            ['label' => 'Permission review note', 'value' => $permissionReviewNote ?: 'No linked permission review note saved yet'],
            ['label' => 'Laravel status', 'value' => $selectedRole->is_active ? 'active' : 'draft'],
            [
                'label' => 'Access guidance',
                'value' => match (true) {
                    $selectedRole->is_active && $selectedRole->permissions_count > 0 => 'This role already carries a Laravel permission bundle, so assignment and scope changes should stay parity-first until the matrix editor is verified.',
                    $selectedRole->is_active => 'This role is active in Laravel, but permission bundle and assignment follow-up should stay parity-first until the matrix editor is verified.',
                    default => 'This role is still a draft shell in Laravel, which keeps it safe for parity checks before operators rely on it for staff access.',
                },
            ],
        ];
    }

    private function giftsBackendGap(string $giftKey): string
    {
        return match ($giftKey) {
            'coffee-voucher' => 'Gift CRUD, all-shop stock assumptions, and redemption persistence should stay preview-only until all-shop-reward parity is verified.',
            'airport-transfer' => 'Gift CRUD, kiosk-scoped stock updates, and redemption persistence should stay preview-only until kiosk-reward parity is verified.',
            'premium-dessert-set' => 'Gift CRUD, zero-stock recovery, and redemption persistence should stay preview-only until paused-zero-stock-recovery parity is verified.',
            'weekend-brunch-pass' => 'Gift CRUD, paused-stock recovery, and redemption persistence should stay preview-only until paused-branch-reopening parity is verified.',
            default => 'Gift CRUD, stock updates, and redemption persistence should stay preview-only until reward parity is verified.',
        };
    }

    private function servicesRulesBackendGap(string $ruleKey): string
    {
        return match ($ruleKey) {
            'birthday-bonus' => 'Rule persistence, birthday-window editing, and publish flow should stay preview-only until all-shop birthday accrual parity is verified.',
            'partner-card-uplift' => 'Rule persistence, partner-card condition editing, and publish flow should stay preview-only until partner-card uplift parity is verified.',
            'night-service-block' => 'Rule persistence, exclusion validation, and publish flow should stay preview-only until bar-service-exclusion parity is verified.',
            default => 'Rule persistence, condition editing, and publish flow should stay preview-only until rule parity is verified.',
        };
    }

    private function reportsCardsByShopBackendGap(int $cardCount, int $shopCount, int $holderLinkedCardCount, int $unassignedCardCount): string
    {
        return match (true) {
            $cardCount === 0 || $shopCount === 0 => 'Report-source seeding, grouped query shaping, and export generation should stay preview-only until branch inventory inputs exist in Laravel.',
            $holderLinkedCardCount > 0 && $unassignedCardCount > 0 => 'Preset handling, assignment-aware grouping, and export generation should stay preview-only until branch-total and inventory-assignment parity are verified.',
            $holderLinkedCardCount > 0 => 'Preset handling, unassigned-inventory shaping, and export generation should stay preview-only until branch-total assignment parity is verified.',
            $unassignedCardCount > 0 => 'Preset handling, holder-linkage shaping, and export generation should stay preview-only until branch-total customer-linkage parity is verified.',
            default => 'Preset handling, grouped query shaping, and export generation should stay preview-only until report parity is verified.',
        };
    }

    private function reportsCardsByShopHandoffSignal(int $holderLinkedCardCount, int $unassignedCardCount): string
    {
        return match (true) {
            $holderLinkedCardCount > 0 && $unassignedCardCount > 0 => 'Keep branch-total and assignment-split findings in the live workspace before asking for export-driven handoff.',
            $holderLinkedCardCount > 0 => 'Keep branch-total and linked-holder inventory findings in the live workspace before asking for export-driven handoff.',
            $unassignedCardCount > 0 => 'Keep branch-total and unassigned inventory findings in the live workspace before asking for export-driven handoff.',
            default => 'Keep branch comparison findings in the live workspace before asking for export-driven handoff.',
        };
    }

    private function reportsCardsByShopTimelineHandoffDescription(int $holderLinkedCardCount, int $unassignedCardCount): string
    {
        return match (true) {
            $holderLinkedCardCount > 0 && $unassignedCardCount > 0 => 'Operators should hand off branch-total and assignment-split findings in the live workspace before relying on exported files for this source.',
            $holderLinkedCardCount > 0 => 'Operators should hand off branch-total and linked-holder inventory findings in the live workspace before relying on exported files for this source.',
            $unassignedCardCount > 0 => 'Operators should hand off branch-total and unassigned inventory findings in the live workspace before relying on exported files for this source.',
            default => 'Operators should hand off branch comparison findings in the live workspace before relying on exported files for this source.',
        };
    }

    private function reportsCardholderStatusBackendGap(int $cardHolderCount, int $linkedCardHolderCount, int $inactiveCardHolderCount, int $blockedLinkedCardCount): string
    {
        return match (true) {
            $cardHolderCount === 0 => 'Report-source seeding, status shaping, and export generation should stay preview-only until holder lifecycle inputs exist in Laravel.',
            $linkedCardHolderCount > 0 && $inactiveCardHolderCount > 0 => 'Preset handling, lifecycle-segmentation shaping, and export generation should stay preview-only until inactive-holder parity is verified.',
            $linkedCardHolderCount > 0 => 'Preset handling, inactive-holder shaping, and export generation should stay preview-only until linked-profile lifecycle parity is verified.',
            $blockedLinkedCardCount > 0 => 'Preset handling, blocked-card shaping, and export generation should stay preview-only until support-style lifecycle parity is verified.',
            default => 'Preset handling, report shaping, and export generation should stay preview-only until lifecycle parity is verified.',
        };
    }

    private function reportsCardholderStatusHandoffSignal(int $linkedCardHolderCount, int $inactiveCardHolderCount, int $blockedLinkedCardCount): string
    {
        return match (true) {
            $linkedCardHolderCount > 0 && $inactiveCardHolderCount > 0 => 'Keep holder lifecycle and linkage findings in the live workspace before asking for export-driven handoff.',
            $blockedLinkedCardCount > 0 => 'Keep holder lifecycle and blocked-card findings in the live workspace before asking for export-driven handoff.',
            $linkedCardHolderCount > 0 => 'Keep holder lifecycle and linked-profile findings in the live workspace before asking for export-driven handoff.',
            default => 'Keep holder lifecycle findings in the live workspace before asking for export-driven handoff.',
        };
    }

    private function reportsCardholderStatusTimelineHandoffDescription(int $linkedCardHolderCount, int $inactiveCardHolderCount, int $blockedLinkedCardCount): string
    {
        return match (true) {
            $linkedCardHolderCount > 0 && $inactiveCardHolderCount > 0 => 'Operators should pass along holder lifecycle and linkage findings in the live review flow before expecting export-driven follow-up.',
            $blockedLinkedCardCount > 0 => 'Operators should pass along holder lifecycle and blocked-card findings in the live review flow before expecting export-driven follow-up.',
            $linkedCardHolderCount > 0 => 'Operators should pass along holder lifecycle and linked-profile findings in the live review flow before expecting export-driven follow-up.',
            default => 'Operators should pass along holder lifecycle findings in the live review flow before expecting export-driven follow-up.',
        };
    }

    private function reportsRoleAccessBackendGap(int $roleCount, int $permissionLinkedRoleCount, int $assignedStaffCount): string
    {
        return match (true) {
            $roleCount === 0 => 'Role-source seeding, preset handling, and export generation should stay preview-only until access-report inputs exist in Laravel.',
            $permissionLinkedRoleCount > 0 && $assignedStaffCount > 0 => 'Preset handling, grouped access shaping, and export generation should stay preview-only until scope and staffing parity are verified.',
            $permissionLinkedRoleCount > 0 => 'Preset handling, assignment-aware shaping, and export generation should stay preview-only until access-report staffing parity is verified.',
            $assignedStaffCount > 0 => 'Preset handling, permission-bundle shaping, and export generation should stay preview-only until access-report bundle parity is verified.',
            default => 'Preset handling, report shaping, and export generation should stay preview-only until access parity is verified.',
        };
    }

    private function reportsRoleAccessHandoffSignal(int $permissionLinkedRoleCount, int $assignedStaffCount): string
    {
        return match (true) {
            $permissionLinkedRoleCount > 0 && $assignedStaffCount > 0 => 'Keep role-coverage and staffing findings in the live workspace before asking for export-driven handoff.',
            $permissionLinkedRoleCount > 0 => 'Keep role-coverage and permission-bundle findings in the live workspace before asking for export-driven handoff.',
            $assignedStaffCount > 0 => 'Keep role-coverage and staff-assignment findings in the live workspace before asking for export-driven handoff.',
            default => 'Keep access coverage findings in the live workspace before asking for export-driven handoff.',
        };
    }

    private function reportsRoleAccessTimelineHandoffDescription(int $permissionLinkedRoleCount, int $assignedStaffCount): string
    {
        return match (true) {
            $permissionLinkedRoleCount > 0 && $assignedStaffCount > 0 => 'Operators should hand off role-coverage and staffing findings in the live review context before trusting export files for access decisions.',
            $permissionLinkedRoleCount > 0 => 'Operators should hand off role-coverage and permission-bundle findings in the live review context before trusting export files for access decisions.',
            $assignedStaffCount > 0 => 'Operators should hand off role-coverage and staff-assignment findings in the live review context before trusting export files for access decisions.',
            default => 'Operators should hand off access-coverage findings in the live review context before trusting export files for access decisions.',
        };
    }

    private function rolesPermissionsOperationalReadiness(Role $selectedRole): string
    {
        return match (true) {
            ! $selectedRole->is_active => 'draft-safe role shell',
            $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 => 'assignment-sensitive live role',
            $selectedRole->permissions_count > 0 => 'permission bundle live, assignment rollout pending',
            $selectedRole->users_count > 0 => 'assignment linked, permission bundle still pending review',
            default => 'active role shell, permission bundle still pending review',
        };
    }

    private function rolesPermissionsAccessFocus(Role $selectedRole): string
    {
        return $selectedRole->is_active
            ? 'Start with visible scope, assigned staff, and permission bundle overlap before discussing any later matrix editing flow.'
            : 'Start with draft status, visible scope gaps, and permission bundle gaps before discussing any later matrix editing flow.';
    }

    private function rolesPermissionsAccessPosture(Role $selectedRole): string
    {
        return $selectedRole->is_active
            ? 'Keep access review in the live workspace first, then leave matrix edits and scope writes gated until parity is proven.'
            : 'Keep draft role review in the workspace first, then leave matrix edits, scope writes, and activation flows gated until parity is proven.';
    }

    private function rolesPermissionsEvidencePriority(Role $selectedRole): string
    {
        return $selectedRole->is_active
            ? 'Keep shop scope, assigned staff, and visible permission bundle entries together before trusting any later matrix view.'
            : 'Keep draft status, scope gaps, and permission bundle gaps together before trusting any later matrix or publish discussion.';
    }

    private function rolesPermissionsBackendGap(Role $selectedRole): string
    {
        return match (true) {
            ! $selectedRole->is_active => 'Draft activation, first permission-bundle wiring, and shop-scoped authorization writes should stay preview-only until access parity is verified.',
            $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 => 'Role assignment, matrix editing, and shop-scoped authorization writes should stay preview-only until access parity is verified.',
            $selectedRole->permissions_count > 0 => 'Assignment linking, matrix editing, and shop-scoped authorization writes should stay preview-only until bundle-led access parity is verified.',
            $selectedRole->users_count > 0 => 'Permission-bundle wiring, matrix editing, and shop-scoped authorization writes should stay preview-only until staff-led access parity is verified.',
            default => 'Role assignment, permission-bundle wiring, and shop-scoped authorization writes should stay preview-only until access parity is verified.',
        };
    }

    private function rolesPermissionsPublishPostureValue(Role $selectedRole): string
    {
        return match (true) {
            ! $selectedRole->is_active => 'draft-only',
            $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 => 'assignment-sensitive',
            default => 'parity-sensitive',
        };
    }

    private function rolesPermissionsHandoffSignal(Role $selectedRole, Collection $scope): string
    {
        return match (true) {
            ! $selectedRole->is_active => 'Draft role should stay in handoff-only posture until review note, bundle, and scope parity are explicit.',
            $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 && $scope->isNotEmpty() => 'Live role already carries scope, staffing, and permission coverage for a useful access handoff review.',
            $selectedRole->permissions_count > 0 && $scope->isNotEmpty() => 'Permission bundle and scope are visible, but staffing impact still needs to catch up before full handoff review.',
            $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 => 'Staffing and permission coverage are visible, but shop scope still needs to catch up before full handoff review.',
            default => 'Access shell is visible, but handoff context is still thin until more live coverage lands.',
        };
    }

    private function rolesPermissionsLifecycleFreshness(Role $selectedRole): string
    {
        return $this->lifecycleFreshnessLabel($selectedRole);
    }

    private function rolesPermissionsLifecycleFreshnessLabel(Role $selectedRole): string
    {
        return $this->rolesPermissionsLifecycleFreshness($selectedRole);
    }

    private function rolesPermissionsSelectedForReviewTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsSelectedForReviewTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsSelectedForReviewTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s selected for Laravel review', $selectedRole->name);
    }

    private function rolesPermissionsSelectedForReviewTimelineDescription(): string
    {
        return 'The shared roles-permissions workspace is now loading this saved role from Laravel data instead of only static preview rows.';
    }

    private function rolesPermissionsStatusTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsStatusTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsStatusTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s status reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsStatusTimelineDescription(Role $selectedRole): string
    {
        return $selectedRole->is_active
            ? 'This role is currently marked as active in Laravel and the management context now treats it as a live access shell.'
            : 'This role is currently marked as draft in Laravel, so the management context keeps it in a safer parity-review posture.';
    }

    private function rolesPermissionsLifecycleTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsLifecycleTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsLifecycleTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s lifecycle freshness reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsLastSavedTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsLastSavedTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsLastSavedTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s last saved timestamp reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsReviewNoteTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsReviewNoteTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsReviewNoteTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s review note reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsAccessNoteTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsAccessNoteTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsAccessNoteTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s access note reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsAssignmentNoteTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsAssignmentNoteTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsAssignmentNoteTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s assignment note reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsScopePostureTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsScopePostureTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsScopePostureTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s scope posture reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsPermissionBundleTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsPermissionBundleTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsPermissionBundleTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s permission bundle reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsPermissionBundleTimelineDescription(mixed $permissionPreview): string
    {
        return $permissionPreview->isNotEmpty()
            ? sprintf('This role currently exposes %s in Laravel and the review context now mirrors that access bundle.', $permissionPreview->take(3)->implode(', '))
            : 'This role currently has no linked permissions in Laravel, so it remains a safe draft for parity-first access review.';
    }

    private function rolesPermissionsPermissionReviewNoteTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsPermissionReviewNoteTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsPermissionReviewNoteTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s permission review note reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsPermissionReviewNoteTimelineDescription(Role $selectedRole): string
    {
        return ($permissionReviewNote = $selectedRole->permissions
            ->pluck('review_note')
            ->first(fn (?string $note): bool => is_string($note) && trim($note) !== '')) !== null
            ? sprintf('The current Laravel permission guidance says: %s', $permissionReviewNote)
            : 'No linked permission review note is saved yet, so permission-bundle guidance still depends on the surrounding workspace cues.';
    }

    private function rolesPermissionsAssignmentScopeTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsAssignmentScopeTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsAssignmentScopeTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s assignment scope reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsAssignmentScopeTimelineDescription(Role $selectedRole, mixed $scope): string
    {
        return $scope->isNotEmpty()
            ? sprintf('This role is currently linked to %d assigned users across %s in Laravel review mode.', $selectedRole->users_count, $scope->join(', '))
            : 'This role is not linked to any scoped shops yet, so it remains a safer draft target for access-parity review.';
    }

    private function rolesPermissionsLifecycleDependencyLabel(Role $selectedRole): string
    {
        return $this->rolesPermissionsLifecycleFreshness($selectedRole);
    }

    private function rolesPermissionsLifecycleTimelineDescription(Role $selectedRole): string
    {
        if ($selectedRole->updated_at === null || $selectedRole->created_at === null) {
            return 'This role does not expose complete Laravel timestamps yet, so lifecycle freshness should stay in review-only posture.';
        }

        return $selectedRole->updated_at->equalTo($selectedRole->created_at)
            ? sprintf('This role was created in Laravel on %s and has not been updated since, so operators are still reviewing the first saved access shell.', $selectedRole->created_at->format('Y-m-d H:i'))
            : sprintf('This role was last updated in Laravel on %s, so the review workspace now reflects post-creation access metadata.', $selectedRole->updated_at->format('Y-m-d H:i'));
    }

    private function rolesPermissionsLastSavedLabel(Role $selectedRole): string
    {
        return $this->lastSavedLabel($selectedRole, 'Y-m-d H:i', 'timestamp visibility pending');
    }

    private function rolesPermissionsLastSavedTimelineDescription(Role $selectedRole): string
    {
        return $this->rolesPermissionsLastSavedSummary($selectedRole);
    }

    private function rolesPermissionsLastSavedSummary(Role $selectedRole): string
    {
        return $selectedRole->updated_at !== null
            ? sprintf('The latest saved Laravel timestamp for this role is %s, giving operators a concrete checkpoint for the current access shell.', $selectedRole->updated_at->format('Y-m-d H:i'))
            : 'This role does not expose a latest saved Laravel timestamp yet, so the current access shell should stay in review-only posture.';
    }

    private function rolesPermissionsReviewFreshness(Role $selectedRole): string
    {
        return match (true) {
            filled($selectedRole->review_note) && $selectedRole->updated_at !== null && $selectedRole->created_at !== null && $selectedRole->updated_at->equalTo($selectedRole->created_at) => 'First review note is already saved on the initial Laravel access shell.',
            filled($selectedRole->review_note) => 'Review note is already saved on the current Laravel access shell.',
            $selectedRole->is_active => 'Live role still needs a saved review note before access handoff can feel grounded.',
            default => 'Draft role still needs a saved review note before parity handoff can feel grounded.',
        };
    }

    private function rolesPermissionsReviewNoteLabel(Role $selectedRole): string
    {
        return $selectedRole->review_note ?: 'No review note saved yet';
    }

    private function rolesPermissionsReviewNoteTimelineDescription(Role $selectedRole): string
    {
        return $this->rolesPermissionsReviewNoteSummary($selectedRole);
    }

    private function rolesPermissionsReviewNoteSummary(Role $selectedRole): string
    {
        return $selectedRole->review_note !== null && trim($selectedRole->review_note) !== ''
            ? sprintf('The current Laravel review note says: %s', $selectedRole->review_note)
            : 'No Laravel review note is saved yet, so parity-sensitive operator context still depends on the surrounding workspace cues.';
    }

    private function rolesPermissionsAccessNoteLabel(Role $selectedRole): string
    {
        return $selectedRole->access_note ?: 'No access note saved yet';
    }

    private function rolesPermissionsAccessNoteTimelineDescription(Role $selectedRole): string
    {
        return $this->rolesPermissionsAccessNoteSummary($selectedRole);
    }

    private function rolesPermissionsAccessNoteSummary(Role $selectedRole): string
    {
        return $selectedRole->access_note !== null && trim($selectedRole->access_note) !== ''
            ? sprintf('The current Laravel access note says: %s', $selectedRole->access_note)
            : 'No Laravel access note is saved yet, so access handoff guidance still depends on the surrounding workspace cues.';
    }

    private function rolesPermissionsAssignmentNoteLabel(Role $selectedRole): string
    {
        return $selectedRole->assignment_note ?: 'No assignment note saved yet';
    }

    private function rolesPermissionsAssignmentNoteTimelineDescription(Role $selectedRole): string
    {
        return $this->rolesPermissionsAssignmentNoteSummary($selectedRole);
    }

    private function rolesPermissionsAssignmentNoteSummary(Role $selectedRole): string
    {
        return $selectedRole->assignment_note !== null && trim($selectedRole->assignment_note) !== ''
            ? sprintf('The current Laravel assignment note says: %s', $selectedRole->assignment_note)
            : 'No Laravel assignment note is saved yet, so assignment handoff guidance still depends on the surrounding workspace cues.';
    }

    private function rolesPermissionsCoverageSignal(Role $selectedRole, mixed $scope): string
    {
        return $this->rolesPermissionsCoverageSignalSummary($selectedRole, $scope);
    }

    private function rolesPermissionsCoverageSignalSummary(Role $selectedRole, mixed $scope): string
    {
        return match (true) {
            $scope->isNotEmpty() && $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 => 'scope, staff, and permission coverage visible',
            $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 => 'staff and permission coverage visible, scope pending',
            $scope->isNotEmpty() && ($selectedRole->users_count > 0 || $selectedRole->permissions_count > 0) => 'scope visible, access coverage building out',
            $scope->isNotEmpty() => 'scope visible, staff and permission coverage pending',
            $selectedRole->users_count > 0 || $selectedRole->permissions_count > 0 => 'partial access coverage visible, scope pending',
            default => 'scope, staff, and permission coverage pending',
        };
    }

    private function rolesPermissionsStatusSignal(Role $selectedRole, mixed $scope): string
    {
        return $this->rolesPermissionsStatusSignalSummary($selectedRole, $scope);
    }

    private function rolesPermissionsStatusSignalSummary(Role $selectedRole, mixed $scope): string
    {
        return match (true) {
            ! $selectedRole->is_active => 'Draft role remains safer for access-rollout parity review before any live-access discussion.',
            $scope->isNotEmpty() && $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 => 'Active role is already visible with scope, staffing, and permission coverage for live-access parity review.',
            $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 => 'Active role is already visible with staffing and permission coverage while scope rollout is still pending.',
            $selectedRole->permissions_count > 0 => 'Active role is already visible with a live permission bundle for matrix parity review.',
            $selectedRole->users_count > 0 => 'Active role is already visible with staffing coverage while permission rollout is still pending.',
            default => 'Active role shell is visible, but staffing and permission coverage are still pending.',
        };
    }

    private function rolesPermissionsCatalogReviewMatrixDisabledReason(mixed $roles): string
    {
        $permissionLinkedCount = $roles->filter(fn (Role $role): bool => $role->permissions_count > 0)->count();
        $activeCount = $roles->where('is_active', true)->count();

        return $this->rolesPermissionsCatalogReviewMatrixDisabledReasonSummary($permissionLinkedCount, $activeCount);
    }

    private function rolesPermissionsCatalogReviewMatrixDisabledReasonSummary(int $permissionLinkedCount, int $activeCount): string
    {
        return match (true) {
            $permissionLinkedCount > 0 => 'Blocked until saved Laravel permission bundles are verified against legacy staff access.',
            $activeCount > 0 => 'Blocked until an active role has a first verified Laravel permission bundle to compare against legacy staff access.',
            default => 'Blocked until a saved draft role has a first verified Laravel permission bundle to compare against legacy staff access.',
        };
    }

    private function rolesPermissionsCatalogPublishRoleDisabledReason(mixed $roles): string
    {
        $permissionLinkedCount = $roles->filter(fn (Role $role): bool => $role->permissions_count > 0)->count();
        $scopedCount = $roles->flatMap(fn (Role $role) => $role->users->pluck('shop_id'))->filter()->unique()->count();
        $activeCount = $roles->where('is_active', true)->count();

        return $this->rolesPermissionsCatalogPublishRoleDisabledReasonSummary($permissionLinkedCount, $scopedCount, $activeCount);
    }

    private function rolesPermissionsCatalogPublishRoleDisabledReasonSummary(int $permissionLinkedCount, int $scopedCount, int $activeCount): string
    {
        return match (true) {
            $permissionLinkedCount > 0 && $scopedCount > 0 => 'Blocked until saved live access bundles clear assignment and shop-scope parity.',
            $permissionLinkedCount > 0 => 'Blocked until saved permission-linked roles also clear shop-scope parity.',
            $activeCount > 0 => 'Blocked until an active role has verified permission-bundle and shop-scope parity.',
            default => 'Blocked until a saved draft role has verified permission-bundle and shop-scope parity.',
        };
    }

    private function rolesPermissionsReviewMatrixDisabledReason(Role $selectedRole): string
    {
        $permissionCount = $selectedRole->permissions->count();
        $assignedUserCount = $selectedRole->users_count ?? $selectedRole->users->count();

        return $this->rolesPermissionsReviewMatrixDisabledReasonSummary($selectedRole, $permissionCount, $assignedUserCount);
    }

    private function rolesPermissionsReviewMatrixDisabledReasonSummary(Role $selectedRole, int $permissionCount, int $assignedUserCount): string
    {
        return match (true) {
            $permissionCount > 0 && $assignedUserCount > 0 => 'Blocked until this assignment-sensitive Laravel permission bundle is verified against legacy staff access.',
            $permissionCount > 0 => 'Blocked until the Laravel permission matrix can be verified against legacy staff access for this live bundle.',
            $selectedRole->is_active => 'Blocked until this active role has a first verified Laravel permission bundle to compare against legacy staff access.',
            default => 'Blocked until this draft role has a first verified Laravel permission bundle to compare against legacy staff access.',
        };
    }

    private function rolesPermissionsPublishRoleDisabledReason(Role $selectedRole, mixed $scope): string
    {
        $permissionCount = $selectedRole->permissions->count();
        $assignedUserCount = $selectedRole->users_count ?? $selectedRole->users->count();

        return $this->rolesPermissionsPublishRoleDisabledReasonSummary($selectedRole, $scope, $permissionCount, $assignedUserCount);
    }

    private function rolesPermissionsPublishRoleDisabledReasonSummary(Role $selectedRole, mixed $scope, int $permissionCount, int $assignedUserCount): string
    {
        return match (true) {
            ! $selectedRole->is_active => 'Blocked until this draft role has a verified permission bundle and shop scope parity.',
            $permissionCount === 0 && $scope->isEmpty() => 'Blocked until this active role has both a verified permission bundle and shop scope parity.',
            $permissionCount === 0 => 'Blocked until this active role has a verified permission bundle to compare against legacy staff access.',
            $scope->isEmpty() => 'Blocked until this live permission bundle also has verified shop scope parity.',
            $assignedUserCount > 0 => 'Blocked until assignment-sensitive live role parity is verified for this Laravel permission bundle.',
            default => 'Blocked until live role assignment parity is verified for this Laravel permission bundle.',
        };
    }

    private function rolesPermissionsScopeRolloutValue(mixed $scope): string
    {
        return $this->rolesPermissionsScopeRolloutValueSummary($scope);
    }

    private function rolesPermissionsScopeRolloutValueSummary(mixed $scope): string
    {
        return $scope->isNotEmpty() ? 'shop-scope-visible' : 'shop-scope-pending';
    }

    private function rolesPermissionsScopeRolloutSummaryPosture(mixed $scope): string
    {
        return $this->rolesPermissionsScopeRolloutSummary($scope);
    }

    private function rolesPermissionsScopeRolloutSummary(mixed $scope): string
    {
        return $scope->isNotEmpty()
            ? 'Shop scope is visible in Laravel review, but scope writes should stay parity-first until the next thin access slice is ready.'
            : 'Shop scope is still pending in Laravel review, which keeps this role safer for draft-first parity checks.';
    }

    private function rolesPermissionsScopeCoverageLabel(mixed $scope): string
    {
        return $this->rolesPermissionsScopeCoverageLabelSummary($scope);
    }

    private function rolesPermissionsScopeCoverageLabelSummary(mixed $scope): string
    {
        $scopeCount = $scope->count();

        return match (true) {
            $scopeCount === 0 => 'No shop scope linked yet',
            $scopeCount === 1 => '1 shop visible in Laravel review',
            default => sprintf('%d shops visible in Laravel review', $scopeCount),
        };
    }

    private function rolesPermissionsScopeCoverageTimelineTitle(Role $selectedRole): string
    {
        return $this->rolesPermissionsScopeCoverageTimelineTitleSummary($selectedRole);
    }

    private function rolesPermissionsScopeCoverageTimelineTitleSummary(Role $selectedRole): string
    {
        return sprintf('%s scope coverage reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsScopeCoverageDependencyLabel(mixed $scope): string
    {
        return $this->rolesPermissionsScopeCoverageDependencySummary($scope);
    }

    private function rolesPermissionsScopeCoverageDependencySummary(mixed $scope): string
    {
        return $this->rolesPermissionsScopeCoverageLabel($scope);
    }

    private function rolesPermissionsScopePostureTimelineDescription(mixed $scope): string
    {
        return $this->rolesPermissionsScopePostureSummary($scope);
    }

    private function rolesPermissionsScopePostureSummary(mixed $scope): string
    {
        return $scope->isNotEmpty()
            ? sprintf('This role currently shows shop scope across %s in Laravel review mode, so scope rollout stays visible while writes remain gated.', $scope->join(', '))
            : 'This role currently has no linked shop scope in Laravel, so the review context keeps it in a safer scope-pending posture.';
    }

    private function rolesPermissionsScopeCoverageTimelineDescription(mixed $scope): string
    {
        return $this->rolesPermissionsScopeCoverageSummary($scope);
    }

    private function rolesPermissionsScopeCoverageSummary(mixed $scope): string
    {
        $scopeCount = $scope->count();

        return match (true) {
            $scopeCount === 0 => 'No shops are currently linked to this role in Laravel review, so scope coverage remains empty while rollout stays blocked.',
            $scopeCount === 1 => sprintf('This role currently exposes shop scope across %s in Laravel review, giving operators one visible branch to compare before any scope writes are enabled.', $scope->join(', ')),
            default => sprintf('This role currently exposes shop scope across %d shops in Laravel review, so operators can verify branch coverage before any scope writes are enabled.', $scopeCount),
        };
    }

    private function rolesPermissionsScopeRolloutDependencyPosture(mixed $scope): string
    {
        return $this->rolesPermissionsScopeRolloutDependencySummary($scope);
    }

    private function rolesPermissionsScopeRolloutDependencySummary(mixed $scope): string
    {
        return $scope->isNotEmpty()
            ? 'This role already shows shop scope in Laravel review, but scope mutation should stay blocked until a dedicated access slice is verified.'
            : 'This role has no visible shop scope yet, so scope rollout should stay in review-only posture until a dedicated access slice is ready.';
    }

    private function rolesPermissionsSelectedRoleTimeline(Role $selectedRole, mixed $scope, mixed $permissionPreview): array
    {
        return [
            [
                'title' => $this->rolesPermissionsSelectedForReviewTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsSelectedForReviewTimelineDescription(),
            ],
            [
                'title' => $this->rolesPermissionsStatusTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsStatusTimelineDescription($selectedRole),
            ],
            [
                'title' => $this->rolesPermissionsLifecycleTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsLifecycleTimelineDescription($selectedRole),
            ],
            [
                'title' => $this->rolesPermissionsLastSavedTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsLastSavedTimelineDescription($selectedRole),
            ],
            [
                'title' => $this->rolesPermissionsReviewNoteTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsReviewNoteTimelineDescription($selectedRole),
            ],
            [
                'title' => $this->rolesPermissionsAccessNoteTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsAccessNoteTimelineDescription($selectedRole),
            ],
            [
                'title' => $this->rolesPermissionsAssignmentNoteTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsAssignmentNoteTimelineDescription($selectedRole),
            ],
            [
                'title' => $this->rolesPermissionsScopePostureTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsScopePostureTimelineDescription($scope),
            ],
            [
                'title' => $this->rolesPermissionsScopeCoverageTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsScopeCoverageTimelineDescription($scope),
            ],
            [
                'title' => $this->rolesPermissionsPermissionBundleTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsPermissionBundleTimelineDescription($permissionPreview),
            ],
            [
                'title' => $this->rolesPermissionsPermissionReviewNoteTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsPermissionReviewNoteTimelineDescription($selectedRole),
            ],
            [
                'title' => $this->rolesPermissionsAssignmentScopeTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsAssignmentScopeTimelineDescription($selectedRole, $scope),
            ],
            [
                'title' => $this->rolesPermissionsTimelineHandoffTitle(),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsTimelineHandoffDescription($selectedRole, $scope),
            ],
        ];
    }

    private function rolesPermissionsTimelineHandoffTitle(): string
    {
        return 'Access handoff stays visible in the workspace';
    }

    private function rolesPermissionsTimelineHandoffDescription(Role $selectedRole, mixed $scope): string
    {
        return match (true) {
            ! $selectedRole->is_active => 'Operators should carry draft status, scope gaps, and permission-bundle gaps in the live workspace before trusting any publish or matrix-edit follow-up.',
            $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 && $scope->isNotEmpty() => 'Operators should carry visible shop scope, assigned staff, and permission-bundle coverage in the live workspace before trusting any publish or matrix-edit follow-up.',
            $selectedRole->permissions_count > 0 && $scope->isNotEmpty() => 'Operators should carry visible shop scope, permission-bundle coverage, and staffing gaps in the live workspace before trusting any publish or matrix-edit follow-up.',
            $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 => 'Operators should carry assigned staff, permission-bundle coverage, and scope gaps in the live workspace before trusting any publish or matrix-edit follow-up.',
            default => 'Operators should carry draft access coverage, scope gaps, and staffing gaps in the live workspace before trusting any publish or matrix-edit follow-up.',
        };
    }

    private function rolesPermissionsSelectedRoleDependencyStatus(Role $selectedRole, mixed $scope, mixed $permissionPreview): array
    {
        $activeShopAssignedUserCount = $selectedRole->users->filter(fn ($user): bool => (bool) $user->shop?->is_active)->count();
        $pausedShopAssignedUserCount = $selectedRole->users->filter(fn ($user): bool => $user->shop !== null && ! $user->shop->is_active)->count();
        $permissionBranchActivitySignal = $selectedRole->permissions_count > 0 && $activeShopAssignedUserCount > 0 && $pausedShopAssignedUserCount > 0
            ? sprintf('%d permission-linked staff are already visible in active branches beside %d permission-linked staff in paused shops for parity review', $activeShopAssignedUserCount, $pausedShopAssignedUserCount)
            : 'paused-branch permission-linked staff coverage is still pending for parity review';
        $scopedPermissionSignal = $selectedRole->permissions_count > 0 && $scope->isNotEmpty()
            ? sprintf('%d scoped shops are already visible for this permission-linked role in parity review', $scope->count())
            : 'scoped permission coverage is still pending for parity review';
        $permissionReviewNote = $selectedRole->permissions
            ->pluck('review_note')
            ->first(fn (?string $note): bool => is_string($note) && trim($note) !== '');

        return [
            ['label' => 'Selected role', 'value' => $selectedRole->name],
            ['label' => 'Review posture', 'value' => 'Selected-role review is running in Laravel-backed read mode only'],
            ['label' => 'Status posture', 'value' => $selectedRole->is_active
                ? 'This role is active in Laravel now, but live-facing access changes should still stay parity-first until assignment and matrix flows are verified.'
                : 'This role remains draft in Laravel, which keeps it safer for parity checks before operators depend on it for live access.'],
            ['label' => 'Lifecycle freshness', 'value' => $this->rolesPermissionsLifecycleDependencyLabel($selectedRole)],
            ['label' => 'Last saved in Laravel', 'value' => $this->rolesPermissionsLastSavedLabel($selectedRole)],
            ['label' => 'Review note', 'value' => $this->rolesPermissionsReviewNoteLabel($selectedRole)],
            ['label' => 'Review freshness', 'value' => $this->rolesPermissionsReviewFreshness($selectedRole)],
            ['label' => 'Access note', 'value' => $this->rolesPermissionsAccessNoteLabel($selectedRole)],
            ['label' => 'Assignment note', 'value' => $this->rolesPermissionsAssignmentNoteLabel($selectedRole)],
            ['label' => 'Coverage signal', 'value' => $this->rolesPermissionsCoverageSignal($selectedRole, $scope)],
            ['label' => 'Role status signal', 'value' => $this->rolesPermissionsStatusSignal($selectedRole, $scope)],
            ['label' => 'Scope rollout posture', 'value' => $this->rolesPermissionsScopeRolloutDependencyPosture($scope)],
            ['label' => 'Scope coverage', 'value' => $this->rolesPermissionsScopeCoverageDependencyLabel($scope)],
            ['label' => 'Matrix posture', 'value' => 'Keep matrix editing blocked until legacy staff-access parity is verified in Laravel'],
            ['label' => 'Assigned staff posture', 'value' => $selectedRole->users_count > 0
                ? 'Linked staff are already affected by this role in Laravel, so assignment parity should be checked before any access changes move forward.'
                : 'No linked staff are affected yet, which keeps this role safer for draft review before assignment parity is confirmed.'],
            ['label' => 'Assignment branch activity signal', 'value' => $activeShopAssignedUserCount > 0 && $pausedShopAssignedUserCount > 0
                ? sprintf('%d assigned staff are already visible in active branches beside %d assigned staff in paused shops for parity review', $activeShopAssignedUserCount, $pausedShopAssignedUserCount)
                : 'paused-branch assignment coverage is still pending for parity review'],
            ['label' => 'Permission posture', 'value' => $permissionPreview->isNotEmpty()
                ? 'The visible Laravel permission bundle is reviewable now, but bundle edits should stay blocked until legacy access mapping is verified.'
                : 'No permissions are linked yet, so this role remains a safer draft shell for parity-first access review.'],
            ['label' => 'Permission review note', 'value' => $permissionReviewNote ?: 'No linked permission review note saved yet'],
            ['label' => 'Scoped permission signal', 'value' => $scopedPermissionSignal],
            ['label' => 'Permission branch activity signal', 'value' => $permissionBranchActivitySignal],
            ['label' => 'Publish posture', 'value' => $selectedRole->is_active
                ? 'This live permission bundle still needs assignment parity checks before publish-style role changes are safe.'
                : 'This draft role should stay unpublished until permission bundle and shop-scope parity are mapped more explicitly.'],
                    ['label' => 'Scope posture', 'value' => $scope->isNotEmpty()
                        ? 'Assigned shops are visible for review, but scope writes should stay parity-first until staff assignment rules are confirmed.'
                        : 'No shop scope is linked yet, which keeps this role safe for draft access review.'],
                    ['label' => 'Handoff signal', 'value' => $this->rolesPermissionsHandoffSignal($selectedRole, $scope)],
                    ['label' => 'Remaining backend gap', 'value' => $this->rolesPermissionsBackendGap($selectedRole)],
        ];
    }

    private function cardsSelectedCardSummary(Card $selectedCard): array
    {
        return [
            ['label' => 'Selected card', 'value' => $this->cardsSelectedCardLabel($selectedCard)],
            ['label' => 'Review mode', 'value' => $selectedCard->status === 'draft'
                ? 'Draft-safe review, this inventory record is still safer for parity checks before operators treat it as issued stock.'
                : 'Live inventory review, this saved Laravel card already carries operational state that should stay parity-first.'],
            ['label' => 'Card status signal', 'value' => $this->cardsStatusSignal($selectedCard)],
            ['label' => 'Operational readiness', 'value' => $this->cardsOperationalReadiness($selectedCard)],
            ['label' => 'Lifecycle stage', 'value' => $this->cardsLifecycleStage($selectedCard)],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardsLifecycleFreshnessLabel($selectedCard)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardsLastSavedLabel($selectedCard)],
            ['label' => 'Review note', 'value' => $this->cardsReviewNoteLabel($selectedCard)],
            ['label' => 'Holder', 'value' => $selectedCard->holder?->full_name ?? 'Unassigned'],
            ['label' => 'Card type', 'value' => $selectedCard->type?->name ?? 'Unknown'],
            ['label' => 'Linkage signal', 'value' => $this->cardsLinkageSignal($selectedCard)],
            ['label' => 'Inventory focus', 'value' => $this->cardsInventoryFocus($selectedCard)],
            ['label' => 'Inventory posture', 'value' => $this->cardsInventoryPosture($selectedCard)],
            ['label' => 'Evidence priority', 'value' => $this->cardsEvidencePriority($selectedCard)],
            ['label' => 'Inventory handoff signal', 'value' => $this->cardsInventoryHandoffSignal($selectedCard)],
            ['label' => 'Backend gap', 'value' => $this->cardsBackendGap($selectedCard)],
            ['label' => 'Shop', 'value' => $selectedCard->shop?->name ?? 'Unassigned'],
            ['label' => 'Shop guidance', 'value' => $this->cardsShopGuidance($selectedCard)],
            ['label' => 'Laravel status', 'value' => $selectedCard->status],
            ['label' => 'Issued', 'value' => $this->cardsIssuedLabel($selectedCard)],
            ['label' => 'Activated', 'value' => $this->cardsActivatedLabel($selectedCard)],
            ['label' => 'Blocked pre-activation signal', 'value' => $this->cardsBlockedPreActivationSignal($selectedCard)],
            ['label' => 'Blocked activated signal', 'value' => $this->cardsBlockedActivatedSignal($selectedCard)],
            ['label' => 'Blocked holder-linked signal', 'value' => $this->cardsBlockedHolderLinkedSignal($selectedCard)],
            ['label' => 'Blocked unassigned signal', 'value' => $this->cardsBlockedUnassignedSignal($selectedCard)],
            ['label' => 'Pre-activation holder-linked signal', 'value' => $this->cardsPreActivationHolderLinkedSignal($selectedCard)],
            ['label' => 'Pre-activation unassigned signal', 'value' => $this->cardsPreActivationUnassignedSignal($selectedCard)],
            ['label' => 'Active holder-linked signal', 'value' => $this->cardsActiveHolderLinkedSignal($selectedCard)],
            ['label' => 'Active unassigned signal', 'value' => $this->cardsActiveUnassignedSignal($selectedCard)],
            ['label' => 'Holder linkage summary', 'value' => $this->cardsHolderLinkageSummary($selectedCard)],
            ['label' => 'Assignment readiness summary', 'value' => $this->cardsAssignmentReadinessSummary($selectedCard)],
            ['label' => 'Assignment posture', 'value' => $this->cardsAssignmentPosture($selectedCard)],
            ['label' => 'Dispute posture', 'value' => $this->cardsDisputePosture($selectedCard)],
            ['label' => 'Activation readiness', 'value' => $this->cardsActivationReadiness($selectedCard)],
            [
                'label' => 'Inventory guidance',
                'value' => $this->cardsInventoryGuidance($selectedCard),
            ],
        ];
    }

    private function cardsSelectedCardLabel(Card $selectedCard): string
    {
        return $selectedCard->number;
    }

    private function cardsStatusSignal(Card $selectedCard): string
    {
        return match ($selectedCard->status) {
            'active' => 'Active card is already visible for live inventory parity review.',
            'blocked' => 'Blocked card remains in operator review posture until dispute parity is verified.',
            default => 'Draft card remains safer for issuance-parity review before any issuance-flow discussion.',
        };
    }

    private function cardsReviewNoteLabel(Card $selectedCard): string
    {
        return $selectedCard->review_note ?: 'No review note saved yet';
    }

    private function cardsIssuedLabel(Card $selectedCard): string
    {
        return $selectedCard->issued_at?->format('Y-m-d') ?? '—';
    }

    private function cardsActivatedLabel(Card $selectedCard): string
    {
        return $selectedCard->activated_at?->format('Y-m-d') ?? '—';
    }

    private function cardsOperationalReadiness(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'blocked' => 'blocked inventory, operator review only',
            $selectedCard->status === 'active' && $selectedCard->holder !== null => 'issued inventory, parity-sensitive',
            $selectedCard->status === 'active' => 'active inventory, holder linkage incomplete',
            $selectedCard->issued_at !== null && $selectedCard->activated_at === null => 'issued inventory, activation pending',
            default => 'draft inventory shell',
        };
    }

    private function cardsLifecycleStage(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->issued_at === null => 'Draft inventory shell, not yet issued in Laravel.',
            $selectedCard->activated_at === null => 'Issued inventory shell, still waiting for activation parity review.',
            default => 'Issued and activated inventory already visible in Laravel.',
        };
    }

    private function cardsShopGuidance(Card $selectedCard): string
    {
        return $selectedCard->shop !== null
            ? 'Keep this card tied to its current branch context during review, because cross-shop inventory handling was parity-sensitive in the old Galaxy flow.'
            : 'No branch is linked yet, so shop-level handling should stay in parity review before operators rely on this card record operationally.';
    }

    private function cardsInventoryGuidance(Card $selectedCard): string
    {
        return match ($selectedCard->status) {
            'active' => 'This card is already active in Laravel, so inventory changes should stay parity-first until blocked and replacement semantics are verified.',
            'blocked' => 'This card is blocked in Laravel, so replacement and dispute handling should remain review-only until legacy card-state parity is confirmed.',
            default => 'This card is still draft inventory in Laravel, which keeps it safe for parity checks before operators treat it as issued stock.',
        };
    }

    private function cardsActivationReadiness(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->issued_at !== null && $selectedCard->activated_at === null => 'Issued inventory is still waiting for activation review in Laravel.',
            $selectedCard->activated_at !== null => 'Activation is already recorded in Laravel for this issued card.',
            default => 'This card has not been issued yet, so activation should remain out of scope during parity review.',
        };
    }

    private function cardsBlockedPreActivationSignal(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'blocked' && $selectedCard->issued_at !== null && $selectedCard->activated_at === null => 'Blocked inventory was issued but never activated, so dispute review should stay separate from active-card recovery handling.',
            $selectedCard->status === 'blocked' => 'Blocked inventory already carries activation context in Laravel for dispute-first review.',
            default => 'Blocked pre-activation review is out of scope for this card right now.',
        };
    }

    private function cardsBlockedActivatedSignal(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'blocked' && $selectedCard->activated_at !== null => 'Blocked inventory had already reached activation before dispute review in Laravel.',
            $selectedCard->status === 'blocked' => 'Blocked activated review is not the active slice for this card right now.',
            default => 'Blocked activated review is out of scope for this card right now.',
        };
    }

    private function cardsBlockedHolderLinkedSignal(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'blocked' && $selectedCard->holder !== null => 'Blocked inventory already carries holder linkage, so dispute and replacement review can stay anchored to the current member record.',
            $selectedCard->status === 'blocked' => 'Blocked holder-linked review is not the active slice for this card right now.',
            default => 'Blocked holder-linked review is out of scope for this card right now.',
        };
    }

    private function cardsBlockedUnassignedSignal(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'blocked' && $selectedCard->holder === null => 'Blocked inventory is still unassigned, so replacement and reassignment review should stay explicit before any holder recovery assumptions are made.',
            $selectedCard->status === 'blocked' => 'Blocked inventory already carries holder linkage in Laravel for dispute-first review.',
            default => 'Blocked unassigned review is out of scope for this card right now.',
        };
    }

    private function cardsPreActivationHolderLinkedSignal(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->issued_at !== null && $selectedCard->activated_at === null && $selectedCard->holder !== null => 'Pre-activation inventory already carries holder linkage, so activation parity can stay anchored to the current member record before live usage expands.',
            $selectedCard->issued_at !== null && $selectedCard->activated_at === null => 'Pre-activation holder-linked review is not the active slice for this card right now.',
            default => 'Pre-activation holder-linked review is out of scope for this card right now.',
        };
    }

    private function cardsPreActivationUnassignedSignal(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->issued_at !== null && $selectedCard->activated_at === null && $selectedCard->holder === null => 'Pre-activation inventory is still unassigned, so holder recovery should stay explicit before activation parity widens into live usage.',
            $selectedCard->issued_at !== null && $selectedCard->activated_at === null => 'Pre-activation unassigned review is not the active slice for this card right now.',
            default => 'Pre-activation unassigned review is out of scope for this card right now.',
        };
    }

    private function cardsActiveHolderLinkedSignal(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'active' && $selectedCard->holder !== null => 'Active inventory already carries holder linkage, so parity review can stay anchored to the current member record before later replacement flows are widened.',
            $selectedCard->status === 'active' => 'Active holder-linked review is not the active slice for this card right now.',
            default => 'Active holder-linked review is out of scope for this card right now.',
        };
    }

    private function cardsActiveUnassignedSignal(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'active' && $selectedCard->holder === null => 'Active inventory is still unassigned, so holder-linkage recovery should stay visible before operators assume a stable member attachment.',
            $selectedCard->status === 'active' => 'Active inventory already carries holder linkage in Laravel for parity-first review.',
            default => 'Active unassigned review is out of scope for this card right now.',
        };
    }

    private function cardsHolderLinkageSummary(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->holder !== null && $selectedCard->status === 'blocked' => 'Holder linkage is present, but this card stays in blocked dispute review until replacement parity is proven.',
            $selectedCard->holder !== null => 'Holder linkage is already present in Laravel for this card review.',
            $selectedCard->issued_at !== null => 'Holder linkage is still missing from this issued card, so assignment recovery remains visible.',
            default => 'Holder linkage is still intentionally absent while this card stays in draft inventory review.',
        };
    }

    private function cardsAssignmentReadinessSummary(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->holder !== null && $selectedCard->status === 'blocked' => 'Holder linkage is present, but assignment changes stay dispute-gated while blocked parity is still under review.',
            $selectedCard->holder !== null => 'Holder linkage is present, so assignment state is ready for parity-first review in Laravel.',
            $selectedCard->issued_at !== null => 'Assignment state is still pending because this issued card has not been linked to a holder yet.',
            default => 'Assignment state is still intentionally pending while this card remains a draft inventory shell.',
        };
    }

    private function cardsAssignmentPosture(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'active' && $selectedCard->holder !== null => 'Active inventory is already anchored to a holder record, so parity review can stay member-linked before wider replacement work opens up.',
            $selectedCard->status === 'active' => 'Active inventory still lacks holder linkage, so assignment recovery should stay visible before operators trust this as stable member stock.',
            $selectedCard->status === 'blocked' && $selectedCard->holder !== null => 'Blocked inventory already carries holder linkage, so dispute review can stay member-linked while replacement and reassignment remain parity-gated.',
            $selectedCard->status === 'blocked' => 'Blocked inventory is still unassigned, so reassignment and replacement review should stay explicit before any holder recovery assumptions are trusted.',
            $selectedCard->issued_at !== null => 'Issued inventory is still unassigned, so assignment work should stay recovery-first until holder linkage catches up with activation review.',
            default => 'Draft inventory is still unassigned, which keeps assignment work safely in shell-review mode before issuance begins.',
        };
    }

    private function cardsDisputePosture(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'blocked' && $selectedCard->activated_at === null => 'Treat this as blocked inventory triage before any active-card recovery assumptions are made.',
            $selectedCard->status === 'blocked' => 'Treat this as a dispute-first blocked card with prior activation context still visible in Laravel.',
            default => 'Dispute handling is not the primary posture for this card right now.',
        };
    }

    private function cardsInventoryFocus(Card $selectedCard): string
    {
        return match ($selectedCard->status) {
            'blocked' => 'Start with blocked status, holder linkage, and dispute context before discussing any later replacement or reassignment flow.',
            'active' => 'Start with card status, holder linkage, and branch ownership before discussing any later replacement or reassignment flow.',
            default => 'Start with draft status, holder linkage gaps, and branch ownership before discussing any later issuance or reassignment flow.',
        };
    }

    private function cardsInventoryPosture(Card $selectedCard): string
    {
        return match ($selectedCard->status) {
            'blocked' => 'Keep blocked inventory in dispute-first review, then leave replacement, reassignment, and cross-branch moves gated until parity is proven.',
            'active' => 'Keep inventory review in the live workspace first, then leave replacement, reassignment, and cross-branch moves gated until parity is proven.',
            default => 'Keep draft inventory in issuance-readiness review first, then leave activation, reassignment, and cross-branch moves gated until parity is proven.',
        };
    }

    private function cardsEvidencePriority(Card $selectedCard): string
    {
        return match ($selectedCard->status) {
            'blocked' => 'Keep blocked status, holder linkage, and dispute context visible together before trusting any later replacement or reassignment discussion.',
            'active' => 'Keep card status, holder linkage, and branch ownership visible together before trusting any later replacement or reassignment discussion.',
            default => 'Keep draft status, holder linkage gaps, and branch ownership visible together before trusting any later issuance or reassignment discussion.',
        };
    }

    private function cardsBackendGap(Card $selectedCard): string
    {
        return match ($selectedCard->status) {
            'blocked' => 'Blocked-card handling, dispute resolution, and replacement flows should stay preview-only until inventory parity is verified.',
            'active' => 'Card lifecycle writes, blocked-card handling, and replacement flows should stay preview-only until inventory parity is verified.',
            default => 'Card issuance, activation, and lifecycle writes should stay preview-only until inventory parity is verified.',
        };
    }

    private function cardsLifecycleFreshnessLabel(Card $selectedCard): string
    {
        return $this->lifecycleFreshnessLabel($selectedCard);
    }

    private function cardsLifecycleFreshnessDescription(Card $selectedCard): string
    {
        return $this->lifecycleFreshnessDescription(
            $selectedCard,
            'This card does not expose complete Laravel timestamps yet, so lifecycle freshness should stay in review-only posture.',
            'This card was created in Laravel on %s and has not been updated since, so operators are still reviewing the first saved inventory shell.',
            'This card was first created in Laravel on %s and last updated on %s, so operators are reviewing inventory that has already changed after initial setup.',
        );
    }

    private function cardsLastSavedLabel(Card $selectedCard): string
    {
        return $this->lastSavedLabel($selectedCard);
    }

    private function cardsLinkageSignal(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->holder !== null && $selectedCard->shop !== null => 'holder and branch linkage visible',
            $selectedCard->holder !== null => 'holder linked, branch visibility pending',
            $selectedCard->shop !== null => 'branch-linked inventory, holder pending',
            default => 'holder and branch linkage pending',
        };
    }

    private function cardsInventoryHandoffSignal(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'blocked' && $selectedCard->holder !== null => 'Blocked holder-linked inventory already carries enough dispute context for a useful handoff review.',
            $selectedCard->status === 'blocked' => 'Blocked inventory should stay in handoff-only posture until dispute and replacement parity are explicit.',
            $selectedCard->status === 'active' && $selectedCard->holder !== null => 'Active issued inventory already carries enough linkage context for a useful handoff review.',
            $selectedCard->status === 'active' => 'Active inventory is visible, but holder linkage context is still thin for handoff review.',
            default => 'Draft inventory should stay in handoff-only posture until issuance parity is explicit.',
        };
    }

    private function cardsInventoryTimelineHandoffDescription(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'blocked' && $selectedCard->holder !== null => 'Operators should carry blocked status, holder linkage, and dispute context in the live workspace before trusting any replacement or reassignment follow-up.',
            $selectedCard->status === 'blocked' => 'Operators should carry blocked status, branch ownership, and holder-linkage gaps in the live workspace before trusting any replacement or reassignment follow-up.',
            $selectedCard->status === 'active' && $selectedCard->holder !== null => 'Operators should carry active status, holder linkage, and branch ownership in the live workspace before trusting any replacement or reassignment follow-up.',
            $selectedCard->status === 'active' => 'Operators should carry active status, branch ownership, and holder-linkage gaps in the live workspace before trusting any replacement or reassignment follow-up.',
            default => 'Operators should carry draft status, holder-linkage gaps, and branch ownership in the live workspace before trusting any issuance or reassignment follow-up.',
        };
    }

    private function cardsSelectedCardDependencyStatus(Card $selectedCard): array
    {
        return [
            ['label' => 'Selected card', 'value' => $this->cardsSelectedCardLabel($selectedCard)],
            ['label' => 'Inventory posture', 'value' => 'Selected-card review is running in Laravel-backed read mode only'],
            ['label' => 'Card status signal', 'value' => $this->cardsStatusSignal($selectedCard)],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardsLifecycleFreshnessLabel($selectedCard)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardsLastSavedLabel($selectedCard)],
            ['label' => 'Issued', 'value' => $this->cardsIssuedLabel($selectedCard)],
            ['label' => 'Review note', 'value' => $this->cardsReviewNoteLabel($selectedCard)],
            ['label' => 'Linkage signal', 'value' => $this->cardsLinkageSignal($selectedCard)],
            ['label' => 'Inventory handoff signal', 'value' => $this->cardsInventoryHandoffSignal($selectedCard)],
            ['label' => 'Lifecycle posture', 'value' => $this->cardsLifecyclePosture($selectedCard)],
            ['label' => 'Assignment posture', 'value' => $this->cardsAssignmentDependencyPosture($selectedCard)],
            ['label' => 'Shop posture', 'value' => $this->cardsShopDependencyPosture($selectedCard)],
            ['label' => 'Remaining backend gap', 'value' => $this->cardsBackendGap($selectedCard)],
        ];
    }

    private function cardsLifecyclePosture(Card $selectedCard): string
    {
        return match ($selectedCard->status) {
            'active' => 'This active card should stay read-only until issue, block, and replacement parity are verified.',
            'blocked' => 'This blocked card should stay under review-only handling until dispute and replacement semantics match the old Galaxy flow.',
            default => 'This draft card should stay in parity review until issuance rules are confirmed in Laravel.',
        };
    }

    private function cardsAssignmentDependencyPosture(Card $selectedCard): string
    {
        return $selectedCard->holder !== null
            ? 'Holder linkage is visible now, but reassignment and replacement actions should stay blocked until inventory parity is verified.'
            : 'No holder is linked yet, which keeps this inventory record safer for assignment-flow-parity review before assignment flows are enabled.';
    }

    private function cardsShopDependencyPosture(Card $selectedCard): string
    {
        return $selectedCard->shop !== null
            ? 'Shop ownership is visible for review, but cross-branch movement should stay blocked until branch inventory rules are verified.'
            : 'No shop is assigned yet, so branch-level inventory handling should stay in review mode only.';
    }

    private function cardholdersSelectedHolderSummary(CardHolder $selectedCardHolder): array
    {
        return [
            ['label' => 'Selected holder', 'value' => $this->cardholdersSelectedHolderLabel($selectedCardHolder)],
            ['label' => 'Review mode', 'value' => $this->cardholdersReviewMode($selectedCardHolder)],
            ['label' => 'Holder status signal', 'value' => $this->cardholdersStatusSignal($selectedCardHolder)],
            ['label' => 'Operational readiness', 'value' => $this->cardholdersOperationalReadiness($selectedCardHolder)],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardholdersLifecycleFreshnessLabel($selectedCardHolder)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardholdersLastSavedLabel($selectedCardHolder)],
            ['label' => 'Review note', 'value' => $this->cardholdersReviewNoteLabel($selectedCardHolder)],
            ['label' => 'Phone', 'value' => $this->cardholdersPhoneLabel($selectedCardHolder)],
            ['label' => 'Linkage signal', 'value' => $this->cardholdersLinkageSignal($selectedCardHolder)],
            ['label' => 'Shop activity signal', 'value' => $this->cardholdersShopActivitySignal($selectedCardHolder)],
            ['label' => 'Holder focus', 'value' => $this->cardholdersHolderFocus($selectedCardHolder)],
            ['label' => 'Holder posture', 'value' => $this->cardholdersHolderPosture($selectedCardHolder)],
            ['label' => 'Evidence priority', 'value' => $this->cardholdersEvidencePriority($selectedCardHolder)],
            ['label' => 'Activity handoff signal', 'value' => $this->cardholdersActivityHandoffSignal($selectedCardHolder)],
            ['label' => 'Backend gap', 'value' => $this->cardholdersBackendGap($selectedCardHolder)],
            ['label' => 'Shop', 'value' => $this->cardholdersShopLabel($selectedCardHolder)],
            ['label' => 'Shop guidance', 'value' => $this->cardholdersShopGuidance($selectedCardHolder)],
            ['label' => 'Linked cards', 'value' => $this->cardholdersLinkedCardsLabel($selectedCardHolder)],
            ['label' => 'Laravel status', 'value' => $this->cardholdersLaravelStatus($selectedCardHolder)],
            [
                'label' => 'Lookup guidance',
                'value' => $this->cardholdersLookupGuidance($selectedCardHolder),
            ],
        ];
    }

    private function cardholdersSelectedHolderLabel(CardHolder $selectedCardHolder): string
    {
        return $selectedCardHolder->full_name;
    }

    private function cardholdersReviewMode(CardHolder $selectedCardHolder): string
    {
        return $selectedCardHolder->is_active
            ? 'Live profile review, this holder already participates in the Laravel lookup surface and should stay parity-first.'
            : 'Dormant-profile review, this inactive holder stays safer for parity checks before any reactivation path is trusted.';
    }

    private function cardholdersStatusSignal(CardHolder $selectedCardHolder): string
    {
        return $selectedCardHolder->is_active
            ? 'Active holder is already visible for live profile parity review.'
            : 'Inactive holder remains safer for reactivation-flow-parity review before any reactivation-flow discussion.';
    }

    private function cardholdersOperationalReadiness(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            ! $selectedCardHolder->is_active => 'inactive profile, review only',
            $selectedCardHolder->cards_count > 0 => 'linked profile, operator-visible',
            default => 'active profile, linkage build-out pending',
        };
    }

    private function cardholdersShopGuidance(CardHolder $selectedCardHolder): string
    {
        return $selectedCardHolder->shop !== null
            ? 'Keep this holder anchored to the current branch during review, because old Galaxy lookup flows depended on branch-aware identity context.'
            : 'No branch is linked yet, so shop-aware lookup behavior should stay in parity review before profile actions are widened.';
    }

    private function cardholdersLaravelStatus(CardHolder $selectedCardHolder): string
    {
        return $selectedCardHolder->is_active ? 'active' : 'inactive';
    }

    private function cardholdersLinkedCardsLabel(CardHolder $selectedCardHolder): string
    {
        return (string) $selectedCardHolder->cards_count;
    }

    private function cardholdersReviewNoteLabel(CardHolder $selectedCardHolder): string
    {
        return $selectedCardHolder->review_note ?: 'No review note saved yet';
    }

    private function cardholdersPhoneLabel(CardHolder $selectedCardHolder): string
    {
        return $selectedCardHolder->phone ?? '—';
    }

    private function cardholdersShopLabel(CardHolder $selectedCardHolder): string
    {
        return $selectedCardHolder->shop?->name ?? 'Unassigned';
    }

    private function cardholdersLookupGuidance(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            (bool) $selectedCardHolder->shop?->is_active === false && $selectedCardHolder->is_active => 'This holder is active in Laravel but anchored to a paused branch, so identity, linkage, and recovery review should stay parity-first until recent-activity sourcing is verified.',
            $selectedCardHolder->is_active => 'This holder is active in Laravel, so identity and linkage review should stay parity-first until recent-activity sourcing is verified.',
            default => 'This holder is inactive in Laravel, which keeps the record safe for parity checks before operators treat it as fully reactivated.',
        };
    }

    private function cardholdersHolderFocus(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            (bool) $selectedCardHolder->shop?->is_active === false && $selectedCardHolder->is_active => 'Start with paused-branch status, branch linkage, and linked-card visibility before discussing any later recovery, profile merge, or lifecycle-change edge case.',
            $selectedCardHolder->is_active => 'Start with active status, branch linkage, and linked-card visibility before discussing any later profile merge or reactivation edge case.',
            default => 'Start with inactive status, branch linkage, and linked-card visibility before discussing any later reactivation or profile merge flow.',
        };
    }

    private function cardholdersHolderPosture(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            (bool) $selectedCardHolder->shop?->is_active === false && $selectedCardHolder->is_active => 'Keep paused-branch holder review in the workspace first, then leave recovery, merge, and lifecycle-change flows gated until branch parity is proven.',
            $selectedCardHolder->is_active => 'Keep live holder review in the workspace first, then leave profile-write, merge, and lifecycle-change flows gated until parity is proven.',
            default => 'Keep inactive holder review in the workspace first, then leave reactivation, merge, and profile-write flows gated until parity is proven.',
        };
    }

    private function cardholdersEvidencePriority(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            (bool) $selectedCardHolder->shop?->is_active === false && $selectedCardHolder->is_active => 'Keep paused-branch status, branch linkage, and linked-card visibility together before trusting any later recovery, merge, or lifecycle-change discussion.',
            $selectedCardHolder->is_active => 'Keep active status, branch linkage, and linked-card visibility together before trusting any later profile merge or lifecycle-change discussion.',
            default => 'Keep inactive status, branch linkage, and linked-card visibility together before trusting any later reactivation or merge discussion.',
        };
    }

    private function cardholdersBackendGap(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            (bool) $selectedCardHolder->shop?->is_active === false && $selectedCardHolder->is_active => 'Recovery handling, profile writes, merge handling, and recent-activity sourcing should stay preview-only until paused-branch holder parity is verified.',
            $selectedCardHolder->is_active => 'Profile writes, merge handling, and recent-activity sourcing should stay preview-only until holder parity is verified.',
            default => 'Reactivation handling, profile writes, and recent-activity sourcing should stay preview-only until holder parity is verified.',
        };
    }

    private function cardholdersLifecycleFreshnessLabel(CardHolder $selectedCardHolder): string
    {
        return $this->lifecycleFreshnessLabel($selectedCardHolder);
    }

    private function cardholdersLifecycleFreshnessDescription(CardHolder $selectedCardHolder): string
    {
        return $this->lifecycleFreshnessDescription(
            $selectedCardHolder,
            'This holder does not expose complete Laravel timestamps yet, so lifecycle freshness should stay in review-only posture.',
            'This holder was created in Laravel on %s and has not been updated since, so operators are still reviewing the first saved profile shell.',
            'This holder was first created in Laravel on %s and last updated on %s, so operators are reviewing a profile shell that has already changed after initial setup.',
        );
    }

    private function cardholdersLastSavedLabel(CardHolder $selectedCardHolder): string
    {
        return $this->lastSavedLabel($selectedCardHolder);
    }

    private function cardholdersLinkageSignal(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            $selectedCardHolder->shop !== null && $selectedCardHolder->cards_count > 0 => 'branch-linked profile with visible cards',
            $selectedCardHolder->shop !== null => 'branch-linked profile, card linkage pending',
            $selectedCardHolder->cards_count > 0 => 'card-linked profile, branch visibility pending',
            default => 'branch and card linkage pending',
        };
    }

    private function cardholdersShopActivitySignal(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            $selectedCardHolder->shop === null => 'Branch assignment is still missing, so branch-aware lookup parity remains incomplete.',
            (bool) $selectedCardHolder->shop->is_active => 'Holder is anchored to an active branch for live lookup review.',
            default => 'Holder is anchored to a paused branch, so branch-recovery context should stay visible during lookup review.',
        };
    }

    private function cardholdersActivityHandoffSignal(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            (bool) $selectedCardHolder->shop?->is_active === false && $selectedCardHolder->cards_count > 0 => 'Paused-branch holder already carries linked-card evidence, so branch-recovery context should stay attached to the activity handoff.',
            (bool) $selectedCardHolder->shop?->is_active === false => 'Paused-branch holder should carry branch-recovery context forward until lookup and reactivation parity are explicit.',
            ! $selectedCardHolder->is_active && $selectedCardHolder->cards_count > 0 => 'Dormant holder already carries linked-card evidence for a useful lifecycle handoff review.',
            ! $selectedCardHolder->is_active => 'Dormant holder should stay in handoff-only posture until reactivation parity is explicit.',
            $selectedCardHolder->cards_count > 0 => 'Active holder already carries linked-card context for a useful activity handoff review.',
            default => 'Active holder exists, but linked-card activity context is still thin for handoff review.',
        };
    }

    private function cardholdersActivityTimelineHandoffDescription(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            (bool) $selectedCardHolder->shop?->is_active === false && $selectedCardHolder->cards_count > 0 => 'Operators should carry paused-branch context, linked-card evidence, and holder status together in the live workspace before trusting any reactivation or merge follow-up.',
            (bool) $selectedCardHolder->shop?->is_active === false => 'Operators should carry paused-branch context, holder status, and card-linkage gaps in the live workspace before trusting any reactivation or merge follow-up.',
            ! $selectedCardHolder->is_active && $selectedCardHolder->cards_count > 0 => 'Operators should carry inactive status, linked-card evidence, and branch context in the live workspace before trusting any reactivation or merge follow-up.',
            ! $selectedCardHolder->is_active => 'Operators should carry inactive status, branch context, and card-linkage gaps in the live workspace before trusting any reactivation or merge follow-up.',
            $selectedCardHolder->cards_count > 0 => 'Operators should carry active status, linked-card evidence, and branch context in the live workspace before trusting any lifecycle-change or merge follow-up.',
            default => 'Operators should carry active status, branch context, and card-linkage gaps in the live workspace before trusting any lifecycle-change or merge follow-up.',
        };
    }

    private function cardholdersSelectedHolderDependencyStatus(CardHolder $selectedCardHolder): array
    {
        return [
            ['label' => 'Selected holder', 'value' => $selectedCardHolder->full_name],
            ['label' => 'Lookup posture', 'value' => 'Selected-holder review is running in Laravel-backed read mode only'],
            ['label' => 'Holder status signal', 'value' => $selectedCardHolder->is_active
                ? 'Active holder is already visible for live profile parity review.'
                : 'Inactive holder remains safer for reactivation-flow-parity review before any reactivation-flow discussion.'],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardholdersLifecycleFreshnessLabel($selectedCardHolder)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardholdersLastSavedLabel($selectedCardHolder)],
            ['label' => 'Review note', 'value' => $selectedCardHolder->review_note ?: 'No review note saved yet'],
            ['label' => 'Linkage signal', 'value' => $this->cardholdersLinkageSignal($selectedCardHolder)],
            ['label' => 'Shop activity signal', 'value' => $this->cardholdersShopActivitySignal($selectedCardHolder)],
            ['label' => 'Activity handoff signal', 'value' => $this->cardholdersActivityHandoffSignal($selectedCardHolder)],
            ['label' => 'Status posture', 'value' => $this->cardholdersStatusPosture($selectedCardHolder)],
            ['label' => 'Card linkage posture', 'value' => $selectedCardHolder->cards_count > 0
                ? 'Linked cards are visible in Laravel, but card-to-holder lifecycle changes should stay parity-first until activity sourcing is verified.'
                : 'No linked cards exist yet, which keeps this holder safer for card-link-parity review before card-link flows are enabled.'],
            ['label' => 'Activity posture', 'value' => $this->cardholdersActivityPosture($selectedCardHolder)],
            ['label' => 'Remaining backend gap', 'value' => $this->cardholdersBackendGap($selectedCardHolder)],
        ];
    }

    private function cardholdersStatusPosture(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            (bool) $selectedCardHolder->shop?->is_active === false && $selectedCardHolder->is_active => 'This holder is visible in a paused branch, so lifecycle changes should stay blocked until branch-recovery and lookup parity are verified.',
            $selectedCardHolder->is_active => 'This active holder is visible for review now, but lifecycle changes should stay blocked until search and profile parity are verified.',
            default => 'This inactive holder should stay review-only until reactivation and duplicate-profile rules are verified.',
        };
    }

    private function cardholdersActivityPosture(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            (bool) $selectedCardHolder->shop?->is_active === false => 'Recent activity remains blocked until a stable Laravel event source preserves paused-branch lookup and recovery parity.',
            default => 'Recent activity remains blocked until a stable Laravel event source exists for holder lookup parity.',
        };
    }

    private function shopsSelectedShopSummary(Shop $selectedShop): array
    {
        return [
            ['label' => 'Selected shop', 'value' => $selectedShop->name],
            ['label' => 'Review mode', 'value' => $selectedShop->is_active
                ? 'Live branch review, this Laravel shop already carries operational visibility and should stay parity-first.'
                : 'Paused-branch review, this shop remains safer for parity checks before operators treat it as fully reopened.'],
            ['label' => 'Operational readiness', 'value' => $this->shopsOperationalReadiness($selectedShop)],
            ['label' => 'Lifecycle freshness', 'value' => $this->shopsLifecycleFreshnessLabel($selectedShop)],
            ['label' => 'Last saved in Laravel', 'value' => $this->shopsLastSavedLabel($selectedShop)],
            ['label' => 'Review note', 'value' => $selectedShop->review_note ?: 'No review note saved yet'],
            ['label' => 'Code', 'value' => $selectedShop->code],
            ['label' => 'Coverage signal', 'value' => $this->shopsCoverageSignal($selectedShop)],
            ['label' => 'Shop status signal', 'value' => $this->shopsStatusSignal($selectedShop)],
            ['label' => 'Branch focus', 'value' => $this->shopsBranchFocus($selectedShop)],
            ['label' => 'Branch posture', 'value' => $this->shopsBranchPosture($selectedShop)],
            ['label' => 'Evidence priority', 'value' => $this->shopsEvidencePriority($selectedShop)],
            ['label' => 'Scope handoff signal', 'value' => $this->shopsScopeHandoffSignal($selectedShop)],
            ['label' => 'Backend gap', 'value' => $this->shopsBackendGap($selectedShop)],
            ['label' => 'Assigned manager', 'value' => $selectedShop->users->first()?->name ?? 'Unassigned'],
            ['label' => 'Manager guidance', 'value' => match (true) {
                ! $selectedShop->is_active && $selectedShop->users_count > 0 => 'Keep current paused-branch manager ownership visible during review, because Galaxy recovery workflows depended on clear branch responsibility.',
                ! $selectedShop->is_active => 'No manager is assigned yet, so recovery ownership expectations should stay parity-first until paused-branch ownership-assignment parity is verified.',
                $selectedShop->users_count > 0 => 'Keep current manager ownership visible during review, because legacy Galaxy branch administration depended on clear branch responsibility.',
                default => 'No manager is assigned yet, so ownership expectations should stay parity-first until ownership-assignment parity is verified.',
            }],
            ['label' => 'Cardholders', 'value' => (string) $selectedShop->card_holders_count],
            ['label' => 'Cards', 'value' => (string) $selectedShop->cards_count],
            ['label' => 'Laravel status', 'value' => $selectedShop->is_active ? 'active' : 'paused'],
            [
                'label' => 'Branch guidance',
                'value' => $selectedShop->is_active
                    ? 'This branch is already active in Laravel, so scope and manager changes should stay parity-first until branch ownership rules are verified.'
                    : 'This branch is still paused, so recovery, ownership, and scope review should stay parity-first before operators treat it as fully live.',
            ],
        ];
    }

    private function shopsOperationalReadiness(Shop $selectedShop): string
    {
        return match (true) {
            ! $selectedShop->is_active => 'paused branch, recovery review only',
            $selectedShop->users_count > 0 && $selectedShop->card_holders_count > 0 && $selectedShop->cards_count > 0 => 'active branch, operator-visible coverage live',
            $selectedShop->users_count > 0 => 'active branch, manager assigned and build-out pending',
            default => 'active branch shell, ownership still forming',
        };
    }

    private function shopsBranchFocus(Shop $selectedShop): string
    {
        return $selectedShop->is_active
            ? 'Start with manager ownership, holder coverage, and card coverage before discussing any later reassignment or scope-mutation flow.'
            : 'Start with paused status, recovery ownership gaps, and branch coverage before discussing any later reopening, reassignment, or scope-recovery flow.';
    }

    private function shopsBranchPosture(Shop $selectedShop): string
    {
        return match (true) {
            ! $selectedShop->is_active => 'Keep paused-branch review in the live workspace first, then leave reopening, reassignment, and scope-mutation flows gated until recovery parity is proven.',
            $selectedShop->users_count > 0 && $selectedShop->card_holders_count > 0 && $selectedShop->cards_count > 0 => 'Keep branch review in the live workspace first, then leave reassignment and scope-mutation flows gated until full branch parity is proven.',
            $selectedShop->users_count > 0 => 'Keep manager-owned branch review in the live workspace first, then leave coverage build-out, reassignment, and scope-mutation flows gated until parity is proven.',
            default => 'Keep branch coverage review in the live workspace first, then leave ownership assignment, reassignment, and scope-mutation flows gated until parity is proven.',
        };
    }

    private function shopsEvidencePriority(Shop $selectedShop): string
    {
        return match (true) {
            ! $selectedShop->is_active => 'Keep paused status, recovery ownership gaps, and any visible holder or card coverage together before trusting any reopening, reassignment, or scope-recovery discussion.',
            $selectedShop->users_count > 0 && $selectedShop->card_holders_count > 0 && $selectedShop->cards_count > 0 => 'Keep manager ownership, holder coverage, and card coverage together before trusting any later reassignment or branch-scope mutation discussion.',
            $selectedShop->users_count > 0 => 'Keep manager ownership, branch readiness gaps, and missing holder or card coverage together before trusting any rollout-flow discussion.',
            default => 'Keep holder coverage, card coverage, and ownership gaps together before trusting any later branch-scope mutation discussion.',
        };
    }

    private function shopsBackendGap(Shop $selectedShop): string
    {
        return match (true) {
            ! $selectedShop->is_active => 'Branch recovery writes, manager reassignment, ownership repair, and shop-scope mutation flows should stay preview-only until paused-branch parity is verified.',
            $selectedShop->users_count > 0 && $selectedShop->card_holders_count > 0 && $selectedShop->cards_count > 0 => 'Branch writes, manager reassignment, and shop-scope mutation flows should stay preview-only until branch parity is verified.',
            $selectedShop->users_count > 0 => 'Coverage backfill writes, manager reassignment, and shop-scope mutation flows should stay preview-only until manager-led branch parity is verified.',
            default => 'Ownership assignment, branch writes, and shop-scope mutation flows should stay preview-only until branch coverage parity is verified.',
        };
    }

    private function shopsLifecycleFreshnessLabel(Shop $selectedShop): string
    {
        return $this->lifecycleFreshnessLabel($selectedShop);
    }

    private function shopsLifecycleFreshnessDescription(Shop $selectedShop): string
    {
        return $this->lifecycleFreshnessDescription(
            $selectedShop,
            'This branch does not expose complete Laravel timestamps yet, so lifecycle freshness should stay in review-only posture.',
            'This branch was created in Laravel on %s and has not been updated since, so operators are still reviewing the first saved branch shell.',
            'This branch was first created in Laravel on %s and last updated on %s, so operators are reviewing a branch shell that has already changed after initial setup.',
        );
    }

    private function shopsLastSavedLabel(Shop $selectedShop): string
    {
        return $this->lastSavedLabel($selectedShop);
    }

    private function shopsCoverageSignal(Shop $selectedShop): string
    {
        return match (true) {
            $selectedShop->users_count > 0 && $selectedShop->card_holders_count > 0 && $selectedShop->cards_count > 0 => 'manager, holder, and card coverage visible',
            $selectedShop->users_count > 0 && ($selectedShop->card_holders_count > 0 || $selectedShop->cards_count > 0) => 'manager coverage visible, branch records building out',
            $selectedShop->card_holders_count > 0 || $selectedShop->cards_count > 0 => 'branch records visible, manager coverage pending',
            $selectedShop->users_count > 0 => 'manager coverage visible, branch records pending',
            default => 'manager and branch coverage pending',
        };
    }

    private function shopsStatusSignal(Shop $selectedShop): string
    {
        return match (true) {
            ! $selectedShop->is_active => 'Paused branch remains safer for reopening-parity review before any reopening-flow discussion.',
            $selectedShop->users_count > 0 && $selectedShop->card_holders_count > 0 && $selectedShop->cards_count > 0 => 'Active branch is already visible with manager and customer coverage for branch coverage parity review.',
            $selectedShop->users_count > 0 => 'Active branch is already visible with manager ownership for rollout review.',
            $selectedShop->card_holders_count > 0 || $selectedShop->cards_count > 0 => 'Active branch is already visible with customer coverage while manager ownership is still pending.',
            default => 'Active branch shell is visible, but manager and customer coverage are still pending.',
        };
    }

    private function shopsScopeHandoffSignal(Shop $selectedShop): string
    {
        return match (true) {
            ! $selectedShop->is_active => 'Paused branch should stay in recovery handoff-only posture until ownership and scope approval are explicit.',
            $selectedShop->users_count > 0 && $selectedShop->card_holders_count > 0 && $selectedShop->cards_count > 0 => 'Branch already shows enough ownership and customer coverage for a useful scope handoff review.',
            $selectedShop->users_count > 0 => 'Manager ownership is visible, but customer coverage still needs to catch up before full scope handoff review.',
            $selectedShop->card_holders_count > 0 || $selectedShop->cards_count > 0 => 'Customer coverage is visible, but ownership handoff is still incomplete for branch-scope review.',
            default => 'Branch shell exists, but ownership and customer handoff context are still thin.',
        };
    }

    private function shopsScopeTimelineHandoffDescription(Shop $selectedShop): string
    {
        return match (true) {
            ! $selectedShop->is_active => 'Operators should carry paused status, recovery ownership gaps, branch coverage, and scope approval context in the live workspace before trusting any recovery or reassignment follow-up.',
            $selectedShop->users_count > 0 && $selectedShop->card_holders_count > 0 && $selectedShop->cards_count > 0 => 'Operators should carry manager ownership, holder coverage, and card coverage in the live workspace before trusting any scope-mutation or reassignment follow-up.',
            $selectedShop->users_count > 0 => 'Operators should carry manager ownership, branch readiness gaps, and missing customer coverage in the live workspace before trusting any scope-mutation or reassignment follow-up.',
            $selectedShop->card_holders_count > 0 || $selectedShop->cards_count > 0 => 'Operators should carry customer coverage, ownership gaps, and branch readiness in the live workspace before trusting any scope-mutation or reassignment follow-up.',
            default => 'Operators should carry branch readiness, ownership gaps, and missing customer coverage in the live workspace before trusting any scope-mutation or recovery follow-up.',
        };
    }

    private function lifecycleFreshnessLabel(Model $model): string
    {
        if ($model->updated_at === null || $model->created_at === null) {
            return 'timestamp visibility pending';
        }

        return $model->updated_at->equalTo($model->created_at)
            ? 'newly created in Laravel review'
            : 'updated after initial Laravel creation';
    }

    private function lifecycleFreshnessDescription(Model $model, string $pendingDescription, string $createdDescription, string $updatedDescription): string
    {
        if ($model->updated_at === null || $model->created_at === null) {
            return $pendingDescription;
        }

        if ($model->updated_at->equalTo($model->created_at)) {
            return sprintf($createdDescription, $model->created_at->format('Y-m-d H:i:s T'));
        }

        return sprintf(
            $updatedDescription,
            $model->created_at->format('Y-m-d H:i:s T'),
            $model->updated_at->format('Y-m-d H:i:s T'),
        );
    }

    private function lastSavedLabel(Model $model, string $format = 'Y-m-d H:i:s T', string $fallback = 'Timestamp pending'): string
    {
        return $model->updated_at?->format($format) ?? $fallback;
    }

    private function shopsSelectedShopDependencyStatus(Shop $selectedShop): array
    {
        return [
            ['label' => 'Selected shop', 'value' => $selectedShop->name],
            ['label' => 'Branch posture', 'value' => 'Selected-shop review is running in Laravel-backed read mode only'],
            ['label' => 'Lifecycle freshness', 'value' => $this->shopsLifecycleFreshnessLabel($selectedShop)],
            ['label' => 'Last saved in Laravel', 'value' => $this->shopsLastSavedLabel($selectedShop)],
            ['label' => 'Review note', 'value' => $selectedShop->review_note ?: 'No review note saved yet'],
            ['label' => 'Coverage signal', 'value' => $this->shopsCoverageSignal($selectedShop)],
            ['label' => 'Shop status signal', 'value' => $this->shopsStatusSignal($selectedShop)],
            ['label' => 'Scope handoff signal', 'value' => $this->shopsScopeHandoffSignal($selectedShop)],
            ['label' => 'Status posture', 'value' => $selectedShop->is_active
                ? 'This active branch is visible for review now, but manager and scope changes should stay blocked until legacy ownership rules are verified.'
                : 'This paused branch should stay review-only until recovery, ownership, and scope parity are verified.'],
            ['label' => 'Manager posture', 'value' => match (true) {
                ! $selectedShop->is_active && $selectedShop->users_count > 0 => 'Assigned managers are visible in this paused branch, but reassignment and recovery follow-up should stay blocked until ownership parity is confirmed.',
                ! $selectedShop->is_active => 'No manager is assigned yet, which keeps this paused branch safer for recovery and ownership-flow parity review before ownership flows are enabled.',
                $selectedShop->users_count > 0 => 'Assigned managers are visible in Laravel, but reassignment should stay blocked until branch ownership parity is confirmed.',
                default => 'No manager is assigned yet, which keeps this branch safer for ownership-flow-parity review before ownership flows are enabled.',
            }],
            ['label' => 'Coverage posture', 'value' => $selectedShop->is_active
                ? sprintf('This branch currently exposes %d cardholders and %d cards for read-only Laravel review.', $selectedShop->card_holders_count, $selectedShop->cards_count)
                : sprintf('This paused branch currently exposes %d cardholders and %d cards for read-only Laravel recovery review.', $selectedShop->card_holders_count, $selectedShop->cards_count)],
            ['label' => 'Remaining backend gap', 'value' => $this->shopsBackendGap($selectedShop)],
        ];
    }

    private function prependLatestBackendWriteTimelineItem(array $page): array
    {
        $status = session('status');

        if (! is_string($status)) {
            return $page;
        }

        $timeline = is_array($page['activityTimeline'] ?? null) ? $page['activityTimeline'] : [];
        array_unshift($timeline, [
            'title' => 'Latest backend write result',
            'time' => 'Current request',
            'description' => $status,
        ]);
        $page['activityTimeline'] = $timeline;

        return $page;
    }

    private function appendLatestBackendWriteDependencyStatus(array $page): array
    {
        $status = session('status');

        if (! is_string($status)) {
            return $page;
        }

        $dependencyStatus = is_array($page['dependencyStatus'] ?? null) ? $page['dependencyStatus'] : [];
        $dependencyStatus[] = [
            'label' => 'Latest flow result',
            'value' => $status,
        ];
        $page['dependencyStatus'] = $dependencyStatus;

        return $page;
    }

    private function linkedTableCell(string $label, string $routeName, array $parameters): array
    {
        return [
            'label' => $label,
            'href' => route($routeName, $parameters, absolute: false),
        ];
    }

    private function adminUser(): ?User
    {
        $user = request()->user();

        return $user instanceof User ? $user : null;
    }

    private function filterShopScopedRecords(iterable $records, callable $shopResolver)
    {
        $adminUser = $this->adminUser();
        $records = collect($records);

        if (! $adminUser instanceof User) {
            return $records;
        }

        return $records
            ->filter(fn (mixed $record): bool => ! $this->cannotAccessRecordShop($adminUser, $shopResolver($record)))
            ->values();
    }

    private function cannotAccessRecordShop(?User $user, ?Shop $shop): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        return ! $user->canAccessShop($shop);
    }

    private function selectedRecordId(string $queryKey): int
    {
        $selectedId = request()->integer($queryKey);

        return $selectedId > 0 ? $selectedId : 0;
    }

    private function resolveLiveFormString(mixed $value, string $resource, array $page): string
    {
        $value = $this->resolveLiveFormConfigValue($value, $resource, $page);

        return is_string($value) ? $value : '';
    }

    private function resolveLiveFormNullableString(mixed $value, string $resource, array $page): ?string
    {
        $value = $this->resolveLiveFormConfigValue($value, $resource, $page);

        return is_string($value) ? $value : null;
    }

    private function resolveLiveFormConfigValue(mixed $value, string $resource, array $page): mixed
    {
        if (! is_callable($value)) {
            return $value;
        }

        return app()->call($value, [
            'resource' => $resource,
            'page' => $page,
            'liveForm' => $page['liveForm'] ?? [],
        ]);
    }

    private function applyLiveFormValues(mixed $fields, array $values): array
    {
        if (! is_array($fields) || $values === []) {
            return is_array($fields) ? $fields : [];
        }

        return array_map(function (mixed $field) use ($values): mixed {
            if (! is_array($field) || ! is_string($field['name'] ?? null)) {
                return $field;
            }

            if (! array_key_exists($field['name'], $values)) {
                return $field;
            }

            $field['value'] = $values[$field['name']];

            return $field;
        }, $fields);
    }

    private function resourceBlocks(array $defaults): array
    {
        if (! is_array($defaults['resourceBlocks'] ?? null)) {
            return [];
        }

        return array_values(array_filter(
            $defaults['resourceBlocks'],
            fn (mixed $block): bool => is_array($block)
                && is_string($block['key'] ?? null)
                && is_string($block['partial'] ?? null)
                && is_string($block['prop'] ?? null)
        ));
    }

    private function pageRationale(array $defaults): array
    {
        if (! is_array($defaults['pageRationale'] ?? null)) {
            return [];
        }

        return array_values(array_filter(
            $defaults['pageRationale'],
            fn (mixed $item): bool => is_string($item)
        ));
    }

    private function phase(array $defaults): int
    {
        return is_int($defaults['phase'] ?? null)
            ? $defaults['phase']
            : 1;
    }
}
