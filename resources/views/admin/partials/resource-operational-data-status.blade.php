<section class="card">
    <h3 style="margin: 0 0 12px; font-size: 1.1rem;">Operational data source status</h3>
    <ul class="list">
        @foreach ($operationalDataStatus as $item)
            <li>
                <strong>{{ $item['label'] }}:</strong>
                {{ $item['value'] }}
            </li>
        @endforeach
    </ul>
</section>
