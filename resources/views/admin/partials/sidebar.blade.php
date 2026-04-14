<aside class="admin-sidebar">
    <div class="admin-brand">Galaxi Admin</div>
    <div class="admin-subtitle">Galaxy-specific admin IA baseline for Phase 1.</div>

    @foreach (config('admin-navigation') as $group)
        <section class="admin-nav-group" aria-label="{{ $group['group'] }}">
            <div class="admin-nav-group-label">{{ $group['group'] }}</div>

            <nav class="admin-nav" aria-label="{{ $group['group'] }} navigation">
                @foreach ($group['items'] as $item)
                    @php($routeName = $item['route'] ?? null)
                    @php($isActive = $routeName ? request()->routeIs($routeName) : false)

                    @if ($routeName)
                        <a href="{{ route($routeName) }}" class="{{ $isActive ? 'is-active' : '' }}">
                            <span>{{ $item['label'] }}</span>
                            <small>{{ $item['description'] }}</small>
                        </a>
                    @else
                        <span class="admin-nav-placeholder">
                            <span>{{ $item['label'] }}</span>
                            <small>{{ $item['description'] }}</small>
                        </span>
                    @endif
                @endforeach
            </nav>
        </section>
    @endforeach
</aside>
