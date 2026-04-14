<section class="card">
    <h3 style="margin: 0 0 12px; font-size: 1.1rem;">{{ $title }}</h3>
    <p style="margin: 0; color: var(--text-muted); line-height: 1.6;">{{ $summary }}</p>
    <ul class="list">
        @foreach ($items as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>
</section>
