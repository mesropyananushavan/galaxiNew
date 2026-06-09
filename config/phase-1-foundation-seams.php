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
            'summary' => 'Admin-side Phase 1 references stay aligned across the seam note, dashboard-focused config, seam-source baseline, and the live admin runtime surface, with the dashboard controller now formatting more of that runtime reference prose and shaping the linked reference-doc inventory before render.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'config/phase-1-reference-docs.php',
                'config/phase-1-seam-sources.php',
                'resources/views/admin/dashboard.blade.php',
            ],
        ],
        [
            'label' => 'Public landing reference trail',
            'summary' => 'Public Galaxy migration references, docs-card heading, metric labels, explanatory notes, config-path callouts, and seam-source bridge references stay aligned across the seam note, landing-page doc config, seam-source baseline, and the live public runtime surface.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'config/landing-docs.php',
                'config/phase-1-seam-sources.php',
                'resources/views/welcome.blade.php',
            ],
        ],
        [
            'label' => 'Public landing shell baseline',
            'summary' => 'The welcome-page hero copy, focus note, emphasis tokens, CTA actions, status snapshot framing, live-surface headings, working-rule copy, named route/controller handoff, controller-passed landing config, controller-prepared hero description HTML, controller-prepared hero actions, controller-owned landing metrics, and controller-formatted reference trails stay aligned through one Galaxy-specific landing config instead of drifting back into starter-era inline Blade text.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'config/landing-foundation.php',
                'app/Http/Controllers/LandingPageController.php',
                'routes/web.php',
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
            'summary' => 'README-level config seam inventory stays aligned across repo guidance plus the admin and public entry surfaces, with both entry controllers formatting parts of that runtime reference trail.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'config/phase-1-seam-sources.php',
                'README.md',
                'app/Http/Controllers/Admin/DashboardController.php',
                'resources/views/admin/dashboard.blade.php',
                'app/Http/Controllers/LandingPageController.php',
                'resources/views/welcome.blade.php',
            ],
        ],
    ],
];
