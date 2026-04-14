@extends('admin.layouts.app')

@section('content')
    <section class="card">
        <span class="eyebrow">Admin / Dashboard</span>
        <h2 style="margin: 16px 0 12px; font-size: 1.75rem;">Phase 1 admin information architecture baseline</h2>
        <p style="margin: 0; color: var(--text-muted); max-width: 780px; line-height: 1.6;">
            The shell now reflects the Galaxy-specific admin map instead of a generic starter dashboard,
            so the next vertical slices can attach to the sections we actually need to migrate.
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
                <p class="metric-label">Next step</p>
                <p class="metric-value">Domain models</p>
            </article>
        </div>
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Mapped sections for migration</h3>
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
