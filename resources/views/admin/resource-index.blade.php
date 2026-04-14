@extends('admin.layouts.app')

@section('content')
    <section class="card">
        <span class="eyebrow">{{ $eyebrow }}</span>
        <h2 style="margin: 16px 0 12px; font-size: 1.75rem;">{{ $pageTitle }} placeholder</h2>
        <p style="margin: 0; color: var(--text-muted); max-width: 780px; line-height: 1.6;">
            {{ $summary }}
        </p>

        @if (! empty($actions))
            <div style="margin-top: 18px;">
                @include('admin.partials.resource-actions', ['actions' => $actions])
            </div>
        @endif

        <div class="placeholder-grid">
            <article class="metric">
                <p class="metric-label">Section key</p>
                <p class="metric-value" style="font-size: 1.2rem;">{{ $resourceKey }}</p>
            </article>
            <article class="metric">
                <p class="metric-label">Phase</p>
                <p class="metric-value">1</p>
            </article>
            <article class="metric">
                <p class="metric-label">Next step</p>
                <p class="metric-value" style="font-size: 1.05rem; line-height: 1.4;">{{ $nextStep }}</p>
            </article>
        </div>
    </section>

    @php
        $resourceBlocks = [
            ['key' => 'metrics', 'partial' => 'admin.partials.resource-summary-metrics', 'prop' => 'metrics'],
            ['key' => 'table', 'partial' => 'admin.partials.operational-index-table', 'prop' => 'table'],
            ['key' => 'operationalGlossary', 'partial' => 'admin.partials.resource-operational-glossary', 'prop' => 'operationalGlossary'],
            ['key' => 'legacyParityNotes', 'partial' => 'admin.partials.resource-legacy-parity-notes', 'prop' => 'legacyParityNotes'],
            ['key' => 'operationalNextSlice', 'partial' => 'admin.partials.resource-operational-next-slice', 'prop' => 'operationalNextSlice'],
            ['key' => 'operationalDataStatus', 'partial' => 'admin.partials.resource-operational-data-status', 'prop' => 'operationalDataStatus'],
            ['key' => 'operationalMigrationBlockers', 'partial' => 'admin.partials.resource-operational-migration-blockers', 'prop' => 'operationalMigrationBlockers'],
            ['key' => 'form', 'partial' => 'admin.partials.resource-form-preview', 'prop' => 'form'],
            ['key' => 'emptyState', 'partial' => 'admin.partials.resource-empty-state', 'prop' => 'emptyState'],
            ['key' => 'notice', 'partial' => 'admin.partials.resource-preview-notice', 'prop' => 'notice'],
            ['key' => 'legacyMapping', 'partial' => 'admin.partials.resource-legacy-mapping', 'prop' => 'legacyMapping'],
            ['key' => 'activityTimeline', 'partial' => 'admin.partials.resource-activity-timeline', 'prop' => 'activityTimeline'],
            ['key' => 'readinessChecklist', 'partial' => 'admin.partials.resource-readiness-checklist', 'prop' => 'readinessChecklist'],
            ['key' => 'dependencyStatus', 'partial' => 'admin.partials.resource-dependency-status', 'prop' => 'dependencyStatus'],
            ['key' => 'implementationHandoff', 'partial' => 'admin.partials.resource-implementation-handoff', 'prop' => 'implementationHandoff'],
        ];
    @endphp

    @foreach ($resourceBlocks as $block)
        @if (! empty(${$block['key']}))
            @include($block['partial'], [$block['prop'] => ${$block['key']}])
        @endif
    @endforeach

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Why this page exists now</h3>
        <ul class="list">
            <li>connect the admin navigation to real Galaxy sections instead of dead placeholders;</li>
            <li>reserve stable route names for future CRUD and reporting flows;</li>
            <li>make the Phase 1 shell visibly closer to the old operational product shape.</li>
        </ul>
    </section>
@endsection
