<section class="card" id="live-form">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px;">
        <h3 style="margin: 0; font-size: 1.1rem;">{{ $liveForm['title'] }}</h3>
    </div>

    @if (! empty($liveForm['description']))
        <p style="margin: 0 0 16px; color: var(--text-muted); line-height: 1.6;">{{ $liveForm['description'] }}</p>
    @endif

    @if ($errors->any())
        <div style="margin: 0 0 16px; padding: 14px 16px; border: 1px solid rgba(239, 68, 68, 0.35); border-radius: 14px; background: rgba(239, 68, 68, 0.08);">
            <strong style="display: block; margin-bottom: 8px;">Live form validation</strong>
            <ul style="margin: 0; padding-left: 18px; color: var(--text-muted); line-height: 1.6;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $liveForm['action'] }}" style="display: grid; gap: 16px;">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
            @foreach ($liveForm['fields'] as $field)
                <label style="display: grid; gap: 8px; font-weight: 600;">
                    <span>
                        {{ $field['label'] }}
                        @if ($field['required'])
                            <span style="color: rgb(248, 113, 113);">*</span>
                        @endif
                    </span>

                    @if ($field['type'] === 'hidden')
                        <input type="hidden" name="{{ $field['name'] }}" value="{{ old($field['name'], $field['value']) }}">
                    @elseif ($field['type'] === 'select')
                        <select
                            name="{{ $field['name'] }}"
                            @required($field['required'])
                            @foreach ($field['attributes'] as $attribute => $value)
                                {{ $attribute }}="{{ $value }}"
                            @endforeach
                            style="border: 1px solid {{ $errors->has($field['name']) ? 'rgba(239, 68, 68, 0.55)' : 'var(--border)' }}; border-radius: 12px; padding: 12px 14px; background: var(--surface-muted); color: var(--text-main);"
                        >
                            @foreach ($field['options'] as $option)
                                <option value="{{ $option['value'] }}" @selected(old($field['name'], $field['value']) === $option['value'])>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    @else
                        <input
                            type="{{ $field['type'] }}"
                            name="{{ $field['name'] }}"
                            value="{{ old($field['name'], $field['value']) }}"
                            @required($field['required'])
                            @if (! empty($field['placeholder']))
                                placeholder="{{ $field['placeholder'] }}"
                            @endif
                            @foreach ($field['attributes'] as $attribute => $value)
                                {{ $attribute }}="{{ $value }}"
                            @endforeach
                            style="border: 1px solid {{ $errors->has($field['name']) ? 'rgba(239, 68, 68, 0.55)' : 'var(--border)' }}; border-radius: 12px; padding: 12px 14px; background: var(--surface-muted); color: var(--text-main);"
                        >
                    @endif

                    @if (! empty($field['help']))
                        <span style="font-size: 0.85rem; font-weight: 400; color: var(--text-muted);">{{ $field['help'] }}</span>
                    @endif

                    @error($field['name'])
                        <span style="font-size: 0.85rem; color: rgb(248, 113, 113);">{{ $message }}</span>
                    @enderror
                </label>
            @endforeach
        </div>

        <div>
            <button type="submit" class="button button-primary">{{ $liveForm['submitLabel'] }}</button>
        </div>
    </form>
</section>
