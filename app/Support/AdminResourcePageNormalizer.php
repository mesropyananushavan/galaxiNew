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
            'rows' => $this->tableRows($table['rows'] ?? []),
        ];
    }

    private function tableRows(mixed $rows): array
    {
        if (! is_array($rows)) {
            return [];
        }

        return array_values(array_filter(
            $rows,
            fn (mixed $row): bool => is_array($row)
                && array_values(array_filter($row, fn (mixed $cell): bool => is_string($cell))) === array_values($row)
        ));
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
            fn (mixed $item): bool => $this->isKeyValueItem($item)
        ));
    }

    private function isKeyValueItem(mixed $item): bool
    {
        return is_array($item)
            && is_string($item['label'] ?? null)
            && is_string($item['value'] ?? null);
    }

    private function primaryPageBlocks(array $page): array
    {
        return [
            'actions' => $this->actions($page['actions'] ?? []),
            'metrics' => $this->metrics($page['metrics'] ?? []),
            'table' => $this->table($page['table'] ?? []),
            'emptyState' => $this->emptyState($page['emptyState'] ?? []),
            'form' => $this->form($page['form'] ?? []),
            'liveForm' => $this->liveForm($page['liveForm'] ?? []),
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
                $itemsKey => $this->summaryListItems($block[$itemsKey] ?? []),
            ]
            : [];
    }

    private function summaryListItems(mixed $items): array
    {
        return $this->stringList($items);
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

        return [
            'title' => $form['title'],
            'actions' => $this->actions($form['actions'] ?? []),
            'sections' => $this->formSections($form['sections'] ?? []),
        ];
    }

    private function liveForm(mixed $form): array
    {
        if (! is_array($form)
            || ! is_string($form['title'] ?? null)
            || ! is_string($form['action'] ?? null)
            || ! is_string($form['submitLabel'] ?? null)
        ) {
            return [];
        }

        return [
            'title' => $form['title'],
            'description' => is_string($form['description'] ?? null) ? $form['description'] : null,
            'action' => $form['action'],
            'submitLabel' => $form['submitLabel'],
            'fields' => array_values(array_filter(array_map(function (mixed $field): ?array {
                if (! is_array($field)
                    || ! is_string($field['name'] ?? null)
                    || ! is_string($field['label'] ?? null)
                    || ! is_string($field['type'] ?? null)
                ) {
                    return null;
                }

                return [
                    'name' => $field['name'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'value' => is_string($field['value'] ?? null) ? $field['value'] : '',
                    'required' => is_bool($field['required'] ?? null) ? $field['required'] : false,
                    'placeholder' => is_string($field['placeholder'] ?? null) ? $field['placeholder'] : null,
                    'help' => is_string($field['help'] ?? null) ? $field['help'] : null,
                    'options' => $this->liveFormOptions($field['options'] ?? []),
                    'attributes' => $this->liveFormAttributes($field['attributes'] ?? []),
                ];
            }, is_array($form['fields'] ?? null) ? $form['fields'] : []))),
        ];
    }

    private function liveFormOptions(mixed $options): array
    {
        if (! is_array($options)) {
            return [];
        }

        return array_values(array_filter(array_map(function (mixed $option): ?array {
            if (! is_array($option)
                || ! is_string($option['label'] ?? null)
                || ! is_string($option['value'] ?? null)
            ) {
                return null;
            }

            return [
                'label' => $option['label'],
                'value' => $option['value'],
            ];
        }, $options)));
    }

    private function liveFormAttributes(mixed $attributes): array
    {
        if (! is_array($attributes)) {
            return [];
        }

        return array_filter($attributes, fn (mixed $value, mixed $key): bool => is_string($key) && is_string($value), ARRAY_FILTER_USE_BOTH);
    }

    private function formSections(mixed $sections): array
    {
        if (! is_array($sections)) {
            return [];
        }

        return array_values(array_filter(array_map(function (mixed $section): ?array {
            if (! is_array($section) || ! is_string($section['title'] ?? null) || ! is_array($section['fields'] ?? null)) {
                return null;
            }

            return [
                'title' => $section['title'],
                'help' => is_string($section['help'] ?? null) ? $section['help'] : null,
                'actions' => $this->actions($section['actions'] ?? []),
                'fields' => $this->formFields($section['fields']),
            ];
        }, $sections)));
    }

    private function formFields(mixed $fields): array
    {
        if (! is_array($fields)) {
            return [];
        }

        return array_values(array_filter(
            $fields,
            fn (mixed $field): bool => is_array($field)
                && is_string($field['label'] ?? null)
                && is_string($field['value'] ?? null)
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
}
