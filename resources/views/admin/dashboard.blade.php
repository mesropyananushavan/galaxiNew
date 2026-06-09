@extends('admin.layouts.app')

@section('content')
    @php
        $dashboardNoteStyle = 'margin: 12px 0 0; color: var(--text-muted); line-height: 1.6;';
        $dashboardWideNoteStyle = $dashboardNoteStyle.' max-width: 780px;';
    @endphp

    <section class="card">
        <span class="eyebrow">Admin / Dashboard</span>
        <h2 style="margin: 16px 0 12px; font-size: 1.75rem;">Phase 1 admin information architecture baseline</h2>
        <p style="margin: 0; color: var(--text-muted); max-width: 780px; line-height: 1.6;">
            The shell now reflects the Galaxy-specific admin map instead of a generic baseline dashboard,
            so the next vertical slices can attach to the sections we actually need to migrate.
        </p>

        @if (is_array($dashboardScopeSummary ?? null))
            <div style="margin-top: 16px; padding: 14px 16px; border: 1px solid rgba(158, 163, 184, 0.2); border-radius: 12px; background: rgba(99, 102, 241, 0.08);">
                <strong style="display: block; margin-bottom: 6px;">{{ $dashboardScopeSummary['label'] }}</strong>
                <span style="color: var(--text-muted); line-height: 1.6;">{{ $dashboardScopeSummary['value'] }}</span>
            </div>
        @endif

        @if (is_array($assignedBranchSnapshot ?? null) && is_array($assignedBranchSnapshot['items'] ?? null))
            <div style="margin-top: 16px; padding: 14px 16px; border: 1px solid rgba(158, 163, 184, 0.2); border-radius: 12px; background: rgba(15, 23, 42, 0.24);">
                <strong style="display: block; margin-bottom: 10px;">{{ $assignedBranchSnapshot['label'] }}</strong>
                <p style="margin: 0 0 12px; color: var(--text-muted); line-height: 1.6; max-width: 780px;">
                    This branch snapshot keeps the assigned Galaxy location in view, so setup gaps and fresh activity are visible before you jump into review.
                </p>
                @foreach (($assignedBranchSnapshot['actionMetrics'] ?? []) as $metric)
                    <p style="{{ $dashboardNoteStyle }}">
                        <strong>{{ $metric['label'] }}:</strong>
                        {{ $metric['value'] }}
                    </p>
                @endforeach
                <div class="placeholder-grid" style="margin-top: 0;">
                    @foreach ($assignedBranchSnapshot['items'] as $item)
                        <article class="metric">
                            <p class="metric-label">{{ $item['label'] }}</p>
                            <p class="metric-value">{{ $item['value'] }}</p>
                        </article>
                    @endforeach
                </div>
                @if (is_array($assignedBranchSnapshot['actions'] ?? null) && $assignedBranchSnapshot['actions'] !== [])
                    <ul class="list" style="margin-top: 12px;">
                        @foreach ($assignedBranchSnapshot['actions'] as $action)
                            <li><a href="{{ $action['route'] }}">{{ $action['label'] }}</a> (Route: {{ $action['path'] ?? $action['route'] }})</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div style="margin-top: 20px;">
            <h3 style="margin: 0 0 8px; font-size: 1.1rem;">Galaxy live foundation snapshot</h3>
            <p style="margin: 0 0 16px; color: var(--text-muted); line-height: 1.6; max-width: 780px;">
                These counters track the first live Galaxy surfaces already present in Phase 1,
                so branch setup and review work can move through real operational entities instead of scaffold-era placeholders.
            </p>
            @if (is_array($foundationHandoffSummary ?? null))
            <p style="margin: 0 0 16px; color: var(--text-muted); line-height: 1.6; max-width: 780px;">
                <strong>{{ $foundationHandoffSummary['label'] }}:</strong>
                {{ $foundationHandoffSummary['value'] }}
            </p>
            @endif
        <p style="margin: 0 0 16px; color: var(--text-muted); line-height: 1.6; max-width: 780px;">
            <strong>Foundation focus:</strong>
            {{ $foundationFocus }}.
        </p>
        <p style="margin: 0 0 16px; color: var(--text-muted); line-height: 1.6; max-width: 780px;">
            <strong>Foundation posture:</strong>
            {{ $foundationPosture }}.
        </p>
        <div class="placeholder-grid">
            @foreach ($foundationSnapshotMetrics as $metric)
                <article class="metric">
                    <p class="metric-label">{{ $metric['label'] }}</p>
                    <p class="metric-value">{{ $metric['value'] }}</p>
                </article>
            @endforeach
        </div>
        </div>
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Phase 1 core entity map</h3>
        <p style="{{ $dashboardWideNoteStyle }}">
            {{ $phaseOneDomainFocus }}
        </p>
        @foreach ($phaseOneDomainMetrics as $metric)
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $metric['label'] }}:</strong>
                {{ $metric['value'] }}
            </p>
        @endforeach
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Entity baseline:</strong>
            <code>config/phase-1-domain-map.php</code> keeps this mapped Galaxy entity inventory aligned.
        </p>
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Entity posture:</strong>
            {{ $phaseOneDomainPosture }}.
        </p>
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Entity guide:</strong>
            {!! $phaseOneDomainGuideText !!} remain the readable and implementation anchors for this mapped Galaxy entity inventory.
        </p>
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Source of truth:</strong>
            {!! $phaseOneDomainSourceOfTruthText !!} remain the readable and implementation anchors for this mapped Galaxy entity inventory.
        </p>
        <ul class="list">
            @foreach ($phaseOneDomainMap as $entity)
                <li>{!! $entity['displaySummary'] !!}</li>
            @endforeach
        </ul>
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Phase 1 seam-source inventory</h3>
        <p style="{{ $dashboardWideNoteStyle }}">
            {{ $phaseOneSeamSourcesFocus }}
        </p>
        @foreach ($phaseOneSeamSourceMetrics as $metric)
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $metric['label'] }}:</strong>
                @if ($metric['html'] ?? false)
                    {!! $metric['value'] !!}
                @else
                    {{ $metric['value'] }}
                @endif
            </p>
        @endforeach
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Seam-source guide:</strong>
            {!! $phaseOneSeamSourcesGuideText !!} remain the readable and implementation anchors for this Phase 1 seam-source trail.
        </p>
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Source of truth:</strong>
            {!! $phaseOneSeamSourcesSourceOfTruthText !!} remain the readable and implementation anchors for this Phase 1 seam-source trail.
        </p>
        <ul class="list">
            @foreach ($phaseOneSeamSources as $seamSource)
                <li>{!! $seamSource['displayLabel'] !!}</li>
            @endforeach
        </ul>
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Phase 1 foundation seams</h3>
        <p style="{{ $dashboardWideNoteStyle }}">
            {{ $phaseOneFoundationSeamsFocus }}
        </p>
        @foreach ($phaseOneFoundationSeamMetrics as $metric)
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $metric['label'] }}:</strong>
                @if ($metric['html'] ?? false)
                    {!! $metric['value'] !!}
                @else
                    {{ $metric['value'] }}
                @endif
            </p>
        @endforeach
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Seam guide:</strong>
            {!! $phaseOneFoundationSeamsGuideText !!} remain the readable and implementation anchors for this mapped seam inventory.
        </p>
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Source of truth:</strong>
            {!! $phaseOneFoundationSeamsSourceOfTruthText !!} remain the readable and implementation anchors for this mapped seam inventory.
        </p>
        <ul class="list">
            @foreach ($phaseOneFoundationSeams as $seam)
                <li>
                    {!! $seam['displaySummary'] !!}
                    @if (filled($seam['sourcesNote'] ?? null))
                        <br>
                        <span style="color: var(--text-muted);">{{ $seam['sourcesNote'] }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Phase 1 reference docs</h3>
        <p style="{{ $dashboardWideNoteStyle }}">
            {{ $phaseOneReferenceDocsFocus }}
        </p>
        @foreach ($phaseOneReferenceDocMetrics as $metric)
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $metric['label'] }}:</strong>
                @if ($metric['html'] ?? false)
                    {!! $metric['value'] !!}
                @else
                    {{ $metric['value'] }}
                @endif
            </p>
        @endforeach
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Reference guide:</strong>
            {!! $phaseOneReferenceDocsGuideText !!} remain the readable anchors for this admin-side Phase 1 reference trail.
        </p>
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Source of truth:</strong>
            {!! $phaseOneReferenceDocsSourceOfTruthText !!} remain the readable and implementation anchors for this admin-side Phase 1 reference trail.
        </p>
        <ul class="list">
            @foreach ($phaseOneReferenceDocs as $referenceDoc)
                <li>{!! $referenceDoc['displayLabel'] !!}</li>
            @endforeach
        </ul>
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Reference seam bridge:</strong>
            <code>config/phase-1-seam-sources.php</code> keeps the README-level seam-source inventory tied into this broader Phase 1 reference trail.
        </p>
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Live review entry points</h3>
        <p style="{{ $dashboardWideNoteStyle }}">
            Use these Galaxy review surfaces to move from branch setup into live operational checks once records start landing.
        </p>
        @foreach ($liveEntryMetrics as $metric)
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $metric['label'] }}:</strong>
                {{ $metric['value'] }}
            </p>
        @endforeach
        @if (is_array($liveEntryHandoffSummary ?? null))
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $liveEntryHandoffSummary['label'] }}:</strong>
                {{ $liveEntryHandoffSummary['value'] }}
            </p>
        @endif
        @if (is_array($liveEntryScopeNote ?? null))
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $liveEntryScopeNote['label'] }}:</strong>
                {{ $liveEntryScopeNote['value'] }}
            </p>
        @endif
        <ul class="list">
            @foreach ($liveReviewEntryPoints as $entryPoint)
                <li><a href="{{ $entryPoint['route'] }}">{{ $entryPoint['label'] }}</a> (Route: {{ $entryPoint['path'] ?? $entryPoint['route'] }})</li>
            @endforeach
        </ul>
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Resume latest live work</h3>
        <p style="{{ $dashboardWideNoteStyle }}">
            Jump back into the latest Galaxy workspace for the branch, holder, card shell, or access shell that most recently changed.
        </p>
        @foreach ($latestWorkspaceMetrics as $metric)
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $metric['label'] }}:</strong>
                {{ $metric['value'] }}
            </p>
        @endforeach
        @if (is_array($latestWorkspaceHandoffSummary ?? null))
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $latestWorkspaceHandoffSummary['label'] }}:</strong>
                {{ $latestWorkspaceHandoffSummary['value'] }}
            </p>
        @endif
        @if (is_array($latestWorkspaceScopeNote ?? null))
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $latestWorkspaceScopeNote['label'] }}:</strong>
                {{ $latestWorkspaceScopeNote['value'] }}
            </p>
        @endif
        @if ($latestWorkspaces !== [])
            <ul class="list">
                @foreach ($latestWorkspaces as $workspace)
                    <li><a href="{{ $workspace['route'] }}">{{ $workspace['label'] }}</a> (Route: {{ $workspace['path'] ?? $workspace['route'] }})</li>
                @endforeach
            </ul>
        @else
            <p style="{{ $dashboardNoteStyle }}">
                No live records have been created yet. Start in the live review entry points above to open the first Galaxy-backed workspace.
            </p>
            <p style="{{ $dashboardWideNoteStyle }}">
                In Phase 1, this usually means the branch is still moving through first-pass setup for Galaxy branches, Galaxy holders, Galaxy card shells, or access structure.
            </p>
        @endif
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Galaxy migration map</h3>
        <p style="{{ $dashboardWideNoteStyle }}">
            These grouped sections mark the Galaxy admin surfaces that still need parity work, so each Phase 1 slice can land against a visible target map.
        </p>
        @foreach ($migrationMapMetrics as $metric)
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $metric['label'] }}:</strong>
                {{ $metric['value'] }}
            </p>
        @endforeach
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Migration-map focus:</strong>
            {{ $migrationMapFocus }}.
        </p>
        <p style="{{ $dashboardNoteStyle }}">
            <strong>Migration-map posture:</strong>
            {{ $migrationMapPosture }}.
        </p>
        @if (is_array($migrationMapHandoffSummary ?? null))
            <p style="{{ $dashboardNoteStyle }}">
                <strong>{{ $migrationMapHandoffSummary['label'] }}:</strong>
                {{ $migrationMapHandoffSummary['value'] }}
            </p>
        @endif
        <ul class="list">
            @foreach ($navigationGroups as $group)
                <li>
                    <strong>{{ $group['group'] }} ({{ count($group['items']) }} surfaces):</strong>
                    @foreach ($group['items'] as $item)
                        {!! $item['displaySummary'] !!}@if (! $loop->last), @else.@endif
                    @endforeach
                </li>
            @endforeach
        </ul>
    </section>
@endsection
