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
                'route' => 'admin.cardholders.index',
                'description' => 'Workers, clients, and holder history.',
            ],
            [
                'label' => 'Cards',
                'route' => 'admin.cards.index',
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
                'route' => 'admin.card-types.index',
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
                'route' => 'admin.shops.index',
                'description' => 'Shop list and scope boundaries.',
            ],
            [
                'label' => 'Roles & Permissions',
                'route' => 'admin.roles-permissions.index',
                'description' => 'Admin roles, permissions, and access baseline.',
            ],
            [
                'label' => 'Reports',
                'description' => 'Operational analytics and histories.',
            ],
        ],
    ],
];
