<section class="card">
    <h3 style="margin: 0 0 12px; font-size: 1.1rem;">First Laravel wiring step</h3>
    <p style="margin: 0; color: var(--text-muted); line-height: 1.6;">{{ $implementationHandoff['summary'] }}</p>
    <ul class="list">
        @foreach ($implementationHandoff['steps'] as $step)
            <li>{{ $step }}</li>
        @endforeach
    </ul>
</section>
