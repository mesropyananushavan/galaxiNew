<section class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px;">
        <h3 style="margin: 0; font-size: 1.1rem;">Operational index shape</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach ($table['filters'] as $filter)
                <span class="eyebrow" style="background: var(--surface-muted); color: var(--text-muted);">{{ $filter }}</span>
            @endforeach
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
            <thead>
                <tr>
                    @foreach ($table['columns'] as $column)
                        <th style="text-align: left; padding: 0 0 12px; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border);">{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($table['rows'] as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td style="padding: 14px 0; border-bottom: 1px solid var(--border);">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
