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
                            @php
                                $cellData = is_array($cell) ? $cell : ['label' => (string) $cell];
                            @endphp
                            <td style="padding: 14px 0; border-bottom: 1px solid var(--border);">
                                @if (! empty($cellData['href']) && ! empty($cellData['method']) && ! in_array($cellData['method'], ['GET'], true))
                                    <form method="POST" action="{{ $cellData['href'] }}" style="margin: 0; display: inline;">
                                        @csrf
                                        @if ($cellData['method'] !== 'POST')
                                            @method($cellData['method'])
                                        @endif

                                        <button type="submit" style="border: 0; padding: 0; background: transparent; color: inherit; text-decoration: underline; text-underline-offset: 2px; cursor: pointer; font: inherit;">
                                            {{ $cellData['label'] }}
                                        </button>
                                    </form>
                                @elseif (! empty($cellData['href']))
                                    <a href="{{ $cellData['href'] }}" style="color: inherit; text-decoration: underline; text-underline-offset: 2px;">{{ $cellData['label'] }}</a>
                                @else
                                    {{ $cellData['label'] }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
