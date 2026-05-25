<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galaxi Foundation</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #0f172a;
            --panel: #111c34;
            --panel-soft: #16233f;
            --border: #2b3a5e;
            --text: #e5ecff;
            --muted: #9fb0d9;
            --accent: #8b5cf6;
            --accent-soft: #c4b5fd;
            --success: #34d399;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 32px;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top, #1e293b 0%, var(--bg) 58%);
            color: var(--text);
        }

        a { color: inherit; }

        .shell {
            max-width: 1080px;
            margin: 0 auto;
            display: grid;
            gap: 24px;
        }

        .hero, .card {
            background: rgba(17, 28, 52, 0.92);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 18px 60px rgba(15, 23, 42, 0.35);
        }

        .hero {
            padding: 32px;
            display: grid;
            gap: 24px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(139, 92, 246, 0.14);
            color: var(--accent-soft);
            font-size: 0.85rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(2.2rem, 5vw, 4.2rem);
            line-height: 1.05;
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .hero-grid, .cards {
            display: grid;
            gap: 20px;
        }

        .hero-grid {
            grid-template-columns: 1.6fr 1fr;
        }

        .cards {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .card {
            padding: 22px;
        }

        .card h2, .card h3 {
            margin: 0 0 10px;
            font-size: 1.05rem;
        }

        ul {
            margin: 12px 0 0;
            padding-left: 18px;
            color: var(--muted);
        }

        li + li { margin-top: 8px; }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            border-radius: 14px;
            border: 1px solid transparent;
            text-decoration: none;
            font-weight: 600;
        }

        .button-primary {
            background: var(--accent);
            color: white;
        }

        .button-secondary {
            border-color: var(--border);
            background: var(--panel-soft);
            color: var(--text);
        }

        .status {
            display: grid;
            gap: 14px;
        }

        .status-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(159, 176, 217, 0.14);
        }

        .status-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .status-label {
            color: var(--muted);
        }

        .status-value {
            color: var(--text);
            text-align: right;
        }

        .success {
            color: var(--success);
            font-weight: 600;
        }

        @media (max-width: 860px) {
            body { padding: 20px; }
            .hero-grid, .cards { grid-template-columns: 1fr; }
            .hero { padding: 24px; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <div class="eyebrow">Galaxi migration foundation</div>

            <div class="hero-grid">
                <div>
                    <h1>Galaxy-specific foundation, not generic scaffolding.</h1>
                    <p>
                        This Phase 1 shell is turning <strong>galaxiNew</strong> into the Galaxy foundation home for admin flows,
                        beginning with branch, cardholder, card, card type, access, and reporting foundations.
                    </p>

                    <div class="actions">
                        <a class="button button-primary" href="{{ url('/admin') }}">Open admin workspace</a>
                        <a class="button button-secondary" href="{{ route('login') }}">Admin login</a>
                    </div>
                </div>

                <aside class="card status">
                    <div>
                        <h2>Phase 1 snapshot</h2>
                        <p>Parity first, redesign later. The current foundation layer is focused on replacing scaffold defaults with Galaxy operational context.</p>
                    </div>

                    <div class="status-row">
                        <span class="status-label">Target posture</span>
                        <span class="status-value">Galaxy foundation monolith with Blade-first admin</span>
                    </div>
                    <div class="status-row">
                        <span class="status-label">Primary route</span>
                        <span class="status-value">/admin</span>
                    </div>
                    <div class="status-row">
                        <span class="status-label">Current focus</span>
                        <span class="status-value">Galaxy-specific admin IA and first live foundations</span>
                    </div>
                    <div class="status-row">
                        <span class="status-label">Reference trail</span>
                        <span class="status-value">Blueprint, Phase 1 plan, checkpoints, progress log</span>
                    </div>
                    <div class="status-row">
                        <span class="status-label">QA rhythm</span>
                        <span class="status-value">Focused checks after each safe slice</span>
                    </div>
                    <div class="status-row">
                        <span class="status-label">Commit trail</span>
                        <span class="status-value">Every safe slice leaves a visible Git checkpoint</span>
                    </div>
                    <div class="status-row">
                        <span class="status-label">Migration mode</span>
                        <span class="status-value success">Phase 1 active</span>
                    </div>
                </aside>
            </div>
        </section>

        <section class="cards">
            <article class="card">
                <h3>Live management surfaces</h3>
                <ul>
                    <li>Shops and branch scope review</li>
                    <li>Cardholders and card inventory foundations</li>
                    <li>Card types, roles, reports, gifts, and rules previews</li>
                </ul>
            </article>

            <article class="card">
                <h3>Working rules</h3>
                <ul>
                    <li>Preserve Galaxy admin information architecture</li>
                    <li>Keep shop-aware access and parity-sensitive flows explicit</li>
                    <li>Land small safe foundation slices with visible Git history</li>
                    <li>Keep checkpoints, analysis notes, and QA references close to the work</li>
                </ul>
            </article>

            <article class="card">
                <h3>Helpful project docs</h3>
                <ul>
                    <li><a href="https://docs.openclaw.ai" target="_blank" rel="noreferrer">OpenClaw docs</a></li>
                    <li><code>docs/blueprint.md</code></li>
                    <li><code>docs/phase-1-plan.md</code></li>
                    <li><code>docs/phase-1-domain-map.md</code></li>
                    <li><code>docs/migration-plan.md</code></li>
                    <li><code>docs/migration_plan.md</code></li>
                    <li><code>docs/admin-information-architecture.md</code></li>
                    <li><code>docs/admin-shell-layering.md</code></li>
                    <li><code>docs/admin-shell-config-map.md</code></li>
                    <li><code>docs/decisions.md</code></li>
                    <li><code>docs/module_mapping.md</code></li>
                    <li><code>docs/db_schema.md</code></li>
                    <li><code>docs/api_endpoints.md</code></li>
                    <li><code>docs/checkpoints/</code></li>
                    <li><code>docs/analysis/</code></li>
                    <li><code>docs/qa-test-environment.md</code></li>
                    <li><code>docs/progress-log.md</code></li>
                </ul>
            </article>
        </section>
    </main>
</body>
</html>
