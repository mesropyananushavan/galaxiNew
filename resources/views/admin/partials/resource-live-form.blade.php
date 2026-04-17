<section class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px;">
        <h3 style="margin: 0; font-size: 1.1rem;">{{ $liveForm['title'] }}</h3>
    </div>

    @if (! empty($liveForm['description']))
        <p style="margin: 0 0 16px; color: var(--text-muted); line-height: 1.6;">{{ $liveForm['description'] }}</p>
    @endif

    <form method="POST" action="{{ $liveForm['action'] }}" style="display: grid; gap: 16px;">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
            @foreach ($liveForm['fields'] as $field)
                <label style="display: grid; gap: 8px; font-weight: 600;">
                    <span>{{ $field['label'] }}</span>

                    @if ($field['type'] === 'hidden')
                        <input type="hidden" name="{{ $field['name'] }}" value="{{ old($field['name'], $field['value']) }}">
                    @else
                        <input
                            type="{{ $field['type'] }}"
                            name="{{ $field['name'] }}"
                            value="{{ old($field['name'], $field['value']) }}"
                            style="border: 1px solid var(--border); border-radius: 12px; padding: 12px 14px; background: var(--surface-muted); color: var(--text-main);"
                        >
                    @endif
                </label>
            @endforeach
        </div>

        <div>
            <button type="submit" class="button button-primary">{{ $liveForm['submitLabel'] }}</button>
        </div>
    </form>
</section>
