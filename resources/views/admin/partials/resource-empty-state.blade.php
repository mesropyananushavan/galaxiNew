<section class="card">
    <h3 style="margin: 0 0 10px; font-size: 1.1rem;">{{ $emptyState['title'] }}</h3>
    <p style="margin: 0; color: var(--text-muted); line-height: 1.6; max-width: 720px;">
        {{ $emptyState['description'] }}
    </p>

    @if (! empty($emptyState['actions']))
        <div style="margin-top: 16px;">
            @include('admin.partials.resource-actions', ['actions' => $emptyState['actions']])
        </div>
    @endif
</section>
