@extends('admin.layouts.app')

@section('content')
    <section class="card">
        <span class="eyebrow">Admin / Dashboard</span>
        <h2 style="margin: 16px 0 12px; font-size: 1.75rem;">Placeholder dashboard shell</h2>
        <p style="margin: 0; color: var(--text-muted); max-width: 780px; line-height: 1.6;">
            This page intentionally stays simple: it establishes the admin route namespace, controller,
            shared layout, and reusable partials so the next vertical slices can plug into a stable shell.
        </p>

        <div class="placeholder-grid">
            <article class="metric">
                <p class="metric-label">Route namespace</p>
                <p class="metric-value">/admin</p>
            </article>
            <article class="metric">
                <p class="metric-label">View layer</p>
                <p class="metric-value">Ready</p>
            </article>
            <article class="metric">
                <p class="metric-label">Next step</p>
                <p class="metric-value">Widgets</p>
            </article>
        </div>
    </section>

    <section class="card">
        <h3 style="margin: 0; font-size: 1.1rem;">Suggested follow-up work</h3>
        <ul class="list">
            <li>add admin auth/guard protection when access model is defined;</li>
            <li>replace placeholder metrics with real counters/services;</li>
            <li>extract styles/assets once the admin UI structure stabilizes.</li>
        </ul>
    </section>
@endsection
