<div style="display: flex; gap: 10px; flex-wrap: wrap;">
    @foreach ($actions as $action)
        @if (! empty($action['href']))
            <a
                href="{{ $action['href'] }}"
                class="eyebrow"
                style="text-decoration: none; {{ ($action['tone'] ?? 'default') === 'secondary' ? 'background: var(--surface-muted); color: var(--text-muted);' : '' }}"
            >
                {{ $action['label'] }}
            </a>
        @else
            <span
                class="eyebrow"
                style="{{ ($action['tone'] ?? 'default') === 'secondary' ? 'background: var(--surface-muted); color: var(--text-muted);' : '' }}"
            >
                {{ $action['label'] }}
            </span>
        @endif
    @endforeach
</div>
