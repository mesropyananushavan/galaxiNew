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

    @if (! empty($metrics))
        @include('admin.partials.resource-summary-metrics', ['metrics' => $metrics])
    @endif

    @if (! empty($table))
        @include('admin.partials.operational-index-table', ['table' => $table])
    @endif

    @if (! empty($form))
        @include('admin.partials.resource-form-preview', ['form' => $form])
    @endif

    @if (! empty($emptyState))
        @include('admin.partials.resource-empty-state', ['emptyState' => $emptyState])
    @endif

    @if (! empty($notice))
        @include('admin.partials.resource-preview-notice', ['notice' => $notice])
    @endif

    @if (! empty($legacyMapping))
        @include('admin.partials.resource-legacy-mapping', ['legacyMapping' => $legacyMapping])
    @endif

    @if (! empty($activityTimeline))
        @include('admin.partials.resource-activity-timeline', ['activityTimeline' => $activityTimeline])
    @endif

    @if (! empty($readinessChecklist))
        @include('admin.partials.resource-readiness-checklist', ['readinessChecklist' => $readinessChecklist])
    @endif

    @if (! empty($dependencyStatus))
        @include('admin.partials.resource-dependency-status', ['dependencyStatus' => $dependencyStatus])
    @endif

    @if (! empty($implementationHandoff))
        @include('admin.partials.resource-implementation-handoff', ['implementationHandoff' => $implementationHandoff])
    @endif

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Why this page exists now</h3>
        <ul class="list">
            <li>connect the admin navigation to real Galaxy sections instead of dead placeholders;</li>
            <li>reserve stable route names for future CRUD and reporting flows;</li>
            <li>make the Phase 1 shell visibly closer to the old operational product shape.</li>
        </ul>
    </section>
@endsection
