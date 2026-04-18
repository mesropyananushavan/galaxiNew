<div style="display: flex; gap: 10px; flex-wrap: wrap;">
    @foreach ($actions as $action)
        @if (! empty($action['href']) && ! empty($action['method']) && ! in_array($action['method'], ['GET'], true))
            <form method="POST" action="{{ $action['href'] }}" style="margin: 0;">
                @csrf
                @if ($action['method'] !== 'POST')
                    @method($action['method'])
                @endif

                <button
                    type="submit"
                    class="eyebrow"
                    style="border: 0; cursor: pointer; {{ ($action['tone'] ?? 'default') === 'secondary' ? 'background: var(--surface-muted); color: var(--text-muted);' : '' }}"
                >
                    {{ $action['label'] }}
                </button>
            </form>
        @elseif (! empty($action['href']))
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
