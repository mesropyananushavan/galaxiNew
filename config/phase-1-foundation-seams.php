<?php

return [
    'focus' => 'Keep the new Galaxy-specific Phase 1 seams visible where contributors review the live foundation shell.',
    'guide' => ['docs/phase-1-foundation-seams.md', 'config/phase-1-foundation-seams.php'],
    'source_of_truth' => ['docs/phase-1-foundation-seams.md', 'config/phase-1-foundation-seams.php'],
    'posture' => 'small config-backed and doc-backed foundation seams stay explicit',
    'items' => [
        [
            'label' => 'Domain baseline',
            'summary' => 'Entity baseline stays aligned across readable docs, implementation config, and the dashboard runtime surface.',
            'sources' => [
                'docs/phase-1-domain-map.md',
                'config/phase-1-domain-map.php',
                'resources/views/admin/dashboard.blade.php',
            ],
        ],
        [
            'label' => 'Admin reference trail',
            'summary' => 'Admin-side Phase 1 references stay aligned across the seam note, dashboard-focused config, seam-source baseline, and the live admin runtime surface.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'config/phase-1-reference-docs.php',
                'config/phase-1-seam-sources.php',
                'resources/views/admin/dashboard.blade.php',
            ],
        ],
        [
            'label' => 'Public landing reference trail',
            'summary' => 'Public Galaxy migration references, docs-card heading, metric labels, explanatory notes, and seam-source bridge copy stay aligned across the seam note, landing-page doc config, seam-source baseline, and the live public runtime surface.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'config/landing-docs.php',
                'config/phase-1-seam-sources.php',
                'resources/views/welcome.blade.php',
            ],
        ],
        [
            'label' => 'Public landing shell baseline',
            'summary' => 'The welcome-page hero copy, emphasis tokens, CTA actions, status snapshot framing, live-surface headings, and working-rule copy stay aligned through one Galaxy-specific landing config instead of drifting back into starter-era inline Blade text.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'config/landing-foundation.php',
                'resources/views/welcome.blade.php',
            ],
        ],
        [
            'label' => 'Top-level repo guidance',
            'summary' => 'Top-level contributor guidance stays aligned across the seam note and README reference trail.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'README.md',
            ],
        ],
        [
            'label' => 'README seam-source trail',
            'summary' => 'README-level config seam inventory stays aligned across repo guidance plus the admin and public entry surfaces.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'config/phase-1-seam-sources.php',
                'README.md',
                'resources/views/admin/dashboard.blade.php',
                'resources/views/welcome.blade.php',
            ],
        ],
    ],
];
