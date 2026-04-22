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
                    ['label' => 'Accrual posture', 'value' => 'Positive accrual receipts should stay parity-first, because receipt math must match the old Galaxy ledger before any correction flow appears.'],
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
                    ['label' => 'Receipt posture', 'value' => 'Fiscal receipt review should remain read-only until Laravel transaction history is verified against the legacy ledger.'],
                    ['label' => 'Accrual posture', 'value' => 'Positive point outcomes still need live transaction-domain parity before any adjustment path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Transaction tables, receipt reads, and adjustment handlers still remain blocked for this receipt preview.'],
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
                    ['label' => 'Accrual posture', 'value' => 'Zero-accrual receipts should stay highly visible, because they drive the most parity-sensitive troubleshooting in the old Galaxy flow.'],
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
                    ['label' => 'Receipt posture', 'value' => 'Receipt lookup should stay read-only until Laravel transaction history is verified against legacy fiscal search behavior.'],
                    ['label' => 'Accrual posture', 'value' => 'Zero-point outcomes still need rule and receipt parity verification before any adjustment path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Transaction tables, receipt reads, and adjustment handlers still remain blocked for this receipt preview.'],
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
                    ['label' => 'Accrual posture', 'value' => 'North Shop accrual receipts should stay branch-aware, because cross-shop troubleshooting must preserve local receipt context.'],
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
                    ['label' => 'Receipt posture', 'value' => 'Branch receipt lookup should stay read-only until Laravel shop filters and transaction history are verified against the old flow.'],
                    ['label' => 'Accrual posture', 'value' => 'Positive branch accrual outcomes still need live transaction-domain parity before any adjustment path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Transaction tables, receipt reads, and adjustment handlers still remain blocked for this receipt preview.'],
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
                    ['label' => 'Condition posture', 'value' => 'Birthday window logic should stay parity-first, because date-sensitive loyalty rules are easy to drift during migration.'],
                    ['label' => 'Priority posture', 'value' => 'Keep this rule near the top of the preview stack until Laravel priority resolution is verified against the old Galaxy order.'],
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
                    ['label' => 'Scope posture', 'value' => 'All-shop scope should remain stable until Laravel scope handling is verified against legacy loyalty behavior.'],
                    ['label' => 'Priority posture', 'value' => 'Priority resolution remains preview-only until overlapping rule order is validated in Laravel.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Rule persistence, condition editing, and publish flow still remain blocked for this rule preview.'],
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
                    ['label' => 'Condition posture', 'value' => 'Partner-card checks should stay tied to visible card-type parity before any Laravel rule editor opens them up.'],
                    ['label' => 'Priority posture', 'value' => 'This scoped uplift should remain below birthday-wide behavior until legacy overlap order is rechecked.'],
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
                    ['label' => 'Scope posture', 'value' => 'Shop-scoped behavior should stay preview-only until Laravel scope checks are verified against legacy branch rules.'],
                    ['label' => 'Priority posture', 'value' => 'Overlap with broader loyalty rules still needs parity verification before any publish path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Rule persistence, scoped validation, and publish flow still remain blocked for this rule preview.'],
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
                    ['label' => 'Condition posture', 'value' => 'Bar-service exclusions should remain draft-only until legacy exception behavior is rechecked in Laravel.'],
                    ['label' => 'Priority posture', 'value' => 'Keep this blocking rule below confirmed accrual logic until exclusion order is verified.'],
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
                    ['label' => 'Scope posture', 'value' => 'North Shop exclusions should stay draft-only until scoped exception behavior is verified against the legacy system.'],
                    ['label' => 'Priority posture', 'value' => 'Blocking-rule order is still preview-only until exclusion precedence is validated in Laravel.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Rule persistence, exclusion validation, and publish flow still remain blocked for this draft rule preview.'],
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
                'label' => 'Publish rule',
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
                    ['label' => 'Scope posture', 'value' => 'All-shop rewards should stay parity-first, because wide-scope catalog changes affect the most operators and redemptions.'],
                    ['label' => 'Stock posture', 'value' => 'Unlimited stock can stay reviewable, but warehouse sync assumptions should remain explicit until Laravel inventory writes exist.'],
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
                    ['label' => 'Scope posture', 'value' => 'All-shop reward coverage should remain stable until Laravel scope handling is verified against the legacy catalog.'],
                    ['label' => 'Stock posture', 'value' => 'Unlimited-stock assumptions still need backend inventory wiring before operators can trust live publish behavior.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Gift CRUD, stock updates, and redemption persistence still remain blocked for this reward preview.'],
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
                    ['label' => 'Scope posture', 'value' => 'Kiosk-scoped rewards should stay branch-aware, because legacy redemption expectations depended on local availability.'],
                    ['label' => 'Stock posture', 'value' => 'Finite stock should remain review-only until Laravel inventory updates can preserve remaining-quantity parity.'],
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
                    ['label' => 'Scope posture', 'value' => 'Shop-scoped reward behavior should stay preview-only until Laravel scope checks are verified against legacy kiosk rules.'],
                    ['label' => 'Stock posture', 'value' => 'Finite-stock handling still needs backend inventory wiring before a publish path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Gift CRUD, stock updates, and redemption persistence still remain blocked for this reward preview.'],
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
                    ['label' => 'Scope posture', 'value' => 'Central Shop reward availability should stay parity-first until paused reward behavior matches the legacy catalog.'],
                    ['label' => 'Stock posture', 'value' => 'Zero-stock rewards should remain paused in review mode until Laravel inventory and reopening flows can reproduce the old behavior safely.'],
                    ['label' => 'Format guidance', 'value' => 'Keep paused zero-stock rewards in compact on-screen review first, because operators need scope, stock, and cost visible together before discussing reopening.'],
                    ['label' => 'Redemption guidance', 'value' => 'Treat this paused reward as review-only until stock recovery and redemption parity are backed by Laravel flows.'],
                ],
                'timeline' => [
                    ['title' => 'Premium dessert set selected for paused reward review', 'time' => 'Current request', 'description' => 'This preview now keeps the zero-stock reward in a dedicated Galaxy review context instead of leaving it as a flat table row.'],
                    ['title' => 'Paused reward handoff stays cautious', 'time' => 'Current request', 'description' => 'Operators should hand off zero-stock and reopening assumptions here before any future publish or stock-update flow is allowed.'],
                    ['title' => 'Paused reward handoff keeps stock evidence visible', 'time' => 'Current request', 'description' => 'Scope, zero-stock state, and points cost should stay visible in the workspace before any reopening discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected gift', 'value' => 'Premium dessert set'],
                    ['label' => 'Scope posture', 'value' => 'Paused shop-scoped reward behavior should stay preview-only until Laravel scope and reopening checks are verified.'],
                    ['label' => 'Stock posture', 'value' => 'Zero-stock handling is still preview-only until inventory sync and recovery behavior are validated in Laravel.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Gift CRUD, stock updates, and redemption persistence still remain blocked for this paused reward preview.'],
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
                    ['label' => 'Scope posture', 'value' => 'Paused branch rewards should stay locally reviewable, because reopening decisions still depend on shop-specific redemption habits.'],
                    ['label' => 'Stock posture', 'value' => 'Finite paused stock should remain review-only until Laravel inventory updates and reopening flows can preserve remaining-quantity parity.'],
                    ['label' => 'Format guidance', 'value' => 'Keep paused finite-stock rewards in compact on-screen review first, because operators need scope, stock, and reopening posture visible together before escalating.'],
                    ['label' => 'Redemption guidance', 'value' => 'Treat this paused branch reward as review-only until stock-aware reopening and redemption parity are backed by Laravel flows.'],
                ],
                'timeline' => [
                    ['title' => 'Weekend brunch pass selected for paused branch reward review', 'time' => 'Current request', 'description' => 'This preview now keeps the paused finite-stock reward in a dedicated Galaxy review context instead of leaving it as a flat table row.'],
                    ['title' => 'Paused branch reward handoff stays stock-aware', 'time' => 'Current request', 'description' => 'Operators should hand off Riverside Shop reopening assumptions here before any future publish or stock-update flow is allowed.'],
                    ['title' => 'Paused branch reward keeps finite-stock evidence visible', 'time' => 'Current request', 'description' => 'Scope, remaining stock, and points cost should stay visible in the workspace before any reopening discussion begins.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected gift', 'value' => 'Weekend brunch pass'],
                    ['label' => 'Scope posture', 'value' => 'Paused branch reward behavior should stay preview-only until Laravel scope and reopening checks are verified.'],
                    ['label' => 'Stock posture', 'value' => 'Finite paused stock still needs backend inventory wiring before operators can trust reopening decisions.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Gift CRUD, stock updates, and redemption persistence still remain blocked for this paused branch reward preview.'],
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
                'label' => 'Publish gift',
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
            ['label' => 'Scoped shops', 'value' => (string) $roles->flatMap(fn (Role $role) => $role->users->pluck('shop_id'))->filter()->unique()->count()],
        ];

        $page['actions'] = [
            [
                'label' => 'New role',
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
                'label' => 'Publish role',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->rolesPermissionsCatalogPublishRoleDisabledReason($roles),
            ],
        ];

        $page['table']['rows'] = $roles->map(function (Role $role): array {
            $scope = $role->users->pluck('shop.name')->filter()->unique();
            $permissionPreview = $role->permissions->pluck('name')->take(3)->implode(', ');

            return [
                $this->linkedTableCell($role->name, 'admin.roles-permissions.index', ['role' => $role->id]),
                $scope->isNotEmpty() ? $scope->join(', ') : 'Unscoped in Laravel read slice',
                $permissionPreview !== '' ? $permissionPreview : 'No permissions linked yet',
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
            $page['liveForm']['title'] = 'Edit role in Laravel';
            $page['liveForm']['description'] = 'Update the selected Galaxy role identity through the shared live form while permission bundles and shop scope remain in review-only mode.';
            $page['liveForm']['method'] = 'PATCH';
            $page['liveForm']['actionRoute'] = 'admin.roles-permissions.update';
            $page['liveForm']['actionRouteParameters'] = [
                'role' => $selectedRole,
            ];
            $page['liveForm']['cancelRoute'] = 'admin.roles-permissions.index';
            $page['liveForm']['cancelLabel'] = 'Create new role';
            $page['liveForm']['cancelRouteParameters'] = [];
            $page['liveForm']['submitLabel'] = 'Save role changes';
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
                'label' => 'Create new role',
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
                'label' => 'Publish role',
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

        $page['actions'] = [
            [
                'label' => 'Issue card',
                'tone' => 'primary',
                'disabled' => true,
                'disabledReason' => $this->cardsCatalogIssueCardDisabledReason($cards),
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
        ];

        $page['table']['rows'] = $cards->map(fn (Card $card): array => [
            $this->cannotAccessRecordShop($adminUser, $card->shop)
                ? $card->number
                : $this->linkedTableCell($card->number, 'admin.cards.index', ['card' => $card->id]),
            $card->holder?->full_name ?? 'Unassigned',
            $card->type?->name ?? 'Unknown',
            $card->shop?->name ?? 'Unassigned',
            $card->status,
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

        $page['actions'] = [
            [
                'label' => 'New cardholder',
                'tone' => 'primary',
                'disabled' => true,
                'disabledReason' => $this->cardholdersCatalogNewHolderDisabledReason($cardHolders),
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
            ['label' => 'Linked cards', 'value' => (string) $cardHolders->sum('cards_count')],
        ];

        $page['table']['rows'] = $cardHolders->map(fn (CardHolder $cardHolder): array => [
            $this->cannotAccessRecordShop($adminUser, $cardHolder->shop)
                ? $cardHolder->full_name
                : $this->linkedTableCell($cardHolder->full_name, 'admin.cardholders.index', ['cardholder' => $cardHolder->id]),
            $cardHolder->phone ?? '—',
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
                'label' => 'New shop',
                'tone' => 'primary',
                'disabled' => true,
                'disabledReason' => $this->shopsCatalogNewShopDisabledReason($shops),
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
            ['label' => 'Assigned managers', 'value' => (string) $shops->filter(fn (Shop $shop): bool => $shop->users_count > 0)->count()],
        ];

        $page['table']['rows'] = $shops->map(fn (Shop $shop): array => [
            $this->cannotAccessRecordShop($adminUser, $shop)
                ? $shop->name
                : $this->linkedTableCell($shop->name, 'admin.shops.index', ['shop' => $shop->id]),
            $shop->code,
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
        $cardHolders = CardHolder::query()->withCount('cards')->with(['shop:id,is_active', 'cards:id,card_holder_id,status'])->get();
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
        $roles = Role::query()->withCount(['permissions', 'users'])->with('users.shop:id,is_active')->get();
        $roleCount = $roles->count();
        $activeRoleCount = $roles->where('is_active', true)->count();
        $permissionLinkedRoleCount = $roles->filter(fn (Role $role): bool => $role->is_active && $role->permissions_count > 0)->count();
        $permissionlessActiveRoleCount = $activeRoleCount - $permissionLinkedRoleCount;
        $scopedPermissionLinkedRoleCount = $roles->filter(fn (Role $role): bool => $role->is_active
            && $role->permissions_count > 0
            && $role->users->contains(fn ($user): bool => $user->shop_id !== null))->count();
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
                    ['label' => 'Source signal', 'value' => $cardCount > 0 && $shopCount > 0 ? 'live cards and branch coverage visible' : 'cards or branch coverage still pending'],
                    ['label' => 'Laravel input signal', 'value' => $cardCount > 0 && $shopCount > 0 ? 'card and branch inputs are ready for on-screen review' : 'card or branch inputs still need live Laravel coverage'],
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
                    ['label' => 'Preset posture', 'value' => 'Keep period presets preview-only until shop-level totals and export parity are verified.'],
                    ['label' => 'Export posture', 'value' => 'Treat this source as review-only until file export formatting and delivery are validated.'],
                ],
                'timeline' => [
                    ['title' => 'Cards by shop source selected for Laravel review', 'time' => 'Current request', 'description' => sprintf('This reporting view now reflects %d tracked cards across %d shops from the current Laravel foundation.', $cardCount, $shopCount)],
                    ['title' => 'Shop-level inventory parity stays review-only', 'time' => 'Current request', 'description' => 'Counts are live-backed now, but grouped report shaping and export output should stay parity-first until reporting pipeline checks exist.'],
                    ['title' => 'Branch inventory handoff stays on-screen first', 'time' => 'Current request', 'description' => 'Operators should hand off branch comparison findings in the live workspace before relying on exported files for this source.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected source', 'value' => 'Cards by shop'],
                    ['label' => 'Laravel inputs', 'value' => sprintf('%d cards and %d shops are currently visible to the reporting workspace.', $cardCount, $shopCount)],
                    ['label' => 'Source signal', 'value' => $cardCount > 0 && $shopCount > 0 ? 'live cards and branch coverage visible' : 'cards or branch coverage still pending'],
                    ['label' => 'Laravel input signal', 'value' => $cardCount > 0 && $shopCount > 0 ? 'card and branch inputs are ready for on-screen review' : 'card or branch inputs still need live Laravel coverage'],
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
                    ['label' => 'Draft inventory signal', 'value' => $draftCardCount > 0 && $cardCount > $draftCardCount
                        ? sprintf('%d draft cards are already visible beside %d issued inventory records for parity review', $draftCardCount, $cardCount - $draftCardCount)
                        : 'draft inventory coverage is still pending for parity review'],
                    ['label' => 'Activation signal', 'value' => $activatedCardCount > 0 && $cardCount > $activatedCardCount
                        ? sprintf('%d activated cards are already visible beside %d not-yet-activated inventory records for parity review', $activatedCardCount, $cardCount - $activatedCardCount)
                        : 'activation coverage is still pending for parity review'],
                    ['label' => 'Scope posture', 'value' => 'Branch-level comparison is the first parity target, so cross-shop shaping should stay conservative until legacy report totals are matched.'],
                    ['label' => 'Grouping posture', 'value' => 'Shop grouping should stay read-only until query shaping is verified against legacy report totals.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Preset handling, grouped query shaping, and export generation still remain preview-only for this source.'],
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
                    ['label' => 'Source signal', 'value' => $cardHolderCount > 0 ? 'live holder status coverage visible' : 'holder status coverage pending'],
                    ['label' => 'Laravel input signal', 'value' => $cardHolderCount > 0 ? 'holder status inputs are ready for on-screen review' : 'holder status inputs still need live Laravel coverage'],
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
                    ['label' => 'Holder branch activity signal', 'value' => $activeShopCardHolderCount > 0 && $pausedShopCardHolderCount > 0
                        ? sprintf('%d holder profiles are already visible in active branches beside %d profiles in paused shops for parity review', $activeShopCardHolderCount, $pausedShopCardHolderCount)
                        : 'paused-branch holder coverage is still pending for parity review'],
                    ['label' => 'Scope guidance', 'value' => 'Keep this source focused on active versus inactive holder posture first, because old Galaxy support flows used status review before deeper profile history.' ],
                    ['label' => 'Default period posture', 'value' => 'Use a current-status review first, then stage preset periods until lifecycle and recency parity are verified.'],
                    ['label' => 'Format guidance', 'value' => 'Prefer a compact on-screen table first, because holder-status review usually started as a fast support triage surface, not an export job.' ],
                    ['label' => 'Preset posture', 'value' => 'Keep status-period presets preview-only until holder lifecycle parity is verified.'],
                    ['label' => 'Export posture', 'value' => 'Treat this source as review-only until summary exports and lifecycle report expectations are validated.'],
                ],
                'timeline' => [
                    ['title' => 'Cardholder status source selected for Laravel review', 'time' => 'Current request', 'description' => sprintf('This reporting view now reflects %d tracked cardholders from the current Laravel foundation.', $cardHolderCount)],
                    ['title' => 'Lifecycle reporting parity stays review-only', 'time' => 'Current request', 'description' => 'Source counts are live-backed now, but period presets and export behavior should stay blocked until reporting parity is verified.'],
                    ['title' => 'Support handoff should keep holder posture visible', 'time' => 'Current request', 'description' => 'Operators should pass along active versus inactive holder findings in the live review flow before expecting export-driven follow-up.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected source', 'value' => 'Cardholder status overview'],
                    ['label' => 'Laravel inputs', 'value' => sprintf('%d cardholders are currently visible to the reporting workspace.', $cardHolderCount)],
                    ['label' => 'Source signal', 'value' => $cardHolderCount > 0 ? 'live holder status coverage visible' : 'holder status coverage pending'],
                    ['label' => 'Laravel input signal', 'value' => $cardHolderCount > 0 ? 'holder status inputs are ready for on-screen review' : 'holder status inputs still need live Laravel coverage'],
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
                    ['label' => 'Holder branch activity signal', 'value' => $activeShopCardHolderCount > 0 && $pausedShopCardHolderCount > 0
                        ? sprintf('%d holder profiles are already visible in active branches beside %d profiles in paused shops for parity review', $activeShopCardHolderCount, $pausedShopCardHolderCount)
                        : 'paused-branch holder coverage is still pending for parity review'],
                    ['label' => 'Scope posture', 'value' => 'Status-first review should stay ahead of deeper segmentation until lifecycle parity and operator lookup habits are matched.'],
                    ['label' => 'Lifecycle posture', 'value' => 'Status aggregation should stay read-only until holder lifecycle and activity parity are verified.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Preset handling, report shaping, and export generation still remain preview-only for this source.'],
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
                    ['label' => 'Source signal', 'value' => $roleCount > 0 ? 'live role coverage visible' : 'role coverage pending'],
                    ['label' => 'Laravel input signal', 'value' => $roleCount > 0 ? 'role inputs are ready for on-screen review' : 'role inputs still need live Laravel coverage'],
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
                    ['label' => 'Scoped bundle signal', 'value' => $scopedPermissionLinkedRoleCount > 0
                        ? sprintf('%d permission-linked roles already carry shop-linked access scope for parity review', $scopedPermissionLinkedRoleCount)
                        : 'shop-linked permission-bundle coverage is still pending'],
                    ['label' => 'Role state signal', 'value' => $activeRoleCount > 0 && $roleCount > $activeRoleCount
                        ? sprintf('%d active roles are already visible beside %d draft access roles for parity review', $activeRoleCount, $roleCount - $activeRoleCount)
                        : 'draft access-role coverage is still pending'],
                    ['label' => 'Permission bundle signal', 'value' => $permissionLinkedRoleCount > 0 && $permissionlessActiveRoleCount > 0
                        ? sprintf('%d permission-linked roles are already visible beside %d unbundled active roles for parity review', $permissionLinkedRoleCount, $permissionlessActiveRoleCount)
                        : 'unbundled active-role coverage is still pending'],
                    ['label' => 'Scope guidance', 'value' => 'Keep this source centered on role coverage and scope visibility first, because old Galaxy access checks were driven by who could see which branch context.' ],
                    ['label' => 'Default period posture', 'value' => 'Use current access coverage review first, then stage preset periods only after scope and assignment parity are verified.'],
                    ['label' => 'Format guidance', 'value' => 'Prefer table-first review here, because access coverage checks need visible role and scope context before any export workflow is trusted.' ],
                    ['label' => 'Preset posture', 'value' => 'Keep access-report presets preview-only until role and scope parity are verified.'],
                    ['label' => 'Export posture', 'value' => 'Treat this source as review-only until access export expectations and file delivery are validated.'],
                ],
                'timeline' => [
                    ['title' => 'Role access source selected for Laravel review', 'time' => 'Current request', 'description' => sprintf('This reporting view now reflects %d tracked roles from the current Laravel foundation.', $roleCount)],
                    ['title' => 'Access reporting parity stays review-only', 'time' => 'Current request', 'description' => 'Source counts are live-backed now, but grouped role exports and access analytics should stay blocked until reporting parity is verified.'],
                    ['title' => 'Access-review handoff should stay visible in the workspace', 'time' => 'Current request', 'description' => 'Operators should hand off role-coverage findings in the live review context before trusting export files for access decisions.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected source', 'value' => 'Role access coverage'],
                    ['label' => 'Laravel inputs', 'value' => sprintf('%d roles are currently visible to the reporting workspace.', $roleCount)],
                    ['label' => 'Source signal', 'value' => $roleCount > 0 ? 'live role coverage visible' : 'role coverage pending'],
                    ['label' => 'Laravel input signal', 'value' => $roleCount > 0 ? 'role inputs are ready for on-screen review' : 'role inputs still need live Laravel coverage'],
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
                    ['label' => 'Scoped bundle signal', 'value' => $scopedPermissionLinkedRoleCount > 0
                        ? sprintf('%d permission-linked roles already carry shop-linked access scope for parity review', $scopedPermissionLinkedRoleCount)
                        : 'shop-linked permission-bundle coverage is still pending'],
                    ['label' => 'Role state signal', 'value' => $activeRoleCount > 0 && $roleCount > $activeRoleCount
                        ? sprintf('%d active roles are already visible beside %d draft access roles for parity review', $activeRoleCount, $roleCount - $activeRoleCount)
                        : 'draft access-role coverage is still pending'],
                    ['label' => 'Permission bundle signal', 'value' => $permissionLinkedRoleCount > 0 && $permissionlessActiveRoleCount > 0
                        ? sprintf('%d permission-linked roles are already visible beside %d unbundled active roles for parity review', $permissionLinkedRoleCount, $permissionlessActiveRoleCount)
                        : 'unbundled active-role coverage is still pending'],
                    ['label' => 'Scope posture', 'value' => 'Scope visibility should stay read-only until access-report parity and branch-assignment shaping are verified.'],
                    ['label' => 'Access posture', 'value' => 'Role coverage should stay read-only until access-report parity and scope shaping are verified.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Preset handling, report shaping, and export generation still remain preview-only for this source.'],
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
                    'label' => $cardType->is_active ? 'Move to draft' : 'Activate type',
                    'href' => route('admin.card-types.toggle-status', $cardType, absolute: false),
                    'method' => 'PATCH',
                ],
            ])->all();
        }

        $page['actions'] = [
            [
                'label' => 'New type',
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
                'label' => 'Publish type',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardTypesCatalogPublishTypeDisabledReason($cardTypes),
            ],
        ];

        if ($latestCardType !== null) {
            $page = $this->appendPageAction($page, [
                'label' => 'Edit latest saved type',
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

        $page['selectedRecordSummary'] = [
            ['label' => 'Selected tier', 'value' => $selectedCardType->name],
            ['label' => 'Slug', 'value' => $selectedCardType->slug],
            ['label' => 'Points rate', 'value' => number_format((float) $selectedCardType->points_rate, 2).'x'],
            ['label' => 'Laravel status', 'value' => $selectedCardType->is_active ? 'active' : 'draft'],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardTypesLifecycleFreshnessLabel($selectedCardType)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardTypesLastSavedLabel($selectedCardType)],
            ['label' => 'Review note', 'value' => $selectedCardType->review_note ?: 'No review note saved yet'],
            ['label' => 'Activation note', 'value' => $selectedCardType->activation_note ?: 'No activation note saved yet'],
            ['label' => 'Rollout note', 'value' => $selectedCardType->rollout_note ?: 'No rollout note saved yet'],
            ['label' => 'Coverage signal', 'value' => $this->cardTypesCoverageSignal($selectedCardType)],
            [
                'label' => 'Status guidance',
                'value' => $selectedCardType->is_active
                    ? 'This tier is live in the current Laravel foundation, so operators should move it back to draft before parity-sensitive rule changes.'
                    : 'This tier is still in draft, which keeps it safe for parity checks before operators treat it as live loyalty behavior.',
            ],
            [
                'label' => 'Rule-import blocker',
                'value' => $selectedCardType->is_active
                    ? 'Rule import should stay blocked for this live tier until legacy accrual parity is verified against the active behavior.'
                    : 'Rule import is still blocked, but draft state keeps this tier safe for parity-first catalog and accrual checks.',
            ],
            [
                'label' => 'Publish guidance',
                'value' => $selectedCardType->is_active
                    ? 'Treat this tier as already live in Laravel, so publish-like changes should wait for rule parity and operator confirmation.'
                    : 'Keep this tier in draft until rule import expectations and old Galaxy behavior are mapped clearly enough to publish safely.',
            ],
            [
                'label' => 'Readiness signal',
                'value' => $selectedCardType->is_active
                    ? 'Partially ready: the tier is live in Laravel, but parity-sensitive follow-up actions should stay gated.'
                    : 'Not ready to publish: draft mode is still the holding state for parity validation and rule-import review.',
            ],
        ];

        $page = $this->appendCardTypeLatestFlowFeedback($page);

        $page['actions'] = [
            [
                'label' => 'Create new type',
                'tone' => 'primary',
                'href' => route('admin.card-types.index', absolute: false).'#live-form',
            ],
            [
                'label' => $selectedCardType->is_active ? 'Move to draft' : 'Activate type',
                'tone' => 'secondary',
                'href' => route('admin.card-types.toggle-status', $selectedCardType, absolute: false),
                'method' => 'PATCH',
            ],
            [
                'label' => sprintf('Editing: %s', $selectedCardType->name),
                'tone' => 'secondary',
            ],
            [
                'label' => 'Import rules',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardTypesImportRulesDisabledReason($selectedCardType),
            ],
            [
                'label' => 'Publish type',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $this->cardTypesPublishTypeDisabledReason($selectedCardType),
            ],
        ];

        $page['activityTimeline'] = [
            [
                'title' => sprintf('%s selected for Laravel edit flow', $selectedCardType->name),
                'time' => 'Current request',
                'description' => 'The shared card-type form is now loading this saved tier directly from Laravel data instead of preview-only defaults.',
            ],
            [
                'title' => sprintf('%s status reflected from model state', $selectedCardType->name),
                'time' => 'Current request',
                'description' => sprintf('This tier is currently marked as %s in Laravel and the management context card now mirrors that state.', $selectedCardType->is_active ? 'active' : 'draft'),
            ],
            [
                'title' => sprintf('%s lifecycle freshness reflected from model state', $selectedCardType->name),
                'time' => 'Current request',
                'description' => $this->cardTypesLifecycleFreshnessDescription($selectedCardType),
            ],
            [
                'title' => sprintf('%s last saved timestamp reflected from model state', $selectedCardType->name),
                'time' => 'Current request',
                'description' => sprintf('The latest saved Laravel timestamp for this tier is %s, giving operators a concrete checkpoint for the current catalog shell.', $this->cardTypesLastSavedLabel($selectedCardType)),
            ],
            [
                'title' => sprintf('%s review note reflected from model state', $selectedCardType->name),
                'time' => 'Current request',
                'description' => $selectedCardType->review_note !== null && trim($selectedCardType->review_note) !== ''
                    ? sprintf('The current Laravel tier review note says: %s', $selectedCardType->review_note)
                    : 'No Laravel tier review note is saved yet, so parity-sensitive tier context still depends on the surrounding workspace cues.',
            ],
            [
                'title' => sprintf('%s activation note reflected from model state', $selectedCardType->name),
                'time' => 'Current request',
                'description' => $selectedCardType->activation_note !== null && trim($selectedCardType->activation_note) !== ''
                    ? sprintf('The current Laravel activation note says: %s', $selectedCardType->activation_note)
                    : 'No Laravel activation note is saved yet, so activation handoff guidance still depends on the surrounding workspace cues.',
            ],
            [
                'title' => sprintf('%s rollout note reflected from model state', $selectedCardType->name),
                'time' => 'Current request',
                'description' => $selectedCardType->rollout_note !== null && trim($selectedCardType->rollout_note) !== ''
                    ? sprintf('The current Laravel rollout note says: %s', $selectedCardType->rollout_note)
                    : 'No Laravel rollout note is saved yet, so rollout guidance still depends on the surrounding workspace cues.',
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
            ['label' => 'Rollout note', 'value' => $selectedCardType->rollout_note ?: 'No rollout note saved yet'],
            ['label' => 'Coverage signal', 'value' => $this->cardTypesCoverageSignal($selectedCardType)],
            ['label' => 'Current status posture', 'value' => $selectedCardType->is_active ? 'Active tiers should stay stable unless parity checks are complete' : 'Draft tiers are the safe place for parity-first validation and copy changes'],
            ['label' => 'Rule-import posture', 'value' => $selectedCardType->is_active ? 'Keep imports blocked until active-tier accrual parity is verified' : 'Imports can be reviewed in draft mode, but they are still not safe to enable yet'],
            ['label' => 'Publish posture', 'value' => $selectedCardType->is_active ? 'Live tiers need parity confirmation before further publish-style changes' : 'Draft tiers should stay unpublished until legacy behavior is mapped more explicitly'],
            ['label' => 'Action gating', 'value' => $selectedCardType->is_active ? 'Allow small state corrections only, keep publish-like and import actions gated' : 'Allow draft-safe edits and validation only, keep live-facing actions gated'],
            ['label' => 'Remaining backend gap', 'value' => 'Publish logic and rule-import parity still remain preview-only for this tier'],
        ];

        $page = $this->appendLatestBackendWriteDependencyStatus($page);

        $page['liveForm']['title'] = 'Edit card type in Laravel';
        $page['liveForm']['description'] = 'Update the selected Galaxy tier through the shared live form without leaving the card-types workspace.';
        $page['liveForm']['method'] = 'PATCH';
        $page['liveForm']['actionRoute'] = 'admin.card-types.update';
        $page['liveForm']['actionRouteParameters'] = [
            'cardType' => $selectedCardType,
        ];
        $page['liveForm']['cancelRoute'] = 'admin.card-types.index';
        $page['liveForm']['cancelLabel'] = 'Create new type';
        $page['liveForm']['cancelRouteParameters'] = [];
        $page['liveForm']['submitLabel'] = 'Save card type changes';
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
            $activeCount > 0 => 'Blocked until saved live tiers clear rollout parity against the old Galaxy catalog.',
            default => 'Blocked until a saved draft tier clears rollout parity before any publish-like move.',
        };
    }

    private function cardTypesImportRulesDisabledReason(CardType $selectedCardType): string
    {
        $cardsCount = $selectedCardType->cards_count ?? 0;

        return match (true) {
            $selectedCardType->is_active && $cardsCount > 0 => 'Blocked until live-tier accrual parity is verified against visible card coverage.',
            $selectedCardType->is_active => 'Blocked until this live tier has visible card coverage for accrual parity review.',
            $cardsCount > 0 => 'Blocked until draft rule parity is verified against visible card coverage.',
            default => 'Blocked until draft parity review has visible card coverage to compare against.',
        };
    }

    private function cardTypesPublishTypeDisabledReason(CardType $selectedCardType): string
    {
        $cardsCount = $selectedCardType->cards_count ?? 0;

        return match (true) {
            $selectedCardType->is_active && $cardsCount > 0 => 'Blocked until live-tier rollout parity is verified across visible card coverage.',
            $selectedCardType->is_active => 'Blocked until this live tier has visible card coverage and rollout parity review.',
            $cardsCount > 0 => 'Blocked until this draft tier clears rule and rollout parity review against visible card coverage.',
            default => 'Blocked until this draft tier clears rule and rollout parity review before any publish-like move.',
        };
    }

    private function cardTypesLastSavedLabel(CardType $selectedCardType): string
    {
        return $this->lastSavedLabel($selectedCardType);
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

        return match (true) {
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
        return match ($selectedReceiptPreview['shop'] ?? null) {
            'North Shop' => 'Blocked until branch-aware receipt lookup is backed by Laravel shop filters and transaction reads.',
            default => 'Blocked until receipt lookup is backed by Laravel transaction reads and fiscal-search parity checks.',
        };
    }

    private function checksPointsSelectedReviewGapsDisabledReason(array $selectedReceiptPreview): string
    {
        return match (true) {
            ($selectedReceiptPreview['points'] ?? null) === '0' => 'Blocked until zero-accrual review is backed by Laravel transaction and rule data.',
            ($selectedReceiptPreview['shop'] ?? null) === 'North Shop' => 'Blocked until branch-aware accrual review is backed by Laravel transaction and rule data.',
            default => 'Blocked until accrual-gap review is backed by Laravel transaction and rule data.',
        };
    }

    private function reportsSelectedSourcePresetDisabledReason(array $selectedReportSource): string
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
        return match ($selectedReportSource['key'] ?? null) {
            'cards-by-shop' => 'Blocked until branch-total export snapshots are verified against legacy grouped totals and file delivery.',
            'cardholder-status' => 'Blocked until holder-status export snapshots are verified against lifecycle summaries and file delivery.',
            'role-access' => 'Blocked until role-access export snapshots are verified against scope summaries and file delivery.',
            default => 'Blocked until reporting exports and file delivery are verified against legacy Galaxy output expectations.',
        };
    }

    private function giftsSelectedStockAuditDisabledReason(array $selectedGiftPreview): string
    {
        return match (true) {
            ($selectedGiftPreview['stock'] ?? null) === '0' => 'Blocked until zero-stock recovery checks are backed by Laravel inventory data and reopening parity.',
            ($selectedGiftPreview['stock'] ?? null) !== 'Unlimited' => 'Blocked until finite-stock checks are backed by Laravel inventory data and scoped stock parity.',
            default => 'Blocked until all-shop stock checks are backed by Laravel inventory data.',
        };
    }

    private function giftsSelectedPublishGiftDisabledReason(array $selectedGiftPreview): string
    {
        return match (true) {
            ($selectedGiftPreview['status'] ?? null) === 'paused' => 'Blocked until this paused reward clears CRUD, stock-recovery, and redemption parity beyond the preview shell.',
            ($selectedGiftPreview['scope'] ?? null) !== 'All shops' => 'Blocked until this scoped reward clears CRUD, scope-parity, and redemption checks beyond the preview shell.',
            default => 'Blocked until this all-shop reward clears CRUD and redemption parity beyond the preview shell.',
        };
    }

    private function servicesRulesSelectedReviewPrioritiesDisabledReason(array $selectedRulePreview): string
    {
        return match (true) {
            ($selectedRulePreview['status'] ?? null) === 'draft' => 'Blocked until draft rule priority order is verified against legacy exclusion precedence in Laravel.',
            ($selectedRulePreview['scope'] ?? null) !== 'All shops' => 'Blocked until scoped rule priority order is verified against broader loyalty overlaps in Laravel.',
            default => 'Blocked until all-shop rule priority order is verified in Laravel.',
        };
    }

    private function servicesRulesSelectedPublishRuleDisabledReason(array $selectedRulePreview): string
    {
        return match (true) {
            ($selectedRulePreview['status'] ?? null) === 'draft' => 'Blocked until this draft rule clears CRUD, exclusion-parity, and publish-safety checks beyond the preview shell.',
            ($selectedRulePreview['scope'] ?? null) !== 'All shops' => 'Blocked until this scoped rule clears CRUD, scope-parity, and publish-safety checks beyond the preview shell.',
            default => 'Blocked until this all-shop rule clears CRUD and publish-safety parity beyond the preview shell.',
        };
    }

    private function cardsSelectedReviewBlockedDisabledReason(Card $selectedCard): string
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
        return match (true) {
            $selectedCardHolder->cards_count > 0 && $selectedCardHolder->is_active => 'Blocked until linked-card activity is backed by a stable Laravel event source for active-holder lookup parity.',
            $selectedCardHolder->cards_count > 0 => 'Blocked until linked-card activity is backed by a stable Laravel event source for holder lookup parity.',
            ! $selectedCardHolder->is_active => 'Blocked until inactive-holder activity history is backed by a stable Laravel event source for lifecycle parity.',
            default => 'Blocked until a stable Laravel activity source exists for holder lookup parity.',
        };
    }

    private function shopsSelectedReviewScopeDisabledReason(Shop $selectedShop): string
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
            ['label' => 'Access note', 'value' => $selectedRole->access_note ?: 'No access note saved yet'],
            ['label' => 'Assignment note', 'value' => $selectedRole->assignment_note ?: 'No assignment note saved yet'],
            ['label' => 'Coverage signal', 'value' => $this->rolesPermissionsCoverageSignal($selectedRole, $scope)],
            ['label' => 'Scope', 'value' => $scope->isNotEmpty() ? $scope->join(', ') : 'Unscoped in Laravel read slice'],
            ['label' => 'Scope coverage', 'value' => $this->rolesPermissionsScopeCoverageLabel($scope)],
            ['label' => 'Scope rollout posture', 'value' => $this->rolesPermissionsScopeRolloutSummaryPosture($scope)],
            ['label' => 'Shop scope preview', 'value' => $scope->isNotEmpty() ? $scope->take(3)->join(', ') : 'No shops linked yet'],
            ['label' => 'Scope guidance', 'value' => $scope->isNotEmpty()
                ? 'This role already has visible shop scope in Laravel, so any scope change should be treated as a parity-sensitive access change.'
                : 'No shop scope is linked yet, which keeps this role safer for draft review before scope parity is confirmed.'],
            ['label' => 'Assigned users', 'value' => (string) $selectedRole->users_count],
            ['label' => 'Assigned staff preview', 'value' => $assignedUserPreview->isNotEmpty() ? $assignedUserPreview->join(', ') : 'No staff linked yet'],
            ['label' => 'Assignment guidance', 'value' => $selectedRole->users_count > 0
                ? 'Assigned staff are already linked in Laravel, so scope and permission changes should be reviewed against real operator impact.'
                : 'No staff are linked yet, which keeps this role safer for draft access review before assignment parity is confirmed.'],
            ['label' => 'Permission count', 'value' => (string) $selectedRole->permissions_count],
            ['label' => 'Permission coverage', 'value' => $selectedRole->permissions_count > 0
                ? 'Live bundle present, review changes as parity-sensitive access coverage.'
                : 'No bundle linked yet, this role remains safer for draft parity review.'],
            ['label' => 'Permission bundle', 'value' => $permissionPreview->isNotEmpty() ? $permissionPreview->take(3)->implode(', ') : 'No permissions linked yet'],
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

    private function rolesPermissionsPublishPostureValue(Role $selectedRole): string
    {
        return match (true) {
            ! $selectedRole->is_active => 'draft-only',
            $selectedRole->users_count > 0 && $selectedRole->permissions_count > 0 => 'assignment-sensitive',
            default => 'parity-sensitive',
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

    private function rolesPermissionsLifecycleTimelineTitle(Role $selectedRole): string
    {
        return sprintf('%s lifecycle freshness reflected from model state', $selectedRole->name);
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
        return $selectedRole->updated_at !== null
            ? sprintf('The latest saved Laravel timestamp for this role is %s, giving operators a concrete checkpoint for the current access shell.', $selectedRole->updated_at->format('Y-m-d H:i'))
            : 'This role does not expose a latest saved Laravel timestamp yet, so the current access shell should stay in review-only posture.';
    }

    private function rolesPermissionsReviewNoteLabel(Role $selectedRole): string
    {
        return $selectedRole->review_note ?: 'No review note saved yet';
    }

    private function rolesPermissionsReviewNoteTimelineDescription(Role $selectedRole): string
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
        return $selectedRole->assignment_note !== null && trim($selectedRole->assignment_note) !== ''
            ? sprintf('The current Laravel assignment note says: %s', $selectedRole->assignment_note)
            : 'No Laravel assignment note is saved yet, so assignment handoff guidance still depends on the surrounding workspace cues.';
    }

    private function rolesPermissionsCoverageSignal(Role $selectedRole, mixed $scope): string
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

    private function rolesPermissionsCatalogReviewMatrixDisabledReason(mixed $roles): string
    {
        $permissionLinkedCount = $roles->filter(fn (Role $role): bool => $role->permissions_count > 0)->count();
        $activeCount = $roles->where('is_active', true)->count();

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
        return $scope->isNotEmpty() ? 'shop-scope-visible' : 'shop-scope-pending';
    }

    private function rolesPermissionsScopeRolloutSummaryPosture(mixed $scope): string
    {
        return $scope->isNotEmpty()
            ? 'Shop scope is visible in Laravel review, but scope writes should stay parity-first until the next thin access slice is ready.'
            : 'Shop scope is still pending in Laravel review, which keeps this role safer for draft-first parity checks.';
    }

    private function rolesPermissionsScopeCoverageLabel(mixed $scope): string
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
        return sprintf('%s scope coverage reflected from model state', $selectedRole->name);
    }

    private function rolesPermissionsScopeCoverageDependencyLabel(mixed $scope): string
    {
        return $this->rolesPermissionsScopeCoverageLabel($scope);
    }

    private function rolesPermissionsScopeRolloutTimelineDescription(mixed $scope): string
    {
        return $scope->isNotEmpty()
            ? sprintf('This role currently shows shop scope across %s in Laravel review mode, so scope rollout stays visible while writes remain gated.', $scope->join(', '))
            : 'This role currently has no linked shop scope in Laravel, so the review context keeps it in a safer scope-pending posture.';
    }

    private function rolesPermissionsScopeCoverageTimelineDescription(mixed $scope): string
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
        return $scope->isNotEmpty()
            ? 'This role already shows shop scope in Laravel review, but scope mutation should stay blocked until a dedicated access slice is verified.'
            : 'This role has no visible shop scope yet, so scope rollout should stay in review-only posture until a dedicated access slice is ready.';
    }

    private function rolesPermissionsSelectedRoleTimeline(Role $selectedRole, mixed $scope, mixed $permissionPreview): array
    {
        return [
            [
                'title' => sprintf('%s selected for Laravel review', $selectedRole->name),
                'time' => 'Current request',
                'description' => 'The shared roles-permissions workspace is now loading this saved role from Laravel data instead of only static preview rows.',
            ],
            [
                'title' => sprintf('%s status reflected from model state', $selectedRole->name),
                'time' => 'Current request',
                'description' => $selectedRole->is_active
                    ? 'This role is currently marked as active in Laravel and the management context now treats it as a live access shell.'
                    : 'This role is currently marked as draft in Laravel, so the management context keeps it in a safer parity-review posture.',
            ],
            [
                'title' => $this->rolesPermissionsLifecycleTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsLifecycleTimelineDescription($selectedRole),
            ],
            [
                'title' => sprintf('%s last saved timestamp reflected from model state', $selectedRole->name),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsLastSavedTimelineDescription($selectedRole),
            ],
            [
                'title' => sprintf('%s review note reflected from model state', $selectedRole->name),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsReviewNoteTimelineDescription($selectedRole),
            ],
            [
                'title' => sprintf('%s access note reflected from model state', $selectedRole->name),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsAccessNoteTimelineDescription($selectedRole),
            ],
            [
                'title' => sprintf('%s assignment note reflected from model state', $selectedRole->name),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsAssignmentNoteTimelineDescription($selectedRole),
            ],
            [
                'title' => sprintf('%s scope posture reflected from model state', $selectedRole->name),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsScopeRolloutTimelineDescription($scope),
            ],
            [
                'title' => $this->rolesPermissionsScopeCoverageTimelineTitle($selectedRole),
                'time' => 'Current request',
                'description' => $this->rolesPermissionsScopeCoverageTimelineDescription($scope),
            ],
            [
                'title' => sprintf('%s permission bundle reflected from model state', $selectedRole->name),
                'time' => 'Current request',
                'description' => $permissionPreview->isNotEmpty()
                    ? sprintf('This role currently exposes %s in Laravel and the review context now mirrors that access bundle.', $permissionPreview->take(3)->implode(', '))
                    : 'This role currently has no linked permissions in Laravel, so it remains a safe draft for parity-first access review.',
            ],
            [
                'title' => sprintf('%s assignment scope reflected from model state', $selectedRole->name),
                'time' => 'Current request',
                'description' => $scope->isNotEmpty()
                    ? sprintf('This role is currently linked to %d assigned users across %s in Laravel review mode.', $selectedRole->users_count, $scope->join(', '))
                    : 'This role is not linked to any scoped shops yet, so it remains a safer draft target for access-parity review.',
            ],
        ];
    }

    private function rolesPermissionsSelectedRoleDependencyStatus(Role $selectedRole, mixed $scope, mixed $permissionPreview): array
    {
        return [
            ['label' => 'Selected role', 'value' => $selectedRole->name],
            ['label' => 'Review posture', 'value' => 'Selected-role review is running in Laravel-backed read mode only'],
            ['label' => 'Status posture', 'value' => $selectedRole->is_active
                ? 'This role is active in Laravel now, but live-facing access changes should still stay parity-first until assignment and matrix flows are verified.'
                : 'This role remains draft in Laravel, which keeps it safer for parity checks before operators depend on it for live access.'],
            ['label' => 'Lifecycle freshness', 'value' => $this->rolesPermissionsLifecycleDependencyLabel($selectedRole)],
            ['label' => 'Last saved in Laravel', 'value' => $this->rolesPermissionsLastSavedLabel($selectedRole)],
            ['label' => 'Review note', 'value' => $this->rolesPermissionsReviewNoteLabel($selectedRole)],
            ['label' => 'Access note', 'value' => $this->rolesPermissionsAccessNoteLabel($selectedRole)],
            ['label' => 'Assignment note', 'value' => $this->rolesPermissionsAssignmentNoteLabel($selectedRole)],
            ['label' => 'Coverage signal', 'value' => $this->rolesPermissionsCoverageSignal($selectedRole, $scope)],
            ['label' => 'Scope rollout posture', 'value' => $this->rolesPermissionsScopeRolloutDependencyPosture($scope)],
            ['label' => 'Scope coverage', 'value' => $this->rolesPermissionsScopeCoverageDependencyLabel($scope)],
            ['label' => 'Matrix posture', 'value' => 'Keep matrix editing blocked until legacy staff-access parity is verified in Laravel'],
            ['label' => 'Assigned staff posture', 'value' => $selectedRole->users_count > 0
                ? 'Linked staff are already affected by this role in Laravel, so assignment parity should be checked before any access changes move forward.'
                : 'No linked staff are affected yet, which keeps this role safer for draft review before assignment parity is confirmed.'],
            ['label' => 'Permission posture', 'value' => $permissionPreview->isNotEmpty()
                ? 'The visible Laravel permission bundle is reviewable now, but bundle edits should stay blocked until legacy access mapping is verified.'
                : 'No permissions are linked yet, so this role remains a safer draft shell for parity-first access review.'],
            ['label' => 'Publish posture', 'value' => $selectedRole->is_active
                ? 'This live permission bundle still needs assignment parity checks before publish-style role changes are safe.'
                : 'This draft role should stay unpublished until permission bundle and shop-scope parity are mapped more explicitly.'],
            ['label' => 'Scope posture', 'value' => $scope->isNotEmpty()
                ? 'Assigned shops are visible for review, but scope writes should stay parity-first until staff assignment rules are confirmed.'
                : 'No shop scope is linked yet, which keeps this role safe for draft access review.'],
            ['label' => 'Remaining backend gap', 'value' => 'Role assignment, matrix editing, and shop-scoped authorization writes still remain preview-only for this workspace'],
        ];
    }

    private function cardsSelectedCardSummary(Card $selectedCard): array
    {
        return [
            ['label' => 'Selected card', 'value' => $selectedCard->number],
            ['label' => 'Review mode', 'value' => $selectedCard->status === 'draft'
                ? 'Draft-safe review, this inventory record is still safer for parity checks before operators treat it as issued stock.'
                : 'Live inventory review, this saved Laravel card already carries operational state that should stay parity-first.'],
            ['label' => 'Operational readiness', 'value' => $this->cardsOperationalReadiness($selectedCard)],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardsLifecycleFreshnessLabel($selectedCard)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardsLastSavedLabel($selectedCard)],
            ['label' => 'Holder', 'value' => $selectedCard->holder?->full_name ?? 'Unassigned'],
            ['label' => 'Card type', 'value' => $selectedCard->type?->name ?? 'Unknown'],
            ['label' => 'Linkage signal', 'value' => $this->cardsLinkageSignal($selectedCard)],
            ['label' => 'Shop', 'value' => $selectedCard->shop?->name ?? 'Unassigned'],
            ['label' => 'Shop guidance', 'value' => $selectedCard->shop !== null
                ? 'Keep this card tied to its current branch context during review, because cross-shop inventory handling was parity-sensitive in the old Galaxy flow.'
                : 'No branch is linked yet, so shop-level handling should stay in parity review before operators rely on this card record operationally.'],
            ['label' => 'Laravel status', 'value' => $selectedCard->status],
            ['label' => 'Activated', 'value' => $selectedCard->activated_at?->format('Y-m-d') ?? '—'],
            [
                'label' => 'Inventory guidance',
                'value' => match ($selectedCard->status) {
                    'active' => 'This card is already active in Laravel, so inventory changes should stay parity-first until blocked and replacement semantics are verified.',
                    'blocked' => 'This card is blocked in Laravel, so replacement and dispute handling should remain review-only until legacy card-state parity is confirmed.',
                    default => 'This card is still draft inventory in Laravel, which keeps it safe for parity checks before operators treat it as issued stock.',
                },
            ],
        ];
    }

    private function cardsOperationalReadiness(Card $selectedCard): string
    {
        return match (true) {
            $selectedCard->status === 'blocked' => 'blocked inventory, operator review only',
            $selectedCard->status === 'active' && $selectedCard->holder !== null => 'issued inventory, parity-sensitive',
            $selectedCard->status === 'active' => 'active inventory, holder linkage incomplete',
            default => 'draft inventory shell',
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

    private function cardsSelectedCardDependencyStatus(Card $selectedCard): array
    {
        return [
            ['label' => 'Selected card', 'value' => $selectedCard->number],
            ['label' => 'Inventory posture', 'value' => 'Selected-card review is running in Laravel-backed read mode only'],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardsLifecycleFreshnessLabel($selectedCard)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardsLastSavedLabel($selectedCard)],
            ['label' => 'Linkage signal', 'value' => $this->cardsLinkageSignal($selectedCard)],
            ['label' => 'Lifecycle posture', 'value' => match ($selectedCard->status) {
                'active' => 'This active card should stay read-only until issue, block, and replacement parity are verified.',
                'blocked' => 'This blocked card should stay under review-only handling until dispute and replacement semantics match the old Galaxy flow.',
                default => 'This draft card should stay in parity review until issuance rules are confirmed in Laravel.',
            }],
            ['label' => 'Assignment posture', 'value' => $selectedCard->holder !== null
                ? 'Holder linkage is visible now, but reassignment and replacement actions should stay blocked until inventory parity is verified.'
                : 'No holder is linked yet, which keeps this inventory record safer for parity review before assignment flows are enabled.'],
            ['label' => 'Shop posture', 'value' => $selectedCard->shop !== null
                ? 'Shop ownership is visible for review, but cross-branch movement should stay blocked until branch inventory rules are verified.'
                : 'No shop is assigned yet, so branch-level inventory handling should stay in review mode only.'],
            ['label' => 'Remaining backend gap', 'value' => 'Card lifecycle writes, blocked-card handling, and replacement flows still remain preview-only for this workspace'],
        ];
    }

    private function cardholdersSelectedHolderSummary(CardHolder $selectedCardHolder): array
    {
        return [
            ['label' => 'Selected holder', 'value' => $selectedCardHolder->full_name],
            ['label' => 'Review mode', 'value' => $selectedCardHolder->is_active
                ? 'Live profile review, this holder already participates in the Laravel lookup surface and should stay parity-first.'
                : 'Dormant-profile review, this inactive holder stays safer for parity checks before any reactivation path is trusted.'],
            ['label' => 'Operational readiness', 'value' => $this->cardholdersOperationalReadiness($selectedCardHolder)],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardholdersLifecycleFreshnessLabel($selectedCardHolder)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardholdersLastSavedLabel($selectedCardHolder)],
            ['label' => 'Phone', 'value' => $selectedCardHolder->phone ?? '—'],
            ['label' => 'Linkage signal', 'value' => $this->cardholdersLinkageSignal($selectedCardHolder)],
            ['label' => 'Shop', 'value' => $selectedCardHolder->shop?->name ?? 'Unassigned'],
            ['label' => 'Shop guidance', 'value' => $selectedCardHolder->shop !== null
                ? 'Keep this holder anchored to the current branch during review, because old Galaxy lookup flows depended on branch-aware identity context.'
                : 'No branch is linked yet, so shop-aware lookup behavior should stay in parity review before profile actions are widened.'],
            ['label' => 'Linked cards', 'value' => (string) $selectedCardHolder->cards_count],
            ['label' => 'Laravel status', 'value' => $selectedCardHolder->is_active ? 'active' : 'inactive'],
            [
                'label' => 'Lookup guidance',
                'value' => $selectedCardHolder->is_active
                    ? 'This holder is active in Laravel, so identity and linkage review should stay parity-first until recent-activity sourcing is verified.'
                    : 'This holder is inactive in Laravel, which keeps the record safe for parity checks before operators treat it as fully reactivated.',
            ],
        ];
    }

    private function cardholdersOperationalReadiness(CardHolder $selectedCardHolder): string
    {
        return match (true) {
            ! $selectedCardHolder->is_active => 'inactive profile, review only',
            $selectedCardHolder->cards_count > 0 => 'linked profile, operator-visible',
            default => 'active profile, linkage build-out pending',
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

    private function cardholdersSelectedHolderDependencyStatus(CardHolder $selectedCardHolder): array
    {
        return [
            ['label' => 'Selected holder', 'value' => $selectedCardHolder->full_name],
            ['label' => 'Lookup posture', 'value' => 'Selected-holder review is running in Laravel-backed read mode only'],
            ['label' => 'Lifecycle freshness', 'value' => $this->cardholdersLifecycleFreshnessLabel($selectedCardHolder)],
            ['label' => 'Last saved in Laravel', 'value' => $this->cardholdersLastSavedLabel($selectedCardHolder)],
            ['label' => 'Linkage signal', 'value' => $this->cardholdersLinkageSignal($selectedCardHolder)],
            ['label' => 'Status posture', 'value' => $selectedCardHolder->is_active
                ? 'This active holder is visible for review now, but lifecycle changes should stay blocked until search and profile parity are verified.'
                : 'This inactive holder should stay review-only until reactivation and duplicate-profile rules are verified.'],
            ['label' => 'Card linkage posture', 'value' => $selectedCardHolder->cards_count > 0
                ? 'Linked cards are visible in Laravel, but card-to-holder lifecycle changes should stay parity-first until activity sourcing is verified.'
                : 'No linked cards exist yet, which keeps this holder safer for identity review before card-link flows are enabled.'],
            ['label' => 'Activity posture', 'value' => 'Recent activity remains blocked until a stable Laravel event source exists for holder lookup parity.'],
            ['label' => 'Remaining backend gap', 'value' => 'Holder search, profile writes, and recent-activity sourcing still remain preview-only for this workspace'],
        ];
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
            ['label' => 'Code', 'value' => $selectedShop->code],
            ['label' => 'Coverage signal', 'value' => $this->shopsCoverageSignal($selectedShop)],
            ['label' => 'Assigned manager', 'value' => $selectedShop->users->first()?->name ?? 'Unassigned'],
            ['label' => 'Manager guidance', 'value' => $selectedShop->users_count > 0
                ? 'Keep current manager ownership visible during review, because legacy Galaxy branch administration depended on clear branch responsibility.'
                : 'No manager is assigned yet, so ownership expectations should stay parity-first until assignment rules are verified.'],
            ['label' => 'Cardholders', 'value' => (string) $selectedShop->card_holders_count],
            ['label' => 'Cards', 'value' => (string) $selectedShop->cards_count],
            ['label' => 'Laravel status', 'value' => $selectedShop->is_active ? 'active' : 'paused'],
            [
                'label' => 'Branch guidance',
                'value' => $selectedShop->is_active
                    ? 'This branch is already active in Laravel, so scope and manager changes should stay parity-first until branch ownership rules are verified.'
                    : 'This branch is still paused, which keeps it safe for parity checks before operators treat it as fully live.',
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
            ['label' => 'Coverage signal', 'value' => $this->shopsCoverageSignal($selectedShop)],
            ['label' => 'Status posture', 'value' => $selectedShop->is_active
                ? 'This active branch is visible for review now, but manager and scope changes should stay blocked until legacy ownership rules are verified.'
                : 'This paused branch should stay review-only until recovery and ownership parity are verified.'],
            ['label' => 'Manager posture', 'value' => $selectedShop->users_count > 0
                ? 'Assigned managers are visible in Laravel, but reassignment should stay blocked until branch ownership parity is confirmed.'
                : 'No manager is assigned yet, which keeps this branch safer for parity review before ownership flows are enabled.'],
            ['label' => 'Coverage posture', 'value' => sprintf('This branch currently exposes %d cardholders and %d cards for read-only Laravel review.', $selectedShop->card_holders_count, $selectedShop->cards_count)],
            ['label' => 'Remaining backend gap', 'value' => 'Branch writes, manager reassignment, and shop-scope mutation flows still remain preview-only for this workspace'],
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
