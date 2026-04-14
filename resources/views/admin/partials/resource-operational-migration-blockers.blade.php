<section class="card">
    <h3 style="margin: 0 0 12px; font-size: 1.1rem;">Operational migration blockers</h3>
    <ul class="list">
        @foreach ($operationalMigrationBlockers as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>
</section>
