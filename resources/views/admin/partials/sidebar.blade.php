<aside class="admin-sidebar">
    <div class="admin-brand">Galaxi Admin</div>
    <div class="admin-subtitle">Minimal navigation shell after Laravel foundation.</div>

    <nav class="admin-nav" aria-label="Admin navigation">
        <a
            href="{{ route('admin.dashboard') }}"
            class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}"
        >
            Dashboard
        </a>
        <a
            href="{{ route('admin.users.index') }}"
            class="{{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}"
        >
            Users
        </a>
    </nav>
</aside>
