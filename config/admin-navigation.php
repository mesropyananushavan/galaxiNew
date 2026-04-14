<?php

return [
    [
        'group' => 'Operations',
        'items' => [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'description' => 'Operational overview and shortcuts.',
            ],
            [
                'label' => 'Cardholders',
                'description' => 'Workers, clients, and holder history.',
            ],
            [
                'label' => 'Cards',
                'description' => 'Card inventory, status, and assignments.',
            ],
            [
                'label' => 'Checks & Points',
                'description' => 'Purchases, accrual, and fiscal search.',
            ],
        ],
    ],
    [
        'group' => 'Catalog',
        'items' => [
            [
                'label' => 'Card Types',
                'description' => 'Card tiers, limits, and rules.',
            ],
            [
                'label' => 'Services & Rules',
                'description' => 'Service groups, conditions, and business rules.',
            ],
            [
                'label' => 'Gifts',
                'description' => 'Gift catalog and redemption settings.',
            ],
        ],
    ],
    [
        'group' => 'Administration',
        'items' => [
            [
                'label' => 'Shops',
                'description' => 'Shop list and scope boundaries.',
            ],
            [
                'label' => 'Roles & Permissions',
                'description' => 'Admin roles, permissions, and access baseline.',
            ],
            [
                'label' => 'Reports',
                'description' => 'Operational analytics and histories.',
            ],
        ],
    ],
];
