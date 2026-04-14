<section class="card">
    <h3 style="margin: 0 0 12px; font-size: 1.1rem;">Migration readiness checklist</h3>
    <ul class="list">
        @foreach ($readinessChecklist as $item)
            <li>
                <strong>{{ $item['status'] }}:</strong>
                {{ $item['label'] }}
            </li>
        @endforeach
    </ul>
</section>
