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

        return array_values(array_filter(array_map(function (mixed $action): ?array {
            if (! is_array($action)
                || ! is_string($action['label'] ?? null)
                || (array_key_exists('tone', $action) && ! is_string($action['tone']))
                || (array_key_exists('href', $action) && ! is_string($action['href']))
                || (array_key_exists('method', $action) && ! is_string($action['method']))
            ) {
                return null;
            }

            return array_filter([
                'label' => $action['label'],
                'tone' => $action['tone'] ?? null,
                'href' => $action['href'] ?? null,
                'method' => $action['method'] ?? null,
            ], fn (mixed $value): bool => $value !== null);
        }, $actions)));
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

        return array_values(array_filter(array_map(function (mixed $row): ?array {
            if (! is_array($row)) {
                return null;
            }

            $cells = array_values(array_filter(array_map(function (mixed $cell): ?array {
                if (is_string($cell)) {
                    return ['label' => $cell];
                }

                if (! is_array($cell)
                    || ! is_string($cell['label'] ?? null)
                    || (array_key_exists('href', $cell) && ! is_string($cell['href']))
                    || (array_key_exists('method', $cell) && ! is_string($cell['method']))
                ) {
                    return null;
                }

                return array_filter([
                    'label' => $cell['label'],
                    'href' => $cell['href'] ?? null,
                    'method' => $cell['method'] ?? null,
                ], fn (mixed $value): bool => $value !== null);
            }, $row)));

            return count($cells) === count($row)
                ? $cells
                : null;
        }, $rows)));
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
            'selectedRecordSummary' => $this->keyValueItems($page['selectedRecordSummary'] ?? []),
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
            'method' => $this->liveFormMethod($form['method'] ?? null),
            'action' => $form['action'],
            'formAttributes' => $this->liveFormAttributes($form['formAttributes'] ?? []),
            'submitLabel' => $form['submitLabel'],
            'submitAttributes' => $this->liveFormAttributes($form['submitAttributes'] ?? []),
            'cancelAction' => $this->liveFormCancelAction($form['cancelAction'] ?? null),
            'cancelAttributes' => $this->liveFormAttributes($form['cancelAttributes'] ?? []),
            'fields' => array_values(array_filter(array_map(function (mixed $field): ?array {
                if (! is_array($field)
                    || ! is_string($field['name'] ?? null)
                    || ! is_string($field['type'] ?? null)
                ) {
                    return null;
                }

                $type = $field['type'];

                if ($type !== 'hidden' && ! is_string($field['label'] ?? null)) {
                    return null;
                }

                return [
                    'name' => $field['name'],
                    'label' => is_string($field['label'] ?? null) ? $field['label'] : '',
                    'type' => $type,
                    'value' => $this->liveFormValue($field['value'] ?? null),
                    'required' => is_bool($field['required'] ?? null) ? $field['required'] : false,
                    'autofocus' => is_bool($field['autofocus'] ?? null) ? $field['autofocus'] : false,
                    'placeholder' => is_string($field['placeholder'] ?? null) ? $field['placeholder'] : null,
                    'help' => is_string($field['help'] ?? null) ? $field['help'] : null,
                    'options' => $this->liveFormOptions($field['options'] ?? []),
                    'attributes' => $this->liveFormAttributes($field['attributes'] ?? []),
                    'wrapperAttributes' => $this->liveFormAttributes($field['wrapperAttributes'] ?? []),
                ];
            }, is_array($form['fields'] ?? null) ? $form['fields'] : []))),
        ];
    }

    private function liveFormMethod(mixed $method): string
    {
        if (! is_string($method)) {
            return 'POST';
        }

        $method = strtoupper($method);

        return in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)
            ? $method
            : 'POST';
    }

    private function liveFormValue(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return '';
    }

    private function liveFormCancelAction(mixed $action): ?array
    {
        if (! is_array($action)
            || ! is_string($action['label'] ?? null)
            || ! is_string($action['href'] ?? null)
        ) {
            return null;
        }

        return [
            'label' => $action['label'],
            'href' => $action['href'],
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
            ) {
                return null;
            }

            $value = $this->liveFormValue($option['value'] ?? null);

            if ($value === '') {
                return null;
            }

            return [
                'label' => $option['label'],
                'value' => $value,
            ];
        }, $options)));
    }

    private function liveFormAttributes(mixed $attributes): array
    {
        if (! is_array($attributes)) {
            return [];
        }

        return array_filter($attributes, function (mixed $value, mixed $key): bool {
            if (! is_string($key)) {
                return false;
            }

            if (is_string($value)) {
                return true;
            }

            return $value === true;
        }, ARRAY_FILTER_USE_BOTH);
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
