<section class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px;">
        <h3 style="margin: 0; font-size: 1.1rem;">{{ $form['title'] }}</h3>
        @include('admin.partials.resource-actions', ['actions' => $form['actions']])
    </div>

    <div style="display: grid; gap: 18px;">
        @foreach ($form['sections'] as $section)
            <div style="border: 1px solid var(--border); border-radius: 16px; padding: 18px;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 10px;">
                    <h4 style="margin: 0; font-size: 1rem;">{{ $section['title'] }}</h4>

                    @if (! empty($section['actions']))
                        @include('admin.partials.resource-actions', ['actions' => $section['actions']])
                    @endif
                </div>

                @if (! empty($section['help']))
                    <p style="margin: 0 0 14px; color: var(--text-muted); line-height: 1.6;">{{ $section['help'] }}</p>
                @endif

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
                    @foreach ($section['fields'] as $field)
                        <div style="border: 1px solid var(--border); border-radius: 14px; padding: 14px 16px; background: var(--surface-muted);">
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px;">{{ $field['label'] }}</div>
                            <div style="font-weight: 600;">{{ $field['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
