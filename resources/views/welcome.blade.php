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
            <div class="eyebrow">{{ data_get($landingFoundation, 'hero.eyebrow') }}</div>

            <div class="hero-grid">
                <div>
                    <h1>{{ data_get($landingFoundation, 'hero.title') }}</h1>
                    <p>
                        {!! $landingHeroDescriptionHtml !!}
                    </p>

                    <div class="actions">
                        @foreach ($landingHeroActions as $action)
                            <a
                                class="button {{ $action['style'] }}"
                                href="{{ $action['href'] }}"
                            >{{ $action['label'] }}</a>
                        @endforeach
                    </div>
                </div>

                <aside class="card status">
                    <div>
                        <h2>{{ data_get($landingFoundation, 'snapshot.title') }}</h2>
                        <p>{{ data_get($landingFoundation, 'snapshot.description') }}</p>
                    </div>

                    <div class="status-row">
                        <span class="status-label">{{ data_get($landingFoundation, 'labels.focus') }}</span>
                        <span class="status-value">{{ data_get($landingFoundation, 'focus') }}</span>
                    </div>

                    @foreach (data_get($landingFoundation, 'status_rows', []) as $row)
                        <div class="status-row">
                            <span class="status-label">{{ $row['label'] }}</span>
                            <span class="status-value{{ filled($row['accent'] ?? null) ? ' '.$row['accent'] : '' }}">{{ $row['value'] }}</span>
                        </div>
                    @endforeach
                </aside>
            </div>
        </section>

        <section class="cards">
            <article class="card">
                <h3>{{ data_get($landingFoundation, 'live_surfaces_title') }}</h3>
                <ul>
                    @foreach (data_get($landingFoundation, 'live_surfaces', []) as $surface)
                        <li>{{ $surface }}</li>
                    @endforeach
                </ul>
            </article>

            <article class="card">
                <h3>{{ data_get($landingFoundation, 'working_rules_title') }}</h3>
                <ul>
                    @foreach (data_get($landingFoundation, 'working_rules', []) as $rule)
                        <li>{{ $rule }}</li>
                    @endforeach
                </ul>
            </article>

            <article class="card">
                <h3>{{ data_get($landingDocs, 'title') }}</h3>
                <p>{{ data_get($landingDocs, 'labels.doc_focus') }} {{ data_get($landingDocs, 'focus') }}</p>
                <p>{{ data_get($landingDocs, 'labels.doc_coverage') }} {{ $landingDocCount }} {{ data_get($landingDocs, 'copy.coverage_suffix') }}</p>
                <p>{{ data_get($landingDocs, 'labels.doc_baseline') }} <code>{{ data_get($landingDocs, 'copy.baseline_path') }}</code> {{ data_get($landingDocs, 'copy.baseline_note') }}</p>
                <p>{{ data_get($landingDocs, 'labels.seam_source_focus') }} {{ data_get($phaseOneSeamSources, 'focus') }}</p>
                <p>{{ data_get($landingDocs, 'labels.seam_source_coverage') }} {{ $landingSeamSourceCount }} {{ data_get($landingDocs, 'copy.seam_source_coverage_suffix') }} <code>{{ data_get($landingDocs, 'copy.seam_source_source_doc') }}</code>.</p>
                <p>{{ data_get($landingDocs, 'labels.seam_source_baseline') }} <code>{{ data_get($landingDocs, 'copy.seam_source_baseline_path') }}</code> {{ data_get($landingDocs, 'copy.seam_source_baseline_note') }}</p>
                <p>{{ data_get($landingDocs, 'labels.seam_source_posture') }} {{ data_get($phaseOneSeamSources, 'posture') }}.</p>
                <p>{!! data_get($landingDocs, 'labels.seam_source_source_of_truth') !!} {!! $landingSeamSourceOfTruthText !!} {{ data_get($landingDocs, 'copy.seam_source_source_of_truth_note') }}</p>
                <p>{!! data_get($landingDocs, 'labels.doc_guide') !!} {!! $landingDocGuideText !!} {{ data_get($landingDocs, 'copy.guide_note') }}</p>
                <p>{{ data_get($landingDocs, 'labels.doc_posture') }} {{ data_get($landingDocs, 'posture') }}.</p>
                <p>{!! data_get($landingDocs, 'labels.source_of_truth') !!} {!! $landingDocSourceOfTruthText !!} {{ data_get($landingDocs, 'copy.source_of_truth_note') }}</p>
                <p>{{ data_get($landingDocs, 'labels.reference_seam_bridge') }} <code>{{ data_get($landingDocs, 'copy.reference_seam_bridge_label_path') }}</code> {{ data_get($landingDocs, 'copy.reference_seam_bridge') }}</p>
                <ul>
                    @foreach (data_get($landingDocs, 'items', []) as $doc)
                        <li>
                            @if (($doc['external'] ?? false) && filled($doc['href'] ?? null))
                                <a href="{{ $doc['href'] }}" target="_blank" rel="noreferrer">{{ $doc['label'] }}</a>
                            @else
                                <code>{{ $doc['label'] }}</code>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </article>
        </section>
    </main>
</body>
</html>
