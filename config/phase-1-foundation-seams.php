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
            'label' => 'Migration baseline',
            'summary' => 'The first Galaxy schema checkpoints stay aligned across readable docs, implementation config, the migration directory, and the live admin runtime surface.',
            'sources' => [
                'docs/phase-1-migration-baseline.md',
                'config/phase-1-migration-baseline.php',
                'database/migrations',
                'resources/views/admin/dashboard.blade.php',
            ],
        ],
        [
            'label' => 'Model skeleton baseline',
            'summary' => 'The first Galaxy Eloquent models and migration anchors stay aligned across readable docs, implementation config, model files, migration files, and the live admin runtime surface.',
            'sources' => [
                'docs/phase-1-model-skeletons.md',
                'config/phase-1-model-skeletons.php',
                'app/Models',
                'database/migrations',
                'resources/views/admin/dashboard.blade.php',
            ],
        ],
        [
            'label' => 'Admin access baseline',
            'summary' => 'The first Galaxy authorization gates, policy mappings, and admin-route guardrail stay aligned across readable docs, implementation config, provider registration, and the live admin runtime surface.',
            'sources' => [
                'docs/phase-1-access-baseline.md',
                'config/phase-1-access-baseline.php',
                'app/Providers/Concerns/RegistersAdminAccessGates.php',
                'app/Providers/Concerns/RegistersAdminPolicies.php',
                'app/Policies/PermissionPolicy.php',
                'routes/admin.php',
                'resources/views/admin/dashboard.blade.php',
            ],
        ],
        [
            'label' => 'Shop-scoped access baseline',
            'summary' => 'The first Galaxy branch-boundary rules stay aligned across readable docs, implementation config, user access helpers, the shop policy seam, and the live admin runtime surface.',
            'sources' => [
                'docs/phase-1-shop-access-baseline.md',
                'config/phase-1-shop-access-baseline.php',
                'app/Models/User.php',
                'app/Policies/ShopPolicy.php',
                'app/Providers/Concerns/RegistersAdminAccessGates.php',
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
            'summary' => 'README-level config seam inventory stays aligned across repo guidance plus the admin and public entry surfaces, with both entry controllers formatting parts of that runtime reference trail and the dashboard controller shaping the admin seam-source inventory before render.',
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
