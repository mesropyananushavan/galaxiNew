<?php

namespace App\Support;

class AdminResourcePageNormalizer
{
    public function normalize(array $page): array
    {
        return $this->primaryPageBlocks($page)
            + $this->previewContextBlocks($page)
            + $this->summaryListBlocks($page)
            + $this->keyValueBlocks($page);
    }

    public function actions(mixed $actions): array
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

    private function primaryPageBlocks(array $page): array
    {
        return [
            'actions' => $this->actions($page['actions'] ?? []),
            'metrics' => $this->metrics($page['metrics'] ?? []),
            'table' => $this->table($page['table'] ?? []),
            'emptyState' => $this->emptyState($page['emptyState'] ?? []),
            'form' => $this->form($page['form'] ?? []),
        ];
    }

    private function previewContextBlocks(array $page): array
    {
        return [
            'notice' => $this->notice($page['notice'] ?? []),
            'readinessChecklist' => $this->labeledStatusItems($page['readinessChecklist'] ?? []),
            'activityTimeline' => $this->timelineItems($page['activityTimeline'] ?? []),
        ];
    }

    private function summaryListBlocks(array $page): array
    {
        return [
            'implementationHandoff' => $this->summaryListBlock($page['implementationHandoff'] ?? [], 'steps'),
            'operationalNextSlice' => $this->summaryListBlock($page['operationalNextSlice'] ?? [], 'steps'),
            'operatorChecklist' => $this->summaryListBlock($page['operatorChecklist'] ?? []),
            'escalationGuide' => $this->summaryListBlock($page['escalationGuide'] ?? []),
            'shiftHandoff' => $this->summaryListBlock($page['shiftHandoff'] ?? []),
            'openIssues' => $this->summaryListBlock($page['openIssues'] ?? []),
        ];
    }

    private function keyValueBlocks(array $page): array
    {
        return [
            'dependencyStatus' => $this->keyValueItems($page['dependencyStatus'] ?? []),
            'legacyMapping' => $this->keyValueItems($page['legacyMapping'] ?? []),
        ];
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

    private function emptyState(mixed $emptyState): array
    {
        if (! is_array($emptyState)) {
            return [];
        }

        return is_string($emptyState['title'] ?? null) && is_string($emptyState['description'] ?? null)
            ? [
                'title' => $emptyState['title'],
                'description' => $emptyState['description'],
                'actions' => $this->actions($emptyState['actions'] ?? []),
            ]
            : [];
    }

    private function form(mixed $form): array
    {
        if (! is_array($form) || ! is_string($form['title'] ?? null)) {
            return [];
        }

        $sections = is_array($form['sections'] ?? null) ? $form['sections'] : [];

        return [
            'title' => $form['title'],
            'actions' => $this->actions($form['actions'] ?? []),
            'sections' => array_values(array_filter(array_map(function (mixed $section): ?array {
                if (! is_array($section) || ! is_string($section['title'] ?? null) || ! is_array($section['fields'] ?? null)) {
                    return null;
                }

                return [
                    'title' => $section['title'],
                    'help' => is_string($section['help'] ?? null) ? $section['help'] : null,
                    'actions' => $this->actions($section['actions'] ?? []),
                    'fields' => array_values(array_filter(
                        $section['fields'],
                        fn (mixed $field): bool => is_array($field)
                            && is_string($field['label'] ?? null)
                            && is_string($field['value'] ?? null)
                    )),
                ];
            }, $sections))),
        ];
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
}
