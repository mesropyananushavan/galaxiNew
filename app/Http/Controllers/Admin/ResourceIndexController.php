<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
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
            default => $page,
        };
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
            [
                'label' => $card->number,
                'href' => route('admin.cards.index', ['card' => $card->id], absolute: false),
            ],
            $card->holder?->full_name ?? 'Unassigned',
            $card->type?->name ?? 'Unknown',
            $card->shop?->name ?? 'Unassigned',
            $card->status,
            $card->activated_at?->format('Y-m-d') ?? '—',
        ])->all();

        $latestCard = $cards->sortByDesc('id')->first();

        if ($latestCard !== null) {
            $actions = is_array($page['actions'] ?? null) ? $page['actions'] : [];

            $page['actions'] = [
                ...$actions,
                [
                    'label' => 'Review latest saved card',
                    'tone' => 'secondary',
                    'href' => route('admin.cards.index', ['card' => $latestCard->id], absolute: false),
                ],
            ];
        }

        $selectedCardId = request()->integer('card');

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
            [
                'label' => $cardHolder->full_name,
                'href' => route('admin.cardholders.index', ['cardholder' => $cardHolder->id], absolute: false),
            ],
            $cardHolder->phone ?? '—',
            $cardHolder->shop?->name ?? 'Unassigned',
            (string) $cardHolder->cards_count,
            $cardHolder->is_active ? 'active' : 'inactive',
            $cardHolder->updated_at?->format('Y-m-d') ?? '—',
        ])->all();

        $latestCardHolder = $cardHolders->sortByDesc('id')->first();

        if ($latestCardHolder !== null) {
            $actions = is_array($page['actions'] ?? null) ? $page['actions'] : [];

            $page['actions'] = [
                ...$actions,
                [
                    'label' => 'Review latest saved holder',
                    'tone' => 'secondary',
                    'href' => route('admin.cardholders.index', ['cardholder' => $latestCardHolder->id], absolute: false),
                ],
            ];
        }

        $selectedCardHolderId = request()->integer('cardholder');

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
            [
                'label' => $shop->name,
                'href' => route('admin.shops.index', ['shop' => $shop->id], absolute: false),
            ],
            $shop->code,
            $shop->users->first()?->name ?? 'Unassigned',
            (string) $shop->card_holders_count,
            (string) $shop->cards_count,
            $shop->is_active ? 'active' : 'paused',
        ])->all();

        $latestShop = $shops->sortByDesc('id')->first();

        if ($latestShop !== null) {
            $actions = is_array($page['actions'] ?? null) ? $page['actions'] : [];

            $page['actions'] = [
                ...$actions,
                [
                    'label' => 'Review latest saved shop',
                    'tone' => 'secondary',
                    'href' => route('admin.shops.index', ['shop' => $latestShop->id], absolute: false),
                ],
            ];
        }

        $selectedShopId = request()->integer('shop');

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
            $actions = is_array($page['actions'] ?? null) ? $page['actions'] : [];

            $page['actions'] = [
                ...$actions,
                [
                    'label' => 'Edit latest saved type',
                    'tone' => 'secondary',
                    'href' => route('admin.card-types.index', ['cardType' => $latestCardType->id], absolute: false).'#live-form',
                ],
            ];
        }

        $selectedCardTypeId = request()->integer('cardType');

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

        if (is_string(session('status'))) {
            $page['selectedRecordSummary'][] = [
                'label' => 'Latest flow result',
                'value' => session('status'),
            ];
        }

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

        $page['dependencyStatus'] = [
            ['label' => 'Selected record', 'value' => $selectedCardType->name],
            ['label' => 'Edit flow state', 'value' => 'Shared live form is running in request-driven PATCH mode'],
            ['label' => 'Current status posture', 'value' => $selectedCardType->is_active ? 'Active tiers should stay stable unless parity checks are complete' : 'Draft tiers are the safe place for parity-first validation and copy changes'],
            ['label' => 'Rule-import posture', 'value' => $selectedCardType->is_active ? 'Keep imports blocked until active-tier accrual parity is verified' : 'Imports can be reviewed in draft mode, but they are still not safe to enable yet'],
            ['label' => 'Publish posture', 'value' => $selectedCardType->is_active ? 'Live tiers need parity confirmation before further publish-style changes' : 'Draft tiers should stay unpublished until legacy behavior is mapped more explicitly'],
            ['label' => 'Action gating', 'value' => $selectedCardType->is_active ? 'Allow small state corrections only, keep publish-like and import actions gated' : 'Allow draft-safe edits and validation only, keep live-facing actions gated'],
            ['label' => 'Remaining backend gap', 'value' => 'Publish logic and rule-import parity still remain preview-only for this tier'],
        ];

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
