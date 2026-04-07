<header class="admin-header">
    <div>
        <h1>{{ $pageTitle ?? 'Admin' }}</h1>
        <p>Baseline admin shell for future modules and widgets.</p>
    </div>

    <div style="display: flex; align-items: center; gap: 12px;">
        <div class="eyebrow">
            <span>System</span>
            <strong>Access baseline ready</strong>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="border: 1px solid var(--border); background: var(--surface); color: var(--text); border-radius: 999px; padding: 8px 14px; font: inherit; cursor: pointer;">
                Logout
            </button>
        </form>
    </div>
</header>
