<section class="card">
    <h3 style="margin: 0 0 16px; font-size: 1.1rem;">Management snapshot</h3>
    <div class="placeholder-grid">
        @foreach ($metrics as $metric)
            <article class="metric">
                <p class="metric-label">{{ $metric['label'] }}</p>
                <p class="metric-value" style="font-size: 1.2rem;">{{ $metric['value'] }}</p>
            </article>
        @endforeach
    </div>
</section>
