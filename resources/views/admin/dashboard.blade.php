@extends('admin.layouts.app')

@section('content')
    <section class="card">
        <span class="eyebrow">Admin / Dashboard</span>
        <h2 style="margin: 16px 0 12px; font-size: 1.75rem;">Phase 1 admin information architecture baseline</h2>
        <p style="margin: 0; color: var(--text-muted); max-width: 780px; line-height: 1.6;">
            The shell now reflects the Galaxy-specific admin map instead of a generic starter dashboard,
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
                            <li><a href="{{ $action['route'] }}">{{ $action['label'] }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div style="margin-top: 20px;">
            <h3 style="margin: 0 0 8px; font-size: 1.1rem;">Galaxy live foundation snapshot</h3>
            <p style="margin: 0 0 16px; color: var(--text-muted); line-height: 1.6; max-width: 780px;">
                These counters track the first live Galaxy surfaces already present in Phase 1,
                so branch setup and review work can move through real operational entities instead of starter placeholders.
            </p>
        <div class="placeholder-grid">
            <article class="metric">
                <p class="metric-label">Route namespace</p>
                <p class="metric-value">/admin</p>
            </article>
            <article class="metric">
                <p class="metric-label">Planned sections</p>
                <p class="metric-value">{{ $plannedSectionCount }}</p>
            </article>
            <article class="metric">
                <p class="metric-label">Live shops</p>
                <p class="metric-value">{{ $shopCount }}</p>
            </article>
            <article class="metric">
                <p class="metric-label">Active shops</p>
                <p class="metric-value">{{ $activeShopCount }}</p>
            </article>
            <article class="metric">
                <p class="metric-label">Live cardholders</p>
                <p class="metric-value">{{ $cardHolderCount }}</p>
            </article>
            <article class="metric">
                <p class="metric-label">Active cardholders</p>
                <p class="metric-value">{{ $activeCardHolderCount }}</p>
            </article>
            <article class="metric">
                <p class="metric-label">Live cards</p>
                <p class="metric-value">{{ $cardCount }}</p>
            </article>
            <article class="metric">
                <p class="metric-label">Active cards</p>
                <p class="metric-value">{{ $activeCardCount }}</p>
            </article>
            <article class="metric">
                <p class="metric-label">Live roles</p>
                <p class="metric-value">{{ $roleCount }}</p>
            </article>
            <article class="metric">
                <p class="metric-label">Live permissions</p>
                <p class="metric-value">{{ $permissionCount }}</p>
            </article>
        </div>
        </div>
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Live review entry points</h3>
        <p style="margin: 12px 0 0; color: var(--text-muted); line-height: 1.6; max-width: 780px;">
            Use these Galaxy review surfaces to move from branch setup into live operational checks once records start landing.
        </p>
        @if (is_array($liveEntryScopeNote ?? null))
            <p style="margin: 12px 0 0; color: var(--text-muted); line-height: 1.6;">
                <strong>{{ $liveEntryScopeNote['label'] }}:</strong>
                {{ $liveEntryScopeNote['value'] }}
            </p>
        @endif
        <ul class="list">
            @foreach ($liveReviewEntryPoints as $entryPoint)
                <li><a href="{{ $entryPoint['route'] }}">{{ $entryPoint['label'] }}</a></li>
            @endforeach
        </ul>
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Resume latest live work</h3>
        <p style="margin: 12px 0 0; color: var(--text-muted); line-height: 1.6; max-width: 780px;">
            Jump back into the latest Galaxy workspace for the branch, cardholder, card, or access item that most recently changed.
        </p>
        @if (is_array($latestWorkspaceScopeNote ?? null))
            <p style="margin: 12px 0 0; color: var(--text-muted); line-height: 1.6;">
                <strong>{{ $latestWorkspaceScopeNote['label'] }}:</strong>
                {{ $latestWorkspaceScopeNote['value'] }}
            </p>
        @endif
        @if ($latestWorkspaces !== [])
            <ul class="list">
                @foreach ($latestWorkspaces as $workspace)
                    <li><a href="{{ $workspace['route'] }}">{{ $workspace['label'] }}</a></li>
                @endforeach
            </ul>
        @else
            <p style="margin: 12px 0 0; color: var(--text-muted); line-height: 1.6;">
                No live records have been created yet. Start in the live review entry points above to open the first Galaxy-backed workspace.
            </p>
            <p style="margin: 12px 0 0; color: var(--text-muted); line-height: 1.6; max-width: 780px;">
                In Phase 1, this usually means the branch is still moving through first-pass setup for shops, cardholders, cards, or access structure.
            </p>
        @endif
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Galaxy migration map</h3>
        <p style="margin: 12px 0 0; color: var(--text-muted); line-height: 1.6; max-width: 780px;">
            These grouped sections mark the Galaxy admin surfaces that still need parity work, so each Phase 1 slice can land against a visible target map.
        </p>
        <ul class="list">
            @foreach ($navigationGroups as $group)
                <li>
                    <strong>{{ $group['group'] }}:</strong>
                    {{ collect($group['items'])->pluck('label')->join(', ') }}.
                </li>
            @endforeach
        </ul>
    </section>
@endsection
