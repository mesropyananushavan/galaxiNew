<?php

namespace Tests\Unit;

use App\Support\AdminResourcePageNormalizer;
use Tests\TestCase;

class AdminResourcePageNormalizerTest extends TestCase
{
    public function test_normalize_returns_empty_safe_defaults_for_missing_or_malformed_top_level_blocks(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'actions' => 'invalid-actions',
            'metrics' => 'invalid-metrics',
            'table' => 'invalid-table',
            'notice' => 'invalid-notice',
            'readinessChecklist' => 'invalid-readiness',
            'activityTimeline' => 'invalid-timeline',
            'dependencyStatus' => 'invalid-dependency-status',
            'legacyMapping' => 'invalid-legacy-mapping',
            'selectedRecordSummary' => 'invalid-selected-record-summary',
            'implementationHandoff' => 'invalid-handoff',
            'operationalNextSlice' => 'invalid-next-slice',
            'operatorChecklist' => 'invalid-operator-checklist',
            'escalationGuide' => 'invalid-escalation-guide',
            'shiftHandoff' => 'invalid-shift-handoff',
            'openIssues' => 'invalid-open-issues',
            'emptyState' => 'invalid-empty-state',
            'form' => 'invalid-form',
        ]);

        $this->assertSame([], $normalized['actions']);
        $this->assertSame([], $normalized['metrics']);
        $this->assertSame([], $normalized['table']);
        $this->assertSame([], $normalized['notice']);
        $this->assertSame([], $normalized['readinessChecklist']);
        $this->assertSame([], $normalized['activityTimeline']);
        $this->assertSame([], $normalized['dependencyStatus']);
        $this->assertSame([], $normalized['legacyMapping']);
        $this->assertSame([], $normalized['selectedRecordSummary']);
        $this->assertSame([], $normalized['implementationHandoff']);
        $this->assertSame([], $normalized['operationalNextSlice']);
        $this->assertSame([], $normalized['operatorChecklist']);
        $this->assertSame([], $normalized['escalationGuide']);
        $this->assertSame([], $normalized['shiftHandoff']);
        $this->assertSame([], $normalized['openIssues']);
        $this->assertSame([], $normalized['emptyState']);
        $this->assertSame([], $normalized['form']);
        $this->assertSame([], $normalized['liveForm']);
    }

    public function test_normalize_returns_empty_rows_when_table_rows_block_is_malformed(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'table' => [
                'filters' => ['Active only'],
                'columns' => ['Code', 'Name'],
                'rows' => 'invalid-rows',
            ],
        ]);

        $this->assertSame(['Active only'], $normalized['table']['filters']);
        $this->assertSame(['Code', 'Name'], $normalized['table']['columns']);
        $this->assertSame([], $normalized['table']['rows']);
    }

    public function test_normalize_keeps_table_cells_with_optional_links(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'table' => [
                'columns' => ['Type', 'Status'],
                'rows' => [
                    [
                        ['label' => 'Galaxy Prime', 'href' => '/admin/card-types?cardType=1#live-form'],
                        'draft',
                    ],
                    [
                        ['label' => 'Broken link', 'href' => ['invalid-href']],
                        'active',
                    ],
                ],
            ],
        ]);

        $this->assertSame([
            [
                ['label' => 'Galaxy Prime', 'href' => '/admin/card-types?cardType=1#live-form'],
                ['label' => 'draft'],
            ],
        ], $normalized['table']['rows']);
    }

    public function test_normalize_keeps_table_cells_with_optional_methods(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'table' => [
                'columns' => ['Type', 'Status action'],
                'rows' => [
                    [
                        'Galaxy Prime',
                        ['label' => 'Move to draft', 'href' => '/admin/card-types/7/toggle-status', 'method' => 'PATCH'],
                    ],
                    [
                        'Broken method',
                        ['label' => 'Broken action', 'href' => '/admin/card-types/7/toggle-status', 'method' => ['PATCH']],
                    ],
                ],
            ],
        ]);

        $this->assertSame([
            [
                ['label' => 'Galaxy Prime'],
                ['label' => 'Move to draft', 'href' => '/admin/card-types/7/toggle-status', 'method' => 'PATCH'],
            ],
        ], $normalized['table']['rows']);
    }

    public function test_normalize_keeps_actions_with_disabled_state_and_reason(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'actions' => [
                [
                    'label' => 'Import rules',
                    'tone' => 'secondary',
                    'disabled' => true,
                    'disabledReason' => 'Blocked until parity review is complete.',
                ],
                [
                    'label' => 'Broken action',
                    'disabled' => 'yes',
                ],
            ],
        ]);

        $this->assertSame([
            [
                'label' => 'Import rules',
                'tone' => 'secondary',
                'disabled' => true,
                'disabledReason' => 'Blocked until parity review is complete.',
            ],
        ], $normalized['actions']);
    }

    public function test_normalize_returns_empty_sections_when_form_sections_block_is_malformed(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'form' => [
                'title' => 'Create or edit role',
                'actions' => [
                    ['label' => 'Publish role', 'tone' => 'primary'],
                ],
                'sections' => 'invalid-sections',
            ],
        ]);

        $this->assertSame('Create or edit role', $normalized['form']['title']);
        $this->assertSame([
            ['label' => 'Publish role', 'tone' => 'primary'],
        ], $normalized['form']['actions']);
        $this->assertSame([], $normalized['form']['sections']);
    }

    public function test_normalize_live_form_keeps_valid_fields_and_ignores_malformed_entries(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Create card type in Laravel',
                'action' => '/admin/card-types',
                'submitLabel' => 'Create card type',
                'fields' => [
                    ['name' => 'name', 'label' => 'Type name', 'type' => 'text', 'value' => 'Gold'],
                    ['label' => 'Missing name', 'type' => 'text'],
                ],
            ],
        ]);

        $this->assertSame('Create card type in Laravel', $normalized['liveForm']['title']);
        $this->assertSame('POST', $normalized['liveForm']['method']);
        $this->assertSame('/admin/card-types', $normalized['liveForm']['action']);
        $this->assertNull($normalized['liveForm']['cancelAction']);
        $this->assertSame('Create card type', $normalized['liveForm']['submitLabel']);
        $this->assertCount(1, $normalized['liveForm']['fields']);
    }

    public function test_normalize_live_form_keeps_valid_html_attributes_and_ignores_malformed_entries(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Create card type in Laravel',
                'action' => '/admin/card-types',
                'submitLabel' => 'Create card type',
                'fields' => [
                    [
                        'name' => 'points_rate',
                        'label' => 'Points rate',
                        'type' => 'number',
                        'value' => '1.50',
                        'required' => true,
                        'autofocus' => true,
                        'placeholder' => '1.50',
                        'help' => 'Decimal multiplier for accrual.',
                        'attributes' => [
                            'step' => '0.01',
                            'min' => '0',
                            'inputmode' => 'decimal',
                            'readonly' => true,
                            'spellcheck' => false,
                            0 => 'broken',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertTrue($normalized['liveForm']['fields'][0]['required']);
        $this->assertTrue($normalized['liveForm']['fields'][0]['autofocus']);
        $this->assertSame('1.50', $normalized['liveForm']['fields'][0]['placeholder']);
        $this->assertSame('Decimal multiplier for accrual.', $normalized['liveForm']['fields'][0]['help']);
        $this->assertSame([
            'step' => '0.01',
            'min' => '0',
            'inputmode' => 'decimal',
            'readonly' => true,
        ], $normalized['liveForm']['fields'][0]['attributes']);
    }

    public function test_normalize_live_form_defaults_invalid_method_to_post(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Update card type in Laravel',
                'method' => 'trace',
                'action' => '/admin/card-types/gold',
                'submitLabel' => 'Update card type',
                'fields' => [
                    ['name' => 'name', 'label' => 'Type name', 'type' => 'text', 'value' => 'Gold'],
                ],
            ],
        ]);

        $this->assertSame('POST', $normalized['liveForm']['method']);
    }

    public function test_normalize_live_form_keeps_valid_cancel_action(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Create card type in Laravel',
                'action' => '/admin/card-types',
                'submitLabel' => 'Create card type',
                'cancelAction' => [
                    'label' => 'Back to catalog',
                    'href' => '/admin/card-types',
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'Type name', 'type' => 'text', 'value' => 'Gold'],
                ],
            ],
        ]);

        $this->assertSame([
            'label' => 'Back to catalog',
            'href' => '/admin/card-types',
        ], $normalized['liveForm']['cancelAction']);
    }

    public function test_normalize_live_form_keeps_valid_submit_attributes(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Create card type in Laravel',
                'action' => '/admin/card-types',
                'submitLabel' => 'Create card type',
                'submitAttributes' => [
                    'data-mode' => 'update',
                    'disabled' => true,
                    'aria-controls' => 'backend-flow-status',
                    'aria-hidden' => false,
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'Type name', 'type' => 'text', 'value' => 'Gold'],
                ],
            ],
        ]);

        $this->assertSame([
            'data-mode' => 'update',
            'disabled' => true,
            'aria-controls' => 'backend-flow-status',
        ], $normalized['liveForm']['submitAttributes']);
    }

    public function test_normalize_live_form_keeps_valid_form_attributes(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Create card type in Laravel',
                'action' => '/admin/card-types',
                'submitLabel' => 'Create card type',
                'formAttributes' => [
                    'data-form-mode' => 'update',
                    'novalidate' => true,
                    'aria-controls' => 'backend-flow-status',
                    'aria-hidden' => false,
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'Type name', 'type' => 'text', 'value' => 'Gold'],
                ],
            ],
        ]);

        $this->assertSame([
            'data-form-mode' => 'update',
            'novalidate' => true,
            'aria-controls' => 'backend-flow-status',
        ], $normalized['liveForm']['formAttributes']);
    }

    public function test_normalize_live_form_keeps_valid_cancel_attributes(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Create card type in Laravel',
                'action' => '/admin/card-types',
                'submitLabel' => 'Create card type',
                'cancelAction' => [
                    'label' => 'Back to catalog',
                    'href' => '/admin/card-types',
                ],
                'cancelAttributes' => [
                    'data-cancel-mode' => 'update',
                    'download' => true,
                    'aria-controls' => 'live-form',
                    'aria-hidden' => false,
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'Type name', 'type' => 'text', 'value' => 'Gold'],
                ],
            ],
        ]);

        $this->assertSame([
            'data-cancel-mode' => 'update',
            'download' => true,
            'aria-controls' => 'live-form',
        ], $normalized['liveForm']['cancelAttributes']);
    }

    public function test_normalize_live_form_keeps_valid_field_wrapper_attributes(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Create card type in Laravel',
                'action' => '/admin/card-types',
                'submitLabel' => 'Create card type',
                'fields' => [
                    [
                        'name' => 'name',
                        'label' => 'Type name',
                        'type' => 'text',
                        'value' => 'Gold',
                        'wrapperAttributes' => [
                            'data-field-mode' => 'edit',
                            'hidden' => true,
                            'aria-hidden' => 'true',
                            'aria-busy' => false,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame([
            'data-field-mode' => 'edit',
            'hidden' => true,
            'aria-hidden' => 'true',
        ], $normalized['liveForm']['fields'][0]['wrapperAttributes']);
    }

    public function test_normalize_live_form_keeps_valid_select_options_and_ignores_malformed_entries(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Create card type in Laravel',
                'action' => '/admin/card-types',
                'submitLabel' => 'Create card type',
                'fields' => [
                    [
                        'name' => 'is_active',
                        'label' => 'Status',
                        'type' => 'select',
                        'value' => '1',
                        'options' => [
                            ['label' => 'Active', 'value' => true],
                            ['label' => 'Draft', 'value' => 0],
                            ['label' => 'Archived', 'value' => 2],
                            ['label' => 'Broken option'],
                            ['label' => 'Empty option', 'value' => null],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame([
            ['label' => 'Active', 'value' => '1'],
            ['label' => 'Draft', 'value' => '0'],
            ['label' => 'Archived', 'value' => '2'],
        ], $normalized['liveForm']['fields'][0]['options']);
    }

    public function test_normalize_live_form_keeps_hidden_fields_without_requiring_labels(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Update card type in Laravel',
                'action' => '/admin/card-types/1',
                'submitLabel' => 'Update card type',
                'fields' => [
                    [
                        'name' => 'mode',
                        'type' => 'hidden',
                        'value' => 'edit',
                    ],
                    [
                        'name' => 'name',
                        'label' => 'Type name',
                        'type' => 'text',
                        'value' => 'Galaxy Prime',
                    ],
                ],
            ],
        ]);

        $this->assertSame('hidden', $normalized['liveForm']['fields'][0]['type']);
        $this->assertSame('', $normalized['liveForm']['fields'][0]['label']);
        $this->assertSame('edit', $normalized['liveForm']['fields'][0]['value']);
        $this->assertSame('text', $normalized['liveForm']['fields'][1]['type']);
    }

    public function test_normalize_live_form_casts_scalar_values_to_strings(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'liveForm' => [
                'title' => 'Update card type in Laravel',
                'action' => '/admin/card-types/1',
                'submitLabel' => 'Update card type',
                'fields' => [
                    [
                        'name' => 'mode',
                        'type' => 'hidden',
                        'value' => 0,
                    ],
                    [
                        'name' => 'points_rate',
                        'label' => 'Points rate',
                        'type' => 'number',
                        'value' => 1.5,
                    ],
                    [
                        'name' => 'is_active',
                        'label' => 'Status',
                        'type' => 'select',
                        'value' => true,
                        'options' => [
                            ['label' => 'Active', 'value' => '1'],
                            ['label' => 'Draft', 'value' => '0'],
                        ],
                    ],
                    [
                        'name' => 'sort_order',
                        'label' => 'Sort order',
                        'type' => 'number',
                        'value' => 2,
                    ],
                ],
            ],
        ]);

        $this->assertSame('0', $normalized['liveForm']['fields'][0]['value']);
        $this->assertSame('1.5', $normalized['liveForm']['fields'][1]['value']);
        $this->assertSame('1', $normalized['liveForm']['fields'][2]['value']);
        $this->assertSame('2', $normalized['liveForm']['fields'][3]['value']);
    }

    public function test_normalize_keeps_valid_table_rows_when_neighboring_rows_are_malformed(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'table' => [
                'columns' => ['Code', 'Name'],
                'rows' => [
                    ['GC-001', 'Galaxy Classic'],
                    ['GC-002', 42],
                    'invalid-row',
                ],
            ],
        ]);

        $this->assertSame(['Code', 'Name'], $normalized['table']['columns']);
        $this->assertSame([
            ['GC-001', 'Galaxy Classic'],
        ], $normalized['table']['rows']);
    }

    public function test_normalize_keeps_valid_form_sections_when_other_section_fields_are_malformed(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'form' => [
                'title' => 'Create or edit role',
                'sections' => [
                    [
                        'title' => 'Role identity',
                        'fields' => [
                            ['label' => 'Role name', 'value' => 'Shop Manager'],
                            ['label' => 'Scope'],
                        ],
                    ],
                    [
                        'title' => 'Broken section',
                        'fields' => 'invalid-fields',
                    ],
                ],
            ],
        ]);

        $this->assertCount(1, $normalized['form']['sections']);
        $this->assertSame('Role identity', $normalized['form']['sections'][0]['title']);
        $this->assertSame([
            ['label' => 'Role name', 'value' => 'Shop Manager'],
        ], $normalized['form']['sections'][0]['fields']);
    }

    public function test_normalize_keeps_valid_summary_steps_when_neighboring_steps_are_malformed(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'implementationHandoff' => [
                'summary' => 'Start with a minimal role create path.',
                'steps' => [
                    'Persist a minimal role record.',
                    ['invalid-step'],
                    42,
                ],
            ],
        ]);

        $this->assertSame('Start with a minimal role create path.', $normalized['implementationHandoff']['summary']);
        $this->assertSame([
            'Persist a minimal role record.',
        ], $normalized['implementationHandoff']['steps']);
    }

    public function test_normalize_keeps_valid_key_value_items_when_neighboring_items_are_malformed(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'dependencyStatus' => [
                ['label' => 'Domain model', 'value' => 'Role and Permission models exist'],
                ['label' => 'Missing value'],
                'invalid-item',
            ],
        ]);

        $this->assertSame([
            ['label' => 'Domain model', 'value' => 'Role and Permission models exist'],
        ], $normalized['dependencyStatus']);
    }

    public function test_normalize_filters_malformed_nested_page_metadata(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'actions' => [
                ['label' => 'Publish role', 'tone' => 'primary'],
                ['tone' => 'secondary'],
                'invalid-action-entry',
            ],
            'notice' => [
                'title' => 'Role publishing is still preview-only',
                'description' => 'Preview actions remain structural until Laravel handlers exist.',
            ],
            'readinessChecklist' => [
                ['status' => 'ready', 'label' => 'Legacy role boundaries mapped'],
                ['label' => 'Missing status'],
            ],
            'activityTimeline' => [
                ['title' => 'Shop Manager bundle reviewed', 'time' => 'Today, 14:20', 'description' => 'Role boundaries were compared against the old Galaxy staff model.'],
                ['title' => 'Cashier draft held back', 'time' => ['invalid-time'], 'description' => 'Draft role remains unpublished.'],
            ],
            'dependencyStatus' => [
                ['label' => 'Domain model', 'value' => 'Role and Permission models plus migration skeletons exist'],
                ['label' => 'Backend dependency'],
            ],
            'legacyMapping' => [
                ['label' => 'Legacy source', 'value' => 'Old Galaxy staff and access matrix'],
                'invalid-legacy-entry',
            ],
            'selectedRecordSummary' => [
                ['label' => 'Selected tier', 'value' => 'Galaxy Prime'],
                ['label' => 'Broken selected record'],
            ],
            'implementationHandoff' => [
                'summary' => 'Start with a minimal role create or update path.',
                'steps' => ['Persist a minimal role record.', 42],
            ],
            'emptyState' => [
                'title' => 'No shop-scoped roles configured yet',
                'description' => 'Seed the first role profile from the old Galaxy matrix.',
                'actions' => [
                    ['label' => 'Create first role', 'tone' => 'primary'],
                    ['label' => ['invalid-label']],
                ],
            ],
            'form' => [
                'title' => 'Create or edit role',
                'actions' => [
                    ['label' => 'Publish role', 'tone' => 'primary'],
                    ['tone' => 'secondary'],
                ],
                'sections' => [
                    [
                        'title' => 'Role identity',
                        'help' => 'Keep legacy naming visible while migration is preview-only.',
                        'actions' => [
                            ['label' => 'Compare staff roles', 'tone' => 'secondary'],
                            'invalid-section-action',
                        ],
                        'fields' => [
                            ['label' => 'Role name', 'value' => 'Shop Manager'],
                            ['label' => 'Scope'],
                        ],
                    ],
                    'invalid-section-entry',
                ],
            ],
        ]);

        $this->assertSame([
            ['label' => 'Publish role', 'tone' => 'primary'],
        ], $normalized['actions']);

        $this->assertSame('Role publishing is still preview-only', $normalized['notice']['title']);
        $this->assertCount(1, $normalized['readinessChecklist']);
        $this->assertCount(1, $normalized['activityTimeline']);
        $this->assertCount(1, $normalized['dependencyStatus']);
        $this->assertCount(1, $normalized['legacyMapping']);
        $this->assertCount(1, $normalized['selectedRecordSummary']);
        $this->assertSame(['Persist a minimal role record.'], $normalized['implementationHandoff']['steps']);
        $this->assertSame([
            ['label' => 'Create first role', 'tone' => 'primary'],
        ], $normalized['emptyState']['actions']);
        $this->assertCount(1, $normalized['form']['actions']);
        $this->assertCount(1, $normalized['form']['sections']);
        $this->assertCount(1, $normalized['form']['sections'][0]['actions']);
        $this->assertCount(1, $normalized['form']['sections'][0]['fields']);
    }

    public function test_normalize_keeps_action_links_when_href_is_valid(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'actions' => [
                ['label' => 'New type', 'tone' => 'primary', 'href' => '#live-form'],
                ['label' => 'Broken link', 'tone' => 'secondary', 'href' => ['invalid-href']],
            ],
            'emptyState' => [
                'title' => 'No custom card types configured yet',
                'description' => 'Start by creating the first Galaxy-specific card tier.',
                'actions' => [
                    ['label' => 'Create first type', 'tone' => 'primary', 'href' => '#live-form'],
                    ['label' => 'Broken empty-state link', 'href' => ['invalid-href']],
                ],
            ],
        ]);

        $this->assertSame([
            ['label' => 'New type', 'tone' => 'primary', 'href' => '#live-form'],
        ], $normalized['actions']);

        $this->assertSame([
            ['label' => 'Create first type', 'tone' => 'primary', 'href' => '#live-form'],
        ], $normalized['emptyState']['actions']);
    }

    public function test_normalize_keeps_action_methods_when_method_is_valid(): void
    {
        $normalizer = new AdminResourcePageNormalizer();

        $normalized = $normalizer->normalize([
            'actions' => [
                ['label' => 'Activate type', 'tone' => 'secondary', 'href' => '/admin/card-types/7/toggle-status', 'method' => 'PATCH'],
                ['label' => 'Broken method', 'href' => '/admin/card-types/7/toggle-status', 'method' => ['PATCH']],
            ],
        ]);

        $this->assertSame([
            ['label' => 'Activate type', 'tone' => 'secondary', 'href' => '/admin/card-types/7/toggle-status', 'method' => 'PATCH'],
        ], $normalized['actions']);
    }
}
