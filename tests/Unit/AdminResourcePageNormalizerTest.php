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
        $this->assertSame([], $normalized['implementationHandoff']);
        $this->assertSame([], $normalized['operationalNextSlice']);
        $this->assertSame([], $normalized['operatorChecklist']);
        $this->assertSame([], $normalized['escalationGuide']);
        $this->assertSame([], $normalized['shiftHandoff']);
        $this->assertSame([], $normalized['openIssues']);
        $this->assertSame([], $normalized['emptyState']);
        $this->assertSame([], $normalized['form']);
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
        $this->assertSame(['Persist a minimal role record.'], $normalized['implementationHandoff']['steps']);
        $this->assertSame([
            ['label' => 'Create first role', 'tone' => 'primary'],
        ], $normalized['emptyState']['actions']);
        $this->assertCount(1, $normalized['form']['actions']);
        $this->assertCount(1, $normalized['form']['sections']);
        $this->assertCount(1, $normalized['form']['sections'][0]['actions']);
        $this->assertCount(1, $normalized['form']['sections'][0]['fields']);
    }
}
