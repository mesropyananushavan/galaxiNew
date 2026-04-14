<section class="card">
    <h3 style="margin: 0 0 12px; font-size: 1.1rem;">Operational glossary</h3>
    <ul class="list">
        @foreach ($operationalGlossary as $item)
            <li>
                <strong>{{ $item['term'] }}:</strong>
                {{ $item['meaning'] }}
            </li>
        @endforeach
    </ul>
</section>
