<section class="card">
    <h3 style="margin: 0 0 12px; font-size: 1.1rem;">Recent activity preview</h3>
    <div style="display: grid; gap: 12px;">
        @foreach ($activityTimeline as $item)
            <article style="padding: 12px 14px; border: 1px solid var(--border-color); border-radius: 12px; background: rgba(15, 23, 42, 0.35);">
                <div style="display: flex; justify-content: space-between; gap: 12px; align-items: baseline; margin-bottom: 6px;">
                    <strong>{{ $item['title'] }}</strong>
                    <span class="metric-label">{{ $item['time'] }}</span>
                </div>
                <p style="margin: 0; color: var(--text-muted); line-height: 1.5;">{{ $item['description'] }}</p>
            </article>
        @endforeach
    </div>
</section>
