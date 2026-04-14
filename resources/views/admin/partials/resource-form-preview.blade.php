<section class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px;">
        <h3 style="margin: 0; font-size: 1.1rem;">{{ $form['title'] }}</h3>
        @include('admin.partials.resource-actions', ['actions' => $form['actions']])
    </div>

    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
        @foreach ($form['fields'] as $field)
            <div style="border: 1px solid var(--border); border-radius: 14px; padding: 14px 16px; background: var(--surface-muted);">
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px;">{{ $field['label'] }}</div>
                <div style="font-weight: 600;">{{ $field['value'] }}</div>
            </div>
        @endforeach
    </div>
</section>
