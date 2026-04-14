<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($pageTitle ?? 'Admin') . ' · Galaxi Admin' }}</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f5f7fb;
            --surface: #ffffff;
            --surface-muted: #f0f4ff;
            --border: #dbe3f0;
            --text: #172033;
            --text-muted: #5f6b85;
            --accent: #4f46e5;
            --accent-soft: #eef2ff;
            --success: #047857;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        a { color: inherit; text-decoration: none; }

        .admin-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 260px 1fr;
        }

        .admin-sidebar {
            background: #10182b;
            color: #e5ecff;
            padding: 24px 20px;
            border-right: 1px solid rgba(219, 227, 240, 0.08);
        }

        .admin-brand {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .admin-subtitle {
            color: rgba(229, 236, 255, 0.7);
            font-size: 0.875rem;
            margin-bottom: 28px;
        }

        .admin-nav-group + .admin-nav-group {
            margin-top: 20px;
        }

        .admin-nav-group-label {
            margin-bottom: 10px;
            color: rgba(229, 236, 255, 0.55);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .admin-nav {
            display: grid;
            gap: 8px;
        }

        .admin-nav a,
        .admin-nav-placeholder {
            display: grid;
            gap: 4px;
            padding: 10px 12px;
            border-radius: 10px;
            color: rgba(229, 236, 255, 0.88);
            background: transparent;
            transition: background 0.15s ease;
        }

        .admin-nav a:hover,
        .admin-nav a.is-active,
        .admin-nav-placeholder {
            background: rgba(255, 255, 255, 0.08);
        }

        .admin-nav small,
        .admin-nav-placeholder small {
            color: rgba(229, 236, 255, 0.58);
            font-size: 0.78rem;
            line-height: 1.35;
        }

        .admin-content {
            display: grid;
            grid-template-rows: auto 1fr;
            min-width: 0;
        }

        .admin-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 28px;
            background: rgba(255, 255, 255, 0.7);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(10px);
        }

        .admin-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .admin-header p {
            margin: 4px 0 0;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .admin-body {
            padding: 28px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(16, 24, 43, 0.06);
        }

        .card + .card {
            margin-top: 20px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 0.825rem;
            font-weight: 600;
        }

        .placeholder-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .metric {
            padding: 18px;
            border-radius: 14px;
            background: var(--surface-muted);
            border: 1px solid var(--border);
        }

        .metric-label {
            margin: 0 0 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .metric-value {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .list {
            margin: 16px 0 0;
            padding-left: 18px;
            color: var(--text-muted);
        }

        @media (max-width: 960px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                border-right: 0;
                border-bottom: 1px solid rgba(219, 227, 240, 0.08);
            }

            .placeholder-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        @include('admin.partials.sidebar')

        <div class="admin-content">
            @include('admin.partials.header')

            <main class="admin-body">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
