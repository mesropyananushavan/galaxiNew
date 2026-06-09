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
            'label' => 'Shops review route',
            'route' => 'admin.shops.index',
            'guard' => 'can:viewAny,Shop',
            'coverage' => 'Keeps branch-catalog review behind the Phase 1 shop policy read guard.',
        ],
        [
            'label' => 'Shops create route',
            'route' => 'admin.shops.store',
            'guard' => 'can:create,Shop',
            'coverage' => 'Keeps the first live Galaxy branch creation path behind the bootstrap-only shop creation guard.',
        ],
        [
            'label' => 'Shops update route',
            'route' => 'admin.shops.update',
            'guard' => 'can:update,shop',
            'coverage' => 'Keeps live branch updates behind the same shop update guardrail used by the shared admin form.',
        ],
        [
            'label' => 'Cardholders review route',
            'route' => 'admin.cardholders.index',
            'guard' => 'can:viewAny,CardHolder',
            'coverage' => 'Keeps holder-catalog review behind the Phase 1 cardholder policy read guard.',
        ],
        [
            'label' => 'Cardholders create route',
            'route' => 'admin.cardholders.store',
            'guard' => 'can:create,CardHolder',
            'coverage' => 'Keeps the first live Galaxy holder creation path behind the scoped cardholder creation guard.',
        ],
        [
            'label' => 'Cardholders update route',
            'route' => 'admin.cardholders.update',
            'guard' => 'can:update,cardholder',
            'coverage' => 'Keeps live holder updates behind the same cardholder update guardrail used by the shared admin form.',
        ],
        [
            'label' => 'Roles & permissions review route',
            'route' => 'admin.roles-permissions.index',
            'guard' => 'can:viewAny,Role + can:viewAny,Permission',
            'coverage' => 'Keeps shared access-shell review and permission-vocabulary review behind both Phase 1 read policies.',
        ],
        [
            'label' => 'Roles & permissions create route',
            'route' => 'admin.roles-permissions.store',
            'guard' => 'can:create,Role',
            'coverage' => 'Keeps the first live Galaxy access-shell creation path behind the bootstrap-only role creation guard.',
        ],
        [
            'label' => 'Roles & permissions update route',
            'route' => 'admin.roles-permissions.update',
            'guard' => 'can:update,role',
            'coverage' => 'Keeps live access-shell identity updates behind the same bootstrap-only role update guardrail used by the shared admin form.',
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
