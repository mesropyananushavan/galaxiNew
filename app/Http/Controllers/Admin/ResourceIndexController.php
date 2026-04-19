<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
use App\Models\Role;
use App\Models\Shop;
use App\Support\AdminResourcePageNormalizer;
use BackedEnum;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\View\View;
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
                    ['label' => 'Troubleshooting guidance', 'value' => 'Treat this receipt as read-only review until Laravel transaction history and adjustment flows exist.'],
                ],
                'timeline' => [
                    ['title' => 'CHK-90421 selected for receipt review', 'time' => 'Current request', 'description' => 'This preview now keeps the positive-accrual receipt in a dedicated Galaxy review context instead of a flat transaction row.'],
                    ['title' => 'Receipt-first handoff stays visible', 'time' => 'Current request', 'description' => 'Operators should pass along verified receipt, card, and shop context here before discussing any later correction flow.'],
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
                    ['label' => 'Troubleshooting guidance', 'value' => 'Treat this receipt as read-only review until Laravel transaction history and rule-backed explanations exist.'],
                ],
                'timeline' => [
                    ['title' => 'CHK-90407 selected for zero-accrual review', 'time' => 'Current request', 'description' => 'This preview now keeps the zero-accrual receipt in a dedicated Galaxy review context instead of a flat transaction row.'],
                    ['title' => 'Zero-accrual handoff stays cautious', 'time' => 'Current request', 'description' => 'Operators should hand off receipt evidence and shop context here before escalating to future correction workflows.'],
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
                    ['label' => 'Troubleshooting guidance', 'value' => 'Treat this receipt as read-only review until Laravel transaction history and shop-aware filters exist.'],
                ],
                'timeline' => [
                    ['title' => 'CHK-90388 selected for branch receipt review', 'time' => 'Current request', 'description' => 'This preview now keeps the North Shop receipt in a dedicated Galaxy review context instead of a flat transaction row.'],
                    ['title' => 'Branch-specific handoff stays receipt-first', 'time' => 'Current request', 'description' => 'Operators should hand off receipt and shop context here before any future transaction-edit or correction flow is considered.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected receipt', 'value' => 'CHK-90388'],
                    ['label' => 'Receipt posture', 'value' => 'Branch receipt lookup should stay read-only until Laravel shop filters and transaction history are verified against the old flow.'],
                    ['label' => 'Accrual posture', 'value' => 'Positive branch accrual outcomes still need live transaction-domain parity before any adjustment path is safe.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Transaction tables, receipt reads, and adjustment handlers still remain blocked for this receipt preview.'],
                ],
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

        $selectedReceipt = request()->query('receipt');

        if (! is_string($selectedReceipt)) {
            return $page;
        }

        $selectedReceiptPreview = collect($receiptPreviews)->first(fn (array $receipt): bool => $receipt['key'] === strtolower($selectedReceipt));

        if (! is_array($selectedReceiptPreview)) {
            return $page;
        }

        $page['selectedRecordSummary'] = $selectedReceiptPreview['summary'];
        $page['actions'] = [
            [
                'label' => 'Back to all receipts',
                'tone' => 'primary',
                'href' => route('admin.checks-points.index', absolute: false),
            ],
            [
                'label' => sprintf('Reviewing: %s', $selectedReceiptPreview['label']),
                'tone' => 'secondary',
            ],
            [
                'label' => 'Find receipt',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until receipt lookup is backed by Laravel transaction reads.',
            ],
            [
                'label' => 'Review accrual gaps',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until accrual-gap review is backed by Laravel transaction and rule data.',
            ],
        ];
        $page['activityTimeline'] = $selectedReceiptPreview['timeline'];
        $page['dependencyStatus'] = $selectedReceiptPreview['dependencyStatus'];

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
                    ['label' => 'Effect guidance', 'value' => 'Treat the uplift as review-only until accrual calculations and birthday eligibility are backed by Laravel writes.'],
                ],
                'timeline' => [
                    ['title' => 'Birthday bonus selected for rule review', 'time' => 'Current request', 'description' => 'This preview now keeps the highest-visibility loyalty uplift in a dedicated Galaxy review context instead of a flat catalog row.'],
                    ['title' => 'Birthday rule handoff stays parity-first', 'time' => 'Current request', 'description' => 'Operators should carry birthday eligibility and accrual notes in the workspace before trusting any future write flow.'],
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
                    ['label' => 'Effect guidance', 'value' => 'Treat the partner uplift as review-only until scoped accrual behavior is backed by Laravel rule writes.'],
                ],
                'timeline' => [
                    ['title' => 'Partner card uplift selected for scope review', 'time' => 'Current request', 'description' => 'This preview now keeps the scoped partner-card rule visible in its own Galaxy review context.'],
                    ['title' => 'Scoped uplift handoff stays branch-aware', 'time' => 'Current request', 'description' => 'Operators should pass along Central Shop scope assumptions here before relying on future rule-editing flows.'],
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
                    ['label' => 'Effect guidance', 'value' => 'Treat the no-accrual effect as a review-only exception until Laravel can safely reproduce the old block semantics.'],
                ],
                'timeline' => [
                    ['title' => 'Night service block selected for exception review', 'time' => 'Current request', 'description' => 'This preview now keeps the draft exclusion rule in a dedicated Galaxy review context instead of leaving it as a flat table row.'],
                    ['title' => 'Draft exclusion handoff stays cautious', 'time' => 'Current request', 'description' => 'Operators should hand off bar-service parity concerns here before any future publish flow is allowed.'],
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

        $selectedRule = request()->query('rule');

        if (! is_string($selectedRule)) {
            return $page;
        }

        $selectedRulePreview = collect($rulePreviews)->first(fn (array $rule): bool => $rule['key'] === $selectedRule);

        if (! is_array($selectedRulePreview)) {
            return $page;
        }

        $page['selectedRecordSummary'] = $selectedRulePreview['summary'];
        $page['actions'] = [
            [
                'label' => 'Back to all rules',
                'tone' => 'primary',
                'href' => route('admin.services-rules.index', absolute: false),
            ],
            [
                'label' => sprintf('Reviewing: %s', $selectedRulePreview['label']),
                'tone' => 'secondary',
            ],
            [
                'label' => 'Review priorities',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until rule priority resolution is verified in Laravel.',
            ],
            [
                'label' => 'Publish rule',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until rule CRUD and parity checks exist beyond the preview shell.',
            ],
        ];
        $page['activityTimeline'] = $selectedRulePreview['timeline'];
        $page['dependencyStatus'] = $selectedRulePreview['dependencyStatus'];

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
                    ['label' => 'Redemption guidance', 'value' => 'Treat this reward as review-only until gift CRUD and redemption parity are backed by Laravel flows.'],
                ],
                'timeline' => [
                    ['title' => 'Coffee voucher selected for reward review', 'time' => 'Current request', 'description' => 'This preview now keeps the baseline all-shop reward in a dedicated Galaxy review context instead of a flat catalog row.'],
                    ['title' => 'All-shop reward handoff stays stock-aware', 'time' => 'Current request', 'description' => 'Operators should carry stock assumptions and reward-availability notes here before trusting future publish flows.'],
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
                    ['label' => 'Redemption guidance', 'value' => 'Treat this scoped reward as review-only until stock-aware redemption behavior is backed by Laravel flows.'],
                ],
                'timeline' => [
                    ['title' => 'Airport transfer selected for scoped reward review', 'time' => 'Current request', 'description' => 'This preview now keeps the kiosk-scoped reward in its own Galaxy review context instead of a flat catalog row.'],
                    ['title' => 'Finite-stock handoff stays branch-specific', 'time' => 'Current request', 'description' => 'Operators should hand off Airport Kiosk stock assumptions here before relying on future gift-write flows.'],
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
                    ['label' => 'Redemption guidance', 'value' => 'Treat this paused reward as review-only until stock recovery and redemption parity are backed by Laravel flows.'],
                ],
                'timeline' => [
                    ['title' => 'Premium dessert set selected for paused reward review', 'time' => 'Current request', 'description' => 'This preview now keeps the zero-stock reward in a dedicated Galaxy review context instead of leaving it as a flat table row.'],
                    ['title' => 'Paused reward handoff stays cautious', 'time' => 'Current request', 'description' => 'Operators should hand off zero-stock and reopening assumptions here before any future publish or stock-update flow is allowed.'],
                ],
                'dependencyStatus' => [
                    ['label' => 'Selected gift', 'value' => 'Premium dessert set'],
                    ['label' => 'Scope posture', 'value' => 'Paused shop-scoped reward behavior should stay preview-only until Laravel scope and reopening checks are verified.'],
                    ['label' => 'Stock posture', 'value' => 'Zero-stock handling is still preview-only until inventory sync and recovery behavior are validated in Laravel.'],
                    ['label' => 'Remaining backend gap', 'value' => 'Gift CRUD, stock updates, and redemption persistence still remain blocked for this paused reward preview.'],
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

        $selectedGift = request()->query('gift');

        if (! is_string($selectedGift)) {
            return $page;
        }

        $selectedGiftPreview = collect($giftPreviews)->first(fn (array $gift): bool => $gift['key'] === $selectedGift);

        if (! is_array($selectedGiftPreview)) {
            return $page;
        }

        $page['selectedRecordSummary'] = $selectedGiftPreview['summary'];
        $page['actions'] = [
            [
                'label' => 'Back to all gifts',
                'tone' => 'primary',
                'href' => route('admin.gifts.index', absolute: false),
            ],
            [
                'label' => sprintf('Reviewing: %s', $selectedGiftPreview['label']),
                'tone' => 'secondary',
            ],
            [
                'label' => 'Stock audit',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until stock checks are backed by Laravel inventory data.',
            ],
            [
                'label' => 'Publish gift',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until gift CRUD and redemption parity exist beyond the preview shell.',
            ],
        ];
        $page['activityTimeline'] = $selectedGiftPreview['timeline'];
        $page['dependencyStatus'] = $selectedGiftPreview['dependencyStatus'];

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
            ['label' => 'Active roles', 'value' => (string) $roles->filter(fn (Role $role): bool => $role->permissions_count > 0)->count()],
            ['label' => 'Draft roles', 'value' => (string) $roles->filter(fn (Role $role): bool => $role->permissions_count === 0)->count()],
            ['label' => 'Scoped shops', 'value' => (string) $roles->flatMap(fn (Role $role) => $role->users->pluck('shop_id'))->filter()->unique()->count()],
        ];

        $page['table']['rows'] = $roles->map(function (Role $role): array {
            $scope = $role->users->pluck('shop.name')->filter()->unique();
            $permissionPreview = $role->permissions->pluck('name')->take(3)->implode(', ');

            return [
                $this->linkedTableCell($role->name, 'admin.roles-permissions.index', ['role' => $role->id]),
                $scope->isNotEmpty() ? $scope->join(', ') : 'Unscoped in Laravel read slice',
                $permissionPreview !== '' ? $permissionPreview : 'No permissions linked yet',
                (string) $role->users_count,
                $role->permissions_count > 0 ? 'active' : 'draft',
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

        $page['actions'] = [
            [
                'label' => 'Back to all roles',
                'tone' => 'primary',
                'href' => route('admin.roles-permissions.index', absolute: false),
            ],
            [
                'label' => sprintf('Reviewing: %s', $selectedRole->name),
                'tone' => 'secondary',
            ],
            [
                'label' => 'Review matrix',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until the Laravel permission matrix can be verified against legacy staff access.',
            ],
            [
                'label' => 'Publish role',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => $selectedRole->permissions_count > 0
                    ? 'Blocked until live role assignment parity is verified for this Laravel permission bundle.'
                    : 'Blocked until this draft role has a verified permission bundle and shop scope parity.',
            ],
        ];

        $page['activityTimeline'] = $this->rolesPermissionsSelectedRoleTimeline(
            $selectedRole,
            $scope,
            $permissionPreview,
        );

        $page['dependencyStatus'] = $this->rolesPermissionsSelectedRoleDependencyStatus(
            $selectedRole,
            $scope,
            $permissionPreview,
        );

        return $page;
    }

    private function enrichCardsPage(array $page): array
    {
        $cards = Card::query()
            ->with(['shop', 'holder', 'type'])
            ->orderBy('number')
            ->get();

        if ($cards->isEmpty()) {
            return $page;
        }

        $page['metrics'] = [
            ['label' => 'Active cards', 'value' => (string) $cards->where('status', 'active')->count()],
            ['label' => 'Draft cards', 'value' => (string) $cards->where('status', 'draft')->count()],
            ['label' => 'Blocked cards', 'value' => (string) $cards->where('status', 'blocked')->count()],
        ];

        $page['table']['rows'] = $cards->map(fn (Card $card): array => [
            $this->linkedTableCell($card->number, 'admin.cards.index', ['card' => $card->id]),
            $card->holder?->full_name ?? 'Unassigned',
            $card->type?->name ?? 'Unknown',
            $card->shop?->name ?? 'Unassigned',
            $card->status,
            $card->activated_at?->format('Y-m-d') ?? '—',
        ])->all();

        $latestCard = $cards->sortByDesc('id')->first();

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

        $page['selectedRecordSummary'] = [
            ['label' => 'Selected card', 'value' => $selectedCard->number],
            ['label' => 'Holder', 'value' => $selectedCard->holder?->full_name ?? 'Unassigned'],
            ['label' => 'Card type', 'value' => $selectedCard->type?->name ?? 'Unknown'],
            ['label' => 'Shop', 'value' => $selectedCard->shop?->name ?? 'Unassigned'],
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

        $page['actions'] = [
            [
                'label' => 'Back to all cards',
                'tone' => 'primary',
                'href' => route('admin.cards.index', absolute: false),
            ],
            [
                'label' => sprintf('Reviewing: %s', $selectedCard->number),
                'tone' => 'secondary',
            ],
            [
                'label' => 'Review blocked cards',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until legacy blocked-card semantics are verified against the Laravel inventory flow.',
            ],
        ];

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
        ];

        return $page;
    }

    private function enrichCardHoldersPage(array $page): array
    {
        $cardHolders = CardHolder::query()
            ->with(['shop'])
            ->withCount('cards')
            ->orderBy('full_name')
            ->get();

        if ($cardHolders->isEmpty()) {
            return $page;
        }

        $page['metrics'] = [
            ['label' => 'Active holders', 'value' => (string) $cardHolders->where('is_active', true)->count()],
            ['label' => 'Inactive holders', 'value' => (string) $cardHolders->where('is_active', false)->count()],
            ['label' => 'Linked cards', 'value' => (string) $cardHolders->sum('cards_count')],
        ];

        $page['table']['rows'] = $cardHolders->map(fn (CardHolder $cardHolder): array => [
            $this->linkedTableCell($cardHolder->full_name, 'admin.cardholders.index', ['cardholder' => $cardHolder->id]),
            $cardHolder->phone ?? '—',
            $cardHolder->shop?->name ?? 'Unassigned',
            (string) $cardHolder->cards_count,
            $cardHolder->is_active ? 'active' : 'inactive',
            $cardHolder->updated_at?->format('Y-m-d') ?? '—',
        ])->all();

        $latestCardHolder = $cardHolders->sortByDesc('id')->first();

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

        $page['selectedRecordSummary'] = [
            ['label' => 'Selected holder', 'value' => $selectedCardHolder->full_name],
            ['label' => 'Phone', 'value' => $selectedCardHolder->phone ?? '—'],
            ['label' => 'Shop', 'value' => $selectedCardHolder->shop?->name ?? 'Unassigned'],
            ['label' => 'Linked cards', 'value' => (string) $selectedCardHolder->cards_count],
            ['label' => 'Laravel status', 'value' => $selectedCardHolder->is_active ? 'active' : 'inactive'],
            [
                'label' => 'Lookup guidance',
                'value' => $selectedCardHolder->is_active
                    ? 'This holder is active in Laravel, so identity and linkage review should stay parity-first until recent-activity sourcing is verified.'
                    : 'This holder is inactive in Laravel, which keeps the record safe for parity checks before operators treat it as fully reactivated.',
            ],
        ];

        $page['actions'] = [
            [
                'label' => 'Back to all holders',
                'tone' => 'primary',
                'href' => route('admin.cardholders.index', absolute: false),
            ],
            [
                'label' => sprintf('Reviewing: %s', $selectedCardHolder->full_name),
                'tone' => 'secondary',
            ],
            [
                'label' => 'Review recent activity',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until a stable Laravel activity source exists for holder lookup parity.',
            ],
        ];

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
        ];

        return $page;
    }

    private function enrichShopsPage(array $page): array
    {
        $shops = Shop::query()
            ->withCount(['users', 'cardHolders', 'cards'])
            ->with(['users' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        if ($shops->isEmpty()) {
            return $page;
        }

        $page['metrics'] = [
            ['label' => 'Active shops', 'value' => (string) $shops->where('is_active', true)->count()],
            ['label' => 'Paused shops', 'value' => (string) $shops->where('is_active', false)->count()],
            ['label' => 'Assigned managers', 'value' => (string) $shops->filter(fn (Shop $shop): bool => $shop->users_count > 0)->count()],
        ];

        $page['table']['rows'] = $shops->map(fn (Shop $shop): array => [
            $this->linkedTableCell($shop->name, 'admin.shops.index', ['shop' => $shop->id]),
            $shop->code,
            $shop->users->first()?->name ?? 'Unassigned',
            (string) $shop->card_holders_count,
            (string) $shop->cards_count,
            $shop->is_active ? 'active' : 'paused',
        ])->all();

        $latestShop = $shops->sortByDesc('id')->first();

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

        $page['selectedRecordSummary'] = [
            ['label' => 'Selected shop', 'value' => $selectedShop->name],
            ['label' => 'Code', 'value' => $selectedShop->code],
            ['label' => 'Assigned manager', 'value' => $selectedShop->users->first()?->name ?? 'Unassigned'],
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

        $page['actions'] = [
            [
                'label' => 'Back to all shops',
                'tone' => 'primary',
                'href' => route('admin.shops.index', absolute: false),
            ],
            [
                'label' => sprintf('Reviewing: %s', $selectedShop->name),
                'tone' => 'secondary',
            ],
            [
                'label' => 'Review branch scope',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until branch ownership rules are confirmed against the legacy Galaxy multi-shop access model.',
            ],
        ];

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
        ];

        return $page;
    }

    private function enrichReportsPage(array $page): array
    {
        $shopCount = Shop::query()->count();
        $cardCount = Card::query()->count();
        $cardHolderCount = CardHolder::query()->count();
        $roleCount = Role::query()->count();

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

        $selectedSource = request()->query('source');

        if (! is_string($selectedSource)) {
            return $page;
        }

        $selectedReportSource = collect($reportSources)->first(fn (array $source): bool => $source['key'] === $selectedSource);

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
                'disabledReason' => 'Blocked until preset handling is backed by Laravel reporting flow validation.',
            ],
            [
                'label' => 'Export source snapshot',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until reporting exports and file delivery are verified against legacy Galaxy output expectations.',
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
                $cardType->is_active ? 'Active in Laravel flow' : 'Draft in Laravel flow',
                [
                    'label' => $cardType->is_active ? 'Move to draft' : 'Activate type',
                    'href' => route('admin.card-types.toggle-status', $cardType, absolute: false),
                    'method' => 'PATCH',
                ],
            ])->all();
        }

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

        $selectedCardType = CardType::query()->find($selectedCardTypeId);

        if ($selectedCardType === null || ! is_array($page['liveForm'] ?? null)) {
            return $page;
        }

        $page['selectedRecordSummary'] = [
            ['label' => 'Selected tier', 'value' => $selectedCardType->name],
            ['label' => 'Slug', 'value' => $selectedCardType->slug],
            ['label' => 'Points rate', 'value' => number_format((float) $selectedCardType->points_rate, 2).'x'],
            ['label' => 'Laravel status', 'value' => $selectedCardType->is_active ? 'active' : 'draft'],
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
                'disabledReason' => $selectedCardType->is_active
                    ? 'Blocked until live-tier accrual parity is verified.'
                    : 'Blocked until draft parity review is complete.',
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
        ];

        $page = $this->prependCardTypeLatestFlowTimelineItem($page);

        $page['dependencyStatus'] = [
            ['label' => 'Selected record', 'value' => $selectedCardType->name],
            ['label' => 'Edit flow state', 'value' => 'Shared live form is running in request-driven PATCH mode'],
            ['label' => 'Current status posture', 'value' => $selectedCardType->is_active ? 'Active tiers should stay stable unless parity checks are complete' : 'Draft tiers are the safe place for parity-first validation and copy changes'],
            ['label' => 'Rule-import posture', 'value' => $selectedCardType->is_active ? 'Keep imports blocked until active-tier accrual parity is verified' : 'Imports can be reviewed in draft mode, but they are still not safe to enable yet'],
            ['label' => 'Publish posture', 'value' => $selectedCardType->is_active ? 'Live tiers need parity confirmation before further publish-style changes' : 'Draft tiers should stay unpublished until legacy behavior is mapped more explicitly'],
            ['label' => 'Action gating', 'value' => $selectedCardType->is_active ? 'Allow small state corrections only, keep publish-like and import actions gated' : 'Allow draft-safe edits and validation only, keep live-facing actions gated'],
            ['label' => 'Remaining backend gap', 'value' => 'Publish logic and rule-import parity still remain preview-only for this tier'],
        ];

        $page = $this->appendCardTypeLatestFlowDependencyStatus($page);

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
        ];

        return $page;
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
            ['label' => 'Review mode', 'value' => $selectedRole->users_count > 0 || $selectedRole->permissions_count > 0
                ? 'Live-impact review, linked staff or permissions already exist in Laravel'
                : 'Draft-safe review, no linked staff or permissions yet in Laravel'],
            ['label' => 'Scope', 'value' => $scope->isNotEmpty() ? $scope->join(', ') : 'Unscoped in Laravel read slice'],
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
            ['label' => 'Laravel status', 'value' => $selectedRole->permissions_count > 0 ? 'active' : 'draft'],
            [
                'label' => 'Access guidance',
                'value' => $selectedRole->permissions_count > 0
                    ? 'This role already carries a Laravel permission bundle, so assignment and scope changes should stay parity-first until the matrix editor is verified.'
                    : 'This role is still a draft shell in Laravel, which keeps it safe for parity checks before operators rely on it for staff access.',
            ],
        ];
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
            ['label' => 'Matrix posture', 'value' => 'Keep matrix editing blocked until legacy staff-access parity is verified in Laravel'],
            ['label' => 'Assigned staff posture', 'value' => $selectedRole->users_count > 0
                ? 'Linked staff are already affected by this role in Laravel, so assignment parity should be checked before any access changes move forward.'
                : 'No linked staff are affected yet, which keeps this role safer for draft review before assignment parity is confirmed.'],
            ['label' => 'Permission posture', 'value' => $permissionPreview->isNotEmpty()
                ? 'The visible Laravel permission bundle is reviewable now, but bundle edits should stay blocked until legacy access mapping is verified.'
                : 'No permissions are linked yet, so this role remains a safer draft shell for parity-first access review.'],
            ['label' => 'Publish posture', 'value' => $selectedRole->permissions_count > 0
                ? 'This live permission bundle still needs assignment parity checks before publish-style role changes are safe.'
                : 'This draft role should stay unpublished until permission bundle and shop-scope parity are mapped more explicitly.'],
            ['label' => 'Scope posture', 'value' => $scope->isNotEmpty()
                ? 'Assigned shops are visible for review, but scope writes should stay parity-first until staff assignment rules are confirmed.'
                : 'No shop scope is linked yet, which keeps this role safe for draft access review.'],
            ['label' => 'Remaining backend gap', 'value' => 'Role assignment, matrix editing, and shop-scoped authorization writes still remain preview-only for this workspace'],
        ];
    }

    private function prependCardTypeLatestFlowTimelineItem(array $page): array
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

    private function appendCardTypeLatestFlowDependencyStatus(array $page): array
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
