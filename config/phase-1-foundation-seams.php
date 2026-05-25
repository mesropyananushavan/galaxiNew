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
            'summary' => 'Admin-side Phase 1 references stay aligned through one dashboard-focused config seam.',
            'sources' => [
                'config/phase-1-reference-docs.php',
                'resources/views/admin/dashboard.blade.php',
            ],
        ],
        [
            'label' => 'Public landing reference trail',
            'summary' => 'Public Galaxy migration references stay aligned through the landing-page doc config seam.',
            'sources' => [
                'config/landing-docs.php',
                'resources/views/welcome.blade.php',
            ],
        ],
    ],
];
