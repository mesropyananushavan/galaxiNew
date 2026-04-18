<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        if (is_array($page['liveForm'] ?? null)) {
            if (is_string($page['liveForm']['actionRoute'] ?? null)) {
                $page['liveForm']['action'] = route(
                    $page['liveForm']['actionRoute'],
                    $this->liveFormActionParameters($page['liveForm']['actionRouteParameters'] ?? []),
                    absolute: false,
                );
            }

            if (is_string($page['liveForm']['cancelRoute'] ?? null)) {
                $page['liveForm']['cancelAction'] = [
                    'label' => is_string($page['liveForm']['cancelLabel'] ?? null) ? $page['liveForm']['cancelLabel'] : 'Back',
                    'href' => route(
                        $page['liveForm']['cancelRoute'],
                        $this->liveFormActionParameters($page['liveForm']['cancelRouteParameters'] ?? []),
                        absolute: false,
                    ),
                ];
            }
        }

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

        return $value;
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
