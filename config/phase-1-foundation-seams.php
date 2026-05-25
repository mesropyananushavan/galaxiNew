<?php

return [
    'focus' => 'Keep the new Galaxy-specific Phase 1 seams visible where contributors review the live foundation shell.',
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
            'summary' => 'Admin-side Phase 1 references stay aligned across the seam note, dashboard-focused config, and the live admin runtime surface.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'config/phase-1-reference-docs.php',
                'resources/views/admin/dashboard.blade.php',
            ],
        ],
        [
            'label' => 'Public landing reference trail',
            'summary' => 'Public Galaxy migration references stay aligned across the seam note, landing-page doc config, and the live public runtime surface.',
            'sources' => [
                'docs/phase-1-foundation-seams.md',
                'config/landing-docs.php',
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
    ],
];
