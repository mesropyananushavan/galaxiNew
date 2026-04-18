<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardType;
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

        return view('admin.resource-index', $page + $normalizedPage + [
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
        if ($resource !== 'card-types') {
            return $page;
        }

        return $this->enrichCardTypesPage($page);
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
