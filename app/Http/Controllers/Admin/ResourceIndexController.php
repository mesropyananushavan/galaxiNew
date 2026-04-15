<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ResourceIndexController extends Controller
{
    public function __invoke(string $resource): View
    {
        $pages = config('admin-pages');

        // Shared shell defaults stay config-driven so the layered resource-page
        // composition can evolve without growing controller conditionals.
        $defaults = $this->defaults(config('admin-resource-page-defaults', []));

        abort_unless(is_array($pages) && array_key_exists($resource, $pages), 404);

        return view('admin.resource-index', $pages[$resource] + [
            'resourceKey' => $resource,
            'actions' => $this->actions($pages[$resource]['actions'] ?? []),
            'metrics' => $this->metrics($pages[$resource]['metrics'] ?? []),
            'table' => $this->table($pages[$resource]['table'] ?? []),
            'notice' => $this->notice($pages[$resource]['notice'] ?? []),
            'readinessChecklist' => $this->labeledStatusItems($pages[$resource]['readinessChecklist'] ?? []),
            'activityTimeline' => $this->timelineItems($pages[$resource]['activityTimeline'] ?? []),
            'dependencyStatus' => $this->keyValueItems($pages[$resource]['dependencyStatus'] ?? []),
            'legacyMapping' => $this->keyValueItems($pages[$resource]['legacyMapping'] ?? []),
            'implementationHandoff' => $this->summaryListBlock($pages[$resource]['implementationHandoff'] ?? [], 'steps'),
            'operationalNextSlice' => $this->summaryListBlock($pages[$resource]['operationalNextSlice'] ?? [], 'steps'),
            'operatorChecklist' => $this->summaryListBlock($pages[$resource]['operatorChecklist'] ?? []),
            'escalationGuide' => $this->summaryListBlock($pages[$resource]['escalationGuide'] ?? []),
            'shiftHandoff' => $this->summaryListBlock($pages[$resource]['shiftHandoff'] ?? []),
            'resourceBlocks' => $this->resourceBlocks($defaults),
            'phase' => $this->phase($defaults),
            'pageRationale' => $this->pageRationale($defaults),
        ]);
    }

    private function defaults(mixed $defaults): array
    {
        return is_array($defaults) ? $defaults : [];
    }

    private function actions(mixed $actions): array
    {
        if (! is_array($actions)) {
            return [];
        }

        return array_values(array_filter(
            $actions,
            fn (mixed $action): bool => is_array($action)
                && is_string($action['label'] ?? null)
                && (! array_key_exists('tone', $action) || is_string($action['tone']))
        ));
    }

    private function metrics(mixed $metrics): array
    {
        if (! is_array($metrics)) {
            return [];
        }

        return array_values(array_filter(
            $metrics,
            fn (mixed $metric): bool => is_array($metric)
                && is_string($metric['label'] ?? null)
                && is_string($metric['value'] ?? null)
        ));
    }

    private function table(mixed $table): array
    {
        if (! is_array($table)) {
            return [];
        }

        return [
            'filters' => $this->stringList($table['filters'] ?? []),
            'columns' => $this->stringList($table['columns'] ?? []),
            'rows' => array_values(array_filter(
                $table['rows'] ?? [],
                fn (mixed $row): bool => is_array($row)
                    && array_values(array_filter($row, fn (mixed $cell): bool => is_string($cell))) === array_values($row)
            )),
        ];
    }

    private function notice(mixed $notice): array
    {
        if (! is_array($notice)) {
            return [];
        }

        return is_string($notice['title'] ?? null) && is_string($notice['description'] ?? null)
            ? [
                'title' => $notice['title'],
                'description' => $notice['description'],
            ]
            : [];
    }

    private function labeledStatusItems(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        return array_values(array_filter(
            $items,
            fn (mixed $item): bool => is_array($item)
                && is_string($item['status'] ?? null)
                && is_string($item['label'] ?? null)
        ));
    }

    private function timelineItems(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        return array_values(array_filter(
            $items,
            fn (mixed $item): bool => is_array($item)
                && is_string($item['title'] ?? null)
                && is_string($item['time'] ?? null)
                && is_string($item['description'] ?? null)
        ));
    }

    private function keyValueItems(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        return array_values(array_filter(
            $items,
            fn (mixed $item): bool => is_array($item)
                && is_string($item['label'] ?? null)
                && is_string($item['value'] ?? null)
        ));
    }

    private function summaryListBlock(mixed $block, string $itemsKey = 'items'): array
    {
        if (! is_array($block)) {
            return [];
        }

        return is_string($block['summary'] ?? null)
            ? [
                'summary' => $block['summary'],
                $itemsKey => $this->stringList($block[$itemsKey] ?? []),
            ]
            : [];
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

    private function stringList(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        return array_values(array_filter(
            $items,
            fn (mixed $item): bool => is_string($item)
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
