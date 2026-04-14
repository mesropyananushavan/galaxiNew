<div style="display: flex; gap: 10px; flex-wrap: wrap;">
    @foreach ($actions as $action)
        <span
            class="eyebrow"
            style="{{ ($action['tone'] ?? 'default') === 'secondary' ? 'background: var(--surface-muted); color: var(--text-muted);' : '' }}"
        >
            {{ $action['label'] }}
        </span>
    @endforeach
</div>
