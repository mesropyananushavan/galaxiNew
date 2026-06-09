<?php

return [
    'focus' => 'Keep the public landing page anchored to Galaxy-specific Phase 1 posture instead of drifting back toward starter copy.',
    'hero' => [
        'eyebrow' => 'Galaxi migration foundation',
        'title' => 'Galaxy-specific foundation, not generic scaffolding.',
        'description' => 'This Phase 1 shell is turning galaxiNew into the Galaxy foundation home for admin flows, beginning with branch, cardholder, card, card type, access, and reporting foundations.',
        'actions' => [
            ['label' => 'Open admin workspace', 'href' => '/admin', 'style' => 'button-primary'],
            ['label' => 'Admin login', 'route' => 'login', 'style' => 'button-secondary'],
        ],
    ],
    'snapshot' => [
        'title' => 'Phase 1 snapshot',
        'description' => 'Parity first, redesign later. The current foundation layer is focused on replacing scaffold defaults with Galaxy operational context.',
    ],
    'status_rows' => [
        ['label' => 'Target posture', 'value' => 'Galaxy foundation monolith with Blade-first admin'],
        ['label' => 'Primary route', 'value' => '/admin'],
        ['label' => 'Current focus', 'value' => 'Galaxy-specific admin IA and first live foundations'],
        ['label' => 'Reference trail', 'value' => 'Blueprint, Phase 1 plan, checkpoints, progress log'],
        ['label' => 'QA rhythm', 'value' => 'Focused checks after each safe slice'],
        ['label' => 'Commit trail', 'value' => 'Every safe slice leaves a visible Git checkpoint'],
        ['label' => 'Migration mode', 'value' => 'Phase 1 active', 'accent' => 'success'],
    ],
    'live_surfaces_title' => 'Live management surfaces',
    'live_surfaces' => [
        'Shops and branch scope review',
        'Cardholders and card inventory foundations',
        'Card types, roles, reports, gifts, and rules previews',
    ],
    'working_rules_title' => 'Working rules',
    'working_rules' => [
        'Preserve Galaxy admin information architecture',
        'Keep shop-aware access and parity-sensitive flows explicit',
        'Land small safe foundation slices with visible Git history',
        'Keep checkpoints, analysis notes, and QA references close to the work',
    ],
];
