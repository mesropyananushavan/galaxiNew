<?php

return [
    'focus' => 'Keep the first Galaxy authorization gates and policy mappings explicit while Phase 1 moves admin access and shop scope away from starter-era assumptions.',
    'guide' => ['docs/phase-1-access-baseline.md', 'config/phase-1-access-baseline.php'],
    'source_of_truth' => ['docs/phase-1-access-baseline.md', 'config/phase-1-access-baseline.php', 'app/Providers/Concerns/RegistersAdminAccessGates.php', 'app/Providers/Concerns/RegistersAdminPolicies.php', 'app/Policies/PermissionPolicy.php', 'routes/admin.php'],
    'posture' => 'documented authorization baseline for live Galaxy admin access',
    'gates' => [
        [
            'label' => 'Admin workspace gate',
            'ability' => 'access-admin',
            'coverage' => 'Protects the Galaxy admin shell behind authenticated permission-bearing staff access.',
        ],
        [
            'label' => 'Shop scope gate',
            'ability' => 'access-shop',
            'coverage' => 'Keeps branch-aware visibility tied to the selected Galaxy shop context.',
        ],
    ],
    'route_guardrails' => [
        [
            'label' => 'Roles & permissions review route',
            'route' => 'admin.roles-permissions.index',
            'guard' => 'can:viewAny,Role + can:viewAny,Permission',
            'coverage' => 'Keeps shared access-shell review and permission-vocabulary review behind both Phase 1 read policies.',
        ],
    ],
    'policies' => [
        [
            'label' => 'Shop policy',
            'model' => 'Shop',
            'policy' => 'ShopPolicy',
            'coverage' => 'Covers branch listing, creation, and branch-scoped updates.',
        ],
        [
            'label' => 'CardHolder policy',
            'model' => 'CardHolder',
            'policy' => 'CardHolderPolicy',
            'coverage' => 'Covers holder listing, creation, and holder updates.',
        ],
        [
            'label' => 'Card policy',
            'model' => 'Card',
            'policy' => 'CardPolicy',
            'coverage' => 'Covers card listing, creation, and assignment/status updates.',
        ],
        [
            'label' => 'Role policy',
            'model' => 'Role',
            'policy' => 'RolePolicy',
            'coverage' => 'Covers Galaxy access-shell listing, creation, and role updates.',
        ],
        [
            'label' => 'Permission policy',
            'model' => 'Permission',
            'policy' => 'PermissionPolicy',
            'coverage' => 'Covers permission-vocabulary review under the same central Phase 1 access baseline.',
        ],
        [
            'label' => 'CardType policy',
            'model' => 'CardType',
            'policy' => 'CardTypePolicy',
            'coverage' => 'Covers tier listing, creation, and status-aware updates.',
        ],
    ],
];
