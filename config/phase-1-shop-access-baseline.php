<?php

return [
    'focus' => 'Keep the first Galaxy branch-scoped access rules explicit while Phase 1 turns shop visibility into a real foundation boundary instead of a starter-era assumption.',
    'guide' => ['docs/phase-1-shop-access-baseline.md', 'config/phase-1-shop-access-baseline.php'],
    'source_of_truth' => ['docs/phase-1-shop-access-baseline.md', 'config/phase-1-shop-access-baseline.php', 'app/Models/User.php', 'app/Policies/ShopPolicy.php', 'app/Providers/Concerns/RegistersAdminAccessGates.php', 'routes/admin.php'],
    'posture' => 'documented shop-scoped access baseline for branch-aware Galaxy review',
    'rules' => [
        [
            'label' => 'Bootstrap branch visibility',
            'rule' => 'Bootstrap admins can access any Galaxy branch.',
            'coverage' => 'Keeps central setup and cross-branch review unblocked during Phase 1 foundation work and the first live shop review routes usable for bootstrap admins.',
        ],
        [
            'label' => 'Scoped branch visibility',
            'rule' => 'Scoped admins can access only their assigned active Galaxy branch.',
            'coverage' => 'Prevents cross-branch review and update drift while shop-aware parity rules are still being verified across the live admin routes.',
        ],
        [
            'label' => 'Paused branch restriction',
            'rule' => 'Admins assigned to paused branches do not receive scoped admin access.',
            'coverage' => 'Keeps paused branch assignments from silently acting like live operational scope.',
        ],
    ],
];
